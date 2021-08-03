<?php
/**
 * Define language settings
 * The filename is equal to the language code and can not be longer than 10 characters
 * it is recomended to use one of the ISO language codes http://www.loc.gov/standards/iso639-2/php/code_list.php
 */
$language_code = "en";
$language_loc = "English";
$language_int = "English";

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
$string["general"][0] = 'TKTDATA - EVENTMANAGEMENT '; // Page title
$string["general"][1] = 'TKTDATA - Your event management system made with love in Switzerland.'; // Page description
$string["general"][2] = 'TKTDATA, Ticketverwaltung, Eventverwaltung, Events'; // Keywords

$string["general"][3] = 'Please activate JavaScript to use this website'; // JavaScript information
$string["general"][4] = 'Building page...'; // Building page...
$string["general"][5] = 'The page <strong>#%page%</strong> does not exits.'; // Fullscreen info (page does not exist)
$string["general"][6] = 'Back'; // Fullscreen info (page does not exist)
$string["general"][7] = 'Access to the page <strong>#%page%</strong> denied. '; // Fullscreen info (page access denied)
$string["general"][8] = 'Back'; // Fullscreen info (page access denied)

/**
* Footer
*/
$string["footer"][0] = '&copy; ' . date("Y") . ' by <span>TKTDATA</span>';

/**
* Errors
*/
$string["error"][0] = 'No access to the system';
$string["error"][1] = 'You do not yet have authorizations to access this system. Please log in to the administrator';
$string["error"][2] = 'Access to invalid ticket';
$string["error"][3] = 'You tried to get an invalid ticket';
$string["error"][4] = 'Database connection failed';
$string["error"][5] = 'A connection to the database could not be established. ';
$string["error"][6] = 'No pub registerd';
$string["error"][7] = 'You need a pub to access the menu';
$string["error"][8] = '404 - Page not found';
$string["error"][9] = 'Error during request';
$string["error"][10] = 'Unknown error';
$string["error"][11] = 'Unknown error. If this occurs repeatedly, please contact the administrator ';

/**
* Login / Auth
*/
$string["auth"][0] = 'Username'; // Input name
$string["auth"][1] = 'Password'; // Input name
$string["auth"][2] = 'LOGIN'; // Input name
$string["auth"][3] = 'Sign in'; // Input title
$string["auth"][4] = 'Forgot password'; // Link name
$string["auth"][5] = 'Reset password'; // Link title
$string["auth"][6] = 'Username / E-Mail'; // Input name
$string["auth"][7] = 'Reset'; // Input name
$string["auth"][8] = 'Reset current password'; // Input title
$string["auth"][9] = 'Return to login'; // Link title
$string["auth"][10] = 'Sign in'; // Link name

/**
* Actions
*/
$string["action"][0] = 'Cancel';
$string["action"][1] = 'Confirm';

/**
* PDF
*/
$string["pdf"][0] = 'TICKET'; // Ticket title
$string["pdf"][1] = 'Ticket provided by <span>TKTDATA</span>'; // Ticket footer
$string["pdf"][2] = 'MENULIST'; // Menu title
$string["pdf"][3] = 'Menüliste provided by <span>TKTDATA</span>'; // Menu footer

/**
 * Menu
 */
$string["menu"][1] = 'Ticket';
$string["menu"][2] = 'Coupons';
$string["menu"][3] = 'Scanner';
$string["menu"][4] = 'Live';
$string["menu"][5] = 'Pubs';
$string["menu"][6] = 'Users';
$string["menu"][7] = 'All tickets';
$string["menu"][8] = 'Groups';
$string["menu"][9] = 'All coupons';
$string["menu"][10] = 'Informations';
$string["menu"][11] = 'QR-Scanner';
$string["menu"][12] = 'Code-Scanner';
$string["menu"][13] = 'Live';
$string["menu"][14] = 'Archive';
$string["menu"][15] = 'Manually';
$string["menu"][16] = 'Overview';
$string["menu"][17] = 'Products';
$string["menu"][18] = 'Setting';
$string["menu"][19] = 'All Users';
$string["menu"][20] = 'Activities';
$string["menu"]["profile"] = 'Profile';

$string["menu"]["mainpage"] = 'Menu #%mainpage% [%mainpagename%]';
$string["menu"]["subpage"] = 'Submenu #%submenu% [%submenuname%] of Menu #%mainmenu%';

/**
 * Media hub
 */
$string["mediahub"][0] = 'Overview';
$string["mediahub"][1] = 'Add image';
$string["mediahub"][2] = 'Alt:';
$string["mediahub"][3] = 'User:';
$string["mediahub"][4] = 'Uploadtime:';
$string["mediahub"][5] = 'Delete';
$string["mediahub"][6] = 'Fullscreen';
$string["mediahub"][7] = 'APPLY';
$string["mediahub"][8] = 'Klick or drag document';
$string["mediahub"][9] = 'Upload ...';
$string["mediahub"][10] = 'Load more';
$string["mediahub"][11] = 'The revision of the alt text failed';
$string["mediahub"][12] = 'Are you sure you want to delete the document?';
$string["mediahub"][13] = 'Removing the document failed';
$string["mediahub"][14] = '(Error while uploading)';

/**
 * Page 7
 */
// List
$string[7][0] = 'Username, First name, Name, Ticketinfo'; // Search form placeholder

$string[7][1] = 'Voided without payment'; // Ticket states
$string[7][2] = 'Blocked & payed'; // Ticket states
$string[7][3] = 'Payment expected'; // Ticket states
$string[7][4] = 'Voided'; // Ticket states
$string[7][5] = 'Blocked'; // Ticket states

$string[7][6] = 'E-Mail'; // Headline
$string[7][7] = 'Purchase date'; // Headline
$string[7][8] = 'Activity'; // Headline

$string[7][9] = 'View ticketdetails'; // Action
$string[7][10] = 'open PDF'; // Action
$string[7][11] = 'Restore'; // Action
$string[7][12] = 'Remove'; // Action
$string[7][13] = 'Name: %name%&#013;ID: %id%'; // Action

$string[7][14] = 'Back'; // nav
$string[7][15] = 'Next'; // nav

// View
$string[7][20] = 'Name: %name%&#013;ID: %id%';
$string[7][21] = 'Return to the previous page';

