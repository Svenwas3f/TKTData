<?php
//Require general file
require_once(dirname(__FILE__, 3). "/general.php");

//Set current user for changelog
$current_user = "Store";

//Check if TicketToken is set
if(! isset($_GET["ticketToken"])) {
  header("Location: " . $url . "store/");
  exit;
}

//Get infos of ticket
$ticket = new Ticket();
$ticket->ticketToken = $_GET["ticketToken"];

//Check if ticketToken exists
if( count($ticket->values()) <= 0 ) {
  header("Location: " . $url . "store/");
  exit;
}

// Start group
$group = new Group();
$group->groupID = $ticket->values()["groupID"];

//Check if price exists
if( $ticket->values()["amount"] <= 0) {
  header("Location: " . $url . "store/ticket/?ticketToken=" . urlencode($ticket->ticketToken) );
  exit;
}

//Check if ticket is already payed
$transaction = retrieveTransaction( $ticket->ticketToken );
if( $transaction["transaction_retrieve_status"] == true ) {
  if($transaction["status"] == "confirmed" || $transaction["pspId"] == 15 || $transaction["pspId"] == 27) { //https://developers.payrexx.com/docs/miscellaneous
    header("Location: " . $url . "store/ticket/?ticketToken=" . urlencode($ticket->ticketToken));
    exit;
  }
}

/*
*##############################################################################
*
* #####       #####   ##   ##  #####     #######   ##       ##   ##      ##
* ##   ##    ##  ##    ## ##   ##   ##   ##          ##  ##        ##  ##
* #####     ########    ###    #####     #######       ##            ##
* ##       ##     ##    ##     ##  ##    ##          ##  ##       ##   ##
* ##      ##      ##    ##     ##   ##   #######   ##      ##   ##       ##
*
*##############################################################################
*/
$response = getGateway( $_GET["ticketToken"], $url . 'store/ticket/?ticketToken=' . urlencode( $ticket->ticketToken ), $url . 'store/ticket/?ticketToken=' . urlencode( $ticket->ticketToken ) );

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - PAY</title>

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

    <script src="<?php echo $url; ?>store/main.js"></script>
    <script src="<?php echo $url; ?>store/buy/ajax.js"></script>

    <!-- Payrexx requirement -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://media.payrexx.com/modal/v1/gateway.min.js"></script>
  </head>
  <body>
    <?php
    //Get fullscreen image
    $ticket = new Ticket();
    $ticket->ticketToken = $_GET["ticketToken"];

    //Get fullscreen image
    if( isset( $group->values()["payment_background_fileID"] &&! empty( $group->values()["payment_background_fileID"] ) ) {
      $backgroundImgUrl = MediaHub::getUrl( $group->values()["payment_background_fileID"] );
    }else {
      $backgroundImgUrl = $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,3) . "/medias/store/background/*")[0], PATHINFO_BASENAME );
    }
     ?>
    <article>
      <div class="pay-container">

        <div class="fullscreen-img" style="background-image: url('<?php echo $url . $path; ?>')">
        </div>

        <?php
        //Payment modal
        if($response["gateway_creation_state"]) {
          echo '<a class="payrexx-modal-window" href="#" data-href="https://' . $group->values()["payment_payrexx_instance"] . '.payrexx.com/?payment=' . $response["hash"] . '">Zahlung jetzt tätigen</a>';
          echo '<script type="text/javascript">';
            echo 'jQuery(\'.payrexx-modal-window\').payrexxModal();';
            echo 'jQuery(\'.payrexx-modal-window\').click();';
          echo '</script>';
        }else {
          Action::fail("Die Zahlungsseite konnte nicht geladen werden. Melden Sie sich beim Administrator.<br />Folgende Fehlermeldung wird ausgegeben: " . $response["message"]);
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
