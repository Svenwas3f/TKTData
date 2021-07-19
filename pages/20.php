<?php
//Get database connection
$conn = Access::connect();

/**
 * Get infos out of database
 */
function display_actions( $search_value = null ){
  //Define variables
  global $url, $url_page, $page, $mainPage, $current_user, $conn;

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
          array(
            'context' => Language::string(4),
          ),
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 50;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List general products
  foreach(  User::actions( $offset, $steps, $search_value) as $action ) {
    if( User::w_access_allowed($page, $current_user) ){
      //Current user can edit and delete user
      if(! empty($action["old_value"]) && ! empty($action["new_value"])){
        $actions = '<a
                      href="' . $url_page . '&view=' . $action["id"] . '"
                      title="' .  Language::string(5, array('%id%' => $action["id"])) . '">
                        <img src="' . $url . '/medias/icons/restore.svg" />
                      </a>';
      }
    }

    // Get action string
    if( json_decode($action["print_message"]) === null ) {
      // No json
      $print_message = $action["print_message"];
    }else {
      // valid json
      $values = json_decode($action["print_message"], true);

      $print_message = Language::string( $values["id"], ($values["replacements"] ?? null) );
    }

    $table->addElement(
      array(
        'row' => array(
          // 'additional' => 'class="' . $class . '" title="' . $title . '"',
          'items' => array(
            array(
              'context' => User::name( $action["userID"] ) . ' (' . $action["userID"] . ')',
            ),
            array(
              'context' => $print_message,
            ),
            array(
              'context' => date("d.m.Y H:i:s", strtotime( $action["modification_time"])),
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

  if( (count(User::actions( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(User::actions( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
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

function single_action(){
  //Define variables
  global $url, $url_page, $conn;

  //Get infos from db
  $action_req = $conn->prepare("SELECT * FROM " . USER_ACTIONS . " WHERE id=:id");
  $action_req->execute(array(":id" => $_GET["view"]));
  $action = $action_req->fetch();

  // Get action string
  if( json_decode($action["print_message"]) === null ) {
    // No json
    $print_message = $action["print_message"];
  }else {
    // valid json
    $values = json_decode($action["print_message"], true);

    $print_message = Language::string( $values["id"], ($values["replacements"] ?? null) );
  }

  //Create html
  $html = '<div class="restore-action">';
    $html .= '<h1>' . $print_message . '</h1>';
    $html .= '<div class="version-container">';
      $html .= '<div class="old"><pre>' . json_encode( json_decode( $action["old_value"] ), JSON_PRETTY_PRINT) . '</pre><span>' . Language::string(8) . '</span></div>';
      $html .= '<div class="new"><pre>' . json_encode( json_decode( $action["new_value"] ), JSON_PRETTY_PRINT) . '</pre><span>' . Language::string(9) . '</span></div>';
    $html .= '</div>';
    $html .= '<a href="' . $url_page . '&restore=' . $_GET["view"] . '">' . Language::string(10) . '</a>';
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
      Action::success( Language::string(11) );
    }else {
      Action::fail( Language::string(12) );
    }
  }else{
    Action::fail( Language::string(13) );
  }
}

/**
 * display single changement
 */
if( isset($_GET["view"])){
  single_action();
}else{
  display_actions( ($_GET["s"] ?? null) );
}
 ?>
