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
* General
*/
$string["general"][0] = 'TKTDATA - EVENTVERWALTUNG'; // Page title
$string["general"][1] = 'TKTDATA - Ihr Eventverwaltungssystem gemacht mit liebe in der Schweiz.'; // Page description
$string["general"][2] = 'TKTDATA, Ticketverwaltung, Eventverwaltung, Events'; // Keywords

$string["general"][3] = 'Bitte aktiviere JavaScript um diese Webseite zu nutzen'; // JavaScript information
$string["general"][4] = 'Lade Seite...'; // Building page...
$string["general"][5] = 'Die Seite <strong>#%page%</strong> existiert nicht.'; // Fullscreen info (page does not exist)
$string["general"][6] = 'Zurück'; // Fullscreen info (page does not exist)
$string["general"][7] = 'Zugriff auf die Seite <strong>#%page%</strong> verweigert.'; // Fullscreen info (page access denied)
$string["general"][8] = 'Zurück'; // Fullscreen info (page access denied)

/**
 * Footer
 */
$string["footer"][0] = '&copy; ' . date("Y") . ' bei <span>TKTDATA</span>';

/**
 * Errors
 */
$string["error"][0] = 'Kein Zugriff auf das System';
$string["error"][1] = 'Sie haben noch keine Berechtigungen um auf dieses System zuzugreifen. Bitte melden Sie sich bei dem Administrator';
$string["error"][2] = 'Zugriff auf ungültiges Ticket';
$string["error"][3] = 'Sie haben versucht ein ungültiges Ticket abzurufen';
$string["error"][4] = 'Datenbankverbindung fehlgeschlagen';
$string["error"][5] = 'Es konnte keine Verbindung zur Datenbank aufgebaut werden.';
$string["error"][6] = 'Keine Wirtschaft angegeben';
$string["error"][7] = 'Für die Getränke und Speisekarte benötigt es eine Wirtschaft.';
$string["error"][8] = '404 - Seite nicht gefunden';
$string["error"][9] = 'Fehler während der Anfrage';
$string["error"][10] = 'Unbekannter Fehler';
$string["error"][11] = 'Unbekannter Fehler. Melden Sie sich bei wiederholtem Auftreten beim Administrator';

/**
* Login / Auth
*/
$string["auth"][0] = 'Benutzername'; // Input name
$string["auth"][1] = 'Passwort'; // Input name
$string["auth"][2] = 'LOGIN'; // Input name
$string["auth"][3] = 'Anmelden'; // Input title
$string["auth"][4] = 'Passwort vergessen'; // Link name
$string["auth"][5] = 'Passwort zurücksetzen'; // Link title
$string["auth"][6] = 'Benutzername / E-Mail'; // Input name
$string["auth"][7] = 'Zurücksetzen'; // Input name
$string["auth"][8] = 'Aktuelles Passwort zurücksetzen'; // Input title
$string["auth"][9] = 'Zum Login'; // Link title
$string["auth"][10] = 'Anmelden'; // Link name

/**
 * Actions
 */
$string["action"][0] = 'Abbrechen';
$string["action"][1] = 'Bestätigen';

/**
 * PDF
 */
$string["pdf"][0] = 'TICKET'; // Ticket title
$string["pdf"][1] = 'Ticket bereitgestellt von <span>TKTDATA</span>'; // Ticket footer
$string["pdf"][2] = 'MENUKARTE'; // Menu title
$string["pdf"][3] = 'Menüliste bereitgestellt von <span>TKTDATA</span> '; // Menu footer

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
$string["menu"]["logout"] = 'Abmelden';

/**
 * Media hub
 */
$string["mediahub"][0] = 'Übersicht';
$string["mediahub"][1] = 'Bild hinzufügen';
$string["mediahub"][2] = 'Alt:';
$string["mediahub"][3] = 'Benutzer:';
$string["mediahub"][4] = 'Hochgeladen:';
$string["mediahub"][5] = 'Entfernen';
$string["mediahub"][6] = 'Vollbild';
$string["mediahub"][7] = 'VERWENDEN';
$string["mediahub"][8] = 'Dokument hineinziehen oder klicken';
$string["mediahub"][9] = 'Hochladen ...';
$string["mediahub"][10] = 'Weitere laden';
$string["mediahub"][11] = 'Das überarbeiten des Alt-Text ist fehlgeschlagen';
$string["mediahub"][12] = 'Sicher, dass Sie das Dokument löschen wollen?';
$string["mediahub"][13] = 'Das entfernen des Dokuments ist fehlgeschlagen';
$string["mediahub"][14] = '(Fehler beim hochladen)';

// $string[17][40] = '';
// $string[17][41] = '';
// $string[17][42] = '';
// $string[17][43] = '';
// $string[17][44] = '';
// $string[17][45] = '';
// $string[17][46] = '';
// $string[17][47] = '';
// $string[17][48] = '';
// $string[17][49] = '';

/**
 * Page 7
 */
// List
$string[7][0] = 'Benutzername, Vorname, Nachname, Ticketinfo'; // Search form placeholder

