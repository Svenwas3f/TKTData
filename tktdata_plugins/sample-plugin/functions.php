<?php
//Start plugin
$plugin = new Plugin();

//Create mainpage
$mainpage = $plugin->add_page(array(
  "name" => "Test",
  "layout" => 6
));

//Add subpage 1
$plugin->add_subpage(array(
  "name" => "Sub-Test 1",
  "mainpage" => $mainpage,
  "image" => null,
  "layout" => 1,
));

//Add subpage 2
$plugin->add_subpage(array(
  "name" => "Sub Test 2",
  "mainpage" => $mainpage,
  "image" => null,
  "layout" => 2,
));
 ?>
