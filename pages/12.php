<?php
//Display form
$searchbar = new HTML('searchbar', array(
  'action' => $url_page,
  'method' => 'post',
  'placeholder' => Language::string(0),
  's' => (isset(  $_POST["s"] ) ? $_POST["s"] : ""),
));

$searchbar->prompt();

if(! empty($_POST)) {
  $scanner = new Scanner();
  $scanner->ticketToken = $_POST["s"];
  echo ($scanner->ticketInfoHTML());
}
 ?>
