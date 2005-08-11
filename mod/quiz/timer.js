// $Id$
//
// QuizTimer
// Provides a counter that keeps track how much
// time user have left to check in started quiz.
//
function countdown_clock() {
    var timeout_id = null;
    quizTimerValue = quizTimerValue - 1;

    if(quizTimerValue == 0) {
        clearTimeout(timeout_id);
        document.forms['responseform'].timeup.value = 1;
        document.forms['responseform'].submit();
    }

    now = quizTimerValue;
    var hours = Math.floor( now / 3600 );
    parseInt(hours);
    now = now - (hours * 3600);
    var minutes = Math.floor(now / 60);
    parseInt(minutes);
    now = now - (minutes * 60);
    var seconds = now;
    parseInt(seconds);

    var t = "" + hours;
    t += ((minutes < 10) ? ":0" : ":") + minutes;
    t += ((seconds < 10) ? ":0" : ":") + seconds;
    window.status = t.toString();

    if(hours == 0 && minutes == 0 && seconds <= 15) {
        //go from fff0f0 to ffe0e0 to ffd0d0...ff2020, ff1010, ff0000 in 15 steps
        var hexascii = "0123456789ABCDEF";
        var col = 'ff' + hexascii.charAt(seconds) + '0' + hexascii.charAt(seconds) + 0;
        changecolor(col);
    }
    document.forms['clock'].time.value = t.toString();
    timeout_id = setTimeout("countdown_clock()", 1000);
}

function movecounter() {

    var pos;

    if (window.innerHeight) {
        pos = window.pageYOffset
    } else if (document.documentElement && document.documentElement.scrollTop) {
        pos = document.documentElement.scrollTop
    } else if (document.body) {
          pos = document.body.scrollTop
    }

    if (pos < theTop) {
        pos = theTop;
    } else {
        pos += 100;
    }
    if (pos == old) {
        this.style.top = pos;
    }
    old = pos;
    temp = setTimeout('movecounter()',100);
}

function getObjectById (name) {

    if (document.getElementById) {
        this.obj = document.getElementById(name);
        this.style = document.getElementById(name).style;
    } else if (document.all) {
        this.obj = document.all[name];
        this.style = document.all[name].style;
    } else if (document.layers) {
        this.obj = document.layers[name];
        this.style = document.layers[name];
    }
}
