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
 * Checkout->list ( $offset [int], $steps [int] ) [$cashier]
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
  public function list( $offset = 0, $steps = 20) {
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
    $checkout = $conn->prepare("SELECT * FROM " . CHECKOUT . " WHERE id=:id");
    $checkout->execute(array(
      ":id" => $this->cashier,
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
