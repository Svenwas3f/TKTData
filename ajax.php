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
        if(User::r_access_allowed(7, $current_user)) {
          // Start form
          $form = new HTML('form', array(
            'method' => 'post',
            'action' => $url,
          ));

          // Start group
          $group = new Group();
          $group->groupID = json_decode($_POST["values"], true)["groupID"];
          $customUserInputs = json_decode($group->values()["custom"], true);

          if(! empty($customUserInputs)) {
            foreach($customUserInputs as $customInput) {
              switch( $customInput["type"] ) {
                //---------------------------- Select-input ----------------------------//
                case "select":
                  $options = explode(",", $customInput["options"]);

                  $form->addElement(
                    array(
                      'type' => 'select',
                      'name' => $customInput["id"],
                      'value' =>  (($customInput["value"] == "") ? "" : $customInput["value"]),
                      'options' => array_combine($options, $options), // Generate correct array
                      'disabled' => ! User::w_access_allowed($page, $current_user),
                      'required' => $customInput["required"],
                    ),
                  );
                break;
                //---------------------------- Radio-input ----------------------------//
                case "radio":
                  $options = explode(",", $customInput["options"]);

                  foreach($options as $option) {
                    $form->addElement(
                      array(
                        'type' => 'radio',
                        'name' => $customInput["id"],
                        'value' =>  str_replace(" ", "_", $option) ?? '',
                        'context' => $option,
                        'checked' => (str_replace(" ", "_", $customInput["value"]) == $option) ? true : false,
                        'disabled' => ! User::w_access_allowed($page, $current_user),
                        'required' => $customInput["required"],
                      ),
                    );
                  }
                break;
                //---------------------------- Checkbox-input ----------------------------//
                case "checkbox":
                  $form->addElement(
                    array(
                      'type' => 'checkbox',
                      'name' => $customInput["id"],
                      'value' =>  $customInput["value"],
                      'context' => $customInput["name"],
                      'checked' => ! empty($customInput["value"]),
                      'disabled' => ! User::w_access_allowed($page, $current_user),
                      'required' => $customInput["required"],
                    ),
                  );
                break;
                //---------------------------- Textarea ----------------------------//
                case "textarea":
                  $form->addElement(
                    array(
                      'type' => 'textarea',
                      'name' => $customInput["id"],
                      'value' => $customInput["value"],
                      'placeholder' => $customInput["name"],
                      'disabled' => ! User::w_access_allowed($page, $current_user),
                      'required' => $customInput["required"],
                    ),
                  );
                break;
                //---------------------------- Text-input [Mail, Number, Date] ----------------------------//
                default: //Text input
                  $form->addElement(
                    array(
                      'type' => $customInput["type"] ?? 'text',
                      'name' => $customInput["id"],
                      'value' => $customInput["value"],
                      'placeholder' => $customInput["name"],
                      'disabled' => ! User::w_access_allowed($page, $current_user),
                      'required' => $customInput["required"],
                    ),
                  );
              }
            }
          }

          $form->prompt();
        }
      break;
      case "get_coupons":
        if(User::r_access_allowed(7, $current_user)) {
          //Create connection
          $conn = Access::connect();

          // Start form
          $form = new HTML('form', array(
            'method' => 'post',
            'action' => $url,
          ));

          //Get infos
          $coupons = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE groupID=:gid");
          $coupons->execute(array(":gid" => json_decode($_POST["values"], true)["groupID"]));

          // Get input
          $headline = (empty($used_coupon->couponID)) ?
                      Language::string(49, null, 7) : //No coupon used
                      $used_coupon->values()["name"] . ' -' . (
                        empty($used_coupon->values()["discount_percent"]) ?
                          ($used_coupon->values()["discount_absolute"] / 100) . ' ' . $group->values()["currency"]  : //Correct absolute amount
                          ($used_coupon->values()["discount_percent"] / 100) . '%' //Correct percent
                      );

          $options = array(
            "" => Language::string(49, null, 7),
          );

          foreach($coupons->fetchAll(PDO::FETCH_ASSOC) as $coupon) {
            // Get new price
            $couponPrice = new Coupon();
            $couponPrice->couponID = $coupon["couponID"];
            $couponPrice = $couponPrice->new_price();

            // Get currency
            $group = new Group();
            $group->groupID = json_decode($_POST["values"], true)["groupID"];

            $options[$coupon["couponID"]] = $coupon["name"] . ' (Neuer Preis: ' . ($couponPrice/100) . ' ' . ($group->values()["currency"]  ?? DEFAULT_CURRENCY) . ')';
          }


          $form->addElement(
            array(
              'type' => 'select',
              'name' => 'coupon',
              'value' => '',
              'headline' => $headline,
              'options' => $options,
              'disabled' => ! User::w_access_allowed($page, $current_user),
            ),
          );

          $form->prompt();
        }
      break;
      case "get_string":
        echo Language::string( json_decode( $_POST["values"], true)["id"], null, 8 );
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
        if(User::w_access_allowed(10, $current_user)) {
          Scanner::updateInfo( json_decode($_POST["values"], true)["content"] );
        }else {
          Action::fail( Language::string( 1, null, 10 ) );
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
        if(User::r_access_allowed(11, $current_user)) {
          $scann = new Scanner();
          $scann->ticketToken = json_decode($_POST["values"], true)["ticketToken"];

          //Update payment if required
          checkPayment( $scann->ticketToken );

          echo $scann->ticketInfoHTML( json_decode($_POST["values"], true)["qr"] ); //Check if video needs to be played again
        }
      break;
      case "get_fullscreen_info":
        if(User::w_access_allowed(11, $current_user)) {
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
                "message" => Language::string(2, null, 11),
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => Language::string( 10, null, 11 ),
              )
            );
          }elseif($payment == 2) {//payment expected
            echo json_encode(
              array(
                "color" => "#e53af8",
                "img" => $url . "medias/icons/error.svg",
                "message" => Language::string(3, null, 11),
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => Language::string( 10, null, 11 ),
              )
            );
          }elseif($state == 0) {//Ticket available
            if($ticket->employ()) {
              echo json_encode(
                array(
                  "color" => "#35e25e",
                  "img" => $url . "medias/icons/success.svg",
                  "sound" => array($url . "medias/audio/success.mp3", $url . "medias/audio/success.oog", $url . "medias/audio/success.wav"),
                  "message" => Language::string(4, null, 11),
                  "button" => false
                )
              );
            }else {
              echo json_encode(
                array(
                  "color" => "#3f4a57",
                  "img" => $url . "medias/icons/error.svg",
                  "message" => Language::string(5, null, 11),
                  "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                  "button" => Language::string( 10, null, 11 ),
                )
              );
            }
          }elseif($state == 1) {//Ticket used
            echo json_encode(
              array(
                "color" => "#2a78a9",
                "img" => $url . "medias/icons/error.svg",
                "message" => Language::string(6, null, 11),
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => Language::string( 10, null, 11 ),
              )
            );
          }elseif($state == 2) {//Ticket blocked
            echo json_encode(
              array(
                "color" => "#d9003d",
                "img" => $url . "medias/icons/error.svg",
                "message" => Language::string(7, null, 11),
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => Language::string( 10, null, 11 ),
              )
            );
          }else { //Unknown error
            echo json_encode(
              array(
                "color" => "#3c2583",
                "img" => $url . "medias/icons/error.svg",
                "message" => Language::string(8, null, 11),
                "sound" => array($url . "medias/audio/error.mp3", $url . "medias/audio/error.oog", $url . "medias/audio/error.wav"),
                "button" => Language::string( 10, null, 11 ),
              )
            );
          }
        }else {
          Action::fail( Language::string(9, null, 11) );
        }
      break;
      case "employ_ticket":
        if(User::w_access_allowed(11, $current_user)) {
          $scann = new Scanner();
          $scann->ticketToken = json_decode($_POST["values"], true)["ticketToken"];

          //Update payment if required
          checkPayment( $scann->ticketToken );

          if($scann->ticketEmploy()) {
            return Action::success( Language::string( 12, null, "scanner") );
          }else {
            return Action::fail( Language::string( 13, null, "scanner") );
          }
        }else {
          Action::fail( Language::string( 14, null, "scanner") );
        }
      break;
    }
  break;

  /**
   * Livedata live informations
   */
  case 15:
    switch($_POST["action"]) {
      case "up":
        if(User::w_access_allowed(15, $current_user)) {
          if(! Livedata::up()) {
            Action::fail( Language::string( 1, null, 15 ));
          }
        }else {
          Action::fail( Language::string( 2, null, 15 ));
        }
      break;
      case "down":
        if(User::w_access_allowed(15, $current_user)) {
          if(! Livedata::down()) {
            Action::fail( Language::string( 3, null, 15 ));
          }
        }else {
          Action::fail( Language::string( 4, null, 15 ));
        }
      break;
      case "trend":
        if(User::r_access_allowed(15, $current_user)) {
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
        if(User::r_access_allowed(15, $current_user)) {
          echo Livedata::visitors();
        }
      break;
      case "history":
        if(User::r_access_allowed(15, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(Livedata::history($min, $max));
        }
      break;
      case "historyUp":
        if(User::r_access_allowed(15, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(
            array(
              'data' => Livedata::historyUp($min, $max),
              '3' => Language::string( 3, null, 14 ),
              '4' => Language::string( 4, null, 14 ),
              '5' => Language::string( 5, null, 14 ),
            ),
          );
        }
      break;
      case "historyDown":
        if(User::r_access_allowed(15, $current_user)) {
          //Set max and min
          $max = Livedata::live_time()["max"];
          $min = Livedata::live_time()["min"];

          //Get data
          echo json_encode(
            array(
              'data' => Livedata::historyDown($min, $max),
              '3' => Language::string( 3, null, 14 ),
              '4' => Language::string( 4, null, 14 ),
              '5' => Language::string( 5, null, 14 ),
            ),
          );
        }
      break;
    }
  break;

  /**
   * Payments
   */
  case 16:
    switch($_POST["action"]) {
      case "refundPayment":
        if(User::r_access_allowed(16, $current_user)) {
          // Start transaction
          $transaction = new Transaction();
          $transaction->paymentID = json_decode($_POST["values"], true)["paymentID"];

          // prepare amount
          $amount = (json_decode($_POST["values"], true)["amount"]) * 100;

          // Refund payment
          $refund = $transaction->refund( $amount );
          if( $refund === false) {
            echo json_encode(array(
              "error" => array(
                "id" => 70,
                "type" => "error",
              ),
            ));
          }elseif( is_string($refund) ) {
            echo json_encode(array(
              "error" => array(
                "id" => 71,
                "type" => "error",
                "replacements" => array(
                  "%refund%" => $refund,
                ),
              ),
            ));
          }else {
            echo json_encode(array(
              "refund" => $transaction->globalValues()["refund"],
              "formated_refund" => number_format(($transaction->globalValues()["refund"] / 100), 2),
              "fees" => $transaction->totalFees(),
              "formated_fees" => number_format(($transaction->totalFees() / 100), 2),
              "new_amount" => ($transaction->totalPrice() - $transaction->globalValues()["refund"]) / 100,
              "formated_new_amount" => number_format(($transaction->totalPrice() - $transaction->globalValues()["refund"] ?? 0) / 100,2),
              "currency" => $transaction->globalValues()["currency"] ?? DEFAULT_CURRENCY,
              "success" => array(
                "id" => 73,
                "type" => "success",
                "replacements" => array(
                  "%refund%" => number_format(($transaction->globalValues()["refund"] / 100), 2),
                  "%currency%" => $transaction->globalValues()["currency"] ?? DEFAULT_CURRENCY,
                ),
              ),
            ));
          }
        }else {
          echo json_encode(array(
            "error" => array(
              "id" => 72,
              "type" => "error",
            ),
          ));
        }
      break;
      case "message":
        if(json_decode($_POST["values"], true)["type"] == "success") {
          Action::success( Language::string(
            json_decode($_POST["values"], true)["id"],
            (json_decode($_POST["values"], true)["replacements"] ?? null),
             16,
          ));
        }else {
          Action::success( Language::string(
            json_decode($_POST["values"], true)["id"],
            (json_decode($_POST["values"], true)["replacements"] ?? null),
             16,
          ));
        }
      break;
      case "togglePickUp":
        if(User::r_access_allowed(16, $current_user)) {
          // Get transaction
          $transaction = new Transaction();
          $transaction->paymentID = json_decode($_POST["values"], true)["paymentID"];

          // Check update variable
          if( $transaction->globalValues()["pick_up"] == 1) {
            $transaction->update(array("pick_up" => 0));

            // Get message
            if($transaction->globalValues()["payment_state"] == 2) { // Payment expected
              $message = Language::string(19, null, 16);
            }else {
              $message = Language::string(20, null, 16);
            }

            // Return array
            echo json_encode(array(
              "pickedUp" => false,
              "message" => $message,
              "img_src" => $url . '/medias/icons/pickUp.svg'
            ));
          }else {
            $transaction->update(array("pick_up" => 1));

            // Get message
            if( $transaction->globalValues()["payment_state"]  == 2 && $transaction->globalValues()["pick_up"] == 1 ) { // Payment expected and picked up
              $message = Language::string(18, null, 16);
            }elseif ( $transaction->globalValues()["payment_state"]  == 2 ) { // Payment expected
              $message = Language::string(19, null, 16);
            }else {
              $message = Language::string(21, null, 16);
            }

            // Return array
            echo json_encode(array(
              "pickedUp" => true,
              "message" => $message,
              "img_src" => $url . '/medias/icons/pickedUp.svg'
            ));
          }
        }
      break;
      case "confirmPayment":
        if(User::r_access_allowed(16, $current_user)) {
            // Get transaction
            $transaction = new Transaction();
            $transaction->paymentID = json_decode($_POST["values"], true)["paymentID"];

            echo (($transaction->update(array("payment_state" => 1)) == true) ? "true" : "false");
        }
      break;
    }
  break;

  /**
   * Pub products
   */
  case 17:
    switch($_POST["action"]) {
      case "toggleVisibility":
          if(User::w_access_allowed(18, $current_user)) {
            // Change toggle
            $product = new Product();
            $product->pub = json_decode($_POST["values"], true)["pub"];
            $product->product_id = json_decode($_POST["values"], true)["product_id"];
            $product->toggleVisibility();

            // Return new image
            if( $product->visibility() ) {
              echo json_encode(array(
                "visibility" => "on",
                "img_src" => $url . '/medias/icons/visibility-on.svg'
              ));
            }else {
              echo json_encode(array(
                "visibility" => "off",
                "img_src" => $url . '/medias/icons/visibility-off.svg'
              ));
            }
          }
      break;
      case "update_availability":
        // Update availability
        $product = new Product();
        $product->pub = json_decode($_POST["values"], true)["pub"];
        $product->product_id = json_decode($_POST["values"], true)["product_id"];

        if( $product->update_availability( json_decode($_POST["values"], true)["availability"] ) ) {
          echo json_encode(array(
            "status" => true,
          ));
        }else {
          echo json_encode(array(
            "status" => false,
          ));
        }
      break;
    }
  break;

  /**
   * Pub
   */
  case 18:
    switch($_POST["action"]) {
      case "add_right":
        if(User::w_access_allowed(18, $current_user)) {
          // Set values
          $values = json_decode( $_POST["values"], true );

          // Add write rights
          $pub = new Pub();
          $pub->pub = $values["pub"];

          // Remove access if exitst
          if(! $pub->remove_access( $values["user"] ) ) {
            Action::fail( Language::string(544, null, 18) );
          }

          if( ($values["type"] ?? "r") == "w") {
            $access_values = array(
              "pub_id" => $values["pub"],
              "user_id" => $values["user"],
              "w" => 1,
              "r" => 1,
            );
          }else {
            $access_values = array(
              "pub_id" => $values["pub"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 1,
            );
          }

          if(! $pub->add( Pub::ACCESS_TALBE, $access_values ) ) {
            Action::fail( Language::string(54, null, 18) );
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
            Action::fail( Language::string(54, null, 18) );
            return false;
          }

          // All good
          if( ($values["type"] ?? "r") == "w") {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/togglePubRights2.svg",
              "title_w" => Language::string( 50, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_w" => "pub_remove_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'w')",

              "img_r" => $url . "/medias/icons/togglePubRights2.svg",
              "title_r" => Language::string( 52, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_r" => "pub_remove_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'r')",
            ));
          }else {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/togglePubRights1.svg",
              "title_w" => Language::string( 51, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_w" => "pub_add_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'w')",

              "img_r" => $url . "/medias/icons/togglePubRights2.svg",
              "title_r" => Language::string( 52, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_r" => "pub_remove_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'r')",
            ));
          }
        }else {
          Action::fail( Language::string( 55, null, 18 ) );
        }
      break;
      case "remove_right":
        if(User::w_access_allowed(18, $current_user)) {
          // Set values
          $values = json_decode( $_POST["values"], true );

          // Add write rights
          $pub = new Pub();
          $pub->pub = $values["pub"];

          // Remove access if exitst
          if(! $pub->remove_access( $values["user"] ) ) {
            Action::fail( Language::string( 54, null, 18 ) );
          }

          if( ($values["type"] ?? "r") == "w") {
            $access_values = array(
              "pub_id" => $values["pub"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 1,
            );
          }else {
            $access_values = array(
              "pub_id" => $values["pub"],
              "user_id" => $values["user"],
              "w" => 0,
              "r" => 0,
            );
          }

          if(! $pub->add( Pub::ACCESS_TALBE, $access_values ) ) {
            Action::fail( Language::string( 54, null, 18 ) );
            return false;
          }

          // All good
          if( ($values["type"] ?? "r") == "w") {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/togglePubRights1.svg",
              "title_w" => Language::string( 51, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_w" => "pub_add_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'w')",

              "img_r" => $url . "/medias/icons/togglePubRights2.svg",
              "title_r" => Language::string( 52, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_r" => "pub_remove_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'r')",
            ));
          }else {
            echo json_encode(array(
              "img_w" => $url . "/medias/icons/togglePubRights1.svg",
              "title_w" => Language::string( 51, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_w" => "pub_add_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'w')",

              "img_r" => $url . "/medias/icons/togglePubRights1.svg",
              "title_r" => Language::string( 53, array(
                '%user%' => $values["user"],
              ), 18 ),
              "onclick_name_r" => "pub_add_right(this, '" . $values["user"] . "', '" . $pub->pub . "', 'r')",
            ));
          }
        }else {
          Action::fail( Language::string( 55, null, 18 ) );
        }
      break;
      case "toggle_tip":
        // Start new pub
        $pub = new Pub();
        $pub->pub = json_decode($_POST["values"], true)["pub"];

        if( $pub->values()["tip"] == 1) {
          // Change state
          $pub->update(array("tip" => 0));

          // Return values
          echo json_encode(array(
            "visibility" => "off",
            "img_src" => $url . '/medias/icons/tip-money-off.svg'
          ));
        }else {
          // Change state
          $pub->update(array("tip" => 1));

          // Return values
          echo json_encode(array(
            "visibility" => "on",
            "img_src" => $url . '/medias/icons/tip-money-on.svg'
          ));
        }
      break;
    }
  break;

  /**
   * Mediahub
   */
  case "MediaHub":
    switch($_POST["action"]) {
      case "add":
        // Add new image
        $mediaHub = new MediaHub();
        if( $mediaHub->addImage( $_FILES["file"], pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME ) ) ) {
          echo json_encode(array(
            "state" => true,
            "alt" => pathinfo($_FILES["file"]["name"], PATHINFO_BASENAME ),
          ));
        }else {
          echo json_encode(array(
            "state" => false,
            "alt" => pathinfo($_FILES["file"]["name"], PATHINFO_BASENAME ),
          ));
        }
      break;
      case "loadMedias":
        $mediaHub = new MediaHub();

        echo json_encode(
          $mediaHub->all(
            (json_decode($_POST["values"], true)["offset"] ?? 0),
            (json_decode($_POST["values"], true)["steps"] ?? 20) )
        );
      break;
      case "details":
        $mediaHub = new MediaHub();
        $mediaHub->fileID = json_decode($_POST["values"], true)["fileID"];

        echo json_encode(
          $mediaHub->fileDetails()
        );
      break;
      case "update":
        $mediaHub = new MediaHub();
        $mediaHub->fileID = json_decode($_POST["values"], true)["fileID"];

        if( $mediaHub->updateImage( json_decode($_POST["values"], true)["alt"] ) ) {
          echo "true";
        }else {
          echo "false";
        }
      break;
      case "remove":
        $mediaHub = new MediaHub();
        $mediaHub->fileID = json_decode($_POST["values"], true)["fileID"];

        if( $mediaHub->removeImage() ) {
          echo "true";
        }else {
          echo "false";
        }
      break;
      case "string":
        echo Language::string( json_decode($_POST["values"], true)["id"], null, 'mediahub' );
      break;
    }
  break;
}
 ?>
