// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

    // If we are unloading, and we can use sendBeacon then do that, Chrome does not permit synchronous XHR requests on unload.
    if (window.mod_scorm_is_window_closing && navigator && navigator.sendBeacon && FormData) {
        // Ok, old API alert, the param is a URI encoded string. We need to split it and convert it to a supported format.
        // I've chosen FormData and FormData.append as they are compatible with our supported browsers:
        //  - https://developer.mozilla.org/en-US/docs/Web/API/FormData/FormData
        //  - https://developer.mozilla.org/en-US/docs/Web/API/FormData/append

        var vars = param.split('&'),
            i = 0,
            pair,
            key,
            value,
            formData = new FormData();
        for (i = 0; i < vars.length; i++) {
            pair = vars[i].split('=');
            key = decodeURIComponent(pair[0]);
            value = decodeURIComponent(pair[1]);
            formData.append(key, value);
        }
        // We'll also inform it that we are unloading, potentially useful in the future.
        formData.append('unloading', '1');

        // The results is true or false, we don't get the response from the server. Make it look like it was a success.
        navigator.sendBeacon(url, formData);
        // This is what a success looks like when it comes back from the server.
        return "true\n0";
    }

    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))
    //popupwin(url+"\n"+param);
    httpReq.open("POST", url,false);
    httpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    try {
        httpReq.send(param);
    } catch (e) {
        return false;
    }
    if (httpReq.status == 200) {
        //popupwin(url+"\n"+param+"\n"+httpReq.responseText);
        return httpReq.responseText;
    } else {
        return httpReq.status;
    }
}

function popupwin(content) {
    var op = window.open();
    op.document.open('text/plain');
    op.document.write(content);
    op.document.close();
}

/**
 * We wire up a small marker for the unload events triggered when the user is navigating away or closing the tab.
 * This is done because Chrome does not allow synchronous XHR requests on the following unload events:
 *  - beforeunload
 *  - unload
 *  - pagehide
 *  - visibilitychange
 */
(function() {
    // Set up a global var. Sorry about this, old code ... old ways.
    window.mod_scorm_is_window_closing = false;
    var toggle = function() {
        window.mod_scorm_is_window_closing = true;
    };
    // Listen to the four events known to represent an unload operation.
    window.addEventListener('beforeunload', toggle);
    window.addEventListener('unload', toggle);
    window.addEventListener('pagehide', toggle);
    window.addEventListener('visibilitychange', toggle);
})();