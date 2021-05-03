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

//Define path to Ticketgroup folder
$ticketGroupPath =  dirname(__FILE__, 3) . "/medias/groups/" . $group->groupID . "/ticket/";

//Check if path is valid
if(is_dir($ticketGroupPath)) {
  //Get infos for ticket
  $logo = glob( $ticketGroupPath . "/logo/*");
  $adverts = glob( $ticketGroupPath . "/adverts/*");
  $title = file_get_contents( $ticketGroupPath . "/title.txt");
}
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
            if( isset($logo) && is_array($logo) ) {
              if( count($logo) > 0 ) {
                echo '<img src="' . $logo[0] . '" />';
              }
            }

            if(! empty($title)) {
              echo '<span>' . $title . '</span>';
            }else {
              echo '<span>Ihr Ticket</span>';
            }
            ?>
          </div>
          <div class="qr-container">
            <img src="<?php qr_img_src( $ticketToken ); ?>" />
          </div>
          <span class="token"><?php echo $ticketToken; ?></span>
        </div>

        <div class="advert advert1">
          <?php
          if( isset($adverts)  && is_array($adverts) ) {

            if(array_key_exists(0, $adverts)) {
              echo '<img src="' . $adverts[0] . '" />';
            }
          }
          ?>
        </div>
      </div>

      <div class="row advert advert2">
        <?php
        if( isset($adverts) && is_array($adverts) ) {
          if(array_key_exists(1, $adverts)) {
            echo '<img src="' . $adverts[1] . '" />';
          }
        }
        ?>
      </div>

      <div class="row advert advert3">
        <?php
        if( isset($adverts) && is_array($adverts) ) {
          if(array_key_exists(2, $adverts)) {
            echo '<img src="' . $adverts[2] . '" />';
          }
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
