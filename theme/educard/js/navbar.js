/**
* Navbar
*/
/* Main menu */
(function() {
    "use strict";
    /**
    * Easy selector helper function
    */
    const select = (el, all = false) => {
        el = el.trim()
        if (all) {
        return [...document.querySelectorAll(el)]
        } else {
        return document.querySelector(el)
        }
    }
    /**
    * Easy event listener function
    */
    const on = (type, el, listener, all = false) => {
        let selectEl = select(el, all)
        if (selectEl) {
            if (all) {
                selectEl.forEach(e => e.addEventListener(type, listener))
            } else {
                selectEl.addEventListener(type, listener)
            }
        }
    }

    /**
    * Easy on scroll event listener 
    */
    const onscroll = (el, listener) => {
        el.addEventListener('scroll', listener)
    }

    /**
    * Toggle .navbar-scrolled class to #navheader when page is scrolled
    */
    let selectNav = select('#navheader')
    if (selectNav) {
    const headerScrolled = () => {
        if (window.scrollY > 100) {
            selectNav.classList.add('navbar-scrolled')
        } else {
            selectNav.classList.remove('navbar-scrolled')
        }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
    }
    /**
     * Theme info.
     */
    const idvar = document.getElementById("theme-info");
    if (idvar) {
        document.getElementById("theme-info").innerHTML = "Moodle Theme Educard - Writer Themes Almond";
    }
    /**
    * Navbar announcements.
    */
    if (document.getElementsByClassName("navbar-1").length > 0) {
        document.body.classList.add("educard-nav-1");
    }
    if (document.getElementsByClassName("navbar-2").length > 0) {
        document.body.classList.add("educard-nav-2");
    }
    if (document.getElementsByClassName("navbar-3").length > 0) {
        document.body.classList.add("educard-nav-3");
    }
    if (document.getElementsByClassName("navbar-4").length > 0) {
        document.body.classList.add("educard-nav-4");
    }
})();