$string[7][1] = 'Entwertet ohne Zahlung'; // Ticket states
$string[7][2] = 'Blockiert & bezahlt'; // Ticket states
$string[7][3] = 'Zahlung erwartet'; // Ticket states
$string[7][4] = 'Entwertet'; // Ticket states
$string[7][5] = 'Blockiert'; // Ticket states

$string[7][6] = 'E-Mail'; // Headline
$string[7][7] = 'Kaufdatum'; // Headline
$string[7][8] = 'Aktion'; // Headline

$string[7][9] = 'Ticketdetails anzeigen'; // Action
$string[7][10] = 'PDF öffnen'; // Action
$string[7][11] = 'Wiederherstellen'; // Action
$string[7][12] = 'Löschen'; // Action
$string[7][13] = 'Name: %name%&#013;ID: %id%'; // Action

$string[7][14] = 'Zurück'; // nav
$string[7][15] = 'Weiter'; // nav

// View
$string[7][20] = 'Name: %name%&#013;ID: %id%';
$string[7][21] = 'Zur vorherigen Seite zurück';

$string[7][22] = 'Ticket entwertet am %date%, Zahlung nicht getätigt.'; // Top bar text
$string[7][23] = 'Blockiertes Ticket, bereits bezahlt.'; // Top bar text
$string[7][24] = 'Zahlung nicht getätigt.'; // Top bar text
$string[7][25] = 'Ticket entwertet am %date%'; // Top bar text
$string[7][26] = 'Ticket blockiert.'; // Top bar text

$string[7][27] = 'PDF'; // Img alt (right menu)
$string[7][28] = 'PDF öffnen'; // Link title (right menu)
$string[7][29] = 'Mail'; // Img alt (right menu)
$string[7][30] = 'Ticket per Mail senden'; // Link title (right menu)
$string[7][31] = 'Mail'; // Img alt (right menu)
$string[7][32] = 'Zahlung anfordern'; // Link title (right menu)
$string[7][33] = 'Erstatten'; // Img alt (right menu)
$string[7][34] = 'Zahlung rückerstatten'; // Link title (right menu)
$string[7][35] = 'Reaktivieren'; // Img alt (right menu)
$string[7][36] = 'Ticket reaktivieren'; // Link title (right menu)
$string[7][37] = 'Entwerten'; // Img alt (right menu)
$string[7][38] = 'Ticket entwerten'; // Link title (right menu)
$string[7][39] = 'Wiederherstellen'; // Img alt (right menu)
$string[7][40] = 'Ticket wiederherstellen'; // Link title (right menu)
$string[7][41] = 'Entfernen'; // Img alt (right menu)
$string[7][42] = 'Ticket entfernen'; // Link title (right menu)

$string[7][43] = 'E-Mail'; // Input name
$string[7][44] = 'Karte'; // Options select
$string[7][45] = 'Rechnung'; // Options select
$string[7][46] = 'Zahlung nicht eingegangen'; // Options select
$string[7][47] = 'Zahlungsmethode'; // Headline select
$string[7][48] = 'Betrag'; // Input name
$string[7][49] = 'Kein Coupon verwendet'; // Coupon
$string[7][50] = '&#9432; Zahlung getätig um %date%'; // Payment time
$string[7][51] = 'Update';

// Add
$string[7][60] = 'Gruppe auswählen';
$string[7][61] = 'Verfügbare Tickets: %availableTickets%/%maxTickets%&#013;Tickets pro Benutzer: %tpu%&#013;Preis: %price% %currency% + %vat%% MwST.&#013;';
$string[7][62] = 'Ticket an Käufer senden';
$string[7][63] = 'Hinzufügen';

// Actions
$string[7][70] = 'Das Ticket konnte <strong>erfolgreich</strong> entwertet werden.'; // Employ message
$string[7][71] = 'Leider konnte das Ticket <strong>nicht</strong> entwertet werden.'; // Employ message
$string[7][72] = 'Das Ticket konnte <strong>erfolgreich</strong> reaktiviert werden.'; // Reactivate message
$string[7][73] = 'Leider konnte das Ticket <strong>nicht</strong> reaktiviert werden.'; // Reactivate message
$string[7][74] = 'Die Mail konnte <strong>erfolgreich</strong> gesendet werden.'; // Email message
$string[7][75] = 'Leider konnte Die Mail <strong>nicht</strong> gesendet werden.'; // Email message
$string[7][76] = 'Die Mail konnte <strong>erfolgreich</strong> gesendet werden.'; // Payment request message
$string[7][77] = 'Leider konnte Die Mail <strong>nicht</strong> gesendet werden.'; // Payment request message
$string[7][78] = 'Möchten Sie die Zahlung für das Ticket %ticketToken% wirklich zurückerstatten?'; // Refund message
$string[7][79] = 'Das Geld wurde erfolgreich rückerstattet.'; // Refund message
$string[7][80] = 'Beim Rückerstatten ist ein Fehler aufgetreten: <br /> %message%'; // Refund message
$string[7][81] = 'Das Ticket konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update ticket
$string[7][82] = 'Leider konnte das Ticket <strong>nicht</strong> überarbeitet werden.'; // Update ticket
$string[7][83] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update ticket

