<?php
// Check if id is set
if( empty($_GET["id"]) ) {
  header("Location: " . $url . "store/" . $type);
}

// Start pub
$pub = new Pub();
$pub->pub = $_GET["id"];

// Start pub
$product = new Product();
$product->pub = $pub->pub;

// Get background image
if( isset( $pub->values()["background_fileID"] ) &&! empty( $pub->values()["background_fileID"] ) ) {
  $mediaHub = new MediaHub();
  $mediaHub->fileID = $pub->values()["background_fileID"];

  $backgroundImgUrl = $mediaHub->getUrl( $pub->values()["background_fileID"] );
  $altImage = $mediaHub->fileDetails()["alt"];
}else {
  $backgroundImgUrl = $url . 'medias/store/background/' . pathinfo( glob(dirname(__FILE__,3) . "/medias/store/background/*")[0], PATHINFO_BASENAME );
  $altImage = "Background";
}

?>
<!-- Go back to last step -->
<div class="return-header">
  <span onclick="history.back();">&larr;</span>
</div>

<div class="menu" style="background-image: url(<?php echo $backgroundImgUrl; ?>)">
  <div class="menu-card">
    <!-- Payment infos -->
    <div class="submenu-total">
      <div class="calculated">
        <span class="total">TOTAL:</span>
        <span class="price">0.00</span>
        <span class="currency"><?php echo ($pub->values()["currency"] ?? DEFAULT_CURRENCY) ?></span>
      </div>

      <button class="pay">BEZAHLEN</button>
    </div>

    <!-- Details -->
    <div class="details">
      <div class="logo">
        <img src="<?php
        if(! empty( $pub->values()["logo_fileID"]) ) {
          // Custom logo
          echo MediaHub::getUrl( $pub->values()["logo_fileID"] );
        }else {
          // Default logo
          echo $url . "medias/store/favicon-color-512-white.png";
        }
        ?>" alt="<?php
        if(! empty( $pub->values()["logo_fileID"]) ) {
          // Custom alt
          echo $logo->fileDetails()["alt"];
        }else {
          // Default alt
          echo "TKTData Logo";
        }
        ?>" />
      </div>
      <div class="text">
        <span class="title"><?php echo $pub->values()["name"]; ?></span>
        <span class="description"><?php echo $pub->values()["description"]; ?></span>
      </div>
    </div>


    <!-- Menu card -->
    <form class="products">
      <?php
      // Generate array
      $sections = $product->sections();
      array_push($sections, array("section" => null));

      // Loop
      foreach($sections as $section) {
        // Get all products
        $products = "";
        foreach( $product->products_by_section( $section["section"] ) as $values ) {
          // Check if visible
          $product->product_id = $values["id"];

          if($product->visibility()) {
            $products .= '<div class="row">';
              $products .= '<span class="product">';
                $products .= $values["name"];
                // Check image
                if(! empty($values["product_fileID"])) {
                  $products .= '<span class="icon">&#9432;</span>';
                  $products .= '<div class="product-img" style="background-image: url(\'' . MediaHub::getUrl( $values["product_fileID"] ) . '\')"></div>';
                }
              $products .= '</span>';
              $products .= '<span class="price">' . number_format(( isset($values["price"]) ? ($values["price"] / 100) : 0), 2) . ' ' . ($pub->values()["currency"] ?? DEFAULT_CURRENCY) . '</span>';
              $products .= '<div class="shoppingbag_options">';
                $products .= '<span class="remove" onclick="remove_product(this.parentNode.children[1])">-</span>';
                $products .= '<input type="text" name="' . $values["id"] . '" pattern="[0-9]{1,3}" value="0" onchange="change_total_price( this )"/>';
                $products .= '<span class="add" onclick="add_product(this.parentNode.children[1])">+</span>';
              $products .= '</div>';
            $products .= '</div>';
          }
        }

        // Check if any products are listet in this section
        if( strlen($products) > 0) {
          // Show section
          echo'<div class="section-container">';
            echo '<div class="header row">';
              echo '<span class="product">' . ($section["section"] ?? "Produkte") . '</span>';
              echo '<span class="accordion"><span onclick="toggle_section(this)">-</span></span>';
            echo '</div>';

            echo '<div class="productlist" style="max-height: 100%;">';
              echo $products; // Show products
            echo '</div>';

          echo '</div>';
        }
      }

      // Check if open amount is allowed
      if( $pub->values()["tip"] === 1) {
        // Show section for tip amount
        echo'<div class="section-container">';
          echo '<div class="header row tip">';
            echo '<span class="product">Trinkgeld</span>';
            echo '<div class="placeholder-js">';
              echo '<span class="input">';
                echo'<input type="text" pattern="[0-9\.]{1,3}" name="tip" placeholder="0.00" onchange="change_total_price( this )" />';
                echo ($pub->values()["currency"] ?? DEFAULT_CURRENCY);
              echo  '</span>';
            echo '</div>';
          echo '</div>';
        echo '</div>';
      }
       ?>
    </form>
  </div>
</div>
