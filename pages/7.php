<?php
function display_tickets( $search_value = null ){
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

  // Start legend
  $legend = new HTML('legend');

  $ticket_states = array(
    array(
      'bcolor' => 'var(--ticket-payment-and-used)',
      'class' => 'payment-and-used',
      'title' => Language::string(1),
    ),
    array(
      'bcolor' => 'var(--ticket-blocked-and-payment)',
      'class' => 'blocked-and-payment',
      'title' => Language::string(2),
    ),
    array(
      'bcolor' => 'var(--ticket-payment-expected)',
      'class' => 'payment-expected',
      'title' => Language::string(3),
    ),
    array(
      'bcolor' => 'var(--ticket-used)',
      'class' => 'used',
      'title' => Language::string(4),
    ),
    array(
      'bcolor' => 'var(--ticket-blocked)',
      'class' => 'blocked',
      'title' => Language::string(5),
    ),
  );

  foreach( $ticket_states as $item ) {
    $legend->addElement(
      array(
        'bcolor' => $item["bcolor"],
        'title' => $item["title"],
      ),
    );
  }

  // Start table
  $table = new HTML('table');

  // Headline
  $table->addElement(
    array(
      'headline' => array(
        'items' => array(
          array(
            'context' => Language::string(6),
          ),
          array(
            'context' => Language::string(7),
          ),
          array(
            'context' => Language::string(8),
          ),
        ),
      ),
    ),
  );

  // Set offset and steps
  $steps = 20;
  $offset = (isset($_GET["row-start"]) ? ($_GET["row-start"] * $steps) : 0);

  // List tickets
  foreach( Ticket::all( $offset, $steps, $search_value) as $ticket ) {
    // Get group infos
    $group = new Group();
    $group->groupID = $ticket["groupID"];
    $groupInfo = $group->values();

    if( $ticket["payment"] == 2 && $ticket["state"] == 1){ //no payment but used
      $ticket_state_class = $ticket_states[0]["class"];
    }elseif( $ticket["payment"] != 2 && $ticket["state"] == 2){ //Blocked/deleted and payed
      $ticket_state_class = $ticket_states[1]["class"];
    }elseif( $ticket["payment"] == 2 && $ticket["state"] != 2){ //Payment expected
      $ticket_state_class = $ticket_states[2]["class"];
    }elseif( $ticket["state"] == 1){ //Ticket used
      $ticket_state_class = $ticket_states[3]["class"];
    }elseif( $ticket["state"] == 2){ //Ticked blocked and no payment
      $ticket_state_class = $ticket_states[4]["class"];
    }else {
      $ticket_state_class = '';
    }

    // Activity
    if( User::w_access_allowed($page, $current_user) ) {
        $ticketToken = Ticket::encryptTicketToken($ticket["groupID"], $ticket["ticketKey"]);
        $actions = '<a
                      href="' . $url_page . '&view=' . urlencode( $ticketToken ) . '"
                      title="' . Language::string(9) . '"><img src="' . $url . '/medias/icons/pencil.svg" /></a>';
        $actions .= '<a
                      href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticketToken ) . '"
                      target="_blank"
                      title="' . Language::string(10) . '"><img src="' . $url . '/medias/icons/pdf.svg" /></a>';
        if( $ticket["state"] == 2 ) {
          $actions .= '<a
                        href="' . $url_page . '&restore=' . urlencode( $ticketToken ) . '"
                        title="' . Language::string(11) . '"><img src="' . $url . '/medias/icons/restore.svg" /></a>';
        }else {
          $actions .= '<a
                        href="' . $url_page . '&remove=' . urlencode( $ticketToken ) . '"
                        title="' . Language::string(12) . '"><img src="' . $url . '/medias/icons/trash.svg" /></a>';
        }
    }else {
      $ticketToken = Ticket::encryptTicketToken($ticket["groupID"], $ticket["ticketKey"]);
      $actions = '<a
                    href="' . $url_page . '&view=' . urlencode( $ticketToken ) . '"
                    title="' . Language::string(9) . '"><img src="' . $url . '/medias/icons/view-eye.svg" /></a>';
      $actions .= '<a
                    href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticketToken ) . '"
                    target="_blank"
                    title="' . Language::string(10) . '"><img src="' . $url . '/medias/icons/pdf.svg" /></a>';
    }

    $table->addElement(
      array(
        'row' => array(
          'items' => array(
            array(
              'context' => '<div
                              class="color"
                              style="background-color: ' . $groupInfo["color"] . ';"
                              title="' . Language::string(13, array(
                                            '%name%' => $groupInfo["name"],
                                            '%id%' => $groupInfo["groupID"],
                                          )) . '"></div>' .
                              $ticket["email"],
            ),
            array(
              'context' => date("d.m.Y H:i:s", strtotime($ticket["purchase_time"])),
            ),
            array(
              'context' => ($actions ?? ''),
            ),
          ),
          'additional' => 'class="' . $ticket_state_class . '"',
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

  if( (count(Ticket::all( ($offset + $steps), $steps, $search_value)) > 0) && (($offset/$steps) > 0) ) { // More and less pages accessable
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
  }elseif (count(Ticket::all( ($offset + $steps), $steps, $search_value)) > 0) { // More pages accessable
    $table->addElement(
      array(
        'footer' => array(
          'context' => $next,
        ),
      ),
    );
  }


  $searchbar->prompt();
  $legend->prompt();
  $table->prompt();
}

