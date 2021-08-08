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
$string["general"][9] = 'TKTDATA - STORE'; // Store page title

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
 * Page 8
 */
$string[8][0] = 'Gruppenname, Gruppen-ID, Beschreibung, etc.';

$string[8][1] = 'Name';
$string[8][2] = 'Verwendung';
$string[8][3] = 'Verkaufszeit';
$string[8][4] = 'Aktion';

$string[8][5] = 'Gruppendetails anzeigen';
$string[8][6] = 'Entfernen';

$string[8][7] = 'Zeitlich-<br />unbeschränkt';

$string[8][8] = 'Zurück zur Übersicht';
$string[8][9] = 'Allgemein';
$string[8][10] = 'Formular';
$string[8][11] = 'Ticket';
$string[8][12] = 'Mail';
$string[8][13] = 'Zahlung';
$string[8][14] = 'SDK';

// General
$string[8][20] = 'Gruppenname';
$string[8][21] = 'Maximum Tickets';
$string[8][22] = 'Tickets pro Benutzer';
$string[8][23] = '<a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Verwende den ISO-Code" target="_blank">Währung</a>';
$string[8][24] = 'Betrag';
$string[8][25] = '<abbr title="Format: YYYY-MM-DD HH:ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Verwenden Sie dasselbe Datum sowie Zeit um das Ticket zeitlich unbeschr&auml;nkt an zu bieten (In Start und Endzeit)">Startzeit</abbr>';
$string[8][26] = '<abbr title="Format: YYYY-MM-DD HH:ii:ss, Es kann jedoch jedes beliebige Format verwendet werden&#13;Verwenden Sie dasselbe Datum sowie Zeit um das Ticket zeitlich unbeschr&auml;nkt an zu bieten (In Start und Endzeit)">Endzeit</abbr>';
$string[8][27] = '<abbr title="Value-Added Tax (MwSt.)">VAT</abbr>';
$string[8][28] = 'Beschreibung';
$string[8][29] = 'Update';

// Custom
$string[8][40] = 'Checkbox';
$string[8][41] = 'Datum';
$string[8][42] = 'E-Mail';
$string[8][43] = 'Nummer';
$string[8][44] = 'Radiobutton';
$string[8][45] = 'Selection';
$string[8][46] = 'Text';
$string[8][47] = 'Textfeld';

$string[8][48] = 'Element';
$string[8][49] = 'Entfernen';
$string[8][50] = 'Name';
$string[8][51] = 'Reihenfolge';
$string[8][52] = 'Pflichtfeld';
$string[8][53] = 'Auswahl hinzufügen';
$string[8][54] = 'Platzhalter';
$string[8][55] = 'Auswahl hinzufügen';
$string[8][56] = 'Update';

// Ticket
$string[8][60] = 'Tickettitel';
$string[8][61] = 'Logo';
$string[8][62] = 'Werbung 1 <abbr title="Es wird nur dieser Inhalt angezeigt. Man kann kein Verhältnis verwenden und muss sich an diesen absoluten Werten orientieren.">(453px &#x00D7; 343px)</abbr>'; // Advert 1
$string[8][63] = 'Werbung 2 <abbr title="Es wird nur dieser Inhalt angezeigt. Man kann kein Verhältnis verwenden und muss sich an diesen absoluten Werten orientieren.">(754px &#x00D7; 343px)</abbr>'; // Advert 2
$string[8][64] = 'Werbung 3 <abbr title="Es wird nur dieser Inhalt angezeigt. Man kann kein Verhältnis verwenden und muss sich an diesen absoluten Werten orientieren.">(754px &#x00D7; 343px)</abbr>'; // Advert 3
$string[8][65] = 'Klicken um auszuwählen'; // Advert 1
$string[8][66] = 'Klicken um auszuwählen'; // Advert 2
$string[8][67] = 'Klicken um auszuwählen'; // Advert 3
$string[8][68] = 'Update'; // Update

$string[8][69] = '&#9888; Klicken Sie auf Update, um ihre Änderungen zu sehen.'; // Update message
$string[8][70] = 'Vorschau wird geladen'; // Preview load message

