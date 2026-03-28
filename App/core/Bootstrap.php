<?php

require_once __DIR__ . DS.'helpers'.DS.'functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function loadClass($className)
{
    
    $dir = realpath(dirname(__DIR__));

    $filename1 = str_replace(
        ['App', 'Controllers', 'Models', 'DB\\', 'Helpers\\', 'Core\\'],
        ['app', 'controllers', 'models', 'db\\', 'helpers\\','core\\'],
        $className
    ) . '.php';
    $filename1 = str_replace('\\', DS, $filename1);
    $filename2 = str_replace('app', '', $filename1);
    $filename3 = $dir . DS. 'core' . DS . $filename2;
    $filename2 = $dir . DS . $filename2;
    $filename1 = $dir . DS . $filename1;

    if (file_exists($filename1)) {
        require $filename1;
    } elseif (file_exists($filename2)) {
        require $filename2;
    } else {
        require $filename3;
    }
}

spl_autoload_register('loadClass');