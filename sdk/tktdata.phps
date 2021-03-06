<?php
/**
 *################################################################################################################################################
 *
 *  #############   ##   ##   #############   #####      #####   #############   #####                             ########  #####     ##   ##
 *       ##        ##  ##         ##         ##   ##    ##  ##       ##         ##  ##                           ##         ##   ##   ##  ##
 *      ##        #####          ##         ##    ##   ########     ##         ########                           ######   ##    ##  #####
 *     ##        ##  ##         ##         ##   ##    ##     ##    ##         ##     ##                               ##  ##   ##   ##  ##
 *    ##        ##    ##       ##         #####      ##      ##   ##         ##      ##                         #######  #####     ##    ##
 *
 *###############################################################################################################################################
 *
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: This file is the public SDK-File to connect to the TKTData-System
 *
 * All answers from the host are given in JSON. use json_decode to get array or class
 * https://www.php.net/manual/de/function.json-decode.php
 *
 ************* Sample *************
 * //Require tktdata file
 * require_once("path/to/file/tktdata.php");
 *
 * //Get info of a ticket
 * $tktdata = new TKTData();
 * $tktdata->private_key = "YOUR_PRIVATE_KEY" //You will find this key on your host in groups->sdk
 * $tktdata->ticketToken = "sample"; //Set ticket token
 * $ticket_infos = $tktdata->get_ticket(); //JSON answer //Use here one function listed in "All functions"
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $private_key: Private key to create a valid signature. This key will be provided from the host. Please ask the admin of the host to get the key. This key have to be protected very well. !IMPORTANT NOTICE! The key can onlay access a specific group.
 * $ticketToken: Crypted token. You can get this token via $tktdata->find_ticketToken();
 * $groupID: Id of the requested group
 * $couponID: Id of coupon
 *
 ************* All functions *************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd throug the function are written after the function name inround brackets ().
 *
 * TKTData->get_ticket () [$ticketToken][$private_key]
 *
 * TKTData->find_ticketToken ( $search_query [Information about ticket (email or custom)] )
 *
 * TKTData->add_ticket ( $values [Array], $mail [boolean to send mail] ) [$private_key]
 *
 * TKTData->update_ticket ( $values [Array] ) [$ticketToken][$private_key]
 *
 * TKTData->remove_ticket () [$ticketToken][$private_key]
 *
 * TKTData->restore_ticket () [$ticketToken][$private_key]
 *
 * TKTData->send_ticket () [$ticketToken]
 *
 * TKTData->get_couponID ( $values [Array] )
 *
 * TKTdata->checkCoupon () [$couponID]
 *
 * TKTData->new_coupon_price () [$couponID]
 *
 * TKTData->get_group () [$groupID]
 *
 * TKTData->usedTickets () [$groupID]
 *
 * TKTData->availableTickets () [$groupID]
 *
 * TKTData->tpu_available ( $value [Email of user]) [$groupID]
 *
 * TKTData->requestGateway() [$ticketToken][$private_key]
 *
 * TKTData->deleteGateway() [$ticketToken][$private_key]
 *
 * TKTData->requestTransaction () [$ticketToken][$private_key]
 *
 * TKTData->checkPayment () [$ticketToken][$private_key];
 *
 * TKTData->send_payment_mail () [$ticketToken]
 *
 ************* state and payment explanation *************
 *
 * STATE
 * 0: Ticket available
 * 1: Ticket used
 * 2: Ticket blocked / Deleted
 *
 * PAYMENT
 * 0: Webpayment via PSP
 * 1: Payment via invoice
 * 2: payment expected
 *
 ************* ERROR *************
 * Scheme [JSON]
 *  code : error_code
 *  message : text_message (en)
 *  request_time : when request was made
 *
 * Errors:
 *  e10: Signature not fond
 *  e11: Invalid signature
 *  e12. Signature expired
 *  e20: Group not found
 *  e30: Ticket not found
 *  e31: TicketToken not found
 *  e32: Failed to add ticket
 *  e33: Failed to update ticket
 *  e34: Failed to remove ticket
 *  e35: Failed to restore ticket
 *  e40: CouponID not found
 *
 */

class TKTData {
  //Private key to generate signature
  public $private_key;
  public $ticketToken;
  public $groupID;
  public $couponID;

  //Variables to acces sdk
  private $get = array();
  private $requestUri = "YOUR_HOST/";

  /**
   * Function to set timestamp
   */
  public function __construct() {
    $this->get["ts"] = time();
  }

  /**
   * Returns a valid signature
   * Requires: $private_key
   */
  private function signature() {
    return hash_hmac("sha1", http_build_query($this->get), $this->private_key);
  }

