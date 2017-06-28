/* global DragDrop, Category, Course */

/**
 * Provides drop down menus for list of action links.
 *
 * @module moodle-course-management
 */

/**
 * Management JS console.
 *
 * Provides the organisation for course and category management JS.
 *
 * @namespace M.course.management
 * @class Console
 * @constructor
 * @extends Base
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
    element: {
        setter: function(node) {
            if (typeof node === 'string') {
                node = Y.one('#' + node);
            }
            return node;
        }
    },

    /**
     * The category listing container node.
     * @attribute categorylisting
     * @type Node
     * @default null
     */
    categorylisting: {
        value: null
    },

    /**
     * The course listing container node.
     * @attribute courselisting
     * @type Node
     * @default null
     */
    courselisting: {
        value: null
    },

    /**
     * The course details container node.
     * @attribute coursedetails
     * @type Node|null
     * @default null
     */
    coursedetails: {
        value: null
    },

    /**
     * The id of the currently active category.
     * @attribute activecategoryid
     * @type Number
     * @default null
     */
    activecategoryid: {
        value: null
    },

    /**
     * The id of the currently active course.
     * @attribute activecourseid
     * @type Number
     * @default Null
     */
    activecourseid: {
        value: null
    },

    /**
     * The categories that are currently available through the management interface.
     * @attribute categories
     * @type Array
     * @default []
     */
    categories: {
        setter: function(item, name) {
            if (Y.Lang.isArray(item)) {
                return item;
            }
            var items = this.get(name);
            items.push(item);
            return items;
        },
        value: []
    },

    /**
     * The courses that are currently available through the management interface.
     * @attribute courses
     * @type Course[]
     * @default Array
     */
    courses: {
        validator: function(val) {
            return Y.Lang.isArray(val);
        },
        value: []
    },

    /**
     * The currently displayed page of courses.
     * @attribute page
     * @type Number
     * @default null
     */
    page: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value: null
    },

    /**
     * The total pages of courses that can be shown for this category.
     * @attribute totalpages
     * @type Number
     * @default null
     */
    totalpages: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value: null
    },

    /**
     * The total number of courses belonging to this category.
     * @attribute totalcourses
     * @type Number
     * @default null
     */
    totalcourses: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('element').getData(name);
                this.set(name, value);
            }
            return value;
        },
        value: null
    },

    /**
     * The URL to use for AJAX actions/requests.
     * @attribute ajaxurl
     * @type String
     * @default /course/ajax/management.php
     */
    ajaxurl: {
        getter: function(value) {
            if (value === null) {
                value = M.cfg.wwwroot + '/course/ajax/management.php';
            }
            return value;
        },
        value: null
    },

    /**
     * The drag drop handler
     * @attribute dragdrop
     * @type DragDrop
     * @default null
     */
    dragdrop: {
        value: null
    }
};
Console.prototype = {

    /**
     * Gets set to true once the first categories have been initialised.
     * @property categoriesinit
     * @private
     * @type {boolean}
     */
    categoriesinit: false,

    /**
     * Initialises a new instance of the Console.
     * @method initializer
     */
    initializer: function() {
        Y.log('Initialising course category management console', 'info', 'moodle-course-management');
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
        this.initialiseCategories(categorylisting);
        this.initialiseCourses();

        if (courselisting) {
            // No need for dragdrop if we don't have a course listing.
            this.set('dragdrop', new DragDrop({console: this}));
        }
    },

    /**
     * Initialises all the categories being shown.
     * @method initialiseCategories
     * @private
     * @return {boolean}
     */
    initialiseCategories: function(listing) {
        var count = 0;
        if (!listing) {
            return false;
        }

        // Disable category bulk actions as nothing will be selected on initialise.
        var menumovecatto = listing.one('#menumovecategoriesto');
        if (menumovecatto) {
            menumovecatto.setAttribute('disabled', true);
        }
        var menuresortcategoriesby = listing.one('#menuresortcategoriesby');
        if (menuresortcategoriesby) {
            menuresortcategoriesby.setAttribute('disabled', true);
        }
        var menuresortcoursesby = listing.one('#menuresortcoursesby');
        if (menuresortcoursesby) {
            menuresortcoursesby.setAttribute('disabled', true);
        }

        listing.all('.listitem[data-id]').each(function(node) {
            this.set('categories', new Category({
                node: node,
                console: this
            }));
            count++;
        }, this);
        if (!this.categoriesinit) {
            this.get('categorylisting').delegate('click', this.handleCategoryDelegation, 'a[data-action]', this);
            this.get('categorylisting').delegate('click', this.handleCategoryDelegation, 'input[name="bcat[]"]', this);
            this.get('categorylisting').delegate('change', this.handleBulkSortByaction, '#menuselectsortby', this);
            this.categoriesinit = true;
            Y.log(count + ' categories being managed', 'info', 'moodle-course-management');
        } else {
            Y.log(count + ' new categories being managed', 'info', 'moodle-course-management');
        }
    },

    /**
     * Initialises all the categories being shown.
     * @method initialiseCourses
     * @private
     * @return {boolean}
     */
    initialiseCourses: function() {
        var category = this.getCategoryById(this.get('activecategoryid')),
            listing = this.get('courselisting'),
            count = 0;
        if (!listing) {
            return false;
        }

        // Disable course move to bulk action as nothing will be selected on initialise.
        var menumovecoursesto = listing.one('#menumovecoursesto');
        if (menumovecoursesto) {
            menumovecoursesto.setAttribute('disabled', true);
        }

        listing.all('.listitem[data-id]').each(function(node) {
            this.registerCourse(new Course({
                node: node,
                console: this,
                category: category
            }));
            count++;
        }, this);
        listing.delegate('click', this.handleCourseDelegation, 'a[data-action]', this);
        listing.delegate('click', this.handleCourseDelegation, 'input[name="bc[]"]', this);
        Y.log(count + ' courses being managed', 'info', 'moodle-course-management');
    },

    /**
     * Registers a course within the management display.
     * @method registerCourse
     * @param {Course} course
     */
    registerCourse: function(course) {
        var courses = this.get('courses');
        courses.push(course);
        this.set('courses', courses);
    },

    /**
     * Handles the event fired by a delegated course listener.
     *
     * @method handleCourseDelegation
     * @protected
     * @param {EventFacade} e
     */
    handleCourseDelegation: function(e) {
        var target = e.currentTarget,
            action = target.getData('action'),
            courseid = target.ancestor('.listitem').getData('id'),
            course = this.getCourseById(courseid);
        if (course) {
            course.handle(action, e);
        } else {
            Y.log('Course with ID ' + courseid + ' could not be found for delegation', 'error', 'moodle-course-management');
        }
    },

    /**
     * Handles the event fired by a delegated course listener.
     *
     * @method handleCategoryDelegation
     * @protected
     * @param {EventFacade} e
     */
    handleCategoryDelegation: function(e) {
        var target = e.currentTarget,
            action = target.getData('action'),
            categoryid = target.ancestor('.listitem').getData('id'),
            category = this.getCategoryById(categoryid);
        if (category) {
            category.handle(action, e);
        } else {
            Y.log('Could not find category to delegate to.', 'error', 'moodle-course-management');
        }
    },

    /**
     * Check if any course is selected.
     *
     * @method isCourseSelected
     * @param {Node} checkboxnode Checkbox node on which action happened.
     * @return bool
     */
    isCourseSelected: function(checkboxnode) {
        var selected = false;

        // If any course selected then show move to category select box.
        if (checkboxnode && checkboxnode.get('checked')) {
            selected = true;
        } else {
            var i,
                course,
                courses = this.get('courses'),
                length = courses.length;
            for (i = 0; i < length; i++) {
                if (courses.hasOwnProperty(i)) {
                    course = courses[i];
                    if (course.get('node').one('input[name="bc[]"]').get('checked')) {
                        selected = true;
                        break;
                    }
                }
            }
        }
        return selected;
    },

    /**
     * Check if any category is selected.
     *
     * @method isCategorySelected
     * @param {Node} checkboxnode Checkbox node on which action happened.
     * @return bool
     */
    isCategorySelected: function(checkboxnode) {
        var selected = false;

        // If any category selected then show move to category select box.
        if (checkboxnode && checkboxnode.get('checked')) {
            selected = true;
        } else {
            var i,
                category,
                categories = this.get('categories'),
                length = categories.length;
            for (i = 0; i < length; i++) {
                if (categories.hasOwnProperty(i)) {
                    category = categories[i];
                    if (category.get('node').one('input[name="bcat[]"]').get('checked')) {
                        selected = true;
                        break;
                    }
                }
            }
        }
        return selected;
    },

    /**
     * Handle bulk sort action.
     *
     * @method handleBulkSortByaction
     * @protected
     * @param {EventFacade} e
     */
    handleBulkSortByaction: function(e) {
        var sortcategoryby = this.get('categorylisting').one('#menuresortcategoriesby'),
            sortcourseby = this.get('categorylisting').one('#menuresortcoursesby'),
            sortbybutton = this.get('categorylisting').one('input[name="bulksort"]'),
            sortby = e;

        if (!sortby) {
            sortby = this.get('categorylisting').one('#menuselectsortby');
        } else {
            if (e && e.currentTarget) {
                sortby = e.currentTarget;
            }
        }

        // If no sortby select found then return as we can't do anything.
        if (!sortby) {
            return;
        }

        if ((this.get('categories').length <= 1) || (!this.isCategorySelected() &&
                (sortby.get("options").item(sortby.get('selectedIndex')).getAttribute('value') === 'selectedcategories'))) {
            if (sortcategoryby) {
                sortcategoryby.setAttribute('disabled', true);
            }
            if (sortcourseby) {
                sortcourseby.setAttribute('disabled', true);
            }
            if (sortbybutton) {
                sortbybutton.setAttribute('disabled', true);
            }
        } else {
            if (sortcategoryby) {
                sortcategoryby.removeAttribute('disabled');
            }
            if (sortcourseby) {
                sortcourseby.removeAttribute('disabled');
            }
            if (sortbybutton) {
                sortbybutton.removeAttribute('disabled');
            }
        }
    },

    /**
     * Returns the category with the given ID.
     * @method getCategoryById
     * @param {Number} id
     * @return {Category|Boolean} The category or false if it can't be found.
     */
    getCategoryById: function(id) {
        var i,
            category,
            categories = this.get('categories'),
            length = categories.length;
        for (i = 0; i < length; i++) {
            if (categories.hasOwnProperty(i)) {
                category = categories[i];
                if (category.get('categoryid') === id) {
                    return category;
                }
            }
        }
        return false;
    },

    /**
     * Returns the course with the given id.
     * @method getCourseById
     * @param {Number} id
     * @return {Course|Boolean} The course or false if not found/
     */
    getCourseById: function(id) {
        var i,
            course,
            courses = this.get('courses'),
            length = courses.length;
        for (i = 0; i < length; i++) {
            if (courses.hasOwnProperty(i)) {
                course = courses[i];
                if (course.get('courseid') === id) {
                    return course;
                }
            }
        }
        return false;
    },

    /**
     * Removes the course with the given ID.
     * @method removeCourseById
     * @param {Number} id
     */
    removeCourseById: function(id) {
        var courses = this.get('courses'),
            length = courses.length,
            course,
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
     * @method performAjaxAction
     * @param {String} action The action to perform.
     * @param {Object} args The arguments to pass through with teh request.
     * @param {Function} callback The function to call when all is done.
     * @param {Object} context The object to use as the context for the callback.
     */
    performAjaxAction: function(action, args, callback, context) {
        var io = new Y.IO();
        args.action = action;
        args.ajax = '1';
        args.sesskey = M.cfg.sesskey;
        if (callback === null) {
            callback = function() {
                Y.log("'Action '" + action + "' completed", 'debug', 'moodle-course-management');
            };
        }
        io.send(this.get('ajaxurl'), {
            method: 'POST',
            on: {
                complete: callback
            },
            context: context,
            data: args,
            'arguments': args
        });
    }
};
Y.extend(Console, Y.Base, Console.prototype);

M.course = M.course || {};
M.course.management = M.course.management || {};
M.course.management.console = null;

/**
 * Initalises the course management console.
 *
 * @method M.course.management.init
 * @static
 * @param {Object} config
 */
M.course.management.init = function(config) {
    M.course.management.console = new Console(config);
};
