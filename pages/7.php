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
      'additional' => 'title="' . Language::string(51) . '"',
    ),
  );

  // Start right menu
  $rightMenu = new HTML('right-menu');

  $rightMenu->addElement(
    array(
      'context' => '<img src="' . $url . 'medias/icons/pdf.svg" alt="' . Language::string(27) . '" title="' . Language::string(28) . '"/>',
      'additional_item' => 'href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode( $ticket->ticketToken ) . '"
                            target="_blank"',
    ),
  );

  $rightMenu->addElement(
    array(
      'context' => '<img src="' . $url . 'medias/icons/mail.svg" alt="' . Language::string(29) . '" title="' . Language::string(30) . '"/>',
      'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&send"',
    ),
  );

  if( $ticket->values()["payment"] == 2 ) {
    $rightMenu->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/payment.svg" alt="' . Language::string(31) . '" title="' . Language::string(32) . '"/>',
        'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&requestPayment"',
      ),
    );
  }

  $transaction = retrieveTransaction( $ticket->ticketToken );
  if($transaction["transaction_retrieve_status"] && $ticket->values()["amount"] > 0 && ($transaction["pspId"] != 15 || $transaction["pspId"] != 27) && $ticket->values()["payment"] == 0) {
    $rightMenu->addElement(
      array(
        'context' => '<img src="' . $url . 'medias/icons/payment-refund.svg" alt="' . Language::string(33) . '" title="' . Language::string(34) . '"/>',
        'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&refund"',
      ),
    );
  }

  if( User::w_access_allowed($page, $current_user) ){
    //Check if ticket is already used
    if( $ticket->values()["state"] == 1 ){
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/toggleUsedTicket2.svg" alt="' . Language::string(35) . '" title="' . Language::string(36) . '"/>',
          'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&reactivate"',
        ),
      );
    }else{
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/toggleUsedTicket1.svg" alt="' . Language::string(37) . '" title="' . Language::string(38) . '"/>',
          'additional_item' => 'href="' . $url_page . '&view=' . urlencode( $ticket->ticketToken ) . '&employ"',
        ),
      );
    }

    //Check if ticket is blocked
    if( $ticket->values()["state"] == 2 ){
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/restore.svg" alt="' . Language::string(39) . '" title="' . Language::string(40) . '"/>',
          'additional_item' => 'href="' . $url_page . '&restore=' . urlencode( $ticket->ticketToken ) . '"',
        ),
      );
    }else{
      $rightMenu->addElement(
        array(
          'context' => '<img src="' . $url . 'medias/icons/trash.svg" alt="' . Language::string(41) . '" title="' . Language::string(42) . '"/>',
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
      'type' => 'email',
      'name' => 'email',
      'value' => $ticket->values()["email"],
      'placeholder' => Language::string(43),
      'disabled' => ! User::w_access_allowed($page, $current_user),
      'required' => true,
    ),
  );

  // Payment method
  $options = array(
    0 => Language::string(44),
    1 => Language::string(45),
    2 => Language::string(46),
  );

  $form->addElement(
    array(
      'type' => 'select',
      'name' => 'payment',
      'value' =>  $options[$ticket->values()["payment"]] ?? '',
      'headline' => Language::string(47),
      'options' => $options,
      'disabled' => ! User::w_access_allowed($page, $current_user),
    ),
  );

  $form->addElement(
    array(
      'type' => 'number',
      'name' => 'amount',
      'value' => number_format(( $ticket->values()["amount"] / 100 ), 2),
      'placeholder' => Language::string(48),
      'input_attributes' => 'step="0.01" value="0.00"',
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
              'required' => $customInput["required"],
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
                'required' => $customInput["required"],
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
              'required' => $customInput["required"],
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
              'required' => $customInput["required"],
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
              'required' => $customInput["required"],
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
              Language::string(49) : //No coupon used
              $used_coupon->values()["name"] . ' -' . (
                empty($used_coupon->values()["discount_percent"]) ?
                  ($used_coupon->values()["discount_absolute"] / 100) . ' ' . $group->values()["currency"]  : //Correct absolute amount
                  ($used_coupon->values()["discount_percent"] / 100) . '%' //Correct percent
              );

  $options = array(
    "" => Language::string(49),
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
                          class='ticket-payment-date'>" . Language::string( 50, array(
                            '%date%' =>date("H:i \a\m d.m.Y", strtotime($ticket->values()["payment_time"]))
                          ) ) .
                        "</span>");
  }

  //Update
  $form->addElement(
    array(
      'type' => 'button',
      'name' => 'update',
      'value' =>  Language::string(51),
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
            Language::string( 22, array(
              '%date%' => date("d.m.Y H:i:s", strtotime($ticket->values()["employ_time"])),
            )) .
          '</div>';
  }elseif( $ticket->values()["payment"] != 2 && $ticket->values()["state"] == 2) { //Blocked/deleted and payed
    echo '<div class="top-bar-ticket ' . $ticket_states[1]["class"] . '">' .
            Language::string(23) .
          '</div>';
  }elseif( $ticket->values()["payment"] == 2 && $ticket->values()["state"] != 2) { //Payment expected
    echo '<div class="top-bar-ticket ' . $ticket_states[2]["class"] . '">' .
            Language::string(24) .
          '</div>';
  }elseif( $ticket->values()["state"] == 1) { //Ticket used
    echo '<div class="top-bar-ticket ' . $ticket_states[3]["class"] . '">' .
            Language::string( 25, array(
              '%date%' => date("d.m.Y H:i:s", strtotime($ticket->values()["employ_time"])),
            )) .
          '</div>';
  }elseif( $ticket->values()["state"] == 2) { //Ticked blocked and no payment
    echo '<div class="top-bar-ticket ' . $ticket_states[4]["class"] . '">' .
            Language::string(26) .
          '</div>';
  }

  $topNav->prompt();
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
          Action::success( Language::string(70) );
        }else {
          Action::fail( Language::string(71) );
        }
      }

      // Reactivate ticket
      elseif( isset( $_GET["reactivate"] ) ) {
        if($ticket->reactivate()) {
          Action::success( Language::string(72) );
        }else {
          Action::fail( Language::string(73) );
        }
      }

      // Send ticket
      elseif( isset( $_GET["send"] ) ) {
        if($ticket->sendTicket( $ticket->values()["email"] )) {
          Action::success( Language::string(74) );
        }else {
          Action::fail( Language::string(75) );
        }
      }

      // Request payment mail
      elseif( isset( $_GET["requestPayment"] ) ) {
        if($ticket->requestPayment( $ticket->values()["email"] )) {
          Action::success( Language::string(76) );
        }else {
          Action::fail( Language::string(77) );
        }
      }

      elseif( isset( $_GET["refund"] ) ) {
        if(! $_POST) {
          Action::confirm(
            Language::string( 78, array(
              '%ticketToken%' => $_GET["view"],
            )),
            $_GET["view"],
            "&view=" . urlencode( $_GET["view"]) . "&refund"
          );
        }

        if( isset($_POST["confirm"]) ) {
          $refund = refundTransaction( $_POST["confirm"] );

          if( $refund["transaction_refund_state"] === true) {
            Action::success( Language::string(79) );
          }else {
            Action::fail( Language::string( 80, array(
              '%message%' => $refund["message"],
            )));
          }
        }
      }

      // Update full ticket
      elseif(! empty($_POST)) {
        if($ticket->update($_POST)) {
          Action::success( Language::string(81) );
        }else {
          Action::fail( Language::string(82) );
        }
      }
    }else {
      Action::fail( Language::string(83) );
    }

    //Display ticket
    single_ticket();
  break;
  case "add":
    //Add
    if(! empty($_POST)) {
      if(User::w_access_allowed($page, $current_user)) {
        $ticket = new Ticket();
        $add = $ticket->add($_POST, false, ($_POST["sendMail"] ?? false));

        if($add == 6) {
          Action::fail( Language::string(84) );
        }elseif($add == 5) {
          Action::fail( Language::string(85) );
        }elseif($add == 4) {
          Action::fail( Language::string(86) );
        }elseif($add == 3) {
          Action::fail( Language::string(87) );
        }elseif($add == 2) {
          Action::fail( Language::string(88) );
        }elseif($add == 1) {
          Action::success( Language::string(89, array(
              '%url_page%' => $url_page,
              '%ticketToken%' => urlencode( $ticket->ticketToken ),
            )
          ));
        }else {
          Action::fail( Language::string(90) );
        }
      }else {
        Action::fail( Language::string(91) );
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
        'additional' => 'title="' . Language::string(21) . '"',
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

      $options[$row["groupID"]]["additional"] = 'style="border-left: 5px solid ' . $row["color"] . ';"
                                                 title="' .
                                                    Language::string( 61, array(
                                                      '%availableTickets%' => $group->availableTickets(),
                                                      '%maxTickets%' => $row["maxTickets"],
                                                      '%tpu%' => $row["tpu"],
                                                      '%price%' => ($row["price"]/100),
                                                      '%currency%' => $row["currency"],
                                                      '%vat%' => $row["vat"],
                                                    ),) . '"';
      $options[$row["groupID"]]["onclick"] = 'group_coupons(' . $row["groupID"] . '); group_custom(' . $row["groupID"] . ')';
      $options[$row["groupID"]]["name"] = $row["name"];
    }

    //group_coupons(' . $row["groupID"] . '); group_custom(' . $row["groupID"] . ')

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
        'type' => 'email',
        'name' => 'email',
        'placeholder' => Language::string(43),
        'disabled' => ! User::w_access_allowed($page, $current_user),
        'required' => true,
      ),
    );

    // Payment method
    $options = array(
      0 => Language::string(44),
      1 => Language::string(45),
      2 => Language::string(46),
    );

    $form->addElement(
      array(
        'type' => 'select',
        'name' => 'payment',
        'headline' => Language::string(47),
        'options' => $options,
        'disabled' => ! User::w_access_allowed($page, $current_user),
      ),
    );

    //Custom
    $form->customHTML('<div class="custom-add-container"></div>');

    //Coupon
    $form->customHTML('<div class="coupon-add-container"></div>');

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
        Action::success( Language::string(92) );
      }else {
        Action::fail( Language::string(93) );
      }
    }else {
      Action::fail( Language::string(94) );
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
        Action::success( Language::string(95) );
      }else {
        Action::fail( Language::string(96) );
      }
    }else {
      Action::fail( Language::string(97) );
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
