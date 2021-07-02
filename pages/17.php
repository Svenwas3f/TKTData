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

// Start prodcut
$product = new Product();
$product->pub = $pub->pub;

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

          if( $product->add( $_POST ) ) {
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
            echo '<span class="unit">' . DEFAULT_CURRENCY . '</span>';
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
    break;
    case "remove_product": // Product
      // Get name of pub
      $product->product_id = $_GET["remove_product"] ?? null;

      // Generate message
      $info = "Möchtest du das Produkt <strong>" . $product->values()["name"] . " (#" . $_GET["remove_product"] . ")</strong>  wirklich löschen?";

      // Display message
      Action::confirm($info, $_GET["remove_product"], "&pub=" . $product->pub);
    break;
    case "view_product":
      // Set product id
      $product->product_id = $_GET["view_product"] ?? null;

      // Update
      if(! empty($_POST)) {
        if( $write_access ) {
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

      // Check if product is accessable
      if( $product->values()["pub_id"] == $pub->pub ) {
        // Display top return button
        echo '<div class="top-nav border-none">';
          echo '<a href="Javascript:history.back()" title="Zur vorherigen Seite zurück"><img src="' . $url . 'medias/icons/history-back.svg"></a>';
        echo '</div>';

        //Display right menu
        echo '<div class="right-sub-menu">';
          echo '<div class="right-menu-container">';
            if($product->visibility()) {
              echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"><img src="' . $url . 'medias/icons/visibility-on.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
            }else {
              echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"><img src="' . $url . 'medias/icons/visibility-off.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
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
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 0 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[0]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 0)">' . $availability[0]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 1 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[1]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 1)">' . $availability[1]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 2 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[2]["color"] . '" onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 2)">' . $availability[2]["title"] . '</a>';
            echo '</div>';
          echo '</div>';

          echo '<div class="right-menu-container">';
            echo '<a href="' . $url_page . '&remove_product=' . $product->product_id . '" class="right-menu-item"><img src="' . $url . 'medias/icons/trash.svg" alt="Mail" title="Produkt entfernen"/></a>';
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
            echo  '<input type="text" name="name" value="' . ($product->values()["name"] ?? "") . '" ' . $disabled . ' required/>';
            echo  '<span class="placeholder">Produktname</span>';
          echo  '</label>';

          // Section
          echo '<div class="select" onclick="toggleOptions(this)">';
            echo '<input type="text" class="selectValue" name="section" ' . (isset($product->values()["section"]) ? 'value="' . $product->values()["section"] . '"' : "") . ' ' . $disabled . '>';
            echo '<span class="headline">' . (isset($product->values()["section"]) ? $product->values()["section"] : "Sektion") . '</span>';

            if( $write_access === true ) {
              echo '<div class="options">';
                foreach( $product->sections() as $section ) {
                  echo '<span data-value="' . $section["section"] . '" onclick="selectElement(this)">' . $section["section"] . '</span>';
                }
                echo '<span onclick="event.stopPropagation()" class="option_add" >';
                  echo '<input type="text"/>';
                  echo '<span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">GO</span>';
                echo '</span>';
              echo '</div>';
            }

          echo '</div>';

          //Preis
          echo  '<label class="txt-input">';
            echo  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  "")  . '" ' . $disabled . ' required/>';
            echo  '<span class="placeholder">Preis</span>';
            echo  '<span class="unit">' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
          echo  '</label>';

          // Produktbild
          echo '<span class="file-info">Produktbild</span>';
          echo '<label class="file-input ' . $disabled . '" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'product_fileID\' )"' ) . '>';
            // Display preview image if possible
            if( isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"]) ) {
              echo '<input type="hidden" name="product_fileID" value="' . $product->values()["product_fileID"] . '" onchange="MediaHubSelected(this)">';
              echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $product->values()["product_fileID"] ) . '\')"></div>';
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
      } elseif ( is_null($product->values()["pub_id"]) ) {
        // Banner global product
        echo '<div class="banner-global-product">';
          echo '&#9888; Dies ist ein globales Produkt und kann nur vom Administrator bearbeitet werden';
        echo '</div>';

        // Display top return button
        echo '<div class="top-nav border-none">';
          echo '<a href="Javascript:history.back()" title="Zur vorherigen Seite zurück"><img src="' . $url . 'medias/icons/history-back.svg"></a>';
        echo '</div>';

        // Right menu
        echo '<div class="right-sub-menu">';
          echo '<div class="right-menu-container">';
          if($product->visibility()) {
            echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"><img src="' . $url . 'medias/icons/visibility-on.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
          }else {
            echo '<a class="right-menu-item" onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"><img src="' . $url . 'medias/icons/visibility-off.svg" alt="Visibility" title="Sichtbarkeit wechseln"/></a>';
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
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 0 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[0]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 0)">' . $availability[0]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 1 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[1]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 1)">' . $availability[1]["title"] . '</a>';
              echo '<a class="right-sub-menu-item ' . ($product->availability() == 2 ? "current" : "") . '" style="border-left: 5px solid ' . $availability[2]["color"] . '" onclick="pub_prdocut_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 2)">' . $availability[2]["title"] . '</a>';
            echo '</div>';
          echo '</div>';
        echo '</div>';

        echo  '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
          echo  '<h1>Produkt ansehen</h1>';
          //Produktname
          echo  '<label class="txt-input">';
            echo  '<input type="text" name="name" value="' . ($product->values()["name"] ?? "") . '" disabled required/>';
            echo  '<span class="placeholder">Produktname</span>';
          echo  '</label>';

          // Section
          echo '<div class="select">';
            echo '<span class="headline">' . (isset($product->values()["section"]) ? $product->values()["section"] : "Sektion") . '</span>';
          echo '</div>';

          //Preis
          echo  '<label class="txt-input">';
            echo  '<input type="text type="number" step="0.05" min="0" name="price" value="' . ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  "")  . '" disabled required/>';
            echo  '<span class="placeholder">Preis</span>';
            echo  '<span class="unit">' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
          echo  '</label>';

          // Produktbild
          echo '<span class="file-info">Produktbild</span>';
          echo '<label class="file-input disabled">';
            // Display preview image if possible
            if( isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"]) ) {
              echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $product->values()["product_fileID"] ) . '\')"></div>';
            }
            echo '<div class="draganddrop">Klicken um auszuwählen</div>';
          echo '</label>';


          //Close form
        echo  '</form>';
      } else {
        Action::fs_info("Du hast keinen Zugriff auf das Produkt (#" . $products->product_id . ") " .  $product->values()["name"]);
      }
    break;
    default:
      // Check if we need to remove product
      if(isset($_POST["confirm"])) {
        if( $write_access ) {
          // Get values
          // $product_remove = new Pub();
          // $product_remove->product_id = $_POST["confirm"];
          // $product_values = $product_remove->product();
          $product->product_id = $_POST["confirm"];
          $product_values = $product->values();

          // Remove
          if( $product->remove() ) {
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

      // Top navigation
      echo '<div class="top-nav border-none">';
        echo '<a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . $_GET["pub"] : "") . '&list=products" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "selected" ) . '" title="Produkte ansehen">PRODUKTE</a>';
        echo '<a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . $_GET["pub"] : "") . '&list=settings" class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "settings" ? "selected" : "") : "") . '" title="Wirtschaftseinstellungen vornehmen">EINSTELLUNGEN</a>';
      echo '</div>';

      // Select page
      if( ($_GET["list"] ?? "") == "settings") {
        // Update
        if(isset($_POST["update"])) {
          if( $write_access ) {
            // Prepare values
            $valid_keys = array("logo_fileID", "description", "background_fileID", "tip");
            $checked_values = array_intersect_key($_POST, array_flip($valid_keys));

            // Remove
            if( $pub->update( $checked_values ) ) {
              Action::success("Die Wirtschaft <strong>" . $pub->values()["name"] . " (#" . $pub->pub . ")</strong> wurde <strong>erfolgreich</strong> überarbeitet.");
            }else {
              Action::fail("Die Wirtschaft <strong>" . $pub->values()["name"] . " (#" . $pub->pub . ")</strong> konnte <strong>nicht</strong> überarbeitet werden.");
            }
          }else {
            Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
          }
        }

        echo '<div class="right-sub-menu">';
          echo '<div class="right-menu-container">';
            echo '<a class="right-menu-item" href="' . $url . 'pdf/menu/?pub=' . $pub->pub . '" target="_blank"><img src="' . $url . 'medias/icons/pdf.svg" alt="PDF" title="Speise und Getränkekarte als PDF ansehen"/></a>';
            if($pub->values()["tip"] == 1) {
              echo '<a class="right-menu-item" onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"><img src="' . $url . 'medias/icons/tip-money-on.svg" alt="Visibility" title="Trinkgeld anzeigen/verbergen"/></a>';
            }else {
              echo '<a class="right-menu-item" onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"><img src="' . $url . 'medias/icons/tip-money-off.svg" alt="Visibility" title="Trinkgeld anzeigen/verbergen"/></a>';
            }
          echo '</div>';
        echo '</div>';

        // List settings
        echo '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" class="right-menu">';
          //Beschreibung
          echo '<div class="box">';
            echo '<p>Details</p>';
            echo '<label class="txt-input">';
              echo '<textarea name="description" ' . $disabled .'/>' . $pub->values()["description"] . '</textarea>';
              echo '<span class="placeholder">Beschreibung</span>';
            echo '</label>';
          echo '</div>';

          // Images
          echo '<div class="box">';
            echo '<p>Bilder</p>';
            echo '<span class="file-info">Logo</span>';
            echo '<label class="file-input ' . $disabled .'" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'logo_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($pub->values()["logo_fileID"]) &&! empty($pub->values()["logo_fileID"]) ) {
                echo '<input type="hidden" name="logo_fileID" value="' . $pub->values()["logo_fileID"] . '" onchange="MediaHubSelected(this)">';
                echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->values()["logo_fileID"] ) . '\')"></div>';
              }else {
                echo '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
                echo '<input type="hidden" name="logo_fileID" onchange="MediaHubSelected(this)">';
              }
              echo '<div class="draganddrop">Klicken um auszuwählen</div>';
            echo '</label>';

            echo '<span class="file-info">Hintergrundbild</span>';
            echo '<label class="file-input ' . $disabled .'" ' . ( $disabled == "disabled" ? "" : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'background_fileID\' )"' ) . '>';
              // Display preview image if possible
              if( isset($pub->values()["background_fileID"]) &&! empty($pub->values()["background_fileID"]) ) {
                echo '<input type="hidden" name="background_fileID" value="' . $pub->values()["background_fileID"] . '" onchange="MediaHubSelected(this)">';
                echo '<div class="preview-image" style="background-image: url(\'' . MediaHub::getUrl( $pub->values()["background_fileID"] ) . '\')"></div>';
              }else {
                echo '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
                echo '<input type="hidden" name="background_fileID" onchange="MediaHubSelected(this)">';
              }
              echo '<div class="draganddrop">Klicken um auszuwählen</div>';
            echo '</label>';
          echo '</div>';

          //Add submit button
          echo '<input type="submit" name="update" value="Update" ' . $disabled .'/>';


        echo '</form>';
      }else {
        //Display form
        echo '<form action="' . $url . '" method="get" class="search">';
          echo '<input type="hidden" name="id" value="' . $mainPage . '" />';
          echo '<input type="hidden" name="sub" value="' . $page . '" />';
          echo '<input type="text" name="s" value ="' . (isset( $_GET["s"] ) ? $_GET["s"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
          echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
        echo '</form>';

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
          foreach( $product->all( $offset, $steps, ($_GET["s"] ?? null), true ) as $values ) {
            // set product id
            $product->product_id = $values["id"];

            // Check if global product
            if( is_null($values["pub_id"]) ) {
              // Global product
              echo  '<tr class="global_product ' . (! $product->visibility() ? "hidden_product" : "") . '" title="' . ($product->visibility() ? "Ein globales Produkt kann hier nicht bearbeitet werden" : "Ein globales Produkt kann hier nicht bearbeitet werden. Dieses Produkt erscheint nicht in der Speise und Getränkekarte") . '">';
                echo  '<td><div class="color" style="background-color: ' . $availability[($product->availability() ?? 0)]["color"] . ';" title="' . $availability[($product->availability() ?? 0)]["title"] . '"></div>' . $values["name"] . '</td>';
                echo  '<td>' . number_format(($values["price"] / 100), 2) . ' ' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY) . '</td>';
                echo  '<td>';
                  echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $values["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
                echo  '</td>';
              echo  '</tr>';
            }else {
              // Product of pub
              echo  '<tr class="' . ($product->visibility() ? "" : "hidden_product") . '" title="' . ($product->visibility() ? "" : "Dieses Produkt erscheint nicht in der Speise und Getränkekarte") . '">';
                echo  '<td><div class="color" style="background-color: ' . $availability[($product->availability() ?? 0)]["color"] . ';" title="' . $availability[($product->availability() ?? 0)]["title"] . '"></div>' . $values["name"] . '</td>';
                echo  '<td>' . number_format(($values["price"] / 100), 2) . ' ' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY) . '</td>';
                echo  '<td>';
                  if( $write_access === true ) {
                      echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $values["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" />';
                      echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&remove_product=' . urlencode( $values["id"] ) . '" title="Produkt entfernen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
                  }else {
                    echo  '<a href="' . $url_page . '&pub=' . urlencode( $pub->pub ) . '&view_product=' . urlencode( $values["id"] ) . '" title="Produktdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" />';
                  }
                echo  '</td>';
              echo  '</tr>';
            }

            // reset product id
            $pub->product_id = null;
          }

          // Menu requred
          echo  '<tr class="nav">';

            if( (count($product->all( ($offset + $steps), 1, ($_GET["s"] ?? null), true )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
            }elseif (count($product->all( ($offset + $steps), 1, ($_GET["s"] ?? null), true )) > 0) { // More pages accessable
              echo  '<td colspan="' . count( $headline_names ) . '">
                          <a href="' . $url_page . (isset($_GET["pub"]) ? "&pub=" . urlencode($_GET["pub"]) : "") . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                          '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
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
      }
    break;
  }
echo '</div>';
 ?>