// Mail
$string[8][80] = 'Banner'; // Input name
$string[8][81] = 'Klicken um auszuwählen'; // Image selection info
$string[8][82] = 'Absender'; // Input name
$string[8][83] = 'Anzeigename'; // Input name
$string[8][84] = 'Betreff'; // Input name
$string[8][85] = 'E-Mail'; // Select info
$string[8][86] = 'Ticket'; // Select info
$string[8][87] = 'Nachricht'; // Input name
$string[8][88] = 'Update'; // Button name

$string[8][89] = '&#9888; Klicken Sie auf Update, um ihre Änderungen zu sehen.'; // Info message
$string[8][90] = 'Von:'; // Preview
$string[8][91] = 'Betreff:'; // Preview

// Payment
$string[8][100] = 'Zahlungsanforderungs-Mail'; // Headline
$string[8][101] = 'Diese Nachricht wird im Mail bei einer Zahlungsanforderung erscheinen. Beachte, dass bei Vorkasse oder Rechnung der Zahlungslink nicht erscheinen wird.'; // Info
$string[8][102] = 'E-Mail'; // Message button
$string[8][103] = 'Zahlungslink'; // Message button
$string[8][104] = 'Nachricht'; // Input name

$string[8][105] = 'Payrexx'; // Headline
$string[8][106] = 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.'; // Info
$string[8][107] = 'Payrexx Instance'; // Input name
$string[8][108] = 'Payrexx Secret'; // Input name

$string[8][109] = 'Store'; // Headline
$string[8][110] = 'Damit Sie auch ohne Programmiererfahrung ein Ticket verkaufen können, beinhaltet dieses System auch einen eigenen <a href="%url%store" target="_blank">Store</a> womit Sie ihre Tickets verkaufen können. Im Folgenden können Sie das Design des Store beeinflussen. Möchten Sie jemanden direkt dieses Ticket verkaufen, verwenden Sie diesen Link: <a href="%url%store/tickets/buy/%group%" target="_blank">%url%store/ticket/buy/%group%</a>'; // Info
$string[8][111] = 'Im Store anzeigen'; // Input name
$string[8][112] = 'Dieses Ticket im Store verkaufen'; // Input title
$string[8][113] = 'Sprache des Store wählen'; // Language
$string[8][114] = 'Logo'; // Input name
$string[8][115] = 'Klicken um auszuwählen'; // Select info
$string[8][116] = 'Hintergrundbild'; // Input name
$string[8][117] = 'Klicken um auszuwählen'; // Select info

$string[8][118] = 'ADFS'; // Headline
$string[8][119] = 'Diese Funktion ist nur verfügbar, wenn der Administrator die simpleSAMLphp einstellungen vorgenommen und einen Pfad im general.php angegeben hat. <a href="https://simplesamlphp.org/" target="_blank">Weitere Informationen</a>'; // Info deactivatead
$string[8][120] = 'Durch aktivieren dieser Funktion, muss der Kunde sich über Ihr ADFS authentifizieren um ein Ticket zu erwerben. Beachten Sie, dass die simpleSAML-Konfiguration manuell vorgenommen werden muss. Ist die Konfiguration fehlerhaft, funktioniert der ganze Bestellungsprozess über den Store für diese Ticketgruppe nicht mehr. Die Authentifizierung kann nicht über ein Drittanbieter via SDK erfolgen.'; // Info activated
$string[8][121] = 'Authentifizierung verlangen'; // Input name
$string[8][121] = 'Anmeldung fordern um Ticket zu kaufen'; // Input title
$string[8][123] = 'Fügen Sie den jeweiligen Array-Key des ADFS-Array in das zugehörig definierte Feld vom Formular ein, um die Daten ihres Active Directory zu übernehmen. Leer gelassene Felder müssen vom Benutzer selbst eingetragen werden. Werden alle Felder definiert, kann der Benutzer nur noch ein Coupon hinzufügen.'; // ADFS custom text
$string[8][124] = 'E-Mail<abbr title="Für den Bestellprozess immer benötigt">*</abbr>'; // Input name
$string[8][125] = 'Update';

