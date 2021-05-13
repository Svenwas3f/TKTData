<?php
///////////////// General /////////////////
// @Author: Sven Waser
// @System: TKTData
// @Version: 1.0
// @Published: July 2020
// @Purpose: This file is used to configure this system at your choice. Please do not make any changes in this file, while you use it in production
///////////////////////////////////////////

//Define system info
define("SYSTEM_VERSION", "1.0.0");

//Define system version name
define("SYSTEM_NAME", "Gauli");

//Set default time zone
//https://www.php.net/manual/en/timezones.php (unfinished list)
//https://en.wikipedia.org/wiki/List_of_tz_database_time_zones (Full detailed list)
define("SYSTEM_TIMEZONE", "Europe/Zurich");

//Set default email where mails will be sent from
//Ex: no-reply@company.com
define("EMAIL", "no-reply@tktdata.ch");

//Define path to ini file
//This file contains database access informations
//Please use an absolute path that full system will work
//Ex. https://coderwall.com/p/91nk1a/php-database-connection-with-file-ini
define("PATH_TO_INI", "C:/xampp/htdocs/www.tktdata.ch/logindata.ini");

//Define string as salt key
//Do not change this value after your system is working!! This string will disable your selled tickets, yoru login informations and secret keys.
//Warning: Do not use any unicode chars like äöü. They are not supported.
//https://www.random.org/strings/?num=1&len=12&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new (Generator)
define("SALT_STRING", "YJqOQZ29VqqE");

//Set default currency
//Please use the ISO Code
//https://en.wikipedia.org/wiki/List_of_circulating_currencies
define("DEFAULT_CURRENCY", "CHF");

//Define path to simple saml config file
// Set value to null if you do not use SimpleSAMLphp otherwise enter a String with the path to the simpleSAMLphp autoload file (../../lib/_autoload.php)
//SimpleSAMLphp Webpage https://simplesamlphp.org/
define("SIMPLE_SAML_CONFIG", null);

//define restore availability after deleting a ticketgroup
//True: Restore images and database, images will not be deleted (Recomended in production)
//False: Restore only database (Recomended while testing)
define("FULL_RESTORE", true);

/////////////////////////////
// Define db table names
/////////////////////////////
//Menu elements stored
define('MENU', 'tktdata_menu');
//Users who are able to access the system
define('USERS', 'tktdata_user');
//Rights for user to access menuelements
define('USER_RIGHTS', 'tktdata_user_rights');
//Changelog of all actions made by a user
define('USER_ACTIONS', 'tktdata_user_actions');
//All tickets
define('TICKETS', 'tktdata_tickets');
//Groups of tickets
define('TICKETS_GROUPS', 'tktdata_tickets_groups');
//Coupons for tickets
define('TICKETS_COUPONS', 'tktdata_tickets_coupons');
//Livedata live
define('LIVEDATA', 'tktdata_livedata_live');
//Livdata Archiv
define('LIVEDATA_ARCHIVE', 'tktdata_livedata_archive');
//Checkout
define('CHECKOUT', "tktdata_checkout");
//Checkout price list
define('CHECKOUT_PRICE_LIST', "tktdata_checkout_price_list");
//Checkout access
define('CHECKOUT_ACCESS', "tktdata_checkout_access");

/////////////////////////////
// Define global vairalbes
/////////////////////////////
$mainPage = isset( $_GET["id"] )?$_GET["id"]:1; //Define min page
$page = isset( $_GET["sub"] )?$_GET["sub"]:$mainPage; //Define sub page, if no sub page return main page
$url = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"] . "/www.tktdata.ch/"; //Base URL
$url_page = $url . '?id=' . $mainPage . ((isset( $_GET["sub"] )) ? "&sub=" . $_GET["sub"] : "") . ((isset( $_GET["row-start"] )) ? "&row-start=" . $_GET["row-start"] : ""); //Create url with parameters (first id then if required sub and after that add row-start)
$current_user = (empty($_SESSION["user"])) ? null : $_SESSION["user"]; //Define current logged in user

//Set default timezone
date_default_timezone_set(SYSTEM_TIMEZONE);

/////////////////////////////
// PHPMailer / Mail functions
/////////////////////////////
require_once( dirname(__FILE__) . "/php/PHPMailer/Exception.php"); //Files need use

require_once( dirname(__FILE__) . "/php/PHPMailer/PHPMailer.php");

require_once( dirname(__FILE__) . "/php/mail.php");

/////////////////////////////
// Require classes
/////////////////////////////

/* Get access to database via Access::$conn */
require_once( dirname(__FILE__) . "/php/db-access.php" );

/* Plugin file */
require_once( dirname(__FILE__) . "/php/plugin.php");

/* Get cross php version support */
require_once( dirname(__FILE__) . "/php/cross-support.php");

/* Get action pop up file */
require_once( dirname(__FILE__) . "/php/action.php");

/* Get information about the user */
require_once( dirname(__FILE__) . "/php/user.php" );

/* Get infos about a ticket group */
require_once( dirname(__FILE__) . "/php/groups.php");

/* Get ticket informations */
require_once( dirname(__FILE__) . "/php/ticket.php" );

/* Get coupons information */
require_once( dirname(__FILE__) . "/php/coupon.php");

/* Get scanner informations */
require_once( dirname(__FILE__) . "/php/scanner.php" );

/* Get livedata informations */
require_once( dirname(__FILE__) . "/php/livedata.php" );

/* En and decrypt tocken static */
require_once( dirname(__FILE__) . "/php/crypt.php" );

/* Payrexx file */
require_once( dirname(__FILE__) . "/php/payrexx.php");

/////////////////////////////
// Require html
/////////////////////////////
require_once( dirname(__FILE__) . "/php/menu.php" );

require_once( dirname(__FILE__) . "/php/footer.php" );

/////////////////////////////
// Require plugins
/////////////////////////////
$plugins = glob( dirname(__FILE__) . "/plugins/*" , GLOB_ONLYDIR );

/* Check plugins */
$plugin = new Plugin();
$plugin->check_plugins();

/* Require function file */
foreach( $plugins as $plugin ) {
  /* Require function file */
  if(file_exists( $plugin . "/functions.php" )) {
    require_once( $plugin . "/functions.php" );
  }
}

?>
