<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to connect to database
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Some functions uses class variable. Those are written behind the function name in square brackets [].
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 * All functions can be used as Static
 *
 * Access->connect ()
 *
 */
class Access {
  public function connect() {
    //Parse ini file
    $dbi = parse_ini_file( PATH_TO_INI );

    $utc = strtotime(gmdate("Y-m-d H:i:s")); //Greenwich mean time
    $systemTime = strtotime(date("Y-m-d H:i:s")); //Local system time
    $secOffset = $systemTime - $utc; //Difference in seconds
    $timeOffset = intval($secOffset/60/60) . ":" .  sprintf("%02d", $secOffset%60);

    //Connect
    try {
      $dsn = "mysql:host=" . $dbi['host'] . ";dbname=" . $dbi['database'];
      $pdo = new PDO( $dsn, $dbi['username'], $dbi['password']);
      $pdo->exec("SET time_zone ='" . $timeOffset . "'"); //Set timezone
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

      return $pdo;
    } catch (PDOException $e) {
      global $url; // Get global variable $url
      header("Location: " . $url . "error/?error=sql" );
      exit;
    }
  }
}
 ?>
