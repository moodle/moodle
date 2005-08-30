<!--
function NewHttpReq() {
    var httpReq = false;
    if (typeof XMLHttpRequest != 'undefined') {
        httpReq = new XMLHttpRequest();
    } else {
        try {
            httpReq = new ActiveXObject("Msxml2.XMLHTTP.4.0");
        } catch (e) {
            try {
                httpReq = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (ee) {
                try {
                    httpReq = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (eee) {
                    httpReq = false;
                }
            }
        }
    }
    return httpReq;
}

function DoRequest(httpReq,url,param) {

    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))

    httpReq.open("POST", url,false);
    httpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    httpReq.send(param);
    if (httpReq.status == 200) {
        return httpReq.responseText;
    } else {
        return httpReq.status;
    }
}

/*function popupwin(content) {
    var op = window.open();
    op.document.open('text/plain');
    op.document.write(content);
    op.document.close();
}*/
//-->