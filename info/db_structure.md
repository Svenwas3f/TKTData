//---------------------
// Tabellenaufbau
//---------------------

Jede Tabelle welche mit dem Ticketsystem TKTData in Verbindung steht besitzt den Prefix tktdata_


//---------------------
// KData Tabellen
//---------------------

tktdata_livedata_archive
tktdata_livedata_live
tktdata_menu
tktdata_tickets
tktdata_tickets_coupon
tktdata_tickets_groups
tktdata_user
tktdata_user_actions
tktdata_user_rights

//---------------------
// KData Tabellen erklärt
//---------------------

//-- tktdata_livedata_archive --//
Alle archivierten Livedaten aus tktdata_livedata_live werden hier abgespeichert

//-- tktdata_livedata_live --//
Alle aktuellen Eintrittsdaten werden hier abgespeichert.

//-- tktata_menu --//
Diese Tabelle beinhaltet alle Menupunkte

//-- tktdata_tickets --//
In dieser Tabelle werden alle Daten der Tickets gespeichert, wie Ticket-Schlüssel etc.

//-- tktdata_tickets_coupon --//
Alle Couponinformationen werden hier abgespeichert

//-- tktdata_tickets_groups --//
Alle Ticketgruppeninformationen werden hier abgespeichert

//-- tktdata_user --//
in dieser Tabelle werden alle Benutzer, welche Zugriff auf die TKTDATA-Verwaltungssoftware haben sollen gespeichert. Um Änderungen am System vorzunehmen, muss der Benutzer jedoch genügen Rechte haben, welche in tktdata_user_rights definiert werden.

//-- tktdata_user_rights --//
In dieser Tabelle werden die Berechtigungen abgespeichert. Jede Seite besitzt zwei Berechtigungsstufen. Die einfache (Lesen) und die erweiterte (Lesen & schreiben). Jeder TKTDATA Seite wird eine eindeutige ID zugeteilt um die Rechte einfacher zu verwalten.

//-- kdata_user_actions --//
In dieser Tabelle werden alle Änderungen, die von den Benutzer vorgenommen wurden, abgespeichert.
