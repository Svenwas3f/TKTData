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