$string[7][22] = 'Ticket voided on %date%, payment not made.'; // Top bar text
$string[7][23] = 'Blocked ticket, already paid.'; // Top bar text
$string[7][24] = 'Payment not made.'; // Top bar text
$string[7][25] = 'Ticket voided on %date%'; // Top bar text
$string[7][26] = 'Ticket blocked.'; // Top bar text

$string[7][27] = 'PDF'; // Img alt (right menu)
$string[7][28] = 'open PDF'; // Link title (right menu)
$string[7][29] = 'Mail'; // Img alt (right menu)
$string[7][30] = 'Send ticket via mail'; // Link title (right menu)
$string[7][31] = 'Mail'; // Img alt (right menu)
$string[7][32] = 'Request payment'; // Link title (right menu)
$string[7][33] = 'Refund'; // Img alt (right menu)
$string[7][34] = 'Refund payment'; // Link title (right menu)
$string[7][35] = 'Reactivate'; // Img alt (right menu)
$string[7][36] = 'Reactivate ticket'; // Link title (right menu)
$string[7][37] = 'Void'; // Img alt (right menu)
$string[7][38] = 'Void ticket'; // Link title (right menu)
$string[7][39] = 'Restore'; // Img alt (right menu)
$string[7][40] = 'Restore ticket'; // Link title (right menu)
$string[7][41] = 'Remove'; // Img alt (right menu)
$string[7][42] = 'Remove ticket'; // Link title (right menu)

$string[7][43] = 'E-Mail'; // Input name
$string[7][44] = 'Card'; // Options select
$string[7][45] = 'Invoice'; // Options select
$string[7][46] = 'Payment not received'; // Options select
$string[7][47] = 'Payment method'; // Headline select
$string[7][48] = 'Amount'; // Input name
$string[7][49] = 'Coupon not used'; // Coupon
$string[7][50] = '&#9432; Payment done on %date%'; // Payment time
$string[7][51] = 'Update';

// Add
$string[7][60] = 'Choose group';
$string[7][61] = 'Available tickets: %availableTickets%/%maxTickets%&#013;Tickets per user: %tpu%&#013;Preis: %price% %currency% + %vat%% VAT.&#013;';
$string[7][62] = 'Send Ticket to shopper';
$string[7][63] = 'Add';

// Actions
$string[7][70] = 'The ticket could be voided <strong>successfully</strong>. '; // Employ message
$string[7][71] = 'Unfortunately the ticket could <strong>not</strong> be voided.'; // Employ message
$string[7][72] = 'The ticket could be reactivated <strong>successfully</strong>.'; // Reactivate message
$string[7][73] = 'Unfortunately the ticket could <strong>not</strong> be reactivated.'; // Reactivate message
$string[7][74] = 'The mail could be sent <strong>successfully</strong>.'; // Email message
$string[7][75] = 'Unfortunately the mail could <strong>not</strong> be sent.'; // Email message
$string[7][76] = 'The mail could be sent <strong>successfully</strong>.'; // Payment request message
$string[7][77] = 'Unfortunately the mail could <strong>not</strong> be sent.'; // Payment request message
$string[7][78] = 'Are you sure you want to refund the payment for the %ticketToken% ticket?'; // Refund message
$string[7][79] = 'The money was successfully refunded.'; // Refund message
$string[7][80] = 'There was an error refunding: <br /> %message%'; // Refund message
$string[7][81] = 'The ticket could be revised <strong>successfully</strong>. '; // Update ticket
$string[7][82] = 'Unfortunately, the ticket <strong>could not</strong> be revised. '; // Update ticket
$string[7][83] = 'You have <strong>no authorization</strong> to perform this action'; // Update ticket

$string[7][84] = 'Coupon could not be used.'; // Add messages
$string[7][85] = 'The mail could not be sent.'; // Add messages
$string[7][86] = 'The time window to buy a ticket is <strong>not</strong> open. Consult the group for more information.'; // Add messages
$string[7][87] = 'The maximum number of tickets has been reached.'; // Add messages
$string[7][88] = 'The maximum number of tickets per user has been reached.'; // Add messages
$string[7][89] = 'The ticket could be created <strong>successfully</strong>. <strong><a href="%url_page%&view=%ticketToken%" class="redirect"> View ticket</a></strong> '; // Add messages
$string[7][90] = 'Unfortunately, the ticket could <strong>not</strong> be created'; // Add message
$string[7][91] = 'You have <strong>no authorization</strong> to perform this action'; // Add message

$string[7][92] = 'The ticket could be blocked <strong>successfully</strong>.';
$string[7][93] = 'Unfortunately the ticket could <strong>not</strong> be blocked.';
$string[7][94] = 'You have <strong>no authorization</strong> to perform this action';

$string[7][95] = 'The ticket could be activated <strong>successfully</strong>.';
$string[7][96] = 'Unfortunately the ticket could <strong>not</strong> be activated.';
$string[7][97] = 'You have <strong>no authorization</strong> to perform this action';

/**
 * Page 8
 */
$string[8][0] = 'Groupname, Group-ID, Description, etc.';

$string[8][1] = 'Name';
$string[8][2] = 'Usage';
$string[8][3] = 'Sales time';
$string[8][4] = 'Activity';

$string[8][5] = 'View groupdetails';
$string[8][6] = 'remove';

$string[8][7] = 'Unlimited<br />time';

$string[8][8] = 'Back to overview';
$string[8][9] = 'General';
$string[8][10] = 'Form';
$string[8][11] = 'Ticket';
$string[8][12] = 'Mail';
$string[8][13] = 'Payment';
$string[8][14] = 'SDK';

// General
$string[8][20] = 'Groupname';
$string[8][21] = 'Maximum tickets';
$string[8][22] = 'Tickets per user';
$string[8][23] = '<a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" title="Use ISO-Code" target="_blank">Currency</a>';
$string[8][24] = 'Amount';
$string[8][25] = '<abbr title="Format: YYYY-MM-DD HH:ii:ss, however, any format can be used &#13;Use the same date and time to offer the ticket indefinitely (in start and end time)">Starttime</abbr>';
$string[8][26] = '<abbr title="Format: YYYY-MM-DD HH:ii:ss, however, any format can be used &#13;Use the same date and time to offer the ticket indefinitely (in start and end time)">Endtime</abbr>';
$string[8][27] = '<abbr title="Value-Added Tax">VAT</abbr>';
$string[8][28] = 'Description';
$string[8][29] = 'Update';

