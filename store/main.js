/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to do js
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * toggleOptions ( ele [element to toogle] )
 *
 * selectElement ( ele [selected element] )
 *
 * showCouponForm ( ele [where to add] )
 *
 * html_coupon_info( ele [coupon name], group [Group ID] )
 *
 */

/**
 * Dropdown toogle
 */
function toggleOptions(ele) {
  ele.getElementsByClassName("options")[0].classList.toggle("choose");
}

/**
 * Select dropdown element
 */
function selectElement(ele) {
  var base = ele.parentNode.parentNode;
  base.getElementsByClassName("headline")[0].innerHTML = ele.textContent;
  base.getElementsByClassName("selectValue")[0].value = ele.getAttribute("data-value");
}

/**
 * Insert display form
 */
function showCouponForm(ele, group) {
  ele.innerHTML = '<label class="txt-input"><input type="text" name="coupon" onchange="html_coupon_info(this.value, ' + group + ')"/><span class="placeholder">Coupon</label><span class="coupon_response"></span>';
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
