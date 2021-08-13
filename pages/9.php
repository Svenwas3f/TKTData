<?php
function display_coupons( $search_value = null ){
  //Define variables
  global  $url_page, $url, $mainPage, $page, $current_user;

  // Start searchbar
  $searchbar = new HTML('searchbar', array(
    'action' => $url,
    'method' => 'get',
    'placeholder' => Language::string(0),
    's' => (isset( $_GET["s"] ) ? $_GET["s"] : ""),
  ));

  $searchbar->addElement( '<input type="hidden" name="id" value="' . $mainPage . '" />' );
  $searchbar->addElement( '<input type="hidden" name="sub" value="' . $page . '" />' );

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
          array(
            'context' => Language::string(3),
          ),
          array(
            'context' => Language::string(4),
          ),
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List coupons
  foreach( Coupon::all( $offset, $steps, ($_GET["s"] ?? null), true) as $coupon ) {
    //define color of goup
    $group = new Group();
    $group->groupID = $coupon["groupID"];
    $groupInfo = $group->values();

    // Generate action
    if( User::w_access_allowed( $page, $current_user ) ) {
        $actions = '<a
                      href="' . $url_page . '&view=' . urlencode($coupon["couponID"]) . '"
                      title="' . Language::string(6) . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
        $actions .= '<a
                      href="' . $url_page . '&remove=' . urlencode( $coupon["couponID"] ) . '"
                      title="' . Language::string(7) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
    }else {
      $actions = '<a
                    href="' . $url_page . '&view=' . urlencode($coupon["couponID"]) . '"
                    title="' . Language::string(6) . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
    }

    // Coupon
    $table->addElement(
      array(
        'row' => array(
          'items' => array(
            array(
              'context' => '<div
                              class="color"
                              style="background-color: ' . $groupInfo["color"] . ';"
                              title="' . Language::string( 5, array(
                                '%name%' => $groupInfo["name"],
                                '%id%' => $groupInfo["groupID"],
                              ), ) . '"></div>' .
                              $coupon["name"],
            ),
            array(
              'context' => ($coupon["used"] ?? 0) .'/' . $coupon["available"],
            ),
            array(
              'context' => (empty($coupon["discount_percent"]) ?
                              number_format(($coupon["discount_absolute"] / 100), 2) . " " . $groupInfo["currency"] : ($coupon["discount_percent"] / 100 . "%")),
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
            style="float: left;">' . Language::string(28) . '</a>';
  $next = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
            style="float: right;">' . Language::string(29) . '</a>';

  if( (count(Coupon::all( ($offset + $steps), $steps, ($_GET["s"] ?? null), true)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(Coupon::all( ($offset + $steps), $steps, ($_GET["s"] ?? null), true)) > 0) { // More pages accessable
    $table->addElement(
      array(
        'footer' => array(
          'context' => $next,
        ),
      ),
    );
  }

  $searchbar->prompt();
  $table->prompt();
}

function single_coupon() {
  //require variables
  global $page, $current_user, $url;

  //Get coupon
  $coupon = new Coupon();
  $coupon->couponID = $_GET["view"];

  //Get group
  $group = new Group();
  $group->groupID = $coupon->values()["groupID"];

  // Display top return button
  $topNav = new HTML('top-nav', array(
    'classes' => 'border-none',
  ));

  $topNav->addElement(
    array(
      'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
      'link' => 'Javascript:history.back()',
      'additional' => 'title="' . Language::string(11) . '"',
    ),
  );

  $form = new HTML('form', array(
    'action' =>  $url . basename($_SERVER['REQUEST_URI']),
    'method' => 'post',
  ));

  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'name',
      'value' => $coupon->values()["name"] ?? null,
      'placeholder' => Language::string(12),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  //Discount
  $form->customHTML('<h4>' . Language::string(13) . '</h4>');
  $form->customHTML('<div style="display: flex;">');

    $form->addElement(
      array(
        'type' => 'radio',
        'name' => 'discount_selection',
        'value' => 'discount_percent',
        'context' => '%',
        'checked' => ! empty($coupon->values()["discount_percent"]),
        'additional' => 'onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'%\';"
                         style="display: flex;"',
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    $form->addElement(
      array(
        'type' => 'radio',
        'name' => 'discount_selection',
        'value' => 'discount_absolute',
        'context' => $group->values()["currency"] ?? DEFAULT_CURRENCY,
        'checked' => ! empty($coupon->values()["discount_absolute"]),
        'additional' => 'onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'' . $group->values()["currency"] . '\';"
                         style="display: flex; margin-left: 10px;"',
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

  $form->customHTML('</div>');

  // Discount
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'discount',
      'value' => (empty($coupon->values()["discount_percent"]) ?
                    number_format(($coupon->values()["discount_absolute"] / 100), 2) :
                    ($coupon->values()["discount_percent"] / 100)),
      'placeholder' => Language::string(13),
      'unit' => (empty($coupon->values()["discount_percent"]) ? ($group->values()["currency"] ?? DEFAULT_CURRENCY) : "%"),
      'input_attributes' => 'step="0.01" value="0.00"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  // Used
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'used',
      'value' => $coupon->values()["used"] ?? 0,
      'placeholder' => Language::string(14),
      'input_attributes' => 'min="0" step="1"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  // Available
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'available',
      'value' => $coupon->values()["available"] ?? null,
      'placeholder' => Language::string(15),
      'input_attributes' => 'min="0" step="1"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  // Start date
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'startDate',
      'value' => $coupon->values()["startDate"] ?? null,
      'placeholder' => Language::string(16),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // End date
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'endDate',
      'value' => $coupon->values()["endDate"] ?? null,
      'placeholder' => Language::string(17),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Update
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' => Language::string(18),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Show html
  echo '<div
          class="headline-maincolor"
          style="background-color: ' . $group->values()["color"] . '"
          title="' . Language::string( 10, array(
            '%name%' => $group->values()["name"],
            '%id%' => $group->values()["groupID"],
          ), ) . '">
        </div>';

  $topNav->prompt();
  $form->prompt();
}

//Get current action
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys

switch(key($action)) {
  case "view":
    //Check if update required
    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        $coupon = new Coupon();
        $coupon->couponID = $_GET["view"];

        //Set correct discount
        $updateValues = $_POST;
        if($_POST["discount_selection"] == "discount_percent") {
          $updateValues["discount_percent"] = $_POST["discount"] * 100;
          $updateValues["discount_absolute"] = "";
        }else {
          $updateValues["discount_percent"] = "";
          $updateValues["discount_absolute"] = $_POST["discount"] * 100;
        }

        //Update
        if($coupon->update($updateValues)) {
          Action::success( Language::string(40) );
        }else {
          Action::fail( Language::string(41) );
        }
      }else {
        Action::fail( Language::string(42) );
      }
    }

    //Display coupon infos
    single_coupon();
  break;
  case "remove":
    //Get coupon informations
    $coupon = new Coupon();
    $coupon->couponID = $_GET["remove"];

    // Generate info
    $info = Language::string( 20, array(
      '%id%' => $_GET["remove"],
      '%name%' => $coupon->values()["name"],
    ), );

    // Display
    Action::confirm( $info, $_GET["remove"] );
  break;
  case "add":
    //add coupon if required
    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        $coupon = new Coupon();
        //Modifie post
        $add_values = $_POST;
        if($_POST["discount_selection"] == "discount_percent") {
          $add_values["discount_percent"] = $_POST["discount"];
          $add_values["discount_absolute"] = "";
        }else {
          $add_values["discount_percent"] = "";
          $add_values["discount_absolute"] = $_POST["discount"];
        }

        switch($coupon->add($add_values)) {
          case 0: //Array does not contain important informations
            Action::fail( Language::string(34) );
          break;
          case 1: //Coupon already exists
            Action::fail( Language::string(35) );
          break;
          case 2: //Failed to add Coupon
            Action::fail( Language::string(36) );
          break;
          case 3: //Successfully added coupon
            Action::success( Language::string(37) );
          break;
        }
      }else {
        Action::fail( Language::string(34) );
      }
    }

    // Display top return button
    $topNav = new HTML('top-nav', array(
      'classes' => 'border-none',
    ));

    $topNav->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
        'link' => 'Javascript:history.back()',
        'additional' => 'title="' . Language::string(11) . '"',
      ),
    );

    // Start form
    $form = new HTML('form', array(
      'action' =>  $url . basename($_SERVER['REQUEST_URI']),
      'method' => 'post',
    ));

    //Select group
    $options = array();

    $groups = $conn->prepare("SELECT * FROM " . TICKETS_GROUPS);
    $groups->execute();
    foreach($groups->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $group = new Group();
      $group->groupID = $row["groupID"];

      $options[$row["groupID"] . "\"
                style='border-left: 5px solid " . $row["color"] . ";'
                title='" .
                  Language::string( 33, array(
                    '%availableTickets%' => $group->availableTickets(),
                    '%maxTickets%' => $row["maxTickets"],
                    '%tpu%' => $row["tpu"],
                    '%price%' => ($row["price"]/100),
                    '%currency%' => $row["currency"],
                    '%vat%' => $row["vat"],
                  ),) . "'"] =
        $row["name"];
    }

    $form->addElement(
      array(
        'type' => 'select',
        'name' => 'groupID',
        'headline' => Language::string(30),
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'options' => $options,
        'required' => true,
      ),
    );

    $form->addElement(
      array(
        'type' => 'text',
        'name' => 'name',
        'placeholder' => Language::string(12),
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    //Discount
    $form->customHTML('<h4>' . Language::string(13) . '</h4>');
    $form->customHTML('<div style="display: flex;">');

      $form->addElement(
        array(
          'type' => 'radio',
          'name' => 'discount_selection',
          'value' => 'discount_percent',
          'context' => '%',
          'additional' => 'onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'%\';"
                           style="display: flex;"',
          'disabled' => ! User::w_access_allowed($page, $current_user),
          'required' => true,
        ),
      );

      $form->addElement(
        array(
          'type' => 'radio',
          'name' => 'discount_selection',
          'value' => 'discount_absolute',
          'context' => Language::string(31),
          'additional' => 'onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'' . Language::string(31) . '\';"
                           style="display: flex; margin-left: 10px;"',
          'disabled' => ! User::w_access_allowed($page, $current_user),
          'required' => true,
        ),
      );

    $form->customHTML('</div>');

    // Discount
    $form->addElement(
      array(
        'type' => 'number',
        'name' => 'discount',
        'placeholder' => Language::string(13),
        'unit' => "%",
        'input_attributes' => 'step="0.01" value="0.00"',
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    // Available
    $form->addElement(
      array(
        'type' => 'number',
        'name' => 'available',
        'placeholder' => Language::string(15),
        'input_attributes' => 'min="0" step="1"',
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    // Start date
    $form->addElement(
      array(
        'type' => 'text',
        'name' => 'startDate',
        'placeholder' => Language::string(16),
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    // End date
    $form->addElement(
      array(
        'type' => 'text',
        'name' => 'endDate',
        'placeholder' => Language::string(17),
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    // Update
    $form->addElement(
      array(
        'type' => 'button',
        'name' => 'add',
        'value' => Language::string(32),
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    $topNav->prompt();
    $form->prompt();
  break;
  default:
    //Remove coupon if required
    if(isset($_POST["confirm"])) {
      if(User::w_access_allowed($page, $current_user)) {
        $coupon = new Coupon();
        $coupon->couponID = $_POST["confirm"];
        if($coupon->remove()) {
          Action::success( Language::string(21) );
        }else {
          Action::fail( Language::string(22) );
        }
      }else {
        Action::fail( Language::string(23) );
      }
    }

    //Display tickets
    $search_value = (!empty($_GET["s"])) ? $_GET["s"] : '';
    display_coupons( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
  }
}
 ?>
