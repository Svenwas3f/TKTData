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

    break;
    case "view":
      // Set ID
      $transaction->paymentID = $_GET["view"];

      // Check payment
      $transaction->paymentCheck();

      // Right menu
      $rightmenu = new HTML('right-menu');

      // PickUp
      if( $transaction->globalValues()["pick_up"] == 0 ) {
        $rightmenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/pickUp.svg" alt="PickUp" title="Transaktion abholen?"/>',
            'additional_item' => 'onclick="togglePickUp(' . $transaction->paymentID . ', this)"',
          ),
        );
      }else {
        $rightmenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/pickedUp.svg" alt="PickUp" title="Transaktion abholen?"/>',
            'additional_item' => 'onclick="togglePickUp(' . $transaction->paymentID . ', this)"',
          ),
        );
      }

      // Payment
      if( $transaction->globalValues()["payment_state"] == 2) {
        $rightmenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/payment_confirm.svg" alt="state" title="Zahlungseingang bestätigen"/>',
            'additional_item' => 'onclick="confirmPayment(' . $transaction->paymentID . ', this)"',
          ),
        );
      }

      // Refund
      if( isset($transaction->globalValues()["payrexx_transaction"]) &&! empty($transaction->globalValues()["payrexx_transaction"])) {
        $rightmenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/payment-refund.svg" alt="Trash" title="Betrag zurückerstatten"/>',
            'dropdown' => array(
              array(
                'context' => '<div class="container">
                                <input type="text" min="0" max="' . (($transaction->totalPrice() - $transaction->globalValues()["refund"]) / 100) . '"/>' .
                                '<span class="currency">' . $transaction->globalValues()["currency"] . '</span>' .
                                '<button onclick="refundPayment(' . $transaction->paymentID . ', this.parentNode.children[0].value)">Erstatten</button>' . '</div>',
                'classes' => 'no-hover refund-payment',
              ),
            ),
          ),
        );
      }

      // Remove
      if(  $transaction->globalValues()["payment_state"] == 1 || array_search( $transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"], array(27, 15) ) != false ) {
        $rightmenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/trash.svg" alt="Mail" title="Produkt entfernen"/>',
            'additional_item' => 'href="' . $url_page . '&remove_product="',
          ),
        );
      }

      // Display top return button
      $topNav = new HTML('top-nav', array('classes' => 'border-none'));
      $topNav->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
          'link' => 'Javascript:history.back()',
          'additional' => 'title="Zur vorherigen Seite zurück"',
        ),
      );

      // Show HTML
      $topNav->prompt();
      $rightmenu->prompt();

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

      // Start searchbar
      $searchbar = new HTML('searchbar', array(
        'action' => $url,
        'method' => 'get',
        'placeholder' => 'Email, Zahlungs-ID, Zahlungszeit',
        's' => ($_GET['s'] ?? ''),
      ));

      $searchbar->addElement( '<input type="hidden" name="id" value="' . $mainPage . '" />' );
      $searchbar->addElement( '<input type="hidden" name="sub" value="' . $page . '" />' );
      $searchbar->addElement( '<input type="hidden" name="pub" value="' . ($_GET["pub"] ?? null) . '" />' );

      // Define colors
      $availability = array(
        0 => array(
          "bcolor" => "var(--transaction-payment-and-pickUp)",
          "title" => "Ohne Zahlung abgeholt",
        ),
        1 => array(
          "bcolor" => "var(--transaction-payment-expected)",
          "title" => "Zahlung erwartet",
        ),
        2 => array(
          "bcolor" => "var(--transaction-no-pickUp)",
          "title" => "Abholung erwartet",
        ),
      );

      //Start Legend
      $legend = new HTML('legend');

      foreach( $availability as $values ) {
        $legend->addElement( array(
          'bcolor' => $values['bcolor'],
          'title' => $values['title']
        ) );
      }


      // Start table
      $table = new HTML('table');

      // Headline
      $table->addElement(
        array(
          'headline' => array(
            'items' => array(
              array(
                'context' => 'Email',
              ),
              array(
                'context' => 'Preis',
              ),
              array(
                'context' => 'Datum',
              ),
              array(
                'context' => 'Atkion',
              ),
            ),
          ),
        ),
      );

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

        // Create actions
        $action = '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view=' . urlencode( $transaction->paymentID ) . '"
                    title="Transaktion anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg"/></a>';
        if(  $transaction->globalValues()["payment_state"] == 1 ||
              array_search( $transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"], array(27, 15) ) != false ) {
          $action .=  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&remove=' . urlencode( $transaction->paymentID ) . '"
                        title="Transaktion entfernen"><img src="' . $url . '/medias/icons/trash.svg"/></a>';
        }

        $table->addElement(
          array(
            'row' => array(
              'additional' => 'class="' . $class . '" title="' . $title . '"',
              'items' => array(
                array(
                  'context' => $transaction->globalValues()["email"],
                ),
                array(
                  'context' => number_format(($transaction->totalPrice() / 100), 2) . ' ' .
                                ($transaction->globalValues()["currency"] ?? DEFAULT_CURRENCY),
                ),
                array(
                  'context' => date("d.m.Y H:i:s", strtotime($transaction->globalValues()["payment_time"])),
                ),
                array(
                  'context' => $action,
                ),
              ),
            ),
          ),
        );
      }

      // Footer
      $last = '<a href="' .
                $url_page .
                (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") .
                ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '"
                style="float: left;">Letze</a>';
      $next = '<a href="' .
                $url_page .
                (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") .
                ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
                style="float: right;">Weiter</a>';

      if( (count($transaction->all( ($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
        $table->addElement(
          array(
            'footer' => array(
              'context' => $last . $next,
            ),
          ),
        );
      }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
        $table->addElement(
          array(
            'footer' => array(
              'context' => $last,
            ),
          ),
        );
      }elseif (count($transaction->all( ($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) { // More pages accessable
        $table->addElement(
          array(
            'footer' => array(
              'context' => $next,
            ),
          ),
        );
      }

      // Show HTML
      $searchbar->prompt();
      $legend->prompt();
      $table->prompt();
    break;
  }
