<?php
//Check if timestamp is set

if(isset($_GET["archive_timestamp"])) {
  //Require general
  require_once(dirname(__FILE__, 4) . "/general.php");

  //Set headers
  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=tktdata_livedata_history_" . date("Y_m_d_H_i_s", strtotime($_GET["archive_timestamp"])) . "_export.csv");
  header("Pragma: no-cache");
  header("Expires: 0");

  //Get data
  echo Livedata::export($_GET["archive_timestamp"]);
}
 ?>
