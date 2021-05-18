<?php
/**
 * Archive
 */
if(isset($_GET["archive"])) { //Display confirmation
  Action::confirm("Möchten Sie den aktuellen Stand tatsächlich archivieren?");
}

if(isset($_POST) && isset($_POST["confirm"])) { //Confirm if required
  Livedata::archive();
}
 ?>
<div class="livedata-live-info">
  <div class="visitors">
    <span class="title">Aktuelle Besucher</span>
    <span class="content"><?php echo Livedata::visitors(); ?></span>
  </div>
  <div class="trend">
    <span class="title">Aktueller Trend</span>
    <?php
    switch(Livedata::trend()) {
      case 0:
        $arrow_url = $url . "medias/icons/arrow_up.svg";
      break;
      case 1:
        $arrow_url = $url . "medias/icons/arrow_down.svg";
      break;
      case 2:
        $arrow_url = $url . "medias/icons/arrow_equal.svg";
      break;
    }
    ?>
    <span class="content"><img src="<?php echo $arrow_url; ?>" class="content-trend-img"></span>
  </div>

  <a class="archive-button" href="<?php echo $url_page; ?>&archive">Archive</a>
</div>



<div class="chart-container" style="position: relative; height:50vh; width:calc(50vw - 125px)">
    <canvas id="history"></canvas>
</div>

<div class="chart-container" style="position: relative; height:50vh; width:calc(50vw - 125px)">
    <canvas id="historyUp"></canvas>
</div>

<div class="chart-container" style="position: relative; height:50vh; width:calc(50vw - 125px)">
    <canvas id="historyDown"></canvas>
</div>

<?php
//Set max and min
$max = Livedata::live_time()["max"];
$min = Livedata::live_time()["min"];

//Get data
$history = Livedata::history($min, $max);
$historyUp = Livedata::historyUp($min, $max);
$historyDown = Livedata::historyDown($min, $max);
 ?>
<script>
var historyData = document.getElementById('history');
var historyUpData = document.getElementById('historyUp');
var historyDownData = document.getElementById('historyDown');

function live_chart(crt, dataX, dataY, title) {
  return  new Chart(crt, {
      type: 'line',
      data: {
          labels: dataY,
          datasets: [{
              label: '# Besucher',
              data: dataX,
              backgroundColor: 'rgba(35, 43, 67, 0.25)',
              borderColor: 'rgb(46, 29, 141)',
              pointBackgroundColor: 'rgb(255, 255, 255)',
              pointBorderColor: 'rgb(46, 29, 141)',
              pointHoverBackgroundColor: 'rgb(46, 29, 141)',
              pointHitRadius: 10,
              borderWidth: 2
          }]
      },
      options: {
          title: {
            display: true,
            text: title,
            fontColor: '#656b7b',
            fontSize: 14,
            padding: 20,
          },
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                  }
              }]
          },
          tooltips: {
              backgroundColor: 'rgb(35, 43, 67)',
              titleFontFamily: 'Open Sans',
              cornerRadius: 4,
              position: 'nearest',
              xPadding: 18,
              yPadding: 12,
              callbacks: {
                labelColor: function(tooltipItem, chart) {
                    return {
                        borderColor: 'white',
                        backgroundColor: 'rgb(46, 29, 141)'
                    };
                },
                  labelTextColor: function(tooltipItem, chart) {
                      return 'white';
                  }
              }
          },
          legend: {
            display: false,
          },
          elements: {
              line: {
                tension: 0.25 // disables bezier curves
              }
          }
      }
  });
}

var chartHistory = live_chart(historyData, <?php echo json_encode($history["x"]); ?>, <?php echo json_encode($history["y"]); ?>, "Verlauf");
var chartHistoryUp = live_chart(historyUp, <?php echo json_encode($historyUp["x"]); ?>, <?php echo json_encode($historyUp["y"]); ?>, "Eintritte");
var chartHistoryDown = live_chart(historyDownData, <?php echo json_encode($historyDown["x"]); ?>, <?php echo json_encode($historyDown["y"]); ?>, "Austritte");

//Chart interval
setInterval(function () {
  livedata_history();
  livedata_historyUp();
  livedata_historyDown();
}, 10000);

//liveinformations interval
setInterval(function () {
  livedata_trend();
  livedata_visitors(function (resp) {document.getElementsByClassName("content")[0].innerHTML = resp;});
}, 1000)

if(screen.width < 700) {
  chartHistory.canvas.parentNode.style.height = '100%';
  chartHistory.canvas.parentNode.style.width = '100vw';
  chartHistoryUp.canvas.parentNode.style.height = '100%';
  chartHistoryUp.canvas.parentNode.style.width = '100vw';
  chartHistoryDown.canvas.parentNode.style.height = '100%';
  chartHistoryDown.canvas.parentNode.style.width = '100vw';
}else if(screen.width < 1100) {
  chartHistory.canvas.parentNode.style.height = '100%';
  chartHistory.canvas.parentNode.style.width = 'calc(100vw - 250px)';
  chartHistory.canvas.parentNode.style.float = 'left';
  chartHistoryUp.canvas.parentNode.style.height = '100%';
  chartHistoryUp.canvas.parentNode.style.width = 'calc(100vw - 250px)';
  chartHistoryUp.canvas.parentNode.style.float = 'left';
  chartHistoryDown.canvas.parentNode.style.height = '100%';
  chartHistoryDown.canvas.parentNode.style.width = 'calc(100vw - 250px)';
  chartHistoryDown.canvas.parentNode.style.float = 'left';
}else {
  chartHistory.canvas.parentNode.style.height = '50vh';
  chartHistory.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistory.canvas.parentNode.style.float = 'left';
  chartHistoryUp.canvas.parentNode.style.height = '50vh';
  chartHistoryUp.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistoryUp.canvas.parentNode.style.float = 'left';
  chartHistoryDown.canvas.parentNode.style.height = '50vh';
  chartHistoryDown.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistoryDown.canvas.parentNode.style.float = 'left';
}
</script>
