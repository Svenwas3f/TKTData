<?php
class ERR {
  //Define variable
  public $error = null;

  /**
   * Check if user can return to system
   * If he can return to first accessable page otherwise do nothing
   */
  public function return_to_system(){
    //Get access of current user id and url
    global $current_user;
    global $url;

    $sub = User::first_accessable_page($current_user); //Get first page to access

    if( User::system_access($current_user) === true){
      //Get main menu id
      $main = Menu::main_id($sub);

      //Redirect to first accessable page
      header("location: ". $url ."?id=". $main ."&sub=". $sub);
      exit;
    }
  }

  /**
   * Create errormessage
   * $id = Name or index of requested value
   */
  public function info($id){
    switch ( $this->error ){
      case 1:
        return array(
          "code" => Language::string( 0, null, "error" ),
          "message" => Language::string( 1, null, "error" ),
        )[$id];
      break;
      case 2:
        return array(
          "code" => Language::string( 2, null, "error" ),
          "message" =>  Language::string( 3, null, "error" ),
        )[$id];
      break;
      case "sql":
        return array(
          "code" => Language::string( 4, null, "error" ),
          "message" => Language::string( 5, null, "error" ),
        )[$id];
      break;
      case "pub":
        return array(
          "code" => Language::string( 6, null, "error" ),
          "message" => Language::string( 7, null, "error",
        ))[$id];
      break;
      default:
        if(! empty(http_response_code())) {
          switch (http_response_code()) {
            case 404:
            return array(
              "code" => "404",
              "message" =>  Language::string( 9, null, "error"),
            )[$id];;
            break;
            default:
            return array(
              "code" => http_response_code(),
              "message" =>  http_response_code() . " - " . Language::string( 9, null, "error"),
            )[$id];;
          }
        }else{
          return array(
            "code" => Language::string( 10, null, "error"),
            "message" => Language::string( 11, null, "error"),
          )[$id];
        }
    }
  }
}
 ?>
