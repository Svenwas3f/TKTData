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
