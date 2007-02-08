/*
Copyright (c) 2006, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 0.12.2
*/
/**
* @module menu
* @description <p>The Menu Library features a collection of widgets that make 
* it easy to add menus to your website or web application.  With the Menu 
* Library you can create website fly-out menus, customized context menus, or 
* application-style menu bars with just a small amount of scripting.</p>
* <ul>
*    <li>Screen-reader accessibility.</li>
*    <li>Keyboard and mouse navigation.</li>
*    <li>A rich event model that provides access to all of a menu's 
*    interesting moments.</li>
*    <li>Support for 
*    <a href="http://en.wikipedia.org/wiki/Progressive_Enhancement">Progressive
*    Enhancement</a>; Menus can be created from simple, 
*    semantic markup on the page or purely through JavaScript.</li>
* </ul>
* @title Menu Library
* @namespace YAHOO.widget
* @requires Event, Dom, Container
*/
(function() {

var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event;

/**
* Singleton that manages a collection of all menus and menu items.  Listens for 
* DOM events at the document level and dispatches the events to the 
* corresponding menu or menu item.
*
* @namespace YAHOO.widget
* @class MenuManager
* @static
*/
YAHOO.widget.MenuManager = function() {

    // Private member variables

    // Flag indicating if the DOM event handlers have been attached

    var m_bInitializedEventHandlers = false,

        // Collection of menus

        m_oMenus = {},
    
    
        //  Collection of menu items 

        m_oItems = {},

        // Collection of visible menus
    
        m_oVisibleMenus = {},

        // Logger


        me = this;

    // Private methods

    /**
    * @method addItem
    * @description Adds an item to the collection of known menu items.
    * @private
    * @param {YAHOO.widget.MenuItem} p_oItem Object specifying the MenuItem 
    * instance to be added.
    */
    function addItem(p_oItem) {

        var sYUIId = Dom.generateId();

        if(p_oItem && m_oItems[sYUIId] != p_oItem) {

            p_oItem.element.setAttribute("yuiid", sYUIId);
    
            m_oItems[sYUIId] = p_oItem;
    
            p_oItem.destroyEvent.subscribe(onItemDestroy, p_oItem);


        }
    
    }

    /**
    * @method removeItem
    * @description Removes an item from the collection of known menu items.
    * @private
    * @param {YAHOO.widget.MenuItem} p_oItem Object specifying the MenuItem 
    * instance to be removed.
    */
    function removeItem(p_oItem) {
    
        var sYUIId = p_oItem.element.getAttribute("yuiid");

        if(sYUIId && m_oItems[sYUIId]) {

            delete m_oItems[sYUIId];


        }
    
    }

    /**
    * @method getMenuRootElement
    * @description Finds the root DIV node of a menu or the root LI node of a 
    * menu item.
    * @private
    * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-58190037">HTMLElement</a>} p_oElement Object specifying 
    * an HTML element.
    */
    function getMenuRootElement(p_oElement) {
    
        var oParentNode;

        if(p_oElement && p_oElement.tagName) {
        
            switch(p_oElement.tagName.toUpperCase()) {
                    
                case "DIV":
    
                    oParentNode = p_oElement.parentNode;
    
                    // Check if the DIV is the inner "body" node of a menu

                    if(
                        Dom.hasClass(p_oElement, "bd") && 
                        oParentNode && 
                        oParentNode.tagName && 
                        oParentNode.tagName.toUpperCase() == "DIV"
                    ) {
                    
                        return oParentNode;
                    
                    }
                    else {
                    
                        return p_oElement;
                    
                    }
                
                break;

                case "LI":
    
                    return p_oElement;

                default:
    
                    oParentNode = p_oElement.parentNode;
    
                    if(oParentNode) {
                    
                        return getMenuRootElement(oParentNode);
                    
                    }
                
                break;
            
            }

        }
        
    }

    // Private event handlers

    /**
    * @method onDOMEvent
    * @description Generic, global event handler for all of a menu's DOM-based 
    * events.  This listens for events against the document object.  If the 
    * target of a given event is a member of a menu or menu item's DOM, the 
    * instance's corresponding Custom Event is fired.
    * @private
    * @param {Event} p_oEvent Object representing the DOM event object passed 
    * back by the event utility (YAHOO.util.Event).
    */
    function onDOMEvent(p_oEvent) {

        // Get the target node of the DOM event
    
        var oTarget = Event.getTarget(p_oEvent),

        // See if the target of the event was a menu, or a menu item

            oElement = getMenuRootElement(oTarget),
            oMenuItem,
            oMenu; 

        if(oElement) {

            var sTagName = oElement.tagName.toUpperCase();
    
            if(sTagName == "LI") {
        
                var sYUIId = oElement.getAttribute("yuiid");
        
                if(sYUIId) {
        
                    oMenuItem = m_oItems[sYUIId];
                    oMenu = oMenuItem.parent;
        
                }
            
            }
            else if(sTagName == "DIV") {
            
                if(oElement.id) {
                
                    oMenu = m_oMenus[oElement.id];
                
                }
            
            }

        }

        if(oMenu) {

            // Map of DOM event names to CustomEvent names
        
            var oEventTypes =  {
                    "click": "clickEvent",
                    "mousedown": "mouseDownEvent",
                    "mouseup": "mouseUpEvent",
                    "mouseover": "mouseOverEvent",
                    "mouseout": "mouseOutEvent",
                    "keydown": "keyDownEvent",
                    "keyup": "keyUpEvent",
                    "keypress": "keyPressEvent"
                },
    
                sCustomEventType = oEventTypes[p_oEvent.type];

            // Fire the Custom Even that corresponds the current DOM event    
    
            if(oMenuItem && !oMenuItem.cfg.getProperty("disabled")) {
            
                oMenuItem[sCustomEventType].fire(p_oEvent);                   
            
            }
    
            oMenu[sCustomEventType].fire(p_oEvent, oMenuItem);
        
        }
        else if(p_oEvent.type == "mousedown") {

            /*
                If the target of the event wasn't a menu, hide all 
                dynamically positioned menus
            */
            
            var oActiveItem;
    
            for(var i in m_oMenus) {
    
                if(m_oMenus.hasOwnProperty(i)) {
    
                    oMenu = m_oMenus[i];
    
                    if(
                        oMenu.cfg.getProperty("clicktohide") && 
                        oMenu.cfg.getProperty("position") == "dynamic"
                    ) {
    
                        oMenu.hide();
    
                    }
                    else {

                        oMenu.clearActiveItem(true);
    
                    }
    
                }
    
            } 

        }

    }

    /**
    * @method onMenuDestroy
    * @description "destroy" event handler for a menu.
    * @private
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
    * fired the event.
    */
    function onMenuDestroy(p_sType, p_aArgs, p_oMenu) {

        if(p_oMenu && m_oMenus[p_oMenu.id]) {

            delete m_oMenus[p_oMenu.id];


        }

    }

    /**
    * @method onItemDestroy
    * @description "destroy" event handler for a MenuItem instance.
    * @private
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item 
    * that fired the event.
    */
    function onItemDestroy(p_sType, p_aArgs, p_oItem) {

        var sYUIId = p_oItem.element.getAttribute("yuiid");

        if(sYUIId) {

            delete m_oItems[sYUIId];

        }

    }

    /**
    * @method onMenuVisibleConfigChange
    * @description Event handler for when the "visible" configuration property 
    * of a Menu instance changes.
    * @private
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
    * fired the event.
    */
    function onMenuVisibleConfigChange(p_sType, p_aArgs, p_oMenu) {

        var bVisible = p_aArgs[0];
        
        if(bVisible) {

            m_oVisibleMenus[p_oMenu.id] = p_oMenu;
            
        
        }
        else if(m_oVisibleMenus[p_oMenu.id]) {
        
            delete m_oVisibleMenus[p_oMenu.id];
            
        
        }
    
    }

    /**
    * @method onItemAdded
    * @description "itemadded" event handler for a Menu instance.
    * @private
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    */
    function onItemAdded(p_sType, p_aArgs) {
    
        addItem(p_aArgs[0]);
    
    }
    

    /**
    * @method onItemRemoved
    * @description "itemremoved" event handler for a Menu instance.
    * @private
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    */
    function onItemRemoved(p_sType, p_aArgs) {

        removeItem(p_aArgs[0]);
    
    }

    return {

        // Privileged methods

        /**
        * @method addMenu
        * @description Adds a menu to the collection of known menus.
        * @param {YAHOO.widget.Menu} p_oMenu Object specifying the Menu  
        * instance to be added.
        */
        addMenu: function(p_oMenu) {
    
            if(p_oMenu && p_oMenu.id && !m_oMenus[p_oMenu.id]) {
    
                m_oMenus[p_oMenu.id] = p_oMenu;
            
        
                if(!m_bInitializedEventHandlers) {
        
                    var oDoc = document;
            
                    Event.addListener(oDoc, "mouseover", onDOMEvent, me, true);
                    Event.addListener(oDoc, "mouseout", onDOMEvent, me, true);
                    Event.addListener(oDoc, "mousedown", onDOMEvent, me, true);
                    Event.addListener(oDoc, "mouseup", onDOMEvent, me, true);
                    Event.addListener(oDoc, "click", onDOMEvent, me, true);
                    Event.addListener(oDoc, "keydown", onDOMEvent, me, true);
                    Event.addListener(oDoc, "keyup", onDOMEvent, me, true);
                    Event.addListener(oDoc, "keypress", onDOMEvent, me, true);
        
                    m_bInitializedEventHandlers = true;
                    
        
                }
        
                p_oMenu.destroyEvent.subscribe(onMenuDestroy, p_oMenu, me);
                
                p_oMenu.cfg.subscribeToConfigEvent(
                    "visible", 
                    onMenuVisibleConfigChange, 
                    p_oMenu
                );
        
                p_oMenu.itemAddedEvent.subscribe(onItemAdded);
                p_oMenu.itemRemovedEvent.subscribe(onItemRemoved);
    
    
            }
    
        },

    
        /**
        * @method removeMenu
        * @description Removes a menu from the collection of known menus.
        * @param {YAHOO.widget.Menu} p_oMenu Object specifying the Menu  
        * instance to be removed.
        */
        removeMenu: function(p_oMenu) {
    
            if(p_oMenu && m_oMenus[p_oMenu.id]) {
    
                delete m_oMenus[p_oMenu.id];
    
    
            }
    
        },
    
    
        /**
        * @method hideVisible
        * @description Hides all visible, dynamically positioned menus.
        */
        hideVisible: function() {
    
            var oMenu;
    
            for(var i in m_oVisibleMenus) {
    
                if(m_oVisibleMenus.hasOwnProperty(i)) {
    
                    oMenu = m_oVisibleMenus[i];
    
                    if(oMenu.cfg.getProperty("position") == "dynamic") {
    
                        oMenu.hide();
    
                    }
    
                }
    
            }        
        
        },

        /**
        * @method getMenus
        * @description Returns an array of all menus registered with the 
        * menu manger.
        * @return {Array}
        */
        getMenus: function() {
        
            return m_oMenus;
        
        },

        /**
        * @method getMenu
        * @description Returns a menu with the specified id.
        * @param {String} p_sId String specifying the id of the menu to
        * be retrieved.
        * @return {YAHOO.widget.Menu}
        */
        getMenu: function(p_sId) {
    
            if(m_oMenus[p_sId]) {
            
                return m_oMenus[p_sId];
            
            }
        
        },

    
        /**
        * @method toString
        * @description Returns a string representing the menu manager.
        * @return {String}
        */
        toString: function() {
        
            return ("MenuManager");
        
        }

    };

}();

})();


