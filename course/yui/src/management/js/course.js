/**
 * A managed course.
 *
 * @namespace M.core_course.management
 * @class Course
 * @constructor
 * @extends Item
 */
function Course() {
    Course.superclass.constructor.apply(this, arguments);
}
Course.NAME = 'moodle-course-management-course';
Course.CSS_PREFIX = 'management-course';
Course.ATTRS = {

    /**
     * The course ID of this course.
     * @attribute courseid
     * @type Int
     */
    courseid : {},

    /**
     * True if this is the selected course.
     * @attribute selected
     * @type Boolean
     * @default null
     */
    selected : {
        getter : function(value, name) {
            if (value === null) {
                value = this.get('node').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value : null
    },
    node : {

    },
    /**
     * The management console tracking this course.
     * @attribute console
     * @type Console
     * @writeOnce
     */
    console : {
        writeOnce : 'initOnly'
    },

    /**
     * The category this course belongs to.
     * @attribute category
     * @type Category
     * @writeOnce
     */
    category : {
        writeOnce : 'initOnly'
    }
};
Course.prototype = {
    /**
     * Initialises the new course instance.
     * @method initializer
     */
    initializer : function() {
        var node = this.get('node'),
            category = this.get('category');
        this.set('courseid', node.getData('id'));
        if (category && category.register_course) {
            category.register_course(this);
        }
        this.set('itemname', 'course');
    },

    /**
     * Returns the name of the course.
     * @method getName
     * @returns {String}
     */
    getName : function() {
        return this.get('node').one('a.coursename').get('innerHTML');
    },

    /**
     * Handles an event relating to this course.
     * @method handle
     * @param {String} action
     * @param {EventFacade} e
     * @returns {Boolean}
     */
    handle : function(action, e) {
        var console = this.get('console'),
            args = {courseid : this.get('courseid')};
        switch (action) {
            case 'moveup':
                e.halt();
                console.perform_ajax_action('movecourseup', args, this.moveup, this);
                break;
            case 'movedown':
                e.halt();
                console.perform_ajax_action('movecoursedown', args, this.movedown, this);
                break;
            case 'show':
                e.halt();
                console.perform_ajax_action('showcourse', args, this.show, this);
                break;
            case 'hide':
                e.halt();
                console.perform_ajax_action('hidecourse', args, this.hide, this);
                break;
            default:
                Y.log('Invalid AJAX action requested of managed course.', 'warn', 'core_course');
                return false;
        }
    },

    /**
     * Removes this course.
     * @method remove
     */
    remove : function() {
        this.get('console').remove_course_by_id(this.get('courseid'));
        this.get('node').remove();
    },

    /**
     * Moves this course after another course.
     *
     * @method moveAfter
     * @param {Int} moveaftercourse The course to move after or 0 to put it at the top.
     * @param {Int} previousid the course it was previously after in case we need to revert.
     */
    moveAfter : function(moveaftercourse, previousid) {
        var console = this.get('console'),
            args = {
                courseid : this.get('courseid'),
                moveafter : moveaftercourse,
                previous : previousid
            };
        console.perform_ajax_action('movecourseafter', args, this.moveAfterResponse, this);
    },

    /**
     * Performs the actual move.
     *
     * @method moveAfterResponse
     * @protected
     * @param {Int} transactionid The transaction ID for the request.
     * @param {Object} response The response to the request.
     * @param {Objects} args The arguments that were given with the request.
     * @returns {Boolean}
     */
    moveAfterResponse : function(transactionid, response, args) {
        var outcome = this.check_ajax_response(transactionid, response, args),
            node = this.get('node'),
            previous;
        if (outcome === false) {
            previous = node.ancestor('ul').one('li[data-id='+args.previous+']');
            Y.log('AJAX failed to move this course after the requested course', 'warn', 'core_course');
            if (previous) {
                // After the last previous.
                previous.insertAfter(node, 'after');
            } else {
                // Start of the list.
                node.ancestor('ul').one('li').insert(node, 'before');
            }
            return false;
        }
        Y.log('AJAX successfully moved course ('+this.getName()+')', 'info', 'core_course');
        this.highlight();
    }
};
Y.extend(Course, Item, Course.prototype);