M.mod_scormform = {};
M.mod_scormform.init = function(Y) {
    var scormform = document.getElementById('scormviewform');
    var cwidth = scormplayerdata.cwidth;
    var cheight = scormplayerdata.cheight;
    var poptions = scormplayerdata.popupoptions;
    var launch = scormplayerdata.launch;
    var currentorg = scormplayerdata.currentorg;
    var sco = scormplayerdata.sco;
    var scorm = scormplayerdata.scorm;
    var launch_url = M.cfg.wwwroot+"/mod/scorm/player.php?a="+scorm+"&currentorg="+currentorg+"&scoid="+sco+"&sesskey="+M.cfg.sesskey;
    var course_url = scormplayerdata.courseurl;

    poptions = poptions + ',resizable=yes'; // Added for IE (MDL-32506).

    if ((cwidth==100) && (cheight==100)) {
        poptions = poptions+',width='+screen.availWidth+',height='+screen.availHeight+',left=0,top=0';
    } else {
        if (cwidth<=100) {
            cwidth = Math.round(screen.availWidth * cwidth / 100);
        }
        if (cheight<=100) {
            cheight = Math.round(screen.availHeight * cheight / 100);
        }
        poptions = poptions+',width='+cwidth+',height='+cheight;
    }

    var scormredirect = function (winobj) {
        winobj.onload = function () {

            // Hide the form and toc if it exists - we don't want to allow multiple submissions when a window is open.
            if (scormform) {
                scormform.hide();
            }

            var scormtoc = Y.one('#toc');
            if (scormtoc) {
                scormtoc.hide();
            }
            // Hide the intro and display a message to the user if the window is closed but for some reason the events
            // below aren't triggered.
            var scormintro = Y.one('#intro');
            scormintro.setHTML('<a href="'+ course_url + '">' + M.str.scorm.popuplaunched + '</a>');
        }
        // When pop-up is closed return to course homepage.
        winobj.onunload = function () {
            // Onunload is called multiple times in the SCORM window - we only want to handle when it is actually closed.
            setTimeout(function() {
                if (!winobj.opener) {
                    // Redirect the parent window to the course homepage.
                    parent.window.location = course_url;
                }
            }, 200)
        }
        // Check to make sure pop-up has been launched - if not display a warning,
        // this shouldn't happen as the pop-up here is launched on user action but good to make sure.
        setTimeout(function() {
            if (!winobj) {
                scormintro.setHTML(M.str.scorm.popupsblocked);
            }}, 800);
    }

    if (launch == true) {
        launch_url = launch_url+"&display=popup";
        var winobj = window.open(launch_url,'Popup', poptions);
        this.target='Popup';
        scormredirect(winobj);
    }
    // Listen for view form submit and generate popup on user interaction.
    if (scormform) {
        scormform.onsubmit = function() {
            var winobj = window.open('', 'Popup', poptions);
            this.target='Popup';
            scormredirect(winobj);
        }
    }
}
