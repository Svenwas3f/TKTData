<?php
//Start session
session_start();

//Require general file
require_once( dirname(__FILE__) . "/general.php");

/**
 * Check if user is logged in
*/
if(!isset($_SESSION["login"]) || $_SESSION["login"] == false || !isset($_SESSION["user"]) || empty($_SESSION["user"])) {
  header("Location: " . $url . "auth.php?rdrp=" . urlencode($url . "?" . explode('?', $_SERVER["REQUEST_URI"], 2)[1])); //Redirect to login page
  exit;
}

/**
* Go to first available page
*/
if(! isset($_GET["id"])){
  $sub = User::first_accessable_page($current_user); //Get first page to access

  if( User::system_access($current_user) === true ){
    //Get main menu id
    $main = Menu::main_id($sub);

    //Redirect to first accessable page
    header("location: ". $url ."?id=". $main ."&sub=". $sub);
    exit;
  }else{
    //Redirect to error page
    header("location: ". $url ."/error?error=1");
    exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/styles/gruvbox-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/highlight.min.js"></script>

    <!-- Plugin scripts -->
    <?php
    /////////////////////////////
    // Require plugins style
    /////////////////////////////
    $plugins = glob( dirname(__FILE__) . "/plugins/*" , GLOB_ONLYDIR );

    foreach( $plugins as $plugin ) {
      /* Require function file */
      if(file_exists( $plugin . "/style.css" )) {
        echo '<link rel="stylesheet" href="' . $url . '/plugins/' . basename( $plugin ) . '/style.css">';
      }
    }
     ?>

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
    /**
     *  Display menu
     */
    echo Menu::display();
     ?>

     <article>
      <?php
      /**
       * Display custom html
       */
      //Create new page variable
      $displayPage = $page;

      /**
       * Check if Main menu page is selected
       */
      $conn = Access::connect();

      $check = $conn->prepare("SELECT id FROM " . MENU . " WHERE id=:page AND (submenu IS NULL OR submenu='' OR submenu='0') ORDER BY layout");
      $check->execute(array(":page" => $page));

      $pages = $conn->prepare("SELECT page FROM " . USER_RIGHTS . " WHERE page IN (SELECT id FROM " . MENU . " WHERE submenu=:page ORDER BY layout) LIMIT 0, 1");
      $pages->execute(array(":page" => $displayPage));

      if( $check->rowCount() > 0){
        //Define new page (First page of main menu)
        $displayPage = $pages->fetch()[0];
      }

      /**
       * Display content of current page if user has access
       */
      if( User::w_access_allowed($displayPage, $current_user) || User::r_access_allowed($displayPage, $current_user ) || $displayPage == "profile"){
        // Check if page is a pluginpage
        $plugin = new Plugin();

        if( $plugin->is_pluginpage( $displayPage )) {
          // Page contains to plugin
          $name = $plugin->get_page( intval($displayPage) )["plugin"];

          //Plugin default path
          $plugin_file = dirname(__FILE__) . "/plugins/" . $name . "/index.php";

          //Check file
          if(file_exists( $plugin_file )) {
            require_once( $plugin_file );
          }else {
            Action::fs_info(
              Language::string( 5, array(
                '%page%' => $page,
              ), "general" ),
              Language::string( 6, null, "general" ),
              $url
            );
          }
        }else {
          // Page is a default page
          echo '<div class="mainpage-' . $mainPage . ' subpage-' . $page . '">'; // Generate container

            //Display page
            require_once( dirname(__FILE__) . "/pages/" . $displayPage . ".php" );

          echo '</div>';
        }
      }else{
        /**
         * Check if user has permission to access this system
         */
        $sub = User::first_accessable_page($current_user); //Get first page to access

        if( $sub !== false ){ //Check if there is a page available
          //Get main menu id
          $main = Menu::main_id($sub);

          //Denied access and button to return to first available page
          Action::fs_info(
            Language::string( 7, array(
              '%page%' => $page,
            ), "general" ),
            Language::string( 8, null, "general" ),
            $url
          );

          }else{
          //Redirect to error page
          header("location: ". $url ."/error?error=1");
        }
      }
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
