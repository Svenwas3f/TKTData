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
            echo '<div class="error">' . Language::string( 40, null, "store") . '</div>';
          }
           ?>
          <div class="headline">
            <?php
            if( $ticket->values()["payment"] != 2) { //No payment required
              echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
              echo '<span' . Language::string( 41, null, "store") . '></span>';
            } elseif ( $transaction["transaction_retrieve_status"] == false ) {
              echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
              echo '<span>' . Language::string( 42, null, "store") . '</span>';
            } elseif ( $transaction["status"] == "confirmed") {
              echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
              echo '<span>' . Language::string( 43, null, "store") . '</span>';
            } elseif ($transaction["pspId"] == 15 ) { //http://developers.payrexx.com/docs/miscellaneous
              echo '<img src="' . $url . 'medias/store/icons/waiting.svg" />';
              echo '<span>' . Language::string( 44, null, "store") . '</span>';
            } elseif ($transaction["pspId"] == 27) { //http://developers.payrexx.com/docs/miscellaneous
              echo '<img src="' . $url . 'medias/store/icons/waiting.svg" />';
              echo '<span>' . Language::string( 45, null, "store") . '</span>';
            } else {
              echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
              echo '<span>' . Language::string( 46, null, "store") . '</span>';
            }
             ?>
          </div>



          <div class="message">
            <?php
            if( $ticket->values()["payment"] != 2) { //Payemt done
              echo Language::string( 47, array(
                      '%mail%' => $ticket->values()["email"],
                    ), "store" );
            } elseif ( $transaction["status"] == "confirmed") {
              echo Language::string( 48, array(
                      '%mail%' => $ticket->values()["email"],
                    ), "store" );
            } elseif ($transaction["status"] == 15 ) { //http://developers.payrexx.com/docs/miscellaneous
              echo Language::string( 49, array(
                      '%mail%' => $ticket->values()["email"],
                    ), "store" );
            } elseif ($transaction["status"] == 27) { //http://developers.payrexx.com/docs/miscellaneous
              echo Language::string( 50, array(
                      '%mail%' => $ticket->values()["email"],
                    ), "store" );
            } else {
              echo Language::string( 51, array(
                      '%mail%' => $ticket->values()["email"],
                    ), "store" );
            }
             ?>
          </div>

          <div class="details">
            <!-- Preis -->
            <div class="item">
              <span class="info"><?php echo Language::string( 52, null, "store"); ?></span>
              <span class="value"><?php echo number_format(((($group->values()["price"]) + ($group->values()["price"] * $group->values()["vat"] / 10000)) / 100), 2) . ' ' . $group->values()["currency"]; ?></span>
            </div>

            <!-- Coupon -->
            <?php
            //Prepare coupon display
            if(! empty( $ticket->values()["coupon"] )) {
              $coupon = new Coupon();
              $coupon->couponID = $ticket->values()["coupon"];
              $coupon = '-' . (empty($coupon->values()["discount_percent"]) ? ($coupon->values()["discount_absolute"] / 100) . " " . $group->values()["currency"] : ($coupon->values()["discount_percent"] / 100 . "%"));
            }else {
              $coupon = Language::string( 53, null, "store");
            }
             ?>
            <div class="item">
              <span class="info"><?php echo Language::string( 54, null, "store"); ?></span>
              <span class="value"><?php echo $coupon; ?></span>
            </div>

            <?php
            if( $ticket->values()["payment"] != 2 ) { //Payment done
                $payment_state = Language::string( 55, null, "store");;
                $payment_time = isset($ticket->values()["payment_time"]) ? date("d.m.Y H:i", strtotime( $ticket->values()["payment_time"] )) : '--.--.---- --:--';
            } elseif ( $transaction["transaction_retrieve_status"] == false ) {
              $payment_state = Language::string( 56, null, "store");;
              $payment_time = '--.--.---- --:--';
            }else {
              switch($transaction["status"]) {
                case "waiting":
                  $payment_state = Language::string( 56, null, "store");
                break;
                case "confirmed":
                  $payment_state = Language::string( 57, null, "store");;
                break;
                case "authorized":
                  $payment_state = Language::string( 58, null, "store");;
                break;
                case "reserved":
                  $payment_state = Language::string( 59, null, "store");;
                break;
                default:
                  $payment_state = Language::string( 60, null, "store");;
                break;
              }

              $payment_time = ($ticket->values()["payment_time"] != 'confirmed') ? '--.--.---- --:--' : date("d.m.Y H:i", strtotime( $transaction["time"] ));
            }
            ?>

            <!-- Status -->
            <div class="item">
              <span class="info"><?php echo Language::string( 61, null, "store"); ?></span>
              <span class="value"><?php echo $payment_state; ?></span>
            </div>

            <!-- Zahldatum -->
            <div class="item">
              <span class="info"><?php echo Language::string( 62, null, "store"); ?></span>
              <span class="value"><?php echo $payment_time; ?></span>
            </div>

            <!-- Total -->
            <div class="item total">
              <span class="info"><?php echo Language::string( 63, null, "store"); ?></span>
              <span class="value"><?php echo number_format(($ticket->values()["amount"] / 100), 2) . ' ' . $group->values()["currency"]; ?></span>
            </div>
          </div>

          <div class="footer">
            <?php echo Language::string( 64, null, "store"); ?>
          </div>
        </div>


      </div>
    </article>
