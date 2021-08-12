<?php
//Require general file
require_once(dirname(__FILE__, 2). "/general.php");

//Set current user
$current_user = "Store";

// Filedata
// Filename stored
$files = array(
  ////////////////////// TICKETS //////////////////////
  "tickets" =>  array(
    "" => "1.php", // Default page
    "buy" => "2.php", // single view to enter details for ticket
    "pay" => "3.php", // Pay ticket online
    "response" => "4.php", // payment response
    "faq" => "5.php", // FAQ of ticket
    "find-ticket" => "6.php", // Search your ticket
  ),

  //////////////////////// PUBS ////////////////////////
  "pubs" => array(
    "" => "7.php",
    "menu" => "8.php",
    "pay" => "9.php",
    "receipt" => "10.php",
  ),
);

$type = isset($files[ $_GET["type"] ]) ? $_GET["type"] : array_key_first( $files );
$page = isset($files[$type][ $_GET["page"] ]) ? $_GET["page"] : array_key_first( $files[$type] );

// Generate full get array
parse_str( str_replace("?", "", stristr( $_SERVER["REQUEST_URI"], "?")), $APPENDED_GET); // select GET parameters and parse to array
$_GET = array_merge( $_GET, $APPENDED_GET); // Merge parameters and add to GET
?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo Language::string( 9, null, "general" ); ?></title>

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
    <link rel="stylesheet" href="<?php echo $url; ?>store/style.css" />
    <link rel="stylesheet" href="<?php echo $url; ?>fonts/fonts.css" />

    <!-- JS -->
    <script src="<?php echo $url; ?>store/main.js"></script>

    <!-- Payrexx requirement -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://media.payrexx.com/modal/v1/gateway.min.js"></script>
  </head>
  <body>
    <article>
      <?php
      // Display page
      if( file_exists( dirname(__FILE__) . "/pages/" . $files[$type][$page] ) ) {
        require_once( dirname(__FILE__) . "/pages/" . $files[$type][$page] );
      }
       ?>
    </article>

    <footer>
      <?php
      // Custom footer
      switch( $type ) {
        ////////////////////// TICKETS //////////////////////
        default:
        case "ticket":
          echo '<div class="container">';
                  echo '<div class="footer-element">';
                    echo '<a href="' . $url . 'store/' . $type . '/faq#contact">' . Language::string( "footer1", null, "store", null, ($lang_code ?? null) ) . '</a>';
                    echo '<a href="' . $url . 'store/' . $type . '/find-ticket">' . Language::string( "footer2", null, "store", null, ($lang_code ?? null) ) . '</a>';
                  echo '</div>';
                  echo '<div class="footer-element">';
                    echo '<a href="' . $url . 'store/' . $type . '/faq#payment-procedure">' . Language::string( "footer3", null, "store", null, ($lang_code ?? null) ) . '</a>';
                    echo '<a href="' . $url . 'store/' . $type . '/faq#payment-options">' . Language::string( "footer4", null, "store", null, ($lang_code ?? null) ) . '</a>';
                  echo '</div>';
                  echo '<div class="footer-element">';
                    echo '<span class="powered">' . Language::string( "footer5", null, "store", null, ($lang_code ?? null) ) . '</span>';
                  echo '</div>';
                echo '</div>';
        break;

        //////////////////////// PUBS ////////////////////////
        case "pubs":
          echo '<div class="container">';
                  echo '<div class="footer-element">';
                  echo '</div>';
                  echo '<div class="footer-element">';
                  echo '</div>';
                  echo '<div class="footer-element">';
                    echo '<span class="powered">' . Language::string( "footer6", null, "store", null, ($lang_code ?? null) ) . '</span>';
                  echo '</div>';
                echo '</div>';
        break;
      }
       ?>
    </footer>
  </body>
</html>