$string[7][84] = 'Coupon konnte nicht angewendet werden.'; // Add messages
$string[7][85] = 'Die Mail konnte nicht versendet werden.'; // Add messages
$string[7][86] = 'Das Zeitfenster um ein Ticket zu lösen ist <strong>nicht</strong> offen. Konsultiere die Gruppe für nähere Informationen.'; // Add messages
$string[7][87] = 'Die maximale Anzahl an Tickets wurde erreicht.'; // Add messages
$string[7][88] = 'Die maximale Anzahl an Tickets pro Benutzer wurde erreicht.'; // Add messages
$string[7][89] = 'Das Ticket konnte <strong>erfolgreich</strong> erstellt werden. <strong><a href="%url_page%&view=%ticketToken%" class="redirect">Ticket ansehen</a></strong>'; // Add messages
$string[7][90] = 'Leider konnte das Ticket <strong>nicht</strong> erstellt werden'; // Add message
$string[7][91] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Add message

$string[7][92] = 'Das Ticket konnte <strong>erfolgreich</strong> blockiert werden.'; // Block message
$string[7][93] = 'Leider konnte das Ticket <strong>nicht</strong> blockiert werden.'; // Block message
$string[7][94] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Block message

$string[7][95] = 'Das Ticket konnte <strong>erfolgreich</strong> aktiviert werden.'; // Activate message
$string[7][96] = 'Leider konnte das Ticket <strong>nicht</strong> aktiviert werden.'; // Activate message
$string[7][97] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Activate message

/**
 * Page 9
 */
// List
$string[9][0] = 'Name, Coupon-ID, Gruppen-ID';
$string[9][1] = 'Name';
$string[9][2] = 'Verwendung';
$string[9][3] = 'Discount';
$string[9][4] = 'Aktion';
$string[9][5] = 'Name: %name%&#013;ID: %id%';
$string[9][6] = 'Coupondetails anzeigen';
$string[9][7] = 'Coupon löschen';
$string[9][8] = 'Weiter';
$string[9][9] = 'Zurück';

// Single
$string[9][10] = 'Name: %name%&#013;ID: %id%';
$string[9][11] = 'Zur vorherigen Seite zurück';
$string[9][12] = 'Name';
$string[9][13] = 'Discount';
$string[9][14] = 'Benützt';
$string[9][15] = 'Verfügbare Benützung';
$string[9][16] = '<abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Leerlassen um Gruppendaten zu verwenden">Startdatum</abbr>';
$string[9][17] = '<abbr title="Format: YYYY-MM-DD HH::ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Leerlassen um Gruppendaten zu verwenden">Enddatum</abbr>';
$string[9][18] = 'Update';

// Remove
$string[9][20] = 'Möchtest du den Coupon <strong>#%id%</strong> mit dem Namen <strong>%name%</strong> wirklich löschen?';
$string[9][21] = 'Der Coupon konnte <strong>erfolgreich</strong> entfernt werden';
$string[9][22] = 'Der Coupon konnte <strong>nicht</strong> entfernt werden';
$string[9][23] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

// Add
$string[9][30] = 'Gruppe auswählen';
$string[9][31] = 'Absolut';
$string[9][32] = 'Hinzufügen';
$string[9][33] = 'Verfügbare Tickets: %availableTickets%/%maxTickets%&#013;Tickets pro Benutzer: %tpu%&#013;Preis: %price% %currency% + %vat%% MwST.&#013;';

$string[9][34] = 'Der Name und die Gruppe werden benötigt, um einen Coupon hinzuzufügen';
$string[9][35] = 'Dieser Coupon <strong>existiert bereits</strong>';
$string[9][36] = 'Der Coupon wurde <strong>nicht</strong> hinzugefügt';
$string[9][37] = 'Der Coupon wurde <strong>erfolgreich</strong> hinzugefügt';
$string[9][38] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

// Update
$string[9][40] = 'Der Coupon konnte <strong>erfolgreich</strong> überarbeitet werden';
$string[9][41] = 'Der Coupon konnte <strong>nicht</strong> überarbeitet werden';
$string[9][42] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

/**
 * Page 10
 */
$string[10][0] = 'Doppelklick in Textfeld um zu bearbeiten. Die Änderungen werden automatisch gespeichert.'; // Admin top bar
$string[10][1] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update text no access

/**
 * Page 11
 */
$string[11][0] = '🎥 Zugriff auf Videostream nicht möglich (bitte stellen Sie sicher, dass Ihre Webcam aktiviert ist)'; // Webcam message
$string[11][1] = '⌛ Video wird geladen...'; // Loading message

$string[11][2] = 'Dieses Ticket existiert nicht. Bitte melden Sie sich beim Personal'; // Ticket does not exist
$string[11][3] = 'Dieses Ticket wurde noch nicht bezahlt. Bitte melden Sie sich beim Personal'; // Not payed
$string[11][4] = 'Herzlich Willkommen'; // Ticket activated successfuly
$string[11][5] = 'Beim einlösen des Tickets ist ein Fehler aufgetreten. Bitte melden Sie sich beim Personal'; // Error while activating
$string[11][6] = 'Dieses Ticket wurde bereits verwendet. Bitte melden Sie sich beim Personal'; // Ticket already activated
$string[11][7] = 'Dieses Ticket wurde blockiert. Bitte melden Sie sich beim Personal'; // Ticket blocked
$string[11][8] = 'Ein unbekannter Fehler ist aufgetreten. Bitte melden Sie sich beim Personal'; // Unknown error
$string[11][9] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access
$string[11][10] = 'Verstanden'; // Button Text

