<?php

namespace app\models\services;

use PDO;
use app\models\services\DirectoryService;
use app\models\services\PaginatorService;
use app\models\services\ImageService;
use app\models\entities\ArticleVM;
use app\models\entities\MediaType_CatVM;
use app\models\entities\AlbumVM;
use app\models\entities\MediaVM;
use app\models\exceptions\ExceptionHandler;
//use app\models\enums\EventsType;
use app\models\enums\PubTypes;
use app\models\enums\AreaPub;

class GalleryService{

    protected $conn;
    protected $mediaVM;
    protected $mediaListVM;
    protected $directoryService;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    #region Selezion galleria
    /**
     * Summary of getRandomDir
     * sceglie le immagini da mostrare nello slaider della pagina iniziale
     * @param mixed $n
     * @return object[]
     */
    public function getRandomDir(?int $n = 5): array
    {
        //1 seleziona 5 album di immagini a caso tra quelli in archivio
        $cats = array_keys(getconfig('sectionsBlog'));
        $sql= "SELECT * FROM pubblicazioni WHERE section=:cat ";
        $stm = $this->conn->prepare($sql);
        $res = $stm->execute(['cat'=>$cats[0]]);
        
        if ($res && $stm->rowCount()) {
            $records = $stm->fetchall(PDO::FETCH_OBJ);
        }
        $row = [];
        for ($i = 0; $i < $n; $i++) {
            $mediaVM=new MediaVM();
            $row[$i] = cast($mediaVM,$records[mt_rand(0, $stm->rowCount() - 1)]) ;
        }

        return $row;
    }

    #endregion

    #region                   CARICA LISTE PER ARGOMENTO (ARTICLI, ALBUMS, CLIPS VIDEO, YOUTUBE VIDEOS ETC)
    public function getAllVideoGalleries($pagination)
    {
        //seleziona l'elenco di tutte gallerie video (AreaPub:e,PubType:5)
        $result = [
            'latestVideoClips' => [],
            'message' => 'nessun video presente',
            'success' => false
        ];

        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Video;

        $sql = $this->getSqlVideoGallery();
        $sql .= " WHERE  md.IdMediaType=:pubType AND 
            (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se
            OR tm.Nome LIKE :se )
            ";

        $mediaList=$pagination->setQuery2($sql, [':pubType',':se'], [$pubType,$ser]);
        
        if ($mediaList) {
            $videoClipVMArr=$this->uploadVideos($mediaList);
            $result = [
                'latestVideoClips' => $videoClipVMArr,
                'success' => true
            ];
        }
        return $result;
    }

    public function getVideoGalleries($n)
    {  
        //seleziona l'elenco delle ultime 'n' gallerie (AreaPub e) (PubType 5)
        $result = [
            'message' => 'nessun video presente',
            'success' => false
        ];

        $sql = $this->getSqlVideoGallery();
        $sql .= "AND md.IdMediaType=5 ";
        $sql .="ORDER BY md.RowVersion DESC ";
        $sql .=" LIMIT " . $n;
        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res) {
            $mediaList = $stm->fetchall(PDO::FETCH_OBJ);
        }

