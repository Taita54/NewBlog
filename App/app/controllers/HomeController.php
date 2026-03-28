<?php

namespace app\controllers;

use app\models\services\DirectoryService;
use app\models\services\GalleryService;

class HomeController extends BaseController
{

    protected $galleryService;
  
    public function __construct(\PDO $conn)
    {
        parent::__construct($conn);
        $this->galleryService = new GalleryService($conn);
    }

    public function showIndex()
    {

        //n.b. name, maxListToShow, e il successivo messages, sono falsi positivi 
        //poiche vngono valorizzati a runtime
        $title = $this->orgParam->name[0];//nb. viene valorizzato a runtime
        $mls = $this->orgDim->maxListsToShow[0];//viene valorizzato a runtime
        $nItm = 5; // numero di slide da visualizzare nel carousel

        $carouselImages = $this->getCarouselImages($nItm);

        $articles = $this->galleryService->getArticlesList($mls);
        $albums = $this->galleryService->getGalleriesList($mls);
        $videoClips = $this->galleryService->getVideoGalleries($mls);
        $videoYTClips = $this->galleryService->getYouTubeVid($mls);
        $_SESSION['startMenu'] = '/';

        $this->content = view('Home/Index', [
            'info' => getInfo(),
            'title' => $title,
            'carouselItems' => array($carouselImages, $this->carouselMessages->messages),
            'articles' => $articles['latestArticles'],
            'albums' => $albums['latestAlbums'],
            'videoClips' => $videoClips['latestVideoClips'],
            'videoYTClips' => $videoYTClips['latestYTVideoClips'],
            'adviceCollection' => $this->getAdvicesList(),
            'svgColl' => $this->galleryService->getIndexSvgImages(),
        ]);

        $this->display();
    }
    public function showInConstruction()
    {
        $this->content = view('Home/InCostruzione', [
            'info' => getInfo(),
            'title' => $this->orgParam->name[0],
        ]);
        $this->display();
    }

    public function startMenu($id)
    {
        setStartMenu($id);
// var_dump('setStartMenu '.$_SESSION['startMenu']);die;
        // This method is not implemented yet, but it can be used to set the start menu.
        // You can add your logic here to set the start menu.
        // For example, you might want to redirect to a specific page or load a specific view.
        // $this->redirect('some/page');
    }

    #region ******************************** LOCAL METHODES ****************************
    private function getCarouselImages($nIt): array
    {
        //estrae n album casuali dall'archivio
        $arrMediaVM = $this->galleryService->getRandomDir($nIt);
        $desiredImageCount = $nIt; // The number of images you want
        $existingsFiles = 0;
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $carouselImages = [];
        //Keep track of checked directories
        $directoriesChecked = [];
        $dirChkdInd = 0;
        foreach ($arrMediaVM as $mediaVM) {
            $dir = new DirectoryService('images', 'albums', $mediaVM->getAnnoSportivo(), 'ev' . $mediaVM->getIdDestination());
            $dirPath = 'resources/images/albums/' . $mediaVM->getAnnoSportivo() . '/ev' . $mediaVM->getIdDestination(); //$dir->getDestFold();
            $webPath = WEBRESOURCES_DIR . 'images/albums/' . $mediaVM->getAnnoSportivo() . '/ev' . $mediaVM->getIdDestination(); //$dir->getWebFold();

            if (is_dir($dirPath)) {
                $files = [];
                $handle = opendir($dirPath);

                while ($file = readdir($handle)) {
                    if ($file !== '.' && $file !== '..') {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowedExtensions)) {
                            $files[] = $file;
                        }
                    }
                }
                closedir($handle);

                if (count($files) > 0) {
                    $directoriesChecked[$dirChkdInd]['path'] = $webPath;
                    $directoriesChecked[$dirChkdInd]['files'] = $files;
                    $directoriesChecked[$dirChkdInd]['description'] = $mediaVM->getDescription();
                    $existingsFiles += count($files);

                    $dirChkdInd++;
                }
            }
        }

        if (count($directoriesChecked)) {
             $secondChance=0;
            //rimescola le immagini di ciascuna directory che ne contiene almeno 1
            // foreach($directoriesChecked as $direcotryChecked){
            //        $array2=shuffle($directoriesChecked[$secondChance]['files']);
            //     $directoriesChecked[$secondChance]['files']=$array2;
            //      $secondChance++;
            /// }

            if ($desiredImageCount > $existingsFiles) {
                $desiredImageCount = $existingsFiles;
            }
            while (count($carouselImages) < $desiredImageCount) {
                if ($secondChance > $dirChkdInd) {
                    $secondChance = 0;
                }
                if (isset($directoriesChecked[$secondChance]['files'])) {
                    $maxImagesIndex = count($directoriesChecked[$secondChance]['files']);
                    if (isset($directoriesChecked[$secondChance]['selectedImm'])) {
                        $nextImageIndex = count($directoriesChecked[$secondChance]['selectedImm']);
                    } else {
                        $nextImageIndex = 0;
                    }
                    if ($nextImageIndex < $maxImagesIndex) {
                        $webPath = $directoriesChecked[$secondChance]['path'];
                        $carouselImages[] = $webPath . DS . $directoriesChecked[$secondChance]['files'][$nextImageIndex];
                        $directoriesChecked[$secondChance]['selectedImm'][] = $directoriesChecked[$secondChance]['files'][$nextImageIndex];
                    }
                }

                $secondChance++;
            }
        }

        //se non è stat selezionata alcuna immagine allora viene caricata una di default
        if (count($carouselImages) < $nIt) {
            while (count($carouselImages) < $nIt) {
                $carouselImages[] = WEBRESOURCES_DIR . 'images' . DS . 'backgrounds' . DS . 'logo.png';
            }
        }

        return $carouselImages;
    }

    private function getAdvicesList(): array
    {
        $advicesList = [];
        $advicesList[0][0] = 'Avvisi e comunicati generali';
        $advicesList[1][0] = 'Avvisi per i corsi';
        $advicesList[2][0] = 'Avvisi per i teams';

        $advicesList[0][1] = $this->galleryService->getGenericAlerts()['advicesList'];
        $advicesList[1][1] = $this->galleryService->getCoursesAdv()['courseAdvices'];
        $advicesList[2][1] = $this->galleryService->getTeamsAdv()['teamAdvices'];

        return $advicesList;
    }
    #end region ************************************************************************
}
