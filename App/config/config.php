<?php

$env = 'develop'; //ambiente= production / develop
defined('ENV')?null:define('ENV',$env);
defined('E_STRICT')?null:define('E_STRICT',2048);
defined('DS') ? null :define('DS', DIRECTORY_SEPARATOR); //definisce lo slash tra i valori di una url

$http= $env === 'develop' ? 'http:' : 'https:'; //codifica http o https in base all'ambiente,
$baseResDir = $http . DS . DS . $_SERVER['SERVER_NAME']; //pone http(o https)//$_ SERVER(corrente)
$greenIsland = $env == 'develop' ? 'www.gdmsoftandpict.it' . DS : null; //il nome del sito viene automaticamente impostato dal server di produzione

defined('RESOURCES_DIR')? null:define('RESOURCES_DIR',__DIR__.DS.'..'.DS.'risorse'.DS);
defined('WEBRESOURCES_DIR')? null:define('WEBRESOURCES_DIR', $baseResDir . DS . $greenIsland . 'risorse' . DS);
defined('WEBEDITOR_DIR')?null:define('WEBEDITOR_DIR', $baseResDir . DS . $greenIsland .'helpers'.DS. 'ckeditor' . DS);
defined('WEBMANAGER_DIR')?null:define('WEBMANAGER_DIR',$baseResDir . DS . $greenIsland .'helpers'.DS. 'filemanager' . DS);

return[
    'recordsxpag'=>10,//$recordsxpag,
    'page'=>1,//$page,
    'thumbsxpag'=>20,
    'recordsxpagoptions'=>[5,10,20,30,40,50],
    'orderByColumns'=>['id','section','type','title','date_created','size'],
    'sectionsBlog'=>['I'=>'Grafica','B'=>'Storie','C'=>'Coding','S'=>'Sport'],
    'sectionsFolds'=>['I'=>'immagini','B'=>'Blog','C'=>'Coding','S'=>'Sport','T'=>'Testi'],
    'pubTypes'=>['imm'=>'Immagini','pdf'=>'Pdf','html'=>'Hyper Text','txt'=>'Testi','svg'=>'Vettoriali','vid'=>'Clip video'],
    'linksNavigator'=>5,
    'maxFileUpload'=> ini_get('upload_max_filesize'),
    'resourcesDir'=>RESOURCES_DIR,
    'webresourcesDir'=>WEBRESOURCES_DIR,
    //'DS'=>DS,
    //'webeditorDir'=>WEBEDITOR_DIR,
    //'webmanagerDir'=>WEBMANAGER_DIR,
    'avatar_width'=>120,
    'thumbnail_width'=>120,
    'preview_width'=>70,
    'roletypes'=>['user','editor','admin'],
    'basemail'=>'giovidime@gmail.com'//modificare dopo l'inserimento nel sito 
];