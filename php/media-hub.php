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
 * MediaHub->all ( $offset [int], $steps [int], $search_value [info_string] )
 */
class MediaHub {
  // Variables
  public $fileID;

  /**
   *
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
   * Returns array of all checkouts
   *
   * $limit: How many rows
   * $offset: Start row
   * $search_value: Search string
   */
  public function all( $offset = 0, $steps = 20, $search_value = null ) {
    //Get database connection
    $conn = Access::connect();

    if( is_null($search_value) || empty($search_value) ) {
      // Select all
      $checkout = $conn->prepare("SELECT * FROM " . MEDIA_HUB . " ORDER BY upload_time DESC LIMIT " . $steps . " OFFSET " . $offset);
      $checkout->execute();
    }else {
      // Select all
      $checkout = $conn->prepare("SELECT * FROM " . CHECKOUT . " WHERE alt=:alt OR upload_user=:upload_user  ORDER BY upload_time DESC LIMIT " . $steps . " OFFSET " . $offset);
      $checkout->execute(array(
        ":alt" => $search_value,
        ":upload_user" => $search_value
      ));
    }

    return $checkout->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   *
   */
  public function addImage( $image, $alt  ) {
    //Get database connection
    $conn = Access::connect();

    // Generate new if
    $this->fileID = $this->generateFileID();

    // Create db query
    $db_upload = $conn->prepare("INSERT INTO " . MEDIA_HUB . " (fileID, alt, upload_time, upload_user) VALUES (:fileID, :alt, :upload_time, :upload_user)");
    $db_upload->execute(array(
      ":fileID" => $this->fileID,
      ":alt" => $alt,
      ":upload_time" => date("Y-m-d H:i:s"),
      ":upload_user" => $current_user
    ));

    // Upload image
  }

  /**
   *
   */
  public function updateImage() {

  }

  /**
   *
   */
  public function removeImage() {
    //Get database connection
    $conn = Access::connect();

    // Check what restore is required
    if( FULL_RESTORE ) {
      // Remove DB
    }else {
      // Remove DB

      // Remove file
    }
  }
}

 ?>
