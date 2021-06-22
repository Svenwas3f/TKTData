<?php
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys
unset($action["pub"]);

// Start pub
$pub = new Pub();

// Get current pub
$accessable_pubs = $pub->accessable( $current_user );
$pub->pub = $_GET["pub"] ?? $accessable_pubs[0];

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
    case "add": //Product
      if(! empty( $_POST )) {
        if( $write_access ) {
          // Prepare post value
          $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);
          $_POST["pub_id"] = $pub->pub;

          if( $pub->add( pub::PRODUCTS_TABLE, $_POST ) ) {
            Action::success("Das Produkt konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href='" . $url_page . "&pub=" . $pub->pub . "&view_product=" . $pub->product_id . "' class='redirect'>Produkt verwalten</a></strong>");
          }else{
            Action::fail("Leider konnte das Produkt <strong>nicht</strong></b> erstellt werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      //Start form to edit, show user
      echo '<div class="pub">';
        echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" class="box-width">';
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
              foreach( $pub->sections() as $section ) {
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
      // Get name of pub
      $pub->product_id = $_GET["remove_product"] ?? null;

      // Generate message
      $info = "Möchtest du das Produkt <strong>" . $pub->product()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";

      // Display message
      Action::confirm($info, $_GET["remove_product"], "&pub=" . $pub->pub);
    break;
    case "view_product":
      // Set product id
      $pub->product_id = $_GET["view_product"] ?? null;

      // Update
      if(! empty($_POST)) {
        if( $write_access ) {
          // Define values
          $_POST["price"] = ($_POST["price"] ? 100 * $_POST["price"] : 0);

          if( $pub->update_product(  $_POST ) ) {
            Action::success("Das Produkt <strong>" . $pub->product()["name"] . " (#" . $pub->product_id . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
          }else {
            Action::fail("Das Produkt <strong>" . $pub->product()["name"] . " (#" . $pub->product_id . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      // Check if product is accessable
      if( $pub->product()["pub_id"] == $pub->pub ) {
        //Display right menu
        echo '<div class="right-sub-menu">';
          echo '<div class="right-menu-container">';
            if($pub->product_visibility()) {
              echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $pub->pub . ', ' . $pub->product_id . ')"><img src="' . $url . 'medias/icons/visibility-on.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
            }else {
              echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $pub->pub . ', ' . $pub->product_id . ')"><img src="' . $url . 'medias/icons/visibility-off.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
            }
          echo '</div>';

          echo '<div class="right-menu-container">';
            echo '<a class="right-menu-item"><img src="' . $url . 'medias/icons/availability.svg" alt="state" title="Produktstatus bestimmen"/></a>';
            echo '<div class="right-sub-menu-container">';
              // Define colors
              $availability = array(
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
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 0 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[0]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 0)">' . $availability[0]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 1 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[1]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 1)">' . $availability[1]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 2 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[2]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 2)">' . $availability[2]["title"] . '</a>';
            echo '</div>';
          echo '</div>';

          echo '<div class="right-menu-container">';
            echo '<a href="' . $url_page . '&remove_product=' . $pub->product_id . '" class="right-menu-item"><img src="' . $url . 'medias/icons/trash.svg" alt="Mail" title="Produkt entfernen"/></a>';
          echo '</div>';
        echo '</div>';

        // Generate html
        echo  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" class="right-menu">';
          if( $write_access === true ) {
            echo  '<h1>Produkt bearbeten</h1>';
          }else {
            echo  '<h1>Produkt ansehen</h1>';
          }

          //Produktname
          echo  '<label class="txt-input">';
            echo  '<input type="text" name="name" value="' . ($pub->product()["name"] ?? "") . '" ' . $disabled . ' required/>';
            echo  '<span class="placeholder">Produktname</span>';
          echo  '</label>';

          // Section
          echo '<div class="select" onclick="toggleOptions(this)">';
            echo '<input type="text" class="selectValue" name="section" ' . (isset($pub->product()["section"]) ? 'value="' . $pub->product()["section"] . '"' : "") . ' ' . $disabled . '>';
            echo '<span class="headline">' . (isset($pub->product()["section"]) ? $pub->product()["section"] : "Sektion") . '</span>';

            if( $write_access === true ) {
              echo '<div class="options">';
                foreach( $pub->sections() as $section ) {
                  echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
                }
                echo '<span onclick="event.stopPropagation()" class="option_add" >';
                  echo '<input type="text"/>';
                  echo '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
                echo '</span>';
              echo '</div>';
            }

          echo '</div>';

          //Währung
          echo  '<label class="txt-input">';
            echo  '<input type="text" name="currency" value="' . ($pub->product()["currency"] ?? DEFAULT_CURRENCY) . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" min="3" max="3" ' . $disabled . ' required/>';
            echo  '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
          echo  '</label>';

          //Preis
          echo  '<label class="txt-input">';
            echo  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($pub->product()["price"] ? number_format(($pub->product()["price"]/100), 2) :  "")  . '" ' . $disabled . ' required/>';
            echo  '<span class="placeholder">Preis</span>';
            echo  '<span class="unit">' . ($pub->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
          echo  '</label>';

          // Produktbild
          echo '<span class="file-info">Produktbild</span>';
          echo '<label class="file-input ' . $disabled . '" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
            // Display preview image if possible
            if( isset($pub->product()["product_fileID"]) &&! empty($pub->product()["product_fileID"]) ) {
              echo '<input type="hidden" name="product_fileID" value="' . $pub->product()["product_fileID"] . '" onchange="MediaHubSelected(this)">';
              echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->product()["product_fileID"] ) . '\')"></div>';
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
      } elseif ( is_null($pub->product()["pub_id"]) ) {
        // Banner global product
        echo '<div class="banner-global-product">';
          echo '&#9888; Dies ist ein globales Produkt und kann nur vom Administrator bearbeitet werden';
        echo '</div>';

        // Right menu
        echo '<div class="right-sub-menu">';
          echo '<div class="right-menu-container">';
          if($pub->product_visibility()) {
            echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $pub->pub . ', ' . $pub->product_id . ')"><img src="' . $url . 'medias/icons/visibility-on.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
          }else {
            echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $pub->pub . ', ' . $pub->product_id . ')"><img src="' . $url . 'medias/icons/visibility-off.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
          }
          echo '</div>';

          echo '<div class="right-menu-container">';
            echo '<a class="right-menu-item"><img src="' . $url . 'medias/icons/availability.svg" alt="state" title="Produktstatus bestimmen"/></a>';
            echo '<div class="right-sub-menu-container">';
              // Define colors
              $availability = array(
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
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 0 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[0]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 0)">' . $availability[0]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 1 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[1]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 1)">' . $availability[1]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($pub->product_availability() == 2 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[2]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $pub->pub . ', ' . $pub->product_id . ', 2)">' . $availability[2]["title"] . '</a>';
            echo '</div>';
          echo '</div>';
        echo '</div>';

        echo  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
          echo  '<h1>Produkt ansehen</h1>';
          //Produktname
          echo  '<label class="txt-input">';
            echo  '<input type="text" name="name" value="' . ($pub->product()["name"] ?? "") . '" disabled required/>';
            echo  '<span class="placeholder">Produktname</span>';
          echo  '</label>';

          // Section
          echo '<div class="select">';
            echo '<span class="headline">' . (isset($pub->product()["section"]) ? $pub->product()["section"] : "Sektion") . '</span>';
          echo '</div>';

          //Währung
          echo  '<label class="txt-input">';
            echo  '<input type="text" name="currency" value="' . ($pub->product()["currency"] ?? DEFAULT_CURRENCY) . '" min="3" max="3" disabled required/>';
            echo  '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
          echo  '</label>';

          //Preis
          echo  '<label class="txt-input">';
            echo  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($pub->product()["price"] ? number_format(($pub->product()["price"]/100), 2) :  "")  . '" disabled required/>';
            echo  '<span class="placeholder">Preis</span>';
            echo  '<span class="unit">' . ($pub->product()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
          echo  '</label>';

          // Produktbild
          echo '<span class="file-info">Produktbild</span>';
          echo '<label class="file-input disabled">';
            // Display preview image if possible
            if( isset($pub->product()["product_fileID"]) &&! empty($pub->product()["product_fileID"]) ) {
              echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->product()["product_fileID"] ) . '\')"></div>';
            }
            echo '<div class="draganddrop">Klicken um auszuwählen</div>';
          echo '</label>';


          //Close form
        echo  '</form>';
      } else {
        Action::fs_info("Du hast keinen Zugriff auf das Produkt (#" . $pub->product_id . ") " .  $pub->product()["name"]);
      }
    break;
    default:
      // Check if we need to remove product
      if(isset($_POST["confirm"])) {
        if( $write_access ) {
          // Get values
          $product_remove = new Pub();
          $product_remove->product_id = $_POST["confirm"];
          $product_values = $product_remove->product();

          // Remove
          if( $product_remove->remove_product() ) {
            Action::success("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> wurde <strong>erfolgreich</strong> gelöscht.");
          }else {
            Action::fail("Das Produkt <strong>" . $product_values["name"] . " (#" . $_POST["confirm"] . ")</strong> konnte <strong>nicht</strong> gelöscht werden.");
          }
        }else {
          Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
        }
      }

      // Fees info
      if( isset($pub->values()["payment_fee_absolute"]) || isset($pub->values()["payment_fee_percent"]) ) {
        echo '<div class="fee-info">';
          // fee info text
          echo 'Onlinezahlungen sind leider nicht ganz gratis, weshalb vom Verkauspreis jeweils <strong>' . ($pub->values()["payment_fee_absolute"] / 100) . ' ' . DEFAULT_CURRENCY . '</strong> und <strong>' . ($pub->values()["payment_fee_percent"] / 100) . '%</strong> abgegeben werden muss.';
        echo '</div>';
      }

      // List all products
      echo '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" class="search">';
        echo  '<input type="text" name="s_product" value ="' . ($_POST["s_product"] ?? "") . '" placeholder="Produktname, Preis">';
        echo  '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
      echo  '</form>';

      // Define colors
      $availability = array(
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
        foreach( $availability as $element ) {
          echo '<div class="legend-element">';
            echo '<div class="legend-button" style="background-color: ' . $element["color"] . '"></div>';
            echo $element["title"];
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

        // List general products
        foreach( $pub->products( $offset, $steps, ($_POST["s_product"] ?? null), true ) as $product ) {
          // set product id
          $pub->product_id = $product["id"];

          // Check if global product
          if( is_null($product["pub_id"]) ) {
            // Global product
            echo  '<tr class="global_product ' . (! $pub->product_visibility() ? "hidden_product" : "") . '" title="' . ($pub->product_visibility() ? "Ein globales Produkt kann hier nicht bearbeitet werden" : "Ein globales Produkt kann hier nicht bearbeitet werden. Dieses Produkt erscheint nicht in der Speise und Getränkekarte") . '">';
              echo  '<td><div class="color" style="background-color: ' . $availability[($pub->product_availability() ?? 0)]["color"] . ';" title="' . $availability[($pub->product_availability() ?? 0)]["title"] . '"></div>' . $product["name"] . '</td>';
              echo  '<td>' . number_format(($product["price"] / 100), 2) . ' ' . $product["currency"] . '</td>';
              echo  '<td>';
                echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $product["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
              echo  '</td>';
            echo  '</tr>';
          }else {
            // Product of pub
            echo  '<tr class="' . ($pub->product_visibility() ? "" : "hidden_product") . '" title="' . ($pub->product_visibility() ? "" : "Dieses Produkt erscheint nicht in der Speise und Getränkekarte") . '">';
              echo  '<td><div class="color" style="background-color: ' . $availability[($pub->product_availability() ?? 0)]["color"] . ';" title="' . $availability[($pub->product_availability() ?? 0)]["title"] . '"></div>' . $product["name"] . '</td>';
              echo  '<td>' . number_format(($product["price"] / 100), 2) . ' ' . $product["currency"] . '</td>';
              echo  '<td>';
                if( $write_access === true ) {
                    echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $product["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
                    echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&remove_product=' . urlencode( $product["id"] ) . '" title="Produkt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
                }else {
                  echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $product["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
                }
              echo  '</td>';
            echo  '</tr>';
          }

          // reset product id
          $pub->product_id = null;
        }

        // Menu requred
        echo  '<tr class="nav">';

          if( (count($pub->products( ($offset + $steps), 1, ($_POST["s_product"] ?? null), true )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                      </td>';
          }elseif (count($pub->products( ($offset + $steps), 1, ($_POST["s_product"] ?? null), true )) > 0) { // More pages accessable
            echo  '<td colspan="' . count( $headline_names ) . '">
                        <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                      </td>';
          }

        echo  '</tr>';

      echo  '</table>';

      if( $write_access === true ) {
        echo  '<a class="add" href="' . $url_page . '&pub=' . $pub->pub . '&add=product">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }

      echo  '</div>';


      // List default settings for pub
    break;
  }
echo '</div>';
 ?>
