/**
* Home page color palette
* 
*/
!function () {
    "use strict";
    document.getElementById('color-theme').addEventListener('change', function (event) {
        document.documentElement.style.setProperty('--theme-color', event.target.value);
        setCookie("theme-color", event.target.value, 7);
        document.documentElement.style.setProperty('--theme-color-rgba', hexToRGB(event.target.value));
        setCookie("theme-color-rgba", hexToRGB(event.target.value), 7);
    });
    document.getElementById('color-theme2').addEventListener('change', function (event) {
        document.documentElement.style.setProperty('--theme-color2', event.target.value);
        setCookie("theme-color2", event.target.value, 7);
        document.documentElement.style.setProperty('--theme-color2-rgba', hexToRGB(event.target.value));
        setCookie("theme-color2-rgba", hexToRGB(event.target.value), 7);
    });
    const myElement = document.getElementById("color-navbar");
    if (myElement) {
        document.getElementById('color-navbar').addEventListener('change', function (event) {
            document.documentElement.style.setProperty('--navbar-color', event.target.value);
            setCookie("navbar-color", event.target.value, 7);
            document.getElementsByClassName('bg-hynavbar')[0].style.setProperty("background-color", event.target.value, "important");
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        var themecolor = getCookie("theme-color");
        if (themecolor) {
            document.documentElement.style.setProperty('--theme-color', themecolor);
            document.getElementById("color-theme").value = themecolor;
        }
        var themecolorrgba = getCookie("theme-color-rgba");
        if (themecolorrgba) {
            document.documentElement.style.setProperty('--theme-color-rgba', themecolorrgba);
        }
        var themecolor2 = getCookie("theme-color2");
        if (themecolor2) {
            document.documentElement.style.setProperty('--theme-color2', themecolor2);
            document.getElementById("color-theme2").value = themecolor2;
        }
        var themecolorrgba2 = getCookie("theme-color2-rgba");
        if (themecolorrgba2) {
            document.documentElement.style.setProperty('--theme-color2-rgba', themecolorrgba2);
        }
        const myElement = document.getElementById("color-navbar");
        if (myElement) {
            var navbarcolor = getCookie("navbar-color");
            if (navbarcolor) {
                document.documentElement.style.setProperty('--navbar-color', navbarcolor);
                document.getElementById("color-navbar").value = navbarcolor;
                document.getElementsByClassName('bg-hynavbar')[0].style.setProperty("background-color", navbarcolor, "important");
            }
        }
    });
    document.getElementById('open-color-pallet').addEventListener('click', function (e) {
        e.preventDefault();
        this.style.display = 'none';
        document.getElementById("color-change").style.animation = 'slide-in-left 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both';
        document.getElementById("color-change").style.display = "block";

    }, false);
    document.getElementById('close-color-pallet').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById("color-change").style.animation = 'slide-out-right 0.5s cubic-bezier(0.550, 0.085, 0.680, 0.530) both';
        //this.parentNode.style.display = 'none';
        document.getElementById("open-color-pallet").style.display = "block";
    }, false);
}()
function hexToRGB(h) {
    "use strict";
    let r = 0, g = 0, b = 0;
    if (h.length == 4) {
        r = "0x" + h[1] + h[1];
        g = "0x" + h[2] + h[2];
        b = "0x" + h[3] + h[3];
    } else if (h.length == 7) {
        r = "0x" + h[1] + h[2];
        g = "0x" + h[3] + h[4];
        b = "0x" + h[5] + h[6];
    }
    return +r + "," + +g + "," + +b;
}
/* Cookie read-write */
function getCookie(cname) {
    "use strict";
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function setCookie(cname, cvalue, exdays) {
    "use strict";
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
