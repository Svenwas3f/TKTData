<?php
class GroupCustomizer extends Group {
  //Define public variable
  public $subpage;

  /**
   * Function to list all groups
   */
  public function list( $search_value = null ) {
    //Define variables
    global $url;
    global $url_page;
    global $page;
    global $current_user;
    global $conn;

    //Start table
    $html = '<table class="rows">';

    //Headline
    $headline_names = array('Name', 'Verwendung', 'Verkaufszeit', 'Aktion');

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

    // Get content
    foreach( Group::all( $offset, $steps, $search_value ) as $group ) {
        //Used tickets
        $currentGroup = new Group();
        $currentGroup->groupID = $group["groupID"];

        $html .= '<tr class="table-list">'; //Start row
          $html .= '<td style="width: 20%;"><div class="color" style="background-color: ' . $group["color"] . ';"></div>' . $group["name"] . '</td>'; //Display user id
          $html .= '<td style="width: 40%;">' . $currentGroup->ticketsNum() . '/' . $group["maxTickets"] . '</td>'; //Display Name (pre and lastname)
          //Check date
          $startTime = ($group["startTime"] == $group["endTime"] ) ? "Zeitlich" : date("d.m.Y H:i:s", strtotime( $group["startTime"] ));
          $endTime = ($group["startTime"] == $group["endTime"] ) ? "unbeschr&auml;nkt" : date("d.m.Y H:i:s", strtotime( $group["endTime"] ));
          $html .= '<td style="width: 20%;">' . $startTime . '-<br />' . $endTime . '</td>'; //Display purchase date


          //Check if current user (logged in user) can edit or see the user
          if( User::w_access_allowed($page, $current_user) ) {
            //Current user can edit and delete user
            $html .= '<td style="width: auto;">
                        <a href="' . $url_page . '&view=' . $group["groupID"] . '" title="Details anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a><a href="' . $url_page . ((isset( $_GET["row-start"] )) ? "&row-start=" . $_GET["row-start"] : "")  . '&remove=' . $group["groupID"] . '" title="Löschen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
            $html .= '</td>';
          } elseif( User::r_access_allowed($page, $current_user) ) {
            $html .= '<td style="width: auto;">
                        <a href="' . $url_page . '&view=' . $group["groupID"] . '" title="Details anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>
                      </td>';
          }
        $html .= '</tr>'; //End row
    }

    // Menu requred
    $html .= '<tr class="nav">';

      if( (count(Group::all( ($offset + $steps), 1, $search_value )) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
        $html .= '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                    <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps + 1, PHP_ROUND_HALF_UP) . '" style="float: right;">Weiter</a>
                  </td>';
      }elseif ( ($offset/$steps) > 0 ) { // Less pages accessables
        $html .= '<td colspan="' . count( $headline_names ) . '">
                    <a href="' . $url_page . '&list=checkout&row-start=' . round($offset/$steps - 1, PHP_ROUND_HALF_UP) . '" style="float: left;">Letze</a>
                  </td>';
      }elseif (count(Group::all( ($offset + $steps), 1 )) > 0) { // More pages accessable
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

  /**
   * Function to display submenu of group page
   */
  public function menu() {
    //Require variables
    global $url_page;

    //Headline with main color
    $html = '<div class="headline-maincolor" style="background-color: ' . $this->values()["color"] . '"></div>';

    //Set menu_elements_name
    $menu_elements = array(
      1 => "Allgemein",
      2 => "Formular",
      3 => "Ticket",
      4 => "Mail",
      5 => "Payment", //Rechnung
      6 => "SDK"
    );

    //Create html
    $html .= '<div class="group-nav">';
    foreach($menu_elements as $key => $name) {
      $cssClass = ($key == $this->subpage) ? 'class="selected"' : '';
      $html .= '<a href="' .  $url_page . '&view=' . $this->groupID . "&selection="  . $key . '" ' . $cssClass . '>' . $name . '</a>';
    }
    $html .= '</div>';

    //Return html
    echo $html;
  }

  /**
   * Function to display general infos
   */
  public function general() {
    //require variables
    global $conn;
    global $page;
    global $url;
    global $current_user;

    //Get group info
    $groupInfo = $this->values();

    //Define disabled
    $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

    //Start form to edit, show user
    $html = '<form action="" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
    //Gruppenname
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="name" value="' . $groupInfo["name"] . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder">Gruppenname</span>';
    $html .= '</label>';

    //Maximum tickets
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" name="maxTickets" min="0" value="' . $groupInfo["maxTickets"] . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder">Maximum Tickets</span>';
    $html .= '</label>';

    //Tickets per user
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" name="tpu" min="0" value="' . $groupInfo["tpu"] . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder">Tickets pro Benutzer</span>';
    $html .= '</label>';

    //Currency
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="currency" max-length="3" value="' . $groupInfo["currency"] . '" onkeyup="document.getElementsByClassName(\'unit\')[0].innerHTML = this.value" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code " target="_blank">Währung</a></span>';
    $html .= '</label>';

    //Price
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" name="price" min="0" step="0.05" value="' . number_format(($groupInfo["price"] / 100), 2) . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder">Betrag</span>';
      $html .= '<span class="unit">' . $groupInfo["currency"] . '</span>';
    $html .= '</label>';

    //Starttime
    $html .= '<label class="txt-input">';
      $html .= '<input type="datetime-local" name="startTime" value="' . ($groupInfo["startTime"] == '0000-00-00 00:00:00' ? '0000-00-00T00:00:00' : date('Y-m-d\TH:i:s', strtotime($groupInfo["startTime"]))) . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Verwenden Sie dasselbe Datum sowie Zeit um das Ticket zeitlich unbeschr&auml;nkt an zu bieten (In Start und Endzeit)">Startzeit</abbr></span>';
    $html .= '</label>';

    //Endtime
    $html .= '<label class="txt-input">';
      $html .= '<input type="datetime-local" name="endTime" value="' . ($groupInfo["endTime"] == '0000-00-00 00:00:00' ? '0000-00-00T00:00:00' : date('Y-m-d\TH:i:s', strtotime($groupInfo["endTime"]))) . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Verwenden Sie dasselbe Datum sowie Zeit um das Ticket zeitlich unbeschr&auml;nkt an zu bieten (In Start und Endzeit)">Endzeit</abbr></span>';
    $html .= '</label>';

    //vat
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" name="vat" min="0" step="0.05" value="' . ($groupInfo["vat"] / 100) . '" ' . $disabled . '/>';
      $html .= '<span class="placeholder"><abbr title="Value-Added Tax (MwSt.)">VAT</abbr></span>';
      $html .= '<span class="unit">%</span>';
    $html .= '</label>';

    //Description
    $html .= '<label class="txt-input">';
      $html .= '<textarea name="description" rows="5" ' . $disabled . '/>' . $groupInfo["description"] . '</textarea>';
      $html .= '<span class="placeholder">Beschreibung</span>';
    $html .= '</label>';

    //Add submit button
    $html .= '<input type="submit" name="general" value="Update" ' . $disabled . '/>';

    //Close form
    $html .= '</form>';
    /**
     * Display content
     */
    echo $html;
  }

  /**
   * Function to display custom userinputs
   */
  public function userInputs() {
    //require variables
    global $conn;
    global $page;
    global $current_user;

    //Get group info
    $groupInfo = $this->values();

    //Start form to edit, show user
    $html = '<form action="" method="post">';
    //Custom elements
    $customElements = json_decode($groupInfo["custom"], true);

    //add custom elements
    if(User::w_access_allowed($page, $current_user)) {
      $html .= '<div class="addInput">';
        $html .= '<span class="button" onclick="add_checkbox()">Checkbox</span>';
        $html .= '<span class="button" onclick="add_text(\'date\')">Datum</span>';
        $html .= '<span class="button" onclick="add_text(\'email\')">E-Mail</span>';
        $html .= '<span class="button" onclick="add_text(\'number\')">Nummer</span>';
        $html .= '<span class="button" onclick="add_radio()">Radiobutton</span>';
        $html .= '<span class="button" onclick="add_select()">Selection</span>';
        $html .= '<span class="button" onclick="add_text(\'text\')">Text</span>';
        $html .= '<span class="button" onclick="add_text(\'textarea\')">Textfeld</span>';
      $html .= '</div>';
    }

    $html .= '<div class="customFormFields">';
    $custom["id"] = 0;

    //Define disabled
    $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

    //Get heighest id
    $max_id = empty($customElements) ? 0 : max(array_column($customElements, "id"));
    $html .= '<input type="hidden" name="current_id" value="' . $max_id . '"/>';

    //Check array
    if( count($customElements) > 0) {
      //Sort array
      foreach($customElements as $key => $value) {
        $orders[$key] = intval($value["order"]);
      }
      array_multisort($orders, SORT_ASC, $customElements);

      foreach( $customElements as $custom ) {
        //---------------
        //Start container
        //---------------
        $container = '<div id="container-' . $custom["id"] . '" class="container-custom-form">';
        //Hidden input
        $container .= '<input type="hidden" name="hidden[]" value="' . $custom["type"] . '%' . $custom["id"] . '%">';
        //Headline
        $container .= '<div><h1 style="display: inline-block">' . ucfirst($custom["type"]) . '-Element</h1><span onclick="removeField(' . $custom["id"] . ')" style="margin: 0px 5px;">Delete</span></div>';
        if($custom["type"] == 'select' || $custom["type"] == 'radio'){
          //Selection or Radioform
          $container .= '<input type="text" name="customField' . $custom["id"] . '[]" placeholder="Name" value="' . $custom["name"] . '" required="true" ' . $disabled . '/>';
          $container .= '<input type="number" name="customField' . $custom["id"] . '[]" placeholder="Reihenfolge" value="' . $custom["order"] . '" ' . $disabled . '/>';
          $container .= '<input type="checkbox" name="customField' . $custom["id"] . '[]" placeholder="Name" value="1" ' . ($custom["required"]==1?"checked":"") . ' ' . $disabled . '/>(Pflichtfeld)';
          $container .= '<span class="button" onclick="addMultiple(' . $custom["id"] . ')" style="margin-bottom: 5px;">Auswahl hinzufügen</span>';
          //Get all options
          $options = explode(',', $custom["value"]);

          for($optionI = 0; $optionI < COUNT($options) - 1; $optionI++){
            $container .= '<div id="multipleContainer-' . $custom["id"].$optionI . '" class="multipleContainer">';
              $container .= '<input type="text" name="multiple' . $custom["id"] . '[]" placeholder="Name" value="' . $options[$optionI] . '" ' . $disabled . '>';
              $container .= '<span onclick="removeMultiple(' . $custom["id"] . ', ' . $optionI . ')" style="margin: 0px 5px;">Delete</span>';
            $container .= '</div>';
          }
        }elseif($custom["type"] == 'checkbox'){
          //Checkbox form
          $container .= '<input type="text" name="customField' . $custom["id"] . '[]" placeholder="Name" value="' . $custom["name"] . '" required="true" ' . $disabled . '/>';
          $container .= '<input type="number" name="customField' . $custom["id"] . '[]" placeholder="Reihenfolge" value="' . $custom["order"] . '" ' . $disabled . '/>';
          $container .= '<input type="checkbox" name="customField' . $custom["id"] . '[]" placeholder="Name" value="1" ' . ($custom["required"]==1?"checked":"") . '  ' . $disabled . '/>(Pflichtfeld)';
        }else{
          //Text form
          $container .= '<input type="text" name="customField' . $custom["id"] . '[]" placeholder="Name" value="' . $custom["name"] . '" required="true" ' . $disabled . '/>';
          $container .= '<input type="text" name="customField' . $custom["id"] . '[]" placeholder="Platzhalter" value="' . $custom["placeholder"] . '" required="true" ' . $disabled . '/>';
          $container .= '<input type="number" name="customField' . $custom["id"] . '[]" placeholder="Reihenfolge" value="' . $custom["order"] . '" ' . $disabled . '/>';
          $container .= '<input type="checkbox" name="customField' . $custom["id"] . '[]" placeholder="Name" value="1" ' . ($custom["required"]==1?"checked":"") . '  ' . $disabled . '/>(Pflichtfeld)';
        }
        $container .= '</div>';

        //Add to form and update $custom["id"]
        $html .= $container;
        $custom["id"]++;
      }
    }

    $html .= '</div><br />';
    //Add submit button
    $html .= '<input type="submit" name="update" value="Update" ' . $disabled . '/>';

    //Close form
    $html .= '</form>';

    /**
     * Display content
     */
    echo $html;
  }

  /**
   * Function to display ticket customizer
   */
  public function ticket() {
    //Require global variable
    global $url;
    global $page;
    global $current_user;

    //Define disabled
    $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

    //Create form
    $advertInfo = "Es wird nur dieser Inhalt angezeigt. Man kann kein Verhältnis verwenden und muss sich an diesen absoluten Werten orientieren.";
    $html = '<div class="grid-container">';
      $html .= '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*" class="form-50 box-width">';
        //Title
        $html .= '<label class="txt-input">';
          //Get Value
          $filePath = dirname(__FILE__, 2) . "/medias/groups/" . $this->groupID . "/ticket/title.txt";
          $inputValue = file_exists( $filePath ) ? file_get_contents( $filePath ) : "";
          $html .= '<input type="text" name="title" value="' . $inputValue . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Tickettitel</span>';
          //$html .= '<span class="unit">CHF</span>';
        $html .= '</label>';
        //logo
        $html .= '<span class="file-info">Logo</span>';
        $html .= '<label class="file-input">';
          $html .= '<input type="file" name="logo" onchange="previewImage(this)" ' . $disabled . '/>';
          //Display image if exists
          $file = glob("medias/groups/" . $this->groupID . "/ticket/logo/*"); //todo
          if( count($file) > 0) {
            $html .= '<div class="preview-image" style="background-image: url(\'' . $file[0] . '\')"></div>';
          }
          $html .= '<div class="draganddrop">Click to select file</div>';
        $html .= '</label>';
        //Advert 1
        $html .= '<span class="file-info">Advert 1 <abbr title="' . $advertInfo . '">(453px &#x00D7; 343px)</abbr></span>';
        $html .= '<label class="file-input">';
          //Display image if exists
          $file = glob("medias/groups/" . $this->groupID . "/ticket/adverts/advertImg0.*");
          if( count($file) > 0) {
            $html .= '<div class="preview-image" style="background-image: url(\'' . $file[0] . '\')"></div>';
          }
          $html .= '<input type="file" name="advert0" onchange="previewImage(this)" ' . $disabled . '/>';
          $html .= '<div class="draganddrop">Click to select file</div>';
        $html .= '</label>';
        //Advert 2
        $html .= '<span class="file-info">Advert 2 <abbr title="' . $advertInfo . '">(754px &#x00D7; 343px)</abbr></span>';
        $html .= '<label class="file-input">';
          $html .= '<input type="file" name="advert1" onchange="previewImage(this)" ' . $disabled . '/>';
          //Display image if exists
          $file = glob("medias/groups/" . $this->groupID . "/ticket/adverts/advertImg1.*");
          if( count($file) > 0) {
            $html .= '<div class="preview-image" style="background-image: url(\'' . $file[0] . '\')"></div>';
          }
          $html .= '<div class="draganddrop">Click to select file</div>';
        $html .= '</label>';
        //Advert 3
        $html .= '<span class="file-info">Advert 3 <abbr title="' . $advertInfo . '">(754px &#x00D7; 343px)</abbr></span>';
        $html .= '<label class="file-input">';
          $html .= '<input type="file" name="advert2" onchange="previewImage(this)" ' . $disabled . '/>';
          //Display image if exists
          $file = glob("medias/groups/" . $this->groupID . "/ticket/adverts/advertImg2.*");
          if( count($file) > 0) {
            $html .= '<div class="preview-image" style="background-image: url(\'' . $file[0] . '\')"></div>';
          }
          $html .= '<div class="draganddrop">Click to select file</div>';
        $html .= '</label>';

        //Submit
        $html .= '<input type="submit" name="ticket" value="UPDATE" ' . $disabled . '/>';

      $html .= '</form>';

      // $ticketToken = Crypt::encrypt( $this->groupID . ", demo");  Change 10.01.2021 Sven Waser TODO
      $ticketToken = Ticket::encryptTicketToken( $this->groupID, "demo");

      $html .= '<div class="ticket-preview">';
        $html .= '<div class="ticket-preview-info-box">&#9888; Klicken Sie auf Update, um ihre Änderungen zu sehen.</div>';
        $html .= '<iframe src="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticketToken ) . '">Loading preview</iframe>';
      $html .= '</div>';
    $html .= '</div>';

    //Display form
    echo $html;
  }

  /**
   * Function to display mail customizer
   */
  public function mail() {
    //Require variables
    global $url;
    global $page;
    global $current_user;

    //Define disabled
    $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

    //Get values
    $group = new Group();
    $group->groupID = $_GET["view"];
    $groupValues = $group->values();

    $html = '<div class="grid-container">';
      //Start form
      $html .= '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*" class="form-50 box-width">';
      //logo
      $html .= '<span class="file-info">Banner</span>';
      $html .= '<label class="file-input">';
        $html .= '<input type="file" name="banner" onchange="previewImage(this)" ' . $disabled . '/>';
        //Display image if exists
        $file = glob("medias/groups/" . $this->groupID . "/mail/banner/*");
        if(count($file) > 0) {
          $html .= '<div class="preview-image" style="background-image: url(\'' . $file[0] . '\')"></div>';
        }
        $html .= '<div class="draganddrop">Click to select file</div>';
      $html .= '</label>';

        //Absender
        $html .= '<label class="txt-input">';
          $html .= '<input type="text" name="mail_from" value="' . $groupValues["mail_from"] . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Absender</span>';
        $html .= '</label>';
        //Anzeigename
        $html .= '<label class="txt-input">';
          $html .= '<input type="text" name="mail_displayName" value="' . $groupValues["mail_displayName"] . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Anzeigename</span>';
        $html .= '</label>';
        //Betreff
        $html .= '<label class="txt-input">';
          $html .= '<input type="text" name="mail_subject" value="' . $groupValues["mail_subject"] . '" ' . $disabled . '/>';
          $html .= '<span class="placeholder">Betref</span>';
        $html .= '</label>';
        //Buttons for message
        $html .= '<div class="btn-msg-container" onclick="mailAppendVal(event)">';
          $html .= '<span>E-Mail</span>';
          $html .= '<span>Ticket</span>';
          if(! empty(json_decode($groupValues["custom"], true))) {
            foreach(json_decode($groupValues["custom"], true) as $input) {
              $html .= '<span>' . $input["name"] . '</span>';
            }
          }
        $html .= '</div>';
        //Message
        $html .= '<label class="txt-input">';
          $html .= '<textarea name="mail_msg" rows="4" ' . $disabled . '>' . str_replace("<br />", "", $groupValues["mail_msg"]) . '</textarea>';
          $html .= '<span class="placeholder">Nachricht</span>';
        $html .= '</label>';

        $html .= '<input type="submit" value="Update" name="update" ' . $disabled . '/>';

      $html .= '</form>';

      $html .= '<div class="ticket-preview">';
        $html .= '<div class="ticket-preview-info-box">&#9888; Klicken Sie auf Update, um ihre Änderungen zu sehen.</div>';
        $html .= '<div class="email-header">';
          //Get initials
          if(empty($groupValues["mail_displayName"])) {
            //No display name
            $initialsArray = explode(" ", $groupValues["mail_from"]);
            $initials = (count($initialsArray) > 1) ? substr($initialsArray[0], 0, 1) . substr($initialsArray[1], 0, 1) : substr($initialsArray[0], 0, 1); //Check if two or one char
          }else {
            $initialsArray = explode(" ", $groupValues["mail_displayName"]); //User has display name
            $initials = (count($initialsArray) > 1) ? substr($initialsArray[0], 0, 1) . substr($initialsArray[1], 0, 1) : substr($initialsArray[0], 0, 1); //Check if two or one char
          }

          //Get header image
          $path = dirname(__FILE__, 2) . '/medias/groups/' . $group->groupID . '/mail/banner/*'; //Path where img is stored

          if(count(glob($path)) > 0) {
            $imgUrl = $url . 'medias/groups/' . $group->groupID . "/mail/banner/" . pathinfo(glob($path)[0], PATHINFO_BASENAME); //Onw image
          }else {
            $imgUrl = $url . 'medias/logo/logo-fitted.png'; //No image found\Logo of tktdata
          }

          $html .= '<div class="circle-initials" title="' . $initials . '"><span>' . $initials . '</span></div>';
          $html .= '<div class="message-lines">';
            $html .= '<span class="from">Von: ' . (empty($groupValues["mail_displayName"]) ? $groupValues["mail_from"] : $groupValues["mail_displayName"]) . ' <span class="mail">&lt;' . $groupValues["mail_from"] . '&gt;</span></span>';
            $html .= '<span class="subject">Betreff: ' . $groupValues["mail_subject"] . '</span>';
          $html .= '</div>';
        $html .= '</div>';

        $html .= '    <style>
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
        $msg = str_replace("%Ticket%", '<table width="100%" cellspacing="0" cellpadding="0"><tr><td><table cellspacing="0" cellpadding="0"><tr><td style="border-radius: 2px;" bgcolor="#232b43"><a href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( Ticket::encryptTicketToken( $group->groupID, "demo") ) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">Dein Ticket</a></td></tr></table></td></tr></table>', $groupValues["mail_msg"]);


        $html .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->
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
      $html .= '</div>';
    $html .= '</div>';

    echo $html;
  }

  /**
   * Function to display payment customizer
   */
  public function payment() {
    //Require variables
    global $url;
    global $page;
    global $current_user;

    //Define disabled
    $disabled = (User::w_access_allowed($page, $current_user)) ? "" : "disabled";

    //Get values
    $group = new Group();
    $group->groupID = $_GET["view"];
    $groupValues = $group->values();

    $html = '<form method="post" action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" enctype="multipart/form-data" accept="image/*" class="sdk-code">';
      //Payment request mail message
      $html .= '<p>Zahlungsanforderungs-Mail</p>';
      $html .= 'Diese Nachricht wird im Mail bei einer Zahlungsanforderung erscheinen. Beachte, dass bei Vorkasse oder Rechnung der Zahlungslink nicht erscheinen wird . ';
      $html .= '<div class="btn-msg-container">';
        $html .= '<span onclick="document.getElementsByName(\'payment_mail_msg\')[0].value += \'%E-Mail%\';">E-Mail</span>';
        $html .= '<span onclick="document.getElementsByName(\'payment_mail_msg\')[0].value += \'%Pay-Link%\';">Zahlungslink</span>';
      $html .= '</div>';
      $html .= '<label class="txt-input">';
        $html .= '<textarea name="payment_mail_msg" rows="4">' . str_replace("<br />", "", $groupValues["payment_mail_msg"]) . '</textarea>';
        $html .= '<span class="placeholder">Nachricht</span>';
      $html .= '</label>';

      //Payrexx
      $html .= '<p>Payrexx</p>';
      $html .= 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren . ';

      //Payrexx instance
      $html .= '<label class="txt-input">';
        $html .= '<input type="text" name="payment_payrexx_instance" value="' . $groupValues["payment_payrexx_instance"] . '" ' . $disabled . '/>';
        $html .= '<span class="placeholder">Payrexx Instance</span>';
      $html .= '</label>';

      //Payrexx secret
      $html .= '<label class="txt-input">';
        $html .= '<input type="text" name="payment_payrexx_secret" value="' . $groupValues["payment_payrexx_secret"] . '" ' . $disabled . '/>';
        $html .= '<span class="placeholder">Payrexx Secret</span>';
      $html .= '</label>';

      //Store infos
      $html .= '<p>Store</p>';
      $html .= 'Damit Sie auch ohne Programmiererfahrung ein Ticket verkaufen können, beinhaltet dieses System auch einen eigenen <a href="' . $url . 'store" target="_blank">Store</a> womit Sie ihre Tickets verkaufen können. Im Folgenden können Sie das Design des Store beeinflussen. Möchten Sie jemanden direkt dieses Ticket verkaufen, verwenden Sie diesen Link: <a href="' . $url . 'store/buy/?group=' . $_GET["view"] . '" target="_blank">' . $url . 'store/buy/?group=' . $_GET["view"] . '</a>';

      //store available
      $html .= '<label class="checkbox">';
        $html .= '<input type="checkbox" name="payment_store" value="true" ' . (($groupValues["payment_store"] === 1) ? "checked" : '')  . '>';
        $html .= '<div class="checkbox-btn" title="Dieses Ticket im Store verkaufen"></div>Im Store anzeigen';
      $html .= '</label>';

      //Logo
      $html .= '<span class="file-info">Logo</span>';
      $html .= '<label class="file-input">';
        $html .= '<input type="file" name="logo" onchange="previewImage(this)" ' . $disabled . '/>';
        //Display image if exists
        $file = "medias/groups/" . $this->groupID . "/store/logo.png";
        if(!file_exists($file)) {
          $html .= '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/favicon-color-512.png\')"></div>';
        }else {
          $html .= '<div class="preview-image" style="background-image: url(\'' .$url . $file . '\')"></div>';
        }
        $html .= '<div class="draganddrop">Click to select file</div>';
      $html .= '</label>';

      //background
      $html .= '<span class="file-info">Hintergrundbild</span>';
      $html .= '<label class="file-input">';
        $html .= '<input type="file" name="background" onchange="previewImage(this)" ' . $disabled . '/>';
        //Display image if exists
        $file = "medias/groups/" . $this->groupID . "/store/background.png";
        if(!file_exists($file)) {
          $html .= '<div class="preview-image" style="background-image: url(\'' . $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,2) . "/medias/store/background/*")[0], PATHINFO_BASENAME ) . '\')"></div>';
        }else {
          $html .= '<div class="preview-image" style="background-image: url(\'' . $url . $file . '\')"></div>';
        }
        $html .= '<div class="draganddrop">Click to select file</div>';
      $html .= '</label>';
      $html .= '<br /><span style="color: #fa8702;">HINWEIS:</span> Im Store werden nur Ticketgruppen angezeigt welche oben ein Häcken gesetzt haben.<br />';

      //ADFS
      if(SIMPLE_SAML_CONFIG === null) {
        $html .= '<div style="opacity: 0.5; margin-bottom: 12.5px;">';
          $html .= '<p>ADFS</p>';
          $html .= 'Diese Funktion ist nur verfügbar, wenn der Administrator die simpleSAMLphp einstellungen vorgenommen und einen Pfad im general.php angegeben hat. <a href="https://simplesamlphp.org/" target="_blank">Weitere Informationen</a>';
          $html .= '<label class="checkbox">';
            $html .= '<input type="checkbox" disabled>';
            $html .= '<div class="checkbox-btn" title="Anmeldung fordern um Ticket zu kaufen"></div>Authentifizierung verlangen';
          $html .= '</label>';
        $html .= '</div>';
      }else {
        $html .= '<div>';
          $html .= '<p>ADFS</p>';
          $html .= 'Durch aktivieren dieser Funktion, muss der Kunde sich über Ihr ADFS authentifizieren um ein Ticket zu erwerben. Beachten Sie, dass die simpleSAML-Konfiguration manuell vorgenommen werden muss. Ist die Konfiguration fehlerhaft, funktioniert der ganze Bestellungsprozess über den Store für diese Ticketgruppe nicht mehr. Die Authentifizierung kann nicht über ein Drittanbieter via SDK erfolgen . ';
          $html .= '<label class="checkbox">';
            $html .= '<input type="checkbox" name="adfs" value="true" ' . (($groupValues["adfs"] === 1) ? "checked" : '')  . ' ' . $disabled . '>';
            $html .= '<div class="checkbox-btn" title="Anmeldung fordern um Ticket zu kaufen"></div>Authentifizierung verlangen';
          $html .= '</label>';

          //Inputs
          $customUserInputs = json_decode($group->values()["custom"], true);
          $customADFS = json_decode($group->values()["adfs_custom"], true);

          $html .= '<span style="display: block; margin-top: 12.5px;">Fügen Sie den jeweiligen Array-Key des ADFS-Array in das zugehörig definierte Feld vom Formular ein, um die Daten ihres Active Directory zu übernehmen. Leer gelassene Felder müssen vom Benutzer selbst eingetragen werden. Werden alle Felder definiert, kann der Benutzer nur noch ein Coupon einfügen.</span>';
          $html .= '<label class="txt-input">';
            $html .= '<input type="text" name="adfs_custom[email]" value="' . $customADFS["email"] . '" ' . $disabled . '/>';
            $html .= '<span class="placeholder">E-Mail<abbr title="Für den Bestellprozess immer benötigt">*</abbr></span>';
          $html .= '</label>';

          if(! empty($customUserInputs)) {
            //Set id and remove unused elements
            for($i = 0; $i < count($customUserInputs); $i++) {
              //Unset elements
              unset($customUserInputs[$i]["placeholder"]);

              //Set new values
              $customUserInputs[$i] = array_merge(array("id" => $i), $customUserInputs[$i]); //Id of input
            }

            //Order array by user input
            foreach($customUserInputs as $key => $value) {
              $orders[$key] = intval($value["order"]);
            }
            array_multisort($orders, SORT_ASC, $customUserInputs);

            //
            for($i = 0; $i < count($customUserInputs); $i++) {
              $html .= '<label class="txt-input">';
                $html .= '<input type="text" name="adfs_custom[' . $customUserInputs[$i]["id"] . ']" value="' . $customADFS[$customUserInputs[$i]["id"]] . '" ' . $disabled . '/>';
                $html .= '<span class="placeholder">' . $customUserInputs[$i]["name"] . '</span>';
              $html .= '</label>';
            }
          }



        $html .= '</div>';
      }

      $html .= '<input type="submit" name="update" value="Update" />';

    $html .= '</form>';


    echo $html;
  }

