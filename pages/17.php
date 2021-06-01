<!-- - Liste aller verfügbaren Kassen<br />
- Produkte hinzufügen<br />
- Produkte entfernen<br />
- Produkte überarbeiten<br />
- Produkte einsehen<br />
- Preisliste generieren<br /> -->


<?php
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys
unset($action["checkout"]);

// Start checkout
$checkout = new Checkout();

// Get disabled
$write = User::w_access_allowed( $page, $current_user );
$disabled = ($write === true ? "" : "disabled");

echo '<div class="checkout">';
  // List accessable checkouts
  $accessable_checkouts = $checkout->accessable( $current_user );
  if( count($accessable_checkouts) > 1 ) {
    // Current visible
    $checkout->cashier = ($_GET["checkout"] ?? $accessable_checkouts[0]);

    // Multiple access
    echo '<div class="header" onclick="this.children[1].classList.toggle(\'visible\')">';
      echo '<span class="current multiple">' . $checkout->values()["name"] . '</span>';
      echo '<div class="checkouts">';
        // List all accessable checkouts
        foreach( $accessable_checkouts as $checkout_id ) {
          if( $checkout->cashier != $checkout_id) {
            // Start new checkout for name
            $name_checkout = new Checkout();
            $name_checkout->cashier = $checkout_id;

            echo '<a href="' . $url_page . '&checkout=' . $checkout_id . '">' . $name_checkout->values()["name"] . '</a>';
          }
        }
      echo '</div>';
    echo '</div>';
  } else {
    // Single access
    $checkout->cashier = $accessable_checkouts[0];

    echo '<div class="header">';
      echo '<span class="current">' . $checkout->values()["name"] . '</span>';
    echo '</div>';
  }

  switch(key($action)) {
    case "add": //Product
      if(! empty( $_POST )) {
        if(User::w_access_allowed($page, $current_user)) {
          // Prepare post value
          $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);
          $_POST["checkout_id"] = $checkout->cashier;

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
      echo '<div class="checkout">';
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
              foreach( $checkout->sections() as $section ) {
                echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
              }
              echo '<span onclick="event.stopPropagation()" class="option_add" >';
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
    break;
    case "remove_product": // Product
      // Get name of checkout
      $checkout->product_id = $_GET["remove_product"] ?? null;

      // Generate message
      $info = "Möchtest du das Produkt <strong>" . $checkout->product()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";

      // Display message
      Action::confirm($info, $_GET["remove_product"]);
    break;
    case "view_product":
      // Set product id
      $checkout->product_id = $_GET["view_product"] ?? null;

      // Update
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

      // Generate html
      echo  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
        echo  '<h1>Produkt bearbeten</h1>';
        //Produktname
        echo  '<label class="txt-input">';
          echo  '<input type="text" name="name" value="' . ($checkout->product()["name"] ?? "") . '" ' . $disabled . ' required/>';
          echo  '<span class="placeholder">Kassenname</span>';
        echo  '</label>';

        // Section
        echo '<div class="select" onclick="toggleOptions(this)">';
          echo '<input type="text" class="selectValue" name="section" ' . (isset($checkout->product()["section"]) ? 'value="' . $checkout->product()["section"] . '"' : "") . ' ' . $disabled . '>';
          echo '<span class="headline">' . (isset($checkout->product()["section"]) ? $checkout->product()["section"] : "Sektion") . '</span>';

          echo '<div class="options">';
            foreach( $checkout->sections() as $section ) {
              echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
            }
            echo '<span onclick="event.stopPropagation()" class="option_add" >';
              echo '<input type="text"/>';
              echo '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
            echo '</span>';
          echo '</div>';
        echo '</div>';

        //Währung
        echo  '<label class="txt-input">';
          echo  '<input type="text" name="currency" value="' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" ' . $disabled . ' required/>';
          echo  '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
        echo  '</label>';

        //Preis
        echo  '<label class="txt-input">';
          echo  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($checkout->product()["price"] ? number_format(($checkout->product()["price"]/100), 2) :  "")  . '" ' . $disabled . ' required/>';
          echo  '<span class="placeholder">Preis</span>';
          echo  '<span class="unit">' . ($checkout->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
        echo  '</label>';

        // Status
        $availability = array(
          0 => "Verfügbar",
          1 => "Wenige verfügbar",
          2 => "Ausverkauft"
        );

        echo '<div class="select" onclick="toggleOptions(this)">';
          echo '<input type="text" class="selectValue" name="availability" ' . $disabled . ' ' . (isset($checkout->product()["availability"]) ? 'value="' . $checkout->product()["availability"] . '"' : "") . ' required>';
          echo '<span class="headline">' . ($availability[$checkout->product()["availability"]] ?? 'Produktverfügbarkeit') . '</span>';

          echo '<div class="options">';
            echo '<span data-value="0" onclick="selectElement(this)">Verfügbar</span>';
            echo '<span data-value="1" onclick="selectElement(this)">Wenige verfügbar</span>';
            echo '<span data-value="2" onclick="selectElement(this)">Ausverkauft</span>';
          echo '</div>';
        echo '</div>';


        // Produktbild
        echo '<span class="file-info">Produktbild</span>';
        echo '<label class="file-input" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
          // Display preview image if possible
          if( isset($checkout->product()["product_fileID"]) &&! empty($checkout->product()["product_fileID"]) ) {
            echo '<input type="hidden" name="product_fileID" value="' . $checkout->product()["product_fileID"] . '" onchange="MediaHubSelected(this)">';
            echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $checkout->product()["product_fileID"] ) . '\')"></div>';
          }else {
            echo '<input type="hidden" name="product_fileID" onchange="MediaHubSelected(this)">';
          }
          echo '<div class="draganddrop">Klicken um auszuwählen</div>';
        echo '</label>';

        //Add submit button
        if( $disabled != "disabled" ) {
          echo  '<input type="submit" name="update" value="Update"/>';
        }

        //Close form
      echo  '</form>';
    break;
    default:
      // Check if we need to remove product
      if(isset($_POST["confirm"])) {
        // Get values
        $product_remove = new Checkout();
        $product_remove->product_id = $_POST["confirm"];
        $product_values = $product_remove->product();

        // Remove
        if( $product_remove->remove_product() ) {
          Action::success("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
        }else {
          Action::fail("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
        }
      }

      // List all products
      echo '<form action="' . $url_page . '" method="post" class="search">';
        echo  '<input type="text" name="s_product" value ="' . (isset(  $_POST["s_products"] ) ? $_POST["s_products"] : "") . '" placeholder="Produktname, Preis">';
        echo  '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
      echo  '</form>';

      // Define colors
      $colors = array(
        0 => array(
          "color" => "#2b4476",
          "title" => "Verfügbar",
        ),
        1 => array(
          "color" => "#7c2b51",
          "title" => "Wenige verfügbar",
        ),
        2 => array(
          "color" => "#e10c23",
          "title" => "Ausverkauft",
        ),
      );

      // Legend
      echo '<div class="legend">';
        foreach( $colors as $color ) {
          echo '<div class="legend-element">';
            echo '<div class="legend-button" style="background-color: ' . $color["color"] . '"></div>';
            echo $color["title"];
          echo '</div>';
        }
      echo '</div>';

      // Table
      echo  '<table class="rows">';
        //Headline
        $headline_names = array('Name', 'Preis', 'Aktion');

        //Start headline
        echo  '<tr>'; //Start row
        foreach( $headline_names as $name ){
          echo  '<th>' . $name . '</th>';
        }
        echo  '</tr>'; //Close row

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        foreach( $checkout->products( $offset, $steps,null ) as $products ) {
          echo  '<tr>';
            echo  '<td><div class="color" style="background-color: ' . $colors[($products["availability"] ?? 0)]["color"] . ';" title="' . $colors[($products["availability"] ?? 0)]["title"] . '"></div>' . $products["name"] . '</td>';
            echo  '<td>' . number_format(($products["price"] / 100), 2) . ' ' . $products["currency"] . '</td>';
            echo  '<td>';
              // color
              if(User::w_access_allowed($page, $current_user)) {
                  echo  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
                  echo  '<a href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '" title="Prdukt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
              }else {
                echo  '<a href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
              }
            echo  '</td>';
          echo  '</tr>';
        }

        // Menu requred
        echo  '<tr class="nav">';

          if( (count($checkout->products( ($offset + $steps), 1, null )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      </td>';
          }elseif (count($checkout->products( ($offset + $steps), 1 )) > 0) { // More pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }

        echo  '</tr>';

      echo  '</table>';

      if(User::w_access_allowed($page, $current_user)) {
        echo  '<a class="add" href="' . $url_page . '&add=product">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }

      echo  '</div>';


      // List default settings for checkout
    break;
  }
echo '</div>';
 ?>
