/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: June 2021
 * @Purpose: File to do ajax actions
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * Ajax ( page [page], callback [callback], action [Action name], values [JSON Values] )
 *
 * toggleOptions ( ele [element to toogle] )
 *
 * selectElement ( ele [selected element] )
 *
 * showCouponForm ( ele [where to add] )
 *
 * html_coupon_info( ele [coupon name], group [Group ID] )
 *
 * check_coupon ( name [Name of coupon], gid [group where coupon can be used], callback [callback function] )
 *
 * discount_price (name [Name of coupon], gid [group where coupon can be used], callback [callback function] )
 *
 * ajax_send_mail ( ticketToken [Token of ticket] )
 *
 * toggle_section ( click [HTML Element] )
 *
 * add_product ( input [HTML INPUT Element] )
 *
 * remove_product ( input [HTML INPUT Element] )
 *
 * change_total_price ( input [HTML INPUT Element] )
 *
 * validateForm ( form [HTML FORM Element] )
 *
 */
/**
 * Ajax function
 *
 * page: Requested page
 * callback: Callback function
 * action: Action name
 * values: JSON Values that are needed
 */
function ajax( page, callback, action =null, values =null ) {
  //Important infos
  var base_url = (location.protocol + '//' + location.host + location.pathname).replace(/store(.)*/, "store");
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
  req.send( "p=" + encodeURIComponent(page) + (action ? "&action=" + encodeURIComponent(action) : "") + (values ? "&values=" + encodeURIComponent(JSON.stringify(values)) : "") );
}

/**
 * Dropdown toogle
 * For custom infpus
 */
function toggleOptions(ele) {
  ele.getElementsByClassName("options")[0].classList.toggle("choose");
}

/**
 * Select dropdown element
 * For custom inputs
 */
function selectElement(ele) {
  var base = ele.parentNode.parentNode;
  base.getElementsByClassName("headline")[0].innerHTML = ele.textContent;
  base.getElementsByClassName("selectValue")[0].value = ele.getAttribute("data-value");
}

/**
 * Insert display form (Enables coupon frm)
*/
function showCouponForm(ele, group) {
  ele.innerHTML = '<label class="txt-input"><input type="text" name="coupon" onchange="html_coupon_info(this.value, ' + group + ')"/><span class="placeholder">Coupon</label><span class="coupon_response"></span>';
}

/**
 * Check if we have a vaild coupon (Checks validity of coupon)
 *
 * ex:
 * check_coupon(name, groupID, function (resp) {
 *   console.log(resp);
 * })
 *
 */
function check_coupon(name, gid, callback) {
  var values = new Object();
  values["name"] = name;
  values["gid"] = gid;

  // Do Ajax
  ajax(2, function(c) {
    callback(c);
  }, "check_coupon", values);
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
  var values = new Object();
  values["name"] = name;
  values["gid"] = gid;

  // Do Ajax
  ajax(2, function(c) {
    callback(c);
  }, "get_price", values);
}

/**
* Check coupon and display new price
*/
function html_coupon_info(ele, group) {
  //Check coupon
 check_coupon(ele, group, function(r) {
    //Coupon box and price tag
    var couponResponse = document.getElementsByClassName("coupon_response")[0];
    var price_tag = document.getElementsByClassName("price")[0];
    var discount_tag = document.getElementsByClassName("discount_price")[0];

    //Get infos
    check = JSON.parse(r.responseText);

    //Check if coupon is valid
    if(check.response == false) {
      /**
       * 01: Not enought informations
       * 02: No coupon found
       * 03: Coupon found
       * 04: Coupon no longer available
       * 05: Coupon price
      */
      couponResponse.innerHTML = check.message;
      price_tag.classList.remove("old_price");
      price_tag.innerHTML = (price_tag.getAttribute("data-baseprice") / 100).toFixed(2);
      discount_tag.innerHTML = "";
      return; //stop the execution of function
    }

    //Set correct coupon name
    ele.value = check.couponName;

    //Check price
    discount_price(ele, group, function(r) {
      //Get infos
      var price = JSON.parse(r.responseText);

      //Check if coupon is valid
      if(check.response == false) {
        /**
         * 01: Not enought informations
         * 02: No coupon found
         * 03: Coupon found
         * 04: Coupon no longer available
         * 05: Coupon price
        */
        couponResponse.innerHTML = check.message;
        price_tag.classList.remove("old_price");
        price_tag.innerHTML = (price_tag.getAttribute("data-baseprice") / 100).toFixed(2);
        discount_tag.innerHTML = "";
        return; //stop the execution of function
      }

      //Update price
      couponResponse.innerHTML = "";
      price_tag.classList.add("old_price");
      price_tag.setAttribute("data-baseprice", price.basePrice);
      price_tag.innerHTML = (price.basePrice / 100).toFixed(2) + " " + price.currency;
      discount_tag.innerHTML = (price.discountPrice / 100).toFixed(2);
    });
  });
}