// Custom
$string[8][40] = 'Checkbox';
$string[8][41] = 'Datum';
$string[8][42] = 'E-Mail';
$string[8][43] = 'Nummer';
$string[8][44] = 'Radiobutton';
$string[8][45] = 'Selection';
$string[8][46] = 'Text';
$string[8][47] = 'Textarea';

$string[8][48] = 'Element';
$string[8][49] = 'Remove';
$string[8][50] = 'Name';
$string[8][51] = 'Order';
$string[8][52] = 'Required';
$string[8][53] = 'Add selection';
$string[8][54] = 'Placeholder';
$string[8][55] = 'Add selection';
$string[8][56] = 'Update';

// Ticket
$string[8][60] = 'Tickettitle';
$string[8][61] = 'Logo';
$string[8][62] = 'Advert 1 <abbr title="Only this content is displayed. You cannot use a ratio and you have to orientate yourself on these absolute values.">(453px &#x00D7; 343px)</abbr>'; // Advert 1
$string[8][63] = 'Advert 2 <abbr title="Only this content is displayed. You cannot use a ratio and you have to orientate yourself on these absolute values.">(754px &#x00D7; 343px)</abbr>'; // Advert 2
$string[8][64] = 'Advert 3 <abbr title="Only this content is displayed. You cannot use a ratio and you have to orientate yourself on these absolute values.">(754px &#x00D7; 343px)</abbr>'; // Advert 3
$string[8][65] = 'Click to select '; // Advert 1
$string[8][66] = 'Click to select '; // Advert 2
$string[8][67] = 'Click to select '; // Advert 3
$string[8][68] = 'Update'; // Update

$string[8][69] = '&#9888; Click Update to see your changes.'; // Update message
$string[8][70] = 'Loading preview'; // Preview load message

// Mail
$string[8][80] = 'Banner'; // Input name
$string[8][81] = 'Click to select'; // Image selection info
$string[8][82] = 'From'; // Input name
$string[8][83] = 'Click to select'; // Input name
$string[8][84] = 'Subject'; // Input name
$string[8][85] = 'E-Mail'; // Select info
$string[8][86] = 'Ticket'; // Select info
$string[8][87] = 'Message'; // Input name
$string[8][88] = 'Update'; // Button name

$string[8][89] = '&#9888; Click Update to see your changes.'; // Info message
$string[8][90] = 'From:'; // Preview
$string[8][91] = 'Subject:'; // Preview

// Payment
$string[8][100] = 'Paymentrequest-Mail'; // Headline
$string[8][101] = 'This message will appear in the mail when a payment request is made. Note that the payment link will not appear in the case of prepayment or invoice.'; // Info
$string[8][102] = 'E-Mail'; // Message button
$string[8][103] = 'paymentlink'; // Message button
$string[8][104] = 'Message'; // Input name

$string[8][105] = 'Payrexx'; // Headline
$string[8][106] = 'To be able to receive a online payment, you need an account at <a href="https://www.payrexx.com" title="Visit the website of Payrexx" target="_blank">Payrexx</a>. Payrexx is a Swiss company. If you would like to have Stripe as your <abbr title="Payment service provider">PSP</abbr>, visit <a href = "https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/ "target="_ blank ">this page</a>.'; // Info
$string[8][107] = 'Payrexx Instance'; // Input name
$string[8][108] = 'Payrexx Secret'; // Input name

$string[8][109] = 'Store'; // Headline
$string[8][110] = 'That you can sell a ticket without any programming experience, this system also includes a <a href="%url%store" target="_blank">store</a> with which you can sell your tickets. In the following you can configure the design of the store. If you would like to sell someone this ticket directly, use this link: <a href="%url%store/tickets/buy/%group%" target="_blank">%url%store/ticket/buy/%group%</a> '; // Info
$string[8][111] = 'show in store'; // Input name
$string[8][112] = 'Sell this ticket in store'; // Input title
$string[8][113] = 'Logo'; // Input name
$string[8][114] = 'Click to select'; // Select info
$string[8][115] = 'Background'; // Input name
$string[8][116] = 'Click to select'; // Select info

$string[8][117] = 'ADFS'; // Headline
$string[8][118] = 'This function is only available if the administrator has made the simpleSAMLphp configuration and specified the path in general.php. <a href="https://simplesamlphp.org/" target="_blank">More information</a>'; // Info deactivatead
$string[8][119] = 'By activating this function, the customer must authenticate himself via your ADFS in order to purchase a ticket. Note that the simpleSAML configuration has to be done manually. If the configuration is incorrect, the entire ordering process via the store for this ticket group will no longer work. The authentication cannot be done by a third party via SDK. '; // Info activated
$string[8][120] = 'Request authentification'; // Input name
$string[8][121] = 'Request authentification for ticket purchase'; // Input title
$string[8][122] = 'Insert the array key of the ADFS array into the corresponding defined field of the form in order to transfer the data from your Active Directory. Fields left empty must be entered by the user himself. If all fields are defined, the user can only add a coupon. '; // ADFS custom text
$string[8][123] = 'E-Mail<abbr title="Required for purchase">*</abbr>'; // Input name
$string[8][124] = 'Update';

// SDK
$string[8][130] = 'Do you really want to renew the secret key?<br /><span style="color: #f0c564;">This action is only recommended if you suspect that this key has been misused or if you are not yet using it productively.</span> '; // Refres message

$string[8][131] = 'Secret key'; // Headline
$string[8][132] = 'In order to be able to make a request via SDK, you have to use this secret key. Note that this key is only available to this group. You can only edit, delete or read tickets that have been assigned to this group.'; // Info
$string[8][133] = 'IMPORTANT: Anyone who has this key can add, delete, edit and read tickets. <strong>Never</strong> publish this key and give the key only to people you trust. If you suspect that this key has been misused, renew it immediately.'; // Notice

$string[8][134] = 'SDK-Document'; // Headline
$string[8][135] = 'Download the required SDK document here.'; // Info
$string[8][136] = 'Download SDK-Document'; // Link button title
$string[8][137] = 'Download'; // Link button text

$string[8][138] = 'Easy to use'; // Headline
$string[8][139] = 'If you have no programming experience, you can do a simple implementation. You find the instructaions at <a href="%url_page%&view=%group%&section=5">Payment&#8594;Store</a> .'; // Info text

