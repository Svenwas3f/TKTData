<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose:
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $couponID: Coupon id
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd throug the function are written after the function name inround brackets ().
 *
 * Coupon->timeWindow () [couponID] {private function}
 *
 * Coupon->all ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * Coupon->get_couponID ( $name [Name of coupon] , $groupID [Id of selected group] )
 *
 * Coupon->add ( $values [Array with new infos] )
 *
 * Coupon->update ( $values [Array with new infos] ) [couponID]
 *
 * Coupon->remove () [couponID]
 *
 * Coupon->values ( $fetchMode [PDO Fetch mode] ) [couponID]
 *
 * Coupon->employ () [couponID]
 *
 * Coupon->check () [couponID]
 *
 * Coupon->new_price () [couponID]
 *
 */
class Coupon {
  //Variables
  public $couponID;

  /**
   * Checks if timewindow is open and returns true or false
   * Requires: couponID
   */
  private function timeWindow () {
    //Check if timewindow is open
    if(($this->values()["startDate"] < date("Y-m-d H:i:s")) && ($this->values()["endDate"] > date("Y-m-d H:i:s"))) {
      return true;
    }elseif(($this->values()["startDate"] == null || "0000-00-00 00:00:00" || undefined) && ($this->values()["endDate"] == null || "0000-00-00 00:00:00" || undefined)) {
      //Check if group window is open
      $groupWindow = new Group();
      $groupWindow->groupID = $this->values()["groupID"];

      return $groupWindow->timeWindow(); //Return if timewindow of group is open
    }
  }

  /**
   * Returns array of all coupons
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function all( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value) ) {
      // Select all
      $coupon = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " ORDER BY couponID DESC LIMIT " . $steps . " OFFSET " . $offset);
      $coupon->execute();
    }else {
      // Select all
      $coupon = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE
      couponID LIKE :cid OR
      name LIKE :name OR
      groupID LIKE :gid
      ORDER BY couponID DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected coupons in range
      $coupon->execute(array(
        ":cid" => "%" . $search_value . "%",
        ":name" => "%" . $search_value . "%",
        ":gid" => "%" . $search_value . "%",
      ));
    }

    return $coupon->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns couponID of a coupon or null
   *
   * $name: Name of coupon
   * $groupID: Id of selected group
   */
  public function get_couponID ( $name, $groupID ) {
    //Get database connection
    $conn = Access::connect();

    //Select
    $couponID = $conn->prepare("SELECT couponID FROM " . TICKETS_COUPONS . " WHERE name=:name AND groupID=:gid");
    $couponID->execute(array(":name" => $name, ":gid" => $groupID));

    //Return couponID
    return $couponID->fetch(PDO::FETCH_ASSOC)["couponID"] ?? null;
  }

  /**
   * Returns an state
   *  0: Array does not contain important informations
   *  1: Coupon already exists
   *  2: Failed to add Coupon
   *  3: Successfully added coupon
   *
   * $values = array(
   *   name [required]
   *   groupID [required]
   *   used
   *   available
   *   discount_percent (Use discount_percentage for a percentage)
   *   discount_absolute (Use discount_absolute for an absolute discount)
   *   startDate
   *   EndDate
   * )
   */
  public function add( $values ) {
    //Require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Define values
    $addKeys = array("name", "groupID", "available", "discount_percent", "discount_absolute", "startDate", "endDate");
    $values = array_intersect_key($values, array_flip($addKeys));
    $values["used"] = '0';
    $values["name"] = str_replace(" ", "-", $values["name"]);
    $values["discount_percent"] = ($values["discount_percent"] == 0) ? null : ($values["discount_percent"] * 100);
    $values["discount_absolute"] = ($values["discount_absolute"] == 0) ? null : ($values["discount_absolute"] * 100);
    $values["startDate"] = empty($values["startDate"]) ? null : ($values["startDate"] == '0000-00-00 00:00:00' ? '0000-00-00 00:00:00' : date("Y-m-d H:i:s", strtotime($values["startDate"])));
    $values["endDate"] = empty($values["endDate"]) ? null : ($values["endDate"] == '0000-00-00 00:00:00' ? '0000-00-00 00:00:00' : date("Y-m-d H:i:s", strtotime($values["endDate"])));


    //Check if array contains required elements
    if(! isset($values["name"]) ||! isset($values["groupID"])) {
      return 0;
    }

    //Check if coupon already exists
    if(! is_null($this->get_couponID($values["name"], $values["groupID"]))) {
      return 1;
    }

    //Create modification
    $change = array(
      "user" => $current_user,
      // "message" => "Added Coupon",
      "message" => json_encode(array(
        "id" => 140,
        "replacements" => array(
          "%name%" => $values["name"],
          "%group%" => $values["groupID"],
        ),
      )),
      "table" => "TICKETS_COUPONS",
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "couponID", "value" => ""),
      "old" => "",
      "new" => $values
    );

    User::modifie( $change );

    //Create sql Statement
    $sql_keys = '';
    $sql_values = '';

    foreach( $values as $key => $value ) {
      $sql_keys .= $key;
      $sql_values .= (empty($value) ? 'DEFAULT' : "'" . $value . "'");

      //Add comma
      $sql_keys .= (array_key_last( $values ) == $key) ? '' : ', '; //Add comma if required (last one does not have a comma)
      $sql_values .= (array_key_last( $values ) == $key) ? ' ' : ', '; //Add comma if required (last one does not have a comma)
    }

