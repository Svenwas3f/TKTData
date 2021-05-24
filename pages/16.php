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
 <div class="media-hub-window">
   <div class="media-header">
      <div class="media-nav">
        <a onclick="MediaHub.window.page( this )" data-page-class="media-list" class="left active">Übersicht</a><a onclick="MediaHub.window.page( this )" data-page-class="media-upload" class="left">Bild hinzufügen</a><a onclick="this.parentNode.parentNode.parentNode.remove()" class="right">&#10006;</a>
      </div>

     <!-- <form action="" method="post" class="search">
       <input type="text" name="s_checkout" value ="" placeholder="Bild suchen"><button><img src="https://localhost/www.tktdata.ch/medias/icons/magnifying-glass.svg" /></button>
     </form> -->
   </div>
   <div class="media-article">
      <div class="media-list">
        <input type="radio" id="5e6302901dcf886e7c7823eb48039669" name="media">
        <label onclick="MediaHub.window.details( this )" for="5e6302901dcf886e7c7823eb48039669">
          <div class="img" style="background-image: url('http://localhost/www.tktdata.ch/medias/hub/5e6302901dcf886e7c7823eb48039669.jpg')"></div>
        </label>
      </div>
      <div class="media-details" style="display:none">
        <div class="img">
          <a onclick="this.parentNode.parentNode.style.display = 'none'" class="close">&#10006;</a>
        </div>
        <div class="media-detail-values">
          <input type="hidden" name="fileID" value="thisismyfileid" />
          <div class="value"><span>Alt:</span><textarea name="alt">Das ist eine Bildbeschreibung</textarea></div>
          <div class="value"><span>Benutzer:</span><input type="text" value="Admin" disabled/></div>
          <div class="value"><span>Hochgeladen:</span><input type="text" value="20.05.2021 07:19" disabled/></div>
        </div>
        <div class="actions">
          <div>
            <a class="remove">Löschen</a> | <a href="">Vollbild</a>
          </div>
          <button>VERWENDEN</button>
        </div>
      </div>
      <div class="media-upload" style="display:none">
        <label ondragover="MediaHub.dropzone.dragover( this, event )" ondragleave="MediaHub.dropzone.dragleave( this )" ondragend="MediaHub.dropzone.dragend( this )" ondrop="MediaHub.dropzone.drop( this, event )">
          <span class="upload_prompt">Dokument hineinziehen oder klicken</span>
          <div class="progress_bar">
            <span class="textoverlay">Hochladen ... </span>
          </div>
          <div class="uploaded_files">
          </div>
          <form action="<?php echo $url; ?>ajax.php" class="media-upload-form">
            <input type="file" name="image" onchange="MediaHub.dropzone.inputSelection( this.parentNode.parentNode )" multiple/>
          </form>
        </label>
      </div>
   </div>
 </div>
