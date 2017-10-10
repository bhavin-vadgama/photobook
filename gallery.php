<?php
session_start();
ini_set('memory_limit', '1024M');
if (!isset($_SESSION['fb_access_token'])) {
    header('location: ./');
}
//require_once ('./vendor/autoload.php');
require_once './php/facebook_class.php';
require_once './php/downloader_class.php';

$fb = new FacebookClass();
$fb->initializeFBWithSession();
if (isset($_REQUEST['download'])) {
    if (isset($_SESSION['user']['zip'])) {
        $dd = new Download(null, null, null);
        $dd->getZip($_SESSION['user']['zip']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="img/favicon.ico">

        <title>photoME</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700,600"
              rel="stylesheet" type="text/css">
        <link href="css/font-awesome.min.css" rel="stylesheet">
        <link href="css/colorbox.css" rel="stylesheet">
        <link href="css/style.min.css" rel="stylesheet">
        <link href="css/responsive.min.css" rel="stylesheet">
        <link href="css/slideshow.css" rel="stylesheet">
        <link href="css/loader.css" rel="stylesheet">
        <script src="js/fetch-album.js" ></script>
        <script src="js/get-albums-zip.js" ></script>
        <script src="js/gdrive_authenticate.js" ></script>
        <script src="js/gdrive-move.js" ></script>
        <script src="js/unload.js"></script>


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.min.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body onunload="unload();">

        <noscript>
        <style type="text/css">
            #content {display:none;}
        </style>
        <div class="noscriptmsg">
            <center>
                <h3 style="font-family: monospace; color: red;">
                    You don't have javascript enabled.  Please enable the javascript to process further.
                </h3></center>
        </div>
        </noscript>

        <!-- begin:navbar -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="z-index:998">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" 
                            data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="gallery.php"><span>photoME</span> Gallery</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li id="zip" style="display:<?php
                        if (isset($_SESSION['user']['zip'])) {
                            echo '';
                        } else {
                            echo 'none';
                        }
                        ?>;color:red;font-weight:bold">
                            <form method="post" action="<?php
                            echo $_SERVER['PHP_SELF'];
                            ?>">
                                <input type="hidden"  name="download" />
                                <button class="zipbtn"style="">
                                    <i class="fa fa-file-zip-o fa-1x"></i>&nbsp;&nbsp;Download Zip
                                </button>
                            </form>
                        </li>
                        <!--<li><a href="about.html">About</a></li>        
                        <li><a href="signup.html"><i class="fa fa-edit"></i> Signup</a></li>-->

                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php
                                echo $_SESSION['user']['picture'];
                                ?>" style="position: relative;top: -10px;float: left;left: -10px;" height="35" width="35" class="profile-image img-circle"/><?php
                                                                                                              echo $_SESSION['user']['name'];
                                                                                                              ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="php/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- end:navbar -->

        <!-- begin:content -->
        <div id="content">
            <div class="container">
                <div class="row container-gallery">

                    <?php
                    $response = $fb->getData('/me?fields=albums{name,picture{url},count}');
                    for ($i = 0; $i < count($response['albums']['data']); ++$i) {
                        echo '<div class="col-md-2 col-sm-4 col-xs-6" id="' . $response['albums']['data'][$i]['id'] . '">';
                        echo '<div class="post-container" id="' . $response['albums']['data'][$i]['id'] . '">';
                        echo '<div class="post-option">';
                        echo '<ul class="list-options">';
                        echo '<li><a href="javascript: void(0);" id="' . $response['albums']['data'][$i]['id'] . '" onclick="downloadAlbum(this);"><i class="fa fa-download"></i> <span>Download</span></a></li>';
                        echo '<li><a href="javascript: void(0);" onclick="moveAlbum(this);" id="' . $response['albums']['data'][$i]['id'] . '"><i class="fa fa-cloud-download" id="' . $response['albums']['data'][$i]['id'] . '"></i> <span>Move</span></a></li>';
                        echo '</ul>';
                        echo '</div>';
                        echo '<div class="post-image">';
                        echo '<a href="javascript:void(0);" onclick="fetchAlbum(this);" class="img-group-gallery" title="' . $response['albums']['data'][$i]['name'] . '" id="' . $response['albums']['data'][$i]['id'] . '"><img src="';
                        if ($response['albums']['data'][$i]['count'] > 0) {
                            echo '' . $response['albums']['data'][$i]['picture']['data']['url'] . '" id="' . $response['albums']['data'][$i]['id'] . '" class="img-responsive" alt="' . $response['albums']['data'][$i]['name'] . '"></a>';
                        } else {
                            echo './img/thumb_image_not_available.png" id="' . $response['albums']['data'][$i]['id'] . '" class="img-responsive" alt="' . $response['albums']['data'][$i]['name'] . '"></a>';
                        }
                        echo '</div>';
                        echo '<div class="post-meta">';
                        echo '<ul class="list-meta list-inline">';
                        echo '<li><i class="fa fa-photo fa-1x"></i> ' . $response['albums']['data'][$i]['count'] . '</li>';
                        echo '</ul>';
                        echo '</div>';
                        echo '<div class="post-desc">';
                        echo '<label class="checkbox-inline"><input type="checkbox" name="album_checklist" id="' . $response['albums']['data'][$i]['id'] . '" /> ' . $response['albums']['data'][$i]['name'] . '</label>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>

                    <!-- dynamic containers -->
                    <!--<div class="col-md-3 col-sm-6 col-xs-12" style="border:1px solid red;">
                    <div class="post-container" style="border:3px solid green;">
                    <div class="post-option">
                    <ul class="list-options">
                    <li><a href="#"><i class="fa fa-download"></i> <span>Download</span></a></li>
                    <li><a href="#"><i class="fa fa-cloud-download"></i> <span>Move</span></a></li>
                    </ul>
                    </div>
            
                    <div class="post-image">
                    <a href="#" class="img-group-gallery" title="Lorem ipsum dolor sit amet"><img src="" class="img-responsive" alt="fransisca gallery"></a>
                    </div>
            
                    <div class="post-meta">
                    <ul class="list-meta list-inline">
                    <li><i class="fa fa-photo"></i> </li>
                    </ul>
                    </div>
            
                    <div class="post-desc">
                    <i class="fa fa-check-square"></i>
                    </div>
                    </div>
                    </div>-->
                    <!-- break -->

                </div>
            </div>
        </div>
        <!-- end:content -->

        <!-- begin:footer -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <p>Developed by <b>Bhavin Vadgama</b></p>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <ul class="list-social">
                            <li><a target="_blank" href="https://twitter.com/vadgama_bhavin"><i class="fa fa-twitter"></i></a></li>
                            <li><a target="_blank" href="https://www.instagram.com/bhavin_vadgama/"><i class="fa fa-instagram"></i></a></li>
                            <li><a target="_blank" href="https://www.linkedin.com/in/bhavin-vadgama/"><i class="fa fa-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:footer -->

        <footer style="border-top: 1px solid #dadada; z-index:2000; position: fixed; bottom: 0;  width: 100%;   background-color:#fff;"> 
            <div class="container" style="text-align:center;">
                <div class="col-lg-12 col-sm-12 col-xs-12 " style="padding: 10px;">
                    <button class="btn btn-md" onclick="downloadAllAlbum();">
                        <span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download All
                    </button>
                    <button class="btn btn-md" onclick="downloadSelectedAlbum();">
                        <span class="glyphicon glyphicon-check"></span>&nbsp;&nbsp;Download Selected
                    </button>
                    <button class="btn btn-md" onclick="moveAll();">
                        <span class="glyphicon glyphicon-cloud-download"></span>&nbsp;&nbsp;Move All
                    </button>
                    <button class="btn btn-md" onclick="moveSeleceted();">
                        <span class="glyphicon glyphicon-check"></span>&nbsp;&nbsp;Move Selected
                    </button>
                </div>
            </div>
        </footer>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="js/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.easing.js"></script>
        <script src="js/masonry.pkgd.min.js"></script>
        <script src="js/jquery.isotope.min.js"></script>
        <script src="js/script.min.js"></script>
        <script src="js/loader.js"></script>

        <!-- slider 
      <div class="display-album-container" style="display:none;">
          <div class="close">
              <a href="#"><span class="" ></span></a>
          </div>
      <div class="album-pic">
          <div class="img-container">
              <img src="img/fransisca-post-image02-big.jpg" />
          </div>
      </div>
          <div class="album-pic-navigation">
              <a href="#" class="left carousel-control"><span class="glyphicon glyphicon-chevron-left"></span></a>
              <span class="count">1/10</span>
              <a href="#" class="right carousel-control"><span class="glyphicon glyphicon-chevron-right"></span></a>
          </div>
      </div>
        -->
        <!-- ajax loader start-->
        <div id="ajax_loader">
            <img src="img/gloader1.svg"/>
        </div>
        <!-- ajax loader end -->
    </body>
</html>