<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: Manage mail
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd throug the function are written after the function name inround brackets ().
 *
 * TKTDataMailer->htmlMail_TktdataMail ( $msg [HTML text message] )
 *
 * TKTDataMailer->htmlMail_TicketMail ( $ticketToken [Crypted token of a ticket] )
 *
 * TKTDataMailer->htmlMail_paymentRequestMail ( $ticketToken [Crypted token of a ticket] )
 *
 * TKTDataMailer->htmlMail_InvoicePub ( $paymentID [INT] )
 *
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exceptions;

class TKTDataMailer extends PHPMailer {
  /**
   * Sends tktdata mail
   *
   * $msg = HTML text message
   */
  public function htmlMail_TktdataMail( $msg, $lang_code = DEFAULT_LANGUAGE ) {
    //Require variables
    global $url;

    // Generate new html
    $return = '<!DOCTYPE html>';
    $return .= '<html lang="de" dir="ltr">';
      $return .= '<head>';
        $return .= '<meta charset="utf-8">';
        $return .= '<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />';

        $return .= '<style>';
        $return .= '@media screen and (max-width: 700px) {';

          $return .= 'table[class="mail-container"] {';
            $return .= 'width: 100% !important;';
          $return .= '}';

          $return .= 'img[class="logo"] {';
            $return .= 'margin: 20px 0px !important;';
          $return .= '}';

        $return .= '}';
        $return .= '</style>';
      $return .= '</head>';

      $return .= '<body>';
        $return .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->';
          $return .= '<tr>';
            $return .= '<td>';
              $return .= '<table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 40px 60px 40px 60px; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->';
                $return .= '<tr>';
                  $return .= '<td>';

                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; font-size: 15pt;font-weight: bolder;">';
                      $return .= '<tr>';
                        $return .= '<td>';
                          $return .= '<img src="' . $url . 'medias/logo/logo-fitted.png" style="display: block; width: 100%;" class="logo" alt="' . Language::string(0, null, "general", null, $lang_code) . '">';
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';


                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">';
                      $return .= '<tr>';
                        $return .= '<td style="margin: 20px 0px;">';
                          $return .= utf8Html( $msg );
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';

                  $return .= '</td>';
                $return .= '</tr>';
              $return .= '</table>';
            $return .= '</td>';
          $return .= '</tr>';
        $return .= '</table>';
      $return .= '</body>';
    $return .= '</html>';

    // Return values
    return $return;
  }

