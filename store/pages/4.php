<?php
//Check if ticketToken
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

//Get group infos
$group = new Group();
$group->groupID = $ticket->values()["groupID"];

//Get transaction
$transaction = retrieveTransaction( $ticket->ticketToken );

//Update payment if required
checkPayment( $ticket->ticketToken );

//Send mail
if( $transaction["transaction_retrieve_status"] == false || $transaction["pspId"] == 15 || $transaction["pspId"] == 27 || $ticket->values()["payment"] == 2 ) {
  $error = $ticket->requestPayment( $ticket->values()["email"] ); //Rechnung senden
}else {
  $error = $ticket->sendTicket( $ticket->values()["email"] ); //Ticket senden
}
 ?>
    <article style="background: url(<?php echo $url;?>medias/store/icons/background.svg)">
      <div class="ticket-container">

        <div class="response-container">
          <?php
          if( $error === false ) {
            echo '<div class="error">Die Mail konnte nicht gesendet werden. Laden Sie die Seite neu um es noch einmal zu versuchen.</div>';
          }
           ?>
          <div class="headline">
            <?php
            if( $ticket->values()["payment"] != 2) { //No payment required
              echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
              echo '<span>Zahlung erfolgreich</span>';
            } elseif ( $transaction["transaction_retrieve_status"] == false ) {
              echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
              echo '<span>Zahlung fehlgeschlagen</span>';
            } elseif ( $transaction["status"] == "confirmed") {
              echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
              echo '<span>Zahlung erfolgreich</span>';
            } elseif ($transaction["pspId"] == 15 ) { //http://developers.payrexx.com/docs/miscellaneous
              echo '<img src="' . $url . 'medias/store/icons/waiting.svg" />';
              echo '<span>Zahlung erwartet</span>';
            } elseif ($transaction["pspId"] == 27) { //http://developers.payrexx.com/docs/miscellaneous
              echo '<img src="' . $url . 'medias/store/icons/waiting.svg" />';
              echo '<span>Zahlung erwartet</span>';
            } else {
              echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
              echo '<span>Zahlung fehlgeschlagen</span>';
            }
             ?>
          </div>



          <div class="message">
            <?php
            if( $ticket->values()["payment"] != 2) { //Payemt done
              echo "Hallo " . $ticket->values()["email"] . "<br />
              <br />
              Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und wird dir zeitnahe per Mail zugestellt. Die Zahlung ist bei uns bereits eingegangen. <br />
              Speichere dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.";
            } elseif ( $transaction["status"] == "confirmed") {
              echo "Hallo " . $ticket->values()["email"] . "<br />
              <br />
              Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und wird dir zeitnahe per Mail zugestellt. Die Zahlung ist bei uns bereits eingegangen. <br />
              Speichere dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.";
            } elseif ($transaction["status"] == 15 ) { //http://developers.payrexx.com/docs/miscellaneous
              echo "Hallo " . $ticket->values()["email"] . "<br />
              <br />
              Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt. <br />
              Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.";
            } elseif ($transaction["status"] == 27) { //http://developers.payrexx.com/docs/miscellaneous
              echo "Hallo " . $ticket->values()["email"] . "<br />
              <br />
              Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt. <br />
              Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.";
            } else {
              echo "Hallo " . $ticket->values()["email"] . "<br />
              <br />
              Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt. <br />
              Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.";
            }
             ?>
          </div>

          <div class="details">
            <!-- Preis -->
            <div class="item">
              <span class="info">Preis:</span>
              <span class="value"><?php echo ((($group->values()["price"]) + ($group->values()["price"] * $group->values()["vat"] / 10000)) / 100) . ' ' . $group->values()["currency"]; ?></span>
            </div>

            <!-- Coupon -->
            <?php
            //Prepare coupon display
            if(! empty( $ticket->values()["coupon"] )) {
              $coupon = new Coupon();
              $coupon->couponID = $ticket->values()["coupon"];
              $coupon = '-' . (empty($coupon->values()["discount_percent"]) ? ($coupon->values()["discount_absolute"] / 100) . " " . $group->values()["currency"] : ($coupon->values()["discount_percent"] / 100 . "%"));
            }else {
              $coupon = 'Nicht verwendet';
            }
             ?>
            <div class="item">
              <span class="info">Coupon:</span>
              <span class="value"><?php echo $coupon; ?></span>
            </div>

            <?php
            if( $ticket->values()["payment"] != 2 ) { //Payment done
                $payment_state = "Bereits getätigt";
                $payment_time = isset($ticket->values()["payment_time"]) ? date("d.m.Y H:i", strtotime( $ticket->values()["payment_time"] )) : '--.--.---- --:--';
            } elseif ( $transaction["transaction_retrieve_status"] == false ) {
              $payment_state = "erwartet";
              $payment_time = '--.--.---- --:--';
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

              $payment_time = ($ticket->values()["payment_time"] != 'confirmed') ? '--.--.---- --:--' : date("d.m.Y H:i", strtotime( $transaction["time"] ));
            }
            ?>

            <!-- Status -->
            <div class="item">
              <span class="info">Status:</span>
              <span class="value"><?php echo $payment_state; ?></span>
            </div>

            <!-- Zahldatum -->
            <div class="item">
              <span class="info">Zahldatum:</span>
              <span class="value"><?php echo $payment_time; ?></span>
            </div>

            <!-- Total -->
            <div class="item total">
              <span class="info">Total:</span>
              <span class="value"><?php echo ($ticket->values()["amount"] / 100) . ' ' . $group->values()["currency"]; ?></span>
            </div>
          </div>

          <div class="footer">
            Ticket proudly provided by <span>TKTDATA</span>
          </div>
        </div>


      </div>
    </article>