(function() {

var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event;


/**
* The Menu class creates a container that holds a vertical list representing 
* a set of options or commands.  Menu is the base class for all 
* menu containers. 
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source 
* for the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement 
* Object specifying the <code>&#60;select&#62;</code> element to be used as 
* the data source for the menu.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu. See configuration class documentation for 
* more details.
* @namespace YAHOO.widget
* @class Menu
* @constructor
* @extends YAHOO.widget.Overlay
*/
YAHOO.widget.Menu = function(p_oElement, p_oConfig) {

    if(p_oConfig) {

        this.parent = p_oConfig.parent;

        this.lazyLoad = p_oConfig.lazyLoad || p_oConfig.lazyload;

        this.itemData = p_oConfig.itemData || p_oConfig.itemdata;

    }


    YAHOO.widget.Menu.superclass.constructor.call(
        this, 
        p_oElement, 
        p_oConfig
    );

};

YAHOO.extend(YAHOO.widget.Menu, YAHOO.widget.Overlay, {



// Constants


/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the 
* menu's <code>&#60;div&#62;</code> element.
* @default "yuimenu"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuimenu",


/**
* @property ITEM_TYPE
* @description Object representing the type of menu item to instantiate and 
* add when parsing the child nodes (either <code>&#60;li&#62;</code> element, 
* <code>&#60;optgroup&#62;</code> element or <code>&#60;option&#62;</code>) 
* of the menu's source HTML element.
* @default YAHOO.widget.MenuItem
* @final
* @type YAHOO.widget.MenuItem
*/
ITEM_TYPE: null,


/**
* @property GROUP_TITLE_TAG_NAME
* @description String representing the tagname of the HTML element used to 
* title the menu's item groups.
* @default H6
* @final
* @type String
*/
GROUP_TITLE_TAG_NAME: "h6",



// Private properties


/** 
* @property _nHideDelayId
* @description Number representing the time-out setting used to cancel the 
* hiding of a menu.
* @default null
* @private
* @type Number
*/
_nHideDelayId: null,


/** 
* @property _nShowDelayId
* @description Number representing the time-out setting used to cancel the 
* showing of a menu.
* @default null
* @private
* @type Number
*/
_nShowDelayId: null,


/** 
* @property _hideDelayEventHandlersAssigned
* @description Boolean indicating if the "mouseover" and "mouseout" event 
* handlers used for hiding the menu via a call to "window.setTimeout" have 
* already been assigned.
* @default false
* @private
* @type Boolean
*/
_hideDelayEventHandlersAssigned: false,


/**
* @property _bHandledMouseOverEvent
* @description Boolean indicating the current state of the menu's 
* "mouseover" event.
* @default false
* @private
* @type Boolean
*/
_bHandledMouseOverEvent: false,


/**
* @property _bHandledMouseOutEvent
* @description Boolean indicating the current state of the menu's
* "mouseout" event.
* @default false
* @private
* @type Boolean
*/
_bHandledMouseOutEvent: false,


/**
* @property _aGroupTitleElements
* @description Array of HTML element used to title groups of menu items.
* @default []
* @private
* @type Array
*/
_aGroupTitleElements: null,


/**
* @property _aItemGroups
* @description Array of menu items.
* @default []
* @private
* @type Array
*/
_aItemGroups: null,


/**
* @property _aListElements
* @description Array of <code>&#60;ul&#62;</code> elements, each of which is 
* the parent node for each item's <code>&#60;li&#62;</code> element.
* @default []
* @private
* @type Array
*/
_aListElements: null,



// Public properties


/**
* @property lazyLoad
* @description Boolean indicating if the menu's "lazy load" feature is 
* enabled.  If set to "true," initialization and rendering of the menu's 
* items will be deferred until the first time it is made visible.  This 
* property should be set via the constructor using the configuration 
* object literal.
* @default false
* @type Boolean
*/
lazyLoad: false,


/**
* @property itemData
* @description Array of items to be added to the menu.  The array can contain 
* strings representing the text for each item to be created, object literals 
* representing the menu item configuration properties, or MenuItem instances.  
* This property should be set via the constructor using the configuration 
* object literal.
* @default null
* @type Array
*/
itemData: null,


/**
* @property activeItem
* @description Object reference to the item in the menu that has focus.
* @default null
* @type YAHOO.widget.MenuItem
*/
activeItem: null,


/**
* @property parent
* @description Object reference to the menu's parent menu or menu item.  
* This property can be set via the constructor using the configuration 
* object literal.
* @default null
* @type YAHOO.widget.MenuItem
*/
parent: null,


/**
* @property srcElement
* @description Object reference to the HTML element (either 
* <code>&#60;select&#62;</code> or <code>&#60;div&#62;</code>) used to 
* create the menu.
* @default null
* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-94282980">HTMLSelectElement</a>|<a 
* href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.
* html#ID-22445964">HTMLDivElement</a>
*/
srcElement: null,



// Events


/**
* @event mouseOverEvent
* @description Fires when the mouse has entered the menu.  Passes back 
* the DOM Event object as an argument.
*/
mouseOverEvent: null,


/**
* @event mouseOutEvent
* @description Fires when the mouse has left the menu.  Passes back the DOM 
* Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
mouseOutEvent: null,


/**
* @event mouseDownEvent
* @description Fires when the user mouses down on the menu.  Passes back the 
* DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
mouseDownEvent: null,


/**
* @event mouseUpEvent
* @description Fires when the user releases a mouse button while the mouse is 
* over the menu.  Passes back the DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
mouseUpEvent: null,


/**
* @event clickEvent
* @description Fires when the user clicks the on the menu.  Passes back the 
* DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
clickEvent: null,


/**
* @event keyPressEvent
* @description Fires when the user presses an alphanumeric key when one of the
* menu's items has focus.  Passes back the DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
keyPressEvent: null,


/**
* @event keyDownEvent
* @description Fires when the user presses a key when one of the menu's items 
* has focus.  Passes back the DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
keyDownEvent: null,


/**
* @event keyUpEvent
* @description Fires when the user releases a key when one of the menu's items 
* has focus.  Passes back the DOM Event object as an argument.
* @type YAHOO.util.CustomEvent
*/
keyUpEvent: null,


/**
* @event itemAddedEvent
* @description Fires when an item is added to the menu.
* @type YAHOO.util.CustomEvent
*/
itemAddedEvent: null,


/**
* @event itemRemovedEvent
* @description Fires when an item is removed to the menu.
* @type YAHOO.util.CustomEvent
*/
itemRemovedEvent: null,


/**
* @method init
* @description The Menu class's initialization method. This method is 
* automatically called by the constructor, and sets up all DOM references 
* for pre-existing markup, and creates required markup if it is not 
* already present.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source 
* for the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement 
* Object specifying the <code>&#60;select&#62;</code> element to be used as 
* the data source for the menu.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu. See configuration class documentation for 
* more details.
*/
init: function(p_oElement, p_oConfig) {

    this._aItemGroups = [];
    this._aListElements = [];
    this._aGroupTitleElements = [];


    if(!this.ITEM_TYPE) {

        this.ITEM_TYPE = YAHOO.widget.MenuItem;

    }


    var oElement;

    if(typeof p_oElement == "string") {

        oElement = document.getElementById(p_oElement);

    }
    else if(p_oElement.tagName) {

        oElement = p_oElement;

    }


    if(oElement && oElement.tagName) {

        switch(oElement.tagName.toUpperCase()) {
    
            case "DIV":

                this.srcElement = oElement;

                if(!oElement.id) {

                    oElement.setAttribute("id", Dom.generateId());

                }


                /* 
                    Note: we don't pass the user config in here yet 
                    because we only want it executed once, at the lowest 
                    subclass level.
                */ 
            
                YAHOO.widget.Menu.superclass.init.call(this, oElement);

                this.beforeInitEvent.fire(YAHOO.widget.Menu);


    
            break;
    
            case "SELECT":
    
                this.srcElement = oElement;

    
                /*
                    The source element is not something that we can use 
                    outright, so we need to create a new Overlay

                    Note: we don't pass the user config in here yet 
                    because we only want it executed once, at the lowest 
                    subclass level.
                */ 

                YAHOO.widget.Menu.superclass.init.call(this, Dom.generateId());

                this.beforeInitEvent.fire(YAHOO.widget.Menu);



            break;

        }

    }
    else {

        /* 
            Note: we don't pass the user config in here yet 
            because we only want it executed once, at the lowest 
            subclass level.
        */ 
    
        YAHOO.widget.Menu.superclass.init.call(this, p_oElement);

        this.beforeInitEvent.fire(YAHOO.widget.Menu);



    }


    if(this.element) {

        var oEl = this.element;

        Dom.addClass(oEl, this.CSS_CLASS_NAME);


        // Subscribe to Custom Events

        this.initEvent.subscribe(this._onInit, this, true);
        this.beforeRenderEvent.subscribe(this._onBeforeRender, this, true);
        this.renderEvent.subscribe(this._onRender, this, true);
        this.beforeShowEvent.subscribe(this._onBeforeShow, this, true);
        this.showEvent.subscribe(this._onShow, this, true);
        this.beforeHideEvent.subscribe(this._onBeforeHide, this, true);
        this.mouseOverEvent.subscribe(this._onMouseOver, this, true);
        this.mouseOutEvent.subscribe(this._onMouseOut, this, true);
        this.clickEvent.subscribe(this._onClick, this, true);
        this.keyDownEvent.subscribe(this._onKeyDown, this, true);

        YAHOO.widget.Module.textResizeEvent.subscribe(
            this._onTextResize, 
            this, 
            true
        );


        if(p_oConfig) {
    
            this.cfg.applyConfig(p_oConfig, true);
    
        }


        // Register the Menu instance with the MenuManager

        YAHOO.widget.MenuManager.addMenu(this);
        

        this.initEvent.fire(YAHOO.widget.Menu);

    }

},



// Private methods


/**
* @method _initSubTree
* @description Iterates the childNodes of the source element to find nodes 
* used to instantiate menu and menu items.
* @private
*/
_initSubTree: function() {

    var oNode;

    if(this.srcElement.tagName == "DIV") {

        /*
            Populate the collection of item groups and item
            group titles
        */

        oNode = this.body.firstChild;

        var nGroup = 0,
            sGroupTitleTagName = this.GROUP_TITLE_TAG_NAME.toUpperCase();

        do {

            if(oNode && oNode.tagName) {

                switch(oNode.tagName.toUpperCase()) {

                    case sGroupTitleTagName:
                    
                        this._aGroupTitleElements[nGroup] = oNode;

                    break;

                    case "UL":

                        this._aListElements[nGroup] = oNode;
                        this._aItemGroups[nGroup] = [];
                        nGroup++;

                    break;

                }
            
            }

        }
        while((oNode = oNode.nextSibling));


        /*
            Apply the "first-of-type" class to the first UL to mimic 
            the "first-of-type" CSS3 psuedo class.
        */

        if(this._aListElements[0]) {

            Dom.addClass(this._aListElements[0], "first-of-type");

        }

    }


    oNode = null;


    if(this.srcElement.tagName) {

        var sSrcElementTagName = this.srcElement.tagName.toUpperCase();


        switch(sSrcElementTagName) {
    
            case "DIV":
    
                if(this._aListElements.length > 0) {
    
    
                    var i = this._aListElements.length - 1;
    
                    do {
    
                        oNode = this._aListElements[i].firstChild;
        
    
                        do {
        
                            if(
                                oNode && 
                                oNode.tagName && 
                                oNode.tagName.toUpperCase() == "LI"
                            ) {
        

                                this.addItem(
                                        new this.ITEM_TYPE(
                                            oNode, 
                                            { parent: this }
                                        ), 
                                        i
                                    );
    
                            }
                
                        }
                        while((oNode = oNode.nextSibling));
                
                    }
                    while(i--);
    
                }
    
            break;
    
            case "SELECT":
    
    
                oNode = this.srcElement.firstChild;
    
                do {
    
                    if(oNode && oNode.tagName) {
                    
                        switch(oNode.tagName.toUpperCase()) {
        
                            case "OPTGROUP":
                            case "OPTION":
        
        
                                this.addItem(
                                        new this.ITEM_TYPE(
                                                oNode, 
                                                { parent: this }
                                            )
                                        );
        
                            break;
        
                        }

                    }
    
                }
                while((oNode = oNode.nextSibling));
    
            break;
    
        }

    }

},


/**
* @method _getFirstEnabledItem
* @description Returns the first enabled item in the menu.
* @return {YAHOO.widget.MenuItem}
* @private
*/
_getFirstEnabledItem: function() {

    var nGroups = this._aItemGroups.length,
        oItem,
        aItemGroup;

    for(var i=0; i<nGroups; i++) {

        aItemGroup = this._aItemGroups[i];
        
        if(aItemGroup) {

            var nItems = aItemGroup.length;
            
            for(var n=0; n<nItems; n++) {
            
                oItem = aItemGroup[n];
                
                if(
                    !oItem.cfg.getProperty("disabled") && 
                    oItem.element.style.display != "none"
                ) {
                
                    return oItem;
                
                }
    
                oItem = null;
    
            }
        
        }
    
    }
    
},


/**
* @method _checkPosition
* @description Checks to make sure that the value of the "position" property 
* is one of the supported strings. Returns true if the position is supported.
* @private
* @param {Object} p_sPosition String specifying the position of the menu.
* @return {Boolean}
*/
_checkPosition: function(p_sPosition) {

    if(typeof p_sPosition == "string") {

        var sPosition = p_sPosition.toLowerCase();

        return ("dynamic,static".indexOf(sPosition) != -1);

    }

},


/**
* @method _addItemToGroup
* @description Adds a menu item to a group.
* @private
* @param {Number} p_nGroupIndex Number indicating the group to which the 
* item belongs.
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance to be added to the menu.
* @param {String} p_oItem String specifying the text of the item to be added 
* to the menu.
* @param {Object} p_oItem Object literal containing a set of menu item 
* configuration properties.
* @param {Number} p_nItemIndex Optional. Number indicating the index at 
* which the menu item should be added.
* @return {YAHOO.widget.MenuItem}
*/
_addItemToGroup: function(p_nGroupIndex, p_oItem, p_nItemIndex) {

    var oItem;

    if(p_oItem instanceof this.ITEM_TYPE) {

        oItem = p_oItem;
        oItem.parent = this;

    }
    else if(typeof p_oItem == "string") {

        oItem = new this.ITEM_TYPE(p_oItem, { parent: this });
    
    }
    else if(typeof p_oItem == "object") {

        p_oItem.parent = this;

        oItem = new this.ITEM_TYPE(p_oItem.text, p_oItem);

    }


    if(oItem) {

        var nGroupIndex = typeof p_nGroupIndex == "number" ? p_nGroupIndex : 0,
            aGroup = this._getItemGroup(nGroupIndex),
            oGroupItem;


        if(!aGroup) {

            aGroup = this._createItemGroup(nGroupIndex);

        }


        if(typeof p_nItemIndex == "number") {

            var bAppend = (p_nItemIndex >= aGroup.length);            


            if(aGroup[p_nItemIndex]) {
    
                aGroup.splice(p_nItemIndex, 0, oItem);
    
            }
            else {
    
                aGroup[p_nItemIndex] = oItem;
    
            }


            oGroupItem = aGroup[p_nItemIndex];

            if(oGroupItem) {

                if(
                    bAppend && 
                    (
                        !oGroupItem.element.parentNode || 
                        oGroupItem.element.parentNode.nodeType == 11
                    )
                ) {
        
                    this._aListElements[nGroupIndex].appendChild(
                        oGroupItem.element
                    );
    
                }
                else {
  
    
                    /**
                    * Returns the next sibling of an item in an array.
                    * @private
                    * @param {p_aArray} Array to search.
                    * @param {p_nStartIndex} Number indicating the index to 
                    * start searching the array.
                    * @return {Object}
                    */
                    function getNextItemSibling(p_aArray, p_nStartIndex) {
                
                            return (
                                    p_aArray[p_nStartIndex] || 
                                    getNextItemSibling(
                                        p_aArray, 
                                        (p_nStartIndex+1)
                                    )
                                );

                    }
    
    
                    var oNextItemSibling = 
                            getNextItemSibling(aGroup, (p_nItemIndex+1));
    
                    if(
                        oNextItemSibling && 
                        (
                            !oGroupItem.element.parentNode || 
                            oGroupItem.element.parentNode.nodeType == 11
                        )
                    ) {
            
                        this._aListElements[nGroupIndex].insertBefore(
                                oGroupItem.element, 
                                oNextItemSibling.element
                            );
        
                    }
    
                }
    

                oGroupItem.parent = this;
        
                this._subscribeToItemEvents(oGroupItem);
    
                this._configureSubmenu(oGroupItem);
                
                this._updateItemProperties(nGroupIndex);
        

                this.itemAddedEvent.fire(oGroupItem);

                return oGroupItem;
    
            }

        }
        else {
    
            var nItemIndex = aGroup.length;
    
            aGroup[nItemIndex] = oItem;

            oGroupItem = aGroup[nItemIndex];
    

            if(oGroupItem) {
    
                if(
                    !Dom.isAncestor(
                        this._aListElements[nGroupIndex], 
                        oGroupItem.element
                    )
                ) {
    
                    this._aListElements[nGroupIndex].appendChild(
                        oGroupItem.element
                    );
    
                }
    
                oGroupItem.element.setAttribute("groupindex", nGroupIndex);
                oGroupItem.element.setAttribute("index", nItemIndex);
        
                oGroupItem.parent = this;
    
                oGroupItem.index = nItemIndex;
                oGroupItem.groupIndex = nGroupIndex;
        
                this._subscribeToItemEvents(oGroupItem);
    
                this._configureSubmenu(oGroupItem);
    
                if(nItemIndex === 0) {
        
                    Dom.addClass(oGroupItem.element, "first-of-type");
        
                }

        

                this.itemAddedEvent.fire(oGroupItem);

                return oGroupItem;
    
            }
    
        }

    }
    
},


/**
* @method _removeItemFromGroupByIndex
* @description Removes a menu item from a group by index.  Returns the menu 
* item that was removed.
* @private
* @param {Number} p_nGroupIndex Number indicating the group to which the menu 
* item belongs.
* @param {Number} p_nItemIndex Number indicating the index of the menu item 
* to be removed.
* @return {YAHOO.widget.MenuItem}
*/    
_removeItemFromGroupByIndex: function(p_nGroupIndex, p_nItemIndex) {

    var nGroupIndex = typeof p_nGroupIndex == "number" ? p_nGroupIndex : 0,
        aGroup = this._getItemGroup(nGroupIndex);

    if(aGroup) {

        var aArray = aGroup.splice(p_nItemIndex, 1),
            oItem = aArray[0];
    
        if(oItem) {
    
            // Update the index and className properties of each member        
            
            this._updateItemProperties(nGroupIndex);
    
            if(aGroup.length === 0) {
    
                // Remove the UL
    
                var oUL = this._aListElements[nGroupIndex];
    
                if(this.body && oUL) {
    
                    this.body.removeChild(oUL);
    
                }
    
                // Remove the group from the array of items
    
                this._aItemGroups.splice(nGroupIndex, 1);
    
    
                // Remove the UL from the array of ULs
    
                this._aListElements.splice(nGroupIndex, 1);
    
    
                /*
                     Assign the "first-of-type" class to the new first UL 
                     in the collection
                */
    
                oUL = this._aListElements[0];
    
                if(oUL) {
    
                    Dom.addClass(oUL, "first-of-type");
    
                }            
    
            }
    

            this.itemRemovedEvent.fire(oItem);    


            // Return a reference to the item that was removed
        
            return oItem;
    
        }

    }
    
},


/**
* @method _removeItemFromGroupByValue
* @description Removes a menu item from a group by reference.  Returns the 
* menu item that was removed.
* @private
* @param {Number} p_nGroupIndex Number indicating the group to which the
* menu item belongs.
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance to be removed.
* @return {YAHOO.widget.MenuItem}
*/    
_removeItemFromGroupByValue: function(p_nGroupIndex, p_oItem) {

    var aGroup = this._getItemGroup(p_nGroupIndex);

    if(aGroup) {

        var nItems = aGroup.length,
            nItemIndex = -1;
    
        if(nItems > 0) {
    
            var i = nItems-1;
        
            do {
        
                if(aGroup[i] == p_oItem) {
        
                    nItemIndex = i;
                    break;    
        
                }
        
            }
            while(i--);
        
            if(nItemIndex > -1) {
        
                return this._removeItemFromGroupByIndex(
                            p_nGroupIndex, 
                            nItemIndex
                        );
        
            }
    
        }
    
    }

},


/**
* @method _updateItemProperties
* @description Updates the "index," "groupindex," and "className" properties 
* of the menu items in the specified group. 
* @private
* @param {Number} p_nGroupIndex Number indicating the group of items to update.
*/
_updateItemProperties: function(p_nGroupIndex) {

    var aGroup = this._getItemGroup(p_nGroupIndex),
        nItems = aGroup.length;

    if(nItems > 0) {

        var i = nItems - 1,
            oItem,
            oLI;

        // Update the index and className properties of each member
    
        do {

            oItem = aGroup[i];

            if(oItem) {
    
                oLI = oItem.element;

                oItem.index = i;
                oItem.groupIndex = p_nGroupIndex;

                oLI.setAttribute("groupindex", p_nGroupIndex);
                oLI.setAttribute("index", i);

                Dom.removeClass(oLI, "first-of-type");

            }
    
        }
        while(i--);


        if(oLI) {

            Dom.addClass(oLI, "first-of-type");

        }

    }

},


/**
* @method _createItemGroup
* @description Creates a new menu item group (array) and its associated 
* <code>&#60;ul&#62;</code> element. Returns an aray of menu item groups.
* @private
* @param {Number} p_nIndex Number indicating the group to create.
* @return {Array}
*/
_createItemGroup: function(p_nIndex) {

    if(!this._aItemGroups[p_nIndex]) {

        this._aItemGroups[p_nIndex] = [];

        var oUL = document.createElement("ul");

        this._aListElements[p_nIndex] = oUL;

        return this._aItemGroups[p_nIndex];

    }

},


/**
* @method _getItemGroup
* @description Returns the menu item group at the specified index.
* @private
* @param {Number} p_nIndex Number indicating the index of the menu item group 
* to be retrieved.
* @return {Array}
*/
_getItemGroup: function(p_nIndex) {

    var nIndex = ((typeof p_nIndex == "number") ? p_nIndex : 0);

    return this._aItemGroups[nIndex];

},


/**
* @method _configureSubmenu
* @description Subscribes the menu item's submenu to its parent menu's events.
* @private
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance with the submenu to be configured.
*/
_configureSubmenu: function(p_oItem) {

    var oSubmenu = p_oItem.cfg.getProperty("submenu");

    if(oSubmenu) {
            
        /*
            Listen for configuration changes to the parent menu 
            so they they can be applied to the submenu.
        */

        this.cfg.configChangedEvent.subscribe(
                this._onParentMenuConfigChange, 
                oSubmenu, 
                true
            );

        this.renderEvent.subscribe(
                this._onParentMenuRender,
                oSubmenu, 
                true
            );

        oSubmenu.beforeShowEvent.subscribe(
                this._onSubmenuBeforeShow, 
                oSubmenu, 
                true
            );

        oSubmenu.showEvent.subscribe(
                this._onSubmenuShow, 
                oSubmenu, 
                true
            );

        oSubmenu.hideEvent.subscribe(
                this._onSubmenuHide, 
                oSubmenu, 
                true
            );

    }

},


/**
* @method _subscribeToItemEvents
* @description Subscribes a menu to a menu item's event.
* @private
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance whose events should be subscribed to.
*/
_subscribeToItemEvents: function(p_oItem) {

    p_oItem.focusEvent.subscribe(this._onMenuItemFocus, p_oItem, this);

    p_oItem.blurEvent.subscribe(this._onMenuItemBlur, this, true);

    p_oItem.cfg.configChangedEvent.subscribe(
        this._onMenuItemConfigChange,
        p_oItem,
        this
    );

},


/**
* @method _getOffsetWidth
* @description Returns the offset width of the menu's 
* <code>&#60;div&#62;</code> element.
* @private
*/
_getOffsetWidth: function() {

    var oClone = this.element.cloneNode(true);

    Dom.setStyle(oClone, "width", "");

    document.body.appendChild(oClone);

    var sWidth = oClone.offsetWidth;

    document.body.removeChild(oClone);

    return sWidth;

},


/**
* @method _cancelHideDelay
* @description Cancels the call to "hideMenu."
* @private
*/
_cancelHideDelay: function() {

    var oRoot = this.getRoot();

    if(oRoot._nHideDelayId) {

        window.clearTimeout(oRoot._nHideDelayId);

    }

},


/**
* @method _execHideDelay
* @description Hides the menu after the number of milliseconds specified by 
* the "hidedelay" configuration property.
* @private
*/
_execHideDelay: function() {

    this._cancelHideDelay();

    var oRoot = this.getRoot(),
        me = this;

    function hideMenu() {
    
        if(oRoot.activeItem) {

            oRoot.clearActiveItem();

        }

        if(oRoot == me && me.cfg.getProperty("position") == "dynamic") {

            me.hide();            
        
        }
    
    }


    oRoot._nHideDelayId = 
        window.setTimeout(hideMenu, oRoot.cfg.getProperty("hidedelay"));

},


/**
* @method _cancelShowDelay
* @description Cancels the call to the "showMenu."
* @private
*/
_cancelShowDelay: function() {

    var oRoot = this.getRoot();

    if(oRoot._nShowDelayId) {

        window.clearTimeout(oRoot._nShowDelayId);

    }

},


/**
* @method _execShowDelay
* @description Shows the menu after the number of milliseconds specified by 
* the "showdelay" configuration property have ellapsed.
* @private
* @param {YAHOO.widget.Menu} p_oMenu Object specifying the menu that should 
* be made visible.
*/
_execShowDelay: function(p_oMenu) {

    var oRoot = this.getRoot();

    function showMenu() {

        p_oMenu.show();    
    
    }


    oRoot._nShowDelayId = 
        window.setTimeout(showMenu, oRoot.cfg.getProperty("showdelay"));

},



// Protected methods


/**
* @method _onMouseOver
* @description "mouseover" event handler for the menu.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onMouseOver: function(p_sType, p_aArgs, p_oMenu) {

    var oEvent = p_aArgs[0],
        oItem = p_aArgs[1],
        oTarget = Event.getTarget(oEvent);


    if(
        !this._bHandledMouseOverEvent && 
        (oTarget == this.element || Dom.isAncestor(this.element, oTarget))
    ) {

        this.clearActiveItem();

        this._bHandledMouseOverEvent = true;
        this._bHandledMouseOutEvent = false;
    
    }


    if(
        oItem && !oItem.handledMouseOverEvent && 
        !oItem.cfg.getProperty("disabled") && 
        (oTarget == oItem.element || Dom.isAncestor(oItem.element, oTarget))
    ) {

        var nShowDelay = this.cfg.getProperty("showdelay"),
            bShowDelay = (nShowDelay > 0);


        if(bShowDelay) {
        
            this._cancelShowDelay();
        
        }
    
    
        var oActiveItem = this.activeItem;
    
        if(oActiveItem) {
    
            oActiveItem.cfg.setProperty("selected", false);
    
            var oActiveSubmenu = oActiveItem.cfg.getProperty("submenu");
    
            if(oActiveSubmenu) {
						
                oActiveSubmenu.hide();
    
            }
    
        }


        var oItemCfg = oItem.cfg;
    
        // Select and focus the current menu item
    
        oItemCfg.setProperty("selected", true);
        oItem.focus();


        if(this.cfg.getProperty("autosubmenudisplay")) {

            // Show the submenu this menu item

            var oSubmenu = oItemCfg.getProperty("submenu");
        
            if(oSubmenu) {
        
                if(bShowDelay) {

                    this._execShowDelay(oSubmenu);
        
                }
                else {

                    oSubmenu.show();

                }
        
            }

        }                        

        oItem.handledMouseOverEvent = true;
        oItem.handledMouseOutEvent = false;

    }

},


/**
* @method _onMouseOut
* @description "mouseout" event handler for the menu.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onMouseOut: function(p_sType, p_aArgs, p_oMenu) {
    
    var oEvent = p_aArgs[0],
        oItem = p_aArgs[1],
        oRelatedTarget = Event.getRelatedTarget(oEvent),
        bMovingToSubmenu = false;


    if(oItem && !oItem.cfg.getProperty("disabled")) {

        var oItemCfg = oItem.cfg,
            oSubmenu = oItemCfg.getProperty("submenu");


        if(
            oSubmenu && 
            (
                oRelatedTarget == oSubmenu.element ||
                Dom.isAncestor(oSubmenu.element, oRelatedTarget)
            )
        ) {

            bMovingToSubmenu = true;

        }


        if( 
            !oItem.handledMouseOutEvent && 
            (
                (
                    oRelatedTarget != oItem.element &&  
                    !Dom.isAncestor(oItem.element, oRelatedTarget)
                ) || bMovingToSubmenu
            )
        ) {

            if(
                !oSubmenu || 
                (oSubmenu && !oSubmenu.cfg.getProperty("visible"))
            ) {

                oItem.cfg.setProperty("selected", false);

                if(
                    oSubmenu && 
                    oSubmenu.cfg.getProperty("showdelay") && 
                    !oSubmenu.cfg.getProperty("visible")
                ) {
                
                     this._cancelShowDelay();
                
                }

            }


            oItem.handledMouseOutEvent = true;
            oItem.handledMouseOverEvent = false;
    
        }

    }


    if(
        !this._bHandledMouseOutEvent && 
        (
            (
                oRelatedTarget != this.element &&  
                !Dom.isAncestor(this.element, oRelatedTarget)
            ) 
            || bMovingToSubmenu
        )
    ) {

        this._bHandledMouseOutEvent = true;
        this._bHandledMouseOverEvent = false;

    }

},


/**
* @method _onClick
* @description "click" event handler for the menu.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onClick: function(p_sType, p_aArgs, p_oMenu) {

    var oEvent = p_aArgs[0],
        oItem = p_aArgs[1],
        oTarget = Event.getTarget(oEvent);

    if(oItem && !oItem.cfg.getProperty("disabled")) {

        var oItemCfg = oItem.cfg,
            oSubmenu = oItemCfg.getProperty("submenu");


        /*
            ACCESSIBILITY FEATURE FOR SCREEN READERS: 
            Expand/collapse the submenu when the user clicks 
            on the submenu indicator image.
        */        

        if(oTarget == oItem.submenuIndicator && oSubmenu) {

            if(oSubmenu.cfg.getProperty("visible")) {

                oSubmenu.hide();
    
            }
            else {

                this.clearActiveItem();

                this.activeItem = oItem;

                oItem.cfg.setProperty("selected", true);

                oSubmenu.show();
    
            }
    
        }
        else {

            var sURL = oItemCfg.getProperty("url"),
                bCurrentPageURL = (sURL.substr((sURL.length-1),1) == "#"),
                sTarget = oItemCfg.getProperty("target"),
                bHasTarget = (sTarget && sTarget.length > 0);

            /*
                Prevent the browser from following links 
                equal to "#"
            */
            
            if(
                oTarget.tagName.toUpperCase() == "A" && 
                bCurrentPageURL && !bHasTarget
            ) {

                Event.preventDefault(oEvent);
            
            }

            if(
                oTarget.tagName.toUpperCase() != "A" && 
                !bCurrentPageURL && !bHasTarget
            ) {
                
                /*
                    Follow the URL of the item regardless of 
                    whether or not the user clicked specifically
                    on the anchor element.
                */
    
                document.location = sURL;
        
            }


            /*
                If the item doesn't navigate to a URL and it doesn't have
                a submenu, then collapse the menu tree.
            */

            if(bCurrentPageURL && !oSubmenu) {
    
                var oRoot = this.getRoot();
                
                if(oRoot.cfg.getProperty("position") == "static") {
    
                    oRoot.clearActiveItem();
    
                }
                else {
    
                    oRoot.hide();
                
                }
    
            }

        }                    
    
    }

},


/**
* @method _onKeyDown
* @description "keydown" event handler for the menu.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onKeyDown: function(p_sType, p_aArgs, p_oMenu) {

    var oEvent = p_aArgs[0],
        oItem = p_aArgs[1],
        oSubmenu;

    if(oItem && !oItem.cfg.getProperty("disabled")) {

        var oItemCfg = oItem.cfg,
            oParentItem = this.parent,
            oRoot,
            oNextItem;


        switch(oEvent.keyCode) {
    
            case 38:    // Up arrow
            case 40:    // Down arrow
    
                if(
                    oItem == this.activeItem && 
                    !oItemCfg.getProperty("selected")
                ) {
    
                    oItemCfg.setProperty("selected", true);
    
                }
                else {
    
                    oNextItem = (oEvent.keyCode == 38) ? 
                        oItem.getPreviousEnabledSibling() : 
                        oItem.getNextEnabledSibling();
            
                    if(oNextItem) {
    
                        this.clearActiveItem();
    
                        oNextItem.cfg.setProperty("selected", true);
                        oNextItem.focus();

                    }
    
                }
    
                Event.preventDefault(oEvent);
    
            break;
            
    
            case 39:    // Right arrow
    
                oSubmenu = oItemCfg.getProperty("submenu");
    
                if(oSubmenu) {
    
                    if(!oItemCfg.getProperty("selected")) {
        
                        oItemCfg.setProperty("selected", true);
        
                    }
    
                    oSubmenu.show();
    
                    oSubmenu.setInitialSelection();
    
                }
                else {
    
                    oRoot = this.getRoot();
                    
                    if(oRoot instanceof YAHOO.widget.MenuBar) {
    
                        oNextItem = oRoot.activeItem.getNextEnabledSibling();
    
                        if(oNextItem) {
                        
                            oRoot.clearActiveItem();
    
                            oNextItem.cfg.setProperty("selected", true);
    
                            oSubmenu = oNextItem.cfg.getProperty("submenu");
    
                            if(oSubmenu) {
    
                                oSubmenu.show();
                            
                            }
    
                            oNextItem.focus();
                        
                        }
                    
                    }
                
                }
    
    
                Event.preventDefault(oEvent);
    
            break;
    
    
            case 37:    // Left arrow
    
                if(oParentItem) {
    
                    var oParentMenu = oParentItem.parent;
    
                    if(oParentMenu instanceof YAHOO.widget.MenuBar) {
    
                        oNextItem = 
                            oParentMenu.activeItem.getPreviousEnabledSibling();
    
                        if(oNextItem) {
                        
                            oParentMenu.clearActiveItem();
    
                            oNextItem.cfg.setProperty("selected", true);
    
                            oSubmenu = oNextItem.cfg.getProperty("submenu");
    
                            if(oSubmenu) {
                            
                                oSubmenu.show();
                            
                            }
    
                            oNextItem.focus();
                        
                        } 
                    
                    }
                    else {
    
                        this.hide();
    
                        oParentItem.focus();
                    
                    }
    
                }
    
                Event.preventDefault(oEvent);
    
            break;        
    
        }


    }


    if(oEvent.keyCode == 27) { // Esc key

        if(this.cfg.getProperty("position") == "dynamic") {
        
            this.hide();

            if(this.parent) {

                this.parent.focus();
            
            }

        }
        else if(this.activeItem) {

            oSubmenu = this.activeItem.cfg.getProperty("submenu");

            if(oSubmenu && oSubmenu.cfg.getProperty("visible")) {
            
                oSubmenu.hide();
                this.activeItem.focus();
            
            }
            else {

                this.activeItem.cfg.setProperty("selected", false);
                this.activeItem.blur();
        
            }
        
        }


        Event.preventDefault(oEvent);
    
    }
    
},


/**
* @method _onTextResize
* @description "textresize" event handler for the menu.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onTextResize: function(p_sType, p_aArgs, p_oMenu) {

    if(this.browser == "gecko" && !this._handleResize) {

        this._handleResize = true;
        return;
    
    }


    var oConfig = this.cfg;

    if(oConfig.getProperty("position") == "dynamic") {

        oConfig.setProperty("width", (this._getOffsetWidth() + "px"));

    }

},



// Private methods


/**
* @method _onInit
* @description "init" event handler for the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onInit: function(p_sType, p_aArgs, p_oMenu) {

    if(
        (
            (this.parent && !this.lazyLoad) || 
            (!this.parent && this.cfg.getProperty("position") == "static") ||
            (
                !this.parent && 
                !this.lazyLoad && 
                this.cfg.getProperty("position") == "dynamic"
            ) 
        ) && 
        this.getItemGroups().length === 0
    ) {
 
        if(this.srcElement) {

            this._initSubTree();
        
        }


        if(this.itemData) {

            this.addItems(this.itemData);

        }
    
    }
    else if(this.lazyLoad) {

        this.cfg.fireQueue();
    
    }

},


/**
* @method _onBeforeRender
* @description "beforerender" event handler for the menu.  Appends all of the 
* <code>&#60;ul&#62;</code>, <code>&#60;li&#62;</code> and their accompanying 
* title elements to the body element of the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onBeforeRender: function(p_sType, p_aArgs, p_oMenu) {

    var oConfig = this.cfg,
        oEl = this.element,
        nListElements = this._aListElements.length;


    if(nListElements > 0) {

        var i = 0,
            bFirstList = true,
            oUL,
            oGroupTitle;


        do {

            oUL = this._aListElements[i];

            if(oUL) {

                if(bFirstList) {
        
                    Dom.addClass(oUL, "first-of-type");
                    bFirstList = false;
        
                }


                if(!Dom.isAncestor(oEl, oUL)) {

                    this.appendToBody(oUL);

                }


                oGroupTitle = this._aGroupTitleElements[i];

                if(oGroupTitle) {

                    if(!Dom.isAncestor(oEl, oGroupTitle)) {

                        oUL.parentNode.insertBefore(oGroupTitle, oUL);

                    }


                    Dom.addClass(oUL, "hastitle");

                }

            }

            i++;

        }
        while(i < nListElements);

    }

},


/**
* @method _onRender
* @description "render" event handler for the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onRender: function(p_sType, p_aArgs, p_oMenu) {

    if(this.cfg.getProperty("position") == "dynamic") {

        var sWidth = 
            this.element.parentNode.tagName.toUpperCase() == "BODY" ? 
            this.element.offsetWidth : this._getOffsetWidth();
    
        this.cfg.setProperty("width", (sWidth + "px"));

    }

},


/**
* @method _onBeforeShow
* @description "beforeshow" event handler for the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
_onBeforeShow: function(p_sType, p_aArgs, p_oMenu) {
    
    if(this.lazyLoad && this.getItemGroups().length === 0) {

        if(this.srcElement) {
        
            this._initSubTree();

        }


        if(this.itemData) {

            if(
                this.parent && this.parent.parent && 
                this.parent.parent.srcElement && 
                this.parent.parent.srcElement.tagName.toUpperCase() == "SELECT"
            ) {

                var nOptions = this.itemData.length;
    
                for(var n=0; n<nOptions; n++) {

                    if(this.itemData[n].tagName) {

                        this.addItem((new this.ITEM_TYPE(this.itemData[n])));
    
                    }
    
                }
            
            }
            else {

                this.addItems(this.itemData);
            
            }
        
        }


        if(this.srcElement) {

            this.render();

        }
        else {

            if(this.parent) {

                this.render(this.parent.element);            

            }
            else {

                this.render(this.cfg.getProperty("container"));
                
            }                

        }

    }
    
},


/**
* @method _onShow
* @description "show" event handler for the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that fired 
* the event.
*/
_onShow: function(p_sType, p_aArgs, p_oMenu) {

    this.setInitialFocus();
    
    var oParent = this.parent;
    
    if(oParent) {

        var oParentMenu = oParent.parent,
            aParentAlignment = oParentMenu.cfg.getProperty("submenualignment"),
            aAlignment = this.cfg.getProperty("submenualignment");


        if(
            (aParentAlignment[0] != aAlignment[0]) &&
            (aParentAlignment[1] != aAlignment[1])
        ) {

            this.cfg.setProperty(
                "submenualignment", 
                [ aParentAlignment[0], aParentAlignment[1] ]
            );
        
        }


        if(
            !oParentMenu.cfg.getProperty("autosubmenudisplay") && 
            oParentMenu.cfg.getProperty("position") == "static"
        ) {

            oParentMenu.cfg.setProperty("autosubmenudisplay", true);


            /**
            * "click" event handler for the document
            * @private
            * @param {Event} p_oEvent Object reference for the DOM event object 
            * passed back by the event utility (YAHOO.util.Event).
            */
            function disableAutoSubmenuDisplay(p_oEvent) {

                if(
                    p_oEvent.type == "mousedown" || 
                    (p_oEvent.type == "keydown" && p_oEvent.keyCode == 27)
                ) {

                    /*  
                        Set the "autosubmenudisplay" to "false" if the user
                        clicks outside the menu bar.
                    */

                    var oTarget = Event.getTarget(p_oEvent);

                    if(
                        oTarget != oParentMenu.element || 
                        !YAHOO.util.Dom.isAncestor(oParentMenu.element, oTarget)
                    ) {

                        oParentMenu.cfg.setProperty(
                            "autosubmenudisplay", 
                            false
                        );

                        Event.removeListener(
                                document, 
                                "mousedown", 
                                disableAutoSubmenuDisplay
                            );

                        Event.removeListener(
                                document, 
                                "keydown", 
                                disableAutoSubmenuDisplay
                            );

                    }
                
                }

            }

            Event.addListener(document, "mousedown", disableAutoSubmenuDisplay);                             
            Event.addListener(document, "keydown", disableAutoSubmenuDisplay);

        }

    }

},


/**
* @method _onBeforeHide
* @description "beforehide" event handler for the menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that fired 
* the event.
*/
_onBeforeHide: function(p_sType, p_aArgs, p_oMenu) {

    var oActiveItem = this.activeItem;

    if(oActiveItem) {

        var oConfig = oActiveItem.cfg;

        oConfig.setProperty("selected", false);

        var oSubmenu = oConfig.getProperty("submenu");

        if(oSubmenu) {

            oSubmenu.hide();

        }

        oActiveItem.blur();

    }

},


/**
* @method _onParentMenuConfigChange
* @description "configchange" event handler for a submenu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oSubmenu Object representing the submenu that 
* subscribed to the event.
*/
_onParentMenuConfigChange: function(p_sType, p_aArgs, p_oSubmenu) {
    
    var sPropertyName = p_aArgs[0][0],
        oPropertyValue = p_aArgs[0][1];

    switch(sPropertyName) {

        case "iframe":
        case "constraintoviewport":
        case "hidedelay":
        case "showdelay":
        case "clicktohide":
        case "effect":

            p_oSubmenu.cfg.setProperty(sPropertyName, oPropertyValue);
                
        break;        
        
    }
    
},


/**
* @method _onParentMenuRender
* @description "render" event handler for a submenu.  Renders a  
* submenu in response to the firing of its parent's "render" event.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oSubmenu Object representing the submenu that 
* subscribed to the event.
*/
_onParentMenuRender: function(p_sType, p_aArgs, p_oSubmenu) {

    var oParentMenu = p_oSubmenu.parent.parent,

        oConfig = {

            constraintoviewport: 
                oParentMenu.cfg.getProperty("constraintoviewport"),

            xy: [0,0],
                
            clicktohide:
                oParentMenu.cfg.getProperty("clicktohide"),
                
            effect:
                oParentMenu.cfg.getProperty("effect"),

            showdelay:
                oParentMenu.cfg.getProperty("showdelay"),
            
            hidedelay:
                oParentMenu.cfg.getProperty("hidedelay")

        };


    /*
        Only sync the "iframe" configuration property if the parent
        menu's "position" configuration is the same.
    */

    if(
        this.cfg.getProperty("position") == 
        oParentMenu.cfg.getProperty("position")
    ) {

        oConfig.iframe = oParentMenu.cfg.getProperty("iframe");
    
    }
               

    p_oSubmenu.cfg.applyConfig(oConfig);


    if(!this.lazyLoad) {

        if(Dom.inDocument(this.element)) {
    
            this.render();
    
        }
        else {

            this.render(this.parent.element);
    
        }

    }
    
},


/**
* @method _onSubmenuBeforeShow
* @description "beforeshow" event handler for a submenu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oSubmenu Object representing the submenu that 
* subscribed to the event.
*/
_onSubmenuBeforeShow: function(p_sType, p_aArgs, p_oSubmenu) {
    
    var oParent = this.parent,
        aAlignment = oParent.parent.cfg.getProperty("submenualignment");

    this.cfg.setProperty(
        "context", 
        [oParent.element, aAlignment[0], aAlignment[1]]
    );
    
},


/**
* @method _onSubmenuShow
* @description "show" event handler for a submenu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oSubmenu Object representing the submenu that 
* subscribed to the event.
*/
_onSubmenuShow: function(p_sType, p_aArgs, p_oSubmenu) {
    
    var oParent = this.parent;

    oParent.submenuIndicator.alt = oParent.EXPANDED_SUBMENU_INDICATOR_ALT_TEXT;

},


/**
* @method _onSubmenuHide
* @description "hide" Custom Event handler for a submenu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oSubmenu Object representing the submenu that 
* subscribed to the event.
*/
_onSubmenuHide: function(p_sType, p_aArgs, p_oSubmenu) {
    
    var oParent = this.parent;

    oParent.submenuIndicator.alt = oParent.COLLAPSED_SUBMENU_INDICATOR_ALT_TEXT;

},


/**
* @method _onMenuItemFocus
* @description "focus" event handler for the menu's items.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item 
* that fired the event.
*/
_onMenuItemFocus: function(p_sType, p_aArgs, p_oItem) {

    this.activeItem = p_oItem;

},


/**
* @method _onMenuItemBlur
* @description "blur" event handler for the menu's items.
* @private
* @param {String} p_sType String representing the name of the event 
* that was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuItemBlur: function(p_sType, p_aArgs) {

    this.activeItem = null;

},


/**
* @method _onMenuItemConfigChange
* @description "configchange" event handler for the menu's items.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item 
* that fired the event.
*/
_onMenuItemConfigChange: function(p_sType, p_aArgs, p_oItem) {

    var sProperty = p_aArgs[0][0];

    switch(sProperty) {

        case "submenu":

            var oSubmenu = p_aArgs[0][1];

            if(oSubmenu) {

                this._configureSubmenu(p_oItem);

            }

        break;

        case "text":
        case "helptext":

            /*
                A change to an item's "text" or "helptext"
                configuration properties requires the width of the parent
                menu to be recalculated.
            */

            if(this.element.style.width) {
    
                var sWidth = this._getOffsetWidth() + "px";

                Dom.setStyle(this.element, "width", sWidth);

            }

        break;

    }

},



// Public event handlers for configuration properties


/**
* @method enforceConstraints
* @description The default event handler executed when the moveEvent is fired,  
* if the "constraintoviewport" configuration property is set to true.
* @param {String} type The name of the event that was fired.
* @param {Array} args Collection of arguments sent when the 
* event was fired.
* @param {Array} obj Array containing the current Menu instance 
* and the item that fired the event.
*/
enforceConstraints: function(type, args, obj) {

     var oConfig = this.cfg,
         pos = args[0],
    
         x = pos[0],
         y = pos[1],
    
         offsetHeight = this.element.offsetHeight,
         offsetWidth = this.element.offsetWidth,
    
         viewPortWidth = YAHOO.util.Dom.getViewportWidth(),
         viewPortHeight = YAHOO.util.Dom.getViewportHeight(),
    
         scrollX = Math.max(
                        document.documentElement.scrollLeft, 
                        document.body.scrollLeft
                    ),

         scrollY = Math.max(
                        document.documentElement.scrollTop, 
                        document.body.scrollTop
                    ),
    
         topConstraint = scrollY + 10,
         leftConstraint = scrollX + 10,
         bottomConstraint = scrollY + viewPortHeight - offsetHeight - 10,
         rightConstraint = scrollX + viewPortWidth - offsetWidth - 10,
    
         aContext = oConfig.getProperty("context"),
         oContextElement = aContext ? aContext[0] : null;


    if (x < 10) {

        x = leftConstraint;

    } else if ((x + offsetWidth) > viewPortWidth) {

        if(
            oContextElement &&
            ((x - oContextElement.offsetWidth) > offsetWidth)
        ) {

            x = (x - (oContextElement.offsetWidth + offsetWidth));

        }
        else {

            x = rightConstraint;

        }

    }

    if (y < 10) {

        y = topConstraint;

    } else if (y > bottomConstraint) {

        if(oContextElement && (y > offsetHeight)) {

            y = ((y + oContextElement.offsetHeight) - offsetHeight);

        }
        else {

            y = bottomConstraint;

        }

    }

    oConfig.setProperty("x", x, true);
    oConfig.setProperty("y", y, true);
    oConfig.setProperty("xy", [x,y], true);

},


/**
* @method configVisible
* @description Event handler for when the "visible" configuration property 
* the menu changes.
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
configVisible: function(p_sType, p_aArgs, p_oMenu) {

    if(this.cfg.getProperty("position") == "dynamic") {

        YAHOO.widget.Menu.superclass.configVisible.call(
            this, 
            p_sType, 
            p_aArgs, 
            p_oMenu
        );

    }
    else {

        var bVisible = p_aArgs[0],
    	    sDisplay = Dom.getStyle(this.element, "display");

        if(bVisible) {

            if(sDisplay != "block") {
                this.beforeShowEvent.fire();
                Dom.setStyle(this.element, "display", "block");
                this.showEvent.fire();
            }
        
        }
        else {

			if(sDisplay == "block") {
				this.beforeHideEvent.fire();
				Dom.setStyle(this.element, "display", "none");
				this.hideEvent.fire();
			}
        
        }

    }

},


/**
* @method configPosition
* @description Event handler for when the "position" configuration property 
* of the menu changes.
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
configPosition: function(p_sType, p_aArgs, p_oMenu) {

    var sCSSPosition = p_aArgs[0] == "static" ? "static" : "absolute",
        oCfg = this.cfg;

    Dom.setStyle(this.element, "position", sCSSPosition);


    if(sCSSPosition == "static") {

        /*
            Remove the iframe for statically positioned menus since it will 
            intercept mouse events.
        */

        oCfg.setProperty("iframe", false);


        // Statically positioned menus are visible by default
        
        Dom.setStyle(this.element, "display", "block");

        oCfg.setProperty("visible", true);

    }
    else {

        /*
            Even though the "visible" property is queued to 
            "false" by default, we need to set the "visibility" property to 
            "hidden" since Overlay's "configVisible" implementation checks the 
            element's "visibility" style property before deciding whether 
            or not to show an Overlay instance.
        */

        Dom.setStyle(this.element, "visibility", "hidden");
    
    }


    if(sCSSPosition == "absolute") {

        var nZIndex = oCfg.getProperty("zindex");

        if(!nZIndex || nZIndex === 0) {

            nZIndex = this.parent ? 
                (this.parent.parent.cfg.getProperty("zindex") + 1) : 1;

            oCfg.setProperty("zindex", nZIndex);

        }

    }

},


