<?php
//Display form
echo '<form action="' . $url_page . '" method="post" class="search">';
  echo '<input type="text" name="search_value" value ="' . (isset(  $_POST["search_value"] ) ? $_POST["search_value"] : "") . '" placeholder="Ticketoken">';
  echo '<button><img src="' . $url . 'medias/icons/magnifying-glass.svg" /></button>';
echo '</form>';

if(! empty($_POST)) {
  $scanner = new Scanner();
  $scanner->ticketToken = $_POST["search_value"];
  echo ($scanner->ticketInfoHTML());
}
 ?>