/**
 * Used for FAQ
 */
function accordion(id){
  //Close all elements
  var children = document.getElementsByClassName("accordion")[0].children;

  for(var i = 0; i < children.length; i++) {
    if(i != id) { //Leave out requested
      var question  = children[i].children[0];
      var answer = children[i].children[1];
      var toogler = question.childNodes[1];

      //Create transition delay
      answer.style.transitionDelay = null;
      question.style.transitionDelay = "0.4s";

      //Accordion the content
      answer.style.maxHeight = null;

      //Rotate toogler
      toogler.style.transform = "rotate(45deg)";
      toogler.style.top = "3px";
    }
  }

  //Open requested
  var question = document.getElementsByClassName("headline")[id];
  var answer = question.nextElementSibling;
  var toogler = question.childNodes[1];

  if(answer.style.maxHeight){
    //Create transition delay
    answer.style.transitionDelay = null;
    question.style.transitionDelay = "0.4s";

    //Accordion the content
    answer.style.maxHeight = null;

    //Rotate toogler
    toogler.style.transform = "rotate(45deg)";
    toogler.style.top = "3px";
  }else{
    //Create transition delay
    question.style.transitionDelay = null;
    answer.style.transitionDelay = "0.1s";

    //Accordion the content
    answer.style.maxHeight = answer.scrollHeight + "px";

    //Rotate toogler
    toogler.style.transform = "rotate(225deg)";
    toogler.style.top = "15px";
  }
}

/**
 * Send mail again
 */
function ajax_send_mail( email, id, offset, steps ) {
  /* Inform user */
  document.getElementsByClassName("ajax-response")[0].innerHTML = '  <div class="message-container"><div class="message waiting" onclick="this.remove()"><img src="' + location.protocol + '//' + location.host + location.pathname.replace(/store(.)*/, "") +  '/medias/icons/waiting.svg"><span>die Mail wird gesendet. Wir bitten um etwas Geduld.</span></div></div>';

  // Ajax request
  var values = new Object();
  values["email"] = email;
  values["id"] = id;
  values["offset"] = offset;
  values["steps"] = steps;

  ajax(6, function(c) {
    document.getElementsByClassName("ajax-response")[0].innerHTML = c.responseText;
  }, "send_mail", values);
}

/**
 * Toogles sections
 *
 * click: Element where click is done
 */
function toggle_section( click ) {
  var products = click.parentNode.parentNode.parentNode.getElementsByClassName("productlist")[0];

  console.log(products.scrollHeight + "px");

  if( products.style.maxHeight ) {
    products.style.maxHeight = null;
    click.innerHTML = "+";
  }else {
    products.style.maxHeight = products.scrollHeight + "px";
    click.innerHTML = "-";
  }
}

/**
 * Moves product selection up
 *
 * input: Input that should be modified
 */
function add_product( input ) {
  var form = input.closest("form");
  var newValue = (parseInt(input.value) + 1);

  // Set new value
  if(newValue >= 0 && newValue < 1000) {
    input.value = newValue;
    input.dispatchEvent( new Event('change') );
  }

  // Clear outline
  var inputs = form.getElementsByTagName("input");
  for(var i = 0; i < inputs.length; i++) {
    inputs[i].parentNode.style.outline = "";
  }
}

/**
 * Moves product selection down
 *
 * input: Input that should be modified
 */
function remove_product( input ) {
  var newValue = (parseInt(input.value) - 1);

  if(newValue >= 0 && newValue < 1000) {
    input.value = newValue;
    input.dispatchEvent( new Event('change') );
  }
}

/**
 * Gets price of form
 *
 * input: Intput that requests change
 */
function change_total_price( input ) {
  // Links
  var base_url = (location.protocol + '//' + location.host + location.pathname).replace(/store(.)*/, "store");
  var ajax_file = base_url + "/ajax.php";

  // Get form value
  var form = input.parentNode.parentNode.parentNode.parentNode.parentNode;
  var formData = new FormData( form );
  formData.append("p", 8);
  formData.append("action", "calculate");

  // Ajax request
  var req = new XMLHttpRequest();
  req.open("POST", ajax_file);
  req.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // Get pricebar
      var submenu_total = document.getElementsByClassName("submenu-total")[0];
      var price = submenu_total.getElementsByClassName("price")[0];

      // get values
      var ajax_response = JSON.parse(this.responseText);

      // Add new price
      price.innerHTML = ajax_response.formated;
    }
  }
  req.send(formData);
}

/**
 * Check if form is valid
 *
 * form: HTML form
 */
function validateForm( form ) {
  // Get all inputs
  var inputs = form.getElementsByTagName("input");

  for(var i = 0; i < inputs.length; i++) {
    if( inputs[i].value != 0 && inputs[i].value != undefined && inputs[i].value != null ) {
      form.submit();
      return true;
    }else{
      inputs[i].parentNode.style.outline = "4px solid #9a2e37";
    }
  }

  // No value found
  return false;
}
