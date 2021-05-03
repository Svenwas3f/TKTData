/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: January 2021
 * @Purpose: File to do ajax actions
 *
 **************** All functions ****************
  * For further description please go to requested function
 *
 * ajax_send_mail ( ticketToken [Token of ticket] )
 *
 */
var base_url = location.protocol + '//' + location.host + location.pathname;
var ajax_file = base_url + "ajax.php";

/**
 * Send mail again
 */
function ajax_send_mail( email, id ) {
  /* Inform user */
  document.getElementsByClassName("ajax-response")[0].innerHTML = '  <div class="message-container"><div class="message waiting" onclick="this.remove()"><img src="' + base_url.replace("/store/find-ticket", "") +  '/medias/icons/waiting.svg"><span>die Mail wird gesendet. Wir bitten um etwas Geduld.</span></div></div>';

  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementsByClassName("ajax-response")[0].innerHTML = this.responseText;
    }
  }
  req.open("GET", ajax_file + encodeURI("?email=" + email + "&id=" + id), true);
  req.send();
}