/**
* @method configIframe
* @description Event handler for when the "iframe" configuration property of 
* the menu changes.
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
configIframe: function(p_sType, p_aArgs, p_oMenu) {    

    if(this.cfg.getProperty("position") == "dynamic") {

        YAHOO.widget.Menu.superclass.configIframe.call(
            this, 
            p_sType, 
            p_aArgs, 
            p_oMenu
        );

    }

},


/**
* @method configHideDelay
* @description Event handler for when the "hidedelay" configuration property 
* of the menu changes.
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
configHideDelay: function(p_sType, p_aArgs, p_oMenu) {

    var nHideDelay = p_aArgs[0],
        oMouseOutEvent = this.mouseOutEvent,
        oMouseOverEvent = this.mouseOverEvent,
        oKeyDownEvent = this.keyDownEvent;

    if(nHideDelay > 0) {

        /*
            Only assign event handlers once. This way the user change 
            the value for the hidedelay as many times as they want.
        */

        if(!this._hideDelayEventHandlersAssigned) {

            oMouseOutEvent.subscribe(this._execHideDelay, true);
            oMouseOverEvent.subscribe(this._cancelHideDelay, this, true);
            oKeyDownEvent.subscribe(this._cancelHideDelay, this, true);

            this._hideDelayEventHandlersAssigned = true;
        
        }

    }
    else {

        oMouseOutEvent.unsubscribe(this._execHideDelay, this);
        oMouseOverEvent.unsubscribe(this._cancelHideDelay, this);
        oKeyDownEvent.unsubscribe(this._cancelHideDelay, this);

        this._hideDelayEventHandlersAssigned = false;

    }

},


