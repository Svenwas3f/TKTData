<?php
echo '<div class="livedata-visitor-bar">';
  echo Livedata::visitors() . " Besucher";
echo '</div>';
 ?>


<div class="livedata-button up" onclick="livedata_up();"><span></span></div>
<div class="livedata-button down" onclick="livedata_down(); livedata_visitors()"><span></span></div>

<script>
  setInterval(function() {
    livedata_visitors(function (resp) {document.getElementsByClassName("livedata-visitor-bar")[0].innerHTML = resp + " Besucher";})
  }
  , 100);
</script>
