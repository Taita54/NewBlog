<?php

class App{
	public function __construct()
	{
		// Inizializza la sessione
		if (!session_start()) {
			ini_set('session.gc_maxlifetime', 20 * 60);
			ini_set('session.gc_probability', 50);
			ini_set('session.gc_divisor', 100);
			session_save_path(WEBRESOURCES_DIR . 'LOGSadvices' . DS);
			session_set_save_handler('s_open', 's_close', 's_read', 's_write', 's_destroy', 's_gc');
			session_start();
		}
		// Carica le configurazioni
		require_once __DIR__ . '/config/config.php';
		require_once __DIR__ . '/config/database.php';
		// Inizializza il router
		$router = new Router();
		$router->dispatch();
	}

	public static function init(array $config){
	}
}