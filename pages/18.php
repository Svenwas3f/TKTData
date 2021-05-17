<?php
function display_checkouts ( $search_value = null ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;

  // Search form
  $html = '<div class="checkout">';
    $html .= '<form action="' . $url_page . '&list=checkout" method="post" class="search">';
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
      $html .= '<a class="add" href="' . $url_page . '&add=checkout">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }

    // Display content
    echo $html;
}

function single_checkout ( $checkout_id ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;

  // Set id
  $checkout = new Checkout();
  $checkout->cashier = $checkout_id;

  // Get disabled
  $write = User::w_access_allowed( $page, $current_user );
  $disabled = ($write === true ? "" : "disabled");

  // Start HTML
  $html =  '<div class="checkout">';
    $html .=  '<div class="top-nav">';
      $html .=  '<a href="' . $url_page . '&view_checkout=' . $checkout->cashier . '&type=general" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "general" ? "selected" : "") : "selected" ) . '" title="Kasse verwalten">Allgemein</a>';
      $html .=  '<a href="' . $url_page . '&view_checkout=' . $checkout->cashier . '&type=access" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "access" ? "selected" : "") : "") . '" title="Rechte verwalten">Rechte</a>';
    $html .=  '</div>';

    switch( $_GET["type"] ?? "") {
      case "access":
      break;
      case "general":
      default:
        // Form
        $html .=  '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*">';
          //Kassenname
          $html .=  '<div class="box">';
            $html .=  '<p>Kassenname</p>';
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="name" value="' . $checkout->values()["name"] . '"/>';
              $html .=  '<span class="placeholder">Kassenname</span>';
            $html .=  '</label>';
          $html .=  '</div>';

          // Payrexx
          $html .=  '<div class="box">';
            $html .=  '<p>Payrexx</p>';
            $html .=  'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren . ';

            // Payrexx instance
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="payment_payrexx_instance" value="' . $checkout->values()["payment_payrexx_instance"] . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Payrexx Instance</span>';
            $html .=  '</label>';

            // Payrexx secret
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="payment_payrexx_secret" value="' . $checkout->values()["payment_payrexx_secret"] . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Payrexx Secret</span>';
            $html .=  '</label>';
          $html .=  '</div>';

          //Add submit button
          $html .=  '<input type="submit" name="update" value="Update"/>';


        $html .=  '</form>';
      break;
    }

  $html .=  '</div>';

  // Display content
  echo $html;
}

