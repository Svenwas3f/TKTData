<?php
$checkout = new Checkout();
$checkout->cashier = 1;

var_dump($checkout->transactions());

$checkout->add( CHECKOUT::DEFAULT_TABLE , array(
  "checkout_id" => "TEST",
  "name" => "hello World",
));
?>
