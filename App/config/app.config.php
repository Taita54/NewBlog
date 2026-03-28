<?php
namespace app\config;

use app\controllers\BaseController;
use app\controllers\HomeController;

$APP_CONFIG=[
    'routes'=>[
        'GET'=>[ 
            'legal/:id' => [Basecontroller::class, 'legalAdvertising'],
            'legal/popup' => [Basecontroller::class, 'popUp'],

            '/' => [HomeController::class, 'showIndex'],
            'main/:fl'=>[HomeController::class,'getFileToDisplay'],

            'gallery/:i/startpage'=>'app\SiteControllers\ImagesController@getStartPag', 
            'photogallery'=>'app\SiteControllers\PostController@getPhotos',
            'pub/:a/:id/show' => 'app\SiteControllers\PostController@getPubsArea',
            'pub/list' => 'app\SiteControllers\PublicationsController@getPublications',
            'pub/addnew' => 'app\SiteControllers\PublicationsController@addPublication',
            'pub/:id/update'=>'app\SiteControllers\PublicationsController@update',
            'pub/:od/:ob/getOrd'=>'app\SiteControllers\PublicationsController@getOrd',
            'pub/recsxpag'=>'app\SiteControllers\PublicationsController@getRecXPag',
            'pub/:i/startpage'=> 'app\SiteControllers\PublicationsController@getStartPag',
            'auth/login'=>'app\SiteControllers\LoginController@showlogin',
            'auth/signup'=>'app\SiteControllers\LoginController@showsignup',
            'auth/forgpwd'=>'app\SiteControllers\LoginController@forgetPassword',
            'conferma/cod/:tk'=>'app\SiteControllers\LoginController@verifyRegister',
            'auth/login/:tk'=>'app\SiteControllers\LoginController@showlogin',
            'users/list' => 'app\SiteControllers\UsersController@getAllUsers',
            'users/:i/startpage'=> 'app\SiteControllers\UsersController@getStartPag',
            'users/:od/:ob/getOrd'=>'app\SiteControllers\UsersController@getOrd',
            'comments/list'=>'app\SiteControllers\CommentsVerifyController@getAllComments',
            'comments/:i/startpage'=> 'app\SiteControllers\CommentsVerifyController@getStartPag',
            'comments/recsxpag'=>'app\SiteControllers\CommentsVerifyController@getRecXPag',
            'comments/:od/:ob/getOrd'=>'app\SiteControllers\CommentsVerifyController@getOrd',       
        ],
        'POST'=>[
            'comm/:id/:p/newcom'=>'app\SiteControllers\CommentController@newCom',
            'comm/:id/:p/update'=>'app\SiteControllers\CommentController@updtComm',
            'comm/:cp/:id/:p/delete'=>'app\SiteControllers\CommentController@deleteComm',
            'comm/:id/:p/newlike'=>'app\SiteControllers\CommentController@newLike',
            'comm/:id/:p/dellike'=>'app\SiteControllers\CommentController@delLike',
            'pub/save'=>'app\SiteControllers\PublicationsController@save',
            'pub/:id/store'=>'app\SiteControllers\PublicationsController@store',
            'pub/:id/delete'=>'app\SiteControllers\PublicationsController@delete',
            'pub/:a/supimg'=>'app\SiteControllers\PublicationsController@saveSuppImage',
            'pub/search'=>'app\SiteControllers\PublicationsController@searchPublications',
            'auth/login'=>'app\SiteControllers\LoginController@login',
            'auth/register'=>'app\SiteControllers\LoginController@register',
            'auth/:id/update'=>'app\SiteControllers\LoginController@register',
            'auth/logout'=>'app\SiteControllers\LoginController@logout',
            'auth/resetpwd'=>'app\SiteControllers\LoginController@resetPwd',
            'auth/delete'=> 'app\SiteControllers\LoginController@delete'                      
        ]
    ]
];

return $APP_CONFIG;