<?php
function display_tickets( $search_value = null ){
  //Define variables
  global $url;
  global $url_page;
  global $page;
  global $current_user;
  global $conn;

  $steps = 20; //Maximal number of rows listed
  $offset = isset( $_GET["row-start"] )? (intval($_GET["row-start"]) * $steps) : 0; //Start position of listet users

  if(! empty($search_value)) {
    //Searched after value
    $ticket = new Ticket();
    $ticket->ticketToken = $search_value;
    $user_search = $conn->prepare("SELECT * FROM " . TICKETS . " WHERE
    ticketKey =:ticketKey1 OR
    ticketKey =:ticketKey2 AND groupID =:gid OR
    groupID LIKE :groupID OR
    coupon LIKE :coupon OR
    email LIKE :email OR
    custom LIKE :custom
    ORDER BY purchase_time DESC LIMIT :offset, :max_rows");//Result of all selected users in range
    $user_search->execute(array(
      ":ticketKey1" => $search_value,
      ":ticketKey2" => $ticket->cryptToken()["ticketKey"],
      ":gid" => $ticket->cryptToken()["gid"],
      ":groupID" => "%" . $search_value . "%",
      ":coupon" => "%" . $search_value . "%",
      ":email" => "%" . $search_value . "%",
      ":custom" => "%" . $search_value . "%",
      ":offset" => $offset,
      ":max_rows" => $steps
    ));


    $total_rows_req = $conn->prepare("SELECT * FROM " . TICKETS . " WHERE
    ticketKey LIKE :ticketKey OR
    groupID LIKE :groupID OR
    coupon LIKE :coupon OR
    email LIKE :email OR
    custom LIKE :custom");
    $total_rows_req->execute(array(
      ":ticketKey" => "%" .  $search_value . "%",
      ":groupID" => "%" . $search_value . "%",
      ":coupon" => "%" . $search_value . "%",
      ":email" => "%" . $search_value . "%",
      ":custom" => "%" . $search_value . "%"
    ));
    $total_rows = $total_rows_req->rowCount();//Get number of all registerd user
  }else {
    //No search
    $user_search = $conn->prepare("SELECT * FROM " . TICKETS . " ORDER BY purchase_time DESC LIMIT :offset, :max_rows");//Result of all selected users in range
    $user_search->execute(array(
      ":offset" => $offset,
      ":max_rows" => $steps
    ));


    $total_rows_req = $conn->prepare("SELECT * FROM " . TICKETS);
    $total_rows_req->execute();
    $total_rows = $total_rows_req->rowCount();//Get number of all registerd user
  }

  /**
   * Start html
   */
  $state_css = ['payment-and-used', 'blocked-and-payment', 'payment-expected', 'used', 'blocked'];
  $state_description = ['Eingelöst ohne Zahlung', 'Blockiert & bezahlt', 'Zahlung erwartet', 'Eingelöst', 'Blockiert'];
  $html = '<div class="legend">';
    for($i = 0; $i < COUNT($state_css); $i++){
      $html .= '<div class="legend-element"><div class="legend-button '.$state_css[$i].'"></div>'.$state_description[$i].'</div>';
    }
  $html .= '</div>';

  //Start table
  $html .= '<table class="rows">';

  //Headline
  $headline_names = array('E-mail', 'Kaufdatum', 'Aktion');

  //Start headline
  //Headline can be changed over array $headline_names
  $html .= '<tr>'; //Start row
  foreach( $headline_names as $name ){
    $html .= '<th>'.$name.'</th>';
  }
  $html .= '</tr>'; //Close row

  //row all tickets
  while( $ticket = $user_search->fetch() ){
    //Define ticket state
    $ticket_state = '';
    if( $ticket["payment"] == 2 && $ticket["state"] == 1){ //no payment but used
      $ticket_state = $state_css[0];
    }elseif( $ticket["payment"] != 2 && $ticket["state"] == 2){ //Blocked/deleted and payed
      $ticket_state = $state_css[1];
    }elseif( $ticket["payment"] == 2 && $ticket["state"] != 2){ //Payment expected
      $ticket_state = $state_css[2];
    }elseif( $ticket["state"] == 1){ //Ticket used
      $ticket_state = $state_css[3];
    }elseif( $ticket["state"] == 2){ //Ticked blocked and no payment
      $ticket_state = $state_css[4];
    }

    //define color of goup
    $group = new Group();
    $group->groupID = $ticket["groupID"];
    $groupInfo = $group->values();


    $html .= '<tr class="table-list '.$ticket_state.'">'; //Start row
      $html .= '<td><div class="color" style="background-color: ' . $groupInfo["color"] . ';" title="Name: ' . $groupInfo["name"] . '&#013;ID: ' . $groupInfo["groupID"] . '"></div>'.$ticket["email"].'</td>'; //Display user id
      $html .= '<td>'.date("d.m.Y H:i:s", strtotime($ticket["purchase_time"])).'</td>'; //Display purchase date

      //Create ticket token
      $ticketToken = Ticket::encryptTicketToken($ticket["groupID"], $ticket["ticketKey"]);

      //Check if current user (logged in user) can edit or see the user
      if( User::w_access_allowed($page, $current_user) ){
        //Current user can edit and delete user
        $html .= '<td style="width: auto;">
                    <a href="' . $url_page . '&view='.urlencode( $ticketToken ).'" title="Ticketdetails anzeigen"><img src="' . $url . '/medias/icons/pencil.svg" /></a>
                    <a href="' . $url . 'pdf/ticket/?ticketToken='.urlencode( $ticketToken ).'" target="_blank" title="PDF öffnen"><img src="' . $url . '/medias/icons/pdf.svg" /></a>';
        //Check if ticket is blocked
        if( $ticket["state"] == 2 ){
          $html .= '<a href="' . $url_page . ((isset( $_GET["row-start"] )) ? "&row-start=" . $_GET["row-start"] : "") . '&restore='.urlencode( $ticketToken ).'" title="Restore"><img src="' . $url . '/medias/icons/restore.svg" /></a>';
        }else{
          $html .= '<a href="' . $url_page . ((isset( $_GET["row-start"] )) ? "&row-start=" . $_GET["row-start"] : "") . '&remove='.urlencode( $ticketToken ).'" title="Löschen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
        }
        $html .= '</td>';
      }elseif( User::r_access_allowed($page, $current_user) ){
        $html .= '<td style="width: auto;">
                    <a href="' . $url_page . '&view='.urlencode( $ticketToken ).'" title="Ticketdetails anzeigen"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>
                    <a href="' . $url . 'pdf/ticket/?ticketToken='.urlencode( $ticketToken ).'" target="_blank" title="PDF öffnen"><img src="' . $url . '/medias/icons/pdf.svg" /></a>
                  </td>';
      }
    $html .= '</tr>'; //End row
  }

  //Range menu
  $html .= '<tr class="nav">';

  if( $offset + $steps >= $total_rows && $total_rows > $steps){ //last page
    $html .= '<td colspan="3">
                <a href="' . $url_page . '&row-start='.round($offset/$steps - 1, PHP_ROUND_HALF_UP).'" style="float: left;">Letze</a>
              </td>';
  }elseif( $offset <= 0 && $total_rows > $steps){ //First page
    $html .= '<td colspan="3">
                <a href="' . $url_page . '&row-start='.round($offset/$steps + 1, PHP_ROUND_HALF_UP).'" style="float: right;">Weiter</a>
              </td>';
   }elseif( $offset > 0){
    $html .= '<td colspan="3">
                <a href="' . $url_page . '&row-start='.round($offset/$steps - 1, PHP_ROUND_HALF_UP).'" style="float: left;">Letze</a>
                <a href="' . $url_page . '&row-start='.round($offset/$steps + 1, PHP_ROUND_HALF_UP).'" style="float: right;">Weiter</a>
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

function single_ticket() {
  //require variables
  global $page;
  global $current_user;
  global $url;
  global $url_page;
  global $conn;

  //Creat new ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $_GET["view"];

  //Update payment if required
  checkPayment( $ticket->ticketToken );

  //Set inputs disabled for read only
  if(! User::w_access_allowed($page, $current_user)) {
    $disabled = "disabled";
  }else{
    $disabled = null;
  }

  //Get all values
  $values = $ticket->values();

  //Get group values
  $group = new Group();
  $group->groupID = $values["groupID"];

  // &#013;
  $html = '<div class="headline-maincolor" style="background-color: ' . $group->values()["color"] . '" title="Name: ' . $group->values()["name"] . '&#013;ID: ' . $group->values()["groupID"] . '"></div>';

  //Top bar
  $state_css = ['payment-and-used', 'blocked-and-payment', 'payment-expected', 'used', 'blocked'];
  if( $values["payment"] == 2 && $values["state"] == 1) { //no payment but used
    $html .= "<div class='top-bar-ticket " . $state_css[0] . "'>Ticket benützt um " . date("d.m.Y H:i:s", strtotime($values["employ_time"])) . ", Zahlung nicht getätigt.</div>";
  }elseif( $values["payment"] != 2 && $values["state"] == 2) { //Blocked/deleted and payed
    $html .= "<div class='top-bar-ticket " . $state_css[1] . "'>Blockiertes Ticket, bereits bezahlt.</div>";
  }elseif( $values["payment"] == 2 && $values["state"] != 2) { //Payment expected
    $html .= "<div class='top-bar-ticket " . $state_css[2] . "'>Zahlung nicht getätigt.</div>";
  }elseif( $values["state"] == 1) { //Ticket used
    $html .= "<div class='top-bar-ticket " . $state_css[3] . "'>Ticket eingelöst am " . date("d.m.Y H:i:s", strtotime($values["employ_time"])) . ".</div>";
  }elseif( $values["state"] == 2) { //Ticked blocked and no payment
    $html .= "<div class='top-bar-ticket " . $state_css[4] . "'>Ticket blockiert.</div>";
  }

  //Display right menu
  $html .= '<div class="right-sub-menu">';
    $html .= '<a href="' . $url . 'pdf/ticket/?ticketToken='.urlencode( $ticket->ticketToken ).'" target="_blank" class="right-menu-item"><img src="' . $url . 'medias/icons/pdf.svg" alt="PDF" title="PDF öffnen"/></a>';
    $html .= '<a href="' . $url_page . '&send=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item"><img src="' . $url . 'medias/icons/mail.svg" alt="Mail" title="Mail erneut senden"/></a>';
    if($values["payment"] == 2) {
      $html .= '<a href="' . $url_page . '&requestPayment=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item"><img src="' . $url . 'medias/icons/payment.svg" alt="Mail" title="Zahlung anfordern"/></a>';
    }
    $transaction = retrieveTransaction( $ticket->ticketToken );
    if($transaction["transaction_retrieve_status"]) {
      if(($transaction["pspId"] != 15 || $transaction["pspId"] != 27) && $values["payment"] == 0) {
        $html .= '<a href="' . $url_page . '&refundPayment=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item"><img src="' . $url . 'medias/icons/payment-refund.svg" alt="Refund" title="Zahlung zurückerstatten"/></a>';
      }
    }
    //Display delete/used only if write access
    if( User::w_access_allowed($page, $current_user) ){
      //Check if ticket is already used
      if( $ticket->values()["state"] == 1 ){
        $html .= '<a href="' . $url_page . '&reactivate=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item" title="Ticket reaktivieren"><img src="' . $url . '/medias/icons/toggleUsedTicket2.svg" /></a>';
      }else{
        $html .= '<a href="' . $url_page . '&employ=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item" title="Ticket abstempeln"><img src="' . $url . '/medias/icons/toggleUsedTicket1.svg" /></a>';
      }

      //Check if ticket is blocked
      if( $ticket->values()["state"] == 2 ){
        $html .= '<a href="' . $url_page . '&restore=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item" title="Restore"><img src="' . $url . '/medias/icons/restore.svg" /></a>';
      }else{
        $html .= '<a href="' . $url_page.'&remove=' . urlencode( $ticket->ticketToken ) . '" class="right-menu-item" title="Löschen"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
      }
    }
  $html .= '</div>';

  //Display full form with value
  $html .= '<form action="' . $url . basename($_SERVER['REQUEST_URI']) . ' " method="post" class="right-menu">';

    //Email
    $html .= '<label class="txt-input">';
      $html .= '<input type="text" name="email" value="' . $values["email"] . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">E-Mail</span>';
    $html .= '</label>';

    //Payment method
    $paymentNames = array('Karte', 'Rechnung', 'Zahlung nicht eingegangen');
    $paymentName = (array_key_exists($values["payment"], $paymentNames)) ? $paymentNames[$values["payment"]] : 'Zahlungsmethode';
    $paymentNumber = (isset($values["payment"])) ? $values["payment"] : '';

    $html .= '<div class="select" onclick="toggleOptions(this)">';
      $html .= '<input type="text" class="selectValue" name="payment" value="' . $paymentNumber . '" ' . $disabled . ' required>';
      $html .= '<span class="headline">' . $paymentName . '</span>';

      $html .= '<div class="options">';
        $html .= '<span data-value="0" onclick="selectElement(this)">Karte</span>';
        $html .= '<span data-value="1" onclick="selectElement(this)">Rechnung</span>';
        $html .= '<span data-value="2" onclick="selectElement(this)">Zahlung nicht eingegangen</span>';
      $html .= '</div>';
    $html .= '</div>';

    //Amount
    $html .= '<label class="txt-input">';
      $html .= '<input type="number" step="0.05" min="0" name="amount" value="' . round(( $values["amount"] / 100 ), 2) . '" ' . $disabled . ' required/>';
      $html .= '<span class="placeholder">Betrag</span>';
      $html .= '<span class="unit">' . $group->values()["currency"] . '</span>';
    $html .= '</label>';

    //Custom
    $customUserInputs = json_decode($values["custom"], true);

    if(! empty($customUserInputs)) {
      foreach($customUserInputs as $customInput) {
        switch( $customInput["type"] ) {
          //---------------------------- Select-input ----------------------------//
          case "select":
          $options = explode(",", $customInput["options"]);
          $html .= '<div class="select" onclick="toggleOptions(this)">';
            $html .= '<input type="text" class="selectValue" name="' . $customInput["id"] . '" value="' .         (($customInput["value"] == "") ? "" : $customInput["value"]) . '" ' . $disabled . '>';
            $html .= '<span class="headline">' . (($customInput["value"] == "") ? "-- Auswahl treffen --" : $customInput["value"]) . '</span>';

            $html .= '<div class="options">';
              foreach($options as $option) {
                if($option != "") {
                  $html .= '<span data-value="' . $option . '" onclick="selectElement(this)">' . $option . '</span>';
                }
              }

            $html .= '</div>';
          $html .= '</div>';
          break;
          //---------------------------- Radio-input ----------------------------//
          case "radio":
          $options = explode(",", $customInput["options"]);
          $html .= '<div class="radio-input-container">';
            $html .= $customInput["name"];
            foreach($options as $option) {
              if($option != "") {
                //Define if current element is value
                $currentValue = str_replace(" ", "_", $customInput["value"]);
                $checked = ($option == $currentValue) ? "checked" : "" ;

                $html .= '<label class="radio">';
                  $html .= '<input type="radio" name="' . $customInput["id"] . '" value="' . str_replace(" ", "_", $option)  . '" ' . $checked . ' ' . $disabled . '/>';
                  $html .= '<div title="Auswahl treffen"></div>';
                  $html .= $option;
                $html .= '</label>';
              }
            }
          $html .= '</div>';
          break;
          //---------------------------- Checkbox-input ----------------------------//
          case "checkbox":
          $html .= '<label class="checkbox">';
            $checked = (! empty($customInput["value"])) ? "checked" : ""; //Define if selected or not

            $html .= '<input type="checkbox" name="' . $customInput["id"] . '" value="' . $customInput["value"] . '" ' . $checked . ' ' . $disabled . '/>';
            $html .= '<div title="Häcken setzen"></div>';
            $html .= $customInput["name"];
          $html .= '</label>';
          break;
          //---------------------------- Textarea ----------------------------//
          case "textarea":
          $html .= '<label class="txt-input">';
            $html .= '<textarea name="' . $customInput["id"] . '" rows="5" ' . $disabled . '>' .$customInput["value"] . '</textarea>';
            $html .= '<span class="placeholder">' . $customInput["name"] . '</span>';
          $html .= '</label>';
          break;
          //---------------------------- Text-input [Mail, Number, Date] ----------------------------//
          default: //Text input
          $html .= '<label class="txt-input">';
            $html .= '<input type="' . $customInput["type"] . '" name="' . $customInput["id"] . '" value="' .$customInput["value"] . '" ' . $disabled . '/>';
            $html .= '<span class="placeholder">' . $customInput["name"] . '</span>';
          $html .= '</label>';
        }
      }
    }

    //Coupon used
    $used_coupon = new Coupon();
    $used_coupon->couponID = $ticket->values()["coupon"];

    //Get infos
    $coupons = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE groupID=:gid");
    $coupons->execute(array(":gid" => $ticket->values()["groupID"]));

    if($coupons->rowCount() > 0) {
      $html .= '<div class="select" onclick="toggleOptions(this)">';
        $html .= '<input type="text" class="selectValue" name="coupon" value="">';
        $html .= '<span class="headline">' .
        //Get coupon informaions
        (
          (empty($used_coupon->couponID)) ?
            "Kein Coupon verwendet" : //No coupon used
            $used_coupon->values()["name"] . ' -' . (
                empty($used_coupon->values()["discount_percent"]) ?
                   ($used_coupon->values()["discount_absolute"] / 100) . ' ' . $group->values()["currency"]  : //Correct absolute amount
                   ($used_coupon->values()["discount_percent"] / 100) . '%' //Correct percent
              )
          ) .
          '</span>';

        $html .= '<div class="options">';
          $html .= '<span data-value="" onclick="selectElement(this)">Kein Coupon verwenden</span>';
          foreach($coupons->fetchAll(PDO::FETCH_ASSOC) as $coupon) {
            $couponPrice = new Coupon();
            $couponPrice->couponID = $coupon["couponID"];
            $couponPrice = $couponPrice->new_price();

            $group = new Group();
            $group->groupID = $ticket->values()["groupID"];
            $html .= '<span data-value="' . $coupon["couponID"] . '" onclick="selectElement(this)">' . $coupon["name"] . ' (Neuer Preis: ' . ($couponPrice/100) . ' ' . $group->values()["currency"] . ')</span>';
          }
        $html .= '</div>';
      $html .= '</div>';
    }

    //Display payment time
    if( $values["payment"] != 2 ) {
      $html .= "<span class='ticket-payment-date'>&#9432; Zahlung getätig um " . date("H:i \a\m d.m.Y", strtotime($values["payment_time"])) . "</span>";
    }


    $html .= '<input type="submit" value="Update" ' . $disabled . '/>';

  $html .= '</form>';

  //display content
  echo $html;
}

//Get current action
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys

switch(key($action)) {
  case "view":
    if(! empty($_POST)) {
      //Update ticket
      if(User::w_access_allowed($page, $current_user)) {
        //do update
        $ticket = new Ticket();
        $ticket->ticketToken = $_GET["view"];

        if($ticket->update($_POST)) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> überarbeitet werden.");
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> überarbeitet werden.");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }

    //Display ticket
    single_ticket();
  break;
  case "add":

    //Add
    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        $ticket = new Ticket();
        $add = $ticket->add($_POST, false, (isset($_POST["sendMail"])) ?? false);

        if($add == 6) {
          Action::fail("Coupon konnte nicht angewendet werden.");
        }elseif($add == 5) {
          Action::fail("Die Mail konnte nicht versendet werden.");
        }elseif($add == 4) {
          Action::fail("Das Zeitfenster um ein Ticket zu lösen ist <strong>nicht</strong></b> offen. Konsultiere die Gruppe für nähere Infomrationen.");
        }elseif($add == 3) {
          Action::fail("Die maximale Anzahl an Tickets wurde erreicht.");
        }elseif($add == 2) {
          Action::fail("Die maximale Anzahl an Tickets pro Benutzer wurde erreicht.");
        }elseif($add == 1) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> erstellt werden.");

          // Redirect to created ticket
          echo "<script>document.location.href='" . $url_page . "&view=" . urlencode( $ticket->ticketToken ) . "'</script>";
          exit;
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> erstellt werden.");
        }
      }else {
        Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
      }
    }


    echo '<form action="' . $url_page . '&add" method="post" class="form-50 box-width">';

      //Gruppe
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
          echo '<span data-value="' . $row["groupID"] . '" onclick="selectElement(this); group_coupons(' . $row["groupID"] . '); group_custom(' . $row["groupID"] . ')" style="border-left: 5px solid ' . $row["color"] . ';" title="' . $title . '">' . $row["name"] . '</span>'; //Display group option
        }
        echo '</div>';

      echo '</div>';

      //Email
      echo '<label class="txt-input">';
        echo '<input type="email" name="email" required/>';
        echo '<span class="placeholder">E-Mail</span>';
      echo '</label>';

      //Payment
      echo '<div class="select" onclick="toggleOptions(this)">';
        echo '<input type="text" class="selectValue" name="payment" required>';
        echo '<span class="headline">Zahlungsmethode</span>';

        echo '<div class="options">';
          echo '<span data-value="0" onclick="selectElement(this)">Karte</span>';
          echo '<span data-value="1" onclick="selectElement(this)">Rechnung</span>';
          echo '<span data-value="2" onclick="selectElement(this)">Zahlung nicht eingegangen</span>';
        echo '</div>';
      echo '</div>';

      //Custom
      echo '<div class="custom-add-container"></div>';

      //Coupon
      echo '<div class="coupon-add-container"></div>';

      //Mail
      echo '<label class="checkbox">';
        echo '<input type="checkbox" name="sendMail" value="true" checked/>';
        echo '<div class="checkbox-btn" title="Ticket an Käufer senden"></div>';
        echo 'Ticket an Käufer senden';
      echo '</label>';

      echo '<input type="submit" value="Eintragen" />';

    echo '</form>';
  break;
  case "remove":
    //Update ticket
    if(User::w_access_allowed($page, $current_user)) {
      //do update
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["remove"];
      if($ticket->remove()) {
        Action::success("Das Ticket konnte <strong>erfolgreich</strong> blockiert werden.");
      }else {
        Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> blockiert werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "restore":
    //Update ticket
    if(User::w_access_allowed($page, $current_user)) {
      //do update
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["restore"];
      if($ticket->restore()) {
        Action::success("Das Ticket konnte <strong>erfolgreich</strong> aktiviert werden.");
      }else {
        Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> aktiviert werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "employ":
    //Update ticket
    if(User::w_access_allowed($page, $current_user)) {
      //do update
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["employ"];
      if($ticket->employ()) {
        Action::success("Das Ticket konnte <strong>erfolgreich</strong> entwertet werden.");
      }else {
        Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> entwertet werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "reactivate":
    //Update ticket
    if(User::w_access_allowed($page, $current_user)) {
      //do update
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["reactivate"];
      if($ticket->reactivate()) {
        Action::success("Das Ticket konnte <strong>erfolgreich</strong> reaktiviert werden.");
      }else {
        Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> reaktiviert werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "send":
    if(User::w_access_allowed($page, $current_user)) {
      //Start ticket class
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["send"];

      //Get recipient
      $to = $ticket->values()["email"];

      if($ticket->sendTicket( $to )) {
        Action::success("Die Mail konnte <strong>erfolgreich</strong> gesendet werden.");
      }else {
        Action::fail("Leider konnte Die Mail <strong>nicht</strong></b> gesendet werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "requestPayment":
    if(User::w_access_allowed($page, $current_user)) {
      //Start ticket class
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["requestPayment"];

      //Get recipient
      $to = $ticket->values()["email"];

      if($ticket->requestPayment( $to )) {
        Action::success("Die Mail konnte <strong>erfolgreich</strong> gesendet werden.");
      }else {
        Action::fail("Leider konnte Die Mail <strong>nicht</strong></b> gesendet werden.");
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }
  break;
  case "refundPayment":
    //Start ticket class
    $ticket = new Ticket();
    $ticket->ticketToken = $_GET["refundPayment"];

    if(! $_POST) {
      Action::confirm("Möchten Sie die Zahlung für das Ticket " . $_GET["refundPayment"]  . " wirklich rückerstatten?", $_GET["refundPayment"], "&refundPayment=" . urlencode( $_GET["refundPayment"]) );
    }

    if( isset($_POST["confirm"]) ) {
      $refund = refundTransaction( $_POST["confirm"]);

      if( $refund["transaction_refund_state"] === true) {
        Action::success("Das Geld wurde erfolgreich rückerstattet.");
      }else {
        Action::fail("Beim Rückerstatten ist ein Fehler aufgetreten: <br /> " . $refund["message"]);
      }
    }

    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
        <span class="horizontal"></span>
        <span class="vertical"></span>
      </a>';
    }

  break;
  default:
    //Display form
    echo '<form action="' . $url_page . '" method="post" class="search">';
      echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Benutzername, Vonrame, Nachname, Ticketinfo">';
      echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
    echo '</form>';

    //Display tickets
    $search_value = (!empty($_POST["search_value"])) ? $_POST["search_value"] : '';
    display_tickets( $search_value );

    //Add button
    if(User::w_access_allowed($page, $current_user)) {
      echo '<a class="add" href="' . $url_page . '&add">
      	<span class="horizontal"></span>
      	<span class="vertical"></span>
      </a>';
    }
}
 ?>
