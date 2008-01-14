/*
Copyright (c) 2006, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 0.12.2
*/
YAHOO.util.Lang = {
    isArray: function(val) { // frames lose type, so test constructor string
        if (val.constructor && val.constructor.toString().indexOf('Array') > -1) {
            return true;
        } else {
            return YAHOO.util.Lang.isObject(val) && val.constructor == Array;
        }
    },
        
    isBoolean: function(val) {
        return typeof val == 'boolean';
    },
    
    isFunction: function(val) {
        return typeof val == 'function';
    },
        
    isNull: function(val) {
        return val === null;
    },
        
    isNumber: function(val) {
        return !isNaN(val);
    },
        
    isObject: function(val) {
        return typeof val == 'object' || YAHOO.util.Lang.isFunction(val);
    },
        
    isString: function(val) {
        return typeof val == 'string';
    },
        
    isUndefined: function(val) {
        return typeof val == 'undefined';
    }
};

/**
 * Provides Attribute configurations.
 * @namespace YAHOO.util
 * @class Attribute
 * @constructor
 * @param hash {Object} The intial Attribute.
 * @param {YAHOO.util.AttributeProvider} The owner of the Attribute instance.
 */

YAHOO.util.Attribute = function(hash, owner) {
    if (owner) { 
        this.owner = owner;
        this.configure(hash, true);
    }
};

YAHOO.util.Attribute.prototype = {
	/**
     * The name of the attribute.
	 * @property name
	 * @type String
	 */
    name: undefined,
    
	/**
     * The value of the attribute.
	 * @property value
	 * @type String
	 */
    value: null,
    
	/**
     * The owner of the attribute.
	 * @property owner
	 * @type YAHOO.util.AttributeProvider
	 */
    owner: null,
    
	/**
     * Whether or not the attribute is read only.
	 * @property readOnly
	 * @type Boolean
	 */
    readOnly: false,
    
	/**
     * Whether or not the attribute can only be written once.
	 * @property writeOnce
	 * @type Boolean
	 */
    writeOnce: false,

	/**
     * The attribute's initial configuration.
     * @private
	 * @property _initialConfig
	 * @type Object
	 */
    _initialConfig: null,
    
	/**
     * Whether or not the attribute's value has been set.
     * @private
	 * @property _written
	 * @type Boolean
	 */
    _written: false,
    
	/**
     * The method to use when setting the attribute's value.
     * The method recieves the new value as the only argument.
	 * @property method
	 * @type Function
	 */
    method: null,
    
	/**
     * The validator to use when setting the attribute's value.
	 * @property validator
	 * @type Function
     * @return Boolean
	 */
    validator: null,
    
    /**
     * Retrieves the current value of the attribute.
     * @method getValue
     * @return {any} The current value of the attribute.
     */
    getValue: function() {
        return this.value;
    },
    
    /**
     * Sets the value of the attribute and fires beforeChange and change events.
     * @method setValue
     * @param {Any} value The value to apply to the attribute.
     * @param {Boolean} silent If true the change events will not be fired.
     * @return {Boolean} Whether or not the value was set.
     */
    setValue: function(value, silent) {
        var beforeRetVal;
        var owner = this.owner;
        var name = this.name;
        
        var event = {
            type: name, 
            prevValue: this.getValue(),
            newValue: value
        };
        
        if (this.readOnly || ( this.writeOnce && this._written) ) {
            return false; // write not allowed
        }
        
        if (this.validator && !this.validator.call(owner, value) ) {
            return false; // invalid value
        }

        if (!silent) {
            beforeRetVal = owner.fireBeforeChangeEvent(event);
            if (beforeRetVal === false) {
                return false;
            }
        }

        if (this.method) {
            this.method.call(owner, value);
        }
        
        this.value = value;
        this._written = true;
        
        event.type = name;
        
        if (!silent) {
            this.owner.fireChangeEvent(event);
        }
        
        return true;
    },
    
    /**
     * Allows for configuring the Attribute's properties.
     * @method configure
     * @param {Object} map A key-value map of Attribute properties.
     * @param {Boolean} init Whether or not this should become the initial config.
     */
    configure: function(map, init) {
        map = map || {};
        this._written = false; // reset writeOnce
        this._initialConfig = this._initialConfig || {};
        
        for (var key in map) {
            if ( key && map.hasOwnProperty(key) ) {
                this[key] = map[key];
                if (init) {
                    this._initialConfig[key] = map[key];
                }
            }
        }
    },
    
    /**
     * Resets the value to the initial config value.
     * @method resetValue
     * @return {Boolean} Whether or not the value was set.
     */
    resetValue: function() {
        return this.setValue(this._initialConfig.value);
    },
    
    /**
     * Resets the attribute config to the initial config state.
     * @method resetConfig
     */
    resetConfig: function() {
        this.configure(this._initialConfig);
    },
    
    /**
     * Resets the value to the current value.
     * Useful when values may have gotten out of sync with actual properties.
     * @method refresh
     * @return {Boolean} Whether or not the value was set.
     */
    refresh: function(silent) {
        this.setValue(this.value, silent);
    }
};