/**
* @method configContainer
* @description Event handler for when the "container" configuration property 
of the menu changes.
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.Menu} p_oMenu Object representing the menu that 
* fired the event.
*/
configContainer: function(p_sType, p_aArgs, p_oMenu) {

	var oElement = p_aArgs[0];

	if(typeof oElement == 'string') {

        this.cfg.setProperty(
                "container", 
                document.getElementById(oElement), 
                true
            );

	}

},



// Public methods


/**
* @method initEvents
* @description Initializes the custom events for the menu.
*/
initEvents: function() {

	YAHOO.widget.Menu.superclass.initEvents.call(this);

    // Create custom events

    var CustomEvent = YAHOO.util.CustomEvent;

    this.mouseOverEvent = new CustomEvent("mouseOverEvent", this);
    this.mouseOutEvent = new CustomEvent("mouseOutEvent", this);
    this.mouseDownEvent = new CustomEvent("mouseDownEvent", this);
    this.mouseUpEvent = new CustomEvent("mouseUpEvent", this);
    this.clickEvent = new CustomEvent("clickEvent", this);
    this.keyPressEvent = new CustomEvent("keyPressEvent", this);
    this.keyDownEvent = new CustomEvent("keyDownEvent", this);
    this.keyUpEvent = new CustomEvent("keyUpEvent", this);
    this.itemAddedEvent = new CustomEvent("itemAddedEvent", this);
    this.itemRemovedEvent = new CustomEvent("itemRemovedEvent", this);

},


/**
* @method getRoot
* @description Finds the menu's root menu.
*/
getRoot: function() {

    var oItem = this.parent;

    if(oItem) {

        var oParentMenu = oItem.parent;

        return oParentMenu ? oParentMenu.getRoot() : this;

    }
    else {
    
        return this;
    
    }

},


/**
* @method toString
* @description Returns a string representing the menu.
* @return {String}
*/
toString: function() {

    return ("Menu " + this.id);

},


/**
* @method setItemGroupTitle
* @description Sets the title of a group of menu items.
* @param {String} p_sGroupTitle String specifying the title of the group.
* @param {Number} p_nGroupIndex Optional. Number specifying the group to which
* the title belongs.
*/
setItemGroupTitle: function(p_sGroupTitle, p_nGroupIndex) {
        
    if(typeof p_sGroupTitle == "string" && p_sGroupTitle.length > 0) {

        var nGroupIndex = typeof p_nGroupIndex == "number" ? p_nGroupIndex : 0,
            oTitle = this._aGroupTitleElements[nGroupIndex];


        if(oTitle) {

            oTitle.innerHTML = p_sGroupTitle;
            
        }
        else {

            oTitle = document.createElement(this.GROUP_TITLE_TAG_NAME);
                    
            oTitle.innerHTML = p_sGroupTitle;

            this._aGroupTitleElements[nGroupIndex] = oTitle;

        }


        var i = this._aGroupTitleElements.length - 1,
            nFirstIndex;

        do {

            if(this._aGroupTitleElements[i]) {

                Dom.removeClass(this._aGroupTitleElements[i], "first-of-type");

                nFirstIndex = i;

            }

        }
        while(i--);


        if(nFirstIndex !== null) {

            Dom.addClass(
                this._aGroupTitleElements[nFirstIndex], 
                "first-of-type"
            );

        }

    }

},



/**
* @method addItem
* @description Appends an item to the menu.
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance to be added to the menu.
* @param {String} p_oItem String specifying the text of the item to be added 
* to the menu.
* @param {Object} p_oItem Object literal containing a set of menu item 
* configuration properties.
* @param {Number} p_nGroupIndex Optional. Number indicating the group to
* which the item belongs.
* @return {YAHOO.widget.MenuItem}
*/
addItem: function(p_oItem, p_nGroupIndex) {

    if(p_oItem) {

        return this._addItemToGroup(p_nGroupIndex, p_oItem);
        
    }

},


/**
* @method addItems
* @description Adds an array of items to the menu.
* @param {Array} p_aItems Array of items to be added to the menu.  The array 
* can contain strings specifying the text for each item to be created, object
* literals specifying each of the menu item configuration properties, 
* or MenuItem instances.
* @param {Number} p_nGroupIndex Optional. Number specifying the group to 
* which the items belongs.
* @return {Array}
*/
addItems: function(p_aItems, p_nGroupIndex) {

    function isArray(p_oValue) {
    
        return (typeof p_oValue == "object" && p_oValue.constructor == Array);
    
    }


    if(isArray(p_aItems)) {

        var nItems = p_aItems.length,
            aItems = [],
            oItem;


        for(var i=0; i<nItems; i++) {

            oItem = p_aItems[i];

            if(isArray(oItem)) {

                aItems[aItems.length] = this.addItems(oItem, i);

            }
            else {

                aItems[aItems.length] = 
                    this._addItemToGroup(p_nGroupIndex, oItem);
            
            }
    
        }


        if(aItems.length) {
        
            return aItems;
        
        }
    
    }

},


/**
* @method insertItem
* @description Inserts an item into the menu at the specified index.
* @param {YAHOO.widget.MenuItem} p_oItem Object reference for the MenuItem 
* instance to be added to the menu.
* @param {String} p_oItem String specifying the text of the item to be added 
* to the menu.
* @param {Object} p_oItem Object literal containing a set of menu item 
* configuration properties.
* @param {Number} p_nItemIndex Number indicating the ordinal position at which
* the item should be added.
* @param {Number} p_nGroupIndex Optional. Number indicating the group to which 
* the item belongs.
* @return {YAHOO.widget.MenuItem}
*/
insertItem: function(p_oItem, p_nItemIndex, p_nGroupIndex) {
    
    if(p_oItem) {

        return this._addItemToGroup(p_nGroupIndex, p_oItem, p_nItemIndex);

    }

},


/**
* @method removeItem
* @description Removes the specified item from the menu.
* @param {YAHOO.widget.MenuItem} p_oObject Object reference for the MenuItem 
* instance to be removed from the menu.
* @param {Number} p_oObject Number specifying the index of the item 
* to be removed.
* @param {Number} p_nGroupIndex Optional. Number specifying the group to 
* which the item belongs.
* @return {YAHOO.widget.MenuItem}
*/
removeItem: function(p_oObject, p_nGroupIndex) {
    
    if(typeof p_oObject != "undefined") {

        var oItem;

        if(p_oObject instanceof YAHOO.widget.MenuItem) {

            oItem = this._removeItemFromGroupByValue(p_nGroupIndex, p_oObject);           

        }
        else if(typeof p_oObject == "number") {

            oItem = this._removeItemFromGroupByIndex(p_nGroupIndex, p_oObject);

        }

        if(oItem) {

            oItem.destroy();


            return oItem;

        }

    }

},


/**
* @method getItemGroups
* @description Returns a multi-dimensional array of all of the items in the menu.
* @return {Array}
*/        
getItemGroups: function() {

    return this._aItemGroups;

},


/**
* @method getItem
* @description Returns the item at the specified index.
* @param {Number} p_nItemIndex Number indicating the ordinal position of the 
* item to be retrieved.
* @param {Number} p_nGroupIndex Optional. Number indicating the group to which 
* the item belongs.
* @return {YAHOO.widget.MenuItem}
*/
getItem: function(p_nItemIndex, p_nGroupIndex) {
    
    if(typeof p_nItemIndex == "number") {

        var aGroup = this._getItemGroup(p_nGroupIndex);

        if(aGroup) {

            return aGroup[p_nItemIndex];
        
        }

    }
    
},


/**
* @method destroy
* @description Removes the menu's <code>&#60;div&#62;</code> element 
* (and accompanying child nodes) from the document.
*/
destroy: function() {

    // Remove Custom Event listeners

    this.mouseOverEvent.unsubscribeAll();
    this.mouseOutEvent.unsubscribeAll();
    this.mouseDownEvent.unsubscribeAll();
    this.mouseUpEvent.unsubscribeAll();
    this.clickEvent.unsubscribeAll();
    this.keyPressEvent.unsubscribeAll();
    this.keyDownEvent.unsubscribeAll();
    this.keyUpEvent.unsubscribeAll();
    this.itemAddedEvent.unsubscribeAll();
    this.itemRemovedEvent.unsubscribeAll();

    var nItemGroups = this._aItemGroups.length,
        nItems,
        oItemGroup,
        oItem,
        i,
        n;


    // Remove all items

    if(nItemGroups > 0) {

        i = nItemGroups - 1;

        do {

            oItemGroup = this._aItemGroups[i];

            if(oItemGroup) {

                nItems = oItemGroup.length;
    
                if(nItems > 0) {
    
                    n = nItems - 1;
        
                    do {

                        oItem = this._aItemGroups[i][n];

                        if(oItem) {
        
                            oItem.destroy();
                        }
        
                    }
                    while(n--);
    
                }

            }

        }
        while(i--);

    }        


    // Continue with the superclass implementation of this method

    YAHOO.widget.Menu.superclass.destroy.call(this);
    

},


/**
* @method setInitialFocus
* @description Sets focus to the menu's first enabled item.
*/
setInitialFocus: function() {

    var oItem = this._getFirstEnabledItem();
    
    if(oItem) {
    
        oItem.focus();
    }
    
},


/**
* @method setInitialSelection
* @description Sets the "selected" configuration property of the menu's first 
* enabled item to "true."
*/
setInitialSelection: function() {

    var oItem = this._getFirstEnabledItem();
    
    if(oItem) {
    
        oItem.cfg.setProperty("selected", true);
    }        

},


