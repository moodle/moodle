M.gradingform_rubric = {};

/**
 * This function is called for each rubric on page.
 */
M.gradingform_rubric.init = function(Y, options) {
    Y.on('click', M.gradingform_rubric.levelclick, '#rubric-'+options.name+' .level', null, Y, options.name);
    // Capture also space and enter keypress.
    Y.on('key', M.gradingform_rubric.levelclick, '#rubric-' + options.name + ' .level', 'space', Y, options.name);
    Y.on('key', M.gradingform_rubric.levelclick, '#rubric-' + options.name + ' .level', 'enter', Y, options.name);

    Y.all('#rubric-'+options.name+' .radio').setStyle('display', 'none')
    Y.all('#rubric-'+options.name+' .level').each(function (node) {
      if (node.one('input[type=radio]').get('checked')) {
        node.addClass('checked');
      }
    });
};

M.gradingform_rubric.levelclick = function(e, Y, name) {
    var el = e.target
    while (el && !el.hasClass('level')) el = el.get('parentNode')
    if (!el) return
    e.preventDefault();
    el.siblings().removeClass('checked');

    // Set aria-checked attribute for siblings to false.
    el.siblings().setAttribute('aria-checked', 'false');
    chb = el.one('input[type=radio]')
    if (!chb.get('checked')) {
        chb.set('checked', true)
        el.addClass('checked')
        // Set aria-checked attribute to true if checked.
        el.setAttribute('aria-checked', 'true');
    } else {
        el.removeClass('checked');
        // Set aria-checked attribute to false if unchecked.
        el.setAttribute('aria-checked', 'false');
        el.get('parentNode').all('input[type=radio]').set('checked', false)
    }
}