// Ticket not found
$string["scanner"][0] = 'TKTDATA'; // Image title
$string["scanner"][1] = 'Das angeforderte Ticket existiert nicht!'; // Error message
$string["scanner"][2] = 'Abbrechen'; // Button

// Ticket infos
$string["scanner"][3] = 'Ticket benützt um %date%, Zahlung nicht getätigt.'; // Payment and ticket state
$string["scanner"][4] = 'Blockiertes Ticket, bereits bezahlt.'; // Payment and ticket state
$string["scanner"][5] = 'Zahlung nicht getätigt.'; // Payment and ticket state
$string["scanner"][6] = 'Ticket entwertet am %date%.'; // Payment and ticket state
$string["scanner"][7] = 'Ticket blockiert.'; // Payment and ticket state
$string["scanner"][8] = 'TKTDATA'; // Title
$string["scanner"][9] = 'E-Mail:'; // Name
$string["scanner"][10] = 'Einlösen'; // Button
$string["scanner"][11] = 'Abbrechen'; // Button

$string["scanner"][12] = 'Das Ticket wurde <strong>erfolgreich entwertet</strong>'; // Message
$string["scanner"][13] = 'Das Ticket wurde <strong>nicht entwertet</strong>'; // Message
$string["scanner"][14] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Message

/**
 * Page 12
 */
$string[12][0] = 'TicketToken';

/**
 * Page 13
 */
$string[13][0] = 'Archivieren'; // Export button
$string[13][1] = '# Besucher'; // label
$string[13][2] = 'Verlauf'; // Diagramm title
$string[13][3] = 'Eintritte'; // Diagramm title
$string[13][4] = 'Austritte'; // Diagramm title
$string[13][5] = 'Aktuelle Besucher'; // Title
$string[13][6] = 'Aktueller Trend'; // Title
$string[13][7] = 'Möchten Sie den aktuellen Stand tatsächlich archivieren?';
$string[13][8] = 'Ihre Daten wurden erfolgreich archiviert';
$string[13][9] = 'Es ist ein Fehler beim archivieren Ihrer Daten aufgetreten';
$string[13][10] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

/**
 * Page 14
 */
$string[14][0] = 'Archiv'; // Select headline
$string[14][1] = 'Exportieren'; // Export button
$string[14][2] = '# Besucher'; // label
$string[14][3] = 'Verlauf'; // Diagramm title
$string[14][4] = 'Eintritte'; // Diagramm title
$string[14][5] = 'Austritte'; // Diagramm title

$string[14][6] = 'Sek.'; // Diagramm labels
$string[14][7] = 'Min.'; // Diagramm labels
$string[14][8] = 'Std.'; // Diagramm labels
$string[14][9] = 'Tage'; // Diagramm labels
$string[14][10] = 'Woche'; // Diagramm labels
$string[14][11] = 'Mo.'; // Diagramm labels
$string[14][12] = 'Jahr'; // Diagramm labels
$string[14][13] = 'Jz.'; // Diagramm labels

/**
 * Page 15
 */
$string[15][0] = 'Besucher'; // Visitors
$string[15][1] = 'Es konnte nicht hochgezählt werden.'; // Up error message
$string[15][2] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Up error message
$string[15][3] = 'Es konnte nicht heruntergezählt werden.'; // Down error message
$string[15][4] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Down error message


/**
 * Page 16
 */
// View
$string[16][0] = 'PickUp'; // Img alt
$string[16][1] = 'Transaktion abholen?'; // Img title
$string[16][2] = 'state'; // Img alt
$string[16][3] = 'Zahlungseingang bestätigen'; // Img title
$string[16][4] = 'Refund'; // Img alt
$string[16][5] = 'Betrag zurückerstatten'; // Img tittle
$string[16][6] = 'Erstatten'; // Button info text
$string[16][7] = 'Trash'; // Img alt
$string[16][8] = 'Transaktion entfernen'; // Img title
$string[16][9] = 'Zur vorherigen Seite zurück'; // Top nav title
$string[16][10] = 'Transaktion'; // Details headline
$string[16][11] = 'E-Mail:'; // Details
$string[16][12] = 'Zahlungs-ID:'; // Details
$string[16][13] = 'Betrag:'; // Details
$string[16][14] = 'Effektiv:'; // Details
$string[16][15] = 'Rückerstattet:'; // Details
$string[16][16] = 'Gebühren:'; // Details
$string[16][17] = 'Status:'; // Details
$string[16][18] = 'Zahlung erwartet, Abgeholt'; // Pickup state
$string[16][19] = 'Zahlung erwartet'; // Pickup state
$string[16][20] = 'Nicht abgeholt'; // Pickup state
$string[16][21] = 'Abgeholt'; // Pickup state
$string[16][22] = 'Zahlungstyp:'; // Detail
$string[16][23] = 'Onlinezahlung'; // Payment type
$string[16][24] = 'Barzahlung'; // Payment type
$string[16][25] = 'Zahlungszeit'; // Detail

