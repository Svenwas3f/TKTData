<?php
//Check if TicketToken is set
if(! isset($_GET["ticketToken"])) {
  header("Location: " . $url . "store/tickets");
  exit;
}

//Get infos of ticket
$ticket = new Ticket();
$ticket->ticketToken = $_GET["ticketToken"];

//Check if ticketToken exists
if( count($ticket->values()) <= 0 ) {
  // header("Location: " . $url . "store/tickets");
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
$response = getGateway( $ticket->ticketToken, $url . 'store/tickets/response/?ticketToken=' . urlencode( $ticket->ticketToken ), $url . 'store/tickets/response/?ticketToken=' . urlencode( $ticket->ticketToken ) );

//Get fullscreen image
$ticket = new Ticket();
$ticket->ticketToken = $_GET["ticketToken"];

//Get fullscreen image
if( isset( $group->values()["payment_background_fileID"] ) &&! empty( $group->values()["payment_background_fileID"] ) ) {
  $backgroundImgUrl = MediaHub::getUrl( $group->values()["payment_background_fileID"] );
}else {
  $backgroundImgUrl = $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,3) . "/medias/store/background/*")[0], PATHINFO_BASENAME );
}
 ?>
  <div class="pay-container">

    <div class="fullscreen-img" style="background-image: url('<?php echo $backgroundImgUrl; ?>')">
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
