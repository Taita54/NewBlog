<?php

namespace app\models\services;

/**
 * Description of DirectoryService
 *
 * @author giovi
 */

use App\Models\Exceptions\ExceptionHandler;


class DirectoryService
{
    //put your code here
    protected string $absFold; //getconfig(webresourcesDir)
    protected string $baseDir='risorse'; //resources quando si crea e getconfig(webresourcesDir) in lettura
    protected string $radix; //publications / images
    protected string $mainFolder; //albums,icons,bakgrounds,articoli,avvisi,coumunicati
    protected string $periodFolder; //anno sportivo o anno solare (default= null)
    protected string $specPath; //path specifico paer eventi (default=null)

    public function __construct(
        // ?string $absFold,
        //?string $baseDir='resources',
        ?string $radix = '',
        ?string $mainFolder = '',
        ?string $periodFolder = '',
        ?string $specPath = ''
    ) {
        $this->absFold=str_replace('public','resources',$_SERVER['DOCUMENT_ROOT']);
        $this->baseDir='resources';
        $this->radix = $radix;
        $this->mainFolder = $mainFolder;
        $this->periodFolder = $periodFolder;
        $this->specPath = $specPath;
    }

    public function getDestFold(){
        $path = $this->baseDir;//$_SERVER['DOCUMENT_ROOT'].DS.$this->baseDir; 
        $path = !empty($this->radix) ? $path .DS. $this->radix : $path;
        $path = !empty($this->mainFolder) ? $path . DS . $this->mainFolder : $path; 
        $path = !empty($this->periodFolder) ? $path . DS . $this->periodFolder : $path;
        $path = !empty($this->specPath) ? $path . DS . $this->specPath : $path;

        return $path;
    }

    public function getWebFold(){
        $path = !empty($this->mainFolder) ?  WEBRESOURCES_DIR. $this->radix . DS . $this->mainFolder : $this->absFold .DS . $this->radix; 
        $path = !empty($this->periodFolder) ? $path . DS . $this->periodFolder : $path;
        $path = !empty($this->specPath) ? $path . DS . $this->specPath : $path;

        return $path;
    }

    public function createDir(?string $perFold = '', ?string $spPath = ''){
        $path =  $this->baseDir .DS. $this->radix . DS . $this->mainFolder;

        if (!empty($this->periodFolder)) {
            $path .= DS . $this->periodFolder;
        }
        if (!empty($this->specPath)) {
            $path .= DS . $this->specPath;
        }

        $this->generatePath($path);

        return $path;
    }
 

    private function generatePath($path){
        if (empty($path)) {
            return false;
        }
        if (file_exists($path)) {
            return false;
        }
        if (!mkdir($path, 0777, true)) {
            $res = 'errore nel creare ' . $path;
        } else {
            return true;
        }
        echo $res;
        return;
    }

    private function getProjectRoot(){
        // Ottieni il percorso della document root del server
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        // Ottieni il percorso dello script corrente
        $scriptName = $_SERVER['SCRIPT_NAME'];
        // Rimuovi il nome dello script e eventuali directory pubbliche (es. 'public')
        $projectPath = dirname(dirname($scriptName));
        // Combina document root e project path
        $projectRoot = realpath($documentRoot . $projectPath);
        // Assicurati che il percorso termini con un separatore di directory
        return rtrim($projectRoot, DS) . DS;
    }
}
