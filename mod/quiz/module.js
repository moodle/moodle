/*
 * JavaScript library for the quiz module.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

M.mod_quiz = M.mod_quiz || {};

M.mod_quiz.init_attempt_form = function(Y) {
    Y.one('#responseform').setAttribute('autocomplete', 'off');
    Y.on('key', function (e) {
        if (!e.target.test('a') && !e.target.test('input[type=submit]') &&
                !e.target.test('input[type=img]')) {
            e.preventDefault();
        }
    }, '#responseform', 'press:13');
    Y.on('submit', M.mod_quiz.timer.stop, '#responseform');
}

M.mod_quiz.init_review_form = function(Y) {
    Y.all('.questionflagsavebutton').remove();

    Y.on('key', function (e) {
        if (!e.target.test('a') && !e.target.test('input[type=submit]') &&
                !e.target.test('input[type=img]')) {
            e.preventDefault();
        }
    }, '.questionflagsaveform', 'press:13');

    Y.on('submit', function(e) { e.halt(); }, '.questionflagsaveform');
}

// Code for updating the countdown timer that is used on timed quizzes.
M.mod_quiz.timer = {
    // YUI object.
    Y: null,

    // Timestamp at which time runs out, according to the student's computer's clock.
    endtime: 0,

    // This records the id of the timeout that updates the clock periodically,
    // so we can cancel.
    timeoutid: null,

    /**
     * @param Y the YUI object
     * @param timeleft, the time remaining, in seconds.
     */
    init: function(Y, timeleft) {
        M.mod_quiz.timer.Y = Y;
        M.mod_quiz.timer.endtime = new Date().getTime() + timeleft*1000;
        M.mod_quiz.timer.update();
        Y.one('#quiz-timer').setStyle('display', 'block');
    },

    /**
     * Stop the timer, if it is running.
     */
    stop: function(e) {
        if (M.mod_quiz.timer.timeoutid) {
            clearTimeout(M.mod_quiz.timer.timeoutid);
        }
    },

    /**
     * Function to convert a number between 0 and 99 to a two-digit string.
     */
    two_digit: function(num) {
        if (num < 10) {
            return '0' + num;
        } else {
            return num;
        }
    },

    // Function to update the clock with the current time left, and submit the quiz if necessary.
    update: function() {
        var Y = M.mod_quiz.timer.Y;
        var secondsleft = Math.floor((M.mod_quiz.timer.endtime - new Date().getTime())/1000);

        // If time has expired, Set the hidden form field that says time has expired.
        if (secondsleft < 0) {
            M.mod_quiz.timer.stop(null);
            Y.one('#quiz-time-left').setContent(M.str.quiz.timesup);
            var input = Y.one('input[name=timeup]');
            input.set('value', 1);
            input.ancestor('form').submit();
            return;
        }

        // If time has nearly expired, change the colour.
        if (secondsleft < 100) {
            Y.one('#quiz-timer').removeClass('timeleft' + (secondsleft + 2))
                    .removeClass('timeleft' + (secondsleft + 1))
                    .addClass('timeleft' + secondsleft);
        }

        // Update the time display.
        var hours = Math.floor(secondsleft/3600);
        secondsleft -= hours*3600;
        var minutes = Math.floor(secondsleft/60);
        secondsleft -= minutes*60;
        var seconds = secondsleft;
        Y.one('#quiz-time-left').setContent('' + hours + ':' +
                M.mod_quiz.timer.two_digit(minutes) + ':' +
                M.mod_quiz.timer.two_digit(seconds));

        // Arrange for this method to be called again soon.
        M.mod_quiz.timer.timeoutid = setTimeout(M.mod_quiz.timer.update, 100);
    }
};

M.mod_quiz.nav = M.mod_quiz.nav || {};

M.mod_quiz.nav.update_flag_state = function(attemptid, questionid, newstate) {
    var Y = M.mod_quiz.nav.Y;
    var navlink = Y.one('#quiznavbutton' + questionid);
    navlink.removeClass('flagged');
    if (newstate == 1) {
        navlink.addClass('flagged');
    }
};

M.mod_quiz.nav.init = function(Y) {
    M.mod_quiz.nav.Y = Y;

    Y.all('#quiznojswarning').remove();

    Y.delegate('click', function(e) {
        if (this.hasClass('thispage')) {
            return;
        }

        e.preventDefault(e);

        var pageidmatch = this.get('href').match(/page=(\d+)/);
        var pageno;
        if (pageidmatch) {
            pageno = pageidmatch[1];
        } else {
            pageno = 0;
        }
        Y.one('#nextpagehiddeninput').set('value', pageno);

        var form = Y.one('#responseform');

        var questionidmatch = this.get('href').match(/#q(\d+)/);
        if (questionidmatch) {
            form.set(action, form.get(action) + '#q' + questionidmatch[1]);
        }

        form.submit();
    }, document.body, '.qnbutton');

    if (Y.one('a.endtestlink')) {
        Y.on('click', function(e) {
            e.preventDefault();
            Y.one('#nextpagehiddeninput').set('value', -1);
            Y.one('#responseform').submit();
        }, 'a.endtestlink');
    }

    if (M.core_question_flags) {
        M.core_question_flags.add_listener(M.mod_quiz.nav.update_flag_state);
    }
};

M.mod_quiz.secure_window = {
    init: function(Y) {
        if (window.location.href.substring(0,4) == 'file') {
            window.location = 'about:blank';
        }
        Y.delegate('contextmenu', M.mod_quiz.secure_window.prevent, document.body, '*');
        Y.delegate('mousedown', M.mod_quiz.secure_window.prevent_mouse, document.body, '*');
        Y.delegate('mouseup', M.mod_quiz.secure_window.prevent_mouse, document.body, '*');
        Y.delegate('dragstart', M.mod_quiz.secure_window.prevent, document.body, '*');
        Y.delegate('selectstart', M.mod_quiz.secure_window.prevent, document.body, '*');
        M.mod_quiz.secure_window.clear_status;
        Y.on('beforeprint', function() {
            Y.one(document.body).setStyle('display', 'none');
        }, window);
        Y.on('afterprint', function() {
            Y.one(document.body).setStyle('display', 'block');
        }, window);
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'press:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'up:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'down:67,86,88+ctrl');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'press:67,86,88+meta');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'up:67,86,88+meta');
        Y.on('key', M.mod_quiz.secure_window.prevent, '*', 'down:67,86,88+meta');
    },

    clear_status: function() {
        window.status = '';
        setTimeout(M.mod_quiz.secure_window.clear_status, 10);
    },

    prevent: function(e) {
        alert(M.str.quiz.functiondisabledbysecuremode);
        e.halt();
    },

    prevent_mouse: function(e) {
        if (e.button != 1 || !/^(INPUT|TEXTAREA|BUTTON|SELECT)$/i.test(e.target.get('tagName'))) {
            alert(M.str.quiz.functiondisabledbysecuremode);
        }
        e.halt();
    },

    close: function(url, delay) {
        setTimeout(function() {
            if (window.opener) {
                window.opener.document.location.reload();
                window.close();
            } else {
                window.location.href = url;
            }
        }, delay*1000);
    }
};
