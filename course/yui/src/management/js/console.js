/**
 * Provides drop down menus for list of action links.
 *
 * @module moodle-core_course-management
 */

/**
 * Management JS console.
 *
 * Provides the organisation for course and category management JS.
 *
 * @namespace M.core_course.management
 * @class Console
 * @constructor
 * @extends Y.Base
 */
function Console() {
    Console.superclass.constructor.apply(this, arguments);
}
Console.NAME = 'moodle-course-management';
Console.CSS_PREFIX = 'management';
Console.ATTRS = {
    /**
     * The HTML element containing the management interface.
     * @attribute element
     * @type Node
     */
    element : {
        setter : function(node) {
            if (typeof(node) === 'string') {
                node = Y.one('#'+node);
            }
            return node;
        }
    },

    /**
     * The category listing container node.
     * @attribute categorylisting
     * @type Node
     */
    categorylisting : {},

    /**
     * The course listing container node.
     * @attribute courselisting
     * @type Node
     */
    courselisting : {},

    /**
     * The course details container node.
     * @attribute coursedetails
     * @type Node|null
     * @default null
     */
    coursedetails : {
        value: null
    },

    /**
     * The id of the currently active category.
     * @attribute activecategoryid
     * @type Int
     */
    activecategoryid : {},

    /**
     * The id of the currently active course.
     * @attribute activecourseid
     * @type Int
     */
    activecourseid : {},

    /**
     * The categories that are currently available through the management interface.
     * @attribute categories
     * @type Array
     * @default []
     */
    categories : {
        setter : function(item, name) {
            if (Y.Lang.isArray(item)) {
                return item;
            }
            var items = this.get(name);
            items.push(item);
            return items;
        },
        value : []
    },

    /**
     * The courses that are currently available through the management interface.
     * @attribute courses
     * @type Array
     * @default []
     */
    courses : {
        setter : function(item, name) {
            if (Y.Lang.isArray(item)) {
                return item;
            }
            var items = this.get(name);
            items.push(item);
            return items;
        },
        value : []
    },

    /**
     * The currently displayed page of courses.
     * @attribute page
     * @type Int
     * @default null
     */
    page : {
        getter : function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value : null
    },

    /**
     * The total pages of courses that can be shown for this category.
     * @attribute totalpages
     * @type Int
     * @default null
     */
    totalpages : {
        getter : function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value : null
    },

    /**
     * The total number of courses belonging to this category.
     * @attribute totalcourses
     * @type Int
     * @default null
     */
    totalcourses : {
        getter : function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value : null
    },

    /**
     * The URL to use for AJAX actions/requests.
     * @attribute ajaxurl
     * @type String
     * @default /course/ajax/management.php
     */
    ajaxurl : {
        getter : function(value) {
            if (value === null) {
                value = M.cfg.wwwroot + '/course/ajax/management.php';
            }
            return value;
        },
        value : null
    },

    /**
     * The drag drop handler
     * @attribute dragdrop
     * @type DragDrop
     * @default null
     */
    dragdrop : {
        value : null
    }
};
Console.prototype = {

    /**
     * Gets set to true once the first categories have been initialised.
     * @property categoriesinit
     * @private
     * @type {boolean}
     */
    categoriesinit : false,

    /**
     * Initialises a new instance of the Console.
     * @method initializer
     */
    initializer : function() {
        Y.log('Initialising course category management console', 'note', 'core_course');
        this.set('element', 'coursecat-management');
        var element = this.get('element'),
            categorylisting = element.one('#category-listing'),
            courselisting = element.one('#course-listing'),
            selectedcategory = null,
            selectedcourse = null;

        if (categorylisting) {
            selectedcategory = categorylisting.one('.listitem[data-selected="1"]');
        }
        if (courselisting) {
            selectedcourse = courselisting.one('.listitem[data-selected="1"]');
        }
        this.set('categorylisting', categorylisting);
        this.set('courselisting', courselisting);
        this.set('coursedetails', element.one('#course-detail'));
        if (selectedcategory) {
            this.set('activecategoryid', selectedcategory.getData('id'));
        }
        if (selectedcourse) {
            this.set('activecourseid', selectedcourse.getData('id'));
        }
        this.initialise_categories(categorylisting);
        this.initialise_courses();

        if (courselisting) {
            // No need for dragdrop if we don't have a course listing.
            this.set('dragdrop', new DragDrop({console:this}));
        }
    },

    /**
     * Initialises all the categories being shown.
     * @method initialise_categories
     * @private
     * @returns {boolean}
     */
    initialise_categories : function(listing) {
        var count = 0;
        if (!listing) {
            return false;
        }
        listing.all('.listitem[data-id]').each(function(node){
            this.set('categories', new Category({
                node : node,
                console : this
            }));
            count++;
        }, this);
        if (!this.categoriesinit) {
            this.get('categorylisting').delegate('click', this.handle_category_delegation, 'a[data-action]', this);
            this.categoriesinit = true;
            Y.log(count+' categories being managed', 'note', 'core_course');
        } else {
            Y.log(count+' new categories being managed', 'note', 'core_course');
        }
    },

    /**
     * Initialises all the categories being shown.
     * @method initialise_courses
     * @private
     * @returns {boolean}
     */
    initialise_courses : function() {
        var category = this.get_category_by_id(this.get('activecategoryid')),
            listing = this.get('courselisting'),
            count = 0;
        if (!listing) {
            return false;
        }
        listing.all('.listitem[data-id]').each(function(node){
            this.set('courses', new Course({
                node : node,
                console : this,
                category : category
            }));
            count++;
        }, this);
        listing.delegate('click', this.handle_course_delegation, 'a[data-action]', this);
        Y.log(count+' courses being managed', 'note', 'core_course');
    },

    /**
     * Handles the event fired by a delegated course listener.
     *
     * @method handle_course_delegation
     * @protected
     * @param {EventFacade} e
     */
    handle_course_delegation : function(e) {
        var target = e.currentTarget,
            action = target.getData('action'),
            courseid = target.ancestor('.listitem').getData('id'),
            course = this.get_course_by_id(courseid);
        course.handle(action, e);
    },

    /**
     * Handles the event fired by a delegated course listener.
     *
     * @method handle_category_delegation
     * @protected
     * @param {EventFacade} e
     */
    handle_category_delegation : function(e) {
        var target = e.currentTarget,
            action = target.getData('action'),
            categoryid = target.ancestor('.listitem').getData('id'),
            category = this.get_category_by_id(categoryid);
        category.handle(action, e);
    },

    /**
     * Returns the category with the given ID.
     * @method get_category_by_id
     * @param {Int} id
     * @returns {Category|Int} The category or the categoryid given if there is no matching category.
     */
    get_category_by_id : function(id) {
        var i, category, categories = this.get('categories'), length = categories.length;
        for (i = 0; i < length; i++) {
            category = categories[i];
            if (category.get('categoryid') === id) {
                return category;
            }
        }
        return id;
    },

    /**
     * Returns the course with the given id.
     * @method get_course_by_id
     * @param {Int} id
     * @returns {Category|Int} The course or the courseid given if there is no matching category.
     */
    get_course_by_id : function(id) {
        var i, course, courses = this.get('courses'), length = courses.length;
        for (i = 0; i < length; i++) {
            course = courses[i];
            if (course.get('courseid') === id) {
                return course;
            }
        }
        return false;
    },

    /**
     * Removes the course with the given ID.
     * @method remove_course_by_id
     * @param {Int} id
     */
    remove_course_by_id : function() {
        var courses = this.get('courses'),
            i;
        for (i = 0; i < length; i++) {
            course = courses[i];
            if (course.get('courseid') === id) {
                courses.splice(i, 1);
                break;
            }
        }
    },

    /**
     * Performs an AJAX action.
     *
     * @method perform_ajax_action
     * @param {String} action The action to perform.
     * @param {Object} args The arguments to pass through with teh request.
     * @param {Function} callback The function to call when all is done.
     * @param {Object} context The object to use as the context for the callback.
     */
    perform_ajax_action : function(action, args, callback, context) {
        var io = new Y.IO();
        args.action = action;
        args.ajax = '1';
        args.sesskey = M.cfg.sesskey;
        io.send(this.get('ajaxurl'), {
            method : 'POST',
            on : {
                complete : callback
            },
            context : context,
            data : build_querystring(args),
            'arguments' : args
        });
    }
};
Y.extend(Console, Y.Base, Console.prototype);

/**
 * Course namespace.
 * @static
 * @namespace M
 * @class course
 */
M.course = M.course || {};

/**
 * Initalises the course management console.
 * @static
 * @param {Object} config
 */
M.course.init_management = function(config) {
    M.course.console = new Console(config);
};