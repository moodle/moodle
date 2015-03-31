YUI.add('moodle-core-blocks', function (Y, NAME) {

/**
 * Provides drag and drop functionality for blocks.
 *
 * @module moodle-core-blockdraganddrop
 */

var AJAXURL = '/lib/ajax/blocks.php',
CSS = {
    BLOCK : 'block',
    BLOCKREGION : 'block-region',
    BLOCKADMINBLOCK : 'block_adminblock',
    EDITINGMOVE : 'editing_move',
    HEADER : 'header',
    LIGHTBOX : 'lightbox',
    REGIONCONTENT : 'region-content',
    SKIPBLOCK : 'skip-block',
    SKIPBLOCKTO : 'skip-block-to',
    MYINDEX : 'page-my-index',
    REGIONMAIN : 'region-main',
    BLOCKSMOVING : 'blocks-moving'
};

var SELECTOR = {
    DRAGHANDLE : '.' + CSS.HEADER + ' .commands .moodle-core-dragdrop-draghandle'
};

/**
 * Legacy drag and drop manager.
 * This drag and drop manager is specifically designed for themes using side-pre and side-post
 * that do not make use of the block output methods introduced by MDL-39824.
 *
 * @namespace M.core.blockdraganddrop
 * @class LegacyManager
 * @constructor
 * @extends M.core.dragdrop
 */
var DRAGBLOCK = function() {
    DRAGBLOCK.superclass.constructor.apply(this, arguments);
};
Y.extend(DRAGBLOCK, M.core.dragdrop, {
    skipnodetop : null,
    skipnodebottom : null,
    dragsourceregion : null,
    initializer : function() {
        // Set group for parent class
        this.groups = ['block'];
        this.samenodeclass = CSS.BLOCK;
        this.parentnodeclass = CSS.REGIONCONTENT;

        // Add relevant classes and ID to 'content' block region on My Home page.
        var myhomecontent = Y.Node.all('body#'+CSS.MYINDEX+' #'+CSS.REGIONMAIN+' > .'+CSS.REGIONCONTENT);
        if (myhomecontent.size() > 0) {
            var contentregion = myhomecontent.item(0);
            contentregion.addClass(CSS.BLOCKREGION);
            contentregion.set('id', CSS.REGIONCONTENT);
            contentregion.one('div').addClass(CSS.REGIONCONTENT);
        }

        // Initialise blocks dragging
        // Find all block regions on the page
        var blockregionlist = Y.Node.all('div.'+CSS.BLOCKREGION);

        if (blockregionlist.size() === 0) {
            return false;
        }

        // See if we are missing either of block regions,
        // if yes we need to add an empty one to use as target
        if (blockregionlist.size() !== this.get('regions').length) {
            var blockregion = Y.Node.create('<div></div>')
                .addClass(CSS.BLOCKREGION);
            var regioncontent = Y.Node.create('<div></div>')
                .addClass(CSS.REGIONCONTENT);
            blockregion.appendChild(regioncontent);
            var pre = blockregionlist.filter('#region-pre');
            var post = blockregionlist.filter('#region-post');

            if (pre.size() === 0 && post.size() === 1) {
                // pre block is missing, instert it before post
                blockregion.setAttrs({id : 'region-pre'});
                post.item(0).insert(blockregion, 'before');
                blockregionlist.unshift(blockregion);
            } else if (post.size() === 0 && pre.size() === 1) {
                // post block is missing, instert it after pre
                blockregion.setAttrs({id : 'region-post'});
                pre.item(0).insert(blockregion, 'after');
                blockregionlist.push(blockregion);
            }
        }

        blockregionlist.each(function(blockregionnode) {

            // Setting blockregion as droptarget (the case when it is empty)
            // The region-post (the right one)
            // is very narrow, so add extra padding on the left to drop block on it.
            new Y.DD.Drop({
                node: blockregionnode.one('div.'+CSS.REGIONCONTENT),
                groups: this.groups,
                padding: '40 240 40 240'
            });

            // Make each div element in the list of blocks draggable
            var del = new Y.DD.Delegate({
                container: blockregionnode,
                nodes: '.'+CSS.BLOCK,
                target: true,
                handles: [SELECTOR.DRAGHANDLE],
                invalid: '.block-hider-hide, .block-hider-show, .moveto',
                dragConfig: {groups: this.groups}
            });
            del.dd.plug(Y.Plugin.DDProxy, {
                // Don't move the node at the end of the drag
                moveOnEnd: false
            });
            del.dd.plug(Y.Plugin.DDWinScroll);

            var blocklist = blockregionnode.all('.'+CSS.BLOCK);
            blocklist.each(function(blocknode) {
                var move = blocknode.one('a.'+CSS.EDITINGMOVE);
                if (move) {
                    move.replace(this.get_drag_handle(move.getAttribute('title'), '', 'iconsmall', true));
                    blocknode.one(SELECTOR.DRAGHANDLE).setStyle('cursor', 'move');
                }
            }, this);
        }, this);
    },

    get_block_id : function(node) {
        return Number(node.get('id').replace(/inst/i, ''));
    },

    get_block_region : function(node) {
        var region = node.ancestor('div.'+CSS.BLOCKREGION).get('id').replace(/region-/i, '');
        if (Y.Array.indexOf(this.get('regions'), region) === -1) {
            // Must be standard side-X
            if (window.right_to_left()) {
                if (region === 'post') {
                    region = 'pre';
                } else if (region === 'pre') {
                    region = 'post';
                }
            }
            return 'side-' + region;
        }
        // Perhaps custom region
        return region;
    },

    get_region_id : function(node) {
        return node.get('id').replace(/region-/i, '');
    },

    drag_start : function(e) {
        // Get our drag object
        var drag = e.target;

        // Store the parent node of original drag node (block)
        // we will need it later for show/hide empty regions
        this.dragsourceregion = drag.get('node').ancestor('div.'+CSS.BLOCKREGION);

        // Determine skipnodes and store them
        if (drag.get('node').previous() && drag.get('node').previous().hasClass(CSS.SKIPBLOCK)) {
            this.skipnodetop = drag.get('node').previous();
        }
        if (drag.get('node').next() && drag.get('node').next().hasClass(CSS.SKIPBLOCKTO)) {
            this.skipnodebottom = drag.get('node').next();
        }

        // Add the blocks-moving class so that the theme can respond if need be.
        Y.one('body').addClass(CSS.BLOCKSMOVING);
    },

    drop_over : function(e) {
        // Get a reference to our drag and drop nodes
        var drag = e.drag.get('node');
        var drop = e.drop.get('node');

        // We need to fix the case when parent drop over event has determined
        // 'goingup' and appended the drag node after admin-block.
        if (drop.hasClass(this.parentnodeclass) &&
                drop.one('.'+CSS.BLOCKADMINBLOCK) &&
                drop.one('.'+CSS.BLOCKADMINBLOCK).next('.'+CSS.BLOCK)) {
            drop.prepend(drag);
        }

        // Block is moved within the same region
        // stop here, no need to modify anything.
        if (this.dragsourceregion.contains(drop)) {
            return false;
        }

        // TODO: Hiding-displaying block region only works for base theme blocks
        // (region-pre, region-post) at the moment. It should be improved
        // to work with custom block regions as well.

        // TODO: Fix this for the case when user drag block towards empty section,
        // then the section appears, then user chnages his mind and moving back to
        // original section. The opposite section remains opened and empty.

        var documentbody = Y.one('body');
        // Moving block towards hidden region-content, display it
        var regionname = this.get_region_id(this.dragsourceregion);
        if (documentbody.hasClass('side-'+regionname+'-only')) {
            documentbody.removeClass('side-'+regionname+'-only');
        }

        // Moving from empty region-content towards the opposite one,
        // hide empty one (only for region-pre, region-post areas at the moment).
        regionname = this.get_region_id(drop.ancestor('div.'+CSS.BLOCKREGION));
        if (this.dragsourceregion.all('.'+CSS.BLOCK).size() === 0 &&
                this.dragsourceregion.get('id').match(/(region-pre|region-post)/i)) {
            if (!documentbody.hasClass('side-'+regionname+'-only')) {
                documentbody.addClass('side-'+regionname+'-only');
            }
        }
    },

    drag_end : function() {
        // clear variables
        this.skipnodetop = null;
        this.skipnodebottom = null;
        this.dragsourceregion = null;
        // Remove the blocks moving class once the drag-drop is over.
        Y.one('body').removeClass(CSS.BLOCKSMOVING);
    },

    drag_dropmiss : function(e) {
        // Missed the target, but we assume the user intended to drop it
        // on the last last ghost node location, e.drag and e.drop should be
        // prepared by global_drag_dropmiss parent so simulate drop_hit(e).
        this.drop_hit(e);
    },

    drop_hit : function(e) {
        var drag = e.drag;
        // Get a reference to our drag node
        var dragnode = drag.get('node');
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
            sesskey : M.cfg.sesskey,
            courseid : this.get('courseid'),
            pagelayout : this.get('pagelayout'),
            pagetype : this.get('pagetype'),
            subpage : this.get('subpage'),
            contextid : this.get('contextid'),
            action : 'move',
            bui_moveid : this.get_block_id(dragnode),
            bui_newregion : this.get_block_region(dropnode)
        };

        if (this.get('cmid')) {
            params.cmid = this.get('cmid');
        }

        if (dragnode.next('.'+this.samenodeclass) && !dragnode.next('.'+this.samenodeclass).hasClass(CSS.BLOCKADMINBLOCK)) {
            params.bui_beforeid = this.get_block_id(dragnode.next('.'+this.samenodeclass));
        }

        // Do AJAX request
        Y.io(M.cfg.wwwroot+AJAXURL, {
            method: 'POST',
            data: params,
            on: {
                start : function() {
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
                    } catch (e) {}
                },
                failure: function(tid, response) {
                    this.ajax_failure(response);
                    lightbox.hide();
                }
            },
            context:this
        });
    }
}, {
    NAME : 'core-blocks-dragdrop',
    ATTRS : {
        courseid : {
            value : null
        },
        cmid : {
            value : null
        },
        contextid : {
            value : null
        },
        pagelayout : {
            value : null
        },
        pagetype : {
            value : null
        },
        subpage : {
            value : null
        },
        regions : {
            value : null
        }
    }
});