    $add_coupon = $conn->prepare( "INSERT INTO " . TICKETS_COUPONS . " (" . $sql_keys . ") VALUES (" . $sql_values . ")" );

    //Create statement and return success or fail
    return ($add_coupon->execute() ? 3 : 2);
  }

  /**
   * Updates a coupon and eturns true or false
   * Requires: $couponID
   *
   * $values = array(
   *   name
   *   groupID
   *   used
   *   available
   *   discount_percent (Use discount_percentage for a percentage)
   *   discount_absolute (Use discount_absolute for an absolute discount)
   *   startDate
   *   EndDate
   * )
   */
  public function update( $values ) {
    //Require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Get correct array
    $updateKeys = array("name", "groupID", "used", "available", "discount_percent", "discount_absolute", "startDate", "endDate");
    $values = array_intersect_key($values, array_flip($updateKeys));
    $values["name"] = str_replace(" ", "-", $values["name"]);
    if(isset($values["discount_absolute"])) {
      $values["discount_absolute"] = (intval($values["discount_absolute"]));
    }

    //Modifie
    $change = array(
      "user" => $current_user,
      // "message" => "Updated Coupon #" . $this->couponID,
      "message" => json_encode(array(
        "id" => 141,
        "replacements" => array(
          "%id%" => $this->couponID,
        ),
      )),
      "table" => "TICKETS_COUPONS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "couponID", "value" => $this->couponID),
      "old" => array_intersect_key($this->values(), array_flip($updateKeys)),
      "new" => $values
    );

    User::modifie($change);

    //Update
    $update = $conn->prepare("UPDATE " . TICKETS_COUPONS . " SET name=:name, groupID=:groupID, used=:used, available=:available, discount_percent=:discount_percent, discount_absolute=:discount_absolute, startDate=:startDate, endDate=:endDate WHERE couponID=:cid");
    return $update->execute(array(
      ":name" => $values["name"] ?? $this->values()["name"],
      ":groupID" => $values["groupID"] ?? $this->values()["groupID"],
      ":used" => $values["used"] ?? $this->values()["used"],
      ":available" => $values["available"] ?? $this->values()["available"],
      ":discount_percent" => $values["discount_percent"] ?? $this->values()["discount_percent"],
      ":discount_absolute" => $values["discount_absolute"] ?? $this->values()["discount_absolute"],
      ":startDate" => (isset($values["startDate"]) ? ($values["startDate"] == "0000-00-00 00:00:00" ? "0000-00-00 00:00:00" : date("Y-m-d H:i:s", strtotime($values["startDate"]))) : $this->values()["startDate"]),
      ":endDate" => (isset($values["endDate"]) ? ($values["endDate"] == "0000-00-00 00:00:00" ? "0000-00-00 00:00:00" : date("Y-m-d H:i:s", strtotime($values["endDate"]))) : $this->values()["endDate"]),
      ":cid" => $this->couponID,
    ));
  }

  /**
   * Removes coupon by ID and returns true or false
   * Requires: $couponID
   */
  public function remove() {
    //Require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Modifie
    $change = array(
      "user" => $current_user,
      // "message" => "Removed Coupon #" . $this->couponID,
      "message" => json_encode(array(
        "id" => 142,
        "replacements" => array(
          "%id%" => $this->couponID,
        ),
      )),
      "table" => "TICKETS_COUPONS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "couponID", "value" => $this->couponID),
      "old" => $this->values(),
      "new" => array("")
    );

    User::modifie($change);

    //Remove
    $remove = $conn->prepare("DELETE FROM " . TICKETS_COUPONS . " WHERE couponID=:cid");
    return $remove->execute(array(":cid" => $this->couponID));
  }

  /**
   * Returns array with values of coupon or false
   * Requires: $couponID
   *
   * $fetchMode: PDO Fetch mode
   */
  public function values( $fetchMode = null ) {
    //Get database connection
    $conn = Access::connect();

    //Select values
    $stmt = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE couponID=:cid");
    $stmt->execute(array(":cid" => $this->couponID));

    //Return array
    return $stmt->fetch($fetchMode);
  }

  /**
   * Employs coupon and returns true or false
   * Requires: $couponID
   */
  public function employ() {
    //Get database connection
    $conn = Access::connect();

    //Check if coupon is available
    if( $this->check() ) {
      //Employ coupon
      $check = $conn->prepare("UPDATE " . TICKETS_COUPONS . " SET used = used + 1 WHERE couponID=:cid");
      return $check->execute(array(":cid" => $this->couponID));
    }

    return false;
  }

  /**
   * Checks if coupon is available and returns true or false
   * Requires: CouponID
   */
  public function check() {
    //Check if coupon is in time range
    if( $this->timeWindow() ) {
      //Check if coupon isnt used before
      if( $this->values()["used"] < $this->values()["available"] ) {
        return true;
      }
    }

    //Return false
    return false;
  }

  /**
   * Returns new price
   * Requires: $couponID
   */
  public function new_price() {
    //Get discount
    $discount = $this->values();

    //Get normal price
    $price = new Group();
    $price->groupID = $discount["groupID"];
    $price = (($price->values()["price"] + ($price->values()["vat"] / 10000) * $price->values()["price"]));

    //Return new price
    if(! empty($discount["discount_percent"])) {
      return round($price - ($price * $discount["discount_percent"] / 10000), 0, PHP_ROUND_HALF_UP);
    }else {
      return (($price - $discount["discount_absolute"]) > 0) ? round($price - $discount["discount_absolute"], 0, PHP_ROUND_HALF_UP) : 0;
    }
  }
}
 ?>
