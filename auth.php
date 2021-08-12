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
      // Get all menupoints
      $menu = $conn->prepare("SELECT id FROM " . MENU . " WHERE submenu <> 0");
      $menu->execute();

      foreach( $menu->fetchAll( PDO::FETCH_NUM ) as $menuID) {
        $rights[$menuID[0]] = array( "w", "r" );
      }

      // Create new admin superuser
      $user = new User();
      $user->add( EMAIL, 'Admin', 'Admin', $rights, false);
      $user->updatePassword('admin', 'admin'); // Set old password

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
  </head>
  <body class="auth-background">

    <div class="auth-form">

      <?php
      $form = new HTML('form', array(
        'action' => (!empty($_GET["rdrp"])) ? $url . "auth.php?rdrp=" . urlencode($_GET["rdrp"]) : $url . "auth.php",
        'method' => 'post',
      ),);

      $form->customHTML('<a href="' . $url . '"><img src="' . $url . 'medias/logo/logo-fitted.png"></a>');

      $form->addElement(
        array(
          'type' => 'text',
          'name' => 'id',
          'placeholder' => Language::string( 0, null, "auth" ),
          'required' => true,
        ),
      );

      $form->addElement(
        array(
          'type' => 'password',
          'name' => 'password',
          'placeholder' => Language::string( 1, null, "auth" ),
          'required' => true,
        ),
      );

      $form->addElement(
        array(
          'type' => 'button',
          'name' => 'login',
          'value' => Language::string( 2, null, "auth" ),
          'additional' => 'title="' . Language::string( 3, null, "auth" ) . '"',
        ),
      );

      $form->customHTML('<a class="reset-link" href="' . $url . 'reset.php" title="' . Language::string( 5, null, "auth" ) . '">' . Language::string( 4, null, "auth" ) . '</a>');

      $form->prompt();
      ?>

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