(function() {
    var Lang = YAHOO.util.Lang;

    /*
    Copyright (c) 2006, Yahoo! Inc. All rights reserved.
    Code licensed under the BSD License:
    http://developer.yahoo.net/yui/license.txt
    */
    
    /**
     * Provides and manages YAHOO.util.Attribute instances
     * @namespace YAHOO.util
     * @class AttributeProvider
     * @uses YAHOO.util.EventProvider
     */
    YAHOO.util.AttributeProvider = function() {};
    
    YAHOO.util.AttributeProvider.prototype = {
        
        /**
         * A key-value map of Attribute configurations
         * @property _configs
         * @protected (may be used by subclasses and augmentors)
         * @private
         * @type {Object}
         */
        _configs: null,
        /**
         * Returns the current value of the attribute.
         * @method get
         * @param {String} key The attribute whose value will be returned.
         */
        get: function(key){
            var configs = this._configs || {};
            var config = configs[key];
            
            if (!config) {
                return undefined;
            }
            
            return config.value;
        },
        
        /**
         * Sets the value of a config.
         * @method set
         * @param {String} key The name of the attribute
         * @param {Any} value The value to apply to the attribute
         * @param {Boolean} silent Whether or not to suppress change events
         * @return {Boolean} Whether or not the value was set.
         */
        set: function(key, value, silent){
            var configs = this._configs || {};
            var config = configs[key];
            
            if (!config) {
                return false;
            }
            
            return config.setValue(value, silent);
        },
    
        /**
         * Returns an array of attribute names.
         * @method getAttributeKeys
         * @return {Array} An array of attribute names.
         */
        getAttributeKeys: function(){
            var configs = this._configs;
            var keys = [];
            var config;
            for (var key in configs) {
                config = configs[key];
                if ( configs.hasOwnProperty(key) && 
                        !Lang.isUndefined(config) ) {
                    keys[keys.length] = key;
                }
            }
            
            return keys;
        },
        
        /**
         * Sets multiple attribute values.
         * @method setAttributes
         * @param {Object} map  A key-value map of attributes
         * @param {Boolean} silent Whether or not to suppress change events
         */
        setAttributes: function(map, silent){
            for (var key in map) {
                if ( map.hasOwnProperty(key) ) {
                    this.set(key, map[key], silent);
                }
            }
        },
    
        /**
         * Resets the specified attribute's value to its initial value.
         * @method resetValue
         * @param {String} key The name of the attribute
         * @param {Boolean} silent Whether or not to suppress change events
         * @return {Boolean} Whether or not the value was set
         */
        resetValue: function(key, silent){
            var configs = this._configs || {};
            if (configs[key]) {
                this.set(key, configs[key]._initialConfig.value, silent);
                return true;
            }
            return false;
        },
    
        /**
         * Sets the attribute's value to its current value.
         * @method refresh
         * @param {String | Array} key The attribute(s) to refresh
         * @param {Boolean} silent Whether or not to suppress change events
         */
        refresh: function(key, silent){
            var configs = this._configs;
            
            key = ( ( Lang.isString(key) ) ? [key] : key ) || 
                    this.getAttributeKeys();
            
            for (var i = 0, len = key.length; i < len; ++i) { 
                if ( // only set if there is a value and not null
                    configs[key[i]] && 
                    ! Lang.isUndefined(configs[key[i]].value) &&
                    ! Lang.isNull(configs[key[i]].value) ) {
                    configs[key[i]].refresh(silent);
                }
            }
        },
    
        /**
         * Adds an Attribute to the AttributeProvider instance. 
         * @method register
         * @param {String} key The attribute's name
         * @param {Object} map A key-value map containing the
         * attribute's properties.
         */
        register: function(key, map) {
            this._configs = this._configs || {};
            
            if (this._configs[key]) { // dont override
                return false;
            }
            
            map.name = key;
            this._configs[key] = new YAHOO.util.Attribute(map, this);
            return true;
        },
        
        /**
         * Returns the attribute's properties.
         * @method getAttributeConfig
         * @param {String} key The attribute's name
         * @private
         * @return {object} A key-value map containing all of the
         * attribute's properties.
         */
        getAttributeConfig: function(key) {
            var configs = this._configs || {};
            var config = configs[key] || {};
            var map = {}; // returning a copy to prevent overrides
            
            for (key in config) {
                if ( config.hasOwnProperty(key) ) {
                    map[key] = config[key];
                }
            }
    
            return map;
        },
        
        /**
         * Sets or updates an Attribute instance's properties. 
         * @method configureAttribute
         * @param {String} key The attribute's name.
         * @param {Object} map A key-value map of attribute properties
         * @param {Boolean} init Whether or not this should become the intial config.
         */
        configureAttribute: function(key, map, init) {
            var configs = this._configs || {};
            
            if (!configs[key]) {
                return false;
            }
            
            configs[key].configure(map, init);
        },
        
        /**
         * Resets an attribute to its intial configuration. 
         * @method resetAttributeConfig
         * @param {String} key The attribute's name.
         * @private
         */
        resetAttributeConfig: function(key){
            var configs = this._configs || {};
            configs[key].resetConfig();
        },
        
        /**
         * Fires the attribute's beforeChange event. 
         * @method fireBeforeChangeEvent
         * @param {String} key The attribute's name.
         * @param {Obj} e The event object to pass to handlers.
         */
        fireBeforeChangeEvent: function(e) {
            var type = 'before';
            type += e.type.charAt(0).toUpperCase() + e.type.substr(1) + 'Change';
            e.type = type;
            return this.fireEvent(e.type, e);
        },
        
        /**
         * Fires the attribute's change event. 
         * @method fireChangeEvent
         * @param {String} key The attribute's name.
         * @param {Obj} e The event object to pass to the handlers.
         */
        fireChangeEvent: function(e) {
            e.type += 'Change';
            return this.fireEvent(e.type, e);
        }
    };
    
    YAHOO.augment(YAHOO.util.AttributeProvider, YAHOO.util.EventProvider);
})();

