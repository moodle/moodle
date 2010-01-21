/**
 * START OF BLOCKS CODE
 * This code can be included in the footer instead of the header if we ever
 * have a static JS file that will be loaded in the footer.
 * Once this is done we will then also be able to remove the blocks.dock.init
 * function and call
 */

/**
 * This namespace will contain all of content (functions, classes, properties)
 * for the block system
 * @namespace
 */
M.blocks = M.blocks || {};

/**
 * The dock namespace: Contains all things dock related
 * @namespace
 */
M.blocks.dock = {
    count:0,        // The number of dock items currently
    totalcount:0,   // The number of dock items through the page life
    exists:false,   // True if the dock exists
    items:[],       // An array of dock items
    node:null,      // The YUI node for the dock itself
    earlybinds:[],  // Events added before the dock was augmented to support events
    /**
     * Strings used by the dock/dockitems
     * @namespace
     */
    strings:{
        addtodock : '[[addtodock]]',
        undockitem : '[[undockitem]]',
        undockall : '[[undockall]]'
    },
    /**
     * Configuration parameters used during the initialisation and setup
     * of dock and dock items.
     * This is here specifically so that themers can override core parameters and
     * design aspects without having to re-write navigation
     * @namespace
     */
    cfg:{
        buffer:10,                          // Buffer used when containing a panel
        position:'left',                    // position of the dock
        orientation:'vertical',             // vertical || horizontal determines if we change the title
        /**
         * Display parameters for the dock
         * @namespace
         */
        display:{
            spacebeforefirstitem: 10,       // Space between the top of the dock and the first item
            mindisplaywidth: null            // Minimum width for the display of dock items
        },
        /**
         * CSS classes to use with the dock
         * @namespace
         */
        css: {
            dock:'dock',                    // CSS Class applied to the dock box
            dockspacer:'dockspacer',        // CSS class applied to the dockspacer
            controls:'controls',            // CSS class applied to the controls box
            body:'has_dock',                // CSS class added to the body when there is a dock
            dockeditem:'dockeditem',        // CSS class added to each item in the dock
            dockedtitle:'dockedtitle',      // CSS class added to the item's title in each dock
            activeitem:'activeitem'         // CSS class added to the active item
        },
        /**
         * Configuration options for the panel that items are shown in
         * @namespace
         */
        panel: {
            close:false,                    // Show a close button on the panel
            draggable:false,                // Make the panel draggable
            underlay:"none",                // Use a special underlay
            modal:false,                    // Throws a lightbox if set to true
            keylisteners:null,              // An array of keylisterners to attach
            visible:false,                  // Visible by default
            effect: null,                   // An effect that should be used with the panel
            monitorresize:false,            // Monitor the resize of the panel
            context:null,                   // Sets up contexts for the panel
            fixedcenter:false,              // Always displays the panel in the center of the screen
            zIndex:null,                    // Sets a specific z index for the panel
            constraintoviewport: false,     // Constrain the panel to the viewport
            autofillheight:'body'           // Which container element should fill out empty space
        }
    },
    /**
     * Augments the classes as required and processes early bindings
     */
    init:function() {
        // Give the dock item class the event properties/methods
        Y.augment(M.blocks.dock.item, Y.EventTarget);
        Y.augment(M.blocks.dock, Y.EventTarget, true);
        // Re-apply early bindings properly now that we can
        M.blocks.dock.apply_binds();
    },
    /**
     * Adds a dock item into the dock
     * @function
     * @param {M.blocks.dock.item} item
     */
    add:function(item) {
        item.id = this.totalcount;
        this.count++;
        this.totalcount++;
        this.items[item.id] = item;
        this.draw();
        this.items[item.id].draw();
        this.fire('dock:itemadded', item);
    },
    append : function(docknode) {
        M.blocks.dock.node.one('#dock_item_container').append(docknode);
    },
    /**
     * Draws the dock
     * @function
     * @return bool
     */
    draw:function() {
        if (this.node !== null) {
            return true;
        }
        this.fire('dock:drawstarted');
        this.item_sizer.init();
        this.node = Y.Node.create('<div id="dock" class="'+M.blocks.dock.cfg.css.dock+' '+M.blocks.dock.cfg.css.dock+'_'+M.blocks.dock.cfg.position+'_'+M.blocks.dock.cfg.orientation+'"></div>');
        this.node.appendChild(Y.Node.create('<div class="'+M.blocks.dock.cfg.css.dockspacer+'" style="height:'+M.blocks.dock.cfg.display.spacebeforefirstitem+'px"></div>'));
        this.node.appendChild(Y.Node.create('<div id="dock_item_container"></div>'));
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            this.node.setStyle('height', this.node.get('winHeight')+'px');
        }

        var dockcontrol = Y.Node.create('<div class="'+M.blocks.dock.cfg.css.controls+'"></div>');
        var removeall = Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+mstr.block.undockall+'" title="'+mstr.block.undockall+'" />');
        removeall.on('removeall|click', this.remove_all, this);
        dockcontrol.appendChild(removeall);
        this.node.appendChild(dockcontrol);

        Y.one(document.body).appendChild(this.node);
        Y.one(document.body).addClass(M.blocks.dock.cfg.css.body);
        this.fire('dock:drawcompleted');
        return true;
    },
    /**
     * Removes the node at the given index and puts it back into conventional page sturcture
     * @function
     * @param {int} uid Unique identifier for the block
     * @return {boolean}
     */
    remove:function(uid) {
        if (!this.items[uid]) {
            return false;
        }
        this.items[uid].remove();
        delete this.items[uid];
        this.count--;
        this.fire('dock:itemremoved', uid);
        if (this.count===0) {
            this.fire('dock:toberemoved');
            this.items = [];
            this.node.remove();
            this.node = null;
            this.fire('dock:removed');
        }
        return true;
    },
    /**
     * Removes all nodes and puts them back into conventional page sturcture
     * @function
     * @return {boolean}
     */
    remove_all:function() {
        for (var i in this.items) {
            this.items[i].remove();
            this.count--;
            delete this.items[i];
        }
        Y.fire('dock:toberemoved');
        this.items = [];
        this.node.remove();
        this.node = null;
        Y.fire('dock:removed');
        return true;
    },
    /**
     * Resizes the active item
     * @function
     * @param {Event} e
     */
    resize:function(e){
        for (var i in this.items) {
            if (this.items[i].active) {
                this.items[i].resize_panel(e);
            }
        }
    },
    /**
     * Hides all [the active] items
     * @function
     */
    hide_all:function() {
        for (var i in this.items) {
            this.items[i].hide();
        }
    },
    /**
     * This smart little function allows developers to attach event listeners before
     * the dock has been augmented to allows event listeners.
     * Once the augmentation is complete this function will be replaced with the proper
     * on method for handling event listeners.
     * Finally apply_binds needs to be called in order to properly bind events.
     * @param {string} event
     * @param {function} callback
     */
    on : function(event, callback) {
        this.earlybinds.push({event:event,callback:callback});
    },
    /**
     * This function takes all early binds and attaches them as listeners properly
     * This should only be called once augmentation is complete.
     */
    apply_binds : function() {
        for (var i in this.earlybinds) {
            var bind = this.earlybinds[i];
            this.on(bind.event, bind.callback);
        }
        this.earlybinds = [];
    },
    item_sizer : {
        enabled : false,
        init : function() {
            M.blocks.dock.on('dock:itemadded', this.check_if_required, this);
            M.blocks.dock.on('dock:itemremoved', this.check_if_required, this);
            Y.on('windowresize', this.check_if_required, this);
        },
        check_if_required : function() {
            var possibleheight = M.blocks.dock.node.get('offsetHeight') - M.blocks.dock.node.one('.controls').get('offsetHeight') - (M.blocks.dock.cfg.buffer*3) - (M.blocks.dock.items.length*2);
            var totalheight = 0;
            for (var id in M.blocks.dock.items) {
                var dockedtitle = Y.get(M.blocks.dock.items[id].title).ancestor('.'+M.blocks.dock.cfg.css.dockedtitle);
                if (dockedtitle) {
                    if (this.enabled) {
                        dockedtitle.setStyle('height', 'auto');
                    }
                    totalheight += dockedtitle.get('offsetHeight') || 0;
                }
            }
            if (totalheight > possibleheight) {
                this.enable(possibleheight);
            }
        },
        enable : function(possibleheight) {
            this.enabled = true;
            var runningcount = 0;
            var usedheight = 0;
            for (var id in M.blocks.dock.items) {
                var itemtitle = Y.get(M.blocks.dock.items[id].title).ancestor('.'+M.blocks.dock.cfg.css.dockedtitle);
                if (!itemtitle) {
                    continue;
                }
                var itemheight = Math.floor((possibleheight-usedheight) / (M.blocks.dock.count - runningcount));
                Y.log("("+possibleheight+"-"+usedheight+") / ("+M.blocks.dock.count+" - "+runningcount+") = "+itemheight);
                var offsetheight = itemtitle.get('offsetHeight');
                itemtitle.setStyle('overflow', 'hidden');
                if (offsetheight > itemheight) {
                    itemtitle.setStyle('height', itemheight+'px');
                    usedheight += itemheight;
                } else {
                    usedheight += offsetheight;
                }
                runningcount++;
            }
            Y.log('possible: '+possibleheight+' - used height: '+usedheight);
        }
    },
    /**
     * Namespace containing methods and properties that will be prototyped
     * to the generic block class and possibly overriden by themes
     * @namespace
     */
    abstract_block_class : {

        id : null,                  // The block instance id
        cachedcontentnode : null,   // The cached content node for the actual block
        blockspacewidth : null,     // The width of the block's original container
        skipsetposition : false,    // If true the user preference isn't updated

        /**
         * This function should be called within the block's constructor and is used to
         * set up the initial controls for swtiching block position as well as an initial
         * moves that may be required.
         *
         * @param {YUI.Node} node The node that contains all of the block's content
         */
        init : function(node) {
            if (!node) {
                node = Y.one('#inst'+this.id);
                if (!node) {
                    return;
                }
            }

            var commands = node.one('.header .title .commands');
            if (!commands) {
                commands = Y.Node.create('<div class="commands"></div>');
                if (node.one('.header .title')) {
                    node.one('.header .title').append(commands);
                }
            }

            var moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>');
            moveto.append(Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+mstr.block.undockitem+'" title="'+mstr.block.undockitem+'" />'));
            if (location.href.match(/\?/)) {
                moveto.set('href', location.href+'&dock='+this.id);
            } else {
                moveto.set('href', location.href+'?dock='+this.id);
            }
            commands.append(moveto);
            commands.all('a.moveto').on('movetodock|click', this.move_to_dock, this);

            node.all('.customcommand').each(function(){
                this.remove();
                commands.appendChild(this);
            });

            // Move the block straight to the dock if required
            if (node.hasClass('dock_on_load')) {
                node.removeClass('dock_on_load')
                this.skipsetposition = true;
                this.move_to_dock();
            }
        },

        /**
         * This function is reponsible for moving a block from the page structure onto the
         * dock
         * @param {event}
         */
        move_to_dock : function(e) {
            if (e) {
                e.halt(true);
            }

            var node = Y.one('#inst'+this.id);
            var blockcontent = node.one('.content');
            if (!blockcontent) {
                return;
            }

            this.cachedcontentnode = node;

            node.all('a.moveto').each(function(moveto){
                Y.Event.purgeElement(Y.Node.getDOMNode(moveto), false, 'click');
                if (moveto.hasClass('customcommand')) {
                    moveto.all('img').each(function(movetoimg){
                        movetoimg.setAttribute('src', get_image_url('t/dock_to_block', 'moodle'));
                        movetoimg.setAttribute('alt', mstr.block.undockitem);
                        movetoimg.setAttribute('title', mstr.block.undockitem);
                    }, this);
                }
            }, this);

            var placeholder = Y.Node.create('<div id="content_placeholder_'+this.id+'"></div>');
            node.replace(Y.Node.getDOMNode(placeholder));
            node = null;

            var spacewidth = this.resize_block_space(placeholder);

            var blocktitle = Y.Node.getDOMNode(this.cachedcontentnode.one('.title h2')).cloneNode(true);
            blocktitle.innerHTML = blocktitle.innerHTML.replace(/([a-zA-Z0-9])/g, "$1<br />");

            var commands = this.cachedcontentnode.all('.title .commands');
            var blockcommands = Y.Node.create('<div class="commands"></div>');
            if (commands.size() > 0) {
                blockcommands = commands.item(0);
            }

            // Create a new dock item for the block
            var dockitem = new M.blocks.dock.item(this.id, blocktitle, blockcontent, blockcommands);
            if (spacewidth !== null && M.blocks.dock.cfg.display.mindisplaywidth == null) {
                dockitem.cfg.display.mindisplaywidth = spacewidth;
            }
            // Wire the draw events to register remove events
            dockitem.on('dockeditem:drawcomplete', function(e){
                // check the contents block [editing=off]
                this.contents.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    M.blocks.dock.remove(this.id)
                }, this);
                // check the commands block [editing=on]
                this.commands.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    M.blocks.dock.remove(this.id)
                }, this);
            }, dockitem);

            // Register an event so that when it is removed we can put it back as a block
            dockitem.on('dockitem:itemremoved', this.return_to_block, this, dockitem);
            M.blocks.dock.add(dockitem);

            if (!this.skipsetposition) {
                // save the users preference
                set_user_preference('docked_block_instance_'+this.id, 1);
            } else {
                this.skipsetposition = false;
            }
        },

        /**
         * Resizes the space that contained blocks if there were no blocks left in
         * it. e.g. if all blocks have been moved to the dock
         * @param {Y.Node} node
         */
        resize_block_space : function(node) {
            node = node.ancestor('.block-region');
            if (node) {
                var width =  node.getStyle('width');
                if (node.all('.sideblock').size() === 0 && this.blockspacewidth === null) {
                    // If the node has no children then we can shrink it
                    this.blockspacewidth = width;
                    node.setStyle('width', '0px');
                } else if (this.blockspacewidth !== null) {
                    // Otherwise if it contains children and we have saved a width
                    // we can reapply the width
                    node.setStyle('width', this.blockspacewidth);
                    this.blockspacewidth = null;
                }
                return width;
            }
            return null;
        },

        /**
         * This function removes a block from the dock and puts it back into the page
         * structure.
         * @param {M.blocks.dock.class.item}
         */
        return_to_block : function(dockitem) {
            var placeholder = Y.one('#content_placeholder_'+this.id);
            this.cachedcontentnode.appendChild(dockitem.contents);
            placeholder.replace(Y.Node.getDOMNode(this.cachedcontentnode));
            this.cachedcontentnode = Y.one('#'+this.cachedcontentnode.get('id'));

            this.resize_block_space(this.cachedcontentnode);

            this.cachedcontentnode.all('a.moveto').each(function(moveto){
                Y.Event.purgeElement(Y.Node.getDOMNode(moveto), false, 'click');
                moveto.on('movetodock|click', this.move_to_dock, this);
                if (moveto.hasClass('customcommand')) {
                    moveto.all('img').each(function(movetoimg){
                        movetoimg.setAttribute('src', get_image_url('t/block_to_dock', 'moodle'));
                        movetoimg.setAttribute('alt', mstr.block.addtodock);
                        movetoimg.setAttribute('title', mstr.block.addtodock);
                    }, this);
                }
             }, this);

            var commands = this.cachedcontentnode.all('.commands');
            var blocktitle = this.cachedcontentnode.all('.title');

            if (commands.size() === 1 && blocktitle.size() === 1) {
                commands.item(0).remove();
                blocktitle.item(0).append(commands.item(0));
            }

            this.cachedcontentnode = null;
            set_user_preference('docked_block_instance_'+this.id, 0);
            return true;
        }
    },
    /**
     * This namespace contains the generic properties, methods and events
     * that will be bound to the M.blocks.dock.item class.
     * These can then be overriden to customise the way dock items work/display
     * @namespace
     */
    abstract_item_class : {

        id : null,              // The unique id for the item
        name : null,            // The name of the item
        title : null,           // The title of the item
        contents : null,        // The content of the item
        commands : null,        // The commands for the item
        active : false,         // True if the item is being shown
        panel : null,           // The YUI2 panel the item will be shown in
        preventhide : false,    // If true the next call to hide will be ignored
        cfg : null,             // The config options for this item by default M.blocks.cfg

        /**
         * Initialises all of the items events
         * @function
         */
        init_events : function() {
            this.publish('dockeditem:drawstart', {prefix:'dockeditem'});
            this.publish('dockeditem:drawcomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:showstart', {prefix:'dockeditem'});
            this.publish('dockeditem:showcomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:hidestart', {prefix:'dockeditem'});
            this.publish('dockeditem:hidecomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:resizestart', {prefix:'dockeditem'});
            this.publish('dockeditem:resizecomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:itemremoved', {prefix:'dockeditem'});
        },

        /**
         * This function draws the item on the dock
         */
        draw : function() {
            this.fire('dockeditem:drawstart');
            var dockitemtitle = Y.Node.create('<div id="dock_item_'+this.id+'_title" class="'+this.cfg.css.dockedtitle+'"></div>');
            dockitemtitle.append(this.title);
            var dockitem = Y.Node.create('<div id="dock_item_'+this.id+'" class="'+this.cfg.css.dockeditem+'"></div>');
            if (M.blocks.dock.count === 1) {
                dockitem.addClass('firstdockitem');
            }
            dockitem.append(dockitemtitle);
            if (this.commands.hasChildNodes) {
                this.contents.appendChild(this.commands);
            }
            M.blocks.dock.append(dockitem);

            var position = dockitemtitle.getXY();
            position[0] += parseInt(dockitemtitle.get('offsetWidth'));
            if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 8) {
                position[0] -= 2;
            }
            this.panel = new YAHOO.widget.Panel('dock_item_panel_'+this.id, {
                close:this.cfg.panel.close,
                draggable:this.cfg.panel.draggable,
                underlay:this.cfg.panel.underlay,
                modal: this.cfg.panel.modal,
                keylisteners: this.cfg.panel.keylisteners,
                visible:this.cfg.panel.visible,
                effect:this.cfg.panel.effect,
                monitorresize:this.cfg.panel.monitorresize,
                context: this.cfg.panel.context,
                fixedcenter: this.cfg.panel.fixedcenter,
                zIndex: this.cfg.panel.zIndex,
                constraintoviewport: this.cfg.panel.constraintoviewport,
                xy:position,
                autofillheight:this.cfg.panel.autofillheight});
            this.panel.showEvent.subscribe(this.resize_panel, this, true);
            this.panel.setBody(Y.Node.getDOMNode(this.contents));
            this.panel.render(M.blocks.dock.node);
            if (this.cfg.display.mindisplaywidth !== null && Y.one(this.panel.body).getStyle('minWidth') == '0px') {
                Y.one(this.panel.body).setStyle('minWidth', this.cfg.display.mindisplaywidth);
                Y.one(this.panel.body).setStyle('minHeight', dockitemtitle.get('offsetHeight')+'px');
            }
            dockitem.on('showitem|mouseover', this.show, this);
            this.fire('dockeditem:drawcomplete');
        },
        /**
         * This function removes the node and destroys it's bits
         * @param {Event} e
         */
        remove : function (e) {
            this.hide(e);
            Y.one('#dock_item_'+this.id).remove();
            this.panel.destroy();
            this.fire('dockitem:itemremoved');
        },
        /**
         * This function toggles makes the item active and shows it
         * @param {event}
         */
        show : function(e) {
            M.blocks.dock.hide_all();
            this.fire('dockeditem:showstart');
            this.panel.show(e, this);
            this.active = true;
            Y.one('#dock_item_'+this.id+'_title').addClass(this.cfg.css.activeitem);
            Y.detach('mouseover', this.show, Y.one('#dock_item_'+this.id));
            Y.one('#dock_item_panel_'+this.id).on('dockpreventhide|click', function(){this.preventhide=true;}, this);
            Y.one('#dock_item_'+this.id).on('dockhide|click', this.hide, this);
            Y.get(window).on('dockresize|resize', this.resize_panel, this);
            Y.get(document.body).on('dockhide|click', this.hide, this);
            this.fire('dockeditem:showcomplete');
            return true;
        },
        /**
         * This function hides the item and makes it inactive
         * @param {event}
         */
        hide : function(e) {
            // Ignore this call is preventhide is true
            if (this.preventhide===true) {
                this.preventhide = false;
            } else if (this.active) {
                this.fire('dockeditem:hidestart');
                this.active = false;
                Y.one('#dock_item_'+this.id+'_title').removeClass(this.cfg.css.activeitem);
                Y.one('#dock_item_'+this.id).on('showitem|mouseover', this.show, this);
                Y.get(window).detach('dockresize|resize');
                Y.get(document.body).detach('dockhide|click');
                this.panel.hide(e, this);
                this.fire('dockeditem:hidecomplete');
            }
        },
        /**
         * This function checks the size and position of the panel and moves/resizes if
         * required to keep it within the bounds of the window.
         */
        resize_panel : function() {
            this.fire('dockeditem:resizestart');
            var panelbody = Y.one(this.panel.body);
            var buffer = this.cfg.buffer;
            var screenheight = parseInt(Y.get(document.body).get('winHeight'));
            var panelheight = parseInt(panelbody.get('offsetHeight'));
            var paneltop = parseInt(this.panel.cfg.getProperty('y'));
            var titletop = parseInt(Y.one('#dock_item_'+this.id+'_title').getY());
            var scrolltop = window.pageYOffset || document.body.scrollTop || 0;

            // This makes sure that the panel is the same height as the dock title to
            // begin with
            if (paneltop > (buffer+scrolltop) && paneltop > (titletop+scrolltop)) {
                this.panel.cfg.setProperty('y', titletop+scrolltop);
            }

            // This makes sure that if the panel is big it is moved up to ensure we don't
            // have wasted space above the panel
            if ((paneltop+panelheight)>(screenheight+scrolltop) && paneltop > buffer) {
                paneltop = (screenheight-panelheight-buffer);
                if (paneltop<buffer) {
                    paneltop = buffer;
                }
                this.panel.cfg.setProperty('y', paneltop+scrolltop);
            }

            // This makes the panel constrain to the screen's height if the panel is big
            if (paneltop <= buffer && ((panelheight+paneltop*2) > screenheight || panelbody.hasClass('oversized_content'))) {
                this.panel.cfg.setProperty('height', screenheight-(buffer*3));
                panelbody.setStyle('height', (screenheight-(buffer*3)-10)+'px');
                panelbody.addClass('oversized_content');
            }
            this.fire('dockeditem:resizecomplete');
        }
    }
};

