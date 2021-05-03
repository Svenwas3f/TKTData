function mailAppendVal(event) {
  var targ = event.target || event.srcElement;
  document.getElementsByName("mail_msg")[0].value += "%" + targ.textContent + "%" || "%" + targ.innerText + "%";
}