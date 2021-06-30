<header>
  <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
    <img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png">
  </div>
</header>

<form action="" method="post"  class="search_bar">
  <label>
    <input type="text" name="search" placeholder="Nach Wirtschaft suchen"/>
    <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
  </label>
</form>
<div class="store-group-list">
  <?php
  // Get all pubs
  $steps = 20;
  $offset = (empty($_GET["row-start"]) ? 0 : $_GET["row-start"] ) * $steps;

  $pubs = Pub::all($offset, $steps, $_GET["s"] ?? null);

  // List pubs
  foreach($pubs as $pub) {
    echo '<a href="' . $url . 'pubs/menu/?pub=' . $pub["pub_id"] . '">';
      echo '<div class="store-group-box">';
        if( empty($pub["logo_fileID"]) ) {
          echo '<img  src="' . $url . 'medias/pubs/favicon-color-512.png"/>';
        }else {
          echo '<img  src="' . MediaHub::getUrl( $pub["logo_fileID"] ) .'"/>';
        }
        echo '<span class="title">' . $pub["name"] . '</span>';
      echo '</div>';
    echo '</a>';
  }
   ?>
</div>

<?php
// Next/last page
if( (count(Pub::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) && (($offset/$steps) > 0) ) {
  echo '<div class="page-nav">';
    echo '<a a href="' . $url . $type . '/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
    echo '<a class="center"></a>';
    echo '<a a href="' . $url . $type . '/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
  echo '</div>';
}elseif( ($offset/$steps) > 0 ) { // Last page
  echo '<div class="page-nav">';
    echo '<a a href="' . $url . $type . '/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
    echo '<a class="center"></a>';
    echo '<a class="right"></a>';
  echo '</div>';
}elseif( (count(Pub::all(($offset + $steps), 1, ($_GET["s"] ?? null))) > 0) ) { // Next page
  echo '<div class="page-nav">';
    echo '<a class="left"></a>';
    echo '<a class="center"></a>';
    echo '<a a href="' . $url . $type . '/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
  echo '</div>';
}
 ?>
