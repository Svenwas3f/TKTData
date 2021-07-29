<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage livdata
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Livedata->is_archive( $date [Date to be checked] ) {private function} {static function}
 *
 * Livedata->up ( $ticketToken [used ticket] ) {static function}
 *
 * Livedata->down ( $ticketToken [used ticket] ) {static function}
 *
 * Livedata->live_time () {static function}
 *
 * Livedata->trend () {static function}
 *
 * Livedata->visitors ( $start [start date, Default, use live info], $end [end date, Default, use live info] ) {static function}
 *
 * Livedata->history ( $start [start date], $end [end date] ) {static function}
 *
 * Livedata->historyUp ( $start [start date], $end [end date] ) {static function}
 *
 * Livedata->historyDown ( $start [start date], $end [end date] ) {static function}
 *
 * Livedata->archive() {static function}
 *
 * Livedata->export( $archiveTimestamp [timestamp of archive] ) {static function}
 *
 **************** liveActions ****************
 * liveAction: int to check if up or down (0: up; 1: down)
 *
 */
class Livedata {
  /**
   * Checks if date is archive and returns true or false
   *
   * $date = Date to be checked
   */
  private static function is_archive( $date ) {
    //Create connection
    $conn = Access::connect();

    //Request db
    $stmt = $conn->prepare("SELECT * FROM " . LIVEDATA_ARCHIVE . " WHERE action_timestamp=:action_timestamp");
    $stmt->execute( array(":action_timestamp" => date("Y-m-d H:i:s", strtotime($date))) );

    //Return type
    return ($stmt->rowCount() > 0) ? true : false;
  }

  /**
   * Counts one up and returns true or false
   */
  public static function up() {
    //Create connection
    $conn = Access::connect();

    //Add element
    $stmt = $conn->prepare("INSERT INTO " . LIVEDATA . " (liveAction, action_timestamp) VALUES ('0', CURRENT_TIMESTAMP)"); //Live action: 0 = up; 1 = down
    return $stmt->execute();
  }

  /**
   * Counts one down and returns true or false
   */
  public static function down() {
    //Create connection
    $conn = Access::connect();

    //Check if minimum reached
    $livedata = new Livedata();
    if($livedata->visitors() < 1) {
      return false;
    }

    //Add element
    $stmt = $conn->prepare("INSERT INTO " . LIVEDATA . " (liveAction, action_timestamp) VALUES ('1', CURRENT_TIMESTAMP)"); //Live action: 0 = up; 1 = down
    return $stmt->execute();
  }

