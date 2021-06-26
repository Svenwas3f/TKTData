<?php
//Require general file
require_once( dirname(__FILE__, 3) . "/general.php");

//Require QRcode
require_once( dirname(__FILE__) . "/qr.php");

// Get pub infos
$pub = new Pub();
$pub->pub = $_GET["pub"];

// Get file infos
$logo = new MediaHub();
$logo->fileID = $pub->values()["logo_fileID"];
?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>MENUKARTE - <?php echo $pub->values()["name"]; ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="../fonts/fonts.css" />
  </head>

  <!-- Menu pixel size: 992 x 1403 -->
  <body>

    <!-- Default footer text -->
    <footer>
      Menulist provided by <span>TKTDATA</span>
    </footer>

    <!-- Content / Menu -->
    <article>
      <!-- Headline -->
      <table class="header">
        <tr class="headline">
          <td class="logo">
            <img src="<?php
            if(! empty( $pub->values()["logo_fileID"]) ) {
              // Custom logo
              echo MediaHub::getUrl( $pub->values()["logo_fileID"] );
            }else {
              // Default logo
              echo $url . "medias/logo/favicon-color-512.png";
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
          </td>
          <td class="details">
            <span class="title"><?php echo $pub->values()["name"]; ?></span>
            <span class="description"><?php echo nl2br( $pub->values()["description"] ); ?></span>
          </td>
        </tr>
      </table>

      <!-- Products -->
      <?php
      foreach($pub->sections() as $section) {
        // Get all products
        $products = "";
        foreach( $pub->products_by_section( $section["section"] ) as $product ) {
          // Check if visible
          $pub->product_id = $product["id"];

          if($pub->product_visibility()) {
            $products .= '<tr>';
              $products .= '<td class="product">' . $product["name"] . '</td>';
              $products .= '<td class="price">' . number_format(( isset($product["price"]) ? ($product["price"] / 100) : 0), 2) . ' ' . $product["currency"] . '</td>';
            $products .= '</tr>';
          }
        }

        // Check if any products are listet in this section
        if( strlen($products) > 0) {
          // Show section
          echo'<table class="products">';
            echo '<tr>';
              echo '<th class="product">' . $section["section"] . '</th>';
              echo '<th class="price"></th>';
            echo '</tr>';

            echo $products; // Show products

          echo '</table>';
        }
      }
       ?>
    </article>

    <!-- Link to onlineshop -->
    <?php
    $onlineshop_link = $url . "pub/?id=" . $pub->pub;
    ?>
    <a class="onlineshop" href="<?php echo $onlineshop_link; ?>" target="_blank">
      <div class="img-container">
        <img src="<?php qr_img_src( $onlineshop_link ); ?>" />
      </div>
      <span><?php echo $onlineshop_link; ?></span>
    </a>
  </body>
</html>
