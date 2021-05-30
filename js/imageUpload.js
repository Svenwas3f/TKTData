/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: Mai 2020
 * @Purpose: File to manage filepreview
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * previewImage( fileInput [HTML-Input] )
 *
 */

/**
 * Appends a peview image localy
 *
 * fileInput: HTML-Input (use this)
 */
function previewImage(fileInput) {
  var reader = new FileReader();

  //Check if input contains file
  if (fileInput.files.length === 0) {
    target.style.background = "";
  } else {
    reader.readAsDataURL(fileInput.files[0]);
    var preview = document.createElement("DIV");
    preview.setAttribute("class", "preview-image");
    reader.onload = function(event) {
      preview.setAttribute("style", "background-image: url(" + reader.result + ")");
    }

    fileInput.parentNode.appendChild(preview);
  }
}

function MediaHubSelected( input ) {
  // Get data attribute
  var url = input.getAttribute("data-url");

  // Check if preview exits
  if( input.closest("label").getElementsByClassName("preview-image")[0] ) {
    input.closest("label").getElementsByClassName("preview-image")[0].style.backgroundImage = "url('" + url + "')";
  }else {
    var preview = document.createElement("div");
    preview.setAttribute("class", "preview-image");
    preview.style.backgroundImage = "url('" + url + "')";

    input.closest("label").appendChild( preview );
  }
}