// SDK
$string[8][130] = 'Möchten Sie den geheimen Schlüssel tatsächlich erneuern?<br /><span style="color: #f0c564;">Diese Aktion wird nur empfohlen, wenn Sie einen Verdacht auf Missbrauch dieses Schlüssels haben oder ihn noch nicht produktiv einsetzen.</span>'; // Refres message

$string[8][131] = 'Geheimschlüssel'; // Headline
$string[8][132] = 'Damit Sie eine Anfrage per SDK machen können, müssen Sie diesen geheimen Schlüssel verwenden. Berücksichtigen Sie, dass dieser Schlüssel nur für diese Gruppe verfügbar ist. Sie können somit nur Tickets, welche dieser Gruppe zugeordnet wurden, überarbeiten, löschen oder auslesen.'; // Info
$string[8][133] = 'WICHTIG: Wer in Besitz dieses Schlüssels ist, kann Tickets hinzufügen, löschen, überarbeiten und auslesen. Veröffentlichen Sie diesen Schlüssel <strong>nie</strong> und geben Sie den Schlüssel nur an vertraute Personen weiter. Vermuten Sie einen Missbrauch dieses Schlüssels, erneuern Sie ihn unverzüglich.'; // Notice

$string[8][134] = 'SDK-Dokument'; // Headline
$string[8][135] = 'Laden Sie sich hier das benötigte SDK-Dokument herunter.'; // Info
$string[8][136] = 'SDK-Dokument herunterladen'; // Link button title
$string[8][137] = 'Download'; // Link button text

$string[8][138] = 'Einfache Verwendung'; // Headline
$string[8][139] = 'Haben Sie keine Programmiererfahrung, können Sie eine einfache Implementierung machen. Diese finden Sie unter dem Reiter <a href="%url_page%&view=%group%&section=5">Zahlung&#8594;Store</a>.'; // Info text

$string[8][140] = 'Dokumentation'; // Headline
$string[8][141] = 'Folgende Informationen können Sie über die SDK erhalten, hinzufügen und überarbeiten.<br />Bitte beachten Sie, dass dies nur eine kleine und undetailierte Dokumentation ist. Eine detailierte Beschreibung der verwendeten Funktionen finden Sie direkt im SDK-Dokument, welches Sie oben herunterladen können.'; // Info
$string[8][142] = 'Ticketinformationen abrufen'; // SDK Code headline
$string[8][143] = 'Ticket-Token abrufen'; // SDK Code headline
$string[8][144] = 'Ticket hinzufügen'; // SDK Code headline
$string[8][145] = 'Ticket überarbeiten'; // SDK Code headline
$string[8][146] = 'Ticket entfernen'; // SDK Code headline
$string[8][147] = 'Ticket wiederherstellen'; // SDK Code headline
$string[8][148] = 'Ticket per Mail senden'; // SDK Code headline
$string[8][149] = 'Coupon-ID per Name erhalten'; // SDK Code headline
$string[8][150] = 'Coupon prüfen'; // SDK Code headline
$string[8][151] = 'Ticketpreis mit Coupon'; // SDK Code headline
$string[8][152] = 'Gruppeninformationen'; // SDK Code headline
$string[8][153] = 'Benuzte Tickets'; // SDK Code headline
$string[8][154] = 'Verfügbare Tickets'; // SDK Code headline
$string[8][155] = 'Pro Benutzer verfügbare Tickets'; // SDK Code headline
$string[8][156] = 'Gateway anfordern'; // SDK Code headline
$string[8][157] = 'Gateway löschen'; // SDK Code headline
$string[8][158] = 'Transaktionsinfos'; // SDK Code headline
$string[8][159] = 'Zahlungserinnerung senden'; // SDK Code headline

// No access to group
$string[8][160] = 'Keine Zugriff auf die Gruppe <strong>#%group%</strong>';

// Add
$string[8][170] = 'Zur vorherigen Seite zurück';
$string[8][171] = 'Gruppe hinzufügen';
$string[8][172] = 'Hinzufügen';

