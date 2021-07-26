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
 * ^Media hub
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
 * Page 17
 */
// Default
$string[17][0] = 'Unfortunately, online payments are not entirely free, which is why <strong>%fee_absolute%%currency%</strong> and <strong>%fee_percent%% </strong> must be given away from the sales price.'; // Fees info
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

// Messages
$string[20][100] = 'Updated profile of %user%';
$string[20][101] = 'Removed profile of %user%';
$string[20][102] = 'Removed acces for %user%';
$string[20][103] = 'Added profile of %user%';
$string[20][103] = 'Version %version% restored';

$string[20][110] = 'New pub added (%name%)';
$string[20][111] = 'New global product (%name%) added';
$string[20][112] = 'New product (%name%) added for pub %pbu% ';
$string[20][113] = 'Added access to pub #%pub% for user (%user%) %name% ';
$string[20][114] = 'Pub #%pub% (%name%) revised';
$string[20][115] = 'Pub #%pub% (%name%) removed ';
$string[20][116] = 'Removed access for the user #%user% (%username%) for the pub #%pub% (%pubname%) ';

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