  /**
   * Returns a valid ticketToken or an error code
   */
  public function find_ticketToken( $search_query) {
    $this->get["value"] = $search_query;
    $this->get["req"] = "findToken";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Returns all ticket informations or an error
   * Requires: $ticketToken, $secret_key
   *
   * $answer = array(
   *   ticketToken
   *   groupID
   *   email
   *   amount
   *   coupon
   *   states => array(
   *      state [0: Ticket available, 1: Ticket used, 2: Ticket blocked / Deleted]
   *      payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *   ),
   *   timestamps => array(
   *      purchase_time
   *      payment_time
   *      employ_time
   *   ),
   *   pdf => array(
   *      url
   *      url_base
   *      ticketToken
   *   ),
   *   custom => array(
   *     array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *    array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *  )
   *)
   */
  public function get_ticket() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "ticketInfos";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Returns true or an error
   * Requires: $ticketToken
   *
   * $values = array(
   *  groupID [required]
   *  amount
   *  payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *  coupon
   *  email [required]
   *  custom => array(
   *     array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *    array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *  )
   * )
   * $mail = true: send mail, false: do not send mail
   */
  public function add_ticket( $values, $mail ) {
    $this->get["req"] = "addTicket";
    $this->get = array_unique( array_merge($this->get, $values)); //Add values to $this->get
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Return true or an error
   * Requires: $ticketToken, $secret_key
   *
   * $values = array(
   *  amount
   *  state [0: Ticket available, 1: Ticket used, 2: Ticket blocked / Deleted]
   *  payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *  coupon
   *  purchase_time
   *  payment_time
   *  employ_time
   *  email [required]
   *  custom => array(
   *     array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *    array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *  )
   * )
   */
  public function update_ticket( $values ) {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "updateTicket";
    $this->get = array_unique( array_merge($this->get, $values)); //Add values to $this->get
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Return true or an error
   * Requires: $ticketToken, $secret_key
   */
  public function remove_ticket() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "removeTicket";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Return true or an error
   * Requires: $ticketToken, $private_key
   */
  public function restore_ticket() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "restoreTicket";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Returns true or false
   * Requires: $ticketToken
   */
  public function send_ticket() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "sendTicket";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Returns couponID or an error
   *
   * $values = array(
   *  name
   *  groupID
   * )
   */
  public function get_couponID( $values ) {
    $this->get["req"] = "getCouponID";
    $this->get = array_unique( array_merge($this->get, $values)); //Add values to $this->get
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Returns true or false
   */
  public function check_coupon() {
    $this->get["req"] = "checkCoupon";
    $this->get["couponID"] = $this->couponID;
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Returns new price with coupon
   */
  public function new_coupon_price() {
    $this->get["req"] = "newCouponPrice";
    $this->get["couponID"] = $this->couponID;
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Return group infos or an error
   * Requires: $groupID
   *
   * $answer = array(
   *  groupID
   *  name
   *  description
   *  maxTickets
   *  tpu
   *  price
   *  vat
   *  currentcy
   *  startTime
   *  endTime
   *  mail => array(
   *    from
   *    displayName
   *    subject
   *    msg
   *  ),
   *  custom => array(
   *     array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *    array(
   *       type
   *       name
   *       placeholder
   *       required
   *       value
   *       order
   *    )
   *  )
   *)
   */
  public function get_group() {
    $this->get["groupID"] = $this->groupID;
    $this->get["req"] = "groupInfos";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Return used tickets
   * Requires: $groupID
   */
  public function usedTickets() {
    $this->get["groupID"] = $this->groupID;
    $this->get["req"] = "usedTickets";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Return available tickets
   * Requires: $groupID
   */
  public function availableTickets() {
    $this->get["groupID"] = $this->groupID;
    $this->get["req"] = "availableTickets";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Return available tickets by user [int] (tpu = tickets per user available)
   * Requires: $groupID
   *
   * $value: Email of user
   */
  public function tpu_available( $value ) {
    $this->get["groupID"] = $this->groupID;
    $this->get["req"] = "tpu";
    $this->get = array_unique( array_merge($this->get, $value)); //Add values to $this->get
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }

  /**
   * Gets a Gateway to create your one modal window or redirect to link for payment
   * For modal window visit: https://developers.payrexx.com/docs/modal-window
   *
   * Requires: $ticketToken, $secret_key
   *
   * returns array(
   *  gateway_creation_state, [Returns true if gateway is created successfully]
   *  [ message, [This is only visible if gateway_creation_state is false. Errormessage] ]
   *  hash,
   *  link,
   *  status,
   *  referenceId, [Equals $ticketToken]
   *  id, [Gateway ID]
   * )
   *
   * $value = array(
   *   success_link, [Redirect after successfull payment]
   *   fail_link, [Redirect after failed payment]
   * )
   */
  public function requestGateway( $value ) {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "requestGateway";
    $this->get = array_unique( array_merge($this->get, $value)); //Add values to $this->get
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Delets a gateway
   *
   * returns true or an array(
   *  gateway_creation_state, [Values = false]
   *  message, [Errormessage]
   * )
   */
  public function deleteGateway() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "deleteGateway";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Gets a Transaction
   *
   * Requires: $ticketToken, $secret_key
   *
   * returns an array {
   *  transaction_retrieve_status, [Returns true if successfully found]
   *  [ message, [This is only visible if transaction_retrieve_status is false. Errormessage]  ]
   *  id, [Transactionid]
   *  status, [payment state]
   *  time, [payment time]
   *  psp, [Payment Service Provider name]
   *  pspId, [PSP id, ]
   *  referenceId, [Equals to $ticketToken]
   * );
   */
  public function requestTransaction() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "requestTransaction";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Updates payment in DB if payment arrived and returns if true if payment was made
   *
   * Requires: $ticketToken, $secret_key
   */
  public function checkPayment() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "checkPayment";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) . "&signature=" . $this->signature() );
  }

  /**
   * Sends payment request once more to user
   *
   * Requires: $ticketToken
   */
  public function send_payment_mail() {
    $this->get["ticketToken"] = $this->ticketToken;
    $this->get["req"] = "requestPayment";
    return file_get_contents( $this->requestUri . "?" . http_build_query($this->get) );
  }
}
 ?>
