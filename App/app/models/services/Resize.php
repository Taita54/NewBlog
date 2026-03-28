<?php

namespace app\models\services;


# ========================================================================#
#
#  Author:    Jarrod Oberto
#  Version:   1.0
#  Date:      17-Jan-10
#  Purpose:   Resizes and saves image
#  Requires : Requires PHP5, GD library.
#  Usage Example:
#                     include("classes/resize_class.php");
#                     $resizeObj = new resize('images/cars/large/input.jpg');
#                     $resizeObj -> resizeImage(150, 100, 0);
#                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
#
#
# ========================================================================#


class Resize
{
    // *** Class variables
    private $image;
    public $width;
    public $height;
    private $imageResized;

    function __construct($fileName)
    {
        // *** Open up the file
        $this->image = $this->openImage($fileName);

        $this->image = $this->rotate($fileName);

        // *** Get width and height
        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    ## --------------------------------------------------------
    /**
     * Summary of openImage verifica il tipo di immagine da caricare
     * @param mixed $file
     * @return \GdImage|bool|resource
     */
    private function openImage($file)
    {
        // *** Get extension
        $extension = strtolower(strrchr($file, '.'));

        switch ($extension) {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $img = @imagecreatefromgif($file);
                break;
            case '.png':
                $img = @imagecreatefrompng($file);
                break;
            default:
                $img = false;
                break;
        }
        return $img;
    }
    ## --------------------------------------------------------
    /**
     * Summary of resizeImage riformula le nuove dimensioni dell'immagine prima di renderla persistente
     * sul server
     * @param mixed $newWidth
     * @param mixed $newHeight
     * @param mixed $option
     */
    public function resizeImage($newWidth = 600, $newHeight = 800, $option = "auto")
    {
        // *** Get optimal width and height - based on $option
        $optionArray = $this->getDimensions($newWidth, $newHeight, $option);

        $optimalWidth  = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];
        // *** Resample - create image canvas of x, y size
        if ($optimalWidth > 0 && $optimalHeight > 0) {
            $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        } else {
            $this->imageResized = imagecreatetruecolor(600, 800);
        }
        // var_dump($this->imageResized);

        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);

        // *** if option is 'crop', then crop too
        if ($option == 'crop') {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
        }
    }

    public function scaleImage($newWidth, $newHeight, $option = "auto")
    {
        //attenzione questo metodo non � ancora completo, occorre implementare il ricoalcolo della larghezza ottimale con restituzione di un oggetto GD
        $optionArray = $this->getDimensions($newWidth, $newHeight, $option);
        $optimalWidth  = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];

        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        $this->imageResized = imagescale($this->image, $optimalWidth, -1, IMG_BILINEAR_FIXED);
    }
    ## --------------------------------------------------------
    private function getDimensions($newWidth, $newHeight, $option)
    {

        switch ($option) {
            case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
                break;
            case 'portrait':
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
                break;
            case 'landscape':
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
                break;
            case 'auto':
                $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
            case 'crop':
                $optionArray = $this->getOptimalCrop($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    ## --------------------------------------------------------
    /**
     * Summary of getSizeByFixedHeight ricalcola la nuova larghezza, tenendo fissa l'altezza desiderata
     * @param mixed $newHeight
     * @return float
     */
    private function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }
    /**
     * Summary of getSizeByFixedWidth ricalcola l'altezza ottimizzata ion riferimento alla larghezza prefissata
     * @param mixed $newWidth
     * @return float
     */
    private function getSizeByFixedWidth($newWidth)
    {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }
    /**
     * Summary of getSizeByAuto ricalcola le dimensioni adattando la pi� piccola a quellfissata come maggiore
     * @param mixed $newWidth
     * @param mixed $newHeight
     * @return array
     */
    private function getSizeByAuto($newWidth, $newHeight)
    {
        if ($this->height < $this->width)
        // *** Image to be resized is wider (landscape)
        {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
        } elseif ($this->height > $this->width)
        // *** Image to be resized is taller (portrait)
        {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
        } else
        // *** Image to be resizerd is a square
        {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            } else if ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
            } else {
                // *** Sqaure being resized to a square
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    ## --------------------------------------------------------
    /**
     * Summary of getOptimalCrop esegue il crop dell'immagine riadattando il taglio della dimensione minore
     * in proporzione a quella maggiore
     * @param mixed $newWidth
     * @param mixed $newHeight
     * @return array<float>
     */
    private function getOptimalCrop($newWidth, $newHeight)
    {

        $heightRatio = $this->height / $newHeight;
        $widthRatio  = $this->width /  $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = $this->height / $optimalRatio;
        $optimalWidth  = $this->width  / $optimalRatio;

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    ## --------------------------------------------------------
    /**
     * Summary of crop esegeue il crop nelle dimensioni stabvilite
     * @param mixed $optimalWidth
     * @param mixed $optimalHeight
     * @param mixed $newWidth
     * @param mixed $newHeight
     */
    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    {
        // *** Find center - this will be used for the crop
        $cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
        $cropStartY = ($optimalHeight / 2) - ($newHeight / 2);

        $crop = $this->imageResized;
        //imagedestroy($this->imageResized);

        // *** Now crop from center to exact requested size
        $this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);
    }
    ## --------------------------------------------------------
    /**
     * Summary of saveImage salvataggio immagine ottenuta
     * @param mixed $savePath
     * @param mixed $imageQuality
     */
    public function saveImage($savePath, $imageQuality = "100")
    {
        // *** Get extension
        $extension = strrchr($savePath, '.');
        $extension = strtolower($extension);
        switch ($extension) {
            case '.jpg':
            case '.jpeg':
                if (imagetypes() & IMG_JPG) {
                    //var_dump($this->image,$savePath);die('219');
                    imagejpeg($this->imageResized, $savePath, $imageQuality);
                }
                break;

            case '.gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->imageResized, $savePath);
                }
                break;

            case '.png':
                // *** Scale quality from 0-100 to 0-9
                $scaleQuality = round(($imageQuality / 100) * 9);

                // *** Invert quality setting as 0 is best, not 9
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);
                }
                break;

                // ... etc

            default:
                // *** No extension - No save.
                break;
        }

        imagedestroy($this->imageResized);
    }
    ## --------------------------------------------------------
    /**
     * Summary of rotate rotazioni fisse dell'immagine
     * @param mixed $file
     * @return \GdImage|bool|resource
     */
    private function rotate($file)
    {
        $img_info = getimagesize($file);
        switch ($img_info['mime']) {
            case 'image/jpg':
            case 'image/jpeg':

                $img = imagecreatefromjpeg($file);
                $exif = exif_read_data($file);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    if ($orientation != 1) {
                        $deg = 0;
                        switch ($orientation) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if ($deg) {
                            $img = imagerotate($img, $deg, 0);
                        }
                        //imagejpeg($img, $filename, 95);
                    }
                }
                break;
            case 'image/png':
                $img = imagecreatefrompng($file);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($file);
                break;
        }
        return $img;
    }
    /**
     * Summary of orientation inversione a specchio
     * @param mixed $file
     * @return mixed
     */
    private function orientation($file): string
    {
        $orientation = 'n';
        $exif = exif_read_data($file);
        if ($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
        }
        return $orientation;
    }
    ## --------------------------------------------------------

}