$string[8][140] = 'Documentation'; // Headline
$string[8][141] = 'The following information can be obtained, added and revised via the SDK.<br />Please note that this is only a small and undetailed documentation. A detailed description of the functions used can be found directly in the SDK document, which you can download above.'; // Info
$string[8][142] = 'Request Ticketinformations'; // SDK Code headline
$string[8][143] = 'Request Ticket-Token'; // SDK Code headline
$string[8][144] = 'Add Ticket'; // SDK Code headline
$string[8][145] = 'Revise Ticket'; // SDK Code headline
$string[8][146] = 'Remove Ticket'; // SDK Code headline
$string[8][147] = 'Restore Ticket'; // SDK Code headline
$string[8][148] = 'Send Ticket via mail'; // SDK Code headline
$string[8][149] = 'Get coupon-ID via name'; // SDK Code headline
$string[8][150] = 'Check coupon'; // SDK Code headline
$string[8][151] = 'Ticketprice with coupon'; // SDK Code headline
$string[8][152] = 'Group informations'; // SDK Code headline
$string[8][153] = 'Used Tickets'; // SDK Code headline
$string[8][154] = 'Available Tickets'; // SDK Code headline
$string[8][155] = 'Tickets per user'; // SDK Code headline
$string[8][156] = 'Request gateway'; // SDK Code headline
$string[8][157] = 'remove gateway'; // SDK Code headline
$string[8][158] = 'Transaction infos'; // SDK Code headline
$string[8][159] = 'Send payment reminder'; // SDK Code headline

// No access to group
$string[8][160] = 'No access to the group <strong>#%group%</strong>';

// Add
$string[8][170] = 'Return to the previous page';
$string[8][171] = 'Add group';
$string[8][172] = 'Add';

// Actions
$string[8][200] = 'The group section <strong>General</strong> could be revised <strong>successfully</strong>.'; // Update section
$string[8][201] = 'The group section <strong>General</strong> could <strong>not</strong> be revised.'; // Update section
$string[8][202] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][203] = 'The group section <strong>Form</strong> could be revised <strong>successfully</strong>.'; // Update section
$string[8][204] = 'The group section <strong>Form</strong> could <strong>not</strong> be revised.'; // Update section
$string[8][205] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][206] = 'The group section <strong>Ticket</strong> could be revised <strong>successfully</strong>.'; // Update section
$string[8][207] = 'The group section <strong>Ticket</strong> could <strong>not</strong> be revised.'; // Update section
$string[8][208] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][209] = 'The group section <strong>Mail</strong> could be revised <strong>successfully</strong>.'; // Update section
$string[8][210] = 'The group section <strong>Mail</strong> could <strong>not</strong> be revised.'; // Update section
$string[8][211] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][212] = 'The group section <strong>Payment</strong> could be revised <strong>successfully</strong>.'; // Update section
$string[8][213] = 'The group section <strong>Payment</strong> could <strong>not</strong> be revised.'; // Update section
$string[8][214] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][215] = 'The secret key could be renewed <strong> successfully </strong>.'; // Update section
$string[8][216] = 'The secret key could <strong>not</strong> be renewed.'; // Update section
$string[8][217] = 'You have <strong>no authorization</strong> to perform this action'; // Update section

$string[8][218] = 'Are you sure you want to remove the group #%id% (%name%)?'; // Remove request
$string[8][219] = 'The group #%id% was successfully removed.'; // Remove group
$string[8][220] = 'The group #%id% could not be removed.'; // Remove group
$string[8][221] = 'You have <strong>no authorization</strong> to perform this action'; // Remove group

$string[8][222] = 'The group could be created <strong>successfully</strong>. <a href="%url_page%&view=%id%" class="redirect">Manage group</a></strong>'; // Add group
$string[8][223] = 'Unfortunately the group <strong>could not</strong> be created.'; // Add group
$string[8][224] = 'You have <strong>no authorization</strong> to perform this action'; // Add group

/**
 * Page 9
 */
// List
$string[9][0] = 'Name, Coupon-ID, Group-ID';
$string[9][1] = 'Name';
$string[9][2] = 'Usage';
$string[9][3] = 'Discount';
$string[9][4] = 'Activity';
$string[9][5] = 'Name: %name%&#013;ID: %id%';
$string[9][6] = 'View coupondetails';
$string[9][7] = 'Delete coupon';
$string[9][8] = 'Next';
$string[9][9] = 'Last';

// Single
$string[9][10] = 'Name: %name%&#013;ID: %id%';
$string[9][11] = 'Return to the previous page';
$string[9][12] = 'Name';
$string[9][13] = 'Discount';
$string[9][14] = 'Used';
$string[9][15] = 'Available use';
$string[9][16] = '<abbr title="Format: YYYY-MM-DD HH::ii:ss, however any format can be used &#13; Leave blank to use group data">Start Date</abbr>';
$string[9][17] = '<abbr title="Format: YYYY-MM-DD HH::ii:ss, however any format can be used &#13; Leave blank to use group data">End Date</abbr>';
$string[9][18] = 'Update';

// Remove
$string[9][20] = 'Do you really want to delete the coupon <strong>#%id%</strong> with the name <strong>%name%</strong>?';
$string[9][21] = 'The coupon could be removed <strong>successfully</strong>';
$string[9][22] = 'The coupon could <strong>not</strong> be removed';
$string[9][23] = 'You have <strong>no authorization</strong> to perform this action';

// Add
$string[9][30] = 'Choose group';
$string[9][31] = 'Absolute';
$string[9][32] = 'Add';
$string[9][33] = 'Available tickets: %availableTickets%/%maxTickets%&#013;Tickets per user: %tpu%&#013;Preis: %price% %currency% + %vat%% VAT.&#013;';

$string[9][34] = 'The name and group are required to add a coupon';
$string[9][35] = 'This coupon <strong>already exists</strong>';
$string[9][36] = 'The coupon was <strong>not</strong> added';
$string[9][37] = 'The coupon was added <strong>successfully</strong>';
$string[9][38] = 'You have <strong>no authorization</strong> to perform this action';

// Update
$string[9][40] = 'The coupon was <strong>successfully</strong> revised';
$string[9][41] = 'The coupon could <strong>not</strong> be revised';
$string[9][42] = 'You have <strong>no authorization</strong> to perform this action';

/**
 * Page 10
 */
$string[10][0] = 'Double click in text field to edit. The changes are saved automatically.'; // Admin top bar
$string[10][1] = 'You have <strong>no authorization</strong> to perform this action'; // Update text no access

/**
 * Page 11
 */
$string[11][0] = '🎥 Unable to access video stream (please make sure you have a webcam enabled)'; // Webcam message
$string[11][1] = '⌛ Loading video...'; // Loading message

