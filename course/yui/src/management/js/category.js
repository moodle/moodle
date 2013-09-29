/**
 * A managed category.
 *
 * @namespace M.core_course.management
 * @class Category
 * @constructor
 * @extends Item
 */
function Category() {
    Category.superclass.constructor.apply(this, arguments);
}
Category.NAME = 'moodle-course-management-category';
Category.CSS_PREFIX = 'management-category';
Category.ATTRS = {
    /**
     * The category ID relating to this category.
     * @attribute categoryid
     * @type Int
     */
    categoryid : {},

    /**
     * True if this category is the currently selected category.
     * @attribute selected
     * @type Boolean
     * @default null
     */
    selected : {
        getter : function(value, name) {
            if (value === null) {
                value = this.get('node').getData(name);
                if (value === null) {
                    value = false;
                }
                this.set(name, value);
            }
            return value;
        },
        value : null
    },

    /**
     * An array fo courses belonging to this category
     * @attribute courses
     * @type Course[]
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
    }
};
Category.prototype = {
    /**
     * Initialises an instance of a Category.
     * @method initializer
     */
    initializer : function() {
        var node = this.get('node');
        this.set('categoryid', node.getData('id'));
        this.set('itemname', 'category');
    },

    /**
     * Returns the name of the category.
     * @method getName
     * @returns {String}
     */
    getName : function() {
        return this.get('node').one('a.categoryname').get('innerHTML');
    },

    /**
     * Registers a course as belonging to this category.
     * @method register_course
     * @param {Course} course
     */
    register_course : function(course) {
        this.set('courses', course);
    },

    /**
     * Handles a category related event.
     *
     * @method handle
     * @param {String} action
     * @param {EventFacade} e
     * @returns {Boolean}
     */
    handle : function(action, e) {
        var catarg = {categoryid : this.get('categoryid')};
        switch (action) {
            case 'moveup':
                e.halt();
                this.get('console').perform_ajax_action('movecategoryup', catarg, this.moveup, this);
                break;
            case 'movedown':
                e.halt();
                this.get('console').perform_ajax_action('movecategorydown', catarg, this.movedown, this);
                break;
            case 'show':
                e.halt();
                this.get('console').perform_ajax_action('showcategory', catarg, this.show, this);
                break;
            case 'hide':
                e.halt();
                this.get('console').perform_ajax_action('hidecategory', catarg, this.hide, this);
                break;
            case 'expand':
                e.halt();
                if (this.get('node').getData('expanded') === '0') {
                    this.get('node').setData('expanded', true);
                    this.get('console').perform_ajax_action('getsubcategorieshtml', catarg, this.loadSubcategories, this);
                }
                this.expand();
                break;
            case 'collapse':
                e.halt();
                this.collapse();
                break;
            default:
                Y.log('Invalid AJAX action requested of managed category.', 'warn', 'core_course');
                return false;
        }
    },

    /**
     * Expands the category making its sub categories visible.
     * @method expand
     */
    expand : function() {
        var node = this.get('node'),
            action = node.one('a[data-action=expand]');
        node.removeClass('collapsed');
        action.setAttribute('data-action', 'collapse');
        action.one('img').setAttrs({
            src : M.util.image_url('t/switch_minus', 'moodle'),
            title : M.util.get_string('collapse', 'moodle'),
            alt : M.util.get_string('collapse', 'moodle')
        });
    },

    /**
     * Collapses the category making its sub categories hidden.
     * @method collapse
     */
    collapse : function() {
        var node = this.get('node'),
            action = node.one('a[data-action=collapse]');
        node.addClass('collapsed');
        action.setAttribute('data-action', 'expand');
        action.one('img').setAttrs({
            src : M.util.image_url('t/switch_plus', 'moodle'),
            title : M.util.get_string('expand', 'moodle'),
            alt : M.util.get_string('expand', 'moodle')
        });
    },

    /**
     * Loads sub categories provided by an AJAX request..
     *
     * @method loadSubcategories
     * @protected
     * @param {Int} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @returns {Boolean}
     */
    loadSubcategories : function(transactionid, response, args) {
        var outcome = this.check_ajax_response(transactionid, response, args),
            node = this.get('node'),
            console = this.get('console');
        if (outcome === false) {
            Y.log('AJAX failed to load sub categories for '+this.get('itemname'), 'warn', 'core_course');
            return false;
        }
        Y.log('AJAX loaded subcategories for '+this.get('itemname'), 'info', 'core_course');
        node.append(outcome.html);
        console.initialise_categories(node);
    },

    /**
     * Moves the course to this category.
     *
     * @method moveCourseTo
     * @param {Course} course
     */
    moveCourseTo : function(course) {
        var self = this;
        Y.use('moodle-core-notification-confirm', function() {
            var confirm = new M.core.confirm({
                title : M.util.get_string('confirm', 'moodle'),
                question : M.util.get_string('confirmcoursemove', 'moodle', {
                    course : course.getName(),
                    category : self.getName()
                }),
                yesLabel : M.util.get_string('yes', 'moodle'),
                noLabel : M.util.get_string('no', 'moodle')
            });
            confirm.on('complete-yes', function() {
                confirm.hide();
                confirm.destroy();
                this.get('console').perform_ajax_action('movecourseintocategory', {
                    categoryid : this.get('categoryid'),
                    courseid : course.get('courseid')
                }, this.completeMoveCourse, this);
            }, self);
            confirm.show();
        });
    },

    /**
     * Completes moving a course to this category.
     * @method completeMoveCourse
     * @protected
     * @param {Int} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @returns {Boolean}
     */
    completeMoveCourse : function(transactionid, response, args) {
        var outcome = this.check_ajax_response(transactionid, response, args),
            course;
        if (outcome === false) {
            Y.log('AJAX failed to move courses into this category: '+this.get('itemname'), 'warn', 'core_course');
            return false;
        }
        course = this.get('console').get_course_by_id(args.courseid);
        Y.log('Moved the course ('+course.getName()+') into this category ('+this.getName()+')', 'info', 'core_course');
        this.highlight();
        if (course) {
            course.remove();
        }
        return true;
    },

    /**
     * Makes an item visible.
     *
     * @method show
     * @param {Int} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @returns {Boolean}
     */
    show : function(transactionid, response, args) {
        var outcome = this.check_ajax_response(transactionid, response, args);
        if (outcome === false) {
            Y.log('AJAX request to show '+this.get('itemname')+' by outcome.', 'warn', 'core_course');
            return false;
        }

        this.markVisible();
        if (outcome.categoryvisibility) {
            this.updateChildVisibility(outcome.categoryvisibility);
        }
        if (outcome.coursevisibility) {
            this.updateCourseVisiblity(outcome.coursevisibility);
        }
        this.updated();
        Y.log('Success: category made visible by AJAX.', 'info', 'core_course');
    },

    /**
     * Hides an item.
     *
     * @method hide
     * @param {Int} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @returns {Boolean}
     */
    hide : function(transactionid, response, args) {
        var outcome = this.check_ajax_response(transactionid, response, args);
        if (outcome === false) {
            Y.log('AJAX request to hide '+this.get('itemname')+' by outcome.', 'warn', 'core_course');
            return false;
        }
        this.markHidden();
        if (outcome.categoryvisibility) {
            this.updateChildVisibility(outcome.categoryvisibility);
        }
        if (outcome.coursevisibility) {
            this.updateCourseVisiblity(outcome.coursevisibility);
        }
        this.updated();
        Y.log('Success: '+this.get('itemname')+' made hidden by AJAX.', 'info', 'core_course');
    },

    /**
     * Updates the visibility of child courses if required.
     * @method updateCourseVisiblity
     * @param courses
     */
    updateCourseVisiblity : function(courses) {
        var console = this.get('console'),
            key,
            course;
        try {
            for (key in courses) {
                course = console.get_course_by_id(courses[key].id);
                if (course.get) {
                    if (courses[key].show === "1") {
                        course.markVisible();
                    } else {
                        course.markHidden();
                    }
                }
            }
        } catch (err) {
            Y.log('Error trying to update course visibility: ' + err.message, 'warn', 'core_course');
        }
    },

    /**
     * Updates the visibility of subcategories if required.
     * @method updateChildVisibility
     * @param categories
     */
    updateChildVisibility : function(categories) {
        var console = this.get('console'),
            key,
            category;
        try {
            for (key in categories) {
                category = console.get_category_by_id(categories[key].id);
                if (category.get) {
                    if (categories[key].show === "1") {
                        category.markVisible();
                    } else {
                        category.markHidden();
                    }
                }
            }
        } catch (err) {
            Y.log('Error trying to update category visibility: ' + err.message, 'warn', 'core_course');
        }
    }
};
Y.extend(Category, Item, Category.prototype);