  /**
   * Function to display api informations
   */
  public function sdk() {
    //Require variables
    global $url;
    global $url_page;

    //refresh secret key action
    if(isset($_GET["refresh"])) {
      Action::confirm('Möchten Sie den geheimen Schlüssel tatsächlich erneuern?<br /><span style="color: #f0c564;">Diese Aktion wird nur empfohlen, wenn Sie einen Verdacht auf Missbrauch dieses Schlüssels haben oder ihn noch nicht produktiv einsetzen.</span>', $_GET["view"], "&view=" . $_GET["view"] . "&selection=" . $_GET["selection"]);
    }

    //SDK Information
    $html = '<div class="sdk-code">';
      $html .= '<p>Geheimer Schlüssel</p>';

      //Info
      $html .= 'Damit Sie eine Anfrage per SDK machen können, müssen Sie diesen geheimen Schlüssel verwenden. Berücksichtigen Sie, dass dieser Schlüssel nur für diese Gruppe verfügbar ist. Sie können somit nur Tickets, welche dieser Gruppe zugeordnet wurden, überarbeiten, löschen oder auslesen.<br />';

      //Secret key
      $html .= '<label class="txt-input">';
        $html .= '<input type="text" value="' . Crypt::decrypt($this->values()["sdk_secret_key"]) . '"/>';
        $html .= '<a class="refresh" href="' . $url_page . '&view=' . $_GET["view"] . '&selection=' . $_GET["selection"] . '&refresh"><img src="' . $url . 'medias/icons/restore.svg"/></a>';
      $html .= '</label>';

      //Notice
      $html .= 'WICHTIG: Wer in Besitz dieses Schlüssels ist, kann Tickets hinzufügen, löschen, überarbeiten und auslesen. Veröffentlichen Sie diesen Schlüssel <b>nie</b> und geben Sie den Schlüssel nur an vertraute Personen weiter. Vermuten Sie einen Missbrauch dieses Schlüssels, erneuern Sie ihn unverzüglich . ';

    $html .= '</div>';

    //SDK Download
    $file = file_get_contents(dirname(__FILE__, 2) . "/sdk/tktdata.phps");
    $fileWithHost = str_replace("YOUR_HOST/", $url. "sdk/req.php", $file); //prepare new file
    file_put_contents(dirname(__FILE__, 2) . "/sdk/tktdata.php.txt", $fileWithHost);

    $html .= '<div class="sdk-code">';
      $html .= '<p>SDK-Dokument</p>';
      $html .= 'Laden Sie sich hier das benötigte SDK-Dokument herunter.<br />';
      $html .= '<a href="' . $url . 'sdk/tktdata.php.txt" class="button" title="SDK-Dokument herunterladen" download>Download</a>';
    $html .= '</div>';

    //Simple implementation
    $html .= '<div class="sdk-code">';
      $html .= '<p>Einfache Einfügung</p>';
      $html .= 'Haben Sie wenig Programmiererfahrung, können Sie eine einfache Implementierung machen. Diese finden Sie unter dem Reiter <a href="' . $url_page . '&view=' . $_GET["view"] . '&selection=5">Payment&#8594;Store</a>.<br />';
    $html .= '</div>';

    //Documentation list
    $html .= '<div class="sdk-code">';
      $html .= '<p>Dokumentation</p>';
      $html .= 'Folgende Informationen können Sie über die SDK erhalten, hinzufügen und überarbeiten.<br />Bitte beachten Sie, dass dies nur eine kleine und undetailierte Dokumentation ist. Eine detailierte Beschreibung der verwendeten Funktionen finden Sie direkt im SDK-Dokument, welches Sie oben herunterladen können.
      <ul style="margin-left: 15px;">
        <li><a href="#sdk-ticketinfos">Ticketinformationen abrufen</a></li>
        <li><a href="#sdk-ticketToken">Ticket-Token abrufen</a></li>
        <li><a href="#sdk-addTicket">Ticket hinzufügen</a></li>
        <li><a href="#sdk-updateTicket">Ticket überarbeiten</a></li>
        <li><a href="#sdk-removeTicket">Ticket entfernen</a></li>
        <li><a href="#sdk-restoreTicket">Ticket wiederherstellen</a></li>
        <li><a href="#sdk-send-ticket">Ticket per Mail senden</a></li>
        <li><a href="#sdk-get-coupon">Coupon-ID per Name erhalten</a></li>
        <li><a href="#sdk-check-coupon">Coupon prüfen</a></li>
        <li><a href="#sdk-price-coupon">Ticketpreis mit Coupon</a></li>
        <li><a href="#sdk-getGroup">Gruppeninformationen</a></li>
        <li><a href="#sdk-usedTickets">Benuzte Tickets</a></li>
        <li><a href="#sdk-availableTickets">Verfügbare Tickets</a></li>
        <li><a href="#sdk-availableTickets">Pro Benutzer verfügbare Tickets</a></li>
        <li><a href="#sdk-requestGateway">Gateway anfordern</a></li>
        <li><a href="#sdk-deleteGateway">Gateway löschen</a></li>
        <li><a href="#sdk-requestTransaction">Transaktionsinfos</a></li>
        <li><a href="#sdk-requestPayment">Zahlungserinnerung senden</a></li>
      </ul>';
    $html .= '</div>';

    //Ticketinformationen
    $html .= '<div class="sdk-code" id="sdk-ticketinfos"><p>Ticketinformationen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->get_ticket(); /*JSON answer*/</code></pre></div>';

    //ticket token
    $html .= '<div class="sdk-code" id="sdk-ticketToken"><p>Ticket-token</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$ticket_infos = $tktdata->find_ticketToken("Sample info"); /*JSON answer*/</code></pre></div>';

    //Add ticket
    $html .= '<div class="sdk-code" id="sdk-addTicket"><p>Ticket hinzufügen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$ticket_infos = $tktdata->add_ticket(array(<br />  "groupID" => "YOUR VALUE [REQUIRED]",<br />  "amount" => "YOUR VALUE",<br />  "payment" => "YOUR VALUE",<br />  "coupon" => "YOUR VALUE",<br />  "email" => "YOUR VALUE [REQUIRED]",<br />  "custom" => "YOUR VALUE"<br />)); /*True or an error in JSON*/</code></pre></div>';

    //update ticket
    $html .= '<div class="sdk-code" id="sdk-updateTicket"><p>Ticket überarbeiten</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->update_ticket(array(<br />  "amount" => "YOUR VALUE",<br />  "state" => "YOUR VALUE", <br />  "payment" => "YOUR VALUE",<br />  "coupon" => "YOUR VALUE",<br />  "purchase_time" => "YOUR VALUE",<br />  "payment_time" => "YOUR VALUE",<br />  "employ_time" => "YOUR_VALUE" ,<br />  "email" => "YOUR VALUE [REQUIRED]",<br />  "custom" => "YOUR VALUE"<br />)); /*True or an error in JSON*/</code></pre></div>';

    //Remove ticket
    $html .= '<div class="sdk-code" id="sdk-removeTicket"><p>Ticket entfernen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->remove_ticket(); /*True or an error in JSON*/</code></pre></div>';

    //Restore ticket
    $html .= '<div class="sdk-code" id="sdk-restoreTicket"><p>Ticket wiederherstellen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->restore_ticket(); /*True or an error in JSON*/</code></pre></div>';

    //Send ticket
    $html .= '<div class="sdk-code" id="sdk-send-ticket"><p>Ticket per Mail senden</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/<br />$ticket_infos = $tktdata->send_ticket(); /*True or false*/</code></pre></div>';

    //get couponID by name
    $html .= '<div class="sdk-code" id="sdk-get-coupon"><p>Coupon-ID per Name erhalten</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$coupon_infos = $tktdata->get_couponID("NAME OF COUPON"); /*JSON answer*/</code></pre></div>';

    //check_coupon
    $html .= '<div class="sdk-code" id="sdk-check-coupon"><p>Coupon prüfen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$tktdata->couponID = "COUPON ID"; /*Coupon ID*/<br />$coupon_infos = $tktdata->check_coupon(); /*JSON answer*/</code></pre></div>';

    //new coupon price
    $html .= '<div class="sdk-code" id="sdk-price-coupon"><p>Ticketpreis mit Coupon</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of coupon*/<br />$tktdata = new TKTData();<br  />$tktdata->couponID = "COUPON ID"; /*Coupon ID*/<br />$coupon_infos = $tktdata->new_coupon_price(); /*JSON answer*/</code></pre></div>';

    //Get group
    $html .= '<div class="sdk-code" id="sdk-getGroup"><p>Gruppeninformationen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$group_infos = $tktdata->get_group(); /*JSON answer*/</code></pre></div>';

    //used tickets
    $html .= '<div class="sdk-code" id="sdk-usedTickets"><p>Benuzte Tickets</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->usedTickets(); /*Number*/</code></pre></div>';

    //available tickets
    $html .= '<div class="sdk-code" id="sdk-availableTickets"><p>Verfügbare Tickets</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->availableTickets(); /*Number*/</code></pre></div>';

    //tpu available tickets
    $html .= '<div class="sdk-code" id="sdk-availableTickets"><p>Pro Benutzer verfügbare Tickets</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of group*/<br />$tktdata = new TKTData();<br />$tktdata->groupID = "GROUP_ID"; /*Set group id*/<br />$ticket_infos = $tktdata->tpu_available("E-MAIL OF USER"); /*Number*/</code></pre></div>';

    //Request gateway
    $html .= '<div class="sdk-code" id="sdk-requestGateway"><p>Gateway anfordern</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->requestGateway(array(<br />  "success_link" => "YOUR VALUE",<br />  "fail_link" => "YOUR VALUE"<br />)); /*JSON answer*/</code></pre></div>';

    //delete gateway
    $html .= '<div class="sdk-code" id="sdk-deleteGateway"><p>Gateway löschen</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->deleteGateway(); /*JSON answer*/</code></pre></div>';

    //requestTransaction
    $html .= '<div class="sdk-code" id="sdk-requestTransaction"><p>Transaktionsinfos</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->private_key = "YOUR_PRIVATE_KEY"; /*You will find this key on your host in groups->sdk*/<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->requestTransaction(); /*JSON answer*/</code></pre></div>';

    //checkPayment
    $html .= '<div class="sdk-code" id="sdk-requestPayment"><p>Zahlungserinnerung senden</p><pre><code class="php">/*Require tktdata file*/<br />require_once("path/to/file/tktdata.php");<br /><br />/*Get infos of ticket*/<br />$tktdata = new TKTData();<br />$tktdata->ticketToken = "sample"; /*Set ticket token*/ <br />$ticket_infos = $tktdata->send_payment_amil(); /*true or false*/</code></pre></div>';

    /**
     * Display content
     */
    echo $html;
  }

