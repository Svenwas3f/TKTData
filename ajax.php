<?php
//Start session
session_start();

//Require general file
require_once( dirname(__FILE__) . "/general.php");

header("Access-Control-Allow-Orgin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

//Check page
switch($_POST["p"]) {
  /**
   * Ticket page
   */
  case 7:
    switch($_POST["action"]) {
      case "get_custom":
        if(User::r_access_allowed(10, $current_user)) {
          $group = new Group();
          $group->groupID = json_decode($_POST["values"], true)["groupID"];
          $customUserInputs = json_decode($group->values()["custom"], true);

          if(! empty($customUserInputs)) {
            //Set id and remove unused elements
            for($i = 0; $i < count($customUserInputs); $i++) {
              //Unset elements
              unset($customUserInputs[$i]["placeholder"]);
              unset($customUserInputs[$i]["required"]);

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
              switch( $customInput["type"] ) {
                //---------------------------- Select-input ----------------------------//
                case "select":
                  $options = explode(",", $customInput["value"]);
                  echo  '<div class="select" onclick="toggleOptions(this)">';
                    echo  '<input type="text" class="selectValue" name="' . $customInput["id"] . '">';
                    echo  '<span class="headline">-- Auswahl treffen --</span>';

                    echo  '<div class="options">';
                      foreach($options as $option) {
                        if($option != "") {
                          echo  '<span data-value="' . $option . '" onclick="selectElement(this)">' . $option . '</span>';
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
                        //Define if current element is value
                        $currentValue = str_replace(" ", "_", $customInput["value"]);

                        echo  '<label class="radio">';
                          echo  '<input type="radio" name="' . $customInput["id"] . '" />';
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
                    echo  '<input type="checkbox" name="' . $customInput["id"] . '" value="' . $customInput["value"] . '" />';
                    echo  '<div title="Häcken setzen"></div>';
                    echo  $customInput["name"];
                  echo  '</label>';
                break;
                //---------------------------- Textarea ----------------------------//
                case "textarea":
                  echo  '<label class="txt-input">';
                    echo  '<textarea name="' . $customInput["id"] . '" rows="5" >' .$customInput["value"] . '</textarea>';
                    echo  '<span class="placeholder">' . $customInput["name"] . '</span>';
                  echo  '</label>';
                break;
                //---------------------------- Text-input [Mail, Number, Date] ----------------------------//
                default: //Text input
                  echo  '<label class="txt-input">';
                    echo  '<input type="' . $customInput["type"] . '" name="' . $customInput["id"] . '" value="' .$customInput["value"] . '" />';
                    echo  '<span class="placeholder">' . $customInput["name"] . '</span>';
                  echo  '</label>';
              }
            }
          }

        }
      break;
      case "get_coupons":
        if(User::r_access_allowed(10, $current_user)) {
          //Create connection
          $conn = Access::connect();

          //Get infos
          $coupons = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE groupID=:gid");
          $coupons->execute(array(":gid" => json_decode($_POST["values"], true)["groupID"]));

          if($coupons->rowCount() > 0) {
            echo '<div class="select" onclick="toggleOptions(this)">';
              echo '<input type="text" class="selectValue" name="coupon" value="">';
              echo '<span class="headline">Wähle Coupon</span>';

              echo '<div class="options">';
                echo '<span data-value="" onclick="selectElement(this)">Kein Coupon verwenden</span>';
                foreach($coupons->fetchAll(PDO::FETCH_ASSOC) as $coupon) {
                  $couponPrice = new Coupon();
                  $couponPrice->couponID = $coupon["couponID"];
                  $couponPrice = $couponPrice->new_price();

                  $group = new Group();
                  $group->groupID = json_decode($_POST["values"], true)["groupID"];
                  echo '<span data-value="' . $coupon["couponID"] . '" onclick="selectElement(this)">' . $coupon["name"] . ' (Neuer Preis: ' . ($couponPrice/100) . ' ' . $group->values()["currency"] . ')</span>';
                }
              echo '</div>';
            echo '</div>';
          }
        }
      break;
    }
  break;
  /**
   * Information page
   */
  case 10:
    switch ($_POST["action"]) {
      case "get_info":
        if(User::r_access_allowed(10, $current_user)) {
          echo Scanner::readInfo( json_decode($_POST["values"], true)["reqType"] );
        }
      break;
      case "update_info":
        if(User::w_access_allowed(9, $current_user)) {
          Scanner::updateInfo( json_decode($_POST["values"], true)["content"] );
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
    }
  break;

  /**
   * Scann with camera
   */
  case 11:
    switch($_POST["action"]) {
      case "get_ticket":
        if(User::r_access_allowed(10, $current_user)) {
          $scann = new Scanner();
          $scann->ticketToken = json_decode($_POST["values"], true)["ticketToken"];

          //Update payment if required
          checkPayment( $scann->ticketToken );

          echo $scann->ticketInfoHTML( json_decode($_POST["values"], true)["qr"] ); //Check if video needs to be played again
        }
      break;
      case "get_fullscreen_info":
        if(User::w_access_allowed($page, $current_user)) {
          $ticket = new Ticket();
          $ticket->ticketToken = json_decode($_POST["values"], true)["ticketToken"];

          //Update payment if required
          checkPayment( $ticket->ticketToken );

          //Return state
          $state = $ticket->values()["state"];
          $payment = $ticket->values()["payment"];

          if($ticket->values() == false) { //Ticket does not exist
            echo json_encode(
              array(
                "color" => "#df2383",
                "img" => $url . "medias/icons/error.svg",
                "message" => "Dieses Ticket existiert nicht. Bitte melden Sie sich beim Personal",
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => true
              )
            );
          }elseif($payment == 2) {//payment expected
            echo json_encode(
              array(
                "color" => "#e53af8",
                "img" => $url . "medias/icons/error.svg",
                "message" => "Dieses Ticket wurde noch nicht bezahlt. Bitte melden Sie sich beim Personal",
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => true
              )
            );
          }elseif($state == 0) {//Ticket available
            if($ticket->employ()) {
              echo json_encode(
                array(
                  "color" => "#35e25e",
                  "img" => $url . "medias/icons/success.svg",
                  "sound" => array($url . "medias/audio/success.mp3", $url . "medias/audio/success.oog", $url . "medias/audio/success.wav"),
                  "message" => "Herzlich Willkommen",
                  "button" => false
                )
              );
            }else {
              echo json_encode(
                array(
                  "color" => "#3f4a57",
                  "img" => $url . "medias/icons/error.svg",
                  "message" => "Beim einlösen des Tickets ist ein Fehler aufgetreten. Bitte melden Sie sich beim Personal",
                  "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                  "button" => true
                )
              );
            }
          }elseif($state == 1) {//Ticket used
            echo json_encode(
              array(
                "color" => "#2a78a9",
                "img" => $url . "medias/icons/error.svg",
                "message" => "Dieses Ticket wurde bereits verwendet. Bitte melden Sie sich beim Personal",
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => true
              )
            );
          }elseif($state == 2) {//Ticket blocked
            echo json_encode(
              array(
                "color" => "#d9003d",
                "img" => $url . "medias/icons/error.svg",
                "message" => "Dieses Ticket wurde blockiert. Bitte melden Sie sich beim Personal",
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => true
              )
            );
          }else { //Unknown error
            echo json_encode(
              array(
                "color" => "#3c2583",
                "img" => $url . "medias/icons/error.svg",
                "message" => "Ein unbekannter Fehler ist aufgetreten. Bitte melden Sie sich beim Personal",
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => true
              )
            );
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
      case "employ_ticket":
        if(User::w_access_allowed($page, $current_user)) {
          $scann = new Scanner();
          $scann->ticketToken = json_decode($_POST["values"], true)["ticketToken"];

          //Update payment if required
          checkPayment( $scann->ticketToken );

          if($scann->ticketEmploy()) {
            return Action::success("Das Ticket wurde <b>erfolgreich aktiviert</b>");
          }else {
            return Action::fail("Das Ticket wurde <b>nicht aktiviert</b>");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
      case "reactivate_ticket";
      break;
    }
  break;

  /**
   * Livedata live informations
   */
  case 15:
    switch($_POST["action"]) {
      case "up":
        if(User::w_access_allowed($page, $current_user)) {
          if(! Livedata::up()) {
            Action::fail("Es konnte nicht hochgezählt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
      case "down":
        if(User::w_access_allowed($page, $current_user)) {
          if(! Livedata::down()) {
            Action::fail("Es konnte nicht heruntergezählt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
      case "trend":
        if(User::r_access_allowed($page, $current_user)) {
          switch(Livedata::trend()) {
            case 0:
              echo $url . "medias/icons/arrow_up.svg";
            break;
            case 1:
              echo $url . "medias/icons/arrow_down.svg";
            break;
            case 2:
              echo $url . "medias/icons/arrow_equal.svg";
            break;
          }
        }
      break;
      case "visitors":
        if(User::r_access_allowed($page, $current_user)) {
          echo Livedata::visitors();
        }
      break;
      case "history":
        if(User::r_access_allowed($page, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(Livedata::history($min, $max));
        }
      break;
      case "historyUp":
        if(User::r_access_allowed($page, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(Livedata::historyUp($min, $max));
        }
      break;
      case "historyDown":
        if(User::r_access_allowed($page, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(Livedata::historyDown($min, $max));
        }
      break;
    }
  break;

  /**
   * Checkout
   */
  case 19:
    switch($_POST["action"]) {
      case "add_right":
        if(User::w_access_allowed($page, $current_user)) {
          // Set values
          $values = json_decode( $_POST["values"], true );

          // Add write rights
          $checkout = new Checkout();
          $checkout->cashier = $values["checkout"];

          // Remove access if exitst
          if(! $checkout->remove_access( $values["user"] ) ) {
            Action::fail("Die Rechte konnten nicht hinzugefügt werden");
          }

          if( ($values["type"] ?? "r") == "w") {
            $access_values = array(
              "checkout_id" => $values["checkout"],
              "user_id" => $values["user"],
              "w" => 1,
              "r" => 1,
            );
          }else {
            $access_values = array(
              "checkout_id" => $values["checkout"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 1,
            );
          }

          if(! $checkout->add( Checkout::ACCESS_TALBE, $access_values ) ) {
            Action::fail("Die Rechte konnten nicht hinzugefügt werden");
            return false;
          }

          // Access to page if not exitst
          $user = new User();
          $user->user = $values["user"];

          if( ($values["type"] ?? "r") == "w") {
            $new_rights = $user->rights();
            $new_rights[16] = array_unique( array_merge($new_rights[16], array("w", "r")));
            $new_rights[17] = array_unique( array_merge($new_rights[17], array("w", "r")));
          }else {
            $new_rights = $user->rights();
            $new_rights[16] = array_unique( array_merge($new_rights[16], array("r")));
            $new_rights[17] = array_unique( array_merge($new_rights[17], array("r")));
          }

          if(! $user->updateRights( $new_rights ) ) {
            Action::fail("Die Rechte konnten nicht hinzugefügt werden");
            return false;
          }

          // All good
          if( ($values["type"] ?? "r") == "w") {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/toggleCheckoutRights2.svg",
              "title_w" => $values["user"] . " hat Schreibrechte auf diese Kasse",
              "onclick_name_w" => "checkout_remove_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'w')",

              "img_r" => $url . "/medias/icons/toggleCheckoutRights2.svg",
              "title_r" => $values["user"] . " hat Leserechte auf diese Kasse",
              "onclick_name_r" => "checkout_remove_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'r')",
            ));
          }else {
            echo json_encode(array(
              "img_r" => $url . "/medias/icons/toggleCheckoutRights1.svg",
              "title_r" => $values["user"] . " hat keine Schreibrechte auf diese Kasse",
              "onclick_name_r" => "checkout_add_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'w')",

              "img_r" => $url . "/medias/icons/toggleCheckoutRights2.svg",
              "title_r" => $values["user"] . " hat Leserechte auf diese Kasse",
              "onclick_name_r" => "checkout_remove_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'r')",
            ));
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
      case "remove_right":
        if(User::w_access_allowed($page, $current_user)) {
          // Set values
          $values = json_decode( $_POST["values"], true );

          // Add write rights
          $checkout = new Checkout();
          $checkout->cashier = $values["checkout"];

          // Remove access if exitst
          if(! $checkout->remove_access( $values["user"] ) ) {
            Action::fail("Die Rechte konnten nicht hinzugefügt werden");
          }

          if( ($values["type"] ?? "r") == "w") {
            $access_values = array(
              "checkout_id" => $values["checkout"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 1,
            );
          }else {
            $access_values = array(
              "checkout_id" => $values["checkout"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 0,
            );
          }

          if(! $checkout->add( Checkout::ACCESS_TALBE, $access_values ) ) {
            Action::fail("Die Rechte konnten nicht hinzugefügt werden");
            return false;
          }

          // All good
          if( ($values["type"] ?? "r") == "w") {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/toggleCheckoutRights1.svg",
              "title_w" => $values["user"] . " hat keine Schreibrechte auf diese Kasse",
              "onclick_name_w" => "checkout_add_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'w')",

              "img_r" => $url . "/medias/icons/toggleCheckoutRights2.svg",
              "title_r" => $values["user"] . " hat Leserechte auf diese Kasse",
              "onclick_name_r" => "checkout_remove_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'r')",
            ));
          }else {
            echo json_encode(array(
              "img_r" => $url . "/medias/icons/toggleCheckoutRights1.svg",
              "title_r" => $values["user"] . " hat keine Schreibrechte auf diese Kasse",
              "onclick_name_r" => "checkout_add_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'w')",

              "img_r" => $url . "/medias/icons/toggleCheckoutRights1.svg",
              "title_r" => $values["user"] . " hat keine Leserechte auf diese Kasse",
              "onclick_name_r" => "checkout_add_right(this, '" . $values["user"] . "', '" . $checkout->cashier . "', 'r')",
            ));
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      break;
    }
  break;
}
 ?>
