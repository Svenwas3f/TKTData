<?php
function qr_img_src($ticketToken){
  //Require library
  require_once( dirname(__FILE__, 3) . "/qrcode/qrlib.php");

  //Create parameters
  $path = "qrcodes/"; //Path to files
  $name = md5( $ticketToken ); //Hash name to prevent innocent
  $extension = '.png'; //File extension of file

  $filePath = $path . $name . $extension; //Full file path

  //Remove previous images
  $QRcodes = glob($path . '*');
  foreach( $QRcodes as $QRcode ){
    if( is_file( $QRcode ) ){
      unlink( $QRcode ); //Remove image
    }
  }

  //Create image
  QRcode::png( $ticketToken, $filePath, QR_ECLEVEL_H, 8, 0 );

  //Return source
  echo $filePath;
}
?>
