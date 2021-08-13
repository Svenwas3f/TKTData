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

// Message if user has no access to this pub
if( $write_access === false && $read_access === false ) {
  Action::fs_info( Language::string(60, array(
    '%pub%' => $pub->pub,
    '%name%' => $pub->values()["name"],
  ),), Language::string(61), $url_page);
  return;
}

echo '<div class="pub">';
  //////////////////////////
  // List accessable pubs
  //////////////////////////
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
            Action::success( Language::string( 62, array(
              '%url_page%' => $url_page,
              '%pub%' => $pub->pub,
              '%product%' => $product->product_id,
            ),) );
          }else{
            Action::fail( Language::string(63) );
          }
        }else {
          Action::fail( Language::string(64) );
        }
      }

      // Start form
      $form = new HTML('form', array(
        'action' => $url . '?' . $_SERVER["QUERY_STRING"],
        'method' => 'post',
        'additional' => 'class="right-menu" style="width: 100%; max-width: 750px;"',
      ));

      $form->customHTML('<h1>' . Language::string(48) . '</h1>');

      $form->addElement(
        array(
          'type' => 'text',
          'name' => 'name',
          'placeholder' => Language::string(38),
          'disabled' => ! $write_access,
          'required' => true,
        ),
      );

      // Generate options
      $options = array();

      foreach($product->sections() as $section) {
        $options[$section["section"]] = $section["section"];
      }

      $form->addElement(
        array(
          'type' => 'select',
          'name' => 'section',
          'options' => $options,
          'headline' => Language::string(39),
          'custom_options' => '<span onclick="event.stopPropagation()" class="option_add" ><input type="text"/><span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">' . Language::string(40) . '</span></span>',
          'disabled' => ! $write_access,
        ),
      );

      $form->addElement(
        array(
          'type' => 'number',
          'name' => 'price',
          'placeholder' => Language::string(41),
          'unit' => ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
          'input_attributes' => 'step="0.01" value="0.00"',
          'disabled' => ! $write_access,
          'required' => true,
        ),
      );

      $form->addElement(
        array(
          'type' => 'image',
          'name' => 'product_fileID',
          'headline' => Language::string(42),
          'select_info' => Language::string(43),
          'preview_image' => ((isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"])) ? MediaHub::getUrl( $product->values()["product_fileID"] ) : null),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->addElement(
        array(
          'type' => 'button',
          'name' => 'create',
          'value' => Language::string(49),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->prompt();
    break;
    case "remove_product": // Product
      // Get name of pub
      $product->product_id = $_GET["remove_product"] ?? null;

      // Generate message
      $info = Language::string( 50, array(
        '%name%' => $product->values()["name"],
        '%product%' => $_GET["remove_product"],
      ), );

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
            Action::success( Language::string(65, array(
              '%name%' => $product->values()["name"],
              '%product%' => $product->product_id,
            ),));
          }else {
            if( $product->update(  $_POST ) ) {
              Action::success( Language::string(66, array(
                '%name%' => $product->values()["name"],
                '%product%' => $product->product_id,
              ),));
            }
          }
        }else {
          Action::fail( Language::string(67) );
        }
      }

      // Check if product is accessable
      if( $product->values()["pub_id"] == $pub->pub ) {
        // Display top return button
        $topNav = new HTML('top-nav', array(
          'classes' => 'border-none',
        ));

        $topNav->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
            'link' => 'Javascript:history.back()',
            'additional' => 'title="' . Language::string(46) . '"',
          ),
        );

        // Display right menu
        $rightMenu = new HTML('right-menu');

        // Generate image url
        $visibility_img = $url . 'medias/icons/visibility-' . ($product->visibility() ? 'on' : 'off') . '.svg';

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $visibility_img . '" alt="' . Language::string(30) . '" title="' . Language::string(31) . '"/>',
            'additional_item' => 'onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"',
          ),
        );

        // Define colors
        $availability = array(
          0 => array(
            "color" => "var(--product-available)",
            "title" => Language::string(18),
          ),
          1 => array(
            "color" => "var(--product-less-available)",
            "title" => Language::string(19),
          ),
          2 => array(
            "color" => "var(--product-sold)",
            "title" => Language::string(20),
          ),
        );

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/availability.svg" alt="' . Language::string(33) . '" title="' . Language::string(34) . '"/>',
            'dropdown' => array(
              array(
                'context' => $availability[0]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[0]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 0)"',
                'classes' => ($product->availability() == 0 ? "current" : ""),
              ),
              array(
                'context' => $availability[1]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[1]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 1)"',
                'classes' => ($product->availability() == 1 ? "current" : ""),
              ),
              array(
                'context' => $availability[2]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[2]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 2)"',
                'classes' => ($product->availability() == 2 ? "current" : ""),
              ),
            ),
          ),
        );

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/trash.svg" alt="' . Language::string(34) . '" title="' . Language::string(35) . '"/>',
            'additional_item' => 'href="' . $url_page . '&pub=' . $pub->pub .'&remove_product=' . $product->product_id . '"',
          ),
        );

        // Start form
        $form = new HTML('form', array(
          'action' => $url . '?' . $_SERVER["QUERY_STRING"],
          'method' => 'post',
          'additional' => 'class="right-menu" style="width: 100%; max-width: 750px;"',
        ));

        if( $write_access === true ) {
          $form->customHTML('<h1>' . Language::string(36) . '</h1>');
        }else {
          $form->customHTML('<h1>' . Language::string(37) . '</h1>');
        }

        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'name',
            'placeholder' => Language::string(38),
            'value' => ($product->values()["name"] ?? ""),
            'disabled' => ! $write_access,
            'required' => true,
          ),
        );

        // Generate options
        $options = array();

        foreach($product->sections() as $section) {
          $options[$section["section"]] = $section["section"];
        }

        $form->addElement(
          array(
            'type' => 'select',
            'name' => 'section',
            'value' => (isset($product->values()["section"]) ? $product->values()["section"] : ''),
            'options' => $options,
            'headline' => (isset($product->values()["section"]) ? $product->values()["section"] : Language::string(39)),
            'custom_options' => '<span onclick="event.stopPropagation()" class="option_add" ><input type="text"/><span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">' . Language::string(40) . '</span></span>',
            'disabled' => ! $write_access,
          ),
        );

        $form->addElement(
          array(
            'type' => 'number',
            'name' => 'price',
            'placeholder' => Language::string(41),
            'value' => ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  ""),
            'unit' => ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
            'input_attributes' => 'step="0.01" value="0.00"',
            'disabled' => ! $write_access,
            'required' => true,
          ),
        );

        $form->addElement(
          array(
            'type' => 'image',
            'name' => 'product_fileID',
            'headline' => Language::string(42),
            'select_info' => Language::string(43),
            'preview_image' => ((isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"])) ? MediaHub::getUrl( $product->values()["product_fileID"] ) : null),
            'value' => ($product->values()["product_fileID"] ?? null),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        $form->addElement(
          array(
            'type' => 'button',
            'name' => 'update',
            'value' => Language::string(44),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );


        $topNav->prompt();
        $rightMenu->prompt();
        $form->prompt();
      } elseif ( is_null($product->values()["pub_id"]) ) {
        // Banner global product
        echo '<div class="banner-global-product">';
          echo Language::string(45);
        echo '</div>';

        // Display top return button
        $topNav = new HTML('top-nav', array(
          'classes' => 'border-none',
        ));

        $topNav->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
            'link' => 'Javascript:history.back()',
            'additional' => 'title="' . Language::string(46) . '"',
          ),
        );

        // Display right menu
        $rightMenu = new HTML('right-menu');

        // Generate image url
        $visibility_img = $url . 'medias/icons/visibility-' . ($product->visibility() ? 'on' : 'off') . '.svg';

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $visibility_img . '" alt="' . Language::string(30) . '" title="' . Language::string(31) . '"/>',
            'additional_item' => 'onclick="pub_product_visiliity_toggle(this, ' . $product->pub . ', ' . $product->product_id . ')"',
          ),
        );

        // Define colors
        $availability = array(
          0 => array(
            "color" => "var(--product-available)",
            "title" => Language::string(18),
          ),
          1 => array(
            "color" => "var(--product-less-available)",
            "title" => Language::string(19),
          ),
          2 => array(
            "color" => "var(--product-sold)",
            "title" => Language::string(20),
          ),
        );

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/availability.svg" alt="' . Language::string(33) . '" title="' . Language::string(34) . '"/>',
            'dropdown' => array(
              array(
                'context' => $availability[0]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[0]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 0)"',
                'classes' => ($product->availability() == 0 ? "current" : ""),
              ),
              array(
                'context' => $availability[1]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[1]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 1)"',
                'classes' => ($product->availability() == 1 ? "current" : ""),
              ),
              array(
                'context' => $availability[2]["title"],
                'additional' => 'style="border-left: 5px solid ' . $availability[2]["color"] . '"
                                  onclick="pub_product_availability(this.parentNode, ' . $product->pub . ', ' . $product->product_id . ', 2)"',
                'classes' => ($product->availability() == 2 ? "current" : ""),
              ),
            ),
          ),
        );

        // Start form
        $form = new HTML('form', array(
          'action' => $url . '?' . $_SERVER["QUERY_STRING"],
          'method' => 'post',
          'additional' => 'class="right-menu" style="width: 100%; max-width: 750px;"',
        ));

        $form->customHTML('<h1>' . Language::string(37) . '</h1>');

        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'name',
            'placeholder' => Language::string(38),
            'value' => ($product->values()["name"] ?? ""),
            'disabled' => true,
          ),
        );

        $form->addElement(
          array(
            'type' => 'select',
            'name' => 'section',
            'headline' => (isset($product->values()["section"]) ? $product->values()["section"] : Language::string(39)),
            'value' => (isset($product->values()["section"]) ? $product->values()["section"] : ''),
            'options' => array(),
            'disabled' => true,
          ),
        );

        $form->addElement(
          array(
            'type' => 'number',
            'name' => 'price',
            'placeholder' => Language::string(41),
            'value' => ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  ""),
            'unit' => ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
            'input_attributes' => 'step="0.01" value="0.00"',
            'disabled' => true,
          ),
        );

        $form->addElement(
          array(
            'type' => 'image',
            'name' => 'product_fileID',
            'headline' => Language::string(42),
            'select_info' => Language::string(43),
            'preview_image' => ((isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"])) ? MediaHub::getUrl( $product->values()["product_fileID"] ) : null),
            'value' => ($product->values()["product_fileID"] ?? null),
            'disabled' => true,
          ),
        );


        $topNav->prompt();
        $rightMenu->prompt();
        $form->prompt();
      } else {
        Action::fs_info( Language::string( 47, array(
          '%product%' => $products->product_id,
          '%name%' => $product->values()["name"],
        ) ) );
      }
    break;
    default:
      // Check if we need to remove product
      if(isset($_POST["confirm"])) {
        if( $write_access ) {
          // Get values
          $product->product_id = $_POST["confirm"];
          $product_values = $product->values();

          // Remove
          if( $product->remove() ) {
            Action::success( Language::string( 68, array(
              '%name%' => $product_values["name"],
              '%product%' => $_POST["confirm"],
            ),) );
          }else {
            Action::fail( Language::string( 69, array(
              '%name%' => $product_values["name"],
              '%product%' => $_POST["confirm"],
            ),) );
          }
        }else {
          Action::fail( Language::string(70) );
        }
      }

      // Fees info
      if( (isset($pub->values()["payment_fee_absolute"]) && !empty($pub->values()["payment_fee_absolute"]))  ||
          (isset($pub->values()["payment_fee_percent"]) && !empty($pub->values()["payment_fee_percent"])) ) {
        echo '<div class="fee-info">';
          // fee info text
          echo Language::string( 0,
            array(
              '%fee_absolute%' => number_format($pub->values()["payment_fee_absolute"] / 100, 2),
              '%currency%' => $pub->values()["currency"] ?? DEFAULT_CURRENCY,
              '%fee_percent%' => ($pub->values()["payment_fee_percent"] / 100),
            ),
          );
        echo '</div>';

      }

      // Start top nav
      $topNav = new HTML('top-nav', array(
        'classes' => 'border-none',
      ));

      $topNav->addElement(
        array(
          'context' => Language::string(1),
          'link' => $url_page . (isset($_GET["pub"]) ? "&pub=" . $_GET["pub"] : "") . '&list=products',
          'additional' => (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? 'class="selected"' : "") : "") .
                          ' title="' . Language::string(2) . '"',
        ),
      );

      $topNav->addElement(
        array(
          'context' => Language::string(3),
          'link' => $url_page . (isset($_GET["pub"]) ? "&pub=" . $_GET["pub"] : "") . '&list=settings',
          'additional' => (isset( $_GET["list"] ) ? ($_GET["list"] == "settings" ? 'class="selected"' : "") : "") .
                          ' title="' . Language::string(4) . '"',
        ),
      );


      $topNav->prompt();

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
              Action::success( Language::string( 71, array(
                '%name%' => $pub->values()["name"],
                '%pub%' => $pub->pub,
              ),) );
            }else {
              Action::fail( Language::string( 72, array(
                '%name%' => $pub->values()["name"],
                '%pub%' => $pub->pub,
              ),) );
            }
          }else {
            Action::fail( Language::string( 73 ) );
          }
        }

        // Start right menu
        $rightMenu = new HTML('right-menu');

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/pdf.svg" alt="' . Language::string(5) . '" title="' . Language::string(6) . '"/>',
            'additional_item' => 'href="' . $url . 'pdf/menu/?pub=' . $pub->pub . '" target="_blank"',
          ),
        );

        // Generate image url
        $tip_img = $url . 'medias/icons/tip-money-' . (($pub->values()["tip"] == 1) ? 'on' : 'off') . '.svg';

        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $tip_img . '" alt="' . Language::string(7) . '" title="' . Language::string(8) . '"/>',
            'additional_item' => 'onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"',
          ),
        );

        // List settings
        $form = new HTML('form', array(
          'action' => $url . '?' . $_SERVER["QUERY_STRING"],
          'method' => 'post',
          'additional' => 'class="right-menu"',
        ));

        $form->customHTML('<div class="box">');

          $form->customHTML('<p>' . Language::string(9) . '</p>');
          $form->addElement(
            array(
              'type' => 'textarea',
              'name' => 'description',
              'value' => $pub->values()["description"] ?? '',
              'placeholder' => Language::string(10),
              'disabled' => ! $write_access,
            ),
          );

        $form->customHTML('</div>');
        $form->customHTML('<div class="box">');

          $form->customHTML('<p>' . Language::string(11) .'</p>');
          $form->addElement(
            array(
              'type' => 'image',
              'headline' => Language::string(12),
              'name' => 'logo_fileID',
              'value' => ($pub->values()["logo_fileID"] ?? null),
              'select_info' => Language::string(13),
              'preview_image' => ((isset($pub->values()["logo_fileID"]) &&! empty($pub->values()["logo_fileID"])) ? MediaHub::getUrl( $pub->values()["logo_fileID"] ) : null),
              'disabled' => ! $write_access,
            ),
          );

          $form->addElement(
            array(
              'type' => 'image',
              'headline' => Language::string(14),
              'name' => 'background_fileID',
              'select_info' => Language::string(15),
              'value' => ($pub->values()["background_fileID"] ?? null),
              'preview_image' => ((isset($pub->values()["background_fileID"]) &&! empty($pub->values()["background_fileID"])) ? MediaHub::getUrl( $pub->values()["background_fileID"] ) : null),
              'disabled' => ! $write_access,
            ),
          );

        $form->customHTML('</div>');

        $form->addelement(
          array(
            'type' => 'button',
            'name' => 'update',
            'value' => Language::string(16),
            'disabled' => ! $write_access,
          ),
        );

        $rightMenu->prompt();
        $form->prompt();
      }else {
        // Start searchbar
        $searchbar = new HTML('searchbar', array(
          'action' => $url,
          'method' => 'get',
          'placeholder' => Language::string(17),
          's' => (isset( $_GET["s"] ) ? $_GET["s"] : ""),
        ));

        $searchbar->addElement( '<input type="hidden" name="id" value="' . $mainPage . '" />' );
        $searchbar->addElement( '<input type="hidden" name="sub" value="' . $page . '" />' );
        $searchbar->addElement( '<input type="hidden" name="list" value="' . ($_GET["list"] ?? '') . '" />' );

        // Define colors
        $availability = array(
          0 => array(
            "color" => "var(--product-available)",
            "title" => Language::string(18),
          ),
          1 => array(
            "color" => "var(--product-less-available)",
            "title" => Language::string(19),
          ),
          2 => array(
            "color" => "var(--product-sold)",
            "title" => Language::string(20),
          ),
        );

        //Start Legend
        $legend = new HTML('legend');

        foreach( $availability as $values ) {
          $legend->addElement( array(
            'bcolor' => $values['color'],
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
                  'context' => Language::string(21),
                ),
                array(
                  'context' => Language::string(22),
                ),
                array(
                  'context' => Language::string(23),
                ),
              ),
            ),
          ),
        );

        // Set offset and steps
        $steps = 20;
        $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

        // List general products
        foreach( $product->all( $offset, $steps, ($_GET["s"] ?? null), true) as $values ) {
          // set product id
          $product->product_id = $values["id"];

          // Generate action
          if( $write_access && is_null($values["pub_id"]) == false ) {
              $actions = '<a
                            href="' . $url_page . '&pub=' . $pub->pub . '&view_product=' . urlencode( $values["id"] ) . '"
                            title="' . Language::string(24) . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
              $actions .= '<a
                            href="' . $url_page . '&pub=' . $pub->pub . '&remove_product=' . urlencode( $values["id"] ) . '"
                            title="' . Language::string(25) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
          }else {
            $actions = '<a
                          href="' . $url_page . '&pub=' . $pub->pub . '&view_product=' . urlencode( $values["id"] ) . '"
                          title="' . Language::string(24) . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
          }

          // Check if global product
          if( is_null($values["pub_id"]) ) {
            // Global product
            $table->addElement(
              array(
                'row' => array(
                  'items' => array(
                    array(
                      'context' => '<div class="color" style="background-color: ' . $availability[($product->availability() ?? 0)]["color"] . ';" title="' . $availability[($product->availability() ?? 0)]["title"] . '"></div>' . $values["name"],
                    ),
                    array(
                      'context' => number_format(($values["price"] / 100), 2) . ' ' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
                    ),
                    array(
                      'context' => ($actions ?? ''),
                    ),
                  ),
                  'additional' => 'class="global_product ' . (! $product->visibility() ? "hidden_product" : "") . '" ' .
                                  'title="' . ($product->visibility() ? Language::string(26) : Language::string(26) . ' ' . Language::string(27)) . '"',
                ),
              ),
            );
          }else {
            $table->addElement(
              array(
                'row' => array(
                  'items' => array(
                    array(
                      'context' => '<div class="color" style="background-color: ' . $availability[($product->availability() ?? 0)]["color"] . ';" title="' . $availability[($product->availability() ?? 0)]["title"] . '"></div>' . $values["name"],
                    ),
                    array(
                      'context' => number_format(($values["price"] / 100), 2) . ' ' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
                    ),
                    array(
                      'context' => ($actions ?? ''),
                    ),
                  ),
                  'additional' => 'class="' . (! $product->visibility() ? "hidden_product" : "") . '" ' .
                                  'title="' . ($product->visibility() ? '' : Language::string(27)) . '"',
                ),
              ),
            );
          }

          // reset product id
          $pub->product_id = null;
        }

        // Footer
        $last = '<a href="' .
                  $url_page .
                  ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                  '&pub=' . $pub->pub .
                  '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '"
                  style="float: left;">' . Language::string(28) . '</a>';
        $next = '<a href="' .
                  $url_page .
                  ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                  '&pub=' . $pub->pub .
                  '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
                  style="float: right;">' . Language::string(29) . '</a>';

        if( (count($product->all( ($offset + $steps), $steps, ($_GET["s"] ?? null), true)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
        }elseif (count($product->all( ($offset + $steps), $steps, ($_GET["s"] ?? null), true)) > 0) { // More pages accessable
          $table->addElement(
            array(
              'footer' => array(
                'context' => $next,
              ),
            ),
          );
        }

        $searchbar->prompt();
        $legend->prompt();
        $table->prompt();

        if( $write_access === true ) {
          echo  '<a class="add" href="' . $url_page . '&pub=' . $pub->pub . '&add=product">
            <span class="horizontal"></span>
            <span class="vertical"></span>
          </a>';
        }
      }
    break;
  }
echo '</div>';
 ?>
