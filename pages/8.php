<?php
// List all groups
function list_groups( $search_value = null ) {
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
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List tickets
  foreach( Group::all( $offset, $steps, $search_value) as $group ) {
    // Get group infos
    $currentGroup = new Group();
    $currentGroup->groupID = $group["groupID"];

    // Activity
    if( User::w_access_allowed($page, $current_user) ) {
        $actions = '<a
                      href="' . $url_page . '&view=' . urlencode( $group["groupID"] ) . '"
                      title="' . Language::string(5) . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
          $actions .= '<a
                        href="' . $url_page . '&remove=' . urlencode( $group["groupID"] ) . '"
                        title="' . Language::string(6) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
    }else {
      $actions = '<a
                    href="' . $url_page . '&view=' . urlencode( $group["groupID"] ) . '"
                    title="' . Language::string(5) . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
    }

    $timeWindow = ( strtotime($group["startTime"]) == strtotime($group["endTime"]) ) ?
                    Language::string(7) :
                    date("d.m.Y H:i:s", strtotime( $group["startTime"] )) . '-<br />' . date("d.m.Y H:i:s", strtotime( $group["endTime"] ));

    $table->addElement(
      array(
        'row' => array(
          'items' => array(
            array(
              'context' => '<div
                              class="color"
                              style="background-color: ' . $group["color"] . ';"
                              ></div>' .
                              $group["name"],
            ),
            array(
              'context' => $currentGroup->ticketsNum() . '/' . $group["maxTickets"],
            ),
            array(
              'context' => $timeWindow,
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
            style="float: left;">' . Language::string(13) . '</a>';
  $next = '<a href="' .
            $url_page .
            ( isset($_GET["s"]) ? "&s=" . urlencode($_GET["s"]) : "" ) .
            '&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '"
            style="float: right;">' . Language::string(14) . '</a>';

  if( (count(Group::all( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(Group::all( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
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

// General infos
function section_general( $groupID, $section ) {
  // Global urls
  global $url_page, $page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Start form
  $form = new HTML('form', array(
    'action' => $url_page . '&view=' . $groupID . '&section=' . $section,
    'method' => 'post',
    'additional' => 'style="width: 100%; max-width: 750px;" class="box-width"',
  ));

  // Group name
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'name',
      'value' => $group->values()["name"],
      'placeholder' => Language::string(20),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Max tickets
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'maxTickets',
      'value' => $group->values()["maxTickets"],
      'placeholder' => Language::string(21),
      'input_attributes' => 'min="0"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Tickets per user
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'tpu',
      'value' => $group->values()["tpu"],
      'placeholder' => Language::string(22),
      'input_attributes' => 'min="0"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Currency
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'currency',
      'value' => ($group->values()["currency"] ?? DEFAULT_CURRENCY),
      'placeholder' => Language::string(23),
      'input_attributes' => 'max-length="3" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Price
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'price',
      'value' => number_format(($group->values()["price"] / 100), 2),
      'placeholder' => Language::string(24),
      'input_attributes' => 'min="0" steps="0.05"',
      'unit' => ($group->values()["currency"] ?? DEFAULT_CURRENCY),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Startdate
  $form->addElement(
    array(
      'type' => 'datetime-local',
      'name' => 'startTime',
      'value' => ($group->values()["startTime"] == '0000-00-00 00:00:00' ?
                   '0000-00-00T00:00:00' :
                    date('Y-m-d\TH:i:s', strtotime($group->values()["startTime"]))),
      'placeholder' => Language::string(25),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Enddate
  $form->addElement(
    array(
      'type' => 'datetime-local',
      'name' => 'endTime',
      'value' => ($group->values()["endTime"] == '0000-00-00 00:00:00' ?
                   '0000-00-00T00:00:00' :
                    date('Y-m-d\TH:i:s', strtotime($group->values()["endTime"]))),
      'placeholder' => Language::string(26),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Vat
  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'vat',
      'value' => ($group->values()["vat"] / 100),
      'placeholder' => Language::string(27),
      'input_attributes' => 'min="0" steps="0.05"',
      'unit' => '%',
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Description
  $form->addElement(
    array(
      'type' => 'textarea',
      'name' => 'description',
      'value' => $group->values()["description"],
      'placeholder' => Language::string(28),
      'input_attributes' => 'min="0"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Button
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'general',
      'value' => Language::string(29),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );
  $form->prompt();
}

// Custom infos
function section_custom( $groupID, $section ) {
  //require variables
  global $conn, $url_page, $page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Start form
  echo '<form action="' . $url_page . '&view=' . $groupID . '&section=' . $section . '" method="post">';

    // Add buttons
    if(User::w_access_allowed($page, $current_user)) {
      echo '<div class="addInput">';
        echo '<span class="button" onclick="add_checkbox()">' . Language::string(40) . '</span>';
        echo '<span class="button" onclick="add_text(\'date\')">' . Language::string(41) . '</span>';
        echo '<span class="button" onclick="add_text(\'email\')">' . Language::string(42) . '</span>';
        echo '<span class="button" onclick="add_text(\'number\')">' . Language::string(43) . '</span>';
        echo '<span class="button" onclick="add_radio()">' . Language::string(44) . '</span>';
        echo '<span class="button" onclick="add_select()">' . Language::string(45) . '</span>';
        echo '<span class="button" onclick="add_text(\'text\')">' . Language::string(46) . '</span>';
        echo '<span class="button" onclick="add_text(\'textarea\')">' . Language::string(47) . '</span>';
      echo '</div>';
    }

    // Custom fields
    echo '<div class="customFormFields">';

      // Get cutom values
      $customElements = json_decode( $group->values()["custom"], true);

      // Get disabled
      $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

      // Generate max id
      $max_id = empty($customElements) ? 0 : max(array_column($customElements, "id"));
      echo '<input type="hidden" name="current_id" value="' . $max_id . '"/>';

      // Check if available
      if( count($customElements) > 0) {
        //Sort array
        foreach($customElements as $key => $value) {
          $orders[$key] = intval($value["order"]);
        }
        array_multisort($orders, SORT_ASC, $customElements);

        // List customs
        foreach( $customElements as $custom ) {
          //---------------
          //Start container
          //---------------
          echo '<div
                  id="container-' . $custom["id"] . '"
                  class="container-custom-form">';
          //Hidden input
          echo '<input
                  type="hidden"
                  name="hidden[]"
                  value="' . $custom["type"] . '%' . $custom["id"] . '%">';
          //Headline
          echo '<div>
                  <h1 style="display: inline-block">' . ucfirst($custom["type"]) . '-' . Language::string(48) . '</h1>
                  <span onclick="removeField(' . $custom["id"] . ')" style="margin: 0px 5px;">' . Language::string(49) . '</span>
                </div>';

          if($custom["type"] == 'select' || $custom["type"] == 'radio'){
            ///////////////
            // Selection or Radioform
            ///////////////
            echo '<input
                    type="text"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(50) . '"
                    value="' . $custom["name"] . '"
                    required="true" ' . $disabled . '/>';
            echo '<input
                    type="number"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(51) . '"
                    value="' . $custom["order"] . '" ' . $disabled . '/>';
            echo '<input
                    type="checkbox"
                    name="customField' . $custom["id"] . '[]"
                    value="1" ' .
                    ($custom["required"]==1?"checked":"") . ' ' . $disabled . '/>
                    (' . Language::string(52) . ')';
            echo '<span
                    class="button"
                    onclick="addMultiple(' . $custom["id"] . ')"
                    style="margin-bottom: 5px;">
                      ' . Language::string(53) . '
                    </span>';
            //Get all options
            $options = explode(',', $custom["value"]);

            for($optionI = 0; $optionI < COUNT($options) - 1; $optionI++){
              echo '<div
                      id="multipleContainer-' . $custom["id"].$optionI . '"
                      class="multipleContainer">';
                echo '<input
                        type="text"
                        name="multiple' . $custom["id"] . '[]"
                        placeholder="' . Language::string(50) . '"
                        value="' . $options[$optionI] . '" ' . $disabled . '>';
                echo '<span
                        onclick="removeMultiple(' . $custom["id"] . ', ' . $optionI . ')"
                        style="margin: 0px 5px;">
                          ' . Language::string(49) . '
                        </span>';
              echo '</div>';
            }
          }elseif($custom["type"] == 'checkbox'){
            ///////////////
            // Checkbox form
            ///////////////
            echo '<input
                    type="text"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(50) . '"
                    value="' . $custom["name"] . '"
                    required="true" ' . $disabled . '/>';
            echo '<input
                    type="number"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(51) . '"
                    value="' . $custom["order"] . '" ' . $disabled . '/>';
            echo '<input
                    type="checkbox"
                    name="customField' . $custom["id"] . '[]"
                    value="1" ' . ($custom["required"]==1?"checked":"") . '  ' .
                    $disabled . '/>
                    (' . Language::string(52) . ')';
          }else{
            ///////////////
            // Text form
            ///////////////
            echo '<input
                    type="text"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(50) . '"
                    value="' . $custom["name"] . '" required="true" ' . $disabled . '/>';
            echo '<input
                    type="text"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(54) . '"
                    value="' . $custom["placeholder"] . '"
                    required="true" ' . $disabled . '/>';
            echo '<input
                    type="number"
                    name="customField' . $custom["id"] . '[]"
                    placeholder="' . Language::string(51) . '"
                    value="' . $custom["order"] . '" ' . $disabled . '/>';
            echo '<input
                    type="checkbox"
                    name="customField' . $custom["id"] . '[]"
                    value="1" ' . ($custom["required"]==1?"checked":"") . '  ' .
                    $disabled . '/>
                    (' . Language::string(52) . ')';
          }
          echo '</div>';
        }
      }


    echo '</div>';


    echo '<input type="submit" name="update" value="' . Language::string(56) . '" ' . $disabled . '/>';

  echo '</form>';
}

// Ticket
function section_ticket( $groupID, $section ) {
  //Require global variable
  global $url, $page, $url_page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Start form
  $form = new HTML('form', array(
    'action' => $url_page . '&view=' . $groupID . '&section=' . $section,
    'method' => 'post',
    'additional' => 'class="form-50 box-width"',
  ));

  // Title
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'ticket_title',
      'value' => $group->values()["ticket_title"],
      'placeholder' => Language::string(60),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Logo
  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'ticket_logo_fileID',
      'value' => $group->values()['ticket_logo_fileID'],
      'headline' => Language::string(61),
      'select_info' => Language::string(65),
      'preview_image' => empty($group->values()["ticket_logo_fileID"]) ? '' : MediaHub::getUrl( $group->values()["ticket_logo_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Advert 1
  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'ticket_advert1_fileID',
      'value' => $group->values()['ticket_advert1_fileID'],
      'headline' => Language::string(62),
      'select_info' => Language::string(65),
      'preview_image' => empty($group->values()["ticket_advert1_fileID"]) ? '' : MediaHub::getUrl( $group->values()["ticket_advert1_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Advert 2
  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'ticket_advert2_fileID',
      'value' => $group->values()['ticket_advert2_fileID'],
      'headline' => Language::string(63),
      'select_info' => Language::string(66),
      'preview_image' => empty($group->values()["ticket_advert2_fileID"]) ? '' : MediaHub::getUrl( $group->values()["ticket_advert2_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Advert 3
  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'ticket_advert3_fileID',
      'value' => $group->values()['ticket_advert3_fileID'],
      'headline' => Language::string(64),
      'select_info' => Language::string(67),
      'preview_image' => empty($group->values()["ticket_advert3_fileID"]) ? '' : MediaHub::getUrl( $group->values()["ticket_advert3_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Button
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'ticket',
      'value' => Language::string(68),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );


  echo '<div class="grid-container">';
    // form
    $form->prompt();

    // Ticket
    $ticketToken = Ticket::encryptTicketToken( $group->groupID, "demo");

    echo '<div class="ticket-preview">';
      if( User::w_access_allowed($page, $current_user) ) {
        echo '<div class="ticket-preview-info-box">' . Language::string(69) . '</div>';
      }
      echo '<iframe src="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticketToken ) . '">' . Language::string(70) . '</iframe>';
    echo '</div>';

  echo '</div>';
}

// Mail
function section_mail( $groupID, $section ) {
  //Require global variable
  global $url, $page, $url_page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Start form
  $form = new HTML('form', array(
    'action' => $url_page . '&view=' . $groupID . '&section=' . $section,
    'method' => 'post',
    'additional' => 'class="form-50 box-width"',
  ));

  // Logo
  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'mail_banner_fileID',
      'value' => $group->values()['mail_banner_fileID'],
      'headline' => Language::string(80),
      'select_info' => Language::string(81),
      'preview_image' => empty($group->values()["mail_banner_fileID"]) ? '' : MediaHub::getUrl( $group->values()["mail_banner_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // From
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'mail_from',
      'value' => $group->values()["mail_from"],
      'placeholder' => Language::string(82),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Title
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'mail_displayName',
      'value' => $group->values()["mail_displayName"],
      'placeholder' => Language::string(83),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Subject
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'mail_subject',
      'value' => $group->values()["mail_subject"],
      'placeholder' => Language::string(84),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Buttons
  $form->customHTML('<div
                      class="btn-msg-container" ' .
                      (User::w_access_allowed($page, $current_user) ? 'onclick="mailAppendVal(event)"' : '') . '>');
  $form->customHTML('<span>' . Language::string(85) . '</span>');
  $form->customHTML('<span>' . Language::string(86) . '</span>');
    if(! empty(json_decode($group->values()["custom"], true))) {
      foreach(json_decode($group->values()["custom"], true) as $input) {
        $form->customHTML('<span>' . $input["name"] . '</span>');
      }
    }
  $form->customHTML('</div>');

  // Message
  $form->addElement(
    array(
      'type' => 'textarea',
      'name' => 'mail_msg',
      'value' => str_replace("<br />", "", $group->values()["mail_msg"]),
      'placeholder' => Language::string(87),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Button
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' => Language::string(88),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Ticket preview
  $ticketPreview = '<div class="ticket-preview">';
    if( User::w_access_allowed($page, $current_user) ) {
      $ticketPreview .= '<div class="ticket-preview-info-box">' . Language::string(89) . '</div>';
    }
    $ticketPreview .= '<div class="email-header">';
      //Get initials
      if(empty($group->values()["mail_displayName"])) {
        //No display name
        $initialsArray = explode(" ", $group->values()["mail_from"]);
        $initials = (count($initialsArray) > 1) ? substr($initialsArray[0], 0, 1) . substr($initialsArray[1], 0, 1) : substr($initialsArray[0], 0, 1); //Check if two or one char
      }else {
        $initialsArray = explode(" ", $group->values()["mail_displayName"]); //User has display name
        $initials = (count($initialsArray) > 1) ? substr($initialsArray[0], 0, 1) . substr($initialsArray[1], 0, 1) : substr($initialsArray[0], 0, 1); //Check if two or one char
      }

      //Get bannner image
      if( isset( $group->values()["mail_banner_fileID"] )) {
        $imgUrl = MediaHub::getUrl( $group->values()["mail_banner_fileID"] ); //No image found\Logo of tktdata
      } else {
        $imgUrl = $url . 'medias/logo/logo-fitted.png'; //No image found\Logo of tktdata
      }

      $ticketPreview .= '<div
                          class="circle-initials"
                          title="' . $initials . '">
                            <span>' . $initials . '</span>
                          </div>';
      $ticketPreview .= '<div class="message-lines">';
        $ticketPreview .= '<span class="from">' .
                            Language::string(90) . ' ' .
                            (empty($group->values()["mail_displayName"]) ? $group->values()["mail_from"] : $group->values()["mail_displayName"]) .
                            ' <span class="mail">&lt;' . $group->values()["mail_from"] . '&gt;</span>
                          </span>';
        $ticketPreview .= '<span class="subject">' . Language::string(91) . ' ' . $group->values()["mail_subject"] . '</span>';
      $ticketPreview .= '</div>';
    $ticketPreview .= '</div>';

    $ticketPreview .= '    <style>
          @media screen and (max-width: 700px) {

            table[class="mail-container"] {
              width: 100% !important;
            }

            img[class="logo"] {
              margin: 20px 0px !important;
            }

          }
        </style>';

    //Message
    $msg = str_replace("%Ticket%", '<table width="100%" cellspacing="0" cellpadding="0"><tr><td><table cellspacing="0" cellpadding="0"><tr><td style="border-radius: 2px;" bgcolor="#232b43"><a href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( Ticket::encryptTicketToken( $group->groupID, "demo") ) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">Dein Ticket</a></td></tr></table></td></tr></table>', $group->values()["mail_msg"]);


    $ticketPreview .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->
          <tr>
            <td>
              <table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 10px 10% 40px 10%; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->
                <tr>
                  <td>

                    <table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; color: #232b43; font-size: 15pt;font-weight: bolder;  margin: 50px 0px;">
                      <tr>
                        <td>
                          <img src="' . $imgUrl . '" style="display: block; width: 100%;" class="logo" alt="TKTDATA TICKETSYSTEM">
                        </td>
                      </tr>
                    </table>


                    <table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">
                      <tr>
                        <td style="margin: 20px 0px;">
                          ' . utf8Html( $msg ) . '
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>';
  $ticketPreview .= '</div>';

  echo '<div class="grid-container">';
    // form
    $form->prompt();

    // Mail
    echo $ticketPreview;
  echo '</div>';
}

// Payment
function section_payment( $groupID, $section ) {
  //Require global variable
  global $url, $page, $url_page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Start form
  $form = new HTML('form', array(
    'action' => $url_page . '&view=' . $groupID . '&section=' . $section,
    'method' => 'post',
    'additional' => 'class="payment"',
  ));

  $form->customHTML('<p>' . Language::string(100) . '</p>');
  $form->customHTML( Language::string(101) );

  $form->customHTML('<div class="btn-msg-container">');
    if( User::w_access_allowed($page, $current_user) ) {
      $form->customHTML('<span
                          onclick="document.getElementsByName(\'payment_mail_msg\')[0].value += \'%E-Mail%\';">' .
                          Language::string(102) .
                        '</span>');
      $form->customHTML('<span
                          onclick="document.getElementsByName(\'payment_mail_msg\')[0].value += \'%Pay-Link%\';">' .
                          Language::string(103) .
                        '</span>');
    }else {
      $form->customHTML('<span>' . Language::string(102) . '</span>');
      $form->customHTML('<span>' . Language::string(103) . '</span>');
    }
  $form->customHTML('</div>');

  // Mail message
  $form->addElement(
    array(
      'type' => 'textarea',
      'name' => 'payment_mail_msg',
      'value' => str_replace("<br />", "", ($group->values()["payment_mail_msg"] ?? '')),
      'placeholder' => Language::string(104),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Payrexx
  $form->customHTML('<p>' . Language::string(105) . '</p>');
  $form->customHTML( Language::string(106) );

  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'payment_payrexx_instance',
      'value' => $group->values()["payment_payrexx_instance"],
      'placeholder' => Language::string(107),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'payment_payrexx_secret',
      'value' => $group->values()["payment_payrexx_secret"],
      'placeholder' => Language::string(108),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Store
  $form->customHTML('<p>' . Language::string(109) . '</p>');
  $form->customHTML( Language::string(110, array(
    '%url%' => $url,
    '%group%' => $groupID,
  )));

  $form->addElement(
    array(
      'type' => 'checkbox',
      'name' => 'payment_store',
      'value' => 'true',
      'checked' => ($group->values()["payment_store"] == 1),
      'context' => Language::string(111),
      'additional_div' => 'title="' . Language::string(112) . '"',
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Languages
  $options = array();
  foreach( Language::all() as $language ) {
    $options[$language["code"]] = $language["loc"] . ' (' . $language["int"] . ')';
  }

  $form->addElement(
    array(
      'type' => 'select',
      'name' => 'payment_store_language',
      'value' => $group->values()["payment_store_language"] ?? null,
      'headline' => (isset($group->values()["payment_store_language"]) ? $options[$group->values()["payment_store_language"]] : Language::string(113)),
      'options' => $options
    ),
  );

  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'payment_logo_fileID',
      'value' => $group->values()['payment_logo_fileID'],
      'headline' => Language::string(114),
      'select_info' => Language::string(115),
      'preview_image' => empty($group->values()["payment_logo_fileID"]) ? '' : MediaHub::getUrl( $group->values()["payment_logo_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  $form->addElement(
    array(
      'type' => 'image',
      'name' => 'payment_background_fileID',
      'value' => $group->values()['payment_background_fileID'],
      'headline' => Language::string(116),
      'select_info' => Language::string(117),
      'preview_image' => empty($group->values()["payment_background_fileID"]) ? '' : MediaHub::getUrl( $group->values()["payment_background_fileID"] ),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // ADFS
  if(SIMPLE_SAML_CONFIG === null) {
    $form->customHTML('<div style="opacity: 0.5; margin-bottom: 12.5px;">');

      $form->customHTML('<p>' . Language::string(118) . '</p>');
      $form->customHTML( Language::string(119) );

      $form->addElement(
        array(
          'type' => 'checkbox',
          'name' => 'adfs',
          'value' => 'true',
          'checked' => ($group->values()["adfs"] == 1),
          'context' => Language::string(121),
          'additional_div' => 'title="' . Language::string(122) . '"',
          'disabled' => ! User::w_access_allowed($page, $current_user),
        ),
      );


    $form->customHTML('</div>');
  }else {
    $form->customHTML('<p>' . Language::string(118) . '</p>');
    $form->customHTML( Language::string(120) );

    $form->addElement(
      array(
        'type' => 'checkbox',
        'name' => 'adfs',
        'value' => 'true',
        'checked' => ($group->values()["adfs"] == 1),
        'context' => Language::string(121),
        'additional_div' => 'title="' . Language::string(122) . '"',
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    // ADFS
    $customUserInputs = json_decode($group->values()["custom"], true);
    $customADFS = json_decode($group->values()["adfs_custom"], true);

    $form->customHTML('<span style="display: block; margin-top: 12.5px;">' . Language::string(123) . '</span>');

    $form->addElement(
      array(
        'type' => 'text',
        'name' => 'adfs_custom[email]',
        'value' => $customADFS["email"] ?? '',
        'placeholder' => Language::string(124),
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    if(! empty($customUserInputs)) {
      // Set id and remove unused elements
      for($i = 0; $i < count($customUserInputs); $i++) {
        // Unset elements
        unset($customUserInputs[$i]["placeholder"]);

        // Set new values
        $customUserInputs[$i] = array_merge(array("id" => $i), $customUserInputs[$i]); //Id of input
      }

      // Order array by user input
      foreach($customUserInputs as $key => $value) {
        $orders[$key] = intval($value["order"]);
      }
      array_multisort($orders, SORT_ASC, $customUserInputs);

      // List all inputs
      for($i = 0; $i < count($customUserInputs); $i++) {
        $form->addElement(
          array(
            'type' => 'text',
            'name' => 'adfs_custom[' . $customUserInputs[$i]["id"] . ']',
            'value' => $customADFS[$customUserInputs[$i]["id"]] ?? '',
            'placeholder' => $customUserInputs[$i]["name"],
            'disabled' => ! User::w_access_allowed($page, $current_user),
          ),
        );
      }
    }
  }

  // Button
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' => Language::string(125),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  $form->prompt();
}

// SDK
function section_sdk( $groupID, $section ) {
  //Require global variable
  global $url, $page, $url_page, $current_user;

  // Start group
  $group = new Group();
  $group->groupID = $groupID;

  // Refresh secret key
  if(isset($_GET["refresh"])) {
    Action::confirm(
      Language::string(130),
      $_GET["view"],
      "&view=" . $_GET["view"] . "&section=" . $section
    );
  }

  $html = '<div class="sdk-code">';
    $html .= '<p>' . Language::string(131) . '</p>';

    //Info
    $html .= Language::string(132) . '<br />';

    //Secret key
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" value="' . Crypt::decrypt($group->values()["sdk_secret_key"]) . '"/>';
      if( User::w_access_allowed($page, $current_user)) {
        $html .= '<a class="refresh" href="' . $url_page . '&view=' . $_GET["view"] . '&section=' . $section . '&refresh"><img src="' . $url . 'medias/icons/restore.svg"/></a>';
      }else {
        $html .= '<a class="refresh disabled"><img src="' . $url . 'medias/icons/restore.svg"/></a>';
      }
    $html .= '</label>';

    //Notice
    $html .= Language::string(133);

  $html .= '</div>';

  //SDK Download
  $file = file_get_contents(dirname(__FILE__, 2) . "/sdk/tktdata.phps");
  $fileWithHost = str_replace("YOUR_HOST/", $url. "sdk/req.php", $file); //prepare new file
  file_put_contents(dirname(__FILE__, 2) . "/sdk/tktdata.php.txt", $fileWithHost);

  $html .= '<div class="sdk-code">';
    $html .= '<p>' . Language::string(134) . '</p>';
    $html .= Language::string(135) . '<br />';
    $html .= '<a href="' . $url . 'sdk/tktdata.php.txt" class="button" title="' . Language::string(136) . '" download>' . Language::string(137) . '</a>';
  $html .= '</div>';

  //Simple implementation
  $html .= '<div class="sdk-code">';
    $html .= '<p>' . Language::string(138) . '</p>';
    $html .= Language::string(139, array(
      '%url_page%' => $url_page,
      '%group%' => $groupID,
    )) . '<br />';
  $html .= '</div>';

  //Documentation list
  $html .= '<div class="sdk-code">';
    $html .= '<p>' . Language::string(140) . '</p>';
    $html .= Language::string(141);
    $html .= '<ul style="margin-left: 15px;">';
      $html .= '<li><a href="#sdk-ticketinfos">' . Language::string(142) . '</a></li>';
      $html .= '<li><a href="#sdk-ticketToken">' . Language::string(143) . '</a></li>';
      $html .= '<li><a href="#sdk-addTicket">' . Language::string(144) . '</a></li>';
      $html .= '<li><a href="#sdk-updateTicket">' . Language::string(145) . '</a></li>';
      $html .= '<li><a href="#sdk-removeTicket">' . Language::string(146) . '</a></li>';
      $html .= '<li><a href="#sdk-restoreTicket">' . Language::string(147) . '</a></li>';
      $html .= '<li><a href="#sdk-send-ticket">' . Language::string(148) . '</a></li>';
      $html .= '<li><a href="#sdk-get-coupon">' . Language::string(149) . '</a></li>';
      $html .= '<li><a href="#sdk-check-coupon">' . Language::string(150) . '</a></li>';
      $html .= '<li><a href="#sdk-price-coupon">' . Language::string(151) . '</a></li>';
      $html .= '<li><a href="#sdk-getGroup">' . Language::string(152) . '</a></li>';
      $html .= '<li><a href="#sdk-usedTickets">' . Language::string(153) . '</a></li>';
      $html .= '<li><a href="#sdk-availableTickets">' . Language::string(154) . '</a></li>';
      $html .= '<li><a href="#sdk-availableTickets">' . Language::string(155) . '</a></li>';
      $html .= '<li><a href="#sdk-requestGateway">' . Language::string(156) . '</a></li>';
      $html .= '<li><a href="#sdk-deleteGateway">' . Language::string(157) . '</a></li>';
      $html .= '<li><a href="#sdk-requestTransaction">' . Language::string(158) . '</a></li>';
      $html .= '<li><a href="#sdk-requestPayment">' . Language::string(159) . '</a></li>';
    $html .= '</ul>';
  $html .= '</div>';

  //Ticketinformationen
  $html .= '<div class="sdk-code" id="sdk-ticketinfos">
              <p>' . Language::string(142) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->get_ticket(); /*JSON answer*/</code></pre>
          </div>';

  //ticket token
  $html .= '<div class="sdk-code" id="sdk-ticketToken">
              <p>' . Language::string(143) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$ticket_infos = $tktdata->find_ticketToken("Sample info"); /*JSON answer*/</code></pre>
            </div>';

  //Add ticket
  $html .= '<div class="sdk-code" id="sdk-addTicket">
              <p>' . Language::string(144) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$ticket_infos = $tktdata->add_ticket(array(<br />  "groupID" => "YOUR VALUE [REQUIRED]",<br />  "amount" => "YOUR VALUE",<br />  "payment" => "YOUR VALUE",<br />  "coupon" => "YOUR VALUE",<br />  "email" => "YOUR VALUE [REQUIRED]",<br />  "custom" => "YOUR VALUE"<br />)); /*True or an error in JSON*/</code></pre>
            </div>';

  //update ticket
  $html .= '<div class="sdk-code" id="sdk-updateTicket">
              <p>' . Language::string(145) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->update_ticket(array(<br />  "amount" => "YOUR VALUE",<br />  "state" => "YOUR VALUE", <br />  "payment" => "YOUR VALUE",<br />  "coupon" => "YOUR VALUE",<br />  "purchase_time" => "YOUR VALUE",<br />  "payment_time" => "YOUR VALUE",<br />  "employ_time" => "YOUR_VALUE" ,<br />  "email" => "YOUR VALUE [REQUIRED]",<br />  "custom" => "YOUR VALUE"<br />)); /*True or an error in JSON*/</code></pre>
            </div>';

  //Remove ticket
  $html .= '<div class="sdk-code" id="sdk-removeTicket">
              <p>' . Language::string(146) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->remove_ticket(); /*True or an error in JSON*/</code></pre>
            </div>';

  //Restore ticket
  $html .= '<div class="sdk-code" id="sdk-restoreTicket">
              <p>' . Language::string(147) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->restore_ticket(); /*True or an error in JSON*/</code></pre>
            </div>';

  //Send ticket
  $html .= '<div class="sdk-code" id="sdk-send-ticket">
              <p>' . Language::string(148) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->send_ticket(); /*True or false*/</code></pre>
            </div>';

  //get couponID by name
  $html .= '<div class="sdk-code" id="sdk-get-coupon">
              <p>' . Language::string(149) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$coupon_infos = $tktdata->get_couponID("NAME OF COUPON"); /*JSON answer*/</code></pre>
            </div>';

  //check_coupon
  $html .= '<div class="sdk-code" id="sdk-check-coupon">
              <p>' . Language::string(150) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$tktdata->couponID = "COUPON ID"; /*Coupon ID*/<br />$coupon_infos = $tktdata->check_coupon(); /*JSON answer*/</code></pre>
            </div>';

  //new coupon price
  $html .= '<div class="sdk-code" id="sdk-price-coupon">
              <p>' . Language::string(151) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$tktdata->couponID = "COUPON ID"; /*Coupon ID*/<br />$coupon_infos = $tktdata->new_coupon_price(); /*JSON answer*/</code></pre>
            </div>';

  //Get group
  $html .= '<div class="sdk-code" id="sdk-getGroup">
              <p>' . Language::string(152) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$group_infos = $tktdata->get_group(); /*JSON answer*/</code></pre>
            </div>';

  //used tickets
  $html .= '<div class="sdk-code" id="sdk-usedTickets">
              <p>' . Language::string(153) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->usedTickets(); /*Number*/</code></pre>
            </div>';

  //available tickets
  $html .= '<div class="sdk-code" id="sdk-availableTickets">
              <p>' . Language::string(154) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->availableTickets(); /*Number*/</code></pre>
            </div>';

  //tpu available tickets
  $html .= '<div class="sdk-code" id="sdk-availableTickets">
              <p>' . Language::string(155) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->tpu_available("E-MAIL OF USER"); /*Number*/</code></pre>
            </div>';

  //Request gateway
  $html .= '<div class="sdk-code" id="sdk-requestGateway">
              <p>' . Language::string(156) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->requestGateway(array(<br />  "success_link" => "YOUR VALUE",<br />  "fail_link" => "YOUR VALUE"<br />)); /*JSON answer*/</code></pre>
            </div>';

  //delete gateway
  $html .= '<div class="sdk-code" id="sdk-deleteGateway">
              <p>' . Language::string(157) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->deleteGateway(); /*JSON answer*/</code></pre>
            </div>';

  //requestTransaction
  $html .= '<div class="sdk-code" id="sdk-requestTransaction">
              <p>' . Language::string(158) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->requestTransaction(); /*JSON answer*/</code></pre>
            </div>';

  //checkPayment
  $html .= '<div class="sdk-code" id="sdk-requestPayment">
              <p>' . Language::string(159) . '</p>
              <pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->send_payment_amil(); /*true or false*/</code></pre>
            </div>';

  echo $html;

}


// Check if list or section
if( isset( $_GET["view"] ) ) {
  // Start group
  $group = new Group();
  $group->groupID = $_GET["view"];

  //Check if group exists
  if( isset( $group->values()["name"] ) ) {
    // Start topnav
    $topNav = new HTML('top-nav', array(
      'additional' => 'style="border-color: ' . $group->values()["color"] . ';"',
    ));

    $topNav->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/history-back.svg">',
        'link' => $url_page,
        'additional' => 'title="' . Language::string(8) . '"',
      ),
    );

    for( $i = 1; $i < 7; $i++ ) {
      $topNav->addElement(
        array(
          'link' => $url_page . '&view=' . $_GET["view"] . "&section=" . $i,
          'context' => Language::string( $i + 8 ),
          'additional' => (($_GET["section"] ?? 1) == $i) ? 'class="selected"' : '',
        ),
      );
    }

    $topNav->prompt();

    switch( $_GET["section"] ?? 1 ) {
      ////////////////////
      // General
      ////////////////////
      case 1:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            // Set price and VAT
            $_POST["price"] = isset($_POST["price"]) ? ($_POST["price"] * 100) : null;
            $_POST["vat"] = isset($_POST["vat"]) ? ($_POST["vat"] * 100) : null;

            if( $group->update( 1, $_POST )) {
              Action::success( Language::string(200) );
            }else{
              Action::fail( Language::string(201) );
            }
          }else {
            Action::fail( Language::string(202) );
          }
        }

        section_general( $_GET["view"], 1 );
      break;

      ////////////////////
      // Custom
      ////////////////////
      case 2:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            if( $group->update( 2, $_POST )) {
              Action::success( Language::string(203) );
            }else{
              Action::fail( Language::string(204) );
            }
          }else {
            Action::fail( Language::string(205) );
          }
        }

        section_custom( $_GET["view"], 2 );
      break;

      ////////////////////
      // Ticket
      ////////////////////
      case 3:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            if( $group->update( 3, $_POST )) {
              Action::success( Language::string(206) );
            }else{
              Action::fail( Language::string(207) );
            }
          }else {
            Action::fail( Language::string(208) );
          }
        }

        section_ticket( $_GET["view"], 3 );
      break;

      ////////////////////
      // Mail
      ////////////////////
      case 4:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            if( $group->update( 4, $_POST )) {
              Action::success( Language::string(209) );
            }else{
              Action::fail( Language::string(210) );
            }
          }else {
            Action::fail( Language::string(211) );
          }
        }

        section_mail( $_GET["view"], 4 );
      break;

      ////////////////////
      // Payment
      ////////////////////
      case 5:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            if( $group->update( 5, $_POST )) {
              Action::success( Language::string(212) );
            }else{
              Action::fail( Language::string(213) );
            }
          }else {
            Action::fail( Language::string(214) );
          }
        }

        section_payment( $_GET["view"], 5 );
      break;

      ////////////////////
      // SDK
      ////////////////////
      case 6:
        // Check if update
        if(! empty($_POST) ) {
          if( User::w_access_allowed($page, $current_user)) {
            if( $group->update( 6, $_POST )) {
              Action::success( Language::string(215) );
            }else{
              Action::fail( Language::string(216) );
            }
          }else {
            Action::fail( Language::string(217) );
          }
        }

        section_sdk( $_GET["view"], 6 );
      break;
    }
  }else {
    // Group does not exist
    Action::fs_info(
      Language::string( 160, array(
        '%group%' => $_GET["view"],
      ))
    );
  }
}elseif( isset( $_GET["remove"] ) ) {
  // Start group
  $group = new Group();
  $group->groupID = $_GET["remove"];

  // Generate info
  $info = Language::string( 218, array(
    '%id%' => $group->groupID,
    '%name%' => $group->values()["name"],
  ));

  //Display request
  Action::confirm( $info,  $_GET["remove"]);
}elseif( isset( $_GET["add"] ) ) {
  // Start group
  $group = new Group();

  //Add if necessary
  if(! empty( $_POST )) {
    //Check if user is permitted to do this action
    if(User::w_access_allowed($page, $current_user)) {
      if( $group->add( $_POST ) ) {
        Action::success(
          Language::string( 222, array(
            '%url_page%' => $url_page,
            '%id%' => $group->groupID,
          ) )
        );
      }else{
        Action::fail( Language::string(223) );
      }
    }else {
      Action::fail( Language::string(224) );
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
      'additional' => 'title="' . Language::string(170) . '"',
    ),
  );

  // Start form
  $form = new HTML('form', array(
    'action' => $url_page . '&add',
    'method' => 'post',
    'additional' => 'style="width: 100%; max-width: 750px;" class="box-width"',
  ));

  // Headline
  $form->customHTML('<h1>' . Language::string(171) . '</h1>');

  // Groupname
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'name',
      'placeholder' => Language::string(20),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  // Button
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'create',
      'value' => Language::string(172),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true
    ),
  );

  $topNav->prompt();
  $form->prompt();
}else {
  // Add remoe
  if(isset($_POST["confirm"])) {
    if( User::w_access_allowed($page, $current_user)) {

      $group = new Group();
      $group->groupID = $_POST["confirm"];

      //Remove group
      if( $group->remove()) {
        Action::success(
          Language::string( 219, array(
            '%id%' => $_POST["confirm"],
          ))
        );
      }else {
        Action::fail(
          Language::string( 220, array(
            '%id%' => $_POST["confirm"],
          ))
        );
      }
    }else {
      Action::fail( Language::string(221) );
    }
  }

  // Generate search value
  $search_value = (!empty($_GET["s"])) ? $_GET["s"] : '';

  // List all
  list_groups( $search_value );

  //Add button
  echo '<a class="add" href="' . $url_page . '&add">
    <span class="horizontal"></span>
    <span class="vertical"></span>
  </a>';
}
 ?>
