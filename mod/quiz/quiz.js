/*
 * JavaScript library for the quiz module.
 *
 * (c) The Open University and others.
 * @author T.J.Hunt@open.ac.uk and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/* Used by quiz navigation links to force a form submit, and hence save the user's data. */
function navigate(page) {
    var ourForm = document.getElementById('responseform');
    ourForm.action = ourForm.action.replace(/page=.*/, 'page=' + page);
    if (ourForm.onsubmit) {
        ourForm.onsubmit();
    }
    ourForm.submit();
}

/* Use this in an onkeypress handler, to stop enter submitting the forum unless you 
are actually on the submit button. Don't stop the user typing things in text areas. */
function check_enter(e) {
    var target = e.target ? e.target : e.srcElement;
    var keyCode = e.keyCode ? e.keyCode : e.which;
    if (keyCode==13 && target.nodeName.toLowerCase()!='a' &&
            (!target.type || !(target.type=='submit' || target.type=='textarea'))) {
        return false;
    } else {
        return true;
    }
}

/* Used to update the on-screen countdown clock for quizzes with a time limit */
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

/* Use to keep the quiz timer on-screen as the user scrolls. */
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
