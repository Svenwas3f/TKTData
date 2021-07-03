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
      //   echo '<div class="error">Die Mail konnte nicht gesendet werden. Laden Sie die Seite neu um es noch einmal zu versuchen.</div>';
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
          echo '<span>Zahlung erwartet</span>';
        }elseif( $payment_state == "confirmed") {
          echo '<img src="' . $url . 'medias/store/icons/success.svg" />';
          echo '<span>Zahlung erfolgreich</span>';
        }else {
          echo '<img src="' . $url . 'medias/store/icons/error.svg" />';
          echo '<span>Zahlung fehlgeschlagen</span>';
        }
         ?>
      </div>

      <div class="message">
        <?php
        // Show message
        if( $payment_method == 27 || $payment_method == 15 ) {
          echo 'Hallo,<br />Bitte bezahle bar an der Kasse. Gib als Zahlungs-ID <strong>#' . $transaction->paymentID . '</strong> an.';
        }elseif( $payment_state == "confirmed") {
          echo 'Hallo,<br />Du kannst mit diesem Beleg deinen Einkauf an der Kasse abholen gehen. Gib als Zahlungs-ID <strong>#' . $transaction->paymentID . '</strong> an. Der Beleg wurde dir auch per Mail (an ' . $transaction->globalValues()["email"] . ') zugestellt.';
        }else {
          echo 'Hallo,<br />Ihre Zahlung ist fehlgeschlagen. Versuchen Sie es erneut oder melden Sie sich beim Personal.';
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
              echo '<span class="name">Trinkgeld</span>';
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
          echo '<span class="name">Total:</span>';
          echo '<span class="price">' . number_format(($total / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
        echo '</div>';
         ?>

        <div class="footer">
          Transaction proudly provided by <span>TKTDATA</span>
        </div>
      </div>
    </div>
  </div>
</article>
