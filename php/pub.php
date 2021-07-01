<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: Mai 2021
 * @Purpose: File to manage pub ations
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $pub: pub Id for pub
 * $product_id: Id of requested product
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * pub->all ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * pub->transactions ( $offset [int], $steps [int] ) [$pub]
 *
 * pub->add ( $table [const], $values [array] ) [$pub]
 *
 * pub->update_pub ( $values [array] ) [$pub]
 *
 * pub->update_product( $values [$array] ) [$product_id]
 *
 * pub->remove_pub () [$pub]
 *
 * pub->remove_product ( $product_id [int] ) [$product_id]
 *
 * pub->remove_access ( $user [int] ) [$pub]
 *
 * pub->access ( $user [int or null], $offset [int], $steps [int] ) [$pub]
 *
 * pub->accessable ( $user [string] )
 *
 * pub->product () [$product_id]
 *
 * pub->products ( $offset [int], $steps [int], $search_value [info_string], $include_globals [boolean] ) [$pub]
 *
 * pub->sections () [$pub]
 *
 * pub->products_by_section () [$pub]
 *
 * pub->global_products ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * pub->product_visibility () [$pub, $product_id]
 *
 * pub->product_availability () [$pub, $product_id]
 *
 * pub->toggleVisibility () [$pub, $product_id]
 *
 * pub->update_availability () [$pub, $product_id]
 *
 * pub->values () [$pub]
 *
 *
 **************** availability explanation ****************
 *
 * AVAILABILITY
 * 0: available
 * 1: little available
 * 2: sold
 *
 */
class Pub {
  //Variables
  public $pub;
  public $product_id;

  //constants
  const DEFAULT_TABLE = PUB;
  const PRODUCTS_TABLE = PUB_PRODUCTS;
  const ACCESS_TALBE = PUB_ACCESS;

  /**
   * Returns array of all pubs
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
      $pub = $conn->prepare("SELECT * FROM " . PUB . " ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset);
      $pub->execute();
    }else {
      // Select all
      $pub = $conn->prepare("SELECT * FROM " . PUB . " WHERE pub_id=:pub_id OR name LIKE :name  ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset);
      $pub->execute(array(
        ":pub_id" => $search_value,
        ":name" => "%" . $search_value . "%"
      ));
    }

    return $pub->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns a list of all transactions (in steps) that belong to the pub
   * requires: $pub
   *
   * $offset: at what row you want to start
   * $steps: How many rows
   *
   * Important notice. This function is recursive so please use the $steps wisely and do not execute this function with a very heigh $steps number
   * The offset you can set as heigh as you want this does not affect the function in executiont ime
   */
  public function transactions( $offset = 0, $steps = 20 ) {
    // Define transaction list
    $transaction_list = array();

    //Payrexx files
    spl_autoload_register(function($class) {
        $root = dirname(__DIR__) . "/php/Payrexx-SDK";
        $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($classFile)) {
            require_once $classFile;
        }
    });

    // $instanceName is a part of the url where you access your payrexx installation.
    // https://{$instanceName}.payrexx.com
    $instanceName = $this->values()["payment_payrexx_instance"];

    // $secret is the payrexx secret for the communication between the applications
    // if you think someone got your secret, just regenerate it in the payrexx administration
    $secret = $this->values()["payment_payrexx_secret"];

    $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

    $transaction = new \Payrexx\Models\Request\Transaction();
    $transaction->setOffset($offset);
    $transaction->setLimit($steps + $offset);

    try {
        $response = $payrexx->getAll($transaction);
    } catch (\Payrexx\PayrexxException $e) {
        Action::fail($e->getMessage());
        return;
    }

