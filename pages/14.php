<?php
//Create connection
$conn = Access::connect();

//Get archive infos
$archive = $conn->prepare("SELECT DISTINCT archive_timestamp FROM " . LIVEDATA_ARCHIVE . " ORDER BY archive_timestamp DESC");
$archive->execute();

//Display select
$archive_timestamp = (isset($_GET["archive"]) ? $_GET["archive"] : null);
echo '<div class="livedata-form">';
  echo '<div class="select" onclick="toggleOptions(this)">';
    echo '<input type="text" class="selectValue" name="livedata" value="' . $archive_timestamp . '" required>';
    echo '<span class="headline">' . (is_null($archive_timestamp) ? Language::string(0) : date("d.m.Y H:i", strtotime($archive_timestamp))) . '</span>';

    echo '<div class="options">';
      foreach($archive->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo '<span onclick="location.href = \'' . $url_page . '&archive=' . urlencode($row["archive_timestamp"]) . '\'">' . date("d.m.Y H:i", strtotime($row["archive_timestamp"])) . '</span>';
      }
    echo '</div>';
  echo '</div>';
echo '</div>';
 ?>


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
//Export button
if(! is_null($archive_timestamp)) {
  echo '<a href="' . $url . 'medias/files/livedata/export.php?archive_timestamp=' . urlencode($archive_timestamp) . '" class="export-button">';
    echo '<img src="' . $url . 'medias/icons/export.svg" title="' . Language::string(1) . '">';
  echo '</a>';
}

//Set max and min
$max = Livedata::live_time($archive_timestamp)["max"];
$min = Livedata::live_time($archive_timestamp)["min"];

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
              label: <?php echo '"' . Language::string(2) . '"'; ?>,
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


var chartHistory = live_chart(
  historyData,
  <?php echo json_encode($history["x"]); ?>,
  <?php echo json_encode($history["y"]); ?>,
  <?php echo '"' . Language::string(3) . '"'; ?>,
);
var chartHistoryUp = live_chart(
  historyUp,
  <?php echo json_encode($historyUp["x"]); ?>,
  <?php echo json_encode($historyUp["y"]); ?>,
  <?php echo '"' . Language::string(4) . '"'; ?>,
);
var chartHistoryDown = live_chart(
  historyDownData,
  <?php echo json_encode($historyDown["x"]); ?>,
  <?php echo json_encode($historyDown["y"]); ?>,
  <?php echo '"' . Language::string(5) . '"'; ?>,
);

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
  chartHistory.canvas.parentNode.style.height = '40vh';
  chartHistory.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistory.canvas.parentNode.style.float = 'left';
  chartHistoryUp.canvas.parentNode.style.height = '40vh';
  chartHistoryUp.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistoryUp.canvas.parentNode.style.float = 'left';
  chartHistoryDown.canvas.parentNode.style.height = '40vh';
  chartHistoryDown.canvas.parentNode.style.width = 'calc(50vw - 134px)';
  chartHistoryDown.canvas.parentNode.style.float = 'left';
}
</script>
