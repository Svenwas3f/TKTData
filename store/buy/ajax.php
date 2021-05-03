<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: August 2020
 * @Purpose: File to manage ajax actions of store
 *
 ************* errors *************
 * 01: Not enought informations
 * 02: No coupon found
 * 03: Coupon found
 * 04: Coupon no longer available
 * 05: Coupon price
 */

//Get general file
require_once(dirname(__FILE__, 3) . "/general.php");

//Set headers
header("Access-Control-Allow-Orgin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");

//Check if enought informaions
if(empty($_GET["name"]) || empty($_GET["gid"]) || empty($_GET["action"])) {
  echo json_encode(array(
    "response" => false,
    "code" => 01,
    "message" => "Not enought informations"
  ));
  exit;
}

//Define variables
$name =  $_GET["name"];
$gid = $_GET["gid"]; //Group ID

//Check if valid coupon
$coupon = new Coupon();
$cid = $coupon->get_couponID($name, $gid);
$group = new Group();
$group->groupID = $gid;

if(is_null($cid)) {
  echo json_encode(array(
    "response" => false,
    "code" => 02,
    "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
    "message" => "No coupon found"
  ));
  exit;
}

//Set coupon id
$coupon->couponID = $cid;


//Do ajax request
switch($_GET["action"]) {
  case "check":
    if($coupon->check()) {
      echo json_encode(array(
        "response" => true,
        "code" => 03,
        "couponName" => $coupon->values()["name"],
        "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
        "message" => "Coupon found"
      ));
      exit;
    }else {
      echo json_encode(array(
        "response" => false,
        "code" => 04,
        "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
        "message" => "Coupon no longer available"
      ));
      exit;
    }
  break;
  case "price":
    if($coupon->check()) {
      echo json_encode(array(
        "response" => true,
        "code" => 05,
        "couponName" => $coupon->values()["name"],
        "basePrice" => $group->values()["price"] + ($group->values()["price"] * $group->values()["vat"]/10000),
        "discountPrice" => $coupon->new_price(),
        "currency" => $group->values()["currency"],
        "message" => "Coupon price"
      ));
      exit;
    }else {
      echo json_encode(array(
        "response" => false,
        "code" => 04,
        "basePrice" => $group->values()["price"],
        "message" => "Coupon no longer available"
      ));
      exit;
    }
  break;
}

 ?>