// Actions
$string[8][200] = 'Der Gruppenabschnitt <strong>Allgemein</strong> konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update section
$string[8][201] = 'Der Gruppenabschnitt <strong>Allgemein</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update section
$string[8][202] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][203] = 'Der Gruppenabschnitt <strong>Formular</strong> konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update section
$string[8][204] = 'Der Gruppenabschnitt <strong>Formular</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update section
$string[8][205] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][206] = 'Der Gruppenabschnitt <strong>Ticket</strong> konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update section
$string[8][207] = 'Der Gruppenabschnitt <strong>Ticket</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update section
$string[8][208] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][209] = 'Der Gruppenabschnitt <strong>Mail</strong> konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update section
$string[8][210] = 'Der Gruppenabschnitt <strong>Mail</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update section
$string[8][211] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][212] = 'Der Gruppenabschnitt <strong>Zahlung</strong> konnte <strong>erfolgreich</strong> überarbeitet werden.'; // Update section
$string[8][213] = 'Der Gruppenabschnitt <strong>Zahlung</strong> konnte <strong>nicht</strong> überarbeitet werden.'; // Update section
$string[8][214] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][215] = 'Der geheime Schlüssel konnte <strong>erfolgreich</strong> erneuert werden.'; // Update section
$string[8][216] = 'Der geheime Schlüssel konnte <strong>nicht</strong> erneuert werden.'; // Update section
$string[8][217] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Update section

$string[8][218] = 'Möchten Sie die Grupe #%id% (%name%) wirklich entfernen?'; // Remove request
$string[8][219] = 'Die Gruppe #%id% wurde erfolgreich entfernt.'; // Remove group
$string[8][220] = 'Die Gruppe #%id% konnte nicht entfernt werden.'; // Remove group
$string[8][221] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Remove group

$string[8][222] = 'Die Gruppe konnte <strong>erfolgreich</strong> erstellt werden. <a href="%url_page%&view=%id%" class="redirect">Gruppe verwalten</a></strong>'; // Add group
$string[8][223] = 'Leider konnte die Gruppe <strong>nicht</strong> erstellt werden.'; // Add group
$string[8][224] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Add group

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
$string[16][30] = 'Alle Produkt für die Auswertung verwenden'; // Toggle title
$string[16][31] = 'Alle'; // Toggle text
$string[16][32] = 'Nur eigene Produkt für die Auswertung verwenden'; // Toggle title
$string[16][33] = 'Eigene'; // Toggle text
$string[16][34] = 'Einnahmen abzüglich Gebühren und Rückerstattungen'; // Mainbox title
$string[16][35] = 'Gebühren:';
$string[16][36] = 'Zurückerstattet:';
$string[16][37] = 'Email, Zahlungs-ID, Zahlungszeit'; // Search form placeholder
$string[16][38] = 'Ohne Zahlung abgeholt'; // Pickup states
$string[16][39] = 'Zahlung erwartet'; // Pickup states
$string[16][40] = 'Abholung erwartet'; // Pickup states
$string[16][41] = 'Email'; // Headline
$string[16][42] = 'Preis'; // Headline
$string[16][43] = 'Datum'; // Headline
$string[16][44] = 'Aktion'; // Headline
$string[16][45] = 'Zahlung erwartet. Produkte bereits abgeholt.'; // Pickup states title
$string[16][46] = 'Zahlung erwartet.'; // Pickup states title
$string[16][47] = 'Abholung erwartet'; // Pickup states title
$string[16][48] = 'Abgeholt'; // Pickup states title
$string[16][49] = 'Transaktion anzeigen'; // Link title
$string[16][50] = 'Transaktion entfernen'; // Link title
$string[16][51] = 'Zurück'; // Footer nav
$string[16][52] = 'Weiter'; // Footer nav

$string[16][53] = 'Die Transaktion <strong>%email% (#%id%)</strong> wurde <strong>erfolgreich</strong> gelöscht.'; // Remove message success
$string[16][54] = 'Die Transaktion <strong>%email% (#%id%)</strong> konnte <strong>nicht</strong> gelöscht werden.'; // Remove message fail
$string[16][55] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // No access

