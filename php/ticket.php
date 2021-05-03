<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage ticket actions
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $ticketToken: Crypted token of a ticket [of Ticket class]
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * Ticket->createTicketKey () {private function}
 *
 * Ticket->encryptTicketToken($group [GroupID], $ticketKey [Key of ticket]) {static function}
 *
 * Ticket->sendTicket( $to [recipient mail adress] ) [$ticketToken]
 *
 * Ticket->requestPayment ( $to [recipient mail adress] ) [$ticketToken]
 *
 * Ticket->updateTicket ( $newValues [new values as array], $modificationTxt [Text displayed in actions page] ) [$ticketToken] {private function}
 *
 * Ticket->cryptToken () [$ticketToken]
 *
 * Ticket->add ( $values [new values as array], $strict [boolean to check timewindow sensitive or not], $mail [boolean to send mail or not] )
 *
 * Ticket->update ( $newValues [new values as array] ) [$ticketToken]
 *
 * Ticket->remove () [$ticketToken]
 *
 * Ticket->restore () [$ticketToken]
 *
 * Ticket->employ () [$ticketToken]
 *
 * Ticket->reactivate () [$ticketToken]
 *
 * Ticket->values ( $fetchMode [PDO Fetch mode] ) [$ticketToken]
 *
 *
 **************** expression explanation ****************
 *
 * TicketKey: Hidden and protected key stored in database. Do never publish this key
 * TicketToken: Public token of ticket. A crypted string out of groupID, TicketKey.
 *
 **************** state and payment explanation ****************
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
 */
class Ticket {

  public $ticketToken;

  /**
   * Returns an unique ticketKey
   */
  private function createTicketKey(){
    //Get database connection
    $conn = Access::connect();

    do {
      //Create a new ticket key
      $ticketKey = bin2hex(random_bytes(4)); //4’294’967’296 possible unique ids (2^32)

      $key_check =  $conn->prepare("SELECT ticketKey FROM " . TICKETS . " WHERE ticketKey=:ticketKey");
      $key_check->execute(array(":ticketKey" => $ticketKey));
    }while( $key_check->rowCount() > 0); //Check if ticket key is already used


    //Return final available ticket key
    return $ticketKey;
  }

  /**
   * Returns ticketToken
   *
   * $roup =
   * $ticketKey =
   */
  public static function encryptTicketToken($group, $ticketKey) {
    return Crypt::encrypt($group . "," . $ticketKey);
  }

  /**
   * Sends HTML Ticket mail
   * requires: $ticketToken
   *
   * $to = reciept Mail
   */
  public function sendTicket( $to ) {
    $mail = new TKTdataMailer();
    $mail->CharSet = "UTF-8";
    $mail->setFrom(EMAIL, "TKTDATA - DEIN TICKET");
    $mail->addAddress( $to );
    $mail->Subject = ( "Ihr Ticket, wir können es kaum erwarten, Sie begrüssen zu dürfen." );
    $mail->msgHTML( $mail->ticketMail( $this->ticketToken) );
    return $mail->send();
  }

  /**
   * Sends HTML request mail
   * requires: $ticketToken
   *
   * $to = reciept Mail
   */
  public function requestPayment( $to ) {
    $mail = new TKTdataMailer();
    $mail->CharSet = "UTF-8";
    $mail->setFrom(EMAIL, "TKTDATA - ZAHLUNGSANFORDERUNG");
    $mail->addAddress( $to );
    $mail->Subject = ( "Zahlungsanforderung für Ihr Ticket" );
    $mail->msgHTML( $mail->paymentRequestMail( $this->ticketToken) );
    return $mail->send();
  }