  /**
  * Sends ticket mail (Add ticket as attachment if required)
  *
  * $ticketToken = Crypted token of a ticket
   */
  public function htmlMail_TicketMail( $ticketToken ) {
    //Require variables
    global $url;

    //Start ticket
    $ticket = new Ticket();
    $ticket->ticketToken = $ticketToken;

    //Start group
    $group = new Group();
    $group->groupID = $ticket->cryptToken()["gid"];

    // Get language
    $lang_code = $group->values()["payment_store_language"];

    //Get header image
    if( isset( $group->values()["mail_banner_fileID"] ) &&! empty( $group->values()["mail_banner_fileID"] ) ) {
      $mediaHub = new MediaHub();
      $mediaHub->fileID = $group->values()["mail_banner_fileID"];

      $imgUrl = $mediaHub->getUrl( $group->values()["mail_banner_fileID"] );
      $altImage = $mediaHub->fileDetails()["alt"];
    }else {
      $imgUrl = $url . 'medias/logo/logo-fitted.png'; //No image found\Logo of tktdata
      $altImage = Language::string(0, null, "general", null, $lang_code);
    }

    //Define message
    $msg = $group->values()["mail_msg"];

    //Replace ticket and email
    $msg = str_replace("%E-Mail%", $ticket->values()["email"], $msg); //Replace email with user email if required
    $msg = str_replace("%Ticket%", '<table width="100%" cellspacing="0" cellpadding="0">' .
                                   '<tr>' .
                                     '<td>' .
                                       '<table cellspacing="0" cellpadding="0">' .
                                         '<tr>' .
                                           '<td style="border-radius: 2px;" bgcolor="#232b43">' .
                                             '<a href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode($ticketToken) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">' . Language::string( 8, null, "email", null, $lang_code ) . '</a>' .
                                           '</td>' .
                                         '</tr>' .
                                       '</table>' .
                                     '</td>' .
                                     '</tr>' .
                                   '</table>', $msg); //Replace ticket with ticket field

    if(! empty($ticket->values()["custom"])) {
      //Get values
      $namesGroup = array_column(json_decode($group->values()["custom"], true), "name"); //Get all names from custom grop
      $namesTickets = array_column(json_decode($ticket->values()["custom"], true), "name"); //Get all names from custom ticket

      foreach($namesGroup as $name) {
        //Get value
        $ticketValues = json_decode($ticket->values()["custom"], true); //Get all values
        $ticketNamesKey = array_search($name, $namesTickets); //Get key of value

        //Value to replace
        $replaceValue = $ticketValues[$ticketNamesKey]["value"];

        //Replace
        $msg = str_replace("%" . $name . "%", $replaceValue, $msg);
      }
    }

    // Generate new html
    $return = '<!DOCTYPE html>';
    $return .= '<html lang="de" dir="ltr">';
      $return .= '<head>';
        $return .= '<meta charset="utf-8">';
        $return .= '<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />';
        $return .= '<title></title>';

        $return .= '<style>';
        $return .= '@media screen and (max-width: 700px) {';

          $return .= 'table[class="mail-container"] {';
            $return .= 'width: 100% !important;';
          $return .= '}';

          $return .= 'img[class="logo"] {';
            $return .= 'margin: 20px 0px !important;';
          $return .= '}';

        $return .= '}';
        $return .= '</style>';
      $return .= '</head>';

      $return .= '<body>';
        $return .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->';
          $return .= '<tr>';
            $return .= '<td>';
              $return .= '<table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 40px 60px 40px 60px; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->';
                $return .= '<tr>';
                  $return .= '<td>';

                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; font-size: 15pt;font-weight: bolder;">';
                      $return .= '<tr>';
                        $return .= '<td>';
                          $return .= '<img src="' . $imgUrl . '" style="display: block; width: 100%;" class="logo" alt="' . $altImage . '">';
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';


                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">';
                      $return .= '<tr>';
                        $return .= '<td style="margin: 20px 0px;">';
                          $return .= utf8Html( $msg );
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';

                  $return .= '</td>';
                $return .= '</tr>';
              $return .= '</table>';
            $return .= '</td>';
          $return .= '</tr>';
        $return .= '</table>';
      $return .= '</body>';
    $return .= '</html>';

    // Return values
    return $return;
  }

