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
            if (right_to_left()) {
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
        if (drop.hasClass(this.parentnodeclass) && drop.one('.'+CSS.BLOCKADMINBLOCK) && drop.one('.'+CSS.BLOCKADMINBLOCK).next('.'+CSS.BLOCK)) {
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
        if (this.dragsourceregion.all('.'+CSS.BLOCK).size() === 0 && this.dragsourceregion.get('id').match(/(region-pre|region-post)/i)) {
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
            Y.log('Both core_renderer::blocks and core_renderer::blocks_for_region have been used.', 'warn', 'moodle-core_blocks');
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
        Y.log('Block drag and drop initialised for the blocks method.', 'info', 'moodle-core_blocks');
        new MANAGER(params);
    } else {
        Y.log('Block drag and drop initialised with the legacy manager (blocks_for_region used).', 'info', 'moodle-core_blocks');
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