(function() {
// internal shorthand
var Dom = YAHOO.util.Dom,
    Lang = YAHOO.util.Lang,
    EventPublisher = YAHOO.util.EventPublisher,
    AttributeProvider = YAHOO.util.AttributeProvider;

/**
 * Element provides an interface to an HTMLElement's attributes and common
 * methods.  Other commonly used attributes are added as well.
 * @namespace YAHOO.util
 * @class Element
 * @uses YAHOO.util.AttributeProvider
 * @constructor
 * @param el {HTMLElement | String} The html element that 
 * represents the Element.
 * @param {Object} map A key-value map of initial config names and values
 */
YAHOO.util.Element = function(el, map) {
    if (arguments.length) {
        this.init(el, map);
    }
};

YAHOO.util.Element.prototype = {
	/**
     * Dom events supported by the Element instance.
	 * @property DOM_EVENTS
	 * @type Object
	 */
    DOM_EVENTS: null,

	/**
     * Wrapper for HTMLElement method.
	 * @method appendChild
	 * @param {Boolean} deep Whether or not to do a deep clone
	 */
    appendChild: function(child) {
        child = child.get ? child.get('element') : child;
        this.get('element').appendChild(child);
    },
    
	/**
     * Wrapper for HTMLElement method.
	 * @method getElementsByTagName
	 * @param {String} tag The tagName to collect
	 */
    getElementsByTagName: function(tag) {
        return this.get('element').getElementsByTagName(tag);
    },
    
	/**
     * Wrapper for HTMLElement method.
	 * @method hasChildNodes
	 * @return {Boolean} Whether or not the element has childNodes
	 */
    hasChildNodes: function() {
        return this.get('element').hasChildNodes();
    },
    
	/**
     * Wrapper for HTMLElement method.
	 * @method insertBefore
	 * @param {HTMLElement} element The HTMLElement to insert
	 * @param {HTMLElement} before The HTMLElement to insert
     * the element before.
	 */
    insertBefore: function(element, before) {
        element = element.get ? element.get('element') : element;
        before = (before && before.get) ? before.get('element') : before;
        
        this.get('element').insertBefore(element, before);
    },
    
	/**
     * Wrapper for HTMLElement method.
	 * @method removeChild
	 * @param {HTMLElement} child The HTMLElement to remove
	 */
    removeChild: function(child) {
        child = child.get ? child.get('element') : child;
        this.get('element').removeChild(child);
        return true;
    },
    
	/**
     * Wrapper for HTMLElement method.
	 * @method replaceChild
	 * @param {HTMLElement} newNode The HTMLElement to insert
	 * @param {HTMLElement} oldNode The HTMLElement to replace
	 */
    replaceChild: function(newNode, oldNode) {
        newNode = newNode.get ? newNode.get('element') : newNode;
        oldNode = oldNode.get ? oldNode.get('element') : oldNode;
        return this.get('element').replaceChild(newNode, oldNode);
    },

    
    /**
     * Registers Element specific attributes.
     * @method initAttributes
     * @param {Object} map A key-value map of initial attribute configs
     */
    initAttributes: function(map) {
        map = map || {}; 
        var element = Dom.get(map.element) || null;
        
        /**
         * The HTMLElement the Element instance refers to.
         * @config element
         * @type HTMLElement
         */
        this.register('element', {
            value: element,
            readOnly: true
         });
    },

    /**
     * Adds a listener for the given event.  These may be DOM or 
     * customEvent listeners.  Any event that is fired via fireEvent
     * can be listened for.  All handlers receive an event object. 
     * @method addListener
     * @param {String} type The name of the event to listen for
     * @param {Function} fn The handler to call when the event fires
     * @param {Any} obj A variable to pass to the handler
     * @param {Object} scope The object to use for the scope of the handler 
     */
    addListener: function(type, fn, obj, scope) {
        var el = this.get('element');
        var scope = scope || this;
        
        el = this.get('id') || el;
        
        if (!this._events[type]) { // create on the fly
            if ( this.DOM_EVENTS[type] ) {
                YAHOO.util.Event.addListener(el, type, function(e) {
                    if (e.srcElement && !e.target) { // supplement IE with target
                        e.target = e.srcElement;
                    }
                    this.fireEvent(type, e);
                }, obj, scope);
            }
            
            this.createEvent(type, this);
            this._events[type] = true;
        }
        
        this.subscribe.apply(this, arguments); // notify via customEvent
    },
    
    
    /**
     * Alias for addListener
     * @method on
     * @param {String} type The name of the event to listen for
     * @param {Function} fn The function call when the event fires
     * @param {Any} obj A variable to pass to the handler
     * @param {Object} scope The object to use for the scope of the handler 
     */
    on: function() { this.addListener.apply(this, arguments); },
    
    
    /**
     * Remove an event listener
     * @method removeListener
     * @param {String} type The name of the event to listen for
     * @param {Function} fn The function call when the event fires
     */
    removeListener: function(type, fn) {
        this.unsubscribe.apply(this, arguments);
    },
    
	/**
     * Wrapper for Dom method.
	 * @method addClass
	 * @param {String} className The className to add
	 */
    addClass: function(className) {
        Dom.addClass(this.get('element'), className);
    },
    
	/**
     * Wrapper for Dom method.
	 * @method getElementsByClassName
	 * @param {String} className The className to collect
	 * @param {String} tag (optional) The tag to use in
     * conjunction with class name
     * @return {Array} Array of HTMLElements
	 */
    getElementsByClassName: function(className, tag) {
        return Dom.getElementsByClassName(className, tag,
                this.get('element') );
    },
    
	/**
     * Wrapper for Dom method.
	 * @method hasClass
	 * @param {String} className The className to add
     * @return {Boolean} Whether or not the element has the class name
	 */
    hasClass: function(className) {
        return Dom.hasClass(this.get('element'), className); 
    },
    
	/**
     * Wrapper for Dom method.
	 * @method removeClass
	 * @param {String} className The className to remove
	 */
    removeClass: function(className) {
        return Dom.removeClass(this.get('element'), className);
    },
    
	/**
     * Wrapper for Dom method.
	 * @method replaceClass
	 * @param {String} oldClassName The className to replace
	 * @param {String} newClassName The className to add
	 */
    replaceClass: function(oldClassName, newClassName) {
        return Dom.replaceClass(this.get('element'), 
                oldClassName, newClassName);
    },
    
	/**
     * Wrapper for Dom method.
	 * @method setStyle
	 * @param {String} property The style property to set
	 * @param {String} value The value to apply to the style property
	 */
    setStyle: function(property, value) {
        return Dom.setStyle(this.get('element'),  property, value);
    },
    
	/**
     * Wrapper for Dom method.
	 * @method getStyle
	 * @param {String} property The style property to retrieve
	 * @return {String} The current value of the property
	 */
    getStyle: function(property) {
        return Dom.getStyle(this.get('element'),  property);
    },
    
	/**
     * Apply any queued set calls.
	 * @method fireQueue
	 */
    fireQueue: function() {
        var queue = this._queue;
        for (var i = 0, len = queue.length; i < len; ++i) {
            this[queue[i][0]].apply(this, queue[i][1]);
        }
    },
    
	/**
     * Appends the HTMLElement into either the supplied parentNode.
	 * @method appendTo
	 * @param {HTMLElement | Element} parentNode The node to append to
	 * @param {HTMLElement | Element} before An optional node to insert before
	 */
    appendTo: function(parent, before) {
        parent = (parent.get) ?  parent.get('element') : Dom.get(parent);
        
        before = (before && before.get) ? 
                before.get('element') : Dom.get(before);
        var element = this.get('element');
        
        var newAddition =  !Dom.inDocument(element);
        
        if (!element) {
            return false;
        }
        
        if (!parent) {
            return false;
        }
        
        if (element.parent != parent) {
            if (before) {
                parent.insertBefore(element, before);
            } else {
                parent.appendChild(element);
            }
        }
        
        
        if (!newAddition) {
            return false; // note return; no refresh if in document
        }
        
        // if a new addition, refresh HTMLElement any applied attributes
        var keys = this.getAttributeKeys();
        
        for (var key in keys) { // only refresh HTMLElement attributes
            if ( !Lang.isUndefined(element[key]) ) {
                this.refresh(key);
            }
        }
    },
    
    get: function(key) {
        var configs = this._configs || {};
        var el = configs.element; // avoid loop due to 'element'
        if (el && !configs[key] && !Lang.isUndefined(el.value[key]) ) {
            return el.value[key];
        }

        return AttributeProvider.prototype.get.call(this, key);
    },

    set: function(key, value, silent) {
        var el = this.get('element');
        if (!el) {
            this._queue[this._queue.length] = ['set', arguments];
            return false;
        }
        
        // set it on the element if not a property
        if ( !this._configs[key] && !Lang.isUndefined(el[key]) ) {
            _registerHTMLAttr.call(this, key);
        }

        return AttributeProvider.prototype.set.apply(this, arguments);
    },
    
    register: function(key) { // protect html attributes
        var configs = this._configs || {};
        var element = this.get('element') || null;
        
        if ( element && !Lang.isUndefined(element[key]) ) {
            return false;
        }
        
        return AttributeProvider.prototype.register.apply(this, arguments);
    },
    
    configureAttribute: function(property, map, init) { // protect html attributes
        var el = this.get('element');
        if (!el) {
            this._queue[this._queue.length] = ['configureAttribute', arguments];
            return;
        }
        
        if (!this._configs[property] && !Lang.isUndefined(el[property]) ) {
            _registerHTMLAttr.call(this, property, map);
        }
        
        return AttributeProvider.prototype.configureAttribute.apply(this, arguments);
    },
    
    getAttributeKeys: function() {
        var el = this.get('element');
        var keys = AttributeProvider.prototype.getAttributeKeys.call(this);
        
        //add any unconfigured element keys
        for (var key in el) {
            if (!this._configs[key]) {
                keys[key] = keys[key] || el[key];
            }
        }
        
        return keys;
    },
    
    init: function(el, attr) {
        this._queue = this._queue || [];
        this._events = this._events || {};
        this._configs = this._configs || {};
        attr = attr || {};
        attr.element = attr.element || el || null;

        this.DOM_EVENTS = {
            'click': true,
            'keydown': true,
            'keypress': true,
            'keyup': true,
            'mousedown': true,
            'mousemove': true,
            'mouseout': true, 
            'mouseover': true, 
            'mouseup': true
        };
        
        var readyHandler = function() {
            this.initAttributes(attr);

            this.setAttributes(attr, true);
            this.fireQueue();
            this.fireEvent('contentReady', {
                type: 'contentReady',
                target: attr.element
            });
        };

        if ( Lang.isString(el) ) {
            _registerHTMLAttr.call(this, 'id', { value: el });
            YAHOO.util.Event.onAvailable(el, function() {
                attr.element = Dom.get(el);
                this.fireEvent('available', {
                    type: 'available',
                    target: attr.element
                }); 
            }, this, true);
            
            YAHOO.util.Event.onContentReady(el, function() {
                readyHandler.call(this);
            }, this, true);
        } else {
            readyHandler.call(this);
        }        
    }
};

/**
 * Sets the value of the property and fires beforeChange and change events.
 * @private
 * @method _registerHTMLAttr
 * @param {YAHOO.util.Element} element The Element instance to
 * register the config to.
 * @param {String} key The name of the config to register
 * @param {Object} map A key-value map of the config's params
 */
var _registerHTMLAttr = function(key, map) {
    var el = this.get('element');
    map = map || {};
    map.name = key;
    map.method = map.method || function(value) {
        el[key] = value;
    };
    map.value = map.value || el[key];
    this._configs[key] = new YAHOO.util.Attribute(map, this);
};

/**
 * Fires when the Element's HTMLElement can be retrieved by Id.
 * <p>See: <a href="#addListener">Element.addListener</a></p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> available<br>
 * <code>&lt;HTMLElement&gt;
 * target</code> the HTMLElement bound to this Element instance<br>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var target = e.target};<br>
 * myTabs.addListener('available', handler);</code></p>
 * @event available
 */
 
/**
 * Fires when the Element's HTMLElement subtree is rendered.
 * <p>See: <a href="#addListener">Element.addListener</a></p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> contentReady<br>
 * <code>&lt;HTMLElement&gt;
 * target</code> the HTMLElement bound to this Element instance<br>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var target = e.target};<br>
 * myTabs.addListener('contentReady', handler);</code></p>
 * @event contentReady
 */

YAHOO.augment(YAHOO.util.Element, AttributeProvider);
})();