/**
* @method clearActiveItem
* @description Sets the "selected" configuration property of the menu's active
* item to "false" and hides the item's submenu.
* @param {Boolean} p_bBlur Boolean indicating if the menu's active item 
* should be blurred.  
*/
clearActiveItem: function(p_bBlur) {

    if(this.cfg.getProperty("showdelay") > 0) {
    
        this._cancelShowDelay();
    
    }


    var oActiveItem = this.activeItem;

    if(oActiveItem) {

        var oConfig = oActiveItem.cfg;

        oConfig.setProperty("selected", false);

        var oSubmenu = oConfig.getProperty("submenu");

        if(oSubmenu) {

            oSubmenu.hide();

        }

        if(p_bBlur) {

            oActiveItem.blur();
        
        }

    }

},


/**
* @description Initializes the class's configurable properties which can be
* changed using the menu's Config object ("cfg").
* @method initDefaultConfig
*/
initDefaultConfig: function() {

    YAHOO.widget.Menu.superclass.initDefaultConfig.call(this);

    var oConfig = this.cfg;

	// Add configuration properties

    /*
        Change the default value for the "visible" configuration 
        property to "false" by re-adding the property.
    */

    /**
    * @config visible
    * @description Boolean indicating whether or not the menu is visible.  If 
    * the menu's "position" configuration property is set to "dynamic" (the 
    * default), this property toggles the menu's <code>&#60;div&#62;</code> 
    * element's "visibility" style property between "visible" (true) or 
    * "hidden" (false).  If the menu's "position" configuration property is 
    * set to "static" this property toggles the menu's 
    * <code>&#60;div&#62;</code> element's "display" style property 
    * between "block" (true) or "none" (false).
    * @default false
    * @type Boolean
    */
    oConfig.addProperty(
        "visible", 
        {
            value:false, 
            handler:this.configVisible, 
            validator:this.cfg.checkBoolean
         }
     );


    /*
        Change the default value for the "constraintoviewport" configuration 
        property to "true" by re-adding the property.
    */

    /**
    * @config constraintoviewport
    * @description Boolean indicating if the menu will try to remain inside 
    * the boundaries of the size of viewport.
    * @default true
    * @type Boolean
    */
    oConfig.addProperty(
        "constraintoviewport", 
        {
            value:true, 
            handler:this.configConstrainToViewport, 
            validator:this.cfg.checkBoolean, 
            supercedes:["iframe","x","y","xy"] 
        } 
    );


    /**
    * @config position
    * @description String indicating how a menu should be positioned on the 
    * screen.  Possible values are "static" and "dynamic."  Static menus are 
    * visible by default and reside in the normal flow of the document 
    * (CSS position: static).  Dynamic menus are hidden by default, reside 
    * out of the normal flow of the document (CSS position: absolute), and 
    * can overlay other elements on the screen.
    * @default dynamic
    * @type String
    */
    oConfig.addProperty(
        "position", 
        {
            value: "dynamic", 
            handler: this.configPosition, 
            validator: this._checkPosition,
            supercedes: ["visible"]
        }
    );


    /**
    * @config submenualignment
    * @description Array defining how submenus should be aligned to their 
    * parent menu item. The format is: [itemCorner, submenuCorner]. By default
    * a submenu's top left corner is aligned to its parent menu item's top 
    * right corner.
    * @default ["tl","tr"]
    * @type Array
    */
    oConfig.addProperty("submenualignment", { value: ["tl","tr"] } );


    /**
    * @config autosubmenudisplay
    * @description Boolean indicating if submenus are automatically made 
    * visible when the user mouses over the menu's items.
    * @default true
    * @type Boolean
    */
	oConfig.addProperty(
	   "autosubmenudisplay", 
	   { 
	       value: true, 
	       validator: oConfig.checkBoolean
       } 
    );


    /**
    * @config showdelay
    * @description Number indicating the time (in milliseconds) that should 
    * expire before a submenu is made visible when the user mouses over 
    * the menu's items.
    * @default 0
    * @type Number
    */
	oConfig.addProperty(
	   "showdelay", 
	   { 
	       value: 0, 
	       validator: oConfig.checkNumber
       } 
    );


    /**
    * @config hidedelay
    * @description Number indicating the time (in milliseconds) that should 
    * expire before the menu is hidden.
    * @default 0
    * @type Number
    */
	oConfig.addProperty(
	   "hidedelay", 
	   { 
	       value: 0, 
	       validator: oConfig.checkNumber, 
	       handler: this.configHideDelay,
	       suppressEvent: true
       } 
    );


    /**
    * @config clicktohide
    * @description Boolean indicating if the menu will automatically be 
    * hidden if the user clicks outside of it.
    * @default true
    * @type Boolean
    */
    oConfig.addProperty(
        "clicktohide",
        {
            value: true,
            validator: oConfig.checkBoolean
        }
    );


	/**
	* @config container
	* @description HTML element reference or string specifying the id 
	* attribute of the HTML element that the menu's markup should be rendered into.
	* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
	* level-one-html.html#ID-58190037">HTMLElement</a>|String
	* @default document.body
	*/
	this.cfg.addProperty(
	   "container", 
	   { value:document.body, handler:this.configContainer } 
   );

}

}); // END YAHOO.extend

})();

/**
* The base class for all menuing containers.
* 
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu module.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source for the 
* menu module.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929
* /level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the menu module.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement Object 
* specifying the <code>&#60;select&#62;</code> element to be used as the data 
* source for the menu module.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu module. See configuration class documentation for 
* more details.
* @class MenuModule
* @constructor
* @extends YAHOO.widget.Overlay
* @deprecated As of version 0.12, all MenuModule functionality has been 
* implemented directly in YAHOO.widget.Menu, making YAHOO.widget.Menu the base 
* class for all menuing containers.
*/
YAHOO.widget.MenuModule = YAHOO.widget.Menu;

