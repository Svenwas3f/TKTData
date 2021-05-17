<?php
//Get database connection
$conn = Access::connect();

/**
 * Get infos out of database
 */
function display_actions( $search_value = null ){
  //Define variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;
  global $conn;

  // Search form
  $html =  '<form action="' . $url_page . '" method="post" class="search">';
    $html .=  '<input type="text" name="s" value ="' . (isset(  $_POST["s"] ) ? $_POST["s"] : "") . '" placeholder="Produktname, Preis">';
    $html .=  '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
  $html .=  '</form>';

  //Start table
  $html .= '<table class="rows">';

  //Headline
  $headline_names = array('User', 'Message', 'Datum', 'Restore details');

  //Start headline
  //Headline can be changed over array $headline_names
  $html .= '<tr>'; //Start row
  foreach( $headline_names as $name ){
    $html .= '<th>' . $name . '</th>';
  }
  $html .= '</tr>'; //Close row

  // Set offset and steps
  $steps = 50;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  foreach( User::actions( $offset, $steps, $search_value) as $action ) {
    $html .= '<tr class="table-list">'; //Start row
      $html .= '<td style="width: 20%;">' . User::name( $action["userID"] ) . ' (' . $action["userID"] . ')</td>'; //Display user id
      $html .= '<td style="width: 50%;">' . $action["print_message"] . '</td>'; //Display Name (pre and lastname)
      $html .= '<td style="width: 20%;">' . date("d.m.Y H:i:s", strtotime( $action["modification_time"])) . '</td>'; //Display purchase date

          //Check if current user (logged in user) can edit or see the user
      if( User::w_access_allowed($page, $current_user) ){
        //Current user can edit and delete user
        $html .= '<td style="width: auto;">';
        if(! empty($action["old_value"]) && ! empty($action["new_value"])){
          $html .= '<a href="' . $url_page . '&view=' . $action["id"] . '" title="Revisionsdetails #' . $action["id"] . '"><img src="' . $url . '/medias/icons/restore.svg" /></a>';
        }
        $html .= '</td>';
      }else{
        $html .= '<td style="width: auto;"></td>';
      }
    $html .= '</tr>'; //End row
  }

  // Menu requred
  $html .=  '<tr class="nav">';

    if( (count(User::actions( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
      $html .=  '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                </td>';
    }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
      $html .=  '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                </td>';
    }elseif (count(User::actions( ($offset + $steps), 1, $search_value )) > 0) { // More pages accessable
      $html .=  '<td colspan="' . count( $headline_names ) . '">
                  <a href="' . $url_page . '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
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

function single_action(){
  //Define variables
  global $url;
  global $url_page;
  global $conn;


  //Get infos from db
  $action_req = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " WHERE id=:id");
  $action_req->execute(array(":id" => $_GET["view"]));
  $action = $action_req->fetch();

  //Create html
  $html = '<div class="restore-action">';
    $html .= '<h1>' . $action["print_message"] . '</h1>';
    $html .= '<div class="version-container">';
      $html .= '<div class="old"><pre>' . json_encode( json_decode( $action["old_value"] ), JSON_PRETTY_PRINT) . '</pre><span>Vorherige Version</span></div>';
      $html .= '<div class="new"><pre>' . json_encode( json_decode( $action["new_value"] ), JSON_PRETTY_PRINT) . '</pre><span>Geänderte Version</span></div>';
    $html .= '</div>';
    $html .= '<a href="' . $url_page . '&restore=' . $_GET["view"] . '">Änderungen zurücksetzen</a>';
  $html .= '</div>';

  //Display html
  echo $html;
}
/**
 * Restore action
 */
if( isset($_GET["restore"]) ){
  if( User::w_access_allowed($page, $current_user)){
    if( User::restore_action( $_GET["restore"] )) {
      Action::success("Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.");
    }else {
      Action::fail("Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden");
    }
  }else{
    Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
  }
}

/**
 * display single changement
 */
if( isset($_GET["view"])){
  single_action();
}else{
  display_actions( ($_POST["s"] ?? null) );
}
 ?>
