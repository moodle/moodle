// $Id$
//
// QuizTimer
// Provides a counter that keeps track how much
// time user have left to check in started quiz.
//
function countdown_clock(theTimer) {
    var timeout_id = null;

    quizTimerValue = Math.floor((ec_quiz_finish - new Date().getTime())/1000);

    if(quizTimerValue <= 0) {
        clearTimeout(timeout_id);
        document.getElementById('timeup').value = 1;
        var ourForm = document.getElementById('responseform');
        if (ourForm.onsubmit) { 
            ourForm.onsubmit();
        }
        ourForm.submit();
        return;
    }

    now = quizTimerValue;
    var hours = Math.floor(now/3600);
    now = now - (hours*3600);
    var minutes = Math.floor(now/60);
    now = now - (minutes*60);
    var seconds = now;

    var t = "" + hours;
    t += ((minutes < 10) ? ":0" : ":") + minutes;
    t += ((seconds < 10) ? ":0" : ":") + seconds;
    window.status = t.toString();

    if(hours == 0 && minutes == 0 && seconds <= 15) {
        //go from fff0f0 to ffe0e0 to ffd0d0...ff2020, ff1010, ff0000 in 15 steps
        var hexascii = "0123456789ABCDEF";
        var col = '#' + 'ff' + hexascii.charAt(seconds) + '0' + hexascii.charAt(seconds) + 0;
        theTimer.style.backgroundColor = col;
    }
    document.getElementById('time').value = t.toString();
    timeout_id = setTimeout("countdown_clock(theTimer)", 1000);
}

function movecounter(timerbox) {
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
        timerbox.style.top = pos + 'px';
    }
    old = pos;
    temp = setTimeout('movecounter(timerbox)',100);
}