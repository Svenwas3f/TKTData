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
      foreach( Checkout::all( $offset, $steps, $search_value ) as $checkout ) {
        $html .= '<tr>';
          $html .= '<td>' . $checkout["name"] . '</td>';
          $html .= '<td>';
            if(User::w_access_allowed($page, $current_user)) {
                $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
                $html .= '<a href="' . $url_page . '&remove_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kasse entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
            }else {
              $html .= '<a href="' . $url_page . '&view_checkout=' . urlencode( $checkout["checkout_id"] ) . '" title="Kassendetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
            }
          $html .= '</td>';
        $html .= '</tr>';
      }

      // Menu requred
      $html .= '<tr class="nav">';

        if( (count(Checkout::all( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
        //Define variables
        $number_rows = 20; //Maximal number of rows listed
        $offset = isset( $_GET["row-start"] ) ? (intval($_GET["row-start"]) * $number_rows) : 0; //Start position of listet users

        /**
         * Start html
         */
        $html .= '<table class="rows">';

        /**
         * Create headline
         */
        $headline_names = array('Benutzername', 'Email', 'Schreiben | Lesen');

        //Start headline
        //Headline can be changed over array $headline_names
        $html .= '<tr>'; //Start row
        foreach( $headline_names as $name ){
          $html .= '<th>' . $name . '</th>';
        }
        $html .= '</tr>'; //Close row

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        foreach( User::all( $offset, $steps, null) as $user) {
            $html .= '<tr class="table-list">'; //Start row
              $html .= '<td style="width: 10%;">' . $user["id"] . '</td>'; //Display user id
              $html .= '<td style="width: 70%;">' . $user["email"] . '</td>'; //Display Name (pre and lastname)

              //Check if current user (logged in user) can edit or see the user
              if( User::w_access_allowed($page, $current_user) ){
                //Current user can edit and delete user
                $write_access = $checkout->access( $user["id"] )["w"] ?? false;
                $checkout_access = $checkout->access( $user["id"] )["r"] ?? false;

                $html .= '<td style="width: auto;">';
                  $html .= '<a onclick="' . ($write_access ? "checkout_remove_right" : "checkout_add_right") . '(this, \'' . $user["id"] . '\', ' . $checkout_id . ', \'w\')"
                  title="' . $user["id"] . ' hat' . ($write_access ? " " : " keine ") . 'Schreibrechte auf diese Kasse">                  <img src="' . $url . '/medias/icons/' . ($write_access ? "toggleCheckoutRights2.svg" : "toggleCheckoutRights1.svg") . '" /></a>';
                  $html .= '<a onclick="' . ($checkout_access ? "checkout_remove_right" : "checkout_add_right") . '(this, \'' . $user["id"] . '\', ' . $checkout_id . ', \'r\')"
                  title="' . $user["id"] . ' hat' . ($checkout_access ? " " : " keine ") . 'Leserechte auf diese Kasse"><img src="' . $url . '/medias/icons/' . ($checkout_access ? "toggleCheckoutRights2.svg" : "toggleCheckoutRights1.svg") . '" /></a>';
                $html .= '</td>';
              }elseif( User::r_access_allowed($page, $current_user) ){
                //Current user can edit and delete user
                $write_access = $checkout->access( $user["id"] )["w"] ?? false;
                $checkout_access = $checkout->access( $user["id"] )["r"] ?? false;

                $html .= '<td style="width: auto;">';
                  $html .= '<a title="' . $user["id"] . ' hat' . ($write_access ? " " : " keine ") . 'Schreibrechte auf diese Kasse">                  <img src="' . $url . '/medias/icons/' . ($write_access ? "toggleCheckoutRights2.svg" : "toggleCheckoutRights1.svg") . '" /></a>';
                  $html .= '<a title="' . $user["id"] . ' hat' . ($checkout_access ? " " : " keine ") . 'Leserechte auf diese Kasse"><img src="' . $url . '/medias/icons/' . ($checkout_access ? "toggleCheckoutRights2.svg" : "toggleCheckoutRights1.svg") . '" /></a>';
                $html .= '</td>';
              }

            $html .= '</tr>'; //End row
          }

          // Menu requred
          $html .=  '<tr class="nav">';

            if( (count(User::all( ($offset + $steps), 1, null )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
              $html .=  '<td colspan="' . count( $headline_names ) . '">
                          <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                          <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                        </td>';
            }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
              $html .=  '<td colspan="' . count( $headline_names ) . '">
                          <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        </td>';
            }elseif (count(User::all( ($offset + $steps), 1, null )) > 0) { // More pages accessable
              $html .=  '<td colspan="' . count( $headline_names ) . '">
                          <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                        </td>';
            }

          $html .=  '</tr>';

        //Close table
        $html .= '</table>';
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

          // Images
          $html .=  '<div class="box">';
            $html .=  '<p>Bilder</p>';
            $html .= '<span class="file-info">Logo</span>';
            $html .= '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'logo_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($checkout->values()["logo_fileID"]) &&! empty($checkout->values()["logo_fileID"]) ) {
                $html .= '<input type="hidden" name="logo_fileID" value="' . $checkout->values()["logo_fileID"] . '" onchange="MediaHubSelected(this)">';
                $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $checkout->values()["logo_fileID"] ) . '\')"></div>';
              }else {
                $html .= '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
                $html .= '<input type="hidden" name="logo_fileID" onchange="MediaHubSelected(this)">';
              }
              $html .= '<div class="draganddrop">Klicken um auszuwählen</div>';
            $html .= '</label>';

            $html .= '<span class="file-info">Hintergrundbild</span>';
            $html .= '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'background_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($checkout->values()["background_fileID"]) &&! empty($checkout->values()["background_fileID"]) ) {
                $html .= '<input type="hidden" name="background_fileID" value="' . $checkout->values()["background_fileID"] . '" onchange="MediaHubSelected(this)">';
                $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $checkout->values()["background_fileID"] ) . '\')"></div>';
              }else {
                $html .= '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
                $html .= '<input type="hidden" name="background_fileID" onchange="MediaHubSelected(this)">';
              }
              $html .= '<div class="draganddrop">Klicken um auszuwählen</div>';
            $html .= '</label>';
          $html .= '</div>';

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

    foreach( Checkout::global_products( $offset, $steps, $search_value ) as $products ) {
      $html .=  '<tr>';
        $html .=  '<td>' . $products["name"] . '</td>';
        $html .=  '<td>' . number_format(($products["price"] / 100), 2) . ' ' . $products["currency"] . '</td>';
        $html .=  '<td>';
          if(User::w_access_allowed($page, $current_user)) {
              $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
              $html .=  '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Prdukt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
          }else {
            $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
          }
        $html .=  '</td>';
      $html .=  '</tr>';
    }

    // Menu requred
    $html .=  '<tr class="nav">';

      if( (count(Checkout::global_products( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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

  // Get disabled
  $write = User::w_access_allowed( $page, $current_user );
  $disabled = ($write === true ? "" : "disabled");


  $html =  '<div class="checkout">';
    $html .=  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
      $html .=  '<h1>Produkt hinzufügen</h1>';
      //Produktname
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text" name="name" value="' . ($checkout->product()["name"] ?? "") . '" ' . $disabled . ' required/>';
        $html .=  '<span class="placeholder">Kassenname</span>';
      $html .=  '</label>';

      // Section
      $html .= '<div class="select" onclick="toggleOptions(this)">';
        $html .= '<input type="text" class="selectValue" name="availability" ' . $disabled . '>';
        $html .= '<span class="headline">Sektion</span>';

        $html .= '<div class="options">';
          foreach( $checkout->sections() as $section ) {
            $html .= '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
          }
          $html .= '<span onclick="event.stopPropagation()" >';
            $html .= '<input type="text"/>';
            $html .= '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
          $html .= '</span>';
        $html .= '</div>';
      $html .= '</div>';

      //Währung
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text" name="currency" value="' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" ' . $disabled . ' required/>';
        $html .=  '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
      $html .=  '</label>';

      //Preis
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($checkout->product()["price"] ? number_format(($checkout->product()["price"]/100), 2) :  "")  . '" ' . $disabled . ' required/>';
        $html .=  '<span class="placeholder">Preis</span>';
        $html .=  '<span class="unit">' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
      $html .=  '</label>';

      // Status
      $availability = array(
        0 => "Verfügbar",
        1 => "Wenige verfügbar",
        2 => "Ausverkauft"
      );

      $html .= '<div class="select" onclick="toggleOptions(this)">';
        $html .= '<input type="text" class="selectValue" name="availability" ' . $disabled . ' ' . (isset($checkout->product()["availability"]) ? 'value="' . $checkout->product()["availability"] . '"' : "") . ' required>';
        $html .= '<span class="headline">' . ($availability[$checkout->product()["availability"]] ?? 'Produktverfügbarkeit') . '</span>';

        $html .= '<div class="options">';
          $html .= '<span data-value="0" onclick="selectElement(this)">Verfügbar</span>';
          $html .= '<span data-value="1" onclick="selectElement(this)">Wenige verfügbar</span>';
          $html .= '<span data-value="2" onclick="selectElement(this)">Ausverkauft</span>';
        $html .= '</div>';
      $html .= '</div>';


      // Produktbild
      $html .= '<span class="file-info">Produktbild</span>';
      $html .= '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
        // Display preview image if possible
        if( isset($checkout->product()["product_fileID"]) &&! empty($checkout->product()["product_fileID"]) ) {
          $html .= '<input type="hidden" name="product_fileID" value="' . $checkout->product()["product_fileID"] . '" onchange="MediaHubSelected(this)">';
          $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $checkout->product()["product_fileID"] ) . '\')"></div>';
        }else {
          $html .= '<input type="hidden" name="product_fileID" onchange="MediaHubSelected(this)">';
        }
        $html .= '<div class="draganddrop">Klicken um auszuwählen</div>';
      $html .= '</label>';

      //Add submit button
      if( $disabled != "disabled" ) {
        $html .=  '<input type="submit" name="update" value="Update"/>';
      }

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

      // Get disabled
      $write = User::w_access_allowed( $page, $current_user );
      $disabled = ($write === true ? "" : "disabled");

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
            echo '<input type="text" name="name" ' . $disabled . ' required/>';
            echo '<span class="placeholder">Produktname</span>';
          echo '</label>';

          // Section
          echo '<div class="select" onclick="toggleOptions(this)">';
            echo '<input type="text" class="selectValue" name="availability" ' . $disabled . '>';
            echo '<span class="headline">Sektion</span>';

            echo '<div class="options">';
              foreach( $checkout->sections() as $section ) {
                echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
              }
              echo '<span onclick="event.stopPropagation()" >';
                echo '<input type="text"/>';
                echo '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
              echo '</span>';
            echo '</div>';
          echo '</div>';

          //Währung
          echo '<label class="txt-input">';
            echo '<input type="text" name="currency" value="' . DEFAULT_CURRENCY . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" ' . $disabled . ' required/>';
            echo '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
          echo '</label>';

          //Preis
          echo '<label class="txt-input">';
            echo '<input type="number" step="0.05" min="0" name="price" ' . $disabled . ' required/>';
            echo '<span class="placeholder">Preis</span>';
            echo '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
          echo '</label>';

          // Status
          $availability = array(
            0 => "Verfügbar",
            1 => "Wenige verfügbar",
            2 => "Ausverkauft"
          );

          echo '<div class="select" onclick="toggleOptions(this)">';
            echo '<input type="text" class="selectValue" name="availability" ' . $disabled . ' required>';
            echo '<span class="headline">Produktverfügbarkeit</span>';

            echo '<div class="options">';
              echo '<span data-value="0" onclick="selectElement(this)">Verfügbar</span>';
              echo '<span data-value="1" onclick="selectElement(this)">Wenige verfügbar</span>';
              echo '<span data-value="2" onclick="selectElement(this)">Ausverkauft</span>';
            echo '</div>';
          echo '</div>';


          // Produktbild
          echo '<span class="file-info">Produktbild</span>';
          echo '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
            echo '<input type="hidden" name="product_fileID" onchange="MediaHubSelected(this)">';
            echo '<div class="draganddrop">Klicken um auszuwählen</div>';
          echo '</label>';

          //Add submit button
          echo '<input type="submit" name="create" value="Erstellen"/>';

          //Close form
        echo '</form>';
      echo '</div>';
    }elseif( ($_GET["add"] ?? "") == "checkout") {
      // Add checkout
      $checkout = new Checkout();

      // Get disabled
      $write = User::w_access_allowed( $page, $current_user );
      $disabled = ($write === true ? "" : "disabled");

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
          echo '<input type="text" name="name" ' . $disabled . '/>';
          echo '<span class="placeholder">Kassenname</span>';
        echo '</label>';

        // Images
        echo '<span class="file-info">Logo</span>';
        echo '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'logo_fileID\' )"' ) . '>';
          echo '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
          echo '<input type="hidden" name="logo_fileID" onchange="MediaHubSelected(this)">';
          echo '<div class="draganddrop">Klicken um auszuwählen</div>';
        echo '</label>';

        echo '<span class="file-info">Hintergrundbild</span>';
        echo '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'background_fileID\' )"' ) . '>';
            echo '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
            echo '<input type="hidden" name="background_fileID" onchange="MediaHubSelected(this)">';
          echo '<div class="draganddrop">Klicken um auszuwählen</div>';
        echo '</label>';

        // Payrexx
        echo '<br />Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren . ';

        // Payrexx instance
        echo '<label class="txt-input">';
          echo '<input type="text" name="payment_payrexx_instance" ' . $disabled . '/>';
          echo '<span class="placeholder">Payrexx Instance</span>';
        echo '</label>';

        // Payrexx secret
        echo '<label class="txt-input">';
          echo '<input type="text" name="payment_payrexx_secret" ' . $disabled . '/>';
          echo '<span class="placeholder">Payrexx Secret</span>';
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
      display_products ( ($_POST["s_product"] ?? null) );
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
      display_checkouts ( ($_POST["s_checkout"] ?? null) );
    }
  break;
}
 ?>