function single_ticket() {
  //require variables
  global $page, $current_user, $url, $url_page, $conn;

  //Creat new ticket
  $ticket = new Ticket();
  $ticket->ticketToken = $_GET["view"];

  //Get group values
  $group = new Group();
  $group->groupID = $ticket->values()["groupID"];

  //Update payment if required
  checkPayment( $ticket->ticketToken );

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

  // Start right menu
  $rightMenu = new HTML('right-menu');

  $rightMenu->addElement(
    array(
      'context' => '<img src="' . $url . 'medias/icons/pdf.svg" alt="' . Language::string(26) . '" title="' . Language::string(27) . '"/>',
      'additional_item' => 'href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticket->ticketToken ) . '"
                            target="_blank"',
    ),
  );

  $rightMenu->addElement(
    array(
      'context' => '<img src="' . $url . 'medias/icons/mail.svg" alt="' . Language::string(28) . '" title="' . Language::string(29) . '"/>',
      'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&send"',
    ),
  );

  if( $ticket->values()["payment"] == 2 ) {
    $rightMenu->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/payment.svg" alt="' . Language::string(30) . '" title="' . Language::string(31) . '"/>',
        'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&requestPayment"',
      ),
    );
  }

  $transaction = retrieveTransaction( $ticket->ticketToken );
  if($transaction["transaction_retrieve_status"] && $ticket->values()["amount"] > 0 && ($transaction["pspId"] != 15 || $transaction["pspId"] != 27) && $ticket->values()["payment"] == 0) {
    $rightMenu->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/payment-refund.svg" alt="' . Language::string(32) . '" title="' . Language::string(33) . '"/>',
        'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&refund"',
      ),
    );
  }

  if( User::w_access_allowed($page, $current_user) ){
    //Check if ticket is already used
    if( $ticket->values()["state"] == 1 ){
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/toggleUsedTicket2.svg" alt="' . Language::string(34) . '" title="' . Language::string(35) . '"/>',
          'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&reactivate"',
        ),
      );
    }else{
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/toggleUsedTicket1.svg" alt="' . Language::string(36) . '" title="' . Language::string(37) . '"/>',
          'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&employ"',
        ),
      );
    }

    //Check if ticket is blocked
    if( $ticket->values()["state"] == 2 ){
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/restore.svg" alt="' . Language::string(38) . '" title="' . Language::string(39) . '"/>',
          'additional_item' => 'href="' . $url_page . '&restore=' . urlencode( $ticket->ticketToken ) . '"',
        ),
      );
    }else{
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/trash.svg" alt="' . Language::string(40) . '" title="' . Language::string(41) . '"/>',
          'additional_item' => 'href="' . $url_page . '&remove=' . urlencode( $ticket->ticketToken ) . '"',
        ),
      );
    }
  }

  // Start form
  $form = new HTML('form', array(
    'action' => $url . basename($_SERVER['REQUEST_URI']),
    'method' => 'post',
    'additional' => 'class="right-menu"',
  ));

  // Email
  $form->addElement(
    array(
      'type' => 'text',
      'name' => 'email',
      'value' => $ticket->values()["email"],
      'placeholder' => Language::string(42),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  // Payment method
  $options = array(
    0 => Language::string(43),
    1 => Language::string(44),
    2 => Language::string(45),
  );

  $form->addElement(
    array(
      'type' => 'select',
      'name' => 'payment',
      'value' =>  $options[$ticket->values()["payment"]] ?? '',
      'headline' => Language::string(46),
      'options' => $options,
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'amount',
      'value' => number_format(( $ticket->values()["amount"] / 100 ), 2),
      'placeholder' => Language::string(47),
      'input_attributes' => 'step="0.05" min="0"',
      'unit' => ($group->values()["currency"] ?? DEFAULT_CURRENCY),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  //Custom
  $customUserInputs = json_decode($ticket->values()["custom"], true);

  if(! empty($customUserInputs)) {
    foreach($customUserInputs as $customInput) {
      switch( $customInput["type"] ) {
        //---------------------------- Select-input ----------------------------//
        case "select":
          $options = explode(",", $customInput["options"]);

          $form->addElement(
            array(
              'type' => 'select',
              'name' => $customInput["id"],
              'value' =>  (($customInput["value"] == "") ? "" : $customInput["value"]),
              'options' => array_combine($options, $options), // Generate correct array
              'disabled' => ! User::w_access_allowed($page, $current_user),
              'required' => true,
            ),
          );
        break;
        //---------------------------- Radio-input ----------------------------//
        case "radio":
          $options = explode(",", $customInput["options"]);

          foreach($options as $option) {
            $form->addElement(
              array(
                'type' => 'radio',
                'name' => $customInput["id"],
                'value' =>  str_replace(" ", "_", $option) ?? '',
                'context' => $option,
                'checked' => (str_replace(" ", "_", $customInput["value"]) == $option) ? true : false,
                'disabled' => ! User::w_access_allowed($page, $current_user),
                'required' => true,
              ),
            );
          }
        break;
        //---------------------------- Checkbox-input ----------------------------//
        case "checkbox":
          $form->addElement(
            array(
              'type' => 'checkbox',
              'name' => $customInput["id"],
              'value' =>  $customInput["value"],
              'context' => $customInput["name"],
              'checked' => ! empty($customInput["value"]),
              'disabled' => ! User::w_access_allowed($page, $current_user),
              'required' => true,
            ),
          );
        break;
        //---------------------------- Textarea ----------------------------//
        case "textarea":
          $form->addElement(
            array(
              'type' => 'textarea',
              'name' => $customInput["id"],
              'value' => $customInput["value"],
              'placeholder' => $customInput["name"],
              'disabled' => ! User::w_access_allowed($page, $current_user),
              'required' => true,
            ),
          );
        break;
        //---------------------------- Text-input [Mail, Number, Date] ----------------------------//
        default: //Text input
          $form->addElement(
            array(
              'type' => $customInput["type"] ?? 'text',
              'name' => $customInput["id"],
              'value' => $customInput["value"],
              'placeholder' => $customInput["name"],
              'disabled' => ! User::w_access_allowed($page, $current_user),
              'required' => true,
            ),
          );
      }
    }
  }

  // Coupon
  $used_coupon = new Coupon();
  $used_coupon->couponID = $ticket->values()["coupon"];

  //Get infos
  $coupons = $conn->prepare("SELECT * FROM " . TICKETS_COUPONS . " WHERE groupID=:gid");
  $coupons->execute(array(":gid" => $ticket->values()["groupID"]));

  // Get input
  $headline = (empty($used_coupon->couponID)) ?
              Language::string(48) : //No coupon used
              $used_coupon->values()["name"] . ' -' . (
                empty($used_coupon->values()["discount_percent"]) ?
                  ($used_coupon->values()["discount_absolute"] / 100) . ' ' . $group->values()["currency"]  : //Correct absolute amount
                  ($used_coupon->values()["discount_percent"] / 100) . '%' //Correct percent
              );

  $options = array(
    "" => Language::string(48),
  );

  foreach($coupons->fetchAll(PDO::FETCH_ASSOC) as $coupon) {
    // Get new price
    $couponPrice = new Coupon();
    $couponPrice->couponID = $coupon["couponID"];
    $couponPrice = $couponPrice->new_price();

    // Get currency
    $group = new Group();
    $group->groupID = $ticket->values()["groupID"];

    $options[$coupon["couponID"]] = $coupon["name"] . ' (Neuer Preis: ' . ($couponPrice/100) . ' ' . ($group->values()["currency"]  ?? DEFAULT_CURRENCY) . ')';
  }


  $form->addElement(
    array(
      'type' => 'select',
      'name' => 'coupon',
      'value' =>  $ticket->values()["coupon"] ?? '',
      'headline' => $headline,
      'options' => $options,
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );


  //Display payment time
  if( $ticket->values()["payment"] != 2 ) {
    $form->customHTML("<span
                          class='ticket-payment-date'>" . Language::string( 49, array(
                            '%date%' =>date("H:i \a\m d.m.Y", strtotime($ticket->values()["payment_time"]))
                          ) ) .
                        "</span>");
  }

  //Update
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' =>  Language::string(50),
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  // Display headline
  echo '<div
          class="headline-maincolor"
          style="background-color: ' . $group->values()["color"] . '"
          title="' . Language::string( 20, array(
                      '%name%' => $group->values()["name"],
                      '%id%' => $group->values()["groupID"],
                    )) . '"></div>';

  $topNav->prompt();

  // Top bar message
  $ticket_states = array(
    array(
      'bcolor' => 'var(--ticket-payment-and-used)',
      'class' => 'payment-and-used',
      'title' => Language::string(1),
    ),
    array(
      'bcolor' => 'var(--ticket-blocked-and-payment)',
      'class' => 'blocked-and-payment',
      'title' => Language::string(2),
    ),
    array(
      'bcolor' => 'var(--ticket-payment-expected)',
      'class' => 'payment-expected',
      'title' => Language::string(3),
    ),
    array(
      'bcolor' => 'var(--ticket-used)',
      'class' => 'used',
      'title' => Language::string(4),
    ),
    array(
      'bcolor' => 'var(--ticket-blocked)',
      'class' => 'blocked',
      'title' => Language::string(5),
    ),
  );

  if( $ticket->values()["payment"] == 2 && $ticket->values()["state"] == 1) { //no payment but used
    echo '<div class="top-bar-ticket ' . $ticket_states[0]["class"] . '">' .
            Language::string( 21, array(
              '%date%' => date("d.m.Y H:i:s", strtotime($ticket->values()["employ_time"])),
            )) .
          '</div>';
  }elseif( $ticket->values()["payment"] != 2 && $ticket->values()["state"] == 2) { //Blocked/deleted and payed
    echo '<div class="top-bar-ticket ' . $ticket_states[1]["class"] . '">' .
            Language::string(22) .
          '</div>';
  }elseif( $ticket->values()["payment"] == 2 && $ticket->values()["state"] != 2) { //Payment expected
    echo '<div class="top-bar-ticket ' . $ticket_states[2]["class"] . '">' .
            Language::string(23) .
          '</div>';
  }elseif( $ticket->values()["state"] == 1) { //Ticket used
    echo '<div class="top-bar-ticket ' . $ticket_states[3]["class"] . '">' .
            Language::string( 24, array(
              '%date%' => date("d.m.Y H:i:s", strtotime($ticket->values()["employ_time"])),
            )) .
          '</div>';
  }elseif( $ticket->values()["state"] == 2) { //Ticked blocked and no payment
    echo '<div class="top-bar-ticket ' . $ticket_states[4]["class"] . '">' .
            Language::string(25) .
          '</div>';
  }

  $rightMenu->prompt();
  $form->prompt();
}

