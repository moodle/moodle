M.mod_scormform = {};
M.mod_scormform.init = function(Y) {
    var scormform = document.getElementById('scormviewform');
    var cwidth = scormplayerdata.cwidth;
    var cheight = scormplayerdata.cheight;
    var poptions = scormplayerdata.popupoptions;
    var courseid = scormplayerdata.courseid;
    var launch = scormplayerdata.launch;
    var currentorg = scormplayerdata.currentorg;
    var sco = scormplayerdata.sco;
    var scorm = scormplayerdata.scorm;
    var launch_url = M.cfg.wwwroot+"/mod/scorm/player.php?a="+scorm+"&currentorg="+currentorg+"&scoid="+sco+"&sesskey="+M.cfg.sesskey;
    var course_url = M.cfg.wwwroot+"/course/view.php?id="+courseid+"&sesskey="+M.cfg.sesskey;

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

    if (launch == true) {
        launch_url = launch_url+"&display=popup";
        window.open(launch_url,'Popup', poptions);
        parent.window.location = course_url;
    }
    scormform.onsubmit = function() {window.open('', 'Popup', poptions); this.target='Popup'; parent.window.location = course_url;};
}
