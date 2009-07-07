var is_ie = (navigator.userAgent.toLowerCase().indexOf("msie") != -1);

document.getElementById(punmask.id).setAttribute("autocomplete", "off");

var unmaskdiv = document.getElementById(punmask.id+"unmaskdiv");

var unmaskchb = document.createElement("input");
unmaskchb.setAttribute("type", "checkbox");
unmaskchb.setAttribute("id", punmask.id+"unmask");
unmaskchb.onchange = function() {unmaskPassword(punmask.id);};
unmaskdiv.appendChild(unmaskchb);

var unmasklbl = document.createElement("label");
unmasklbl.innerHTML = punmask.unmaskstr;
if (is_ie) {
  unmasklbl.setAttribute("htmlFor", punmask.id+"unmask");
} else {
  unmasklbl.setAttribute("for", punmask.id+"unmask");
}
unmaskdiv.appendChild(unmasklbl);

if (is_ie) {
  // ugly hack to work around the famous onchange IE bug
  unmaskchb.onclick = function() {this.blur();};
  unmaskdiv.onclick = function() {this.blur();};
}