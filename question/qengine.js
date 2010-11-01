M.core_question_engine = M.core_question_engine || {};

/**
 * Initialise a form that contains questions printed using print_question.
 * This has the effect of:
 * 1. Turning off browser autocomlete.
 * 2. Stopping enter from submitting the form (or toggling the next flag) unless
 *    keyboard focus is on the submit button or the flag.
 * 3. Removes any '.questionflagsavebutton's, since we have JavaScript to toggle
 *    the flags using Ajax.
 * @param Y the Yahoo object. Needs to have the DOM and Event modules loaded.
 * @param form something that can be passed to Y.one, to find the form element.
 */
M.core_question_engine.init_form = function(Y, form) {
    Y.one(form).setAttribute('autocomplete', 'off');
    Y.on('key', function (e) {
        if (!e.target.test('a') && !e.target.test('input[type=submit]') &&
                !e.target.test('input[type=img]')) {
            e.preventDefault();
        }
    }, form, 'press:13');
    Y.one(form).all('.questionflagsavebutton').remove();
}
