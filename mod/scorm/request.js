function NewHttpReq() {
    var httpReq = false;
    if (typeof XMLHttpRequest!='undefined') {
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

function DoRequest(httpReq,url) {
    //
    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))
    //
    httpReq.open("get", url,false);
    httpReq.send(null);
    return httpReq.responseText;
}