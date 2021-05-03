<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: January 2021
 * @Purpose: File to manage ajax actions of store
 *
 ************* errors *************
 * 01: Not enought informations
 * 02: No coupon found
 * 03: Coupon found
 * 04: Coupon no longer available
 * 05: Coupon price
 */

//Get general file
require_once(dirname(__FILE__, 3) . "/general.php");

//Send mail again
if(isset($_GET["email"]) && isset($_GET["id"])) {

  //Set connection variable
  $conn = Access::connect();

  //Select all response
  $stmt = $conn->prepare("SELECT * FROM " . TICKETS . " WHERE email=:email ORDER BY purchase_time ASC");
  $stmt->execute(array(
    ":email" => $_GET["email"]
  ));
  $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //Get ticket infos
  $ticket = new Ticket();
  $ticket->ticketToken = Ticket::encryptTicketToken( $response[$_GET["id"]]["groupID"], $response[$_GET["id"]]["ticketKey"] );

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
 ?>
