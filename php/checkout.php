<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: Mai 2021
 * @Purpose: File to manage cashier ations
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $cashier: checkout Id for cashier
 * $product_id: Id of requested product
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Checkout->all ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * Checkout->transactions ( $offset [int], $steps [int] ) [$cashier]
 *
 * Checkout->add ( $table [const], $values [array] ) [$cashier]
 *
 * Checkout->update_checkout ( $values [array] ) [$cashier]
 *
 * Checkout->update_product( $values [$array] ) [$product_id]
 *
 * Checkout->remove_checkout () [$cashier]
 *
 * Checkout->remove_product ( $product_id [int] ) [$product_id]
 *
 * Checkout->remove_access ( $user [int] ) [$cashier]
 *
 * Checkout->access ( $user [int or null], $offset [int], $steps [int] ) [$cashier]
 *
 * Checkout->product () [$product_id]
 *
 * Checkout->products ( $offset [int], $steps [int], $search_value [info_string] ) [$cashier]
 *
 * Checkout->sections () [$cashier]
 *
 * Checkout->global_products ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * Checkout->values () [$cashier]
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
class Checkout {
  //Variables
  public $cashier;
  public $product_id;

  //constants
  const DEFAULT_TABLE = CHECKOUT;
  const PRODUCTS_TABLE = CHECKOUT_PRODUCTS;
  const ACCESS_TALBE = CHECKOUT_ACCESS;

  /**
   * Returns array of all checkouts
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
      $checkout = $conn->prepare("SELECT * FROM " . CHECKOUT . " ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset);
      $checkout->execute();
    }else {
      // Select all
      $checkout = $conn->prepare("SELECT * FROM " . CHECKOUT . " WHERE checkout_id=:checkout_id OR name=:name  ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset);
      $checkout->execute(array(
        ":checkout_id" => $search_value,
        ":name" => $search_value
      ));
    }

    return $checkout->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns a list of all transactions (in steps) that belong to the checkout
   * requires: $cashier
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
      if( $response->getPurpose() == "cashier-" . $this->cashier ) {
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
   *                            payment_payrexx_secret
   *                          )
   *          PRODUCTS_TABLE: array(
   *                            checkout_id,
   *                            name,
   *                            price,
   *                            currency
   *                          )
   *          ACCESS_TABLE:   array(
   *                            checkout_id,
   *                            user_id
   *                          )
   */
  public function add( $table = SELF::DEFAULT_TABLE, $values ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("checkout_id", "name", "logo_fileID", "background_fileID", "payment_payrexx_instance", "payment_payrexx_secret", "id", "user_id", "w", "r", "section", "price", "product_fileID", "availability", "currency");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    //Generate query
    $add_query = "INSERT INTO " . $table . " ";
    $add_query .= "(" . implode(", ", array_keys($checked_values)) . ") ";
    $add_query .= "VALUES ('" . implode("', '", $checked_values) . "')";

    //Create modification
    $change = array(
      "user" => $current_user,
      "message" => "Added " . ($table == SELF::DEFAULT_TABLE ? "checkout" : ($table == SELF::PRODUCTS_TABLE ? "product" : "access")),
      "table" => ($table == SELF::DEFAULT_TABLE ? "CHECKOUT" : ($table == SELF::PRODUCTS_TABLE ? "CHECKOUT_PRODUCTS" : "CHECKOUT_ACCESS")),
      "function" => "INSERT INTO",
      "primary_key" => array("key" => "checkout_id", "value" => ""),
      "old" => "",
      "new" => $checked_values
    );

    User::modifie( $change );

    // execute query
    $add = $conn->prepare($add_query);
    $result = $add->execute();

    if( $result === true ) {
      if($table == SELF::DEFAULT_TABLE) {
        $this->cashier = $conn->lastInsertId();
      } elseif($table == SELF::PRODUCTS_TABLE) {
        $this->product_id = $conn->lastInsertId();
      }
      return true;
    }else {
      return false;
    }
  }