$string[16][26] = 'Produkte'; // Products headline
$string[16][27] = 'Trinkgeld'; // Tip money info
$string[16][28] = 'Name unbekannt'; // Name of product not found
$string[16][29] = 'Total:'; // Total info

// List
$string[16][30] = 'Email, Zahlungs-ID, Zahlungszeit'; // Search form placeholder
$string[16][31] = 'Ohne Zahlung abgeholt'; // Pickup states
$string[16][32] = 'Zahlung erwartet'; // Pickup states
$string[16][33] = 'Abholung erwartet'; // Pickup states
$string[16][34] = 'Email'; // Headline
$string[16][35] = 'Preis'; // Headline
$string[16][36] = 'Datum'; // Headline
$string[16][37] = 'Aktion'; // Headline
$string[16][38] = 'Zahlung erwartet. Produkte bereits abgeholt.'; // Pickup states title
$string[16][39] = 'Zahlung erwartet.'; // Pickup states title
$string[16][40] = 'Abholung erwartet'; // Pickup states title
$string[16][41] = 'Abgeholt'; // Pickup states title
$string[16][42] = 'Transaktion anzeigen'; // Link title
$string[16][43] = 'Transaktion entfernen'; // Link title
$string[16][44] = 'Zurück'; // Footer nav
$string[16][45] = 'Weiter'; // Footer nav

$string[16][46] = 'Die Transaktion <strong>%email% (#%id%)</strong> wurde <strong>erfolgreich</strong> gelöscht.'; // Remove message success
$string[16][47] = 'Die Transaktion <strong>%email% (#%id%)</strong> konnte <strong>nicht</strong> gelöscht werden.'; // Remove message fail
$string[16][48] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access

// Remove
$string[16][50] = 'Möchtest du die Transaktion <strong>%email% (#%id%)</strong>  wirklich löschen?';

// No access to pub
$string[16][60] = 'Du hast keinen Zugriff auf die Wirtschaft (#%id%) <strong>%name%</strong>'; // Fullscreen message
$string[16][61] = 'Zurück'; // Fullscreen return button

// Ajax messages
$string[16][70] = 'Rückerstattung fehlgeschlagen.';
$string[16][71] = 'Rückerstattung fehlgeschlagen. %refund%';
$string[16][72] = 'Dieser Benutzer hat keine Berechtigung zu dieser Aktion';
$string[16][73] = 'Erfolgreich -%refund% %currency% erstattet.';
$string[16][74] = 'Zahlungseingang konnte nicht bestätigt werden';

/**
 * Page 17
 */
// Default
$string[17][0] = 'Onlinezahlungen sind leider nicht ganz gratis, weshalb vom Verkauspreis jeweils <strong>%fee_absolute% %currency%</strong> und <strong>%fee_percent%%</strong> abgegeben werden muss.'; // Fees info
$string[17][1] = 'PRODUKTE'; // Top nav item
$string[17][2] = 'Produkte ansehen'; // Top nav item title
$string[17][3] = 'EINSTELLUNGEN'; // Top nav item
$string[17][4] = 'Wirtschaftseinstellungen vornehmen'; // Top nav item title

$string[17][5] = 'PDF'; // Right menu alt
$string[17][6] = 'Speise und Getränkekarte als PDF ansehen'; // Right menu title
$string[17][7] = 'Visibility'; // Right menu alt
$string[17][8] = 'Trinkgeld anzeigen/verbergen';

$string[17][9] = 'Details'; // Form header
$string[17][10] = 'Beschreibung'; // Input name
$string[17][11] = 'Bilder'; // Form header
$string[17][12] = 'Logo'; // Input name
$string[17][13] = 'Klicken um auszuwählen'; // Select info
$string[17][14] = 'Hintergrundbild'; // Input name
$string[17][15] = 'Klicken um auszuwählen'; // Select info
$string[17][16] = 'Update'; // Update

$string[17][17] = 'Produktname, Preis'; // Search form placeholder
$string[17][18] = 'Verfügbar'; // Availability types
$string[17][19] = 'Wenige verfügbar'; // Availability types
$string[17][20] = 'Ausverkauft'; // Availability types

$string[17][21] = 'Name'; // headlines
$string[17][22] = 'Preis'; // headlines
$string[17][23] = 'Aktion'; // headlines
$string[17][24] = 'Produktdetails anzeigen'; // Top nav title
$string[17][25] = 'Produkt entfernen'; // Top nav title
$string[17][26] = 'Ein globales Produkt kann hier nicht bearbeitet werden'; // Product list info
$string[17][27] = 'Dieses Produkt erscheint nicht in der Speise und Getränkekarte'; // Product list info
$string[17][28] = 'Zurück'; // Table navigation
$string[17][29] = 'Weiter'; // Table navigation

