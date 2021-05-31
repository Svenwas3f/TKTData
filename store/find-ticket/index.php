<?php
//Require general file
require_once(dirname(__FILE__, 3) . "/general.php");

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - TICKET</title>

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
    <script src="<?php echo $url; ?>store/find-ticket/ajax.js"></script>

  </head>
  <body>
    <article>

      <header>
        <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
          <a href="<?php echo $url; ?>store/"><img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png"></a>
        </div>
      </header>

      <form action="" method="post"  class="search_bar">
        <label>
          <input type="email" name="search" placeholder="Deine E-Mail" value="<?php echo $_POST["search"] ?? ''; ?>" required/>
          <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
        </label>
      </form>

      <div class="ticket_responses">
        <?php
        //Check if check is requested
        if(! empty($_POST["search"])) {
          //Set connection variable
          $conn = Access::connect();

          //Select all response
          $stmt = $conn->prepare("SELECT * FROM " . TICKETS . " WHERE email=:email ORDER BY purchase_time DESC");
          $stmt->execute(array(
            ":email" => $_POST["search"]
          ));
          $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $response = array_filter($response, 'is_array');
          if(count( $response ) > 0) {

            for($i = 0; $i < count($response); $i++) {
              //Get current ID
              $currentID = (count($response) - 1) - $i;

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
                    echo '<img  src="' . MediaHub::getUrl( $group->values()["payment_logo_fileID"] ) .'"/>';
                  }else {
                    echo '<img  src="' . $url . 'medias/store/favicon-color-512.png"/>';
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
                  echo '<button onclick="ajax_send_mail(\'' . $_POST["search"] . '\', \'' . $currentID . '\')">Erneut senden</button>';
                  echo '<button onclick="window.open(\'' . $url . 'store/faq/#contact\', \'_blank\')">Veranstalter</button>';
                echo '</div>';

              echo '</div>';
            }
          } else {
            echo '<div class="not-found"><img src="' . $url . 'medias/icons/not-found.svg" /></div>';
          }

        }
         ?>
      </div>


      <div class="ajax-response">

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
