<?php
function display_pubs ( $search_value = null ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $mainPage;
  global $current_user;

  // Search form
  $html = '<div class="pub">';
    //Display form
    $html .= '<form action="' . $url . '" method="get" class="search">';
      $html .= '<input type="hidden" name="id" value="' . $mainPage . '" />';
      $html .= '<input type="hidden" name="sub" value="' . $page . '" />';
      $html .= '<input type="hidden" name="list" value="' . ($_GET["list"] ?? "") . '" />';
      $html .= '<input type="text" name="s" value ="' . (isset( $_GET["s"] ) ? $_GET["s"] : "") . '" placeholder="Benutzername, Vorname, Nachname, Ticketinfo">';
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
      foreach( Pub::all( $offset, $steps, $search_value ) as $pub ) {
        $html .= '<tr>';
          $html .= '<td>' . $pub["name"] . '</td>';
          $html .= '<td>';
            if(User::w_access_allowed($page, $current_user)) {
                $html .= '<a href="' . $url_page . '&view_pub=' . urlencode( $pub["pub_id"] ) . '" title="Wirtschaftdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
                $html .= '<a href="' . $url_page . '&remove_pub=' . urlencode( $pub["pub_id"] ) . '" title="Wirtschaft entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
            }else {
              $html .= '<a href="' . $url_page . '&view_pub=' . urlencode( $pub["pub_id"] ) . '" title="Wirtschaftdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
            }
          $html .= '</td>';
        $html .= '</tr>';
      }

      // Menu requred
      $html .= '<tr class="nav">';

        if( (count(Pub::all( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
          $html .= '<td colspan="' . count( $headline_names ) . '">
                      <a href="' . $url_page . '&list=pub' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      <a href="' . $url_page . '&list=pub' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                    </td>';
        }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
          $html .= '<td colspan="' . count( $headline_names ) . '">
                      <a href="' . $url_page . '&list=pub' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                    </td>';
        }elseif (count(Pub::all( ($offset + $steps), 1 )) > 0) { // More pages accessable
          $html .= '<td colspan="' . count( $headline_names ) . '">
                      <a href="' . $url_page . '&list=pub' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                    </td>';
        }

      $html .= '</tr>';

    $html .= '</table>';

    if(User::w_access_allowed($page, $current_user)) {
      $html .= '<a class="add" href="' . $url_page . '&add=pub">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }

    // Display content
    echo $html;
}

function single_pub ( $pub_id ) {
  //Require variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;

  // Set id
  $pub = new Pub();
  $pub->pub = $pub_id;

  // Get disabled
  $write = User::w_access_allowed( $page, $current_user );
  $disabled = ($write === true ? "" : "disabled");

  // Start HTML
  $html =  '<div class="pub">';
    $html .=  '<div class="top-nav">';
      $html .=  '<a href="' . $url_page . '&view_pub=' . $pub->pub . '&type=general" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "general" ? "selected" : "") : "selected" ) . '" title="Wirtschaft verwalten">Allgemein</a>';
      $html .=  '<a href="' . $url_page . '&view_pub=' . $pub->pub . '&type=access" class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "access" ? "selected" : "") : "") . '" title="Rechte verwalten">Rechte</a>';
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
                $write_access = $pub->access( $user["id"] )["w"] ?? false;
                $pub_access = $pub->access( $user["id"] )["r"] ?? false;

                $html .= '<td style="width: auto;">';
                  $html .= '<a onclick="' . ($write_access ? "pub_remove_right" : "pub_add_right") . '(this, \'' . $user["id"] . '\', ' . $pub_id . ', \'w\')"
                  title="' . $user["id"] . ' hat' . ($write_access ? " " : " keine ") . 'Schreibrechte auf diese Wirtschaft">                  <img src="' . $url . '/medias/icons/' . ($write_access ? "togglePubRights2.svg" : "togglepubRights1.svg") . '" /></a>';
                  $html .= '<a onclick="' . ($pub_access ? "pub_remove_right" : "pub_add_right") . '(this, \'' . $user["id"] . '\', ' . $pub_id . ', \'r\')"
                  title="' . $user["id"] . ' hat' . ($pub_access ? " " : " keine ") . 'Leserechte auf diese Wirtschaft"><img src="' . $url . '/medias/icons/' . ($pub_access ? "togglepubRights2.svg" : "togglePubRights1.svg") . '" /></a>';
                $html .= '</td>';
              }elseif( User::r_access_allowed($page, $current_user) ){
                //Current user can not edit and delete user
                $write_access = $pub->access( $user["id"] )["w"] ?? false;
                $pub_access = $pub->access( $user["id"] )["r"] ?? false;

                $html .= '<td style="width: auto;" class="disabled">';
                  $html .= '<a title="' . $user["id"] . ' hat' . ($write_access ? " " : " keine ") . 'Schreibrechte auf diese Wirtschaft"><img src="' . $url . '/medias/icons/' . ($write_access ? "togglepubRights2.svg" : "togglepubRights1.svg") . '" /></a>';
                  $html .= '<a title="' . $user["id"] . ' hat' . ($pub_access ? " " : " keine ") . 'Leserechte auf diese Wirtschaft"><img src="' . $url . '/medias/icons/' . ($pub_access ? "togglepubRights2.svg" : "togglepubRights1.svg") . '" /></a>';
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
        $html .= '<div class="right-sub-menu">';
          $html .= '<div class="right-menu-container">';
            $html .= '<a class="right-menu-item" href="' . $url . 'pdf/menu/?pub=' . $pub->pub . '" target="_blank"><img src="' . $url . 'medias/icons/pdf.svg" alt="PDF" title="Speise und Getränkekarte als PDF ansehen"/></a>';
            if($pub->values()["tip"] == 1) {
              $html .= '<a class="right-menu-item" onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"><img src="' . $url . 'medias/icons/tip-money-on.svg" alt="Visibility" title="Trinkgeld anzeigen/verbergen"/></a>';
            }else {
              $html .= '<a class="right-menu-item" onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"><img src="' . $url . 'medias/icons/tip-money-off.svg" alt="Visibility" title="Trinkgeld anzeigen/verbergen"/></a>';
            }
          $html .= '</div>';
        $html .= '</div>';

        // Form
        $html .=  '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" class="right-menu">';
          //Wirtschaftnname
          $html .=  '<div class="box">';
            $html .= '<p>Generell</p>';
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="name" value="' . $pub->values()["name"] . '" ' . $disabled .'/>';
              $html .=  '<span class="placeholder">Wirtschaftname</span>';
            $html .=  '</label>';
          $html .=  '</div>';

          //Beschreibung
          $html .=  '<div class="box">';
            $html .=  '<label class="txt-input">';
              $html .=  '<textarea name="description" ' . $disabled .'/>' . $pub->values()["description"] . '</textarea>';
              $html .=  '<span class="placeholder">Beschreibung</span>';
            $html .=  '</label>';
          $html .=  '</div>';

          // Images
          $html .=  '<div class="box">';
            $html .=  '<p>Bilder</p>';
            $html .= '<span class="file-info">Logo</span>';
            $html .= '<label class="file-input ' . $disabled .'" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'logo_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($pub->values()["logo_fileID"]) &&! empty($pub->values()["logo_fileID"]) ) {
                $html .= '<input type="hidden" name="logo_fileID" value="' . $pub->values()["logo_fileID"] . '" onchange="MediaHubSelected(this)">';
                $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->values()["logo_fileID"] ) . '\')"></div>';
              }else {
                $html .= '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
                $html .= '<input type="hidden" name="logo_fileID" onchange="MediaHubSelected(this)">';
              }
              $html .= '<div class="draganddrop">Klicken um auszuwählen</div>';
            $html .= '</label>';

            $html .= '<span class="file-info">Hintergrundbild</span>';
            $html .= '<label class="file-input ' . $disabled .'" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'background_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($pub->values()["background_fileID"]) &&! empty($pub->values()["background_fileID"]) ) {
                $html .= '<input type="hidden" name="background_fileID" value="' . $pub->values()["background_fileID"] . '" onchange="MediaHubSelected(this)">';
                $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->values()["background_fileID"] ) . '\')"></div>';
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
            $html .=  'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.';

            // Payrexx instance
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="payment_payrexx_instance" value="' . $pub->values()["payment_payrexx_instance"] . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Payrexx Instance</span>';
            $html .=  '</label>';

            // Payrexx secret
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="text" name="payment_payrexx_secret" value="' . $pub->values()["payment_payrexx_secret"] . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Payrexx Secret</span>';
            $html .=  '</label>';

            //Währung
            $html .= '<label class="txt-input">';
              $html .= '<input type="text" name="currency" value="' . ( $pub->values()["currency"] ?? DEFAULT_CURRENCY ) . '" min="3" max="3" ' . $disabled . '/>';
              $html .= '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
            $html .= '</label>';
          $html .=  '</div>';

          // Fees
          $html .=  '<div class="box">';
            $html .=  '<p>Gebühren</p>';
            $html .=  'Pro Transaktion verlangt der Anbieter entsprechende Gebühren. Bitte definiere hier, welche Gebüren dein Zahlungsanbieter verlang um die Auswertung korrekt zu erhalten. Die beiden Gebühren werden zusammengezählt und entsprechent verrechnet. An den Produktpreisen ändert sich dadurch nichts.';

            // Payrexx instance
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="number" name="payment_fee_absolute" value="' . ($pub->values()["payment_fee_absolute"] / 100) . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Absolute Gebühren</span>';
              $html .=  '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
            $html .=  '</label>';

            // Payrexx secret
            $html .=  '<label class="txt-input">';
              $html .=  '<input type="number" name="payment_fee_percent" value="' . ($pub->values()["payment_fee_percent"] / 100) . '" ' . $disabled . '/>';
              $html .=  '<span class="placeholder">Prozentuale Gebühren</span>';
              $html .=  '<span class="unit">%</span>';
            $html .=  '</label>';
          $html .=  '</div>';

          //Add submit button
          $html .=  '<input type="submit" name="update" value="Update" ' . $disabled .'/>';


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
  global $mainPage;
  global $current_user;

  //Display form
  $html = '<form action="' . $url . '" method="get" class="search">';
    $html .= '<input type="hidden" name="id" value="' . $mainPage . '" />';
    $html .= '<input type="hidden" name="sub" value="' . $page . '" />';
    $html .= '<input type="hidden" name="list" value="' . $_GET["list"] . '" />';
    $html .= '<input type="text" name="s" value ="' . (isset( $_GET["s"] ) ? $_GET["s"] : "") . '" placeholder="Benutzername, Vorname, Nachname, Ticketinfo">';
    $html .= '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
  $html .= '</form>';

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

    foreach( Product::global_products( $offset, $steps, $search_value ) as $products ) {
      $html .=  '<tr>';
        $html .=  '<td>' . $products["name"] . '</td>';
        $html .=  '<td>' . number_format(($products["price"] / 100), 2) . ' ' . DEFAULT_CURRENCY . '</td>';
        $html .=  '<td>';
          if(User::w_access_allowed($page, $current_user)) {
              $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
              $html .=  '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Produkt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
          }else {
            $html .=  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
          }
        $html .=  '</td>';
      $html .=  '</tr>';
    }

    // Menu requred
    $html .=  '<tr class="nav">';

      if( (count(Product::global_products( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                    <a href="' . $url_page . '&list=products' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  </td>';
      }elseif (count(Product::global_products( ($offset + $steps), 1 )) > 0) { // More pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=products' . (isset( $_GET["s"] ) ? "&s=" . urlencode($_GET["s"]) : "") . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
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
  $product = new Product();
  $product->product_id = $product_id;

  // Get disabled
  $write = User::w_access_allowed( $page, $current_user );
  $disabled = ($write === true ? "" : "disabled");

  // Start html
  $html =  '<div class="pub">';
    $html .=  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
      if( User::w_access_allowed( $page, $current_user) ) {
        $html .=  '<h1>Produkt bearbeiten</h1>';
      }else {
        $html .=  '<h1>Produkt ansehen</h1>';
      }
      //Produktname
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text" name="name" value="' . ($product->values()["name"] ?? "") . '" ' . $disabled . ' required/>';
        $html .=  '<span class="placeholder">Wirtschaftnname</span>';
      $html .=  '</label>';

      // Section
      $html .= '<div class="select" onclick="toggleOptions(this)">';
        $html .= '<input type="text" class="selectValue" name="section" ' . (isset($product->values()["section"]) ? 'value="' . $product->values()["section"] . '"' : "") . ' ' . $disabled . '>';
        $html .= '<span class="headline">' . (isset($product->values()["section"]) ? $product->values()["section"] : "Sektion") . '</span>';

        if( User::w_access_allowed( $page, $current_user) ) {
          $html .= '<div class="options">';
            foreach( $product->sections() as $section ) {
              $html .= '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
            }
            $html .= '<span onclick="event.stopPropagation()" class="option_add" >';
              $html .= '<input type="text"/>';
              $html .= '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
            $html .= '</span>';
          $html .= '</div>';
        }
      $html .= '</div>';

      //Preis
      $html .=  '<label class="txt-input">';
        $html .=  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  "")  . '" ' . $disabled . ' required/>';
        $html .=  '<span class="placeholder">Preis</span>';
        $html .=  '<span class="unit"><abbr title="Es wird jeweils die Standartwährung verwendet, sofern bei einer Wirtschaft keine andere Währung angegeben wird.">' . DEFAULT_CURRENCY . '</abbr></span>';
      $html .=  '</label>';

      // Produktbild
      $html .= '<span class="file-info">Produktbild</span>';
      $html .= '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
        // Display preview image if possible
        if( isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"]) ) {
          $html .= '<input type="hidden" name="product_fileID" value="' . $product->values()["product_fileID"] . '" onchange="MediaHubSelected(this)">';
          $html .= '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $product->values()["product_fileID"] ) . '\')"></div>';
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
  case "remove_pub":
    // Get name of pub
    $pub = new Pub();
    $pub->pub = $_GET["remove_pub"];

    // Generate message
    $info = "Möchtest du die Wirtschaft <strong>" . $pub->values()["name"] . " (#" . $_GET["remove_pub"] . ")</strong>  wirklich löschen?";

    // Display message
    Action::confirm($info, $_GET["remove_pub"], "&list=pub");
  break;
  case "remove_product":
    // Get name of pub
    $product = new Product();
    $product->product_id = $_GET["remove_product"];

    // Generate message
    $info = "Möchtest du das Produkt <strong>" . $product->values()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";

    // Display message
    Action::confirm($info, $_GET["remove_product"], "&list=products");
  break;
  case "view_pub":
    // View pub
    $pub = new pub();
    $pub->pub = $_GET["view_pub"];

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
            $_POST["payment_fee_absolute"] = ($_POST["payment_fee_absolute"] ? 100 *$_POST["payment_fee_absolute"] : 0);
            $_POST["payment_fee_percent"] = ($_POST["payment_fee_percent"] ? 100 * $_POST["payment_fee_percent"] : 0);

            if( $pub->update(  $_POST ) ) {
              Action::success("Die Wirtschaft <strong>" . $pub->values()["name"] . " (#" . $pub->pub . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
            }else {
              Action::fail("Die Wirtschaft <strong>" . $pub->values()["name"] . " (#" . $pub->pub . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
            }
          break;
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    // View single
    single_pub ( $pub->pub );
  break;
  case "view_product":
    // set product id
    $product = new Product();
    $product->product_id = $_GET["view_product"];

    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        // Define values
        $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);

        if( $product->update(  $_POST ) ) {
          Action::success("Das Produkt <strong>" . $product->values()["name"] . " (#" . $product->product_id . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
        }else {
          Action::fail("Das Produkt <strong>" . $product->values()["name"] . " (#" . $product->product_id . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    // Display top return button
    echo '<div class="pub">';
      echo '<div class="top-nav">';
        echo '<a href="Javascript:history.back()" title="Zur vorherigen Seite zurück"><img src="' . $url . 'medias/icons/history-back.svg"></a>';
      echo '</div>';
    echo '</div>';

    // View single
    single_product( $product->product_id );
  break;
  case "add":
    if( ($_GET["add"] ?? "") == "product") {
      // Add product
      $product = new Product();

      // Get disabled
      $write = User::w_access_allowed( $page, $current_user );
      $disabled = ($write === true ? "" : "disabled");

      if(! empty( $_POST )) {
        if(User::w_access_allowed($page, $current_user)) {
          // Prepare post value
          $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);

          if( $product->add( $_POST ) ) {
            Action::success("Die Wirtschaft konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_product=" . $pub->product_id . "' class='redirect'>Produkt verwalten</a></strong>");
          }else{
            Action::fail("Leider konnte die Wirtschaft <strong>nicht</strong></b> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      //Start form to edit, show user
      echo '<div class="pub">';
        echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
          echo '<h1>Produkt hinzufügen</h1>';
          //Produktname
          echo '<label class="txt-input">';
            echo '<input type="text" name="name" ' . $disabled . ' required/>';
            echo '<span class="placeholder">Produktname</span>';
          echo '</label>';

          // Section
          echo '<div class="select" onclick="toggleOptions(this)">';
            echo '<input type="text" class="selectValue" name="section" ' . $disabled . '>';
            echo '<span class="headline">Sektion</span>';

            echo '<div class="options">';
              foreach( $product->sections() as $section ) {
                echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
              }
              echo '<span onclick="event.stopPropagation()" class="option_add" >';
                echo '<input type="text"/>';
                echo '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
              echo '</span>';
            echo '</div>';
          echo '</div>';

          //Preis
          echo '<label class="txt-input">';
            echo '<input type="number" step="0.05" min="0" name="price" ' . $disabled . ' required/>';
            echo '<span class="placeholder">Preis</span>';
            echo '<span class="unit"><abbr title="Es wird jeweils die Standartwährung verwendet, sofern bei einer Wirtschaft keine andere Währung angegeben wird.">' . DEFAULT_CURRENCY . '</abbr></span>';
          echo '</label>';


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
    }elseif( ($_GET["add"] ?? "") == "pub") {
      // Add pub
      $pub = new pub();

      // Get disabled
      $write = User::w_access_allowed( $page, $current_user );
      $disabled = ($write === true ? "" : "disabled");

      if(! empty( $_POST )) {
        // Prepare post
        $_POST["payment_fee_absolute"] = ($_POST["payment_fee_absolute"] ? 100 *$_POST["payment_fee_absolute"] : 0);
        $_POST["payment_fee_percent"] = ($_POST["payment_fee_percent"] ? 100 * $_POST["payment_fee_percent"] : 0);

        if(User::w_access_allowed($page, $current_user)) {
          if( $pub->add( Pub::DEFAULT_TABLE, $_POST ) ) {
            Action::success("Die Wirtschaft konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&view_pub=" . $pub->pub . "' class='redirect'>Wirtschaft verwalten</a></strong>");
          }else{
            Action::fail("Leider konnte die Wirtschaft <strong>nicht</strong></b> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      //Start form to edit, show user
      echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
        echo '<h1>Wirtschaft hinzufügen</h1>';
        //Wirtschaftnname
        echo '<label class="txt-input">';
          echo '<input type="text" name="name" ' . $disabled . '/>';
          echo '<span class="placeholder">Wirtschaftname</span>';
        echo '</label>';

        //Beschreibung
        echo '<label class="txt-input">';
            echo '<textarea name="description" ' . $disabled .'/></textarea>';
            echo '<span class="placeholder">Wirtschaftname</span>';
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
        echo '<br />Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.';

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

        //Währung
        echo '<label class="txt-input">';
            echo '<input type="text" name="currency" min="3" max="3" ' . $disabled . '/>';
            echo '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
          echo '</label>';

        // Fees
        echo '<br />Pro Transaktion verlangt der Anbieter entsprechende Gebühren. Bitte definiere hier, welche Gebüren dein Zahlungsanbieter verlang um die Auswertung korrekt zu erhalten. Die beiden Gebühren werden zusammengezählt und entsprechent verrechnet. An den Produktpreisen ändert sich dadurch nichts.';

        // Payrexx instance
        echo '<label class="txt-input">';
          echo '<input type="number" name="payment_fee_absolute" ' . $disabled . '/>';
          echo '<span class="placeholder">Absolute Gebühren</span>';
          echo '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
        echo '</label>';

        // Payrexx secret
        echo '<label class="txt-input">';
          echo '<input type="number" name="payment_fee_percent" ' . $disabled . '/>';
          echo '<span class="placeholder">Prozentuale Gebühren</span>';
          echo '<span class="unit">%</span>';
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
    echo '<div class="pub">';
      echo '<div class="top-nav">';
        echo '<a href="' . $url_page . '&list=pub" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "pub" ? "selected" : "") : "selected" ) . '" title="Wirtschaften auflisten">WIRTSCHAFTEN</a>';
        echo '<a href="' . $url_page . '&list=products" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "") . '" title="Produkte auflisten">GLOBALE PRODUKTE</a>';
      echo '</div>';
    echo '</div>';

    if( ($_GET["list"] ?? "") == "products") {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $product = new Product();
        $product->product_id = $_POST["confirm"];
        $product_values = $product->values();

        // Remove
        if( $product->remove() ) {
          Action::success("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
        }else {
          Action::fail("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
        }
      }

      // List products
      display_products ( ($_GET["s"] ?? null) );
    }else {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $pub = new Pub();
        $pub->pub = $_POST["confirm"];
        $pub_values = $pub->values();

        // Remove
        if( $pub->remove() ) {
          Action::success("Die Wirtschaft <strong>" . $pub_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
        }else {
          Action::fail("Die Wirtschaft <strong>" . $pub_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
        }
      }

      // List pubs
      display_pubs ( ($_GET["s"] ?? null) );
    }
  break;
}
 ?>
