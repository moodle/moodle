YUI.add('moodle-core-dock', function (Y, NAME) {

/**
 * Dock JS.
 *
 * This file contains the DOCK object and all dock related global namespace methods and properties.
 *
 * @module moodle-core-dock
 */


var LOGNS = 'moodle-core-dock',
    BODY = Y.one(Y.config.doc.body),
    CSS = {
        dock: 'dock',                    // CSS Class applied to the dock box
        dockspacer: 'dockspacer',        // CSS class applied to the dockspacer
        controls: 'controls',            // CSS class applied to the controls box
        body: 'has_dock',                // CSS class added to the body when there is a dock
        buttonscontainer: 'buttons_container',
        dockeditem: 'dockeditem',        // CSS class added to each item in the dock
        dockeditemcontainer: 'dockeditem_container',
        dockedtitle: 'dockedtitle',      // CSS class added to the item's title in each dock
        activeitem: 'activeitem',        // CSS class added to the active item
        dockonload: 'dock_on_load'
    },
    SELECTOR = {
        dockableblock: '.block[data-instanceid][data-dockable]',
        blockmoveto: '.block[data-instanceid][data-dockable] .moveto',
        panelmoveto: '#dockeditempanel .commands a.moveto',
        dockonload: '.block.'+CSS.dockonload,
        blockregion: '[data-blockregion]'
    },
    DOCK,
    DOCKPANEL,
    TABHEIGHTMANAGER,
    BLOCK,
    DOCKEDITEM;

M.core = M.core || {};
M.core.dock = M.core.dock || {};

/**
 * The dock - once initialised.
 *
 * @private
 * @property _dock
 * @type DOCK
 */
M.core.dock._dock = null;

/**
 * An associative array of dockable blocks.
 * @property _dockableblocks
 * @type {Array} An array of BLOCK objects organised by instanceid.
 * @private
 */
M.core.dock._dockableblocks = {};

/**
 * Initialises the dock.
 * This method registers dockable blocks, and creates delegations to dock them.
 * @static
 * @method init
 */
M.core.dock.init = function() {
    Y.all(SELECTOR.dockableblock).each(M.core.dock.registerDockableBlock);
    BODY.delegate('click', M.core.dock.dockBlock, SELECTOR.blockmoveto);
    BODY.delegate('key', M.core.dock.dockBlock, SELECTOR.blockmoveto, 'enter');
};

/**
 * Returns an instance of the dock.
 * Initialises one if one hasn't already being initialised.
 *
 * @static
 * @method get
 * @return DOCK
 */
M.core.dock.get = function() {
    if (this._dock === null) {
        this._dock = new DOCK();
    }
    return this._dock;
};

/**
 * Registers a dockable block with the dock.
 *
 * @static
 * @method registerDockableBlock
 * @param {int} id The block instance ID.
 * @return void
 */
M.core.dock.registerDockableBlock = function(id) {
    if (typeof id === 'object' && typeof id.getData === 'function') {
        id = id.getData('instanceid');
    }
    M.core.dock._dockableblocks[id] = new BLOCK({id : id});
};

/**
 * Docks a block given either its instanceid, its node, or an event fired from within the block.
 * @static
 * @method dockBlockByInstanceID
 * @param id
 * @return void
 */
M.core.dock.dockBlock = function(id) {
    if (typeof id === 'object' && id.target !== 'undefined') {
        id = id.target;
    }
    if (typeof id === "object") {
        if (!id.test(SELECTOR.dockableblock)) {
            id = id.ancestor(SELECTOR.dockableblock);
        }
        if (typeof id === 'object' && typeof id.getData === 'function' && !id.ancestor('.'+CSS.dock)) {
            id = id.getData('instanceid');
        } else {
            Y.log('Invalid instanceid given to dockBlockByInstanceID', 'warn', LOGNS);
            return;
        }
    }
    var block = M.core.dock._dockableblocks[id];
    if (block) {
        block.moveToDock();
    }
};

/**
 * Fixes the title orientation. Rotating it if required.
 *
 * @static
 * @method fixTitleOrientation
 * @param {Node} title The title node we are looking at.
 * @param {String} text The string to use as the title.
 * @return {Node} The title node to use.
 */
M.core.dock.fixTitleOrientation = function(title, text) {
    var dock = M.core.dock.get(),
        fontsize = '11px',
        transform = 'rotate(270deg)',
        test,
        width,
        height,
        container,
        verticaldirection = M.util.get_string('thisdirectionvertical', 'langconfig');
    title = Y.one(title);

    if (dock.get('orientation') !== 'vertical') {
        // If the dock isn't vertical don't adjust it!
        title.set('innerHTML', text);
        return title;
    }

    if (Y.UA.ie > 0 && Y.UA.ie < 8) {
        // IE 6/7 can't rotate text so force ver
        verticaldirection = 'ver';
    }

    switch (verticaldirection) {
        case 'ver':
            // Stacked is easy
            return title.set('innerHTML', text.split('').join('<br />'));
        case 'ttb':
            transform = 'rotate(90deg)';
            break;
        case 'btt':
            // Nothing to do here. transform default is good.
            break;
    }

    if (Y.UA.ie === 8) {
        // IE8 can flip the text via CSS but not handle transform. IE9+ can handle the CSS3 transform attribute.
        title.set('innerHTML', text);
        title.setAttribute('style', 'writing-mode: tb-rl; filter: flipV flipH;display:inline;');
        title.addClass('filterrotate');
        return title;
    }

    // We need to fix a font-size - sorry theme designers.
    test = Y.Node.create('<h2 class="transform-test-heading"><span class="transform-test-node" style="font-size:' +
            fontsize + ';">' + text + '</span></h2>');
    BODY.insert(test, 0);
    width = test.one('span').get('offsetWidth') * 1.2;
    height = test.one('span').get('offsetHeight');
    test.remove();

    title.set('innerHTML', text);
    title.addClass('css3transform');

    // Move the title into position
    title.setStyles({
        'position' : 'relative',
        'fontSize' : fontsize,
        'width' : width,
        'top' : (width - height)/2
    });

    // Positioning is different when in RTL mode.
    if (window.right_to_left()) {
        title.setStyle('left', width/2 - height);
    } else {
        title.setStyle('right', width/2 - height);
    }

    // Rotate the text
    title.setStyles({
        'transform' : transform,
        '-ms-transform' : transform,
        '-moz-transform' : transform,
        '-webkit-transform' : transform,
        '-o-transform' : transform
    });

    container = Y.Node.create('<div></div>');
    container.append(title);
    container.setStyles({
        height : width + (width / 4),
        position : 'relative'
    });
    return container;
};

/**
 * Informs the dock that the content of the block has changed.
 * This should be called by the blocks JS code if its content has been updated dynamically.
 * This method ensure the dock resizes if need be.
 *
 * @static
 * @method notifyBlockChange
 * @param {Number} instanceid
 * @return void
 */
M.core.dock.notifyBlockChange = function(instanceid) {
    if (this._dock !== null) {
        var dock = M.core.dock.get(),
            activeitem = dock.getActiveItem();
        if (activeitem && activeitem.get('blockinstanceid') === parseInt(instanceid, 10)) {
            dock.resizePanelIfRequired();
        }
    }
};

/**
 * The Dock.
 *
 * @namespace M.core.dock
 * @class Dock
 * @constructor
 * @extends Base
 * @uses EventTarget
 */
DOCK = function() {
    DOCK.superclass.constructor.apply(this, arguments);
};
DOCK.prototype = {
    /**
     * Tab height manager used to ensure tabs are always visible.
     * @protected
     * @property tabheightmanager
     * @type TABHEIGHTMANAGER
     */
    tabheightmanager : null,
    /**
     * Will be an eventtype if there is an eventype to prevent.
     * @protected
     * @property preventevent
     * @type String
     */
    preventevent : null,
    /**
     * Will be an object if there is a delayed event in effect.
     * @protected
     * @property delayedevent
     * @type {Object}
     */
    delayedevent : null,
    /**
     * An array of currently docked items.
     * @protected
     * @property dockeditems
     * @type Array
     */
    dockeditems : [],
    /**
     * Set to true once the dock has been drawn.
     * @protected
     * @property dockdrawn
     * @type Boolean
     */
    dockdrawn : false,
    /**
     * The number of blocks that are currently docked.
     * @protected
     * @property count
     * @type Number
     */
    count : 0,
    /**
     * The total number of blocks that have been docked.
     * @protected
     * @property totalcount
     * @type Number
     */
    totalcount : 0,
    /**
     * A hidden node used as a holding area for DOM objects used by blocks that have been docked.
     * @protected
     * @property holdingareanode
     * @type Node
     */
    holdingareanode : null,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        Y.log('Dock initialising', 'debug', LOGNS);

        // Publish the events the dock has
        /**
         * Fired when the dock first starts initialising.
         * @event dock:starting
         */
        this.publish('dock:starting', {prefix: 'dock',broadcast:  2,emitFacade: true, fireOnce:true});
        /**
         * Fired after the dock is initialised for the first time.
         * @event dock:initialised
         */
        this.publish('dock:initialised', {prefix: 'dock',broadcast:  2,emitFacade: true, fireOnce:true});
        /**
         * Fired before the dock structure and content is first created.
         * @event dock:beforedraw
         */
        this.publish('dock:beforedraw', {prefix:'dock', fireOnce:true});
        /**
         * Fired before the dock is changed from hidden to visible.
         * @event dock:beforeshow
         */
        this.publish('dock:beforeshow', {prefix:'dock'});
        /**
         * Fires after the dock has been changed from hidden to visible.
         * @event dock:shown
         */
        this.publish('dock:shown', {prefix:'dock', broadcast: 2});
        /**
         * Fired after the dock has been changed from visible to hidden.
         * @event dock:hidden
         */
        this.publish('dock:hidden', {prefix:'dock', broadcast: 2});
        /**
         * Fires when an item is added to the dock.
         * @event dock:itemadded
         */
        this.publish('dock:itemadded', {prefix:'dock'});
        /**
         * Fires when an item is removed from the dock.
         * @event dock:itemremoved
         */
        this.publish('dock:itemremoved', {prefix:'dock'});
        /**
         * Fires when a block is added or removed from the dock.
         * This happens after the itemadded and itemremoved events have been called.
         * @event dock:itemschanged
         */
        this.publish('dock:itemschanged', {prefix:'dock', broadcast: 2});
        /**
         * Fires once when the docks panel is first initialised.
         * @event dock:panelgenerated
         */
        this.publish('dock:panelgenerated', {prefix:'dock', fireOnce:true});
        /**
         * Fires when the dock panel is about to be resized.
         * @event dock:panelresizestart
         */
        this.publish('dock:panelresizestart', {prefix:'dock'});
        /**
         * Fires after the dock panel has been resized.
         * @event dock:resizepanelcomplete
         */
        this.publish('dock:resizepanelcomplete', {prefix:'dock'});

        // Apply theme customisations here before we do any real work.
        this._applyThemeCustomisation();
        // Inform everyone we are now about to initialise.
        this.fire('dock:starting');
        this._ensureDockDrawn();
        // Inform everyone the dock has been initialised
        this.fire('dock:initialised');
    },
    /**
     * Ensures that the dock has been drawn.
     * @private
     * @method _ensureDockDrawn
     * @return {Boolean}
     */
    _ensureDockDrawn : function() {
        if (this.dockdrawn === true) {
            return true;
        }
        var dock = this._initialiseDockNode(),
            clickargs = {
                cssselector:'.'+CSS.dockedtitle,
                delay:0
            },
            mouseenterargs = {
                cssselector:'.'+CSS.dockedtitle,
                delay:0.5,
                iscontained:true,
                preventevent:'click',
                preventdelay:3
            };
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            // Adjust for IE 6 (can't handle fixed pos)
            dock.setStyle('height', dock.get('winHeight')+'px');
        }

        this.fire('dock:beforedraw');

        this._initialiseDockControls();

        this.tabheightmanager = new TABHEIGHTMANAGER({dock:this});

        // Attach the required event listeners
        // We use delegate here as that way a handful of events are created for the dock
        // and all items rather than the same number for the dock AND every item individually
        Y.delegate('click', this.handleEvent, this.get('dockNode'), '.'+CSS.dockedtitle, this, clickargs);
        Y.delegate('mouseenter', this.handleEvent, this.get('dockNode'), '.'+CSS.dockedtitle, this, mouseenterargs);
        this.get('dockNode').on('mouseleave', this.handleEvent, this, {cssselector:'#dock', delay:0.5, iscontained:false});

        Y.delegate('click', this.handleReturnToBlock, this.get('dockNode'), SELECTOR.panelmoveto, this);
        Y.delegate('dock:actionkey', this.handleDockedItemEvent, this.get('dockNode'), '.'+CSS.dockeditem, this);

        BODY.on('click', this.handleEvent, this,  {cssselector:'body', delay:0});
        this.on('dock:itemschanged', this.resizeBlockSpace, this);
        this.on('dock:itemschanged', this.checkDockVisibility, this);
        this.on('dock:itemschanged', this.resetFirstItem, this);
        this.dockdrawn = true;
        return true;
    },
    /**
     * Handles an actionkey event on the dock.
     * @param {EventFacade} e
     * @method handleDockedItemEvent
     * @return {Boolean}
     */
    handleDockedItemEvent : function(e) {
        if (e.type !== 'dock:actionkey') {
            return false;
        }
        var target = e.target,
            dockeditem = '.'+CSS.dockeditem;
        if (!target.test(dockeditem)) {
            target = target.ancestor(dockeditem);
        }
        if (!target) {
            return false;
        }
        e.halt();
        this.dockeditems[target.getAttribute('rel')].toggle(e.action);
    },
    /**
     * Call the theme customisation method "customise_dock_for_theme" if it exists.
     * @private
     * @method _applyThemeCustomisation
     */
    _applyThemeCustomisation : function() {
        // Check if there is a customisation function
        if (typeof(customise_dock_for_theme) === 'function') {
            // First up pre the legacy object.
            M.core_dock = this;
            M.core_dock.cfg = {
                buffer : null,
                orientation : null,
                position : null,
                spacebeforefirstitem : null,
                removeallicon : null
            };
            M.core_dock.css = {
                dock : null,
                dockspacer : null,
                controls : null,
                body : null,
                buttonscontainer : null,
                dockeditem : null,
                dockeditemcontainer : null,
                dockedtitle : null,
                activeitem : null
            };
            try {
                // Run the customisation function
                window.customise_dock_for_theme(this);
            } catch (exception) {
                // Do nothing at the moment.
                Y.log('Exception while attempting to apply theme customisations.', 'error', LOGNS);
            }
            // Now to work out what they did.
            var key, value,
                warned = false,
                cfgmap = {
                    buffer : 'bufferPanel',
                    orientation : 'orientation',
                    position : 'position',
                    spacebeforefirstitem : 'bufferBeforeFirstItem',
                    removeallicon : 'undockAllIconUrl'
                };
            // Check for and apply any legacy configuration.
            for (key in M.core_dock.cfg) {
                if (Y.Lang.isString(key) && cfgmap[key]) {
                    value = M.core_dock.cfg[key];
                    if (value === null) {
                        continue;
                    }
                    if (!warned) {
                        Y.log('Warning: customise_dock_for_theme has changed. Please update your code.', 'warn', LOGNS);
                        warned = true;
                    }
                    // Damn, the've set something.
                    Y.log('Note for customise_dock_for_theme code: M.core_dock.cfg.' + key +
                            ' is now dock.set(\'' + key + '\', value)',
                            'debug', LOGNS);
                    this.set(cfgmap[key], value);
                }
            }
            // Check for and apply any legacy CSS changes..
            for (key in M.core_dock.css) {
                if (Y.Lang.isString(key)) {
                    value = M.core_dock.css[key];
                    if (value === null) {
                        continue;
                    }
                    if (!warned) {
                        Y.log('Warning: customise_dock_for_theme has changed. Please update your code.', 'warn', LOGNS);
                        warned = true;
                    }
                    // Damn, they've set something.
                    Y.log('Note for customise_dock_for_theme code: M.core_dock.css.' + key + ' is now CSS.' + key + ' = value',
                            'debug', LOGNS);
                    CSS[key] = value;
                }
            }
        }
    },
    /**
     * Initialises the dock node, creating it and its content if required.
     *
     * @private
     * @method _initialiseDockNode
     * @return {Node} The dockNode
     */
    _initialiseDockNode : function() {
        var dock = this.get('dockNode'),
            positionorientationclass = CSS.dock+'_'+this.get('position')+'_'+this.get('orientation'),
            holdingarea = Y.Node.create('<div></div>').setStyles({display:'none'}),
            buttons = this.get('buttonsNode'),
            container = this.get('itemContainerNode');

        if (!dock) {
            dock = Y.one('#'+CSS.dock);
        }
        if (!dock) {
            dock = Y.Node.create('<div id="'+CSS.dock+'"></div>');
            BODY.append(dock);
        }
        dock.setAttribute('role', 'menubar').addClass(positionorientationclass);
        if (Y.all(SELECTOR.dockonload).size() === 0) {
            // Nothing on the dock... hide it using CSS
            dock.addClass('nothingdocked');
        } else {
            positionorientationclass = CSS.body+'_'+this.get('position')+'_'+this.get('orientation');
            BODY.addClass(CSS.body).addClass();
        }

        if (!buttons) {
            buttons = dock.one('.'+CSS.buttonscontainer);
        }
        if (!buttons) {
            buttons = Y.Node.create('<div class="'+CSS.buttonscontainer+'"></div>');
            dock.append(buttons);
        }

        if (!container) {
            container = dock.one('.'+CSS.dockeditemcontainer);
        }
        if (!container) {
            container = Y.Node.create('<div class="'+CSS.dockeditemcontainer+'"></div>');
            buttons.append(container);
        }

        BODY.append(holdingarea);
        this.holdingareanode = holdingarea;

        this.set('dockNode', dock);
        this.set('buttonsNode', buttons);
        this.set('itemContainerNode', container);

        return dock;
    },
    /**
     * Initialises the dock controls.
     *
     * @private
     * @method _initialiseDockControls
     */
    _initialiseDockControls : function() {
        // Add a removeall button
        // Must set the image src seperatly of we get an error with XML strict headers

        var removeall = Y.Node.create('<img alt="'+M.util.get_string('undockall', 'block')+'" tabindex="0" />');
        removeall.setAttribute('src',this.get('undockAllIconUrl'));
        removeall.on('removeall|click', this.removeAll, this);
        removeall.on('dock:actionkey', this.removeAll, this, {actions:{enter:true}});
        this.get('buttonsNode').append(Y.Node.create('<div class="'+CSS.controls+'"></div>').append(removeall));
    },
    /**
     * Returns the dock panel. Initialising it if it hasn't already been initialised.
     * @method getPanel
     * @return {DOCKPANEL}
     */
    getPanel : function() {
        var panel = this.get('panel');
        if (!panel) {
            panel = new DOCKPANEL({dock:this});
            panel.on('panel:visiblechange', this.resize, this);
            Y.on('windowresize', this.resize, this);
            // Initialise the dockpanel .. should only happen once
            this.set('panel', panel);
            this.fire('dock:panelgenerated');
        }
        return panel;
    },
    /**
     * Resizes the dock panel if required.
     * @method resizePanelIfRequired
     */
    resizePanelIfRequired : function() {
        this.resize();
        var panel = this.get('panel');
        if (panel) {
            panel.correctWidth();
        }
    },
    /**
     * Handles a dock event sending it to the right place.
     *
     * @method handleEvent
     * @param {EventFacade} e
     * @param {Object} options
     * @return {Boolean}
     */
    handleEvent : function(e, options) {
        var item = this.getActiveItem(),
            target,
            targetid,
            regex = /^dock_item_(\d+)_title$/,
            self = this;
        if (options.cssselector === 'body') {
            if (!this.get('dockNode').contains(e.target)) {
                if (item) {
                    item.hide();
                }
            }
        } else {
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
                    setTimeout(function(){
                        self.preventevent = null;
                    }, options.preventdelay * 1000);
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
            targetid = target.get('id');
            if (targetid.match(regex)) {
                item = this.dockeditems[targetid.replace(regex, '$1')];
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
    },
    /**
     * Delays an event.
     *
     * @method delayEvent
     * @param {EventFacade} event
     * @param {Object} options
     * @param {Node} target
     * @return {Boolean}
     */
    delayEvent : function(event, options, target) {
        var self = this;
        self.delayedevent = (function(){
            return {
                target : target,
                event : BODY.on('mousemove', function(e){
                    self.delayedevent.target = e.target;
                }),
                timeout : null
            };
        })(self);
        self.delayedevent.timeout = setTimeout(function(){
            self.delayedevent.timeout = null;
            self.delayedevent.event.detach();
            if (options.iscontained === self.get('dockNode').contains(self.delayedevent.target)) {
                self.handleEvent(event, {cssselector:options.cssselector, delay:0, iscontained:options.iscontained});
            }
        }, options.delay*1000);
        return true;
    },
    /**
     * Resizes block spaces.
     * @method resizeBlockSpace
     */
    resizeBlockSpace : function() {
        if (Y.all(SELECTOR.dockonload).size() > 0) {
            // Do not resize during initial load
            return;
        }

        var populatedRegionCount = 0,
            populatedBlockRegions = [],
            unpopulatedBlockRegions = [],
            isMoving = false,
            populatedLegacyRegions = [],
            containsLegacyRegions = false,
            classesToAdd = [],
            classesToRemove = [];

        // First look for understood regions.
        Y.all(SELECTOR.blockregion).each(function(region){
            var regionname = region.getData('blockregion');
            if (region.all('.block').size() > 0) {
                populatedBlockRegions.push(regionname);
                populatedRegionCount++;
            } else if (region.all('.block_dock_placeholder').size() > 0) {
                unpopulatedBlockRegions.push(regionname);
            }
        });

        // Next check for legacy regions.
        Y.all('.block-region').each(function(region){
            if (region.test(SELECTOR.blockregion)) {
                // This is a new region, we've already processed it.
                return;
            }

            // Sigh - there are legacy regions.
            containsLegacyRegions = true;

            var regionname = region.get('id').replace(/^region\-/, 'side-'),
                hasblocks = (region.all('.block').size() > 0);

            if (hasblocks) {
                populatedLegacyRegions.push(regionname);
                populatedRegionCount++;
            } else {
                // This legacy region has no blocks so cannot have the -only body tag.
                classesToRemove.push(
                        regionname + '-only'
                    );
            }
        });

        if (BODY.hasClass('blocks-moving')) {
            // When we're moving blocks, we do not want to collapse.
            isMoving = true;
        }

        Y.each(unpopulatedBlockRegions, function(regionname) {
            classesToAdd.push(
                    // This block region is empty.
                    'empty-region-' + regionname,

                    // Which has the same effect as being docked.
                    'docked-region-' + regionname
                );
            classesToRemove.push(
                    // It is no-longer used.
                    'used-region-' + regionname,

                    // It cannot be the only region on screen if it is empty.
                    regionname + '-only'
                );
        }, this);

        Y.each(populatedBlockRegions, function(regionname) {
            classesToAdd.push(
                    // This block region is in use.
                    'used-region-' + regionname
                );
            classesToRemove.push(
                    // It is not empty.
                    'empty-region-' + regionname,

                    // Is it not docked.
                    'docked-region-' + regionname
                );

            if (populatedRegionCount === 1 && isMoving === false) {
                // There was only one populated region, and we are not moving blocks.
                classesToAdd.push(regionname + '-only');
            } else {
                // There were multiple block regions visible - remove any 'only' classes.
                classesToRemove.push(regionname + '-only');
            }
        }, this);

        if (containsLegacyRegions) {
            // Handle the classing for legacy blocks. These have slightly different class names for the body.
            if (isMoving || populatedRegionCount !== 1) {
                Y.each(populatedLegacyRegions, function(regionname) {
                    classesToRemove.push(regionname + '-only');
                });
            } else {
                Y.each(populatedLegacyRegions, function(regionname) {
                    classesToAdd.push(regionname + '-only');
                });
            }
        }

        if (!BODY.hasClass('has-region-content')) {
            // This page does not have a content region, therefore content-only is implied when all block regions are docked.
            if (populatedRegionCount === 0 && isMoving === false) {
                // If all blocks are docked, ensure that the content-only class is added anyway.
                classesToAdd.push('content-only');
            } else {
                // Otherwise remove it.
                classesToRemove.push('content-only');
            }
        }

        // Modify the body clases.
        Y.each(classesToRemove, function(className) {
            BODY.removeClass(className);
        });
        Y.each(classesToAdd, function(className) {
            BODY.addClass(className);
        });
    },
    /**
     * Adds an item to the dock.
     * @method add
     * @param {DOCKEDITEM} item
     */
    add : function(item) {
        // Set the dockitem id to the total count and then increment it.
        item.set('id', this.totalcount);
        Y.log('Adding block '+item._getLogDescription()+' to the dock.', 'debug', LOGNS);
        this.count++;
        this.totalcount++;
        this.dockeditems[item.get('id')] = item;
        this.dockeditems[item.get('id')].draw();
        this.fire('dock:itemadded', item);
        this.fire('dock:itemschanged', item);
    },
    /**
     * Appends an item to the dock (putting it in the item container.
     * @method append
     * @param {Node} docknode
     */
    append : function(docknode) {
        this.get('itemContainerNode').append(docknode);
    },
    /**
     * Handles events that require a docked block to be returned to the page./
     * @method handleReturnToBlock
     * @param {EventFacade} e
     */
    handleReturnToBlock : function(e) {
        e.halt();
        this.remove(this.getActiveItem().get('id'));
    },
    /**
     * Removes a docked item from the dock.
     * @method remove
     * @param {Number} id The docked item id.
     * @return {Boolean}
     */
    remove : function(id) {
        if (!this.dockeditems[id]) {
            return false;
        }
        Y.log('Removing block '+this.dockeditems[id]._getLogDescription()+' from the dock.', 'debug', LOGNS);
        this.dockeditems[id].remove();
        delete this.dockeditems[id];
        this.count--;
        this.fire('dock:itemremoved', id);
        this.fire('dock:itemschanged', id);
        return true;
    },
    /**
     * Ensures the the first item in the dock has the correct class.
     * @method resetFirstItem
     */
    resetFirstItem : function() {
        this.get('dockNode').all('.'+CSS.dockeditem+'.firstdockitem').removeClass('firstdockitem');
        if (this.get('dockNode').one('.'+CSS.dockeditem)) {
            this.get('dockNode').one('.'+CSS.dockeditem).addClass('firstdockitem');
        }
    },
    /**
     * Removes all docked blocks returning them to the page.
     * @method removeAll
     * @return {Boolean}
     */
    removeAll : function() {
        Y.log('Undocking all '+this.dockeditems.length+' blocks', 'debug', LOGNS);
        var i;
        for (i in this.dockeditems) {
            if (Y.Lang.isNumber(i) || Y.Lang.isString(i)) {
                this.remove(i);
            }
        }
        return true;
    },
    /**
     * Hides the active item.
     * @method hideActive
     */
    hideActive : function() {
        var item = this.getActiveItem();
        if (item) {
            item.hide();
        }
    },
    /**
     * Checks wether the dock should be shown or hidden
     * @method checkDockVisibility
     */
    checkDockVisibility : function() {
        var bodyclass = CSS.body+'_'+this.get('position')+'_'+this.get('orientation');
        if (!this.count) {
            this.get('dockNode').addClass('nothingdocked');
            BODY.removeClass(CSS.body).removeClass();
            this.fire('dock:hidden');
        } else {
            this.fire('dock:beforeshow');
            this.get('dockNode').removeClass('nothingdocked');
            BODY.addClass(CSS.body).addClass(bodyclass);
            this.fire('dock:shown');
        }
    },
    /**
     * This function checks the size and position of the panel and moves/resizes if
     * required to keep it within the bounds of the window.
     * @method resize
     * @return {Boolean}
     */
    resize : function() {
        var panel = this.getPanel(),
            item = this.getActiveItem(),
            buffer,
            screenh,
            docky,
            titletop,
            containery,
            containerheight,
            scrolltop,
            panelheight,
            dockx,
            titleleft;
        if (!panel.get('visible') || !item) {
            return true;
        }

        this.fire('dock:panelresizestart');
        if (this.get('orientation') === 'vertical') {
            buffer = this.get('bufferPanel');
            screenh = parseInt(BODY.get('winHeight'), 10)-(buffer*2);
            docky = this.get('dockNode').getY();
            titletop = item.get('dockTitleNode').getY()-docky-buffer;
            containery = this.get('itemContainerNode').getY();
            containerheight = containery-docky+this.get('buttonsNode').get('offsetHeight');
            scrolltop = panel.get('bodyNode').get('scrollTop');
            panel.get('bodyNode').setStyle('height', 'auto');
            panel.get('node').removeClass('oversized_content');
            panelheight = panel.get('node').get('offsetHeight');

            if (Y.UA.ie > 0 && Y.UA.ie < 7) {
                panel.setTop(item.get('dockTitleNode').getY());
            } else if (panelheight > screenh) {
                panel.setTop(buffer-containerheight);
                panel.get('bodyNode').setStyle('height', (screenh-panel.get('headerNode').get('offsetHeight'))+'px');
                panel.get('node').addClass('oversized_content');
            } else if (panelheight > (screenh-(titletop-buffer))) {
                panel.setTop(titletop-containerheight-(panelheight - (screenh-titletop))+buffer);
            } else {
                panel.setTop(titletop-containerheight+buffer);
            }

            if (scrolltop) {
                panel.get('bodyNode').set('scrollTop', scrolltop);
            }
        }

        if (this.get('position') === 'right') {
            panel.get('node').setStyle('left', '-' + panel.get('node').get('offsetWidth') + 'px');

        } else if (this.get('position') === 'top') {
            dockx = this.get('dockNode').getX();
            titleleft = item.get('dockTitleNode').getX()-dockx;
            panel.get('node').setStyle('left', titleleft+'px');
        }

        this.fire('dock:resizepanelcomplete');
        return true;
    },
    /**
     * Returns the currently active dock item or false
     * @method getActiveItem
     * @return {DOCKEDITEM}
     */
    getActiveItem : function() {
        var i;
        for (i in this.dockeditems) {
            if (this.dockeditems[i].active) {
                return this.dockeditems[i];
            }
        }
        return false;
    },
    /**
     * Adds an item to the holding area.
     * @method addToHoldingArea
     * @param {Node} node
     */
    addToHoldingArea : function(node) {
        this.holdingareanode.append(node);
    }
};

Y.extend(DOCK, Y.Base, DOCK.prototype, {
    NAME : 'moodle-core-dock',
    ATTRS : {
        /**
         * The dock itself. #dock.
         * @attribute dockNode
         * @type Node
         * @writeOnce
         */
        dockNode : {
            writeOnce : true
        },
        /**
         * The docks panel.
         * @attribute panel
         * @type DOCKPANEL
         * @writeOnce
         */
        panel : {
            writeOnce : true
        },
        /**
         * A container within the dock used for buttons.
         * @attribute buttonsNode
         * @type Node
         * @writeOnce
         */
        buttonsNode : {
            writeOnce : true
        },
        /**
         * A container within the dock used for docked blocks.
         * @attribute itemContainerNode
         * @type Node
         * @writeOnce
         */
        itemContainerNode : {
            writeOnce : true
        },

        /**
         * Buffer used when containing a panel.
         * @attribute bufferPanel
         * @type Number
         * @default 10
         */
        bufferPanel : {
            value : 10,
            validator : Y.Lang.isNumber
        },

        /**
         * Position of the dock.
         * @attribute position
         * @type String
         * @default left
         */
        position : {
            value : 'left',
            validator : Y.Lang.isString
        },

        /**
         * vertical || horizontal determines if we change the title
         * @attribute orientation
         * @type String
         * @default vertical
         */
        orientation : {
            value : 'vertical',
            validator : Y.Lang.isString,
            setter : function(value) {
                if (value.match(/^vertical$/i)) {
                    return 'vertical';
                }
                return 'horizontal';
            }
        },

        /**
         * Space between the top of the dock and the first item.
         * @attribute bufferBeforeFirstItem
         * @type Number
         * @default 10
         */
        bufferBeforeFirstItem : {
            value : 10,
            validator : Y.Lang.isNumber
        },

        /**
         * Icon URL for the icon to undock all blocks
         * @attribute undockAllIconUrl
         * @type String
         * @default t/dock_to_block
         */
        undockAllIconUrl: {
            value : M.util.image_url((window.right_to_left()) ? 't/dock_to_block_rtl' : 't/dock_to_block', 'moodle'),
            validator : Y.Lang.isString
        }
    }
});
Y.augment(DOCK, Y.EventTarget);
/**
 * Dock JS.
 *
 * This file contains the panel class used by the dock to display the content of docked blocks.
 *
 * @module moodle-core-dock
 */

/**
 * Panel.
 *
 * @namespace M.core.dock
 * @class Panel
 * @constructor
 * @extends Base
 * @uses EventTarget
 */
DOCKPANEL = function() {
    DOCKPANEL.superclass.constructor.apply(this, arguments);
};
DOCKPANEL.prototype = {
    /**
     * True once the panel has been created.
     * @property created
     * @protected
     * @type {Boolean}
     */
    created : false,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        Y.log('Panel initialising', 'debug', LOGNS);
        /**
         * Fired before the panel is shown.
         * @event dockpane::beforeshow
         */
        this.publish('dockpanel:beforeshow', {prefix:'dockpanel'});
        /**
         * Fired after the panel is shown.
         * @event dockpanel:shown
         */
        this.publish('dockpanel:shown', {prefix:'dockpanel'});
        /**
         * Fired before the panel is hidden.
         * @event dockpane::beforehide
         */
        this.publish('dockpanel:beforehide', {prefix:'dockpanel'});
        /**
         * Fired after the panel is hidden.
         * @event dockpanel:hidden
         */
        this.publish('dockpanel:hidden', {prefix:'dockpanel'});
        /**
         * Fired when ever the dock panel is either hidden or shown.
         * Always fired after the shown or hidden events.
         * @event dockpanel:visiblechange
         */
        this.publish('dockpanel:visiblechange', {prefix:'dockpanel'});
    },
    /**
     * Creates the Panel if it has not already been created.
     * @method create
     * @return {Boolean}
     */
    create : function() {
        if (this.created) {
            return true;
        }
        this.created = true;
        var dock = this.get('dock'),
            node = dock.get('dockNode');
        this.set('node', Y.Node.create('<div id="dockeditempanel" class="dockitempanel_hidden"></div>'));
        this.set('contentNode', Y.Node.create('<div class="dockeditempanel_content"></div>'));
        this.set('headerNode', Y.Node.create('<div class="dockeditempanel_hd"></div>'));
        this.set('bodyNode', Y.Node.create('<div class="dockeditempanel_bd"></div>'));
        node.append(
            this.get('node').append(this.get('contentNode').append(this.get('headerNode')).append(this.get('bodyNode')))
        );
    },
    /**
     * Displays the panel.
     * @method show
     */
    show : function() {
        this.create();
        this.fire('dockpanel:beforeshow');
        this.set('visible', true);
        this.get('node').removeClass('dockitempanel_hidden');
        this.fire('dockpanel:shown');
        this.fire('dockpanel:visiblechange');
    },
    /**
     * Hides the panel
     * @method hide
     */
    hide : function() {
        this.fire('dockpanel:beforehide');
        this.set('visible', false);
        this.get('node').addClass('dockitempanel_hidden');
        this.fire('dockpanel:hidden');
        this.fire('dockpanel:visiblechange');
    },
    /**
     * Sets the panel header.
     * @method setHeader
     * @param {Node|String} content
     */
    setHeader : function(content) {
        this.create();
        var header = this.get('headerNode'),
            i;
        header.setContent(content);
        if (arguments.length > 1) {
            for (i = 1; i < arguments.length; i++) {
                if (Y.Lang.isNumber(i) || Y.Lang.isString(i)) {
                    header.append(arguments[i]);
                }
            }
        }
    },
    /**
     * Sets the panel body.
     * @method setBody
     * @param {Node|String} content
     */
    setBody : function(content) {
        this.create();
        this.get('bodyNode').setContent(content);
    },
    /**
     * Sets the new top mark of the panel.
     *
     * @method setTop
     * @param {Number} newtop
     */
    setTop : function(newtop) {
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            this.get('node').setY(newtop);
        } else {
            this.get('node').setStyle('top', newtop.toString()+'px');
        }
    },
    /**
     * Corrects the width of the panel.
     * @method correctWidth
     */
    correctWidth : function() {
        var bodyNode = this.get('bodyNode'),
            // Width of content.
            width = bodyNode.get('clientWidth'),
            // Scrollable width of content.
            scroll = bodyNode.get('scrollWidth'),
            // Width of content container with overflow.
            offsetWidth = bodyNode.get('offsetWidth'),
            // The new width - defaults to the current width.
            newWidth = width,
            // The max width (80% of screen).
            maxWidth = Math.round(bodyNode.get('winWidth') * 0.8);

        // If the scrollable width is more than the visible width
        if (scroll > width) {
            //   Content width
            // + the difference
            // + any rendering difference (borders, padding)
            // + 10px to make it look nice.
            newWidth = width + (scroll - width) + ((offsetWidth - width)*2) + 10;
        }

        // Make sure its not more then the maxwidth
        if (newWidth > maxWidth) {
            newWidth = maxWidth;
        }

        // Set the new width if its more than the old width.
        if (newWidth > offsetWidth) {
            this.get('node').setStyle('width', newWidth+'px');
        }
    }
};
Y.extend(DOCKPANEL, Y.Base, DOCKPANEL.prototype, {
    NAME : 'moodle-core-dock-panel',
    ATTRS : {
        /**
         * The dock itself.
         * @attribute dock
         * @type DOCK
         * @writeonce
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * The node that contains the whole panel.
         * @attribute node
         * @type Node
         */
        node : {
            value : null
        },
        /**
         * The node that contains the header, body and footer.
         * @attribute contentNode
         * @type Node
         */
        contentNode : {
            value : null
        },
        /**
         * The node that contains the header
         * @attribute headerNode
         * @type Node
         */
        headerNode : {
            value : null
        },
        /**
         * The node that contains the body
         * @attribute bodyNode
         * @type Node
         */
        bodyNode : {
            value : null
        },
        /**
         * True if the panel is currently visible.
         * @attribute visible
         * @type Boolean
         */
        visible : {
            value : false
        }
    }
});
Y.augment(DOCKPANEL, Y.EventTarget);
/**
 * Dock JS.
 *
 * This file contains the tab height manager.
 * The tab height manager is responsible for ensure all tabs are visible all the time.
 *
 * @module moodle-core-dock
 */