  /**
   * Updates ticket and returns true or false
   * Requires: $ticketToken
   *
   * $newValues = array(
   *   amount
   *   state [0: Ticket available, 1: Ticket used, 2: Ticket blocked / Deleted]
   *   payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *   payrexx_gateway
   *   payrexx_transaction
   *   payment_time
   *   employ_time
   *   coupon
   *   email
   * );
   *
   * $modificationTXT = Text displayed in activity page (use %ticket% to display ticketToken)
   */
  private function updateTicket( $newValues, $modificationTxt = "Updated ticket %ticket%" ) {
    //require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Create new array
    $tableColumns = array(
      "amount",
      "state",
      "payment",
      "payrexx_gateway",
      "payrexx_transaction",
      "payment_time",
      "employ_time",
      "coupon",
      "email"
    );
    $sqlValues = array_intersect_key($newValues, array_flip($tableColumns)); //Generate all accessable columns and leafe out others

    //Check amount
    if(array_key_exists("amount", $sqlValues) && is_float($sqlValues["amount"])) {
      $sqlValues["amount"] = ($sqlValues["amount"]  * 100);
    }

    //Check payment time
    if(array_key_exists("payment", $sqlValues) && $sqlValues["payment"] != 2 && empty($sqlValues["payment_time"]) ) {
      if(empty($this->values()["payment_time"]) || $this->values()["payment_time"] == '0000-00-00 00:00:00') {
        $sqlValues["payment_time"] = date("Y-m-d H:i:s");
      }
    } elseif (array_key_exists("payment", $sqlValues) && $sqlValues["payment"] == 2) {
      $sqlValues["payment_time"] = '0000-00-00 00:00:00';
    }

    if(! empty($this->values()["custom"])) {
      //Create custom element
      $customElements = json_decode($this->values()["custom"], true);
      if(is_array($customElements)) {
        foreach($customElements as $key => $customElement) { //Go through every custom input
          if(! empty( $newValues[$customElement["id"]] )) { //Check if new variable is available
            if(empty( $customElement["options"] )) { //Check if custom input is select/radio
              $customElements[$key]["value"] = $newValues[$customElement["id"]]; //Replace value
            }else {
              if(in_array( $newValues[$customElement["id"]], explode(",", $customElement["options"]))) { //Check if value is in array
                //Element is valid
                $customElements[$key]["value"] = $newValues[$customElement["id"]]; //Replace value
              }
            }
          }
        }
      }

      //Add custom element to array
      $sqlValues["custom"] = json_encode($customElements);
    }

    //Get old data
    $oldData = array_intersect_key($newValues, $this->values(PDO::FETCH_ASSOC));
    $oldData["custom"] = $this->values()["custom"];


    //Create update SQL
    $updateQuery = "UPDATE " . TICKETS . " SET ";
    foreach($sqlValues as $key => $value) {
      //Update query check
      if( $value === NULL ) {
        $updateQuery .= $key . "=NULL";
      } else {
        $updateQuery .= $key . "='" . $value . "'";
      }
      $updateQuery .= (array_key_last( $sqlValues ) == $key) ? ' ' : ', '; //Add comma if required (last one does not have a comma)
    }
    $updateQuery .= "WHERE groupID='" . $this->cryptToken()["gid"] . "' AND ticketKey='" . $this->cryptToken()["ticketKey"] . "'";

    $update = $conn->prepare($updateQuery);

    //Modifie ticket
    $change = array(
      "user" => $current_user,
      "message" => str_replace("%ticket%", "#" . $this->ticketToken, $modificationTxt),
      "table" => "TICKETS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "ticketKey", "value" => $this->cryptToken()["ticketKey"]),
      "old" => $oldData,
      "new" => $sqlValues,
    );

    User::modifie( $change );

    //Execute query
    return $update->execute();
  }

  /**
   * Decrypt $ticketToken and return array
   * Requires: $ticketToken
   */
  public function cryptToken(){
    //decrypt ticket token
    $ticketKey = Crypt::decrypt($this->ticketToken);

    //Get infos as array
    $keyParts = explode(",", $ticketKey);

    //Return full array wiht text option
    return array(
      0 => $keyParts[0],
      'gid' => $keyParts[0],
      1 => $keyParts[1],
      'ticketKey' => $keyParts[1],
    );
  }

