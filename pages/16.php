<!-- - Alle verfügbaren Kassen anzeigen<br />
- Auflistung aller Transaktionen der gewählten Kase (Mit weiter und zurück)<br />
- QR-Download bereitstellen (Link auf ein weiteres Dokument)<br />

<?php
$html = '<div class="select" onclick="toggleOptions(this)">';
  $html .= '<input type="text" class="selectValue" name="checkout" value=""  required>';
  $html .= '<span class="headline">Auswählen</span>';

  $html .= '<div class="options">';
    $html .= '<span data-value="0" onclick="selectElement(this)">Karte</span>';
    $html .= '<span data-value="1" onclick="selectElement(this)">Rechnung</span>';
    $html .= '<span data-value="2" onclick="selectElement(this)">Zahlung nicht eingegangen</span>';
  $html .= '</div>';
$html .= '</div>';
echo $html;
 ?> -->




<script src="<?php echo $url; ?>/js/media-hub.js"></script>
<form action="" class="form">
  <script>
    MediaHub.window.open( document.getElementsByTagName("form")[0], "img_mail" );
  </script>
</form>
