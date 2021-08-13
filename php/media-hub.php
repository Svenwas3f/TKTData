<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage media hub actions
 *
 ************* Class Variables *************
 * If a function requires such a variable, you will find a hint in the comments of the function
 * $fileID: Identification number of file
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * MediaHub->generateFileID () {private}
 *
 * MediaHub->getBasename( $fileID [string] ) {static}
 *
 * MediaHub->getUrl( $fileID [string] ) {static}
 *
 * MediaHub->all ( $offset [int], $steps [int], $search_value [info_string] )
 *
 * MediaHub->addImage ( $image [file], $alt [string])
 *
 * MediaHub->updateImage ( $alt [string] ) [$fileID]
 *
 * MediaHub->removeImage () [$fileID]
 *
 * MediaHub->fileDetails () [$fileID]
 */
class MediaHub {
  // Variables
  public $fileID;

  /**
   * Generates an new fileID
   */
  private function generateFileID() {
    // Scann all files
    $scanned_files = scandir( dirname(__FILE__, 2) . "/medias/hub/");

    do {
      // Generate new number
      $new_fileID = bin2hex(random_bytes(16));
    }while ( count(glob( dirname(__FILE__, 2) . "/medias/hub/" . $new_fileID . ".*" )) > 0 );

    // Return new id
    return $new_fileID;
  }

  /**
   * Get basename by fileID
   *
   * $fileID: Identification number of file
   */
  public static function getBasename( $fileID ) {
    return pathinfo( glob( dirname(__FILE__, 2) . "/medias/hub/" . $fileID . ".*")[0], PATHINFO_BASENAME );
  }

  /**
   * Get url by fileID
   *
   * $fileID: Identification number of file
   */
  public static function getUrl( $fileID ) {
    //Require global variable
    global $url;

    // Return url
    return $url . "medias/hub/" . MediaHub::getBasename( $fileID );
  }

  /**
   * Returns array of all files
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function all( $offset = 0, $steps = 20, $search_value = null ) {
    // Get global variables
    global $url;

    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value) ) {
      // Select all
      $files = $conn->prepare("SELECT * FROM " . MEDIA_HUB . " ORDER BY upload_time DESC LIMIT " . $steps . " OFFSET " . $offset);
      $files->execute();
    }else {
      // Select all
      $files = $conn->prepare("SELECT * FROM " . CHECKOUT . " WHERE alt=:alt OR upload_user=:upload_user  ORDER BY upload_time DESC LIMIT " . $steps . " OFFSET " . $offset);
      $files->execute(array(
        ":alt" => $search_value,
        ":upload_user" => $search_value
      ));
    }

    // Get url
    $db_list = $files->fetchAll( PDO::FETCH_ASSOC );
    foreach( $db_list as $key => $item ) {
      $search = glob( dirname(__FILE__, 2) . "/medias/hub/" . $item["fileID"] . ".*");

      // Add to array
      if( count( $search ) > 0) {
        $db_list[$key]["extension"] = pathinfo( $search[0], PATHINFO_EXTENSION  );
        $db_list[$key]["url"] = $url . "medias/hub/" . pathinfo( $search[0], PATHINFO_BASENAME  );
      }
    }

    return $db_list;
  }

  /**
   * Uploades an image to mediahub
   *
   * $image: $_FILES[""] Array of one image
   * $alt = Alt text that should be displayed if image not found
   */
  public function addImage( $image, $alt  ) {
    // Get global values
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Generate new if
    $this->fileID = $this->generateFileID();

    // Upload time
    $upload_time = date("Y-m-d H:i:s");

    // Create db query
    $db_upload = $conn->prepare("INSERT INTO " . MEDIA_HUB . " (fileID, alt, upload_time, upload_user) VALUES (:fileID, :alt, :upload_time, :upload_user)");
    $db_upload->execute(array(
      ":fileID" => $this->fileID,
      ":alt" => $alt,
      ":upload_time" => $upload_time,
      ":upload_user" => $current_user
    ));

    if (!file_exists( dirname(__FILE__, 2) . "/medias/hub/" )) {
      mkdir( dirname(__FILE__, 2) . "/medias/hub/", 0777, true );
    }

    // Upload image
    if( move_uploaded_file( $image["tmp_name"], dirname(__FILE__, 2) . "/medias/hub/" . $this->fileID . "." . pathinfo( $image["name"], PATHINFO_EXTENSION ) )) {
      //Create modification
      $change = array(
        "user" => $current_user,
        "message" => "Added new Image (MediaHub)",
        "table" => "MEDIA_HUB",
        "function" => "INSERT INTO",
        "primary_key" => array("key" => "fileID", "value" => $this->fileID),
        "old" => "",
        "new" => array(
          "alt" => $alt,
          "upload_time" => $upload_time,
          "upload_user" => $current_user,
        )
      );

      User::modifie( $change );

      // Return message
      return true;
    }else {
      // Return message
      return false;
    }
  }

