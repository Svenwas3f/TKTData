<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: April 2021
 * @Purpose: Manage plugins
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $plugin_name: Folder name of plugin
 * $reserved_mainpages: Reserved numbers
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd throug the function are written after the function name inround brackets ().
 *
 * Plugin->all ()
 *
 * Plugin->check_plugins ()
 *
 * Plugin->add_page ( $array ) [plugin_name]
 *
 * Plugin->add_subpage ( $array ) [plugin_name]
 *
 * Plugin->remove_page ( $mainpage ) [$reserved_mainpages]
 *
 * Plugin->get_page ( $page_name ) [$plugin_name]
 *
 * Plugin->get_subpage ( $page_name, $mainpage ) [$plugin_name]
 *
 */

class Plugin {
  //Variable
  public $plugin_name;
  private $reserved_mainpages = array(1,2,3,4,5); //By default set. Cannot be removed

  /**
   * Creates the default Plugin name
   * works only if the class plugin is executed in /plugins/plugin-name/functions.php
   */
  function __construct() {
    //get folder name
    $this->plugin_name = basename(dirname(debug_backtrace()[0]["file"])); //Gets plugin name automatically if executed in /plugin/plugin-folder
  }

  /**
   * Lists all valid plugin names
   */
  function all() {
    //Get plugins path
    $plugins_path = glob( dirname(__FILE__, 2) . "/plugins/*" , GLOB_ONLYDIR );

    //get array names
    return array_map(function($v) {
      return basename($v);
    }, $plugins_path);
  }

  /**
   * Checks valid plugins and removes deleted plugins out of the db
   *
   * returns true or false
   */
  function check_plugins() {
    //Get database connection
    $conn = Access::connect();

    //Get all plugins in the db
    $db_plugins = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu=0 AND id NOT IN (" . implode(",", $this->reserved_mainpages) . ")");
    $db_plugins->execute();
    $db_list = $db_plugins->fetchAll( PDO::FETCH_ASSOC );

    //non active plugins
    $deleted_plugins = array_diff( array_column($db_list, "plugin"), $this->all() );

    //Remove
    foreach( $deleted_plugins as $to_be_deleted ) {
      //Get main page id
      $mainpage = $conn->prepare("SELECT id FROM " . MENU  . " WHERE plugin=:plugin AND submenu=0");
      $mainpage->execute(array(
        ":plugin" => $to_be_deleted,
      ));
      $mainpage = $mainpage->fetch( PDO::FETCH_NUM )[0];
      if(! $this->remove_page( $mainpage )) {
        return false;
      }
    }

    //All ok
    return true;
  }

  /**
   * Adds a page
   * requires: $plugin_name
   *
   * returns the mainpage id or false
   *
   * $page_infos = array(
   *    name
   *    layout (needs to be over 5)
   * )
   */
  function add_page( $page_infos ) {
    //Get database connection
    $conn = Access::connect();

    //Check if page already has been added
    $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE name=:name AND layout=:layout AND plugin=:plugin AND submenu=0");
    $page_check->execute(array(
      ":name" => $page_infos["name"],
      ":layout" => ($page_infos["layout"] > 5 ? $page_infos["layout"] : 6),
      ":plugin" => $this->plugin_name,
    ));
    if($page_check->rowCount() > 0) {
      return $page_check->fetch( PDO::FETCH_ASSOC )["id"];
    }

    //Create statement
    $page_add = $conn->prepare("INSERT INTO " . MENU . " (name, submenu, image, layout, plugin) VALUES (:name, 0, NULL, :layout, :plugin)");
    if($page_add->execute(array(
      ":name" => $page_infos["name"],
      ":layout" => ($page_infos["layout"] > 5 ? $page_infos["layout"] : 6),
      ":plugin" => $this->plugin_name,
    ))) {
      return $conn->lastInsertId();
    }else {
      return false;
    }
  }

