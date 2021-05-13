<?php
//Get database connection
$conn = Access::connect();

/**
 * Get infos out of database
 */
function display_actions(){
  //Define variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;
  global $conn;

  $number_rows = 50; //Maximal number of rows listed
  $offset = isset( $_GET["row-start"] )? (intval($_GET["row-start"]) * $number_rows) : 0; //Start position of listet actions

  $action_rows = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " ORDER BY modification_time DESC, id DESC LIMIT :offset, :numRows");
  $action_rows->execute(array(":offset" => $offset, ":numRows" => $number_rows));

  $action_total_rows = $conn->prepare("SELECT id FROM " . USER_ACTIONS);
  $action_total_rows->execute();
  $total_rows = $action_total_rows->rowCount(); //Get number of all registerd action

  //Start table
  $html = '<table class="rows">';

  //Headline
  $headline_names = array('User', 'Message', 'Datum', 'Restore details');

  //Start headline
  //Headline can be changed over array $headline_names
  $html .= '<tr>'; //Start row
  foreach( $headline_names as $name ){
    $html .= '<th>'.$name.'</th>';
  }
  $html .= '</tr>'; //Close row

  //row all tickets
  while( $action = $action_rows->fetch() ){

    $html .= '<tr class="table-list">'; //Start row
      $html .= '<td style="width: 20%;">' . User::name( $action["userID"] ) . ' (' . $action["userID"] . ')</td>'; //Display user id
      $html .= '<td style="width: 50%;">' . $action["print_message"] . '</td>'; //Display Name (pre and lastname)
      $html .= '<td style="width: 20%;">'.date("d.m.Y H:i:s", strtotime( $action["modification_time"])).'</td>'; //Display purchase date

          //Check if current user (logged in user) can edit or see the user
      if( User::w_access_allowed($page, $current_user) ){
        //Current user can edit and delete user
        $html .= '<td style="width: auto;">';
        if(! empty($action["old_value"]) && ! empty($action["new_value"])){
          $html .= '<a href="'.$url_page.'&view=' . $action["id"] . '" title="Revisionsdetails #'.$action["id"].'"><img src="' . $url . '/medias/icons/restore.svg" /></a>';
        }
        $html .= '</td>';
      }else{
        $html .= '<td style="width: auto;"></td>';
      }
    $html .= '</tr>'; //End row
  }

  //Range menu
  $html .= '<tr class="nav">';

  if( $offset + $number_rows >= $total_rows && $total_rows > $number_rows){ //last page
    $html .= '<td colspan="4">
                <a href="'.$url_page.'&row-start='.round($offset/$number_rows - 1, PHP_ROUND_HALF_UP).'" style="float: left;">Letze</a>
              </td>';
  }elseif( $offset <= 0 && $total_rows > $number_rows){ //First page
    $html .= '<td colspan="4">
                <a href="'.$url_page.'&row-start='.round($offset/$number_rows + 1, PHP_ROUND_HALF_UP).'" style="float: right;">Weiter</a>
              </td>';
   }elseif( $offset > 0){
    $html .= '<td colspan="4">
                <a href="'.$url_page.'&row-start='.round($offset/$number_rows - 1, PHP_ROUND_HALF_UP).'" style="float: left;">Letze</a>
                <a href="'.$url_page.'&row-start='.round($offset/$number_rows + 1, PHP_ROUND_HALF_UP).'" style="float: right;">Weiter</a>
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
  display_actions();
}
 ?>
