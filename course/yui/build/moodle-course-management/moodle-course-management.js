YUI.add('moodle-course-management', function (Y, NAME) {

var Category;
var Console;
var Course;
var DragDrop;
var Item;
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
Console = function() {
    Console.superclass.constructor.apply(this, arguments);
};
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
        } else {
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
/**
 * Drag and Drop handler
 *
 * @namespace M.course.management
 * @class DragDrop
 * @constructor
 * @extends Base
 */
DragDrop = function(config) {
    Console.superclass.constructor.apply(this, [config]);
};
DragDrop.NAME = 'moodle-course-management-dd';
DragDrop.CSS_PREFIX = 'management-dd';
DragDrop.ATTRS = {
    /**
     * The management console this drag and drop has been set up for.
     * @attribute console
     * @type Console
     * @writeOnce
     */
    console: {
        writeOnce: 'initOnly'
    }
};
DragDrop.prototype = {
    /**
     * True if the user is dragging a course upwards.
     * @property goingup
     * @protected
     * @default false
     */
    goingup: false,

    /**
     * The last Y position of the course being dragged
     * @property lasty
     * @protected
     * @default null
     */
    lasty: null,

    /**
     * The sibling above the course being dragged currently (tracking its original position).
     *
     * @property previoussibling
     * @protected
     * @default false
     */
    previoussibling: null,

    /**
     * Initialises the DragDrop instance.
     * @method initializer
     */
    initializer: function() {
        var managementconsole = this.get('console'),
            container = managementconsole.get('element'),
            categorylisting = container.one('#category-listing'),
            courselisting = container.one('#course-listing > .course-listing'),
            categoryul = (categorylisting) ? categorylisting.one('ul.ml') : null,
            courseul = (courselisting) ? courselisting.one('ul.ml') : null,
            canmoveoutof = (courselisting) ? courselisting.getData('canmoveoutof') : false,
            contstraint = (canmoveoutof) ? container : courseul;

        if (!courseul) {
            // No course listings found.
            return false;
        }

        while (contstraint.get('scrollHeight') === 0 && !contstraint.compareTo(window.document.body)) {
            contstraint = contstraint.get('parentNode');
        }

        courseul.all('> li').each(function(li) {
            this.initCourseListing(li, contstraint);
        }, this);
        courseul.setData('dd', new Y.DD.Drop({
            node: courseul
        }));
        if (canmoveoutof && categoryul) {
            // Category UL may not be there if viewmode is just courses.
            categoryul.all('li > div').each(function(div) {
                this.initCategoryListitem(div);
            }, this);
        }
        Y.DD.DDM.on('drag:start', this.dragStart, this);
        Y.DD.DDM.on('drag:end', this.dragEnd, this);
        Y.DD.DDM.on('drag:drag', this.dragDrag, this);
        Y.DD.DDM.on('drop:over', this.dropOver, this);
        Y.DD.DDM.on('drop:enter', this.dropEnter, this);
        Y.DD.DDM.on('drop:exit', this.dropExit, this);
        Y.DD.DDM.on('drop:hit', this.dropHit, this);

    },

    /**
     * Initialises a course listing.
     * @method initCourseListing
     * @param Node
     */
    initCourseListing: function(node, contstraint) {
        node.setData('dd', new Y.DD.Drag({
            node: node,
            target: {
                padding: '0 0 0 20'
            }
        }).addHandle(
            '.drag-handle'
        ).plug(Y.Plugin.DDProxy, {
            moveOnEnd: false,
            borderStyle: false
        }).plug(Y.Plugin.DDConstrained, {
            constrain2node: contstraint
        }));
    },

    /**
     * Initialises a category listing.
     * @method initCategoryListitem
     * @param Node
     */
    initCategoryListitem: function(node) {
        node.setData('dd', new Y.DD.Drop({
            node: node
        }));
    },

    /**
     * Dragging has started.
     * @method dragStart
     * @private
     * @param {EventFacade} e
     */
    dragStart: function(e) {
        var drag = e.target,
            node = drag.get('node'),
            dragnode = drag.get('dragNode');
        node.addClass('course-being-dragged');
        dragnode.addClass('course-being-dragged-proxy').set('innerHTML', node.one('a.coursename').get('innerHTML'));
        this.previoussibling = node.get('previousSibling');
    },

    /**
     * Dragging has ended.
     * @method dragEnd
     * @private
     * @param {EventFacade} e
     */
    dragEnd: function(e) {
        var drag = e.target,
            node = drag.get('node');
        node.removeClass('course-being-dragged');
        this.get('console').get('element').all('#category-listing li.highlight').removeClass('highlight');
    },

    /**
     * Dragging in progress.
     * @method dragDrag
     * @private
     * @param {EventFacade} e
     */
    dragDrag: function(e) {
        var y = e.target.lastXY[1];
        if (y < this.lasty) {
            this.goingup = true;
        } else {
            this.goingup = false;
        }
        this.lasty = y;
    },

    /**
     * The course has been dragged over a drop target.
     * @method dropOver
     * @private
     * @param {EventFacade} e
     */
    dropOver: function(e) {
        // Get a reference to our drag and drop nodes
        var drag = e.drag.get('node'),
            drop = e.drop.get('node'),
            tag = drop.get('tagName').toLowerCase();
        if (tag === 'li' && drop.hasClass('listitem-course')) {
            if (!this.goingup) {
                drop = drop.get('nextSibling');
                if (!drop) {
                    drop = e.drop.get('node');
                    drop.get('parentNode').append(drag);
                    return false;
                }
            }
            drop.get('parentNode').insertBefore(drag, drop);
            e.drop.sizeShim();
        }
    },

    /**
     * The course has been dragged over a drop target.
     * @method dropEnter
     * @private
     * @param {EventFacade} e
     */
    dropEnter: function(e) {
        var drop = e.drop.get('node'),
            tag = drop.get('tagName').toLowerCase();
        if (tag === 'div') {
            drop.ancestor('li.listitem-category').addClass('highlight');
        }
    },

    /**
     * The course has been dragged off a drop target.
     * @method dropExit
     * @private
     * @param {EventFacade} e
     */
    dropExit: function(e) {
        var drop = e.drop.get('node'),
            tag = drop.get('tagName').toLowerCase();
        if (tag === 'div') {
            drop.ancestor('li.listitem-category').removeClass('highlight');
        }
    },

    /**
     * The course has been dropped on a target.
     * @method dropHit
     * @private
     * @param {EventFacade} e
     */
    dropHit: function(e) {
        var drag = e.drag.get('node'),
            drop = e.drop.get('node'),
            iscategory = (drop.ancestor('.listitem-category') !== null),
            iscourse = !iscategory && (drop.test('.listitem-course')),
            managementconsole = this.get('console'),
            categoryid,
            category,
            courseid,
            course,
            aftercourseid,
            previoussibling,
            previousid;

        if (!drag.test('.listitem-course')) {
            return false;
        }
        courseid = drag.getData('id');
        if (iscategory) {
            categoryid = drop.ancestor('.listitem-category').getData('id');
            category = managementconsole.getCategoryById(categoryid);
            if (category) {
                course = managementconsole.getCourseById(courseid);
                if (course) {
                    category.moveCourseTo(course);
                }
            }
        } else if (iscourse || drop.ancestor('#course-listing')) {
            course = managementconsole.getCourseById(courseid);
            previoussibling = drag.get('previousSibling');
            aftercourseid = (previoussibling) ? previoussibling.getData('id') || 0 : 0;
            previousid = (this.previoussibling) ? this.previoussibling.getData('id') : 0;
            if (aftercourseid !== previousid) {
                course.moveAfter(aftercourseid, previousid);
            }
        } else {
        }
    }
};
Y.extend(DragDrop, Y.Base, DragDrop.prototype);
/**
 * A managed course.
 *
 * @namespace M.course.management
 * @class Item
 * @constructor
 * @extends Base
 */
Item = function() {
    Item.superclass.constructor.apply(this, arguments);
};
Item.NAME = 'moodle-course-management-item';
Item.CSS_PREFIX = 'management-item';
Item.ATTRS = {
    /**
     * The node for this item.
     * @attribute node
     * @type Node
     */
    node: {},

    /**
     * The management console.
     * @attribute console
     * @type Console
     */
    console: {},

    /**
     * Describes the type of this item. Should be set by the extending class.
     * @attribute itemname
     * @type {String}
     * @default item
     */
    itemname: {
        value: 'item'
    }
};
Item.prototype = {
    /**
     * The highlight timeout for this item if there is one.
     * @property highlighttimeout
     * @protected
     * @type Timeout
     * @default null
     */
    highlighttimeout: null,

    /**
     * Checks and parses an AJAX response for an item.
     *
     * @method checkAjaxResponse
     * @protected
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Object|Boolean}
     */
    checkAjaxResponse: function(transactionid, response, args) {
        if (response.status !== 200) {
            return false;
        }
        if (transactionid === null || args === null) {
            return false;
        }
        var outcome = Y.JSON.parse(response.responseText);
        if (outcome.error !== false) {
            new M.core.exception(outcome);
        }
        if (outcome.outcome === false) {
            return false;
        }
        return outcome;
    },

    /**
     * Moves an item up by one.
     *
     * @method moveup
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    moveup: function(transactionid, response, args) {
        var node,
            nodeup,
            nodedown,
            previous,
            previousup,
            previousdown,
            tmpnode,
            outcome = this.checkAjaxResponse(transactionid, response, args);
        if (outcome === false) {
            return false;
        }
        node = this.get('node');
        previous = node.previous('.listitem');
        if (previous) {
            previous.insert(node, 'before');
            previousup = previous.one(' > div a.action-moveup');
            nodedown = node.one(' > div a.action-movedown');
            if (!previousup || !nodedown) {
                // We can have two situations here:
                //   1. previousup is not set and nodedown is not set. This happens when there are only two courses.
                //   2. nodedown is not set. This happens when they are moving the bottom course up.
                // node up and previous down should always be there. They would be required to trigger the action.
                nodeup = node.one(' > div a.action-moveup');
                previousdown = previous.one(' > div a.action-movedown');
                if (!previousup && !nodedown) {
                    // Ok, must be two courses. We need to switch the up and down icons.
                    tmpnode = Y.Node.create('<a style="visibility:hidden;">&nbsp;</a>');
                    previousdown.replace(tmpnode);
                    nodeup.replace(previousdown);
                    tmpnode.replace(nodeup);
                    tmpnode.destroy();
                } else if (!nodedown) {
                    // previous down needs to be given to node.
                    nodeup.insert(previousdown, 'after');
                }
            }
            nodeup = node.one(' > div a.action-moveup');
            if (nodeup) {
                // Try to re-focus on up.
                nodeup.focus();
            } else {
                // If we can't focus up we're at the bottom, try to focus on up.
                nodedown = node.one(' > div a.action-movedown');
                if (nodedown) {
                    nodedown.focus();
                }
            }
            this.updated(true);
        } else {
            // Aha it succeeded but this is the top item in the list. Pagination is in play!
            // Refresh to update the state of things.
            window.location.reload();
        }
    },

    /**
     * Moves an item down by one.
     *
     * @method movedown
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    movedown: function(transactionid, response, args) {
        var node,
            next,
            nodeup,
            nodedown,
            nextup,
            nextdown,
            tmpnode,
            outcome = this.checkAjaxResponse(transactionid, response, args);
        if (outcome === false) {
            return false;
        }
        node = this.get('node');
        next = node.next('.listitem');
        if (next) {
            node.insert(next, 'before');
            nextdown = next.one(' > div a.action-movedown');
            nodeup = node.one(' > div a.action-moveup');
            if (!nextdown || !nodeup) {
                // next up and node down should always be there. They would be required to trigger the action.
                nextup = next.one(' > div a.action-moveup');
                nodedown = node.one(' > div a.action-movedown');
                if (!nextdown && !nodeup) {
                    // We can have two situations here:
                    //   1. nextdown is not set and nodeup is not set. This happens when there are only two courses.
                    //   2. nodeup is not set. This happens when we are moving the first course down.
                    // Ok, must be two courses. We need to switch the up and down icons.
                    tmpnode = Y.Node.create('<a style="visibility:hidden;">&nbsp;</a>');
                    nextup.replace(tmpnode);
                    nodedown.replace(nextup);
                    tmpnode.replace(nodedown);
                    tmpnode.destroy();
                } else if (!nodeup) {
                    // next up needs to be given to node.
                    nodedown.insert(nextup, 'before');
                }
            }
            nodedown = node.one(' > div a.action-movedown');
            if (nodedown) {
                // Try to ensure the up is focused again.
                nodedown.focus();
            } else {
                // If we can't focus up we're at the top, try to focus on down.
                nodeup = node.one(' > div a.action-moveup');
                if (nodeup) {
                    nodeup.focus();
                }
            }
            this.updated(true);
        } else {
            // Aha it succeeded but this is the bottom item in the list. Pagination is in play!
            // Refresh to update the state of things.
            window.location.reload();
        }
    },

    /**
     * Makes an item visible.
     *
     * @method show
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    show: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            hidebtn;
        if (outcome === false) {
            return false;
        }

        this.markVisible();
        hidebtn = this.get('node').one('a[data-action=hide]');
        if (hidebtn) {
            hidebtn.focus();
        }
        this.updated();
    },

    /**
     * Marks the item as visible
     * @method markVisible
     */
    markVisible: function() {
        this.get('node').setAttribute('data-visible', '1');
        return true;
    },

    /**
     * Hides an item.
     *
     * @method hide
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    hide: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            showbtn;
        if (outcome === false) {
            return false;
        }
        this.markHidden();
        showbtn = this.get('node').one('a[data-action=show]');
        if (showbtn) {
            showbtn.focus();
        }
        this.updated();
    },

    /**
     * Marks the item as hidden.
     * @method makeHidden
     */
    markHidden: function() {
        this.get('node').setAttribute('data-visible', '0');
        return true;
    },

    /**
     * Called when ever a node is updated.
     *
     * @method updated
     * @param {Boolean} moved True if this item was moved.
     */
    updated: function(moved) {
        if (moved) {
            this.highlight();
        }
    },

    /**
     * Highlights this option for a breif time.
     *
     * @method highlight
     */
    highlight: function() {
        var node = this.get('node');
        node.siblings('.highlight').removeClass('highlight');
        node.addClass('highlight');
        if (this.highlighttimeout) {
            window.clearTimeout(this.highlighttimeout);
        }
        this.highlighttimeout = window.setTimeout(function() {
            node.removeClass('highlight');
        }, 2500);
    }
};
Y.extend(Item, Y.Base, Item.prototype);
/**
 * A managed category.
 *
 * @namespace M.course.management
 * @class Category
 * @constructor
 * @extends Item
 */
Category = function() {
    Category.superclass.constructor.apply(this, arguments);
};
Category.NAME = 'moodle-course-management-category';
Category.CSS_PREFIX = 'management-category';
Category.ATTRS = {
    /**
     * The category ID relating to this category.
     * @attribute categoryid
     * @type Number
     * @writeOnce
     * @default null
     */
    categoryid: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('node').getData('id');
                this.set(name, value);
            }
            return value;
        },
        value: null,
        writeOnce: true
    },

    /**
     * True if this category is the currently selected category.
     * @attribute selected
     * @type Boolean
     * @default null
     */
    selected: {
        getter: function(value, name) {
            if (value === null) {
                value = this.get('node').getData(name);
                if (value === null) {
                    value = false;
                }
                this.set(name, value);
            }
            return value;
        },
        value: null
    },

    /**
     * An array of courses belonging to this category.
     * @attribute courses
     * @type Course[]
     * @default Array
     */
    courses: {
        validator: function(val) {
            return Y.Lang.isArray(val);
        },
        value: []
    }
};
Category.prototype = {
    /**
     * Initialises an instance of a Category.
     * @method initializer
     */
    initializer: function() {
        this.set('itemname', 'category');
    },

    /**
     * Returns the name of the category.
     * @method getName
     * @return {String}
     */
    getName: function() {
        return this.get('node').one('a.categoryname').get('innerHTML');
    },

    /**
     * Registers a course as belonging to this category.
     * @method registerCourse
     * @param {Course} course
     */
    registerCourse: function(course) {
        var courses = this.get('courses');
        courses.push(course);
        this.set('courses', courses);
    },

    /**
     * Handles a category related event.
     *
     * @method handle
     * @param {String} action
     * @param {EventFacade} e
     * @return {Boolean}
     */
    handle: function(action, e) {
        var catarg = {categoryid: this.get('categoryid')},
            selected = this.get('console').get('activecategoryid');
        if (selected && selected !== catarg.categoryid) {
            catarg.selectedcategory = selected;
        }
        switch (action) {
            case 'moveup':
                e.preventDefault();
                this.get('console').performAjaxAction('movecategoryup', catarg, this.moveup, this);
                break;
            case 'movedown':
                e.preventDefault();
                this.get('console').performAjaxAction('movecategorydown', catarg, this.movedown, this);
                break;
            case 'show':
                e.preventDefault();
                this.get('console').performAjaxAction('showcategory', catarg, this.show, this);
                break;
            case 'hide':
                e.preventDefault();
                this.get('console').performAjaxAction('hidecategory', catarg, this.hide, this);
                break;
            case 'expand':
                e.preventDefault();
                if (this.get('node').getData('expanded') === '0') {
                    this.get('node').setAttribute('data-expanded', '1').setData('expanded', 'true');
                    this.get('console').performAjaxAction('getsubcategorieshtml', catarg, this.loadSubcategories, this);
                }
                this.expand();
                break;
            case 'collapse':
                e.preventDefault();
                this.collapse();
                break;
            case 'select':
                var c = this.get('console'),
                    movecategoryto = c.get('categorylisting').one('#menumovecategoriesto');
                // If any category is selected and there are more then one categories.
                if (movecategoryto) {
                    if (c.isCategorySelected(e.currentTarget) &&
                            c.get('categories').length > 1) {
                        movecategoryto.removeAttribute('disabled');
                    } else {
                        movecategoryto.setAttribute('disabled', true);
                    }
                    c.handleBulkSortByaction();
                }
                break;
            default:
                return false;
        }
    },

    /**
     * Expands the category making its sub categories visible.
     * @method expand
     */
    expand: function() {
        var node = this.get('node'),
            action = node.one('a[data-action=expand]'),
            ul = node.one('ul[role=group]');
        node.removeClass('collapsed').setAttribute('aria-expanded', 'true');
        action.setAttribute('data-action', 'collapse').setAttrs({
            title: M.util.get_string('collapsecategory', 'moodle', this.getName())
        });

        require(['core/str', 'core/templates', 'core/notification'], function(Str, Templates, Notification) {
            Str.get_string('collapse', 'core')
                .then(function(string) {
                    return Templates.renderPix('t/switch_minus', 'core', string);
                })
                .then(function(html) {
                    html = Y.Node.create(html).addClass('tree-icon').getDOMNode().outerHTML;
                    return action.set('innerHTML', html);
                }).fail(Notification.exception);
        });

        if (ul) {
            ul.setAttribute('aria-hidden', 'false');
        }
        this.get('console').performAjaxAction('expandcategory', {categoryid: this.get('categoryid')}, null, this);
    },

    /**
     * Collapses the category making its sub categories hidden.
     * @method collapse
     */
    collapse: function() {
        var node = this.get('node'),
            action = node.one('a[data-action=collapse]'),
            ul = node.one('ul[role=group]');
        node.addClass('collapsed').setAttribute('aria-expanded', 'false');
        action.setAttribute('data-action', 'expand').setAttrs({
            title: M.util.get_string('expandcategory', 'moodle', this.getName())
        });

        require(['core/str', 'core/templates', 'core/notification'], function(Str, Templates, Notification) {
            Str.get_string('expand', 'core')
                .then(function(string) {
                    return Templates.renderPix('t/switch_plus', 'core', string);
                })
                .then(function(html) {
                    html = Y.Node.create(html).addClass('tree-icon').getDOMNode().outerHTML;
                    return action.set('innerHTML', html);
                }).fail(Notification.exception);
        });

        if (ul) {
            ul.setAttribute('aria-hidden', 'true');
        }
        this.get('console').performAjaxAction('collapsecategory', {categoryid: this.get('categoryid')}, null, this);
    },

    /**
     * Loads sub categories provided by an AJAX request..
     *
     * @method loadSubcategories
     * @protected
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean} Returns true on success - false otherwise.
     */
    loadSubcategories: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            node = this.get('node'),
            managementconsole = this.get('console'),
            ul,
            actionnode;
        if (outcome === false) {
            return false;
        }
        node.append(outcome.html);
        managementconsole.initialiseCategories(node);
        if (M.core && M.core.actionmenu && M.core.actionmenu.newDOMNode) {
            M.core.actionmenu.newDOMNode(node);
        }
        ul = node.one('ul[role=group]');
        actionnode = node.one('a[data-action=collapse]');
        if (ul && actionnode) {
            actionnode.setAttribute('aria-controls', ul.generateID());
        }
        return true;
    },

    /**
     * Moves the course to this category.
     *
     * @method moveCourseTo
     * @param {Course} course
     */
    moveCourseTo: function(course) {
        var self = this;
        Y.use('moodle-core-notification-confirm', function() {
            var confirm = new M.core.confirm({
                title: M.util.get_string('confirm', 'moodle'),
                question: M.util.get_string('confirmcoursemove', 'moodle', {
                    course: course.getName(),
                    category: self.getName()
                }),
                yesLabel: M.util.get_string('move', 'moodle'),
                noLabel: M.util.get_string('cancel', 'moodle')
            });
            confirm.on('complete-yes', function() {
                confirm.hide();
                confirm.destroy();
                this.get('console').performAjaxAction('movecourseintocategory', {
                    categoryid: this.get('categoryid'),
                    courseid: course.get('courseid')
                }, this.completeMoveCourse, this);
            }, self);
            confirm.show();
        });
    },

    /**
     * Completes moving a course to this category.
     * @method completeMoveCourse
     * @protected
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    completeMoveCourse: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            managementconsole = this.get('console'),
            category,
            course,
            totals;
        if (outcome === false) {
            return false;
        }
        course = managementconsole.getCourseById(args.courseid);
        if (!course) {
            return false;
        }
        this.highlight();
        if (course) {
            if (outcome.paginationtotals) {
                totals = managementconsole.get('courselisting').one('.listing-pagination-totals');
                if (totals) {
                    totals.set('innerHTML', outcome.paginationtotals);
                }
            }
            if (outcome.totalcatcourses !== 'undefined') {
                totals = this.get('node').one('.course-count span');
                if (totals) {
                    totals.set('innerHTML', totals.get('innerHTML').replace(/^\d+/, outcome.totalcatcourses));
                }
            }
            if (typeof outcome.fromcatcoursecount !== 'undefined') {
                category = managementconsole.get('activecategoryid');
                category = managementconsole.getCategoryById(category);
                if (category) {
                    totals = category.get('node').one('.course-count span');
                    if (totals) {
                        totals.set('innerHTML', totals.get('innerHTML').replace(/^\d+/, outcome.fromcatcoursecount));
                    }
                }
            }
            course.remove();
        }
        return true;
    },

    /**
     * Makes an item visible.
     *
     * @method show
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    show: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            hidebtn;
        if (outcome === false) {
            return false;
        }

        this.markVisible();
        hidebtn = this.get('node').one('a[data-action=hide]');
        if (hidebtn) {
            hidebtn.focus();
        }
        if (outcome.categoryvisibility) {
            this.updateChildVisibility(outcome.categoryvisibility);
        }
        if (outcome.coursevisibility) {
            this.updateCourseVisiblity(outcome.coursevisibility);
        }
        this.updated();
    },

    /**
     * Hides an item.
     *
     * @method hide
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    hide: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            showbtn;
        if (outcome === false) {
            return false;
        }
        this.markHidden();
        showbtn = this.get('node').one('a[data-action=show]');
        if (showbtn) {
            showbtn.focus();
        }
        if (outcome.categoryvisibility) {
            this.updateChildVisibility(outcome.categoryvisibility);
        }
        if (outcome.coursevisibility) {
            this.updateCourseVisiblity(outcome.coursevisibility);
        }
        this.updated();
    },

    /**
     * Updates the visibility of child courses if required.
     * @method updateCourseVisiblity
     * @chainable
     * @param courses
     */
    updateCourseVisiblity: function(courses) {
        var managementconsole = this.get('console'),
            key,
            course;
        try {
            for (key in courses) {
                if (typeof courses[key] === 'object') {
                    course = managementconsole.getCourseById(courses[key].id);
                    if (course) {
                        if (courses[key].visible === "1") {
                            course.markVisible();
                        } else {
                            course.markHidden();
                        }
                    }
                }
            }
        } catch (err) {
        }
        return this;
    },

    /**
     * Updates the visibility of subcategories if required.
     * @method updateChildVisibility
     * @chainable
     * @param categories
     */
    updateChildVisibility: function(categories) {
        var managementconsole = this.get('console'),
            key,
            category;
        try {
            for (key in categories) {
                if (typeof categories[key] === 'object') {
                    category = managementconsole.getCategoryById(categories[key].id);
                    if (category) {
                        if (categories[key].visible === "1") {
                            category.markVisible();
                        } else {
                            category.markHidden();
                        }
                    }
                }
            }
        } catch (err) {
        }
        return this;
    }
};
Y.extend(Category, Item, Category.prototype);
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
            if (previous) {
                // After the last previous.
                previous.insertAfter(node, 'after');
            } else {
                // Start of the list.
                node.ancestor('ul').one('li').insert(node, 'before');
            }
            return false;
        }
        this.highlight();
    }
};
Y.extend(Course, Item, Course.prototype);


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "io-base",
        "moodle-core-notification-exception",
        "json-parse",
        "dd-constrain",
        "dd-proxy",
        "dd-drop",
        "dd-delegate",
        "node-event-delegate"
    ]
});
