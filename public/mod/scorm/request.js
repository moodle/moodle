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

/**
 *
 * @param {XMLHttpRequest} httpReq
 * @param {string} url
 * @param {string} param
 * @param {boolean} allowBeaconAPI Should the BeaconAPI be used if required? Defaults to true
 *    If True, and we can use the Beacon API and are should use the beacon API then we will.
 *    If False, we will not use the Beacon API, even if we expect a synchronous XHR request to fail.
 * @returns {string|boolean|*}
 * @constructor
 */
function DoRequest(httpReq, url, param, allowBeaconAPI) {

    // Default allowBeaconAPI to true. This argument was added to the function late.
    if (typeof allowBeaconAPI === 'undefined') {
        allowBeaconAPI = true;
    }

    /**
     * Returns true if we are able to use the Beacon API in this browser.
     * @returns boolean
     */
    var canUseBeaconAPI = function() {
        return (allowBeaconAPI && navigator && navigator.sendBeacon && FormData);
    };

    /**
     * Returns true if we should use the Beacon API.
     * We don't use the Beacon API unless we have to as it stiffles our ability to return data on the request.
     * @returns {boolean}
     */
    var useBeaconAPI = function() {
        if (typeof window.mod_scorm_useBeaconAPI === 'undefined' || window.mod_scorm_useBeaconAPI === false) {
            // Last ditch effort, the SCORM package may have introduced its own listeners before our listeners.
            // This is OLD API, window.event is not reliable and is not recommended API.
            // https://developer.mozilla.org/en-US/docs/Web/API/Window/event
            if (window.event && ['beforeunload', 'unload', 'pagehide'].indexOf(window.event.type)) {
                window.mod_scorm_useBeaconAPI = true;
            }
        }
        return (window.mod_scorm_useBeaconAPI && canUseBeaconAPI());
    };

    /**
     * Uses the Beacon API to communicate this request to the server.
     * This function always returns a successful result, because we don't get the actual result, the page doesn't wait for it.
     * @param {string} url
     * @param {string} param
     * @returns {string}
     */
    var useSendBeacon = function(url, param) {
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

        // We're going to add a token to the URL that will identify this request as going to the beacon API.
        // In the future this would allow our server side scripts to respond differently when the beacon API
        // is being used, as the response will be discarded.
        if (url.indexOf('?') === -1) {
            // First param
            url += '?api=beacon';
        } else {
            url += '&api=beacon';
        }

        // The results is true or false, we don't get the response from the server. Make it look like it was a success.
        var outcome = navigator.sendBeacon(url, formData);
        if (!outcome) {
            if (console && console.log) {
                console.log('mod_scorm: Failed to queue navigator.sendBeacon request');
            }
            return "false\n101";
        }
        // This is what a success looks like when it comes back from the server.
        return "true\n0";
    };

    // If we are unloading, and we can use sendBeacon then do that, Chrome does not permit synchronous XHR requests on unload.
    if (useBeaconAPI()) {
        return useSendBeacon(url, param);
    }

    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))
    //popupwin(url+"\n"+param);
    httpReq.open("POST", url,false);
    httpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    try {
        httpReq.send(param);
    } catch (e) {
        if (console && console.log) {
            // This may be frivolous as during a shutdown the console log will most likely be lost. But it may help someone.
            var message = 'XHR request from mod_scorm::DoRequest failed';
            if (canUseBeaconAPI()) {
                message += '; attempting to use Beacon API.';
            }
            console.log(message);
        }
        // The HTTP request failed. We don't know why, but as a last ditch effort, in case we are unloading and haven't detected it
        // we will attempt to send the request one more time using the Beacon API. This will result in a successful result regardless
        // of the actual outcome.
        if (canUseBeaconAPI()) {
            return useSendBeacon(url, param);
        }
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
 * Global variable to track whether we should use the Beacon API instead of synchronous XHR.
 * This gets set to true in situations where we expect synchronoush XHR requests to fail.
 */
window.mod_scorm_useBeaconAPI = false;

/**
 * We wire up a small marker for the unload events triggered when the user is navigating away or closing the tab.
 * This is done because Chrome does not allow synchronous XHR requests on the following unload events:
 *  - beforeunload
 *  - unload
 *  - pagehide
 *  - visibilitychange
 */
function mod_scorm_monitorForBeaconRequirement(target) {

    if (typeof target.mod_scorm_monitoring_for_beacon_requirement !== 'undefined') {
        // We're already observing unload events on this target.
        console.log('mod_scorm: unload event handlers already attached');
        return;
    }
    target.mod_scorm_monitoring_for_beacon_requirement = true;

    // The navigator.sendBeacon API is available in all browsers EXCEPT Internet Explorer (IE)
    // Internet explorer should never get past this check.
    if (!navigator || !navigator.sendBeacon) {
        // We can't use the BeaconAPI. There is no point in proceeding to observe unload events.
        // This is done after adding the flag to target, and establishing the window variable.
        return;
    }

    /**
     * Turns on the use of the Beacon API.
     */
    var toggleOn = function() {
        window.mod_scorm_useBeaconAPI = true;
    };

    /**
     * Turns off the use of the Beacon API.
     */
    var toggleOff = function() {
        window.mod_scorm_useBeaconAPI = false;
    };

    /**
     * Observes an event.
     * Required because this patch will be backported.
     * @param {string} on
     * @param {CallableFunction} callback
     */
    var observe = function(on, callback) {
        if (!target.addEventListener) {
            console.log('Unable to attach page dismissal event listeners');
            return null;
        }
        return target.addEventListener(on, callback);
    };

    // Listen to the three events known to represent an unload operation.
    observe('beforeunload', toggleOn);
    observe('unload', toggleOn);
    observe('pagehide', toggleOn);

    // Listen to the event fired when navigating to a page and ensure we toggle useBeaconAPI off.
    // This shouldn't be needed (page should be uncached) but just in case!
    observe('pageshow', toggleOff);

    // Finally listen to the visibility change event, and respond to it.
    // This unfortunately is not ideal, but is required as a SCORM package may also be listening to this and
    // trying to save content when the user hides the page. As this can occur as part of the page dismissal lifecycle
    // we also need to ensure we use the Beacon API here.
    observe('visibilitychange', function() {
        // Visible means synchronous XHR permitted, use XHR.
        // Hidden means synchronous XHR not permitted, use Beacon API.
        if (document.visibilityState === 'visible' || document.visibilityState === 'prerender') {
            toggleOff();
        } else if (document.visibilityState === 'hidden') {
            toggleOn();
        }
    });
}
// Begin monitoring on the main window immediately.
mod_scorm_monitorForBeaconRequirement(window);