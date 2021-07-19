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


/**
 * Profil
 */
 // Update password
 $string["profile"][0] = 'The password was <strong> successfully </strong> changed.';
 $string["profile"][1] = 'The password could <strong> not </strong> be changed.';

 // Update infos
 $string["profile"][2] = 'Your change was <strong> successfully </strong> implemented.';
 $string["profile"][3] = 'Your change could <strong> not </strong> be implemented.';

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