(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
        Lang = YAHOO.util.Lang;
    
    /**
     * A representation of a Tab's label and content.
     * @namespace YAHOO.widget
     * @class Tab
     * @extends YAHOO.util.Element
     * @constructor
     * @param element {HTMLElement | String} (optional) The html element that 
     * represents the TabView. An element will be created if none provided.
     * @param {Object} properties A key map of initial properties
     */
    var Tab = function(el, attr) {
        attr = attr || {};
        if (arguments.length == 1 && !Lang.isString(el) && !el.nodeName) {
            attr = el;
            el = attr.element;
        }

        if (!el && !attr.element) {
            el = _createTabElement.call(this, attr);
        }

        this.loadHandler =  {
            success: function(o) {
                this.set('content', o.responseText);
            },
            failure: function(o) {
            }
        };
        
        Tab.superclass.constructor.call(this, el, attr);
        
        this.DOM_EVENTS = {}; // delegating to tabView
    };

    YAHOO.extend(Tab, YAHOO.util.Element);
    var proto = Tab.prototype;
    
    /**
     * The default tag name for a Tab's inner element.
     * @property LABEL_INNER_TAGNAME
     * @type String
     * @default "em"
     */
    proto.LABEL_TAGNAME = 'em';
    
    /**
     * The class name applied to active tabs.
     * @property ACTIVE_CLASSNAME
     * @type String
     * @default "on"
     */
    proto.ACTIVE_CLASSNAME = 'selected';
    
    /**
     * The class name applied to disabled tabs.
     * @property DISABLED_CLASSNAME
     * @type String
     * @default "disabled"
     */
    proto.DISABLED_CLASSNAME = 'disabled';
    
    /**
     * The class name applied to dynamic tabs while loading.
     * @property LOADING_CLASSNAME
     * @type String
     * @default "disabled"
     */
    proto.LOADING_CLASSNAME = 'loading';

    /**
     * Provides a reference to the connection request object when data is
     * loaded dynamically.
     * @property dataConnection
     * @type Object
     */
    proto.dataConnection = null;
    
    /**
     * Object containing success and failure callbacks for loading data.
     * @property loadHandler
     * @type object
     */
    proto.loadHandler = null;
    
    /**
     * Provides a readable name for the tab.
     * @method toString
     * @return String
     */
    proto.toString = function() {
        var el = this.get('element');
        var id = el.id || el.tagName;
        return "Tab " + id; 
    };
    
    /**
     * Registers TabView specific properties.
     * @method initAttributes
     * @param {Object} attr Hash of initial attributes
     */
    proto.initAttributes = function(attr) {
        attr = attr || {};
        Tab.superclass.initAttributes.call(this, attr);
        
        var el = this.get('element');
        
        /**
         * The event that triggers the tab's activation.
         * @config activationEvent
         * @type String
         */
        this.register('activationEvent', {
            value: attr.activationEvent || 'click'
        });        

        /**
         * The element that contains the tab's label.
         * @config labelEl
         * @type HTMLElement
         */
        this.register('labelEl', {
            value: attr.labelEl || _getlabelEl.call(this),
            method: function(value) {
                var current = this.get('labelEl');

                if (current) {
                    if (current == value) {
                        return false; // already set
                    }
                    
                    this.replaceChild(value, current);
                } else if (el.firstChild) { // ensure label is firstChild by default
                    this.insertBefore(value, el.firstChild);
                } else {
                    this.appendChild(value);
                }  
            } 
        });

        /**
         * The tab's label text (or innerHTML).
         * @config label
         * @type String
         */
        this.register('label', {
            value: attr.label || _getLabel.call(this),
            method: function(value) {
                var labelEl = this.get('labelEl');
                if (!labelEl) { // create if needed
                    this.set('labelEl', _createlabelEl.call(this));
                }
                
                _setLabel.call(this, value);
            }
        });
        
        /**
         * The HTMLElement that contains the tab's content.
         * @config contentEl
         * @type HTMLElement
         */
        this.register('contentEl', { // TODO: apply className?
            value: attr.contentEl || document.createElement('div'),
            method: function(value) {
                var current = this.get('contentEl');

                if (current) {
                    if (current == value) {
                        return false; // already set
                    }
                    this.replaceChild(value, current);
                }
            }
        });
        
        /**
         * The tab's content.
         * @config content
         * @type String
         */
        this.register('content', {
            value: attr.content, // TODO: what about existing?
            method: function(value) {
                this.get('contentEl').innerHTML = value;
            }
        });

        var _dataLoaded = false;
        
        /**
         * The tab's data source, used for loading content dynamically.
         * @config dataSrc
         * @type String
         */
        this.register('dataSrc', {
            value: attr.dataSrc
        });
        
        /**
         * Whether or not content should be reloaded for every view.
         * @config cacheData
         * @type Boolean
         * @default false
         */
        this.register('cacheData', {
            value: attr.cacheData || false,
            validator: Lang.isBoolean
        });
        
        /**
         * The method to use for the data request.
         * @config loadMethod
         * @type String
         * @default "GET"
         */
        this.register('loadMethod', {
            value: attr.loadMethod || 'GET',
            validator: Lang.isString
        });

        /**
         * Whether or not any data has been loaded from the server.
         * @config dataLoaded
         * @type Boolean
         */        
        this.register('dataLoaded', {
            value: false,
            validator: Lang.isBoolean,
            writeOnce: true
        });
        
        /**
         * Number if milliseconds before aborting and calling failure handler.
         * @config dataTimeout
         * @type Number
         * @default null
         */
        this.register('dataTimeout', {
            value: attr.dataTimeout || null,
            validator: Lang.isNumber
        });
        
        /**
         * Whether or not the tab is currently active.
         * If a dataSrc is set for the tab, the content will be loaded from
         * the given source.
         * @config active
         * @type Boolean
         */
        this.register('active', {
            value: attr.active || this.hasClass(this.ACTIVE_CLASSNAME),
            method: function(value) {
                if (value === true) {
                    this.addClass(this.ACTIVE_CLASSNAME);
                    this.set('title', 'active');
                } else {
                    this.removeClass(this.ACTIVE_CLASSNAME);
                    this.set('title', '');
                }
            },
            validator: function(value) {
                return Lang.isBoolean(value) && !this.get('disabled') ;
            }
        });
        
        /**
         * Whether or not the tab is disabled.
         * @config disabled
         * @type Boolean
         */
        this.register('disabled', {
            value: attr.disabled || this.hasClass(this.DISABLED_CLASSNAME),
            method: function(value) {
                if (value === true) {
                    Dom.addClass(this.get('element'), this.DISABLED_CLASSNAME);
                } else {
                    Dom.removeClass(this.get('element'), this.DISABLED_CLASSNAME);
                }
            },
            validator: Lang.isBoolean
        });
        
        /**
         * The href of the tab's anchor element.
         * @config href
         * @type String
         * @default '#'
         */
        this.register('href', {
            value: attr.href || '#',
            method: function(value) {
                this.getElementsByTagName('a')[0].href = value;
            },
            validator: Lang.isString
        });
        
        /**
         * The Whether or not the tab's content is visible.
         * @config contentVisible
         * @type Boolean
         * @default false
         */
        this.register('contentVisible', {
            value: attr.contentVisible,
            method: function(value) {
                if (value == true) {
                    this.get('contentEl').style.display = 'block';
                    
                    if ( this.get('dataSrc') ) {
                     // load dynamic content unless already loaded and caching
                        if ( !this.get('dataLoaded') || !this.get('cacheData') ) {
                            _dataConnect.call(this);
                        }
                    }
                } else {
                    this.get('contentEl').style.display = 'none';
                }
            },
            validator: Lang.isBoolean
        });
    };
    
    var _createTabElement = function(attr) {
        var el = document.createElement('li');
        var a = document.createElement('a');
        
        a.href = attr.href || '#';
        
        el.appendChild(a);
        
        var label = attr.label || null;
        var labelEl = attr.labelEl || null;
        
        if (labelEl) { // user supplied labelEl
            if (!label) { // user supplied label
                label = _getLabel.call(this, labelEl);
            }
        } else {
            labelEl = _createlabelEl.call(this);
        }
        
        a.appendChild(labelEl);
        
        return el;
    };
    
    var _getlabelEl = function() {
        return this.getElementsByTagName(this.LABEL_TAGNAME)[0];
    };
    
    var _createlabelEl = function() {
        var el = document.createElement(this.LABEL_TAGNAME);
        return el;
    };
    
    var _setLabel = function(label) {
        var el = this.get('labelEl');
        el.innerHTML = label;
    };
    
    var _getLabel = function() {
        var label,
            el = this.get('labelEl');
            
            if (!el) {
                return undefined;
            }
        
        return el.innerHTML;
    };
    
    var _dataConnect = function() {
        if (!YAHOO.util.Connect) {
            return false;
        }

        Dom.addClass(this.get('contentEl').parentNode, this.LOADING_CLASSNAME);
        
        this.dataConnection = YAHOO.util.Connect.asyncRequest(
            this.get('loadMethod'),
            this.get('dataSrc'), 
            {
                success: function(o) {
                    this.loadHandler.success.call(this, o);
                    this.set('dataLoaded', true);
                    this.dataConnection = null;
                    Dom.removeClass(this.get('contentEl').parentNode,
                            this.LOADING_CLASSNAME);
                },
                failure: function(o) {
                    this.loadHandler.failure.call(this, o);
                    this.dataConnection = null;
                    Dom.removeClass(this.get('contentEl').parentNode,
                            this.LOADING_CLASSNAME);
                },
                scope: this,
                timeout: this.get('dataTimeout')
            }
        );
    };
    
    YAHOO.widget.Tab = Tab;
    
    /**
     * Fires before the active state is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p>If handler returns false, the change will be cancelled, and the value will not
     * be set.</p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> beforeActiveChange<br>
     * <code>&lt;Boolean&gt;
     * prevValue</code> the current value<br>
     * <code>&lt;Boolean&gt;
     * newValue</code> the new value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('beforeActiveChange', handler);</code></p>
     * @event beforeActiveChange
     */
        
    /**
     * Fires after the active state is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> activeChange<br>
     * <code>&lt;Boolean&gt;
     * prevValue</code> the previous value<br>
     * <code>&lt;Boolean&gt;
     * newValue</code> the updated value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('activeChange', handler);</code></p>
     * @event activeChange
     */
     
    /**
     * Fires before the tab label is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p>If handler returns false, the change will be cancelled, and the value will not
     * be set.</p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> beforeLabelChange<br>
     * <code>&lt;String&gt;
     * prevValue</code> the current value<br>
     * <code>&lt;String&gt;
     * newValue</code> the new value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('beforeLabelChange', handler);</code></p>
     * @event beforeLabelChange
     */
        
    /**
     * Fires after the tab label is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> labelChange<br>
     * <code>&lt;String&gt;
     * prevValue</code> the previous value<br>
     * <code>&lt;String&gt;
     * newValue</code> the updated value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('labelChange', handler);</code></p>
     * @event labelChange
     */
     
    /**
     * Fires before the tab content is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p>If handler returns false, the change will be cancelled, and the value will not
     * be set.</p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> beforeContentChange<br>
     * <code>&lt;String&gt;
     * prevValue</code> the current value<br>
     * <code>&lt;String&gt;
     * newValue</code> the new value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('beforeContentChange', handler);</code></p>
     * @event beforeContentChange
     */
        
    /**
     * Fires after the tab content is changed.
     * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
     * <p><strong>Event fields:</strong><br>
     * <code>&lt;String&gt; type</code> contentChange<br>
     * <code>&lt;String&gt;
     * prevValue</code> the previous value<br>
     * <code>&lt;Boolean&gt;
     * newValue</code> the updated value</p>
     * <p><strong>Usage:</strong><br>
     * <code>var handler = function(e) {var previous = e.prevValue};<br>
     * myTabs.addListener('contentChange', handler);</code></p>
     * @event contentChange
     */
})();

