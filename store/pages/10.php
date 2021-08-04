<?php
// Check if id is set
if( empty($_GET["id"]) ) {
  header("Location: " . $url . "store/" . $type);
}

// Start transaction
$transaction = new Transaction();
$transaction->paymentID = $_GET["id"];

// Check if payment was successfull
$transaction->paymentCheck();

if( empty($transaction->globalValues()["payrexx_transaction"]) ) {
  header("Location: " . $url . "store/" . $type . "/pay/" . $transaction->paymentID);
}

// Send receipt

?>
<article style="background: url(<?php echo $url;?>medias/store/icons/background.svg)">
  <div class="receipt-container">

    <div class="response-container">
      <?php
      // if( $error === false ) {
      //   echo '<div class="error">' . Language::string(140, null, "store") . '</div>';
      // }
       ?>
      <div class="headline">
        <?php
        // Get payment options
        $payment = $transaction->getGateway()->getInvoices()[0]["transactions"][0];
        $payment_state = $payment["status"];
        $payment_method = $payment["pspId"];

        if( $payment_method == 27 || $payment_method == 15 ) {
          echo '<img src="' . $url . 'medias/store/icons/waiting.svg" />';
          echo '<span>' . Language::string( 141, null, "store", null, null, $transaction->globalValues()['pub_id'] ) . '</span>';
        }elseif( $payment_state == "confirmed") {
          echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
          echo '<span>' . Language::string( 142, null, "store", null, null, $transaction->globalValues()['pub_id'] ) . '</span>';
        }else {
          echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
          echo '<span>' . Language::string( 143, null, "store", null, null, $transaction->globalValues()['pub_id'] ) . '</span>';
        }
         ?>
      </div>

      <div class="message">
        <?php
        // Show message
        if( $payment_method == 27 || $payment_method == 15 ) {
          echo Language::string( 144, array(
                  '%id%' => $transaction->paymentID,
                ), "store", null, null, $transaction->globalValues()['pub_id'] );
        }elseif( $payment_state == "confirmed") {
          echo Language::string( 145, array(
                  '%id%' => $transaction->paymentID,
                  '%mail%' => $transaction->globalValues()["email"],
                ), "store", null, null, $transaction->globalValues()['pub_id'] );
        }else {
          echo Language::string( 146, null, "store", null, null, $transaction->globalValues()['pub_id'] );
        }
         ?>
      </div>

      <div class="details">
        <?php
        // Calculate total
        $total = 0;

        // List all products
        foreach( $transaction->values() as $values ) {
          // Get product details
          if($values["product_id"] == 0) {
            echo '<div class="item">';
              echo '<span class="quantity">1x</span>';
              echo '<span class="name">' . Language::string( 147, null, "store", null, null, $transaction->globalValues()['pub_id'] ) . '</span>';
              echo '<span class="price">' . number_format(($values["price"] / 100), 2) . ' ' . $values["currency"] . '</span>';
            echo '</div>';

            // Add total
            $total = $total + ( $values["price"] * $values["quantity"] );
          }else {
            $product = new Product();
            $product->product_id = $values["product_id"];

            echo '<div class="item">';
              echo '<span class="quantity">' . $values["quantity"] . 'x</span>';
              echo '<span class="name">' . $product->values()["name"] . '</span>';
              echo '<span class="price">' . number_format(($values["price"] / 100), 2) . ' ' . $values["currency"] . '</span>';
            echo '</div>';

            // Add total
            $total = $total + ( $values["price"] * $values["quantity"] );
          }
        }

        // List total
        echo '<div class="item total">';
          echo '<span class="name">' . Language::string( 148, null, "store", null, null, $transaction->globalValues()['pub_id'] ) . '</span>';
          echo '<span class="price">' . number_format(($total / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
        echo '</div>';
         ?>

        <div class="footer">
          <?php echo Language::string( 149, null, "store", null, null, $transaction->globalValues()['pub_id'] ); ?>
        </div>
      </div>
    </div>
  </div>
</article>