M.core = M.core || {};
M.core.blockdraganddrop = M.core.blockdraganddrop || {};

/**
 * True if the page is using the new blocks methods.
 * @private
 * @static
 * @property M.core.blockdraganddrop._isusingnewblocksmethod
 * @type Boolean
 * @default null
 */
M.core.blockdraganddrop._isusingnewblocksmethod = null;

/**
 * Returns true if the page is using the new blocks methods.
 * @static
 * @method M.core.blockdraganddrop.is_using_blocks_render_method
 * @return Boolean
 */
M.core.blockdraganddrop.is_using_blocks_render_method = function() {
    if (this._isusingnewblocksmethod === null) {
        var goodregions = Y.all('.block-region[data-blockregion]').size();
        var allregions = Y.all('.block-region').size();
        this._isusingnewblocksmethod = (allregions === goodregions);
        if (goodregions > 0 && allregions > 0 && goodregions !== allregions) {
        }
    }
    return this._isusingnewblocksmethod;
};

/**
 * Initialises a drag and drop manager.
 * This should only ever be called once for a page.
 * @static
 * @method M.core.blockdraganddrop.init
 * @param {Object} params
 * @return Manager
 */
M.core.blockdraganddrop.init = function(params) {
    if (this.is_using_blocks_render_method()) {
        new MANAGER(params);
    } else {
        new DRAGBLOCK(params);
    }
};

