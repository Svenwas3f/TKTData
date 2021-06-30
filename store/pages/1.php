<header>
  <div class="banner" style="background-image: url('<?php echo $url; ?>medias/store/banner.jpg')">
    <img class="logo" src="<?php echo $url; ?>medias/store/logo-fitted.png">
  </div>
</header>



<form action="" method="get"  class="search_bar">
  <label>
    <input type="text" name="s" placeholder="Nach Veranstaltung suchen"/>
    <button class="icon"><img src="<?php echo $url; ?>medias/icons/magnifying-glass.svg"></button>
  </label>
</form>
<div class="store-group-list">
  <?php
  //List all groups
  $steps = 20;
  $offset = (empty($_GET["row-start"]) ? 0 : $_GET["row-start"] ) * $steps;

  foreach( Group::all( $offset, $steps, ($_GET["s"] ?? null) ) as $group ) {
    // Check if group should be visible
    if( $group["payment_store"] == 1 ) {
      // Check availability of ticket
      $groupCheck = new Group();
      $groupCheck->groupID = $group["groupID"];

      if($groupCheck->values() === false) {
        $groupState = 3; //Group does not exist
      }elseif($groupCheck->availableTickets() <= 0) {
        $groupState = 2; //sold out
      }elseif($groupCheck->timeWindow() === false) {
        $groupState = 1; //timewindow closed
      }else {
        $groupState = 0; //Ok
      }

      // Generate HTML
      echo '<a ' . (($groupState == 0) ? ('href="' . $url . 'store/' . $type . '/buy/' . $group["groupID"] . '"') : "" ) . '>';
        //Display every box of group
        echo '<div class="store-group-box">';
          //Banner required
          switch($groupState) {
            case 1:
              echo '<div class="banner" style="background-color: #b91657;">Abgelaufen</div>';
            break;
            case 2:
              echo '<div class="banner" style="background-color: #4c4ca1;">Ausverkauft</div>';
            break;
            case 3:
              echo '<div class="banner" style="background-color: #80007c;">Existiert nicht</div>';
            break;
          }

          //Get logo
          if( isset( $groupCheck->values()["payment_logo_fileID"] ) &&! empty( $groupCheck->values()["payment_logo_fileID"] ) ) {
            echo '<img  src="' . MediaHub::getUrl( $groupCheck->values()["payment_logo_fileID"] ) .'"/>';
          }else {
            echo '<img  src="' . $url . 'medias/store/favicon-color-512.png"/>';
          }
          echo '<span class="title">' . $group["name"] . '</span>';
          echo '<span class="info">' . (($group["price"] + ($group["vat"] / 10000) * $group["price"]) / 100) . ' ' . $group["currency"] . '</span>';
        echo '</div>';
      echo '</a>';
    }
  }
   ?>
</div>

<?php
if( (count(Group::all(($offset + $steps), 1, ($_GET["s"] ?? null) )) > 0) && (($offset/$steps) > 0) ) {
  echo '<div class="page-nav">';
    echo '<a a href="' . $url . 'store/' . $type . '/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
    echo '<a class="center"></a>';
    echo '<a a href="' . $url . 'store/' . $type . '/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
  echo '</div>';
  // echo "last/next";
}elseif( ($offset/$steps) > 0 ) {
  echo '<div class="page-nav">';
    echo '<a a href="' . $url . 'store/' . $type . '/?row-start=' . ($offset/$steps - 1) . '" class="left" title="Vorherige Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-back.svg"</a>';
    echo '<a class="center"></a>';
    echo '<a class="right"></a>';
  echo '</div>';
  // echo "last";
}elseif( (count(Group::all(($offset + $steps), 1, ($_GET["s"] ?? null) )) > 0) ) {
  echo '<div class="page-nav">';
    echo '<a class="left"></a>';
    echo '<a class="center"></a>';
    echo '<a a href="' . $url . 'store/' . $type . '/?row-start=' . ($offset/$steps + 1) . '" class="right" title="Weitere Wirtschaften ansehen"><img src="' . $url . 'medias/store/icons/page-next.svg"</a>';
  echo '</div>';
  // echo "next";
}
 ?>
