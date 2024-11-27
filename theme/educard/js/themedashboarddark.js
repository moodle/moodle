"use strict";
var dlcurrentTheme = localStorage.getItem("theme-dashboard-educard");
if (dlcurrentTheme == "dark") {
    dliconchange();
}

function dliconchange() {
  window.addEventListener("DOMContentLoaded", () => {
    document.getElementById("darki").classList.toggle("bxs-sun");
  });
}

function dashboarddark() {
  document.body.classList.toggle("dark-dashboard");
  document.getElementById("darki").classList.toggle("bxs-sun");
  var dbtheme = document.body.classList.contains("dark-dashboard") ? "dark" : "light";
  localStorage.setItem("theme-dashboard-educard", dbtheme);
}
