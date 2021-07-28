<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2021
 * @Purpose: File to manage language
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 */
class Language {
  /**
   * This function gets translated string or returns null if no string found
   *
   * $id: String id located in the language file
   * $replacement: multiple array for dynamical content, default null
   * $p: Page where string is requested, default $page
   * $user: User that wants to see translation, default $current_user
   */
  public static function string( $id, $replacement = null, $p = null, $user = null ) {
    // Get globals
    global $current_user, $page;

    // Generate variables
    $user = $user ?? $current_user;
    $p = $p ?? $page;

    // Check class
    if(class_exists('Plugin')) {
      // Check if page is plugin or not
      $plugin = new Plugin();
    }

    // Check if page is plugin or not
    // $plugin = new Plugin();

    if( isset($plugin) && $plugin->is_pluginpage( $p ) ) {
      // Generate path to language file
      $language_file_path = dirname(__FILE__, 2) . "/plugins/" . $plugin->get_page( intval( $p ) )["plugin"] . "/lang/" . Language::user_preference( $user ) . ".php";

      // Check if file exist
      if(! file_exists( $language_file_path )) {
        // Get first accessable language
        $language_file_path = glob(dirname(__FILE__, 2) . "/plugins/" . $plugin->get_page( intval( $p ) )["plugin"] . "/lang/*.php")[0];
      }
    }else {
      // Generate path to language file
      $language_file_path = dirname(__FILE__, 2) . "/lang/" . Language::user_preference( $user ) . ".php";
      // Check if file exist
      if(! file_exists( $language_file_path )) {
        // Get first accessable language
        $language_file_path = glob(dirname(__FILE__, 2) . "/lang/*.php")[0];
      }
    }

    // Check language file by user preference
    if( file_exists( $language_file_path )) {
      // Get content
      $language_package = fopen( $language_file_path, "r" );
      $language_string_package = fread( $language_package, filesize($language_file_path) ); // Get content of file
      fclose( $language_package );

      // Replace php tags
      $language_string_package = str_replace('<?php', '', $language_string_package);
      $language_string_package = str_replace('?>', '', $language_string_package);

      // Convert language string package to php
      eval( $language_string_package );

      // Get string
      $string = $string[$p][$id] ?? null;

      // Check if there are replaceable parts
      if(! is_null($replacement) ) {
        foreach( $replacement as $search=>$replace ) {
          $string = str_replace( $search, $replace, $string );
        }
      }
      // Return string
      return $string;
    }else {
      return false;
    }
  }

  /**
   * This functions gets the users language preference
   *
   * $user: User ID or by default (null) $current_user
   */
  public static function user_preference( $user = null) {
    // Get globals
    global $current_user;

    // Generate variables
    $language = new User();
    $language->user = $user ?? $current_user;

    // Get user language selection
    return $language->values()["language"] ?? DEFAULT_LANGUAGE;
  }

  /**
   * This function lists all languages of the system
   */
  public function all() {
    // Create array
    $languages = array();

    // Scann directory
    foreach( glob( dirname(__FILE__, 2) . "/lang/*.php") as $file ) {
      // Get content
      $language_package = fopen( $file, "r" );
      $language_string_package = fread( $language_package, filesize($file) ); // Get content of file
      fclose( $language_package );

      // Replace php tags
      $language_string_package = str_replace('<?php', '', $language_string_package);
      $language_string_package = str_replace('?>', '', $language_string_package);

      // Convert language string package to php
      eval( $language_string_package );

      // Push to array
      array_push( $languages, array(
        "code" => $language_code,
        "loc" => $language_loc,
        "int" => $language_int,
      ) );
    }

    // Return array
    return $languages;
  }
}
