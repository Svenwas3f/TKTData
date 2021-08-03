<script>
function loadMessageByHash() {
  if(window.location.hash) {
    var hash = window.location.hash.replace("#", "");
    var element = document.getElementById(hash);
    element.children[0].click();
  }
}

window.onload = loadMessageByHash;
window.onhashchange = loadMessageByHash;
</script>

<header>
  <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
    <a href="<?php echo $url . "store/" . $type; ?>"><img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png"></a>
  </div>
</header>


<div class="accordion">
  <div class="element" id="payment-procedure">
    <div class="headline" onclick="accordion(0)">
      <?php echo Language::string(70, null, "store"); ?>
      <span class="toggler"></span>
    </div>
    <div class="content">
      <div class="text">
        <?php
        echo Language::string( 71, array(
                '%url%' => $url,
              ), "store" );
         ?>
      </div>
    </div>
  </div>

  <div class="element" id="ticket-lost">
    <div class="headline" onclick="accordion(1)">
      <?php echo Language::string(72, null, "store"); ?>
      <span class="toggler"></span>
    </div>
    <div class="content">
      <div class="text">
        <?php
        echo Language::string( 73, array(
                '%url%' => $url,
              ), "store" );
         ?>
      </div>
    </div>
  </div>

  <div class="element" id="payment-options">
    <div class="headline" onclick="accordion(2)">
      <?php echo Language::string(74, null, "store"); ?>
      <span class="toggler"></span>
    </div>
    <div class="content">
      <div class="text">
        <?php echo Language::string(75, null, "store"); ?>
      </div>
    </div>
  </div>

  <div class="element" id="personal-tickets">
    <div class="headline" onclick="accordion(3)">
      <?php echo Language::string(76, null, "store"); ?>
      <span class="toggler"></span>
    </div>
    <div class="content">
      <div class="text">
        <?php echo Language::string(77, null, "store"); ?>
      </div>
    </div>
  </div>

  <div class="element" id="contact">
    <div class="headline" onclick="accordion(4)">
      <?php echo Language::string(78, null, "store"); ?>
      <span class="toggler"></span>
    </div>
    <div class="content">
      <div class="text">
        <?php echo Language::string(79, null, "store"); ?>
      </div>
    </div>
  </div>
</div>