// view
$string[17][30] = 'Visibility'; // Right menu alt
$string[17][31] = 'Sichtbarkeit wechseln'; // Right menu title
$string[17][32] = 'state'; //Right menu  alt
$string[17][33] = 'Produktstatus bestimmen'; //Right menu title
$string[17][34] = 'RMF'; // Remove alt
$string[17][35] = 'Produkt entfernen'; // Title action remove
$string[17][36] = 'Produkt bearbeiten'; // Title action edit
$string[17][37] = 'Produkt ansehen'; // Title action view

$string[17][38] = 'Produktname'; // Input name
$string[17][39] = 'Gruppe wählen'; // Input name
$string[17][40] = 'GO'; // Input name
$string[17][41] = 'Preis'; // Input name
$string[17][42] = 'Produktbild'; // Input name
$string[17][43] = 'Klicken um auszuwählen'; // Image headline
$string[17][44] = 'Update';
$string[17][45] = '&#9888; Dies ist ein globales Produkt und kann nur vom Administrator bearbeitet werden.'; // Global message info
$string[17][46] = 'Zur vorherigen Seite zurück'; // Return button
$string[17][47] = 'Du hast keinen Zugriff auf das Produkt (#%product%) %name%';

// Add
$string[17][48] = 'Produkt hinzufügen'; // Input name
$string[17][49] = 'Erstellen'; // Input name

// Remove
$string[17][50] = 'Möchtest du das Produkt <strong>%name% (#%product%)</strong>  wirklich löschen?'; // Message

// Actions
$string[17][60] = 'Du hast keinen Zugriff auf die Wirtschaft (#%pub%) <strong>%name%</strong>'; // No access to pub message
$string[17][61] = 'Zurück'; // No access to pub, return button
$string[17][62] = 'Das Produkt konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href="%url_page%&pub=%pub%&view_product=%product%" class="redirect">Produkt verwalten</a></strong>'; // Successfully added product
$string[17][63] = 'Leider konnte das Produkt <strong>nicht</strong> erstellt werden.'; // Error while adding product
$string[17][64] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access
$string[17][65] = 'Das Produkt <strong>%name% (#%product%)</strong> wurde <strong>erfolgreich</strong> überarbeitet.'; // Update of product successfull
$string[17][66] = 'Das Produkt <strong>%name% (#%product%)</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update of product failed
$string[17][67] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access
$string[17][68] = 'Das Produkt <strong>%name% (#%product%)</strong> wurde <strong>erfolgreich</strong> gelöscht.'; // Removed product successful
$string[17][69] = '"Das Produkt <strong>%name% (#%product%)</strong> konnte <strong>nicht</strong> gelöscht werden."'; // Removed product failed
$string[17][70] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access
$string[17][71] = 'Die Wirtschaft <strong>%name% (#%pub%)</strong> wurde <strong>erfolgreich</strong> überarbeitet.';
$string[17][72] = 'Die Wirtschaft <strong>%name% (#%pub%)</strong> konnte <strong>nicht</strong> überarbeitet werden.';
$string[17][73] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

/**
 * Page 18
 */
// List pubs
$string[18][0] = 'Name, ID'; // Search form placeholder
$string[18][1] = 'Name'; // Headlines
$string[18][2] = 'Aktion'; // Headlines
$string[18][3] = 'Wirtschaftdetails anzeigen'; // Action title
$string[18][4] = 'Wirtschaft entfernen'; // Action title
$string[18][5] = 'Zurück'; // Table navigation
$string[18][6] = 'Weiter'; // Table navigation

// List products
$string[18][10] = 'Name, Preis'; // Search form placeholder
$string[18][11] = 'Name'; // Headlines
$string[18][12] = 'Preis'; // Headlines
$string[18][13] = 'Aktion'; // Headlines
$string[18][14] = 'Produktdetails anzeigen'; // Action title
$string[18][15] = 'Produkt entfernen'; // Action title
$string[18][16] = 'Zurück'; // Table navigation
$string[18][17] = 'Weiter'; // Table navigation

// Single pub
$string[18][20] = 'Allgemein'; // Top nav item
$string[18][21] = 'Rechte'; // Top nav item
$string[18][22] = 'Wirtschaft verwalten'; // Top nav item title
$string[18][23] = 'Rechte verwalten'; // Top nav item title

$string[18][24] = 'PDF'; // Right menu alt pdf
$string[18][25] = 'Speise und Getränkekarte als PDF ansehen'; // Right menu title pdf
$string[18][26] = 'Sichtbarkeit'; // Right menu alt tip money
$string[18][27] = 'Trinkgeld anzeigen/verbergen'; // Right menu title tip money

$string[18][28] = 'Generell'; // generally title
$string[18][29] = 'Wirtschaftsname'; // Input name
$string[18][30] = 'Beschreibung'; // Input name
$string[18][31] = 'Bilder'; // images title
$string[18][32] = 'Klicken um auszuwählen'; // Image input select info
$string[18][33] = 'Logo'; // Image name
$string[18][34] = 'Hintergrundbild'; // Image name
$string[18][35] = 'Payrexx'; // Payrexx title
$string[18][36] = 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.'; // Payrexx info
$string[18][37] = 'Payrexx Instance'; // Input name
$string[18][38] = 'Payrexx Secret'; // Input name
$string[18][39] = 'Währung'; // Input name
$string[18][40] = 'Gebühren'; // Fees title
$string[18][41] = 'Pro Transaktion verlangt der Anbieter entsprechende Gebühren. Bitte definiere hier, welche Gebüren dein Zahlungsanbieter verlang um die Auswertung korrekt zu erhalten. Die beiden Gebühren werden zusammengezählt und entsprechend verrechnet. An den Produktpreisen ändert sich dadurch nichts.'; // Fees info
$string[18][42] = 'Absolute Gebühren'; // Input name
$string[18][43] = 'Prozentuale Gebühren'; // Input name
$string[18][44] = 'Update'; // Button value