  /**
   * Updates alt text of an image
   * requires: $fileID
   *
   * $alt: New alt text as string
   */
  public function updateImage( $alt ) {
    // Get global values
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Prepare change
    $change = array(
      "user" => $current_user,
      "message" => "Updated image (MediaHub) #" . $this->fileID,
      "table" => "MEDIA_HUB",
      "function" => "UPDATE",
      "primary_key" => array("key" => "fileID", "value" => $this->fileID),
      "old" => array("alt" => $this->fileDetails()["alt"]),
      "new" => array(
        "alt" => $alt,
      )
    );

    // Update
    $updateImage = $conn->prepare("UPDATE " . MEDIA_HUB . " SET alt=:alt WHERE fileID=:fileID");
    if( $updateImage->execute(array(
      ":alt" => $alt,
      ":fileID" => $this->fileID,
    )) ) {
      // Add modifictaion
      User::modifie( $change );

      // return success
      return true;
    }else {
      // return fail
      return false;
    }
  }

  /**
   * Removes an image
   * requires: $fileID
   */
  public function removeImage() {
    // Get global values
    global $current_user;

    //Get database connection
    $conn = Access::connect();

    // Check what restore is required
    if( FULL_RESTORE ) {
      // Remove from database
      $change = array(
        "user" => $current_user,
        "message" => "Removed image (MediaHub) #" . $this->fileID,
        "table" => "MEDIA_HUB",
        "function" => "DELETE",
        "primary_key" => array("key" => "fileID", "value" => $this->fileID),
        "old" => $this->fileDetails(),
        "new" => array("")
      );

      // Remove DB
      $remove = $conn->prepare("DELETE FROM " . MEDIA_HUB . " WHERE fileID=:fileID");
      if( $remove->execute(array(
        ":fileID" => $this->fileID,
      )) ) {
        // Add modifictaion
        User::modifie( $change );

        // return success
        return true;
      }
    }else {
      // Remove from database
      $change = array(
        "user" => $current_user,
        "message" => "Removed image (MediaHub) #" . $this->fileID,
        "table" => "MEDIA_HUB",
        "function" => "DELETE",
        "primary_key" => array("key" => "fileID", "value" => $this->fileID),
        "old" => $this->fileDetails(),
        "new" => array()
      );

      // Remove DB
      $remove = $conn->prepare("DELETE FROM " . MEDIA_HUB . " WHERE fileID=:fileID");
      if( $remove->execute(array(
        ":fileID" => $this->fileID,
      )) ) {
        // Add modifictaion
        User::modifie( $change );
      }

      // Remove file
      return unlink( glob(dirname(__FILE__, 2) . "/medias/hub/" . $this->fileID . ".*")[0] );
    }
  }

  /**
   * Returns array with details of file
   */
  public function fileDetails() {
    //Get database connection
    $conn = Access::connect();

    // Select values
    $details = $conn->prepare("SELECT alt, upload_time, upload_user FROM " . MEDIA_HUB . " WHERE fileID=:fileID");
    $details->execute(array(
      ":fileID" => $this->fileID,
    ));

    return $details->fetch( PDO::FETCH_ASSOC );
  }
}

 ?>
