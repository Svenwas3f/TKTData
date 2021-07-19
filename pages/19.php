<?php
//Get database connection
$conn = Access::connect();

function display_users( $search_value = null ) {
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
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List general products
  foreach(  User::all( $offset, $steps, $search_value) as $user ) {
    //Check if current user (logged in user) can edit or see the user
    if( User::w_access_allowed($page, $current_user) ){
      //Current user can edit and delete user
      $actions = '<a href="' . $url_page . '&view=' . $user["id"] . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>
                  <a href="' . $url_page . '&remove=' . $user["id"] . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
    }elseif( User::r_access_allowed($page, $current_user) ){
      $actions = '<a href="' . $url_page . '&view=' . $user["id"] . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
    }

    $table->addElement(
      array(
        'row' => array(
          // 'additional' => 'class="' . $class . '" title="' . $title . '"',
          'items' => array(
            array(
              'context' => $user["id"],
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

  if( (count(User::all( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(User::all( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
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
  $table->prompt();
}

function single_user( $user_registerd ) {
  //require variables
  global $url_page, $conn, $page, $current_user;

  // Start user
  $user = new User();
  $user->user = $user_registerd;

  //////////////////////////////////////
  // Start form
  //////////////////////////////////////
  $form = new HTML('form', array(
    'action' => $url_page . '&view=' . $user_registerd,
    'method' => 'post',
    'additional' => 'style="max-width: 500px;"',
  ));

  $form->customHTML('<h4>' . Language::string(10) . '</h4>');

  // ID
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'userID',
      'value' => $user->values()["id"],
      'placeholder' => Language::string(11),
      'disabled' => true,
    ),
  );

  // Name
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'name',
      'value' => $user->values()["name"],
      'placeholder' => Language::string(12),
    ),
  );

  // Email
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'mail',
      'value' => $user->values()["email"],
      'placeholder' => Language::string(13),
    ),
  );

  //////////////////////////////////////
  // Rights
  //////////////////////////////////////
  $form->customHTML('<h4 style="Margin-top: 20px;">' . Language::string(14) . '</h4>');

  //Get all meu elements
  $menu_elements = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu IS NULL OR submenu='' OR submenu='0' ORDER BY layout");
  $menu_elements->execute();

  //Display all menu elements
  while($menu = $menu_elements->fetch() ){
    //Display name
    $plugin = new Plugin();

    if( $plugin->is_pluginpage( $menu["id"] ) ) {
      $form->customHTML('<div class="right-menu-title">
                          <span>' . (Language::string( 'menu' , null, $menu["id"]) ?? $menu["name"]) . '</span><span class="writeorread" title="' . Language::string(15) . '">W</span><span class="writeorread" title="' . Language::string(16) . '">R</span>
                        </div>');
    }else {
      $form->customHTML('<div class="right-menu-title">
                          <span>' . (Language::string( $menu["id"] , null, 'menu') ?? $menu["name"]) . '</span><span class="writeorread" title="' . Language::string(15) . '">W</span><span class="writeorread" title="' . Language::string(16) . '">R</span>
                        </div>');
    }

    //Get all pages of menu (submenu)
    $submenus = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu=:submenu");
    $submenus->execute(array(":submenu" => $menu["id"]));

    //Go through every submenu
    while($submenu = $submenus->fetch()){
      //Check if they have a right
      $right_req = $conn->prepare("SELECT * FROM " . USER_RIGHTS . " WHERE page=:submenu AND userid=:user") ;//Select all rights of user and page
      $right_req->execute(array(":submenu" => $submenu["id"], ":user" => $user_registerd));
      $right = $right_req->fetch();

      $form->customHTML('<div class="submenu-rights">');

        // Check name and plugin
        if( $plugin->is_pluginpage( $submenu["id"] ) ) {
        $form->customHTML('<span title="' .
                            Language::string( 'subpage', array(
                              '%submenu%' => $submenu["id"],
                              '%submenuname%' => (Language::string( $submenu["id"] , null, 'menu') ?? $submenu["name"]),
                              '%mainmenu%' => $menu["id"],
                            ), 'menu' ) . '">' .
                              (Language::string( 'menu' , null, $submenu["id"]) ?? $submenu["name"]) .
                            '</span>');
        }else {
          $form->customHTML('<span
                              title="' .
                              Language::string( 'subpage', array(
                                '%submenu%' => $submenu["id"],
                                '%submenuname%' => (Language::string( $submenu["id"] , null, 'menu') ?? $submenu["name"]),
                                '%mainmenu%' => $menu["id"],
                              ), 'menu') . '">' .
                                (Language::string( $submenu["id"], null, 'menu' ) ?? $submenu["name"]) .
                              '</span>');
        }

        $form->addElement(
          array(
            'type' => 'checkbox',
            'name' => $submenu["id"] . '[]',
            'value' => 'w',
            'checked' => User::w_access_allowed( $submenu["id"], $user_registerd ),
            'classes' => 'user-rights-checkbox',
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
            'additional_div' => 'title="' . Language::string(17) . '"',
          )
        );

        $form->addElement(
          array(
            'type' => 'checkbox',
            'name' => $submenu["id"] . '[]',
            'value' => 'r',
            'checked' => User::r_access_allowed( $submenu["id"], $user_registerd ),
            'classes' => 'user-rights-checkbox',
            'disabled' => ! User::w_access_allowed( $page, $current_user ),
            'additional_div' => 'title="' . Language::string(18) . '"',
          )
        );

      $form->customHTML('</div>');
    }
  }

  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' => Language::string(19),
      'disabled' => ! User::w_access_allowed( $page, $current_user ),
      'additional' => 'title="' . Language::string(20) . '"'
    )
  );

  $form->prompt();
}

//Remove user finaly
if( isset($_POST["confirm"])) {
  //Create new user
  $user = new User();
  $user->user = $_POST["confirm"];

  if( User::w_access_allowed($page, $current_user)) {
    if( $user->remove()) {
      Action::success('Der Benutzer (' . $_POST["confirm"] . ') wurde erfolgreich entfernt . ');
    }else {
      Action::fail('Der Benutzer (' . $_POST["confirm"] . ') konnte nicht entfernt werden . ');
    }
  }else {
    Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
  }
}

//Get current action
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys

switch(key($action)) {
  case "add":
    //Add user
    if( isset( $_POST["add"])) {
      if( User::w_access_allowed($page, $current_user)) {
        $user = new User();
        if($user->add($_POST["mail"], $_POST["userID"], $_POST["name"], $_POST, (isset($_POST["sendMail"])) ? true : false)) {
          Action::success("Der Benutzer wurde <strong>erfolgreich</strong> hinzugefügt.");
        }else{
          Action::fail("Der Benutzer konnte <strong>nicht</strong> hinzugefügt werden");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    //Start form to edit, show user
    echo '<form action="" method="post" style="max-width: 500px;">';
    /**
     * Read user info
     */

    //ID
    echo '<label class="txt-input">';
      echo '<input type="text" name="userID"/>';
      echo '<span class="placeholder">Benutzername</span>';
    echo '</label>';

    //Name
    echo '<label class="txt-input">';
      echo '<input type="text" name="name"/>';
      echo '<span class="placeholder">Name</span>';
    echo '</label>';

    //E-Mail
    echo '<label class="txt-input">';
      echo '<input type="email" name="mail" required/>';
      echo '<span class="placeholder">E-Mail</span>';
    echo '</label>';

    /**
     * User rights
     */

    $menu_elements = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu IS NULL OR submenu='' OR submenu='0' ORDER BY layout");
    $menu_elements->execute();

    //Display all menu elements
    while($menu = $menu_elements->fetch() ){
      //Display name
      echo '<div class="right-menu-title"><span>' . $menu["name"] . '</span><span class="writeorread" title="Schreibberechtigung">W</span><span class="writeorread" title="Leseberechtigung">R</span></div>';

      //Get all pages of menu (submenu)
      $submenus = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu=:submenu");
      $submenus->execute(array(":submenu" => $menu["id"]));
      //Go through every submenu
      while($submenu = $submenus->fetch()){
        //Display content
        echo '<div class="submenu-rights">';
        echo '<span title="Submenu #' . $submenu["id"] . ' [' . $submenu["name"] . '] von dem Menu #' . $menu["id"] . ' [' . $menu["name"] . ']">' . $submenu["name"] . '</span>'; //Menu name
        echo '<label class="checkbox user-rights-checkbox"><input type="checkbox" name="' . $submenu["id"] . '[]" value="w"/><div class="checkbox-btn" title="Schreibberechtigung setzen"></div></label>'; //Write checkbox
        echo '<label class="checkbox user-rights-checkbox"><input type="checkbox" name="' . $submenu["id"] . '[]" value="r"/><div class="checkbox-btn" title="Leseberechtigung setzen"></div></label>'; //Read checkbox
        echo '</div>';
      }
    }

    //Display mail button
    echo '<label class="checkbox"><input type="checkbox" name="sendMail" value="true" checked/><div class="checkbox-btn" title="Mail an neuen Benutzer senden."></div> Zugangsdaten an Benutzer senden.</label>';

    //Confirm form
    echo '<input type="submit" name="add" value="Hinzufügen" title="Benutzer hinzufügen"/>';

    echo '</form>';
  break;
  case "view":
    //Create new user
    $user = new User();
    $user->user = $_GET["view"];

    //Update user
    if( isset( $_POST["update"])) {
      if( User::w_access_allowed($page, $current_user)) {
        if($user->updateRights( $_POST ) && $user->updateInfos($_POST["name"], $_POST["mail"], null)) {
          Action::success("Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.");
        }else{
          Action::fail("Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    //Display user
    single_user($user->user);
  break;
  case "remove":
    //display remove form
    Action::confirm('Möchten Sie den Benutzer ' . User::name($_GET["remove"]) . ' (' . $_GET["remove"] . ') unwiederruflich entfernen?', $_GET["remove"]);
  break;
  default:
    //Display result
    $search_value = (!empty($_GET["s"])) ? $_GET["s"] : '';
    display_users( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
}


 ?>
