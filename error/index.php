<?php
//Start session
session_start();

//Require files
require_once(dirname(__FILE__) . "/error.php"); //Error class
require_once(dirname(__FILE__, 2) . "/general.php"); //Action

// Get url
$general_path = dirname(__FILE__, 2) . "/general.php" ;
$general = fopen( $general_path, "r" );
$general_content = fread( $general, filesize($general_path) );
fclose( $general );

// Extract $url
preg_match('/(\$url)+(\s)*=(.)*(;){1}/', $general_content, $matches) ? eval($matches[0]) : '';

//Create error
$e = new ERR();
$e->error = $_GET["error"] ?? 0;

/**
 * Return user to system if required and possible
 */
if( ($_GET["error"] ?? 0) == 1){
  $e->return_to_system();
}
 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo Language::string( 0, null, "general" ); ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">

    <meta name="author" content="Sven Waser">
    <meta name="publisher" content="Sven Waser">
    <meta name="copyright" content="Sven Waser">
    <meta name="reply-to" content="sven.waser@sven-waser.ch">

    <meta name="description" content="<?php echo Language::string( 1, null, "general" ); ?>">
    <meta name="keywords" content="<?php echo Language::string( 2, null, "general" ); ?>">

    <meta name="content-language" content="de">
    <meta name="robots" content="noindex">

    <meta name="theme-color" content="#232b43">


    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $url; ?>medias/logo/favicon.ico">
    <link rel="shortcut icon" href="<?php echo $url; ?>medias/logo/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo $url; ?>medias/logo/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $url; ?>medias/logo/logo-512.png">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="<?php echo $url; ?>medias/logo/logo-512.png">
    <meta name="msapplication-TileColor" content="#232b43">
    <meta name="msapplication-TileImage" content="<?php echo $url; ?>medias/logo/logo-512.png">

    <!-- Custom scripts -->
    <link rel="stylesheet" href="<?php echo $url; ?>style.css" />
    <link rel="stylesheet" href="<?php echo $url; ?>fonts/fonts.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/styles/gruvbox-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/highlight.min.js"></script>

    <!-- Custom js -->
    <script src="<?php echo $url; ?>js/jsQR.js"></script><!--https://github.com/cozmo/jsQR-->
    <script src="<?php echo $url; ?>js/input.js"></script>
    <script src="<?php echo $url; ?>js/mail.js"></script>
    <script src="<?php echo $url; ?>js/menu.js"></script>
    <script src="<?php echo $url; ?>js/ajax.js"></script>
    <script src="<?php echo $url; ?>js/imageUpload.js"></script>
    <script src="<?php echo $url; ?>js/Chart.js"></script>
    <script src="<?php echo $url; ?>js/media-hub.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    </head>
    <body onload="document.getElementsByClassName('loader')[0].style.display = 'none';">
      <!-- noscript and loader -->
      <noscript>
        <div class="fullscreen center">
          <form>
            <img src="medias/logo/logo-fitted.png" />
            <span><?php echo Language::string( 3, null, "general" ); ?></span>
          </form>
        </div>
      </noscript>


      <div class="loader">
        <span class="text"><?php echo Language::string( 4, null, "general" ); ?></span>
        <div class="letter">
          <span class="base"></span>
          <span class="top"></span>
        </div>
      </div>

      <?php
      Action::fs_info($e->info("message"))
       ?>
    </article>
    <?php
    /**
     *  Display footer
     */
    footer();
     ?>
  </body>
</html>