    foreach( $response as $response ) {
      if( $response->getPurpose() == "pub-" . $this->pub ) {
        // Add to array
        array_push( $transaction_list,
          array(
            "transaction_retrieve_status" => true,
            "id" => $transaction->getId(),
            "status" => $transaction->getStatus(),
            "time" => $transaction->getTime(),
            "psp" => $transaction->getPsp(),
            "pspId" => $transaction->getPspId(),
            "purpose" => $transaction->getPurpose(),
          )
        );

        // Check if maximum reached
        if( count( $transation_list ) >= $steps) {
          return $transaction_list; // Return list
        }
      }
    }

    // Check if max requested or max transactions are reached
    if( !empty( $transaction_list ) && count( $transaction_list ) < $steps && count( $response ) == $steps ) {
      // Get new values
      $next_request = $this->list( ($offset + $steps), $steps ); // Recursive use of this function

      // Add new value
      if(! is_null( $next_request )) {
        array_push( $transaction_list,  $next_request );
      }
    }

    // All elements found
    return $transaction_list;
  }

  /**
   * Adds a new row in requested table
   *
   * $table: Uses Table name use class constants (Supports DEFAULT_TABLE and PRODUCTS_TABLE)
   * $values: Array with new values
   *          DFAULT TABLE:   array(
   *                            name,
   *                            payment_payrexx_instance,
   *                            payment_payrexx_secret,
   *                            payment_fee_absolute, (100 = 1)
   *                            payment_fee_percent (1000 = 100%)
   *                          )
   *          PRODUCTS_TABLE: array(
   *                            pub_id,
   *                            name,
   *                            price,
   *                            currency
   *                          )
   *          ACCESS_TABLE:   array(
   *                            pub_id,
   *                            user_id
   *                          )
   */
  public function add( $table = SELF::DEFAULT_TABLE, $values ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("pub_id", "name", "description", "logo_fileID", "background_fileID", "payment_payrexx_instance", "currency", "payment_payrexx_secret", "payment_fee_absolute", "payment_fee_percent", "tip", "id", "user_id", "w", "r", "section", "price", "product_fileID");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    //Generate query
    $add_query = "INSERT INTO " . $table . " ";
    $add_query .= "(" . implode(", ", array_keys($checked_values)) . ") ";
    $add_query .= "VALUES ('" . implode("', '", $checked_values) . "')";

    // Restore message
    $restore_message = array(
      "pub" => "Added new pub (" . $checked_values["name"] . ")",
      "product" => "Added new" . (is_null($checked_values["pub_id"] ?? null) ? " global" : " ") .  " product (" . ($checked_values["name"] ?? "unknown") . ") " . (is_null($checked_values["pub_id"] ?? null) ? "" : ("for pub #" . $checked_values["pub_id"])),
      "access" => "Added access to pub #" . ($checked_values["pub_id"] ?? "unknown") . " for User (" . $current_user . ") " . User::name( $current_user ),
    );

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => ($table == SELF::DEFAULT_TABLE ? $restore_message["pub"] : ($table == SELF::PRODUCTS_TABLE ? $restore_message["product"] : $restore_message["access"])),
      "table" => ($table == SELF::DEFAULT_TABLE ? "pub" : ($table == SELF::PRODUCTS_TABLE ? "PUB_PRODUCTS" : "PUB_ACCESS")),
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "pub_id", "value" => ""),
      "old" => "",
      "new" => $checked_values
    );

    User::modifie( $change );

    // execute query
    $add = $conn->prepare($add_query);
    $result = $add->execute();

    if( $result === true ) {
      if($table == SELF::DEFAULT_TABLE) {
        $this->pub = $conn->lastInsertId();
      } elseif($table == SELF::PRODUCTS_TABLE) {
        $this->product_id = $conn->lastInsertId();
      }
      return true;
    }else {
      return false;
    }
  }

  /**
   * Updates a pub
   * requires: $pub
   *
   * $values: Array with new values
   *          array(
   *            name,
   *            logo_fileID,
   *            background_fileID,
   *            payment_payrexx_instance,
   *            payment_payrexx_secret,
   *            payment_fee_absolute, (100 = 1)
   *            payment_fee_percent (10000 = 100%)
   *         )
   */
  public function update_pub( $values) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("name", "logo_fileID", "description", "background_fileID", "currency", "payment_payrexx_instance", "payment_payrexx_secret", "payment_fee_absolute", "payment_fee_percent", "tip");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    // Generate values and keys
    $update_query = "UPDATE " . PUB . " SET ";
    foreach( $checked_values as $key => $value ) {
      if( strlen($value) > 0) { // empty does handle 0 as no value
        $update_query .= $key . " = '" . $value . "', ";
      }
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE pub_id=:pub_id";

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Updated pub #" . $this->pub . " (" . $this->values()["name"] . ")",
      "table" => "PUB",
      "function" => "UPDATE",
      "primary_key" => array("key" => "pub_id", "value" => $this->pub),
      "old" => array_intersect_key($this->values(), array_flip($valid_keys)),
      "new" => $checked_values
    );

    User::modifie($change);

    // Update query
    $update = $conn->prepare( $update_query );
    return $update->execute(array(
      ":pub_id" => $this->pub,
    ));
  }

  /**
   * Updates a product
   * requires: $product_id
   *
   * $product_id: Id of product (stored in database)
   * $values: Array with new values
   *          array(
   *            name,
   *            section,
   *            price,
   *            currency,
   *            product_fileID,
   *         )
   */
  public function update_product( $values ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("name", "section", "price", "product_fileID");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    // Generate values and keys
    $update_query = "UPDATE " . PUB_PRODUCTS . " SET ";
    foreach( $checked_values as $key => $value ) {
      if( empty($value) ) {
        $update_query .= $key . " = NULL, ";
      }else {
        $update_query .= $key . " = '" . $value . "', ";
      }
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE id=:product_id";

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Updated product #" . $this->product_id . " (" . $this->product()["name"] . ")",
      "table" => "PUB_PRODUCTS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "id", "value" => $this->product_id),
      "old" => array_intersect_key($this->product(), array_flip($valid_keys)),
      "new" => $checked_values
    );

    User::modifie($change);

    // Update query
    $update = $conn->prepare( $update_query );
    return $update->execute(array(
      ":product_id" => $this->product_id,
    ));
  }

  /**
   * Removes a pub
   * requires: $pub
   */
  public function remove_pub() {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Removed pub #" . $this->pub . " (" . $this->values()["name"] . ")",
      "table" => "PUB",
      "function" => "UPDATE",
      "primary_key" => array("key" => "pub_id", "value" => $this->pub),
      "old" => array_intersect_key($this->values(), array_flip(array("name", "logo_fileID", "background_fileID", "payment_payrexx_instance", "payment_payrexx_secret"))),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . PUB . " WHERE pub_id=:pub_id");
    return $remove->execute(array(
      ":pub_id" => $this->pub,
    ));
  }

  /**
   * Removes a product
   * requires: $product_id
   *
   */
  public function remove_product() {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Removed product #" . $this->product_id . " (" . $this->product()["name"] . ")",
      "table" => "PUB_PRODUCTS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "id", "value" => $this->product_id),
      "old" => array_intersect_key($this->product(), array_flip(array("pub_id", "name", "section", "price", "currency", "product_fileID", "availability"))),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . PUB_PRODUCTS . " WHERE id=:id");
    return $remove->execute(array(
      ":id" => $this->product_id,
    ));
  }

  /**
   * Removes an access
   * requires: $pub
   *
   * $user: User id (stored in database)
   */
  public function remove_access( $user ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Get ID of access
    $access_id = $conn->prepare("SELECT * FROM " . PUB_ACCESS . " WHERE pub_id=:pub_id AND user_id=:user_id");
    $access_id->execute(array(
      ":pub_id" => $this->pub,
      ":user_id" => $user,
    ));
    $id = $access_id->fetch( PDO::FETCH_ASSOC )["id"] ?? false;

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Removed access for User #" . $user . " (" . User::name( $user ) . ") on pub #" . $this->pub . " (" . $this->values()["name"] . ")",
      "table" => "PUB_ACCESS",
      "function" => "UPDATE",
      "primary_key" => array("key1" => "pub_id", "value1" => $this->pub, "key2" => "user_id", "value2" => $user),
      "old" => ((empty(array_column($this->access(), "id")) ? "" : $this->access()[array_search( $id, array_column($this->access(), "id"))])),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . PUB_ACCESS . " WHERE pub_id=:pub_id AND user_id=:user_id");
    return $remove->execute(array(
      ":pub_id" => $this->pub,
      ":user_id" => $user,
    ));
  }

  /**
   * Returns true or false or if user is equal to null it returns list of users who have access to the pub
   * requires: $pub
   *
   * $user = User Id or null (Lists all user with access to this pub)
   * $offset: at what row you want to start
   * $steps: How many rows
   */
  public function access( $user = null, $offset = 0, $steps = 20 ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($user) ) {
      // Get all users that have access to this pub
      $users = $conn->prepare("SELECT * FROM " . PUB_ACCESS . " WHERE user_id=:user_id LIMIT " . $steps . " OFFSET " . $offset);
      $users->execute(array(
        ":user_id" => $user,
      ));

      return $users->fetchAll( PDO::FETCH_ASSOC );
    }else {
      // Check if user has access to this pub
      $check = $conn->prepare("SELECT * FROM " . PUB_ACCESS . " WHERE user_id=:user_id AND pub_id=:pub_id");
      $check->execute(array(
        ":user_id" => $user,
        ":pub_id" => $this->pub,
      ));
      $rights = $check->fetch( PDO::FETCH_ASSOC );

      // Check if user exists
      return array_intersect_key(($rights === false ? array() : $rights), array_flip(array("w", "r")));
    }
  }

  /**
  * Function to get all accessable pubs
  *
  * $user: User ID for whom you want to know the accessable pubs
  */
  public function accessable( $user ) {
    //Get database connection
    $conn = Access::connect();

    // SQL Request
    $accessable = $conn->prepare("SELECT pub_id FROM " . PUB_ACCESS . " WHERE user_id=:user_id AND( w=1 OR r=1)");
    $accessable->execute(array(
      ":user_id" => $user,
    ));

    // Modifie result
    $result = array_map(function($v) { return $v[0]; }, $accessable->fetchAll( PDO::FETCH_NUM ));

    return $result;
  }

  /**
   * Lists values of a product
   * requires: $product_id
   */
  public function product() {
    //Get database connection
    $conn = Access::connect();

    // Generate sql
    $product = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE id=:id");
    $product->execute(array(
      ":id" => $this->product_id,
    ));

    return $product->fetch( PDO::FETCH_ASSOC );
  }

  /**
   * Returns a list of all products for this pub
   * requires: $pub
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   * $include_globals: Set to true if you want to display the global products aswell
   */
  public function products( $offset = 0, $steps = 20, $search_value = null, $include_globals = false ) {
    //Get database connection
    $conn = Access::connect();

    // Prepare global products
    $global_products = ($include_globals ? " OR pub_id IS NULL" : "");

    if( is_null($search_value) || empty($search_value) ) {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE pub_id=:pub_id " . $global_products . " ORDER BY pub_id DESC, name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute(array(
        "pub_id" => $this->pub,
      ));
    }else {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE (pub_id=:pub_id " . $global_products . ") AND (name LIKE :name OR price=:price) ORDER BY pub_id DESC, name ASC LIMIT " . $steps . " OFFSET " . $offset );

      $products->execute(array(
        "pub_id" => $this->pub,
        ":name" => "%" . $search_value . "%",
        ":price" => (floatval($search_value) == 0 ? $search_value : (floatval($search_value) * 100) ),
      ));
    }

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns array with all section-names
   * requires: $pub or no pub for global sections
   */
  public function sections() {
    //Get database connection
    $conn = Access::connect();

    if( isset($this->pub) ) {
      // Get all sections by pub (including globals)
      $sections = $conn->prepare("SELECT DISTINCT section FROM " . PUB_PRODUCTS . " WHERE section IS NOT NULL AND (pub_id=:pub_id OR pub_id IS NULL) ORDER BY section ASC");
      $sections->execute(array(
        ":pub_id" => $this->pub,
      ));

      return $sections->fetchAll( PDO::FETCH_ASSOC );
    }else {
      // Get all global sections
      $sections = $conn->prepare("SELECT DISTINCT section FROM " . PUB_PRODUCTS . " WHERE section IS NOT NULL AND pub_id IS NULL ORDER BY section ASC");
      $sections->execute();

      return $sections->fetchAll( PDO::FETCH_ASSOC );
    }
  }

  /**
   * Returns all products by section
   * If you set $pub the global products are included aswell
   *
   * requires: $pub or no pub for global sections
   */
  public function products_by_section( $section ) {
    //Get database connection
    $conn = Access::connect();

    // Check if we need all products without a section
    if(empty($section) && $section !== 0) {
      // Request section
      if( isset($this->pub) ) {
        // Get all products of pub and section (inclubing globals)
        $sections = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE section IS NULL AND (pub_id=:pub_id OR pub_id IS NULL) ORDER BY name ASC, price ASC");
        $sections->execute(array(
          ":pub_id" => $this->pub,
        ));
      }else {
        // Get all global products and section
        $sections = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE section IS NULL AND pub_id IS NULL ORDER BY name ASC, price ASC");
        $sections->execute();
      }
    }else {
      // Request section
      if( isset($this->pub) ) {
        // Get all products of pub and section (inclubing globals)
        $sections = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE section=:section AND (pub_id=:pub_id OR pub_id IS NULL) ORDER BY name ASC, price ASC");
        $sections->execute(array(
          ":section" => $section,
          ":pub_id" => $this->pub,
        ));
      }else {
        // Get all global products and section
        $sections = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE section=:section AND pub_id IS NULL ORDER BY name ASC, price ASC");
        $sections->execute(array(
          ":section" => $section,
        ));
      }
    }

    // Return array
    return $sections->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns list of all global products
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function global_products( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value) ) {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE pub_id IS NULL ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute();
    }else {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . PUB_PRODUCTS . " WHERE pub_id IS NULL AND (name LIKE :name OR price=:price) ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute(array(
        ":name" => "%" . $search_value . "%",
        ":price" => (floatval($search_value) == 0 ? $search_value : (floatval($search_value) * 100) ),
      ));
    }

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Gets current visibility of product
   * requires: $pub, $product_id
   */
  public function product_visibility() {
    //Get database connection
    $conn = Access::connect();

    // get visibility
    $visibility = $conn->prepare("SELECT visible FROM " . PUB_PRODUCTS_META . " WHERE pub_id=:pub_id AND product_id=:product_id");
    $visibility->execute(array(
      ":pub_id" => $this->pub,
      ":product_id" => $this->product_id
    ));

    return boolval( ($visibility->fetch( PDO::FETCH_NUM )[0] ?? 1) );
  }

  /**
   * Gets current availability of product
   * requiest: $pub, $product_id
   */
  public function product_availability() {
    //Get database connection
    $conn = Access::connect();

    // get visibility
    $visibility = $conn->prepare("SELECT availability FROM " . PUB_PRODUCTS_META . " WHERE pub_id=:pub_id AND product_id=:product_id");
    $visibility->execute(array(
      ":pub_id" => $this->pub,
      ":product_id" => $this->product_id
    ));

    return ($visibility->fetch( PDO::FETCH_NUM )[0] ?? 0);
  }

  /**
   * Toggles visibility of an product
   * requires: $pub, $product_id
   */
  public function toggleVisibility() {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check if product is visible or no
    $check = $conn->prepare("SELECT visible FROM " . PUB_PRODUCTS_META . " WHERE pub_id=:pub_id AND product_id=:product_id");
    $check->execute(array(
      ":pub_id" => $this->pub,
      ":product_id" => $this->product_id
    ));
    $check = $check->fetch( PDO::FETCH_NUM );

    // Do action
    // If no value is set the product is visible by default
    if( $check != false && (count( $check ) > 0) && (($check[0] ?? null) != 1)) {
      // expose product
      $expose = $conn->prepare("INSERT INTO " . PUB_PRODUCTS_META . " (pub_id, product_id, visible) VALUES (:pub_id, :product_id, 1) ON DUPLICATE KEY UPDATE visible=1");

      //Create modification
      $change = array(
        "user" => $current_user,
        "message" => "Exposed product #" . $this->product_id . " (" . $this->product()["name"] . ")",
        "table" => "PUB_PRODUCTS_META",
        "function" => "UPDATE",
        "primary_key" => array("key1" => "pub_id", "value1" => $this->pub, "key2" => "product_id", "value2" => $this->product_id),
        "old" => array("visible" => 0),
        "new" => array("visible" => 1),
      );

      User::modifie( $change );

      return $expose->execute(array(
        ":pub_id" => $this->pub,
        ":product_id" => $this->product_id,
      ));
    }else {
      // hide product
      $hide = $conn->prepare("INSERT INTO " . PUB_PRODUCTS_META . " (pub_id, product_id, visible) VALUES (:pub_id, :product_id, 0) ON DUPLICATE KEY UPDATE visible=0");

      //Create modification
      $change = array(
        "user" => $current_user,
        "message" => "Hid product #" . $this->product_id . " (" . $this->product()["name"] . ")",
        "table" => "PUB_PRODUCTS_META",
        "function" => "UPDATE",
        "primary_key" => array("key1" => "pub_id", "value1" => $this->pub, "key2" => "product_id", "value2" => $this->product_id),
        "old" => array("visible" => 1),
        "new" => array("visible" => 0),
      );

      User::modifie( $change );

      return $hide->execute(array(
        ":pub_id" => $this->pub,
        ":product_id" => $this->product_id,
      ));
    }
  }

  /**
   * Updates availability
   * requires: $pub, $product_id (Set $pub to null if you want to use global products)
   *
   * $availability = 0: available
   *                 1: little available
   *                 2: sold
   */
  public function update_availability( $availability ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => "Changed availability of product #" . $this->product_id . " (" . $this->product()["name"] . ")",
      "table" => "PUB_PRODUCTS_META",
      "function" => "UPDATE",
      "primary_key" => array("key1" => "pub_id", "value1" => $this->pub, "key2" => "product_id", "value2" => $this->product_id),
      "old" => array("availability" => $this->product_availability()),
      "new" => array("availability" => $availability),
    );

    User::modifie( $change );

    // Update availability
    $update = $conn->prepare("INSERT INTO " . PUB_PRODUCTS_META . " (pub_id, product_id, availability) VALUES (:pub_id, :product_id, :availability1) ON DUPLICATE KEY UPDATE availability=:availability2");
    return $update->execute(array(
      ":pub_id" => $this->pub,
      ":product_id" => $this->product_id,
      ":availability1" => $availability,
      ":availability2" => $availability,
    ));
  }

  /**
   * Returns all values from access, products and all general values of this pub
   * requires: $pub
   */
  public function values() {
    //Get database connection
    $conn = Access::connect();

    // Get all values from pub
    $pub = $conn->prepare("SELECT * FROM " . PUB . " WHERE pub_id=:pub_id");
    $pub->execute(array(
      ":pub_id" => $this->pub,
    ));
    return $pub->fetch( PDO::FETCH_ASSOC );
  }
}
 ?>
