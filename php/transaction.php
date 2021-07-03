<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2021
 * @Purpose: File to manage transaction actions
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $paymentID: registerd paymentID
 * $pub: Pub ID
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Transaction->newPaymentID () {private}
 *
 * Transaction->all ( $offset [int], $steps [int], $search_value [info_string] ) {optional $pub}
 *
 * Transaction->add ( $values [array], $pub_id [int], $gateway [boolen])
 *
 * Transaction->update ( $values [array] ) [$paymentID]
 *
 * Transaction->remove () [$paymentID]
 *
 * Transaction->paymentCheck () [$paymentID]
 *
 * Transaction->getGateway () [$paymentID]
 *
 * Transaction->refund( $amount [INT] ) [$paymentID]
 *
 * Transaction->values () [$paymentID]
 *
 * Transaction->globalValues () [$paymentID]
 *
 */
class Transaction {
  public $paymentID;
  public $pub;

  /**
   * Function that gets the next highter paymentID
   */
  private function newPaymentID() {
    //Get database connection
    $conn = Access::connect();

    // List max
    $nextPaymentID = $conn->prepare("SELECT (MAX(paymentID) + 1) FROM " . PUB_TRANSACTIONS);
    $nextPaymentID->execute();

    // Set new paymentID
    $this->paymentID = $nextPaymentID->fetch( PDO::FETCH_NUM )[0] ?? 1;
  }

