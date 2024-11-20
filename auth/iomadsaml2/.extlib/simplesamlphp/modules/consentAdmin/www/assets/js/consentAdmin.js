var xmlHttp;

function checkConsent()
{
    var show_spid = this.id.charAt(this.id.length-1);
    var checkbox = document.getElementById("checkbox_"+show_spid);

    xmlHttp = GetXmlHttpObject()
    if (xmlHttp === null) {
        alert("Browser does not support HTTP Request")
        return
    }

    var url = "consentAdmin.php"
    url = url+"?cv="+checkbox.value
    url = url+"&action="+checkbox.checked
    url = url+"&sid="+Math.random()

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
            document.getElementById("consentText_" + show_spid).innerHTML = xmlHttp.responseText;
        }
    }

    xmlHttp.open("GET", url, true)
    xmlHttp.send(null)
}

// This function creates an XMLHttpRequest
function GetXmlHttpObject()
{
    var xmlHttp = null;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        //Internet Explorer
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}

function toggleShowAttributes()
{
    var show_spid = this.id.charAt(this.id.length-1);

    var disp = document.getElementById('attributes_' + show_spid);
    var showing = document.getElementById('showing_' + show_spid);
    var hiding = document.getElementById('hiding_' + show_spid);

    disp.style.display = (disp.style.display == 'none' ? 'block' : 'none');
    showing.style.display = (disp.style.display == 'none' ? 'inline' : 'none');
    hiding.style.display = (disp.style.display == 'none' ? 'none' : 'inline');
}

document.addEventListener(
    'DOMContentLoaded',
    function () {
        var show_hide = document.getElementsByClassName("show_hide");
        for (var i = 0; i < show_hide.length; i++) {
            show_hide[i].addEventListener(
                'click',
                toggleShowAttributes
            );
        }

        var checkbox = document.getElementsByClassName("checkbox");
        for (var j = 0; j < checkbox.length; j++) {
            checkbox[j].addEventListener(
                'click',
                checkConsent
            );
        }
    }
);
