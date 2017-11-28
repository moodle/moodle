/* global BLOCK, LOGNS, DOCKEDITEM */

/**
 * Dock JS.
 *
 * This file contains the block class used to manage blocks (both docked and not) for the dock.
 *
 * @module moodle-core-dock
 */

/**
 * Block.
 *
 * @namespace M.core.dock
 * @class Block
 * @constructor
 * @extends Base
 */
BLOCK = function() {
    BLOCK.superclass.constructor.apply(this, arguments);
};
BLOCK.prototype = {
    /**
     * A content place holder used when the block has been docked.
     * @property contentplaceholder
     * @protected
     * @type Node
     */
    contentplaceholder: null,
    /**
     * The skip link associated with this block.
     * @property contentskipanchor
     * @protected
     * @type Node
     */
    contentskipanchor: null,
    /**
     * The cached content node for the actual block
     * @property cachedcontentnode
     * @protected
     * @type Node
     */
    cachedcontentnode: null,
    /**
     * If true the user preference isn't updated
     * @property skipsetposition
     * @protected
     * @type Boolean
     */
    skipsetposition: true,
    /**
     * The dock item associated with this block
     * @property dockitem
     * @protected
     * @type DOCKEDITEM
     */
    dockitem: null,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer: function() {
        var node = Y.one('#inst' + this.get('id'));
        if (!node) {
            return false;
        }

        Y.log('Initialised block with instance id:' + this.get('id'), 'debug', LOGNS);

        M.core.dock.ensureMoveToIconExists(node);

        // Move the block straight to the dock if required
        if (node.hasClass(CSS.dockonload)) {
            node.removeClass(CSS.dockonload);
            this.moveToDock();
        }
        this.skipsetposition = false;
        return true;
    },
    /**
     * Returns the class associated with this block.
     * @method _getBlockClass
     * @private
     * @param {Node} node
     * @return String
     */
    _getBlockClass: function(node) {
        var block = node.getData('block'),
            classes,
            matches;
        if (Y.Lang.isString(block) && block !== '') {
            return block;
        }
        classes = node.getAttribute('className').toString();
        matches = /(^| )block_([^ ]+)/.exec(classes);
        if (matches) {
            return matches[2];
        }
        return matches;
    },

    /**
     * This function is responsible for moving a block from the page structure onto the dock.
     * @method moveToDock
     * @param {EventFacade} e
     */
    moveToDock: function(e) {
        if (e) {
            e.halt(true);
        }

        var dock = M.core.dock.get(),
            id = this.get('id'),
            blockcontent = Y.one('#inst' + id).one('.content'),
            icon = (window.right_to_left()) ? 't/dock_to_block_rtl' : 't/dock_to_block',
            breakchar = (location.href.match(/\?/)) ? '&' : '?',
            blocktitle,
            blockcommands,
            movetoimg,
            moveto;

        if (!blockcontent) {
            return;
        }

        Y.log('Moving block to the dock:' + this.get('id'), 'debug', LOGNS);

        this.recordBlockState();

        blocktitle = this.cachedcontentnode.one('.title h2').cloneNode(true);

        // Build up the block commands.
        // These should not actually added to the DOM.
        blockcommands = this.cachedcontentnode.one('.title .commands');
        if (blockcommands) {
            blockcommands = blockcommands.cloneNode(true);
        } else {
            blockcommands = Y.Node.create('<div class="commands"></div>');
        }
        movetoimg = Y.Node.create('<img />').setAttrs({
            alt: Y.Escape.html(M.util.get_string('undockitem', 'block')),
            title: Y.Escape.html(M.util.get_string('undockblock', 'block', blocktitle.get('innerHTML'))),
            src: M.util.image_url(icon, 'moodle')
        });
        moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>').setAttrs({
            href: Y.config.win.location.href + breakchar + 'dock=' + id
        });
        moveto.append(movetoimg);
        blockcommands.append(moveto.append(movetoimg));

        // Create a new dock item for the block
        this.dockitem = new DOCKEDITEM({
            block: this,
            dock: dock,
            blockinstanceid: id,
            title: blocktitle,
            contents: blockcontent,
            commands: blockcommands,
            blockclass: this._getBlockClass(Y.one('#inst' + id))
        });
        // Register an event so that when it is removed we can put it back as a block
        dock.add(this.dockitem);

        if (!this.skipsetposition) {
            // save the users preference
            M.util.set_user_preference('docked_block_instance_' + id, 1);
        }

        this.set('isDocked', true);
    },
    /**
     * Records the block state and adds it to the docks holding area.
     * @method recordBlockState
     */
    recordBlockState: function() {
        var id = this.get('id'),
            dock = M.core.dock.get(),
            node = Y.one('#inst' + id),
            skipanchor = node.previous();
        // Disable the skip anchor when docking
        if (skipanchor.hasClass('skip-block')) {
            this.contentskipanchor = skipanchor;
            this.contentskipanchor.hide();
        }
        this.cachedcontentnode = node;
        this.contentplaceholder = Y.Node.create('<div class="block_dock_placeholder"></div>');
        node.replace(this.contentplaceholder);
        dock.addToHoldingArea(node);
        node = null;
    },

    /**
     * This function removes a block from the dock and puts it back into the page structure.
     * @method returnToPage
     * @return {Boolean}
     */
    returnToPage: function() {
        var id = this.get('id');

        Y.log('Moving block out of the dock:' + this.get('id'), 'debug', LOGNS);

        // Enable the skip anchor when going back to block mode
        if (this.contentskipanchor) {
            this.contentskipanchor.show();
        }

        if (this.cachedcontentnode.one('.header')) {
            this.cachedcontentnode.one('.header').insert(this.dockitem.get('contents'), 'after');
        } else {
            this.cachedcontentnode.insert(this.dockitem.get('contents'));
        }

        this.contentplaceholder.replace(this.cachedcontentnode);
        this.cachedcontentnode = null;

        M.util.set_user_preference('docked_block_instance_' + id, 0);
        this.set('isDocked', false);
        return true;
    }
};
Y.extend(BLOCK, Y.Base, BLOCK.prototype, {
    NAME: 'moodle-core-dock-block',
    ATTRS: {
        /**
         * The block instance ID
         * @attribute id
         * @writeOnce
         * @type Number
         */
        id: {
            writeOnce: 'initOnly',
            setter: function(value) {
                return parseInt(value, 10);
            }
        },
        /**
         * True if the block has been docked.
         * @attribute isDocked
         * @default false
         * @type Boolean
         */
        isDocked: {
            value: false
        }
    }
});