$string[11][2] = 'This ticket does not exist. Please report to the staff'; // Ticket does not exist
$string[11][3] = 'This ticket has not been paid. Please report to the staff '; // Not payed
$string[11][4] = 'Welcome'; // Ticket activated successfuly
$string[11][5] = 'An error occurred while redeeming the ticket. Please report to the staff'; // Error while activating
$string[11][6] = 'This ticket has already been used. Please report to the staff'; // Ticket already activated
$string[11][7] = 'This ticket has been blocked. Please report to the staff'; // Ticket blocked
$string[11][8] = 'An unknown error has occurred. Please report to the staff'; // Unknown error
$string[11][9] = 'You have <strong>no authorization</strong> to perform this action'; // No access
$string[11][10] = 'Understood'; // Button Text

// Ticket not found
$string["scanner"][0] = 'TKTDATA'; // Image title
$string["scanner"][1] = 'The requested ticket does not exist!'; // Error message
$string["scanner"][2] = 'Cancel'; // Button

// Ticket infos
$string["scanner"][3] = 'Ticket used at %date%, payment not made.'; // Payment and ticket state
$string["scanner"][4] = 'Blocked ticket, already paid.'; // Payment and ticket state
$string["scanner"][5] = 'Payment not made.'; // Payment and ticket state
$string["scanner"][6] = 'Ticket voided on %date%.'; // Payment and ticket state
$string["scanner"][7] = 'Ticket blocked.'; // Payment and ticket state
$string["scanner"][8] = 'TKTDATA'; // Title
$string["scanner"][9] = 'E-Mail:'; // Name
$string["scanner"][10] = 'Redeem'; // Button
$string["scanner"][11] = 'Cancel'; // Button

$string["scanner"][12] = 'The ticket was <strong>successfully voided</strong>'; // Message
$string["scanner"][13] = 'The ticket was <strong>not voided</strong>'; // Message
$string["scanner"][14] = 'You have <strong>no authorization</strong> to perform this action'; // Message

/**
 * Page 12
 */
$string[12][0] = 'TicketToken';

/**
 * Page 13
 */
$string[13][0] = 'Archiving'; // Export button
$string[13][1] = '# Visitors'; // label
$string[13][2] = 'Progress'; // Diagramm title
$string[13][3] = 'Entrances'; // Diagramm title
$string[13][4] = 'Exits'; // Diagramm title
$string[13][5] = 'Current visitors'; // Title
$string[13][6] = 'Current trend'; // Title
$string[13][7] = 'Do you really want to archive the current status?';
$string[13][8] = 'Your data has been successfully archived';
$string[13][9] = 'An error occurred while archiving your data';
$string[13][10] = 'You have <strong>no authorization</strong> to perform this action ';

/**
 * Page 14
 */
$string[14][0] = 'Archive'; // Select headline
$string[14][1] = 'Export'; // Export button
$string[14][2] = '# Visitors'; // label
$string[14][3] = 'Progress'; // Diagramm title
$string[14][4] = 'Entrances'; // Diagramm title
$string[14][5] = 'Exits'; // Diagramm title

$string[14][6] = 'Sec.'; // Diagramm labels
$string[14][7] = 'Min.'; // Diagramm labels
$string[14][8] = 'Hrs.'; // Diagramm labels
$string[14][9] = 'Days'; // Diagramm labels
$string[14][10] = 'Weeks'; // Diagramm labels
$string[14][11] = 'Mo.'; // Diagramm labels
$string[14][12] = 'Year'; // Diagramm labels
$string[14][13] = 'Jz.'; // Diagramm labels

/**
 * Page 15
 */
$string[15][0] = 'Visitors'; // Visitors
$string[15][1] = 'It couldn\'t be counted up.'; // Up error message
$string[15][2] = 'You have <strong>no authorization</strong> to perform this action'; // Up error message
$string[15][3] = 'It couldn\'t be counted down.'; // Down error message
$string[15][4] = 'You have <strong>no authorization</strong> to perform this action'; // Down error message

/**
 * Page 16
 */
// View
$string[16][0] = 'PickUp'; // Img alt
$string[16][1] = 'Pick up transaction?'; // Img title
$string[16][2] = 'state'; // Img alt
$string[16][3] = 'Confirm payment'; // Img title
$string[16][4] = 'Refund'; // Img alt
$string[16][5] = 'Refund amount'; // Img tittle
$string[16][6] = 'Refund'; // Button info text
$string[16][7] = 'Trash'; // Img alt
$string[16][8] = 'Remove transaction'; // Img title
$string[16][9] = 'Return to the previous page'; // Top nav title
$string[16][10] = 'Transaction'; // Details headline
$string[16][11] = 'E-Mail:'; // Details
$string[16][12] = 'Payment-ID:'; // Details
$string[16][13] = 'Amount:'; // Details
$string[16][14] = 'Effectively:'; // Details
$string[16][15] = 'Refunded:'; // Details
$string[16][16] = 'Fees:'; // Details
$string[16][17] = 'State:'; // Details
$string[16][18] = 'Payment expected, picked up'; // Pickup state
$string[16][19] = 'Payment expected'; // Pickup state
$string[16][20] = 'Unclaimed'; // Pickup state
$string[16][21] = 'Picked up'; // Pickup state
$string[16][22] = 'Payment type:'; // Detail
$string[16][23] = 'Online'; // Payment type
$string[16][24] = 'cash'; // Payment type
$string[16][25] = 'Payment time:'; // Detail

$string[16][26] = 'Products'; // Products headline
$string[16][27] = 'Tip'; // Tip money info
$string[16][28] = 'Unknown name'; // Name of product not found
$string[16][29] = 'Total:'; // Total info

// List
$string[16][30] = 'Email, Payment-ID, Payment time'; // Search form placeholder
$string[16][31] = 'Picked up without payment'; // Pickup states
$string[16][32] = 'Payment expected'; // Pickup states
$string[16][33] = 'Pick up expected'; // Pickup states
$string[16][34] = 'Email'; // Headline
$string[16][35] = 'Price'; // Headline
$string[16][36] = 'Date'; // Headline
$string[16][37] = 'Activity'; // Headline
$string[16][38] = 'Payment expected. Product already picked up.'; // Pickup states title
$string[16][39] = 'Payment expected.'; // Pickup states title
$string[16][40] = 'Pick up expected'; // Pickup states title
$string[16][41] = 'Picked up'; // Pickup states title
$string[16][42] = 'View transaction'; // Link title
$string[16][43] = 'Remove transaction'; // Link title
$string[16][44] = 'Last'; // Footer nav
$string[16][45] = 'Next'; // Footer nav