/**
 * This class represents a generic block
 * @class genericblock
 * @constructor
 * @param {int} uid
 */
M.blocks.genericblock = function(uid){
    // Save the unique id as the blocks id
    if (uid && this.id==null) {
        this.id = uid;
    }
    if (this instanceof M.blocks.genericblock) {
        this.init();
    }
};
/** Properties */
M.blocks.genericblock.prototype.name =                    M.blocks.dock.abstract_block_class.name;
M.blocks.genericblock.prototype.cachedcontentnode =       M.blocks.dock.abstract_block_class.cachedcontentnode;
M.blocks.genericblock.prototype.blockspacewidth =         M.blocks.dock.abstract_block_class.blockspacewidth;
M.blocks.genericblock.prototype.skipsetposition =         M.blocks.dock.abstract_block_class.skipsetposition;
/** Methods **/
M.blocks.genericblock.prototype.init =                    M.blocks.dock.abstract_block_class.init;
M.blocks.genericblock.prototype.move_to_dock =            M.blocks.dock.abstract_block_class.move_to_dock;
M.blocks.genericblock.prototype.resize_block_space =      M.blocks.dock.abstract_block_class.resize_block_space;
M.blocks.genericblock.prototype.return_to_block =         M.blocks.dock.abstract_block_class.return_to_block;