(function() {

    /**
     * The tabview module provides a widget for managing content bound to tabs.
     * @module tabview
     * @requires yahoo, dom, event
     *
     */
    /**
     * A widget to control tabbed views.
     * @namespace YAHOO.widget
     * @class TabView
     * @extends YAHOO.util.Element
     * @constructor
     * @param {HTMLElement | String | Object} el(optional) The html 
     * element that represents the TabView, or the attribute object to use. 
     * An element will be created if none provided.
     * @param {Object} attr (optional) A key map of the tabView's 
     * initial attributes.  Ignored if first arg is attributes object.
     */
    YAHOO.widget.TabView = function(el, attr) {
        attr = attr || {};
        if (arguments.length == 1 && !Lang.isString(el) && !el.nodeName) {
            attr = el; // treat first arg as attr object
            el = attr.element || null;
        }
        
        if (!el && !attr.element) { // create if we dont have one
            el = _createTabViewElement.call(this, attr);
        }
    	YAHOO.widget.TabView.superclass.constructor.call(this, el, attr); 
    };

    YAHOO.extend(YAHOO.widget.TabView, YAHOO.util.Element);
    
    var proto = YAHOO.widget.TabView.prototype;
    var Dom = YAHOO.util.Dom;
    var Lang = YAHOO.util.Lang;
    var Event = YAHOO.util.Event;
    var Tab = YAHOO.widget.Tab;
    
    
    /**
     * The className to add when building from scratch. 
     * @property CLASSNAME
     * @default "navset"
     */
    proto.CLASSNAME = 'yui-navset';
    
    /**
     * The className of the HTMLElement containing the TabView's tab elements
     * to look for when building from existing markup, or to add when building
     * from scratch. 
     * All childNodes of the tab container are treated as Tabs when building
     * from existing markup.
     * @property TAB_PARENT_CLASSNAME
     * @default "nav"
     */
    proto.TAB_PARENT_CLASSNAME = 'yui-nav';
    
    /**
     * The className of the HTMLElement containing the TabView's label elements
     * to look for when building from existing markup, or to add when building
     * from scratch. 
     * All childNodes of the content container are treated as content elements when
     * building from existing markup.
     * @property CONTENT_PARENT_CLASSNAME
     * @default "nav-content"
     */
    proto.CONTENT_PARENT_CLASSNAME = 'yui-content';
    
    proto._tabParent = null;
    proto._contentParent = null; 
    
    /**
     * Adds a Tab to the TabView instance.  
     * If no index is specified, the tab is added to the end of the tab list.
     * @method addTab
     * @param {YAHOO.widget.Tab} tab A Tab instance to add.
     * @param {Integer} index The position to add the tab. 
     * @return void
     */
    proto.addTab = function(tab, index) {
        var tabs = this.get('tabs');
        if (!tabs) { // not ready yet
            this._queue[this._queue.length] = ['addTab', arguments];
            return false;
        }
        
        index = (index === undefined) ? tabs.length : index;
        
        var before = this.getTab(index);
        
        var self = this;
        var el = this.get('element');
        var tabParent = this._tabParent;
        var contentParent = this._contentParent;

        var tabElement = tab.get('element');
        var contentEl = tab.get('contentEl');

        if ( before ) {
            tabParent.insertBefore(tabElement, before.get('element'));
        } else {
            tabParent.appendChild(tabElement);
        }

        if ( contentEl && !Dom.isAncestor(contentParent, contentEl) ) {
            contentParent.appendChild(contentEl);
        }
        
        if ( !tab.get('active') ) {
            tab.set('contentVisible', false, true); /* hide if not active */
        } else {
            this.set('activeTab', tab, true);
            
        }

        var activate = function(e) {
            YAHOO.util.Event.preventDefault(e);
            self.set('activeTab', this);
        };
        
        tab.addListener( tab.get('activationEvent'), activate);
        
        tab.addListener('activationEventChange', function(e) {
            if (e.prevValue != e.newValue) {
                tab.removeListener(e.prevValue, activate);
                tab.addListener(e.newValue, activate);
            }
        });
        
        tabs.splice(index, 0, tab);
    };

    /**
     * Routes childNode events.
     * @method DOMEventHandler
     * @param {event} e The Dom event that is being handled.
     * @return void
     */
    proto.DOMEventHandler = function(e) {
        var el = this.get('element');
        var target = YAHOO.util.Event.getTarget(e);
        var tabParent = this._tabParent;
        
        if (Dom.isAncestor(tabParent, target) ) {
            var tabEl;
            var tab = null;
            var contentEl;
            var tabs = this.get('tabs');

            for (var i = 0, len = tabs.length; i < len; i++) {
                tabEl = tabs[i].get('element');
                contentEl = tabs[i].get('contentEl');

                if ( target == tabEl || Dom.isAncestor(tabEl, target) ) {
                    tab = tabs[i];
                    break; // note break
                }
            } 
            
            if (tab) {
                tab.fireEvent(e.type, e);
            }
        }
    };
    
    /**
     * Returns the Tab instance at the specified index.
     * @method getTab
     * @param {Integer} index The position of the Tab.
     * @return YAHOO.widget.Tab
     */
    proto.getTab = function(index) {
    	return this.get('tabs')[index];
    };
    
    /**
     * Returns the index of given tab.
     * @method getTabIndex
     * @param {YAHOO.widget.Tab} tab The tab whose index will be returned.
     * @return int
     */
    proto.getTabIndex = function(tab) {
        var index = null;
        var tabs = this.get('tabs');
    	for (var i = 0, len = tabs.length; i < len; ++i) {
            if (tab == tabs[i]) {
                index = i;
                break;
            }
        }
        
        return index;
    };
    
    /**
     * Removes the specified Tab from the TabView.
     * @method removeTab
     * @param {YAHOO.widget.Tab} item The Tab instance to be removed.
     * @return void
     */
    proto.removeTab = function(tab) {
        var tabCount = this.get('tabs').length;

        var index = this.getTabIndex(tab);
        var nextIndex = index + 1;
        if ( tab == this.get('activeTab') ) { // select next tab
            if (tabCount > 1) {
                if (index + 1 == tabCount) {
                    this.set('activeIndex', index - 1);
                } else {
                    this.set('activeIndex', index + 1);
                }
            }
        }
        
        this._tabParent.removeChild( tab.get('element') );
        this._contentParent.removeChild( tab.get('contentEl') );
        this._configs.tabs.value.splice(index, 1);
    	
    };
    
    /**
     * Provides a readable name for the TabView instance.
     * @method toString
     * @return String
     */
    proto.toString = function() {
        var name = this.get('id') || this.get('tagName');
        return "TabView " + name; 
    };
    
    /**
     * The transiton to use when switching between tabs.
     * @method contentTransition
     */
    proto.contentTransition = function(newTab, oldTab) {
        newTab.set('contentVisible', true);
        oldTab.set('contentVisible', false);
    };
    
    /**
     * Registers TabView specific properties.
     * @method initAttributes
     * @param {Object} attr Hash of initial attributes
     */
    proto.initAttributes = function(attr) {
        YAHOO.widget.TabView.superclass.initAttributes.call(this, attr);
        
        if (!attr.orientation) {
            attr.orientation = 'top';
        }
        
        var el = this.get('element');
        
        /**
         * The Tabs belonging to the TabView instance.
         * @config tabs
         * @type Array
         */
        this.register('tabs', {
            value: [],
            readOnly: true
        });

        /**
         * The container of the tabView's label elements.
         * @property _tabParent
         * @private
         * @type HTMLElement
         */
        this._tabParent = 
                this.getElementsByClassName(this.TAB_PARENT_CLASSNAME,
                        'ul' )[0] || _createTabParent.call(this);
            
        /**
         * The container of the tabView's content elements.
         * @property _contentParent
         * @type HTMLElement
         * @private
         */
        this._contentParent = 
                this.getElementsByClassName(this.CONTENT_PARENT_CLASSNAME,
                        'div')[0] ||  _createContentParent.call(this);
        
        /**
         * How the Tabs should be oriented relative to the TabView.
         * @config orientation
         * @type String
         * @default "top"
         */
        this.register('orientation', {
            value: attr.orientation,
            method: function(value) {
                var current = this.get('orientation');
                this.addClass('yui-navset-' + value);
                
                if (current != value) {
                    this.removeClass('yui-navset-' + current);
                }
                
                switch(value) {
                    case 'bottom':
                    this.appendChild(this._tabParent);
                    break;
                }
            }
        });
        
        /**
         * The index of the tab currently active.
         * @config activeIndex
         * @type Int
         */
        this.register('activeIndex', {
            value: attr.activeIndex,
            method: function(value) {
                this.set('activeTab', this.getTab(value));
            },
            validator: function(value) {
                return !this.getTab(value).get('disabled'); // cannot activate if disabled
            }
        });
        
        /**
         * The tab currently active.
         * @config activeTab
         * @type YAHOO.widget.Tab
         */
        this.register('activeTab', {
            value: attr.activeTab,
            method: function(tab) {
                var activeTab = this.get('activeTab');
                
                if (tab) {  
                    tab.set('active', true);
                }
                
                if (activeTab && activeTab != tab) {
                    activeTab.set('active', false);
                }
                
                if (activeTab && tab != activeTab) { // no transition if only 1
                    this.contentTransition(tab, activeTab);
                } else if (tab) {
                    tab.set('contentVisible', true);
                }
            },
            validator: function(value) {
                return !value.get('disabled'); // cannot activate if disabled
            }
        });

        if ( this._tabParent ) {
            _initTabs.call(this);
        }
        
        for (var type in this.DOM_EVENTS) {
            if ( this.DOM_EVENTS.hasOwnProperty(type) ) {
                this.addListener.call(this, type, this.DOMEventHandler);
            }
        }
    };
    
    /**
     * Creates Tab instances from a collection of HTMLElements.
     * @method createTabs
     * @private
     * @param {Array|HTMLCollection} elements The elements to use for Tabs.
     * @return void
     */
    var _initTabs = function() {
        var tab,
            attr,
            contentEl;
            
        var el = this.get('element');   
        var tabs = _getChildNodes(this._tabParent);
        var contentElements = _getChildNodes(this._contentParent);

        for (var i = 0, len = tabs.length; i < len; ++i) {
            attr = {};
            
            if (contentElements[i]) {
                attr.contentEl = contentElements[i];
            }

            tab = new YAHOO.widget.Tab(tabs[i], attr);
            this.addTab(tab);
            
            if (tab.hasClass(tab.ACTIVE_CLASSNAME) ) {
                this._configs.activeTab.value = tab; // dont invoke method
            }
        }
    };
    
    var _createTabViewElement = function(attr) {
        var el = document.createElement('div');

        if ( this.CLASSNAME ) {
            el.className = this.CLASSNAME;
        }
        
        return el;
    };
    
    var _createTabParent = function(attr) {
        var el = document.createElement('ul');

        if ( this.TAB_PARENT_CLASSNAME ) {
            el.className = this.TAB_PARENT_CLASSNAME;
        }
        
        this.get('element').appendChild(el);
        
        return el;
    };
    
    var _createContentParent = function(attr) {
        var el = document.createElement('div');

        if ( this.CONTENT_PARENT_CLASSNAME ) {
            el.className = this.CONTENT_PARENT_CLASSNAME;
        }
        
        this.get('element').appendChild(el);
        
        return el;
    };
    
    var _getChildNodes = function(el) {
        var nodes = [];
        var childNodes = el.childNodes;
        
        for (var i = 0, len = childNodes.length; i < len; ++i) {
            if (childNodes[i].nodeType == 1) {
                nodes[nodes.length] = childNodes[i];
            }
        }
        
        return nodes;
    };

/**
 * Fires before the activeTab is changed.
 * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
 * <p>If handler returns false, the change will be cancelled, and the value will not
 * be set.</p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> beforeActiveTabChange<br>
 * <code>&lt;<a href="YAHOO.widget.Tab.html">YAHOO.widget.Tab</a>&gt;
 * prevValue</code> the currently active tab<br>
 * <code>&lt;<a href="YAHOO.widget.Tab.html">YAHOO.widget.Tab</a>&gt;
 * newValue</code> the tab to be made active</p>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var previous = e.prevValue};<br>
 * myTabs.addListener('beforeActiveTabChange', handler);</code></p>
 * @event beforeActiveTabChange
 */
    
/**
 * Fires after the activeTab is changed.
 * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> activeTabChange<br>
 * <code>&lt;<a href="YAHOO.widget.Tab.html">YAHOO.widget.Tab</a>&gt;
 * prevValue</code> the formerly active tab<br>
 * <code>&lt;<a href="YAHOO.widget.Tab.html">YAHOO.widget.Tab</a>&gt;
 * newValue</code> the new active tab</p>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var previous = e.prevValue};<br>
 * myTabs.addListener('activeTabChange', handler);</code></p>
 * @event activeTabChange
 */
 
/**
 * Fires before the orientation is changed.
 * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
 * <p>If handler returns false, the change will be cancelled, and the value will not
 * be set.</p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> beforeOrientationChange<br>
 * <code>&lt;String&gt;
 * prevValue</code> the current orientation<br>
 * <code>&lt;String&gt;
 * newValue</code> the new orientation to be applied</p>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var previous = e.prevValue};<br>
 * myTabs.addListener('beforeOrientationChange', handler);</code></p>
 * @event beforeOrientationChange
 */
    
/**
 * Fires after the orientation is changed.
 * <p>See: <a href="YAHOO.util.Element.html#addListener">Element.addListener</a></p>
 * <p><strong>Event fields:</strong><br>
 * <code>&lt;String&gt; type</code> orientationChange<br>
 * <code>&lt;String&gt;
 * prevValue</code> the former orientation<br>
 * <code>&lt;String&gt;
 * newValue</code> the new orientation</p>
 * <p><strong>Usage:</strong><br>
 * <code>var handler = function(e) {var previous = e.prevValue};<br>
 * myTabs.addListener('orientationChange', handler);</code></p>
 * @event orientationChange
 */
})();

