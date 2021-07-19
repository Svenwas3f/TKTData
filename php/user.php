<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage user actions
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $user: User
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 ******** custom functions ********
 *
 * User->hashPw ( $password [plain password] ) {private function}
 *
 * User->name ( $user [identification number] ) {static function}
 *
 * User->r_access_allowed ( $page [selected page], $user [identification number] ) {static function}
 *
 * User->w_access_allowed ($page [selected page], $user [identification number] ) {static function}
 *
 * User->first_accessable_page ( $user [identification number] ) {static function}
 *
 * User->system_access ( $user [identification number] ) {static function}
 *
 * User->modifie ( $achnage [array of informations] ) {static function}
 *
 * User->authorize ( $user [identification number], $password [plain password] ) {static function}
 *
 * User->authorizeId ( $id [email or identification number] ) {static function}
 *
 * User->resetPassword ( $user [identification number] ) {static function}
 *
 * User->all ( $offset [int], $steps [int], $search_value [info_string] ) {static function}
 *
 ******** User functions ********
 *
 * User->updateRights ( $newValues [array with new values] ) [$user]
 *
 * User->updateInfos ($name [display name], $email [email of user] ) [$user]
 *
 * User->updatePassword ($pw1 [first password], $pw2 [second password] ) [$user]
 *
 * User->values( $fetchMode [PDO Fetch mode] ) [$user]
 *
 * User->rights () [$user]
 *
 * User->remove () [$user]
 *
 * User->add ($email [email of user], $userID [identification number], $name [display name of user], $rights [array for userrights], $sendMail [boolean, send mail to user or not] ) [$user]
 *
 ******** Backup system ********
 *
 * User->restore_action ( $id [restore id] )
 *
 */
class User {
  /**
   * Returns hashed password
   *
   * $password = plain password
   */
  private function hashPw($password) {
    //Hash password
    $salt_parts = str_split(SALT_STRING, round(strlen( SALT_STRING ) / 2)); //Not working with unicode chars such as äöü
    // return hash("sha512", $password);
    return hash("sha512", $salt_parts[0] . $password . $salt_parts[1]);
  }

  /**
   * Returns name of user

   * $user = identification number
   */
  public static function name($user){
    $conn = Access::connect();
    $stmt = $conn->prepare("SELECT name FROM " . USERS . " WHERE id=:user");
    $stmt->execute(array( ":user" => $user ));

    return $stmt->fetch()[0] ?? null;
  }

