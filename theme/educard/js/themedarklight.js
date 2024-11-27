"use strict";
var dlcurrentTheme = localStorage.getItem("theme-dashboard-educard");
if (dlcurrentTheme == "dark") {
    dliconchange();
}

function dliconchange() {
  window.addEventListener("DOMContentLoaded", () => {
    document.getElementById("darki-fp").classList.toggle("bxs-sun");
  });
}

function fpdark() {
  document.body.classList.toggle("dark-frontpage");
  document.getElementById("darki-fp").classList.toggle("bxs-sun");
  var dbtheme = document.body.classList.contains("dark-frontpage") ? "dark" : "light";
  localStorage.setItem("theme-dashboard-educard", dbtheme);
}