(function() {

var Dom = YAHOO.util.Dom,
    Module = YAHOO.widget.Module,
    Menu = YAHOO.widget.Menu;

/**
* Creates an item for a menu.
* 
* @param {String} p_oObject String specifying the text of the menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying 
* the <code>&#60;li&#62;</code> element of the menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
* specifying the <code>&#60;optgroup&#62;</code> element of the menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object 
* specifying the <code>&#60;option&#62;</code> element of the menu item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu item. See configuration class documentation 
* for more details.
* @class MenuItem
* @constructor
*/
YAHOO.widget.MenuItem = function(p_oObject, p_oConfig) {

    if(p_oObject) {

        if(p_oConfig) {
    
            this.parent = p_oConfig.parent;
            this.value = p_oConfig.value;
            
        }

        this.init(p_oObject, p_oConfig);

    }

};

YAHOO.widget.MenuItem.prototype = {

    // Constants

    /**
    * @property SUBMENU_INDICATOR_IMAGE_PATH
    * @description String representing the path to the image to be used for the 
    * menu item's submenu arrow indicator.
    * @default "nt/ic/ut/alt1/menuarorght8_nrm_1.gif"
    * @final
    * @type String
    */
    SUBMENU_INDICATOR_IMAGE_PATH: "nt/ic/ut/alt1/menuarorght8_nrm_1.gif",

    /**
    * @property SELECTED_SUBMENU_INDICATOR_IMAGE_PATH
    * @description String representing the path to the image to be used for the 
    * submenu arrow indicator when the menu item is selected.
    * @default "nt/ic/ut/alt1/menuarorght8_hov_1.gif"
    * @final
    * @type String
    */
    SELECTED_SUBMENU_INDICATOR_IMAGE_PATH: 
        "nt/ic/ut/alt1/menuarorght8_hov_1.gif",

    /**
    * @property DISABLED_SUBMENU_INDICATOR_IMAGE_PATH
    * @description String representing the path to the image to be used for the 
    * submenu arrow indicator when the menu item is disabled.
    * @default "nt/ic/ut/alt1/menuarorght8_dim_1.gif"
    * @final
    * @type String
    */
    DISABLED_SUBMENU_INDICATOR_IMAGE_PATH: 
        "nt/ic/ut/alt1/menuarorght8_dim_1.gif",

    /**
    * @property COLLAPSED_SUBMENU_INDICATOR_ALT_TEXT
    * @description String representing the alt text for the image to be used 
    * for the submenu arrow indicator.
    * @default "Collapsed.  Click to expand."
    * @final
    * @type String
    */
    COLLAPSED_SUBMENU_INDICATOR_ALT_TEXT: "Collapsed.  Click to expand.",

    /**
    * @property EXPANDED_SUBMENU_INDICATOR_ALT_TEXT
    * @description String representing the alt text for the image to be used 
    * for the submenu arrow indicator when the submenu is visible.
    * @default "Expanded.  Click to collapse."
    * @final
    * @type String
    */
    EXPANDED_SUBMENU_INDICATOR_ALT_TEXT: "Expanded.  Click to collapse.",

    /**
    * @property DISABLED_SUBMENU_INDICATOR_ALT_TEXT
    * @description String representing the alt text for the image to be used 
    * for the submenu arrow indicator when the menu item is disabled.
    * @default "Disabled."
    * @final
    * @type String
    */
    DISABLED_SUBMENU_INDICATOR_ALT_TEXT: "Disabled.",

    /**
    * @property CHECKED_IMAGE_PATH
    * @description String representing the path to the image to be used for 
    * the checked state.
    * @default "nt/ic/ut/bsc/menuchk8_nrm_1.gif"
    * @final
    * @type String
    */
    CHECKED_IMAGE_PATH: "nt/ic/ut/bsc/menuchk8_nrm_1.gif",
    

    /**
    * @property SELECTED_CHECKED_IMAGE_PATH
    * @description String representing the path to the image to be used for 
    * the selected checked state.
    * @default "nt/ic/ut/bsc/menuchk8_hov_1.gif"
    * @final
    * @type String
    */
    SELECTED_CHECKED_IMAGE_PATH: "nt/ic/ut/bsc/menuchk8_hov_1.gif",
    

    /**
    * @property DISABLED_CHECKED_IMAGE_PATH
    * @description String representing the path to the image to be used for 
    * the disabled checked state.
    * @default "nt/ic/ut/bsc/menuchk8_dim_1.gif"
    * @final
    * @type String
    */
    DISABLED_CHECKED_IMAGE_PATH: "nt/ic/ut/bsc/menuchk8_dim_1.gif",
    

    /**
    * @property CHECKED_IMAGE_ALT_TEXT
    * @description String representing the alt text for the image to be used 
    * for the checked image.
    * @default "Checked."
    * @final
    * @type String
    */
    CHECKED_IMAGE_ALT_TEXT: "Checked.",
    
    
    /**
    * @property DISABLED_CHECKED_IMAGE_ALT_TEXT
    * @description String representing the alt text for the image to be used 
    * for the checked image when the item is disabled.
    * @default "Checked. (Item disabled.)"
    * @final
    * @type String
    */
    DISABLED_CHECKED_IMAGE_ALT_TEXT: "Checked. (Item disabled.)",

    /**
    * @property CSS_CLASS_NAME
    * @description String representing the CSS class(es) to be applied to the 
    * <code>&#60;li&#62;</code> element of the menu item.
    * @default "yuimenuitem"
    * @final
    * @type String
    */
    CSS_CLASS_NAME: "yuimenuitem",

    /**
    * @property SUBMENU_TYPE
    * @description Object representing the type of menu to instantiate and 
    * add when parsing the child nodes of the menu item's source HTML element.
    * @final
    * @type YAHOO.widget.Menu
    */
    SUBMENU_TYPE: null,

    /**
    * @property IMG_ROOT
    * @description String representing the prefix path to use for 
    * non-secure images.
    * @default "http://us.i1.yimg.com/us.yimg.com/i/"
    * @type String
    */
    IMG_ROOT: "http://us.i1.yimg.com/us.yimg.com/i/",
    

    /**
    * @property IMG_ROOT_SSL
    * @description String representing the prefix path to use for securely 
    * served images.
    * @default "https://a248.e.akamai.net/sec.yimg.com/i/"
    * @type String
    */
    IMG_ROOT_SSL: "https://a248.e.akamai.net/sec.yimg.com/i/",

    // Private member variables
    
    /**
    * @property _oAnchor
    * @description Object reference to the menu item's 
    * <code>&#60;a&#62;</code> element.
    * @default null 
    * @private
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-48250443">HTMLAnchorElement</a>
    */
    _oAnchor: null,
    

    /**
    * @property _oText
    * @description Object reference to the menu item's text node.
    * @default null
    * @private
    * @type TextNode
    */
    _oText: null,
    
    
    /**
    * @property _oHelpTextEM
    * @description Object reference to the menu item's help text 
    * <code>&#60;em&#62;</code> element.
    * @default null
    * @private
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-58190037">HTMLElement</a>
    */
    _oHelpTextEM: null,
    
    
    /**
    * @property _oSubmenu
    * @description Object reference to the menu item's submenu.
    * @default null
    * @private
    * @type YAHOO.widget.Menu
    */
    _oSubmenu: null,

    /**
    * @property _checkImage
    * @description Object reference to the menu item's checkmark image.
    * @default null
    * @private
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-17701901">HTMLImageElement</a>
    */
    _checkImage: null,

    // Public properties

	/**
    * @property constructor
	* @description Object reference to the menu item's constructor function.
    * @default YAHOO.widget.MenuItem
	* @type YAHOO.widget.MenuItem
	*/
	constructor: YAHOO.widget.MenuItem,

	/**
    * @property imageRoot
	* @description String representing the root path for all of the menu 
	* item's images.
	* @type String
	*/
	imageRoot: null,

	/**
    * @property isSecure
	* @description Boolean representing whether or not the current browsing 
	* context is secure (HTTPS).
	* @type Boolean
	*/
	isSecure: Module.prototype.isSecure,

    /**
    * @property index
    * @description Number indicating the ordinal position of the menu item in 
    * its group.
    * @default null
    * @type Number
    */
    index: null,

    /**
    * @property groupIndex
    * @description Number indicating the index of the group to which the menu 
    * item belongs.
    * @default null
    * @type Number
    */
    groupIndex: null,

    /**
    * @property parent
    * @description Object reference to the menu item's parent menu.
    * @default null
    * @type YAHOO.widget.Menu
    */
    parent: null,

    /**
    * @property element
    * @description Object reference to the menu item's 
    * <code>&#60;li&#62;</code> element.
    * @default <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level
    * -one-html.html#ID-74680021">HTMLLIElement</a>
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-74680021">HTMLLIElement</a>
    */
    element: null,

    /**
    * @property srcElement
    * @description Object reference to the HTML element (either 
    * <code>&#60;li&#62;</code>, <code>&#60;optgroup&#62;</code> or 
    * <code>&#60;option&#62;</code>) used create the menu item.
    * @default <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-74680021">HTMLLIElement</a>|<a href="http://www.
    * w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#ID-38450247"
    * >HTMLOptGroupElement</a>|<a href="http://www.w3.org/TR/2000/WD-DOM-
    * Level-1-20000929/level-one-html.html#ID-70901257">HTMLOptionElement</a>
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-74680021">HTMLLIElement</a>|<a href="http://www.w3.
    * org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#ID-38450247">
    * HTMLOptGroupElement</a>|<a href="http://www.w3.org/TR/2000/WD-DOM-
    * Level-1-20000929/level-one-html.html#ID-70901257">HTMLOptionElement</a>
    */
    srcElement: null,

    /**
    * @property value
    * @description Object reference to the menu item's value.
    * @default null
    * @type Object
    */
    value: null,

    /**
    * @property submenuIndicator
    * @description Object reference to the <code>&#60;img&#62;</code> element 
    * used to create the submenu indicator for the menu item.
    * @default <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-17701901">HTMLImageElement</a>
    * @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-17701901">HTMLImageElement</a>
    */
    submenuIndicator: null,

	/**
    * @property browser
	* @description String representing the browser.
	* @type String
	*/
	browser: Module.prototype.browser,

    // Events

    /**
    * @event destroyEvent
    * @description Fires when the menu item's <code>&#60;li&#62;</code> 
    * element is removed from its parent <code>&#60;ul&#62;</code> element.
    * @type YAHOO.util.CustomEvent
    */
    destroyEvent: null,

    /**
    * @event mouseOverEvent
    * @description Fires when the mouse has entered the menu item.  Passes 
    * back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    mouseOverEvent: null,

    /**
    * @event mouseOutEvent
    * @description Fires when the mouse has left the menu item.  Passes back 
    * the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    mouseOutEvent: null,

    /**
    * @event mouseDownEvent
    * @description Fires when the user mouses down on the menu item.  Passes 
    * back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    mouseDownEvent: null,

    /**
    * @event mouseUpEvent
    * @description Fires when the user releases a mouse button while the mouse 
    * is over the menu item.  Passes back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    mouseUpEvent: null,

    /**
    * @event clickEvent
    * @description Fires when the user clicks the on the menu item.  Passes 
    * back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    clickEvent: null,

    /**
    * @event keyPressEvent
    * @description Fires when the user presses an alphanumeric key when the 
    * menu item has focus.  Passes back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    keyPressEvent: null,

    /**
    * @event keyDownEvent
    * @description Fires when the user presses a key when the menu item has 
    * focus.  Passes back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    keyDownEvent: null,

    /**
    * @event keyUpEvent
    * @description Fires when the user releases a key when the menu item has 
    * focus.  Passes back the DOM Event object as an argument.
    * @type YAHOO.util.CustomEvent
    */
    keyUpEvent: null,

    /**
    * @event focusEvent
    * @description Fires when the menu item receives focus.
    * @type YAHOO.util.CustomEvent
    */
    focusEvent: null,

    /**
    * @event blurEvent
    * @description Fires when the menu item loses the input focus.
    * @type YAHOO.util.CustomEvent
    */
    blurEvent: null,

    /**
    * @method init
    * @description The MenuItem class's initialization method. This method is 
    * automatically called by the constructor, and sets up all DOM references 
    * for pre-existing markup, and creates required markup if it is not 
    * already present.
    * @param {String} p_oObject String specifying the text of the menu item.
    * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying 
    * the <code>&#60;li&#62;</code> element of the menu item.
    * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
    * specifying the <code>&#60;optgroup&#62;</code> element of the menu item.
    * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
    * one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object 
    * specifying the <code>&#60;option&#62;</code> element of the menu item.
    * @param {Object} p_oConfig Optional. Object literal specifying the 
    * configuration for the menu item. See configuration class documentation 
    * for more details.
    */
    init: function(p_oObject, p_oConfig) {

        this.imageRoot = (this.isSecure) ? this.IMG_ROOT_SSL : this.IMG_ROOT;

        if(!this.SUBMENU_TYPE) {
    
            this.SUBMENU_TYPE = Menu;
    
        }

        // Create the config object

        this.cfg = new YAHOO.util.Config(this);

        this.initDefaultConfig();

        var oConfig = this.cfg;

        if(this._checkString(p_oObject)) {

            this._createRootNodeStructure();

            oConfig.setProperty("text", p_oObject);

        }
        else if(this._checkDOMNode(p_oObject)) {

            switch(p_oObject.tagName.toUpperCase()) {

                case "OPTION":

                    this._createRootNodeStructure();

                    oConfig.setProperty("text", p_oObject.text);

                    this.srcElement = p_oObject;

                break;

                case "OPTGROUP":

                    this._createRootNodeStructure();

                    oConfig.setProperty("text", p_oObject.label);

                    this.srcElement = p_oObject;

                    this._initSubTree();

                break;

                case "LI":

                    // Get the anchor node (if it exists)

                    var oAnchor = this._getFirstElement(p_oObject, "A"),
                        sURL = "#",
                        sTarget,
                        sText;

                    // Capture the "text" and/or the "URL"

                    if(oAnchor) {

                        sURL = oAnchor.getAttribute("href");
                        sTarget = oAnchor.getAttribute("target");

                        if(oAnchor.innerText) {
                
                            sText = oAnchor.innerText;
                
                        }
                        else {
                
                            var oRange = oAnchor.ownerDocument.createRange();
                
                            oRange.selectNodeContents(oAnchor);
                
                            sText = oRange.toString();             
                
                        }

                    }
                    else {

                        var oText = p_oObject.firstChild;

                        sText = oText.nodeValue;

                        oAnchor = document.createElement("a");
                        
                        oAnchor.setAttribute("href", sURL);

                        p_oObject.replaceChild(oAnchor, oText);
                        
                        oAnchor.appendChild(oText);

                    }

                    this.srcElement = p_oObject;
                    this.element = p_oObject;
                    this._oAnchor = oAnchor;
    

                    // Check if emphasis has been applied to the MenuItem

                    var oEmphasisNode = this._getFirstElement(oAnchor),
                        bEmphasis = false,
                        bStrongEmphasis = false;

                    if(oEmphasisNode) {

                        // Set a reference to the text node 

                        this._oText = oEmphasisNode.firstChild;

                        switch(oEmphasisNode.tagName.toUpperCase()) {

                            case "EM":

                                bEmphasis = true;

                            break;

                            case "STRONG":

                                bStrongEmphasis = true;

                            break;

                        }

                    }
                    else {

                        // Set a reference to the text node 

                        this._oText = oAnchor.firstChild;

                    }

                    /*
                        Set these properties silently to sync up the 
                        configuration object without making changes to the 
                        element's DOM
                    */ 

                    oConfig.setProperty("text", sText, true);
                    oConfig.setProperty("url", sURL, true);
                    oConfig.setProperty("target", sTarget, true);
                    oConfig.setProperty("emphasis", bEmphasis, true);
                    oConfig.setProperty(
                        "strongemphasis", 
                        bStrongEmphasis, 
                        true
                    );

                    this._initSubTree();

                break;

            }            

        }

        if(this.element) {

            Dom.addClass(this.element, this.CSS_CLASS_NAME);

            // Create custom events
    
            var CustomEvent = YAHOO.util.CustomEvent;
    
            this.destroyEvent = new CustomEvent("destroyEvent", this);
            this.mouseOverEvent = new CustomEvent("mouseOverEvent", this);
            this.mouseOutEvent = new CustomEvent("mouseOutEvent", this);
            this.mouseDownEvent = new CustomEvent("mouseDownEvent", this);
            this.mouseUpEvent = new CustomEvent("mouseUpEvent", this);
            this.clickEvent = new CustomEvent("clickEvent", this);
            this.keyPressEvent = new CustomEvent("keyPressEvent", this);
            this.keyDownEvent = new CustomEvent("keyDownEvent", this);
            this.keyUpEvent = new CustomEvent("keyUpEvent", this);
            this.focusEvent = new CustomEvent("focusEvent", this);
            this.blurEvent = new CustomEvent("blurEvent", this);

            if(p_oConfig) {
    
                oConfig.applyConfig(p_oConfig);
    
            }        

            oConfig.fireQueue();

        }

    },

    // Private methods

    /**
    * @method _getFirstElement
    * @description Returns an HTML element's first HTML element node.
    * @private
    * @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-58190037">HTMLElement</a>} p_oElement Object 
    * reference specifying the element to be evaluated.
    * @param {String} p_sTagName Optional. String specifying the tagname of 
    * the element to be retrieved.
    * @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-58190037">HTMLElement</a>}
    */
    _getFirstElement: function(p_oElement, p_sTagName) {

        var oElement;

        if(p_oElement.firstChild && p_oElement.firstChild.nodeType == 1) {

            oElement = p_oElement.firstChild;

        }
        else if(
            p_oElement.firstChild && 
            p_oElement.firstChild.nextSibling && 
            p_oElement.firstChild.nextSibling.nodeType == 1
        ) {

            oElement = p_oElement.firstChild.nextSibling;

        }

        if(p_sTagName) {

            return (oElement && oElement.tagName.toUpperCase() == p_sTagName) ? 
                oElement : false;

        }

        return oElement;

    },

    /**
    * @method _checkString
    * @description Determines if an object is a string.
    * @private
    * @param {Object} p_oObject Object to be evaluated.
    * @return {Boolean}
    */
    _checkString: function(p_oObject) {

        return (typeof p_oObject == "string");

    },

    /**
    * @method _checkDOMNode
    * @description Determines if an object is an HTML element.
    * @private
    * @param {Object} p_oObject Object to be evaluated.
    * @return {Boolean}
    */
    _checkDOMNode: function(p_oObject) {

        return (p_oObject && p_oObject.tagName);

    },

    /**
    * @method _createRootNodeStructure
    * @description Creates the core DOM structure for the menu item.
    * @private
    */
    _createRootNodeStructure: function () {

        this.element = document.createElement("li");

        this._oText = document.createTextNode("");

        this._oAnchor = document.createElement("a");
        this._oAnchor.appendChild(this._oText);
        
        this.cfg.refireEvent("url");

        this.element.appendChild(this._oAnchor);            

    },

    /**
    * @method _initSubTree
    * @description Iterates the source element's childNodes collection and uses 
    * the child nodes to instantiate other menus.
    * @private
    */
    _initSubTree: function() {

        var oSrcEl = this.srcElement,
            oConfig = this.cfg;

        if(oSrcEl.childNodes.length > 0) {

            if(
                this.parent.lazyLoad && 
                this.parent.srcElement && 
                this.parent.srcElement.tagName.toUpperCase() == "SELECT"
            ) {

                oConfig.setProperty(
                        "submenu", 
                        { id: Dom.generateId(), itemdata: oSrcEl.childNodes }
                    );

            }
            else {

                var oNode = oSrcEl.firstChild,
                    aOptions = [];
    
                do {
    
                    if(oNode && oNode.tagName) {
    
                        switch(oNode.tagName.toUpperCase()) {
                
                            case "DIV":
                
                                oConfig.setProperty("submenu", oNode);
                
                            break;
         
                            case "OPTION":
        
                                aOptions[aOptions.length] = oNode;
        
                            break;
               
                        }
                    
                    }
                
                }        
                while((oNode = oNode.nextSibling));
    
    
                var nOptions = aOptions.length;
    
                if(nOptions > 0) {
    
                    var oMenu = new this.SUBMENU_TYPE(Dom.generateId());
                    
                    oConfig.setProperty("submenu", oMenu);
    
                    for(var n=0; n<nOptions; n++) {
        
                        oMenu.addItem((new oMenu.ITEM_TYPE(aOptions[n])));
        
                    }
        
                }
            
            }

        }

    },

    /**
    * @method _preloadImage
    * @description Preloads an image by creating an image element from the 
    * specified path and appending the image to the body of the document.
    * @private
    * @param {String} p_sPath String specifying the path to the image.                
    */
    _preloadImage: function(p_sPath) {

        var sPath = this.imageRoot + p_sPath;

        if(!document.images[sPath]) {

            var oImage = document.createElement("img");
            oImage.src = sPath;
            oImage.name = sPath;
            oImage.id = sPath;
            oImage.style.display = "none";
            
            document.body.appendChild(oImage);

        }
    
    },

    // Event handlers for configuration properties

    /**
    * @method configText
    * @description Event handler for when the "text" configuration property of 
    * the menu item changes.
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */
    configText: function(p_sType, p_aArgs, p_oItem) {

        var sText = p_aArgs[0];

        if(this._oText) {

            this._oText.nodeValue = sText;

        }

    },

    /**
    * @method configHelpText
    * @description Event handler for when the "helptext" configuration property 
    * of the menu item changes.
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configHelpText: function(p_sType, p_aArgs, p_oItem) {

        var me = this,
            oHelpText = p_aArgs[0],
            oEl = this.element,
            oConfig = this.cfg,
            aNodes = [oEl, this._oAnchor],
            oImg = this.submenuIndicator;

        /**
        * Adds the "hashelptext" class to the necessary nodes and refires the 
        * "selected" and "disabled" configuration events.
        * @private
        */
        function initHelpText() {

            Dom.addClass(aNodes, "hashelptext");

            if(oConfig.getProperty("disabled")) {

                oConfig.refireEvent("disabled");

            }

            if(oConfig.getProperty("selected")) {

                oConfig.refireEvent("selected");

            }                

        }

        /**
        * Removes the "hashelptext" class and corresponding DOM element (EM).
        * @private
        */
        function removeHelpText() {

            Dom.removeClass(aNodes, "hashelptext");

            oEl.removeChild(me._oHelpTextEM);
            me._oHelpTextEM = null;

        }

        if(this._checkDOMNode(oHelpText)) {

            if(this._oHelpTextEM) {

                this._oHelpTextEM.parentNode.replaceChild(
                    oHelpText, 
                    this._oHelpTextEM
                );

            }
            else {

                this._oHelpTextEM = oHelpText;

                oEl.insertBefore(this._oHelpTextEM, oImg);

            }

            initHelpText();

        }
        else if(this._checkString(oHelpText)) {

            if(oHelpText.length === 0) {

                removeHelpText();

            }
            else {

                if(!this._oHelpTextEM) {

                    this._oHelpTextEM = document.createElement("em");

                    oEl.insertBefore(this._oHelpTextEM, oImg);

                }

                this._oHelpTextEM.innerHTML = oHelpText;

                initHelpText();

            }

        }
        else if(!oHelpText && this._oHelpTextEM) {

            removeHelpText();

        }

    },

    /**
    * @method configURL
    * @description Event handler for when the "url" configuration property of 
    * the menu item changes.
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configURL: function(p_sType, p_aArgs, p_oItem) {

        var sURL = p_aArgs[0];

        if(!sURL) {

            sURL = "#";

        }

        this._oAnchor.setAttribute("href", sURL);

    },

    /**
    * @method configTarget
    * @description Event handler for when the "target" configuration property 
    * of the menu item changes.  
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configTarget: function(p_sType, p_aArgs, p_oItem) {

        var sTarget = p_aArgs[0],
            oAnchor = this._oAnchor;

        if(sTarget && sTarget.length > 0) {

            oAnchor.setAttribute("target", sTarget);

        }
        else {

            oAnchor.removeAttribute("target");
        
        }

    },

    /**
    * @method configEmphasis
    * @description Event handler for when the "emphasis" configuration property
    * of the menu item changes.  
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configEmphasis: function(p_sType, p_aArgs, p_oItem) {

        var bEmphasis = p_aArgs[0],
            oAnchor = this._oAnchor,
            oText = this._oText,
            oConfig = this.cfg,
            oEM;

        if(bEmphasis && oConfig.getProperty("strongemphasis")) {

            oConfig.setProperty("strongemphasis", false);

        }

        if(oAnchor) {

            if(bEmphasis) {

                oEM = document.createElement("em");
                oEM.appendChild(oText);

                oAnchor.appendChild(oEM);

            }
            else {

                oEM = this._getFirstElement(oAnchor, "EM");

                if(oEM) {

                    oAnchor.removeChild(oEM);
                    oAnchor.appendChild(oText);

                }

            }

        }

    },

    /**
    * @method configStrongEmphasis
    * @description Event handler for when the "strongemphasis" configuration 
    * property of the menu item changes. 
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configStrongEmphasis: function(p_sType, p_aArgs, p_oItem) {

        var bStrongEmphasis = p_aArgs[0],
            oAnchor = this._oAnchor,
            oText = this._oText,
            oConfig = this.cfg,
            oStrong;

        if(bStrongEmphasis && oConfig.getProperty("emphasis")) {

            oConfig.setProperty("emphasis", false);

        }

        if(oAnchor) {

            if(bStrongEmphasis) {

                oStrong = document.createElement("strong");
                oStrong.appendChild(oText);

                oAnchor.appendChild(oStrong);

            }
            else {

                oStrong = this._getFirstElement(oAnchor, "STRONG");

                if(oStrong) {

                    oAnchor.removeChild(oStrong);
                    oAnchor.appendChild(oText);

                }

            }

        }

    },

    /**
    * @method configChecked
    * @description Event handler for when the "checked" configuration property 
    * of the menu item changes. 
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configChecked: function(p_sType, p_aArgs, p_oItem) {
    
        var bChecked = p_aArgs[0],
            oEl = this.element,
            oConfig = this.cfg,
            oImg;
        

        if(bChecked) {

            this._preloadImage(this.CHECKED_IMAGE_PATH);
            this._preloadImage(this.SELECTED_CHECKED_IMAGE_PATH);
            this._preloadImage(this.DISABLED_CHECKED_IMAGE_PATH);

            oImg = document.createElement("img");
            oImg.src = (this.imageRoot + this.CHECKED_IMAGE_PATH);
            oImg.alt = this.CHECKED_IMAGE_ALT_TEXT;

            var oSubmenu = this.cfg.getProperty("submenu");

            if(oSubmenu) {

                oEl.insertBefore(oImg, oSubmenu.element);

            }
            else {

                oEl.appendChild(oImg);            

            }

            Dom.addClass([oEl, oImg], "checked");

            this._checkImage = oImg;

            if(oConfig.getProperty("disabled")) {

                oConfig.refireEvent("disabled");

            }

            if(oConfig.getProperty("selected")) {

                oConfig.refireEvent("selected");

            }
        
        }
        else {

            oImg = this._checkImage;

            Dom.removeClass([oEl, oImg], "checked");

            if(oImg) {

                oEl.removeChild(oImg);

            }

            this._checkImage = null;
        
        }

    },

    /**
    * @method configDisabled
    * @description Event handler for when the "disabled" configuration property 
    * of the menu item changes. 
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configDisabled: function(p_sType, p_aArgs, p_oItem) {

        var bDisabled = p_aArgs[0],
            oAnchor = this._oAnchor,
            aNodes = [this.element, oAnchor],
            oEM = this._oHelpTextEM,
            oConfig = this.cfg,
            oImg,
            sImgSrc,
            sImgAlt;

        if(oEM) {

            aNodes[2] = oEM;

        }

        if(this.cfg.getProperty("checked")) {
    
            sImgAlt = this.CHECKED_IMAGE_ALT_TEXT;
            sImgSrc = this.CHECKED_IMAGE_PATH;
            oImg = this._checkImage;
            
            if(bDisabled) {
    
                sImgAlt = this.DISABLED_CHECKED_IMAGE_ALT_TEXT;
                sImgSrc = this.DISABLED_CHECKED_IMAGE_PATH;
            
            }

            oImg.src = document.images[(this.imageRoot + sImgSrc)].src;
            oImg.alt = sImgAlt;
            
        }    

        oImg = this.submenuIndicator;

        if(bDisabled) {

            if(oConfig.getProperty("selected")) {

                oConfig.setProperty("selected", false);

            }

            oAnchor.removeAttribute("href");

            Dom.addClass(aNodes, "disabled");

            sImgSrc = this.DISABLED_SUBMENU_INDICATOR_IMAGE_PATH;
            sImgAlt = this.DISABLED_SUBMENU_INDICATOR_ALT_TEXT;

        }
        else {

            oAnchor.setAttribute("href", oConfig.getProperty("url"));

            Dom.removeClass(aNodes, "disabled");

            sImgSrc = this.SUBMENU_INDICATOR_IMAGE_PATH;
            sImgAlt = this.COLLAPSED_SUBMENU_INDICATOR_ALT_TEXT;

        }

        if(oImg) {

            oImg.src = this.imageRoot + sImgSrc;
            oImg.alt = sImgAlt;

        }

    },

    /**
    * @method configSelected
    * @description Event handler for when the "selected" configuration property 
    * of the menu item changes. 
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */    
    configSelected: function(p_sType, p_aArgs, p_oItem) {

        if(!this.cfg.getProperty("disabled")) {

            var bSelected = p_aArgs[0],
                oEM = this._oHelpTextEM,
                aNodes = [this.element, this._oAnchor],
                oImg = this.submenuIndicator,
                sImgSrc;

            if(oEM) {
    
                aNodes[aNodes.length] = oEM;  
    
            }
            
            if(oImg) {

                aNodes[aNodes.length] = oImg;  
            
            }
    

            if(this.cfg.getProperty("checked")) {
    
                sImgSrc = this.imageRoot + (bSelected ? 
                    this.SELECTED_CHECKED_IMAGE_PATH : this.CHECKED_IMAGE_PATH);
    
                this._checkImage.src = document.images[sImgSrc].src;
                
            }

            if(bSelected) {
    
                Dom.addClass(aNodes, "selected");
                sImgSrc = this.SELECTED_SUBMENU_INDICATOR_IMAGE_PATH;
    
            }
            else {
    
                Dom.removeClass(aNodes, "selected");
                sImgSrc = this.SUBMENU_INDICATOR_IMAGE_PATH;
    
            }
    
            if(oImg) {
    
                oImg.src = document.images[(this.imageRoot + sImgSrc)].src;

            }

        }

    },

    /**
    * @method configSubmenu
    * @description Event handler for when the "submenu" configuration property 
    * of the menu item changes. 
    * @param {String} p_sType String representing the name of the event that 
    * was fired.
    * @param {Array} p_aArgs Array of arguments sent when the event was fired.
    * @param {YAHOO.widget.MenuItem} p_oItem Object representing the menu item
    * that fired the event.
    */
    configSubmenu: function(p_sType, p_aArgs, p_oItem) {

        var oEl = this.element,
            oSubmenu = p_aArgs[0],
            oImg = this.submenuIndicator,
            oConfig = this.cfg,
            aNodes = [this.element, this._oAnchor],
            oMenu,
            bLazyLoad = this.parent && this.parent.lazyLoad;

        if(oSubmenu) {

            if(oSubmenu instanceof Menu) {

                oMenu = oSubmenu;
                oMenu.parent = this;
                oMenu.lazyLoad = bLazyLoad;

            }
            else if(
                typeof oSubmenu == "object" && 
                oSubmenu.id && 
                !oSubmenu.nodeType
            ) {

                var sSubmenuId = oSubmenu.id,
                    oSubmenuConfig = oSubmenu;

                oSubmenuConfig.lazyload = bLazyLoad;
                oSubmenuConfig.parent = this;

                oMenu = new this.SUBMENU_TYPE(sSubmenuId, oSubmenuConfig);

                // Set the value of the property to the Menu instance
                
                this.cfg.setProperty("submenu", oMenu, true);

            }
            else {

                oMenu = new this.SUBMENU_TYPE(
                                oSubmenu,
                                { lazyload: bLazyLoad, parent: this }                
                            );

                // Set the value of the property to the Menu instance
                
                this.cfg.setProperty("submenu", oMenu, true);

            }

            if(oMenu) {

                this._oSubmenu = oMenu;

                if(!oImg) { 

                    this._preloadImage(this.SUBMENU_INDICATOR_IMAGE_PATH);
                    this._preloadImage(
                            this.SELECTED_SUBMENU_INDICATOR_IMAGE_PATH
                        );

                    this._preloadImage(
                            this.DISABLED_SUBMENU_INDICATOR_IMAGE_PATH
                        );

                    oImg = document.createElement("img");

                    oImg.src = 
                        (this.imageRoot + this.SUBMENU_INDICATOR_IMAGE_PATH);

                    oImg.alt = this.COLLAPSED_SUBMENU_INDICATOR_ALT_TEXT;

                    oEl.appendChild(oImg);

                    this.submenuIndicator = oImg;

                    Dom.addClass(aNodes, "hassubmenu");

                    if(oConfig.getProperty("disabled")) {
    
                        oConfig.refireEvent("disabled");

                    }

                    if(oConfig.getProperty("selected")) {
    
                        oConfig.refireEvent("selected");
    
                    }                
    
                }
            
            }

        }
        else {

            Dom.removeClass(aNodes, "hassubmenu");

            if(oImg) {

                oEl.removeChild(oImg);

            }

            if(this._oSubmenu) {

                this._oSubmenu.destroy();

            }

        }

    },

    // Public methods

	/**
    * @method initDefaultConfig
	* @description Initializes an item's configurable properties.
	*/
	initDefaultConfig : function() {

        var oConfig = this.cfg,
            CheckBoolean = oConfig.checkBoolean;

        // Define the config properties

        /**
        * @config text
        * @description String specifying the text label for the menu item.  
        * When building a menu from existing HTML the value of this property
        * will be interpreted from the menu's markup.
        * @default ""
        * @type String
        */
        oConfig.addProperty(
            "text", 
            { 
                value: "", 
                handler: this.configText, 
                validator: this._checkString, 
                suppressEvent: true 
            }
        );
        

        /**
        * @config helptext
        * @description String specifying additional instructional text to 
        * accompany the text for the nenu item.
        * @default null
        * @type String|<a href="http://www.w3.org/TR/
        * 2000/WD-DOM-Level-1-20000929/level-one-html.html#ID-58190037">
        * HTMLElement</a>
        */
        oConfig.addProperty("helptext", { handler: this.configHelpText });

        /**
        * @config url
        * @description String specifying the URL for the menu item's anchor's 
        * "href" attribute.  When building a menu from existing HTML the value 
        * of this property will be interpreted from the menu's markup.
        * @default "#"
        * @type String
        */        
        oConfig.addProperty(
            "url", 
            { value: "#", handler: this.configURL, suppressEvent: true }
        );

        /**
        * @config target
        * @description String specifying the value for the "target" attribute 
        * of the menu item's anchor element. <strong>Specifying a target will 
        * require the user to click directly on the menu item's anchor node in
        * order to cause the browser to navigate to the specified URL.</strong> 
        * When building a menu from existing HTML the value of this property 
        * will be interpreted from the menu's markup.
        * @default null
        * @type String
        */        
        oConfig.addProperty(
            "target", 
            { handler: this.configTarget, suppressEvent: true }
        );

        /**
        * @config emphasis
        * @description Boolean indicating if the text of the menu item will be 
        * rendered with emphasis.  When building a menu from existing HTML the 
        * value of this property will be interpreted from the menu's markup.
        * @default false
        * @type Boolean
        */
        oConfig.addProperty(
            "emphasis", 
            { 
                value: false, 
                handler: this.configEmphasis, 
                validator: CheckBoolean, 
                suppressEvent: true 
            }
        );

        /**
        * @config strongemphasis
        * @description Boolean indicating if the text of the menu item will be 
        * rendered with strong emphasis.  When building a menu from existing 
        * HTML the value of this property will be interpreted from the
        * menu's markup.
        * @default false
        * @type Boolean
        */
        oConfig.addProperty(
            "strongemphasis",
            {
                value: false,
                handler: this.configStrongEmphasis,
                validator: CheckBoolean,
                suppressEvent: true
            }
        );

        /**
        * @config checked
        * @description Boolean indicating if the menu item should be rendered 
        * with a checkmark.
        * @default false
        * @type Boolean
        */
        oConfig.addProperty(
            "checked", 
            {
                value: false, 
                handler: this.configChecked, 
                validator: this.cfg.checkBoolean, 
                suppressEvent: true,
                supercedes:["disabled"]
            } 
        );

        /**
        * @config disabled
        * @description Boolean indicating if the menu item should be disabled.  
        * (Disabled menu items are  dimmed and will not respond to user input 
        * or fire events.)
        * @default false
        * @type Boolean
        */
        oConfig.addProperty(
            "disabled",
            {
                value: false,
                handler: this.configDisabled,
                validator: CheckBoolean,
                suppressEvent: true
            }
        );

        /**
        * @config selected
        * @description Boolean indicating if the menu item should 
        * be highlighted.
        * @default false
        * @type Boolean
        */
        oConfig.addProperty(
            "selected",
            {
                value: false,
                handler: this.configSelected,
                validator: CheckBoolean,
                suppressEvent: true
            }
        );

        /**
        * @config submenu
        * @description Object specifying the submenu to be appended to the 
        * menu item.  The value can be one of the following: <ul><li>Object 
        * specifying a Menu instance.</li><li>Object literal specifying the
        * menu to be created.  Format: <code>{ id: [menu id], itemdata: 
        * [<a href="YAHOO.widget.Menu.html#itemData">array of values for 
        * items</a>] }</code>.</li><li>String specifying the id attribute 
        * of the <code>&#60;div&#62;</code> element of the menu.</li><li>
        * Object specifying the <code>&#60;div&#62;</code> element of the 
        * menu.</li></ul>
        * @default null
        * @type Menu|String|Object|<a href="http://www.w3.org/TR/2000/
        * WD-DOM-Level-1-20000929/level-one-html.html#ID-58190037">
        * HTMLElement</a>
        */
        oConfig.addProperty("submenu", { handler: this.configSubmenu });

	},

    /**
    * @method getNextEnabledSibling
    * @description Finds the menu item's next enabled sibling.
    * @return YAHOO.widget.MenuItem
    */
    getNextEnabledSibling: function() {

        if(this.parent instanceof Menu) {

            var nGroupIndex = this.groupIndex;

            /**
            * Finds the next item in an array.
            * @private
            * @param {p_aArray} Array to search.
            * @param {p_nStartIndex} Number indicating the index to 
            * start searching the array.
            * @return {Object}
            */
            function getNextArrayItem(p_aArray, p_nStartIndex) {
    
                return p_aArray[p_nStartIndex] || 
                    getNextArrayItem(p_aArray, (p_nStartIndex+1));
    
            }
    
    
            var aItemGroups = this.parent.getItemGroups(),
                oNextItem;
    
    
            if(this.index < (aItemGroups[nGroupIndex].length - 1)) {
    
                oNextItem = getNextArrayItem(
                        aItemGroups[nGroupIndex], 
                        (this.index+1)
                    );
    
            }
            else {
    
                var nNextGroupIndex;
    
                if(nGroupIndex < (aItemGroups.length - 1)) {
    
                    nNextGroupIndex = nGroupIndex + 1;
    
                }
                else {
    
                    nNextGroupIndex = 0;
    
                }
    
                var aNextGroup = getNextArrayItem(aItemGroups, nNextGroupIndex);
    
                // Retrieve the first menu item in the next group
    
                oNextItem = getNextArrayItem(aNextGroup, 0);
    
            }
    
            return (
                oNextItem.cfg.getProperty("disabled") || 
                oNextItem.element.style.display == "none"
            ) ? 
            oNextItem.getNextEnabledSibling() : oNextItem;

        }

    },

    /**
    * @method getPreviousEnabledSibling
    * @description Finds the menu item's previous enabled sibling.
    * @return {YAHOO.widget.MenuItem}
    */
    getPreviousEnabledSibling: function() {

        if(this.parent instanceof Menu) {

            var nGroupIndex = this.groupIndex;

            /**
            * Returns the previous item in an array 
            * @private
            * @param {p_aArray} Array to search.
            * @param {p_nStartIndex} Number indicating the index to 
            * start searching the array.
            * @return {Object}
            */
            function getPreviousArrayItem(p_aArray, p_nStartIndex) {
    
                return p_aArray[p_nStartIndex] || 
                    getPreviousArrayItem(p_aArray, (p_nStartIndex-1));
    
            }

            /**
            * Get the index of the first item in an array 
            * @private
            * @param {p_aArray} Array to search.
            * @param {p_nStartIndex} Number indicating the index to 
            * start searching the array.
            * @return {Object}
            */    
            function getFirstItemIndex(p_aArray, p_nStartIndex) {
    
                return p_aArray[p_nStartIndex] ? 
                    p_nStartIndex : 
                    getFirstItemIndex(p_aArray, (p_nStartIndex+1));
    
            }
    
            var aItemGroups = this.parent.getItemGroups(),
                oPreviousItem;
    
            if(
                this.index > getFirstItemIndex(aItemGroups[nGroupIndex], 0)
            ) {
    
                oPreviousItem = 
                    getPreviousArrayItem(
                        aItemGroups[nGroupIndex], 
                        (this.index-1)
                    );
    
            }
            else {
    
                var nPreviousGroupIndex;
    
                if(nGroupIndex > getFirstItemIndex(aItemGroups, 0)) {
    
                    nPreviousGroupIndex = nGroupIndex - 1;
    
                }
                else {
    
                    nPreviousGroupIndex = aItemGroups.length - 1;
    
                }
    
                var aPreviousGroup = 
                        getPreviousArrayItem(aItemGroups, nPreviousGroupIndex);
    
                oPreviousItem = 
                    getPreviousArrayItem(
                        aPreviousGroup, 
                        (aPreviousGroup.length - 1)
                    );
    
            }

            return (
                oPreviousItem.cfg.getProperty("disabled") || 
                oPreviousItem.element.style.display == "none"
            ) ? 
            oPreviousItem.getPreviousEnabledSibling() : oPreviousItem;

        }

    },

    /**
    * @method focus
    * @description Causes the menu item to receive the focus and fires the 
    * focus event.
    */
    focus: function() {

        var oParent = this.parent,
            oAnchor = this._oAnchor,
            oActiveItem = oParent.activeItem;

        function setFocus() {

            try {

                oAnchor.focus();

            }
            catch(e) {
            
            }

        }

        if(
            !this.cfg.getProperty("disabled") && 
            oParent && 
            oParent.cfg.getProperty("visible") && 
            this.element.style.display != "none"
        ) {

            if(oActiveItem) {

                oActiveItem.blur();

            }

            /*
                Setting focus via a timer fixes a race condition in Firefox, IE 
                and Opera where the browser viewport jumps as it trys to 
                position and focus the menu.
            */

            window.setTimeout(setFocus, 0);
            
            this.focusEvent.fire();

        }

    },

    /**
    * @method blur
    * @description Causes the menu item to lose focus and fires the 
    * onblur event.
    */    
    blur: function() {

        var oParent = this.parent;

        if(
            !this.cfg.getProperty("disabled") && 
            oParent && 
            Dom.getStyle(oParent.element, "visibility") == "visible"
        ) {

            this._oAnchor.blur();

            this.blurEvent.fire();

        }

    },

	/**
    * @method destroy
	* @description Removes the menu item's <code>&#60;li&#62;</code> element 
	* from its parent <code>&#60;ul&#62;</code> element.
	*/
    destroy: function() {

        var oEl = this.element;

        if(oEl) {

            // If the item has a submenu, destroy it first

            var oSubmenu = this.cfg.getProperty("submenu");

            if(oSubmenu) {
            
                oSubmenu.destroy();
            
            }

            // Remove CustomEvent listeners
    
            this.mouseOverEvent.unsubscribeAll();
            this.mouseOutEvent.unsubscribeAll();
            this.mouseDownEvent.unsubscribeAll();
            this.mouseUpEvent.unsubscribeAll();
            this.clickEvent.unsubscribeAll();
            this.keyPressEvent.unsubscribeAll();
            this.keyDownEvent.unsubscribeAll();
            this.keyUpEvent.unsubscribeAll();
            this.focusEvent.unsubscribeAll();
            this.blurEvent.unsubscribeAll();
            this.cfg.configChangedEvent.unsubscribeAll();

            // Remove the element from the parent node

            var oParentNode = oEl.parentNode;

            if(oParentNode) {

                oParentNode.removeChild(oEl);

                this.destroyEvent.fire();

            }

            this.destroyEvent.unsubscribeAll();

        }

    },

    /**
    * @method toString
    * @description Returns a string representing the menu item.
    * @return {String}
    */
    toString: function() {
    
        return ("MenuItem: " + this.cfg.getProperty("text"));
    
    }

};

})();

