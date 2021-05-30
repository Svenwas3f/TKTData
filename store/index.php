<?php
//Require general file
require_once(dirname(__FILE__, 2). "/general.php");

//Set current user
$current_user = "Store";

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - STORE</title>

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
    <link rel="stylesheet" href="<?php echo $url; ?>store/style.css" />
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
          <input type="text" name="search" placeholder="Nach Ticket suchen"/>
          <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
        </label>
      </form>
      <div class="store-group-list">
        <?php
        //List all groups
        $conn = Access::connect();

        //Search set
        if(empty($_POST)) {
          $allGroups = $conn->prepare("SELECT * FROM  " . TICKETS_GROUPS . " WHERE payment_store='1'");
          $allGroups->execute();
        }else {
          $allGroups = $conn->prepare("SELECT * FROM  " . TICKETS_GROUPS . " WHERE payment_store='1' AND name LIKE :name");
          $allGroups->execute(array(":name" => "%" . $_POST["search"] . "%"));
        }

        foreach($allGroups->fetchAll(PDO::FETCH_ASSOC) as $group) {
          //Get state of group
          $groupCheck = new Group();
          $groupCheck->groupID = $group["groupID"];

          if($groupCheck->values() === false) {
            $groupState = 3; //Group does not exist
          }elseif($groupCheck->availableTickets() <= 0) {
            $groupState = 2; //sold out
          }elseif($groupCheck->timeWindow() === false) {
            $groupState = 1; //timewindow closed
          }else {
            $groupState = 0; //Ok
          }

          //Define onclick
          $onclick = ($groupState == 0) ? 'onclick="location.href = \'' . $url . 'store/buy/?group=' . $group["groupID"] . '\'"' : '';

          //Display every box of group
          echo '<div class="store-group-box" ' . $onclick . '>';
            //Banner required
            switch($groupState) {
              case 1:
                echo '<div class="banner" style="background-color: #b91657;">Abgelaufen</div>';
              break;
              case 2:
                echo '<div class="banner" style="background-color: #4c4ca1;">Ausverkauft</div>';
              break;
              case 3:
                echo '<div class="banner" style="background-color: #80007c;">Existiert nicht</div>';
              break;
            }

            //Get logo
            //Get fullscreen image
            if( isset( $groupCheck->values()["payment_logo_fileID"] ) ) {
              echo '<img  src="' . MediaHub::getUrl( $groupCheck->values()["payment_logo_fileID"] ) .'"/>';
            }else {
              echo '<img  src="' . $url . 'medias/store/favicon-color-512.png"/>';
            }
            echo '<span class="title">' . $group["name"] . '</span>';
            echo '<span class="info">' . (($group["price"] + ($group["vat"] / 10000) * $group["price"]) / 100) . ' ' . $group["currency"] . '</span>';
          echo '</div>';
        }
         ?>
      </div>
    </article>

    <footer>
      <div class="container">
        <div class="footer-element">
          <a href="<?php echo $url; ?>store/faq#contact">Kontakt</a>
          <a href="<?php echo $url; ?>store/find-ticket">Mein Ticket finden</a>
        </div>
        <div class="footer-element">
          <a href="<?php echo $url; ?>store/faq#payment-procedure">Wie kaufe ich ein Ticket?</a>
          <a href="<?php echo $url; ?>store/faq#payment-options">Welche Zahlungsmöglichkeiten gibt es?</a>
        </div>
        <div class="footer-element">
          <span class="powered">Powered by <span>TKTDATA</span></span>
        </div>
      </div>
    </footer>
  </body>
</html>