$string[18][45] = 'Benutzername'; // Input name
$string[18][46] = 'Email'; // Input name
$string[18][47] = 'Schreiben | Lesen'; // Input name
$string[18][48] = '%user% hat Schreibrechte auf diese Wirtschaft'; // Toggle title
$string[18][49] = '%user% hat keine Schreibrechte auf diese Wirtschaft'; // Toggle title
$string[18][50] = '%user% hat Leserechte auf diese Wirtschaft'; // Toggle title
$string[18][51] = '%user% hat keine Leserechte auf diese Wirtschaft'; // Toggle title
$string[18][52] = 'Die Rechte konnten nicht hinzugefügt werden.'; // Fail info
$string[18][53] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Fail info

//Single product
$string[18][60] = 'Zur vorherigen Seite zurück'; // Top nav title
$string[18][61] = 'Produkt bearbeiten'; // Headline
$string[18][62] = 'Produkt ansehen'; // Headline
$string[18][63] = 'Produktname'; // Input name
$string[18][64] = 'GO'; // Input select button
$string[18][65] = 'Preis'; // Input name
$string[18][66] = 'Es wird jeweils die Standartwährung verwendet, sofern bei einer Wirtschaft keine andere Währung angegeben wird.'; // Abbr info price
$string[18][67] = 'Produktbild'; // Input name
$string[18][68] = 'Klicken um auszuwählen'; // Input name
$string[18][69] = 'Update'; // Input name

// Actions
$string[18][70] = 'Möchtest du die Wirtschaft <strong>%name% (#%id%)</strong> wirklich löschen?'; // Remove pub message
$string[18][71] = 'Möchtest du das Produkt <strong>%name% (#%id%)</strong>  wirklich löschen?'; // Remove product message

$string[18][72] = 'Die Wirtschaft <strong>%name% (#%id%)</strong> wurde <strong>erfolgreich</strong> überarbeitet.'; // Update pub success
$string[18][73] = 'Die Wirtschaft <strong>%name% (#%id%)</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update pub fail
$string[18][74] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update pub no access

$string[18][75] = 'Das Produkt <strong>%name% (#%id%)</strong> wurde <strong>erfolgreich</strong> überarbeitet.'; // Update product success
$string[18][76] = 'Das Produkt <strong>%name% (#%id%)</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update product fail
$string[18][77] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update product no access

$string[18][78] = 'Die Wirtschaft konnte <strong>erfolgreich</strong> erstellt werden.<strong><a href="%url_page%&view_product=%productid%" class="redirect">Produkt verwalten</a></strong>'; // Add product success
$string[18][79] = 'Leider konnte die Wirtschaft <strong>nicht</strong> erstellt werden.'; // Add product fail
$string[18][80] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Add product no access
$string[18][81] = 'Produkt hinzufügen'; // Add product
$string[18][82] = 'Gruppe wählen'; // Select info
$string[18][83] = 'Erstellen'; // Create product
$string[18][84] = 'Erstellen'; // Create pub

// List pubs and products actions
$string[18][90] = 'WIRTSCHAFTEN';
$string[18][91] = 'Wirtschaften auflisten';
$string[18][92] = 'GLOBALE PRODUKTE';
$string[18][93] = 'Produkte auflisten';
$string[18][94] = 'Das Produkt <strong>%name% (#%id%)</strong> wurde <strong>erfolgreich</strong> gelöscht.';
$string[18][95] = 'Das Produkt <strong>%name% (#%id%)</strong> konnte <strong>nicht</strong> gelöscht werden.';
$string[18][96] = 'Die Wirtschaft <strong>%name% (#%id%)</strong> wurde <strong>erfolgreich</strong> gelöscht.';
$string[18][97] = 'Die Wirtschaft <strong>%name% (#%id%)</strong> konnte <strong>nicht</strong> gelöscht werden.';

/**
 * Page 19
 */
// List view
$string[19][0] = 'Benutzername, Vorname, Nachname, Ticketinfo'; // Search form placeholder
$string[19][1] = 'Benutzername'; // Headlines
$string[19][2] = 'E-Mail'; // Headlines
$string[19][3] = 'Aktion'; // Headlines
$string[19][4] = 'Zurück'; // Table navigation
$string[19][5] = 'Weiter'; // Table navigation

// Sinlgle view
$string[19][10] = 'Benutzerdaten'; // Input placeholder
$string[19][11] = 'Benutzername'; // Input placeholder
$string[19][12] = 'Name'; // Input placeholder
$string[19][13] = 'E-Mail'; // Input placeholder
$string[19][14] = 'Sprache wählen'; // Input placeholder

