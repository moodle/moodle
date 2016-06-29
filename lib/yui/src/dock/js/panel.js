/* global DOCKPANEL, LOGNS */

/**
 * Dock JS.
 *
 * This file contains the panel class used by the dock to display the content of docked blocks.
 *
 * @module moodle-core-dock
 */

/**
 * Panel.
 *
 * @namespace M.core.dock
 * @class Panel
 * @constructor
 * @extends Base
 * @uses EventTarget
 */
DOCKPANEL = function() {
    DOCKPANEL.superclass.constructor.apply(this, arguments);
};
DOCKPANEL.prototype = {
    /**
     * True once the panel has been created.
     * @property created
     * @protected
     * @type {Boolean}
     */
    created : false,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        Y.log('Panel initialising', 'debug', LOGNS);
        /**
         * Fired before the panel is shown.
         * @event dockpane::beforeshow
         */
        this.publish('dockpanel:beforeshow', {prefix:'dockpanel'});
        /**
         * Fired after the panel is shown.
         * @event dockpanel:shown
         */
        this.publish('dockpanel:shown', {prefix:'dockpanel'});
        /**
         * Fired before the panel is hidden.
         * @event dockpane::beforehide
         */
        this.publish('dockpanel:beforehide', {prefix:'dockpanel'});
        /**
         * Fired after the panel is hidden.
         * @event dockpanel:hidden
         */
        this.publish('dockpanel:hidden', {prefix:'dockpanel'});
        /**
         * Fired when ever the dock panel is either hidden or shown.
         * Always fired after the shown or hidden events.
         * @event dockpanel:visiblechange
         */
        this.publish('dockpanel:visiblechange', {prefix:'dockpanel'});
    },
    /**
     * Creates the Panel if it has not already been created.
     * @method create
     * @return {Boolean}
     */
    create : function() {
        if (this.created) {
            return true;
        }
        this.created = true;
        var dock = this.get('dock'),
            node = dock.get('dockNode');
        this.set('node', Y.Node.create('<div id="dockeditempanel" class="dockitempanel_hidden"></div>'));
        this.set('contentNode', Y.Node.create('<div class="dockeditempanel_content"></div>'));
        this.set('headerNode', Y.Node.create('<div class="dockeditempanel_hd"></div>'));
        this.set('bodyNode', Y.Node.create('<div class="dockeditempanel_bd"></div>'));
        node.append(
            this.get('node').append(this.get('contentNode').append(this.get('headerNode')).append(this.get('bodyNode')))
        );
    },
    /**
     * Displays the panel.
     * @method show
     */
    show : function() {
        this.create();
        this.fire('dockpanel:beforeshow');
        this.set('visible', true);
        this.get('node').removeClass('dockitempanel_hidden');
        this.fire('dockpanel:shown');
        this.fire('dockpanel:visiblechange');
    },
    /**
     * Hides the panel
     * @method hide
     */
    hide : function() {
        this.fire('dockpanel:beforehide');
        this.set('visible', false);
        this.get('node').addClass('dockitempanel_hidden');
        this.fire('dockpanel:hidden');
        this.fire('dockpanel:visiblechange');
    },
    /**
     * Sets the panel header.
     * @method setHeader
     * @param {Node|String} content
     */
    setHeader : function(content) {
        this.create();
        var header = this.get('headerNode'),
            i;
        header.setContent(content);
        if (arguments.length > 1) {
            for (i = 1; i < arguments.length; i++) {
                if (Y.Lang.isNumber(i) || Y.Lang.isString(i)) {
                    header.append(arguments[i]);
                }
            }
        }
    },
    /**
     * Sets the panel body.
     * @method setBody
     * @param {Node|String} content
     */
    setBody : function(content) {
        this.create();
        this.get('bodyNode').setContent(content);
    },
    /**
     * Sets the new top mark of the panel.
     *
     * @method setTop
     * @param {Number} newtop
     */
    setTop : function(newtop) {
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            this.get('node').setY(newtop);
        } else {
            this.get('node').setStyle('top', newtop.toString()+'px');
        }
    },
    /**
     * Corrects the width of the panel.
     * @method correctWidth
     */
    correctWidth : function() {
        var bodyNode = this.get('bodyNode'),
            // Width of content.
            width = bodyNode.get('clientWidth'),
            // Scrollable width of content.
            scroll = bodyNode.get('scrollWidth'),
            // Width of content container with overflow.
            offsetWidth = bodyNode.get('offsetWidth'),
            // The new width - defaults to the current width.
            newWidth = width,
            // The max width (80% of screen).
            maxWidth = Math.round(bodyNode.get('winWidth') * 0.8);

        // If the scrollable width is more than the visible width
        if (scroll > width) {
            //   Content width
            // + the difference
            // + any rendering difference (borders, padding)
            // + 10px to make it look nice.
            newWidth = width + (scroll - width) + ((offsetWidth - width)*2) + 10;
        }

        // Make sure its not more then the maxwidth
        if (newWidth > maxWidth) {
            newWidth = maxWidth;
        }

        // Set the new width if its more than the old width.
        if (newWidth > offsetWidth) {
            this.get('node').setStyle('width', newWidth+'px');
        }
    }
};
Y.extend(DOCKPANEL, Y.Base, DOCKPANEL.prototype, {
    NAME : 'moodle-core-dock-panel',
    ATTRS : {
        /**
         * The dock itself.
         * @attribute dock
         * @type DOCK
         * @writeonce
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * The node that contains the whole panel.
         * @attribute node
         * @type Node
         */
        node : {
            value : null
        },
        /**
         * The node that contains the header, body and footer.
         * @attribute contentNode
         * @type Node
         */
        contentNode : {
            value : null
        },
        /**
         * The node that contains the header
         * @attribute headerNode
         * @type Node
         */
        headerNode : {
            value : null
        },
        /**
         * The node that contains the body
         * @attribute bodyNode
         * @type Node
         */
        bodyNode : {
            value : null
        },
        /**
         * True if the panel is currently visible.
         * @attribute visible
         * @type Boolean
         */
        visible : {
            value : false
        }
    }
});
Y.augment(DOCKPANEL, Y.EventTarget);
