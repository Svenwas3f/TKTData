<?php
//SDK of TKTData
require_once( dirname(__FILE__, 2) . "/general.php");

require_once( dirname(__FILE__) . "/sdk.php");

//Set response headers
header("Access-Control-Allow-Orgin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

//Start sdk
$sdk = new SDK_TKTData();

//Set variables
if( isset($_GET["ticketToken"]) &&! empty($_GET["ticketToken"])) {
  $sdk->ticketToken = $_GET["ticketToken"];
}

if( isset($_GET["groupID"]) &&! empty($_GET["groupID"])) {
  $sdk->groupID = $_GET["groupID"];
}

if( isset($_GET["couponID"]) &&! empty($_GET["couponID"])) {
  $sdk->couponID = $_GET["couponID"];
}

/**
 * Do actions
 * NOTE: Superglobal such as $_GET and $_REQEST are already encoded
 * NOTE: If $_GET is set, signature is required
 */
switch( $_GET["req"] ) {
  /******************* TICKET *******************/
  //Find ticketToken
  case "findToken":
    echo json_encode( $sdk->find_ticketToken( $_GET["value"] ) );
  break;
  //Get ticket
  case "ticketInfos":
    echo json_encode( $sdk->get_ticket( $_GET ) );
  break;
  //Add ticket
  case "addTicket":
    echo json_encode( $sdk->add_ticket( $_GET ) );
  break;
  //Update ticket
  case "updateTicket":
    echo json_encode( $sdk->update_ticket( $_GET ) );
  break;
  //Remove ticket
  case "removeTicket":
    echo json_encode( $sdk->remove_ticket( $_GET ) );
  break;
  //Restore ticket
  case "restoreTicket":
    echo json_encode( $sdk->restore_ticket( $_GET ) );
  break;
  //Send ticket
  case "sendTicket":
    echo json_encode( $sdk->send_ticket() );
  break;

  /******************* Coupons *******************/
  //Get coupon ID
  case "getCouponID";
    echo json_encode( $sdk->get_couponID( $_GET ) );
  break;
  //check Coupon
  case "checkCoupon":
    echo json_encode( $sdk->check_coupon() );
  break;
  //Get new price with coupon
  case "newCouponPrice":
    echo json_encode( $sdk->new_coupon_price() );
  break;

  /******************* Groups *******************/
  //Get group
  case "groupInfos":
    echo json_encode( $sdk->get_group() );
  break;
  //Used tickets
  case "usedTickets":
    echo json_encode( $sdk->used_tickets() );
  break;
  //Available tickets
  case "availableTickets":
    echo json_encode( $sdk->available_tickets() );
  break;
  //tpu
  case "tpu":
    echo json_encode( $sdk->tpu_available( $_GET["value"]) );
  break;

  /******************* Payment *******************/
  //request Gateway
  case "requestGateway":
    echo json_encode( $sdk->requestGateway( $_GET["success_link"] ?? null, $_GET["fail_link"] ?? null, $_GET) );
  break;
  //deleteGateway
  case "deleteGateway":
    echo json_encode( $sdk->deleteGateway( $_GET ) );
  break;
  //request Transaction
  case "requestTransaction":
    echo json_encode( $sdk->requestTransaction( $_GET ) );
  break;
  //check payment
  case "checkPayment":
    echo json_encode( $sdk->checkPayment( $_GET ) );
  break;
  //request payment mail
  case "requestPayment":
    echo json_encode( $sdk->send_payment_mail() );
  break;
}
?>