$string[19][15] = 'Zugriffsrechte'; // Page access title
$string[19][16] = 'Schreibberechtigung'; // Page access title
$string[19][17] = 'Leseberechtigung'; // Page access title
$string[19][18] = 'Schreibberechtigung setzen'; // Page access title
$string[19][19] = 'Leseberechtigung setzen'; // Page access title

$string[19][20] = 'Zugangsdaten an Benutzer senden'; // Send access checkbox
$string[19][21] = 'Mail an neuen Benutzer senden'; // Send access checkbox

$string[19][22] = 'UPDATE'; // Update
$string[19][23] = 'Benutzer aktualisieren'; // Update title

$string[19][24] = 'Zur vorherigen Seite zurück'; // Return button


// Update user
$string[19][50] = 'Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.';
$string[19][51] = 'Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden.';
$string[19][52] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen.';

// Add user
$string[19][55] = 'Der Benutzer wurde <strong>erfolgreich</strong> hinzugefügt.';
$string[19][56] = 'Der Benutzer konnte <strong>nicht</strong> hinzugefügt werden.';
$string[19][57] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen.';

// Remove user
$string[19][60] = 'Möchten Sie den Benutzer %username% (%user%) unwiederruflich entfernen?';
$string[19][61] = 'Der Benutzer (%user%) wurde erfolgreich entfernt.';
$string[19][62] = 'Der Benutzer (%user%) konnte nicht entfernt werden.';
$string[19][63] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen';

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
$string[20][6] = 'Zurück'; // Table navigation
$string[20][7] = 'Weiter'; // Table navigation
$string[20][8] = 'Vorherige Version';  // Action response
$string[20][9] = 'Geänderte Version'; // Single action view
$string[20][10] = 'Änderungen zurücksetzen'; // Single action view
$string[20][11] = 'Ihre Änderung wurde <strong>erfolgreich</strong> durchgeführt.'; // Action response
$string[20][12] = 'Ihre Änderung konnte <strong>nicht</strong> durchgeführt werden'; // Action response
$string[20][13] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Action response
$string[20][14] = 'Zur vorherigen Seite zurück'; // Return button


// Messages
$string[20][100] = 'Profil von %user% überarbeitet'; // User.php
$string[20][101] = 'Profil von %user% entfernt'; // User.php
$string[20][102] = 'Zugriffsrechte von %user% entfernt'; // User.php
$string[20][103] = 'Profil von %user% hinzugefügt'; // User.php
$string[20][103] = 'Version %version% wiederhergestellt'; // User.php

$string[20][110] = 'Neue Wirtschaft hinzugefügt (%name%)'; // pub.php
$string[20][111] = 'Neues globales Produkt (%name%) hinzugefügt'; // pub.php
$string[20][112] = 'Neues Produkt (%name%) hinzugefügt für Wirschaft %pub%'; // pub.php
$string[20][113] = 'Zugriff zur Wirtschaft #%pub% für den Benutzer (%user%) %name% hinzugefügt'; // pub.php
$string[20][114] = 'Wirtschaft #%pub% (%name%) überarbeitet'; // pub.php
$string[20][115] = 'Wirtschaft #%pub% (%name%) entfernt'; // pub.php
$string[20][116] = 'Zugriff für den Benuter #%user% (%username%) für die Wirtschaft #%pub% (%pubname%) entfernt'; // pub.php

$string[20][120] = 'Neues globales Produkt (%name%) hinzugefügt'; // product.php
$string[20][121] = 'Neues Produkt (%name%) hinzugefügt für Wirtschaft #%pub%'; // product.php
$string[20][122] = 'Produkt #%id% (%name%) überarbeitet'; // product.php
$string[20][123] = 'Produkt #%id% (%name%) entfernt'; // product.php
$string[20][124] = 'Produkt #%id% (%name%) ins Menu (#%pub%) aufgenommen'; // product.php
$string[20][125] = 'Produkt #%id% (%name%) aus dem Menu (#%pub%) entfernt'; // product.php
$string[20][126] = 'Verfügbarkeit von Produkt #%id% (%name%) angepasst'; // product.php

$string[20][130] = 'Neue Transaktion #%id% hinzugefügt'; // transaction.php
$string[20][131] = 'Transaktion #%id% überarbeitet'; // transaction.php
$string[20][132] = 'Transaktion #%id% entfernt'; // transaction.php

$string[20][140] = 'Coupon (%name%) hinzugefügt zur Gruppe #%group%'; // coupon.php
$string[20][141] = 'Coupon #%id% überarbeitet'; // coupon.php
$string[20][142] = 'Coupon #%id% entfernt'; // coupon.php

$string[20][150] = 'Neues Ticket (%ticketToken%) hinzugefügt'; // ticket.php
$string[20][151] = 'Ticket (%ticketToken%) überarbeitet'; // ticket.php
$string[20][152] = 'Ticket (%ticketToken%) entfernt'; // ticket.php
$string[20][153] = 'Ticket (%ticketToken%) wiederhergestellt'; // ticket.php
$string[20][154] = 'Ticket (%ticketToken%) entwertet'; // ticket.php
$string[20][155] = 'Ticket (%ticketToken%) manuell reaktiviert'; // ticket.php

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
