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
 * @extends Y.Base
 */
var BLOCK = function() {
    BLOCK.superclass.constructor.apply(this, arguments);
};
BLOCK.prototype = {
    /**
     * A content place holder used when the block has been docked.
     * @property contentplaceholder
     * @protected
     * @type Node
     */
    contentplaceholder : null,
    /**
     * The skip link associated with this block.
     * @property contentskipanchor
     * @protected
     * @type Node
     */
    contentskipanchor : null,
    /**
     * The cached content node for the actual block
     * @property cachedcontentnode
     * @protected
     * @type Node
     */
    cachedcontentnode : null,
    /**
     * If true the user preference isn't updated
     * @property skipsetposition
     * @protected
     * @type Boolean
     */
    skipsetposition : true,
    /**
     * The dock item associated with this block
     * @property dockitem
     * @protected
     * @type DOCKITEM
     */
    dockitem : null,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        var node = Y.one('#inst'+this.get('id'));
        if (!node) {
            return false;
        }

        Y.log('Initialised block with instance id:'+this.get('id'), 'note', LOGNS);

        M.core.dock.ensureMoveToIconExists(node);

        // Move the block straight to the dock if required
        if (node.hasClass(CSS.dockonload)) {
            node.removeClass(CSS.dockonload);
            var commands = node.one('.header .title .commands');
            if (!commands) {
                commands = Y.Node.create('<div class="commands"></div>');
                if (node.one('.header .title')) {
                    node.one('.header .title').append(commands);
                }
            }
            this.moveToDock(null, commands);
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
    _getBlockClass : function(node) {
        var classes = node.getAttribute('className').toString(),
            regex = /(^|\s)(block_[a-zA-Z0-9_]+)(\s|$)/,
            matches = regex.exec(classes);
        if (matches) {
            return matches[2];
        }
        return matches;
    },

    /**
     * This function is reponsible for moving a block from the page structure onto the dock.
     * @method moveToDock
     * @param {EventFacade} e
     */
    moveToDock : function(e) {
        if (e) {
            e.halt(true);
        }

        var dock = M.core.dock.get(),
            id = this.get('id'),
            blockcontent = Y.one('#inst'+id).one('.content');

        if (!blockcontent) {
            return;
        }

        Y.log('Moving block to the dock:'+this.get('id'), 'note', LOGNS);

        var icon = (right_to_left()) ? 't/dock_to_block_rtl' : 't/dock_to_block',
            char = (location.href.match(/\?/)) ? '&' : '?',
            blocktitle,
            blockcommands,
            movetoimg,
            moveto;

        this.recordBlockState();

        blocktitle = this.cachedcontentnode.one('.title h2').cloneNode(true);
        blockcommands = this.cachedcontentnode.one('.title .commands').cloneNode(true);

        // Must set the image src seperatly of we get an error with XML strict headers
        movetoimg = Y.Node.create('<img alt="'+Y.Escape.html(M.str.block.undockitem)+'" title="'+
            Y.Escape.html(M.util.get_string('undockblock', 'block', blocktitle.innerHTML)) +'" />');
        movetoimg.setAttribute('src', M.util.image_url(icon, 'moodle'));
        moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>').append(movetoimg);
        moveto.set('href', location.href + char + 'dock='+id);
        blockcommands.append(moveto);

        // Create a new dock item for the block
        this.dockitem = new DOCKEDITEM({
            block : this,
            dock : dock,
            blockinstanceid : id,
            title : blocktitle,
            contents : blockcontent,
            commands : blockcommands,
            blockclass : this._getBlockClass(Y.one('#inst'+id))
        });
        // Register an event so that when it is removed we can put it back as a block
        dock.add(this.dockitem);

        if (!this.skipsetposition) {
            // save the users preference
            M.util.set_user_preference('docked_block_instance_'+id, 1);
        }

        this.set('idDocked', true);
    },
    /**
     * Records the block state and adds it to the docks holding area.
     * @method recordBlockState
     */
    recordBlockState : function() {
        var id = this.get('id'),
            dock = M.core.dock.get(),
            node = Y.one('#inst'+id),
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
        if (!this.cachedcontentnode.one('.title .commands')) {
            this.cachedcontentnode.one('.title').append(Y.Node.create('<div class="commands"></div>'));
        }
    },

    /**
     * This function removes a block from the dock and puts it back into the page structure.
     * @method returnToBlock
     * @return {Boolean}
     */
    returnToBlock : function() {
        var id = this.get('id');

        Y.log('Moving block out of the dock:'+this.get('id'), 'note', LOGNS);

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
        this.cachedcontentnode = Y.one('#'+this.cachedcontentnode.get('id'));

        var commands = this.dockitem.get('commands');
        if (commands) {
            commands.all('.hidepanelicon').remove();
            commands.all('.moveto').remove();
            commands.remove();
        }
        this.cachedcontentnode.one('.title').append(commands);
        this.cachedcontentnode = null;
        M.util.set_user_preference('docked_block_instance_'+id, 0);
        this.set('idDocked', false);
        return true;
    }
};
Y.extend(BLOCK, Y.Base, BLOCK.prototype, {
    NAME : 'moodle-core-dock-block',
    ATTRS : {
        /**
         * The block instance ID
         * @attribute id
         * @writeOnce
         * @type Number
         */
        id : {
            writeOnce : 'initOnly',
            setter : function(value) {
                return parseInt(value, 10);
            }
        },
        /**
         * True if the block has been docked.
         * @attribute isDocked
         * @default false
         * @type Boolean
         */
        isDocked : {
            value : false
        }
    }
});
