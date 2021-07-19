<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage menu
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * Menu->display ()
 *
 * Menu->main_id ()
 *
 */
class Menu {
  /**
  * Returns navigation html
  */
  public function display(){
    //Require global variables
    global $url;
    global $mainPage;
    global $current_user;

    //Set connection variable
    $conn = Access::connect();

    //Select menu items
    $mainMenu = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu IS NULL OR submenu='' OR submenu='0' ORDER BY layout");
    $mainMenu->execute();

    // $subMenu = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu= :mainPage ORDER BY layout");
    // $subMenu->execute(array(":mainPage" => $mainPage));

    //Create html
    $nav = '<div class="mobile-nav-bar"><img src="' . $url . '/medias/icons/menu.svg" onclick="document.getElementsByTagName(\'nav\')[0].classList.toggle(\'open\')"/><a href="' . $url . '"><img src="' . $url . '/medias/logo/favicon-color-512.png" /></a></div>';
    $nav .= '<nav>';
      //Main nav
      $nav .= '<div class="container">';
      $nav .= '<a href="' . $url . '"><img src="' . $url . 'medias/logo/logo-fitted.png" class="logo"></a>'; //Display logo

      //Get throug every mainmenu element
      while($mainMenuElement = $mainMenu->fetch()) {
        //Check if user has access to this page
        if( User::w_access_allowed($mainMenuElement["id"], $current_user) || User::r_access_allowed($mainMenuElement["id"], $current_user) ){

          // Check if plugin
          $plugin = new Plugin();

          if( $plugin->is_pluginpage( $mainMenuElement["id"] ) ) {
            // Plugin menu element
            $nav .= '<div class="page-container">
                      <a onclick="openMenu(' . $mainMenuElement["id"] . ')"
                        title="Menu #' . $mainMenuElement["id"] . ' [' . (Language::string( $mainMenuElement["id"] , null, 'menu') ?? $mainMenuElement["name"]) . ']">' .
                        (Language::string( 'menu' , null, $mainMenuElement["id"]) ?? $mainMenuElement["name"]) .
                      '</a>';
          }else {
            // default menu element
            $nav .= '<div class="page-container">
                      <a onclick="openMenu(' . $mainMenuElement["id"] . ')"
                        title="Menu #' . $mainMenuElement["id"] . ' [' . (Language::string( $mainMenuElement["id"] , null, 'menu') ?? $mainMenuElement["name"]) . ']">' .
                        (Language::string( $mainMenuElement["id"] , null, 'menu') ?? $mainMenuElement["name"]) .
                      '</a>';
          }

          //Select submenus
          $subMenu = $conn->prepare("SELECT * FROM " . MENU . " WHERE submenu= :mainPage ORDER BY layout");
          $subMenu->execute(array(":mainPage" => $mainMenuElement["id"]));

          while($subMenuElement = $subMenu->fetch()) {
            if( User::w_access_allowed($subMenuElement["id"], $current_user) || User::r_access_allowed($subMenuElement["id"], $current_user) ) {
              // Check if open or closed
              $class = ($mainMenuElement["id"] == $mainPage) ? 'subOpen' : 'hidden'; //Define if open or closed

              //Check img
              if(file_exists( dirname(__FILE__, 2) . '/medias/icons/' .  $subMenuElement["image"] ) && is_file(dirname(__FILE__, 2) . '/medias/icons/' .  $subMenuElement["image"])) {
                $img_subpage = $url . 'medias/icons/' .  $subMenuElement["image"];
              }elseif(file_exists( dirname(__FILE__, 2) . '/plugins/' . $subMenuElement["plugin"] . '/' . $subMenuElement["image"] ) &&
                      is_file( dirname(__FILE__, 2) . '/plugins/' . $subMenuElement["plugin"] . '/' . $subMenuElement["image"] )) {
                $img_subpage= $url . 'plugins/' . $subMenuElement["plugin"] . '/' . $subMenuElement["image"];
              }else {
                $img_subpage= $url . 'medias/logo/favicon-color-512.png';
              }

              // Check if plugin
              if( $plugin->is_pluginpage( $subMenuElement["id"] ) ) {
                // Plugin sub menu element
                $nav .= '<div class="subpage-container ' . $class . ' mainpage'  . $mainMenuElement["id"] . '">
                          <a href="' . $url . '?id=' . $mainMenuElement["id"] . '&sub=' . $subMenuElement["id"] . '"
                          title="Submenu #' . $subMenuElement["id"] . ' [' . (Language::string( $subMenuElement["id"] , null, 'menu') ?? $subMenuElement["name"]) . ']">
                            <img src="' . $img_subpage . '" />' .
                            (Language::string( 'menu' , null, $subMenuElement["id"]) ?? $subMenuElement["name"])  .
                          '</a>
                        </div>';
              }else {
                // Default sub menu element
                $nav .= '<div class="subpage-container ' . $class . ' mainpage'  . $mainMenuElement["id"] . '">
                          <a href="' . $url . '?id=' . $mainMenuElement["id"] . '&sub=' . $subMenuElement["id"] . '"
                          title="Submenu #' . $subMenuElement["id"] . ' [' . (Language::string( $subMenuElement["id"] , null, 'menu') ?? $subMenuElement["name"]) . ']">
                            <img src="' . $img_subpage . '" />' .
                            (Language::string( $subMenuElement["id"] , null, 'menu') ?? $subMenuElement["name"])  .
                          '</a>
                        </div>';
              }
            }
          }

          $nav .= '</div>';

        }
      }

      //Profil menu
      $nav .= '<div class="page-container">
                <a href="' . $url . '?id=profile" title="' . (Language::string('profile', null, 'menu') ?? 'profile') . '">' . (Language::string('profile', null, 'menu') ?? 'profile') . '</a>
              </div>';

      $nav .= '</div>'; //Close submenu container

      //Logout
      $nav .= '<a href="' . $url . 'auth.php?logout" title="Abmelden" class="logout">';
        $nav .= '<img src="' . $url . 'medias/icons/logout.svg" />';
      $nav .= '</a>';


      $nav .= '<div class="version">&#9432; Version: ' . SYSTEM_VERSION . ' - ' . SYSTEM_NAME . '</div>';
    $nav .= '</nav>'; //Close full nav

    //Display nav
    return $nav;
  }

  /**
   * Returns main_id of a subpage
   */
  public function main_id( $sub ) {
    //Set connection variable
    $conn = Access::connect();

    //Select menu items
    $mainID = $conn->prepare("SELECT submenu FROM " . MENU . " WHERE id=:submenu");
    $mainID->execute(array(":submenu" => $sub));

    //Return value
    return $mainID->fetch()[0];
  }
}
 ?>
