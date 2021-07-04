<?php
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys
unset($action["pub"]);
unset($action["s"]); //Remove search value

// Start pub
$pub = new Pub();

// Get current pub
$accessable_pubs = $pub->accessable( $current_user );
$pub->pub = $_GET["pub"] ?? $accessable_pubs[0];

// Start transaction
$transaction = new Transaction();
$transaction->pub = $pub->pub;

// Get Access
$write_page = User::w_access_allowed( $page, $current_user );
$write_pub = boolval( $pub->access( $current_user )["w"] ?? 0 );
$write_access = ($write_page === true && $write_pub === true) ?  true : false;

$read_page = User::r_access_allowed( $page, $current_user );
$read_pub = boolval( $write_pub === true ? true : $pub->access( $current_user )["r"] ?? 0 );
$read_access = ($read_page === true && $read_pub === true) ?  true : false;

// Get disabled
$disabled = ($write_access === true ? "" : "disabled");

// Message if user has no access to this pub
if( $write_access === false && $read_access === false ) {
  Action::fs_info("Du hast keinen Zugriff auf die Wirtschaft (#" . $pub->pub . ") <strong>"  . $pub->values()['name'] ."</strong>", "Zurück", $url_page);
  return;
}

echo '<div class="pub">';
  // List accessable pubs
  if( count($accessable_pubs) > 1 ) {
    // Multiple access
    echo '<div class="header" onclick="this.children[1].classList.toggle(\'visible\')">';
      echo '<span class="current multiple">' . $pub->values()["name"] . '</span>';
      echo '<div class="pubs">';
        // List all accessable pubs
        foreach( $accessable_pubs as $pub_id ) {
          if( $pub->pub != $pub_id) {
            // Start new pub for name
            $name_pub = new Pub();
            $name_pub->pub = $pub_id;

            echo '<a href="' . $url_page . '&pub=' . $pub_id . '">' . $name_pub->values()["name"] . '</a>';
          }
        }
      echo '</div>';
    echo '</div>';
  } else {
    echo '<div class="header">';
      echo '<span class="current">' . $pub->values()["name"] . '</span>';
    echo '</div>';
  }

  switch(key($action)) {
    case "add":

    var_dump($_POST);
      if(! empty($_POST)) {
        if( $write_access ) {
          // Add
          if( $transaction->add( array($_POST), $pub->pub ) ) {
            Action::success("Die Transaktion <strong> (#" . $transaction->paymentID . ")</strong> wurde <strong>erfolgreich</strong> erstellt.");
          }else {
            Action::fail("Die Transaktion <strong>(#" . $transaction->paymentID . ")</strong> konnte <strong>nicht</strong> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      // Start html
      echo '<form action="' . $url_page . '&pub=' . $pub->pub . '&add" method="post">';
        // Payment state
        echo '<div class="select" onclick="toggleOptions(this)">';
          echo '<input type="text" class="selectValue" name="payment_state" ' . $disabled . ' required>';
          echo '<span class="headline">Zahlungsart wählen</span>';

          echo '<div class="options">';
            echo '<span data-value="1" onclick="selectElement(this)">Barzahlung</span>';
            echo '<span data-value="2" onclick="selectElement(this)">Zahlung erwartet</span>';
          echo '</div>';
        echo '</div>';

        //Price
        echo '<label class="txt-input">';
          echo '<input type="text" name="price" ' . $disabled . ' required/>';
          echo '<span class="placeholder">Preis</span>';
          echo '<span class="unit">' . $pub->values()["currency"] . '</span>';
        echo '</label>';

        //Email
        echo '<label class="txt-input">';
          echo '<input type="text" name="email" ' . $disabled . '/>';
          echo '<span class="placeholder">Email</span>';
        echo '</label>';

        echo '<button>Hinzufügen</button>';

      echo '</form>';

    break;
    case "view":
      // Set ID
      $transaction->paymentID = $_GET["view"];

      // Check payment
      $transaction->paymentCheck();

      echo '<div class="right-sub-menu">';
        // pickUp
        echo '<div class="right-menu-container">';
          if( $transaction->globalValues()["pick_up"] == 0) {
            echo '<a class="right-menu-item" onclick="togglePickUp(' . $transaction->paymentID . ', this)"><img src="' . $url . 'medias/icons/pickUp.svg" alt="PickUp" title="Transaktion abholen?"/></a>';
          }else {
            echo '<a class="right-menu-item" onclick="togglePickUp(' . $transaction->paymentID . ', this)"><img src="' . $url . 'medias/icons/pickedUp.svg" alt="PickUp" title="Transaktion abholen?"/></a>';
          }
        echo '</div>';

        // Payment
        if( $transaction->globalValues()["payment_state"] == 2) {
          echo '<div class="right-menu-container" onclick="confirmPayment(' . $transaction->paymentID . ', this)">';
            echo '<a class="right-menu-item"><img src="' . $url . 'medias/icons/payment_confirm.svg" alt="state" title="Zahlungseingang bestätigen"/></a>';
          echo '</div>';
        }

        // Refund
        if( isset($transaction->globalValues()["payrexx_transaction"]) &&! empty($transaction->globalValues()["payrexx_transaction"])) {
          echo '<div class="right-menu-container">';
            echo '<a class="right-menu-item"><img src="' . $url . 'medias/icons/payment-refund.svg" alt="Trash" title="Betrag zurückerstatten"/></a>';
            echo '<div class="right-sub-menu-container">';
              echo '<div class="right-sub-menu-item no-hover refund-payment">';
                echo '<div class="container">';
                  echo '<input type="text" min="0" max="' . (($transaction->totalPrice() - $transaction->globalValues()["refund"]) / 100) . '"/>';
                  echo '<span class="currency">' . $transaction->globalValues()["currency"] . '</span>';
                  echo '<button onclick="refundPayment(' . $transaction->paymentID . ', this.parentNode.children[0].value)">Erstatten</button>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
          echo '</div>';
        }

        // Remove
        if(  $transaction->globalValues()["payment_state"] == 1 || array_search( $transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"], array(27, 15) ) != false ) {
          echo '<div class="right-menu-container">';
            echo '<a href="' . $url_page . '&remove_product=" class="right-menu-item"><img src="' . $url . 'medias/icons/trash.svg" alt="Mail" title="Produkt entfernen"/></a>';
          echo '</div>';
        }
      echo '</div>';

      // Display top return button
      echo '<div class="top-nav border-none">';
        echo '<a href="Javascript:history.back()" title="Zur vorherigen Seite zurück"><img src="' . $url . 'medias/icons/history-back.svg"></a>';
      echo '</div>';

      // Start view
      echo '<div class="view">';

        // Get details
        echo '<div class="details">';
          echo '<span class="headline">Transaktion #' . $transaction->paymentID . '</span>';
          echo '<div class="details-list">';

            // Email
            echo '<div class="detail-item email">';
              echo '<span class="type">E-Mail:</span>';
              echo '<span class="value">' . $transaction->globalValues()["email"] . '</span>';
            echo '</div>';

            // Payment ID
            echo '<div class="detail-item paymentID">';
              echo '<span class="type">Zahlungs-ID:</span>';
              echo '<span class="value">' . $transaction->paymentID . '</span>';
            echo '</div>';

            // Price
            echo '<div class="detail-item amount">';
              echo '<span class="type">Betrag:</span>';
              echo '<span class="value">' . number_format(($transaction->totalPrice() / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
            echo '</div>';

            // Price
            echo '<div class="detail-item new_amount">';
              echo '<span class="type">Effektiv:</span>';
              echo '<span class="value">' . number_format((($transaction->totalPrice() - $transaction->globalValues()["refund"]) / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
            echo '</div>';

            // Refund
            echo '<div class="detail-item refund">';
              echo '<span class="type">Rückerstattet:</span>';
              echo '<span class="value">' . number_format(($transaction->globalValues()["refund"] / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
            echo '</div>';

            // Fees
            echo '<div class="detail-item fees">';
              echo '<span class="type">Gebühren:</span>';
              echo '<span class="value">' . number_format(($transaction->totalFees() / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
            echo '</div>';

            // Payment state
            echo '<div class="detail-item state">';
              echo '<span class="type">Status:</span>';
              echo '<span class="value">';
                if( $transaction->globalValues()["payment_state"]  == 2 && $transaction->globalValues()["pick_up"] == 1 ) { // Payment expected and picked up
                  echo "Zahlung erwartet, Abgeholt";
                }elseif ( $transaction->globalValues()["payment_state"]  == 2 ) { // Payment expected
                  echo "Zahlung erwartet";
                }elseif( $transaction->globalValues()["pick_up"] == 0 ) { // not picked up
                  echo "Nicht abgeholt";
                }else {
                  echo "Abgeholt";
                }
              echo '</span>';
            echo '</div>';

            // Payment option
            echo '<div class="detail-item state">';
              echo '<span class="type">Zahlungstyp:</span>';
              echo '<span class="value">';
                if(  $transaction->globalValues()["payment_state"] != 1 && array_search( $transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"], array(27, 15) ) === false ) {
                  echo "Onlinezahlung";
                }else {
                  echo "Barzahlung";
                }
              echo '</span>';
            echo '</div>';

            // Payment time
            echo '<div class="detail-item paymentTime">';
              echo '<span class="type">Zahlungszeit:</span>';
              echo '<span class="value">' . date("d.m.Y H:i:s", strtotime($transaction->globalValues()["payment_time"])) . '</span>';
            echo '</div>';

          echo '</div>';
        echo '</div>';

        // Products
        echo '<div class="products">';
          // Headline
          echo '<div class="headline">Produkte</div>';

          foreach( $transaction->values() as $product ) {
            // Get product
            $productName = new Product();
            $productName->product_id = $product["product_id"];

            echo '<div class="row">';
              echo '<span class="quantity">' . ($product["quantity"] ?? 1) . 'x</span>';
              echo '<span class="name">' . ($product["product_id"] == 0 ? "Trinkgeld" : ($productName->values()["name"] ?? "Name unbekannt")) . '</span>';
              echo '<span class="price">' . number_format(($product["price"] / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
            echo '</div>';
          }

          // Total
          echo '<div class="total">';
            echo '<span class="text">Total:</span>';
            echo '<span class="value">' . number_format(($transaction->totalPrice() / 100), 2) . ' ' . $transaction->globalValues()["currency"] . '</span>';
          echo '</div>';
        echo '</div>';

      echo '</div>';
    break;
    case "remove":
      // Get name of pub
      $transaction->paymentID = $_GET["remove"] ?? null;

      // Generate message
      $info = "Möchtest du die Transaktion <strong>" . $transaction->globalvalues()["email"] . " (#" . $_GET["remove"] . ")</strong>  wirklich löschen?";

      // Display message
      Action::confirm($info, $_GET["remove"], "&pub=" . $_GET["pub"]);
    break;
    default:
      if(isset($_POST["confirm"])) {
        if( $write_access ) {
          // Get values
          $transaction->paymentID = $_POST["confirm"];
          $email = $transaction->globalValues()["email"];

          // Remove
          if( $transaction->remove() ) {
            Action::success("Die Transaktion <strong>" . $email . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
          }else {
            Action::fail("Die Transaktion <strong>" . $email . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      echo '<form action="' . $url . '" method="get" class="search">';
        echo '<input type="hidden" name="id" value="' . $mainPage . '" />';
        echo '<input type="hidden" name="sub" value="' . $page . '" />';
        echo '<input type="hidden" name="pub" value="' . ($_GET["pub"] ?? null) . '" />';
        echo '<input type="text" name="s" value ="' . (isset( $_GET["s"] ) ? $_GET["s"] : "") . '" placeholder="Email, Zahlungs-ID, Zahlungszeit">';
        echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
      echo '</form>';


      // Define colors
      $availability = array(
        0 => array(
          "color" => "var(--transaction-payment-and-pickUp)",
          "title" => "Ohne Zahlung abgeholt",
        ),
        1 => array(
          "color" => "var(--transaction-payment-expected)",
          "title" => "Zahlung erwartet",
        ),
        2 => array(
          "color" => "var(--transaction-no-pickUp)",
          "title" => "Abholung erwartet",
        ),
      );

      // Legend
      echo '<div class="legend">';
        foreach( $availability as $element ) {
          echo '<div class="legend-element">';
            echo '<div class="legend-button" style="background-color: ' . $element["color"] . '"></div>';
            echo $element["title"];
          echo '</div>';
        }
      echo '</div>';


      echo  '<table class="rows">';
        //Headline
        $headline_names = array('Email', 'Preis', 'Datum', 'Aktion');

        //Start headline
        echo  '<tr>'; //Start row
        foreach( $headline_names as $name ){
          echo  '<th>' . $name . '</th>';
        }
        echo  '</tr>'; //Close row

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        // List general products
        foreach( $transaction->all( $offset, $steps, ($_GET["s"] ?? null)) as $values ) {
          // Set paymentID
          $transaction->paymentID = $values["paymentID"];

          // Check payment
          $transaction->paymentCheck();

          // Generate class
          if( $transaction->globalValues()["payment_state"]  == 2 && $transaction->globalValues()["pick_up"] == 1 ) { // Payment expected and picked up
            $class = "transaction payment-and-pickUp";
            $title = "Zahlung erwartet. Produkte bereits abgeholt.";
          }elseif ( $transaction->globalValues()["payment_state"]  == 2 ) { // Payment expected
            $class = "transaction payment-expected";
            $title = "Zahlung erwartet.";
          }elseif( $transaction->globalValues()["pick_up"] == 0 ) { // not picked up
            $class = "transaction no-pickUp";
            $title = "Abholung erwartet";
          }else {
            $class = "transaction";
            $title = "Abgeholt";
          }

          echo  '<tr class="' . $class . '" title="' . $title . '">';
            echo  '<td>' . $transaction->globalValues()["email"] . '</td>';
            echo  '<td>' . number_format(($transaction->totalPrice() / 100), 2) . ' ' . ($transaction->globalValues()["currency"] ?? DEFAULT_CURRENCY) . '</td>';
            echo  '<td>' . date("d.m.Y H:i:s", strtotime($transaction->globalValues()["payment_time"])) . '</td>';
            echo  '<td>';
              echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view=' . urlencode( $transaction->paymentID ) . '" title="Transaktion anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
              if(  $transaction->globalValues()["payment_state"] == 1 || array_search( $transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"], array(27, 15) ) != false ) {
                echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&remove=' . urlencode( $transaction->paymentID ) . '" title="Transaktion entfernen"><img src="' . $url . '/medias/icons/trash.svg" />';
              }
            echo  '</td>';
          echo  '</tr>';
        }

        // Menu requred
        echo  '<tr class="nav">';

          if( (count($transaction->all( ($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                        '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        <a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                        '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                        '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      </td>';
          }elseif (count($transaction->all( ($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) { // More pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                        '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }

        echo  '</tr>';

      echo  '</table>';

      if( $write_access === true ) {
        echo  '<a class="add" href="' . $url_page . '&pub=' . $pub->pub . '&add">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }
    break;
  }
