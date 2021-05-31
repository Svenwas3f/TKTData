<?php
//Require general file
require_once( dirname(__FILE__, 3)) . "/general.php";

//Require qr file
require_once( dirname(__FILE__) . "/qr.php");

//Get ticket infos
$ticketToken = $_GET["ticketToken"];
$ticketInfo = explode(",", Crypt::decrypt( $ticketToken ));

//Get group infos
$group = new Group();
$group->groupID = $ticketInfo[0];
$groupValues = $group->values();

?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TICKET - <?php echo $group->values()["name"]; ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="../fonts/fonts.css" />
  </head>
  <body>
    <!-- Ticket pixel size: 992 x 1403 -->
    <article>

      <div class="row">
        <div class="ticket-info">
          <div class="headline">
            <?php
            // Logo
            if( isset($groupValues["ticket_logo_fileID"]) &&! empty($groupValues["ticket_logo_fileID"]) ) {
              echo '<img src="' . MediaHub::getUrl( $groupValues["ticket_logo_fileID"] ) . '" />';
            }

            // Title
            echo '<span>' . $groupValues["ticket_title"] . '</span>'
            ?>
          </div>
          <div class="qr-container">
            <img src="<?php qr_img_src( $ticketToken ); ?>" />
          </div>
          <span class="token"><?php echo $ticketToken; ?></span>
        </div>

        <div class="advert advert1">
          <?php
          // Advert 1
          if( isset($groupValues["ticket_advert1_fileID"]) &&! empty($groupValues["ticket_advert1_fileID"]) ) {
            echo '<img src="' . MediaHub::getUrl( $groupValues["ticket_advert1_fileID"] ) . '" />';
          }
          ?>
        </div>
      </div>

      <div class="row advert advert2">
        <?php
        // Advert 2
        if( isset($groupValues["ticket_advert2_fileID"]) &&! empty($groupValues["ticket_advert2_fileID"]) ) {
          echo '<img src="' . MediaHub::getUrl( $groupValues["ticket_advert2_fileID"] ) . '" />';
        }
        ?>
      </div>

      <div class="row advert advert3">
        <?php
        // Advert 3
        if( isset($groupValues["ticket_advert3_fileID"]) &&! empty($groupValues["ticket_advert3_fileID"]) ) {
          echo '<img src="' . MediaHub::getUrl( $groupValues["ticket_advert3_fileID"] ) . '" />';
        }
        ?>
      </div>

    </article>

    <footer>
      <div>
        Provided by <span>TKTDATA</span> & Sven Waser
      </div>
    </footer>
  </body>
</html>