$string[16][46] = 'The transaction <strong>%email% (#%id%)</strong> was deleted <strong>successfully</strong>.'; // Remove message success
$string[16][47] = 'The transaction <strong>%email% (#%id%)</strong> could <strong>not</strong> be deleted.'; // Remove message fail
$string[16][48] = 'You have <strong>no authorization</strong> to perform this action'; // No access

// Remove
$string[16][50] = 'Do you really want to delete the transaction <strong>%email% (#%id%)</strong>? ';

// No access to pub
$string[16][60] = 'You have no access to the pub (#%id%) <strong>%name%</strong> '; // Fullscreen message
$string[16][61] = 'Back'; // Fullscreen return button

// Ajax messages
$string[16][70] = 'Refund failed.';
$string[16][71] = 'Refund failed. %refund%';
$string[16][72] = 'This user does not have authorization for this action ';
$string[16][73] = 'Successful -%refund% %currency% reimbursed.';
$string[16][74] = 'Receipt of payment could not be confirmed ';


/**
 * Page 17
 */
// Default
$string[17][0] = 'Unfortunately, online payments are not entirely free, which is why <strong>%fee_absolute% %currency%</strong> and <strong>%fee_percent%%</strong> must be given away from the sales price.'; // Fees info
$string[17][1] = 'PRODUCTS'; // Top nav item
$string[17][2] = 'View products'; // Top nav item title
$string[17][3] = 'SETTINGS'; // Top nav item
$string[17][4] = 'Carry pubsettings'; // Top nav item title

$string[17][5] = 'PDF'; // Right menu alt
$string[17][6] = 'View the menu as a PDF'; // Right menu title
$string[17][7] = 'Visibility'; // Right menu alt
$string[17][8] = 'Enabled/Disable Tip';

$string[17][9] = 'Details'; // Form header
$string[17][10] = 'Description'; // Input name
$string[17][11] = 'Images'; // Form header
$string[17][12] = 'Logo'; // Input name
$string[17][13] = 'Click to select'; // Select info
$string[17][14] = 'Background image'; // Input name
$string[17][15] = 'Click to select'; // Select info
$string[17][16] = 'Update'; // Update

$string[17][17] = 'Productname, Price'; // Search form placeholder
$string[17][18] = 'Available'; // Availability types
$string[17][19] = 'Little available'; // Availability types
$string[17][20] = 'Sold out'; // Availability types

$string[17][21] = 'Name'; // headlines
$string[17][22] = 'Price'; // headlines
$string[17][23] = 'Activity'; // headlines
$string[17][24] = 'View productdetails'; // Top nav title
$string[17][25] = 'Remove product'; // Top nav title
$string[17][26] = 'A global product cannot be edited here'; // Product list info
$string[17][27] = 'This product does not appear in the menu'; // Product list info
$string[17][28] = 'Last'; // Table navigation
$string[17][29] = 'Next'; // Table navigation

// view
$string[17][30] = 'Visibility'; // Right menu alt
$string[17][31] = 'Change visibility'; // Right menu title
$string[17][32] = 'state'; //Right menu  alt
$string[17][33] = 'Determine product status '; //Right menu title
$string[17][34] = 'RMF'; // Remove alt
$string[17][35] = 'Remove product'; // Title action remove
$string[17][36] = 'Edit product'; // Title action edit
$string[17][37] = 'View Product'; // Title action view

$string[17][38] = 'Productname'; // Input name
$string[17][39] = 'Choose group'; // Input name
$string[17][40] = 'GO'; // Input name
$string[17][41] = 'Price'; // Input name
$string[17][42] = 'Product image'; // Input name
$string[17][43] = 'Click to select'; // Image headline
$string[17][44] = 'Update';
$string[17][45] = '&#9888; This is a global product and can only be edited by the administrator.'; // Global message info
$string[17][46] = 'Return to the previous page '; // Return button
$string[17][47] = 'You do not have access to the product (#%product%) %name%';

// Add
$string[17][48] = 'Add product'; // Input name
$string[17][49] = 'Create'; // Input name

// Remove
$string[17][50] = 'Do you really want to delete the product <strong>%name% (#%product%)</strong>?'; // Message

// Actions
$string[17][60] = 'You have no access to the pub (#%pub%) <strong>%name% </strong>'; // No access to pub message
$string[17][61] = 'Back'; // No access to pub, return button
$string[17][62] = 'The product could be <strong> successfully </strong>created.<strong> <a href="%url_page%&pub=%pub%&view_product=%product%" class="redirect">Manage product</a></strong>'; // Successfully added product
$string[17][63] = 'Unfortunately the product <strong>could not</strong> be created.'; // Error while adding product
$string[17][64] = 'You have <strong>no authorization</strong> to perform this action.'; // No access
$string[17][65] = 'The product <strong>%name% (#%product%) </strong>has been <strong> successfully</strong> revised.'; // Update of product successfull
$string[17][66] = 'The product <strong>%name% (#%product%)</strong> could <strong>not</strong> be revised.'; // Update of product failed
$string[17][67] = 'You have <strong> no authorization </strong> to perform this action '; // No access
$string[17][68] = 'The product <strong>%name% (#%product%)</strong> was <strong>successfully</strong> deleted.'; // Removed product successful
$string[17][69] = 'The product <strong>%name% (#%product%)</strong> could <strong>not</strong> be deleted.'; // Removed product failed
$string[17][70] = 'You have <strong>no authorization</strong> to perform this action.'; // No access
$string[17][71] = 'The pub <strong>%name% (#%pub%)</strong> has been <strong>successfully</strong> redesigned.';
$string[17][72] = 'The pub <strong>%name% (#%pub%)</strong> could <strong>not</strong> be redesigned.';
$string[17][73] = 'You have <strong>no authorization</strong> to perform this action.';

/**
 * Page 18
 */
// List pubs
$string[18][0] = 'Name, ID'; // Search form placeholder
$string[18][1] = 'Name'; // Headlines
$string[18][2] = 'Activity'; // Headlines
$string[18][3] = 'View pubdetails'; // Action title
$string[18][4] = 'Remove pub'; // Action title
$string[18][5] = 'Last'; // Table navigation
$string[18][6] = 'Next'; // Table navigation

