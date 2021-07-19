<?php
/**
 * Define language settings
 * The filename is equal to the language code and can not be longer than 10 characters
 * it is recomended to use one of the ISO language codes http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
$language_code = "de";
$language_loc = "Deutsch";
$language_int = "German";

/**
 * Start language package
 *
 * The string convenction is an array following a structure what starts first with the page identification
 * and then a local identification for the page. This means that you can use a key on every page without
 * having troubles. It is recomended to use integres and write its useage as a comment behind the text if
 * required.
 */

/**
 * Menu
 */
$string["menu"][1] = 'Ticket';
$string["menu"][2] = 'Coupons';
$string["menu"][3] = 'Scanner';
$string["menu"][4] = 'Live';
$string["menu"][5] = 'Wirtschaften';
$string["menu"][6] = 'Benutzer';
$string["menu"][7] = 'Alle Tickets';
$string["menu"][8] = 'Gruppen';
$string["menu"][9] = 'Alle Coupons';
$string["menu"][10] = 'Informationen';
$string["menu"][11] = 'QR-Scanner';
$string["menu"][12] = 'Code-Scanner';
$string["menu"][13] = 'Live';
$string["menu"][14] = 'Archiv';
$string["menu"][15] = 'Manuell';
$string["menu"][16] = 'Übersicht';
$string["menu"][17] = 'Produkte';
$string["menu"][18] = 'Einstellungen';
$string["menu"][19] = 'Alle Benutzer';
$string["menu"][20] = 'Aktivitäten';
$string["menu"]["profile"] = 'Profil';

$string["menu"]["mainpage"] = 'Menu #%mainpage% [%mainpagename%]';
$string["menu"]["subpage"] = 'Submenu #%submenu% [%submenuname%] von Menu #%mainmenu%';


/**
 * Page 16
 */
$string[1][1] = "Hello World";

/**
 * Page 19
 */
// List view
$string[19][0] = 'Benutzername, Vorname, Nachname, Ticketinfo';
$string[19][1] = 'Benutzername';
$string[19][2] = 'E-Mail';
$string[19][3] = 'Aktion';

// Sinlgle view
$string[19][10] = 'Benutzerdaten';
$string[19][11] = 'Benutzername';
$string[19][12] = 'Name';
$string[19][13] = 'E-Mail';

$string[19][14] = 'Zugriffsrechte';
$string[19][15] = 'Schreibberechtigung';
$string[19][16] = 'Leseberechtigung';
$string[19][17] = 'Schreibberechtigung setzen';
$string[19][18] = 'Leseberechtigung seetzen';

$string[19][19] = 'UPDATE';
$string[19][20] = 'Benutzer aktualisieren';

/**
 * Page 20
 */
// Page
$string[20][0] = 'Benutzer'; // Search form placeholder
$string[20][1] = 'Initiator'; // Headlines
$string[20][2] = 'Tätigkeit'; // Headlines
$string[20][3] = 'Datum'; // Headlines
$string[20][4] = 'Wiederherstellungsdetails'; // Headlines
$string[20][5] = 'Revisionsdetails #%id%'; // Action title
$string[20][6] = 'Letze'; // Table navigation
$string[20][7] = 'Weiter'; // Table navigation
$string[20][8] = 'Vorherige Version';  // Action response
$string[20][9] = 'Geänderte Version'; // Single action view
$string[20][10] = 'Änderungen zurücksetzen'; // Single action view
$string[20][11] = 'Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.'; // Action response
$string[20][12] = 'Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden'; // Action response
$string[20][13] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Action response

// Messages
$string[20][100] = 'Profil von %user% überarbeitet';
$string[20][101] = 'Profil von %user% entfernt';
$string[20][102] = 'Zugriffsrechte von %user% entfernt';
$string[20][103] = 'Profil von %user% hinzugefügt';
$string[20][103] = 'Version %version% wiederhergestellt';

/**
 * Profil
 */
// Update password
$string["profile"][0] = 'Das Passwort wurde <strong>erfolgreich</strong> geändert.';
$string["profile"][1] = 'Das Passwort konnte <strong>nicht</strong> geändert werden';

// Update infos
$string["profile"][2] = 'Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.';
$string["profile"][3] = 'Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden';

// Inputs
$string["profile"][10] = 'Benutzerdaten';
$string["profile"][11] = 'Benutzername';
$string["profile"][12] = 'Name';
$string["profile"][13] = 'E-Mail';
$string["profile"][14] = 'Sprache wählen';

$string["profile"][15] = 'Passwort ändern';
$string["profile"][16] = 'Neues Passwort';
$string["profile"][17] = 'Passwort bestätigen';

// Confirm form
$string["profile"][18] = 'UPDATE';


 ?>