  /**
  * Sends ticket mail (Add ticket as attachment if required)
  *
  * $ticketToken = Crypted token of a ticket
   */
  public function htmlMail_paymentRequestMail( $ticketToken ) {
    //Require variables
    global $url;

    //Start ticket
    $ticket = new Ticket();
    $ticket->ticketToken = $ticketToken;

    //Start group
    $group = new Group();
    $group->groupID = $ticket->cryptToken()["gid"];

    // Get language
    $lang_code = $group->values()["payment_store_language"];

    //Define message
    $msg = $group->values()["payment_mail_msg"];

    //Get header image
    $path = dirname(__FILE__, 2) . '/medias/groups/' . $group->groupID . '/mail/banner/*'; //Path where img is stored
    if(! glob($path)) {
      $imgUrl = $url . 'medias/logo/logo-fitted.png'; //No image found\Logo of tktdata
    }else {
      $imgUrl = $url . 'medias/groups/' . $group->groupID . "/mail/banner/" . pathinfo(glob($path)[0], PATHINFO_BASENAME); //Onw image
    }

    //Get transaction
    $transaction = retrieveTransaction( $ticketToken );

    //Replace ticket and email
    $msg = str_replace("%E-Mail%", $ticket->values()["email"], $msg); //Replace email with user email if required
    if( $transaction["transaction_retrieve_status"] == false || $transaction["pspId"] !== 15 || $transaction["pspId"] !== 27 ) {
      $msg = str_replace("%Pay-Link%", '<table width="100%" cellspacing="0" cellpadding="0">' .
                                         '<tr>' .
                                           '<td>' .
                                             '<table cellspacing="0" cellpadding="0">' .
                                               '<tr>' .
                                                 '<td style="border-radius: 2px;" bgcolor="#232b43">' .
                                                   '<a href="' . $url . 'store/ticket/pay/?ticketToken=' . urlencode($ticketToken) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">' . Language::string( 11, null, "email", null, $lang_code ) . '</a>' .
                                                 '</td>' .
                                               '</tr>' .
                                             '</table>' .
                                           '</td>' .
                                         '</tr>' .
                                       '</table>', $msg); //Replace ticket with ticket field
    }

    // Generate new html
    $return = '<!DOCTYPE html>';
    $return .= '<html lang="de" dir="ltr">';
      $return .= '<head>';
        $return .= '<meta charset="utf-8">';
        $return .= '<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />';
        $return .= '<title></title>';

        $return .= '<style>';
        $return .= '@media screen and (max-width: 700px) {';

          $return .= 'table[class="mail-container"] {';
            $return .= 'width: 100% !important;';
          $return .= '}';

          $return .= 'img[class="logo"] {';
            $return .= 'margin: 20px 0px !important;';
          $return .= '}';

        $return .= '}';
        $return .= '</style>';
      $return .= '</head>';

      $return .= '<body>';
        $return .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->';
          $return .= '<tr>';
            $return .= '<td>';
              $return .= '<table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 40px 60px 40px 60px; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->';
                $return .= '<tr>';
                  $return .= '<td>';

                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; font-size: 15pt;font-weight: bolder;">';
                      $return .= '<tr>';
                        $return .= '<td>';
                          $return .= '<img src="' . $imgUrl . '" style="display: block; width: 100%;" class="logo" alt="TKTDATA TICKETSYSTEM">';
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';


                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">';
                      $return .= '<tr>';
                        $return .= '<td style="margin: 20px 0px;">';
                          $return .= utf8Html( $msg );
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';

                  $return .= '</td>';
                $return .= '</tr>';
              $return .= '</table>';
            $return .= '</td>';
          $return .= '</tr>';
        $return .= '</table>';
      $return .= '</body>';
    $return .= '</html>';

    // Return values
    return $return;
  }

