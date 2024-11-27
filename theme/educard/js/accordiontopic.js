/**
* Single Course dashboard side menu
*/
!function() {
    "use strict";
    var acc = document.getElementsByClassName("accordioneducard");
    var i;
    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener('click', function() {
            this.classList.toggle('educardactive');
            var paneleducard = this.nextElementSibling;
            if (paneleducard.style.display === 'block') {
                paneleducard.style.display = 'none';
            } else {
                paneleducard.style.display = 'block';
            }
        });
    }
}()
function TopnavFunction() {
    "use strict";
    var acc = document.getElementsByClassName('mycourse20');
    var j;
    var topnav = document.getElementsByClassName('coursetopnavicon');
    topnav[0].classList.toggle('coursetopnavactive');
    for (j = 0; j < acc.length; j++) {
        if (acc[j].style.display === "block") {
            acc[j].style.display = "none";
        } else {
            acc[j].style.display = "block";
        }
    }
}
function TopnavFunction1() {
    "use strict";
    var acc = document.getElementsByClassName('coursesection30');
    var j;
    var topnav = document.getElementsByClassName('coursetopnavicon1');
    topnav[0].classList.toggle('coursetopnavactive1');
    for (j = 0; j < acc.length; j++) {
        if (acc[j].style.display === "block") {
            acc[j].style.display = "none";
        } else {
            acc[j].style.display = "block";
        }
    }
}
