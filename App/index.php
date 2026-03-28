<?php
use core\Router;
use app\models\services\UserService;

spl_autoload_register(function($class){
	$prefix= 'app\\';
	$baseDir=__DIR__.'/';

    if (strpos($class, $prefix) === 0) {
        $className = str_replace($prefix, '', $class);
        $filePath = $baseDir . '/app/' . str_replace('\\', '/', $className) . '.php';
       
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            error_log("File NON trovato: " . $filePath); // Log dell'errore
        }
    }
    // Se non è in 'App\', prova a cercare in 'helpers/' o in altre cartelle
    // Supponiamo che tutte le classi Helper siano in 'helpers/'
    $helperPath = $baseDir . DS
                . 'helpers' . DS
                . str_replace('\\', DS, $class)
                . '.php';

    if (file_exists($helperPath)) {
        require_once $helperPath;
    }
});

require_once 'core/Router.php';
require_once 'config/config.php';
require_once 'config/database.php'; // Include la configurazione del database
require_once 'config/app.config.php'; // Include la configurazione delle rotte
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/fpdf.php';
include_once __DIR__ . '/helpers/getid3/getid3.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/sessionFunctions.php';
require_once __DIR__ . '/helpers/normalizer.php';
require_once __DIR__ . '/helpers/cesare.php';

// Verifica della sessione utente PRIMA del routing
if (!session_start()) {
    ini_set('session.gc_maxlifetime', 20 * 60);
    // imposto la probabilit� di avvio del garbage collector al 50%
    ini_set('session.gc_probability', 50);
    ini_set('session.gc_divisor', 100);
    session_save_path(WEBRESOURCES_DIR. 'LOGSadvices' . DS);
    // dichiaro quali funzioni gestiranno gli eventi delle sessioni
    session_set_save_handler('s_open', 's_close', 's_read', 's_write', 's_destroy', 's_gc');

    session_start();
}// Avvia la sessione

if (isset($_SESSION['user_id'])) {
    // Utente già loggato
    try {
        $userService=new UserService($pdo);
        $user = $userService->getUserBy('id',$_SESSION['user_id']); // Recupera i dati dell'utente dal database
        if ($user) {
            $_SESSION['curUser'] = $user; // Salva le informazioni dell'utente nella sessione
        } else {
            session_destroy(); // Distruggi la sessione se l'utente non esiste
        }
    } catch (PDOException $e) {
        error_log("Errore durante il recupero dell'utente dalla sessione: " . $e->getMessage());
        session_destroy(); //Distruggi la sessione in caso di errore
    }
}

if (!isset($APP_CONFIG) || !is_array($APP_CONFIG) || !isset($APP_CONFIG['routes'])) {
    die("Errore: APP_CONFIG non è definito o non contiene le rotte.");
}

$router = new Router($APP_CONFIG['routes']);
$route = $router->dispatch();

if (is_array($route) && count($route) > 1) {
    list($controllerClass, $controllerMethod) = $route;
    $params = $route[2] ?? []; // parametri eventuali

    $controller = new $controllerClass($pdo);

    try {
        if(str_contains($controllerClass,'helpers')) {
            var_dump($controller);die();
        }
        $controller->$controllerMethod(...$params); // Usa ...$params per passare i parametri
    } catch (\Exception $e) {
        error_log("Errore durante l'esecuzione del controller: " . $e->getMessage());
        http_response_code(500);
        echo "Errore interno del server.";
    }
} else {
    http_response_code(404);
    echo "404 - Pagina non trovata";
}