// Remove
$string[16][60] = 'Möchtest du die Transaktion <strong>%email% (#%id%)</strong>  wirklich löschen?';

// No access to pub
$string[16][70] = 'Du hast keinen Zugriff auf die Wirtschaft (#%id%) <strong>%name%</strong>'; // Fullscreen message
$string[16][71] = 'Zurück'; // Fullscreen return button

// Ajax messages
$string[16][80] = 'Rückerstattung fehlgeschlagen.';
$string[16][81] = 'Rückerstattung fehlgeschlagen. %refund%';
$string[16][82] = 'Dieser Benutzer hat keine Berechtigung zu dieser Aktion';
$string[16][83] = 'Erfolgreich -%refund% %currency% erstattet.';
$string[16][84] = 'Zahlungseingang konnte nicht bestätigt werden';

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
$string[18][35] = 'Store';
$string[18][36] = 'Sprache des Store wählen';
$string[18][37] = 'Payrexx'; // Payrexx title
$string[18][38] = 'Damit Sie online direkt eine Zahlung empfangen können, benötien Sie ein Konto bei <a href="https://www.payrexx.com" title="Besuchen Sie die Webseite von Payrexx" target="_blank">Payrexx</a>. Payrexx ist ein schweizer Unternehmen. Möchten Sie Stripe als Ihren <abbr title="Payment service provider">PSP</abbr> haben, können Sie sich auf <a href="https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/" target="_blank">dieser Seite</a> informieren.'; // Payrexx info
$string[18][39] = 'Payrexx Instance'; // Input name
$string[18][40] = 'Payrexx Secret'; // Input name
$string[18][41] = 'Währung'; // Input name
$string[18][42] = 'Gebühren'; // Fees title
$string[18][43] = 'Pro Transaktion verlangt der Anbieter entsprechende Gebühren. Bitte definiere hier, welche Gebüren dein Zahlungsanbieter verlang um die Auswertung korrekt zu erhalten. Die beiden Gebühren werden zusammengezählt und entsprechend verrechnet. An den Produktpreisen ändert sich dadurch nichts.'; // Fees info
$string[18][44] = 'Absolute Gebühren'; // Input name
$string[18][45] = 'Prozentuale Gebühren'; // Input name
$string[18][46] = 'Update'; // Button value

$string[18][47] = 'Benutzername'; // Input name
$string[18][48] = 'Email'; // Input name
$string[18][49] = 'Schreiben | Lesen'; // Input name
$string[18][50] = '%user% hat Schreibrechte auf diese Wirtschaft'; // Toggle title
$string[18][51] = '%user% hat keine Schreibrechte auf diese Wirtschaft'; // Toggle title
$string[18][52] = '%user% hat Leserechte auf diese Wirtschaft'; // Toggle title
$string[18][53] = '%user% hat keine Leserechte auf diese Wirtschaft'; // Toggle title
$string[18][54] = 'Die Rechte konnten nicht hinzugefügt werden.'; // Fail info
$string[18][55] = 'Sie haben <strong>keine Berechtigung</strong> um diese Aktion durchzuführen'; // Fail info

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
$string[18][85] = 'Die Unterseite existiert nicht.'; // No section found
$string[18][86] = 'Zurück'; // No section found

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
$string[20][104] = 'Version %version% wiederhergestellt'; // User.php

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

$string[20][160] = 'Grupe #%id% überarbeitet [Allgemein]'; // groups.php
$string[20][161] = 'Grupe #%id% überarbeitet [Formular]'; // groups.php
$string[20][162] = 'Grupe #%id% überarbeitet [Ticket]'; // groups.php
$string[20][163] = 'Grupe #%id% überarbeitet [Mail]'; // groups.php
$string[20][164] = 'Grupe #%id% überarbeitet [Zahlung]'; // groups.php
$string[20][165] = 'Geheimer Schlüssel der Gruppe #%id% erneuert'; // groups.php
$string[20][166] = 'Gruppe %name% hinzugefügt'; // groups.php
$string[20][167] = 'Gruppe #%id% entfernt'; // groups.php

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

