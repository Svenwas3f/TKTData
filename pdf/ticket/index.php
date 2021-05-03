<?php
//Check if ticketToken is set
if(! $_GET["ticketToken"]){
  header("Location: ../error/?error=2 "); //Redirect to ticket error
  exit;
}

//Require library
require_once( dirname(__FILE__, 2) . "/dompdf/autoload.inc.php");


// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->set_option('isHtml5ParserEnabled', true);

//Path to the ticket file wiht ticket token
$ticketPath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/ticket.php?ticketToken=" . urlencode($_GET["ticketToken"]);

$dompdf->loadHtml(file_get_contents($ticketPath));

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("ticket", array("Attachment" => 0));
?>
