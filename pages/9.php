<?php
//Check if user can edit this file
if(User::w_access_allowed($page, $current_user)) {
  echo '<div class="info-box">Doppelklick in Textfeld um zu bearbeiten. Die Änderungen werden automatisch gespeichert.</div>';

  //Display info
  echo '<div class="scanner-info-txt" ondblclick="scanner_request_infoTxt()">';
    $scannInfo = new Scanner();
    echo $scannInfo->readInfo();
  echo '</div>';
}else {
  //Display info
  echo '<div class="scanner-info-txt">';
    $scannInfo = new Scanner();
    echo $scannInfo->readInfo();
  echo '</div>';
}
 ?>
