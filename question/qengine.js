// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript required by the question engine.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Scroll manager is a class that help with saving the scroll positing when you
 * click on an action icon, and then when the page is reloaded after processing
 * the action, it scrolls you to exactly where you were. This is much nicer for
 * the user.
 *
 * To use this in your code, you need to ensure that:
 * 1. The button that triggers the action has to have a click event handler that
 *    calls M.core_scroll_manager.save_scroll_pos
 * 2. The script that process the action has to grab the scrollpos parameter
 *    using $scrollpos = optional_param('scrollpos', 0, PARAM_INT);
 * 3. After doing the processing, it must add ->param('scrollpos', $scrollpos)
 *    to the URL that it redirects to.
 * 4. Finally, on the page that is reloaded (which should be the same as the one
 *    the user started on) you need to call M.core_scroll_manager.scroll_to_saved_pos
 *    on page load.
 */
M.core_scroll_manager = M.core_scroll_manager || {};

/**
 * In the form that contains the element, set the value of the form field with
 * name scrollpos to the current scroll position. If there is no element with
 * that name, it creates a hidden form field wiht that name within the form.
 * @param element the element in the form. Should be something that can be
 *      passed to Y.one.
 */
M.core_scroll_manager.save_scroll_pos = function(Y, element) {
    if (typeof(element) == 'string') {
        // Have to use getElementById here because element id can contain :.
        element = Y.one(document.getElementById(element));
    }
    var form = element.ancestor('form');
    if (!form) {
        return;
    }
    var scrollpos = form.one('input[name=scrollpos]');
    if (!scrollpos) {
        scrollpos = form.appendChild(form.create('<input type="hidden" name="scrollpos" />'));
    }
    scrollpos.set('value', form.get('docScrollY'));
}

/**
 * Event handler that can be used on a link. Assumes that the link already
 * contains at least one URL parameter.
 */
M.core_scroll_manager.save_scroll_action = function(e) {
    var link = e.target.ancestor('a[href]');
    if (!link) {
        M.core_scroll_manager.save_scroll_pos({}, e.target);
        return;
    }
    link.set('href', link.get('href') + '&scrollpos=' + link.get('docScrollY'));
}

/**
 * If there is a parameter like scrollpos=123 in the URL, scroll to that saved position.
 */
M.core_scroll_manager.scroll_to_saved_pos = function(Y) {
    var matches = window.location.href.match(/^.*[?&]scrollpos=(\d*)(?:&|$|#).*$/, '$1');
    if (matches) {
        // onDOMReady is the effective one here. I am leaving the immediate call to
        // window.scrollTo in case it reduces flicker.
        window.scrollTo(0, matches[1]);
        Y.on('domready', function() { window.scrollTo(0, matches[1]); });

        // And the following horror is necessary to make it work in IE 8.
        // Note that the class ie8 on body is only there in Moodle 2.0 and OU Moodle.
        if (Y.one('body').hasClass('ie')) {
            M.core_scroll_manager.force_ie_to_scroll(Y, matches[1])
        }
    }
}

/**
 * Beat IE into submission.
 * @param targetpos the target scroll position.
 */
M.core_scroll_manager.force_ie_to_scroll = function(Y, targetpos) {
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
M.core_question_engine.init_submit_button = function(Y, button, slot) {
    var buttonel = document.getElementById(button);
    Y.on('click', function(e) {
        M.core_scroll_manager.save_scroll_pos(Y, button);
        buttonel.form.action = buttonel.form.action + '#q' + slot;
    }, buttonel);
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
                !e.target.test('input[type=img]') && !e.target.test('textarea')) {
            e.preventDefault();
        }
    }, form, 'press:13');

    Y.one(form).all('.questionflagsavebutton').remove();

    M.core_scroll_manager.scroll_to_saved_pos(Y);
}

/**
 * Event handler to stop a question form being submitted more than once.
 * @param e the form submit event.
 * @param form the form element.
 */
M.core_question_engine.prevent_repeat_submission = function(e, Y) {
    if (M.core_question_engine.questionformalreadysubmitted) {
        e.halt();
        return;
    }

    setTimeout(function() {
        Y.all('input[type=submit]').set('disabled', true);
    }, 0);
    M.core_question_engine.questionformalreadysubmitted = true;
}
