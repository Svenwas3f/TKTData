/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to do ajax actions
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * Ajax ( page [page], callback [callback], action [Action name], values [JSON Values] )
 *
 * scanner_request_ticket ( ticketToken [Crypted token of a ticket], qr [boolean, video starts again; false: hides ticket [default]] )
 *
 * scanner_request_fullscreen_message ( ticketToken [Crypted token of a ticket] )
 *
 * scanner_cancel_fullscreen_message ()
 *
 * scanner_request_infoTxt( reqType [true: Returns with <br />. false: Returns with linebreaks \r\n] )
 *
 * scanner_request_update_infoTxt ( ele [Textarea element] )
 *
 * scanner_cancel_ticket ( video [boolean] )
 *
 * scanner_employ_ticket ( ticketToken [Crypted token of a ticket] )
 *
 * livedata_up ()
 *
 * livedata_down ()
 *
 * livedata_visitors ( add [HTML Element] )
 *
 * livedata_trend ()
 *
 * livedata_history ()
 *
 * livedata_historyUp ()
 *
 * livedata_historyDown ()
 *
 * group_custom ( group [groupID] )
 *
 * group_coupons ( group [groupID] )
 *
 * input_string ( id [stringID], callback [callback] )
 *
 * pub_product_visiliity_toggle ( item [HTML Element], pub [pubID], product_id [product_id] )
 *
 * pub_product_availability ( container [HTML Element], pub [pubID], product_id [product_id], availability [availability state])
 *
 * pub_add_right ( link [link element], user [UserID], pub [pubID], type [access type] )
 *
 * pub_remove_right ( link [link element], user [UserID], pub [pubID], type [access type] )
 *
 * toggleTipMoney( pub [pubID], img [HTML Element] )
 *
 * refundPayment ( paymentID [paymentID], amount [INT] )
 *
 * togglePickUp ( paymentID [paymentID], icon [HTML Element] )
 *
 */

/**
 * Ajax function
 *
 * page: Requested page
 * callback: Callback function
 * action: Action name
 * values: JSON Values that are needed
 */
function ajax( page, callback, action =null, values =null ) {
  //Important infos
  var base_url = location.protocol + '//' + location.host + location.pathname;
  var ajax_file = base_url + "/ajax.php";

  //Connect
  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      callback(this);
    }
  }
  req.open("POST", ajax_file, true);
  req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  req.send( "p=" + encodeURIComponent(page) + (action ? "&action=" + encodeURIComponent(action) : "") + (values ? "&values=" + encodeURIComponent(JSON.stringify(values)) : "") );
}

/**
 * Function to get infos of ticket
 *
 * ticketToken: Crypted token of a ticket
 * qr: boolean, video starts again; false: hides ticket [default]
 */
function scanner_request_ticket(ticketToken, qr = false) {
  var values = new Object();
  values["ticketToken"] = ticketToken;
  values["qr"] = qr;

  ajax(11, function(c) {
    document.getElementsByClassName("result-ticket")[0].innerHTML = c.responseText;
  }, "get_ticket", values);
}

/**
 * Function to get fullscreeninfos of ticket
 *
 * ticketToken: Crypted token of a ticket
 */
function scanner_request_fullscreen_message(ticketToken) {
  var values = new Object();
  values["ticketToken"] = ticketToken;

  ajax(11, function(c) {
    //Get response text
    ajax_response = JSON.parse(c.responseText);


    //Create new html
    var html = '<div class="fullscreen-result" style="background-color:  ' + ajax_response.color + ';">';
    html += '<div class="fullscreen-result-info">';
    html += '<img src="' + ajax_response.img + '">';
    html += '<span>' + ajax_response.message + '</span>';
    if(ajax_response.sound != false) {
      html += '<audio autoplay>';
        for(var i = 0; i < ajax_response.sound.length; i++) {
          html += '<source src="' + ajax_response.sound[i] + '" >';
        }
      html += '</audio>';
    }
    if (ajax_response.button == false) {
      setTimeout(scanner_cancel_fullscreen_message, 2000);
    } else {
      html += '<button onclick="scanner_cancel_fullscreen_message()">' + ajax_response.button + '</button>';
    }

    html += '</div>';
    html += '</div>';

    //Add html
    document.getElementsByClassName("fullscreen-alert-container")[0].innerHTML = html;
  }, "get_fullscreen_info", values);
}

/**
 * Removes fullscreen message and starts video again
 */
function scanner_cancel_fullscreen_message() {
  document.getElementsByClassName("fullscreen-result")[0].remove();

  tick(); //Search for new qr-code
  document.getElementsByTagName('canvas')[0].style.display = 'block'; //Display canva video
}

/**
 * Return infotext
 *
 * reqType = true: Returns with <br />. false: Returns with linebreaks \r\n
 */
function scanner_request_infoTxt( req_type = false ) {
  var values = new Object();
  values["reqType"] = req_type;
  ajax( 10, function(c) {
    document.getElementsByClassName("scanner-info-txt")[0].innerHTML = "<textarea onkeyup='scanner_request_update_infoTxt(this)'>" + c.responseText + "</textarea>";
  }, "get_info", values);
}

