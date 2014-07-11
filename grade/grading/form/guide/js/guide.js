M.gradingform_guide = {};

/**
 * This function is called for each guide on page.
 */
M.gradingform_guide.init = function(Y, options) {
    var currentfocus = Y.one('.markingguideremark');

    Y.all('.markingguideremark').on('blur', function(e) {
        currentfocus = e.currentTarget;
    });
    Y.all('.markingguidecomment').on('click', function(e) {
        currentfocus.set('value', currentfocus.get('value') + '\n' + e.currentTarget.get('text'));
        currentfocus.focus();
    });

    Y.all('.showmarkerdesc input[type=radio]').on('click', function(e) {
        if (e.currentTarget.get('value')=='false') {
            Y.all('.criteriondescriptionmarkers').addClass('hide');
        } else {
            Y.all('.criteriondescriptionmarkers').removeClass('hide');
        }
    });

    Y.all('.showstudentdesc input[type=radio]').on('click', function(e) {
        if (e.currentTarget.get('value')=='false') {
            Y.all('.criteriondescription').addClass('hide');
        } else {
            Y.all('.criteriondescription').removeClass('hide');
        }
    });
};