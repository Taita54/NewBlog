<?php
namespace App\Helpers;

use App\Helpers\Cesare;
use DateTime;

class Normalizer
{
    function NormalizedUserName(string $var){

        $result='';

        $pattern="/^[a-zA-Z0-9_]{1}$/";
        $arVal=str_split($var);
        $i=0; $s='';
        foreach($arVal as $c){
            if(!preg_match($pattern,$c)){
                break;
            }else{
                $s.=$c;
                $i++;
            }
        }
        if($i==strlen($var)){
            $result=strtoupper($s);
        }
        unset($c);
        return $result;
    }

    function NormalizedEmail(string $var){

        $result='';

        $pattern="/^[a-zA-Z0-9\-\_@.]{1}$/";
        $arVal=str_split($var);
        $i=0; $s='';
        foreach($arVal as $c){
            if(!preg_match($pattern,$c)){
                break;
            }else{
                $s.=$c;
                $i++;
            }
        }
        if($i==strlen($var)){
            $result=strtoupper($s);
        }
        unset($c);
        return $result;
    }
    /**
     * Summary of SecurityStamp usa 'algoritmo cesare sulla data corrente 
     * per creare una stringa cifrata di 32 caratteri
     * @return string
     */
    function SecurityStamp()
    {

        $chiavePos= mt_rand(8,32);

        $testo=getCurrentDate();
        $str= $testo->format('U');

        $cifra=new Cesare($chiavePos);
       // $cifra=$cifra->Cesare($chiavePos);
        return $cifra->cifratura($str);

    }
    /**
     * Summary of ConcurrencyStamp quest afunzione non viene utilizzata
     * @return string
     */
    function setUserIdstr()
    {
        $resPattern=array(8,4,4,4,12);
        $result='';

        foreach($resPattern as $c)
        {
            $chunk='';
            for($i=0;$i<$c;$i++)
            {
                $chr='';
                $rndChrType=mt_rand(0,1);
                if($rndChrType===0)
                {
                    $chr=chr(mt_rand(97,122));
                }
                else
                {
                    $chr=chr(mt_rand(48,57));
                }
                $chunk =$chunk.$chr;
            }
            $result =$result.$chunk.'-';
        }
        return substr($result, 0,-1);
    }
    function ConcurrencyStamp()
    {
        // Ottieni la data e ora correnti in un formato standard
        $currentDateTime = (new DateTime())->format('Y-m-d H:i:s');

        // Genera un valore casuale
        $randomValue = bin2hex(random_bytes(16)); // Genera 16 byte di valore casuale

        // Combina il timestamp e il valore casuale
        return hash('sha256', $currentDateTime . $randomValue);
    }
    /**
     * Summary of generate_activation_code genera un codice univoco di attivazione per un utente
     * che conferma la prorpia identit�
     * @return string
     */
    function generate_activation_code():string
    {
        return bin2hex(random_bytes(16));
    }

    function normalizeToUp(string $text):string{
        return strtoupper($text);
    }

    function isConcurrencyStampValid($providedStamp, $currentStamp)
    {
        return hash_equals($providedStamp, $currentStamp);
    }
}
