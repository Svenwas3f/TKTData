<?php
//Start session
session_start();

//Require files
require_once( dirname(__FILE__) . "/general.php");

/**
 * Check if the user wants to log in
 */
if(!empty($_POST)) {
  //Get table
  $conn = Access::connect();
  $table = $conn->prepare("SELECT * FROM " . USERS);
  $table->execute();

  //Check if first login
  if($table->rowCount() === 0) {
    if(strtolower($_POST["id"]) === "admin" && strtolower($_POST["password"]) === "admin") {
      //Login successful
      $_SESSION["login"] = true;
      $_SESSION["user"] = "Admin";
    }
  }else {
    if(User::authorize($_POST["id"], $_POST["password"])) {
      //Login successful
      $_SESSION["login"] = true;
      $_SESSION["user"] = $_POST["id"];
    }
  }
}

/**
 * Check if user wants to log out
 */
if(isset($_GET["logout"]) && isset($_SESSION["login"]) && $_SESSION["login"] && isset($_SESSION["user"]) &&! empty($_SESSION["user"])) {
  //Delete all variables
  $_SESSION = array();

  //Reset sessions
  $session_destroyed = session_destroy();
}

/**
 * Check if user is already logged in
 */
if((isset($_SESSION["login"]) && $_SESSION["login"]) && (isset($_SESSION["user"]) &&! empty($_SESSION["user"]))) {
  //Check if redirect url is set
  if(isset($_GET["rdrp"])) {
    header("Location: " . urldecode($_GET["rdrp"])); //Redirect to selected page
  }else {
      header("Location: " . $url); //Redirect to first accessable page
  }
}
?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - TICKETVERWALTUNG</title>

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
  </head>
  <body class="auth-background">

    <div class="auth-form">
      <form action="<?php echo (!empty($_GET["rdrp"])) ? $url . "auth.php?rdrp=" . urlencode($_GET["rdrp"]) : $url . "auth.php"; ?>" method="post">
        <a href="<?php $url; ?>"><img src="<?php echo $url; ?>medias/logo/logo-fitted.png"></a>
        <!-- User -->
        <label class="txt-input">
          <input type="text" name="id" required/>
          <span class="placeholder">Benutzername</span>
        </label>
        <!-- Password -->
        <label class="txt-input">
          <input type="password" name="password" required/>
          <span class="placeholder">Passwort</span>
        </label>

        <input type="submit" name="login" value="LOGIN" title="Einloggen"/>

        <a class="reset-link" href="<?php echo $url; ?>reset.php" title="Passwort zurücksetzen">Passwort vergessen</a>
      </form>
    </div>

      <?php
      //Login failed
      if(!empty($_POST) && empty($_SESSION["login"])) {
        Action::fail('Der Benutzername und das Passwort stimmen nicht überein');
      }

      //Logout successful
      if(isset($session_destroyed)) {
        if($session_destroyed === true) {
          Action::success('Sie wurden erfolgreich von dieser Sitzung abgemeldet.');
        }else {
          Action::fail('Sie wurden nicht von dieser Sitzung abgemeldet. Laden Sie die Seite.');
        }
      }
       ?>

    <div class="auth-footer">
      <?php
      /**
       *  Display footer
       */
      footer();
       ?>
    </div>
  </body>
</html>
