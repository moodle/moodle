/* eslint-disable no-empty-function */
/**
 * The core drag and drop module for Moodle which extends the YUI drag and
 * drop functionality with additional features.
 *
 * @module moodle-core-dragdrop
 */
var MOVEICON = {
    pix: "i/move_2d",
    largepix: "i/dragdrop",
    component: 'moodle',
    cssclass: 'moodle-core-dragdrop-draghandle'
};

/**
 * General DRAGDROP class, this should not be used directly,
 * it is supposed to be extended by your class
 *
 * @class M.core.dragdrop
 * @constructor
 * @extends Base
 */
var DRAGDROP = function() {
    DRAGDROP.superclass.constructor.apply(this, arguments);
};

Y.extend(DRAGDROP, Y.Base, {
    /**
     * Whether the item is being moved upwards compared with the last
     * location.
     *
     * @property goingup
     * @type Boolean
     * @default null
     */
    goingup: null,

    /**
     * Whether the item is being moved upwards compared with the start
     * point.
     *
     * @property absgoingup
     * @type Boolean
     * @default null
     */
    absgoingup: null,

    /**
     * The class for the object.
     *
     * @property samenodeclass
     * @type String
     * @default null
     */
    samenodeclass: null,

    /**
     * The class on the parent of the item being moved.
     *
     * @property parentnodeclass
     * @type String
     * @default
     */
    parentnodeclass: null,

    /**
     * The label to use with keyboard drag/drop to describe items of the same Node.
     *
     * @property samenodelabel
     * @type Object
     * @default null
     */
    samenodelabel: null,

    /**
     * The label to use with keyboard drag/drop to describe items of the parent Node.
     *
     * @property samenodelabel
     * @type Object
     * @default null
     */
    parentnodelabel: null,

    /**
     * The groups for this instance.
     *
     * @property groups
     * @type Array
     * @default []
     */
    groups: [],

    /**
     * The previous drop location.
     *
     * @property lastdroptarget
     * @type Node
     * @default null
     */
    lastdroptarget: null,

    /**
     * Should the direction of a keyboard drag and drop item be detected.
     *
     * @property detectkeyboarddirection
     * @type Boolean
     * @default false
     */
    detectkeyboarddirection: false,

    /**
     * Listeners.
     *
     * @property listeners
     * @type Array
     * @default null
     */
    listeners: null,

    /**
     * The initializer which sets up the move action.
     *
     * @method initializer
     * @protected
     */
    initializer: function() {
        this.listeners = [];

        // Listen for all drag:start events.
        this.listeners.push(Y.DD.DDM.on('drag:start', this.global_drag_start, this));

        // Listen for all drag:over events.
        this.listeners.push(Y.DD.DDM.on('drag:over', this.globalDragOver, this));

        // Listen for all drag:end events.
        this.listeners.push(Y.DD.DDM.on('drag:end', this.global_drag_end, this));

        // Listen for all drag:drag events.
        this.listeners.push(Y.DD.DDM.on('drag:drag', this.global_drag_drag, this));

        // Listen for all drop:over events.
        this.listeners.push(Y.DD.DDM.on('drop:over', this.global_drop_over, this));

        // Listen for all drop:hit events.
        this.listeners.push(Y.DD.DDM.on('drop:hit', this.global_drop_hit, this));

        // Listen for all drop:miss events.
        this.listeners.push(Y.DD.DDM.on('drag:dropmiss', this.global_drag_dropmiss, this));

        // Add keybaord listeners for accessible drag/drop
        this.listeners.push(Y.one(Y.config.doc.body).delegate('key', this.global_keydown,
                'down:32, enter, esc', '.' + MOVEICON.cssclass, this));

        // Make the accessible drag/drop respond to a single click.
        this.listeners.push(Y.one(Y.config.doc.body).delegate('click', this.global_keydown,
                '.' + MOVEICON.cssclass, this));
    },

    /**
     * The destructor to shut down the instance of the dragdrop system.
     *
     * @method destructor
     * @protected
     */
    destructor: function() {
        new Y.EventHandle(this.listeners).detach();
    },

    /**
     * Build a new drag handle Node.
     *
     * @method get_drag_handle
     * @param {String} title The title on the drag handle
     * @param {String} classname The name of the class to add to the node
     * wrapping the drag icon
     * @param {String} iconclass Additional class to add to the icon.
     * @return Node The built drag handle.
     */
    get_drag_handle: function(title, classname, iconclass) {

        var dragelement = Y.Node.create('<span></span>')
            .addClass(classname)
            .setAttribute('title', title)
            .setAttribute('tabIndex', 0)
            .setAttribute('data-draggroups', this.groups)
            .setAttribute('role', 'button');
        dragelement.addClass(MOVEICON.cssclass);

        window.require(['core/templates'], function(Templates) {
            Templates.renderPix('i/move_2d', 'core').then(function(html) {
                var dragicon = Y.Node.create(html);
                dragicon.setStyle('cursor', 'move');
                if (typeof iconclass != 'undefined') {
                    dragicon.addClass(iconclass);
                }
                dragelement.appendChild(dragicon);
            });
        });

        return dragelement;
    },

    lock_drag_handle: function(drag, classname) {
        drag.removeHandle('.' + classname);
    },

    unlock_drag_handle: function(drag, classname) {
        drag.addHandle('.' + classname);
        drag.get('activeHandle').focus();
    },

    ajax_failure: function(response) {
        var e = {
            name: response.status + ' ' + response.statusText,
            message: response.responseText
        };
        return new M.core.exception(e);
    },

    in_group: function(target) {
        var ret = false;
        Y.each(this.groups, function(v) {
            if (target._groups[v]) {
                ret = true;
            }
        }, this);
        return ret;
    },
    /*
     * Drag-dropping related functions
     */
    global_drag_start: function(e) {
        // Get our drag object
        var drag = e.target;
        // Check that drag object belongs to correct group
        if (!this.in_group(drag)) {
            return;
        }
        // Store the nodes current style, so we can restore it later.
        this.originalstyle = drag.get('node').getAttribute('style');
        // Set some general styles here
        drag.get('node').setStyle('opacity', '.25');
        drag.get('dragNode').setStyles({
            opacity: '.75',
            borderColor: drag.get('node').getStyle('borderColor'),
            backgroundColor: drag.get('node').getStyle('backgroundColor')
        });
        drag.get('dragNode').empty();
        this.drag_start(e);
    },

    /**
     * Drag-dropping related functions
     *
     * @param {EventFacade} e
     */
    globalDragOver: function(e) {
        this.dragOver(e);
    },

    global_drag_end: function(e) {
        var drag = e.target;
        // Check that drag object belongs to correct group
        if (!this.in_group(drag)) {
            return;
        }
        // Put our general styles back
        drag.get('node').setAttribute('style', this.originalstyle);
        this.drag_end(e);
    },

    global_drag_drag: function(e) {
        var drag = e.target,
            info = e.info;

        // Check that drag object belongs to correct group
        if (!this.in_group(drag)) {
            return;
        }

        // Note, we test both < and > situations here. We don't want to
        // effect a change in direction if the user is only moving side
        // to side with no Y position change.

        // Detect changes in the position relative to the start point.
        if (info.start[1] < info.xy[1]) {
            // We are going up if our final position is higher than our start position.
            this.absgoingup = true;

        } else if (info.start[1] > info.xy[1]) {
            // Otherwise we're going down.
            this.absgoingup = false;
        }

        // Detect changes in the position relative to the last movement.
        if (info.delta[1] < 0) {
            // We are going up if our final position is higher than our start position.
            this.goingup = true;

        } else if (info.delta[1] > 0) {
            // Otherwise we're going down.
            this.goingup = false;
        }

        this.drag_drag(e);
    },

    global_drop_over: function(e) {
        // Check that drop object belong to correct group.
        if (!e.drop || !e.drop.inGroup(this.groups)) {
            return;
        }

        // Get a reference to our drag and drop nodes.
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');

        // Save last drop target for the case of missed target processing.
        this.lastdroptarget = e.drop;

        // Are we dropping within the same parent node?
        if (drop.hasClass(this.samenodeclass)) {
            var where;

            if (this.goingup) {
                where = "before";
            } else {
                where = "after";
            }

            // Add the node contents so that it's moved, otherwise only the drag handle is moved.
            drop.insert(drag, where);
        } else if ((drop.hasClass(this.parentnodeclass) || drop.test('[data-droptarget="1"]')) && !drop.contains(drag)) {
            // We are dropping on parent node and it is empty
            if (this.goingup) {
                drop.append(drag);
            } else {
                drop.prepend(drag);
            }
        }
        this.drop_over(e);
    },

    global_drag_dropmiss: function(e) {
        // drag:dropmiss does not have e.drag and e.drop properties
        // we substitute them for the ease of use. For e.drop we use,
        // this.lastdroptarget (ghost node we use for indicating where to drop)
        e.drag = e.target;
        e.drop = this.lastdroptarget;
        // Check that drag object belongs to correct group
        if (!this.in_group(e.drag)) {
            return;
        }
        // Check that drop object belong to correct group
        if (!e.drop || !e.drop.inGroup(this.groups)) {
            return;
        }
        this.drag_dropmiss(e);
    },

    global_drop_hit: function(e) {
        // Check that drop object belong to correct group
        if (!e.drop || !e.drop.inGroup(this.groups)) {
            return;
        }
        this.drop_hit(e);
    },

    /**
     * This is used to build the text for the heading of the keyboard
     * drag drop menu and the text for the nodes in the list.
     * @method find_element_text
     * @param {Node} n The node to start searching for a valid text node.
     * @return {string} The text of the first text-like child node of n.
     */
    find_element_text: function(n) {
        var text = '';

        // Try to resolve using aria-label first.
        text = n.get('aria-label') || '';
        if (text.length > 0) {
            return text;
        }

        // Now try to resolve using aria-labelledby.
        var labelledByNode = n.get('aria-labelledby');
        if (labelledByNode) {
            var labelNode = Y.one('#' + labelledByNode);
            if (labelNode && labelNode.get('text').length > 0) {
                return labelNode.get('text');
            }
        }

        // The valid node types to get text from.
        var nodes = n.all('h2, h3, h4, h5, span:not(.actions):not(.menu-action-text), p, div.no-overflow, div.dimmed_text');

        nodes.each(function() {
            if (text === '') {
                if (Y.Lang.trim(this.get('text')) !== '') {
                    text = this.get('text');
                }
            }
        });

        if (text !== '') {
            return text;
        }
        return M.util.get_string('emptydragdropregion', 'moodle');
    },

    /**
     * This is used to initiate a keyboard version of a drag and drop.
     * A dialog will open listing all the valid drop targets that can be selected
     * using tab, tab, tab, enter.
     * @method global_start_keyboard_drag
     * @param {Event} e The keydown / click event on the grab handle.
     * @param {Node} dragcontainer The resolved draggable node (an ancestor of the drag handle).
     * @param {Node} draghandle The node that triggered this action.
     */
    global_start_keyboard_drag: function(e, draghandle, dragcontainer) {
        M.core.dragdrop.keydragcontainer = dragcontainer;
        M.core.dragdrop.keydraghandle = draghandle;

        // Get the name of the thing to move.
        var nodetitle = this.find_element_text(dragcontainer);
        var dialogtitle = M.util.get_string('movecontent', 'moodle', nodetitle);

        // Build the list of drop targets.
        var droplist = Y.Node.create('<ul></ul>');
        droplist.addClass('dragdrop-keyboard-drag');
        var listitem, listlink, listitemtext;

        // Search for possible drop targets.
        var droptargets = Y.all('.' + this.samenodeclass + ', .' + this.parentnodeclass);

        droptargets.each(function(node) {
            var validdrop = false;
            var labelroot = node;
            var className = node.getAttribute("class").split(' ').join(', .');

            if (node.drop && node.drop.inGroup(this.groups) && node.drop.get('node') !== dragcontainer &&
                    !(node.next(className) === dragcontainer && !this.detectkeyboarddirection)) {
                // This is a drag and drop target with the same class as the grabbed node.
                validdrop = true;
            } else {
                var elementgroups = node.getAttribute('data-draggroups').split(' ');
                var i, j;
                for (i = 0; i < elementgroups.length; i++) {
                    for (j = 0; j < this.groups.length; j++) {
                        if (elementgroups[i] === this.groups[j] && !node.ancestor('.yui3-dd-proxy') && !(node == dragcontainer ||
                            node.next(className) === dragcontainer || node.get('children').item(0) == dragcontainer)) {
                                // This is a parent node of the grabbed node (used for dropping in empty sections).
                                validdrop = true;
                                // This node will have no text - so we get the first valid text from the parent.
                                labelroot = node.get('parentNode');
                                break;
                        }
                    }
                    if (validdrop) {
                        break;
                    }
                }
            }

            if (validdrop) {
                // It is a valid drop target - create a list item for it.
                listitem = Y.Node.create('<li></li>');
                listlink = Y.Node.create('<a></a>');
                nodetitle = this.find_element_text(labelroot);

                if (this.samenodelabel && node.hasClass(this.samenodeclass)) {
                    listitemtext = M.util.get_string(this.samenodelabel.identifier, this.samenodelabel.component, nodetitle);
                } else if (this.parentnodelabel && node.hasClass(this.parentnodeclass)) {
                    listitemtext = M.util.get_string(this.parentnodelabel.identifier, this.parentnodelabel.component, nodetitle);
                } else {
                    listitemtext = M.util.get_string('tocontent', 'moodle', nodetitle);
                }
                listlink.setContent(listitemtext);

                // Add a data attribute so we can get the real drop target.
                listlink.setAttribute('data-drop-target', node.get('id'));
                // Allow tabbing to the link.
                listlink.setAttribute('tabindex', '0');
                listlink.setAttribute('role', 'button');

                // Set the event listeners for enter, space or click.
                listlink.on('click', this.global_keyboard_drop, this);
                listlink.on('key', this.global_keyboard_drop, 'down:enter,32', this);

                // Add to the list or drop targets.
                listitem.append(listlink);
                droplist.append(listitem);
            }
        }, this);

        // Create the dialog for the interaction.
        M.core.dragdrop.dropui = new M.core.dialogue({
            headerContent: dialogtitle,
            bodyContent: droplist,
            draggable: true,
            visible: true,
            center: true,
            modal: true
        });

        M.core.dragdrop.dropui.after('visibleChange', function(e) {
            // After the dialogue has been closed, we call the cancel function. This will
            // ensure that tidying up happens (e.g. focusing on the start Node).
            if (e.prevVal && !e.newVal) {
                this.global_cancel_keyboard_drag();
            }
        }, this);

        // Focus the first drop target.
        if (droplist.one('a')) {
            droplist.one('a').focus();
        }
    },

    /**
     * This is used as a simulated drag/drop event in order to prevent any
     * subtle bugs from creating a real instance of a drag drop event. This means
     * there are no state changes in the Y.DD.DDM and any undefined functions
     * will trigger an obvious and fatal error.
     * The end result is that we call all our drag/drop handlers but do not bubble the
     * event to anyone else.
     *
     * The functions/properties implemented in the wrapper are:
     * e.target
     * e.drag
     * e.drop
     * e.drag.get('node')
     * e.drop.get('node')
     * e.drag.addHandle()
     * e.drag.removeHandle()
     *
     * @method simulated_drag_drop_event
     * @param {Node} dragnode The drag container node
     * @param {Node} dropnode The node to initiate the drop on
     */
    simulated_drag_drop_event: function(dragnode, dropnode) {

        // Subclass for wrapping both drag and drop.
        var DragDropWrapper = function(node) {
            this.node = node;
        };

        // Method e.drag.get() - get the node.
        DragDropWrapper.prototype.get = function(param) {
            if (param === 'node' || param === 'dragNode' || param === 'dropNode') {
                return this.node;
            }
            if (param === 'activeHandle') {
                return this.node.one('.editing_move');
            }
            return null;
        };

        // Method e.drag.inGroup() - we have already run the group checks before triggering the event.
        DragDropWrapper.prototype.inGroup = function() {
            return true;
        };

        // Method e.drag.addHandle() - we don't want to run this.
        DragDropWrapper.prototype.addHandle = function() {};
        // Method e.drag.removeHandle() - we don't want to run this.
        DragDropWrapper.prototype.removeHandle = function() {};

        // Create instances of the DragDropWrapper.
        this.drop = new DragDropWrapper(dropnode);
        this.drag = new DragDropWrapper(dragnode);
        this.target = this.drop;
    },

    /**
     * This is used to complete a keyboard version of a drag and drop.
     * A drop event will be simulated based on the drag and drop nodes.
     * @method global_keyboard_drop
     * @param {Event} e The keydown / click event on the proxy drop node.
     */
    global_keyboard_drop: function(e) {
        // The drag node was saved.
        var dragcontainer = M.core.dragdrop.keydragcontainer;
        // The real drop node is stored in an attribute of the proxy.
        var droptarget = Y.one('#' + e.target.getAttribute('data-drop-target'));

        // Close the dialog.
        M.core.dragdrop.dropui.hide();
        // Cancel the event.
        e.preventDefault();
        // Detect the direction of travel.
        if (this.detectkeyboarddirection && dragcontainer.getY() > droptarget.getY()) {
            // We can detect the keyboard direction and it is going up.
            this.absgoingup = true;
            this.goingup = true;
        } else {
            // The default behaviour is to treat everything as moving down.
            this.absgoingup = false;
            this.goingup = false;
        }
        // Convert to drag drop events.
        var dragevent = new this.simulated_drag_drop_event(dragcontainer, dragcontainer);
        var dropevent = new this.simulated_drag_drop_event(dragcontainer, droptarget);
        // Simulate the full sequence.
        this.drag_start(dragevent);
        this.global_drop_over(dropevent);

        if (droptarget.hasClass(this.parentnodeclass) && droptarget.contains(dragcontainer)) {
            // Handle the case where an item is dropped into a container (for example an activity into a new section).
            droptarget.prepend(dragcontainer);
        }

        this.global_drop_hit(dropevent);
    },

    /**
     * This is used to cancel a keyboard version of a drag and drop.
     *
     * @method global_cancel_keyboard_drag
     */
    global_cancel_keyboard_drag: function() {
        if (M.core.dragdrop.keydragcontainer) {
            // Focus on the node which was being dragged.
            M.core.dragdrop.keydraghandle.focus();
            M.core.dragdrop.keydragcontainer = null;
        }
        if (M.core.dragdrop.dropui) {
            M.core.dragdrop.dropui.destroy();
        }
    },

    /**
     * Process key events on the drag handles.
     *
     * @method global_keydown
     * @param {EventFacade} e The keydown / click event on the drag handle.
     */
    global_keydown: function(e) {
        var draghandle = e.target.ancestor('.' + MOVEICON.cssclass, true),
            dragcontainer,
            draggroups;

        if (draghandle === null) {
            // The element clicked did not have a a draghandle in it's lineage.
            return;
        }

        if (e.keyCode === 27) {
            // Escape to cancel from anywhere.
            this.global_cancel_keyboard_drag();
            e.preventDefault();
            return;
        }

        // Only process events on a drag handle.
        if (!draghandle.hasClass(MOVEICON.cssclass)) {
            return;
        }

        // Do nothing if not space or enter.
        if (e.keyCode !== 13 && e.keyCode !== 32 && e.type !== 'click') {
            return;
        }

        // Check the drag groups to see if we are the handler for this node.
        draggroups = draghandle.getAttribute('data-draggroups').split(' ');
        var i, j;
        var validgroup = false;

        for (i = 0; i < draggroups.length; i++) {
            for (j = 0; j < this.groups.length; j++) {
                if (draggroups[i] === this.groups[j]) {
                    validgroup = true;
                    break;
                }
            }
            if (validgroup) {
                break;
            }
        }
        if (!validgroup) {
            return;
        }

        // Valid event - start the keyboard drag.
        dragcontainer = draghandle.ancestor('.yui3-dd-drop');
        this.global_start_keyboard_drag(e, draghandle, dragcontainer);

        e.preventDefault();
    },


    // Abstract functions definitions.

    /**
     * Callback to use when dragging starts.
     *
     * @method drag_start
     * @param {EventFacade} e
     */
    drag_start: function() {},

    /**
     * Callback to use for the drag:over event.
     *
     * @method dragOver
     * @param {EventFacade} e
     */
    dragOver: function() {},

    /**
     * Callback to use when dragging ends.
     *
     * @method drag_end
     * @param {EventFacade} e
     */
    drag_end: function() {},

    /**
     * Callback to use during dragging.
     *
     * @method drag_drag
     * @param {EventFacade} e
     */
    drag_drag: function() {},

    /**
     * Callback to use when dragging ends and is not over a drop target.
     *
     * @method drag_dropmiss
     * @param {EventFacade} e
     */
    drag_dropmiss: function() {},

    /**
     * Callback to use when a drop over event occurs.
     *
     * @method drop_over
     * @param {EventFacade} e
     */
    drop_over: function() {},

    /**
     * Callback to use on drop:hit.
     *
     * @method drop_hit
     * @param {EventFacade} e
     */
    drop_hit: function() {}
}, {
    NAME: 'dragdrop',
    ATTRS: {}
});

M.core = M.core || {};
M.core.dragdrop = DRAGDROP;