  /**
   * Updates a checkout
   * requires: $cashier
   *
   * $values: Array with new values
   *          array(
   *            name,
   *            logo_fileID,
   *            background_fileID,
   *            payment_payrexx_instance,
   *            payment_payrexx_secret
   *         )
   */
  public function update_checkout( $values) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("name", "logo_fileID", "background_fileID", "payment_payrexx_instance", "payment_payrexx_secret");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    // Generate values and keys
    $update_query = "UPDATE " . CHECKOUT . " SET ";
    foreach( $checked_values as $key => $value ) {
      $update_query .= $key . " = '" . $value . "', ";
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE checkout_id=:checkout_id";

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Updated checkout #" . $this->cashier,
      "table" => "CHECKOUT",
      "function" => "UPDATE",
      "primary_key" => array("key" => "checkout_id", "value" => $this->cashier),
      "old" => array_intersect_key($this->values(), array_flip($valid_keys)),
      "new" => $valid_keys
    );

    User::modifie($change);

    // Update query
    $update = $conn->prepare( $update_query );
    return $update->execute(array(
      ":checkout_id" => $this->cashier,
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
   *            availability
   *         )
   */
  public function update_product( $values ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("name", "section", "price", "currency", "product_fileID", "availability");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    // Generate values and keys
    $update_query = "UPDATE " . CHECKOUT_PRODUCTS . " SET ";
    foreach( $checked_values as $key => $value ) {
      $update_query .= $key . " = '" . $value . "', ";
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE id=:product_id";

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Updated product #" . $this->product_id,
      "table" => "CHECKOUT_PRODUCTS",
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
   * Removes a checkout
   * requires: $cashier
   */
  public function remove_checkout() {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Removed Checkout #" . $this->cashier,
      "table" => "CHECKOUT",
      "function" => "UPDATE",
      "primary_key" => array("key" => "checkout_id", "value" => $this->cashier),
      "old" => array_intersect_key($this->values(), array_flip(array("name", "payment_payrexx_instance", "payment_payrexx_secret"))),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . CHECKOUT . " WHERE checkout_id=:checkout_id");
    return $remove->execute(array(
      ":checkout_id" => $this->cashier,
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
      "message" => "Removed product #" . $this->product_id,
      "table" => "CHECKOUT_PRODUCTS",
      "function" => "UPDATE",
      "primary_key" => array("key" => "id", "value" => $this->product_id),
      "old" => array_intersect_key($this->product(), array_flip(array("checkout_id", "name", "price", "currency"))),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . CHECKOUT_PRODUCTS . " WHERE id=:id");
    return $remove->execute(array(
      ":id" => $this->product_id,
    ));
  }

  /**
   * Removes an access
   * requires: $cashier
   *
   * $user: User id (stored in database)
   */
  public function remove_access( $user ) {
    // Get global
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Get ID of access
    $access_id = $conn->prepare("SELECT * FROM " . CHECKOUT_ACCESS . " WHERE checkout_id=:checkout_id AND user_id=:user_id");
    $access_id->execute(array(
      ":checkout_id" => $this->cashier,
      ":user_id" => $user,
    ));
    $id = $access_id->fetch( PDO::FETCH_ASSOC )["id"] ?? false;

    //Modifie
    $change = array(
      "user" => $current_user,
      "message" => "Removed access for User #" . $user . "(" . User::name( $user ) . ")",
      "table" => "CHECKOUT_ACCESS",
      "function" => "UPDATE",
      "primary_key" => array("key1" => "checkout_id", "value1" => $this->cashier, "key2" => "user_id", "value2" => $user),
      "old" => ((empty(array_column($this->access(), "id")) ? "" : $this->access()[array_search( $id, array_column($this->access(), "id"))])),
      "new" => array("")
    );

    User::modifie($change);

    // Remove
    $remove = $conn->prepare("DELETE FROM " . CHECKOUT_ACCESS . " WHERE checkout_id=:checkout_id AND user_id=:user_id");
    return $remove->execute(array(
      ":checkout_id" => $this->cashier,
      ":user_id" => $user,
    ));
  }

  /**
   * Returns true or false or if user is equal to null it returns list of users who have access to the checkout
   * requires: $cashier
   *
   * $user = User Id or null (Lists all user with access to this checkout)
   */
  public function access( $user = null, $offset = 0, $steps = 20 ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($user) ) {
      // Get all users that have access to this checkout
      $users = $conn->prepare("SELECT * FROM " . CHECKOUT_ACCESS . " WHERE user_id=:user_id LIMIT " . $steps . " OFFSET " . $offset);
      $users->execute(array(
        ":user_id" => $user,
      ));

      return $users->fetchAll( PDO::FETCH_ASSOC );
    }else {
      // Check if user has access to this checkout
      $check = $conn->prepare("SELECT * FROM " . CHECKOUT_ACCESS . " WHERE user_id=:user_id AND checkout_id=:checkout_id");
      $check->execute(array(
        ":user_id" => $user,
        ":checkout_id" => $this->cashier,
      ));
      $rights = $check->fetch( PDO::FETCH_ASSOC );

      // Check if user exists
      return array_intersect_key(($rights === false ? array() : $rights), array_flip(array("w", "r")));
    }
  }

  /**
   * Lists values of a product
   * requires: $product_id
   */
  public function product() {
    //Get database connection
    $conn = Access::connect();

    // Generate sql
    $product = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE id=:id");
    $product->execute(array(
      ":id" => $this->product_id,
    ));

    return $product->fetch( PDO::FETCH_ASSOC );
  }

  /**
   * Returns a list of all products for this checkout
   * requires: $cashier
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function products( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value) ) {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=:checkout_id ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute(array(
        "checkout_id" => $this->cashier,
      ));
    }else {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=:checkout_id AND (name=:name OR price=:price) ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute(array(
        "checkout_id" => $this->cashier,
        ":name" => $search_value,
        ":price" => (intval($search_value) * 100),
      ));
    }

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns array with all section-names
   * requires: $cashier or no cashier for global products
   */
  public function sections() {
    //Get database connection
    $conn = Access::connect();

    if( isset($this->cashier) ) {
      // Get all sections by cashier
      $sections = $conn->prepare("SELECT DISTINCT section FROM " . CHECKOUT_PRODUCTS . " WHERE section IS NOT NULL AND checkout_id=:checkout_id");
      $sections->execute(array(
        ":checkout_id" => $this->cashier,
      ));

      return $sections->fetchAll( PDO::FETCH_ASSOC );
    }else {
      // Get all global sections
      $sections = $conn->prepare("SELECT DISTINCT section FROM " . CHECKOUT_PRODUCTS . " WHERE section IS NOT NULL AND checkout_id IS NULL");
      $sections->execute();

      return $sections->fetchAll( PDO::FETCH_ASSOC );
    }
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
      $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id IS NULL ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute();
    }else {
      // Get all products
      $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id IS NULL AND (name=:name OR price=:price) ORDER BY name ASC LIMIT " . $steps . " OFFSET " . $offset );
      $products->execute(array(
        ":name" => $search_value,
        ":price" => (intval($search_value) * 100),
      ));
    }

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns all values from access, products and all general values of this checkout
   * requires: $cashier
   */
  public function values() {
    //Get database connection
    $conn = Access::connect();

    // Get all values from checkout
    $checkout = $conn->prepare("SELECT * FROM " . CHECKOUT . " WHERE checkout_id=:checkout_id");
    $checkout->execute(array(
      ":checkout_id" => $this->cashier,
    ));
    return $checkout->fetch( PDO::FETCH_ASSOC );
  }
}
 ?>