/*
 * Legacy code to keep things working.
 */
M.core_blocks = M.core_blocks || {};
M.core_blocks.init_dragdrop = function(params) {
    M.core.blockdraganddrop.init(params);
};
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
var MANAGER = function() {
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
    skipnodetop : null,

    /**
     * The skip block link from below the block being dragged while a drag is in progress.
     * Required by the M.core.dragdrop from whom this class extends.
     * @private
     * @property skipnodebottom
     * @type Node
     * @default null
     */
    skipnodebottom : null,

    /**
     * An associative object of regions and the
     * @property regionobjects
     * @type {Object} Primitive object mocking an associative array.
     * @type {BLOCKREGION} [regionname]* Each item uses the region name as the key with the value being
     *      an instance of the BLOCKREGION class.
     */
    regionobjects : {},

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        var regionnames = this.get('regions'),
            i = 0,
            region,
            regionname,
            dragdelegation;

        // Evil required by M.core.dragdrop.
        this.groups = ['block'];
        this.samenodeclass = CSS.BLOCK;
        this.parentnodeclass = CSS.BLOCKREGION;

        // Add relevant classes and ID to 'content' block region on My Home page.
        var myhomecontent = Y.Node.all('body#'+CSS.MYINDEX+' #'+CSS.REGIONMAIN+' > .'+CSS.REGIONCONTENT);
        if (myhomecontent.size() > 0) {
            var contentregion = myhomecontent.item(0);
            contentregion.addClass(CSS.BLOCKREGION);
            contentregion.set('id', CSS.REGIONCONTENT);
            contentregion.one('div').addClass(CSS.REGIONCONTENT);
        }

        for (i in regionnames) {
            regionname = regionnames[i];
            region = new BLOCKREGION({
                manager : this,
                region : regionname,
                node : Y.one('#block-region-'+regionname)
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
                nodes: '.'+CSS.BLOCK,
                target: true,
                handles: [SELECTOR.DRAGHANDLE],
                invalid: '.block-hider-hide, .block-hider-show, .moveto, .block_fake',
                dragConfig: {groups: this.groups}
            });
            dragdelegation.dd.plug(Y.Plugin.DDProxy, {
                // Don't move the node at the end of the drag
                moveOnEnd: false
            });
            dragdelegation.dd.plug(Y.Plugin.DDWinScroll);

            // On the DD Manager start operation, we enable all block regions so that they can be drop targets. This
            // must be done *before* drag:start but after dragging has been initialised.
            Y.DD.DDM.on('ddm:start', this.enable_all_regions, this);

            region.change_block_move_icons(this);
        }
    },

    /**
     * Returns the ID of the block the given node represents.
     * @method get_block_id
     * @param {Node} node
     * @return {int} The blocks ID in the database.
     */
    get_block_id : function(node) {
        return Number(node.get('id').replace(/inst/i, ''));
    },

    /**
     * Returns the block region that the node is part of or belonging to.
     * @method get_block_region
     * @param {Y.Node} node
     * @return {string} The region name.
     */
    get_block_region : function(node) {
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
    get_region_object : function(node) {
        return this.regionobjects[this.get_block_region(node)];
    },

    /**
     * Enables all fo the regions so that they are all visible while dragging is occuring.
     *
     * @method enable_all_regions
     */
    enable_all_regions : function() {
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
    disable_regions_if_required : function() {
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
    drag_start : function(e) {
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

    /**
     * Called by M.core.dragdrop.global_drop_over when something is dragged over a drop target.
     * @method drop_over
     * @param {Event} e
     */
    drop_over : function(e) {
        // Get a reference to our drag and drop nodes
        var drag = e.drag.get('node');
        var drop = e.drop.get('node');

        // We need to fix the case when parent drop over event has determined
        // 'goingup' and appended the drag node after admin-block.
        if (drop.hasClass(CSS.REGIONCONTENT) &&
                drop.one('.'+CSS.BLOCKADMINBLOCK) &&
                drop.one('.'+CSS.BLOCKADMINBLOCK).next('.'+CSS.BLOCK)) {
            drop.prepend(drag);
        }
    },

    /**
     * Called by M.core.dragdrop.global_drop_end when a drop has been completed.
     * @method drop_end
     */
    drop_end : function() {
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
    drag_dropmiss : function(e) {
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
    drop_hit : function(e) {
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
            sesskey : M.cfg.sesskey,
            courseid : this.get('courseid'),
            pagelayout : this.get('pagelayout'),
            pagetype : this.get('pagetype'),
            subpage : this.get('subpage'),
            contextid : this.get('contextid'),
            action : 'move',
            bui_moveid : this.get_block_id(dragnode),
            bui_newregion : this.get_block_region(dropnode)
        };

        if (this.get('cmid')) {
            params.cmid = this.get('cmid');
        }

        if (dragnode.next('.'+CSS.BLOCK) && !dragnode.next('.'+CSS.BLOCK).hasClass(CSS.BLOCKADMINBLOCK)) {
            params.bui_beforeid = this.get_block_id(dragnode.next('.'+CSS.BLOCK));
        }

        // Do AJAX request
        Y.io(M.cfg.wwwroot+AJAXURL, {
            method: 'POST',
            data: params,
            on: {
                start : function() {
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
                    } catch (e) {}
                },
                failure: function(tid, response) {
                    this.ajax_failure(response);
                    lightbox.hide();
                },
                complete : function() {
                    this.disable_regions_if_required();
                }
            },
            context:this
        });
    }
};
Y.extend(MANAGER, M.core.dragdrop, MANAGER.prototype, {
    NAME : 'core-blocks-dragdrop-manager',
    ATTRS : {
        /**
         * The Course ID if there is one.
         * @attribute courseid
         * @type int|null
         * @default null
         */
        courseid : {
            value : null
        },

        /**
         * The Course Module ID if there is one.
         * @attribute cmid
         * @type int|null
         * @default null
         */
        cmid : {
            value : null
        },

        /**
         * The Context ID.
         * @attribute contextid
         * @type int|null
         * @default null
         */
        contextid : {
            value : null
        },

        /**
         * The current page layout.
         * @attribute pagelayout
         * @type string|null
         * @default null
         */
        pagelayout : {
            value : null
        },

        /**
         * The page type string, should be used as the id for the body tag in the theme.
         * @attribute pagetype
         * @type string|null
         * @default null
         */
        pagetype : {
            value : null
        },

        /**
         * The subpage identifier, if any.
         * @attribute subpage
         * @type string|null
         * @default null
         */
        subpage : {
            value : null
        },

        /**
         * An array of block regions that are present on the page.
         * @attribute regions
         * @type array|null
         * @default Array[]
         */
        regions : {
            value : []
        }
    }
});
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
    initializer : function() {
        var node = this.get('node');
        if (!node) {
            node = this.create_and_add_node();
        }
        var body = Y.one('body'),
            hasblocks = node.all('.'+CSS.BLOCK).size() > 0,
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
    create_and_add_node : function() {
        var c = Y.Node.create,
            region = this.get('region'),
            node = c('<div id="block-region-'+region+'" data-droptarget="1"></div>')
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
                post = Y.one('#block-region-'+haspost);
                if (post) {
                    post.insert(node, 'before');
                    added = true;
                }
            } else {
                pre = Y.one('#block-region-'+haspre);
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
    change_block_move_icons : function(manager) {
        var handle, icon;
        this.get('node').all('.'+CSS.BLOCK+' a.'+CSS.EDITINGMOVE).each(function(moveicon){
            moveicon.setStyle('cursor', 'move');
            handle = manager.get_drag_handle(moveicon.getAttribute('title'), '', 'icon', true);
            icon = handle.one('img');
            icon.addClass('iconsmall');
            icon.removeClass('icon');
            moveicon.replace(handle);
        });
    },

    /**
     * Returns the class name on the body that signifies the document knows about this region.
     * @method get_has_region_class
     * @return String
     */
    get_has_region_class : function() {
        return 'has-region-'+this.get('region');
    },

    /**
     * Returns the class name to use on the body if the region contains no blocks.
     * @method get_empty_region_class
     * @return String
     */
    get_empty_region_class : function() {
        return 'empty-region-'+this.get('region');
    },

    /**
     * Returns the class name to use on the body if the region contains blocks.
     * @method get_used_region_class
     * @return String
     */
    get_used_region_class : function() {
        return 'used-region-'+this.get('region');
    },

    /**
     * Returns the node to use as the drop target for this region.
     * @method get_droptarget
     * @return Node
     */
    get_droptarget : function() {
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
    enable : function() {
        Y.one('body').addClass(this.get_used_region_class()).removeClass(this.get_empty_region_class());
    },

    /**
     * Disables the region if it contains no blocks, essentially hiding it from the user.
     * @method disable_if_required
     */
    disable_if_required : function() {
        if (this.get('node').all('.'+CSS.BLOCK).size() === 0) {
            Y.one('body').addClass(this.get_empty_region_class()).removeClass(this.get_used_region_class());
        }
    }
};
Y.extend(BLOCKREGION, Y.Base, BLOCKREGION.prototype, {
    NAME : 'core-blocks-dragdrop-blockregion',
    ATTRS : {

        /**
         * The drag and drop manager that created this block region instance.
         * @attribute manager
         * @type M.core.blockdraganddrop.Manager
         * @writeOnce
         */
        manager : {
            // Can only be set during initialisation and must be set then.
            writeOnce : 'initOnly',
            validator : function (value) {
                return Y.Lang.isObject(value) && value instanceof MANAGER;
            }
        },

        /**
         * The name of the block region this object represents.
         * @attribute region
         * @type String
         * @writeOnce
         */
        region : {
            // Can only be set during initialisation and must be set then.
            writeOnce : 'initOnly',
            validator : function (value) {
                return Y.Lang.isString(value);
            }
        },

        /**
         * The node the block region HTML starts at.s
         * @attribute region
         * @type Y.Node
         */
        node : {
            validator : function (value) {
                return Y.Lang.isObject(value) || Y.Lang.isNull(value);
            }
        },

        /**
         * True if the block region currently contains blocks.
         * @attribute hasblocks
         * @type Boolean
         * @default false
         */
        hasblocks : {
            value : false,
            validator : function (value) {
                return Y.Lang.isBoolean(value);
            }
        }
    }
});


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "io",
        "dom",
        "dd",
        "dd-scroll",
        "moodle-core-dragdrop",
        "moodle-core-notification"
    ]
});