/**
 * Store
 */
// 1.php
$string["store"][0] = 'Nach Veranstaltung suchen';
$string["store"][1] = 'Abgelaufen';
$string["store"][2] = 'Ausverkauft';
$string["store"][3] = 'Existiert nicht';
$string["store"][4] = 'Vorherige Veranstaltungen ansehen';
$string["store"][5] = 'Weitere Veranstaltungen ansehen';

// 2.php
$string["store"][10] = 'Coupon konnte nicht angewendet werden.';
$string["store"][11] = 'Die Mail konnte nicht versendet werden.';
$string["store"][12] = 'Das Zeitfenster um ein Ticket zu lösen ist <strong>nicht</strong> offen. Konsultiere die Gruppe für nähere Infomrationen.';
$string["store"][13] = 'Die maximale Anzahl an Tickets wurde erreicht.';
$string["store"][14] = 'Die maximale Anzahl an Tickets pro Benutzer wurde erreicht.';
$string["store"][15] = 'Das Ticket konnte <strong>erfolgreich</strong> erstellt werden.';
$string["store"][16] = 'Leider konnte das Ticket <strong>nicht</strong> erstellt werden.';
$string["store"][17] = 'E-Mail';
$string["store"][18] = '-- Auswahl treffen --';
$string["store"][19] = 'Auswahl treffen';
$string["store"][20] = 'Häcken setzen';
$string["store"][21] = 'Coupon einlösen';
$string["store"][22] = 'Zu bezahlen:';
$string["store"][23] = 'BEZAHLEN';

// 3.php
$string["store"][30] = 'Zahlung jetzt tätigen';
$string["store"][31] = 'Die Zahlungsseite konnte nicht geladen werden. Melden Sie sich beim Administrator.<br />Folgende Fehlermeldung wird ausgegeben: %message%';

// 4.php
$string["store"][40] = 'Die Mail konnte nicht gesendet werden. Laden Sie die Seite neu um es noch einmal zu versuchen.';
$string["store"][41] = 'Zahlung erfolgreich'; // Payment state successful
$string["store"][42] = 'Zahlung fehlgeschlagen'; // Transaction failed
$string["store"][43] = 'Zahlung erfolgreich'; // Confirmed
$string["store"][44] = 'Zahlung erwartet'; // 15 invoice
$string["store"][45] = 'Zahlung erwartet'; // 27 prepayment
$string["store"][46] = 'Zahlung fehlgeschlagen'; // unknown error
$string["store"][47] = 'Hallo %mail%<br /><br />Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und wird dir zeitnahe per Mail zugestellt. Die Zahlung ist bei uns bereits eingegangen. <br />Speichere dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.';// Payment state successful
$string["store"][48] = 'Hallo %mail%<br /><br />Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und wird dir zeitnahe per Mail zugestellt. Die Zahlung ist bei uns bereits eingegangen.<br />Speichere dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.'; // Confirmed
$string["store"][49] = 'Hallo %mail%<br /><br />Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt.<br />Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.'; // 15 invoice
$string["store"][50] = 'Hallo %mail%<br /><br />Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt.<br />Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.'; // 27 Invoice
$string["store"][51] = '"Hallo %mail%<br /><br />Es freut uns, dass du dabei bist. Dein Ticket wurde erfolgreich erstellt und eine Rechnug an deine Mail gesendet. Nach Zahlungseingang wird dir das Ticket per Mail zugestellt.<br />Speichere dann dein Ticket auf dein Mobiltelefon und freue dich auf den besten Event des Jahres.'; // Unknown error
$string["store"][52] = 'Preis:'; // Price
$string["store"][53] = 'Nicht verwendet'; // Coupon
$string["store"][54] = 'Coupon:'; // Coupon
$string["store"][55] = 'Zahlung getätigt'; // payment state
$string["store"][56] = 'Zahlung erwartet'; // payment state
$string["store"][57] = 'Zahlung getätigt'; // payment state
$string["store"][58] = 'Zahlung authorisiert'; // payment state
$string["store"][59] = 'Zahlung reserviert'; // payment state
$string["store"][60] = 'Zahlung unbekannt'; // payment state
$string["store"][61] = 'Zahlstatus:';
$string["store"][62] = 'Zahldatum:';
$string["store"][63] = 'Total:';
$string["store"][64] = 'Ticket stolz zur Verfügung gestellt von <span>TKTDATA</span>'; // Footer text

