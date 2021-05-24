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
        <a href="" class="left active">Übersicht</a><a href="" class="left">Bild hinzufügen</a><a onclick="this.parentNode.parentNode.parentNode.remove()" class="right">&#10006;</a>
      </div>

     <!-- <form action="" method="post" class="search">
       <input type="text" name="s_checkout" value ="" placeholder="Bild suchen"><button><img src="https://localhost/www.tktdata.ch/medias/icons/magnifying-glass.svg" /></button>
     </form> -->
   </div>
   <div class="media-article">
      <div class="media-list" style="display:none">
        <label>
          <input type="radio" name="media">
          <div class="img" style="background-image: url('https://images.unsplash.com/photo-1603993097397-89c963e325c7?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')"></div>
        </label>
      </div>
      <div class="media-details" style="display:none">
        <div class="img" style="background-image: url('https://images.unsplash.com/photo-1603993097397-89c963e325c7?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
          <a onclick="this.parentNode.parentNode.remove()" class="close">&#10006;</a>
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
      <div class="media-upload">
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
