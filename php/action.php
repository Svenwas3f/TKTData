<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to display action messages
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Action->success ( $info [Info text that will be displayed])
 *
 * Action->fail ( $info [Info text that will be displayed])
 *
 * Action->confirm ( $info [Info text that will be displayed], $value [Value of Input for confirmation], $advanceUrl [Url parameters that will be added after $url_page])
 *
 * Action->fs_info ( $info [Info text that will be displayed], $value [Value of deine button], $link [Target after clicking on denie])
 *
 */
class Action {
  /**
   * Returns success message
   */
  public static function success($info) {
    //Require variables
    global $url;

    //Display message
    echo '<div class="message-container">';
      echo '<div class="message success" onclick="this.remove()">';
        echo '<img src="' . $url . 'medias/icons/success.svg" />';
        echo '<span>' . $info . '</span>';
      echo '</div>';
    echo '</div>';
  }

  /**
   * Returns error message
   */
  public static function fail($info) {
    //Require variables
    global $url;

    //Display message
    echo '<div class="message-container">';
      echo '<div class="message error" onclick="this.remove()">';
        echo '<img src="' . $url . 'medias/icons/error.svg" />';
        echo '<span>' . $info . '</span>';
      echo '</div>';
    echo '</div>';
  }

  /**
   * Returns confirm form
   */
  public static function confirm($info, $value = null, $advanceUrl = null) {
    //Require variables
    global $url_page;
    global $url;

    //Display
    echo '<div class="fullscreen">';
      echo '<form action="' . $url_page . $advanceUrl . '" method="post">';
      echo '<img src="' . $url . 'medias/logo/logo-fitted.png" />';
        echo '<span>' . $info . '</span>';
        echo '<button name="denie">' . Language::string( 0, null, "action" ) . '</button>';
        echo '<button name="confirm" value="' . $value . '">' . Language::string( 1, null, "action" ) .'</button>';
      echo '</form>';
    echo '</div>';
  }

  /**
   * Returns fullscreen info box
   */
  public static function fs_info($info, $value = null, $link = null) {
    //Require variables
    global $url;
    global $url_page;
    global $url;

    //Display
    echo '<div class="fullscreen center">';
      echo '<form action="' . (! empty($link) ? $link : $url_page) . '" method="post">';
      echo '<img src="' . $url . 'medias/logo/logo-fitted.png" />';
        echo '<span>' . $info . '</span>';
        if(! empty($value)) {
          echo '<button name="denie">' . $value . '</button>';
        }
      echo '</form>';
    echo '</div>';
  }
}
 ?>
