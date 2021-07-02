<?php
//Require general file
require_once( dirname(__FILE__, 2) . "/general.php");

header("Access-Control-Allow-Orgin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

//Check page
switch($_POST["p"] ?? null) {
  /**
   * buy ticket
   */
  case 2:
    /**
     ************* errors *************
     * 01: Not enought informations
     * 02: No coupon found
     * 03: Coupon found
     * 04: Coupon no longer available
     * 05: Coupon price
     */
    switch($_POST["action"]) {
      case "check_coupon":
        //Check if valid coupon
        $coupon = new Coupon();
        $cid = $coupon->get_couponID(json_decode($_POST["values"], true)["name"], json_decode($_POST["values"], true)["gid"]);
        $group = new Group();
        $group->groupID = json_decode($_POST["values"], true)["gid"];

        //Set coupon id
        $coupon->couponID = $cid;

        if($coupon->check()) {
          echo json_encode(array(
            "response" => true,
            "code" => 03,
            "couponName" => $coupon->values()["name"],
            "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
            "message" => "Coupon found"
          ));
          exit;
        }else {
          echo json_encode(array(
            "response" => false,
            "code" => 04,
            "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
            "message" => "Coupon no longer available"
          ));
          exit;
        }
      break;
      case "get_price":
        //Check if valid coupon
        $coupon = new Coupon();
        $cid = $coupon->get_couponID(json_decode($_POST["values"], true)["name"], json_decode($_POST["values"], true)["gid"]);
        $group = new Group();
        $group->groupID = json_decode($_POST["values"], true)["gid"];

        //Set coupon id
        $coupon->couponID = $cid;

        if($coupon->check()) {
          echo json_encode(array(
            "response" => true,
            "code" => 05,
            "couponName" => $coupon->values()["name"],
            "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
            "discountPrice" => $coupon->new_price(),
            "currency" => $group->values()["currency"],
            "message" => "Coupon price"
          ));
          exit;
        }else {
          echo json_encode(array(
            "response" => false,
            "code" => 04,
            "basePrice" => $group->values()["price"],
            "message" => "Coupon no longer available"
          ));
          exit;
        }
      break;
    }
  break;

  /**
   * Resend mail
   */
  case 6:
    switch($_POST["action"]) {
      case "send_mail":
        // Get values
        $values = json_decode($_POST["values"], true);

        // Check access
        if(isset($values["email"]) && isset($values["id"])) {
          // Select Ticket
          $selected = Ticket::all($values["offset"], $values["steps"], $values["email"])[$values["id"]];

          $ticket = new Ticket();
          $ticket->ticketToken = Ticket::encryptTicketToken( $selected["groupID"], $selected["ticketKey"] );

          //Update payment if required
          checkPayment( $ticket->ticketToken );

          //Get recipient
          $to = $ticket->values()["email"];

          //Check if payment is accepted
          if( checkPayment( $ticket->ticketToken ) || $ticket->values()["payment"] != 2 ) {
            //Send mail
            if( $ticket->sendTicket( $to ) ) {
              Action::success("Das Ticket wurde erfolgreich gesendet");
            }else {
              Action::fail("Beim senden ist ein Fehler aufgetreten. Versuche es erneut.");
            }
          }else {
            //Send mail
            if( $ticket->requestPayment( $to ) ) {
              Action::success("Das Ticket wurde erfolgreich gesendet");
            }else {
              Action::fail("Beim senden ist ein Fehler aufgetreten. Versuche es erneut.");
            }
          }

        }else {
          Action::fail("Kein TicketToken angegeben.");
        }
      break;
    }
  break;

  /**
   * Generate payment
   */
  case 8:
    switch($_POST["action"]) {
      case "calculate":
        // Generate price
        $price = 0;

        // Loop through every product
        foreach( $_POST as $productID=>$quantity ) {
          if( is_int($productID) ) {
            // Get product
            $product = new Pub();
            $product->product_id = $productID;

            // get price
            $price = $price + ($product->product()["price"] * $quantity);
          }
        }

        // Add tip if available
        if( isset($_POST["tip"]) &&! empty($_POST["tip"]) && $_POST["tip"] > 0) {
          $price = $price + ($_POST["tip"] * 100);
        }

        echo json_encode(array(
          "plain" => $price,
          "calculated" => ($price /100),
          "formated" => number_format(($price / 100), 2),
        ));
      break;
    }
  break;
}
 ?>