function display_products ( $search_value = null ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;

  // Search form
  $html =  '<form action="' . $url_page . '&list=products" method="post" class="search">';
    $html .=  '<input type="text" name="s_product" value ="' . (isset(  $_POST["s_products"] ) ? $_POST["s_products"] : "") . '" placeholder="Produktname, Preis">';
    $html .=  '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
  $html .=  '</form>';

  // Table
  $html .=  '<table class="rows">';
    //Headline
    $headline_names = array('Name', 'Preis', 'Aktion');

    //Start headline
    $html .=  '<tr>'; //Start row
    foreach( $headline_names as $name ){
      $html .=  '<th>' . $name . '</th>';
    }
    $html .=  '</tr>'; //Close row

    // Set offset and steps
    $steps = 20;
    $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

    foreach( Checkout::global_products( $offset, $steps ) as $products ) {
      $html .=  '<tr>';
        $html .=  '<td>' . $products["name"] . '</td>';
        $html .=  '<td>' . number_format(($products["price"] / 100), 2) . ' ' . $products["currency"] . '</td>';
        $html .=  '<td>';
          if(User::w_access_allowed($page, $current_user)) {
              $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
              $html .=  '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Prdukt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
          }else {
            $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
          }
        $html .=  '</td>';
      $html .=  '</tr>';
    }

    // Menu requred
    $html .=  '<tr class="nav">';

      if( (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                    <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  </td>';
      }elseif (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) { // More pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }

    $html .=  '</tr>';

  $html .=  '</table>';

  if(User::w_access_allowed($page, $current_user)) {
    $html .=  '<a class="add" href="' . $url_page . '&add=product">
      <span class="horizontal"></span>
      <span class="vertical"></span>
    </a>';
  }

  $html .=  '</div>';

  // Display content
  echo $html;
}

function single_product ( $product_id ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;

  // Set id
  $checkout = new Checkout();
  $checkout->product_id = $product_id;

  $html =  '<div class="checkout">';
    $html .=  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
      $html .=  '<h1>Produkt hinzufügen</h1>';
      //Produktname
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text" name="name" value="' . ($checkout->product()["name"] ?? "") . '" required/>';
        $html .=  '<span class="placeholder">Kassenname</span>';
      $html .=  '</label>';

      //Währung
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text" name="currency" value="' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" required/>';
        $html .=  '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
      $html .=  '</label>';

      //Preis
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($checkout->product()["price"] ? number_format(($checkout->product()["price"]/100), 2) :  "")  . '" required/>';
        $html .=  '<span class="placeholder">Preis</span>';
        $html .=  '<span class="unit">' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
      $html .=  '</label>';

      //Add submit button
      $html .=  '<input type="submit" name="update" value="Update"/>';

      //Close form
    $html .=  '</form>';
  $html .=  '</div>';

  // Display content
  echo $html;
}

//Get current action
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys

switch(key($action)) {
  case "remove_checkout":
    // Get name of checkout
    $checkout = new Checkout();
    $checkout->cashier = $_GET["remove_checkout"];

    // Generate message
    $info = "Möchtest du die Kasse <strong>" . $checkout->values()["name"] . " (#" . $_GET["remove_checkout"] . ")</strong>  wirklich löschen?";

    // Display message
    Action::confirm($info, $_GET["remove_checkout"], "&list=checkout");
  break;
  case "remove_product":
    // Get name of checkout
    $checkout = new Checkout();
    $checkout->product_id = $_GET["remove_product"];

    // Generate message
    $info = "Möchtest du das Produkt <strong>" . $checkout->product()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";

    // Display message
    Action::confirm($info, $_GET["remove_product"], "&list=products");
  break;
  case "view_checkout":
    // View checkout
    $checkout = new Checkout();
    $checkout->cashier = $_GET["view_checkout"];

    // Update if required
    if(! empty( $_POST )) {
      if(User::w_access_allowed($page, $current_user)) {
        switch( $_GET["type"] ?? "" ) {
          ////////////////////
          // UPDATE GENERAL //
          ////////////////////
          case "general":
          default:
            // Check what part needs to be updated
            if( $checkout->update_checkout(  $_POST ) ) {
              Action::success("Die Kasse <strong>" . $checkout->values()["name"] . " (#" . $checkout->cashier . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
            }else {
              Action::fail("Die Kasse <strong>" . $checkout->values()["name"] . " (#" . $checkout->cashier . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
            }
          break;
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    // View single
    single_checkout ( $checkout->cashier );
  break;
  case "view_product":
    // set product id
    $checkout = new Checkout();
    $checkout->product_id = $_GET["view_product"];

    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        // Define values
        $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);

        if( $checkout->update_product(  $_POST ) ) {
          Action::success("Das Produkt <strong>" . $checkout->product()["name"] . " (#" . $checkout->product_id . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
        }else {
          Action::fail("Das Produkt <strong>" . $checkout->product()["name"] . " (#" . $checkout->product_id . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }
    // View single
    single_product( $checkout->product_id );
  break;
  case "add":
    if( ($_GET["add"] ?? "") == "product") {
      // Add product
      $checkout = new Checkout();

      if(! empty( $_POST )) {
        if(User::w_access_allowed($page, $current_user)) {
          // Prepare post value
          $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);

          if( $checkout->add( CHECKOUT::PRODUCTS_TABLE, $_POST ) ) {
            Action::success("Die Kasse konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_product=" . $checkout->product_id . "' class='redirect'>Produkt verwalten</a></strong>");
          }else{
            Action::fail("Leider konnte die Kasse <strong>nicht</strong></b> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      //Start form to edit, show user
      echo '<div class="checkbox">';
        echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
          echo '<h1>Produkt hinzufügen</h1>';
          //Produktname
          echo '<label class="txt-input">';
            echo '<input type="text" name="name" required/>';
            echo '<span class="placeholder">Kassenname</span>';
          echo '</label>';

          //Währung
          echo '<label class="txt-input">';
            echo '<input type="text" name="currency" value="' . DEFAULT_CURRENCY . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" required/>';
            echo '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
          echo '</label>';

          //Preis
          echo '<label class="txt-input">';
            echo '<input type="number" step="0.05" min="0" name="price" required/>';
            echo '<span class="placeholder">Preis</span>';
            echo '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
          echo '</label>';

          //Add submit button
          echo '<input type="submit" name="create" value="Erstellen"/>';

          //Close form
        echo '</form>';
      echo '</div>';
    }elseif( ($_GET["add"] ?? "") == "checkout") {
      // Add checkout
      $checkout = new Checkout();

      if(! empty( $_POST )) {
        if(User::w_access_allowed($page, $current_user)) {
          if( $checkout->add( CHECKOUT::DEFAULT_TABLE, $_POST ) ) {
            Action::success("Die Kasse konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_checkout=" . $checkout->cashier . "' class='redirect'>Kasse verwalten</a></strong>");
          }else{
            Action::fail("Leider konnte die Kasse <strong>nicht</strong></b> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      //Start form to edit, show user
      echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
        echo '<h1>Kasse hinzufügen</h1>';
        //Kassenname
        echo '<label class="txt-input">';
          echo '<input type="text" name="name"/>';
          echo '<span class="placeholder">Kassenname</span>';
        echo '</label>';

        //Add submit button
        echo '<input type="submit" name="create" value="Erstellen"/>';

      //Close form
      echo '</form>';
    }else {
      Action::fs_info('Die Unterseite existiert nicht . ', "Zurück", $url_page );
    }
  break;
  case "list":
  default:
    // Display top menu
    echo '<div class="checkout">';
      echo '<div class="top-nav">';
        echo '<a href="' . $url_page . '&list=checkout" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "checkout" ? "selected" : "") : "selected" ) . '" title="Kassen auflisten">KASSEN</a>';
        echo '<a href="' . $url_page . '&list=products" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "") . '" title="Produkte auflisten">PRODUKTE</a>';
      echo '</div>';
    echo '</div>';

    if( ($_GET["list"] ?? "") == "products") {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $checkout = new Checkout();
        $checkout->product_id = $_POST["confirm"];
        $product_values = $checkout->product();

        // Remove
        if( $checkout->remove_product() ) {
          Action::success("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
        }else {
          Action::fail("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
        }
      }

      // List products
      display_products ( ($_POST["s_product"] ?? "") );
    }else {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $checkout = new Checkout();
        $checkout->cashier = $_POST["confirm"];
        $checkout_values = $checkout->values();

        // Remove
        if( $checkout->remove_checkout() ) {
          Action::success("Die Kasse <strong>" . $checkout_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
        }else {
          Action::fail("Die Kasse <strong>" . $checkout_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
        }
      }

      // List checkouts
      display_checkouts ( ($_POST["s_checkout"] ?? "") );
    }
  break;
}





// // Get read or write access for page
// $read = User::r_access_allowed( $page, $current_user );
// $write = User::w_access_allowed( $page, $current_user );
// $disabled = ($write === true ? "" : "disabled");
//
// // Get values
// $checkout = new Checkout();
//
//
//
// // Get page
// if( isset($_GET["add"]) ) {
//   //////////////////////////////////////////////////////////////////
//   // Add checkout
//   //////////////////////////////////////////////////////////////////
//   if( $_GET["add"] == "checkout" ) {
//     // Check post
//     if(! empty( $_POST )) {
//       if(User::w_access_allowed($page, $current_user)) {
//         if( $checkout->add( CHECKOUT::DEFAULT_TABLE, $_POST ) ) {
//           Action::success("Die Kasse konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_checkout=" . $checkout->cashier . "' class='redirect'>Kasse verwalten</a></strong>");
//         }else{
//           Action::fail("Leider konnte die Kasse <strong>nicht</strong></b> erstellt werden.");
//         }
//       }else {
//         Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
//       }
//     }
//
//     //Start form to edit, show user
//     $html = '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
//       $html .= '<h1>Kasse hinzufügen</h1>';
//       //Kassenname
//       $html .= '<label class="txt-input">';
//         $html .= '<input type="text" name="name"/>';
//         $html .= '<span class="placeholder">Kassenname</span>';
//       $html .= '</label>';
//
//       //Add submit button
//       $html .= '<input type="submit" name="create" value="Erstellen"/>';
//
//     //Close form
//     $html .= '</form>';
//   //////////////////////////////////////////////////////////////////
//   // Add product
//   //////////////////////////////////////////////////////////////////
//   }elseif( $_GET["add"] == "product" ) {
//     // Check post
//     if(! empty( $_POST )) {
//       if(User::w_access_allowed($page, $current_user)) {
//         // Prepare post value
//         $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);
//
//         if( $checkout->add( CHECKOUT::PRODUCTS_TABLE, $_POST ) ) {
//           Action::success("Die Kasse konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_product=" . $checkout->product_id . "' class='redirect'>Produkt verwalten</a></strong>");
//         }else{
//           Action::fail("Leider konnte die Kasse <strong>nicht</strong></b> erstellt werden.");
//         }
//       }else {
//         Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
//       }
//     }
//
//     //Start form to edit, show user
//     $html = '<div class="checkbox">';
//       $html .= '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
//         $html .= '<h1>Produkt hinzufügen</h1>';
//         //Produktname
//         $html .= '<label class="txt-input">';
//           $html .= '<input type="text" name="name" required/>';
//           $html .= '<span class="placeholder">Kassenname</span>';
//         $html .= '</label>';
//
//         //Währung
//         $html .= '<label class="txt-input">';
//           $html .= '<input type="text" name="currency" value="' . DEFAULT_CURRENCY . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" required/>';
//           $html .= '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
//         $html .= '</label>';
//
//         //Preis
//         $html .= '<label class="txt-input">';
//           $html .= '<input type="number" step="0.05" min="0" name="price" required/>';
//           $html .= '<span class="placeholder">Preis</span>';
//           $html .= '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
//         $html .= '</label>';
//
//         //Add submit button
//         $html .= '<input type="submit" name="create" value="Erstellen"/>';
//
//         //Close form
//       $html .= '</form>';
//     $html .= '</div>';
//   }else {
//     Action::fs_info('Die Unterseite existiert nicht . ', "Zurück", $url_page );
//     return;
//   }
// }elseif( isset($_GET["view_checkout"]) ) {
//   //////////////////////////////////////////////////////////////////
//   // view checkout
//   //////////////////////////////////////////////////////////////////
//
//   // Set id
//   $checkout->cashier = $_GET["view_checkout"];
//
//   // Update/remove/add
//   if(! empty( $_POST )) {
//     if(User::w_access_allowed($page, $current_user)) {
//       switch( $_GET["type"] ?? "" ) {
//         ////////////////////
//         // UPDATE GENERAL //
//         ////////////////////
//         case "general":
//         default:
//           // Check what part needs to be updated
//           if( $checkout->update_checkout(  $_POST ) ) {
//             Action::success("Die Kasse <strong>" . $checkout->values()["name"] . " (#" . $checkout->cashier . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
//           }else {
//             Action::fail("Die Kasse <strong>" . $checkout->values()["name"] . " (#" . $checkout->cashier . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
//           }
//         break;
//       }
//     }else {
//       Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
//     }
//   }
//
//   // Start HTML
//   $html = '<div class="checkout">';
//     $html .= '<div class="top-nav">';
//       $html .= '<a href="' . $url_page . '&view_checkout=' . $checkout->cashier . '&type=general" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "general" ? "selected" : "") : "selected" ) . '" title="Kasse verwalten">Allgemein</a>';
//       $html .= '<a href="' . $url_page . '&view_checkout=' . $checkout->cashier . '&type=access" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "access" ? "selected" : "") : "") . '" title="Rechte verwalten">Rechte</a>';
//     $html .= '</div>';
//
//     switch( $_GET["type"] ?? "") {
//       case "access":
//       break;
//       case "general":
//       default:
//         // Form
//         $html .= '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*">';
//           //Kassenname
//           $html .= '<div class="box">';
//             $html .= '<p>Kassenname</p>';
//             $html .= '<label class="txt-input">';
//               $html .= '<input type="text" name="name" value="' . $checkout->values()["name"] . '"/>';
//               $html .= '<span class="placeholder">Kassenname</span>';
//             $html .= '</label>';
//           $html .= '</div>';
//
//           // Payrexx
//           $html .= '<div class="box">';
//             $html .= '<p>Payrexx</p>';
//             $html .= 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren . ';
//
//             // Payrexx instance
//             $html .= '<label class="txt-input">';
//               $html .= '<input type="text" name="payment_payrexx_instance" value="' . $checkout->values()["payment_payrexx_instance"] . '" ' . $disabled . '/>';
//               $html .= '<span class="placeholder">Payrexx Instance</span>';
//             $html .= '</label>';
//
//             // Payrexx secret
//             $html .= '<label class="txt-input">';
//               $html .= '<input type="text" name="payment_payrexx_secret" value="' . $checkout->values()["payment_payrexx_secret"] . '" ' . $disabled . '/>';
//               $html .= '<span class="placeholder">Payrexx Secret</span>';
//             $html .= '</label>';
//           $html .= '</div>';
//
//           //Add submit button
//           $html .= '<input type="submit" name="update" value="Update"/>';
//
//
//         $html .= '</form>';
//       break;
//     }
//
//   $html .= '</div>';
// } elseif ( isset($_GET["view_product"] )) {
//   //////////////////////////////////////////////////////////////////
//   // view prduct
//   //////////////////////////////////////////////////////////////////
//
//   // set product id
//   $checkout->product_id = $_GET["view_product"];
//
//   if(! empty($_POST)) {
//     if(User::w_access_allowed($page, $current_user)) {
//       // Define values
//       $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);
//
//       if( $checkout->update_product(  $_POST ) ) {
//         Action::success("Das Produkt <strong>" . $checkout->product()["name"] . " (#" . $checkout->product_id . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
//       }else {
//         Action::fail("Das Produkt <strong>" . $checkout->product()["name"] . " (#" . $checkout->product_id . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
//       }
//     }else {
//       Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
//     }
//   }
//
//   //Start form to edit, show user
//   $html = '<div class="checkout">';
//     $html .= '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
//       $html .= '<h1>Produkt hinzufügen</h1>';
//       //Produktname
//       $html .= '<label class="txt-input">';
//         $html .= '<input type="text" name="name" value="' . ($checkout->product()["name"] ?? "") . '" required/>';
//         $html .= '<span class="placeholder">Kassenname</span>';
//       $html .= '</label>';
//
//       //Währung
//       $html .= '<label class="txt-input">';
//         $html .= '<input type="text" name="currency" value="' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" required/>';
//         $html .= '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
//       $html .= '</label>';
//
//       //Preis
//       $html .= '<label class="txt-input">';
//         $html .= '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($checkout->product()["price"] ? number_format(($checkout->product()["price"]/100), 2) :  "")  . '" required/>';
//         $html .= '<span class="placeholder">Preis</span>';
//         $html .= '<span class="unit">' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
//       $html .= '</label>';
//
//       //Add submit button
//       $html .= '<input type="submit" name="update" value="Update"/>';
//
//       //Close form
//     $html .= '</form>';
//   $html .= '</div>';
//
// } elseif ( isset($_GET["remove_checkout"]) ) { // Remove checkout
//   //////////////////////////////////////////////////////////////////
//   // remove checkout
//   //////////////////////////////////////////////////////////////////
//
//   // Get name of checkout
//   $checkout->cashier = $_GET["remove_checkout"];
//
//   // Generate message
//   $info = "Möchtest du die Kasse <strong>" . $checkout->values()["name"] . " (#" . $_GET["remove_checkout"] . ")</strong>  wirklich löschen?";
//
//   // Display message
//   Action::confirm($info, $_GET["remove_checkout"], "&list=checkout");
// } elseif ( isset($_GET["remove_product"]) ) { // Remove product
//   //////////////////////////////////////////////////////////////////
//   // remove product
//   //////////////////////////////////////////////////////////////////
//
//   // Get name of checkout
//   $checkout->product_id = $_GET["remove_product"];
//
//   // Generate message
//   $info = "Möchtest du das Produkt <strong>" . $checkout->product()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";
//
//   // Display message
//   Action::confirm($info, $_GET["remove_product"], "&list=products");
// } else {
//   // Display top menu
//   $html = '<div class="checkout">';
//     $html .= '<div class="top-nav">';
//       $html .= '<a href="' . $url_page . '&list=checkout" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "checkout" ? "selected" : "") : "selected" ) . '" title="Kassen auflisten">KASSEN</a>';
//       $html .= '<a href="' . $url_page . '&list=products" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "") . '" title="Produkte auflisten">PRODUKTE</a>';
//     $html .= '</div>';
//   $html .= '</div>';
//
//   //////////////////////////////////////////////////////////////////
//   // List checkouts/products
//   //////////////////////////////////////////////////////////////////
//   switch( $_GET["list"] ?? "" ) {
//     case "products":
//       // remove
//       if(isset($_POST["confirm"])) {
//         // Get values
//         $checkout->product_id = $_POST["confirm"];
//         $product_values = $checkout->product();
//
//         // Remove
//         if( $checkout->remove_product() ) {
//           Action::success("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
//         }else {
//           Action::fail("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
//         }
//       }
//
//       // Search form
//       $html .= '<form action="' . $url_page . '" method="post" class="search">';
//         $html .= '<input type="text" name="s_products" value ="' . (isset(  $_POST["s_products"] ) ? $_POST["s_products"] : "") . '" placeholder="Produktname, Preis">';
//         $html .= '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
//       $html .= '</form>';
//
//       // Table
//       $html .= '<table class="rows">';
//         //Headline
//         $headline_names = array('Name', 'Preis', 'Aktion');
//
//         //Start headline
//         $html .= '<tr>'; //Start row
//         foreach( $headline_names as $name ){
//           $html .= '<th>' . $name . '</th>';
//         }
//         $html .= '</tr>'; //Close row
//
//         // Set offset and steps
//         $steps = 20;
//         $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);
//
//         foreach( Checkout::global_products( $offset, $steps ) as $products ) {
//           $html .= '<tr>';
//             $html .= '<td>' . $products["name"] . '</td>';
//             $html .= '<td>' . number_format(($products["price"] / 100), 2) . ' ' . $products["currency"] . '</td>';
//             $html .= '<td>';
//               if(User::w_access_allowed($page, $current_user)) {
//                   $html .= '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
//                   $html .= '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Prdukt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
//               }else {
//                 $html .= '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
//               }
//             $html .= '</td>';
//           $html .= '</tr>';
//         }
//
//         // Menu requred
//         $html .= '<tr class="nav">';
//
//           if( (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
//             $html .= '<td colspan="' . count( $headline_names ) . '">
//                         <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
//                         <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
//                       </td>';
//           }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
//             $html .= '<td colspan="' . count( $headline_names ) . '">
//                         <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
//                       </td>';
//           }elseif (count(Checkout::global_products( ($offset + $steps), 1 )) > 0) { // More pages accessable
//             $html .= '<td colspan="' . count( $headline_names ) . '">
//                         <a href="' . $url_page . '&list=products&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
//                       </td>';
//           }
//
//         $html .= '</tr>';
//
//       $html .= '</table>';
//
//       if(User::w_access_allowed($page, $current_user)) {
//         $html .= '<a class="add" href="' . $url_page . '&add=product">
//           <span class="horizontal"></span>
//           <span class="vertical"></span>
//         </a>';
//       }
//
//       $html .= '</div>';
//     break;
//     case "checkout":
//     default:
//       // remove
//       if(isset($_POST["confirm"])) {
//         // Get values
//         $checkout->cashier = $_POST["confirm"];
//         $checkout_values = $checkout->values();
//
//         // Remove
//         if( $checkout->remove_checkout() ) {
//           Action::success("Das Produkt <strong>" . $checkout_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
//         }else {
//           Action::fail("Das Produkt <strong>" . $checkout_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
//         }
//       }
//
//       // Search form
//       $html = '<div class="checkout">';
//         $html .= '<form action="' . $url_page . '" method="post" class="search">';
//           $html .= '<input type="text" name="s_checkout" value ="' . (isset(  $_POST["s_checkout"] ) ? $_POST["s_checkout"] : "") . '" placeholder="Name der Kasse">';
//           $html .= '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
//         $html .= '</form>';
//
//         // Table
//         $html .= '<table class="rows">';
//           //Headline
//           $headline_names = array('Name', 'Aktion');
//
//           //Start headline
//           $html .= '<tr>'; //Start row
//           foreach( $headline_names as $name ){
//             $html .= '<th>' . $name . '</th>';
//           }
//           $html .= '</tr>'; //Close row
//
//           // Set offset and steps
//           $steps = 20;
//           $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);
//
//           // Get content
//           foreach( Checkout::all( $offset, $steps ) as $checkout ) {
//             $html .= '<tr>';
//               $html .= '<td>' . $checkout["name"] . '</td>';
//               $html .= '<td>';
//                 if(User::w_access_allowed($page, $current_user)) {
//                     $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
//                     $html .= '<a href="' . $url_page . '&remove_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kasse entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
//                 }else {
//                   $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
//                 }
//               $html .= '</td>';
//             $html .= '</tr>';
//           }
//
//           // Menu requred
//           $html .= '<tr class="nav">';
//
//             if( (count(Checkout::all( ($offset + $steps), 1 )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
//               $html .= '<td colspan="' . count( $headline_names ) . '">
//                           <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
//                           <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
//                         </td>';
//             }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
//               $html .= '<td colspan="' . count( $headline_names ) . '">
//                           <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
//                         </td>';
//             }elseif (count(Checkout::all( ($offset + $steps), 1 )) > 0) { // More pages accessable
//               $html .= '<td colspan="' . count( $headline_names ) . '">
//                           <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
//                         </td>';
//             }
//
//           $html .= '</tr>';
//
//         $html .= '</table>';
//
//         if(User::w_access_allowed($page, $current_user)) {
//           $html .= '<a class="add" href="' . $url_page . '&add=checkout">
//             <span class="horizontal"></span>
//             <span class="vertical"></span>
//           </a>';
//         }
//       break;
//     }
//     $html .= '</div>';
//   $html .= '</div>';
//
// }
//
//
// echo $html;
//
//
 ?>
