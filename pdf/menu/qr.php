<?php
function qr_img_src( $onlineshop_url ){
  //Require library
  require_once( dirname(__FILE__, 3) . "/qrcode/qrlib.php");

  //Create parameters
  $path = "qrcodes/"; //Path to files
  $name = md5(  $onlineshop_url  ); //Hash name to prevent innocent
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
  QRcode::png( $onlineshop_url, $filePath, QR_ECLEVEL_H, 8, 0 );

  //Return source
  echo $filePath;
}
?>