  /**
   * Sends payment invoice for transactions
   *
   * $paymentID = Number (INT)
   */
  public function htmlMail_InvoicePub( $paymentID ) {
    // Get global values
    global $url;

    // Start transaction
    $transaction = new Transaction();
    $transaction->paymentID = $paymentID;

    // Check if payment was successfull
    $transaction->paymentCheck();

    // Generate image
    $pub = new Pub();
    $pub->pub = $transaction->globalValues()["pub_id"];

    // Get language
    $lang_code =  $pub->values()["payment_store_language"];

    if( empty($pub->values()["logo_fileID"]) ) {
      $imgUrl = $url . 'medias/logo/logo-fitted.png';
    }else {
      $imgUrl = MediaHub::getUrl( $pub->values()["logo_fileID"] );
    }

    // Generate info text
    $infoText = Language::string( 14, array(
      "%email%" => $transaction->globalValues()["email"],
      "%pubname%" => $pub->values()["name"],
      "%paymentid%" => $transaction->paymentID,
    ), "email", null, $lang_code );

    // Get payment type
    if( $transaction->globalValues()["payment_state"] != 1 && array_search( ($transaction->getGateway()->getInvoices()[0]["transactions"][0]["pspId"] ?? null), array(27, 15) ) === false ) {
      $paymentType = Language::string( 15, null, "email", null, $lang_code );
    }else {
      $paymentType = Language::string( 16, null, "email", null, $lang_code );
    }

    // Generate transaction infos
    $transactionInfos = '<table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">';
      $transactionInfos .= '<tr>';
        $transactionInfos .= '<td style="font-style: italic;">' . Language::string( 17, null, "email", null, $lang_code ) . ':</td>';
        $transactionInfos .= '<td style="text-align: right;">' . $transaction->paymentID . '</td>';
      $transactionInfos .= '</tr>';
      $transactionInfos .= '<tr>';
        $transactionInfos .= '<td style="font-style: italic;">' . Language::string( 18, null, "email", null, $lang_code ) . ':</td>';
        $transactionInfos .= '<td style="text-align: right;">' . date("d.m.Y H:i:s", strtotime($transaction->globalValues()["payment_time"])) . '</td>';
      $transactionInfos .= '</tr>';
      $transactionInfos .= '<tr>';
        $transactionInfos .= '<td style="font-style: italic;">' . Language::string( 19, null, "email", null, $lang_code ) . ':</td>';
        $transactionInfos .= '<td style="text-align: right;">' . $paymentType . '</td>';
      $transactionInfos .= '</tr>';
    $transactionInfos .= '</table>';



    // Generate products list
    $productList = '<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse">';

      foreach( $transaction->values() as $values ) {
        // Tip money
        if($values["product_id"] == 0) {
          $productList .= '<tr style="border-top: 1px solid #bdbfc7;">';
            $productList .= '<td style="padding: 5px 0px;">1x</td>';
            $productList .= '<td style="padding: 5px 0px;">' . Language::string( 20, null, "email", null, $lang_code ) . '</td>';
            $productList .= '<td style="padding: 5px 0px; text-align: right;">' . number_format(($values["price"] / 100), 2) . ' ' . $values["currency"] . '</td>';
          $productList .= '</tr>';

          // Add total
          $total = ($total ?? 0) + ( $values["price"] * $values["quantity"]);
        }else {
          $product = new Product();
          $product->product_id = $values["product_id"];

          $productList .= '<tr style="border-top: 1px solid #bdbfc7;">';
            $productList .= '<td style="padding: 5px 0px;">' . $values["quantity"] . 'x</td>';
            $productList .= '<td style="padding: 5px 0px;">' . $product->values()["name"] . '</td>';
            $productList .= '<td style="padding: 5px 0px; text-align: right;">' . number_format(($values["price"] / 100), 2) . ' ' . $values["currency"] . '</td>';
          $productList .= '</tr>';

          // Add total
          $total = ($total ?? 0) + ( $values["price"] * $values["quantity"]);
        }
      }

      // Add total row
      $productList .= '<tr style="border-top: 1px solid #bdbfc7;">';
        $productList .= '<td style="padding: 5px 0px; font-weight: bold;" colspan="2">' . Language::string( 21, null, "email", null, $lang_code ) . ':</td>';
        $productList .= '<td style="padding: 5px 0px; font-weight: bold; text-align: right;">' . number_format(($total / 100), 2) . ' ' . $values["currency"] . '</td>';
      $productList .= '</tr>';

    $productList .= '</table>';


    // Generate new html
    $return = '<!DOCTYPE html>';
    $return .= '<html lang="de" dir="ltr">';
      $return .= '<head>';
        $return .= '<meta charset="utf-8">';
        $return .= '<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />';
        $return .= '<title></title>';

        $return .= '<style>';
        $return .= '@media screen and (max-width: 700px) {';

          $return .= 'table[class="mail-container"] {';
            $return .= 'width: 100% !important;';
          $return .= '}';

          $return .= 'img[class="logo"] {';
            $return .= 'margin: 20px 0px !important;';
          $return .= '}';

        $return .= '}';
        $return .= '</style>';
      $return .= '</head>';

      $return .= '<body>';
        $return .= '<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->';
          $return .= '<tr>';
            $return .= '<td>';
              $return .= '<table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 40px 60px 40px 60px; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->';
                $return .= '<tr>';
                  $return .= '<td>';

                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; font-size: 15pt;font-weight: bolder;">';
                      $return .= '<tr>';
                        $return .= '<td>';
                          $return .= '<img src="' . $imgUrl . '" style="display: block; width: 100%;" class="logo" alt="TKTDATA TICKETSYSTEM">';
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';


                    $return .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0px;">';
                      $return .= '<tr>';
                        $return .= '<td style="margin: 20px 0px;">';
                          $return .= utf8Html( $infoText );
                        $return .= '</td>';
                      $return .= '</tr>';
                    $return .= '</table>';

                    $return .= '<hr style="color: #bdbfc7;"/>';

                    // Transaction informations
                    $return .= $transactionInfos;

                    // Product list
                    $return .= $productList;

                  $return .= '</td>';
                $return .= '</tr>';
              $return .= '</table>';
            $return .= '</td>';
          $return .= '</tr>';
        $return .= '</table>';
      $return .= '</body>';
    $return .= '</html>';

    // Return values
    return $return;
  }
}
 ?>
