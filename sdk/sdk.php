<?php
/**
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
************* Class Variables *************
* If a function requires such a variable, you will find a hint in the comments of the function
* $ticketToken: Crypted token. You can get this token via $tktdata->find_ticketToken();
* $groupID: Id of the requested group
* $couponID: Coupon id
*
************* All functions *************
* For further description please go to requested function
* Some functions uses class variable. Those are written behind the function name in square brackets [].
* Variables witch have to be passd throug the function are written after the function name inround brackets ().
*
* SDK_TKTData->get_ticket ( ) [$ticketToken]
*
* SDK_TKTData->find_ticketToken ( $search_query [Information about ticket (email or custom)] )
*
* SDK_TKTData->add_ticket ( $values [Array] )
*
* SDK_TKTData->update_ticket ( $values [Array] ) [$ticketToken]
*
* SDK_TKTData->remove_ticket () [$ticketToken]
*
* SDK_TKTData->restore_ticket () [$ticketToken]
*
* SDK_TKTData->send_ticket () [$ticketToken]
*
* SDK_TKTData->get_couponID( $values [Array] )
*
* SDK_TKTData->check_coupon () [$couponID]
*
* SDK_TKTData->new_coupon_price () [$couponID]
*
* SDK_TKTData->get_group () [$groupID]
*
* SDK_TKTData->usedTickets () [$groupID]
*
* SDK_TKTData->availableTickets () [$groupID]
*
* SDK_TKTData->tpu_available ( $value [Email of user]) [$groupID]
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
//Set current user to sdk
$current_user = "SDK Request";

class SDK_TKTData {
  public $ticketToken;
  public $groupID;
  public $couponID;

  /**
   * Error messages
   */
  public $response;

  public function __construct() {
    $this->response = array(

      /**
       * 1x = Siganture
       * 2x = Group
       * 3x = Ticket
       * 4x = Coupon
       * 5x = Payment
       */


      //Signature not found
      "e10" => array(
        "code" => "e10",
        "message" => "Signature not fond",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Invalid signature
      "e11" => array(
        "code" => "e11",
        "message" => "Invalid signature",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Signature expired
      "e12" => array(
        "code" => "e12",
        "message" => "Signature expired",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Group not found
      "e20" => array(
        "code" => "e20",
        "message" => "Group not found",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Ticket not found
      "e30" => array(
        "code" => "e30",
        "message" => "Ticket not found",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //TicketToken not found
      "e31" => array(
        "code" => "e31",
        "message" => "TicketToken not found",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Failed to add ticket
      "e32" => array(
        "code" => "e32",
        "message" => "Failed to add ticket",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Failed to update ticket
      "e33" => array(
        "code" => "e33",
        "message" => "Failed to update ticket",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Failed to remove ticket
      "e34" => array(
        "code" => "e34",
        "message" => "Failed to remove ticket",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Failed to restore ticket
      "e35" => array(
        "code" => "e35",
        "message" => "Failed to restore ticket",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //Failed to send mail
      "e36" => array(
        "code" => "e36",
        "message" => "Failed to send mail",
        "request_time" => date("Y-m-d H:i:s"),
      ),

      //CouponID not found
      "e40" => array(
        "code" => "e40",
        "message" => "CouponID not found",
        "request_time" => date("Y-m-d H:i:s"),
      ),

    );
  }

  /**
   * Function to get secret key
   * false => e20
   */
  public function secret_key() {
    //Check group
    if($this->group_check()) {
      //Check if ticketToke is set
      if(! empty($this->ticketToken)) {
        //Extract ticketToken and set groupID
        $ticketExtractor = new Ticket();
        $ticketExtractor->ticketToken = $this->ticketToken;
        $this->groupID = $ticketExtractor->cryptToken()["gid"];
      }else {
        return $this->response["e20"];
      }
    }

    //Decrypt secret key
    $storedKey = new Group();
    $storedKey->groupID = $this->groupID;

    //Encrypt secret key
    return Crypt::decrypt($storedKey->values()["sdk_secret_key"]);
  }

  /**
   * Function to check if a signature is set
   * false => e10
   */
  public function signature_set( $request_array ) {
    if(! isset($request_array["signature"]) || empty($request_array["signature"]) ) {
      return $this->response["e10"];
    }else {
      return true;
    }
  }

  /**
   * Function to check if signature is valid
   * fasle => e11
   * NOTE: Please check first with SDK_TKTData::signature_set() if a signature is set
   * NOTE: Please check fist with SDK_TKTData::group_check() if the group exists
   */
  public function signature_check( $request_array ) {
    //Get secret key
    $secret_key = $this->secret_key();

    if(! $this->signature_set( $request_array ) ) {
      return $this->response["e11"];
    }

    //Create signature
    $get_para = $request_array;
    unset($get_para['signature']);
    $get_para = http_build_query($get_para);
    $current_signature = hash_hmac("sha1", $get_para, $secret_key);

    //Check if signature is not older than 3 minutes
    if(isset($request_array["ts"]) &&! empty($request_array["ts"]) ) {
      if(($request_array["ts"] + 3 * 60) < time()) { //check three mintutes
        return $this->response["e12"];
      }
    }

    //Compare signature
    return (hash_equals($current_signature, $request_array["signature"])) ? true : $this->response["e11"];
  }

  /**
   * Fucntion to check if group exists
   * false => e20
   */
  public function group_check() {
    //Check if group exist
    $group = new Group();
    $group->groupID = $this->groupID;

    return (!$group->values()) ? $this->response["e20"] : true;
  }

  /******************* TICKET *******************/
  /**
   * Function to get ticket informations
   * NOTE: Sigature required
   * false => e30
   */
  public function get_ticket( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      //require variables
      global $url;

      //Get values
      $ticket = new Ticket();
      $ticket->ticketToken = $this->ticketToken;
      $ticketValues =  $ticket->values(PDO::FETCH_ASSOC);

      if(! $ticketValues) {
        return $this->response["e30"];
      }

      //Generate public values
      $ticketValuesPublic = array(
        "ticketToken" => $this->ticketToken,
        "groupID" => $ticketValues["groupID"],
        "email" => $ticketValues["email"],
        "amount" => $ticketValues["amount"],
        "coupon" => $ticketValues["coupon"],
        "states" => array(
          "state" => $ticketValues["state"],
          "payment" => $ticketValues["payment"],
        ),
        "timestamps" => array(
          "purchase_time" => $ticketValues["purchase_time"],
          "payment_time" => $ticketValues["payment_time"],
          "employ_time" => $ticketValues["employ_time"]
        ),
        "pdf" => array(
          "url" => $url . "/pdf/ticket/?ticketToken=" . $this->ticketToken,
          "url_base" => $url,
          "ticketToken" => $this->ticketToken,
        ),
        "custom" => json_decode($ticketValues["custom"], true),
      );

      return $ticketValuesPublic;
    }else {
      return $this->response["e11"];
    }
  }

  /**
   * Function to get ticketToken by email or custom info
   * false => e31
   */
  public function find_ticketToken( $search_query ) {
    //Get database connection
    $conn = Access::connect();

    if(filter_var($search_query, FILTER_VALIDATE_EMAIL)) { //Check if $search_query is a mail
      //Search mail
      $stmt = $conn->prepare("SELECT groupID, ticketKey FROM " . TICKETS . " WHERE email=:email");
      $stmt->execute(array(":email" => $search_query));

      if($stmt->rowCount() > 0) {
        //Get all results
        $results = array();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ticket) {
          array_push($results, Crypt::encrypt( implode(",", $ticket)));
        }

        //Return all results
        return $results;
      }else {
        return $this->response["e31"];
      }

    }else {
      //Search in custom
      $stmt = $conn->prepare("SELECT groupID, ticketKey FROM " . TICKETS . " WHERE custom LIKE '%" . $search_query . "%'");
      $stmt->execute();

      if($stmt->rowCount() > 0) {
        //Get all results
        $results = array();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $ticket) {
          array_push($results, Crypt::encrypt( implode(",", $ticket)));
        }

        //Return all results
        return $results;
      }else {
        return $this->response["e31"];
      }
    }
  }

  /**
   * Function to add Ticket
   * NOTE: Sigature required
   * false => e32
   */
  public function add_ticket( $values, $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      $ticket = new Ticket();
      $ticket->ticketToken = $this->ticketToken;
      return ($ticket->add( $values, true, $request_array["mail"])) ? true : $this->response["e32"];
    }else {
      return $this->response["e11"];
    }
  }

  /**
   * Function to update ticket
   * NOTE: Sigature required
   * false => e33
   */
  public function update_ticket( $values ) {
    if($this->signature_check( $values ) === true) {
      $ticket = new Ticket();
      $ticket->ticketToken = $this->ticketToken;
      return($ticket->update( $values )) ? true : $this->response["e33"];
    }else {
      return $this->response["e11"];
    }
  }

  /**
   * Function to remove ticket
   * NOTE: Signature required
   * false => e34
   */
  public function remove_ticket( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      $ticket = new Ticket();
      $ticket->ticketToken = $thic->ticketToken;
      return ($ticket->remove()) ? true : $this->response["e34"];
    }else {
      return $this->response["e11"];
    }
  }

  /**
   * Function to restore ticket
   * NOTE: Signature required
   * false => e35
   */
  public function restore_ticket( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      $ticket = new Ticket();
      $ticket->ticketToken = $this->ticketToken;
      return ($ticket->restore()) ? true : $this->response["e35"];
    }else {
      return $this->response["e11"];
    }
  }

  /**
   * Function to send ticket again
   * false => e36
   */
  public function send_ticket() {
    //Start ticket class
    $ticket = new Ticket();
    $ticket->ticketToken = $this->ticketToken;

    //Send mail
    return ($ticket->sendTicket( $ticket->values()["email"] )) ? true : $this->response["e36"];
  }

  /******************* coupons *******************/
  /*
   * Function to get couponID by name and group
   * false => e40
   */
  public function get_couponID( $values ) {
    //Get coupon
    $coupon = new Coupon();
    $couponID = $coupon->get_couponID($values["name"], $values["groupID"]);

    //Return id
    return (is_null($couponID) ? $this->response["e40"] : couponID);
  }

  /**
   * Function to check if coupon is available
   */
  public function check_coupon() {
    //Get coupon
    $coupon = new Coupon();
    $coupon->couponID = $this->couponID;

    return $coupon->checkCoupon();
  }

  /**
   * Get new price with coupon
   */
  public function new_coupon_price() {
    //Get coupon
    $coupon = new Coupon();
    $coupon->couponID = $this->couponID;

    return $coupon->new_price();
  }

  /******************* GROUP *******************/
  /**
   * Function to get group infos
   * false => e20
   */
  public function get_group() {
    //check if group  exists
    if(! $this->group_check()) {
      return $this->response["e20"];
    }

    //Get infos
    $group = new Group();
    $group->groupID = $this->groupID;
    $values = $group->values();

    //Generate public values
    $groupValuesPublic = array(
      "groupID" => $values["groupID"],
      "name" => $values["name"],
      "description" => $values["description"],
      "maxTickets" => $values["maxTickets"],
      "tpu" => $values["tpu"],
      "price" => $values["price"],
      "vat" => $values["vat"],
      "currentcy" => $values["currency"],
      "startTime" => $values["startTime"],
      "endTime" => $values["endTime"],
      "mail" => array(
        "from" => $values["mail_from"],
        "displayName" => $values["mail_displayName"],
        "subject" => $values["mail_subject"],
        "msg" => $values["mail_msg"],
      ),
      "custom" => json_decode($values["custom"], true)
    );

    return $groupValuesPublic;
  }

  /**
   * Function to get number of used tickets
   */
  public function used_tickets() {
    $group = new Group();
    $goup->groupID = $this->groupID;
    return $group->ticketsNum();
  }

  /**
   * Function to get number of available tickets
   */
  public function available_tickets() {
    $group = new Group();
    $group->groupID = $this->groupID;
    return $group->availableTickets();
  }

  /**
   * Function to get tickets per user available
   */
  public function tpu_available( $email ) {
    $group = new Group();
    $group->groupID = $this->groupID;
    return $group->tpuAvailable( $email );
  }

  /**
   * Function to request gateway
   *
   * $success_link, $fail_link: Redirect after successfull/failed payment
   */
  public function requestGateway( $success_link = null, $fail_link = null, $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      //Require
      global $url;

      $ticket = new Ticket();
      $ticket->ticketToken = $this->ticketToken;

      //Define links
      $success_link = $success_link ?? $url . "store/ticket/?ticketToken=" . $this->ticketToken;
      $fail_link = $fail_link ?? $url . "store/ticket/?ticketToken=" . $this->ticketToken;

      if(empty( $ticket->values()["payrexx_gateway"]) || $ticket->values()["payrexx_gateway"] == 0) {
        return getGateway( $this->ticketToken, $success_link, $fail_link);
      }else {
        return retrieveGateway( $this->ticketToken );
      }
    }else {
      return $this->response["e10"];
    }
  }

  /**
   * Retuns transaction informations
   */
  public function deleteGateway( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      return deleteGateway( $this->ticketToken );
    }else {
      return $this->response["e10"];
    }
  }

  /**
   * Retuns transaction informations
   */
  public function requestTransaction( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      return retrieveTransaction( $this->ticketToken );
    }else {
      return $this->response["e10"];
    }
  }

  /**
   * Retuns payment informations
   */
  public function checkPayment( $request_array ) {
    if($this->signature_check( $request_array ) === true) {
      return checkPayment( $this->ticketToken );
    }else {
      return $this->response["e10"];
    }
  }

  /**
   * Send smail and returns true or false
   */
  public function send_payment_mail() {
    $ticket = new Ticket();
    $ticket->ticketToken = $this->ticketToken;
    return $ticket->requestPayment( $ticket->values()["email"] );
  }
}
 ?>
