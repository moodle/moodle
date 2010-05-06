/**
 * The dock namespace: Contains all things dock related
 * @namespace
 */
M.core_dock = {
    count : 0,        // The number of dock items currently
    totalcount : 0,   // The number of dock items through the page life
    exists : false,   // True if the dock exists
    items : [],       // An array of dock items
    node : null,      // The YUI node for the dock itself
    earlybinds : [],  // Events added before the dock was augmented to support events
    Y : null,         // The YUI instance to use with dock related code
    /**
     * Strings used by the dock/dockitems
     * @namespace
     */
    strings : {
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
            mindisplaywidth: null,          // Minimum width for the display of dock items
            removeallicon: M.util.image_url('t/dock_to_block', 'moodle')
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
            modalzindex:1000,               // Sets the zIndex for the modal to avoid collisions
            keylisteners:null,              // An array of keylisterners to attach
            visible:false,                  // Visible by default
            effect: null,                   // An effect that should be used with the panel
            monitorresize:false,            // Monitor the resize of the panel
            context:null,                   // Sets up contexts for the panel
            fixedcenter:false,              // Always displays the panel in the center of the screen
            zIndex:9999999,                 // Sets a specific z index for the panel. Has to be high to avoid MCE and filepicker
            constraintoviewport: false,     // Constrain the panel to the viewport
            autofillheight:'body'           // Which container element should fill out empty space
        }
    },
    /**
     * Augments the classes as required and processes early bindings
     */
    init:function(Y) {
        this.Y = Y;
        // Give the dock item class the event properties/methods
        this.Y.augment(M.core_dock.item, this.Y.EventTarget);
        this.Y.augment(M.core_dock, this.Y.EventTarget, true);
        // Re-apply early bindings properly now that we can
        M.core_dock.apply_binds();
        // Check if there is a customisation function
        if (typeof(customise_dock_for_theme) === 'function') {
            customise_dock_for_theme();
        }
    },
    /**
     * Adds a dock item into the dock
     * @function
     * @param {M.core_dock.item} item
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
    /**
     * Appends a dock item to the dock
     * @param {YUI.Node} docknode
     */
    append : function(docknode) {
        M.core_dock.node.one('#dock_item_container').append(docknode);
    },
    /**
     * Initialises a generic block object
     * @param {YUI} Y
     * @param {int} id
     */
    init_genericblock : function(Y, id) {
        var genericblock = new this.genericblock();
        genericblock.id = id;
        genericblock.init(Y, Y.one('#inst'+id));
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
        this.item_sizer.init(this.Y);
        this.node = this.Y.Node.create('<div id="dock" class="'+M.core_dock.cfg.css.dock+' '+this.cfg.css.dock+'_'+this.cfg.position+'_'+this.cfg.orientation+'"></div>');
        this.node.appendChild(this.Y.Node.create('<div class="'+M.core_dock.cfg.css.dockspacer+'" style="height:'+M.core_dock.cfg.display.spacebeforefirstitem+'px"></div>'));
        this.node.appendChild(this.Y.Node.create('<div id="dock_item_container"></div>'));
        if (this.Y.UA.ie > 0 && this.Y.UA.ie < 7) {
            this.node.setStyle('height', this.node.get('winHeight')+'px');
        }
        var dockcontrol = this.Y.Node.create('<div class="'+M.core_dock.cfg.css.controls+'"></div>');
        var removeall = this.Y.Node.create('<img src="'+this.cfg.display.removeallicon+'" alt="'+M.str.block.undockall+'" title="'+M.str.block.undockall+'" />');
        removeall.on('removeall|click', this.remove_all, this);
        dockcontrol.appendChild(removeall);
        this.node.appendChild(dockcontrol);

        this.Y.one(document.body).appendChild(this.node);
        this.Y.one(document.body).addClass(M.core_dock.cfg.css.body);
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
            this.Y.one(document.body).removeClass(M.core_dock.cfg.css.body);
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
        this.Y.fire('dock:toberemoved');
        this.items = [];
        this.node.remove();
        this.node = null;
        this.Y.one(document.body).removeClass(M.core_dock.cfg.css.body);
        this.Y.fire('dock:removed');
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
            this.items[i].hide(null, true);
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
    /**
     * Namespace for the dock sizer which is responsible for ensuring that dock
     * items are visible at all times, this is required because otherwise when there
     * were enough dock items to fit on the dock those that ran over the size of
     * the dock would not be usable
     * @namespace
     */
    item_sizer : {
        enabled : false,        // True if the item_sizer is being used, false otherwise
        Y : null,               // The YUI instance
        /**
         * Initialises the dock sizer which then attaches itself to the required
         * events in order to monitor the dock
         * @param {YUI} Y
         */
        init : function(Y) {
            this.Y = Y;
            M.core_dock.on('dock:itemadded', this.check_if_required, this);
            M.core_dock.on('dock:itemremoved', this.check_if_required, this);
            this.Y.on('windowresize', this.check_if_required, this);
        },
        /**
         * Check if the size dock items needs to be adjusted
         */
        check_if_required : function() {
            var possibleheight = M.core_dock.node.get('offsetHeight') - M.core_dock.node.one('.controls').get('offsetHeight') - (M.core_dock.cfg.buffer*3) - (M.core_dock.items.length*2);
            var totalheight = 0;
            for (var id in M.core_dock.items) {
                var dockedtitle = this.Y.get(M.core_dock.items[id].title).ancestor('.'+M.core_dock.cfg.css.dockedtitle);
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
        /**
         * Enables the dock sizer and resizes where required.
         */
        enable : function(possibleheight) {
            this.enabled = true;
            var runningcount = 0;
            var usedheight = 0;
            for (var id in M.core_dock.items) {
                var itemtitle = this.Y.get(M.core_dock.items[id].title).ancestor('.'+M.core_dock.cfg.css.dockedtitle);
                if (!itemtitle) {
                    continue;
                }
                var itemheight = Math.floor((possibleheight-usedheight) / (M.core_dock.count - runningcount));
                this.Y.log("("+possibleheight+"-"+usedheight+") / ("+M.core_dock.count+" - "+runningcount+") = "+itemheight);
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
            this.Y.log('possible: '+possibleheight+' - used height: '+usedheight);
        }
    },
    /**
     * Namespace containing methods and properties that will be prototyped
     * to the generic block class and possibly overriden by themes
     * @namespace
     */
    abstract_block_class : {

        Y : null,                   // A YUI instance to use with the block
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
        init : function(Y, node) {

            this.Y = Y;
            if (!node) {
                return;
            }

            var commands = node.one('.header .title .commands');
            if (!commands) {
                commands = this.Y.Node.create('<div class="commands"></div>');
                if (node.one('.header .title')) {
                    node.one('.header .title').append(commands);
                }
            }

            var moveto = this.Y.Node.create('<input type="image" class="moveto customcommand requiresjs" src="'+M.util.image_url('t/block_to_dock', 'moodle')+'" alt="'+M.str.block.addtodock+'" title="'+M.str.block.addtodock+'" />');
            moveto.on('movetodock|click', this.move_to_dock, this, commands);

            var blockaction = node.one('.block_action');
            if (blockaction) {
                blockaction.prepend(moveto);
            } else {
                commands.append(moveto);
            }

            // Move the block straight to the dock if required
            if (node.hasClass('dock_on_load')) {
                node.removeClass('dock_on_load')
                this.skipsetposition = true;
                this.move_to_dock(null, commands);
            }
        },

        /**
         * This function is reponsible for moving a block from the page structure onto the
         * dock
         * @param {event}
         */
        move_to_dock : function(e, commands) {
            if (e) {
                e.halt(true);
            }

            var node = this.Y.one('#inst'+this.id);
            var blockcontent = node.one('.content');
            if (!blockcontent) {
                return;
            }

            var blockclass = (function(classes){
                var r = /(^|\s)(block_[a-zA-Z0-9_]+)(\s|$)/;
                var m = r.exec(classes);
                return (m)?m[2]:m;
            })(node.getAttribute('className').toString());

            this.cachedcontentnode = node;

            var placeholder = this.Y.Node.create('<div id="content_placeholder_'+this.id+'"></div>');
            node.replace(this.Y.Node.getDOMNode(placeholder));
            node = null;

            var spacewidth = this.resize_block_space(placeholder);

            var blocktitle = this.Y.Node.getDOMNode(this.cachedcontentnode.one('.title h2')).cloneNode(true);
            blocktitle = this.fix_title_orientation(blocktitle);

            var blockcommands = this.cachedcontentnode.one('.title .commands');
            var moveto = this.Y.Node.create('<a class="moveto customcommand requiresjs"></a>');
            moveto.append(this.Y.Node.create('<img src="'+M.util.image_url('t/dock_to_block', 'moodle')+'" alt="'+M.str.block.undockitem+'" title="'+M.str.block.undockitem+'" />'));
            if (location.href.match(/\?/)) {
                moveto.set('href', location.href+'&dock='+this.id);
            } else {
                moveto.set('href', location.href+'?dock='+this.id);
            }
            blockcommands.append(moveto);

            // Create a new dock item for the block
            var dockitem = new M.core_dock.item(this.Y, this.id, blocktitle, blockcontent, blockcommands, blockclass);
            if (spacewidth !== null && M.core_dock.cfg.display.mindisplaywidth == null) {
                dockitem.cfg.display.mindisplaywidth = spacewidth;
            }
            // Wire the draw events to register remove events
            dockitem.on('dockeditem:drawcomplete', function(e){
                // check the contents block [editing=off]
                this.contents.all('.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    M.core_dock.remove(this.id)
                }, this);
                // check the commands block [editing=on]
                this.commands.all('.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    M.core_dock.remove(this.id)
                }, this);
                // Add a close icon
                var closeicon = this.Y.Node.create('<span class="hidepanelicon"><img src="'+M.util.image_url('t/delete', 'moodle')+'" alt="" style="width:11px;height:11px;cursor:pointer;" /></span>');
                closeicon.on('forceclose|click', M.core_dock.hide_all, M.core_dock);
                closeicon.on('forceclose|click', M.core_dock.hide_all, M.core_dock);
                this.commands.append(closeicon);
            }, dockitem);

            // Register an event so that when it is removed we can put it back as a block
            dockitem.on('dockitem:itemremoved', this.return_to_block, this, dockitem);
            M.core_dock.add(dockitem);

            if (!this.skipsetposition) {
                // save the users preference
                M.util.set_user_preference('docked_block_instance_'+this.id, 1);
            } else {
                this.skipsetposition = false;
            }
        },
        /**
         * Corrects the orientation of the title, which for the default
         * dock just means making it vertical
         * @param {YUI.Node} node
         */
        fix_title_orientation : function(node) {
            node.innerHTML = node.innerHTML.replace(/(.)/g, "$1<br />");
            return node;
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
                if (node.all('.block').size() === 0 && this.blockspacewidth === null) {
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
         * @param {M.core_dock.class.item}
         */
        return_to_block : function(dockitem) {
            var placeholder = this.Y.one('#content_placeholder_'+this.id);
            
            if (this.cachedcontentnode.one('.header')) {
                this.cachedcontentnode.one('.header').insert(dockitem.contents, 'after');
            } else {
                this.cachedcontentnode.insert(dockitem.contents);
            }

            placeholder.replace(this.Y.Node.getDOMNode(this.cachedcontentnode));
            this.cachedcontentnode = this.Y.one('#'+this.cachedcontentnode.get('id'));

            this.resize_block_space(this.cachedcontentnode);


            var commands = this.cachedcontentnode.one('.commands');
            if (commands) {
                commands.all('.hidepanelicon').remove();
                commands.all('.moveto').remove();
                commands.remove();
            }
            this.cachedcontentnode.one('.title').append(commands);
            this.cachedcontentnode = null;
            M.util.set_user_preference('docked_block_instance_'+this.id, 0);
            return true;
        }
    },
    /**
     * This namespace contains the generic properties, methods and events
     * that will be bound to the M.core_dock.item class.
     * These can then be overriden to customise the way dock items work/display
     * @namespace
     */
    abstract_item_class : {

        Y : null,               // The YUI instance to use with this dock item
        id : null,              // The unique id for the item
        name : null,            // The name of the item
        title : null,           // The title of the item
        contents : null,        // The content of the item
        commands : null,        // The commands for the item
        active : false,         // True if the item is being shown
        panel : null,           // The YUI2 panel the item will be shown in
        preventhide : false,    // If true the next call to hide will be ignored
        cfg : null,             // The config options for this item by default M.core_dock.cfg

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
            var dockitemtitle = this.Y.Node.create('<div id="dock_item_'+this.id+'_title" class="'+this.cfg.css.dockedtitle+'"></div>');
            dockitemtitle.append(this.title);
            var dockitem = this.Y.Node.create('<div id="dock_item_'+this.id+'" class="'+this.cfg.css.dockeditem+'"></div>');
            if (M.core_dock.count === 1) {
                dockitem.addClass('firstdockitem');
            }
            dockitem.append(dockitemtitle);
            M.core_dock.append(dockitem);

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
            this.panel.showMaskEvent.subscribe(function(){
                this.Y.one(this.panel.mask).setStyle('zIndex', this.cfg.panel.modalzindex);
            }, this, true);
            if (this.commands.hasChildNodes) {
                this.panel.setHeader(this.Y.Node.getDOMNode(this.commands));
            }
            this.panel.setBody(this.Y.Node.getDOMNode(this.contents));
            this.panel.render(M.core_dock.node);
            this.Y.one(this.panel.body).addClass(this.blockclass);
            if (this.cfg.display.mindisplaywidth !== null && this.Y.one(this.panel.body).getStyle('minWidth') == '0px') {
                this.Y.one(this.panel.body).setStyle('minWidth', this.cfg.display.mindisplaywidth);
                this.Y.one(this.panel.body).setStyle('minHeight', dockitemtitle.get('offsetHeight')+'px');
            }
            dockitem.on('showitem|mouseover', this.show, this);
            dockitem.on('showitem|click', this.show, this);
            this.fire('dockeditem:drawcomplete');
        },
        /**
         * This function removes the node and destroys it's bits
         * @param {Event} e
         */
        remove : function (e) {
            this.hide(e);
            this.Y.one('#dock_item_'+this.id).remove();
            this.panel.destroy();
            this.fire('dockitem:itemremoved');
        },
        /**
         * This function toggles makes the item active and shows it
         * @param {event}
         */
        show : function(e) {
            M.core_dock.hide_all();
            this.fire('dockeditem:showstart');
            this.panel.show(e, this);
            this.active = true;
            // Add active item class first up
            this.Y.one('#dock_item_'+this.id+'_title').addClass(this.cfg.css.activeitem);
            // Remove the two show event listeners
            this.Y.detach('mouseover', this.show, this.Y.one('#dock_item_'+this.id));
            this.Y.detach('click', this.show, this.Y.one('#dock_item_'+this.id));
            // Add control events to ensure we don't cause annoyance
            this.Y.one('#dock_item_panel_'+this.id).on('dockpreventhide|click', function(){this.preventhide=true;}, this);
            // Add resize event so we keep it viewable
            this.Y.get(window).on('dockresize|resize', this.resize_panel, this);

            // If the event was fired by mouse over then we also want to hide when
            // the user moves the mouse out of the area
            if (e.type == 'mouseover') {
                this.Y.one(this.panel.element).on('dockhide|mouseleave', this.delay_hide, this);
                this.preventhide = true;
                setTimeout(function(obj){
                    if (obj.preventhide) {
                        obj.preventhide = false;
                    }
                }, 1000, this);
            }

            // Attach the default hide events, clicking the heading or the body
            this.Y.one('#dock_item_'+this.id).on('dockhide|click', this.hide, this);
            this.Y.get(document.body).on('dockhide|click', this.hide, this);
            
            this.fire('dockeditem:showcomplete');
            return true;
        },
        /**
         * This function hides the item and makes it inactive
         * @param {event} e
         * @param {boolean} ignorepreventhide If true preventhide is ignored
         */
        hide : function(e) {
            // Check whether a second argument has been passed
            var ignorepreventhide = (arguments.length==2 && arguments[1]);
            // Ignore this call is preventhide is true
            if (this.preventhide===true && !ignorepreventhide) {
                this.preventhide = false;
                if (e) {
                    // Stop all propagation immediatly or the next element (likely body)
                    // will fire this event again and the item will hide
                    e.stopImmediatePropagation();
                }
            } else if (this.active) {
                // Display any hide delay running, mouseleave-mouseenter-click
                this.delayhiderunning = false;
                this.fire('dockeditem:hidestart');
                // No longer active
                this.active = false;
                // Remove the active class
                this.Y.one('#dock_item_'+this.id+'_title').removeClass(this.cfg.css.activeitem);
                // Add the show event again
                this.Y.one('#dock_item_'+this.id).on('showitem|mouseover', this.show, this);
                // Remove the hide events
                this.Y.detach('mouseleave', this.delayhide, this.Y.one(this.panel.element));
                this.Y.get(window).detach('dockresize|resize');
                this.Y.get(document.body).detach('dockhide|click');
                // Hide the panel
                this.panel.hide(e, this);
                this.fire('dockeditem:hidecomplete');
            }
        },
        /**
         * This function sets the item to hide after a specific delay, that delay is
         * this.delayhidetimeout.
         * @param {Event} e
         */
        delay_hide : function(e) {
            // The hide delay timeout is running now
            this.delayhiderunning = true;
            // Add the re-enter event to cancel the delay timeout
            var delayhideevent = this.Y.one(this.panel.element).on('delayhide|mouseover', function(){this.delayhiderunning = false;}, this);
            // Set the timeout + callback and pass the this for scope and the event so
            // it can be easily detached
            setTimeout(function(obj, ev){
                if (obj.delayhiderunning) {
                    ev.detach();
                    obj.hide();
                }
            }, this.delayhidetimeout, this, delayhideevent);
        },
        /**
         * This function checks the size and position of the panel and moves/resizes if
         * required to keep it within the bounds of the window.
         */
        resize_panel : function() {
            this.fire('dockeditem:resizestart');

            var panelheader = this.Y.one(this.panel.header);
            panelheader = (panelheader)?panelheader.get('offsetHeight'):0;
            var panelbody = this.Y.one(this.panel.body);

            var buffer = this.cfg.buffer;
            var screenheight = parseInt(this.Y.get(document.body).get('winHeight'));
            var panelheight = parseInt(panelheader + panelbody.get('offsetHeight'));
            var paneltop = parseInt(this.panel.cfg.getProperty('y'));
            var titletop = parseInt(this.Y.one('#dock_item_'+this.id+'_title').getY());
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
                panelbody.setStyle('height', (screenheight-panelheader-(buffer*3)-10)+'px');
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
 */
M.core_dock.genericblock = function() {};
/** Properties */
M.core_dock.genericblock.prototype.cachedcontentnode =       M.core_dock.abstract_block_class.cachedcontentnode;
M.core_dock.genericblock.prototype.blockspacewidth =         M.core_dock.abstract_block_class.blockspacewidth;
M.core_dock.genericblock.prototype.skipsetposition =         M.core_dock.abstract_block_class.skipsetposition;
/** Methods **/
M.core_dock.genericblock.prototype.init =                    M.core_dock.abstract_block_class.init;
M.core_dock.genericblock.prototype.move_to_dock =            M.core_dock.abstract_block_class.move_to_dock;
M.core_dock.genericblock.prototype.resize_block_space =      M.core_dock.abstract_block_class.resize_block_space;
M.core_dock.genericblock.prototype.return_to_block =         M.core_dock.abstract_block_class.return_to_block;
M.core_dock.genericblock.prototype.fix_title_orientation =   M.core_dock.abstract_block_class.fix_title_orientation;

/**
 * This class represents an item in the dock
 * @class item
 * @constructor
 * @param {YUI} Y The YUI instance to use for this item
 * @param {int} uid The unique ID for the item
 * @param {this.Y.Node} title
 * @param {this.Y.Node} contents
 * @param {this.Y.Node} commands
 * @param {string} blockclass
 */
M.core_dock.item = function(Y, uid, title, contents, commands, blockclass){
    this.Y = Y;
    if (uid && this.id==null) {
        this.id = uid;
    }
    if (title && this.title==null) {
        this.title = title;
    }
    if (contents && this.contents==null) {
        this.contents = contents;
    }
    if (commands && this.commands==null) {
        this.commands = commands;
    }
    if (blockclass && this.blockclass==null) {
        this.blockclass = blockclass
    }
    this.init_events();
}
/** Properties */
M.core_dock.item.prototype.id =                 M.core_dock.abstract_item_class.id;
M.core_dock.item.prototype.name =               M.core_dock.abstract_item_class.name;
M.core_dock.item.prototype.title =              M.core_dock.abstract_item_class.title;
M.core_dock.item.prototype.contents =           M.core_dock.abstract_item_class.contents;
M.core_dock.item.prototype.commands =           M.core_dock.abstract_item_class.commands;
M.core_dock.item.prototype.active =             M.core_dock.abstract_item_class.active;
M.core_dock.item.prototype.panel =              M.core_dock.abstract_item_class.panel;
M.core_dock.item.prototype.preventhide =        M.core_dock.abstract_item_class.preventhide;
M.core_dock.item.prototype.cfg =                M.core_dock.cfg;
M.core_dock.item.prototype.blockclass =         null;
M.core_dock.item.prototype.delayhiderunning =   false;
M.core_dock.item.prototype.delayhidetimeout =   1000; // 1 Second
/** Methods **/
M.core_dock.item.prototype.init_events =        M.core_dock.abstract_item_class.init_events;
M.core_dock.item.prototype.draw =               M.core_dock.abstract_item_class.draw;
M.core_dock.item.prototype.remove =             M.core_dock.abstract_item_class.remove;
M.core_dock.item.prototype.show =               M.core_dock.abstract_item_class.show;
M.core_dock.item.prototype.hide =               M.core_dock.abstract_item_class.hide;
M.core_dock.item.prototype.delay_hide =         M.core_dock.abstract_item_class.delay_hide;
M.core_dock.item.prototype.resize_panel =       M.core_dock.abstract_item_class.resize_panel;

/**
 * This ensures that the first time the dock module is used it is initiatlised.
 * 
 * NOTE: Never convert the second argument to a function reference...
 * doing so causes scoping issues
 */
YUI.add('core_dock', function(Y) {M.core_dock.init(Y);}, '0.0.0.1', 'requires', M.yui.loader.modules['core_dock'].requires);