// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains classes used to manage the navigation structures in Moodle
 * and was introduced as part of the changes occuring in Moodle 2.0
 *
 * @since 2.0
 * @package javascript
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This namespace will contain all of content (functions, classes, properties)
 * for the block system
 * @namespace
 */
var blocks = blocks || {};
blocks.setup_generic_block = function(uid) {
    Y.use('base','dom','io','node', 'event-custom', function() {
        var block = new blocks.genericblock(uid);
        block.init();
    });
}

/**
 * @namespace
 */
blocks.dock = {
    count:0,        // The number of dock items through the page life
    exists:false,   // True if the dock exists
    items:[],       // An array of dock items
    node:null,      // The YUI node for the dock itself
    earlybinds:[],
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
        display:{
            spacebeforefirstitem: 10         // Space between the top of the dock and the first item
        },
        css: {
            dock:'dock',                  // CSS Class applied to the dock box
            dockspacer:'dockspacer',      // CSS class applied to the dockspacer
            controls:'controls',            // CSS class applied to the controls box
            body:'has_dock',                // CSS class added to the body when there is a dock
            dockeditem:'dockeditem',            // CSS class added to each item in the dock
            dockedtitle:'dockedtitle',         // CSS class added to the item's title in each dock
            activeitem:'activeitem'          // CSS class added to the active item
        },
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
     * Adds a dock item into the dock
     * @function
     */
    add:function(item) {
        item.id = this.count;
        this.count++;
        this.items[item.id] = item;
        this.draw();
        this.items[item.id].draw();
        this.fire('dock:itemadded', item);
    },
    /**
     * Draws the dock
     * @function
     */
    draw:function() {
        if (this.node !== null) {
            return true;
        }
        this.fire('dock:drawstarted');
        this.node = Y.Node.create('<div id="dock" class="'+blocks.dock.cfg.css.dock+'"></div>');
        this.node.appendChild(Y.Node.create('<div class="'+blocks.dock.cfg.css.dockspacer+'" style="height:'+blocks.dock.cfg.display.spacebeforefirstitem+'px"></div>'));
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            this.node.setStyle('height', this.node.get('winHeight')+'px');
        }

        var dockcontrol = Y.Node.create('<div class="'+blocks.dock.cfg.css.controls+'"></div>');
        var removeall = Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+blocks.dock.strings.undockall+'" title="'+blocks.dock.strings.undockall+'" />');
        removeall.on('removeall|click', this.remove_all, this);
        dockcontrol.appendChild(removeall);
        this.node.appendChild(dockcontrol);

        Y.one(document.body).appendChild(this.node);
        Y.one(document.body).addClass(blocks.dock.cfg.css.body);
        this.fire('dock:drawcompleted');
        return true;
    },
    /**
     * Removes the node at the given index and puts it back into conventional page sturcture
     * @function
     */
    remove:function(uid) {
        this.items[uid].remove();
        this.fire('dock:itemremoved', uid);
        this.count--;
        if (this.count===0) {
            this.fire('dock:toberemoved');
            this.items = [];
            this.node.remove();
            this.node = null;
            this.fire('dock:removed');
        }
    },
    /**
     * Removes all nodes and puts them back into conventional page sturcture
     * @function
     */
    remove_all:function() {
        for (var i in this.items) {
            this.items[i].remove();
            this.items[i] = null;
        }
        Y.fire('dock:toberemoved');
        this.items = [];
        this.node.remove();
        this.node = null;
        Y.fire('dock:removed');
    },
    /**
     * Resizes the active item
     * @function
     */
    resize:function(e){
        for (var i in this.items) {
            if (this.items[i].active) {
                this.items[i].resize_panel(e);
            }
        }
    },
    /**
     * Hides all (the active) item
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
    /**
     * Namespace containing methods and properties that will be prototyped
     * to the generic block class and possibly overriden by themes
     * @namespace
     */
    abstract_block_class : {

        id : null,
        cachedcontentnode : null,
        blockspacewidth : null,
        skipsetposition : false,

        /**
         * This function should be called within the block's constructor and is used to
         * set up the initial controls for swtiching block position as well as an initial
         * moves that may be required.
         *
         * @param {YUI.Node} The node that contains all of the block's content
         */
        init : function(node) {
            if (!node) {
                node = Y.one('#inst'+this.id);
            }

            var commands = node.one('.header .title .commands');
            if (!commands) {
                commands = Y.Node.create('<div class="commands"></div>');
                if (node.one('.header .title')) {
                    node.one('.header .title').append(commands);
                }
            }

            var moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>');
            moveto.append(Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+blocks.dock.strings.undockitem+'" title="'+blocks.dock.strings.undockitem+'" />'));
            if (location.href.match(/\?/)) {
                moveto.set('href', location.href+'&dock='+this.id);
            } else {
                moveto.set('href', location.href+'?dock='+this.id);
            }
            commands.append(moveto);
            commands.all('a.moveto').on('movetodock|click', this.move_to_dock, this);

            var customcommands = node.all('.customcommand');
            if (customcommands.size() > 0) {
                customcommands.each(function(){
                    this.remove();
                    commands.appendChild(this);
                });
            }

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

            this.cachedcontentnode = node;

            node.all('a.moveto').each(function(moveto){
                Y.Event.purgeElement(Y.Node.getDOMNode(moveto), false, 'click');
                if (moveto.hasClass('customcommand')) {
                    moveto.all('img').each(function(movetoimg){
                        movetoimg.setAttribute('src', get_image_url('t/dock_to_block', 'moodle'));
                        movetoimg.setAttribute('alt', blocks.dock.strings.undockitem);
                        movetoimg.setAttribute('title', blocks.dock.strings.undockitem);
                    }, this);
                }
            }, this);

            var placeholder = Y.Node.create('<div id="content_placeholder_'+this.id+'"></div>');
            node.replace(Y.Node.getDOMNode(placeholder));
            node = null;

            this.resize_block_space(placeholder);

            var blocktitle = Y.Node.getDOMNode(this.cachedcontentnode.one('.title h2')).cloneNode(true);
            blocktitle.innerHTML = blocktitle.innerHTML.replace(/([a-zA-Z0-9])/g, "$1<br />");

            var commands = this.cachedcontentnode.all('.title .commands');
            var blockcommands = Y.Node.create('<div class="commands"></div>');
            if (commands.size() > 0) {
                blockcommands = commands.item(0);
            }

            var dockitem = new blocks.dock.item(this.id, blocktitle, blockcontent, blockcommands);
            dockitem.on('dockeditem:drawcomplete', function(e){
                // check the contents block [editing=off]
                this.contents.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    blocks.dock.remove(this.id)
                }, this);
                // check the commands block [editing=on]
                this.commands.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    blocks.dock.remove(this.id)
                }, this);
            }, dockitem);
            dockitem.on('dock:itemremoved', this.return_to_block, this, dockitem);
            blocks.dock.add(dockitem);

            if (!this.skipsetposition) {
                set_user_preference('docked_block_instance_'+this.id, 1);
            } else {
                this.skipsetposition = false;
            }
        },

        /**
         * Resizes the space that contained blocks if there were no blocks left in
         * it. e.g. if all blocks have been moved to the dock
         */
        resize_block_space : function(node) {
            node = node.ancestor('.block-region');
            if (node) {
                if (node.all('.sideblock').size() === 0 && this.blockspacewidth === null) {
                    this.blockspacewidth = node.getStyle('width');
                    node.setStyle('width', '0px');
                } else if (this.blockspacewidth !== null) {
                    node.setStyle('width', this.blockspacewidth);
                    this.blockspacewidth = null;
                }
            }
        },

        /**
         * This function removes a block from the dock and puts it back into the page
         * structure.
         * @param {blocks.dock.class.item}
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
                        movetoimg.setAttribute('alt', blocks.dock.strings.addtodock);
                        movetoimg.setAttribute('title', blocks.dock.strings.addtodock);
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

    abstract_item_class : {
        id : null,
        name : null,
        title : null,
        contents : null,
        commands : null,
        events : null,
        active : false,
        panel : null,
        preventhide : false,
        cfg : null,

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
            if (blocks.dock.count === 1) {
                dockitem.addClass('firstdockitem');
            }
            dockitem.append(dockitemtitle);
            if (this.commands.hasChildNodes) {
                this.contents.appendChild(this.commands);
            }
            blocks.dock.node.append(dockitem);

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
            this.panel.render(blocks.dock.node);
            dockitem.on('showitem|mouseover', this.show, this);
            this.fire('dockeditem:drawcomplete');
        },
        /**
         * This function removes the node and destroys it's bits
         */
        remove : function (e) {
            this.hide(e);
            Y.one('#dock_item_'+this.id).remove();
            this.panel.destroy();
            this.fire('dock:itemremoved');
        },
        /**
         * This function toggles makes the item active and shows it
         * @param {event}
         */
        show : function(e) {
            blocks.dock.hide_all();
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
                this.panel.cfg.setProperty('height', screenheight-(buffer*2));
                panelbody.setStyle('height', (screenheight-(buffer*3))+'px');
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
blocks.genericblock = function(uid){
    if (uid && this.id==null) {
        this.id = uid;
    }
};
/** Properties */
blocks.genericblock.prototype.name =                    blocks.dock.abstract_block_class.name;
blocks.genericblock.prototype.cachedcontentnode =       blocks.dock.abstract_block_class.cachedcontentnode;
blocks.genericblock.prototype.blockspacewidth =         blocks.dock.abstract_block_class.blockspacewidth;
blocks.genericblock.prototype.skipsetposition =         blocks.dock.abstract_block_class.skipsetposition;
/** Methods **/
blocks.genericblock.prototype.init =                    blocks.dock.abstract_block_class.init;
blocks.genericblock.prototype.move_to_dock =            blocks.dock.abstract_block_class.move_to_dock;
blocks.genericblock.prototype.resize_block_space =      blocks.dock.abstract_block_class.resize_block_space;
blocks.genericblock.prototype.return_to_block =         blocks.dock.abstract_block_class.return_to_block;

/**
 * This class represents an item in the dock
 * @class item
 * @constructor
 */
blocks.dock.item = function(uid, title, contents, commands){
    if (uid && this.id==null) this.id = uid;
    if (title && this.title==null) this.title = title;
    if (contents && this.contents==null) this.contents = contents;
    if (commands && this.commands==null) this.commands = commands;
    this.init_events();
}
/** Properties */
blocks.dock.item.prototype.id =                 blocks.dock.abstract_item_class.id;
blocks.dock.item.prototype.name =               blocks.dock.abstract_item_class.name;
blocks.dock.item.prototype.title =              blocks.dock.abstract_item_class.title;
blocks.dock.item.prototype.contents =           blocks.dock.abstract_item_class.contents;
blocks.dock.item.prototype.commands =           blocks.dock.abstract_item_class.commands;
blocks.dock.item.prototype.events =             blocks.dock.abstract_item_class.events;
blocks.dock.item.prototype.active =             blocks.dock.abstract_item_class.active;
blocks.dock.item.prototype.panel =              blocks.dock.abstract_item_class.panel;
blocks.dock.item.prototype.preventhide =        blocks.dock.abstract_item_class.preventhide;
blocks.dock.item.prototype.cfg =                blocks.dock.cfg;
/** Methods **/
blocks.dock.item.prototype.init_events =        blocks.dock.abstract_item_class.init_events;
blocks.dock.item.prototype.draw =               blocks.dock.abstract_item_class.draw;
blocks.dock.item.prototype.remove =             blocks.dock.abstract_item_class.remove;
blocks.dock.item.prototype.show =               blocks.dock.abstract_item_class.show;
blocks.dock.item.prototype.hide =               blocks.dock.abstract_item_class.hide;
blocks.dock.item.prototype.resize_panel =       blocks.dock.abstract_item_class.resize_panel;

YUI({base: moodle_cfg.yui3loaderBase}).use('event-custom','event', 'node', function(Y){
    // Give the dock item class the event properties/methods
    Y.augment(blocks.dock.item, Y.EventTarget);
    Y.augment(blocks.dock, Y.EventTarget, true);
    blocks.dock.apply_binds();
});