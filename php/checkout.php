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
 */
class Checkout {
  //Variables
  public $cashier;

  /**
   *
   */
  public function list( $offset = 0, $steps = 20) {
    // Get payrexx variables

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
    $instanceName = 'kantifest-solothurn';

    // $secret is the payrexx secret for the communication between the applications
    // if you think someone got your secret, just regenerate it in the payrexx administration
    $secret = 'bdRhxmdANQySBh9jiW7r0qgUapZ7aq';

    $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

    $transaction = new \Payrexx\Models\Request\Transaction();
    $transaction->setOffset($offset);
    $transaction->setLimit($steps + $offset);

    try {
        $response = $payrexx->getAll($transaction);
    } catch (\Payrexx\PayrexxException $e) {
        print $e->getMessage();
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
   *
   */
  public function access( $user = null ) {
    // Require global variables
    global $conn;

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

  public function products() {
    // Require global variables
    global $conn;

    // Get all products
    $products = $conn->prepare("SELECT * FROM " . CHECKOUT_PRODUCTS . " WHERE checkout_id=:checkout_id AND checkout_id=NULL" );
    $products->execute(array(
      "checkout_id" => $this->cashier,
    ));

    return $products->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   *
   */
  public function values() {
    // Require global variables
    global $conn;

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
    );
  }
}
 ?>