// List products
$string[18][10] = 'Name, Price'; // Search form placeholder
$string[18][11] = 'Name'; // Headlines
$string[18][12] = 'Price'; // Headlines
$string[18][13] = 'Activity'; // Headlines
$string[18][14] = 'Show product details'; // Action title
$string[18][15] = 'Remove product'; // Action title
$string[18][16] = 'Last'; // Table navigation
$string[18][17] = 'Next'; // Table navigation

// Single pub
$string[18][20] = 'Generally'; // Top nav item
$string[18][21] = 'Rights'; // Top nav item
$string[18][22] = 'Manage pub'; // Top nav item title
$string[18][23] = 'Manage rights'; // Top nav item title

$string[18][24] = 'PDF'; // Right menu alt pdf
$string[18][25] = 'View the menu as a PDF'; // Right menu title pdf
$string[18][26] = 'Visibility'; // Right menu alt tip money
$string[18][27] = 'Show / hide tips'; // Right menu title tip money

$string[18][28] = 'Generally'; // generally title
$string[18][29] = 'Pubname'; // Input name
$string[18][30] = 'Description'; // Input name
$string[18][31] = 'Images'; // images title
$string[18][32] = 'Click to select'; // Image input select info
$string[18][33] = 'Logo'; // Image name
$string[18][34] = 'Backgroundimage'; // Image name
$string[18][35] = 'Payrexx'; // Payrexx title
$string[18][36] = 'To be able to receive a payment directly online, you need an account at <a href="https://www.payrexx.com" title="Visit the website of Payrexx" target="_blank">Payrexx</a> . Payrexx is a Swiss company. If you would like to have Stripe as your <abbr title = "Payment service provider">PSP</abbr>, you can visit <a href = "https://www.payrexx.com/de/resources/knowledge-hub/payrexx-for-stripe/ "target =" _ blank ">this page</a>.'; // Payrexx info
$string[18][37] = 'Payrexx Instance'; // Input name
$string[18][38] = 'Payrexx Secret'; // Input name
$string[18][39] = 'Currency'; // Input name
$string[18][40] = 'Fees'; // Fees title
$string[18][41] = 'The provider charges corresponding fees for each transaction. Please define here which fees your payment provider requires in order to receive the evaluation correctly. The two fees are added together and offset accordingly. This does not change the product prices. '; // Fees info
$string[18][42] = 'Absolute fees'; // Input name
$string[18][43] = 'Percentage Gebühren'; // Input name
$string[18][44] = 'Update'; // Button value

$string[18][45] = 'Username'; // Input name
$string[18][46] = 'Email'; // Input name
$string[18][47] = 'Write | Read'; // Input name
$string[18][48] = '%user% has write access to this pub'; // Toggle title
$string[18][49] = '%user% has no write access to this pub'; // Toggle title
$string[18][50] = '%user% has reading access to this pub'; // Toggle title
$string[18][51] = '%user% has no reading access to this pub'; // Toggle title
$string[18][52] = 'The rights could not be added.'; // Fail info
$string[18][53] = 'You have <strong>no authorization</strong> to perform this action.'; // Fail info

//Single product
$string[18][60] = 'Return to the previous page'; // Top nav title
$string[18][61] = 'Edit product'; // Headline
$string[18][62] = 'View product'; // Headline
$string[18][63] = 'Productname'; // Input name
$string[18][64] = 'GO'; // Input select button
$string[18][65] = 'Price'; // Input name
$string[18][66] = 'The standard currency is used in each case, unless another currency is specified for an pub.'; // Abbr info price
$string[18][67] = 'Productimage'; // Input name
$string[18][68] = 'Click to select'; // Input name
$string[18][69] = 'Update'; // Input name

// Actions
$string[18][70] = 'Do you really want to delete the pub <strong>%name% (#%id%)</strong>? '; // Remove pub message
$string[18][71] = 'Do you really want to delete the product <strong>%name% (#%id%)</strong>?'; // Remove product message

$string[18][72] = 'The pub <strong>%name% (#%id%)</strong> has been <strong>successfully</strong> redesigned.'; // Update pub success
$string[18][73] = 'The pub <strong>%name% (#%id%)</strong> could <strong>not</strong> be redesigned.'; // Update pub fail
$string[18][74] = 'You have <strong>no authorization</strong> to perform this action'; // Update pub no access

$string[18][75] = 'The product <strong>%name% (#%id%)</strong> has been <strong>successfully</strong> redesigned.'; // Update product success
$string[18][76] = 'The product <strong>%name% (#%id%)</strong> could <strong>not</strong> be revised.'; // Update product fail
$string[18][77] = 'You have <strong>no authorization</strong> to perform this action'; // Update product no access

$string[18][78] = 'The pub could be created <strong>successfully</strong>. <strong><a href="%url_page%&view_product=%productid%" class="redirect">Manage product</a></strong>'; // Add product success
$string[18][79] = 'Unfortunately, the pub <strong>could not</strong> be created.'; // Add product fail
$string[18][80] = 'You have <strong>no authorization</strong> to perform this action'; // Add product no access
$string[18][81] = 'Add product'; // Add product
$string[18][82] = 'Choose group'; // Select info
$string[18][83] = 'Create'; // Create product
$string[18][84] = 'Create'; // Create pub

// List pubs and products actions
$string[18][90] = 'PUBS';
$string[18][91] = 'List pubs';
$string[18][92] = 'GLOBAL PRODUCTS';
$string[18][93] = 'List products';
$string[18][94] = 'The product <strong>%name% (#%id%)</strong> was <strong> successfully </strong> deleted.';
$string[18][95] = 'The product <strong>%name% (#%id%)</strong> could <strong>not</strong> be deleted.';
$string[18][96] = 'The pub <strong>%name% (#%id%)</strong> was <strong>successfully</strong> deleted.';
$string[18][97] = 'The pub <strong>%name% (#%id%)</strong> could <strong>not</strong> be deleted.';

/**
 * Page 19
 */
// List view
$string[19][0] = 'Username, Prename, Name, Ticketinfo'; // Search form placeholder
$string[19][1] = 'Username'; // Headlines
$string[19][2] = 'E-Mail'; // Headlines
$string[19][3] = 'Activity'; // Headlines
$string[19][4] = 'Last'; // Table navigation
$string[19][5] = 'Next'; // Table navigation