/**
 * Tab height manager.
 *
 * @namespace M.core.dock
 * @class TabHeightManager
 * @constructor
 * @extends Base
 */
TABHEIGHTMANAGER = function() {
    TABHEIGHTMANAGER.superclass.constructor.apply(this, arguments);
};
TABHEIGHTMANAGER.prototype = {
    /**
     * Initialises the dock sizer which then attaches itself to the required
     * events in order to monitor the dock
     * @method initializer
     */
    initializer : function() {
        var dock = this.get('dock');
        dock.on('dock:itemschanged', this.checkSizing, this);
        Y.on('windowresize', this.checkSizing, this);
    },
    /**
     * Check if the size dock items needs to be adjusted
     * @method checkSizing
     */
    checkSizing : function() {
        var dock = this.get('dock'),
            node = dock.get('dockNode'),
            items = dock.dockeditems,
            containermargin = parseInt(node.one('.dockeditem_container').getStyle('marginTop').replace('/[^0-9]+$/', ''), 10),
            dockheight = node.get('offsetHeight') - containermargin,
            controlheight = node.one('.controls').get('offsetHeight'),
            buffer = (dock.get('bufferPanel') * 3),
            possibleheight = dockheight - controlheight - buffer - (items.length*2),
            totalheight = 0,
            id, dockedtitle;
        if (items.length > 0) {
            for (id in items) {
                if (Y.Lang.isNumber(id) || Y.Lang.isString(id)) {
                    dockedtitle = Y.one(items[id].get('title')).ancestor('.'+CSS.dockedtitle);
                    if (dockedtitle) {
                        if (this.get('enabled')) {
                            dockedtitle.setStyle('height', 'auto');
                        }
                        totalheight += dockedtitle.get('offsetHeight') || 0;
                    }
                }
            }
            if (totalheight > possibleheight) {
                this.enable(possibleheight);
            }
        }
    },
    /**
     * Enables the dock sizer and resizes where required.
     * @method enable
     * @param {Number} possibleheight
     */
    enable : function(possibleheight) {
        var dock = this.get('dock'),
            items = dock.dockeditems,
            count = dock.count,
            runningcount = 0,
            usedheight = 0,
            id, itemtitle, itemheight, offsetheight;
        Y.log('Enabling the dock tab sizer.', 'debug', LOGNS);
        this.set('enabled', true);
        for (id in items) {
            if (Y.Lang.isNumber(id) || Y.Lang.isString(id)) {
                itemtitle = Y.one(items[id].get('title')).ancestor('.'+CSS.dockedtitle);
                if (!itemtitle) {
                    continue;
                }
                itemheight = Math.floor((possibleheight-usedheight) / (count - runningcount));
                offsetheight = itemtitle.get('offsetHeight');
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
    }
};
Y.extend(TABHEIGHTMANAGER, Y.Base, TABHEIGHTMANAGER.prototype, {
    NAME : 'moodle-core-tabheightmanager',
    ATTRS : {
        /**
         * The dock.
         * @attribute dock
         * @type DOCK
         * @writeOnce
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * True if the item_sizer is being used, false otherwise.
         * @attribute enabled
         * @type Bool
         */
        enabled : {
            value : false
        }
    }
});
/**
 * Dock JS.
 *
 * This file contains the action key event definition that is used for accessibility handling within the Dock.
 *
 * @module moodle-core-dock
 */

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
 *
 * @namespace M.core.dock
 * @class ActionKey
 */
Y.Event.define("dock:actionkey", {
    // Webkit and IE repeat keydown when you hold down arrow keys.
    // Opera links keypress to page scroll; others keydown.
    // Firefox prevents page scroll via preventDefault() on either
    // keydown or keypress.
    _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

    /**
     * The keys to trigger on.
     * @property _keys
     */
    _keys: {
        //arrows
        '37': 'collapse',
        '39': 'expand',
        //(@todo: lrt/rtl/M.core_dock.cfg.orientation decision to assign arrow to meanings)
        '32': 'toggle',
        '13': 'enter'
    },

    /**
     * Handles key events
     * @method _keyHandler
     * @param {EventFacade} e
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {Object} args
     */
    _keyHandler: function (e, notifier, args) {
        var actObj;
        if (!args.actions) {
            actObj = {collapse: true, expand: true, toggle: true, enter: true};
        } else {
            actObj = args.actions;
        }
        if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {
            e.action = this._keys[e.keyCode];
            notifier.fire(e);
        }
    },

    /**
     * Subscribes to events.
     * @method on
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     */
    on: function (node, sub, notifier) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args === null) {
            //no actions given
            sub._detacher = node.on(this._event, this._keyHandler, this, notifier, {actions: false});
        } else {
            sub._detacher = node.on(this._event, this._keyHandler, this, notifier, sub.args[0]);
        }
    },

    /**
     * Detaches an event listener
     * @method detach
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     */
    detach: function (node, sub) {
        //detach our _detacher handle of the subscription made in on()
        sub._detacher.detach();
    },

    /**
     * Creates a delegated event listener.
     * @method delegate
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {String|function} filter Selector string or function that accpets an event object and returns null.
     */
    delegate: function (node, sub, notifier, filter) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args === null) {
            //no actions given
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler, filter, this, notifier, {actions:false});
        } else {
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler, filter, this, notifier, sub.args[0]);
        }
    },

    /**
     * Detaches a delegated event listener.
     * @method detachDelegate
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {String|function} filter Selector string or function that accpets an event object and returns null.
     */
    detachDelegate: function (node, sub) {
        sub._delegateDetacher.detach();
    }
});
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
     * @type DOCKEDITEM
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

        Y.log('Initialised block with instance id:'+this.get('id'), 'debug', LOGNS);

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
    _getBlockClass : function(node) {
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
    moveToDock : function(e) {
        if (e) {
            e.halt(true);
        }

        var dock = M.core.dock.get(),
            id = this.get('id'),
            blockcontent = Y.one('#inst'+id).one('.content'),
            icon = (window.right_to_left()) ? 't/dock_to_block_rtl' : 't/dock_to_block',
            breakchar = (location.href.match(/\?/)) ? '&' : '?',
            blocktitle,
            blockcommands,
            movetoimg,
            moveto;

        if (!blockcontent) {
            return;
        }

        Y.log('Moving block to the dock:'+this.get('id'), 'debug', LOGNS);

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
            alt : Y.Escape.html(M.util.get_string('undockitem', 'block')),
            title : Y.Escape.html(M.util.get_string('undockblock', 'block', blocktitle.get('innerHTML'))),
            src : M.util.image_url(icon, 'moodle')
        });
        moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>').setAttrs({
            href : Y.config.win.location.href + breakchar + 'dock='+id
        });
        moveto.append(movetoimg);
        blockcommands.append(moveto.append(movetoimg));

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

        this.set('isDocked', true);
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
    },

    /**
     * This function removes a block from the dock and puts it back into the page structure.
     * @method returnToPage
     * @return {Boolean}
     */
    returnToPage : function() {
        var id = this.get('id');

        Y.log('Moving block out of the dock:'+this.get('id'), 'debug', LOGNS);

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

        M.util.set_user_preference('docked_block_instance_'+id, 0);
        this.set('isDocked', false);
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
/**
 * Dock JS.
 *
 * This file contains the docked item class.
 *
 * @module moodle-core-dock
 */

/**
 * Docked item.
 *
 * @namespace M.core.dock
 * @class DockedItem
 * @constructor
 * @extends Base
 * @uses EventTarget
 */
DOCKEDITEM = function() {
    DOCKEDITEM.superclass.constructor.apply(this, arguments);
};
DOCKEDITEM.prototype = {
    /**
     * Set to true if this item is currently being displayed.
     * @property active
     * @protected
     * @type Boolean
     */
    active : false,
    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        var title = this.get('title'),
            titlestring,
            type;
        /**
         * Fired before the docked item has been drawn.
         * @event dockeditem:drawstart
         */
        this.publish('dockeditem:drawstart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been drawn.
         * @event dockeditem:drawcomplete
         */
        this.publish('dockeditem:drawcomplete', {prefix:'dockeditem'});
        /**
         * Fired before the docked item is to be shown.
         * @event dockeditem:showstart
         */
        this.publish('dockeditem:showstart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been shown.
         * @event dockeditem:showcomplete
         */
        this.publish('dockeditem:showcomplete', {prefix:'dockeditem'});
        /**
         * Fired before the docked item has been hidden.
         * @event dockeditem:hidestart
         */
        this.publish('dockeditem:hidestart', {prefix:'dockeditem'});
        /**
         * Fired after the docked item has been hidden.
         * @event dockeditem:hidecomplete
         */
        this.publish('dockeditem:hidecomplete', {prefix:'dockeditem'});
        /**
         * Fired when the docked item is removed from the dock.
         * @event dockeditem:itemremoved
         */
        this.publish('dockeditem:itemremoved', {prefix:'dockeditem'});
        if (title) {
            type = title.get('nodeName');
            titlestring = title.cloneNode(true);
            title = Y.Node.create('<'+type+'></'+type+'>');
            title = M.core.dock.fixTitleOrientation(title, titlestring.get('text'));
            this.set('title', title);
            this.set('titlestring', titlestring);
        }
        Y.log('Initialised dockeditem for block with title "'+this._getLogDescription(), 'debug', LOGNS);
    },
    /**
     * This function draws the item on the dock.
     * @method draw
     * @return Boolean
     */
    draw : function() {
        var create = Y.Node.create,
            dock = this.get('dock'),
            count = dock.count,
            docktitle,
            dockitem,
            closeicon,
            closeiconimg,
            id = this.get('id');

        this.fire('dockeditem:drawstart');

        docktitle = create('<div id="dock_item_'+id+'_title" role="menu" aria-haspopup="true" class="'+CSS.dockedtitle+'"></div>');
        docktitle.append(this.get('title'));
        dockitem = create('<div id="dock_item_'+id+'" class="'+CSS.dockeditem+'" tabindex="0" rel="'+id+'"></div>');
        if (count === 1) {
            dockitem.addClass('firstdockitem');
        }
        dockitem.append(docktitle);
        dock.append(dockitem);

        closeiconimg = create('<img alt="' + M.util.get_string('hidepanel', 'block') +
                '" title="' + M.util.get_string('hidedockpanel', 'block') + '" />');
        closeiconimg.setAttribute('src', M.util.image_url('t/dockclose', 'moodle'));
        closeicon = create('<span class="hidepanelicon" tabindex="0"></span>').append(closeiconimg);
        closeicon.on('forceclose|click', this.hide, this);
        closeicon.on('dock:actionkey',this.hide, this, {actions:{enter:true,toggle:true}});
        this.get('commands').append(closeicon);

        this.set('dockTitleNode', docktitle);
        this.set('dockItemNode', dockitem);

        this.fire('dockeditem:drawcomplete');
        return true;
    },
    /**
     * This function toggles makes the item active and shows it.
     * @method show
     * @return Boolean
     */
    show : function() {
        var dock = this.get('dock'),
            panel = dock.getPanel(),
            docktitle = this.get('dockTitleNode');

        dock.hideActive();
        this.fire('dockeditem:showstart');
        Y.log('Showing '+this._getLogDescription(), 'debug', LOGNS);
        panel.setHeader(this.get('titlestring'), this.get('commands'));
        panel.setBody(Y.Node.create('<div class="block_' + this.get('blockclass') + ' block_docked"></div>')
             .append(this.get('contents')));
        if (M.core.actionmenu !== undefined) {
            M.core.actionmenu.newDOMNode(panel.get('node'));
        }
        panel.show();
        panel.correctWidth();

        this.active = true;
        // Add active item class first up
        docktitle.addClass(CSS.activeitem);
        // Set aria-exapanded property to true.
        docktitle.set('aria-expanded', "true");
        this.fire('dockeditem:showcomplete');
        dock.resize();
        return true;
    },
    /**
     * This function hides the item and makes it inactive.
     * @method hide
     */
    hide : function() {
        this.fire('dockeditem:hidestart');
        Y.log('Hiding "'+this._getLogDescription(), 'debug', LOGNS);
        if (this.active) {
            // No longer active
            this.active = false;
            // Hide the panel
            this.get('dock').getPanel().hide();
        }
        // Remove the active class
        // Set aria-exapanded property to false
        this.get('dockTitleNode').removeClass(CSS.activeitem).set('aria-expanded', "false");
        this.fire('dockeditem:hidecomplete');
    },
    /**
     * A toggle between calling show and hide functions based on css.activeitem
     * Applies rules to key press events (dock:actionkey)
     * @method toggle
     * @param {String} action
     */
    toggle : function(action) {
        var docktitle = this.get('dockTitleNode');
        if (docktitle.hasClass(CSS.activeitem) && action !== 'expand') {
            this.hide();
        } else if (!docktitle.hasClass(CSS.activeitem) && action !== 'collapse')  {
            this.show();
        }
    },
    /**
     * This function removes the node and destroys it's bits.
     * @method remove.
     */
    remove : function () {
        this.hide();
        // Return the block to its original position.
        this.get('block').returnToPage();
        // Remove the dock item node.
        this.get('dockItemNode').remove();
        this.fire('dockeditem:itemremoved');
    },
    /**
     * Returns the description of this item to use for log calls.
     * @method _getLogDescription
     * @private
     * @return {String}
     */
    _getLogDescription : function() {
        return this.get('titlestring').get('innerHTML')+' ('+this.get('blockinstanceid')+')';
    }
};
Y.extend(DOCKEDITEM, Y.Base, DOCKEDITEM.prototype, {
    NAME : 'moodle-core-dock-dockeditem',
    ATTRS : {
        /**
         * The block this docked item is associated with.
         * @attribute block
         * @type BLOCK
         * @writeOnce
         * @required
         */
        block : {
            writeOnce : 'initOnly'
        },
        /**
         * The dock itself.
         * @attribute dock
         * @type DOCK
         * @writeOnce
         * @required
         */
        dock : {
            writeOnce : 'initOnly'
        },
        /**
         * The docked item ID. This will be given by the dock.
         * @attribute id
         * @type Number
         */
        id : {},
        /**
         * Block instance id.Taken from the associated block.
         * @attribute blockinstanceid
         * @type Number
         * @writeOnce
         */
        blockinstanceid : {
            writeOnce : 'initOnly',
            setter : function(value) {
                return parseInt(value, 10);
            }
        },
        /**
         * The title  nodeof the docked item.
         * @attribute title
         * @type Node
         * @default null
         */
        title : {
            value : null
        },
        /**
         * The title string.
         * @attribute titlestring
         * @type String
         */
        titlestring : {
            value : null
        },
        /**
         * The contents of the docked item
         * @attribute contents
         * @type Node
         * @writeOnce
         * @required
         */
        contents : {
            writeOnce : 'initOnly'
        },
        /**
         * Commands associated with the block.
         * @attribute commands
         * @type Node
         * @writeOnce
         * @required
         */
        commands : {
            writeOnce : 'initOnly'
        },
        /**
         * The block class.
         * @attribute blockclass
         * @type String
         * @writeOnce
         * @required
         */
        blockclass : {
            writeOnce : 'initOnly'
        },
        /**
         * The title node for the docked block.
         * @attribute dockTitleNode
         * @type Node
         */
        dockTitleNode : {
            value : null
        },
        /**
         * The item node for the docked block.
         * @attribute dockItemNode
         * @type Node
         */
        dockItemNode : {
            value : null
        },
        /**
         * The container for the docked item (will contain the block contents when visible)
         * @attribute dockcontainerNode
         * @type Node
         */
        dockcontainerNode : {
            value : null
        }
    }
});
Y.augment(DOCKEDITEM, Y.EventTarget);


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "event-custom",
        "event-mouseenter",
        "event-resize",
        "escape",
        "moodle-core-dock-loader"
    ]
});
