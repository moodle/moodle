var FORMATCHOOSER = function() {
    FORMATCHOOSER.superclass.constructor.apply(this, arguments);
};

Y.extend(FORMATCHOOSER, Y.Base, {
    initializer: function(params) {
        if (params && params.formid) {
            var form = Y.one('#' + params.formid);
            var updatebut = form.one('#id_updatecourseformat');
            var formatselect = form.one('#id_format');
            var ancestor = updatebut.ancestor('fieldset');
            var action = form.get('action');
            if (updatebut && formatselect) {
                updatebut.setStyle('display', 'none');
                formatselect.on('change', function() {
                    form.set('action', action + '#' + ancestor.get('id'));
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
