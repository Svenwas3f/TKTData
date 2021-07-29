<?php
function display_pubs ( $search_value = null ) {
  //Require variables
  global $url, $url_page, $page, $mainPage, $current_user;

  // Start searchbar
  $searchbar = new HTML('searchbar', array(
    'action' => $url,
    'method' => 'get',
    'placeholder' => Language::string(0),
    's' => $search_value,
  ));

  $searchbar->addElement( '<input type="hidden" name="id" value="' . $mainPage . '" />' );
  $searchbar->addElement( '<input type="hidden" name="sub" value="' . $page . '" />' );
  $searchbar->addElement( '<input type="hidden" name="list" value="' . ($_GET["list"] ?? '') . '" />' );

  // Start table
  $table = new HTML('table');

  // Headline
  $table->addElement(
    array(
      'headline' => array(
        'items' => array(
          array(
            'context' => Language::string(1),
          ),
          array(
            'context' => Language::string(2),
          ),
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List general products
  foreach(  Pub::all( $offset, $steps, $search_value) as $pub ) {
    if( User::w_access_allowed($page, $current_user) ) {
        $actions = '<a
                      href="' . $url_page . '&view_pub=' . urlencode( $pub["pub_id"] ) . '"
                      title="' . Language::string(3) . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
        $actions .= '<a
                      href="' . $url_page . '&remove_pub=' . urlencode( $pub["pub_id"] ) . '"
                      title="' . Language::string(4) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
    }else {
      $actions = '<a
                    href="' . $url_page . '&view_pub=' . urlencode( $pub["pub_id"] ) . '"
                    title="' . Language::string(3) . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
    }

    $table->addElement(
      array(
        'row' => array(
          'items' => array(
            array(
              'context' => $pub["name"],
            ),
            array(
              'context' => ($actions ?? ''),
            ),
          ),
        ),
      ),
    );
  }

  // Footer
  $last = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '"
            style="float: left;">' . Language::string(5) . '</a>';
  $next = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
            style="float: right;">' . Language::string(6) . '</a>';

  if( (count(Pub::all( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(Pub::all( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
    $table->addElement(
      array(
        'footer' => array(
          'context' => $next,
        ),
      ),
    );
  }


  // Display
  echo '<div class="pub">';

    $searchbar->prompt();
    $table->prompt();

  echo '</div>';
}

function single_pub ( $pub_id ) {
  //Require variables
  global $url, $url_page, $page, $current_user;

  // Set id
  $pub = new Pub();
  $pub->pub = $pub_id;

  // Start HTML
  $html =  '<div class="pub">';

  $topNav = new HTML('top-nav');

  $topNav->addElement(
    array(
      'context' => Language::string(20),
      'link' => $url_page . '&view_pub=' . $pub->pub . '&type=general',
      'additional' => 'class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "general" ? "selected" : "") : "selected" ) . '" ' .
                      'title="' . Language::string(22) . '"',
    ),
  );

  $topNav->addElement(
    array(
      'context' => Language::string(21),
      'link' => $url_page . '&view_pub=' . $pub->pub . '&type=access',
      'additional' => 'class="' . (isset( $_GET["type"] ) ? ($_GET["type"] == "access" ? "selected" : "") : "") . '" ' .
                      'title="' . Language::string(23) . '"',
    ),
  );

  ////////////////////
  // Select page
  ////////////////////
  switch( $_GET["type"] ?? "") {
    ////////////////////
    // Manage access
    ////////////////////
    case "access":
      // Start table
      $table = new HTML('table');

      // Headline
      //     $headline_names = array('Benutzername', 'Email', 'Schreiben | Lesen');
      $table->addElement(
        array(
          'headline' => array(
            'items' => array(
              array(
                'context' => Language::string(45),
              ),
              array(
                'context' => Language::string(46),
              ),
              array(
                'context' => Language::string(47),
              ),
            ),
          ),
        ),
      );

      // Set offset and steps
      $steps = 20;
      $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

      // List all users
      foreach(  User::all( $offset, $steps ) as $user ) {
        // Check user infos
        $write_access = $pub->access( $user["id"] )["w"] ?? false;
        $read_access = $pub->access( $user["id"] )["r"] ?? false;

        if( User::w_access_allowed($page, $current_user) ){
          //Current user can edit
          $actions = '<a
                      onclick="' . ($write_access ? "pub_remove_right" : "pub_add_right") .
                        '(this, \'' . $user["id"] . '\', ' . $pub->pub . ', \'w\')"
                        title="' . ($write_access ?
                        Language::string(48, array(
                          '%user%' => User::name( $user["id"] ),
                        )) : Language::string(49, array(
                          '%user%' => User::name( $user["id"] ),
                        ))) . '">' .
                      '<img src="' . $url . '/medias/icons/' .
                        ($write_access ? "togglePubRights2.svg" : "togglepubRights1.svg") . '" />' .
                    '</a>';
          $actions .= '<a
                      onclick="' . ($read_access ? "pub_remove_right" : "pub_add_right") .
                        '(this, \'' . $user["id"] . '\', ' . $pub->pub . ', \'r\')"
                        title="' . ($read_access ?
                        Language::string(50, array(
                          '%user%' => User::name( $user["id"] ),
                        )) : Language::string(51, array(
                          '%user%' => User::name( $user["id"] ),
                        ))) . '">' .
                      '<img src="' . $url . '/medias/icons/' .
                        ($read_access ? "togglePubRights2.svg" : "togglepubRights1.svg") . '" />' .
                    '</a>';
        }else {
          // Current user can not edit
          $actions = '<a
                        title="' . ($write_access ?
                        Language::string(48, array(
                          '%user%' => User::name( $user["id"] ),
                        )) : Language::string(49, array(
                          '%user%' => User::name( $user["id"] ),
                        ))) . '">' .
                      '<img src="' . $url . '/medias/icons/' .
                        ($write_access ? "togglePubRights2.svg" : "togglepubRights1.svg") . '" />' .
                    '</a>';
          $actions .= '<a
                        title="' . ($read_access ?
                        Language::string(50, array(
                          '%user%' => User::name( $user["id"] ),
                        )) : Language::string(51, array(
                          '%user%' => User::name( $user["id"] ),
                        ))) . '">' .
                      '<img src="' . $url . '/medias/icons/' .
                        ($read_access ? "togglePubRights2.svg" : "togglepubRights1.svg") . '" />' .
                    '</a>';
        }

        $table->addElement(
          array(
            'row' => array(
              'items' => array(
                array(
                  'context' => User::name( $user["id"] ) . ' (' . $user["id"] . ')',
                ),
                array(
                  'context' => $user["email"],
                ),
                array(
                  'context' => $actions ?? '',
                ),
              ),
            ),
          ),
        );
      }

      // Footer
      $last = '<a href="' .
                $url_page .
                ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '"
                style="float: left;">' . Language::string(6) . '</a>';
      $next = '<a href="' .
                $url_page .
                ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
                '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
                style="float: right;">' . Language::string(7) . '</a>';

      if( (count(User::all( ($offset + $steps), $steps )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
      }elseif (count(User::all( ($offset + $steps), $steps )) > 0) { // More pages accessable
        $table->addElement(
          array(
            'footer' => array(
              'context' => $next,
            ),
          ),
        );
      }
    break;
    ////////////////////
    // General options
    ////////////////////
    case "general":
    default:
      // Start right menu
      $rightMenu = new HTML('right-menu');

      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/pdf.svg" alt="' . Language::string(24) . '" title="' . Language::string(25) . '"/>',
          'additional_item' => 'href="' . $url . 'pdf/menu/?pub=' . $pub->pub . '"
                          target="_blank"',
        ),
      );

      if($pub->values()["tip"] == 1) {
        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/tip-money-on.svg" alt="' . Language::string(26) . '" title="' . Language::string(27) . '"/>',
            'additional_item' => 'onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"',
          ),
        );
      }else {
        $rightMenu->addElement(
          array(
            'context' => '<img src="' . $url . 'medias/icons/tip-money-off.svg" alt="' . Language::string(26) . '" title="' . Language::string(27) . '"/>',
            'additional_item' => 'onclick="toggleTipMoney(\'' . $pub->pub . '\', this.children[0])"',
          ),
        );
      }


      // Start from
      $form = new HTML('form', array(
        'action' => $url . '?' . $_SERVER["QUERY_STRING"],
        'method' => 'post',
        'additional' => 'class="right-menu"',
      ));

      // Pubname
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(28) . '</p>');

        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'name',
            'value' => ($pub->values()["name"] ?? ''),
            'placeholder' => Language::string(29),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
            'required' => true,
          ),
        );
      $form->customHTML('</div>');

      // Description
      $form->customHTML('<div class="box">');
        $form->addElement(
          array(
            'type' => 'textarea',
            'name' => 'description',
            'value' => ($pub->values()["description"] ?? ''),
            'placeholder' => Language::string(30),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );
      $form->customHTML('</div>');

      // Images
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(31) . '</p>');
      $form->customHTML('</div>');

      $form->addElement(
        array(
          'type' => 'image',
          'headline' => Language::string(33),
          'name' => 'logo_fileID',
          'select_info' => Language::string(32),
          'value' => ($pub->values()["logo_fileID"] ?? null),
          'preview_image' => ((isset($pub->values()["logo_fileID"]) &&! empty($pub->values()["logo_fileID"])) ? MediaHub::getUrl( $pub->values()["logo_fileID"] ) : null),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->addElement(
        array(
          'type' => 'image',
          'headline' => Language::string(34),
          'name' => 'background_fileID',
          'select_info' => Language::string(32),
          'value' => ($pub->values()["background_fileID"] ?? null),
          'preview_image' => ((isset($pub->values()["background_fileID"]) &&! empty($pub->values()["background_fileID"])) ? MediaHub::getUrl( $pub->values()["background_fileID"] ) : null),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      // Payrexx
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(35) . '</p>');
        $form->customHTML( Language::string(36) );

        // Payrexx instance
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_payrexx_instance',
            'value' => ($pub->values()["payment_payrexx_instance"] ?? ''),
            'placeholder' => Language::string(37),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Payrexx secret
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_payrexx_secret',
            'value' => ($pub->values()["payment_payrexx_secret"] ?? ''),
            'placeholder' => Language::string(38),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Currency
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'currency',
            'value' => ($pub->values()["currency"] ?? ''),
            'placeholder' => '<a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">' . Language::string(39) . '</a>',
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

      $form->customHTML('</div>');

      // Fees
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(40) . '</p>');
        $form->customHTML( Language::string(41) );

        // Fees absolute
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_fee_absolute',
            'value' => (isset($pub->values()["payment_fee_absolute"]) ? number_format($pub->values()["payment_fee_absolute"] / 100, 2) : 0),
            'unit' => ($pub->values()["currency"] ?? DEFAULT_CURRENCY),
            'placeholder' => Language::string(42),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Fees percent
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_fee_percent',
            'value' => (isset($pub->values()["payment_fee_percent"]) ? number_format($pub->values()["payment_fee_percent"] / 100, 2) : 0),
            'unit' => '%',
            'placeholder' => Language::string(43),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

      $form->customHTML('</div>');

      $form->addelement(
        array(
          'type' => 'button',
          'name' => 'update',
          'value' => Language::string(44),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );
    break;
  }

  // Display
  echo '<div class="pub">';

    isset($topNav) ? $topNav->prompt() : '';
    isset($rightMenu) ? $rightMenu->prompt() : '';
    isset($form) ? $form->prompt() : '';
    isset($table) ? $table->prompt() : '';

  echo '</div>';
}

function display_products ( $search_value = null ) {
  //Require variables
  global $url, $url_page, $page, $mainPage, $current_user;

  // Start searchbar
  $searchbar = new HTML('searchbar', array(
    'action' => $url,
    'method' => 'get',
    'placeholder' => Language::string(10),
    's' => $search_value,
  ));

  $searchbar->addElement( '<input type="hidden" name="id" value="' . $mainPage . '" />' );
  $searchbar->addElement( '<input type="hidden" name="sub" value="' . $page . '" />' );
  $searchbar->addElement( '<input type="hidden" name="list" value="' . ($_GET["list"] ?? '') . '" />' );

  // Start table
  $table = new HTML('table');

  // Headline
  $table->addElement(
    array(
      'headline' => array(
        'items' => array(
          array(
            'context' => Language::string(11),
          ),
          array(
            'context' => Language::string(12),
          ),
          array(
            'context' => Language::string(13),
          ),
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List general products
  foreach(  Product::global_products( $offset, $steps, $search_value) as $products ) {
    if( User::w_access_allowed($page, $current_user) ) {
      $actions =  '<a
                    href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '"
                    title="' . Language::string(14) . '"><img src="' . $url . '/medias/icons/pencil.svg" />';
      $actions .=  '<a
                    href="' . $url_page . '&remove_product=' . urlencode( $products["id"] ) . '"
                    title="' . Language::string(15) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
    }else {
      $actions .=  '<a
                    href="' . $url_page . '&view_product=' . urlencode( $products["id"] ) . '"
                    title="' . Language::string(14) . '"><img src="' . $url . '/medias/icons/view-eye.svg" />';
    }

    $table->addElement(
      array(
        'row' => array(
          'items' => array(
            array(
              'context' => $products["name"],
            ),
            array(
              'context' => number_format(($products["price"] / 100), 2) . ' ' . DEFAULT_CURRENCY,
            ),
            array(
              'context' => ($actions ?? ''),
            ),
          ),
        ),
      ),
    );
  }

  // Footer
  $last = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&list=products' .
            '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '"
            style="float: left;">' . Language::string(16) . '</a>';
  $next = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&list=products' .
            '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
            style="float: right;">' . Language::string(17) . '</a>';

  if( (count(Product::global_products( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(Product::global_products( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
    $table->addElement(
      array(
        'footer' => array(
          'context' => $next,
        ),
      ),
    );
  }


  // Display
  echo '<div class="pub">';

    $searchbar->prompt();
    $table->prompt();

  echo '</div>';
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

  $form = new HTML('form', array(
    'action' => $url . '?' . $_SERVER["QUERY_STRING"],
    'method' => 'post',
    'additional' => 'style="width: 100%; max-width: 750px;" class="box-width"',
  ));

  if( User::w_access_allowed( $page, $current_user )) {
    $form->customHTML('<h1>' . Language::string(61) . '</h1>');
  }else {
    $form->customHTML('<h1>' . Language::string(52) . '</h1>');
  }

  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'name',
      'placeholder' => Language::string(63),
      'value' => ($product->values()["name"] ?? ""),
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
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
      'custom_options' => '<span onclick="event.stopPropagation()" class="option_add" ><input type="text"/><span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">' . Language::string(64) . '</span></span>',
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
    ),
  );

  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'price',
      'placeholder' => Language::string(65),
      'value' => ($product->values()["price"] ? number_format(($product->values()["price"]/100), 2) :  ""),
      'unit' => '<abbr title="' . Language::string(66) . '">' . DEFAULT_CURRENCY . '</abbr>',
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
    ),
  );

  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'product_fileID',
      'headline' => Language::string(67),
      'select_info' => Language::string(68),
      'preview_image' => ((isset($product->values()["product_fileID"]) &&! empty($product->values()["product_fileID"])) ? MediaHub::getUrl( $product->values()["product_fileID"] ) : null),
      'value' => ($product->values()["product_fileID"] ?? null),
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
    ),
  );

  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' => Language::string(69),
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
    ),
  );


  echo '<div class="pub">';

    $form->prompt();

  echo '</div>';
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
    $info = Language::string( 70,
      array(
        '%name%' => $pub->values()["name"],
        '%id%' => $_GET["remove_pub"],
      ),
    );

    // Display message
    Action::confirm($info, $_GET["remove_pub"], "&list=pub");
  break;
  case "remove_product":
    // Get name of pub
    $product = new Product();
    $product->product_id = $_GET["remove_product"];

    // Generate message
    $info = Language::string( 71,
      array(
        '%name%' => $product->values()["name"],
        '%id%' => $_GET["remove_product"],
      ),
    );

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
              Action::success(Language::string( 72,
                array(
                  '%name%' => $pub->values()["name"],
                  '%id%' => $pub->pub,
                ),
              ));
            }else {
              Action::fail( Language::string( 73,
                array(
                  '%name%' => $pub->values()["name"],
                  '%id%' => $pub->pub,
                ),
              ));
            }
          break;
        }
      }else {
        Action::fail( Language::string(74) );
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
          Action::success( Language::string( 75,
            array(
              '%name%' => $product->values()["name"],
              '%id%' => $product->product_id,
            ),
          ));
        }else {
          Action::fail( Language::string( 76,
            array(
              '%name%' => $product->values()["name"],
              '%id%' => $product->product_id,
            ),
          ));
        }
      }else {
        Action::fail( Language::string(77) );
      }
    }

    // Display top return button
    $topNav = new HTML('top-nav');

    $topNav->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
        'link' => 'Javascript:history.back()',
        'additional' => 'title="' . Language::string(60) . '"',
      ),
    );


    $topNav->prompt();

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
            Action::success( Language::string( 78,
              array(
                '%url_page%' => $url_page,
                '%productid%' => $product->product_id,
              ),
             ) );
          }else{
            Action::fail( Language::string(79) );
          }
        }else {
          Action::fail( Language::string(80) );
        }
      }

      $form = new HTML('form', array(
        'action' => $url . '?' . $_SERVER["QUERY_STRING"],
        'method' => 'post',
        'additional' => 'style="width: 100%; max-width: 750px;" class="box-width"',
      ));

      if( User::w_access_allowed( $page, $current_user )) {
        $form->customHTML('<h1>' . Language::string(81) . '</h1>');
      }

      $form->addElement(
        array(
          'type' => 'text',
          'name' => 'name',
          'placeholder' => Language::string(63),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
          'required' => true,
        ),
      );

      // Generate options
      $options = array();
      $product = new Product();

      foreach($product->sections() as $section) {
        $options[$section["section"]] = $section["section"];
      }

      $form->addElement(
        array(
          'type' => 'select',
          'name' => 'section',
          'options' => $options,
          'headline' => Language::string(82),
          'custom_options' => '<span onclick="event.stopPropagation()" class="option_add" ><input type="text"/><span class="button" onclick="useNewOption( this.parentNode.children[0].value, this.parentNode.parentNode.parentNode )">' . Language::string(64) . '</span></span>',
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );


      $form->addElement(
        array(
          'type' => 'number',
          'name' => 'price',
          'placeholder' => Language::string(65),
          'unit' => '<abbr title="' . Language::string(66) . '">' . DEFAULT_CURRENCY . '</abbr>',
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->addElement(
        array(
          'type' => 'image',
          'name' => 'product_fileID',
          'headline' => Language::string(67),
          'select_info' => Language::string(68),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->addElement(
        array(
          'type' => 'button',
          'name' => 'create',
          'value' => Language::string(83),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );


      echo '<div class="pub">';

        $form->prompt();

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

      // Start from
      $form = new HTML('form', array(
        'action' => $url . '?' . $_SERVER["QUERY_STRING"],
        'method' => 'post',
        'additional' => ' style="width: 100%; max-width: 750px;" class="box-width"',
      ));

      // Pubname
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(28) . '</p>');

        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'name',
            'placeholder' => Language::string(29),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
            'required' => true,
          ),
        );
      $form->customHTML('</div>');

      // Description
      $form->customHTML('<div class="box">');
        $form->addElement(
          array(
            'type' => 'textarea',
            'name' => 'description',
            'placeholder' => Language::string(30),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );
      $form->customHTML('</div>');

      // Images
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(31) . '</p>');
      $form->customHTML('</div>');

      $form->addElement(
        array(
          'type' => 'image',
          'headline' => Language::string(33),
          'name' => 'logo_fileID',
          'select_info' => Language::string(32),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      $form->addElement(
        array(
          'type' => 'image',
          'headline' => Language::string(34),
          'name' => 'background_fileID',
          'select_info' => Language::string(32),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      // Payrexx
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(35) . '</p>');
        $form->customHTML( Language::string(36) );

        // Payrexx instance
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_payrexx_instance',
            'value' => ($pub->values()["payment_payrexx_instance"] ?? ''),
            'placeholder' => Language::string(37),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Payrexx secret
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_payrexx_secret',
            'value' => ($pub->values()["payment_payrexx_secret"] ?? ''),
            'placeholder' => Language::string(38),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Currency
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'currency',
            'placeholder' => '<a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">' . Language::string(39) . '</a>',
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

      $form->customHTML('</div>');

      // Fees
      $form->customHTML('<div class="box">');
        $form->customHTML('<p>' . Language::string(40) . '</p>');
        $form->customHTML( Language::string(41) );

        // Fees absolute
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_fee_absolute',
            'unit' => DEFAULT_CURRENCY,
            'placeholder' => Language::string(42),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

        // Fees percent
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'payment_fee_percent',
            'unit' => '%',
            'placeholder' => Language::string(43),
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
          ),
        );

      $form->customHTML('</div>');

      $form->addelement(
        array(
          'type' => 'button',
          'name' => 'create',
          'value' => Language::string(84),
          'disabled' => ! User::w_access_allowed( $page, $current_user ),
        ),
      );

      echo '<div class="pub">';

        $form->prompt();

      echo '</div>';
    }else {
      Action::fs_info('Die Unterseite existiert nicht . ', "Zurück", $url_page );
    }
  break;
  case "list":
  default:
    $topNav = new HTML('top-nav');

    $topNav->addElement(
      array(
        'context' => Language::string(90),
        'link' => $url_page . '&list=pub',
        'additional' => 'class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "pub" ? "selected" : "") : "selected" ) . '" ' .
                        'title="' . Language::string(91) . '"',
      ),
    );

    $topNav->addElement(
      array(
        'context' => Language::string(92),
        'link' => $url_page . '&list=products',
        'additional' => 'class="' . (isset( $_GET["list"] ) ? ($_GET["list"] == "products" ? "selected" : "") : "") . '" ' .
                        'title="' . Language::string(93) . '"',
      ),
    );

    echo '<div class="pub">';

      $topNav->prompt();

    echo '</div>';

    /////////////////////////////
    // LIST GLOBAL PRODUCTS
    /////////////////////////////
    if( ($_GET["list"] ?? "") == "products") {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $product = new Product();
        $product->product_id = $_POST["confirm"];
        $product_values = $product->values();

        // Remove
        if( $product->remove() ) {
          Action::success( Language::string(94,
            array(
              '%name%' => $product_values["name"],
              '%id%' => $_POST["confirm"],
            ),
          ));
        }else {
          Action::fail( Language::string(94,
            array(
              '%name%' => $product_values["name"],
              '%id%' => $_POST["confirm"],
            ),
          ));
        }
      }

      // List products
      display_products ( ($_GET["s"] ?? null) );

      if(User::w_access_allowed($page, $current_user)) {
        echo '<a class="add" href="' . $url_page . '&add=product">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }

    /////////////////////////////
    // LIST PUBS
    /////////////////////////////
    }else {
      // remove
      if(isset($_POST["confirm"])) {
        // Get values
        $pub = new Pub();
        $pub->pub = $_POST["confirm"];
        $pub_values = $pub->values();

        // Remove
        if( $pub->remove() ) {
          Action::success( Language::string( 96,
            array(
              '%name%' => $pub_values["name"] ?? '',
              '%id%' => $_POST["confirm"],
            ),
          ));
        }else {
          Action::fail( Language::string( 96,
            array(
              '%name%' => $pub_values["name"],
              '%id%' => $_POST["confirm"],
            ),
          ));
        }
      }

      // List pubs
      display_pubs ( ($_GET["s"] ?? null) );

      if(User::w_access_allowed($page, $current_user)) {
        echo '<a class="add" href="' . $url_page . '&add=pub">
          <span class="horizontal"></span>
          <span class="vertical"></span>
        </a>';
      }
    }
  break;
}
 ?>
