<!-- Video -->
<div class="qr-scanner">
  <video muted playsinline></video>
  <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
  <div class="video">
    <canvas hidden></canvas>
  </div>
</div>

<!-- Result  -->
<div class="result-ticket"></div>

<!-- Fullscreen -->
<script>
  function toogleFullscreen() {
    //True: Adds class; false: removes class
    if( document.getElementsByClassName("fullscreen-icon")[0].classList.toggle("esc") ) {
      document.getElementsByClassName("fullscreen-icon")[0].src = '<?php echo $url; ?>medias/icons/esc-fullscreen.svg';
      document.getElementsByTagName('article')[0].requestFullscreen();
    }else {
      document.getElementsByClassName("fullscreen-icon")[0].src = '<?php echo $url; ?>medias/icons/fullscreen.svg';
      document.exitFullscreen();
    }
  }
</script>
<img src="<?php echo $url; ?>medias/icons/fullscreen.svg" class="fullscreen-icon" onclick="toogleFullscreen()" />

<div class="fullscreen-alert-container"></div>



<script>
var video = document.getElementsByTagName("video")[0];
var canvasElement = document.getElementsByTagName("canvas")[0];
var canvas = canvasElement.getContext("2d");
var loadingMessage = document.getElementById("loadingMessage");

// Use facingMode: environment to attemt to get the front camera on phones
navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment", height: 720 } }).then(function(stream) {
  video.srcObject = stream;
  video.play();
  requestAnimationFrame(tick);
});

function tick() {
  loadingMessage.innerText = "⌛ Loading video..."
  if (video.readyState === video.HAVE_ENOUGH_DATA) {
    loadingMessage.hidden = true;
    canvasElement.hidden = false;

    canvasElement.height = video.videoHeight;
    canvasElement.width = video.videoWidth;
    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
    var code = jsQR(imageData.data, imageData.width, imageData.height, {
      inversionAttempts: "dontInvert",
    });
    if (code) {
      //Check if we use fullscreen or not
      if((window.fullScreen) || (window.innerWidth == screen.width && window.innerHeight == screen.height)) {
        scanner_request_fullscreen_message(code.data);
        canvasElement.style.display = "none";
        return;
      } else {
        //No fullscreen, activation required
        scanner_request_ticket(code.data, true);
        canvasElement.style.display = "none";
        return;
      }
    }
  }
  requestAnimationFrame(tick);
}
</script>
