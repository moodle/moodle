/**
 * YUI module for advanced grading methods - the manage page
 *
 * @author David Mudrak <david@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
YUI.add('moodle-core_grading-manage', function(Y) {

    var MANAGE = function() {
        MANAGE.superclass.constructor.apply(this, arguments);
    }

    Y.extend(MANAGE, Y.Base, {

        initializer : function(config) {
            this.setup_messagebox();
        },

        setup_messagebox : function() {
            Y.one('#actionresultmessagebox span').setContent(M.util.get_string('clicktoclose', 'core_grading'));
            Y.one('#actionresultmessagebox').on('click', function(e) {
                e.halt();
                var box = e.currentTarget;
                var anim = new Y.Anim({
                    node: box,
                    duration: 1,
                    to: { opacity: 0, height: 0 },
                });
                anim.run();
                anim.on('end', function() {
                    var box = this.get('node'); // this === anim
                    box.remove(true);
                });
            });
        }

    }, {
        NAME : 'grading_manage_page',
        ATTRS : { }
    });

    M.core_grading = M.core_grading || {};

    M.core_grading.init_manage = function(config) {
        return new MANAGE(config);
    }

}, '@VERSION@', { requires:['base', 'anim'] });
