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
 * MediaHub.window.loadMore ( list [List Element], offset [int] )
 *
 * MediaHub.medias.load ( offset [int], steps [int] )
 *
 * MediaHub.medias.update ( fileID [string], alt [string] )
 *
 * MediaHub.medias.remove ( linkt [Link Element], fileID [string] )
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

  /**
   * Ajax function to get translated string
   *
   * id: ID of string
   * callback: Callback function
   */
  static getString( id, callback ) {
    // Define values
    var values = new Object();
    values["id"] = id;

    // Request string
    MediaHub.ajax(function(c) {
      callback(c.responseText);
    }, 'string', values);
  }

  // Manage window
  static window = {
    /**
     * Opens a new selection window
     */
    "open" : function(form, name) {
      // Start
      var mediaContainer = document.createElement("div");
          mediaContainer.setAttribute("class", "media-hub-window window-" + name);
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
                MediaHub.getString( 0, function(response) {
                  mediaNavLink1.appendChild( document.createTextNode( response ) );
                } )
            var mediaNavLink2 = document.createElement("a");
                mediaNavLink2.setAttribute("onclick", "MediaHub.window.page( this )");
                mediaNavLink2.setAttribute("class", "left");
                mediaNavLink2.setAttribute("data-page-class", "media-upload");
                MediaHub.getString( 1, function(response) {
                  mediaNavLink2.appendChild( document.createTextNode( response ) );
                } )
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
                mediaList.innerHTML = html.innerHTML;
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
                        document.createElement("span")
                      );
                      MediaHub.getString( 2, function(response) {
                        mediaDetailsValuesInput2.children[0].appendChild( document.createTextNode( response ) );
                      });


                      var mediaDetailsValuesInput2Input = document.createElement("textarea");
                          mediaDetailsValuesInput2Input.setAttribute("name", "alt");
                      mediaDetailsValuesInput2.appendChild( mediaDetailsValuesInput2Input );

                  var mediaDetailsValuesInput3 = document.createElement("div");
                      mediaDetailsValuesInput3.setAttribute("class", "value");
                      mediaDetailsValuesInput3.appendChild(
                        document.createElement("span")
                      );
                      MediaHub.getString( 3, function(response) {
                        mediaDetailsValuesInput3.children[0].appendChild( document.createTextNode( response ) );
                      });


                      var mediaDetailsValuesInput3Input = document.createElement("input");
                          mediaDetailsValuesInput3Input.setAttribute("type", "text");
                          mediaDetailsValuesInput3Input.setAttribute("disabled", "");
                      mediaDetailsValuesInput3.appendChild( mediaDetailsValuesInput3Input );

                  var mediaDetailsValuesInput4 = document.createElement("div");
                      mediaDetailsValuesInput4.setAttribute("class", "value");
                      mediaDetailsValuesInput4.appendChild(
                        document.createElement("span")
                      );
                      MediaHub.getString( 4, function(response) {
                        mediaDetailsValuesInput4.children[0].appendChild( document.createTextNode( response ) );
                      });

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
                          MediaHub.getString( 5, function(response) {
                            mediaDetailsActionsLinks1.appendChild( document.createTextNode( response ) );
                          });
                      var mediaDetailsActionsLinks2 = document.createElement("a");
                          mediaDetailsActionsLinks2.setAttribute("class", "view_fullscreen");
                          mediaDetailsActionsLinks2.setAttribute("target", "_blank");
                          mediaDetailsActionsLinks2.setAttribute("href", "");
                          MediaHub.getString( 6, function(response) {
                            mediaDetailsActionsLinks2.appendChild( document.createTextNode( response ) );
                          });
                  mediaDetailsActionsLinks.appendChild( mediaDetailsActionsLinks1 );
                  mediaDetailsActionsLinks.appendChild( document.createTextNode(" | ") );
                  mediaDetailsActionsLinks.appendChild( mediaDetailsActionsLinks2 );

                var mediaDetailsActionsButton = document.createElement("a");
                    mediaDetailsActionsButton.setAttribute("class", "button");
                    mediaDetailsActionsButton.setAttribute("onclick", "MediaHub.medias.use(this, '" + name + "')");
                    MediaHub.getString( 7, function(response) {
                      mediaDetailsActionsButton.appendChild( document.createTextNode( response ) );
                    });

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
                      MediaHub.getString( 8, function(response) {
                        mediaUploadLabelPrompt.appendChild( document.createTextNode( response ) );
                      });
                  var mediaUploadLabelProgress = document.createElement("div");
                      mediaUploadLabelProgress.setAttribute("class", "progress_bar");
                      var mediaUploadLabelProgressSpan = document.createElement("span");
                          mediaUploadLabelProgressSpan.setAttribute("class", "textoverlay");
                          MediaHub.getString( 9, function(response) {
                            mediaUploadLabelProgressSpan.appendChild( document.createTextNode( response ) );
                          });
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

     form.appendChild(mediaContainer);
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
          MediaHub.window.pageContent( pages[i].getAttribute("class") );
          pages[i].style.display = "";
        }else {
          pages[i].style.display = "none";
        }
      }
    },

    /**
     * Switch page content reload
     *
     * page: Class name of page that should be reloaded
     */
    "pageContent" : function( page ) {
      switch( page ) {
        /**
         * List all medias
         */
        case "media-list":
          MediaHub.medias.load(function(html) {
            document.getElementsByClassName("media-list")[0].innerHTML = html.innerHTML;
          });
        break;
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

      // Define values
      var values = new Object();
      values["fileID"] = fileID;
      MediaHub.ajax(function(c) {
        // Get response
        var ajax_response = JSON.parse(c.responseText);

        // Generate readable date
        var upload_time = new Date(ajax_response["upload_time"]);
        var readable_upload_time =
            upload_time.getDate() + "." + (upload_time.getMonth() + 1) + "." + upload_time.getFullYear() + " " +
            upload_time.getHours() + ":" + upload_time.getMinutes();

        // Change values
        details.getElementsByTagName("textarea")[0].value = ajax_response["alt"];
        details.getElementsByTagName("textarea")[0].setAttribute("onchange", "MediaHub.medias.update('" + fileID + "', this.value)");
        details.getElementsByTagName("input")[0].value = fileID; // hidden
        details.getElementsByTagName("input")[1].value = ajax_response["upload_user"]; // User
        details.getElementsByTagName("input")[2].value = readable_upload_time; // Upload time

      }, "details", values);

      // Set details actions
      details.getElementsByClassName("actions")[0].getElementsByClassName("view_fullscreen")[0].href = imageURL.split("\"")[1];
      details.getElementsByClassName("actions")[0].getElementsByClassName("remove")[0].setAttribute("onclick", "MediaHub.medias.remove(this, '" + fileID + "')");

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
      // Remove old button
      list.getElementsByClassName("button")[0].remove();

      // Add new content
      MediaHub.medias.load(function( html ) {
        list.innerHTML += html.innerHTML;
      }, offset);
    }
  };

  // Manage actions
  static medias = {
    /**
     * Loads images and if required button
     *
     * callback: Function for result
     * offset: start point (number)
     * steps: How many entries
     */
    "load" : function( callback, offset = 0, steps = 30) {
      // Define values
      var values = new Object();
      values["offset"] = offset;
      values["steps"] = (steps + 1);

      MediaHub.ajax(function(c) {
        //Get response text
        var ajax_response = JSON.parse(c.responseText);

        // Start html
        var html = document.createElement("div");

        // Append input
        for( var i = 0; i < Math.min(steps, ajax_response.length) ; i++ ) {
          var input = document.createElement("input");
              input.setAttribute("type", "radio");
              input.setAttribute("id", ajax_response[i].fileID);
              input.setAttribute("name", "media");

          html.appendChild( input );
        }

        // Append label
        for( var i = 0; i < Math.min(steps, ajax_response.length) ; i++ ) {
          var label = document.createElement("label");
              label.setAttribute("onclick", 'MediaHub.window.details( this )');
              label.setAttribute("for", ajax_response[i].fileID);

              var img = document.createElement("div");
                  img.setAttribute("class", "img");
                  img.setAttribute("style", "background-image: url('" + ajax_response[i].url + "')");

              label.appendChild( img );

          html.appendChild( label );
        }

        // Check if load more is required
        if( ajax_response.length >= steps ) {
          MediaHub.getString( 10, function(response) {
            var moreImages = document.createElement("a");
                moreImages.setAttribute("class", "button");
                moreImages.setAttribute("onclick", "MediaHub.window.moreMedias(this.parentNode, " + (offset + steps) + " )");
                moreImages.appendChild( document.createTextNode( response ) );

            html.appendChild( moreImages );

            // List all images
            callback( html );
          });
        }else { // No more button required
          // List all images
          callback( html );
        }

      }, "loadMedias", values);
    },

    /**
     * Updates alt text of a file
     *
     * fileID: ID of file that will be updated
     * alt: new Alt-Text
     */
    "update" : function ( fileID, alt ) {
      // Generate values
      var values = new Object();
      values["fileID"] = fileID;
      values["alt"] = alt;

      // Request
      MediaHub.ajax( function(c) {
        if( c == "false") {
          MediaHub.getString( 11, function(response) {
            window.alert( response );
          });
        }
      }, "update", values );
    },

    /**
     * Removes an image
     *
     * link: link where remove is executed
     * fileID: ID of file that will be deleted
     */
    "remove" : function ( link, fileID ) {
      MediaHub.getString( 12, function(response) {
        var checkDelete = window.confirm( response );

        // Remove if required
        if( checkDelete ) {
          // Generate values
          var values = new Object();
          values["fileID"] = fileID;

          // Request
          MediaHub.ajax( function(c) {
            if( c == "false") {
              MediaHub.getString( 13, function(response) {
                window.alert( response );
              });
            }else {
              document.getElementById(fileID).remove();
              document.querySelectorAll('[for="' + fileID + '"]')[0].remove();
              link.closest(".media-details").style.display = "none";
            }
          }, "remove", values );
        }
      });
    },

    /**
     * Function that creates an Input
     *
     * name: name of input
     * fileID: Id of file
     */
    "use" : function( link, name ) {
      // Get form and window
      var form = link.closest("form");
      var mediaHubWindow = link.closest(".window-" + name);

      // Get values and set new input
      var fileID = mediaHubWindow.getElementsByClassName("media-details")[0].getElementsByTagName("input")[0].value;
      var input = form.querySelectorAll("[name='" + name + "']");

      // Get url
      var details = mediaHubWindow.getElementsByClassName("media-details")[0];
      var imageURL = details.getElementsByClassName("img")[0].style.backgroundImage.split("\"")[1];

      if( input.length > 0 ) {
        input[0].value = fileID;
        input[0].setAttribute("data-url", imageURL);
        input[0].dispatchEvent( new Event('change') ); // force programmatically onchange event
      }else {
        input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", name);
        input.setAttribute("value", fileID);
        input.setAttribute("data-url", imageURL);
        input.dispatchEvent( new Event('change') ); // force programmatically onchange event

        form.appendChild( input );
      }

      // remove window
      mediaHubWindow.remove();
    }
  }

  // Manage dropzone actions and make ajax request
  static dropzone = {
    "dragover" : function( dropzone, evt ) {
      evt.preventDefault();
      dropzone.style.borderStyle = "solid";
    },
    "dragleave" : function( dropzone ) {
      dropzone.style.borderStyle = "dashed";
    },
    "dragend" : function( dropzone ) {
      dropzone.style.borderStyle = "dashed";
    },
    "drop" : function( dropzone, evt ) {
      evt.preventDefault();
      dropzone.getElementsByTagName("input")[0].files = evt.dataTransfer.files;
      MediaHub.uploadMedia( dropzone, evt.dataTransfer.files );
    },
    "inputSelection" : function( dropzone ) {
      MediaHub.uploadMedia( dropzone, dropzone.getElementsByTagName("input")[0].files );
    }
  };

  // Inputupload
  static uploadMedia( dropzone, upload_files ) {
    // Reset border
    dropzone.style.borderStyle = "dashed";
    var progressbar = dropzone.getElementsByClassName("progress_bar")[0];
    var textoverlay = progressbar.getElementsByClassName("textoverlay")[0];
    var input = dropzone.getElementsByTagName("input")[0];
    var files = input.files;

    progressbar.style.display = "block";
    progressbar.classList.add("animate");
    MediaHub.getString( 9, function(response) {
      textoverlay.innerHTML = response;
    });

    for( var i = 0; i < files.length; i++) {
      // Get form
      var form = dropzone.getElementsByClassName("media-upload-form")[0];

      // Get form value
      var formData = new FormData( form );
      formData.append("p", "MediaHub");
      formData.append("action", "add");
      formData.append("file", files[i]);


      // Ajax request
      var req = new XMLHttpRequest();
      req.open("POST", form.getAttribute("action"));
      req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          //Get response text
          var ajax_response = JSON.parse(this.responseText);

          if(ajax_response["state"] == true) {
            // Add file
            dropzone.getElementsByClassName("uploaded_files")[0].style.display = "Block";
            dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += "<span>" + ajax_response["alt"] + "</span>";
          }else {
            // Add file
            dropzone.getElementsByClassName("uploaded_files")[0].style.display = "Block";
            dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += "<span class='failed'>";
              dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += ajax_response["alt"];
              MediaHub.getString( 14, function(response) {
                dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += " " + response;
              });
            dropzone.getElementsByClassName("uploaded_files")[0].innerHTML += "</span>";
          }
        }
      }
      req.send(formData);
    }

    // Set progressbar to 100% and clear interval
    progressbar.classList.remove("animate");
    textoverlay.innerHTML = "Fertig";

    // Remove progressbar after 10 sec
    setTimeout(function() {
      progressbar.style.display = "none";
    }, 10000);
  }
}
