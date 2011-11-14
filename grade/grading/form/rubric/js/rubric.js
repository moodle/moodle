M.gradingform_rubric = {};

/**
 * This function is called for each rubric on page.
 */
M.gradingform_rubric.init = function(Y, options) {
    Y.on('click', M.gradingform_rubric.levelclick, '#rubric-'+options.name+' .level', null, Y, options.name);
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
    chb = el.one('input[type=radio]')
    if (!chb.get('checked')) {
        chb.set('checked', true)
        el.addClass('checked')
    } else {
        el.removeClass('checked');
        el.get('parentNode').all('input[type=radio]').set('checked', false)
    }
}
