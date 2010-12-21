M.core_question_engine = M.core_question_engine || {};

/**
 * Flag used by M.core_question_engine.prevent_repeat_submission.
 */
M.core_question_engine.questionformalreadysubmitted = false;

/**
 * Initialise a question submit button. This saves the scroll position and
 * sets the fragment on the form submit URL so the page reloads in the right place.
 * @param id the id of the button in the HTML.
 * @param slot the number of the question_attempt within the usage.
 */
M.core_question_engine.init_submit_button(Y, button, slot) {
    Y.on('click', function(e) {
        var scrollpos = document.getElementById('scrollpos');
        if (scrollpos) {
            scrollpos.value = YAHOO.util.Dom.getDocumentScrollTop();
        }
        button.form.action = button.form.action + '#q' + slot;
    }, button);
}

/**
 * Initialise a form that contains questions printed using print_question.
 * This has the effect of:
 * 1. Turning off browser autocomlete.
 * 2. Stopping enter from submitting the form (or toggling the next flag) unless
 *    keyboard focus is on the submit button or the flag.
 * 3. Removes any '.questionflagsavebutton's, since we have JavaScript to toggle
 *    the flags using ajax.
 * 4. Scroll to the position indicated by scrollpos= in the URL, if it is there.
 * 5. Prevent the user from repeatedly submitting the form.
 * @param Y the Yahoo object. Needs to have the DOM and Event modules loaded.
 * @param form something that can be passed to Y.one, to find the form element.
 */
M.core_question_engine.init_form = function(Y, form) {
    Y.one(form).setAttribute('autocomplete', 'off');

    Y.on('submit', M.core_question_engine.prevent_repeat_submission, form, form, Y);

    Y.on('key', function (e) {
        if (!e.target.test('a') && !e.target.test('input[type=submit]') &&
                !e.target.test('input[type=img]')) {
            e.preventDefault();
        }
    }, form, 'press:13');

    Y.one(form).all('.questionflagsavebutton').remove();

    var matches = window.location.href.match(/^.*[?&]scrollpos=(\d*)(?:&|$|#).*$/, '$1');
    if (matches) {
        // onDOMReady is the effective one here. I am leaving the immediate call to
        // window.scrollTo in case it reduces flicker.
        window.scrollTo(0, matches[1]);
        Y.on('domready', function() { window.scrollTo(0, matches[1]); });

        // And the following horror is necessary to make it work in IE 8.
        // Note that the class ie8 on body is only there in Moodle 2.0 and OU Moodle.
        if (YAHOO.util.Dom.hasClass(document.body, 'ie')) {
            question_force_ie_to_scroll(matches[1])
        }
    }
}

/**
 * Event handler to stop the quiz form being submitted more than once.
 * @param e the form submit event.
 * @param form the form element.
 */
M.core_question_engine.prevent_repeat_submission(e, Y) {
    if (M.core_question_engine.questionformalreadysubmitted) {
        e.halt();
        return;
    }

    setTimeout(function() {
        Y.all('input[type=submit]').disabled = true;
    }, 0);
    M.core_question_engine.questionformalreadysubmitted = true;
}

/**
 * Beat IE into submission.
 * @param targetpos the target scroll position.
 */
M.core_question_engine.force_ie_to_scroll(targetpos) {
    var hackcount = 25;
    function do_scroll() {
        window.scrollTo(0, targetpos);
        hackcount -= 1;
        if (hackcount > 0) {
            setTimeout(do_scroll, 10);
        }
    }
    Y.on('load', do_scroll, window);
}
