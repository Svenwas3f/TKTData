<?php
//Start plugin
$plugin = new Plugin();

//Echo text
// echo "Dies ist die Seite #" . $page . " mit dem Namen <strong>" .  $plugin->get_page( intval($page) )["name"] . "</strong>";
echo Language::string(0, array(
  '%pageid%' => $page,
  '%pagename%' => $plugin->get_page( intval($page) )["name"],
));
 ?>
