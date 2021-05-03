<?php
//Start session
session_start();

//Require general file
require_once(dirname(__FILE__, 2) .  "/general.php");
require_once(dirname(__FILE__) . "/error.php"); //Error class

//Create error
$e = new ERR();
$e->error = $_GET["error"];

/**
 * Return user to system if required and possible
 */
if( $_GET["error"] == 1){
  $e->return_to_system();
}
 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>KDATA - ERROR</title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">

    <meta name="author" content="Sven Waser">
    <meta name="publisher" content="Sven Waser">
    <meta name="copyright" content="Sven Waser">
    <meta name="reply-to" content="sven.waser@sven-waser.ch">

    <meta name="description" content="KDATA. Eine Verwaltungssoftware für die Kantonsschule Solothurn">
    <meta name="keywords" content="KDATA">

    <meta name="content-language" content="de">
    <meta name="robots" content="noindex">

    <meta name="theme-color" content="#232b43">


    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $url; ?>/medias/logo/favicon.ico">
    <link rel="shortcut icon" href="<?php echo $url; ?>/medias/logo/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo $url; ?>/medias/logo/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $url; ?>/medias/logo/logo-512.png">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="<?php echo $url; ?>/medias/logo/logo-512.png">
    <meta name="msapplication-TileColor" content="#232b43">
    <meta name="msapplication-TileImage" content="<?php echo $url; ?>/medias/logo/logo-512.png">

    <!-- Custom scripts -->
    <link rel="stylesheet" href="<?php echo $url; ?>/style.css" />
    <link rel="stylesheet" href="<?php echo $url; ?>/fonts/fonts.css" />


  </head>
  <body>
    <?php
    Action::fs_info($e->info("message"))
     ?>
    </article>
  </body>
</html>