/**
* Creates an item for a menu module.
* 
* @param {String} p_oObject String specifying the text of the menu module item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying the 
* <code>&#60;li&#62;</code> element of the menu module item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object specifying 
* the <code>&#60;optgroup&#62;</code> element of the menu module item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object specifying the 
* <code>&#60;option&#62;</code> element of the menu module item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu module item. See configuration class documentation
* for more details.
* @class MenuModuleItem
* @constructor
* @deprecated As of version 0.12, all MenuModuleItem functionality has been 
* implemented directly in YAHOO.widget.MenuItem, making YAHOO.widget.MenuItem 
* the base class for all menu items.
*/
YAHOO.widget.MenuModuleItem = YAHOO.widget.MenuItem;

/**
* Creates a list of options or commands which are made visible in response to 
* an HTML element's "contextmenu" event ("mousedown" for Opera).
*
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the context menu.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source for the 
* context menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object specifying the 
* <code>&#60;div&#62;</code> element of the context menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-94282980">HTMLSelectElement</a>} p_oElement Object specifying 
* the <code>&#60;select&#62;</code> element to be used as the data source for 
* the context menu.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the context menu. See configuration class documentation 
* for more details.
* @class ContextMenu
* @constructor
* @extends YAHOO.widget.Menu
* @namespace YAHOO.widget
*/
YAHOO.widget.ContextMenu = function(p_oElement, p_oConfig) {

    YAHOO.widget.ContextMenu.superclass.constructor.call(
            this, 
            p_oElement,
            p_oConfig
        );

};

