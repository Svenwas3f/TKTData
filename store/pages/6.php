<header>
  <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
    <a href="<?php echo $url . "store/" . $type; ?>"><img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png"></a>
  </div>
</header>

<form action="<?php echo $url . "store/" . $type . "/" . $page . "/";?>" method="get"  class="search_bar">
  <label>
    <input type="email" name="s" placeholder="Deine E-Mail" value="<?php echo $_GET["s"] ?? ''; ?>" required/>
    <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
  </label>
</form>

<div class="ticket_responses">
  <?php
  //Check if check is requested
  if(! empty($_GET["s"])) {
    //Select all response
    $steps = 10;
    $offset = (empty($_GET["row-start"]) ? 0 : $_GET["row-start"] ) * $steps;
    $response = Ticket::all($offset, $steps, $_GET["s"] ?? null);

    $response = array_filter($response, 'is_array');

    if(count( $response ) > 0) {

      for($i = 0; $i < count($response); $i++) {
        //Update payment if required
        checkPayment( Ticket::encryptTicketToken($response[$i]["groupID"], $response[$i]["ticketKey"]) );

        //Group
        $group = new Group();
        $group->groupID = $response[$i]["groupID"];

        //ticket
        $ticket = new Ticket();
        $ticket->ticketToken = Ticket::encryptTicketToken($response[$i]["groupID"], $response[$i]["ticketKey"]);

        //HTML Response
        echo '<div class="ticket_response">';

          if($group->values() === false) {
            echo '<div class="banner" style="background-color: #80007c;">Existiert nicht</div>';
          }elseif($group->timeWindow() === false) {
            echo '<div class="banner" style="background-color: #b91657;">Abgelaufen</div>';
          }

          echo '<div class="logo">';
            if( isset( $group->values()["payment_logo_fileID"] ) &&! empty( $group->values()["payment_logo_fileID"] ) ) {
              $mediaHub = new MediaHub();
              $mediaHub->fileID = $group->values()["payment_logo_fileID"];

              echo '<img  src="' . $mediaHub->getUrl( $group->values()["payment_logo_fileID"] ) .'" alt="' . $mediaHub->fileDetails()["alt"] . '"/>';
            }else {
              echo '<img  src="' . $url . 'medias/store/favicon-color-512.png" alt="LOGO"/>';
            }
          echo '</div>';

          echo '<div class="details">';
            echo '<span class="headline">' . $group->values()["name"] . '</span>';
            $transaction = retrieveTransaction( Ticket::encryptTicketToken($response[$i]["groupID"], $response[$i]["ticketKey"]) );

            if ( $ticket->values()["payment"] != 2 ) {
              $payment_state = "erfolgreich getätigt";
            } elseif ( $transaction["transaction_retrieve_status"] == false ) {
              $payment_state = "erwartet";
            }else {
              switch($transaction["status"]) {
                case "waiting":
                  $payment_state = "erwartet";
                break;
                case "confirmed":
                  $payment_state = "erfolgreich getätigt";
                break;
                case "authorized":
                  $payment_state = "authorisiert";
                break;
                case "reserved":
                  $payment_state = "reserviert";
                break;
                default:
                  $payment_state = "unbekannt";
                break;
              }
            }

            echo '<span class="subinfos">' . ($response[$i]["amount"] / 100) . ' ' . $group->values()["currency"] . ' | Zahlung ' . $payment_state . '</span>';
          echo '</div>';

          echo '<div class="send">';
            echo '<button onclick="ajax_send_mail(\'' . $_GET["s"] . '\', \'' . $i . '\', \'' . $offset . '\', \'' . $steps . '\')">Erneut senden</button>';
            echo '<button onclick="window.open(\'' . $url . 'store/' . $type . '/faq/#contact\', \'_blank\')">Veranstalter</button>';
          echo '</div>';

        echo '</div>';
      }
    } else {
      echo '<div class="not-found"><img src="' . $url . 'medias/icons/not-found.svg" /></div>';
    }

  }
   ?>
</div>

<?php
//Check if check is requested
if(! empty($_GET["s"])) {
  // Next/last page
  if( (count(Ticket::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) && (($offset/$steps) > 0) ) {
    echo '<div class="page-nav">';
      echo '<a a href="' . $url . 'store/' . $type . '/' . $page .  '/?s=' . urlencode($_GET["s"]) . '&row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Tickets ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg" /></a>';
      echo '<a class="center"></a>';
      echo '<a a href="' . $url . 'store/' . $type . '/' . $page .  '/?s=' . urlencode($_GET["s"]) . '&row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Tickets ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg" /></a>';
    echo '</div>';
  }elseif( ($offset/$steps) > 0 ) { // Last page
    echo '<div class="page-nav">';
      echo '<a a href="' . $url . 'store/' . $type . '/' . $page .  '/?s=' . urlencode($_GET["s"]) . '&row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Tickets ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg" /></a>';
      echo '<a class="center"></a>';
      echo '<a class="right"></a>';
    echo '</div>';
  }elseif( (count(Ticket::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) ) { // Next page
    echo '<div class="page-nav">';
      echo '<a class="left"></a>';
      echo '<a class="center"></a>';
      echo '<a a href="' . $url . 'store/' . $type . '/' . $page .  '/?s=' . urlencode($_GET["s"]) . '&row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Tickets ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg" /></a>';
    echo '</div>';
  }
}
 ?>


<div class="ajax-response"></div>