/**
 * This file contains the drag and drop manager class.
 *
 * Provides drag and drop functionality for blocks.
 *
 * @module moodle-core-blockdraganddrop
 */

/**
 * Constructs a new Block drag and drop manager.
 *
 * @namespace M.core.blockdraganddrop
 * @class Manager
 * @constructor
 * @extends M.core.dragdrop
 */
MANAGER = function() {
    MANAGER.superclass.constructor.apply(this, arguments);
};
MANAGER.prototype = {

    /**
     * The skip block link from above the block being dragged while a drag is in progress.
     * Required by the M.core.dragdrop from whom this class extends.
     * @private
     * @property skipnodetop
     * @type Node
     * @default null
     */
    skipnodetop: null,

    /**
     * The skip block link from below the block being dragged while a drag is in progress.
     * Required by the M.core.dragdrop from whom this class extends.
     * @private
     * @property skipnodebottom
     * @type Node
     * @default null
     */
    skipnodebottom: null,

    /**
     * An associative object of regions and the
     * @property regionobjects
     * @type {Object} Primitive object mocking an associative array.
     * @type {BLOCKREGION} [regionname]* Each item uses the region name as the key with the value being
     *      an instance of the BLOCKREGION class.
     */
    regionobjects: {},

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer: function() {
        Y.log('Initialising drag and drop for blocks.', 'info');
        var regionnames = this.get('regions');
        var i = 0;
        var dragContainer;
        var dragdelegation;
        var region;
        var regionContainer;
        var regionname;

        // Evil required by M.core.dragdrop.
        this.groups = ['block'];
        this.samenodeclass = CSS.BLOCK;
        this.parentnodeclass = CSS.BLOCKREGION;
        // Detect the direction of travel.
        this.detectkeyboarddirection = true;

        // Add relevant classes and ID to 'content' block region on Dashboard page.
        var myhomecontent = Y.Node.all('body#' + CSS.MYINDEX + ' #' + CSS.REGIONMAIN + ' > .' + CSS.REGIONCONTENT);
        if (myhomecontent.size() > 0) {
            var contentregion = myhomecontent.item(0);
            contentregion.addClass(CSS.BLOCKREGION);
            contentregion.set('id', CSS.REGIONCONTENT);
            contentregion.one('div').addClass(CSS.REGIONCONTENT);
        }

        for (i in regionnames) {
            regionname = regionnames[i];
            regionContainer = Y.one('#block-region-' + regionname);
            region = new BLOCKREGION({
                manager: this,
                region: regionname,
                node: regionContainer,
            });
            this.regionobjects[regionname] = region;

            // Setting blockregion as droptarget (the case when it is empty)
            // The region-post (the right one)
            // is very narrow, so add extra padding on the left to drop block on it.
            new Y.DD.Drop({
                node: region.get_droptarget(),
                groups: this.groups,
                padding: '40 240 40 240'
            });

            // Make each div element in the list of blocks draggable
            dragdelegation = new Y.DD.Delegate({
                container: region.get_droptarget(),
                nodes: '.' + CSS.BLOCK,
                target: true,
                handles: [SELECTOR.DRAGHANDLE],
                invalid: '.block-hider-hide, .block-hider-show, .moveto, .block_fake',
                dragConfig: {groups: this.groups}
            });

            dragdelegation.dd.plug(Y.Plugin.DDProxy, {
                // Don't move the node at the end of the drag
                moveOnEnd: false
            });

            if (regionContainer === null) {
                dragdelegation.dd.plug(Y.Plugin.DDWinScroll);
            } else {
                dragContainer = regionContainer.ancestor('.drag-container', true);
                if (dragContainer) {
                    dragdelegation.dd.plug(Y.Plugin.DDNodeScroll, {
                        node: dragContainer,
                    });
                } else {
                    dragdelegation.dd.plug(Y.Plugin.DDWinScroll);
                }
            }

            // On the DD Manager start operation, we enable all block regions so that they can be drop targets. This
            // must be done *before* drag:start but after dragging has been initialised.
            Y.DD.DDM.on('ddm:start', this.enable_all_regions, this);

            region.change_block_move_icons(this);
        }
        Y.log('Initialisation of drag and drop for blocks complete.', 'info');
    },

    /**
     * Returns the ID of the block the given node represents.
     * @method get_block_id
     * @param {Node} node
     * @return {int} The blocks ID in the database.
     */
    get_block_id: function(node) {
        return Number(node.get('id').replace(/inst/i, ''));
    },

    /**
     * Returns the block region that the node is part of or belonging to.
     * @method get_block_region
     * @param {Y.Node} node
     * @return {string} The region name.
     */
    get_block_region: function(node) {
        if (!node.test('[data-blockregion]')) {
            node = node.ancestor('[data-blockregion]');
        }
        return node.getData('blockregion');
    },

    /**
     * Returns the BLOCKREGION instance that represents the block region the given node is part of.
     * @method get_region_object
     * @param {Y.Node} node
     * @return {BLOCKREGION}
     */
    get_region_object: function(node) {
        return this.regionobjects[this.get_block_region(node)];
    },

    /**
     * Enables all fo the regions so that they are all visible while dragging is occuring.
     *
     * @method enable_all_regions
     */
    enable_all_regions: function() {
        var groups = Y.DD.DDM.activeDrag.get('groups');

        // As we're called by Y.DD.DDM, we can't be certain that the call
        // relates specifically to a block drag/drop operation. Test
        // whether the relevant group applies here.
        if (!groups || Y.Array.indexOf(groups, 'block') === -1) {
            return;
        }

        var i;
        for (i in this.regionobjects) {
            if (!this.regionobjects.hasOwnProperty(i)) {
                continue;
            }
            this.regionobjects[i].enable();
        }
    },

    /**
     * Disables enabled regions if they contain no blocks.
     * @method disable_regions_if_required
     */
    disable_regions_if_required: function() {
        var i = 0;
        for (i in this.regionobjects) {
            this.regionobjects[i].disable_if_required();
        }
    },

    /**
     * Called by M.core.dragdrop.global_drag_start when dragging starts.
     * @method drag_start
     * @param {Event} e
     */
    drag_start: function(e) {
        // Get our drag object
        var drag = e.target;

        // Store the parent node of original drag node (block)
        // we will need it later for show/hide empty regions

        // Determine skipnodes and store them
        if (drag.get('node').previous() && drag.get('node').previous().hasClass(CSS.SKIPBLOCK)) {
            this.skipnodetop = drag.get('node').previous();
        }
        if (drag.get('node').next() && drag.get('node').next().hasClass(CSS.SKIPBLOCKTO)) {
            this.skipnodebottom = drag.get('node').next();
        }
    },

    dragOver: function(e) {
        var nearestRegion = e.drop.get('node').ancestor('.drag-container', true);
        if (nearestRegion) {
            if (e.drag[Y.Plugin.DDNodeScroll]) {
                if (e.drag[Y.Plugin.DDNodeScroll].get('node') === nearestRegion) {
                    // Do not bother resetting the region - it has not changed.
                    return;
                } else {
                    e.drag.unplug(Y.Plugin.DDNodeScroll);
                }
            }
            e.drag.plug(Y.Plugin.DDNodeScroll, {
                node: nearestRegion,
            });
        }
    },

    /**
     * Called by M.core.dragdrop.global_drop_over when something is dragged over a drop target.
     * @method drop_over
     * @param {Event} e
     */
    drop_over: function(e) {
        // Get a reference to our drag and drop nodes
        var drag = e.drag.get('node');
        var drop = e.drop.get('node');

        // We need to fix the case when parent drop over event has determined
        // 'goingup' and appended the drag node after admin-block.
        if (drop.hasClass(CSS.REGIONCONTENT) &&
                drop.one('.' + CSS.BLOCKADMINBLOCK) &&
                drop.one('.' + CSS.BLOCKADMINBLOCK).next('.' + CSS.BLOCK)) {
            drop.prepend(drag);
        }
    },

    /**
     * Called by M.core.dragdrop.global_drop_end when a drop has been completed.
     * @method drop_end
     */
    drop_end: function() {
        // Clear variables.
        this.skipnodetop = null;
        this.skipnodebottom = null;
        this.disable_regions_if_required();
    },

    /**
     * Called by M.core.dragdrop.global_drag_dropmiss when something has been dropped on a node that isn't contained by
     * a drop target.
     *
     * @method drag_dropmiss
     * @param {Event} e
     */
    drag_dropmiss: function(e) {
        // Missed the target, but we assume the user intended to drop it
        // on the last ghost node location, e.drag and e.drop should be
        // prepared by global_drag_dropmiss parent so simulate drop_hit(e).
        this.drop_hit(e);
    },

    /**
     * Called by M.core.dragdrop.global_drag_hit when something has been dropped on a drop target.
     * @method drop_hit
     * @param {Event} e
     */
    drop_hit: function(e) {
        // Get a reference to our drag node
        var dragnode = e.drag.get('node');
        var dropnode = e.drop.get('node');

        // Amend existing skipnodes
        if (dragnode.previous() && dragnode.previous().hasClass(CSS.SKIPBLOCK)) {
            // the one that belongs to block below move below
            dragnode.insert(dragnode.previous(), 'after');
        }
        // Move original skipnodes
        if (this.skipnodetop) {
            dragnode.insert(this.skipnodetop, 'before');
        }
        if (this.skipnodebottom) {
            dragnode.insert(this.skipnodebottom, 'after');
        }

        // Add lightbox if it not there
        var lightbox = M.util.add_lightbox(Y, dragnode);

        // Prepare request parameters
        var params = {
            sesskey: M.cfg.sesskey,
            pagehash: this.get('pagehash'),
            action: 'move',
            bui_moveid: this.get_block_id(dragnode),
            bui_newregion: this.get_block_region(dropnode)
        };

        if (this.get('cmid')) {
            params.cmid = this.get('cmid');
        }

        if (dragnode.next('.' + CSS.BLOCK) && !dragnode.next('.' + CSS.BLOCK).hasClass(CSS.BLOCKADMINBLOCK)) {
            params.bui_beforeid = this.get_block_id(dragnode.next('.' + CSS.BLOCK));
        }

        // Do AJAX request
        Y.io(M.cfg.wwwroot + AJAXURL, {
            method: 'POST',
            data: params,
            on: {
                start: function() {
                    lightbox.show();
                },
                success: function(tid, response) {
                    window.setTimeout(function() {
                        lightbox.hide();
                    }, 250);
                    try {
                        var responsetext = Y.JSON.parse(response.responseText);
                        if (responsetext.error) {
                            new M.core.ajaxException(responsetext);
                        }
                    } catch (e) {
                        // Ignore.
                    }
                },
                failure: function(tid, response) {
                    this.ajax_failure(response);
                    lightbox.hide();
                },
                complete: function() {
                    this.disable_regions_if_required();
                }
            },
            context: this
        });
    }
};
Y.extend(MANAGER, M.core.dragdrop, MANAGER.prototype, {
    NAME: 'core-blocks-dragdrop-manager',
    ATTRS: {
        /**
         * The page identifier.
         * @attribute pagehash
         * @type string|null
         * @default null
         */
        pagehash: {
            value: null
        },

        /**
         * An array of block regions that are present on the page.
         * @attribute regions
         * @type array|null
         * @default Array[]
         */
        regions: {
            value: []
        }
    }
});
