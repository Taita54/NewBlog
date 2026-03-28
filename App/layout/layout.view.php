<!DOCTYPE html>
<html lang="en" class="h-100">
    <head>
        <!-- Required meta tags always come first -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--<meta http-equiv="Content-Security-Policy" content="default-src https:">-->
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>GdmSoftandPict</title>
        <!-- Bootstrap CSS -->
        <!--<link href="/css/bootstrap.css" rel="stylesheet">-->
        <link href="/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> 
        <link href="/css/commentsList.css" rel="stylesheet">
        <link href="/css/loginAndCommentAdd.css" rel="stylesheet">
        <link href="/css/loginpage.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        <link href="<?= $webresourcesDir . 'fontawesome'.DS.'css'.DS.'all.css' ?>" rel="stylesheet">
        <!-- Favicons -->
        <link rel="shortcut icon" href="<?= $webresourcesDir . 'Testi'.DS.'favicon'.DS.'favicon.ico' ?>">

    </head>

    <body>
        <nav class="navbar navbar-expand-sm navbar-light fixed-top bg-primary" id="main_nav">
            <a class="navbar-brand" href="#">
                <img src="<?= $webresourcesDir . 'Testi\icons\favicon.png' ?>" 
                     width="30" height="30" alt="" loading="lazy">gdmsoftandpict 
            </a> 

            <ul class="nav navbar-nav" >
                <li class="nav-item active">
                    <a class="nav-link" href="/">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Coding</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Sport</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Stories</a>
                </li>
                <?php if (isUserLoggedin() && isUserAdmin()) { ?>
                    <li class="nav-item">
                         <a class="nav-link" href="">Reserved Area</a>
                    </li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav flex-row justify-content-between ml-auto">
                <form action="" class="form-inline mt-1 mt-ms-0" method="POST" id="searchForm">
                        <span class="nav-item">
                            <input class="inline" type="search" name="search" value="" placeholder="Search">
                        </span>
                    <button onclick="document.forms.searchForm.page.value=1;document.forms.searchForm.submit()"
                            class="btn btn-outline-light btn-sm" type="submit">   
                            <i class="fas fa-search"></i>
                        </button>
                </form>

                     <li class="nav-item">
                        <form class="form-inline mt-1 mt-ms-0" method="GET" action="/pub/recsxpag" id="pagForm">
                            <select name="recordsxpag" id="recordsxpag" class="mr-1"
                                    onchange="document.forms.pagForm.submit()" >
                                <option value="">rxp</option>
                                <?php foreach (getConfig('recordsxpagoptions') as $val) { ?>
                                <option <?= recordsxpag() == $val ? 'selected' : ''; ?> value="<?=$val?>"><?= $val ?></option>
                                <?php } ?>
                            </select>
                        </form>
                    </li>
 
                    <li class="nav-item">
                        <div class="col-sm-10">                                
                            <div class="custom-avatar-file">
                                <img name="miniavatar" class="miniavatar" src="<?=$avatarImg?>" width="<?=$prevavatarW?>" alt="">   
                            </div>
                        </div>
                    </li>
                    <li class="nav-link">
                        <h5> Welcome <?= getUserLoggedInFullname() ?></h5>
                    </li>
                    <li class="nav-link">
                        <a href="" class="btn btn-sm btn-outline-info">UPDATE</a>
                    </li>
                    <li class="nav-item">
                        <form class="form-inline mt-2 mt-ms-0" role="form" method="post" action="">
                            <input type="hidden" name="action" value="logout">
                            <button  class="btn btn-sm btn-dark">LOGOUT</button>
                        </form>
                    </li>

                    <li class="nav-link">
                        <a href="" class="btn btn-sm btn-outline-info">REGISTER</a>
                    </li>
                    <li class="nav-link">
                        <a href="" class="btn btn-sm btn-success">LOG IN</a>
                    </li>
            
            </ul>
        </nav>

        <main role="main">
            
            <div class="container">
                <?= $this->content ?>
            </div><!--FINE DIV CLASS CONTAINER -->

        </main>      
        <footer class="container-fluid">
            <p class="float-right"><a href="#">Back to top</a></p>
            <p>&copy; 2020-<?php echo date("Y") ?>  
                <img src="<?= $webresourcesDir . DS.'Testi'.DS.'icons'.DS.'copyright.png' ?>" width="100" height="30" alt="" loading="lazy">&middot;
                <a href="">Privacy</a> &middot; 
                <a href="">Cookies</a> &middot; 
                <a href="">Terms</a>&middot;
            </p>
        </footer>
    </body>
        
        <!-- jQuery first, then Tether, then Bootstrap JS. -->
        <script src="/js/jquery-3.4.1.slim.min.js" ></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="<?= $webresourcesDir .DS. 'fontawesome'.DS.'js'.DS.'all.js' ?>"></script>
        <script>$('#message').fadeOut(8000);</script>
</html>