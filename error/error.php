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
          "code" => "No access to system",
          "message" =>  "Sie haben noch keine Berechtigungen um auf dieses System zuzugreifen. Bitte melden Sie sich bei dem Administrator")[$id];
      break;
      case 2:
        return array(
          "code" => "invalid ticket accessed",
          "message" =>  "Sie haben versucht ein ungültiges Ticket abzurufen")[$id];
      break;
      case "sql":
        return array(
          "code" => "DB Connetion failed",
          "message" => "Es konnte keine Verbindung zur Datenbank aufgebaut werden.")[$id];
      break;
      case "pub":
        return array(
          "code" => "Keine Wirtschaft angegeben",
          "message" => "Für die Getränke und Speisekarte benötigt es eine Wirtschaft.")[$id];
      break;
      default:
        if(! empty(http_response_code())) {
          switch (http_response_code()) {
            case 404:
            return array(
              "code" => "404",
              "message" =>  "404 - Page not found")[$id];;
            break;
            default:
            return array(
              "code" => http_response_code(),
              "message" =>  http_response_code() . " - Error during request")[$id];;
          }
        }else{
          return array(
            "code" => "Unknwon error",
            "message" =>  "Unbekannter Fehler. Melden Sie sich bei wiederholtem Auftreten beim Administrator")[$id];
        }
    }
  }
}
 ?>
