<?php
// Get read or write access for page
$read = User::r_access_allowed( $page, $current_user );
$write = User::w_access_allowed( $page, $current_user );
$disabled = ($write === true ? "" : "disabled");

// Get values
$checkout = new Checkout();

// Get page
if( isset($_GET["add"]) ) {
  if( $_GET["add"] == "checkout" ) {

  }elseif( $_GET["add"] == "product" ) {

  }else {
    Action::fs_info('Die Unterseite existiert nicht.', "Zurück", $url_page );
    return;
  }
}elseif( isset($_GET["view"]) ) {
  // Set id
  $checkout->cashier = $_GET["view"];

  // Check if user has access to this checkout
  if( $checkout->access( $current_user ) ) {
    // Update/remove/add
    if(! empty( $_POST )) {

    }

    // Start HTML
    $html = '<div class="checkbox">';
      $html = '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*">';
        // Payrexx
        $html .= '<div class="box">';
          $html .= '<h1>Payrexx</h1>';
          $html .= 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.';

          // Payrexx instance
          $html .= '<label class="txt-input">';
            $html .= '<input type="text" name="payment_payrexx_instance" value="' . $checkout->values["checkout"]["payment_payrexx_instance"] . '" ' . $disabled . '/>';
            $html .= '<span class="placeholder">Payrexx Instance</span>';
          $html .= '</label>';

          // Payrexx secret
          $html .= '<label class="txt-input">';
            $html .= '<input type="text" name="payment_payrexx_secret" value="' . $checkout->values["checkout"]["payment_payrexx_secret"] . '" ' . $disabled . '/>';
            $html .= '<span class="placeholder">Payrexx Secret</span>';
          $html .= '</label>';
        $html .= '</div>';

        // Produkte
        $html .= '<div class="box">';
          $html .= '<h1>Produkte</h1>';
        $html .= '</div>';
      $html .= '</form>';
    $html .= '</div>';
  }else {
    Action::fs_info('Zugriff für die Kasse <strong>#' . $checkout->cashier . "</strong> verweigert.", "Zurück", $url_page);
  }
} else {
  // Start HTML
  $html = '<div class="checkbox">';
    $html = '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*">';
      // Kassen
      $html .= '<div class="box">';
        $html .= '<h1>Kassen</h1>';
      $html .= '</div>';

      // Grundprodukte
      $html .= '<div class="box">';
        $html .= '<h1>Grundprodukte</h1>';
      $html .= '</div>';
    $html .= '</form>';
  $html .= '</div>';
}


echo $html;


 ?>
