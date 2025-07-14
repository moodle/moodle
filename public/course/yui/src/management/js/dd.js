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
            categoryul = (categorylisting) ? categorylisting.one('ul.category-list') : null,
            courseul = (courselisting) ? courselisting.one('ul.course-list') : null,
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
            Y.log('It was not a course being dragged.', 'warn', 'moodle-course-management');
            return false;
        }
        courseid = drag.getData('id');
        if (iscategory) {
            categoryid = drop.ancestor('.listitem-category').getData('id');
            Y.log('Course ' + courseid + ' dragged into category ' + categoryid);
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
            Y.log('Course dropped over unhandled target.', 'info', 'moodle-course-management');
        }
    }
};
Y.extend(DragDrop, Y.Base, DragDrop.prototype);
