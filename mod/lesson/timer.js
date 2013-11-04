/*/////////////////////////////////////////////////////////
// This code is based off of
// "Live Clock Lite" script - Version 1.0
// By Mark Plachetta (astroboy@zip.com.au)
//
// The original script displayed a clock.
// Mark Nielsen modified it to be a countdown timer
// for the lesson module in moodle.
//
//    Below is the code that is used to call this page.
//    echo "<script type=\"text/javascript\">\n";
//        echo "var starttime = ". $timer->starttime . ";\n";
//        echo "var servertime = ". time() . ";\n";
//        echo "var testlength = ". $lesson->maxtime * 60 .";\n";
//        echo "document.write('<script type=\"text/javascript\" src=\"liveclock_lite.js\"><\/script>');\n";
//        echo "window.onload = function () { show_clock(); }";
//    echo "</script>\n";
//
//////////////////////////////////////////////////////////*/

    var myfont_face = "Arial";
    var myfont_size = "12";
    var myfont_color = "#000000";
    var myback_color = "#FFFFFF";
    var mywidth = 80;
    var my12_hour = 1;
    var stopclock = 0;
    var myclock = '';
    var timeleft, hours, minutes, secs;
    var javatimeDate = new Date();
    var javatime = javatimeDate.getTime();
    javatime = Math.floor(javatime/1000);

    var dn = "";
    var old = "";

    if (document.all||document.getElementById) {
        document.write('<span id="LiveClockIE" style="width:'+mywidth+'px;"></span>');
    } else if (document.layers) {
        document.write('<ilayer id="ClockPosNS"><layer width="'+mywidth+'" id="LiveClockNS"></layer></ilayer>');
    } else {
        old = "true"; show_clock();
    }

    /*function leave() {  // feable attempt to run a script when someone leaves a timed test early, failed so far
        window.onunload = window.open('http://www.google.com','','toolbar=no,menubar=no,location=no,height=500,width=500');
    }
    leave();*/

    function show_clock() {

        if (typeof(clocksettings) != 'undefined') {
            if (clocksettings.starttime) {
                starttime = parseInt(clocksettings.starttime);
            }
            if (clocksettings.servertime) {
                servertime =  parseInt(clocksettings.servertime);
            }
            if (clocksettings.testlength) {
                testlength =  parseInt(clocksettings.testlength);
            }
        }

        difference = javatime - servertime;
        starttime = starttime + difference;

        //show clock in NS 4
        if (document.layers)
                document.ClockPosNS.visibility="show";
        if (old == "die") { return; }

        currentDate = new Date();
        current = currentDate.getTime();
        current = Math.floor(current/1000);

        var myclock = '<font style="color:'+myfont_color+'; font-family:'+myfont_face+'; font-size:'+myfont_size+'pt;">';
        if (current > starttime + testlength) {
            myclock += "Time is up";
            stopclock = 1;
        } else {
            timeleft = starttime + testlength - current;
            hours = Math.floor(timeleft/3600);
            timeleft = timeleft - (hours * 3600);
            minutes = Math.floor(timeleft/60);
            secs = timeleft - (minutes * 60);

            if (secs < 10) {
                secs = "0"+secs;
            }
            if (minutes < 10) {
                minutes = "0"+minutes;
            }
            myclock += hours+":"+minutes+":"+secs;
        }
        myclock += '</font>';

        if (old == "true") {
            document.write(myclock);
            old = "die"; return;
        }

        if (document.layers) {
            clockpos = document.ClockPosNS;
            liveclock = clockpos.document.LiveClockNS;
            liveclock.document.write(myclock);
            liveclock.document.close();
        } else if (document.all) {
            LiveClockIE.innerHTML = myclock;
        } else if (document.getElementById) {
            document.getElementById("LiveClockIE").innerHTML = myclock;
        }

        if (stopclock == 0) {
            setTimeout("show_clock()",1000);
        }
}

