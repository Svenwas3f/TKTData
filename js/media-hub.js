/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: Mai 2021
 * @Purpose: File to manage media hub actions
 *
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * MediaHub.ajax ( callback [callback function], action [String], values [object] )
 *
 * MediaHub.window.open ()
 *
 * MediaHub.window.page ( page [Navigation Element] )
 *
 * MediaHub.window.details ( label [Label Element] )
 *
 * MediaHub.medias.load ( offset [int], steps [int] )
 *
 *
 */
class MediaHub {
  /**
   * Ajax function
   *
   * callback: Callback function
   * action: Name of action for ajax [string]
   * values: Passed values for ajax [object]
   */
  static ajax(callback, action = null, values = null ) {
    //Important infos
    var base_url = location.protocol + '//' + location.host + location.pathname;
    var ajax_file = base_url + "/ajax.php";

    //Connect
    var req = new XMLHttpRequest();
    req.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        callback(this);
      }
    }
    req.open("POST", ajax_file, true);
    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    req.send( "p=" + encodeURIComponent("MediaHub") + (action ? "&action=" + encodeURIComponent(action) : "") + (values ? "&values=" + encodeURIComponent(JSON.stringify(values)) : "") );
  }

  // Manage window
  static window = {
    /**
     * Opens a new selection window
     */
    "open" : function() {
      // Start
      var mediaContainer = document.createElement("div");
          mediaContainer.setAttribute("class", "media-hub-window");
      // Generate header
      var mediaHeader = document.createElement("div");
          mediaHeader.setAttribute("class", "media-header");
      var mediaNav = document.createElement("div");
          mediaNav.setAttribute("class", "media-nav");
            // Create menu links
            var mediaNavLink1 = document.createElement("a");
                mediaNavLink1.setAttribute("onclick", "MediaHub.window.page( this )");
                mediaNavLink1.setAttribute("class", "left active");
                mediaNavLink1.setAttribute("data-page-class", "media-list");
                mediaNavLink1.appendChild( document.createTextNode("Übersicht") );
            var mediaNavLink2 = document.createElement("a");
                mediaNavLink2.setAttribute("onclick", "MediaHub.window.page( this )");
                mediaNavLink2.setAttribute("class", "left");
                mediaNavLink2.setAttribute("data-page-class", "media-upload");
                mediaNavLink2.appendChild( document.createTextNode("Bild hinzufügen") );
            var mediaCloseWindow = document.createElement("a");
                mediaCloseWindow.setAttribute("onclick", "this.parentNode.parentNode.parentNode.remove()");
                mediaCloseWindow.setAttribute("class", "right");
                mediaCloseWindow.appendChild( document.createTextNode("✖") ); //&#10006;
            mediaNav.appendChild( mediaNavLink1 );
            mediaNav.appendChild( mediaNavLink2 );
            mediaNav.appendChild( mediaCloseWindow );
          mediaHeader.appendChild( mediaNav );
        mediaContainer.appendChild( mediaHeader );
      // Generate article
      var mediaArticle = document.createElement("div");
          mediaArticle.setAttribute("class", "media-article");
          // Start List
          var mediaList = document.createElement("div");
              mediaList.setAttribute("class", "media-list");

              MediaHub.medias.load( function( html ) {
                mediaList.innerHTML = html;
              } );
          // Details
          var mediaDetails = document.createElement("div");
              mediaDetails.setAttribute("class", "media-details");
              mediaDetails.setAttribute("style", "display: none;");
              var mediaDetailsImg = document.createElement("div");
                  mediaDetailsImg.setAttribute("class", "img");
                  var mediaCloseDetails = document.createElement("a");
                      mediaCloseDetails.setAttribute("onclick", "this.parentNode.parentNode.style.display = \'none\'");
                      mediaCloseDetails.setAttribute("class", "close");
                      mediaCloseDetails.appendChild( document.createTextNode("✖") ); //&#10006;
                  mediaDetailsImg.appendChild(mediaCloseDetails);
              var mediaDetailsValues = document.createElement("div");
                  mediaDetailsValues.setAttribute("class", "media-detail-values");
                  var mediaDetailsValuesInput1 = document.createElement("input");
                      mediaDetailsValuesInput1.setAttribute("type", "hidden");
                      mediaDetailsValuesInput1.setAttribute("name", "fileID");

                  var mediaDetailsValuesInput2 = document.createElement("div");
                      mediaDetailsValuesInput2.setAttribute("class", "value");
                      mediaDetailsValuesInput2.appendChild(
                        document.createElement("span").appendChild(
                          document.createTextNode("Alt:")
                        )
                      );
                      var mediaDetailsValuesInput2Input = document.createElement("textarea");
                          mediaDetailsValuesInput2Input.setAttribute("name", "alt");
                      mediaDetailsValuesInput2.appendChild( mediaDetailsValuesInput2Input );

                  var mediaDetailsValuesInput3 = document.createElement("div");
                      mediaDetailsValuesInput3.setAttribute("class", "value");
                      mediaDetailsValuesInput3.appendChild(
                        document.createElement("span").appendChild(
                          document.createTextNode("Benutzer:")
                        )
                      );
                      var mediaDetailsValuesInput3Input = document.createElement("input");
                          mediaDetailsValuesInput3Input.setAttribute("type", "text");
                          mediaDetailsValuesInput3Input.setAttribute("disabled", "");
                      mediaDetailsValuesInput3.appendChild( mediaDetailsValuesInput3Input );

                  var mediaDetailsValuesInput4 = document.createElement("div");
                      mediaDetailsValuesInput4.setAttribute("class", "value");
                      mediaDetailsValuesInput4.appendChild(
                        document.createElement("span").appendChild(
                          document.createTextNode("Hochgeladen:")
                        )
                      );
                      var mediaDetailsValuesInput4Input = document.createElement("input");
                          mediaDetailsValuesInput4Input.setAttribute("type", "text");
                          mediaDetailsValuesInput4Input.setAttribute("disabled", "");
                      mediaDetailsValuesInput4.appendChild( mediaDetailsValuesInput4Input );

                  mediaDetailsValues.appendChild( mediaDetailsValuesInput1 );
                  mediaDetailsValues.appendChild( mediaDetailsValuesInput2 );
                  mediaDetailsValues.appendChild( mediaDetailsValuesInput3 );
                  mediaDetailsValues.appendChild( mediaDetailsValuesInput4 );

              var mediaDetailsActions = document.createElement("div");
                  mediaDetailsActions.setAttribute("class", "actions");

                  var mediaDetailsActionsLinks = document.createElement("div");
                      var mediaDetailsActionsLinks1 = document.createElement("a");
                          mediaDetailsActionsLinks1.setAttribute("onclick", "MediaHub.window.page( this )");
                          mediaDetailsActionsLinks1.setAttribute("class", "remove");
                          mediaDetailsActionsLinks1.appendChild( document.createTextNode("Löschen") );
                      var mediaDetailsActionsLinks2 = document.createElement("a");
                          mediaDetailsActionsLinks2.setAttribute("class", "view_fullscreen");
                          mediaDetailsActionsLinks2.setAttribute("target", "_blank");
                          mediaDetailsActionsLinks2.setAttribute("href", "");
                          mediaDetailsActionsLinks2.appendChild( document.createTextNode("Vollbild") );
                  mediaDetailsActionsLinks.appendChild( mediaDetailsActionsLinks1 );
                  mediaDetailsActionsLinks.appendChild( document.createTextNode(" | ") );
                  mediaDetailsActionsLinks.appendChild( mediaDetailsActionsLinks2 );

                var mediaDetailsActionsButton = document.createElement("button");
                    mediaDetailsActionsButton.appendChild( document.createTextNode("VERWENDEN") );

              mediaDetailsActions.appendChild( mediaDetailsActionsLinks );
              mediaDetailsActions.appendChild( mediaDetailsActionsButton );


              mediaDetails.appendChild( mediaDetailsImg );
              mediaDetails.appendChild( mediaDetailsValues );
              mediaDetails.appendChild( mediaDetailsActions );
          // Upload
          var mediaUpload = document.createElement("div");
              mediaUpload.setAttribute("class", "media-upload");
              mediaUpload.setAttribute("style", "display: none;");

              var mediaUploadLabel = document.createElement("label");
                  mediaUploadLabel.setAttribute("ondragover", "MediaHub.dropzone.dragover( this, event )");
                  mediaUploadLabel.setAttribute("ondragleave", "MediaHub.dropzone.dragleave( this )");
                  mediaUploadLabel.setAttribute("ondragend", "MediaHub.dropzone.dragend( this )");
                  mediaUploadLabel.setAttribute("ondrop", "MediaHub.dropzone.drop( this, event )");

                  var mediaUploadLabelPrompt = document.createElement("span");
                      mediaUploadLabelPrompt.setAttribute("class", "upload_prompt")
                      mediaUploadLabelPrompt.appendChild( document.createTextNode("Dokument hineinziehen oder klicken") );
                  var mediaUploadLabelProgress = document.createElement("div");
                      mediaUploadLabelProgress.setAttribute("class", "progress_bar");
                      var mediaUploadLabelProgressSpan = document.createElement("span");
                          mediaUploadLabelProgressSpan.setAttribute("class", "textoverlay");
                          mediaUploadLabelProgressSpan.appendChild( document.createTextNode("Hochladen ...") );
                      mediaUploadLabelProgress.appendChild( mediaUploadLabelProgressSpan );
                  var mediaUploadLabelUploadedFiles = document.createElement("div");
                      mediaUploadLabelUploadedFiles.setAttribute("class", "uploaded_files");
                  var mediaUploadLabelForm = document.createElement("form");
                      mediaUploadLabelForm.setAttribute("action", window.location.href.replace(/\?(.*)/, "") + "ajax.php");
                      mediaUploadLabelForm.setAttribute("class", "media-upload-form");
                      var mediaUploadLabelFormInput = document.createElement("input");
                          mediaUploadLabelFormInput.setAttribute("type", "file");
                          mediaUploadLabelFormInput.setAttribute("name", "file");
                          mediaUploadLabelFormInput.setAttribute("onchange", "MediaHub.dropzone.inputSelection( this.parentNode.parentNode )");
                          mediaUploadLabelFormInput.setAttribute("multiple", "");
                      mediaUploadLabelForm.appendChild( mediaUploadLabelFormInput );

                  mediaUploadLabel.appendChild( mediaUploadLabelPrompt );
                  mediaUploadLabel.appendChild( mediaUploadLabelProgress );
                  mediaUploadLabel.appendChild( mediaUploadLabelUploadedFiles );
                  mediaUploadLabel.appendChild( mediaUploadLabelForm );
              mediaUpload.appendChild( mediaUploadLabel );


          mediaArticle.appendChild( mediaList );
          mediaArticle.appendChild( mediaDetails );
          mediaArticle.appendChild( mediaUpload );
      mediaContainer.appendChild( mediaArticle );

     document.getElementsByTagName("article")[0].appendChild(mediaContainer);
    },

    /**
     * Changes page
     *
     * page: HTML Naviagation Element
     */
    "page" : function( page ) {
      // Get active page
      var nav = page.closest(".media-nav");
      var active = nav.getElementsByClassName("active")[0];

      // Remove active
      active.classList.remove("active");

      // Add to new page
      page.classList.add("active");

      // Close all pages
      var pages = nav.closest(".media-hub-window").getElementsByClassName("media-article")[0].children;

      for(var i = 0; i < pages.length; i++) {
        if( pages[i].getAttribute("class") == page.getAttribute("data-page-class") ) {
          pages[i].style.display = "";
        }else {
          pages[i].style.display = "none";
        }
      }
    },

    /**
     * Shows details for usage
     *
     * label: HTML Label Element
     */
    "details" : function( label ) {
      var details = label.closest(".media-article").getElementsByClassName("media-details")[0];
      var imageURL = label.getElementsByClassName("img")[0].style.backgroundImage;
      var fileID = label.getAttribute("for");

      // Set details image
      details.getElementsByClassName("img")[0].style.backgroundImage = imageURL;

      // Set details values
      // Define values
      var values = new Object();
      values["fileID"] = fileID;
      MediaHub.ajax(function(c) {
        // Get response
        var ajax_response = JSON.parse(c.responseText);

        // Change values
        details.getElementsByTagName("textarea")[0].innerHTML = ajax_response["alt"]; // Alt
        details.getElementsByTagName("input")[1].value = ajax_response["upload_user"]; // User
        details.getElementsByTagName("input")[2].value = ajax_response["upload_time"]; // Upload time

      }, "details", values);

      // Set details actions
      details.getElementsByClassName("actions")[0].getElementsByClassName("view_fullscreen")[0].href = imageURL.split("\"")[1];

      // Display details
      details.style.display = "block";
    },

    /**
     * Loads new medias into a window
     *
     * list: HTML div Element
     * offset: Number where to start
     */
    "moreMedias" : function( list, offset ) {
      // Add new content
      MediaHub.medias.load(function( html ) {
        list.innerHTML += html;
      }, offset);

      // Remove old button
      list.getElementsByTagName("button")[0].remove();
    }
  };

  // Manage actions
  static medias = {
    "load" : function( callback, offset = 0, steps = 31) {
      // Define values
      var values = new Object();
      values["offset"] = offset;
      values["steps"] = steps;

      MediaHub.ajax(function(c) {
        //Get response text
        var ajax_response = JSON.parse(c.responseText);
        var html = "";

        // Load images
        for( var i = 0; i < Math.min((steps - 1), ajax_response.length) ; i++ ) {
          html += '<input type="radio" id="' + ajax_response[i].fileID + '" name="media">';
          html += '<label onclick="MediaHub.window.details( this )" for="' + ajax_response[i].fileID + '">';
          html += '<div class="img" style="background-image: url(\'http://localhost/www.tktdata.ch/medias/hub/' + ajax_response[i].fileID + '.jpg\')"></div>';
          html += '</label>';
        }

        // Check if load more is required
        if( ajax_response.length >= steps ) {
          html += "<button onclick='MediaHub.window.moreMedias( this.parentNode, " + (offset + steps) + " )'>Weitere laden</button>";
        }

        callback( html );

      }, "loadMedias", values);
    },
  }

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
