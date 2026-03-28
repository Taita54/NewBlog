<?php

namespace app\models\exceptions;

use Exception;

/**
 * Description of MyException
 *
 * @author giovi
 */
class ExceptionHandler extends Exception
{
    
    // Aggiungi proprietà personalizzate se necessario
    protected $details;
    protected $emptyFileMessage;
  

    public function __construct($message, $code = 0, $details = null, Exception $previous = null)
    {
        // Assicurarsi che tutto sia inizializzato correttamente
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    // Sovrascrivi il metodo __toString per fornire un output più dettagliato
    public function __toString(): string
    {
        return sprintf(
            "%s: [%d] %s\nDettagli: %s\nStack trace:\n%s",
            __CLASS__,
            $this->code,
            $this->message,
            $this->details ? json_encode($this->details) : 'Nessun dettaglio',
            $this->getTraceAsString()
        );
    }


    public function ApriFile($file)
    {
        $exMessage = '';
        if (!@fopen($file, 'r')) {

            //stabiliamo le modalità di gestione dell'errore
            // throw new Exception ('Il ' . $file . ' non si apre!!!');
            $exMessage = ('<p>Il file ' . $file . '<br>non è stato trovato!!!</p>');
        }
        return $exMessage;
    }

    public function NoData($dt)
    {
        $exMessage = '';
        if (!$dt) {
            $exMessage = ("<p>Nessun dato trovato</p> <br> {$dt->getMessage()} ");
        } else {
            $exMessage = $this->message;
        }
        return $exMessage;
    }
    // Metodo per ottenere i dettagli aggiuntivi
    public function getDetails()
    {
        return $this->details;
    }

    public function getEmptyFileMessage($fileName)
    {
        if (ENV == 'develop') {
            return "Il file '$fileName' è vuoto o non esiste.";
        } else {
            return 'Il file cercato è vuoto o non esiste.';
        }
    }

    public function getNotEnoughPermissionsMessage($fileName)
    {
        if (ENV == 'develop') {
            return "Non hai i permessi necessari per accedere al file '$fileName'.";
        } else {
            return 'Non hai i permessi necessari per accedere al file.';
        }
    }


}