YAHOO.extend(YAHOO.widget.ContextMenu, YAHOO.widget.Menu, {

// Private properties

/**
* @property _oTrigger
* @description Object reference to the current value of the "trigger" 
* configuration property.
* @default null
* @private
* @type String|<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/leve
* l-one-html.html#ID-58190037">HTMLElement</a>|Array
*/
_oTrigger: null,

// Public properties

/**
* @property contextEventTarget
* @description Object reference for the HTML element that was the target of the
* "contextmenu" DOM event ("mousedown" for Opera) that triggered the display of 
* the context menu.
* @default null
* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-58190037">HTMLElement</a>
*/
contextEventTarget: null,

/**
* @method init
* @description The ContextMenu class's initialization method. This method is 
* automatically called by the constructor, and sets up all DOM references for 
* pre-existing markup, and creates required markup if it is not already present.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the context menu.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source for 
* the context menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object specifying the 
* <code>&#60;div&#62;</code> element of the context menu.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-
* html.html#ID-94282980">HTMLSelectElement</a>} p_oElement Object specifying 
* the <code>&#60;select&#62;</code> element to be used as the data source for 
* the context menu.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the context menu. See configuration class documentation 
* for more details.
*/
init: function(p_oElement, p_oConfig) {

    if(!this.ITEM_TYPE) {

        this.ITEM_TYPE = YAHOO.widget.ContextMenuItem;

    }

    // Call the init of the superclass (YAHOO.widget.Menu)

    YAHOO.widget.ContextMenu.superclass.init.call(this, p_oElement);

    this.beforeInitEvent.fire(YAHOO.widget.ContextMenu);

    if(p_oConfig) {

        this.cfg.applyConfig(p_oConfig, true);

    }
    
    
    this.initEvent.fire(YAHOO.widget.ContextMenu);
    
},

// Private methods

/**
* @method _removeEventHandlers
* @description Removes all of the DOM event handlers from the HTML element(s) 
* whose "context menu" event ("click" for Opera) trigger the display of 
* the context menu.
* @private
*/
_removeEventHandlers: function() {

    var Event = YAHOO.util.Event,
        oTrigger = this._oTrigger,
        bOpera = (this.browser == "opera");

    // Remove the event handlers from the trigger(s)

    Event.removeListener(
        oTrigger, 
        (bOpera ? "mousedown" : "contextmenu"), 
        this._onTriggerContextMenu
    );    
    
    if(bOpera) {
    
        Event.removeListener(oTrigger, "click", this._onTriggerClick);

    }

},

// Private event handlers

/**
* @method _onTriggerClick
* @description "click" event handler for the HTML element(s) identified as the 
* "trigger" for the context menu.  Used to cancel default behaviors in Opera.
* @private
* @param {Event} p_oEvent Object representing the DOM event object passed back 
* by the event utility (YAHOO.util.Event).
* @param {YAHOO.widget.ContextMenu} p_oMenu Object representing the context 
* menu that is handling the event.
*/
_onTriggerClick: function(p_oEvent, p_oMenu) {

    if(p_oEvent.ctrlKey) {
    
        YAHOO.util.Event.stopEvent(p_oEvent);

    }
    
},

/**
* @method _onTriggerContextMenu
* @description "contextmenu" event handler ("mousedown" for Opera) for the HTML 
* element(s) that trigger the display of the context menu.
* @private
* @param {Event} p_oEvent Object representing the DOM event object passed back 
* by the event utility (YAHOO.util.Event).
* @param {YAHOO.widget.ContextMenu} p_oMenu Object representing the context 
* menu that is handling the event.
*/
_onTriggerContextMenu: function(p_oEvent, p_oMenu) {

    // Hide any other ContextMenu instances that might be visible

    YAHOO.widget.MenuManager.hideVisible();

    var Event = YAHOO.util.Event,
        oConfig = this.cfg;

    if(p_oEvent.type == "mousedown" && !p_oEvent.ctrlKey) {

        return;

    }

    this.contextEventTarget = Event.getTarget(p_oEvent);

    // Position and display the context menu

    var nX = Event.getPageX(p_oEvent),
        nY = Event.getPageY(p_oEvent);

    oConfig.applyConfig( { xy:[nX, nY], visible:true } );
    oConfig.fireQueue();

    /*
        Prevent the browser's default context menu from appearing and 
        stop the propagation of the "contextmenu" event so that 
        other ContextMenu instances are not displayed.
    */

    Event.stopEvent(p_oEvent);
    
},

// Public methods

/**
* @method toString
* @description Returns a string representing the context menu.
* @return {String}
*/
toString: function() {

    return ("ContextMenu " + this.id);

},

/**
* @method initDefaultConfig
* @description Initializes the class's configurable properties which can be 
* changed using the context menu's Config object ("cfg").
*/
initDefaultConfig: function() {

    YAHOO.widget.ContextMenu.superclass.initDefaultConfig.call(this);

    /**
    * @config trigger
    * @description The HTML element(s) whose "contextmenu" event ("mousedown" 
    * for Opera) trigger the display of the context menu.  Can be a string 
    * representing the id attribute of the HTML element, an object reference 
    * for the HTML element, or an array of strings or HTML element references.
    * @default null
    * @type String|<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
    * level-one-html.html#ID-58190037">HTMLElement</a>|Array
    */
    this.cfg.addProperty("trigger", { handler: this.configTrigger });

},

/**
* @method destroy
* @description Removes the context menu's <code>&#60;div&#62;</code> element 
* (and accompanying child nodes) from the document.
*/
destroy: function() {

    // Remove the DOM event handlers from the current trigger(s)

    this._removeEventHandlers();
    

    // Continue with the superclass implementation of this method

    YAHOO.widget.ContextMenu.superclass.destroy.call(this);

},

// Public event handlers for configuration properties

/**
* @method configTrigger
* @description Event handler for when the value of the "trigger" configuration 
* property changes. 
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.ContextMenu} p_oMenu Object representing the context 
* menu that fired the event.
*/
configTrigger: function(p_sType, p_aArgs, p_oMenu) {
    
    var Event = YAHOO.util.Event,
        oTrigger = p_aArgs[0];

    if(oTrigger) {

        /*
            If there is a current "trigger" - remove the event handlers 
            from that element(s) before assigning new ones
        */

        if(this._oTrigger) {
        
            this._removeEventHandlers();

        }

        this._oTrigger = oTrigger;

        /*
            Listen for the "mousedown" event in Opera b/c it does not 
            support the "contextmenu" event
        */ 
  
        var bOpera = (this.browser == "opera");

        Event.addListener(
            oTrigger, 
            (bOpera ? "mousedown" : "contextmenu"), 
            this._onTriggerContextMenu,
            this,
            true
        );

        /*
            Assign a "click" event handler to the trigger element(s) for
            Opera to prevent default browser behaviors.
        */

        if(bOpera) {
        
            Event.addListener(
                oTrigger, 
                "click", 
                this._onTriggerClick,
                this,
                true
            );

        }

    }
    else {
    
        this._removeEventHandlers();
    
    }
    
}

}); // END YAHOO.extend

/**
* Creates an item for a context menu.
* 
* @param {String} p_oObject String specifying the text of the context menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying the 
* <code>&#60;li&#62;</code> element of the context menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
* specifying the <code>&#60;optgroup&#62;</code> element of the context 
* menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object specifying 
* the <code>&#60;option&#62;</code> element of the context menu item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the context menu item. See configuration class 
* documentation for more details.
* @class ContextMenuItem
* @constructor
* @extends YAHOO.widget.MenuItem
*/
YAHOO.widget.ContextMenuItem = function(p_oObject, p_oConfig) {

    YAHOO.widget.ContextMenuItem.superclass.constructor.call(
        this, 
        p_oObject, 
        p_oConfig
    );

};

YAHOO.extend(YAHOO.widget.ContextMenuItem, YAHOO.widget.MenuItem, {

/**
* @method init
* @description The ContextMenuItem class's initialization method. This method 
* is automatically called by the constructor, and sets up all DOM references 
* for pre-existing markup, and creates required markup if it is not 
* already present.
* @param {String} p_oObject String specifying the text of the context menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying the 
* <code>&#60;li&#62;</code> element of the context menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
* specifying the <code>&#60;optgroup&#62;</code> element of the context 
* menu item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object specifying 
* the <code>&#60;option&#62;</code> element of the context menu item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the context menu item. See configuration class 
* documentation for more details.
*/
init: function(p_oObject, p_oConfig) {
    
    if(!this.SUBMENU_TYPE) {

        this.SUBMENU_TYPE = YAHOO.widget.ContextMenu;

    }

    /* 
        Call the init of the superclass (YAHOO.widget.MenuItem)
        Note: We don't pass the user config in here yet 
        because we only want it executed once, at the lowest 
        subclass level.
    */ 

    YAHOO.widget.ContextMenuItem.superclass.init.call(this, p_oObject);

    var oConfig = this.cfg;

    if(p_oConfig) {

        oConfig.applyConfig(p_oConfig, true);

    }

    oConfig.fireQueue();

},

// Public methods

/**
* @method toString
* @description Returns a string representing the context menu item.
* @return {String}
*/
toString: function() {

    return ("MenuBarItem: " + this.cfg.getProperty("text"));

}
    
}); // END YAHOO.extend

/**
* Horizontal collection of items, each of which can contain a submenu.
* 
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu bar.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source for the 
* menu bar.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object specifying 
* the <code>&#60;div&#62;</code> element of the menu bar.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement Object 
* specifying the <code>&#60;select&#62;</code> element to be used as the data 
* source for the menu bar.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu bar. See configuration class documentation for
* more details.
* @class Menubar
* @constructor
* @extends YAHOO.widget.Menu
* @namespace YAHOO.widget
*/
YAHOO.widget.MenuBar = function(p_oElement, p_oConfig) {

    YAHOO.widget.MenuBar.superclass.constructor.call(
            this, 
            p_oElement,
            p_oConfig
        );

};

YAHOO.extend(YAHOO.widget.MenuBar, YAHOO.widget.Menu, {

/**
* @method init
* @description The MenuBar class's initialization method. This method is 
* automatically called by the constructor, and sets up all DOM references for 
* pre-existing markup, and creates required markup if it is not already present.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the menu bar.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;select&#62;</code> element to be used as the data source for the 
* menu bar.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object specifying 
* the <code>&#60;div&#62;</code> element of the menu bar.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-94282980">HTMLSelectElement</a>} p_oElement Object 
* specifying the <code>&#60;select&#62;</code> element to be used as the data 
* source for the menu bar.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu bar. See configuration class documentation for
* more details.
*/
init: function(p_oElement, p_oConfig) {

    if(!this.ITEM_TYPE) {

        this.ITEM_TYPE = YAHOO.widget.MenuBarItem;

    }

    // Call the init of the superclass (YAHOO.widget.Menu)

    YAHOO.widget.MenuBar.superclass.init.call(this, p_oElement);

    this.beforeInitEvent.fire(YAHOO.widget.MenuBar);

    if(p_oConfig) {

        this.cfg.applyConfig(p_oConfig, true);

    }

    this.initEvent.fire(YAHOO.widget.MenuBar);

},

// Constants

/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the menu 
* bar's <code>&#60;div&#62;</code> element.
* @default "yuimenubar"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuimenubar",

// Protected event handlers

/**
* @method _onKeyDown
* @description "keydown" Custom Event handler for the menu bar.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.MenuBar} p_oMenuBar Object representing the menu bar 
* that fired the event.
*/
_onKeyDown: function(p_sType, p_aArgs, p_oMenuBar) {

    var Event = YAHOO.util.Event,
        oEvent = p_aArgs[0],
        oItem = p_aArgs[1],
        oSubmenu;

    if(oItem && !oItem.cfg.getProperty("disabled")) {

        var oItemCfg = oItem.cfg;

        switch(oEvent.keyCode) {
    
            case 37:    // Left arrow
            case 39:    // Right arrow
    
                if(
                    oItem == this.activeItem && 
                    !oItemCfg.getProperty("selected")
                ) {
    
                    oItemCfg.setProperty("selected", true);
    
                }
                else {
    
                    var oNextItem = (oEvent.keyCode == 37) ? 
                            oItem.getPreviousEnabledSibling() : 
                            oItem.getNextEnabledSibling();
            
                    if(oNextItem) {
    
                        this.clearActiveItem();
    
                        oNextItem.cfg.setProperty("selected", true);
    
    
                        if(this.cfg.getProperty("autosubmenudisplay")) {
                        
                            oSubmenu = oNextItem.cfg.getProperty("submenu");
                            
                            if(oSubmenu) {
                        
                                oSubmenu.show();
                                oSubmenu.activeItem.blur();
                                oSubmenu.activeItem = null;
                            
                            }
                
                        }           
    
                        oNextItem.focus();
    
                    }
    
                }
    
                Event.preventDefault(oEvent);
    
            break;
    
            case 40:    // Down arrow
    
                if(this.activeItem != oItem) {
    
                    this.clearActiveItem();
    
                    oItemCfg.setProperty("selected", true);
                    oItem.focus();
                
                }
    
                oSubmenu = oItemCfg.getProperty("submenu");
    
                if(oSubmenu) {
    
                    if(oSubmenu.cfg.getProperty("visible")) {
    
                        oSubmenu.setInitialSelection();
                        oSubmenu.setInitialFocus();
                    
                    }
                    else {
    
                        oSubmenu.show();
                    
                    }
    
                }
    
                Event.preventDefault(oEvent);
    
            break;
    
        }

    }

    if(oEvent.keyCode == 27 && this.activeItem) { // Esc key

        oSubmenu = this.activeItem.cfg.getProperty("submenu");

        if(oSubmenu && oSubmenu.cfg.getProperty("visible")) {
        
            oSubmenu.hide();
            this.activeItem.focus();
        
        }
        else {

            this.activeItem.cfg.setProperty("selected", false);
            this.activeItem.blur();
    
        }

        Event.preventDefault(oEvent);
    
    }

},

/**
* @method _onClick
* @description "click" event handler for the menu bar.
* @protected
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {YAHOO.widget.MenuBar} p_oMenuBar Object representing the menu bar 
* that fired the event.
*/
_onClick: function(p_sType, p_aArgs, p_oMenuBar) {

    YAHOO.widget.MenuBar.superclass._onClick.call(
        this, 
        p_sType, 
        p_aArgs, 
        p_oMenuBar
    );

    var oItem = p_aArgs[1];
    
    if(oItem && !oItem.cfg.getProperty("disabled")) {

         var Event = YAHOO.util.Event,
             Dom = YAHOO.util.Dom,
    
             oEvent = p_aArgs[0],
             oTarget = Event.getTarget(oEvent),
    
             oActiveItem = this.activeItem,
             oConfig = this.cfg;

        // Hide any other submenus that might be visible
    
        if(oActiveItem && oActiveItem != oItem) {
    
            this.clearActiveItem();
    
        }
    
    
        // Select and focus the current item
    
        oItem.cfg.setProperty("selected", true);
        oItem.focus();
    

        // Show the submenu for the item
    
        var oSubmenu = oItem.cfg.getProperty("submenu");

        if(oSubmenu && oTarget != oItem.submenuIndicator) {
        
            if(oSubmenu.cfg.getProperty("visible")) {
            
                oSubmenu.hide();
            
            }
            else {
            
                oSubmenu.show();                    
            
            }
        
        }
    
    }

},

// Public methods

/**
* @method toString
* @description Returns a string representing the menu bar.
* @return {String}
*/
toString: function() {

    return ("MenuBar " + this.id);

},

/**
* @description Initializes the class's configurable properties which can be
* changed using the menu bar's Config object ("cfg").
* @method initDefaultConfig
*/
initDefaultConfig: function() {

    YAHOO.widget.MenuBar.superclass.initDefaultConfig.call(this);

    var oConfig = this.cfg;

	// Add configuration properties

    /*
        Set the default value for the "position" configuration property
        to "static" by re-adding the property.
    */

    /**
    * @config position
    * @description String indicating how a menu bar should be positioned on the 
    * screen.  Possible values are "static" and "dynamic."  Static menu bars 
    * are visible by default and reside in the normal flow of the document 
    * (CSS position: static).  Dynamic menu bars are hidden by default, reside
    * out of the normal flow of the document (CSS position: absolute), and can 
    * overlay other elements on the screen.
    * @default static
    * @type String
    */
    oConfig.addProperty(
        "position", 
        {
            value: "static", 
            handler: this.configPosition, 
            validator: this._checkPosition,
            supercedes: ["visible"]
        }
    );

    /*
        Set the default value for the "submenualignment" configuration property
        to ["tl","bl"] by re-adding the property.
    */

    /**
    * @config submenualignment
    * @description Array defining how submenus should be aligned to their 
    * parent menu bar item. The format is: [itemCorner, submenuCorner].
    * @default ["tl","bl"]
    * @type Array
    */
    oConfig.addProperty("submenualignment", { value: ["tl","bl"] } );

    /*
        Change the default value for the "autosubmenudisplay" configuration 
        property to "false" by re-adding the property.
    */

    /**
    * @config autosubmenudisplay
    * @description Boolean indicating if submenus are automatically made 
    * visible when the user mouses over the menu bar's items.
    * @default false
    * @type Boolean
    */
	oConfig.addProperty(
	   "autosubmenudisplay", 
	   { value: false, validator: oConfig.checkBoolean } 
    );

}
 
}); // END YAHOO.extend

/**
* Creates an item for a menu bar.
* 
* @param {String} p_oObject String specifying the text of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying the 
* <code>&#60;li&#62;</code> element of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
* specifying the <code>&#60;optgroup&#62;</code> element of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object specifying 
* the <code>&#60;option&#62;</code> element of the menu bar item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu bar item. See configuration class documentation 
* for more details.
* @class MenuBarItem
* @constructor
* @extends YAHOO.widget.MenuItem
*/
YAHOO.widget.MenuBarItem = function(p_oObject, p_oConfig) {

    YAHOO.widget.MenuBarItem.superclass.constructor.call(
        this, 
        p_oObject, 
        p_oConfig
    );

};

YAHOO.extend(YAHOO.widget.MenuBarItem, YAHOO.widget.MenuItem, {

/**
* @method init
* @description The MenuBarItem class's initialization method. This method is 
* automatically called by the constructor, and sets up all DOM references for 
* pre-existing markup, and creates required markup if it is not already present.
* @param {String} p_oObject String specifying the text of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-74680021">HTMLLIElement</a>} p_oObject Object specifying the 
* <code>&#60;li&#62;</code> element of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-38450247">HTMLOptGroupElement</a>} p_oObject Object 
* specifying the <code>&#60;optgroup&#62;</code> element of the menu bar item.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-70901257">HTMLOptionElement</a>} p_oObject Object specifying 
* the <code>&#60;option&#62;</code> element of the menu bar item.
* @param {Object} p_oConfig Optional. Object literal specifying the 
* configuration for the menu bar item. See configuration class documentation 
* for more details.
*/
init: function(p_oObject, p_oConfig) {

    if(!this.SUBMENU_TYPE) {

        this.SUBMENU_TYPE = YAHOO.widget.Menu;

    }

    /* 
        Call the init of the superclass (YAHOO.widget.MenuItem)
        Note: We don't pass the user config in here yet 
        because we only want it executed once, at the lowest 
        subclass level.
    */ 

    YAHOO.widget.MenuBarItem.superclass.init.call(this, p_oObject);  

    var oConfig = this.cfg;

    if(p_oConfig) {

        oConfig.applyConfig(p_oConfig, true);

    }

    oConfig.fireQueue();

},

// Constants

/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the 
* <code>&#60;li&#62;</code> element of the menu bar item.
* @default "yuimenubaritem"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuimenubaritem",

/**
* @property SUBMENU_INDICATOR_IMAGE_PATH
* @description String representing the path to the image to be used for the 
* menu bar item's submenu arrow indicator.
* @default "nt/ic/ut/alt1/menuarodwn8_nrm_1.gif"
* @final
* @type String
*/
SUBMENU_INDICATOR_IMAGE_PATH: "nt/ic/ut/alt1/menuarodwn8_nrm_1.gif",

/**
* @property SELECTED_SUBMENU_INDICATOR_IMAGE_PATH
* @description String representing the path to the image to be used for the 
* submenu arrow indicator when the menu bar item is selected.
* @default "nt/ic/ut/alt1/menuarodwn8_hov_1.gif"
* @final
* @type String
*/
SELECTED_SUBMENU_INDICATOR_IMAGE_PATH: "nt/ic/ut/alt1/menuarodwn8_hov_1.gif",

/**
* @property DISABLED_SUBMENU_INDICATOR_IMAGE_PATH
* @description String representing the path to the image to be used for the 
* submenu arrow indicator when the menu bar item is disabled.
* @default "nt/ic/ut/alt1/menuarodwn8_dim_1.gif"
* @final
* @type String
*/
DISABLED_SUBMENU_INDICATOR_IMAGE_PATH: "nt/ic/ut/alt1/menuarodwn8_dim_1.gif",

// Public methods

/**
* @method toString
* @description Returns a string representing the menu bar item.
* @return {String}
*/
toString: function() {

    return ("MenuBarItem: " + this.cfg.getProperty("text"));

}
    
}); // END YAHOO.extend