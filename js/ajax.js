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

  ajax(10, function(c) {
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

  ajax(10, function(c) {
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
    if (ajax_response.button == true) {
      html += '<button onclick="scanner_cancel_fullscreen_message()">Verstanden</button>';
    } else {
      setTimeout(scanner_cancel_fullscreen_message, 2000);
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
  ajax( 9, function(c) {
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

  ajax(9, function(c) {}, "update_info", values)
}

/**
 * Cancels ticket and go back to video if  required
 *
 * video: boolean, true: displays video, false = disables ticket [default]
 */
function scanner_cancel_ticket(video = false) {
  document.getElementsByClassName('scann-result-container')[0].remove();

  if (video === true) {
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

  ajax(10, function(c) {
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
  ajax(13, function(c) {
    //Display error message
    document.body.innerHTML += c.responseText;
  }, "up");
}

/**
 * Set livedaa down
 */
function livedata_down() {
  ajax(13, function(c) {
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
  ajax(13, function(c) {
    //Display error message
    var reqAnswer = c.responseText;
    callback( c.responseText );
  }, "visitors")
} //TODO

/**
 * Changes trend in content-trend-img
 */
function livedata_trend() {
  ajax(13, function(c) {
    //Display error message
    document.getElementsByClassName("content-trend-img")[0].src = c.responseText;
  }, "trend");
}

/**
 * Updates history chart
 */
function livedata_history() {
  ajax(13, function(c) {
    //Display message
    var data = c.responseText;
    var historyData = document.getElementById('history');
    var chartHistory = live_chart(historyData, JSON.parse(data).x, JSON.parse(data).y, "Verlauf");
  }, "history");
}

/**
 * Updates historyUp chart
 */
function livedata_historyUp() {
  ajax(13, function(c) {
    //Display message
    var data = c.responseText;
    var historyData = document.getElementById('historyUp');
    var chartHistory = live_chart(historyData, JSON.parse(data).x, JSON.parse(data).y, "Eintritte");
  }, "historyUp");
}

/**
 * Updates historyDown chart
 */
function livedata_historyDown() {
  ajax(13, function(c) {
    //Display message
    var data = c.responseText;
    var historyData = document.getElementById('historyDown');
    var chartHistory = live_chart(historyData, JSON.parse(data).x, JSON.parse(data).y, "Eintritte");
  }, "historyDown");
}

/**
 * Gets custom elements of group
 */
function group_custom(group) {
  var values = new Object();
  values["groupID"] = group;

  ajax(6, function(c) {
    //Display message
    document.getElementsByClassName("custom-add-container")[0].innerHTML = c.responseText;
  }, "get_custom", values);
}

/**
 * Gets all coupons of group
 */
function group_coupons(group) {
  var values = new Object();
  values["groupID"] = group;

  ajax(6, function(c) {
    //Display message
    document.getElementsByClassName("custom-add-container")[0].innerHTML = c.responseText;
  }, "get_coupons", values);
}