// 5.php
$string["store"][70] = 'Wie kaufe ich ein Ticket';
$string["store"][71] = 'Der Ticketkauf ist ganz einfach und kann von dir problemlos durchgeführ werden. Besuche dazu <a href="%url%store/" target="_blank">%url%store/</a> und wähle Dein Ticket aus. Du wirst auf eine neue Seite weitergeleitet wo du Deine Kontaktangaben machen must. Deine E-Mail wird zwingend benötigt, damit wir Dir dein Ticket zustellen können. Die restlichen Angaben können entweder zwingend oder freiwillig sein, dies kommt auf das Ticket an. Wenn du einen Coupon besitzt kannst du diesen einlösen, indem du auf &quot;Coupon einlösen&quot; klickst. Nach dem ausfüllen des Feldes wird Dir entsprechen der Ticketpreis angepasst. Bitte klicke dich aus dem Eingabefeld um die Änderung zu sehen. Danach kannst du auf &quot;Bezahlen&quot; klicken.<br />
Nun die hälfte ist bereits erledigt. Warte einen Augenblick, bis das Zahlfenster geladen hat. Wähle nun Deine bevorzugte Zahlungsmethode aus und folge den Anweisungen des Zahlungsfenster. Nach der erfolgreichen Zahlung, warte kurz bis du auf eine neue Seite weitergeleitet wirts.<br />
Du hast nun erfolgreich dein Ticket bestellt. Du solltest nun eine Zusammenfassung deiner Bestellung sehen. Das Ticket wird Dir unverzüglich per Mail zugestellt. Dies kann jedoch einige Minuten in anspruch nehmen.';
$string["store"][72] = 'Ich habe mein Ticket verloren!';
$string["store"][73] = 'Nur keine Panik. Das ist halb so wild, Dein Ticket ist bei uns gespeichert und wir können es Dir ganz einfach erneut zusenden. Besuche dafür <a href="%url%store/ticket/find-ticket" target="_blank">%url%store/ticket/find-ticket</a>. Gib deine E-Mailadresse ein und klicke auf das &quot;Such-Symbol&quot;. Es kommt nun eine Liste mit allen deinen Tickets. Um das Ticket erneut zu erhalten, kannst du einfach auf &quot;erneut senden&quot; klicken. Möchtest du den Veranstalter kontaktieren, kannst du auf &quot;Veranstalter&quot; klicken. Du wirst auf eine Kontaktseite weitergeleitet';
$string["store"][74] = 'Zahlungsmöglichkeiten';
$string["store"][75] = 'Jedes Ticket hat seine eigenen Zahlungsmethoden. Grundsätzlich sind jedoch alle gängigen Zahlungsmethoden verfügbar. Von Mastercard über Visa bis hin zur Rechnung oder Vorkasse ist praktisch alles enthalten.';
$string["store"][76] = 'Personalisierte Tickets';
$string["store"][77] = 'Bitte beachte, dass alle Tickets personalisiert sind und somit nicht an andere übertragen werden können. Möchtest du eine Ticketänderung vornehmen so melde Dich bitte im voraus bei der Eventleitung.';
$string["store"][78] = 'Kontakt';
$string["store"][79] = 'Das System läuft unter <span>TKTDATA</span>. Melde dich bei Problemen bei:<br />
<br />
Max Muster<br />
<a href="mailto:max.muster@example.ch">max.muster@example.ch</a><br />
Musterstrasse 1<br />
1234 Musterort <br />
079 123 45 67<br />';