  /**
   * Returns minimun and maximum timestamp of live-informations
   */
  public static function live_time( $archive_timestamp = null) {
    //Create connection
    $conn = Access::connect();

    if(isset($archive_timestamp)) {
      //Get lowest and heighest timestamp
      $min_max = $conn->prepare("SELECT min(action_timestamp) as `min`, max(action_timestamp) as `max` FROM " . LIVEDATA_ARCHIVE . " WHERE archive_timestamp=:archive_timestamp"); // && self::is_archive( $archive_timestamp )

      $min_max->execute(array(":archive_timestamp" => $archive_timestamp));
    }else {
      //Get lowest and heighest timestamp
      $min_max = $conn->prepare("SELECT min(action_timestamp) as `min`, max(action_timestamp) as `max` FROM " . LIVEDATA);
      $min_max->execute();
    }

    //Return array
    return $min_max->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Returns trend of last 15 Minutes
   *
   * 0: Trend up
   * 1: Trend down
   * 2: No trend dedected
   */
  public function trend() {
    //Create connection
    $conn = Access::connect();

    //Get up and down data in last 15 minutes
    $stmt = $conn->prepare("SELECT liveAction, COUNT(liveAction) as trend FROM " . LIVEDATA . " WHERE action_timestamp > '" . date("Y-m-d H:i:s", strtotime("-15 minutes")) . "' GROUP BY liveAction");
    $stmt->execute();

    //Get result
    $trendData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Modifie result
    if(! is_array($trendData)) {
      $trendData = array();
    }

    if(! array_key_exists(0, $trendData)) {
      array_push($trendData, array(
        "liveAction" => 0,
        "trend" => 0,
      ));
    }

    if(! array_key_exists(1, $trendData)) {
      array_push($trendData, array(
        "liveAction" => 1,
        "trend" => 0,
      ));
    }

    /**
     * Return data trend
     *
     * 0: Trend up
     * 1: Trend down
     * 2: No trend dedected
     */
    if( $trendData[0]["trend"] > $trendData[1]["trend"] ) {
      return $trendData[0]["liveAction"];
    }elseif( $trendData[0]["trend"] == $trendData[1]["trend"] ) {
      return 2;
    }else {
      return $trendData[1]["liveAction"];
    }
  }

  /**
   * Returns current visitors (not less than 0)
   *
   * $start: Start date [Default, use live info]
   * $end: End date [Default, use live info]
   */
  public function visitors( $start = null, $end = null) {
    //Create connection
    $conn = Access::connect();

    if(is_null($start) || is_null($end)) {
      //Get up data
      $up = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . LIVEDATA . "  WHERE liveAction=0");
      $up->execute();

      //Get down data
      $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . LIVEDATA . "  WHERE liveAction=1");
      $down->execute();
    }else {
      //Get table
      $table = self::is_archive($start) ? LIVEDATA_ARCHIVE : LIVEDATA;

      //Get up data
      $up = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . $table . "
        WHERE liveAction=0 AND
        action_timestamp >= '" . date("Y-m-d H:i:s", strtotime($start)) . "' AND
        action_timestamp <= '" . date("Y-m-d H:i:s", strtotime($end)) . "'");
      $up->execute();

      $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . $table . "
        WHERE liveAction=1 AND
        action_timestamp >= '" . date("Y-m-d H:i:s", strtotime($start)) . "' AND
        action_timestamp <= '" . date("Y-m-d H:i:s", strtotime($end)) . "'");
      $down->execute();
    }