//Get current action
$action = $_GET;
unset($action["id"]); //Remove page
unset($action["sub"]); //Remove subpage
unset($action["row-start"]); //Remove tow to get only valid keys

switch(key($action)) {
  case "view":
    if(User::w_access_allowed($page, $current_user)) {
      // Start ticket
      $ticket = new Ticket();
      $ticket->ticketToken = $_GET["view"];

      // Employ ticket
      if( isset( $_GET["employ"] ) ) {
        if($ticket->employ()) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> entwertet werden.");
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> entwertet werden.");
        }
      }

      // Reactivate ticket
      elseif( isset( $_GET["reactivate"] ) ) {
        if($ticket->reactivate()) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> reaktiviert werden.");
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> reaktiviert werden.");
        }
      }

      // Send ticket
      elseif( isset( $_GET["send"] ) ) {
        if($ticket->sendTicket( $ticket->values()["email"] )) {
          Action::success("Die Mail konnte <strong>erfolgreich</strong> gesendet werden.");
        }else {
          Action::fail("Leider konnte Die Mail <strong>nicht</strong></b> gesendet werden.");
        }
      }

      // Request payment mail
      elseif( isset( $_GET["requestPayment"] ) ) {
        if($ticket->requestPayment( $ticket->values()["email"] )) {
          Action::success("Die Mail konnte <strong>erfolgreich</strong> gesendet werden.");
        }else {
          Action::fail("Leider konnte Die Mail <strong>nicht</strong></b> gesendet werden.");
        }
      }

      elseif( isset( $_GET["refund"] ) ) {
        if(! $_POST) {
          Action::confirm("Möchten Sie die Zahlung für das Ticket " . $_GET["view"]  . " wirklich rückerstatten?", $_GET["view"], "&view=" . urlencode( $_GET["view"]) . "&refund" );
        }

        if( isset($_POST["confirm"]) ) {
          $refund = refundTransaction( $_POST["confirm"] );

          if( $refund["transaction_refund_state"] === true) {
            Action::success("Das Geld wurde erfolgreich rückerstattet.");
          }else {
            Action::fail("Beim Rückerstatten ist ein Fehler aufgetreten: <br /> " . $refund["message"]);
          }
        }
      }

      // Update full ticket
      elseif(! empty($_POST)) {
        if($ticket->update($_POST)) {
          Action::success("Das Ticket konnte <strong>erfolgreich</strong> überarbeitet werden.");
        }else {
          Action::fail("Leider konnte das Ticket <strong>nicht</strong></b> überarbeitet werden.");
        }
      }
    }else {
      Action::fail("Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen");
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
      'action' => $url . basename($_SERVER['REQUEST_URI']),
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
                  Language::string( 61, array(
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
        'headline' => Language::string(60),
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'options' => $options,
        'required' => true,
      ),
    );

    // Email
    $form->addElement(
      array(
        'type' => 'text',
        'name' => 'email',
        'placeholder' => Language::string(42),
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    // Payment method
    $options = array(
      0 => Language::string(43),
      1 => Language::string(44),
      2 => Language::string(45),
    );

    $form->addElement(
      array(
        'type' => 'select',
        'name' => 'payment',
        'headline' => Language::string(46),
        'options' => $options,
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    // Send mail
    $form->addElement(
      array(
        'type' => 'checkbox',
        'name' => 'sendMail',
        'context' => Language::string(62),
        'checked' => true,
        'value' => 'true',
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    // Add
    $form->addElement(
      array(
        'type' => 'button',
        'name' => 'add',
        'value' => Language::string(63),
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    $topNav->prompt();
    $form->prompt();
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

    //Display tickets
    $search_value = (!empty($_GET["s"])) ? $_GET["s"] : '';
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
    //Display tickets
    $search_value = (!empty($_GET["s"])) ? $_GET["s"] : '';
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
