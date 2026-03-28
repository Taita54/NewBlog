<?php

namespace app\controllers;

use app\models\entities\ConfigVM;
use app\models\entities\UserVM;
use app\models\exceptions\ExceptionHandler;


class BaseController
{
    protected $conn;
    protected $config;
    protected $rep = 'helpers' . DS . 'jsonRepository' . DS . 'paramRepos.json';
    protected $content = '';
    protected $layout = 'layout/layoutview.php';
    protected UserVM $curUser;
    protected ConfigVM $orgParam;
    protected ConfigVM $orgDim;
    protected ConfigVM $carouselMessages;

    public function __construct($conn){
        $this->conn = $conn;
        $this->orgDim = new ConfigVM();
        $this->orgParam = new ConfigVM();
        $this->carouselMessages = new ConfigVM();

        $params = $this->getParams(); // Assumes this function correctly retrieves JSON data

        // Use the setConfVal method to correctly set data.
        $this->setOrgParam($params['organization']['dati']);
        $this->setOrgDim($params['dimensions']['dati']);
        $this->setCarouselMessages($params['carouselmessages']['dati']);
    }

    public function display($data = []){ 
        if(!is_null($this->getCurUser())){
            $curUserName = $this->curUser->getNormalizedUserName();
            $curUserRole = $this->curUser->getUserRoles();
            $curUserAuth = $this->curUser->getUserRoleAuthorizations();
            if (isset($_SESSION['roleMenues'])) {
                foreach ($_SESSION['roleMenues'] as $menuTitle) {
                    $roleMenues[] = $this->getJsonPar('rep' . $menuTitle);
                }
            }
        }


        $menuJson = $this->getJsonPar('repGenerale');
        $titles = $menuJson['repGenerale']['menuTitles'];
        $isUserLogged=$this->getIsUserLogged();
        $logo = WEBRESOURCES_DIR . 'Testi' . DS . 'icons' . DS . 'Logo.png';
        $legal_file= '';
        $data['content'] = $this->content;
        var_dump($data);die;
        require $this->layout;
    }

    #region metodi protetti che richiamano metodi privati i BaseController ************************************
    protected function isUserLogged(){
        return $this->getIsUserLogged();
    } 

    protected function getCurUser(){

        if (isset($_SESSION['curUser'])) {
            $this->curUser = unserialize($_SESSION['curUser']);
        }else{
            $this->curUser=new UserVM();
        }
        return $this->curUser;
    }   
    #endregion*************************************************************************************************

    #region **********************************GETTERS & SETTERS ***********************************************
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getOrgParam()
    {
        return $this->orgParam;
    }
    public function setOrgParam($val): void{
        foreach ($val as $key => $value) {
            $this->orgParam->setConfVal($key, $value);
        }
    }

    public function getOrgDim(){
        return $this->orgDim;
    }

    public function setOrgDim($val): void{
        foreach ($val as $key => $value) {
            $this->orgDim->setConfVal($key, $value);
        }
    }

    public function getCarouselMessages(){
        return $this->carouselMessages;
    }

    public function setCarouselMessages($val): void{
        foreach ($val as $key => $value) {
            $this->carouselMessages->setConfVal($key, $value);
        }
    }
    #endregion *******************************************************************************************

    #region ********************************** PRIVACY & COOKIEPOLICY ******************************************

        public function legalAdvertising($id)
        {
            $legalFile = $this->getLegalFile($id);
            $legalFilePath = __DIR__ .DS. '..'.DS.'..'.DS.'resources'.DS.'publications'.DS.'guide'.DS . $legalFile . '.php';

            if (! file_exists($legalFilePath)) {
                throw new ExceptionHandler($this->getEmptyFileMessage($legalFilePath));
            }

            ob_start();                     // Avvia il buffer di output
            include_once $legalFilePath;    // Include il file, eseguendo il codice PHP al suo interno
            $legalContent = ob_get_clean(); // Recupera e pulisce il buffer

            $decoded_title=html_entity_decode(htmlspecialchars($title,ENT_QUOTES,'UTF-8'));
            $decoded_content = html_entity_decode($content);

            $legalContent=[
                'title'=>$decoded_title,
                'html' =>$decoded_content
            ];

            header('Content-Type: application/json');
            echo json_encode($legalContent);
            exit; // Ferma ulteriori esecuzion

        }

   #endregion *************************************************************************************************
   
    #region **********************************metodi privati *********************************************

    private function getEmptyFileMessage($fileName){
        if (ENV == 'develop') {
            return "Il file '$fileName' è vuoto o non esiste.";
        } else {
            return 'Il file cercato è vuoto o non esiste.';
        }

    }
    private function getJsonPar(string $rpt){
        $this->rep = 'helpers' . DS . 'jsonRepository' . DS . $rpt . '.json'; //__DIR__.DS.'..'.DS
        $rep_json = $this->getRepository();
        $rep_php = json_decode($rep_json, true);
        return $rep_php;
    }
    private function getParams(){
        $this->rep = 'helpers' . DS . 'jsonRepository' . DS . 'paramRepos.json';
        $rep_json = $this->getRepository();
        $rep_php = json_decode($rep_json, true);
        return $rep_php;
    }
    private function getRepository(array $data = [])
    {
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require $this->rep;
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }
    private function getIsUserLogged():bool
    {
      return isset($_SESSION['curUser'])?true:false;
    }

    private function getLegalFile($id)
    {
        switch ($id) {
            case 1:
                $legalFile = 'privacy';
                $title = 'Informativa Privacy';
                break;
            case 2:
                $legalFile = 'cookiePolicy';
                $title = 'Informativa uso cookies';
                break;
            case 3:
                $legalFile='guida';
                break;
            case 4:
                $legalFile='consenso';
                break;
            case 5:
                $legalFile='cookieAdvertise';
                break;
            case 6:
                $legalFile='informativa';
                break;
            case 7:
                $legalFile='optionalTerms';
                break;
            default:
                $legalFile = 'privacy';
                $title = 'Normativa Privacy';
        }
        return $legalFile;
    }
    #endregion ******************************************************************************************
    // Metodi utili che possono essere estesi da altri controller (es. metodi per la gestione degli errori)
}
