<?php
namespace App\Helpers;
/**
 * Description of Cesare
 *
 * @author giovi
 */
class Cesare {

    //put your code here
    var $alfabeto = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0','1', '2', '3', '4', '5', '6','7','8','9');
    var $chiavePos;
    var $chiave;
    var $min;
    var $max;



    public function __construct(int $min =1,int $max=32) {
        $this->min= $min;
        $this->max= $max;
    }

    // La chiave � un valore numerico da 1 a 32
    function Cesare($chiavePos) {
        $chiave = mt_rand($this->min,$this->max);
        if ($chiave > $this->max)
        {
            $this->chiave = $chiave % $this->max+1;
        }
        else
        {
            $this->chiave = $chiave;
        }
        $this->chiavePos = $chiave;
    }

    // Metodo per sostituzione lettera in fase di cifratura
    function sostituisciLettera($lettera) {
        (int) $index = ($lettera) % $this->max+1;
        return $this->alfabeto[$index];
    }
    
    function getindexfromchar($lettera){
        $index=0;
        foreach($this->alfabeto as $c)
        {
           if($c==$lettera){
               break;
           }
           $index++;
        }
        return $index;
    }

    // Metodo per sostituzione lettera in fase di decifratura
    function ripristinaLettera($lettera) {
        if (in_array(strtoupper($lettera), $this->alfabeto))
        {
            $posizione = array_search(strtoupper($lettera), $this->alfabeto);
            if (($posizione - $this->chiave) >= 0)
            {
                return strtolower($this->alfabeto[($posizione - $this->chiave)]);
            }
            else
            {
                return strtolower($this->alfabeto[(count($this->alfabeto) + ($posizione - $this->chiave))]);
            }
        }
        else
        {
            return $lettera;
        }
    }

    // Metodo per cifratura di un testo in chiaro
    function cifratura($testo) {
 /*
  * TODO, rendere la criptazione con scambio di caratteri, attraverso l'uso della Xor
  * QUINDI ELIMINARE QUESTO COMMENTO;
  */
        $key         = $this->chiave;
        $txt_cifrato = "";
//echo ' $testo in chiaro '.$testo.' poskey '.$this->chiavePos.' chiave '.$key.'<br/>';

        for ($i = 0; $i <= 2; $i++) {
            $carattere         = mt_rand(1, 33);
            $carattere_cifrato = $this->sostituisciLettera($carattere);
            $txt_cifrato       = $txt_cifrato . $carattere_cifrato;
        }
        //indirizzo chiave
        $carattere   = $this->sostituisciLettera($this->chiavePos);
        $txt_cifrato = $txt_cifrato . $carattere;
        for ($i = 0; $i < 4; $i++) {
            $carattere         = mt_rand(1, 33);
            $carattere_cifrato = $this->sostituisciLettera($carattere);
            $txt_cifrato       = $txt_cifrato . $carattere_cifrato;
        }
        $curCol = 8;
        for ($i = 0; $i < strlen($testo); $i++) {
            if ($curCol == $this->chiavePos)
            {
                $carattere         = $key;
                $carattere_cifrato = $this->sostituisciLettera($carattere);
                $txt_cifrato       = $txt_cifrato . $carattere_cifrato;             
                $curCol++;$i--;

            }else{
                $carattere         = substr($testo, $i, 1);
                $carattere_cifrato = $this->sostituisciLettera($carattere);
                $txt_cifrato       = $txt_cifrato . $carattere_cifrato;
                $curCol++;                
            }
        }
        
        for ($i = strlen($txt_cifrato); $i < 32; $i++) {
            if ($curCol == $this->chiavePos)
            {
                $carattere         = $key;
            }else{
                $carattere         = mt_rand(1, 33);
            }
            
            $carattere_cifrato = $this->sostituisciLettera($carattere);            
            $txt_cifrato       = $txt_cifrato . $carattere_cifrato;
           // $curCol++;
        }

        //$txt_cifrato = $txt_cifrato;
        return $txt_cifrato;
    }

    // Metodo per decifratura di un testo cifrato
    function decifratura($testo) {

        $txt_chiaro = "";
        $colKey=substr($testo,3,1);
        $colInd=$this->getindexfromchar($colKey);        
        $keyChr=substr($testo,$colInd,1);
        $keyVal= $this->getindexfromchar($colKey);
        $curCol=0;
        //indaga solo sui caratteri significativi
        for ($i = 8; $i < strlen($testo); $i++) {
            if($i!==$colInd){
                $curCol++;//aggiunge il carattere in coda alla decriptazione ad esclusione della key
                $carattere        = substr($testo, $i, 1);
                $carattere_chiaro = $this->getindexfromchar($carattere);
                $txt_chiaro       = $txt_chiaro . $carattere_chiaro;   
            }
            //se il testo cercato ha raggiunto le lunghezza stabilita esce
            if($curCol==10)
            {
                break;
            }
        }
        return $txt_chiaro;
    }

}