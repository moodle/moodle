/**
 * The dock namespace: Contains all things dock related
 * @namespace
 */
M.core_dock = {
    count : 0,              // The number of dock items currently
    totalcount : 0,         // The number of dock items through the page life
    items : [],             // An array of dock items
    earlybinds : [],        // Events added before the dock was augmented to support events
    Y : null,               // The YUI instance to use with dock related code
    initialised : false,    // True once thedock has been initialised
    delayedevent : null,    // Will be an object if there is a delayed event in effect
    preventevent : null,    // Will be an eventtype if there is an eventyoe to prevent
    holdingarea : null
};
/**
 * Namespace containing the nodes that relate to the dock
 * @namespace
 */
M.core_dock.nodes = {
    dock : null, // The dock itself
    body : null, // The body of the page
    panel : null // The docks panel
};
/**
 * Configuration parameters used during the initialisation and setup
 * of dock and dock items.
 * This is here specifically so that themers can override core parameters and
 * design aspects without having to re-write navigation
 * @namespace
 */
M.core_dock.cfg = {
    buffer:10,                          // Buffer used when containing a panel
    position:'left',                    // position of the dock
    orientation:'vertical',             // vertical || horizontal determines if we change the title
    spacebeforefirstitem: 10,           // Space between the top of the dock and the first item
    removeallicon: M.util.image_url('t/dock_to_block', 'moodle')
};
/**
 * CSS classes to use with the dock
 * @namespace
 */
M.core_dock.css = {
    dock:'dock',                    // CSS Class applied to the dock box
    dockspacer:'dockspacer',        // CSS class applied to the dockspacer
    controls:'controls',            // CSS class applied to the controls box
    body:'has_dock',                // CSS class added to the body when there is a dock
    buttonscontainer: 'buttons_container',
    dockeditem:'dockeditem',        // CSS class added to each item in the dock
    dockeditemcontainer:'dockeditem_container',
    dockedtitle:'dockedtitle',      // CSS class added to the item's title in each dock
    activeitem:'activeitem'         // CSS class added to the active item
};
/**
 * Augments the classes as required and processes early bindings
 */
