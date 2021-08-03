<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage group activities
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $groupID: Id of selected group
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * Group->updateUserInputs ( $values [new values as array] ) [$groupID] {private function}
 *
 * Group->refreshSecretKey ( $values array with confirmation) [$groupID] {private function}
 *
 * Group->all ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * Group->update ( $selection [page where update will be made], $values [new values as array] ) Summary of all privare update* functions [$groupID indirect]
 *
 * Group->add ( $values [new values as array] )
 *
 * Group->remove () [$groupID]
 *
 * Group->values () [$groupID]
 *
 * Group->ticketsNum () [$groupID]
 *
 * Group->availableTickets () [$groupID]
 *
 * Group->tpuAvailable ( $email [identification of user with email] ) [$groupID]
 *
 * Group->timeWindow () [$groupID]
 *
 */
class Group {
  //Define public variables
  public $groupID;

  /**
   * Returns true or false
   * Requires: $groupID
   *
   * $values = array(
   *    hidden
   *    customField + number [array]
   *    multiple + number
   * );
   */
  private function updateUserInputs( $values ) {
    //Get variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Additional inputs
    $customInputs = array();

    //Check if values are available
    if(isset($values["hidden"])) {
      foreach($values["hidden"] as $formEle) {
        $formEleId = str_replace("%", "", substr($formEle, strpos($formEle, "%"), strpos($formEle, "%", -1)));
        $formEleType = substr($formEle, 0, strpos($formEle, "%"));

        if($formEleType == 'select' || $formEleType == 'radio'){
          //Selection/radio answer
          //Select all options divided by a coma
          $options = '';
          foreach($values["multiple".$formEleId] as $multiple){
            $options .= $multiple.",";
          }
          //Create array
          array_push($customInputs, array(
            "id" => $formEleId,
            "type" => $formEleType,
            "name" => $values["customField".$formEleId][0],
            "placeholder" => '',
            "required" => empty($values['customField'.$formEleId][2])?'0':$values["customField".$formEleId][2],
            "value" => $options,
            "order" => $values["customField".$formEleId][1]
          )
        );
        }elseif($formEleType == 'checkbox'){
          //Checkbox
          array_push($customInputs, array(
            "id" => $formEleId,
            "type" => $formEleType,
            "name" => $values["customField".$formEleId][0],
            "placeholder" => '',
            "required" => empty($values['customField'.$formEleId][2])?'0':$values["customField".$formEleId][2],
            "value" => '',
            "order" => $values["customField".$formEleId][1]
          )
        );
        }else{
          //Textanswer
          array_push($customInputs, array(
            "id" => $formEleId,
            "type" => $formEleType,
            "name" => $values["customField".$formEleId][0],
            "placeholder" => $values["customField".$formEleId][1],
            "required" => empty($values['customField'.$formEleId][3])?'0':$values["customField".$formEleId][3],
            "value" => '',
            "order" => $values["customField".$formEleId][2]
          )
        );
        }
      }

      //Order array by user input
      foreach($customInputs as $key => $value) {
        $orders[$key] = intval($value["order"]);
      }
      array_multisort($orders, SORT_ASC, $customInputs);
    }

    //Update adfs_custom if required
    if($this->values()["adfs"] == 1) {
      //Get ADFS values
      $customADFS_DB = json_decode($this->values()["adfs_custom"], true);
      $customADFS = array("email" => $customADFS_DB["email"]);

      foreach(array_column($customInputs, "id") as $id) {
        $customADFS[$id] = $customADFS_DB[$id] ?? "";
      }
    }

    //Get old informations
    $oldGroup = $conn->prepare("SELECT custom, adfs_custom FROM " . TICKETS_GROUPS . " WHERE groupID=:gid");
    $oldGroup->execute(array(":gid" => $this->groupID));
    $row = $oldGroup->fetch(PDO::FETCH_ASSOC);

    $oldGroupInfo = array(
      "custom" => $row["custom"],
      "adfs_custom" => $row["adfs_custom"],
    );

    $newGroupInfo = array(
      "custom" => json_encode($customInputs),
      "adfs_custom" => (isset($customADFS)) ? json_encode($customADFS) : $row["adfs_custom"],
    );

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => "Updated Group #" . $this->groupID . " [Form]",
      "table" => "TICKETS_GROUPS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "groupID", "value" => $this->groupID),
      "old" => $oldGroupInfo,
      "new" => $newGroupInfo
    );

    User::modifie( $change );

    //Insert into db
    $updateGroup = $conn->prepare("UPDATE " . TICKETS_GROUPS . " SET custom=:custom, adfs_custom=:adfs_custom WHERE groupID=:gid");

