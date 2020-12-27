YUI.add('moodle-course-formatchooser', function (Y, NAME) {

var FORMATCHOOSER = function() {
    FORMATCHOOSER.superclass.constructor.apply(this, arguments);
};

Y.extend(FORMATCHOOSER, Y.Base, {
    initializer: function(params) {
        if (params && params.formid) {
            var updatebut = Y.one('#' + params.formid + ' #id_updatecourseformat');
            var formatselect = Y.one('#' + params.formid + ' #id_format');
            var ancestor = updatebut.ancestor('fieldset');
            var action = Y.one('form.mform').get('action');
            if (updatebut && formatselect) {
                updatebut.setStyle('display', 'none');
                formatselect.on('change', function() {
                    Y.one('form.mform').set('action', action + '#' + ancestor.get('id'));
                    updatebut.simulate('click');
                });
            }
        }
    }
});

M.course = M.course || {};
M.course.init_formatchooser = function(params) {
    return new FORMATCHOOSER(params);
};


}, '@VERSION@', {"requires": ["base", "node", "node-event-simulate"]});
