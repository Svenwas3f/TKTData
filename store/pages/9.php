<?php
// Check if id is set
if( empty($_GET["id"]) ) {
  header("Location: " . $url . "store/" . $type);
}

// Start transaction
$transaction = new Transaction();
$transaction->paymentID = $_GET["id"];

// Check if transaction exitst
if(empty($transaction->values())) {
  header("Location: " . $url . "store/" . $type);
}

// Check if payment is already done
$transaction->paymentCheck();

if(! empty($transaction->globalValues()["payrexx_transaction"]) ) {
  header("Location: " . $url . "store/" . $type . "/receipt/" . $transaction->paymentID);
}

// Start pub
$pub = new Pub();
$pub->pub = $transaction->globalValues()["pub_id"];

// Get current language
$lang_code = $pub->values()["payment_store_language"];

// Start product
$product = new Product();
$product->pub = $pub->pub;

// Get background image
if( isset( $pub->values()["background_fileID"] ) &&! empty( $pub->values()["background_fileID"] ) ) {
  $mediaHub = new MediaHub();
  $mediaHub->fileID = $pub->values()["background_fileID"];

  $backgroundImgUrl = $mediaHub->getUrl( $pub->values()["background_fileID"] );
}else {
  $backgroundImgUrl = $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,3) . "/medias/store/background/*")[0], PATHINFO_BASENAME );
}
 ?>


<div class="pay-container">

  <div class="fullscreen-img" style="background-image: url('<?php echo $backgroundImgUrl; ?>')">
  </div>

  <?php
  // Get response
  $response = $transaction->getGateway();

  //Payment modal
  if( is_object( $response ) ) {
    echo '<a class="payrexx-modal-window" href="#" data-href="https://' . $pub->values()["payment_payrexx_instance"] . '.payrexx.com/?payment=' . $response->getHash() . '">' . Language::string( 130, null, "store", null, $lang_code ) . '</a>';
    echo '<script type="text/javascript">';
    echo 'jQuery(\'.payrexx-modal-window\').payrexxModal();';
    echo 'jQuery(\'.payrexx-modal-window\').click();';
    echo '</script>';
  }else {
    Action::fail(
      Language::string( 131, array(
        '%message%' => $response
      ), "store", null, $lang_code
    ));
  }
  ?>

</div>
