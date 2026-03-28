<?php
function getConfig($param, $default = null){
    $resources = require 'config/config.php'; //$resources = require '../config/config.php';
    return array_key_exists($param, $resources) ? $resources[$param] : $default;
}

function handleAjaxRequest(){
    // Verifica se la richiesta è AJAX
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {

        // Logica per gestire la richiesta AJAX
        $data = [
            'status' => 'success',
            'message' => 'Questa è una risposta AJAX.',
            //   'content' => generatePartial($partial) // Una funzione che restituisce contenuti da inviare
        ];

        // Imposta l'header Content-Type appropriato per JSON
        header('Content-Type: application/json');

        // Invia una risposta JSON
        echo json_encode($data);
        exit;
    } else {
        // Non è una richiesta AJAX, gestire normalmente
        header("HTTP/1.0 400 Bad Request");
        echo 'Richiesta non valida';
        exit;
    }
}

function view(string $view, array $data = []): string{
    extract($data, EXTR_OVERWRITE);
    ob_start();
    require   'app' . DS . 'views' . DS . $view . '.php';//__DIR__ . DS . '..' . DS . 
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function getOtherPartials(string $partial, array $data = []){
    extract($data);
    ob_start();
    require  __DIR__ . DS . '..' . DS . 'app' . DS . 'views' . DS . 'otherPartials' . DS . $partial . '.php';
    $partial = ob_get_contents();
    ob_end_clean();
    return $partial;
}

function jsonRep(array $data=[]){
    // $data = [];
    extract($data, EXTR_OVERWRITE);
    ob_start();
    require 'jsonRepository' . DS . 'paramRepos.json';
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function report(string $rpt, string $rptPath=''): array{
    // extract($data, EXTR_OVERWRITE);
    $file_path= $rptPath . DS . $rpt;
    $content = [];
    if(file_exists($file_path)){
        ob_start();
        // // require __DIR__ . DS . '..' . DS . 'helpers' . DS . 'jsonRepository' . DS . $rpt . '.json';
        // require 'jsonRepository'.DS.$rpt.'.json';
        //$content = ob_get_contents();
        $json_data = file_get_contents($file_path);
        $content = json_decode($json_data, true);
        // require $file_path;
        // $content =  ob_get_contents(); // Assuming $data is defined in the included file
        ob_end_clean();
    }
    return $content;
}
/**
 * mostra il messaggio di $_SESSION['message'] nella partial a scomparsa
 * @return
 */
function getInfo(){
    if (!empty($_SESSION['message'])) {
        $info = getOtherPartials('_message');
    } else {
        $info = null;
    }
    return $info;
}

function getPhoto($imgDir, $f){
    if (!empty($f) && !is_null($f)) {
        $foto = $imgDir . DS . $f ;
    } else {
        $foto = WEBRESOURCES_DIR. 'images' . DS . 'backgrounds' . DS . 'logo.png';
    }
    return $foto;
}

function getPhotoLink($link){
    $target = WEBRESOURCES_DIR . 'images' . DS . 'backgrounds';
    symlink($target, $link);
    return readlink($link);
}

#region ************************************** GESTIONE DATE *********************************************
/**
 * blocco di codice html per l'input di date
 * @param string $dt
 * @param string $dat
 */

function hereDate(string $dt, string|null $dat, ?string $oncng = null, ?string $cl = null){
    // Validazione della data
    $dat = $dat ? date('Y-m-d', strtotime(str_replace('/', '-', $dat))) : date('Y-m-d');
    if (!strtotime($dat)) {
        $dat = date('Y-m-d'); // Imposta la data odierna in caso di errore
        // In alternativa, si potrebbe gestire l'errore in modo più esplicito.
    }

    $datForDisplay = date('d/m/Y', strtotime($dat)); // Formattazione per la visualizzazione

    echo <<<EOS
        <div class="date-picker-wrapper form-control" onclick="document.getElementById('$dt-date-input').click();">
            <input type="date" id="$dt-date-input" class="date_input $cl" name="$dt" value="$dat" onchange="setCorrect(this, '$dt-display');$oncng" />
        </div>
EOS; // <-- E questo EOS; deve essere sulla prima colonna della riga.
           // <input type="text" id="$dt-display" class="date-display" readonly value="$datForDisplay" />
            // <span class="arrow">&#9660</span>
}

/**
 * restituisce la data di input in formato italiano
 * @param string $dt
 * @return string
 */
function itaDate(?string $dt = ''): string
{
    if (!empty($dt) && strtotime($dt) !== false) {
        return date('d/m/Y', strtotime($dt));
    }

    return '';
}

/**
 * restituisce la data di input in formato italiano con aggiunta di ore minuti secondi
 * @param string $dt
 * @return string
 */
function itaDateTime(?string $dt = ''){
    if (!empty($dt)) {
        $newDt = date('d/m/Y H:i:s', strtotime((string)$dt));
    } else {
        $newDt = '';
    }
    return $newDt;
}

/**
 * Summary of dateInterval in base al valore di $interv, determina se $date è più piccolo
 * o più grande della data odierna
 * @param mixed $date   // la data è in formato timestamp = numero di 10 cifre
 * @param mixed $interv //format PT1D = 1gionro, PT1H =1 ora  etc.
 *
 * @return bool //true se alla data e ora corrente non è ancora trascorso l'intervallo stabilito
 */
function getInterval($date, $interval){
    // Creazione oggetto DateTime dalla data specificata
    $startDate = new DateTime($date);

    // Creazione oggetto DateInterval dall'intervallo specificato
    $intervalDuration = new DateInterval($interval);

    // Somma l'intervallo alla data iniziale
    $endDate = clone $startDate;
    $endDate->add($intervalDuration);

    // Ottieni la data corrente
    $currentDate = new DateTime();

    // Verifica se la data corrente è oltre la data finale calcolata
    return $currentDate >= $endDate;
}

/**
 * verifica se un valore è una data e restituisce una data in formato Y-m-d o null
 * @param string $dat
 * @return string|null
 */

function dateRevert(string $dtn):string|null{
    $data = DateTime::createFromFormat('d/m/Y', $dtn);
    if ($data === false) {
        // Gestisci l'errore.  Ad esempio:
        error_log("Errore nella conversione della data: $dtn"); // Registra l'errore in un log
        return null; // O lancia un'eccezione, dipende dalla tua strategia di gestione degli errori
    } else {
        $verifiedDate = $data->format('Y-m-d');
        return $verifiedDate;
    }
}
/**
 * verifica se un valore proposto è di tipo data
 * @param string $date
 * @param string $format
 * @return boolean
 */
function validateDate(string|null $date, $format = 'd/m/Y'){
    if(!is_null($date)){
        $d = DateTime::createFromFormat($format,(string) $date);
        return $d && $d->format($format) == $date;
    }else{
        return false;
     }   
}

function getAnnoSportivo(string $data): string{
    if(!str_contains($data,'-') && strlen($data) == 4){
        //se la data è un anno, allora lo trasformo in anno sportivo
        $year = $data;
        $nextYear = $year+1;
        return $year.'-'.substr($nextYear, -2);
    }else{
        return $data;
    }
}

#endregion ************************ FINE GESTIONE DATE **************************************

function testPattern($value,$pattern):bool{
    $result=false;
    if (preg_match($pattern, $value)) {$result=true;}
    return $result;
}

#region ************************************ GESTIONE  spostamenti tra pagine (RETURN HERE) **************************


function redirect(?string $url = '/'){
    header("Location:$url");
}

function getReturnHere(){
    return   $_SESSION['returnHere']; 
}  

function setReturnHereCaller($caller){
    $_SESSION['returnHere']  = str_replace('_', '/', $caller);
}

function setReturnHere($url){
    $_SESSION['returnHere']= $url;
}

#endregion *********************************************************************************

#region *********************************
/**
 * Class casting: fa il cast di un oggetto ad uno di tipo specifico se esiste 
 * oppure ad una stringa se il valore non esiste
 *
 * @param string|object $destination 
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject){
    if (is_string($destination)) {
        $destination = new $destination();
    }

    $sourceReflection = new \ReflectionObject($sourceObject);
    $sourceProperties = $sourceReflection->getProperties();

    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);

        // Attempt to use a setter method
        $setterMethod = 'set' . ucfirst($name); //creates setID, setIDPersona etc.
        if (method_exists($destination, $setterMethod)) {
            $destination->$setterMethod($value);
        } else {
            // Fallback to public properties, if setter method doesn't exist.
            $destination->$name = $value;
        }
    }
    return $destination;
}
/**
 * Summary of castToSpecificObject fa il casting di un oggetto da una classe standard ad una di tipo specifico
 * questa funzione serve solo come confronto della precedente
 * @param stdClass $stdClassObject
 * @param object $targetObject
 * @return object
 */
function castToSpecificObject(stdClass $stdClassObject, object $targetObject): object{
    $reflection = new \ReflectionClass($targetObject);
    $methods    = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

    foreach ($methods as $method) {
        $methodName = $method->getName();
        if (strpos($methodName, 'set') === 0) {
            $propertyName = lcfirst(substr($methodName, 3)); // Estrae il nome della proprietà

            if (property_exists($stdClassObject, $propertyName)) {
                $value = $stdClassObject->{$propertyName};

                //Gestione del tipo di dato - questa parte può essere migliorata
                switch (gettype($value)) {
                    case "integer":
                        $value = (int) $value;
                        break;
                    case "double":
                        $value = (float) $value;
                        break;
                    case "boolean":
                        $value = (bool) $value;
                        break;
                    default:
                        $value = (string) $value;
                }

                try {
                    $reflection->getMethod($methodName)->invokeArgs($targetObject, [$value]);
                } catch (\ReflectionException $e) {
                    // Gestisci l'eccezione, ad esempio loggandola:
                    error_log("Errore durante il casting di {$propertyName}: " . $e->getMessage());
                }
            }
        }
    }
    return $targetObject;
}
/**
 * Summary of getTextBetween: cerca un testo all'interno di una frase
 * @param mixed $string
 * @param mixed $startWord
 * @param mixed $endWord
 * @return string
 */
function getTextBetween($string, $startWord, $endWord){
    $startPos = strpos($string, $startWord);
    if ($startPos === false) {
        return ''; // start word not found
    }
    $startPos += strlen($startWord); // Move past the start word

    $endPos = strpos($string, $endWord, $startPos);
    if ($endPos === false) {
        return ''; // end word not found
    }

    return substr($string, $startPos, $endPos - $startPos);
}
/**
 * Summary of getValueByKey ritorna la chiave di un array come valore
 * @param mixed $array
 * @param mixed $key
 * @return string
 */
function getValueByKey($array, $key):string{
    $result='';
    if (array_key_exists($key, $array)) {
        $result = $array[$key];
    }
    return $result;
}
/**
 * Summary of shuffledArry: mischia il contenuto di un array
 * @param mixed $array
 * @return array
 */
function shuffledArry($array){
    // Get array length
    $count = count($array);
    // Create a range of indicies
    $indi = range(0, $count - 1);
    // Randomize indicies array
    shuffle($indi);
    // Initialize new array
    $newarray = array($count);
    // Holds current index
    $i = 0;
    // Shuffle multidimensional array
    foreach ($indi as $index) {
        $newarray[$i] = $array[$index];
        $i++;
    }
    return $newarray;
}
/**
* Summary of getRndImg: restituisce un'immagine casuale da una cartella specificata
 * @param string $path
 * @return string
 */
function getRndImg(string $path="") {
    $arr = [];
    $dir=getConfig('resourcesDir').$path;
    $webresourcesDir = getConfig('webresourcesDir') . $path ;

    if (is_dir($dir)) {
        $dh = opendir($dir);
            while (($file = readdir($dh)) !== false){
            	if ($file !== '.' && $file !== '..'){
                      $el = $dir . DS. $file;
                      if (is_image($el)){
                      array_push($arr,  $dir.DS.$file);
                     }
                }
            }
            closedir($dh);       
    }
    $imgres = $arr[mt_rand(0, count($arr)-1)];
    return $imgres;
}
/**
 * Summary of is_image: verifica se un file è un'immagine
 * @param string $path
 * @return boolean
 */
function is_image($path) {
    $a = getimagesize($path);
    $image_type = $a[2];

    if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
        return true;
    }
    return false;
}
/**
 * Summary of setStartMenu: imposta la variabile di sessione startMenu
 * @param mixed $startMenuValue
 */
function setStartMenu($startMenuValue){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $startMenuValue = $_POST['startMenu'];
        
        // Imposta la variabile di sessione
        $_SESSION['startMenu'] = $startMenuValue;
        
        // Rispondi al client (opzionale)
        echo "Sessione startMenu impostata su: " . htmlspecialchars($startMenuValue);
    }
}
/**
 * Summary of plurale: restituisce la forma plurale di una parola
 * @param string $string
 * @return string
 */
function plurale($string){
        $endvocal=['a','e','o'];
        $endletter=['m','t'];
        $plurale=$string;
        if(in_array(substr($string, -1), $endvocal)){
            $plurale=substr($string, 0, -1).'i';
        }elseif(in_array(substr($string, -1), $endletter)){
            $plurale=$string.'s';
        }

        return lcfirst($plurale);
    }

function calculateCols($containerWidth, $imageWidth){
    $cols = floor($containerWidth / $imageWidth);
    return max(1, $cols); // Assicura che ci sia almeno una colonna
}
#endregion -------------------------------