<?php
//Require general file
require_once(dirname(__FILE__, 3). "/general.php");

//Set current user for changelog
$current_user = "Store";

//Check if ticket isn'texpired/not exiting
//Get state of group
$group = new Group();
$group->groupID = $_GET["group"];

if($group->values() === false) {
  header("Location: " . $url . "/store");
}elseif($group->availableTickets() <= 0) {
  header("Location: " . $url . "/store");
}elseif($group->timeWindow() === false) {
  header("Location: " . $url . "/store");
}

if( $group->values() === false ||
    $group->values()["payment_store"] != 1 ||
    $group->availableTickets() <= 0 ||
    $group->timeWindow() === false ) {
      // Redirect
      header("Location: " . $url . "/store");
}

//Check ADFS
if($group->values()["adfs"] == 1) {
  //Require login
  require_once( SIMPLE_SAML_CONFIG );

  //Request login
  $as = new \SimpleSAML\Auth\Simple('default-sp');
  $as->requireAuth();

  //Get infos
  $user_informations = $as->getAttributes();

  //Clean session
  $session = \SimpleSAML\Session::getSessionFromRequest();
  $session->cleanup();
}

/* Add ticket if required */
if(!empty($_POST)) {

  $ticket = new Ticket();

  /* Get coupon */
  if(! empty($_POST["coupon"])) {
    $coupon = new Coupon();
    $couponID = $coupon->get_couponID( $_POST["coupon"], $_GET["group"] );
  }

  // Define values
  $values = array();
  $customADFS = json_decode($group->values()["adfs_custom"], true); //Custom adfs

  //Add payment state / coupon / groupID / email
  $values["groupID"] = $_POST["groupID"] ?? '';
  $values["email"] =  ((isset($user_informations[$customADFS["email"]][0])) ? $user_informations[$customADFS["email"]][0] : ($_POST["email"] ?? '')); //Required
  $values["payment"] = 2;
  $values["coupon"] = $couponID ?? '';

  //Add custom values
  foreach(json_decode($group->values()["custom"], true) as $input) {
    $name = $input["name"]; //Define name

    //Update array
    if(isset($user_informations[$customADFS[$input["id"]]][0]) && !empty($user_informations[$customADFS[$input["id"]]][0])) { //ADFS priority
      $values[$name] = $user_informations[$customADFS[$input["id"]]][0];
    }elseif(isset($_POST[$name])) { //Post value
      $values[$name] = $_POST[$name];
    }else { //No value
      $values[$name] = "";
    }
  }

  //Create ticket
  $ticket = new Ticket();
  $add = $ticket->add($values, true, false);

  if( $add == 1 ) {
    /* Redirect to payment */
    header("Location: " . $url . "store/pay/?ticketToken=" . urlencode($ticket->ticketToken));
  }
}
 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>TKTDATA - BUY</title>

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
  </head>
  <body>
    <article class="buy">
      <?php
      if(isset($add)) {
        if($add == 6) {
          Action::fail("Coupon konnte nicht angewendet werden.");
        }elseif($add == 5) {
          Action::fail("Die Mail konnte nicht versendet werden.");
        }elseif($add == 4) {
          Action::fail("Das Zeitfenster um ein Ticket zu lösen ist <strong>nicht</strong></b> offen. Konsultiere die Gruppe für nähere Infomrationen.");
        }elseif($add == 3) {
          Action::fail("Die maximale Anzahl an Tickets wurde erreicht.");
        }elseif($add == 2) {
          Action::fail("Die maximale Anzahl an Tickets pro Benutzer wurde erreicht.");
        }elseif($add == 1) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> erstellt werden.");
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> erstellt werden.");
        }
      }
       ?>

      <!-- Go back to last step -->
      <div class="return-header">
        <span onclick="history.back();">&larr;</span>
      </div>

      <?php
      //Get fullscreen image
      if( isset( $group->values()["payment_background_fileID"] ) ) {
        $backgroundImgUrl = MediaHub::getUrl( $group->values()["payment_background_fileID"] );
      }else {
        $backgroundImgUrl = $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,3) . "/medias/store/background/*")[0], PATHINFO_BASENAME );
      }
      ?>
      <div class="buy-container">
        <div class="fullscreen-img" style="background-image: url('<?php echo $backgroundImgUrl; ?>');">
          <img class="logo" src="" />
        </div>

        <div class="buy-form-container">
          <form action="<?php echo $url . "store/buy/?group=" . $_GET["group"]; ?>" method="post">
            <!-- Hidden -->
            <input type="hidden" name="groupID" value="<?php echo $_GET["group"] ?>" />

            <!-- Ticket informations -->
            <div class="general">
              <span class="title"><?php echo $group->values()["name"]; ?></span>
              <span class="description"><?php echo $group->values()["description"]; ?></span>
            </div>

            <!-- User inputs -->
            <div class="custom">
              <!-- <h4>Kontaktangaben</h4> -->
              <?php
              $customUserInputs = json_decode($group->values()["custom"], true);
              $customADFS = json_decode($group->values()["adfs_custom"], true);

              //First
              echo '<label class="txt-input">';
                echo '<input type="text" name="email" ' . (isset($user_informations) &&  isset($user_informations[$customADFS["email"]]) ? ('value="' . $user_informations[$customADFS["email"]] . '" disabled') : ('required')) . '/>';
                echo '<span class="placeholder">E-Mail</span>';
              echo '</label>';

              if(! empty($customUserInputs)) {
                //Set id and remove unused elements
                for($i = 0; $i < count($customUserInputs); $i++) {
                  //Unset elements
                  unset($customUserInputs[$i]["placeholder"]);

                  //Set new values
                  $customUserInputs[$i] = array_merge(array("id" => $i), $customUserInputs[$i]); //Id of input
                }

                //Order array by user input
                foreach($customUserInputs as $key => $value) {
                  $orders[$key] = intval($value["order"]);
                }
                array_multisort($orders, SORT_ASC, $customUserInputs);

                //Display inputs
                foreach($customUserInputs as $customInput) {
                  //Parameters
                  $adfs_value = (isset($user_informations[$customADFS[$customInput["id"]]]) ? $user_informations[$customADFS[$customInput["id"]]] : "");
                  $disabled = (isset($user_informations[$customADFS[$customInput["id"]]]) ? "disabled" : "");
                  $required = (($customInput["required"] == 1 && empty($disabled)) ? "required" : ""); //Check disabled does not need a value

                  //Parameters merged
                  $parameters = $adfs_value . ' ' . $disabled . ' ' . $required;
                  switch( $customInput["type"] ) {
                    //---------------------------- Select-input ----------------------------//
                    case "select":
                      $options = explode(",", $customInput["value"]);
                      echo  '<div class="select" onclick="toggleOptions(this)">';
                        echo  '<input type="text" class="selectValue" name="' . $customInput["id"] . '"' . $required . ' ' . $disabled . '>';
                        echo  '<span class="headline">-- Auswahl treffen --</span>';

                        echo  '<div class="options">';
                          foreach($options as $option) {
                            if($option != "") {
                              echo  '<span data-value="' . $option . '" onclick="selectElement(this)" ' . ((!empty($adfs_value) && $adfs_value == $option) ? "selected" : "") . '>' . $option . '</span>';
                            }
                          }

                        echo  '</div>';
                      echo  '</div>';
                    break;
                    //---------------------------- Radio-input ----------------------------//
                    case "radio":
                      $options = explode(",", $customInput["value"]);
                      echo  '<div class="radio-input-container">';
                        echo  $customInput["name"];
                        foreach($options as $option) {
                          if($option != "") {
                            echo  '<label class="radio">';
                              echo  '<input type="radio" name="' . $customInput["id"] . '" ' . ((!empty($adfs_value) && $adfs_value == $option) ? "checked" : "") . ' ' . $required . ' ' . $disabled . '/>';
                              echo  '<div title="Auswahl treffen"></div>';
                              echo  $option;
                            echo  '</label>';
                          }
                        }
                      echo  '</div>';
                    break;
                    //---------------------------- Checkbox-input ----------------------------//
                    case "checkbox":
                      echo  '<label class="checkbox">';
                        echo  '<input type="checkbox" name="' . $customInput["id"] . '" ' . ((!empty($adfs_value) && $adfs_value == $option) ? "checked" : "") . ' ' . $required . ' ' . $disabled . '/>';
                        echo  '<div title="Häcken setzen"></div>';
                        echo  $customInput["name"];
                      echo  '</label>';
                    break;
                    //---------------------------- Textarea ----------------------------//
                    case "textarea":
                      echo  '<label class="txt-input">';
                        echo  '<textarea name="' . $customInput["id"] . '" rows="5" ' . $required . ' ' . $disabled . '> ' . $adfs_value . '</textarea>';
                        echo  '<span class="placeholder">' . $customInput["name"] . '</span>';
                      echo  '</label>';
                    break;
                    //---------------------------- Text-input [Mail, Number, Date] ----------------------------//
                    default: //Text input
                      echo  '<label class="txt-input">';
                        echo  '<input type="' . $customInput["type"] . '" name="' . $customInput["id"] . '" ' . $parameters . '/>';
                        echo  '<span class="placeholder">' . $customInput["name"] . '</span>';
                      echo  '</label>';
                  }
                }
              }
               ?>
            </div>

            <!-- Coupon -->
            <div class="coupon">
              <span class="toggle" onclick="showCouponForm(document.getElementsByClassName('coupon')[0], <?php echo $group->groupID; ?>);">Coupon einlösen</span>
            </div>

            <!-- Price -->
            <div class="price_tag">
              <span class="name">Zu bezahlen:</span>
              <span class="price"><?php echo number_format(($group->values()["price"] + ($group->values()["vat"] / 10000) * $group->values()["price"]) / 100, 2); ?></span>
              <span class="discount_price"></span>
              <span class="unit"><?php echo $group->values()["currency"]; ?></span>
            </div>

            <!-- Pay -->
            <button>
              <div class="container">
                <img src="<?php echo $url; ?>medias/store/icons/pay.svg" />
                <span>BEZAHLEN</span>
              </div>
            </button>

          </form>
        </div>
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
