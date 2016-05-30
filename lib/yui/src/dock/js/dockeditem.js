/**
 * Dock JS.
 *
 * This file contains the docked item class.
 *
 * @module moodle-core-dock
 */

/**
 * Docked item.
 *
 * @namespace M.core.dock
 * @class DockedItem
 * @constructor
 * @extends Base
 * @uses EventTarget
 */
DOCKEDITEM = function() {
    DOCKEDITEM.superclass.constructor.apply(this, arguments);
};
DOCKEDITEM.prototype = {
    /**
     * Set to true if this item is currently being displayed.
     * @property active
     * @protected
     * @type Boolean
     */
    active : false,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        var title = this.get('title'),
            titlestring,
            type;
        /**
         * Fired before the docked item has been drawn.
         * @event dockeditem:drawstart
         */
        this.publish('dockeditem:drawstart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been drawn.
         * @event dockeditem:drawcomplete
         */
        this.publish('dockeditem:drawcomplete', {prefix:'dockeditem'});
        /**
         * Fired before the docked item is to be shown.
         * @event dockeditem:showstart
         */
        this.publish('dockeditem:showstart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been shown.
         * @event dockeditem:showcomplete
         */
        this.publish('dockeditem:showcomplete', {prefix:'dockeditem'});
        /**
         * Fired before the docked item has been hidden.
         * @event dockeditem:hidestart
         */
        this.publish('dockeditem:hidestart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been hidden.
         * @event dockeditem:hidecomplete
         */
        this.publish('dockeditem:hidecomplete', {prefix:'dockeditem'});
        /**
         * Fired when the docked item is removed from the dock.
         * @event dockeditem:itemremoved
         */
        this.publish('dockeditem:itemremoved', {prefix:'dockeditem'});
        if (title) {
            type = title.get('nodeName');
            titlestring = title.cloneNode(true);
            title = Y.Node.create('<'+type+'></'+type+'>');
            title = M.core.dock.fixTitleOrientation(title, titlestring.get('text'));
            this.set('title', title);
            this.set('titlestring', titlestring);
        }
        Y.log('Initialised dockeditem for block with title "'+this._getLogDescription(), 'debug', LOGNS);
    },
    /**
     * This function draws the item on the dock.
     * @method draw
     * @return Boolean
     */
    draw : function() {
        var create = Y.Node.create,
            dock = this.get('dock'),
            count = dock.count,
            docktitle,
            dockitem,
            closeicon,
            closeiconimg,
            id = this.get('id');

        this.fire('dockeditem:drawstart');

        docktitle = create('<div id="dock_item_'+id+'_title" role="menu" aria-haspopup="true" class="'+CSS.dockedtitle+'"></div>');
        docktitle.append(this.get('title'));
        dockitem = create('<div id="dock_item_'+id+'" class="'+CSS.dockeditem+'" tabindex="0" rel="'+id+'"></div>');
        if (count === 1) {
            dockitem.addClass('firstdockitem');
        }
        dockitem.append(docktitle);
        dock.append(dockitem);

        closeiconimg = create('<img alt="' + M.util.get_string('hidepanel', 'block') +
                '" title="' + M.util.get_string('hidedockpanel', 'block') + '" />');
        closeiconimg.setAttribute('src', M.util.image_url('t/dockclose', 'moodle'));
        closeicon = create('<span class="hidepanelicon" tabindex="0"></span>').append(closeiconimg);
        closeicon.on('forceclose|click', this.hide, this);
        closeicon.on('dock:actionkey',this.hide, this, {actions:{enter:true,toggle:true}});
        this.get('commands').append(closeicon);

        this.set('dockTitleNode', docktitle);
        this.set('dockItemNode', dockitem);

        this.fire('dockeditem:drawcomplete');
        return true;
    },
    /**
     * This function toggles makes the item active and shows it.
     * @method show
     * @return Boolean
     */
    show : function() {
        var dock = this.get('dock'),
            panel = dock.getPanel(),
            docktitle = this.get('dockTitleNode');

        dock.hideActive();
        this.fire('dockeditem:showstart');
        Y.log('Showing '+this._getLogDescription(), 'debug', LOGNS);
        panel.setHeader(this.get('titlestring'), this.get('commands'));
        panel.setBody(Y.Node.create('<div class="block_' + this.get('blockclass') + ' block_docked"></div>')
             .append(this.get('contents')));
        if (M.core.actionmenu !== undefined) {
            M.core.actionmenu.newDOMNode(panel.get('node'));
        }
        panel.show();
        panel.correctWidth();

        this.active = true;
        // Add active item class first up
        docktitle.addClass(CSS.activeitem);
        // Set aria-exapanded property to true.
        docktitle.set('aria-expanded', "true");
        this.fire('dockeditem:showcomplete');
        dock.resize();
        return true;
    },
    /**
     * This function hides the item and makes it inactive.
     * @method hide
     */
    hide : function() {
        this.fire('dockeditem:hidestart');
        Y.log('Hiding "'+this._getLogDescription(), 'debug', LOGNS);
        if (this.active) {
            // No longer active
            this.active = false;
            // Hide the panel
            this.get('dock').getPanel().hide();
        }
        // Remove the active class
        // Set aria-exapanded property to false
        this.get('dockTitleNode').removeClass(CSS.activeitem).set('aria-expanded', "false");
        this.fire('dockeditem:hidecomplete');
    },
    /**
     * A toggle between calling show and hide functions based on css.activeitem
     * Applies rules to key press events (dock:actionkey)
     * @method toggle
     * @param {String} action
     */
    toggle : function(action) {
        var docktitle = this.get('dockTitleNode');
        if (docktitle.hasClass(CSS.activeitem) && action !== 'expand') {
            this.hide();
        } else if (!docktitle.hasClass(CSS.activeitem) && action !== 'collapse')  {
            this.show();
        }
    },
    /**
     * This function removes the node and destroys it's bits.
     * @method remove.
     */
    remove : function () {
        this.hide();
        // Return the block to its original position.
        this.get('block').returnToPage();
        // Remove the dock item node.
        this.get('dockItemNode').remove();
        this.fire('dockeditem:itemremoved');
    },
    /**
     * Returns the description of this item to use for log calls.
     * @method _getLogDescription
     * @private
     * @return {String}
     */
    _getLogDescription : function() {
        return this.get('titlestring').get('innerHTML')+' ('+this.get('blockinstanceid')+')';
    }
};
Y.extend(DOCKEDITEM, Y.Base, DOCKEDITEM.prototype, {
    NAME : 'moodle-core-dock-dockeditem',
    ATTRS : {
        /**
         * The block this docked item is associated with.
         * @attribute block
         * @type BLOCK
         * @writeOnce
         * @required
         */
        block : {
            writeOnce : 'initOnly'
        },
        /**
         * The dock itself.
         * @attribute dock
         * @type DOCK
         * @writeOnce
         * @required
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * The docked item ID. This will be given by the dock.
         * @attribute id
         * @type Number
         */
        id : {},
        /**
         * Block instance id.Taken from the associated block.
         * @attribute blockinstanceid
         * @type Number
         * @writeOnce
         */
        blockinstanceid : {
            writeOnce : 'initOnly',
            setter : function(value) {
                return parseInt(value, 10);
            }
        },
        /**
         * The title  nodeof the docked item.
         * @attribute title
         * @type Node
         * @default null
         */
        title : {
            value : null
        },
        /**
         * The title string.
         * @attribute titlestring
         * @type String
         */
        titlestring : {
            value : null
        },
        /**
         * The contents of the docked item
         * @attribute contents
         * @type Node
         * @writeOnce
         * @required
         */
        contents : {
            writeOnce : 'initOnly'
        },
        /**
         * Commands associated with the block.
         * @attribute commands
         * @type Node
         * @writeOnce
         * @required
         */
        commands : {
            writeOnce : 'initOnly'
        },
        /**
         * The block class.
         * @attribute blockclass
         * @type String
         * @writeOnce
         * @required
         */
        blockclass : {
            writeOnce : 'initOnly'
        },
        /**
         * The title node for the docked block.
         * @attribute dockTitleNode
         * @type Node
         */
        dockTitleNode : {
            value : null
        },
        /**
         * The item node for the docked block.
         * @attribute dockItemNode
         * @type Node
         */
        dockItemNode : {
            value : null
        },
        /**
         * The container for the docked item (will contain the block contents when visible)
         * @attribute dockcontainerNode
         * @type Node
         */
        dockcontainerNode : {
            value : null
        }
    }
});
Y.augment(DOCKEDITEM, Y.EventTarget);
