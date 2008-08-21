// hopefully fool ie IE proof way of getting DOM element
function safeGetElement(doc, el) {
    return doc.ids ? doc.ids[el] : doc.getElementById ? doc.getElementById(el) : doc.all[el];
}

// Add in a JS controlled link for toggling the Debug logging
var logButton = document.createElement('a');
logButton.id = 'mod-scorm-log-toggle';
logButton.name = 'logToggle';
logButton.href = 'javascript:toggleLog();';
if (getLoggingActive() == "A") {
    logButton.innerHTML = '<?php print_string('scormloggingon','scorm') ?>';
} else {
    logButton.innerHTML = '<?php print_string('scormloggingoff','scorm') ?>';
}
var content = safeGetElement(document, 'content');
content.appendChild(logButton);

// retrieve cookie data
function getCookie (cookie_name){
    var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
    if ( results ) {
        return (unescape(results[2]));
    } else {
        return null;
    }
}

// retrieve the logging flag from a Cookie
function getLoggingActive () {
    var loggingActive = getCookie('SCORMLoggingActive');
    if (!loggingActive) {
        loggingActive = 'A';
    }
    return loggingActive;
}

// set the logging flag in a cookie
function setLoggingActive (flag) {
    new cookie("SCORMLoggingActive", flag, 365, "/").set();
}

// toggle the logging 
function toggleLog () {
    if (getLoggingActive() == "A") {
        AppendToLog("Moodle Logging Deactivated", 0);
        setLoggingActive('N');
        logButton.innerHTML = '<?php print_string('scormloggingoff','scorm') ?>';
    } else {
        setLoggingActive('A');
        AppendToLog("Moodle Logging Activated", 0);
        logButton.innerHTML = '<?php print_string('scormloggingon','scorm') ?>';
        logPopUpWindow.focus();
    }
}

// globals for the log accumulation
var logString = "";
var logRow = 0;
var logPopUpWindow = "N";

// add each entry to the log, or setup the log pane first time round
function UpdateLog(s) {
    var s1 = '<html><head><style>\n'
        + 'body {font-family: Arial, Helvetica, Sans-Serif;font-size: xx-small;'
        + 'margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px; '
        + 'background-color: ffffff;}\n'
        + '.even {background-color: ffffff; width: 100%;}\n'
        + '.odd {background-color: e8f2fe; width: 100%;}\n'
        + '.error {background-color: ffffff; color: red; width: 100%;}\n'
        + '<\/style><\/head><body STYLE="background-color: ffffff; color: black"'
        + 'marginwidth="0" leftmargin="0" hspace="0">'
        + '<input type="hidden" id="mod-scorm-logstate" name="mod-scorm-logstate" value="A" \/>'
        + '<h3 id="mod-scorm-marker">SCORM API Activity Log<\/h3>';

    // Is logging active?
    if (getLoggingActive() != "A") {
        return;
    }

    var popupdoc = '';
    logString += s;
    if (logPopUpWindow != 'N' && !logPopUpWindow.closed) {
        popupdoc = logPopUpWindow.document;
        popupdoc.body.innerHTML += s;
    } else {
        logPopUpWindow = open( '', 'logpopupwindow', '' );
        popupdoc = logPopUpWindow.document;
        // Is logging active?
        var marker = safeGetElement(popupdoc, 'mod-scorm-marker');
        if (marker) {
            popupdoc.body.innerHTML += s;
        } else {
            popupdoc.open();
            popupdoc.write(s1);
            popupdoc.write(logString);
            popupdoc.write('<\/body><\/html>')
            popupdoc.close();
            popupdoc.title = 'SCORM API Activity Log';
            logPopUpWindow.focus();
        }
    }
    if (popupdoc.body.childNodes.length > 0) {
        popupdoc.body.lastChild.scrollIntoView();
    };
}

//add an individual log entry
function AppendToLog(s, rc) {
    var sStyle;
    if (rc != 0) {
        sStyle = 'class="error';
    } else if (logRow % 2 != 0) {
        sStyle = 'class="even';
    } else {
        sStyle = 'class="odd';
    }
    sStyle += '"';
    var now = new Date();
    now.setTime( now.getTime() );
    s = '<div ' + sStyle + '>' + now.toGMTString() + ': ' + s + '<\/div>';
    UpdateLog(s);
    // switch colours for a new section of work
    if (s.match(/Commit|Loaded|Initialize|Terminate|Finish|Moodle SCORM|Moodle Logging/)) {
        logRow++;
    }
}

// format a log entry
function LogAPICall(func, nam, val, rc) {
    // drop call to GetLastError for the time being - it produces too much chatter
    if (func.match(/GetLastError/)) {
        return;
    }
    var s = func + '("' + nam + '"';
    if (val != null && ! (func.match(/GetValue|GetLastError/))) {
        s += ', "' + val + '"';
    }
    s += ')';
    if (func.match(/GetValue/)) {
        s += ' - ' + val;
    }
    s += ' => ' + String(rc);
    AppendToLog(s, rc);
}
