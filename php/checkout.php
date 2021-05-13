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
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Checkout->transactions ( $offset [int], $steps [int] ) [$cashier]
 *
 * Checkout->add ( $table [const], $values [array] ) [$cashier]
 *
 * Checkout->update_checkout ( $values [array] ) [$cashier]
 *
 * Checkout->update_product( $product_id [int], $values [$array] ) [$cashier]
 *
 * Checkout->remove_checkout () [$cashier]
 *
 * Checkout->remove_product ( $product_id [int] ) [$cashier]
 *
 * Checkout->remove_access ( $user [int] ) [$cashier]
 *
 * Checkout->access ( $user [int or null] ) [$cashier]
 *
 * Checkout->products () [$cashier]
 *
 * Checkout->global_products ()
 *
 * Checkout->values () [$cashier]
 *
 *
 */
class Checkout {
  //Variables
  public $cashier;

  //constants
  const DEFAULT_TABLE = CHECKOUT;
  const PRODUCTS_TABLE = CHECKOUT_PRODUCTS;
  const ACCESS_TALBE = CHECKOUT_ACCESS;

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
  public function transactions( $offset = 0, $steps = 20) {
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
    $instanceName = $this->values()["checkout"]["payment_payrexx_instance"];

    // $secret is the payrexx secret for the communication between the applications
    // if you think someone got your secret, just regenerate it in the payrexx administration
    $secret = $this->values()["checkout"]["payment_payrexx_secret"];

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
    //Get database connection
    $conn = Access::connect();

    //Generate query
    $add_query = "INSERT INTO " . $table . " ";
    $add_query .= "(" . implode(", ", array_flip($values)) . ") ";
    $add_query .= "VALUES ('" . implode("', '", $values) . "')";

    // execute query
    $add = $conn->prepare($add_query);
    return $add->execute();
  }

  /**
   * Updates a checkout
   * requires: $cashier
   *
   * $values: Array with new values
   *          array(
   *            name,
   *            payment_payrexx_instance,
   *            payment_payrexx_secret
   *         )
   */
  public function update_checkout( $values) {
    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_values = array("name", "payment_payrexx_instance", "payment_payrexx_secret");
    $checked_values = array_intersect_key($values, array_flip($valid_values));

    // Generate values and keys
    $update_query = "UPDATE " . CHECKOUT . " SET ";
    foreach( $checked_values as $key => $value ) {
      $update_query .= "'" . $key . "' = '" . $value . "', ";
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE checkout_id=:checkout_id";

    // Update query
    $update = $conn->prepare( $update_query );
    return $update->execute(array(
      ":checkout_id" => $this->cashier,
    ));
  }

  /**
   * Updates a product
   * requires: $cashier
   *
   * $product_id: Id of product (stored in database)
   * $values: Array with new values
   *          array(
   *            checkout_id,
   *            name,
   *            price,
   *            currency
   *         )
   */
  public function update_product( $product_id, $values ) {
    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_values = array("name", "price", "currency");
    $checked_values = array_intersect_key($values, array_flip($valid_values));

    // Generate values and keys
    $update_query = "UPDATE " . CHECKOUT_PRODUCTS . " SET ";
    foreach( $checked_values as $key => $value ) {
      $update_query .= "'" . $key . "' = '" . $value . "', ";
    }
    $update_query = substr( $update_query, 0, -2 ) . " WHERE checkout_id=:checkout_id AND id=:id";

    // Update query
    $update = $conn->prepare( $update_query );
    return $update->execute(array(
      ":checkout_id" => $this->cashier,
      ":id" => $product_id,
    ));
  }

  /**
   * Removes a checkout
   * requires: $cashier
   */
  public function remove_checkout() {
    //Get database connection
    $conn = Access::connect();

    // Remove
    $remove = $conn->prepare("DELETE FROM " . CHECKOUT . " WHERE checkout_id=:checkout_id");
    return $remove->execute(array(
      ":checkout_id" => $this->cashier,
    ));
  }

  /**
   * Removes a product
   * requires: $cashier
   *
   * $product_id: Id of product (stored in database)
   */
  public function remove_product( $product_id ) {
    //Get database connection
    $conn = Access::connect();

    // Remove
    $remove = $conn->prepare("DELETE FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=:checkout_id AND id=:id");
    return $remove->execute(array(
      ":checkout_id" => $this->cashier,
      ":id" => $product_id,
    ));
  }

  /**
   * Removes an access
   * requires: $cashier
   *
   * $user: User id (stored in database)
   */
  public function remove_access( $user ) {
    //Get database connection
    $conn = Access::connect();

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
  public function access( $user = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($user) ) {
      // Get all users that have access to this checkout
      $users = $conn->prepare("SELECT * FROM " . CHECKOUT_ACCESS . " WHERE user_id=:user_id");
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

      // Check if user exists
      return ($check->rowCount() > 0 ? true :  false);
    }
  }

  /**
   * Returns a list of all products for this checkout
   * requires: $cashier
   */
  public function products() {
    //Get database connection
    $conn = Access::connect();

    // Get all products
    $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=:checkout_id" );
    $products->execute(array(
      "checkout_id" => $this->cashier,
    ));

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns list of all global products
   */
  public function global_products() {
    //Get database connection
    $conn = Access::connect();

    // Get all products
    $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=NULL" );
    $products->execute();

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns all values from access, products, global_procutds and all general values of this checkout
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

    // Combine all values
    return array(
      "checkout" => $checkout->fetch( PDO::FETCH_ASSOC ),
      "access" => $this->access(),
      "products" => $this->products(),
      "global_products" => $this->global_products(),
    );
  }
}
 ?>