  /**
   * Add ticket and returns type
   * Requires: $ticketToken
   *
   * Return types:
   *  0: failed to add ticket
   *  1: Ticked added successfuly
   *  2: max tickets per user reached (Group->tpuAvailable())
   *  3: max tickets reached (Group->availableTickets() > 0)
   *  4: timewindow is closed (Group->timeWindow(); only if $strict mode is on)
   *  5: Mail not sent
   *  6: Coupon not available
   *
   * $values = array(
   *   groupID
   *   email
   *   amount
   *   payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *   payrexx_gateway
   *   payrexx_transaction
   *   custom
   *   coupon
   * );
   * $strict = Strict mode on check timewindow
   * $mail = true: sends ticket mail, false no ticket sent
   */
  public function add( $values, $strict = true, $mail = true ) {
    //Set activity
    global $current_user;

    //Ticket key
    $ticketKey = self::createTicketKey();

    $this->ticketToken = Ticket::encryptTicketToken($values["groupID"], $ticketKey);

    //Check if user can buy a ticket
    $group = new Group();
    $group->groupID = $values["groupID"];

    if(! $group->tpuAvailable( $values["email"] )) {
      return 2;
    }

    //Check if there are any tickets available
    if($group->availableTickets() < 1) {
      return 3;
    }

    //Check if timeWindow is open and if sensibility is on
    if(! $group->timeWindow() && $strict) {
      return 4;
    }

    //Update amount
    $amount = $group->values()["price"] + ($group->values()["price"] * ($group->values()["vat"] / 10000)); //Defalt amount

    //Check if coupon is set and get new price
    if(!empty($_POST["coupon"])) {
      $coupon = new Coupon();
      $coupon->couponID = $values["coupon"];

      if($coupon->employ()) {
        $amount = $coupon->new_price();
      }else {
        return 6;
      }
    }

    //Define coupon
    $values["coupon"] = empty($values["coupon"]) ? 0 : $values["coupon"];

    //Create custom infos
    $group = new Group();
    $group->groupID = $values["groupID"];
    $customInfos = json_decode($group->values()["custom"], true); //Store full json in ticket to prevent changes in Group

    //Create customTicket array if custom elements are required
    if(! empty($customInfos)) {
      for($i = 0; $i < count($customInfos); $i++) {
        //Unset elements
        unset($customInfos[$i]["placeholder"]);
        unset($customInfos[$i]["required"]);

        //Set new values
        $customInfos[$i] = array_merge(array("id" => $i), $customInfos[$i]); //Id of input
        $customInfos[$i]["options"] = $customInfos[$i]["value"];
        $customInfos[$i]["value"] = ( isset($values[$i])) ? $values[$i] : ''; //Set value if one exists
      }

      //Order array by user input
      foreach($customInfos as $key => $value) {
        $orders[$key] = intval($value["order"]);
      }
      array_multisort($orders, SORT_ASC, $customInfos);

    }else {
      $customInfos = null;
    }

    //Append to values
    $values["custom"] = $customInfos;
    $values["ticketKey"] = $ticketKey;
    $values["purchase_time"] = date("Y-m-d H:i:s");
    $values["state"] = 0;
    $values["amount"] = $amount;

    //Check values
    $key_check = array("ticketKey", "groupID", "amount", "state", "payment", "payrexx_gateway", "payrexx_transaction", "purchase_time", "coupon", "email", "custom");
    $values = array_intersect_key($values, array_flip($key_check));

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => "Added Ticket",
      "table" => "TICKETS",
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "ticketKey", "value" => $ticketKey),
      "old" => "",
      "new" => $values
    );

    User::modifie( $change );

    //Get database connection
    $conn = Access::connect();

    //Encode json to store in db
    $values["custom"] = json_encode($values["custom"]);

    //Remove sendMail
    unset($values["sendMail"]);

    $add_ticket = $conn->prepare("INSERT INTO " . TICKETS . "
    (" . implode(",", array_keys($values)) . ") VALUES
    ('" . implode("', '", $values) . "')");
    
    //Check if mail needs to be send
    if($mail == true) {
      $this->sendTicket( $values["email"] );
    }

    //Return if successfuly added
    return ($add_ticket->execute()) ? 1 : 0;
  }

  /**
   * Updates a ticket and returns true or false
   * Requires: $ticketToken
   *
   * $newValues = array(
   *   amount
   *   state [0: Ticket available, 1: Ticket used, 2: Ticket blocked / Deleted]
   *   payment [0: Webpayment via PSP, 1: Payment via invoice, 2: payment expected]
   *   payment_time
   *   employ_time
   *   coupon
   *   email
   * );
   */
  public function update( $newValues ) {
    //Generate correct amount
    if(array_key_exists("amount", $newValues)) {
      $newValues["amount"] = floatval($newValues["amount"]);
    }

    //Update ticket
    return $this->updateTicket( $newValues );
  }

  /**
   * Remove ticket and returns true or false [Ticket will just be disable to prevent to use ticket while to be dedected as not existing]
   * Requires: $ticketToken
   */
  public function remove() {
    return $this->updateTicket(array("state" => 2), "Removed Ticket %ticket%");
  }

  /**
   * Restore ticket and returns true or false
   * Requires: $ticketToken
   */
  public function restore() {
    return $this->updateTicket(array("state" => 0), "Restored Ticket %ticket%");
  }

  /**
   * Set ticket as used and returns true or false
   * Requires: $ticketToken
   */
  public function employ() {
    //Check if livedata is activated
    if(class_exists("Livedata")) {
      Livedata::up(); //Set livedata up
    }

    return $this->updateTicket(array("state" => 1, "employ_time" => date("Y-m-d H:i:s")), "Employed Ticket %ticket%");
  }

  /**
   * Reactivate ticket and returns true or false
   * Requires: $ticketToken
   */
  public function reactivate() {
    //Check if livedata is activated
    if(class_exists("Livedata")) {
      Livedata::down(); //Set livedata down
    }

    return $this->updateTicket(array("state" => 0, "employ_time" => NULL), "Manually reactivated Ticket %ticket%");
  }

  /**
   * Returns infos about ticket
   * Requires: $ticketToken
   *
   * $fetchMode = [PDO::FETCH_ASSOC, PDO::FETCH]
   */
  public function values($fetchMode = null) {
    //Get database connection
    $conn = Access::connect();

    //Decrypt key and split to components
    $decryptedKey = $this->cryptToken();

    $ticketInfo = $conn->prepare("SELECT * FROM " . TICKETS . " WHERE
      ticketKey=:ticketKey AND groupID=:gid LIMIT 0, 1");
    $ticketInfo->execute(array(":ticketKey" => $decryptedKey["ticketKey"], ":gid" => $decryptedKey["gid"]));

    //Return content
    $result = $ticketInfo->fetch($fetchMode);
    if( count($result) > 2) {
      return $result;
    }else {
      return $result[0];
    }

  }
}
 ?>