/**
 * Update infotext
 *
 * ele = Textarea element
 */
function scanner_request_update_infoTxt(ele) {
  var values = new Object();
  values["content"] = ele.value;

  ajax(10, function(c) {}, "update_info", values)
}

/**
 * Cancels ticket and go back to video if  required
 *
 * video: boolean, true: displays video, false = disables ticket [default]
 */
function scanner_cancel_ticket(video = false) {
  document.getElementsByClassName('scann-result-container')[0].remove();

  if (video == true) {
    tick(); //Search for new qr-code
    document.getElementsByTagName('canvas')[0].style.display = 'block'; //Display canva video
  }
}

/**
 * Function to employ ticket
 *
 * ticketToken: Crypted token of a ticket
 */
function scanner_employ_ticket(ticketToken) {
  var values = new Object();
  values["ticketToken"] = ticketToken;

  ajax(11, function(c) {
    //Display info message
    document.body.innerHTML += c.responseText;

    //Remove activation button
    document.getElementsByClassName("activate")[0].remove();
  }, "employ_ticket", values);
}

/**
 * Set livedata up
 */
function livedata_up() {
  ajax(15, function(c) {
    //Display error message
    document.body.innerHTML += c.responseText;
  }, "up");
}

/**
 * Set livedaa down
 */
function livedata_down() {
  ajax(15, function(c) {
    //Display error message
    document.body.innerHTML += c.responseText;
  }, "down");
}

/**
 * Get visitor infos
 *
 * callback: Callback function. this.responseText passed as first parameter
 */
function livedata_visitors(callback) {
  ajax(15, function(c) {
    //Display error message
    callback( c.responseText );
  }, "visitors");
}

/**
 * Changes trend in content-trend-img
 */
function livedata_trend() {
  ajax(15, function(c) {
    //Display error message
    document.getElementsByClassName("content-trend-img")[0].src = c.responseText;
  }, "trend");
}

/**
 * Updates history chart
 */
function livedata_history() {
  ajax(15, function(c) {
    //Display message
    var values = JSON.parse( c.responseText );
    var historyData = document.getElementById('history');
    var chartHistory = live_chart(historyData, values.data.x, values.data.y, values[3]);
  }, "history");
}

/**
 * Updates historyUp chart
 */
function livedata_historyUp() {
  ajax(15, function(c) {
    //Display message
    var values = JSON.parse( c.responseText );
    var historyData = document.getElementById('historyUp');
    var chartHistory = live_chart(historyData, values.data.x, values.data.y, values[4]);
  }, "historyUp");
}

/**
 * Updates historyDown chart
 */
function livedata_historyDown() {
  ajax(15, function(c) {
    //Display message
    var values = JSON.parse( c.responseText );
    var historyData = document.getElementById('historyDown');
    var chartHistory = live_chart(historyData, values.data.x, values.data.y, values[5]);
  }, "historyDown");
}

/**
 * Gets custom elements of group
 *
 * group: group ID
 */
function group_custom( group ) {
  var values = new Object();
  values["groupID"] = group;

  ajax(7, function(c) {
    // Generate html
    var container = document.createElement('div');
    container.innerHTML = c.responseText;

    var custom = container.getElementsByTagName("form")[0].innerHTML;

    //Display message
    document.getElementsByClassName("custom-add-container")[0].innerHTML = custom;
  }, "get_custom", values);
}

/**
 * Gets all coupons of group
 *
 * group: group ID
 */
function group_coupons(group) {
  var values = new Object();
  values["groupID"] = group;

  ajax(7, function(c) {
    // Generate html
    var container = document.createElement('div');
    container.innerHTML = c.responseText;

    var select = container.getElementsByTagName("form")[0].innerHTML;

    //Display message
    document.getElementsByClassName("coupon-add-container")[0].innerHTML = select;
  }, "get_coupons", values);
}

/**
 * Get language string
 * id: Language string id
 * callback: callback function
 */
function input_string( id, callback) {
  var values = new Object();
  values["id"] = id;

  ajax( 7, function(c) {
    callback( c.responseText );
  }, "get_string", values);
}

/**
 * Changes visibility of a product and the pub
 *
 * item: container of image
 * pub: pub ID
 * product_id: product ID
 */
function pub_product_visiliity_toggle(item, pub, product_id) {
  var values = new Object();
  values["pub"] = pub,
  values["product_id"] = product_id;

  ajax(17, function(c) {
    // Get response
    var ajax_response = JSON.parse(c.responseText);

    item.firstChild.src = ajax_response.img_src;
  }, "toggleVisibility", values);
}

/**
 * Changes availability of a product and the pub
 *
 * container: container where availability states are stored
 * pub: pb ID
 * product_id: product ID
 * availability = 0: available
 *                1: little available
 *                2: sold
 */
function pub_product_availability( container, pub, product_id, availability) {
  var values = new Object();
  values["pub"] = pub,
  values["product_id"] = product_id;
  values["availability"] = availability;

  ajax(17, function(c) {
    // Get response
    var ajax_response = JSON.parse(c.responseText);

    if( ajax_response.status == true) {
      // Remove classes
      for(var i = 0; i < container.children.length; i++) {
        container.children[i].classList.remove("current");
      }

      // Add to new class
      container.children[availability].classList.add("current");
    }
  }, "update_availability", values);
}

