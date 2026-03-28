<?php
namespace app\controllers;

use app\models\entities\ConfigVM;
use app\models\services\DirectoryService;
use app\controllers\BaseController;

class ConfigController extends BaseController
{

    protected $jsonString;
    protected $arrGroup;
    protected $rep  =   'helpers' . DS . 'paramRepos.json';
    protected $imgFold;
    protected $destDir;
    protected $imageService;

    public function __construct()
    {
      
    } 

    public function showIndex($idGroup){
        $json_rep= $this->getRepository();
        $php_rep = json_decode($json_rep,true);
        $paramVM = new ConfigVM();
        $paramVM = $php_rep[$idGroup]['dati'];
        $_SESSION['php_rep'] = $php_rep;

        $this->content=view(
            'input'.DS.$idGroup,
            [
                'title'=>'manutenzione ',
                'groupName'=>$idGroup,
                'maxFileSize'=>getConfig('maxFileUpload'),
                'imgDir'=>$this->imgFold,
                'params'=>$paramVM
            ]
        );
    }

    public function getJsonMenu(string $rpt=''){
        $this->rep ='helpers'.DS.'jsonRepository'. DS . $rpt . '.json';//__DIR__.DS.'..'.DS
        $rep_json = $this->getRepository();
        $rep_php = json_decode($rep_json, true);

        return $rep_php;
    }

    public function saveParam($idGroup){
        $rep_php = $_SESSION['rep_php'];
        unset($_SESSION['rep_php']);
        foreach ($_POST as $key => $value) {
            if (isset($rep_php[$idGroup]['dati'][$key])) {
                $rep_php[$idGroup]['dati'][$key][0] = $value;
            }
        }
        if (!empty($_FILES)) {
            $i = 0;
            $FILES = $_FILES;
            foreach ($FILES as $FILE) {
                $fn = "imm" . $i;
                if ($FILE['name'] !== '') {
                    //se l'immagine nuova esiste gi� nella cartella di destinazione, non fa nulla, altrimenti
                    //la copia nella destinazione e la ridimensiona
                    $destDir = new DirectoryService(getConfig('resourcesDir'), 'resources' . DS, 'images', 'backgrounds');
                    $destDir->createDir();
                    $destDir = $destDir->getDestFold();
                    switch ($fn) {
                        case 'imm0':
                            $unique = 'brand';
                            break;
                        case 'imm1':
                            $unique = 'logo';
                            break;
                    }
                    switch ($FILE["type"]) {
                        case 'image/jpeg':
                            $ext = '.jpg';
                            break;
                        case 'image/png':
                            $ext = '.png';
                            break;
                        case 'image/gif':
                            $ext = ".gif";
                            break;
                        default:
                            $ext = '.jpeg';
                    }
                    $result = $this->imageService->uploadParamImg($FILE, $unique, $destDir);
                    if ($result['success'] == false) {
                        echo $result["message"];
                        break;
                    }

                    //associa il nome del nuovo file al campo che dovr� essere salvato
                    $rep_php[$idGroup]['dati'][$fn][0] = $unique . $ext; //$FILE['name'];
                }
                $i++;
            }
        }

        // Salva il JSON aggiornato
        $jsonContent = json_encode($rep_php, JSON_PRETTY_PRINT);
        file_put_contents($this->rep, $jsonContent);
        return redirect("/");
    }

    public function getOrgParam(string $idGroup = ''){
        $this->rep = 'helpers/jsonRepository' . DS . 'paramRepos.json';
        $rep_json = $this->getRepository();
        $rep_php = json_decode($rep_json, true);
        $paramVM = new ConfigVM();
        $paramVM = $rep_php[$idGroup]['dati'];
        return $paramVM;
    }

    private function getRepository(array $data=[]){
        extract($data,EXTR_OVERWRITE);
        ob_start();
        require $this->rep;
        $content=ob_get_contents();
        ob_clean();
        return $content;
    }



}