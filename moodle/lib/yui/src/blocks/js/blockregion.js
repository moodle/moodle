/* global MANAGER */

/**
 * This file contains the Block Region class used by the drag and drop manager.
 *
 * Provides drag and drop functionality for blocks.
 *
 * @module moodle-core-blockdraganddrop
 */

/**
 * Constructs a new block region object.
 *
 * @namespace M.core.blockdraganddrop
 * @class BlockRegion
 * @constructor
 * @extends Base
 */
var BLOCKREGION = function() {
    BLOCKREGION.superclass.constructor.apply(this, arguments);
};
BLOCKREGION.prototype = {
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer: function() {
        var node = this.get('node');
        Y.log('Block region `' + this.get('region') + '` initialising', 'info');
        if (!node) {
            Y.log('block region known about but no HTML structure found for it. Guessing structure.', 'warn');
            node = this.create_and_add_node();
        }
        var body = Y.one('body'),
            hasblocks = node.all('.' + CSS.BLOCK).size() > 0,
            hasregionclass = this.get_has_region_class();
        this.set('hasblocks', hasblocks);
        if (!body.hasClass(hasregionclass)) {
            body.addClass(hasregionclass);
        }
        body.addClass((hasblocks) ? this.get_used_region_class() : this.get_empty_region_class());
        body.removeClass((hasblocks) ? this.get_empty_region_class() : this.get_used_region_class());
    },
    /**
     * Creates a generic block region node and adds it to the DOM at the best guess location.
     * Any calling of this method is an unfortunate circumstance.
     * @method create_and_add_node
     * @return Node The newly created Node
     */
    create_and_add_node: function() {
        var c = Y.Node.create,
            region = this.get('region'),
            node = c('<div id="block-region-' + region + '" data-droptarget="1"></div>')
                .addClass(CSS.BLOCKREGION)
                .setData('blockregion', region),
            regions = this.get('manager').get('regions'),
            i,
            haspre = false,
            haspost = false,
            added = false,
            pre,
            post;

        for (i in regions) {
            if (regions[i].match(/(pre|left)/)) {
                haspre = regions[i];
            } else if (regions[i].match(/(post|right)/)) {
                haspost = regions[i];
            }
        }

        if (haspre !== false && haspost !== false) {
            if (region === haspre) {
                post = Y.one('#block-region-' + haspost);
                if (post) {
                    post.insert(node, 'before');
                    added = true;
                }
            } else {
                pre = Y.one('#block-region-' + haspre);
                if (pre) {
                    pre.insert(node, 'after');
                    added = true;
                }
            }
        }
        if (added === false) {
            Y.one('body').append(node);
        }
        this.set('node', node);

        return node;
    },

    /**
     * Change the move icons to enhanced drag handles and changes the cursor to a move icon when over the header.
     * @param M.core.dragdrop the block manager
     * @method change_block_move_icons
     */
    change_block_move_icons: function(manager) {
        var handle;
        this.get('node').all('.' + CSS.BLOCK + ' a.' + CSS.EDITINGMOVE).each(function(moveicon) {
            moveicon.setStyle('cursor', 'move');
            handle = manager.get_drag_handle(moveicon.getAttribute('title'), '', 'icon', true);
            moveicon.replace(handle);
        });
    },

    /**
     * Returns the class name on the body that signifies the document knows about this region.
     * @method get_has_region_class
     * @return String
     */
    get_has_region_class: function() {
        return 'has-region-' + this.get('region');
    },

    /**
     * Returns the class name to use on the body if the region contains no blocks.
     * @method get_empty_region_class
     * @return String
     */
    get_empty_region_class: function() {
        return 'empty-region-' + this.get('region');
    },

    /**
     * Returns the class name to use on the body if the region contains blocks.
     * @method get_used_region_class
     * @return String
     */
    get_used_region_class: function() {
        return 'used-region-' + this.get('region');
    },

    /**
     * Returns the node to use as the drop target for this region.
     * @method get_droptarget
     * @return Node
     */
    get_droptarget: function() {
        var node = this.get('node');
        if (node.test('[data-droptarget="1"]')) {
            return node;
        }
        return node.one('[data-droptarget="1"]');
    },

    /**
     * Enables the block region so that we can be sure the user can see it.
     * This is done even if it is empty.
     * @method enable
     */
    enable: function() {
        Y.one('body').addClass(this.get_used_region_class()).removeClass(this.get_empty_region_class());
    },

    /**
     * Disables the region if it contains no blocks, essentially hiding it from the user.
     * @method disable_if_required
     */
    disable_if_required: function() {
        if (this.get('node').all('.' + CSS.BLOCK).size() === 0) {
            Y.one('body').addClass(this.get_empty_region_class()).removeClass(this.get_used_region_class());
        }
    }
};
Y.extend(BLOCKREGION, Y.Base, BLOCKREGION.prototype, {
    NAME: 'core-blocks-dragdrop-blockregion',
    ATTRS: {

        /**
         * The drag and drop manager that created this block region instance.
         * @attribute manager
         * @type M.core.blockdraganddrop.Manager
         * @writeOnce
         */
        manager: {
            // Can only be set during initialisation and must be set then.
            writeOnce: 'initOnly',
            validator: function(value) {
                return Y.Lang.isObject(value) && value instanceof MANAGER;
            }
        },

        /**
         * The name of the block region this object represents.
         * @attribute region
         * @type String
         * @writeOnce
         */
        region: {
            // Can only be set during initialisation and must be set then.
            writeOnce: 'initOnly',
            validator: function(value) {
                return Y.Lang.isString(value);
            }
        },

        /**
         * The node the block region HTML starts at.s
         * @attribute region
         * @type Y.Node
         */
        node: {
            validator: function(value) {
                return Y.Lang.isObject(value) || Y.Lang.isNull(value);
            }
        },

        /**
         * True if the block region currently contains blocks.
         * @attribute hasblocks
         * @type Boolean
         * @default false
         */
        hasblocks: {
            value: false,
            validator: function(value) {
                return Y.Lang.isBoolean(value);
            }
        }
    }
});
