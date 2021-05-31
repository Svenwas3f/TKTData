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
 * TKTDataMailer->tktdataMail ( $msg [HTML text message] )
 *
 * TKTDataMailer->tickerMail ( $ticketToken [Crypted token of a ticket] )
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
  function tktdataMail( $msg ) {
    //Require variables
    global $url;

    //HTML mail
    return '<!DOCTYPE html>
    <html lang="de" dir="ltr">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
        <title></title>

        <style>
          @media screen and (max-width: 700px) {

            table[class="mail-container"] {
              width: 100% !important;
            }

            img[class="logo"] {
              margin: 20px 10% !important;
            }

          }
        </style>

        <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
      </head>
      <body>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->
          <tr>
            <td>
              <table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 10px 10% 40px 10%; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->
                <tr>
                  <td>

                    <table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; color: #232b43; font-size: 15pt;font-weight: bolder; margin: 50px 0px;">
                      <tr>
                        <td>
                          <img src="' . $url . 'medias/logo/logo-fitted.png" style="display: block; width: 100%;" class="logo" alt="TKTDATA TICKETSYSTEM">
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
        </table>
      </body>
    </html>';
  }

  /**
  * Sends ticket mail (Add ticket as attachment if required)
  *
  * $ticketToken = Crypted token of a ticket
   */
  function ticketMail( $ticketToken ) {
    //Require variables
    global $url;

    //Start ticket
    $ticket = new Ticket();
    $ticket->ticketToken = $ticketToken;

    //Start group
    $group = new Group();
    $group->groupID = $ticket->cryptToken()["gid"];

    //Get header image
    if( isset( $group->values()["mail_banner_fileID"] ) &&! empty( $group->values()["mail_banner_fileID"] ) ) {
      $imgUrl = MediaHub::getUrl( $group->values()["mail_banner_fileID"] ); //Onw image
    }else {
      $imgUrl = $url . 'medias/logo/logo-fitted.png'; //No image found\Logo of tktdata
    }

    //Define message
    $msg = $group->values()["mail_msg"];

    //Replace ticket and email
    $msg = str_replace("%E-Mail%", $ticket->values()["email"], $msg); //Replace email with user email if required
    $msg = str_replace("%Ticket%", '<table width="100%" cellspacing="0" cellpadding="0"><tr><td><table cellspacing="0" cellpadding="0"><tr><td style="border-radius: 2px;" bgcolor="#232b43"><a href="' . $url . 'pdf/ticket/?ticketToken=' . urlencode($ticketToken) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">Dein Ticket</a></td></tr></table></td></tr></table>', $msg); //Replace ticket with ticket field

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


    //Return content
    return '<!DOCTYPE html>
    <html lang="de" dir="ltr">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
        <title></title>

        <style>
        @media screen and (max-width: 700px) {

          table[class="mail-container"] {
            width: 100% !important;
          }

          img[class="logo"] {
            margin: 20px 0px !important;
          }

        }
        </style>

        <body>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->
              <tr>
                <td>
                  <table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 10px 10% 40px 10%; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->
                    <tr>
                      <td>

                        <table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; color: #232b43; font-size: 15pt;font-weight: bolder; margin: 50px 0px;">
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
            </table>
        </body>
      </html>';
  }

  /**
  * Sends ticket mail (Add ticket as attachment if required)
  *
  * $ticketToken = Crypted token of a ticket
   */
  function paymentRequestMail( $ticketToken ) {
    //Require variables
    global $url;

    //Start ticket
    $ticket = new Ticket();
    $ticket->ticketToken = $ticketToken;

    //Start group
    $group = new Group();
    $group->groupID = $ticket->cryptToken()["gid"];

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
      $msg = str_replace("%Pay-Link%", '<table width="100%" cellspacing="0" cellpadding="0"><tr><td><table cellspacing="0" cellpadding="0"><tr><td style="border-radius: 2px;" bgcolor="#232b43"><a href="' . $url . 'store/pay/?ticketToken=' . urlencode($ticketToken) . '" target="_blank" style="padding: 8px 12px; border: 1px solid #232b43;border-radius: 2px;font-family: Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">Online Zahlen</a></td></tr></table></td></tr></table>', $msg); //Replace ticket with ticket field
    }
    //Return content
    return '<!DOCTYPE html>
    <html lang="de" dir="ltr">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
        <title></title>

        <style>
        @media screen and (max-width: 700px) {

          table[class="mail-container"] {
            width: 100% !important;
          }

          img[class="logo"] {
            margin: 20px 0px !important;
          }

        }
        </style>

        <body>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fafafa; height: 100%; padding: 40px;"> <!-- Container -->
              <tr>
                <td>
                  <table  width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f6; margin: 0px auto; color: black; padding: 10px 10% 40px 10%; font-family: \'Open Sans\', sans-serif; font-size: 14pt; overflow: hidden;" class="mail-container"> <!-- Content -->
                    <tr>
                      <td>

                        <table width="100%" cellspacing="0" cellpadding="0" style="text-align: center; background-color: #232b43; font-size: 15pt;font-weight: bolder;">
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
            </table>
        </body>
      </html>';
  }
}
 ?>
