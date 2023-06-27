/**
 * A managed course.
 *
 * @namespace M.course.management
 * @class Course
 * @constructor
 * @extends Item
 */
Course = function() {
    Course.superclass.constructor.apply(this, arguments);
};
Course.NAME = 'moodle-course-management-course';
Course.CSS_PREFIX = 'management-course';
Course.ATTRS = {

    /**
     * The course ID of this course.
     * @attribute courseid
     * @type Number
     */
    courseid: {},

    /**
     * True if this is the selected course.
     * @attribute selected
     * @type Boolean
     * @default null
     */
    selected: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('node').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value: null
    },
    node: {

    },
    /**
     * The management console tracking this course.
     * @attribute console
     * @type Console
     * @writeOnce
     */
    console: {
        writeOnce: 'initOnly'
    },

    /**
     * The category this course belongs to.
     * @attribute category
     * @type Category
     * @writeOnce
     */
    category: {
        writeOnce: 'initOnly'
    }
};
Course.prototype = {
    /**
     * Initialises the new course instance.
     * @method initializer
     */
    initializer: function() {
        var node = this.get('node'),
            category = this.get('category');
        this.set('courseid', node.getData('id'));
        if (category && category.registerCourse) {
            category.registerCourse(this);
        }
        this.set('itemname', 'course');
    },

    /**
     * Returns the name of the course.
     * @method getName
     * @return {String}
     */
    getName: function() {
        return this.get('node').one('a.coursename').get('innerHTML');
    },

    /**
     * Handles an event relating to this course.
     * @method handle
     * @param {String} action
     * @param {EventFacade} e
     * @return {Boolean}
     */
    handle: function(action, e) {
        var managementconsole = this.get('console'),
            args = {courseid: this.get('courseid')};
        switch (action) {
            case 'moveup':
                e.halt();
                managementconsole.performAjaxAction('movecourseup', args, this.moveup, this);
                break;
            case 'movedown':
                e.halt();
                managementconsole.performAjaxAction('movecoursedown', args, this.movedown, this);
                break;
            case 'show':
                e.halt();
                managementconsole.performAjaxAction('showcourse', args, this.show, this);
                break;
            case 'hide':
                e.halt();
                managementconsole.performAjaxAction('hidecourse', args, this.hide, this);
                break;
            case 'select':
                var c = this.get('console'),
                    movetonode = c.get('courselisting').one('#menumovecoursesto');
                if (movetonode) {
                    if (c.isCourseSelected(e.currentTarget)) {
                        movetonode.removeAttribute('disabled');
                    } else {
                        movetonode.setAttribute('disabled', true);
                    }
                }
                break;
            default:
                Y.log('Invalid AJAX action requested of managed course.', 'warn', 'moodle-course-management');
                return false;
        }
    },

    /**
     * Removes this course.
     * @method remove
     */
    remove: function() {
        this.get('console').removeCourseById(this.get('courseid'));
        this.get('node').remove();
    },

    /**
     * Moves this course after another course.
     *
     * @method moveAfter
     * @param {Number} moveaftercourse The course to move after or 0 to put it at the top.
     * @param {Number} previousid the course it was previously after in case we need to revert.
     */
    moveAfter: function(moveaftercourse, previousid) {
        var managementconsole = this.get('console'),
            args = {
                courseid: this.get('courseid'),
                moveafter: moveaftercourse,
                previous: previousid
            };
        managementconsole.performAjaxAction('movecourseafter', args, this.moveAfterResponse, this);
    },

    /**
     * Performs the actual move.
     *
     * @method moveAfterResponse
     * @protected
     * @param {Number} transactionid The transaction ID for the request.
     * @param {Object} response The response to the request.
     * @param {Objects} args The arguments that were given with the request.
     * @return {Boolean}
     */
    moveAfterResponse: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            node = this.get('node'),
            previous;
        if (outcome === false) {
            previous = node.ancestor('ul').one('li[data-id=' + args.previous + ']');
            Y.log('AJAX failed to move this course after the requested course', 'warn', 'moodle-course-management');
            if (previous) {
                // After the last previous.
                previous.insertAfter(node, 'after');
            } else {
                // Start of the list.
                node.ancestor('ul').one('li').insert(node, 'before');
            }
            return false;
        }
        Y.log('AJAX successfully moved course (' + this.getName() + ')', 'info', 'moodle-course-management');
        this.highlight();
    }
};
Y.extend(Course, Item, Course.prototype);