/**
 * Set new pub rights
 *
 * link: onclick link for new icons
 * user: User who should have access to pub
 * pub: Pub ID
 * type: r (read) or w (write)
 */
function pub_add_right( link, user, pub, type = "r" ) {
  var values = new Object();
  values["user"] = user;
  values["pub"] = pub,
  values["type"] = type;

  ajax(18, function(c) {
    try {
      // Get json
      var ajax_response = JSON.parse(c.responseText);

      // Add values
      var td = link.parentNode;

      td.children[0].children[0].src = ajax_response.img_w;
      td.children[0].title = ajax_response.title_w;
      td.children[0].setAttribute("onclick", ajax_response.onclick_name_w);

      td.children[1].children[0].src = ajax_response.img_r;
      td.children[1].title = ajax_response.title_r;
      td.children[1].setAttribute("onclick", ajax_response.onclick_name_r);

    } catch (e) {
      document.getElementsByTagName("body")[0].innerHTML += c.responseText;
    }
  }, "add_right", values);
}

/**
 * remove pub rights
 *
 * link: onclick link for new icons
 * user: User who should have access to pub
 * pub: Pub ID
 * type: r (read) or w (write)
 */
function pub_remove_right( link, user, pub, type = "r" ) {
  var values = new Object();
  values["user"] = user;
  values["pub"] = pub,
  values["type"] = type;

  ajax(18, function(c) {
    try {
      // Get json
      var ajax_response = JSON.parse(c.responseText);

      // Add values
      var td = link.parentNode;

      td.children[0].children[0].src = ajax_response.img_w;
      td.children[0].title = ajax_response.title_w;
      td.children[0].setAttribute("onclick", ajax_response.onclick_name_w);


      td.children[1].children[0].src = ajax_response.img_r;
      td.children[1].title = ajax_response.title_r;
      td.children[1].setAttribute("onclick", ajax_response.onclick_name_r);

    } catch (e) {
      document.getElementsByTagName("body")[0].innerHTML += c.responseText;
    }
  }, "remove_right", values);
}

/**
 * toggles tip money availability
 *
 * pub: Pub ID
 * img: HTML Element where new image should be placed
 */
function toggleTipMoney( pub, img ) {
  var values = new Object();
  values["pub"] = pub;

  ajax(18, function(c) {
    // Get json
    var ajax_response = JSON.parse(c.responseText);

    // Set new src
    img.src = ajax_response.img_src;
  }, "toggle_tip", values);
}

/**
 * refunds amount
 *
 * paymentID. Payment ID
 * Amount: Amount that should be refounded
 */
function refundPayment( paymentID, amount ) {
  var values = new Object();
  values["paymentID"] = paymentID;
  values["amount"] = amount;

  ajax(16, function(c) {
    // Get json
    var ajax_response = JSON.parse(c.responseText);

    // Check answer
    if(ajax_response.hasOwnProperty("error")) {
      ajax(16, function(c) {
        document.getElementsByTagName("article")[0].innerHTML += c.responseText;
      }, "message", ajax_response.error)

    }else {
      // Get details
      var details = document.getElementsByClassName("details")[0];

      // Change values
      details.getElementsByClassName("refund")[0].getElementsByClassName("value")[0].innerHTML = ajax_response.formated_refund + " " + ajax_response.currency;
      details.getElementsByClassName("fees")[0].getElementsByClassName("value")[0].innerHTML = ajax_response.formated_fees + " " + ajax_response.currency;
      details.getElementsByClassName("new_amount")[0].getElementsByClassName("value")[0].innerHTML = ajax_response.formated_new_amount + " " + ajax_response.currency;

      // // Show success
      ajax(16, function(c) {
        document.getElementsByTagName("article")[0].innerHTML += c.responseText;
      }, "message", ajax_response.success)
    }


  }, "refundPayment", values);
}

/**
 * Toggles pickup
 *
 * paymentID: Payment ID
 * icon: HTLM Icon container
 */
function togglePickUp( paymentID, icon ) {
  var values = new Object();
  values["paymentID"] = paymentID;

  ajax(16, function(c) {
    // Get json
    var ajax_response = JSON.parse(c.responseText);

    console.log(ajax_response.message);

    // Change icon
    icon.children[0].src = ajax_response.img_src;

    // Change info text
    document.getElementsByClassName('detail-item state')[0].getElementsByClassName("value")[0].innerHTML = ajax_response.message;
  }, "togglePickUp", values);
}

/**
 *
 */
function confirmPayment( paymentID, icon ) {
  var values = new Object();
  values["paymentID"] = paymentID;

  ajax(16, function(c) {
    if( c.responseText == "true" ) {
      icon.remove();
    }else {
      // Generate error
      var message = new Object();
      message["id"] = 74;
      message["type"] = "error";

      ajax(16, function(c) {
        document.getElementsByTagName("article")[0].innerHTML += c.responseText;
      }, "message", message)
    }
  }, "confirmPayment", values);
}
