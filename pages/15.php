<?php
echo '<div class="livedata-visitor-bar">';
  echo Livedata::visitors() . " " . Language::string(0);
echo '</div>';

// Check if access allowed
if( User::w_access_allowed($page, $current_user) ) {
  echo '<div class="livedata-button up" onclick="livedata_up();"><span></span></div>';
  echo '<div class="livedata-button down" onclick="livedata_down();"><span></span></div>';
}else {
  echo '<div class="livedata-button up disabled"><span></span></div>';
  echo '<div class="livedata-button down disabled"><span></span></div>';
}


 ?>

<script>
  setInterval(function() {
    livedata_visitors(
      function (resp) {
        document.getElementsByClassName("livedata-visitor-bar")[0].innerHTML = resp + " <?php echo Language::string(0); ?>";
      }
    );
  }
  , 100);
</script>
