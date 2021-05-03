/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to do ajax actions
 *
 **************** All functions ****************
  * For further description please go to requested function
 *
 * check_coupon ( name [Name of coupon], gid [group where coupon can be used], callback [callback function] )
 *
 * discount_price (name [Name of coupon], gid [group where coupon can be used], callback [callback function] )
 *
 */
 var base_url = location.protocol + '//' + location.host + location.pathname;
 var ajax_file = base_url + "ajax.php";

/**
 * Check if we have a vaild coupon
 *
 * ex:
 * check_coupon(name, groupID, function (resp) {
 *   console.log(resp);
 * })
 *
 */
function check_coupon(name, gid, callback) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      callback(this);
    }
  }
  req.open("GET", ajax_file + encodeURI("?action=check&name=" + name + "&gid=" + gid), true);
  req.send();
}

/**
 * Get new price with coupon
 *
 * ex:
 * discount_price(name, groupID, function (resp) {
 *   console.log(resp);
 * })
 *
 * JSON Answer {
 *  response
 *  code
 *  couponName
 *  price
 *  currency
 *  message
 * }
 */
function discount_price(name, gid, callback) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      callback(this);
    }
  }
  req.open("GET", ajax_file + encodeURI("?action=price&name=" + name + "&gid=" + gid), true);
  req.send();
}
