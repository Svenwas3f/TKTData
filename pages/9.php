<?php
//Get database connection
$conn = Access::connect();

function display_coupons( $search_value = null ){
  //Define variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;
  global $conn;

  /**
   * Start html
   */
  //Start table
  $html = '<table class="rows">';

  //Headline
  $headline_names = array('Name', 'Verwendung', 'Discount', 'Aktion');

  //Start headline
  //Headline can be changed over array $headline_names
  $html .= '<tr>'; //Start row
  foreach( $headline_names as $name ){
    $html .= '<th>'.$name.'</th>';
  }
  $html .= '</tr>'; //Close row

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // Get content
  foreach( Coupon::all( $offset, $steps, $search_value ) as $coupon ) {
    //define color of goup
    $group = new Group();
    $group->groupID = $coupon["groupID"];
    $groupInfo = $group->values();


    $html .= '<tr class="table-list">'; //Start row
      $html .= '<td><div class="color" style="background-color: ' . $groupInfo["color"] . ';" title="Name: ' . $groupInfo["name"] . '&#013;ID: ' . $groupInfo["groupID"] . '"></div>' . $coupon["name"].'</td>';
      $html .= '<td>' . ($coupon["used"] ?? 0) .'/' . $coupon["available"] . '</td>';
      $html .= '<td>-' . (empty($coupon["discount_percent"]) ? number_format(($coupon["discount_absolute"] / 100), 2) . " " . $groupInfo["currency"] : ($coupon["discount_percent"] / 100 . "%")) . '</td>'; //Display discount

      //Check if current user (logged in user) can edit or see the user
      if( User::w_access_allowed($page, $current_user) ){
        //Current user can edit and delete user
        $html .= '<td style="width: auto;">
                    <a href="' . $url_page . '&view=' . $coupon["couponID"] . '" title="Coupondetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
          $html .= '<a href="' . $url_page . ((isset( $_GET["row-start"] )) ? "&row-start=" . $_GET["row-start"] : "") . '&remove=' . $coupon["couponID"] . '" title="Löschen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
        $html .= '</td>';
      }elseif( User::r_access_allowed($page, $current_user) ){
        $html .= '<td style="width: auto;">
                    <a href="' . $url_page . '&view=' . $coupon["couponID"] . '" title="Coupondetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>
                    <a href="' . $url . 'pdf/ticket/?ticketToken=' . $coupon["couponID"] . '" target="_blank" title="PDF öffnen"><img src="' . $url . '/medias/icons/pdf.svg" /></a>
                  </td>';
      }
    $html .= '</tr>'; //End row
  }

  // Menu requred
  $html .= '<tr class="nav">';

    if( (count(Coupon::all( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
      $html .= '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                </td>';
    }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
      $html .= '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                </td>';
    }elseif (count(Coupon::all( ($offset + $steps), 1 )) > 0) { // More pages accessable
      $html .= '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                </td>';
    }

  $html .= '</tr>';

  //Close table
  $html .= '</table>';

  /**
   * Display table
   */
  echo $html;
}

function single_coupon() {
  //require variables
  global $page;
  global $current_user;
  global $url;

  //Get disabled
  $disabled = (! User::r_access_allowed($page, $current_user)) ? "disabled":"";

  //Get coupon
  $coupon = new Coupon();
  $coupon->couponID = $_GET["view"];

  //Get group
  $group = new Group();
  $group->groupID = $coupon->values()["groupID"];

  $html = '<div class="headline-maincolor" style="background-color: ' . $group->values()["color"] . '" title="Name: ' . $group->values()["name"] . '&#013;ID: ' . $group->values()["groupID"] . '"></div>';

  //Display full form with value
  $html .= '<form action="' . $url . basename($_SERVER['REQUEST_URI']) . ' " method="post">';
    // $html .= '<span class="unit">' . $coupon->values()["name"] . '</span>';
    //Name
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="name" value="' . $coupon->values()["name"] . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">Name</span>';
    $html .= '</label>';

    //Discount
    $html .= '<h4>Discount</h4>';
    $html .= '<div style="display: flex;">';
      $html .= '<label class="radio" onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'%\';" style="display: flex;">';
        $html .= '<input type="radio" name="discount_selection" value="discount_percent" ' . (! empty($coupon->values()["discount_percent"]) ? "checked" : "" ) . ' ' . $disabled . ' required />';
        $html .= '<div></div>%';
      $html .= '</label>';
      $html .= '<label class="radio" onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'' . $group->values()["currency"] . '\';" style="display: flex; margin-left: 10px;">';
        $html .= '<input type="radio" name="discount_selection" value="discount_absolute" ' . (! empty($coupon->values()["discount_absolute"]) ? "checked" : "" ) . ' ' . $disabled . ' required />';
        $html .= '<div></div>' . $group->values()["currency"];
      $html .= '</label>';
    $html .= '</div>';


    $html .= '<label class="txt-input">';
      $html .= '<input type="number" min="0" step="0.05" name="discount" value="' . (empty($coupon->values()["discount_percent"]) ? number_format(($coupon->values()["discount_absolute"] / 100), 2) : ($coupon->values()["discount_percent"] / 100)) . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">Discount</span>';
      $html .= '<span class="unit">' . (empty($coupon->values()["discount_percent"]) ? $group->values()["currency"] : "%") . '</span>';
    $html .= '</label>';

    //Used
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" step="1" min="0" name="used" value="' . $coupon->values()["used"] . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">Benützt</span>';
    $html .= '</label>';

    //Available
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" step="1" min="0" name="available" value="' . $coupon->values()["available"] . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">Verfügbare Benützung</span>';
    $html .= '</label>';

    //Start date
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="startDate" value="' . $coupon->values()["startDate"] . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><abbr title="Leerlassen um Gruppendaten zu verwenden">Startdatum</abbr></span>';
    $html .= '</label>';

    //End date
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="endDate" value="' . $coupon->values()["endDate"] . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><abbr title="Leerlassen um Gruppendaten zu verwenden">Enddatum</abbr></span>';
    $html .= '</label>';

    //Submit button
    $html .= '<input type="submit" value="Update" ' . $disabled . '/>';

  $html .= '</form>';

  echo $html;

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
          Action::success("Der Coupon konnte <strong>erfolgreich</strong> überarbeitet werden");
        }else {
          Action::fail("Der Coupon konnte <strong>nicht</strong> überarbeitet werden");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    //Display coupon infos
    single_coupon();
  break;
  case "remove":
    //Get coupon informations
    $coupon = new Coupon();
    $coupon->couponID = $_GET["remove"];
    Action::confirm("Möchtest du den Coupon <b>#" . $_GET["remove"] . "</b> mit dem Namen <b>" . $coupon->values()["name"] . "</b> wirklich löschen?", $_GET["remove"]);
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
            Action::fail("Der Name und die Gruppe werden benötigt, um einen Coupon hinzuzufügen");
          break;
          case 1: //Coupon already exists
            Action::fail("Dieser Coupon <strong>existiert bereits</strong>");
          break;
          case 2: //Failed to add Coupon
            Action::fail("Der Coupon wurde <strong>nicht</strong> hinzugefügt");
          break;
          case 3: //Successfully added coupon
            Action::success("Der Coupon wurde <strong>erfolgreich</strong> hinzugefügt");
          break;
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }


    //Get disabled
    $disabled = (! User::r_access_allowed($page, $current_user)) ? "disabled":"";

    //Start form
    echo '<form action="' . $url . basename($_SERVER['REQUEST_URI']) . ' " method="post" style="overflow-x: visible;">';

      //Select group
      echo '<div class="select" onclick="toggleOptions(this)">';
        echo '<input type="text" class="selectValue" name="groupID" required>';
        echo '<span class="headline">Gruppe auswählen</span>';

        //Select all groups
        $groups = $conn->prepare("SELECT * FROM " . TICKETS_GROUPS);
        $groups->execute();
        echo '<div class="options">';
        while($row = $groups->fetch(PDO::FETCH_ASSOC)) {
          $group = new Group();
          $group->groupID = $row["groupID"];

          $title = 'Verfügbare Tickets: ' . $group->availableTickets() . '/' . $row["maxTickets"] . '&#013;Tickets pro Benutzer: ' . $row["tpu"]. '&#013;Preis: ' . ($row["price"]/100) . ' ' . $row["currency"] . ' + ' . $row["vat"] . '% MwST.&#013;'
          ;
          echo '<span data-value="' . $row["groupID"] . '" onclick="selectElement(this)" style="border-left: 5px solid ' . $row["color"] . ';" title="' . $title . '">' . $row["name"] . '</span>'; //Display group option
        }
        echo '</div>';

      echo '</div>';

      //Set name
      echo '<label class="txt-input">';
        echo '<input type="text" name="name" ' . $disabled . ' required/>';
        echo '<span class="placeholder">Name</span>';
      echo '</label>';

      //Set discount
      echo '<h4>Discount</h4>';
      echo '<div style="display: flex;">';
        echo '<label class="radio" onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'%\';" style="display: flex;">';
          echo '<input type="radio" name="discount_selection" value="discount_percent" ' . $disabled . ' checked required />';
          echo '<div></div>%';
        echo '</label>';
        echo '<label class="radio" onclick="document.getElementsByClassName(\'unit\')[0].innerHTML = \'Absolut\';" style="display: flex; margin-left: 10px;">';
          echo '<input type="radio" name="discount_selection" value="discount_absolute" ' . $disabled . ' required />';
          echo '<div></div>Absolut';
        echo '</label>';
      echo '</div>';


      echo '<label class="txt-input">';
        echo '<input type="number" min="0" step="0.05" name="discount" ' . $disabled . ' required/>';
        echo '<span class="placeholder">Discount</span>';
        echo '<span class="unit">%</span>';
      echo '</label>';

      //Set useage
      echo '<label class="txt-input">';
        echo '<input type="number" step="1" min="0" name="available" ' . $disabled . ' required/>';
        echo '<span class="placeholder">Verfügbare Benützung</span>';
      echo '</label>';

      //Set dates
      echo '<label class="txt-input">';
        echo '<input type="text" name="startDate" ' . $disabled . '/>';
        echo '<span class="placeholder"><abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Leerlassen um Gruppendaten zu verwenden">Startdatum</abbr></span>';
      echo '</label>';

      echo '<label class="txt-input">';
        echo '<input type="text" name="endDate" ' . $disabled . '/>';
        echo '<span class="placeholder"><abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Leerlassen um Gruppendaten zu verwenden">Enddatum</abbr></span>';
      echo '</label>';

      //Submit button
      echo '<input type="submit" name="add" value="Hinzufügen" ' . $disabled . '/>';
    echo '</form>';
  break;
  default:
    //Remove coupon if required
    if(isset($_POST["confirm"])) {
      if(User::w_access_allowed($page, $current_user)) {
        $coupon = new Coupon();
        $coupon->couponID = $_POST["confirm"];
        if($coupon->remove()) {
          Action::success("Der Coupon konnte <strong>erfolgreich</strong> entfernt werden");
        }else {
          Action::fail("Der Coupon konnte <strong>nicht</strong> entfernt werden");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Name, Gruppe, ID">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
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
