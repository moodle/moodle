YUI.add('moodle-course-formatchooser', function(Y) {
    var FORMATCHOOSER = function() {
        FORMATCHOOSER.superclass.constructor.apply(this, arguments);
    }

    Y.extend(FORMATCHOOSER, Y.Base, {
        initializer : function(params) {
            if (params && params.formid) {
                var updatebut = Y.one('#'+params.formid+' #id_updatecourseformat');
                var formatselect = Y.one('#'+params.formid+' #id_format');
                if (updatebut && formatselect) {
                    updatebut.setStyle('display', 'none');
                    formatselect.on('change', function() {
                        updatebut.simulate('click');
                    });
                }
            }
        }
    });

    M.course = M.course || {};
    M.course.init_formatchooser = function(params) {
        return new FORMATCHOOSER(params);
    }
}, '@VERSION@', {requires:['base', 'node', 'node-event-simulate']});
