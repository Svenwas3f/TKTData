<?php
//Require general file
require_once(dirname(__FILE__, 2). "/general.php");

//Set current user
$current_user = "pub";

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - PUB</title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">

    <meta name="author" content="Sven Waser">
    <meta name="publisher" content="Sven Waser">
    <meta name="copyright" content="Sven Waser">
    <meta name="reply-to" content="sven.waser@sven-waser.ch">

    <meta name="description" content="Wilkommen auf dem TKTData Store. Kaufen Sie sich hier ein Ticket für den nächsten Event">
    <meta name="keywords" content="TKTData, TKTData Store, Store">

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
    <link rel="stylesheet" href="<?php echo $url; ?>pubs/style.css" />
    <link rel="stylesheet" href="<?php echo $url; ?>fonts/fonts.css" />

  </head>
  <body>
    <article>

      <header>
        <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
          <img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png">
        </div>
      </header>

      <form action="" method="post"  class="search_bar">
        <label>
          <input type="text" name="search" placeholder="Nach Wirtschaft suchen"/>
          <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
        </label>
      </form>
      <div class="store-group-list">
        <?php
        // Get all pubs
        $steps = 20;
        $offset = (empty($_GET["row-start"]) ? 0 : $_GET["row-start"] ) * $steps;

        $pubs = Pub::all($offset, $steps, $_GET["s"] ?? null);

        // List pubs
        foreach($pubs as $pub) {
          echo '<a href="' . $url . 'pubs/menu/?pub=' . $pub["pub_id"] . '">';
            echo '<div class="store-group-box">';
              if( empty($pub["logo_fileID"]) ) {
                echo '<img  src="' . $url . 'medias/pubs/favicon-color-512.png"/>';
              }else {
                echo '<img  src="' . MediaHub::getUrl( $pub["logo_fileID"] ) .'"/>';
              }
              echo '<span class="title">' . $pub["name"] . '</span>';
            echo '</div>';
          echo '</a>';
        }
         ?>
      </div>

      <?php
      // Next/last page
      if( (count(Pub::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) && (($offset/$steps) > 0) ) {
        echo '<div class="page-nav">';
          echo '<a a href="' . $url . 'pubs/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
          echo '<a class="center"></a>';
          echo '<a a href="' . $url . 'pubs/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
        echo '</div>';
        // echo "last/next";
      }elseif( ($offset/$steps) > 0 ) {
        echo '<div class="page-nav">';
          echo '<a a href="' . $url . 'pubs/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
          echo '<a class="center"></a>';
          echo '<a class="right"></a>';
        echo '</div>';
        // echo "last";
      }elseif( (count(Pub::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) ) {
        echo '<div class="page-nav">';
          echo '<a class="left"></a>';
          echo '<a class="center"></a>';
          echo '<a a href="' . $url . 'pubs/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
        echo '</div>';
        // echo "next";
      }
       ?>
    </article>

    <footer>
      <div class="container">
        <div class="footer-element">
        </div>
        <div class="footer-element">
        </div>
        <div class="footer-element">
          <span class="powered">Powered by <span>TKTDATA</span></span>
        </div>
      </div>
    </footer>
  </body>
</html>