// Sinlgle view
$string[19][10] = 'Userdata'; // Input placeholder
$string[19][11] = 'Username'; // Input placeholder
$string[19][12] = 'Name'; // Input placeholder
$string[19][13] = 'E-Mail'; // Input placeholder
$string[19][14] = 'Select language'; // Input placeholder

$string[19][15] = 'Access rights'; // Page access title
$string[19][16] = 'Write permission'; // Page access title
$string[19][17] = 'Read permission'; // Page access title
$string[19][18] = 'Set write permission'; // Page access title
$string[19][19] = 'Set read permission'; // Page access title

$string[19][20] = 'Send credentials to users'; // Send access checkbox
$string[19][21] = 'Send mail to new user'; // Send access checkbox

$string[19][22] = 'UPDATE'; // Update
$string[19][23] = 'Update user'; // Update title

$string[19][24] = 'Return to the previous page '; // Return button


// Update user
$string[19][50] = 'Your change was <strong>successfully</strong> implemented.';
$string[19][51] = 'Your change could <strong>not</strong> be implemented.';
$string[19][52] = 'You have <strong>no authorization</strong> to perform this action.';

// Add user
$string[19][55] = 'The user was added <strong>successfully</strong>.';
$string[19][56] = 'The user could <strong>not</strong> be added.';
$string[19][57] = 'You have <strong>no authorization</strong> to perform this action.';

// Remove user
$string[19][60] = 'Do you want to permanently remove the user %username% (%user%)?';
$string[19][61] = 'The user (%user%) was successfully removed.';
$string[19][62] = 'The user (%user%) could not be removed.';
$string[19][63] = 'You have <strong>no authorization</strong> to perform this action.';

/**
 * Page 20
 */
// Page
$string[20][0] = 'User'; // Search form placeholder
$string[20][1] = 'Initiator'; // Headlines
$string[20][2] = 'Activity'; // Headlines
$string[20][3] = 'Date'; // Headlines
$string[20][4] = 'Recovery details'; // Headlines
$string[20][5] = 'Revision details #%id%'; // Action title
$string[20][6] = 'Last'; // Table navigation
$string[20][7] = 'Next'; // Table navigation
$string[20][8] = 'Previous version'; // Single action view
$string[20][9] = 'Changed version'; // Single action view
$string[20][10] = 'Reset changes'; // Single action view
$string[20][11] = 'Your change was <strong>successfully</strong> implemented.'; // Action response
$string[20][12] = 'Your change could <strong>not</strong> be implemented.'; // Action response
$string[20][13] = 'You have <strong>no authorization</strong> to perform this action.'; // Action response
$string[20][14] = 'Return to the previous page '; // Return button

// Messages
$string[20][100] = 'Updated profile of %user%'; // user.php
$string[20][101] = 'Removed profile of %user%'; // user.php
$string[20][102] = 'Removed acces for %user%'; // user.php
$string[20][103] = 'Added profile of %user%'; // user.php
$string[20][104] = 'Version %version% restored'; // user.php

$string[20][110] = 'New pub added (%name%)'; // pu.php
$string[20][111] = 'New global product (%name%) added'; // pu.php
$string[20][112] = 'New product (%name%) added for pub %pbu% '; // pu.php
$string[20][113] = 'Added access to pub #%pub% for user (%user%) %name% '; // pu.php
$string[20][114] = 'Pub #%pub% (%name%) revised'; // pu.php
$string[20][115] = 'Pub #%pub% (%name%) removed '; // pu.php
$string[20][116] = 'Removed access for the user #%user% (%username%) for the pub #%pub% (%pubname%) '; // pu.php

$string[20][120] = 'New global product (%name%) added'; // products.php
$string[20][121] = 'New product (%name%) added for pub #%pub%'; // products.php
$string[20][122] = 'Product #%id% (%name%) revised'; // products.php
$string[20][123] = 'Product #%id% (%name%) deleted'; // products.php
$string[20][124] = 'Product #%id% (%name%) added to the menu (#%pub%)'; // products.php
$string[20][125] = 'Product #%id% (%name%) removed from the menu (#%pub%)'; // products.php
$string[20][126] = 'Availability of product #%id% (%name%) adjusted'; // products.php

$string[20][130] = 'Added new Transaction #%id%'; // transaction.php
$string[20][131] = 'Transaction #%id% revised'; // transaction.php
$string[20][132] = 'Transaction #%id% removed'; // transaction.php

$string[20][140] = 'Coupon (%name%) added to group #%group%'; // coupon.php
$string[20][141] = 'Coupon #%id% revised'; // coupon.php
$string[20][142] = 'Coupon #%id% removed'; // coupon.php

$string[20][150] = 'New Ticket (%ticketToken%) add'; // ticket.php
$string[20][151] = 'Ticket (%ticketToken%) revised'; // ticket.php
$string[20][152] = 'Ticket (%ticketToken%) removed'; // ticket.php
$string[20][153] = 'Ticket (%ticketToken%) restored'; // ticket.php
$string[20][154] = 'Ticket (%ticketToken%) voided'; // ticket.php
$string[20][155] = 'Ticket (%ticketToken%) manually reactivated'; // ticket.php

$string[20][160] = 'Revised group #%id% [General]'; // groups.php
$string[20][161] = 'Revised group #%id% [Form]'; // groups.php
$string[20][162] = 'Revised group #%id% [Ticket]'; // groups.php
$string[20][163] = 'Revised group #%id% [Mail]'; // groups.php
$string[20][164] = 'Revised group #%id% [Payment]'; // groups.php
$string[20][165] = 'Renewed secret key of group #%id%'; // groups.php
$string[20][166] = 'Added group %name%'; // groups.php
$string[20][167] = 'Removed group #%id% '; // groups.php

/**
 * Profil
 */
 // Update password
 $string["profile"][0] = 'The password was <strong>successfully</strong> changed.';
 $string["profile"][1] = 'The password could <strong>not</strong> be changed.';

 // Update infos
 $string["profile"][2] = 'Your change was <strong>successfully</strong> implemented.';
 $string["profile"][3] = 'Your change could <strong>not</strong> be implemented.';

 // Inputs
 $string["profile"][10] = 'Userdata';
 $string["profile"][11] = 'Username';
 $string["profile"][12] = 'Name';
 $string["profile"][13] = 'E-Mail';
 $string["profile"][14] = 'Select language';

 $string["profile"][15] = 'Change password';
 $string["profile"][16] = 'New password';
 $string["profile"][17] = 'Confirm password';

 // Confirm form
 $string["profile"][18] = 'UPDATE';

 ?>
