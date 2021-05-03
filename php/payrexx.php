<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: January 2021
 * @Purpose: File to manage payment actions
 *
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * getGateway( $ticketToken [ticketToken], $success_link [Link success], $fail_link [Link fail] )
 *
 * retrieveGateway( $ticketToken [ticketToken] )
 *
 * deleteGateway( $ticketToken [ticketToken] )
 *
 * retrieveTransaction ( $ticketToken [ticketToken], $limit [Limit of lookups], $offset [start point] )
 *
 * refundTransaction( $ticketToken [ticketToken] ) {
 *
 * checkPayment ( $ticketToken [ticketToken] )
 *
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

 /*
 *##############################################################################
 *
 * #####       #####   ##   ##  #####     #######   ##       ##   ##      ##
 * ##   ##    ##  ##    ## ##   ##   ##   ##          ##  ##        ##  ##
 * #####     ########    ###    #####     #######       ##            ##
 * ##       ##     ##    ##     ##  ##    ##          ##  ##       ##   ##
 * ##      ##      ##    ##     ##   ##   #######   ##      ##   ##       ##
 *
 *##############################################################################
 */

/**
 * Creates a gateway and returns important infos
 *
 * $ticketToken: Ticket token
 * $success_link: Redirect after successfull payment
 * $fail_link: Redirect after failed payment
 *
 * returns array(
 *  gateway_creation_state, [Returns true if gateway is created successfully]
 *  [ message, [This is only visible if gateway_creation_state is false. Errormessage] ]
 *  hash,
 *  link,
 *  status,
 *  referenceId, [Equals $ticketToken]
 *  id, [Gateway ID]
 * )
 */
function getGateway( $ticketToken, $success_link, $fail_link ) {
  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  //Check if there is already an existing gateway
  if(! empty( $ticket->values()["payrexx_gateway"] ) && $ticket->values()["payrexx_gateway"] != 0) {
    //Check if amount or currency changed
    $retrieveGateway =  retrieveGateway( $ticketToken );

    if($retrieveGateway["amount"] == $ticket->values()["amount"] && $retrieveGateway["currency"] == $group->values()["currency"] ) {
      //Return Gateway Infos
      return $retrieveGateway;
    }
  }

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
  $instanceName = $group->values()["payment_payrexx_instance"];

  // $secret is the payrexx secret for the communication between the applications
  // if you think someone got your secret, just regenerate it in the payrexx administration
  $secret = $group->values()["payment_payrexx_secret"];

  //New payrexx
  $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

  $gateway = new \Payrexx\Models\Request\Gateway();

  //New gateway required
  // amount multiplied by 100
  $gateway->setAmount( $ticket->values()["amount"] );

  // currency ISO code
  $gateway->setCurrency( $group->values()["currency"] );

  // VAT rate percentage (nullable)
  $gateway->setVatRate( $group->values()["vat"] );

  //success and failed url in case that merchant redirects to payment site instead of using the modal view
  $gateway->setSuccessRedirectUrl( $success_link );
  $gateway->setFailedRedirectUrl( $fail_link );

  //Set email
  $gateway->addField($type = 'email', $value = $ticket->values()["email"]);

  // optional: reference id of merchant (e. g. order number)
  $gateway->setReferenceId( $ticket->ticketToken );

  try {
      $response = $payrexx->create($gateway);

      //Update ticket
      $ticket->update(array(
        "payrexx_gateway" => $response->getId(),
      ));

      return array(
        "gateway_creation_state" => true,
        "hash" => $response->getHash(),
        "link" => $response->getLink(),
        "status" => $response->getStatus(),
        "referenceId" => $response->getReferenceId(),
        "amount" => $response->getAmount(),
        "currency" => $response->getCurrency(),
        "id" => $response->getId(),
      );
  } catch (\Payrexx\PayrexxException $e) {
      return array(
        "gateway_creation_state" => false,
        "message" => $e->getMessage(),
      );
  }
}

/**
 * Returns an gateway info
 *
 * $ticketToken: Ticket token
 *
 * returns array(
 *  gateway_creation_state, [Returns true if gateway is created successfully]
 *  [ message, [This is only visible if gateway_creation_state is false. Errormessage] ]
 *  hash,
 *  link,
 *  status,
 *  referenceId, [Equals $ticketToken]
 *  transaction [Transaction data]
 *  id, [Gateway ID]
 * )
 */
function retrieveGateway( $ticketToken ) {
  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

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
  $instanceName = $group->values()["payment_payrexx_instance"];

  // $secret is the payrexx secret for the communication between the applications
  // if you think someone got your secret, just regenerate it in the payrexx administration
  $secret = $group->values()["payment_payrexx_secret"];

  $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

  $gateway = new \Payrexx\Models\Request\Gateway();
  $gateway->setId( $ticket->values()["payrexx_gateway"] );

  try {
      $response = $payrexx->getOne($gateway);

      return array(
        "gateway_creation_state" => true,
        "hash" => $response->getHash(),
        "link" => $response->getLink(),
        "status" => $response->getStatus(),
        "referenceId" => $response->getReferenceId(),
        "transaction" => (isset($response->getInvoices()[0]["transactions"][0]) ? $response->getInvoices()[0]["transactions"][0] : null),
        "amount" => $response->getAmount(),
        "currency" => $response->getCurrency(),
        "id" => $response->getId(),
      );
  } catch (\Payrexx\PayrexxException $e) {
    return array(
      "gateway_creation_state" => false,
      "message" => $e->getMessage(),
    );
  }
}

/**
 * Delets a gateway
 *
 * $ticketToken: Ticket token
 *
 * returns true or an array(
 *  gateway_creation_state, [Values = false]
 *  message, [Errormessage]
 * )
 */
function deleteGateway( $ticketToken ) {
  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  if($ticket->values()["payrexx_gateway"] = 0 || empty($ticket->values()["payrexx_gateway"])) {
    return true;
  }

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
  $instanceName = $group->values()["payment_payrexx_instance"];

  // $secret is the payrexx secret for the communication between the applications
  // if you think someone got your secret, just regenerate it in the payrexx administration
  $secret = $group->values()["payment_payrexx_secret"];

  $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

  $gateway = new \Payrexx\Models\Request\Gateway();
  $gateway->setId( $ticket->values()["payrexx_gateway"] );

  try {
      $response = $payrexx->delete($gateway);

      $ticket->update(array(
        "payrexx_gateway" => 0,
      ));

      return true;
  } catch (\Payrexx\PayrexxException $e) {
    return array(
      "gateway_creation_state" => false,
      "message" => $e->getMessage(),
    );
  }
}

/**
 * Selects transaction by TicketToken or reference id
 *
 * $ticketToken: ticketToken
 * $limit: Limit of entries per lookup
 * $offset: Start point (INT)
 *
 * NOTICE: This is an recursive function please choose the limit and offset rational
 *
 * returns an array {
 *  transaction_retrieve_status, [Returns true if successfully found]
 *  [ message, [This is only visible if transaction_retrieve_status is false. Errormessage]  ]
 *  id, [Transactionid]
 *  status, [payment state]
 *  time, [payment time]
 *  psp, [Payment Service Provider name]
 *  pspId, [PSP id, ]
 *  referenceId, [Equals to $ticketToken]
 * );
 *
 */
function retrieveTransaction( $ticketToken ) {
  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  //check transaction
  if($ticket->values()["payrexx_transaction"] == "") {
    //Check gateway
    $gateway = retrieveGateway( $ticketToken );

    if(! isset($gateway["transaction"]) || is_null($gateway["transaction"])) {
      //No transaction found
      return array(
        "transaction_retrieve_status" => false,
        "message" => "No transaction found",
      );
    }else {
      //Transaction found
      $ticket->update(array("payrexx_transaction" => $gateway["transaction"]["id"]));
    }
  }

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
  $instanceName = $group->values()["payment_payrexx_instance"];

  // $secret is the payrexx secret for the communication between the applications
  // if you think someone got your secret, just regenerate it in the payrexx administration
  $secret = $group->values()["payment_payrexx_secret"];

  //Start payrexx
  $payrexx = new \Payrexx\Payrexx($instanceName, $secret);
  $transaction = new \Payrexx\Models\Request\Transaction();
  $transaction->setId( $ticket->values()["payrexx_transaction"] );

  try {
      $transaction = $payrexx->getOne($transaction);
      return array(
            "transaction_retrieve_status" => true,
            "id" => $transaction->getId(),
            "status" => $transaction->getStatus(),
            "time" => $transaction->getTime(),
            "psp" => $transaction->getPsp(),
            "pspId" => $transaction->getPspId(),
            "referenceId" => $transaction->getReferenceId(),
          );
  } catch (\Payrexx\PayrexxException $e) {
    return array(
      "transaction_retrieve_status" => false,
      "message" => $e->getMessage(),
    );
  }
}

/**
 * Refunds a transaction
 *
 * returns an array(
 *   transaction_refund_state,[Returns true if successfully found]
 *   [ message, [This is only visible if transaction_retrieve_status is false. Errormessage]  ]
 *   referenceId, [Equals to ticketToken]
 * )
 */
function refundTransaction( $ticketToken ) {
  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  //Load payrexx infos
  spl_autoload_register(function($class) {
      $root = dirname(__DIR__, 2) . "/php/Payrexx-SDK";
      $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
      if (file_exists($classFile)) {
          require_once $classFile;
      }
  });

  // $instanceName is a part of the url where you access your payrexx installation.
  // https://{$instanceName}.payrexx.com
  $instanceName = $group->values()["payment_payrexx_instance"];

  // $secret is the payrexx secret for the communication between the applications
  // if you think someone got your secret, just regenerate it in the payrexx administration
  $secret = $group->values()["payment_payrexx_secret"];

  //Get transaction ID
  $transactionID = retrieveTransaction( $ticketToken )["id"];

  $payrexx = new \Payrexx\Payrexx($instanceName, $secret);

  $transaction = new \Payrexx\Models\Request\Transaction();
  $transaction->setId( $transactionID );

  // amount multiplied by 100
  $transaction->setAmount( $ticket->values()["amount"] );

  try {
    $response = $payrexx->refund($transaction);

    $ticket->update(array(
      "amount" => -00
    ));

    return array(
      "transaction_refund_state" => true,
      "referenceId" => $ticket->ticketToken,
    );
  } catch (\Payrexx\PayrexxException $e) {
    return array(
      "transaction_refund_state" => false,
      "message" => $e->getMessage(),
    );
  }
}

/**
 * Updates payment in DB if payment arrived and returns if true if payment was made
 *
 * $ticketToken: ticketToken
 */
function checkPayment( $ticketToken ) {
  //Global
  global $current_user;
  $current_user_loged = $current_user;
  $current_user = "Store";

  //Get infos of ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $ticketToken;

  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  //Check state
  if( $ticket->values()["payment"] != 2 ) {
    $current_user = $current_user_loged;
    return true;
  }

  //Check if amoutn is 0
  if( $ticket->values()["amount"] <= 0) {
    if( $ticket->update(array(
      "payment_time" => date("Y-m-d H:i:s"),
      "payment" => 0
    ))) {
      $current_user = $current_user_loged;
      return true;
    }else {
      $current_user = $current_user_loged;
      return false;
    }
    $current_user = $current_user_loged;
    return true;
  }

  //Get transaction
  $transaction = retrieveTransaction( $ticketToken );

  //Check payment request
  if( $transaction["transaction_retrieve_status"] == false ) {
    $current_user = $current_user_loged;
    return false;
  }

  if( $transaction["status"] == "confirmed" ) {
    if( $ticket->update(array(
      "payment_time" => $transaction["time"],
      "payment" => 0
    ))) {
      $current_user = $current_user_loged;
      return true;
    }else {
      $current_user = $current_user_loged;
      return false;
    }
  }else {
    $current_user = $current_user_loged;
    return false;
  }
}
 ?>
