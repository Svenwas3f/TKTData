<?php
//for this plugin you need to activate libzip.
//More infos on the php webpage https://www.php.net/manual/de/zip.requirements.php

//Add pages
$plugin = new Plugin();
$mainpage = $plugin->add_page(array(
  "name" => "Ticketgenerator",
  "layout" => 6,
));

$plugin->add_subpage(array(
  "name" => "Tickets generieren",
  "mainpage" => $mainpage,
  "image" => "medias/ticket.svg",
  "layout" => 1,
));
 ?>