/**
 * This class represents an item in the dock
 * @class item
 * @constructor
 * @param {int} uid The unique ID for the item
 * @param {Y.Node} title
 * @param {Y.Node} contents
 * @param {Y.Node} commands
 */
M.blocks.dock.item = function(uid, title, contents, commands){
    if (uid && this.id==null) this.id = uid;
    if (title && this.title==null) this.title = title;
    if (contents && this.contents==null) this.contents = contents;
    if (commands && this.commands==null) this.commands = commands;
    this.init_events();
}
/** Properties */
M.blocks.dock.item.prototype.id =                 M.blocks.dock.abstract_item_class.id;
M.blocks.dock.item.prototype.name =               M.blocks.dock.abstract_item_class.name;
M.blocks.dock.item.prototype.title =              M.blocks.dock.abstract_item_class.title;
M.blocks.dock.item.prototype.contents =           M.blocks.dock.abstract_item_class.contents;
M.blocks.dock.item.prototype.commands =           M.blocks.dock.abstract_item_class.commands;
M.blocks.dock.item.prototype.active =             M.blocks.dock.abstract_item_class.active;
M.blocks.dock.item.prototype.panel =              M.blocks.dock.abstract_item_class.panel;
M.blocks.dock.item.prototype.preventhide =        M.blocks.dock.abstract_item_class.preventhide;
M.blocks.dock.item.prototype.cfg =                M.blocks.dock.cfg;
/** Methods **/
M.blocks.dock.item.prototype.init_events =        M.blocks.dock.abstract_item_class.init_events;
M.blocks.dock.item.prototype.draw =               M.blocks.dock.abstract_item_class.draw;
M.blocks.dock.item.prototype.remove =             M.blocks.dock.abstract_item_class.remove;
M.blocks.dock.item.prototype.show =               M.blocks.dock.abstract_item_class.show;
M.blocks.dock.item.prototype.hide =               M.blocks.dock.abstract_item_class.hide;
M.blocks.dock.item.prototype.resize_panel =       M.blocks.dock.abstract_item_class.resize_panel;

YUI.add('blocks_dock', M.blocks.dock.init, '0.0.0.1', 'requires', yui3loader.modules['blocks_dock'].requires);