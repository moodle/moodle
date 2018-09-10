// Javascript functions for onetopic course format

M.course = M.course || {};

M.course.format = M.course.format || {};

M.course.format.showInfo = function(id) {

    new M.core.dialogue({
                draggable: true,
                headerContent: '<span>' + M.util.get_string('info', 'moodle') + '</span>',
                bodyContent: Y.Node.one('#' + id),
                centered: true,
                width: '480px',
                modal: true,
                visible: true
            });

    Y.Node.one('#' + id).show();

};

M.course.format.dialogueinitloaded = false;

M.course.format.dialogueinit = function() {

    if (M.course.format.dialogueinitloaded) {
        return;
    }

    M.course.format.dialogueinitloaded = true;
    Y.all('[data-infoid]').each(function(node) {
        node.on('click', function() {
            M.course.format.showInfo(node.getAttribute('data-infoid'));
        });
    });
};