    if($updateGroup->execute(array(
      ":custom" => json_encode($customInputs),
      ":adfs_custom" => (isset($customADFS)) ? json_encode($customADFS) : $row["adfs_custom"],
      ":gid" =>  $this->groupID
    ))) {
      return true;
    }else {
      return false;
    }
  }

  /**
   * Returns true or false
   * Requires: $groupID
   *
   * $values = array(
   *   confirm
   * );
   */
  private function refreshSecretKey($values) { //SDK
    //Get variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Check if confirm is set
    if(!isset($values["confirm"]) || empty($values["confirm"])) {
      return false;
    }

    //Generate secret key
    $key = "";
    $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012345678901234567890123456789";
    for($i = 0; $i < 36; $i++) {
      $key .= str_split($str)[random_int(0, (strlen($str) - 1))];
    }
    $secretKey = Crypt::encrypt($key);

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Updated secret key of group #" . $this->groupID ,
      "table" => "TICKETS_GROUPS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "groupID", "value" => $this->groupID),
      "old" => "No restore possible for this action",
      "new" => ''
    );

    User::modifie($change);

    //Add secret key
    $stmt = $conn->prepare("UPDATE " . TICKETS_GROUPS . " SET sdk_secret_key=:secret_key");
    return $stmt->execute(array(":secret_key" => $secretKey));
  }

  /**
   * Returns array of all groups
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
      $groups = $conn->prepare("SELECT * FROM " . TICKETS_GROUPS . " ORDER BY name, startTime, endTime, tpu DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
      $groups->execute();
    }else {
      //Searched after value
      $groups = $conn->prepare("SELECT * FROM " . TICKETS_GROUPS . " WHERE
      groupID LIKE :groupID OR
      maxTickets LIKE :maxTickets OR
      price LIKE :price OR
      startTime LIKE :startTime OR
      endTime LIKE :endTime OR
      tpu LIKE :tpu OR
      description LIKE :description OR
      name LIKE :name OR
      custom LIKE :custom
      ORDER BY name, startTime, endTime, tpu DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
      $groups->execute(array(
        ":groupID" => "%" .  $search_value . "%",
        ":maxTickets" => "%" . $search_value . "%",
        ":price" => "%" . $search_value . "%",
        ":startTime" => "%" . $search_value . "%",
        ":endTime" => "%" . $search_value . "%",
        ":tpu" => "%" . $search_value . "%",
        ":description" => "%" . $search_value . "%",
        ":name" => "%" . $search_value . "%",
        ":custom" => "%" . $search_value . "%",
      ));
    }

    return $groups->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Updates a group and returns true or false
   * Requires: $groupID
   *
   * $selection = Subsubpage ID (INT)
   * $values = array() [Contains key of DB or required values for subfunction]
   */
  public function update($selection, $values) {
    //Require variables
    global $current_user;

    //Create connection
    $conn = Access::connect();

    // Generate values
    switch( $selection )  {
      case 1: //General
        $group_page = "Allgemein";
      break;
      case 2: //userInputs
        $group_page = "Formular";
        return $this->updateUserInputs($values);
      break;
      case 3: //ticket
        $group_page = "Ticket";
      break;
      case 4: //mail
        $group_page = "Mail";

        //Modifie values
        $values["mail_msg"] = nl2br(htmlspecialchars($values["mail_msg"]));
      break;
      case 5: //payment
        $group_page = "Payment";

        //Modifie values
        $values["payment_store"] = (isset($values["payment_store"]) ? 1 : 0);
        $values["payment_mail_msg"] = nl2br(htmlspecialchars($values["payment_mail_msg"]));
        $values["adfs"] = (isset($values["adfs"]) ? 1 : 0 );
        $values["adfs_custom"] = isset($values["adfs_custom"]) ? json_encode($values["adfs_custom"]) : '';
      break;
      case 6: //SDK
        $group_page = "SDK";
        return $this->refreshSecretKey($values);
      break;
      default: //Default is false
        return false;
    }

    // Valid keys
    $valid_keys = array( "maxTickets", "price", "vat", "currency", "startTime", "endTime", "tpu", "ticket_title", "ticket_logo_fileID", "ticket_advert1_fileID", "ticket_advert2_fileID", "ticket_advert3_fileID", "mail_banner_fileID", "mail_from", "mail_displayName", "mail_subject", "mail_msg", "payment_mail_msg", "payment_store", "payment_logo_fileID", "payment_background_fileID", "adfs", "adfs_custom", "payment_payrexx_instance", "payment_payrexx_secret", "description", "name", "custom" );

    // Ccheck values
    $checked_values = array_intersect_key( $values, array_flip( $valid_keys ) );

    // Create sql
    $sql = "UPDATE " . TICKETS_GROUPS . " SET ";
    foreach( $checked_values as $key=>$value ) {
      $sql .= $key . "='" . $value . "', ";
    }
    $sql = substr( $sql, 0, -2 ) . " WHERE groupID=:gid";

    // Get old values
    $old_values = array_intersect_key( $this->values(), $checked_values );

    // Do request
    $update = $conn->prepare( $sql );
    if( $update->execute(array( ":gid" => $this->groupID )) ) {
      //Create modification
      $change = array(
        "user" => $current_user,
        "message" => "Updated Group #" . $this->groupID ." [" . $group_page . "]",
        "table" => "TICKETS_GROUPS",
        "function" => "UPDATE",
        "primary_key" => array("key" => "groupID", "value" => $this->groupID),
        "old" => $old_values,
        "new" => $checked_values,
      );

      User::modifie( $change );
    }else {
      // Something went wrong
      return false;
    }

    // Everything ok
    return true;
  }

  /**
   * Adds a new group and returns true or false
   *
   * $values = array(
   *    color
   *    name
   * );
   */
  public function add( $values ) {
    //request variables
    global $current_user;

    //Create connection
    $conn = Access::connect();

    //Create random color
    $color = '#' . bin2hex(random_bytes(3));

    //Insert into table
    $addGroup = $conn->prepare("INSERT INTO " . TICKETS_GROUPS . " (color, name, maxTickets, price, vat, tpu, startTime, endTime) VALUES (:color, :name, 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00')");
    if(! $addGroup->execute(array(":color" => $color, ":name" => $values["name"]))) {
      return false;
    }

    $this->groupID = $conn->lastInsertId();

    //Set secret key
    $this->refreshSecretKey(array("confirm" => "update"));

    $change = array(
      "user" => $current_user,
      "message" => "Added Group " . $values["name"],
      "table" => "TICKETS_GROUPS",
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "groupID", "value" => $conn->lastInsertId()),
      "old" => "",
      "new" => array(
        "name" => $values["name"],
        "color" => $color
      )
    );

    User::modifie( $change );

    //everything done successufully
    return true;
  }

  /**
   * Removes a group and returns true or false
   * Requires: $groupID
   *
   * FULL_RESTORE informations: (Consutlate general.php file for future informations abrout FULL_RESTORE)
   *   true = remove database entry
   *   false = remove database entry and files
   */
  public function remove() {
    //request variables
    global $current_user;

    //Create connection
    $conn = Access::connect();

    //Get restore data
    $restore = $conn->prepare("SELECT name, maxTickets, tpu, currency, price, startTime, endTime, vat, description, custom, color FROM " . TICKETS_GROUPS . " WHERE groupID=:gid");
    $restore->execute(array(":gid" => $this->groupID));
    $restoreData = $restore->fetch(PDO::FETCH_ASSOC);

    //Remove from database
    $removeRow = $conn->prepare("DELETE FROM " . TICKETS_GROUPS . " WHERE groupID=:gid");
    if(! $removeRow->execute(array(":gid" => $this->groupID))) {
      return false;
    }

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => "Removed Group #" . $this->groupID,
      "table" => "TICKETS_GROUPS",
      "function" => "DELETE",
      "primary_key" => array("key" => "groupID", "value" => $this->groupID),
      "old" => $restoreData,
      "new" => json_decode ("{}")
    );

    User::modifie( $change );

    //everything done successufully
    return true;
  }

  /**
   * Returns values of a group
   * Requires: $groupID
   */
  public function values() {
    //Connect to database
    $conn = Access::connect();

    //Get all infos out of database
    $groupInfos = $conn->prepare("SELECT * FROM " . TICKETS_GROUPS . " WHERE groupID=:gid");
    $groupInfos->execute(array(":gid" => $this->groupID));

    //GroupInfosArray
    $groupInfos = $groupInfos->fetch(PDO::FETCH_ASSOC);

    //Define currency
    $groupInfos["currency"] = (empty($groupInfos["currency"]) ? DEFAULT_CURRENCY : $groupInfos["currency"]);

    //Return array
    return $groupInfos;
  }

  /**
   * Returns number of used tickets for this group
   * Requires: $groupID
   */
  public function ticketsNum() {
      //Get database connection
      $conn = Access::connect();

      //Count all tickets that are used
      $counter = $conn->prepare("SELECT groupID FROM " . TICKETS . " WHERE groupID=:gid AND state <> 2");
      $counter->execute(array(":gid" => $this->groupID));

      //Return counts
      return $counter->rowCount();
  }

  /**
   * Returns number of available tickets for this group
   * Requires: $groupID
   */
  public function availableTickets() {
    $availableTickets = $this->values()["maxTickets"] - $this->ticketsNum(); //Calculate available tickets
    return ($availableTickets > 0) ? $availableTickets : 0;
  }

  /**
   * Returns number of acquired tickets by user and group
   * Requires: $groupID
   *
   * $email = Identification mail of user
   */
  public function tpuAvailable( $email ) {
    if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    //Get database connection
    $conn = Access::connect();

    //Count all tickets of user wiht this group
    $counter = $conn->prepare("SELECT groupID FROM " . TICKETS . " WHERE groupID=:gid AND email=:email AND state <> 2");
    $counter->execute(array(":gid" => $this->groupID, ":email" => $email));

    //Return if available or not
    return ($this->values()["tpu"] > $counter->rowCount()) ? true : false;
  }

  /**
   * Checks if timewindow is open and returns true or false
   * Requires: $groupID
   */
  public function timeWindow() {
    //Define dates
    $startTime = strtotime($this->values()["startTime"]);
    $endTime = strtotime($this->values()["endTime"]);
    $currentTime = strtotime("now");

    //If both dates are the same
    if($startTime === $endTime) {
      return true; //open
    }

    //If current time is in range
    if(($startTime <= $currentTime) && ($endTime >= $currentTime)) {
      return true; //open
    }

    //window closed
    return false;
  }
}
 ?>