  /**
   * Adds a subpage
   * requires: $plugin_name
   *
   * returns id or false
   *
   * $page_infos = array(
   *    mainpage
   *    name
   *    image
   *    layout
   * )
   */
  function add_subpage( $page_infos ) {
    //Get database connection
    $conn = Access::connect();

    //Check if page already has been added
    $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE name=:name AND submenu=:mainpage AND layout=:layout AND plugin=:plugin");
    $page_check->execute(array(
      ":name" => $page_infos["name"],
      ":mainpage" => $page_infos["mainpage"],
      ":layout" => ($page_infos["layout"] > 5 ? $page_infos["layout"] : 6),
      ":plugin" => $this->plugin_name,
    ));
    if($page_check->rowCount() > 0) {
      return $page_check->fetch( PDO::FETCH_ASSOC )["id"];
    }

    //Create statement
    $page_add = $conn->prepare("INSERT INTO " . MENU . " (name, submenu, image, layout, plugin) VALUES (:name, :mainpage, :image, :layout, :plugin)");
    if($page_add->execute(array(
      ":name" => $page_infos["name"],
      ":mainpage" => $page_infos["mainpage"],
      ":image" => $page_infos["image"],
      ":layout" => ($page_infos["layout"] > 5 ? $page_infos["layout"] : 6),
      ":plugin" => $this->plugin_name,
    ))) {
      return $conn->lastInsertId();
    }else {
      return false;
    }
  }

  /**
   * Removes a mainpage including its subpages
   * requires: $reserved_mainpages
   *
   * returns true if all pages are deleted
   * returns false if an error occured. This could be the error
   *    mainpage is set to one of the reserved pages
   *    was not able to remove pages
   *    was not able to remove user rights
   *
   * $mainpage = mainpage ID
   */
  function remove_page( $mainpage ) {
    //Check if mainpage is valid
    if(! is_bool(array_search( $mainpage, $this->reserved_mainpages ))) {
      return false;
    }

    //Get database connection
    $conn = Access::connect();

    /* Get all page ids */
    $pages_id = $conn->prepare("SELECT id FROM " . MENU  . " WHERE submenu=:mainpage");
    $pages_id->execute(array(":mainpage" => $mainpage));
    $pages_id = array_merge(
      array( $mainpage ),
      array_column($pages_id->fetchAll( PDO::FETCH_NUM ), "0"),
    );

    /* Remove pages out of menu */
    $pages_remove = $conn->prepare("DELETE FROM " . MENU  . " WHERE id=:id OR submenu=:submenu");
    if(! $pages_remove->execute(array(
      ":id" => $mainpage,
      ":submenu" => $mainpage,
    ))) {
      return false;
    }

    /* Remove pages out of user rights */
    $userrights_remove = $conn->prepare("DELETE FROM " . USER_RIGHTS . " WHERE page IN (" . implode(",", $pages_id ). ")");
    return $userrights_remove->execute(array());
  }

  /**
   * Gets ID of a page by name or reverse
   * requires: $plugin_name
   *
   * returns ID or false
   *
   * $page = Name or ID of page
   *
   */
  function get_page( $page ) {
    //Get database connection
    $conn = Access::connect();

    if( is_int($page) ) {
      //Get name
      $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE id=:id");
      $page_check->execute(array(
        ":id" => $page,
      ));
      return $page_check->fetch( PDO::FETCH_ASSOC );
    }else {
      //Get id
      $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE name=:name AND plugin=:plugin AND submenu=0");
      $page_check->execute(array(
        ":name" => $page_name,
        ":plugin" => $this->plugin_name,
      ));
      return $page_check->fetch( PDO::FETCH_ASSOC );
    }
  }

  /**
   * Gets ID of a sub page by name or reverse
   * requires: $plugin_name
   *
   * returns ID or false
   *
   * $page = Name or ID of page
   * $mainpage = Mainppage ID, required if $page is a Name
   *
   */
  function get_subpage( $page, $mainpage = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_int($page) ) {
      //Get name
      $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE id=:id");
      $page_check->execute(array(
        ":id" => $page,
      ));
      return $page_check->fetch( PDO::FETCH_ASSOC );
    }else {
      //Get id
      // mainpage required
      $page_check = $conn->prepare("SELECT * FROM " . MENU . " WHERE name=:name AND submenu=:mainpage AND plugin=:plugin");
      $page_check->execute(array(
        ":name" => $page_name,
        ":mainpage" => $mainpage,
        ":plugin" => $this->plugin_name,
      ));
      return $page_check->fetch( PDO::FETCH_ASSOC );
    }
  }

}
 ?>