        $videoClipVMArr=$this->uploadVideos($mediaList);
        $result = [
            'latestVideoClips' => $videoClipVMArr,
            'success' => true
        ];
        return $result;
    }
    /**
     * summary of getGalleriesList:
     * seleziona le ultime 'n' gallerie in ordine di aggiornamento
     */
    public function getAllAlbumsGalleries($pagination){
            //seleziona l'elenco di tutte gallerie video (,PubType:5)
            $result = [
            'latestAlbums' => [],
            'message' => 'nessun album presente',
            'success' => false
        ];

        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Album;

        $sql = $this->getSqlAlbumsGalleries();
        $sql .= " WHERE  md.IdMediaType=:pubType AND 
            (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se
            OR tm.Nome LIKE :se )
            ";

        $mediaList=$pagination->setQuery2($sql, [':pubType',':se'], [$pubType,$ser]);

        if ($mediaList) {
            $albumsVMArr = $this->uploadAlbum($mediaList);
            $result = [
                'latestAlbums' => $albumsVMArr,
                'success' => true
            ];
        }
        return $result;

    }
    public function getGalleriesList($n)
    {
        //seleziona l'elenco delle ultime 'n' gallerie (AreaPub e) (PubType 4)
        $result = [
            'message' => 'nessun album presente',
            'success' => false
        ];
        $sql = $this->getSqlAlbumsGalleries();
        $sql .= "AND md.IdMediaType=4  ";
        $sql .= "ORDER BY md.RowVersion DESC ";
        $sql .= " LIMIT " . $n;

        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res) {
            $mediaListArr = $stm->fetchall(PDO::FETCH_OBJ);
            $albumsVMArr = $this->uploadAlbum($mediaListArr);
            $result = [
                'latestAlbums' => $albumsVMArr,
                'success' => true
            ];
        }
        return $result;
    }

    public function getAllArticles($pagination){
        //seleziona l'elenco di tutti gli articoli ( PubType 2)
        $result = [
            'latestArticles' => [],
            'message' => 'nessun articolo presente',
            'success' => false
        ];

        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Articolo;

        $sql = "SELECT * FROM tmedia as md ";
        $sql .= "WHERE md.IdMediaType=2 AND 
            (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se )
            ";
        $mediaList=$pagination->setQuery2($sql, [':pubType',':se'], [$pubType,$ser]);

        if ($mediaList) {
            $articlesVMArr = $this->uploadArticle($mediaList);
            $result = [
                'latestArticles' => $articlesVMArr,
                'success' => true
            ];
        }
        return $result;
    }

    public function getArticlesList($n)
    {
        //raccoglie gli ultimi n articoli (PubTypes 2) di team e corsi (AreaPub c e t)
        $result = [
            'message' => 'nessun articolo presente',
            'success' => false
        ];
        $sql = "SELECT md.*,tm.nome,tm.foto ";
        $sql .="FROM tmedia AS md ";
        $sql .="right JOIN tteams AS tm ";
        $sql .="ON md.IDDestination=tm.ID ";
        $sql .="RIGHT JOIN tcorsi AS co ";
        $sql .="ON tm.IDCorso=co.Id ";
        $sql .="WHERE md.IdMediaType=2 ";
        $sql .="ORDER BY md.RowVersion DESC ";
        if ($n > 0) {
            $sql .= " LIMIT " . $n;
        }
        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res) {
            $mediaListArr = $stm->fetchall(PDO::FETCH_OBJ);
            $articlesVMArr = $this->uploadArticle($mediaListArr);
            $result = [
                'latestArticles' => $articlesVMArr,
                'success' => true
            ];
        }
        return $result;
    }

    public function getModulesList($pagination):array{
        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Modulo;

        $sql = "SELECT * FROM tmedia as md";
        $sql .= " WHERE  md.IdMediaType=:pubType";
        $sql .= " AND (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se )
            ";

        $mediaList=$pagination->setQuery2($sql, [':pubType',':se'], [$pubType,$ser]);
  
        $mediaListVM=[];
        if ($mediaList) {
            foreach($mediaList as $record){
                $album = new AlbumVM();
                $album = cast($album, $record);
                $album->setRandImage(WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS . 'logo.png'); //imposta l'immagine di default
                $mediaListVM[] = $album;
            }
        }

        return $mediaListVM;
    }
    public function getYouTubeList($pagination):array{
        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Link;

        $sql = "SELECT * FROM tmedia as md";
        $sql .= " WHERE  md.IdMediaType=:pubType";
        $sql .= " AND (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se )
            ";

        $mediaList=$pagination->setQuery2($sql, [':pubType',':se'], [$pubType,$ser]);
  
        $mediaListVM=[];
        if ($mediaList) {
            foreach($mediaList as $record){
                $album = new AlbumVM();
                $album = cast($album, $record);
                $album->setRandImage(WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS . 'logo.png'); //imposta l'immagine di default
                $mediaListVM[] = $album;
            }
        }

        return $mediaListVM;
    }
    public function getYouTubeVid($n){

        $result = [
            'message' => 'nessun video presente',
            'success' => false
        ];
        $sql = "SELECT md.*,ev.dataevento,ev.IDTipoEvento,tm.nome,tm.foto,co.Descrizione as CourseName ";
        $sql .= "FROM tteams AS tm JOIN tcorsi AS co ";
        $sql .= "ON tm.IDCorso=co.Id JOIN teventi AS ev ";
        $sql .= "ON tm.ID=ev.IDSquadra JOIN tmedia AS md ";
        $sql .= "ON ev.ID=md.IdDestination AND md.IdMediaType=7 ";
        $sql .= "ORDER BY md.RowVersion DESC ";
        if ($n > 0) {
            $sql .= " LIMIT " . $n;
        }
        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if($res){
            $mediaList = $stm->fetchall(PDO::FETCH_OBJ);
            $videoClipVMArr = [];
            foreach ($mediaList as $video) {
                $videoColl = new AlbumVM();
                $videoColl = cast($videoColl, $video);
                $videoColl->setAlt_Text(array_search($videoColl->getIDTipoEvento(), EventsType::dummy_array));
                $videoThumb = $videoColl->getFileName();
                $videoColl->setRandImage($videoThumb);
                $directoryService = new DirectoryService('images', 'albums' . DS . 'teams');
                $videoColl->setTeamAvatar(getPhoto($directoryService->getWebFold(), $videoColl->getTeamAvatar())); //aggiunge il corretto path alla foto
                $videoClipVMArr[] = $videoColl;
            }
        }
        $result = [
            'latestYTVideoClips' => $videoClipVMArr,
            'success' => true
        ];
        
        return $result;
    }

    public function getGuidesList($pagination,$area):array{
        $search = str_replace('_DOT_', '.', $pagination->getSearch() ?? '');
        $ser = '%' . $search . '%';
        $pubType = PubTypes::Guida;
 
        $sql = "SELECT * FROM tmedia as md WHERE md.IdMediaType = :pubType";

        // Conditional check for $area
        if ($area != 'n') {
            $sql .= " AND (md.Area = :area OR md.Area = 'n')";
            $params = [':pubType', ':area', ':se'];  // Correct order is important
            $values = [$pubType, $area, $ser];
        } else {
            $sql .= " AND md.Area = 'n'";
            $params = [':pubType', ':se'];
            $values = [$pubType, $ser];
        }

        $sql .= " AND (md.Titolo LIKE :se
            OR md.Author LIKE :se
            OR md.Publisher LIKE :se
            OR md.Data_creazione LIKE :se )";

        $params = array_unique($params);
        $mediaList= $pagination->setQuery2($sql, $params,$values);
        if ($mediaList) {
            $articlesVMArr = $this->uploadGuide($mediaList);

            $result = [
                'latestArticles' => $articlesVMArr,
                'success' => true
            ];
        }
        return $result;
    }
    public function getGenericAlerts()
    {
        $result=[
            'message'=>'nessun avviso in corso',
            'advicesList' =>[],
            'success'=>false
        ];

        $sql = "SELECT md.* FROM tmedia as md  ";
        $sql .="WHERE md.IDDestination=0 ";
        $sql .="AND md.IdMediaType=1 ";
        $sql .="AND md.area='n' ";
        $sql .="AND md.Data_scadenza>CURDATE() ";
        $sql .="ORDER BY md.Data_scadenza DESC;";

        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res && $stm->rowCount()) {
            $resultListVM = $stm->fetchall(PDO::FETCH_OBJ);
            $advicesList=[];
            foreach($resultListVM as $resAdv)
            {
                $advice=new ArticleVM();
                $advice=cast($advice,$resAdv);
                $advice->setAlt_Text(array_search($advice->getIdMediaType(), EventsType::dummy_array));
                $artThumb = $this->getArticleThumb($advice);
                $advice->setArtThumb($artThumb);
                $directoryService = new DirectoryService('images', 'backgrounds');
                $advice->setTeamAvatar(getPhoto($directoryService->getWebFold(), $advice->getTeamAvatar())); //aggiunge il corretto path alla foto
                $advicesList[] = $advice;

            }
            $result = [
                'message' =>'',
                'advicesList' => $advicesList,
                'success' => true
            ];
        }
        return $result;
    }

    public function getCoursesAdv()
    {
        // avvisi e comunicati sui corsi (AreaPub c) (PubTypes 1 e 3)
        $result = [
            'courseAdvices' => null,
            'success' => false
        ];
        $sql = "SELECT md.*,co.foto,co.Descrizione,co.AnnoSportivo FROM tmedia as md RIGHT JOIN tcorsi AS co ON md.IDDestination=co.Id WHERE (md.IdMediaType=1 OR md.IdMediaType=3) AND md.area='c' AND (co.DataChiusura>CURDATE() OR md.Data_scadenza>CURDATE()) ORDER BY md.RowVersion DESC; ";

        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res) {
            $resultListVM= $stm->fetchall(PDO::FETCH_OBJ);
            $advicesList = [];
            foreach ($resultListVM as $resAdv) {
                $advice = new ArticleVM();
                $advice = cast($advice, $resAdv);
                $advice->setAlt_Text(array_search($advice->getIdMediaType(), EventsType::dummy_array));
                $artThumb = $this->getArticleThumb($advice);
                $advice->setArtThumb($artThumb);
                $directoryService = new DirectoryService('images', 'albums'.DS.'courses');
                $advice->setTeamAvatar(getPhoto($directoryService->getWebFold(), $advice->getTeamAvatar())); //aggiunge il corretto path alla foto
                $advicesList[] = $advice;
            }

            $result = [
                'courseAdvices' => $advicesList,
                'success' => true
            ];
        }
        return $result;
    }

    public function getTeamsAdv()
    {
        //avvisi e comunicati per i teams (AreaPub t) (PubTypes 1 e 3)
        $result = [
            'teamAdvices' => null,
            'success' => false
        ];
        $sql = "SELECT md.*,tm.nome,tm.foto FROM tmedia as md ";
        $sql .="RIGHT JOIN tteams AS tm ";
        $sql .="ON md.IDDestination=tm.ID ";
        $sql .="WHERE (md.IdMediaType=1 OR md.IdMediaType=3) ";
        $sql .="AND md.area='t' AND Data_scadenza>CURDATE() ";
        $sql .="ORDER BY md.Data_scadenza DESC;";

        $stm = $this->conn->prepare($sql);
        $res = $stm->execute();
        if ($res) {
            $resultListVM = $stm->fetchall(PDO::FETCH_OBJ);
            $advicesList = [];
            foreach ($resultListVM as $resAdv) {
                $advice = new ArticleVM();
                $advice = cast($advice, $resAdv);
                $advice->setAlt_Text(array_search($advice->getIdMediaType(), EventsType::dummy_array));
                $artThumb = $this->getArticleThumb($advice);
                $advice->setArtThumb($artThumb);
                $directoryService = new DirectoryService('images', 'albums' . DS . 'teams');
                $advice->setTeamAvatar(getPhoto($directoryService->getWebFold(), $advice->getTeamAvatar())); //aggiunge il corretto path alla foto
                $advicesList[] = $advice;
            }
            $result = [
                'teamAdvices' => $advicesList,
                'success' => true
            ];
        }
        return $result;
    }
    #endregion

    #region uploading delle directories dei contenuti

    protected function uploadArticle($allArticles){
        $albumsVMArr = [];
        foreach ($allArticles as $record){
            $album=new ArticleVM();
            $album=cast($album,$record);
            $album->setAlt_Text(array_search($album->getIdMediaType(), EventsType::dummy_array));
            $artThumb = $this->getArticleThumb($album);
            $album->setArtThumb($artThumb);
            $directoryService=new DirectoryService('images','albums'.DS.'teams');
            $album->setTeamAvatar( getPhoto($directoryService->getWebFold(), $album->getTeamAvatar()));//aggiunge il corretto path alla foto
            $albumsVMArr[]=$album;
        }
        return $albumsVMArr;
    }
    protected function uploadAlbum($allImages){
       // die('galleryservice 498');
        $albumsVMArr = [];
        foreach ($allImages as $record) {

            $album=new AlbumVM();
            $album=cast($album,$record);
            $album->setAlt_Text(array_search($album->getIDTipoEvento(), EventsType::dummy_array));
            $albumRandImg = $this->getRandImage($album); //prende una immagine dall'album per mostrarla nella pagi a iniziale
            $album->setRandImage($albumRandImg);
            $directoryService = new DirectoryService('images', 'albums' . DS . 'teams');
            $album->setTeamAvatar(getPhoto($directoryService->getWebFold(), $album->getTeamAvatar()));
            $albumsVMArr[] = $album;
        }

        return $albumsVMArr;
    }
    protected function uploadVideos($allVideoRecords){
        $videoClipVMArr = [];
        foreach ($allVideoRecords as $video) {
            $clip = new AlbumVM();
            $clip = cast($clip, $video);
            $clip->setAlt_Text(array_search($clip->getIDTipoEvento(), EventsType::dummy_array));
            $videoThumb=$this->getVideoThumb($clip);
            $directoryService = new DirectoryService('images', 'albums' . DS . 'teams');
            $clip->setTeamAvatar(getPhoto($directoryService->getWebFold(), $clip->getTeamAvatar())); //aggiunge il corretto path alla foto
            $clip->setRandImage($videoThumb);
           // var_dump($clip);
         //   $dirClip=new DirectoryService('images','videos',$clip->getAnnoSportivo(),'ev'.$clip->getIdDestination());
          //  $clip->setRandImage($dirClip->getWebFold().DS.$clip->getFileName());
            $videoClipVMArr[] = $clip;
        }
      //  die;
        return $videoClipVMArr;
    }
    #endregion
    protected function uploadGuide($allGuides){
        $albumsVMArr = [];
        foreach ($allGuides as $record) {
            $album = new ArticleVM();
            $album = cast($album, $record);
            $artThumb = $this->getGuideThumb($album);
            $album->setArtThumb($artThumb);
            $albumsVMArr[] = $album;
        }
        return $albumsVMArr;
    }
    #region protected methods for getting images and videos

    protected function getSqlVideoGallery(){
        $sql = "SELECT md.*,ev.dataevento,ev.IDTipoEvento,tm.nome,tm.foto,co.Descrizione as CourseName ";
        $sql .="FROM tteams AS tm JOIN tcorsi AS co ";
        $sql .="ON tm.IDCorso=co.Id JOIN teventi AS ev ";
        $sql .="ON tm.ID=ev.IDSquadra JOIN tmedia AS md ";
        $sql .="ON ev.ID=md.IdDestination ";

        return $sql;
    }
    protected function getSqlAlbumsGalleries(){
        $sql = "SELECT md.*,ev.dataevento,ev.IDTipoEvento,tm.nome,tm.foto,co.Descrizione as CourseName ";
        $sql .="FROM tteams AS tm JOIN tcorsi AS co ";
        $sql .="ON tm.IDCorso=co.Id JOIN teventi AS ev ";
        $sql .="ON tm.ID=ev.IDSquadra JOIN tmedia AS md ";
        $sql .="ON ev.ID=md.IdDestination ";

        return $sql;
    }
    protected function getRandImage($album)
    {
        $directory = new DirectoryService('images', 'albums', $album->getAnnoSportivo(),  'ev' .  $album->getIdDestination());
        $imageService=new ImageService();
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $fileCollection=[];
        $radImage='';
//         $fileCollection = glob($directory->getDestFold() . DS.'*.' . implode('|', $allowedExtensions));

        if (is_dir($directory->getDestFold())) {
            // apro la cartella che voglio leggere  
            $handle = opendir($directory->getDestFold());
            // scorro tutti i file presenti nella cartella
            while ($file = readdir($handle)) {
                $filePath = $directory->getDestFold() . DS . $file;
                if (is_file($filePath)) {
                    // Ottieni l'estensione del file
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    // Controlla se l'estensione è tra quelle consentite
                    if (in_array($extension, $allowedExtensions)) {
                        $imageVM = $imageService->associateImgData($filePath);
                        $imageVM->setFile($imageVM->getAlt());
                        array_push($fileCollection, $imageVM);
                    }
                }
            }
            if (count($fileCollection) >= 1) {
                $randIndex = mt_rand(0, count($fileCollection)-1);
                $radImage = $directory->getWebFold() . DS . $fileCollection[$randIndex]->getFile();
            }
            // chiudo la cartella che ho letto 
            closedir($handle);
        } else {
            $radImage = WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS . 'logo.png';
            // throw new ExceptionHandler($this->getEmptyFileMessage($directory->getDestFold()));
        }    
        return $radImage;
    }
    protected function getArticleThumb($article)
    {
        $short_description = '...';
        $directory = new DirectoryService('publications', 'articoli', $article->getAnnoSportivo());
        $articleThumb='';

        if (is_dir($directory->getDestFold())) {
            $filePath = $directory->getDestFold() . DS . $article->getFileName();
            if (is_file($filePath)) {
                $articleThumb = file_get_contents($directory->getDestFold() . DS . $article->getFileName(),true);
                $max_length = 100; // numero di caratteri che vuoi mostrare
                if (mb_strlen($articleThumb) > $max_length) {
                    $short_description = mb_substr($articleThumb, 0, $max_length) . '...';
                } else {
                    $short_description = $articleThumb . '...';
                }
            }
        } else {
            $short_description = '...';
            //throw new ExceptionHandler($this->getEmptyFileMessage($directory->getDestFold()));
        }
        return $short_description;
    }
    protected function getGuideThumb($guide){
        $short_description = '...';
        $directory = new DirectoryService('publications', 'guide');
        $guideThumb = '';

        if (is_dir($directory->getDestFold())) {
            $filePath = $directory->getDestFold() . DS . $guide->getFileName();
            if (is_file($filePath)) {
                $guideThumb = file_get_contents($directory->getDestFold() . DS . $guide->getFileName(), true);
                $max_length = 100; // numero di caratteri che vuoi mostrare
                if (mb_strlen($guideThumb) > $max_length) {
                    $short_description = mb_substr($guideThumb, 0, $max_length) . '...';
                } else {
                    $short_description = $guideThumb . '...';
                }
            }
        } else {
            $short_description = '...';
            //throw new ExceptionHandler($this->getEmptyFileMessage($directory->getDestFold()));
        }
        return $short_description;
    }
    protected function getVideoThumb(AlbumVM $video)
    {
        $directory = new DirectoryService('images', 'videos', $video->getAnnoSportivo(),  'ev' .$video->getIdDestination());
        $randVideo = '';
if($video->getIdDestination()=='209'){
   // var_dump($directory->getDestFold(),$directory->getWebFold(),$video->getFileName());die;
}
        /*  */
        if (is_dir($directory->getDestFold())) {
                $filePath = $directory->getWebFold() . DS . $video->getFileName();
                $randVideo =$filePath;// $directory->getWebFold() . DS . $video->getFileName();
                // if (is_file($filePath)) {
                // }
                
                // throw new ExceptionHandler($this->getEmptyFileMessage($directory->getDestFold()));
            }else{
                    $randVideo = WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS . 'logo.png';
        }

        return $randVideo;
    }
    public function getIndexSvgImages()
    {
        $svgCollFold=WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS ;
        $svgColl=[];
        for($i=0;$i<3;$i++)
        {
            array_push($svgColl,$svgCollFold."svg".($i+1).".svg.png");
        }
        return $svgColl;
    }

    #endregion

  
    

}