  /**
   * Returns array of all transactions
   * use $pub if you want all transactions by pub
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function all( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( isset($this->pub) &&! empty($this->pub)) {
      if( is_null($search_value) || empty($search_value) ) {
        //No search
        $tickets = $conn->prepare("SELECT * FROM " . PUB_TRANSACTIONS . " WHERE pub_id=:pub_id ORDER BY payment_time DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
        $tickets->execute(array(
          ":pub_id" => $this->pub,
        ));
      }else {
        // Select all
        $tickets = $conn->prepare("SELECT * FROM " . PUB_TRANSACTIONS . " WHERE pub_id=:pub ID AND (paymentID=:paymentID OR product_id=:product_id OR email=:email OR payment_time=:payment_time) ORDER BY payment_time DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
        $tickets->execute(array(
          ":pub_id" => $this->pub,
          ":paymentID" => $search_value,
          ":product_id" => $search_value,
          ":email" => $search_value,
          ":payment_time" => date("Y-m-d H:i:s", strtotime($search_value)),
        ));
      }
    }else {
      if( is_null($search_value) || empty($search_value) ) {
        //No search
        $tickets = $conn->prepare("SELECT * FROM " . PUB_TRANSACTIONS . " ORDER BY payment_time DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
        $tickets->execute();
      }else {
        // Select all
        $tickets = $conn->prepare("SELECT * FROM " . PUB_TRANSACTIONS . " WHERE paymentID=:paymentID OR pub_id=:pub_id OR product_id=:product_id OR email=:email OR payment_time=:payment_time ORDER BY payment_time DESC LIMIT " . $steps . " OFFSET " . $offset);//Result of all selected users in range
        $tickets->execute(array(
          ":paymentID" => $search_value,
          ":pub_id" => $search_value,
          ":product_id" => $search_value,
          ":email" => $search_value,
          ":payment_time" => date("Y-m-d H:i:s", strtotime($search_value)),
        ));
      }
    }

    return $tickets->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Adds new transaction
   *
   * $values: array(
   *     productID [Required]
   *     price
   *     currency
   *     quantity
   * )
   * $pub_id: ID of pub
   * $gateway: Create gateway, true or false
   */
  public function add( $values, $pub_id, $gateway = true) {
    // Gloable
    global $url;

    //Get database connection
    $conn = Access::connect();

    // Generate new paymentID
    $this->newPaymentID();

    // Start pub
    $pub = new Pub;
    $pub->pub = $pub_id;

    // Calculate total amount
    if( is_array($values) && is_array($values[array_keys($values)[0]])) {
      // Price
      $price = 0;

      // Counter
      foreach( $values as $value) {
        // Set product
        $product = new Product();
        $product->product_id = $value["productID"];

        // Add price
        $price = $price + ( ($value["quantity"] ?? 1) * ($value["price"] ?? $product->values()["price"]) );
      }
    }else {
      return false;
    }

    // Check if gateway is required
    if($gateway == true) {
      // Generate new gatewayID
      spl_autoload_register(function($class) {
          $root = dirname(__DIR__) . "/php/Payrexx-SDK";
          $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
          if (file_exists($classFile)) {
              require_once $classFile;
          }
      });

      // $instanceName is a part of the url where you access your payrexx installation.
      // https://{$instanceName}.payrexx.com
      $instanceName = $pub->values()["payment_payrexx_instance"];

      // $secret is the payrexx secret for the communication between the applications
      // if you think someone got your secret, just regenerate it in the payrexx administration
      $secret = $pub->values()["payment_payrexx_secret"];

      $payrexx = new \Payrexx\Payrexx($instanceName, $secret);
      $gateway = new \Payrexx\Models\Request\Gateway();

      // amount multiplied by 100
      $gateway->setAmount( $price );

      // currency ISO code
      $gateway->setCurrency( $pub->values()["currency"] ?? DEFAULT_CURRENCY );

      //success and failed url in case that merchant redirects to payment site instead of using the modal view
      $gateway->setSuccessRedirectUrl( $url . "store/pubs/receipt/" . $this->paymentID);
      $gateway->setFailedRedirectUrl( $url . "store/pubs/receipt/" . $this->paymentID);

      // empty array = all available psps
      $gateway->setPsp([]);

      // optional: reference id of merchant (e. g. order number)
      $gateway->setReferenceId( $this->paymentID );

      try {
          $response = $payrexx->create($gateway);
          $gateway_id = $response->getId();
      } catch (\Payrexx\PayrexxException $e) {
          return false;
      }
    }

    // Check if array is correct
    if( is_array($values) && is_array($values[array_keys($values)[0]])) {
      // Add new values
      foreach( $values as $value) {
        // Add new value
        $product = new Product();
        $product->product_id = $value["productID"];

        // Create insert sql
        $add = $conn->prepare("INSERT INTO " . PUB_TRANSACTIONS . " (pub_id, paymentID, payrexx_gateway, product_id, price, currency, quantity, fee_absolute, fee_percent) VALUES
        (:pub_id, :paymentID, :payrexx_gateway, :product_id, :price, :currency, :quantity, :fee_absolute, :fee_percent)");
        if(! $add->execute(array(
          ":pub_id" => $pub->pub,
          ":paymentID" => $this->paymentID,
          ":payrexx_gateway" => $gateway_id ?? null,
          ":product_id" => $value["productID"],//edit
          ":price" => $value["price"] ?? $product->values()["price"] ?? 0,
          ":currency" => $value["currency"] ?? $pub->values()["currency"] ?? DEFAULT_CURRENCY,
          ":quantity" => $value["quantity"] ?? 1,
          ":fee_absolute" => $pub->values()["payment_fee_absolute"],
          ":fee_percent" => $pub->values()["payment_fee_percent"],
        ))) {
          return false;
        }
      }
    }

    // everything ok
    return true;
  }

  /**
   * Updates payment options of transaction
   * requires: $transactionID
   *
   * $values: array(
   *    payrexx_transaction
   *    payrexx_gateway
   *    payment_time
   * )
   */
  public function update( $values ) {
    //Get database connection
    $conn = Access::connect();

    // Check values
    $valid_keys = array("payrexx_transaction", "payrexx_gateway", "email", "payment_time");
    $checked_values = array_intersect_key($values, array_flip($valid_keys));

    // Create query
    $update = $conn->prepare("UPDATE " . PUB_TRANSACTIONS . " SET payrexx_transaction=:payrexx_transaction, payrexx_gateway=:payrexx_gateway, email=:email, payment_time=:payment_time WHERE paymentID = :paymentID");
    return $update->execute(array(
      ":payrexx_transaction" => $values["payrexx_transaction"] ?? $this->globalValues()["payrexx_transaction"],
      ":payrexx_gateway" => $values["payrexx_gateway"] ?? $this->globalValues()["payrexx_gateway"],
      ":email" => $values["email"] ?? $this->globalValues()["email"],
      ":payment_time" => $values["payment_time"] ?? $this->globalValues()["payment_time"],
      ":paymentID" => $this->paymentID,
    ));
  }

  /**
   * Removes an payment
   * requires: $paymentID
   */
  public function remove() {
    //Get database connection
    $conn = Access::connect();

    // Remove
    $remove = $conn->prepare("DELETE FROM " . PUB_TRANSACTIONS . " WHERE paymentID = :paymentID");
    return $remove->execute(array(
      ":paymentID" => $this->paymentID,
    ));
  }

  /**
   * Checks payment and returns true after check (updates required values if payment was successfull)
   * requires: $paymentID
   */
  public function paymentCheck() {
    // Check if transaction is set
    if(! empty( $this->globalValues()["payrexx_transaction"] ) ) {
      return true;
    }

    // Read gateway infos
    $gateway = $this->getGateway();

    if( is_object($gateway) ) {
      // Check transaction
      if(! empty($gateway->getInvoices()) ) {
        // Check if you can get transactionid
        $transactionID = $this->getGateway()->getInvoices()[0]["transactions"][0]["id"] ?? null;
        $payment_time = $this->getGateway()->getInvoices()[0]["transactions"][0]["time"] ?? null;
        $email = $this->getGateway()->getInvoices()[0]["transactions"][0]["contact"]["email"] ?? null;

        if(! is_null( $transactionID ) &&! is_null( $email )) {
          $this->update(array(
            "payrexx_transaction" => $transactionID,
            "email" => $email,
            "payment_time" => $payment_time,
          ));
        }
      }
    }

    // Transaction not found, check successfully over
    return true;
  }

  /**
   * Gets values of an gateway
   * requires: $paymentID
   */
  public function getGateway() {
    // Get gateway
    $gateway_id = $this->globalValues()["payrexx_gateway"];

    // Get pub
    $pub = new Pub();
    $pub->pub = $this->globalValues()["pub_id"];

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
    $instanceName = $pub->values()["payment_payrexx_instance"];

    // $secret is the payrexx secret for the communication between the applications
    // if you think someone got your secret, just regenerate it in the payrexx administration
    $secret = $pub->values()["payment_payrexx_secret"];

    $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

    $gateway = new \Payrexx\Models\Request\Gateway();
    $gateway->setId( $this->globalValues()["payrexx_gateway"] );

    try {
        $response = $payrexx->getOne($gateway);
        return $response;
    } catch (\Payrexx\PayrexxException $e) {
      return $e->getMessage();
    }
  }

  /**
   * Refunds a transaction by amount
   * requires: $paymentID
   *
   * Amount: Amount to be refunded (in cents)
   */
  public function refund( $amount ) {
    // Get gateway
    $transaction_id = $this->globalValues()["payrexx_transaction"];

    // Get pub
    $pub = new Pub();
    $pub->pub = $this->globalValues()["pub_id"];

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
    $instanceName = $pub->values()["payment_payrexx_instance"];

    // $secret is the payrexx secret for the communication between the applications
    // if you think someone got your secret, just regenerate it in the payrexx administration
    $secret = $pub->values()["payment_payrexx_secret"];

    $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

    $transaction = new \Payrexx\Models\Request\Transaction();
    $transaction->setId($transaction_id);

    // amount multiplied by 100
    $transaction->setAmount( $amount );

    try {
        return $payrexx->refund($transaction);
    } catch (\Payrexx\PayrexxException $e) {
        return $e->getMessage();
    }
  }

  /**
   * Returns all values of a paymentID
   * requires: $paymentID
   */
  public function values() {
    //Get database connection
    $conn = Access::connect();

    // Select all
    $values = $conn->prepare("SELECT * FROM " . PUB_TRANSACTIONS . " WHERE paymentID=:paymentID");
    $values->execute(array(":paymentID" => $this->paymentID));

    // return array
    return $values->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Returns all global and same values of a paymentID
   * requires: paymentID
   */
  public function globalValues() {
    //Get database connection
    $conn = Access::connect();

    // Select all
    $values = $conn->prepare("SELECT DISTINCT pub_id, payrexx_transaction, payrexx_gateway, currency, email, fee_absolute, fee_percent FROM " . PUB_TRANSACTIONS . " WHERE paymentID=:paymentID");
    $values->execute(array(":paymentID" => $this->paymentID));

    // return array
    return $values->fetch( PDO::FETCH_ASSOC );
  }
}
