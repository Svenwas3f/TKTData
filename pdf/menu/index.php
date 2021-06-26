<?php
//Require library
require_once( dirname(__FILE__, 2) . "/dompdf/autoload.inc.php");

//Require general file
require_once( dirname(__FILE__, 3)) . "/general.php";

//Check if pub is set
if(! isset($_GET["pub"])){
  // Not set, turn to error page
  header("Location: " . $url . "error/?error=pub");
}


// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->set_option('isHtml5ParserEnabled', true);

//Path to the ticket file wiht ticket token
$menu_path = $url . "/pdf/menu/menu.php?pub=" . $_GET["pub"];

$dompdf->loadHtml(file_get_contents($menu_path));

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("menu", array("Attachment" => 0));
?>