M.core_dock.init = function(Y) {
    if (this.initialised) {
        return true;
    }
    var css = this.css;
    this.initialised = true;
    this.Y = Y;
    this.nodes.body = Y.one(document.body);

    // Give the dock item class the event properties/methods
    Y.augment(this.item, Y.EventTarget);
    Y.augment(this, Y.EventTarget, true);
    /**
     * A 'dock:actionkey' Event.
     * The event consists of the left arrow, right arrow, enter and space keys.
     * More keys can be mapped to action meanings.
     * actions: collapse , expand, toggle, enter.
     *
     * This event is subscribed to by dockitems.
     * The on() method to subscribe allows specifying the desired trigger actions as JSON.
     *
     * This event can also be delegated if needed.
     * Todo: This could be centralised, a similar Event is defined in blocks/navigation/yui/navigation/navigation.js
     */
    Y.Event.define("dock:actionkey", {
        // Webkit and IE repeat keydown when you hold down arrow keys.
        // Opera links keypress to page scroll; others keydown.
        // Firefox prevents page scroll via preventDefault() on either
        // keydown or keypress.
        _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

        _keys: {
            //arrows
            '37': 'collapse',
            '39': 'expand',
            //(@todo: lrt/rtl/M.core_dock.cfg.orientation decision to assign arrow to meanings)
            '32': 'toggle',
            '13': 'enter'
        },

        _keyHandler: function (e, notifier, args) {
            if (!args.actions) {
                var actObj = {collapse:true, expand:true, toggle:true, enter:true};
            } else {
                var actObj = args.actions;
            }
            if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {
                e.action = this._keys[e.keyCode];
                notifier.fire(e);
            }
        },

        on: function (node, sub, notifier) {
            // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
            if (sub.args == null) {
                //no actions given
                sub._detacher = node.on(this._event, this._keyHandler,this, notifier, {actions:false});
            } else {
                sub._detacher = node.on(this._event, this._keyHandler,this, notifier, sub.args[0]);
            }
        },

        detach: function (node, sub, notifier) {
            //detach our _detacher handle of the subscription made in on()
            sub._detacher.detach();
        },

        delegate: function (node, sub, notifier, filter) {
            // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
            if (sub.args == null) {
                //no actions given
                sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, {actions:false});
            } else {
                sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, sub.args[0]);
            }
        },

        detachDelegate: function (node, sub, notifier) {
            sub._delegateDetacher.detach();
        }
    });
    // Publish the events the dock has
    this.publish('dock:beforedraw', {prefix:'dock'});
    this.publish('dock:beforeshow', {prefix:'dock'});
    this.publish('dock:shown', {prefix:'dock'});
    this.publish('dock:hidden', {prefix:'dock'});
    this.publish('dock:initialised', {prefix:'dock'});
    this.publish('dock:itemadded', {prefix:'dock'});
    this.publish('dock:itemremoved', {prefix:'dock'});
    this.publish('dock:itemschanged', {prefix:'dock'});
    this.publish('dock:panelgenerated', {prefix:'dock'});
    this.publish('dock:panelresizestart', {prefix:'dock'});
    this.publish('dock:resizepanelcomplete', {prefix:'dock'});
    this.publish('dock:starting', {prefix: 'dock',broadcast:  2,emitFacade: true});
    this.fire('dock:starting');
    // Re-apply early bindings properly now that we can
    this.applyBinds();
    // Check if there is a customisation function
    if (typeof(customise_dock_for_theme) === 'function') {
        try {
            // Run the customisation function
            customise_dock_for_theme();
        } catch (exception) {
            // Do nothing at the moment
        }
    }

    var dock = Y.one('#dock');
    if (!dock) {
        // Start the construction of the dock
        dock = Y.Node.create('<div id="dock" class="'+css.dock+' '+css.dock+'_'+this.cfg.position+'_'+this.cfg.orientation+'"></div>')
                    .append(Y.Node.create('<div class="'+css.buttonscontainer+'"></div>')
                        .append(Y.Node.create('<div class="'+css.dockeditemcontainer+'"></div>')));
        this.nodes.body.append(dock);
    } else {
        dock.addClass(css.dock+'_'+this.cfg.position+'_'+this.cfg.orientation);
    }
    this.holdingarea = Y.Node.create('<div></div>').setStyles({display:'none'});
    this.nodes.body.append(this.holdingarea);
    if (Y.UA.ie > 0 && Y.UA.ie < 7) {
        // Adjust for IE 6 (can't handle fixed pos)
        dock.setStyle('height', dock.get('winHeight')+'px');
    }
    // Store the dock
    this.nodes.dock = dock;
    this.nodes.buttons = dock.one('.'+css.buttonscontainer);
    this.nodes.container = this.nodes.buttons.one('.'+css.dockeditemcontainer);

    if (Y.all('.block.dock_on_load').size() == 0) {
        // Nothing on the dock... hide it using CSS
        dock.addClass('nothingdocked');
    } else {
        this.nodes.body.addClass(this.css.body).addClass(this.css.body+'_'+this.cfg.position+'_'+this.cfg.orientation);
    }

    this.fire('dock:beforedraw');

    // Add a removeall button
    // Must set the image src seperatly of we get an error with XML strict headers
    var removeall = Y.Node.create('<img alt="'+M.str.block.undockall+'" title="'+M.str.block.undockall+'" tabindex="0"/>');
    removeall.setAttribute('src',this.cfg.removeallicon);
    removeall.on('removeall|click', this.remove_all, this);
    removeall.on('dock:actionkey', this.remove_all, this, {actions:{enter:true}});
    this.nodes.buttons.appendChild(Y.Node.create('<div class="'+css.controls+'"></div>').append(removeall));

    // Create a manager for the height of the tabs. Once set this can be forgotten about
    new (function(Y){
        return {
            enabled : false,        // True if the item_sizer is being used, false otherwise
            /**
             * Initialises the dock sizer which then attaches itself to the required
             * events in order to monitor the dock
             * @param {YUI} Y
             */
            init : function() {
                M.core_dock.on('dock:itemschanged', this.checkSizing, this);
                Y.on('windowresize', this.checkSizing, this);
            },
            /**
             * Check if the size dock items needs to be adjusted
             */
            checkSizing : function() {
                var dock = M.core_dock;
                var possibleheight = dock.nodes.dock.get('offsetHeight') - dock.nodes.dock.one('.controls').get('offsetHeight') - (dock.cfg.buffer*3) - (dock.items.length*2);
                var totalheight = 0;
                for (var id in dock.items) {
                    var dockedtitle = Y.one(dock.items[id].title).ancestor('.'+dock.css.dockedtitle);
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
                var dock = M.core_dock;
                var runningcount = 0;
                var usedheight = 0;
                this.enabled = true;
                for (var id in dock.items) {
                    var itemtitle = Y.one(dock.items[id].title).ancestor('.'+dock.css.dockedtitle);
                    if (!itemtitle) {
                        continue;
                    }
                    var itemheight = Math.floor((possibleheight-usedheight) / (dock.count - runningcount));
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
            }
        };
    })(Y).init();

    // Attach the required event listeners
    // We use delegate here as that way a handful of events are created for the dock
    // and all items rather than the same number for the dock AND every item individually
    Y.delegate('click', this.handleEvent, this.nodes.dock, '.'+this.css.dockedtitle, this, {cssselector:'.'+this.css.dockedtitle, delay:0});
    Y.delegate('mouseenter', this.handleEvent, this.nodes.dock, '.'+this.css.dockedtitle, this, {cssselector:'.'+this.css.dockedtitle, delay:0.5, iscontained:true, preventevent:'click', preventdelay:3});
    //Y.delegate('mouseleave', this.handleEvent, this.nodes.body, '#dock', this,  {cssselector:'#dock', delay:0.5, iscontained:false});
    this.nodes.dock.on('mouseleave', this.handleEvent, this, {cssselector:'#dock', delay:0.5, iscontained:false});

    this.nodes.body.on('click', this.handleEvent, this,  {cssselector:'body', delay:0});
    this.on('dock:itemschanged', this.resizeBlockSpace, this);
    this.on('dock:itemschanged', this.checkDockVisibility, this);
    this.on('dock:itemschanged', this.resetFirstItem, this);
    // Inform everyone the dock has been initialised
    this.fire('dock:initialised');
    return true;
};
/**
 * Get the panel docked blocks will be shown in and initialise it if we havn't already.
 */
M.core_dock.getPanel = function() {
    if (this.nodes.panel === null) {
        // Initialise the dockpanel .. should only happen once
        this.nodes.panel = (function(Y, parent){
            var dockpanel = Y.Node.create('<div id="dockeditempanel" class="dockitempanel_hidden"><div class="dockeditempanel_content"><div class="dockeditempanel_hd"></div><div class="dockeditempanel_bd"></div></div></div>');
            // Give the dockpanel event target properties and methods
            Y.augment(dockpanel, Y.EventTarget);
            // Publish events for the dock panel
            dockpanel.publish('dockpanel:beforeshow', {prefix:'dockpanel'});
            dockpanel.publish('dockpanel:shown', {prefix:'dockpanel'});
            dockpanel.publish('dockpanel:beforehide', {prefix:'dockpanel'});
            dockpanel.publish('dockpanel:hidden', {prefix:'dockpanel'});
            dockpanel.publish('dockpanel:visiblechange', {prefix:'dockpanel'});
            // Cache the content nodes
            dockpanel.contentNode = dockpanel.one('.dockeditempanel_content');
            dockpanel.contentHeader = dockpanel.contentNode.one('.dockeditempanel_hd');
            dockpanel.contentBody = dockpanel.contentNode.one('.dockeditempanel_bd');
            // Set the x position of the panel
            //dockpanel.setX(parent.get('offsetWidth'));
            dockpanel.visible = false;
            // Add a show event
            dockpanel.show = function() {
                this.fire('dockpanel:beforeshow');
                this.visible = true;
                this.removeClass('dockitempanel_hidden');
                this.fire('dockpanel:shown');
                this.fire('dockpanel:visiblechange');
            };
            // Add a hide event
            dockpanel.hide = function() {
                this.fire('dockpanel:beforehide');
                this.visible = false;
                this.addClass('dockitempanel_hidden');
                this.fire('dockpanel:hidden');
                this.fire('dockpanel:visiblechange');
            };
            // Add a method to set the header content
            dockpanel.setHeader = function(content) {
                this.contentHeader.setContent(content);
                if (arguments.length > 1) {
                    for (var i=1;i < arguments.length;i++) {
                        this.contentHeader.append(arguments[i]);
                    }
                }
            };
            // Add a method to set the body content
            dockpanel.setBody = function(content) {
                this.contentBody.setContent(content);
            };
            // Add a method to set the top of the panel position
            dockpanel.setTop = function(newtop) {
                if (Y.UA.ie > 0 && Y.UA.ie < 7) {
                    this.setY(newtop);
                } else {
                    this.setStyle('top', newtop.toString()+'px');
                }
                return;
            };
            /**
             * Increases the width of the panel to avoid horizontal scrolling
             * if possible.
             */
            dockpanel.correctWidth = function() {
                var bd = this.one('.dockeditempanel_bd');

                // Width of content
                var w = bd.get('clientWidth');
                // Scrollable width of content
                var s = bd.get('scrollWidth');
                // Width of content container with overflow
                var ow = this.get('offsetWidth');
                // The new width
                var nw = w;
                // The max width (80% of screen)
                var mw = Math.round(this.get('winWidth') * 0.8);

                // If the scrollable width is more than the visible width
                if (s > w) {
                    //   Content width
                    // + the difference
                    // + any rendering difference (borders, padding)
                    // + 10px to make it look nice.
                    nw = w + (s-w) + ((ow-w)*2) + 10;
                }

                // Make sure its not more then the maxwidth
                if (nw > mw) {
                    nw = mw;
                }

                // Set the new width if its more than the old width.
                if (nw > ow) {
                    this.setStyle('width', nw+'px');
                }
            }
            // Put the dockpanel in the body
            parent.append(dockpanel);
            // Return it
            return dockpanel;
        })(this.Y, this.nodes.dock);
        this.nodes.panel.on('panel:visiblechange', this.resize, this);
        this.Y.on('windowresize', this.resize, this);
        this.fire('dock:panelgenerated');
    }
    return this.nodes.panel;
};
/**
 * Handles a generic event within the dock
 * @param {Y.Event} e
 * @param {object} options Event configuration object
 */
M.core_dock.handleEvent = function(e, options) {
    var item = this.getActiveItem();
    if (options.cssselector == 'body') {
        if (!this.nodes.dock.contains(e.target)) {
            if (item) {
                item.hide();
            }
        }
    } else {
        var target;
        if (e.target.test(options.cssselector)) {
            target = e.target;
        } else {
            target = e.target.ancestor(options.cssselector);
        }
        if (!target) {
            return true;
        }
        if (this.preventevent !== null && e.type === this.preventevent) {
            return true;
        }
        if (options.preventevent) {
            this.preventevent = options.preventevent;
            if (options.preventdelay) {
                setTimeout(function(){M.core_dock.preventevent = null;}, options.preventdelay*1000);
            }
        }
        if (this.delayedevent && this.delayedevent.timeout) {
            clearTimeout(this.delayedevent.timeout);
            this.delayedevent.event.detach();
            this.delayedevent = null;
        }
        if (options.delay > 0) {
            return this.delayEvent(e, options, target);
        }
        var targetid = target.get('id');
        if (targetid.match(/^dock_item_(\d+)_title$/)) {
            item = this.items[targetid.replace(/^dock_item_(\d+)_title$/, '$1')];
            if (item.active) {
                item.hide();
            } else {
                item.show();
            }
        } else if (item) {
            item.hide();
        }
    }
    return true;
};
/**
 * This function delays an event and then fires it providing the cursor if either
 * within or outside of the original target (options.iscontained=true|false)
 * @param {Y.Event} event
 * @param {object} options
 * @param {Y.Node} target
 * @return bool
 */
M.core_dock.delayEvent = function(event, options, target) {
    var self = this;
    self.delayedevent = (function(){
        return {
            target : target,
            event : self.nodes.body.on('mousemove', function(e){
                self.delayedevent.target = e.target;
            }),
            timeout : null
        };
    })(self);
    self.delayedevent.timeout = setTimeout(function(){
        self.delayedevent.timeout = null;
        self.delayedevent.event.detach();
        if (options.iscontained == self.nodes.dock.contains(self.delayedevent.target)) {
            self.handleEvent(event, {cssselector:options.cssselector, delay:0, iscontained:options.iscontained});
        }
    }, options.delay*1000);
    return true;
};
/**
 * Corrects the orientation of the title, which for the default
 * dock just means making it vertical
 * The orientation is determined by M.str.langconfig.thisdirectionvertical:
 *    ver : Letters are stacked rather than rotated
 *    ttb : Title is rotated clockwise so the first letter is at the top
 *    btt : Title is rotated counterclockwise so the first letter is at the bottom.
 * @param {string} title
 */
M.core_dock.fixTitleOrientation = function(item, title, text) {
    var Y = this.Y;
    
    var title = Y.one(title);

    if(M.core_dock.cfg.orientation != 'vertical') {
        // If the dock isn't vertical don't adjust it!
        title.setContent(text);
        return title
    }

    if (Y.UA.ie > 0 && Y.UA.ie < 8) {
        // IE 6/7 can't rotate text so force ver
        M.str.langconfig.thisdirectionvertical = 'ver';
    }

    var clockwise = false;
    switch (M.str.langconfig.thisdirectionvertical) {
        case 'ver':
            // Stacked is easy
            return title.setContent(text.split('').join('<br />'));
        case 'ttb':
            clockwise = true;
            break;
        case 'btt':
            clockwise = false;
            break;
    }

    if (Y.UA.ie > 7) {
        // IE8 can flip the text via CSS but not handle SVG
        title.setContent(text);
        title.setAttribute('style', 'writing-mode: tb-rl; filter: flipV flipH;display:inline;');
        title.addClass('filterrotate');
        return title;
    }

    // Cool, we can use SVG!
    var test = Y.Node.create('<h2><span style="font-size:10px;">'+text+'</span></h2>');
    this.nodes.body.append(test);
    var height = test.one('span').get('offsetWidth')+4;
    var width = test.one('span').get('offsetHeight')*2;
    var qwidth = width/4;
    test.remove();

    // Create the text for the SVG
    var txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    txt.setAttribute('font-size','10px');
    if (clockwise) {
        txt.setAttribute('transform','rotate(90 '+(qwidth/2)+' '+qwidth+')');
    } else {
        txt.setAttribute('y', height);
        txt.setAttribute('transform','rotate(270 '+qwidth+' '+(height-qwidth)+')');
    }
    txt.appendChild(document.createTextNode(text));

    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('version', '1.1');
    svg.setAttribute('height', height);
    svg.setAttribute('width', width);
    svg.appendChild(txt);

    title.append(svg);

    item.on('dockeditem:drawcomplete', function(txt, title){
        txt.setAttribute('fill', Y.one(title).getStyle('color'));
    }, item, txt, title);

    return title;
};
/**
 * Resizes the space that contained blocks if there were no blocks left in
 * it. e.g. if all blocks have been moved to the dock
 * @param {Y.Node} node
 */
M.core_dock.resizeBlockSpace = function(node) {

    if (this.Y.all('.block.dock_on_load').size()>0) {
        // Do not resize during initial load
        return;
    }
    var blockregions = [];
    var populatedblockregions = 0;
    this.Y.all('.block-region').each(function(region){
        var hasblocks = (region.all('.block').size() > 0);
        if (hasblocks) {
            populatedblockregions++;
        }
        blockregions[region.get('id')] = {hasblocks: hasblocks, bodyclass: region.get('id').replace(/^region\-/, 'side-')+'-only'};
    });
    var bodynode = M.core_dock.nodes.body;
    var showregions = false;
    if (bodynode.hasClass('blocks-moving')) {
        // open up blocks during blocks positioning
        showregions = true;
    }

    var noblocksbodyclass = 'content-only';
    var i = null;
    if (populatedblockregions==0 && showregions==false) {
        bodynode.addClass(noblocksbodyclass);
        for (i in blockregions) {
            bodynode.removeClass(blockregions[i].bodyclass);
        }
    } else if (populatedblockregions==1 && showregions==false) {
        bodynode.removeClass(noblocksbodyclass);
        for (i in blockregions) {
            if (!blockregions[i].hasblocks) {
                bodynode.removeClass(blockregions[i].bodyclass);
            } else {
                bodynode.addClass(blockregions[i].bodyclass);
            }
        }
    } else {
        bodynode.removeClass(noblocksbodyclass);
        for (i in blockregions) {
            bodynode.removeClass(blockregions[i].bodyclass);
        }
    }
};
/**
 * Adds a dock item into the dock
 * @function
 * @param {M.core_dock.item} item
 */
M.core_dock.add = function(item) {
    item.id = this.totalcount;
    this.count++;
    this.totalcount++;
    this.items[item.id] = item;
    this.items[item.id].draw();
    this.fire('dock:itemadded', item);
    this.fire('dock:itemschanged', item);
};
/**
 * Appends a dock item to the dock
 * @param {YUI.Node} docknode
 */
M.core_dock.append = function(docknode) {
    this.nodes.container.append(docknode);
};
/**
 * Initialises a generic block object
 * @param {YUI} Y
 * @param {int} id
 */
M.core_dock.init_genericblock = function(Y, id) {
    if (!this.initialised) {
        this.init(Y);
    }
    new this.genericblock(id).initialise_block(Y, Y.one('#inst'+id));
};
/**
 * Removes the node at the given index and puts it back into conventional page sturcture
 * @function
 * @param {int} uid Unique identifier for the block
 * @return {boolean}
 */
M.core_dock.remove = function(uid) {
    if (!this.items[uid]) {
        return false;
    }
    this.items[uid].remove();
    delete this.items[uid];
    this.count--;
    this.fire('dock:itemremoved', uid);
    this.fire('dock:itemschanged', uid);
    return true;
};
/**
 * Ensures the the first item in the dock has the correct class
 */
M.core_dock.resetFirstItem = function() {
    this.nodes.dock.all('.'+this.css.dockeditem+'.firstdockitem').removeClass('firstdockeditem');
    if (this.nodes.dock.one('.'+this.css.dockeditem)) {
        this.nodes.dock.one('.'+this.css.dockeditem).addClass('firstdockitem');
    }
};
/**
 * Removes all nodes and puts them back into conventional page sturcture
 * @function
 * @return {boolean}
 */
M.core_dock.remove_all = function(e) {
    for (var i in this.items) {
        this.remove(i);
    }
    return true;
};
/**
 * Hides the active item
 */
M.core_dock.hideActive = function() {
    var item = this.getActiveItem();
    if (item) {
        item.hide();
    }
};
/**
 * Checks wether the dock should be shown or hidden
 */
M.core_dock.checkDockVisibility = function() {
    if (!this.count) {
        this.nodes.dock.addClass('nothingdocked');
        this.nodes.body.removeClass(this.css.body)
                       .removeClass(this.css.body+'_'+this.cfg.position+'_'+this.cfg.orientation);
        this.fire('dock:hidden');
    } else {
        this.fire('dock:beforeshow');
        this.nodes.dock.removeClass('nothingdocked');
        this.nodes.body.addClass(this.css.body)
                       .addClass(this.css.body+'_'+this.cfg.position+'_'+this.cfg.orientation);
        this.fire('dock:shown');
    }
};
/**
 * This smart little function allows developers to attach event listeners before
 * the dock has been augmented to allows event listeners.
 * Once the augmentation is complete this function will be replaced with the proper
 * on method for handling event listeners.
 * Finally applyBinds needs to be called in order to properly bind events.
 * @param {string} event
 * @param {function} callback
 */
M.core_dock.on = function(event, callback) {
    this.earlybinds.push({event:event,callback:callback});
};
/**
 * This function takes all early binds and attaches them as listeners properly
 * This should only be called once augmentation is complete.
 */
M.core_dock.applyBinds = function() {
    for (var i in this.earlybinds) {
        var bind = this.earlybinds[i];
        this.on(bind.event, bind.callback);
    }
    this.earlybinds = [];
};
/**
 * This function checks the size and position of the panel and moves/resizes if
 * required to keep it within the bounds of the window.
 */
M.core_dock.resize = function() {
    this.fire('dock:panelresizestart');
    var panel = this.getPanel();
    var item = this.getActiveItem();
    if (!panel.visible || !item) {
        return;
    }

    if (this.cfg.orientation=='vertical') {
        var buffer = this.cfg.buffer;
        var screenheight = parseInt(this.nodes.body.get('winHeight'))-(buffer*2);
        var docky = this.nodes.dock.getY();
        var titletop = item.nodes.docktitle.getY()-docky-buffer;
        var containery = this.nodes.container.getY();
        var containerheight = containery-docky+this.nodes.buttons.get('offsetHeight');
        var scrolltop = panel.contentBody.get('scrollTop');
        panel.contentBody.setStyle('height', 'auto');
        panel.removeClass('oversized_content');
        var panelheight = panel.get('offsetHeight');

        if (this.Y.UA.ie > 0 && this.Y.UA.ie < 7) {
            panel.setTop(item.nodes.docktitle.getY());
        } else if (panelheight > screenheight) {
            panel.setTop(buffer-containerheight);
            panel.contentBody.setStyle('height', (screenheight-panel.contentHeader.get('offsetHeight'))+'px');
            panel.addClass('oversized_content');
        } else if (panelheight > (screenheight-(titletop-buffer))) {
            var difference = panelheight - (screenheight-titletop);
            panel.setTop(titletop-containerheight-difference+buffer);
        } else {
            panel.setTop(titletop-containerheight+buffer);
        }

        if (scrolltop) {
            panel.contentBody.set('scrollTop', scrolltop);
        }
    }

    if (this.cfg.position=='right') {
        panel.setStyle('left', -panel.get('offsetWidth')+'px');

    } else if (this.cfg.position=='top') {
        var dockx = this.nodes.dock.getX();
        var titleleft = item.nodes.docktitle.getX()-dockx;
        panel.setStyle('left', titleleft+'px');
    }

    this.fire('dock:resizepanelcomplete');
    return;
};
/**
 * Returns the currently active dock item or false
 */
M.core_dock.getActiveItem = function() {
    for (var i in this.items) {
        if (this.items[i].active) {
            return this.items[i];
        }
    }
    return false;
};
/**
 * This class represents a generic block
 * @class M.core_dock.genericblock
 * @constructor
 */
M.core_dock.genericblock = function(id) {
    // Nothing to actually do here but it needs a constructor!
    if (id) {
        this.id = id;
    }
};
M.core_dock.genericblock.prototype = {
    Y : null,                   // A YUI instance to use with the block
    id : null,                  // The block instance id
    cachedcontentnode : null,   // The cached content node for the actual block
    blockspacewidth : null,     // The width of the block's original container
    skipsetposition : false,    // If true the user preference isn't updated
    isdocked : false,           // True if it is docked
    /**
     * This function should be called within the block's constructor and is used to
     * set up the initial controls for swtiching block position as well as an initial
     * moves that may be required.
     *
     * @param {YUI} Y
     * @param {YUI.Node} node The node that contains all of the block's content
     * @return {M.core_dock.genericblock}
     */
    initialise_block : function(Y, node) {
        M.core_dock.init(Y);
        
        this.Y = Y;
        if (!node) {
            return false;
        }

        var commands = node.one('.header .title .commands');
        if (!commands) {
            commands = this.Y.Node.create('<div class="commands"></div>');
            if (node.one('.header .title')) {
                node.one('.header .title').append(commands);
            }
        }

        // Must set the image src seperatly of we get an error with XML strict headers
        var moveto = Y.Node.create('<input type="image" class="moveto customcommand requiresjs" alt="'+M.str.block.addtodock+'" title="'+M.str.block.addtodock+'" />');
        moveto.setAttribute('src', M.util.image_url('t/block_to_dock', 'moodle'));
        moveto.on('movetodock|click', this.move_to_dock, this, commands);

        var blockaction = node.one('.block_action');
        if (blockaction) {
            blockaction.prepend(moveto);
        } else {
            commands.append(moveto);
        }

        // Move the block straight to the dock if required
        if (node.hasClass('dock_on_load')) {
            node.removeClass('dock_on_load');
            this.skipsetposition = true;
            this.move_to_dock(null, commands);
        }
        return this;
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

        var Y = this.Y;
        var dock = M.core_dock;

        var node = Y.one('#inst'+this.id);
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

        node.replace(Y.Node.getDOMNode(Y.Node.create('<div id="content_placeholder_'+this.id+'" class="block_dock_placeholder"></div>')));
        M.core_dock.holdingarea.append(node);
        node = null;

        var blocktitle = Y.Node.getDOMNode(this.cachedcontentnode.one('.title h2')).cloneNode(true);

        var blockcommands = this.cachedcontentnode.one('.title .commands');
        if (!blockcommands) {
            blockcommands = Y.Node.create('<div class="commands"></div>');
            this.cachedcontentnode.one('.title').append(blockcommands);
        }

        // Must set the image src seperatly of we get an error with XML strict headers
        var movetoimg = Y.Node.create('<img alt="'+M.str.block.undockitem+'" title="'+M.str.block.undockitem+'" />');
        movetoimg.setAttribute('src', M.util.image_url('t/dock_to_block', 'moodle'));
        var moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>').append(movetoimg);
        if (location.href.match(/\?/)) {
            moveto.set('href', location.href+'&dock='+this.id);
        } else {
            moveto.set('href', location.href+'?dock='+this.id);
        }
        blockcommands.append(moveto);

        // Create a new dock item for the block
        var dockitem = new dock.item(Y, this.id, blocktitle, blockcontent, blockcommands, blockclass);
        // Wire the draw events to register remove events
        dockitem.on('dockeditem:drawcomplete', function(e){
            // check the contents block [editing=off]
            this.contents.all('.moveto').on('returntoblock|click', function(e){
                e.halt();
                dock.remove(this.id);
            }, this);
            // check the commands block [editing=on]
            this.commands.all('.moveto').on('returntoblock|click', function(e){
                e.halt();
                dock.remove(this.id);
            }, this);
            // Add a close icon
            // Must set the image src seperatly of we get an error with XML strict headers
            var closeicon = Y.Node.create('<span class="hidepanelicon" tabindex="0"><img alt="" style="width:11px;height:11px;cursor:pointer;" /></span>');
            closeicon.one('img').setAttribute('src', M.util.image_url('t/dockclose', 'moodle'));
            closeicon.on('forceclose|click', this.hide, this);
            closeicon.on('dock:actionkey',this.hide, this, {actions:{enter:true,toggle:true}});
            this.commands.append(closeicon);
        }, dockitem);
        // Register an event so that when it is removed we can put it back as a block
        dockitem.on('dockeditem:itemremoved', this.return_to_block, this, dockitem);
        dock.add(dockitem);
        
        if (!this.skipsetposition) {
            // save the users preference
            M.util.set_user_preference('docked_block_instance_'+this.id, 1);
        } else {
            this.skipsetposition = false;
        }

        this.isdocked = true;
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

        var commands = this.cachedcontentnode.one('.title .commands');
        if (commands) {
            commands.all('.hidepanelicon').remove();
            commands.all('.moveto').remove();
            commands.remove();
        }
        this.cachedcontentnode.one('.title').append(commands);
        this.cachedcontentnode = null;
        M.util.set_user_preference('docked_block_instance_'+this.id, 0);
        this.isdocked = false;
        return true;
    }
};

/**
 * This class represents an item in the dock
 * @class M.core_dock.item
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
    this.publish('dockeditem:drawstart', {prefix:'dockeditem'});
    this.publish('dockeditem:drawcomplete', {prefix:'dockeditem'});
    this.publish('dockeditem:showstart', {prefix:'dockeditem'});
    this.publish('dockeditem:showcomplete', {prefix:'dockeditem'});
    this.publish('dockeditem:hidestart', {prefix:'dockeditem'});
    this.publish('dockeditem:hidecomplete', {prefix:'dockeditem'});
    this.publish('dockeditem:itemremoved', {prefix:'dockeditem'});
    if (uid && this.id==null) {
        this.id = uid;
    }
    if (title && this.title==null) {
        this.titlestring = title.cloneNode(true);
        this.title = document.createElement(title.nodeName);
        M.core_dock.fixTitleOrientation(this, this.title, this.titlestring.firstChild.nodeValue);
    }
    if (contents && this.contents==null) {
        this.contents = contents;
    }
    if (commands && this.commands==null) {
        this.commands = commands;
    }
    if (blockclass && this.blockclass==null) {
        this.blockclass = blockclass;
    }
    this.nodes = (function(){
        return {docktitle : null, dockitem : null, container: null};
    })();
};
/**
 *
 */
M.core_dock.item.prototype = {
    Y : null,               // The YUI instance to use with this dock item
    id : null,              // The unique id for the item
    name : null,            // The name of the item
    title : null,           // The title of the item
    titlestring : null,     // The title as a plain string
    contents : null,        // The content of the item
    commands : null,        // The commands for the item
    active : false,         // True if the item is being shown
    blockclass : null,      // The class of the block this item relates to
    nodes : null,
    /**
     * This function draws the item on the dock
     */
    draw : function() {
        this.fire('dockeditem:drawstart');

        var Y = this.Y;
        var css = M.core_dock.css;

        this.nodes.docktitle = Y.Node.create('<div id="dock_item_'+this.id+'_title" class="'+css.dockedtitle+'"></div>');
        this.nodes.docktitle.append(this.title);
        this.nodes.dockitem = Y.Node.create('<div id="dock_item_'+this.id+'" class="'+css.dockeditem+'" tabindex="0"></div>');
        this.nodes.dockitem.on('dock:actionkey', this.toggle, this);
        if (M.core_dock.count === 1) {
            this.nodes.dockitem.addClass('firstdockitem');
        }
        this.nodes.dockitem.append(this.nodes.docktitle);
        M.core_dock.append(this.nodes.dockitem);
        this.fire('dockeditem:drawcomplete');
        return true;
    },
    /**
     * This function toggles makes the item active and shows it
     */
    show : function() {
        M.core_dock.hideActive();
        var Y = this.Y;
        var css = M.core_dock.css;
        var panel = M.core_dock.getPanel();
        this.fire('dockeditem:showstart');
        panel.setHeader(this.titlestring, this.commands);
        panel.setBody(Y.Node.create('<div class="'+this.blockclass+' block_docked"></div>').append(this.contents));
        panel.show();
        panel.correctWidth();

        this.active = true;
        // Add active item class first up
        this.nodes.docktitle.addClass(css.activeitem);
        this.fire('dockeditem:showcomplete');
        M.core_dock.resize();
        return true;
    },
    /**
     * This function hides the item and makes it inactive
     */
    hide : function() {
        var css = M.core_dock.css;
        this.fire('dockeditem:hidestart');
        // No longer active
        this.active = false;
        // Remove the active class
        this.nodes.docktitle.removeClass(css.activeitem);
        // Hide the panel
        M.core_dock.getPanel().hide();
        this.fire('dockeditem:hidecomplete');
    },
    /**
     * A toggle between calling show and hide functions based on css.activeitem
     * Applies rules to key press events (dock:actionkey)
     * @param {Event} e
     */
    toggle : function(e) {
        var css = M.core_dock.css;
        if (this.nodes.docktitle.hasClass(css.activeitem) && !(e.type == 'dock:actionkey' && e.action=='expand')) {
            this.hide();
        } else if (!this.nodes.docktitle.hasClass(css.activeitem) && !(e.type == 'dock:actionkey' && e.action=='collapse'))  {
            this.show();
        }
    },
    /**
     * This function removes the node and destroys it's bits
     * @param {Event} e
     */
    remove : function () {
        this.hide();
        this.nodes.dockitem.remove();
        this.fire('dockeditem:itemremoved');
    }
};
