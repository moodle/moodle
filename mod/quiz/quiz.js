/*
 * JavaScript library for the quiz module.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

function init_quiz_form() {
    var responseform = document.getElementById('responseform');
    responseform.setAttribute('autocomplete', 'off');
    YAHOO.util.Event.addListener(responseform, 'keypress', check_enter);
    YAHOO.util.Event.addListener(responseform, 'submit', quiz_timer.stop);
}

/* Use this in an onkeypress handler, to stop enter submitting the forum unless you
are actually on the submit button. Don't stop the user typing things in text areas. */
function check_enter(e) {
    var target = e.target ? e.target : e.srcElement;
    var keyCode = e.keyCode ? e.keyCode : e.which;
    if (keyCode==13 && target.nodeName.toLowerCase()!='a' &&
            (!target.type || !(target.type=='submit' || target.type=='textarea'))) {
        YAHOO.util.Event.preventDefault(e);
    }
}

// Code for updating the countdown timer that is used on timed quizzes.
quiz_timer = {
    // The outer div, so we can get at it to move it when the page scrolls.
    timerouter: null,

    // The element that the time should be displayed in.
    timerdisplay: null,

    // The main quiz for, which we will need to submit when the time expires.
    quizform: null,

    // String that is displayed after the time has run out.
    strtimeup: '',

    // How long is left, in seconds.
    endtime: 0,

    // How often we update the clock display. Delay in milliseconds.
    updatedelay: 500,

    // This records the id of the timeout that updates the clock periodically, so we can cancel it
    // Once time has run out.
    timeoutid: null,

    // Colours used to change the timer bacground colour when time had nearly run out.
    // This array is indexed by number of seconds left.
    finalcolours: [
        '#ff0000',
        '#ff1111',
        '#ff2222',
        '#ff3333',
        '#ff4444',
        '#ff5555',
        '#ff6666',
        '#ff7777',
        '#ff8888',
        '#ff9999',
        '#ffaaaa',
        '#ffbbbb',
        '#ffcccc',
        '#ffdddd',
        '#ffeeee',
        '#ffffff',
    ],

    // Initialise method.
    initialise: function(strtimeup, timeleft) {
        // Set some fields.
        quiz_timer.strtimeup = strtimeup;
        quiz_timer.endtime = new Date().getTime() + timeleft*1000;

        // Get references to some bits of the DOM we need.
        quiz_timer.timerouter = document.getElementById('quiz-timer');
        quiz_timer.timerdisplay = document.getElementById('quiz-time-left');
        quiz_timer.quizform = document.getElementById('responseform');

        // Make the timer visible.
        quiz_timer.timerouter.style.display = 'block';

        // Get things started.
        quiz_timer.update_time();
    },

    // Stop method. Stops the timer if it is running.
    stop: function() {
        if (quiz_timer.timeoutid) {
            clearTimeout(quiz_timer.timeoutid);
        }
    },

    // Function that updates the text displayed in element timer_display.
    set_displayed_time: function(str) {
        var display = quiz_timer.timerdisplay
        if (!display.firstChild) {
            display.appendChild(document.createTextNode(str))
        } else if (display.firstChild.nodeType == 3) {
            display.firstChild.replaceData(0, display.firstChild.length, str);
        } else {
            display.replaceChild(document.createTextNode(str), display.firstChild);
        }
    },

    // Function to convert a number between 0 and 99 to a two-digit string.
    two_digit: function(num) {
        if (num < 10) {
            return '0' + num;
        } else {
            return num;
        }
    },

    // Function to update the clock with the current time left, and submit the quiz if necessary.
    update_time: function() {
        var secondsleft = Math.floor((quiz_timer.endtime - new Date().getTime())/1000);

        // If time has expired, Set the hidden form field that says time has expired.
        if (secondsleft < 0) {
            quiz_timer.stop();
            quiz_timer.set_displayed_time(quiz_timer.strtimeup);
            quiz_timer.quizform.elements.timeup.value = 1;
            if (quiz_timer.quizform.onsubmit) {
                quiz_timer.quizform.onsubmit();
            }
            quiz_timer.quizform.submit();
            return;
        }

        // If time has nearly expired, change the colour.
        if (secondsleft < quiz_timer.finalcolours.length) {
            quiz_timer.timerouter.style.backgroundColor = quiz_timer.finalcolours[secondsleft];
        }

        // Update the time display.
        var hours = Math.floor(secondsleft/3600);
        secondsleft -= hours*3600;
        var minutes = Math.floor(secondsleft/60);
        secondsleft -= minutes*60;
        var seconds = secondsleft;
        quiz_timer.set_displayed_time('' + hours + ':' + quiz_timer.two_digit(minutes) + ':' +
                quiz_timer.two_digit(seconds));

        // Arrange for this method to be called again soon.
        quiz_timer.timeoutid = setTimeout(quiz_timer.update_time, quiz_timer.updatedelay);
    }
};