  /**
   * Function to display add form
   */
  public function addForm() {
    //require variables
    global $url;

    //Start form to edit, show user
    $html = '<form action="' . $url . '?' . $_SERVER["QUERY_STRING"] . '" method="post" style="width: 100%; max-width: 750px;" class="box-width">';
    $html .= '<h1>Gruppe hinzufügen</h1>';
    //Gruppenname
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="name"/>';
      $html .= '<span class="placeholder">Gruppenname</span>';
    $html .= '</label>';

    //Add submit button
    $html .= '<input type="submit" name="create" value="Erstellen"/>';

    //Close form
    $html .= '</form>';

    //Return html
    echo $html;
  }
}

/**
 *
 * Display content
 *
 */

//Create new html class
$groupCustomizer = new GroupCustomizer();
$groupCustomizer->groupID = (empty($_GET["view"])) ?
                            ((empty($_GET["remove"])) ? '' : $_GET["remove"])
                            : $_GET["view"];
$groupCustomizer->subpage = (empty($_GET["selection"])) ? 1 : $_GET["selection"];

if(isset($_GET["view"])) {
  //Update if necessary
  if(! empty( $_POST )) {
    //Check if user is permitted to do this action
    if(User::w_access_allowed($page, $current_user)) {
      $values = (isset($_POST) && isset($_FILES)) ? array_merge($_POST, $_FILES) : $_POST;
      if( $groupCustomizer->update( $groupCustomizer->subpage, $values )) {
        Action::success("Die Gruppe konnte <strong>erfolgreich</strong> überarbeitet werden.");
      }else{
        Action::fail("Leider konnte die Gruppe <strong>nicht</strong></b> überarbeitet werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }
  }

  //Display menu
  $groupCustomizer->menu();

  //Display group
  switch( $groupCustomizer->subpage ) {
    case 1: //General
      $groupCustomizer->general();
    break;
    case 2: //userInputs
      $groupCustomizer->userInputs();
    break;
    case 3: //Ticket
      $groupCustomizer->ticket();
    break;
    case 4: //Mail
      $groupCustomizer->mail();
    break;
    case 5: //Payment
      $groupCustomizer->payment();
    break;
    case 6: //SDK
      $groupCustomizer->sdk();
    break;
    default: //1
      $groupCustomizer->general();
  }

}

if(isset( $_GET["add"] )) {
  //Add if necessary
  if(! empty( $_POST )) {
    //Check if user is permitted to do this action
    if(User::w_access_allowed($page, $current_user)) {
      if( $groupCustomizer->add( $_POST ) ) {
        Action::success("Die Gruppe konnte <strong>erfolgreich</strong> erstellt werden.");
      }else{
        Action::fail("Leider konnte die Gruppe <strong>nicht</strong></b> erstellt werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }
  }

  //Display form to add new group
  $groupCustomizer->addForm();
}

if(isset( $_GET["remove"])) {
  $info = 'Möchten Sie die Grupe #' . $groupCustomizer->groupID . ' (' . $groupCustomizer->values()["name"] . ') wirklich entfernen?';

  if(! FULL_RESTORE) {
    $info .= '<span style="color: #f0c564;">Beachten Sie, dass die verwendeten Bilder für Ticket und E-Mail nicht wiederhergestellt werden können!</span>';
  }

  //Display request
  Action::confirm( $info,  $groupCustomizer->groupID);
}

if(isset($_POST["confirm"])) {
  if( User::w_access_allowed($page, $current_user)) {

    $groupCustomizerDelete = new GroupCustomizer();
    $groupCustomizerDelete->groupID = $_POST["confirm"];

    if(! isset($_GET["selection"]) || $_GET["selection"] != 6) { //On page 6 (SDK), secret key will be refreshed
      //Remove group
      if( $groupCustomizerDelete->remove()) {
        Action::success('Die Gruppe #' . $_POST["confirm"] . ' wurde erfolgreich entfernt . ');
      }else {
        Action::fail('Der Gruppe #' . $_POST["confirm"] . ' konnte nicht entfernt werden . ');
      }
    }
  }else {
    Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
  }
}

if(! isset($_GET["view"]) && ! isset($_GET["add"])) {
  //Display search form
  echo '<form action="' . $url_page . '" method="post" class="search">';
    echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Name, Preis, Beschreibung, Eigene Elemente">';
    echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
  echo '</form>';

  //Display tickets
  $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';

  $groupCustomizer->list( $search_value );

  //Add button
  echo '<a class="add" href="' . $url_page . '&add">
  	<span class="horizontal"></span>
  	<span class="vertical"></span>
  </a>';
}
?>