  /**
   * Checks if user can read this page and returns true or false
   *
   * $page = selected page (use $page)
   * $user = identification number
   */
  public static function r_access_allowed($page, $user){
    //Check if user is allowed to see this page
    //First statement checks page
    //Second statement checks if the mainmenu has access
    $conn = Access::connect();
    $stmt = $conn->prepare("SELECT id FROM " . USER_RIGHTS . " WHERE
      (`userId`= :user1 AND `page`= :page1 AND `r`=1)
      OR
      (`userId`= :user2 AND `page` IN (SELECT `id` FROM " . MENU . " WHERE `submenu`= :page2) AND `r`=1)
    ");
    $stmt->execute(array(
      ":user1" => $user,
      ":page1" => $page,
      ":user2" => $user,
      ":page2" => $page
    ));

    if($stmt->rowCount() > 0) {
      return true;
    }else {
      return false;
    }

  }

  /**
   * Checks if user can read edit page and returns true or false
   *
   * $page = selected page (use $page)
   * $user = identification number
   */
  public static function w_access_allowed($page, $user){
    //Check if user is allowed to edit this page
    //First statement checks page
    //Second statement checks if the mainmenu has access
    $conn = Access::connect();
    $stmt = $conn->prepare("SELECT id FROM " . USER_RIGHTS . " WHERE
      (`userId`= :user1 AND `page`= :page1 AND `w`=1)
      OR
      (`userId`= :user2 AND `page` IN (SELECT `id` FROM " . MENU . " WHERE `submenu`= :page2) AND `w`=1)
    ");
    $stmt->execute(array(
      ":user1" => $user,
      ":page1" => $page,
      ":user2" => $user,
      ":page2" => $page
    ));

    if($stmt->rowCount() > 0) {
      return true;
    }else {
      return false;
    }

  }

  /**
   * Returns first accessable page for user
   *
   * $user = identification number
   */
  public static function first_accessable_page($user){
    //Return id
    $conn = Access::connect();
    $stmt = $conn->prepare("SELECT id FROM " . MENU . " WHERE id IN ( SELECT page FROM " . USER_RIGHTS . " WHERE userid= :user) ORDER BY submenu, layout ");
    $stmt->execute(array(
      ":user" => $user,
    ));
    $first_page = $stmt->fetch()[0];


    return intval($first_page);
  }

  /**
   * Checks if user has access to system and returns true or false
   *
   * $user = identification number
   */
  public static function system_access($user){
    if( User::first_accessable_page($user) > 0){
      return true;
    }else{
      return false;
    }
   }

  /**
   * Adds action to activity and returns true or false
   *
   * $change = array(
   *  "user" => User who made action,
   *  "message" => Action description,
   *  "table" => edited table,
   *  "function" => function used to edit table,
   *  "old" => old value before change,
   *  "new" => new value of change
   * )
   */
  public static function modifie( $change ){
    //Sql to change$
    $conn = Access::connect();
    $stmt = $conn->prepare("INSERT INTO " . USER_ACTIONS . " (
        userID,
        print_message,
        affected_table,
        id_cell,
        sql_modification,
        old_value,
        new_value,
        modification_time
      ) VALUES (
        :user,
        :message,
        :table,
        :primaryKey,
        :function,
        :old_value,
        :new_value,
        CURRENT_TIMESTAMP
      )");

    $result = $stmt->execute(array(
      ":user" => $change["user"],
      ":message" => $change["message"],
      ":table" => $change["table"],
      ":primaryKey" => (! empty($change["primary_key"])) ? json_encode($change["primary_key"]) : "",
      ":function" => $change["function"],
      ":old_value" => (! empty($change["old"])) ? json_encode($change["old"]) : "",
      ":new_value" => (! empty($change["new"])) ? json_encode($change["new"]) : ""
    ));

    if( $result ) {
      return true;
    }else {
      return false;
    }

  }

  /**
   * Authorize user and return true or false
   *
   * $user = identification number
   * $password = plain password for identification number
   */
  public static function authorize( $user, $password ) {
    //Get database connection
    $conn = Access::connect();

    //Hash password
    $password = User::hashPw($password);

    //Create authorization statement
    $authorize = $conn->prepare("SELECT id FROM " . USERS . " WHERE (id=:id OR email=:email) AND password=:password");
    $authorize->execute(array(":id" => $user, ":email" => $user, ":password" => $password));

    //Return if user ist authorized
    return ($authorize->rowCount() > 0) ? true : false;
  }

  /**
   * Returns false or informations about user
   *
   * $id = email or identification number
   */
  public static function authorizeId( $id ) {
    //Get database connection
    $conn = Access::connect();

    //Create query
    $authorize = $conn->prepare("SELECT id, email, name FROM " . USERS . " WHERE id=:id OR email=:email");
    $authorize->execute(array(":id" => $id, ":email" => $id));

    //Check if user exists
    if($authorize->rowCount() > 0) {
      return $authorize->fetch(PDO::FETCH_ASSOC); //Return id and email of user
    }else {
      return false; //User does not exist
    }
  }


  /**
   * Returns new password or false
   *
   * $user = identification number
   */
  public static function resetPassword( $user ) {
    //Get database connection
    $conn = Access::connect();

    //Check if user exists
    $userCheck = $conn->prepare("SELECT id FROM " . USERS . " WHERE id=:id");
    $userCheck->execute(array(":id" => $user));
    if($userCheck->rowCount() < 1) {
      return false;
    }

    //Crete new password
    $allowedCharacters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvw0123456789$!-_<>()[]{}/*+#%&?";
    $chA = str_split($allowedCharacters); //Char array
    $rk = array_rand($chA, 8); //random_keys
    $newPassword = $chA[$rk[0]] . $chA[$rk[1]] . $chA[$rk[2]] . $chA[$rk[3]] . $chA[$rk[4]] . $chA[$rk[5]] . $chA[$rk[6]] . $chA[$rk[7]];

    //Insert new password
    $userClass = new User();
    $userClass->user = $user;
    $userClass->updatePassword($newPassword, $newPassword);

    //return
    return $newPassword;
  }

  /**
   * Returns array of all users
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public static function all( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value)) {
      $users = $conn->prepare("SELECT * FROM " . USERS . " ORDER BY name LIMIT " . $steps . " OFFSET " . $offset );
      $users->execute();
    }else {
      $users = $conn->prepare("SELECT * FROM " . USERS . " WHERE name LIKE :name OR email=:email OR id=:id ORDER BY name LIMIT " . $steps . " OFFSET " . $offset );
      $users->execute(array(
        ":name" => "%" . $search_value . "%",
        ":email" => $search_value,
        ":id" => $search_value,
      ));
    }

    // Return array
    return $users->fetchAll( PDO::FETCH_ASSOC );
  }

  /**************************************************** User actions ***************************************************/

  public $user;

  /**
   * Updates user rights and returns true or false
   * Requires: $user
   *
   * $newValues = array(
   *   page = array(
   *      w
   *      r
   *   )
   * );
   */
  public function updateRights($newValues) {
    //Require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Read current rights
    $old_rights = $conn->prepare("SELECT page, r, w FROM " . USER_RIGHTS . " WHERE userId=:user");
    $old_rights->execute(array(":user" => $this->user));
    $old_rights = $old_rights->fetchAll(PDO::FETCH_ASSOC);

    //Delete current rights
    $remove_rights = $conn->prepare("DELETE FROM " . USER_RIGHTS . " WHERE userid=:user");
    if(! $remove_rights->execute(array(":user" => $this->user))) {//If failed then return false
      return false;
    }

    //Add new rights
    foreach($newValues as $page => $access_infos) {
      //Check if pages exists
      $pageExists = $conn->prepare("SELECT id FROM " . MENU . " WHERE id=:page");
      $pageExists->execute(array(":page" => $page));
      if( $pageExists->rowCount() != 1 ) {
        continue; //Page does not exist skip to next
      }

      //Check if user has read or write access
      $new_rights = array();
      if( in_array("w", $access_infos) ) { //Check if array contains write access
        //User has write access on this page that includes read access
        array_push( $new_rights, array("page" => $page, "w" => 1, "r" => 1)); //Push new array

        //Add rights
        $add_rights = $conn->prepare("INSERT INTO " . USER_RIGHTS . " (userid, page, r, w) VALUES(:user, :page, 1, 1)");
        if(! $add_rights->execute(array(":user" => $this->user, ":page" => $page))) {
          return false;
        }

      }else {
        //User has read access
        array_push( $new_rights, array("page" => $page, "w" => 0, "r" => 1)); //Push new array

        //Add rights
        $add_rights = $conn->prepare("INSERT INTO " . USER_RIGHTS . " (userid, page, r, w) VALUES(:user, :page, 1, 0)");
        if(! $add_rights->execute(array(":user" => $this->user, ":page" => $page))) {
          return false;
        }

      }
    }

    //Create modification
    $change = array(
      "user" => $current_user,
      // "message" => "Updated profile from  " . $this->user,
      "message" => json_encode(array(
        "id" => 100,
        "replacements" => array(
          '%user%' => $this->user,
        ),
      )),
      "table" => "USER_RIGHTS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "userId", "value" => $this->user),
      "old" =>  $old_rights,
      "new" => $new_rights
    );

    User::modifie( $change );

    //Return success
    return true;
  }

  /**
   * Update infos and returns true or false
   * Requires: $user
   *
   * $name = Display name of user
   * $email = Email of user
   */
  public function updateInfos($name, $email, $language) {
    //Require variables
    global $current_user;

    //Check if email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 2;
    }

    //Get database connection
    $conn = Access::connect();

    //Read current infos and add modification
    $old_values = $conn->prepare("SELECT name, email, language FROM " . USERS . " WHERE id=:id");
    $old_values->execute(array(":id" => $this->user));
    $old_values = $old_values->fetch(PDO::FETCH_ASSOC);

    $change = array(
      "user" => $current_user,
      "message" => json_encode(array(
        "id" => 100,
        "replacements" => array(
          '%user%' => $this->user,
        ),
      )),
      "table" => "USERS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "id", "value" => $this->user),
      "old" => $old_values,
      "new" => array("name" => $name, "email" => $email, "language" => $language)
    );

    User::modifie( $change );


    //Update current infos
    $newInfos = $conn->prepare("UPDATE " . USERS . " SET name=:name, email=:email, language=:language WHERE id=:id");

    return $newInfos->execute(array(
      ":name" => ($name ?? $old_values["name"]),
      ":email" => ($email ?? $old_values["email"]),
      ":language" => ($language ?? $old_values["language"]),
      ":id" => $this->user));
  }

  /**
   * Updates password and returns true or false
   * Requires: $user
   *
   * $pw1 = Password one
   * $pw2 = confirmations password
   */
  public function updatePassword($pw1, $pw2) {
    //Get database connection
    $conn = Access::connect();

    //Check if bouth passwords are the same
    if($pw1 !== $pw2) {
      return false;
    }

    //Check if new password is the same as old
    if($this->hashPw($pw1) === $this->values()["password"]) {
      return false;
    }

    //Set new password
    $newPassword = $conn->prepare("UPDATE " . USERS . " SET password=:password WHERE id=:id");
    return $newPassword->execute(array(":password" => $this->hashPw($pw1), ":id" => $this->user));
  }

  /**
   * Returns value
   * Requires: $user
   *
   * $fetchMode = PDO Fetch mode
   */
  public function values($fetchMode = null) {
    //Get database connection
    $conn = Access::connect();

    $userInfo = $conn->prepare("SELECT * FROM " . USERS . " WHERE id=:id LIMIT 0, 1");
    $userInfo->execute(array(":id" => $this->user));


    //Return content
    return $userInfo->fetch($fetchMode);
  }

  /**
   * Returns all rights
   * requires: $user
   */
  public function rights() {
    //Get database connection
    $conn = Access::connect();

    $userInfo = $conn->prepare("SELECT * FROM " . USER_RIGHTS . " WHERE userId=:userId");
    $userInfo->execute(array(":userId" => $this->user));

    // Start rights
    $rights = array();

    foreach( $userInfo->fetchAll() as $page) {
      $rights[$page["page"]] = array(
        (isset($page["w"]) ? "w" : ""),
        (isset($page["r"]) ? "w" : ""),
      );
    }

    return $rights;
  }

  /**
   * Remove all rights and remove user and returns true or false;
   *
   */
  public function remove(){
    //Require variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();


    //Create modification
    $old_values_user_req = $conn->prepare( "SELECT * FROM " . USERS . " WHERE id=:user");
    $old_values_user_req->execute(array(":user" => $this->user));
    $old_values_user = $old_values_user_req->fetch();

    $old_values_rights = array();

    $old_rights = $conn->prepare("SELECT page, r, w FROM " . USER_RIGHTS . " WHERE userId= :user");
    $old_rights->execute(array(":user" => $this->user));

    while($row =  $old_rights->fetch()){
      array_push( $old_values_rights, array(
        "page" => $row[0],
        "w" => $row[2],
        "r" => $row[1]
      ));
    }

    $change_user = array(
      "user" => $current_user,
      // "message" => "Removed " . $this->user . "'s profile",
      "message" => json_encode(array(
        "id" => 101,
        "replacements" => array(
          '%user%' => $this->user,
        ),
      )),
      "table" => "USERS",
      "function" => "DELETE",
      "primary_key" => array("key" => "userId", "value" => $this->user),
      "old" =>  $old_values_user,
      "new" => json_decode ("{}")
    );

    $change_rights = array(
      "user" => $current_user,
      // "message" => "Removed  " . $this->user . "'s rights",
      "message" => json_encode(array(
        "id" => 102,
        "replacements" => array(
          '%user%' => $this->user,
        ),
      )),
      "table" => "USER_RIGHTS",
      "function" => "DELETE",
      "primary_key" => array("key" => "userId", "value" => $this->user),
      "old" =>  $old_values_rights,
      "new" => json_decode ("{}")
    );

    User::modifie( $change_user );
    User::modifie( $change_rights );

    //Remove user
    $remove_user = $conn->prepare("DELETE FROM ".USERS." WHERE id=:user");
    if(! $remove_user->execute(array(":user" => $this->user))) {
      return false;
    }

    //Remove rights
    $remove_rights = $conn->prepare("DELETE FROM ".USER_RIGHTS." WHERE userid=:user");
    if(! $remove_rights->execute(array(":user" => $this->user))){
      return false;
    }

    //everthing correct
    return true;
  }

  /**
   * Add user and return true or false
   *
   * $email = email of user
   * $userID = identification number [not required]
   * $userName = Name of user [not required]
   * $rights = rights of user ([ pageid => "w/r", 5 => "w" ]) [not required]
   * $sendMail = boolean to check if user should recieve an email [not required]
   */
  public function add($email, $userID = null, $name = null, $rights = null, $sendMail = true){
    //Require variables
    global $url;
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Userid not set, create one
    if(empty($userID)) {
      do {
        $userID = "k".mt_rand(10000,99999); //Create user id

        //Check id
        $id_check = $conn->prepare("SELECT * FROM " . USERS . " WHERE id=:userID");
        $id_check->execute(array(":userID" => $userID));
      } while ( $id_check->rowCount() > 0);
    }

    //Check if email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    //Check if username is available
    $id_check = $conn->prepare("SELECT * FROM " . USERS . " WHERE id=:userID");
    $id_check->execute(array(":userID" => $userID));
    if($id_check->rowCount() > 0) {
      return false;
    }

    //Add user
    $addUser = $conn->prepare("INSERT INTO " . USERS . " (id, name, email) VALUES(:id, :name, :email)");
    if(!$addUser->execute(array(":id" => $userID, ":name" => $name, ":email" => $email))) {
      return false;
    }

    //Set user
    $this->user = $userID;

    //Update/add rights
    if(!$this->updateRights($rights)) {
      return false;
    }

    //Create new password
    $password = User::resetPassword($userID);
    if($password === false) {
      return false;
    }

    //Send welcome mail
    if($sendMail) {
      $msg = 'Guten Tag ' . $name . '<br />
      <br />
      Sie wurden beim System registriert. <br />
      Melden Sie sich unter <a href="' . $url . '/auth.php" title="Zum login">' . $url . '/auth.php</a> mit folgenden Daten an:<br />
      Benutzername: <strong><b>' . $this->user . '</b></strong><br />
      Passwort: <strong><b>' . $password . '</b></strong><br />
      <br />
      Vielen Dank.';

      $mail = new TKTDataMailer();
      $mail->CharSet = "UTF-8";
      $mail->setFrom(EMAIL, "TKTDATA - WELCOME");
      $mail->addAddress($email);
      $mail->Subject = "Willkommen bei TKTDATA. Sie wurden zu unserem System hinzugefügt.";
      $mail->msgHTML( $mail->tktdataMail( $msg ) );

      if(! $mail->send()) {
        return false;
      }

    }

    //Create modification
    $change = array(
      "user" => $current_user,
      // "message" => "Added User " . $name . " (" . $this->user . ")",
      "message" => json_encode(array(
        "id" => 103,
        "replacements" => array(
          '%user%' => $this->user,
        ),
      )),
      "table" => "USERS",
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "id", "value" => $this->user),
      "old" => "",
      "new" => ""
    );

    User::modifie( $change );


    //All ok
    return true;
  }

  /****************************************************
   * backup system
  ***************************************************/

  /**
   * Returns array of all actions
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public static function actions( $offset = 0, $steps = 50, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    // Select actions
    if( is_null($search_value) || empty($search_value)) {
      $actions = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " ORDER BY modification_time DESC, id DESC LIMIT " . $steps . " OFFSET " . $offset);
      $actions->execute();
    }else {
      $actions = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " WHERE print_message LIKE :print_message OR userID LIKE :userID ORDER BY modification_time DESC, id DESC LIMIT " . $steps . " OFFSET " . $offset);
      $actions->execute(array(
        ":print_message" => "%" . $search_value . "%",
        ":userID" => "%" . $search_value . "%",
      ));
    }

    // Return array
    return $actions->fetchAll( PDO::FETCH_ASSOC );
  }


  /**
   * Restore action and return true or false
   *
   * $id = id of restore
   */
  //Add in db a field delete and delete everything before update to ensure that the user has restored rights and not more
  public function restore_action( $id ){
    //Define variables
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //get data
    $restore_data = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " WHERE id=:id");
    $restore_data->execute(array(":id" => $id));
    $data = $restore_data->fetch(PDO::FETCH_BOTH);

    $new_data = json_decode( $data["old_value"], true );
    $primary_key = json_decode( $data["id_cell"], true );

    //keys
    $primaryKey = array();
    $primaryValue = array();
    for( $i = 0; $i < count($primary_key); $i++) {
      array_push($primaryKey, array_values( $primary_key )[$i]);
      array_push($primaryValue, array_values( $primary_key )[++$i]);
    }

    //Function to check if array has other array in it
    function contains_array($array){
        foreach($array as $value){
            if(is_array($value)) {
              return true;
            }
        }
        return false;
    }

    //Check if db stores json
    // function json_dbValue($array) {
    //   //Array to store new json_encoded array
    //   $result = $array;
    //
    //   //Go trhoug first array
    //   foreach($array as $key1 => $value1) {
    //     if( is_array($value1)) {
    //       //Loop through new array
    //       foreach( $value1 as $key2 => $value2) {
    //         if( is_array($value2)) {
    //           $result[$key1][$key2] = json_encode($value2); //Value is stored as JSON in database
    //         }else {
    //           $result[$key1][$key2] = $value2; //Value is stored as FIELDS in database
    //         }
    //       }
    //
    //     }else {
    //       $result[$key1] = $value1;
    //     }
    //   }
    //
    //   //Return result
    //   return $result;
    // }

    //Get new data
    $keys = "";
    for( $i = 0; $i < count($primaryKey); $i++) {
      $keys .= $primaryKey[$i] . "='" . $primaryValue[$i] . "'";
    }

    if(contains_array($new_data)) {
      $acctual_data_req = $conn->prepare("SELECT " . implode(", ", array_keys($new_data[0])) . " FROM " . constant( $data["affected_table"] ) . " WHERE " . $keys);
      $acctual_data_req->execute();
      $acctual_data = $acctual_data_req->fetchAll(PDO::FETCH_ASSOC);
    }else{
      $acctual_data_req = $conn->prepare("SELECT " . implode(", ", array_keys($new_data)) . " FROM " . constant( $data["affected_table"] ) . " WHERE " . $keys);
      $acctual_data_req->execute();
      $acctual_data = $acctual_data_req->fetchAll(PDO::FETCH_ASSOC);
    }

    //Modifie user
    User::modifie(array(
      "user" => $current_user,
      // "message" => "Restored version #" . $data["id"],
      "message" => json_encode(array(
        "id" => 104,
        "replacements" => array(
          '%version%' => $data["id"],
        ),
      )),      "table" => $data["affected_table"],
      "function" => $data["sql_modification"],
      "primary_key" => $primary_key,
      "old" => $acctual_data,
      "new" => json_decode($data["old_value"])
    ));

    //Restore data
    if(! contains_array( $new_data )) {
      //Single data
      $restore_sql = "INSERT INTO " . constant( $data["affected_table"] ) . " (";
        $restore_sql .= "`" . implode("`, `", $primaryKey) . "`,";
        $restore_sql .= "`" . implode("`, `", array_keys( $new_data )) . "`";
      $restore_sql .= ") VALUES (";
        $restore_sql .= "'" . implode("`, `", $primaryValue) . "', ";
        foreach( $new_data as $data ) {
          if( is_null($data) ) {
            $restore_sql .= "NULL, ";
          }else {
            $restore_sql .= "'" . $data . "', ";
          }
        }
      $restore_sql = substr($restore_sql, 0, -2) . ") ON DUPLICATE KEY UPDATE ";

      foreach( $new_data as $key => $value ){
        $comma = (($key == array_key_last($new_data) ? "" : ", "));
        $restore_sql .= $key . "='" . $value . "'" . $comma;
      }

      //Execute query
      $restore = $conn->prepare($restore_sql);
      if( $restore->execute() ) {
        return true;
      }else{
        return false;
      }

    }else{
      //Multiple data sent
      foreach( $new_data as $values ){
        $restore_sql = "INSERT INTO " . constant( $data["affected_table"] ) . " (
            `" . implode("`, `", $primaryKey) . "`,
            `" . implode("`, `", array_keys( $values )) . "`
          ) VALUES (
            '" . implode("`, `", $primaryValue) . "',
            '" . implode("', '", $values) . "'
          ) ON DUPLICATE KEY UPDATE ";
        foreach( $values as $key => $value ){
          $comma = (($key == array_key_last($values) ? "" : ", "));
          $restore_sql .= $key . "='" . $value . "'" . $comma;
        }

        //Execute query
        $restore = $conn->prepare($restore_sql);
        if(! $restore->execute()){
          return false;
        }
      }
    }

    //Everything ok
    return true;

  }
}
 ?>
