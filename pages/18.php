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
    Action::fs_info('Die Unterseite existiert nicht . ', "Zurück", $url_page );
    return;
  }
}elseif( isset($_GET["view_checkout"]) ) {
  // Set id
  $checkout->cashier = $_GET["view_checkout"];

  // Update/remove/add
  if(! empty( $_POST )) {

  }

  // Start HTML
  $html = '<div class="checkbox">';
    $html = '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*">';
      // Name


      // Payrexx
      $html .= '<div class="box">';
        $html .= '<h1>Payrexx</h1>';
        $html .= 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren . ';

        // Payrexx instance
        $html .= '<label class="txt-input">';
          $html .= '<input type="text" name="payment_payrexx_instance" value="' . $checkout->values()["checkout"]["payment_payrexx_instance"] . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Payrexx Instance</span>';
        $html .= '</label>';

        // Payrexx secret
        $html .= '<label class="txt-input">';
          $html .= '<input type="text" name="payment_payrexx_secret" value="' . $checkout->values()["checkout"]["payment_payrexx_secret"] . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Payrexx Secret</span>';
        $html .= '</label>';
      $html .= '</div>';

      // Rechte
      $html .= '<div class="box">';
        $html .= '<h1>Rechte</h1>';
      $html .= '</div>';
    $html .= '</form>';
  $html .= '</div>';
} elseif ( isset($_GET["view_product"] )) {

} else {
  // Update/remove/add
  if(! empty( $_POST )) {

  }

  // Display top menu
  $html = '<div class="checkout">';
    $html .= '<div class="top-nav">';
      $html .= '<a href="' . $url_page . '&list=checkout" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "checkout" ? "selected" : "") : "selected" ) . '" title="Kassen auflisten">KASSEN</a>';
      $html .= '<a href="' . $url_page . '&list=products" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "") . '" title="Produkte auflisten">PRODUKTE</a>';
    $html .= '</div>';
  $html .= '</div>';


  switch( $_GET["list"] ?? "" ) {
    case "products":
      // Search form
      $html .= '<form action="' . $url_page . '" method="post" class="search">';
        $html .= '<input type="text" name="s_products" value ="' . (isset(  $_POST["s_products"] ) ? $_POST["s_products"] : "") . '" placeholder="Produktname, Preis">';
        $html .= '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
      $html .= '</form>';

      // Table
      $html .= '<table class="rows">';
        //Headline
        $headline_names = array('Name', 'Preis', 'Aktion');

        //Start headline
        $html .= '<tr>'; //Start row
        foreach( $headline_names as $name ){
          $html .= '<th>' . $name . '</th>';
        }
        $html .= '</tr>'; //Close row

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        foreach( Checkout::global_products( $offset, $steps ) as $products ) {
          $html .= '<tr>';
            $html .= '<td>' . $products["name"] . '</td>';
            $html .= '<td>' . ($products["price"] / 100) . ' ' . $products["currency"] . '</td>';
            $html .= '<td>';
              if(User::w_access_allowed($page, $current_user)) {
                  $html .= '<a href="' . $url_page . '&view_products=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
                  $html .= '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Prdukt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
              }else {
                $html .= '<a href="' . $url_page . '&view_products=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
              }
            $html .= '</td>';
          $html .= '</tr>';
        }

        // Menu requred
        $html .= '<tr class="nav">';

          if( (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      </td>';
          }elseif (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) { // More pages accessable
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }

        $html .= '</tr>';

      $html .= '</table>';

      if(User::w_access_allowed($page, $current_user)) {
        $html .= '<a class="add" href="' . $url_page . '&add">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }
    break;
    case "checkout":
    default:
      // Search form
      $html .= '<form action="' . $url_page . '" method="post" class="search">';
        $html .= '<input type="text" name="s_checkout" value ="' . (isset(  $_POST["s_checkout"] ) ? $_POST["s_checkout"] : "") . '" placeholder="Name der Kasse">';
        $html .= '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
      $html .= '</form>';

      // Table
      $html .= '<table class="rows">';
        //Headline
        $headline_names = array('Name', 'Aktion');

        //Start headline
        $html .= '<tr>'; //Start row
        foreach( $headline_names as $name ){
          $html .= '<th>' . $name . '</th>';
        }
        $html .= '</tr>'; //Close row

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        // Get content
        foreach( Checkout::all( $offset, $steps ) as $checkout ) {
          $html .= '<tr>';
            $html .= '<td>' . $checkout["name"] . '</td>';
            $html .= '<td>';
              if(User::w_access_allowed($page, $current_user)) {
                  $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
                  $html .= '<a href="' . $url_page . '&remove_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kasse entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
              }else {
                $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
              }
            $html .= '</td>';
          $html .= '</tr>';
        }

        // Menu requred
        $html .= '<tr class="nav">';

          if( (count(Checkout::all( ($offset + $steps), 1 )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      </td>';
          }elseif (count(Checkout::all( ($offset + $steps), 1 )) > 0) { // More pages accessable
            $html .= '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }

        $html .= '</tr>';

      $html .= '</table>';

      if(User::w_access_allowed($page, $current_user)) {
        $html .= '<a class="add" href="' . $url_page . '&add">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }
    break;
  }
  $html .= '</div>';
}


echo $html;


 ?>
