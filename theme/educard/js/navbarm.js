!function () {
    "use strict";
    const submenus = document.querySelectorAll('.aside_mobile .menu-item-has-children > a');
    for (const submenu of submenus) {
        submenu.addEventListener('click', function handleClick() {
            var clssubmenu = submenu.nextElementSibling;
            submenu.classList.toggle('a-active');
            if (clssubmenu.style.display === "block") {
                clssubmenu.style.display = "none";
            } else {
                clssubmenu.style.display = "block";
            }
        });
    }

    var el = document.querySelector(".mobile_trigger");
    if(el){
        el.addEventListener('click', function() {
            document.querySelector(".mobile_trigger").classList.toggle('active');
            document.querySelector(".aside_mobile").classList.toggle('open');
        });
    }
    var el = document.querySelector(".trigger-left");
    if(el){
        el.addEventListener('click', function() {
            document.querySelector(".mobile_trigger").classList.toggle('active');
            document.querySelector(".aside_mobile").classList.toggle('open');
        });
    }

}();