    //Get result
    $visitors = $up->fetch()["trend"] - $down->fetch()["trend"];
    return ($visitors < 0 ) ? 0 : $visitors;
  }

  /**
   * Returns history data of dates
   *
   * $start: Start date
   * $end: End date
   */
  public function history($start, $end) {
    //Create connection
    $conn = Access::connect();

    //Get difference
    $diff = (self::is_archive($end)) ? ((strtotime($end) - strtotime($start)) / 8) : ((time() - strtotime($start)) / 8);

    //Get seconds, minutes, hours, days, weeks, months, years, decades
    $units = array(
      Language::string(6, null, 14),
      Language::string(7, null, 14),
      Language::string(8, null, 14),
      Language::string(9, null, 14),
      Language::string(10, null, 14),
      Language::string(11, null, 14),
      Language::string(12, null, 14),
      Language::string(13, null, 14),
    );
    if($diff < 60) { // return seconds
      $unit = $units[0];
      $unitDv = 1;
    }elseif ($diff < 60*60) { //resturn minutes
      $unit = $units[1];
      $unitDv = 60;
    }elseif ($diff < 60*60*24) { //return hours
      $unit = $units[2];
      $unitDv = 60*60;
    }elseif ($diff < 60*60*24*7){ //return weeks
      $unit = $units[3];
      $unitDv = 60*60*24;
    }elseif ($diff < 60*60*24*365/12) { //return days
      $unit = $units[4];
      $unitDv = 60*60*24*7;
    }elseif ($diff < 60*60*24*365) { //retun months
      $unit = $units[5];
      $unitDv = 60*60*24*365/12;
    }elseif ($diff < 60*60*24*365*10) { //return years
      $unit = $units[6];
      $unitDv = 60*60*24*365;
    }else { //return decades
      $unit = $units[7];
      $unitDv = 60*60*24*365*10;
    }

    //Define results
    $dataY = array();
    $dataX = array();

    for($i = 0; $i <= 8; $i++) {
      //Define start end end
      $partEnd = date("Y-m-d H:i:s", strtotime($start) + $diff * $i);

      //Set dataY
      array_push($dataY, "-" . round(((8 - $i) * $diff / $unitDv), 0) . " " . $unit);

      //Set dataX
      array_push($dataX, self::visitors($start, $partEnd));
    }

    //Return values
    return array("x" => $dataX,"y" =>  $dataY);
  }

  /**
   * Returns up data of dates
   *
   * $start: Start date
   * $end: End date
   */
  public function historyUp($start, $end) {
    //Create connection
    $conn = Access::connect();

    //Get difference
    $diff = (self::is_archive($end)) ? ((strtotime($end) - strtotime($start)) / 8) : ((time() - strtotime($start)) / 8);

    //Get seconds, minutes, hours, days, months, years, decades
    $units = array(
      Language::string(6, null, 14),
      Language::string(7, null, 14),
      Language::string(8, null, 14),
      Language::string(9, null, 14),
      Language::string(10, null, 14),
      Language::string(11, null, 14),
      Language::string(12, null, 14),
      Language::string(13, null, 14),
    );
    if($diff < 60) { // return seconds
      $unit = $units[0];
      $unitDv = 1;
    }elseif ($diff < 60*60) { //resturn minutes
      $unit = $units[1];
      $unitDv = 60;
    }elseif ($diff < 60*60*24) { //return hours
      $unit = $units[2];
      $unitDv = 60*60;
    }elseif ($diff < 60*60*24*7){ //return weeks
      $unit = $units[3];
      $unitDv = 60*60*24;
    }elseif ($diff < 60*60*24*365/12) { //return days
      $unit = $units[4];
      $unitDv = 60*60*24*7;
    }elseif ($diff < 60*60*24*365) { //retun months
      $unit = $units[5];
      $unitDv = 60*60*24*365/12;
    }elseif ($diff < 60*60*24*365*10) { //return years
      $unit = $units[6];
      $unitDv = 60*60*24*365;
    }else { //return decades
      $unit = $units[7];
      $unitDv = 60*60*24*365*10;
    }

    //Define results
    $dataY = array();
    $dataX = array();

    for($i = 0; $i <= 8; $i++) {
      //Define start end end
      $partStart = date("Y-m-d H:i:s", strtotime($start) + $diff * ($i - 1));
      $partEnd = date("Y-m-d H:i:s", strtotime($start) + $diff * $i);

      if(is_null($start) || is_null($end)) {
        //Get down data
        $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . LIVEDATA . "  WHERE liveAction=0");
        $down->execute();
      }else {
        //Get table
        $table = self::is_archive($start) ? LIVEDATA_ARCHIVE : LIVEDATA;

        //Get down data
        $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . $table . "
          WHERE liveAction=0 AND
          action_timestamp >= '" . date("Y-m-d H:i:s", strtotime($partStart)) . "' AND
          action_timestamp <= '" . date("Y-m-d H:i:s", strtotime($partEnd)) . "'");
        $down->execute();
      }

      //Set dataY
      array_push($dataY, "-" . round(((8 - $i) * $diff / $unitDv), 0) . " " . $unit);

      //Set dataX
      array_push($dataX, $down->fetch()[0]);
    }

    //Return values
    return array("x" => $dataX,"y" =>  $dataY);
  }

  /**
   * Returns down data of dates
   *
   * $start: Start date
   * $end: End date
   */
  public function historyDown($start, $end) {
    //Create connection
    $conn = Access::connect();

    //Get difference
    $diff = (self::is_archive($end)) ? ((strtotime($end) - strtotime($start)) / 8) : ((time() - strtotime($start)) / 8);

    //Get seconds, minutes, hours, days, months, years, decades
    $units = array(
      Language::string(6, null, 14),
      Language::string(7, null, 14),
      Language::string(8, null, 14),
      Language::string(9, null, 14),
      Language::string(10, null, 14),
      Language::string(11, null, 14),
      Language::string(12, null, 14),
      Language::string(13, null, 14),
    );
    if($diff < 60) { // return seconds
      $unit = $units[0];
      $unitDv = 1;
    }elseif ($diff < 60*60) { //resturn minutes
      $unit = $units[1];
      $unitDv = 60;
    }elseif ($diff < 60*60*24) { //return hours
      $unit = $units[2];
      $unitDv = 60*60;
    }elseif ($diff < 60*60*24*7){ //return weeks
      $unit = $units[3];
      $unitDv = 60*60*24;
    }elseif ($diff < 60*60*24*365/12) { //return days
      $unit = $units[4];
      $unitDv = 60*60*24*7;
    }elseif ($diff < 60*60*24*365) { //retun months
      $unit = $units[5];
      $unitDv = 60*60*24*365/12;
    }elseif ($diff < 60*60*24*365*10) { //return years
      $unit = $units[6];
      $unitDv = 60*60*24*365;
    }else { //return decades
      $unit = $units[7];
      $unitDv = 60*60*24*365*10;
    }

    //Define results
    $dataY = array();
    $dataX = array();

    for($i = 0; $i <= 8; $i++) {
      //Define start end end
      $partStart = date("Y-m-d H:i:s", strtotime($start) + $diff * ($i - 1));
      $partEnd = date("Y-m-d H:i:s", strtotime($start) + $diff * $i);

      if(is_null($start) || is_null($end)) {
        //Get down data
        $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . LIVEDATA . "  WHERE liveAction=1");
        $down->execute();
      }else {
        //Get table
        $table = self::is_archive($start) ? LIVEDATA_ARCHIVE : LIVEDATA;

        //Get down data
        $down = $conn->prepare("SELECT COUNT(liveAction) as trend FROM " . $table . "
          WHERE liveAction=1 AND
          action_timestamp >= '" . date("Y-m-d H:i:s", strtotime($partStart)) . "' AND
          action_timestamp <= '" . date("Y-m-d H:i:s", strtotime($partEnd)) . "'");
        $down->execute();
      }

      //Set dataY
      array_push($dataY, "-" . round(((8 - $i) * $diff / $unitDv), 0) . " " . $unit);

      //Set dataX
      array_push($dataX, $down->fetch()[0]);
    }

    //Return values
    return array("x" => $dataX,"y" =>  $dataY);
  }

  /**
   * Function to archive livedata
   */
  public static function archive() {
    //Create connection
    $conn = Access::connect();

    //Create archive timestamp
    $archiveTimestamp = date("Y-m-d H:i:s");

    //Import data
    $import = $conn->prepare("INSERT INTO " . LIVEDATA_ARCHIVE . " (archive_timestamp, liveAction, action_timestamp) SELECT :archiveTimestamp, liveAction, action_timestamp FROM " . LIVEDATA);
    if(! $import->execute(array(":archiveTimestamp" => $archiveTimestamp)) ) {
      return false;
    }

    //Remvoe data from live
    $truncate = $conn->prepare("TRUNCATE " . LIVEDATA);
    if(! $truncate->execute()) {
      return false;
    }

    //All OK
    return true;
  }

  /**
   * Function to get CSV export
   *
   * $archiveTimestamp: timestamp of archive
   */
  public static function export( $archiveTimestamp ) {
    //Create connection
    $conn = Access::connect();

    //Get content
    $export = $conn->prepare("SELECT liveAction, action_timestamp FROM " . LIVEDATA_ARCHIVE . " WHERE archive_timestamp=:archiveTimestamp");
    $export->execute(array(":archiveTimestamp" => date("Y-m-d H:i:s", strtotime($archiveTimestamp))));

    //return csv
    $csv_values = $export->fetchAll(PDO::FETCH_ASSOC);

    $csvArray = ["header" => implode (",", array_keys($csv_values[0]))] + array_map(function($item) {
        return implode (",", $item);
    }, $csv_values);
    $csv = implode("\n", $csvArray);

    return $csv;
  }
}
 ?>
