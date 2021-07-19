<?php
//Get database connection
$conn = Access::connect();

function display_users( $search_value = null ) {
  //Require variables
  global $url;
  global $url_page;
  global $conn;
  global $page;
  global $current_user;

  //Define variables
  $number_rows = 20; //Maximal number of rows listed
  $offset = isset( $_GET["row-start"] ) ? (intval($_GET["row-start"]) * $number_rows) : 0; //Start position of listet users

  /**
   * Start html
   */
  $html = '<table class="rows">';

  /**
   * Create headline
   */
  $headline_names = array('Benutzername', 'Email', 'Aktion');

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

  foreach( User::all( $offset, $steps, $search_value) as $user) {
      $html .= '<tr class="table-list">'; //Start row
        $html .= '<td style="width: 10%;">' . $user["id"] . '</td>'; //Display user id
        $html .= '<td style="width: 70%;">' . $user["email"] . '</td>'; //Display Name (pre and lastname)

        //Check if current user (logged in user) can edit or see the user
        if( User::w_access_allowed($page, $current_user) ){
          //Current user can edit and delete user
          $html .= '<td style="width: auto;">
                      <a href="' . $url_page . '&view=' . $user["id"] . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>
                      <a href="' . $url_page . '&remove=' . $user["id"] . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>
                    </td>';
        }elseif( User::r_access_allowed($page, $current_user) ){
          $html .= '<td style="width: auto;">
                      <a href="' . $url_page . '&view=' . $user["id"] . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>
                    </td>';
        }

      $html .= '</tr>'; //End row
    }

    // Menu requred
    $html .=  '<tr class="nav">';

      if( (count(User::all( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                    <a href="' . $url_page . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  </td>';
      }elseif (count(User::all( ($offset + $steps), 1, $search_value )) > 0) { // More pages accessable
        $html .=  '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }

    $html .=  '</tr>';

  //Close table
  $html .= '</table>';

  /**
   * Display table
   */
  echo $html;
}

function single_user($user) {
  //require variables
  global $conn;
  global $page;
  global $current_user;

  //Get user info
  //TODO: Convert to user class
  $user_info_req = $conn->prepare("SELECT * FROM " . USERS . " WHERE id=:user");
  $user_info_req->execute(array(":user" => $user));
  $user_info = $user_info_req->fetch();

  //Start form to edit, show user
  $html = '<form action="" method="post" style="max-width: 500px;">';
  /**
   * Read user info
   */

  //ID
  $html .= '<label class="txt-input">';
    $html .= '<input type="text" value="' . $user_info["id"] . '" disabled/>';
    $html .= '<span class="placeholder">Benutzername</span>';
  $html .= '</label>';

  //Name
  $html .= '<label class="txt-input">';
    $html .= '<input type="text" name="name" value="' . $user_info["name"] . '"/>';
    $html .= '<span class="placeholder">Name</span>';
  $html .= '</label>';

  //E-Mail
  $html .= '<label class="txt-input">';
    $html .= '<input type="text" name="mail" value="' . $user_info["email"] . '" required/>';
    $html .= '<span class="placeholder">E-Mail</span>';
  $html .= '</label>';

  /**
   * Read rights
  */
  //Get all meu elements
  $menu_elements = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu IS NULL OR submenu='' OR submenu='0' ORDER BY layout");
  $menu_elements->execute();

  //Display all menu elements
  while($menu = $menu_elements->fetch() ){
    //Display name
    $html .= '<div class="right-menu-title"><span>' . $menu["name"] . '</span><span class="writeorread" title="Schreibberechtigung">W</span><span class="writeorread" title="Leseberechtigung">R</span></div>';

    //Get all pages of menu (submenu)
    $submenus = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu=:submenu");
    $submenus->execute(array(":submenu" => $menu["id"]));
    //Go through every submenu
    while($submenu = $submenus->fetch()){
      //Check if they have a right
      $right_req = $conn->prepare("SELECT * FROM " . USER_RIGHTS . " WHERE page=:submenu AND userid=:user") ;//Select all rights of user and page
      $right_req->execute(array(":submenu" => $submenu["id"], ":user" => $user));
      $right = $right_req->fetch();

      //If user can only read, disable buttons
      $disabled = User::w_access_allowed( $page, $current_user ) === true?'':'disabled'; //Set input disabled if user has only readaccess
      $wChecked = (isset($right["w"]) ? ($right["w"] == 1?'checked':'') : ""); //Write access
      $rChecked = (isset($right["w"]) && isset($right["r"])) ? (( $right["r"] == 1 || $right["w"] == 1 )?'checked':'') : ""; //Read access if you have read access or write access

      //Display content
      $html .= '<div class="submenu-rights">';
        $html .= '<span title="Submenu #' . $submenu["id"] . ' [' . $submenu["name"] . '] von dem Menu #' . $menu["id"] . ' [' . $menu["name"] . ']">' . $submenu["name"] . '</span>'; //Menu name
        $html .= '<label class="checkbox user-rights-checkbox"><input type="checkbox" name="' . $submenu["id"] . '[]" value="w" ' . $disabled . ' ' . $wChecked . '/><div class="checkbox-btn" title="Schreibberechtigung setzen"></div></label>'; //Write checkbox
        $html .= '<label class="checkbox user-rights-checkbox"><input type="checkbox" name="' . $submenu["id"] . '[]" value="r" ' . $disabled . ' ' . $rChecked . '/><div class="checkbox-btn" title="Leseberechtigung setzen"></div></label>'; //Read checkbox
      $html .= '</div>';
    }

  }

  //Add submit button
  $html .= '<input type="submit" name="update" value="UPDATE" title="Benutzer aktualisieren" ' . $disabled . '/>';

  //Close form
  $html .= '</form>';

  /**
   * Display content
   */
  echo $html;
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
    //Display form
    echo '<form action="' . $url . '" method="get" class="search">';
      echo '<input type="hidden" name="id" value="' . $mainPage . '" />';
      echo '<input type="hidden" name="sub" value="' . $page . '" />';
      echo '<input type="text" name="s" value ="' . (isset( $_GET["s"] ) ? $_GET["s"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

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