// Set up synchronisation between question flags and the corresponding button in the nav panel.
function quiz_init_nav_flags() {
    var navblock = document.getElementById('quiznavigation');
    var buttons = YAHOO.util.Dom.getElementsByClassName('qnbutton', 'a', navblock);
    for (var i = 0; i < buttons.length; i++) {
        var button = buttons[i];
        var questionid = button.id.match(/\d+/)[0];
        button.stateupdater = new quiz_nav_updater(button, questionid);
    }
}

// Make the links in the attempt nav panel submit the form.
function quiz_init_attempt_nav() {
    var warning = document.getElementById('quiznojswarning');
    warning.parentNode.removeChild(warning);
    var navblock = document.getElementById('quiznavigation');
    var buttons = YAHOO.util.Dom.getElementsByClassName('qnbutton', 'a', navblock);
    for (var i = 0; i < buttons.length; i++) {
        var button = buttons[i];
        if (YAHOO.util.Dom.hasClass(button, 'thispage')) {
            continue;
        }
        var pageidmatch = button.href.match(/page=(\d+)/);
        var page;
        if (pageidmatch) {
            page = pageidmatch[1];
        } else {
            page = 0;
        }
        var nav = {pageid: page};
        var questionidmatch = button.href.match(/#q(\d+)/);
        if (questionidmatch) {
            nav.questionid = questionidmatch[1];
        }
        YAHOO.util.Event.addListener(button, 'click', quiz_nav_button_click, nav);
    }
    var endlink = YAHOO.util.Dom.getElementsByClassName('endtestlink', 'a', navblock)[0];
    YAHOO.util.Event.addListener(endlink, 'click', quiz_end_test_click);
}

function quiz_nav_button_click(e, nav) {
    YAHOO.util.Event.preventDefault(e);
    document.getElementById('nextpagehiddeninput').value = nav.pageid;
    var form = document.getElementById('responseform');
    if (nav.questionid) {
        form.action += '#q' + nav.questionid;
    }
    form.submit();
}

function quiz_end_test_click(e) {
    YAHOO.util.Event.preventDefault(e);
    document.getElementById('nextpagehiddeninput').value = -1;
    document.getElementById('responseform').submit();
}

function quiz_nav_updater(element, questionid) {
    this.element = element;
    question_flag_changer.add_flag_state_listener(questionid, this);
};

quiz_nav_updater.prototype.flag_state_changed = function(newstate) {
    this.element.className = this.element.className.replace(/\s*\bflagged\b\s*/, ' ');
    if (newstate) {
        this.element.className += ' flagged';
    }
};

quiz_secure_window = {
    // The message displayed when the secure window interferes with the user.
    protection_message: null,

    // Used by close. The URL to redirect to, if we find we are not acutally in a pop-up window.
    close_next_url: '',

    // Code for secure window. This used to be in protect_js.php. I don't understand it,
    // I have just moved it for clenliness reasons.
    initialise: function(strmessage) {
        quiz_secure_window.protection_message = strmessage;
        if (document.layers) {
            document.captureEvents(Event.MOUSEDOWN);
        }
        document.onmousedown = quiz_secure_window.intercept_click;
        document.oncontextmenu = function() {alert(quiz_secure_window.protection_message); return false;};
    },

    // Code for secure window. This used to be in protect_js.php. I don't understand it,
    // I have just moved it for clenliness reasons.
    intercept_click: function(e) {
        if (document.all) {
            if (event.button==1) {
               return false;
            }
            if (event.button==2) {
               alert(quiz_securewindow_message);
               return false;
            }
        }
        if (document.layers) {
            if (e.which > 1) {
               alert(quiz_securewindow_message);
               return false;
            }
        }
    },

    close: function(url, delay) {
        if (url != '') {
            quiz_secure_window.close_next_url = url;
        }
        if (delay > 0) {
            setTimeout(function() {quiz_secure_window.close('', 0);}, delay*1000);
        } else {
            if (window.opener) {
                window.opener.document.location.reload();
                window.close();
            } else if (quiz_secure_window.close_next_url != '') {
                window.location.href = quiz_secure_window.close_next_url;
            }
        }
    }
};

function reveal_start_button() {
    document.getElementById('quizstartbutton').style.cssText = '';
}