// 6.php
$string["store"][90] = 'Deine E-Mail'; // Placeholder
$string["store"][91] = 'Existiert nicht';
$string["store"][92] = 'Abgelaufen';
$string["store"][93] = 'Zahlung getätigt';
$string["store"][94] = 'Zahlung erwartet';
$string["store"][95] = 'Zahlung getätigt';
$string["store"][96] = 'Zahlung authorisiert';
$string["store"][97] = 'Zahlung reserviert';
$string["store"][98] = 'Zahlung unbekannt';
$string["store"][99] = 'Erneut senden';
$string["store"][100] = 'Veranstalter';
$string["store"][101] = 'Vorherige Tickets ansehen';
$string["store"][102] = 'Weitere Tickets ansehen';

// 7.php
$string["store"][110] = 'Nach Wirtschaft suchen';
$string["store"][111] = 'Vorherige Wirtschaften ansehen';
$string["store"][112] = 'Weitere Wirtschaften ansehen';

// 8.php
$string["store"][120] = 'Der Zahlungsvorgang konnte nicht gestartet werden.';
$string["store"][121] = 'TOTAL:';
$string["store"][122] = 'BEZAHLEN';
$string["store"][123] = 'TKTData Logo';
$string["store"][124] = 'Trinkgeld';

// 9.php
$string["store"][130] = 'Zahlung jetzt tätigen';
$string["store"][131] = 'Die Zahlungsseite konnte nicht geladen werden. Melden Sie sich beim Administrator.<br />Folgende Fehlermeldung wird ausgegeben: %message%';

// 10.php
$string["store"][140] = 'Die Mail konnte nicht gesendet werden. Laden Sie die Seite neu um es noch einmal zu versuchen.';
$string["store"][141] = 'Zahlung erwartet';
$string["store"][142] = 'Zahlung erfolgreich';
$string["store"][143] = 'Zahlung fehlgeschlagen';
$string["store"][144] = 'Hallo %mail%,<br />Bitte bezahle bar an der Kasse. Gib als Zahlungs-ID <strong>#%id%</strong> an.';
$string["store"][145] = 'Hallo %mail%,<br />Du kannst mit diesem Beleg deinen Einkauf an der Kasse abholen gehen. Gib als Zahlungs-ID <strong>#%id%</strong> an. Der Beleg wurde dir auch per Mail (an %mail%) zugestellt.';
$string["store"][146] = 'Hallo,<br />Ihre Zahlung ist fehlgeschlagen. Versuchen Sie es erneut oder melden Sie sich beim Personal.';
$string["store"][147] = 'Trinkgeld';
$string["store"][148] = 'Total:';
$string["store"][149] = 'Store stolz zur Verfügung gestellt von <span>TKTDATA</span>';

// Ajax.php / ajax.js
$string["store"][160] = 'Coupon nicht gefunden';
$string["store"][161] = 'Coupon nicht mehr verfügbar';
$string["store"][162] = 'Ticketpreis mit Coupon';
$string["store"][163] = 'Coupon nicht mehr verfügbar';
$string["store"][164] = 'Das Ticket wurde erfolgreich gesendet';
$string["store"][165] = 'Beim senden ist ein Fehler aufgetreten. Versuche es erneut.';
$string["store"][166] = 'Das Ticket wurde erfolgreich gesendet';
$string["store"][167] = 'Beim senden ist ein Fehler aufgetreten. Versuche es erneut.';
$string["store"][168] = 'Kein TicketToken angegeben.';

$string["store"][169] = 'Coupon';
$string["store"][170] = 'Die Mail wird gesendet. Wir bitten um etwas Geduld.';

// Store footer
$string["store"]["footer1"] = 'Kontakt'; // Ticket
$string["store"]["footer2"] = 'Mein Ticket finden'; // Ticket
$string["store"]["footer3"] = 'Wie kaufe ich ein Ticket?'; // Ticket
$string["store"]["footer4"] = 'Welche Zahlungsmöglichkeiten gibt es?'; // Ticket
$string["store"]["footer5"] = 'Unterstützt von <span>TKTDATA</span>'; // Ticket
$string["store"]["footer6"] = 'Unterstützt von <span>TKTDATA</span>'; // Pubs
 ?>
