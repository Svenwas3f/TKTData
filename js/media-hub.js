class MediaHub {
  // Manage dropzone actions and make ajax request
  static dropzone = {
    "dragover" : function( dropzone, event ) {
      dropzone.style.borderStyle = "solid";
      event.preventDefault();
    },
    "dragleave" : function( dropzone ) {
      dropzone.style.borderStyle = "dashed";
    },
    "dragend" : function( dropzone ) {
      dropzone.style.borderStyle = "dashed";
    },
    "drop" : function( dropzone, event ) {
      event.preventDefault();
      MediaHub.uploadMedia( dropzone, event.dataTransfer.files );
    },
    "inputSelection" : function( dropzone ) {
      MediaHub.uploadMedia( dropzone, dropzone.getElementsByTagName("input")[0].files );
    }
  };

  // Inputupload
  static uploadMedia( dropzone, upload_files ) {
    // Reset border
    dropzone.style.borderStyle = "dashed";

    // Check if enough files
    if( upload_files.length ) {
      // make progressbar visible
      var progressbar = dropzone.getElementsByClassName("progress_bar")[0];
      var textoverlay = progressbar.getElementsByClassName("textoverlay")[0];

      progressbar.style.display = "block";
      progressbar.classList.add("animate");
      textoverlay.innerHTML = "Hochladen ...";

      // Upload every file
      for( var i = 0; i < upload_files.length; i++ ) {
        // Get form
        var form = dropzone.getElementsByClassName("media-upload-form")[0]

        // Get form value
        var formData = new FormData( form );
        formData.append("p", "MediaHub");
        formData.append("action", "add");


        // Ajax request
        var req = new XMLHttpRequest();
        req.open("POST", form.getAttribute("action"));
        req.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText)
            if(this.responseText == "true") {
              // Add file
              dropzone.getElementsByClassName("uploaded_files")[0].style.display = "Block";
              dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += "<span>" + upload_files[0].name + "</span>";
            }else {
              // Add file
              dropzone.getElementsByClassName("uploaded_files")[0].style.display = "Block";
              dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += "<span class='failed'>" + upload_files[0].name + " (Fehler beim hochladen)</span>";
            }

          }
        }
        req.send(formData);
      }

      // Set progressbar to 100% and clear interval
      progressbar.classList.remove("animate");
      textoverlay.innerHTML = "Fertig";

      // Remove progressbar after 10 sec
      progressbar.style.display = "none";
    }
  }
}
