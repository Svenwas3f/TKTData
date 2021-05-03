<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage scanner actions
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $ticketToken: Crypted token of a ticket [of Ticket class]
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * Scanner->ticketInfo () [$ticketToken]
 *
 * Scanner->ticketInfoHTML ( $qr [boolean] ) [$ticketToken]
 *
 * Scanner->ticketEmploy () [$ticketToken] Alias of Ticket::employ();
 *
 * Scanner->ticketReactivate () [$ticketToken] Alias of Ticket::reactivate();
 *
 * Scanner->updateInfo ( $content [content of file])
 *
 * Scanner->readInfo ( $html [boolean] )
 *
 */
class Scanner extends Ticket {
  /**
   * Return info of ticket or false
   * Requires: $ticketToken
   */
  public function ticketInfo() {
    $accessabel_tokenInfo = array("groupID", "state", "amount", "payment", "purchase_time", "payment_time", "employ_time", "coupon", "email", "custom"); //Availabel infos
    return array_intersect_key($this->values(), array_flip($accessabel_tokenInfo));
  }

  /**
   * Return info of ticket in HTML or false
   * Requires: $ticketToken
   *
   * $qr = boolean true: video starts again; false: hides ticket [default]
   */
  public function ticketInfoHTML( $qr = false ) {
    //Require global variables
    global $url;

    //Get infos of ticket
    $ticketInfo = $this->ticketInfo();

    //Check if ticket exists
    if(is_null($ticketInfo)) {
      $html = '<div class="scann-result-container">';

        //Header image
        $html .= '<div class="header-img" title="TKTDATA"/></div>';

        //Infos
        $html .= '<div class="infos">';
          $html .= '<div class="row">';
            $html .= '<div style="color: #cf650d; text-align: center; padding: 40px;">Das angeforderte Ticket existiert nicht!</div>';
          $html .= '</div>';
        $html .= '</div>';

        //Buttons
        $html .= '<div class="button-container">';
          $html .= '<button class="cancel" onclick="scanner_cancel_ticket(' . $qr . ')">Abbrechen</button>';
        $html .= '</div>';

      $html .= '</div>';
      return $html;
    }

    $html = '<div class="scann-result-container">';
    //Top bar
    //Define ticket state
    $state_css = ['payment-and-used', 'blocked-and-payment', 'payment-expected', 'used', 'blocked'];
    if( $ticketInfo["payment"] == 2 && $ticketInfo["state"] == 1) { //no payment but used
      $html .= "<div class='top-bar-ticket " . $state_css[0] . "'>Ticket benützt um " . date("d.m.Y H:i:s", strtotime($ticketInfo["employ_time"])) . ", Zahlung nicht getätigt.</div>";
    }elseif( $ticketInfo["payment"] != 2 && $ticketInfo["state"] == 2) { //Blocked/deleted and payed
      $html .= "<div class='top-bar-ticket " . $state_css[1] . "'>Blockiertes Ticket, bereits bezahlt.</div>";
    }elseif( $ticketInfo["payment"] == 2 && $ticketInfo["state"] != 2) { //Payment expected
      $html .= "<div class='top-bar-ticket " . $state_css[2] . "'>Zahlung nicht getätigt.</div>";
    }elseif( $ticketInfo["state"] == 1) { //Ticket used
      $html .= "<div class='top-bar-ticket " . $state_css[3] . "'>Ticket eingelöst am " . date("d.m.Y H:i:s", strtotime($ticketInfo["employ_time"])) . ".</div>";
    }elseif( $ticketInfo["state"] == 2) { //Ticked blocked and no payment
      $html .= "<div class='top-bar-ticket " . $state_css[4] . "'>Ticket blockiert.</div>";
    }else {
      $html .= '';
    }

    //Header image
    $html .= '<div class="header-img" title="TKTDATA"/></div>';

    //Group name
    $group = new Group();
    $group->groupID = $ticketInfo["groupID"];
    $html .= '<div class="groupInfo" style="background-color: ' . $group->values()["color"] . '">' . $group->values()["name"] . '</div>';

    //General infos
    $html .= '<div class="infos">';
      $html .= '<div class="row">';
        $html .= '<div class="cell-4">E-Mail:</div>';
        $html .= '<div class="cell-4-3"><a href="mailto:' . $ticketInfo["email"] . '">' . $ticketInfo["email"] . '</a></div>';
      $html .= '</div>';

      //Custom infos
      $customInfos = json_decode($ticketInfo["custom"], true);

      if(is_array($customInfos)) {
        foreach($customInfos as $custom) {
          switch( $custom["type"] ) {
            case "email":
            $html .= '<div class="row">';
              $html .= '<div class="cell-4">' . $custom["name"] . ':</div>';
              $html .= '<div class="cell-4-3"><a href="mailto:' . $custom["value"] . '">' . $custom["value"] . '</a></div>';
            $html .= '</div>';
            break;
            case "date":
              $oneYear = 365*60*60*24;
              $differenceToNow = time() - strtotime($custom["value"]);
              $difference = floor($differenceToNow/$oneYear);

              if( $difference < 10) {
                $color = "#a7151e"; //winered
              }elseif( $difference < 12 ) {
                $color = "#f7189f"; //pink
              }elseif( $difference < 14 ) {
                $color = "#d25118"; //orange
              }elseif( $difference < 16 ) {
                $color = "#0fd11c"; //lightgreen
              }elseif( $difference < 18 ) {
                $color = "#3dbd8d"; //turkis
              }elseif( $difference < 20 ) {
                $color = "#f8bf2d"; //yellow
              }elseif( $difference < 30 ) {
                $color = "#ef364f"; //red
              }elseif( $difference < 40 ) {
                $color = "#750d6f"; //purple
              }elseif( $difference < 50 ) {
                $color = "#491c0f"; //brown
              }elseif( $difference < 60 ) {
                $color = "#144a07"; //darkgreen
              }elseif( $difference < 70 ) {
                $color = "#3846d0"; //Blue
              }else {
                $color = "#c16b18"; //ocker
              }

              $html .= '<div class="row">';
                $html .= '<div class="cell-4">' . $custom["name"] . ':</div>';
                $html .= '<div class="cell-4-3">' . date("d.m.Y", strtotime($custom["value"]))  . ' <span class="date-count" style="background-color: ' . $color . ';">' . $difference . '</span></div>';
              $html .= '</div>';
              break;
            default:
            $html .= '<div class="row">';
              $html .= '<div class="cell-4">' . $custom["name"] . ':</div>';
              $html .= '<div class="cell-4-3">' . $custom["value"]  . '</div>';
            $html .= '</div>';
          }
        }
      }

    $html .= '</div>';

    //Buttons
    $html .= '<div class="button-container">';
      if( $ticketInfo["state"] == 0) {
        $html .= '<button class="activate" onclick="scanner_employ_ticket(\'' . $this->ticketToken . '\')">Aktivieren</button>';
      }
      $html .= '<button class="cancel" onclick="scanner_cancel_ticket(' . $qr . ')">Abbrechen</button>';
    $html .= '</div>';

    return $html;
  }

  /**
   * Return true or false
   * Requires: $ticketToken
   */
  public function ticketEmploy() {
    return $this->employ();
  }

  /**
   * Return true or false
   * Requires: $ticketToken
   */
  public function ticketReactivate() {
    return $this->reactivate();
  }

  /**
   * Return true or false
   * $content = content for file
   */
  public function updateInfo( $content ) {
    //Path where file is
    $path = dirname(__FILE__, 2) . "/medias/files/scanner/9_info.html";

    //Check if file exists
    if(! file_exists($path)) {
      return false;
    }

    //Put content to file
    return file_put_contents( $path, str_replace(array("\r", "\n"), "", nl2br($content)) );
  }

  /**
   * returns content of file as html
   * $html: true = Returns with <br />. false = Returns with linebreaks \r\n
   */
  public function readInfo( $html = true ) {
    $path = dirname(__FILE__, 2) . "/medias/files/scanner/9_info.html";
    $fileContent = file_get_contents( $path ); //Get info of file
    return ($html === true) ? $fileContent : str_replace(array("<br />", "<br>"), "\r\n", $fileContent); //Return html
  }
}
 ?>
