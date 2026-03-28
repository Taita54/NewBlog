<?php

namespace app\models\services;

use app\controllers\ConfigController;
use app\models\services\Resize;
use app\models\entities\ImageVM;
use app\models\exceptions\ExceptionHandler;
use finfo;


class ImageService
{
    protected $filename;
    protected $destFold; // la cartella di destinazione del file
    protected $newName; // il nuovo nome del file
    protected $newWidth;
    protected $newHeight;
    protected $resizeObj;

    public function __construct($filename = null, $width = null, $height = null){
        
    }

    public function uploadImage(string $filename, string $destFold,?int $maxSize=3000000 ){
        $newFileName='';
        if (isset($_FILES['file'])) {
            $files = $_FILES['file'];
            // Se è stato caricato un solo file, convertiamo l'array in un formato multi-file
            if (!is_array($files['name'])) {
                $files = array(
                    'name' => array($files['name']),
                    'type' => array($files['type']),
                    'tmp_name' => array($files['tmp_name']),
                    'error' => array($files['error']),
                    'size' => array($files['size'])
                );
            }
            $status=false;$res=false;
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $sourcePath = $files['tmp_name'][$i];
                    if($files['size'][$i] > $maxSize) {
                        $status=true;
                        continue; // Salta questo file e passa al successivo
                    }

                    $result = $this->verifyFileType($files['type'][$i]);
                    $extension = $result['ext'];
                    // Qui puoi chiamare la tua funzione per copiare e rinominare il file
                    $newFileName = $this->copyImageWithUniqueHash($sourcePath, $destFold, $extension);

                    if ($newFileName) {
                        $res=true;
                        // Puoi fare qualcosa qui, come salvare il nome del file in un database di ciscuna immagine
                    } else {
                        $res=false;
                    }
                } else {
                    $res=false;
                }
            }
        } else {
           $res=false;
        }

        $_SESSION['message'] = $this->getMessage($res, $status);
        $_SESSION['success'] = $res;

        return $newFileName;
    }
    private function copyImageWithUniqueHash($sourcePath, $destinationFolder,$extension){
        $folderPrefix = basename($destinationFolder);
        $imageHash = md5_file($sourcePath);

        if($this->isImageDuplicate($imageHash, $destinationFolder)){
            return false;
        }

        $counter = $this->contaImmagini($destinationFolder);

        $newFileName = sprintf("%s_%d_%s.%s", 'IMG'. $folderPrefix, $counter, substr($imageHash, 0, 12), $extension);
        $newPath = $destinationFolder . '/' . $newFileName;

        if (copy($sourcePath, $newPath)) {
            return $newFileName;
        } else {
            return false;
        }
    }

    public function uploadParamImg(array $FILE, string $filename, string $destFold){
        $result = $this->anyFile();
        if ($result['success'] == false) {
            return $result;
        }

        $destFold = $destFold . DS;

        //verifica se il file � stato inviato via HTTPPOST
        if (!is_uploaded_file($FILE['tmp_name'])) {
            $result['message'] = 'NO FILE UPLOADED via HTTPPOST ';
            return $result;
        }
        $result = $this->verifyFileType($FILE['type']);
        if ($result['success'] == false) {
            return $result;
        }
        $ext = $result['ext'];
        if (!move_uploaded_file($FILE['tmp_name'], $destFold . $filename . $ext)) {
            $result['message'] = 'new file not uploaded';
            return $result;
        }

        $this->resizeObj = new Resize($destFold . $filename . $ext);
        $this->resizeObj->resizeImage(600, 800);
        $this->resizeObj->saveImage($destFold . $filename . $ext, 75);
        
        $result['imgName'] = $filename .  $ext;
        $result['success'] = true;
        return $result; //restituisce l'immagine salvata
    }
    //salva un documento in formATO pdf
    public function uploadPDF(string $filename, string $destFold){
        //var_dump($_FILES['file'], $filename, $destFold);
        $config = new ConfigController();
        $dims = $config->getOrgParam('dimensions');
        $destFold = $destFold . DS;
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            if ($_FILES['file']['type'] != "application/pdf") {
                echo '<p>Il file non � un PDF</p>';
                die;
            } else if ($_FILES['file']['size'] >(int)$dims['maxFileUpload'][0]) {
                echo '<p class="error">File troppo grande. Dimensione massima: ' .  (int)$dims['maxFileUpload'][0] . 'KB</p>';
                die;
            } else {
                $result['success'] = move_uploaded_file($_FILES['file']['tmp_name'], $destFold . $filename . '.pdf');
                //var_dump($filename,$destFold);die;
            }
        }

        $result['imgName'] = $filename . '.pdf';
        $result['size'] = $_FILES['file']['size'];
        return $result;
    }
    //salva un documento in formato immagine
    public function updateDocImg(string $filename,string $destFold){
       if (isset($_FILES['file'])) {
            $files = $_FILES['file'];
            if (!is_array($files['name'])) {
                $files = array(
                    'name' => array($files['name']),
                    'type' => array($files['type']),
                    'tmp_name' => array($files['tmp_name']),
                    'error' => array($files['error']),
                    'size' => array($files['size'])
                );
            }
            if ($files['error'][0] === UPLOAD_ERR_OK) {
                $sourcePath = $files['tmp_name'][0];
                $result = $this->verifyFileType($files['type'][0]);
                $extension = $result['ext'];
                $result['success'] = move_uploaded_file($sourcePath, $destFold.DS . $filename .$extension);
            }
       }
        $result['imgName'] = $filename . $extension;
        $result['size'] = $_FILES['file']['size'];
        return $result; 
    }
    public function associateImgData($filename){
        try {
            $imageVM = new ImageVM();
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $imageVM->setType(finfo_file($finfo, $filename));
            list($fileType, $type) = explode('/', $imageVM->getType());

            $imageVM->setFile($filename);
            $imageVM->setBytes(filesize($filename));
            $imageVM->setAlt(basename($filename));
            $stats = stat($filename);
            $imageVM->setCreationDate(date('d/m/Y', $stats['mtime']));

            if ($fileType == 'image') {
                list($valH, $valW) = $this->exifImages($type, $filename);
                $imageVM->setHeight($valH);
                $imageVM->setWidth($valW);
            } else {
                $imageVM->setValidExif(false);
            }
        } catch (ExceptionHandler $ex) {
            error_log($ex->getMessage());
        }

        return $imageVM;
    }

    private function anyFile() {
        //verifica
        if (empty($_FILES)) {
            $result['message'] = 'NO FILE UPLOADED';
            $result['success'] = false;
        } else {
            $result = [
                'message' => '',
                'success' => true
            ];
        }
        return $result;
    }
    public function verifyFileType(string $info){
        $result = ['message' => '', 'success' => true, 'ext' => ''];
        // $finfo = finfo_open(FILEINFO_MIME);
        // $info = finfo_file($finfo, $FILE['tmp_name']);
        // $ext = '';
        if (stristr($info, 'pdf') == true) {
            $ext = '.pdf';
        }
        if (stristr($info, 'image/jpeg') == true) {
            $ext = '.jpg';
        }
        if (stristr($info, 'image/png') == true) {
            $ext = '.png';
        }
        if (stristr($info, 'image/gif') == true) {
            $ext = '.gif';
        }
        if (stristr($info, 'image/svg') == true) {
            $ext = '.svg';
        }
        if (stristr($info, 'image/ico') == true) {
            $ext = '.ico';
        }
        if (empty($ext)) {
            $result['message'] = 'wrong file type';
            $result['success'] = false;
            return $result;
        }
        $result['ext'] = $ext;
        return $result;
    }

    public function copyImage(string $fileName,string $destFold):array{
        //VEDI ANCHE CLASSE IMAGESERVICE
        //la dir di destinazione deve contenere il path assoluto  con $_SERVER[DOCUMENT_ROOT]
        //ma in real qui non funzione e si deve usare __DIR__ (vedi config)
        // $destFold = getConfig('resourcesDir') . $destFold;

        $config = new ConfigController();
        $dims = $config->getOrgParam('dimensions');

        $result = [
            'success' => false,
            'message' => 'PROBLEM SAVING IMAGE',
            'filename' => ''
        ];
        if (empty($_FILES)) {
            $result['message'] = 'NO FILE UPLOADED';
            return $result;
        }
        $FILE = $_FILES['file'];

        //verifica se il file � stato inviato via HTTPPOST
        if (!is_uploaded_file($FILE['tmp_name'])) {
            $result['message'] = 'NO FILE UPLOADED via httppost ';
            return $result;
        }
        //controlla il tipo di file ammesso
        $finfo = finfo_open(FILEINFO_MIME);
        $info = finfo_file($finfo, $FILE['tmp_name']);
        $ext = '';
        if (stristr($info, 'pdf') == true) {
            $ext = '.pdf';
        }
        if (stristr($info, 'image/jpeg') == true) {
            $ext = '.jpg';
        }
        if (stristr($info, 'image/png') == true) {
            $ext = '.png';
        }
        if (stristr($info, 'image/gif') == true) {
            $ext = '.gif';
        }
        if (stristr($info, 'image/svg') == true) {
            $ext = '.svg';
        }
        if (stristr($info, 'image/ico') == true) {
            $ext = '.ico';
        }
        if (empty($ext)) {
            $result['message'] = 'wrong file type';
            return $result;
        }
        //controlla dimensione del file
        if ($FILE['size'] >(int)$dims['maxFileUpload'][0]) {
            $result['message'] = 'size exceded';
            return $result;
        }
        
        if (!move_uploaded_file($FILE['tmp_name'], $destFold. DS . $fileName . $ext)) {          
            $result['message'] = 'new file not uploaded';
            return $result;
        }
      
        //todo  utilizzare una routne di resize per il salvataggio definitivo
        $newimg = '';
        switch ($ext) {
            case '.jpg':
                $newimg = \imagecreatefromjpeg($destFold .DS. $fileName . $ext);
                break;
            case '.gif':
                $newimg = \imagecreatefromgif($destFold .DS. $fileName . $ext);
                break;
            case '.png':
                $newimg = \imagecreatefrompng($destFold .DS. $fileName . $ext);
                break;
                //case '.pdf':
                //    $newimg = move_uploaded_file($destFold . $filename.$ext);
                //    break;
            case '.svg':
                //nb ifiles svg non vanno trattati come immagini, in quanto possono essere
                //direttamente visualizzati con html <img src="data:image/svg+xml;utf8,<?= $encodedSVG ?">

                //   $newimg = \imagecreatefromsvg($destFold . $filename.$ext);
                break;
            default:
        }
        if ($ext !== '.pdf') {
            imagedestroy($newimg);
        }

        $result['success'] = true;
        $result['message'] = '';
        $result['filename'] = $fileName . $ext;

        return $result;
    }

    public function transmitFilesToSave(string $filePath){
        // $dirPath = dirname($filePath);
        // var_dump($dirPath);die;
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        $fileBaseName=$_POST['fileName'];

        $this->uploadImage($fileBaseName,$filePath);
    } 
    private function rearrange($files){
        foreach ($files as $key1 => $val1) {
            foreach ($val1 as $key2 => $val2) {
                for ($i = 0, $count = count($val2); $i < $count; $i++) {
                    $newFiles[$i][$key2] = $val2[$i];
                }
            }
        }
        return $newFiles;
    }

    private function exifImages($type, $filename): array{
        $h = 0;
        $w = 0;
        // Verifica se il tipo dell'immagine è JPEG (2) o PNG (3)
        if ($type == IMAGETYPE_JPEG) {
            $exif = exif_read_data($filename, 0, true);
            // Verifica se il file ha dati EXIF validi
            if ($exif !== false) {
                // Aggiungi ulteriori informazioni EXIF necessarie
                $h = $exif['COMPUTED']['Height'];
                $w = $exif['COMPUTED']['Width'];
            }
        }
        //png non ha un exif uguale a quello di jpg perciò
        //useremo i valori di default già attribuiti 
        if ($type == IMAGETYPE_PNG) {
            // Usa getimagesize per ottenere le informazioni sull'immagine
            list($width, $height) = getimagesize($filename);
            $h = $height;
            $w = $width;
        }
        $exif = [$h, $w];
        return $exif;
    }

    private function contaImmagini($path){
        // Array delle estensioni di file immagine comuni
        $estensioniImmagine = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

        // Contatore per le immagini
        $conteggio = 0;

        // Verifica se la directory esiste
        if (!is_dir($path)) {
            return false;
        }

        // Apre la directory
        if ($handle = opendir($path)) {
            // Legge il contenuto della directory
            while (($file = readdir($handle)) !== false) {
                // Ignora . e ..
                if ($file != "." && $file != "..") {
                    // Ottiene l'estensione del file
                    $estensione = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                    // Verifica se l'estensione è nell'array delle estensioni immagine
                    if (in_array($estensione, $estensioniImmagine)) {
                        $conteggio++;
                    }
                }
            }
            closedir($handle);
        }

        return $conteggio;
    } 

    private  function isImageDuplicate($newHash, $destinationFolder){
        foreach (glob($destinationFolder . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $existingImage) {
            if (str_contains($existingImage,substr($newHash,0,12))) {
                return true;
            }
        }

        return false;
    }

    public function getTotalImageSize(array $imageCollection): int{
        try {
            $totalSize = 0;
            foreach ($imageCollection as $imageVM) {
                 $totalSize += $imageVM->getBytes(); 
            }
            return $totalSize;
        } catch (ExceptionHandler $e) {
            // Handle the ExceptionHandler appropriately, log it, or display a message.
            error_log("Error getting image sizes: " . $e->getMessage()); // Log the error for debugging.
            return 0; // Or throw the exception if you prefer to let calling code handle it.
        } catch (\Exception $e) { // Handle other possible exceptions (like file_exists error).
            error_log("Unexpected error getting image sizes: " . $e->getMessage());
            return 0; // Or throw the exception.
        }
    }
    public function formatBytes(int $bytes): string{
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    private function getMessage(bool $res, bool $status): string{
        // Se $res è FALSE, il risultato è sempre 'messaggio 3', indipendentemente da $status
        if ($res === false) {
            return 'Errore nel salvataggio files';
        } 
        // Se $res è TRUE, controlliamo il valore di $status
        else {
            // Se $res è TRUE e $status è TRUE
            if ($status === true) {
                return 'Files salvato correttamente';
            } 
            // Se $res è TRUE e $status è FALSE
            else { // $status === false
                return 'Alcuni files non sono stati salvati';
            }
        }
    }

}
