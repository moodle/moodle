YUI.add('gallery-bootstrap-engine', function(Y) {

/**
 * Bootstrap Engine for Plug and Play widgets. This class is meant to be used in
 * conjuntion with the Injection Engine (gallery-bootstrap-engine). It facilitates the use of
 * an iframe as a sandbox to execute certain tasks and/or a presention element.
 *
 * @module gallery-bootstrap-engine
 * @requires node, base-base
 * @class Y.BootstrapEngine
 * @param config {Object} Configuration object
 * @extends Y.Base
 * @constructor
 */

///////////////////////////////////////////////////////////////////////////
//
// Private shorthands, constants and variables
//
///////////////////////////////////////////////////////////////////////////

var ATTR_HOST = 'host';

///////////////////////////////////////////////////////////////////////////
//
// Class definition
//
///////////////////////////////////////////////////////////////////////////

function BootstrapEngine () {
    BootstrapEngine.superclass.constructor.apply(this, arguments);
}

Y.mix(BootstrapEngine, {

    /**
     * The identity of the class.
     * @property BootstrapEngine.NAME
     * @type string
     * @static
     * @final
     * @readOnly
     * @default 'bootstrap'
     */
    NAME: 'bootstrap',

    /**
     * Static property used to define the default attribute configuration of
     * the class.
     * @property BootstrapEngine.ATTRS
     * @type Object
     * @protected
     * @static
     */
    ATTRS: {
        /**
         * @attribute container
         * @type {Selector|Node}
         * @writeOnce
         * @description selector or node for the iframe's container. This is relative to the parent document.
         */
        container: {
             getter: function (v) {
                 var host = this.get(ATTR_HOST);
                 return host && host.one( v );
             }
        },
        /**
         * @attribute iframe
         * @type {Node}
         * @readyOnly
         * @description Node reference to the iframe on the parent document.
         */
        iframe: {
            getter: function () {
                var c = this.get('container');
                return c && c.one('iframe' );
            }
        },
        /**
         * @attribute host
         * @type {Object}
         * @readyOnly
         * @description A "Y" reference bound to the parent document.
         */
        host: {
            readyOnly: true
        },
        /**
         * @attribute ready
         * @type {Boolean}
         * @readyOnly
         * @description A "Y" reference bound to the parent document.
         */
        ready: {
            value: false,
            readyOnly: true
        }
    }

});

Y.extend(BootstrapEngine, Y.Base, {
    /**
     * Any extra YUI module that you want to use by default in HOST YUI instance.
     * "node" module will be added automatically since it's required by bootstrap.
     * @property EXTRAS
     * @type Array
     * @default []
     */
    EXTRAS: [],

    /**
     * Construction logic executed during Bootstrap Engine instantiation.
     *
     * @method initializer
     * @param cfg {Object} Initial configuration
     * @protected
     */
    initializer: function () {
        var instance = this,
            parent, win, doc,
            use = Y.Array(instance.EXTRAS),
            host,
            callBootFn = function () {
                // finishing the initialization process async to facilitate
                // addons to hook into _boot/_init/_bind/_ready if needed.
                // todo: after migrating to 3.4 this is not longer needed, and we can use initializer and destroyer
                // in each extension
                Y.later(0, instance, function() {
                    instance._boot();
                });
            };

        try {
            parent = Y.config.win.parent;
            win = parent && parent.window;
            doc = win && win.document;
        } catch(e) {
            Y.log ('Parent window is not available or is a different domain', 'warn', 'bootstrap');
        }

        Y.log ('Initialization', 'info', 'bootstrap');
        // parent is optional to facilitate testing and headless execution
        if (parent && win && doc) {
            host = YUI({
                bootstrap: false,
                win: win,
                doc: doc
            });
            use.push('node', function() {
                callBootFn();
            });

            // Creating a new YUI instance bound to the parent window
            instance._set(ATTR_HOST, host.use.apply(host, use));
        } else {
            callBootFn();
        }
    },

    /**
     * Basic initialization routine, styling the iframe, binding events and
     * connecting the bootstrap engine with the injection engine.
     *
     * @method _boot
     * @protected
     */
    _boot: function () {
        var instance = this,
            auto;
        Y.log ('Boot', 'info', 'bootstrap');
        // connecting with the injection engine before doing anything else
        auto = instance._connect();
        // adjust the iframe container in preparation for the first display action
        instance._styleIframe();
        // create some objects and markup
        instance._init();
        // binding some extra events
        instance._bind();
        // if the connect process wants to automatically execute the _ready, it should returns true.
        if (auto) {
            // connecting the bootstrap with the injection engine
            instance._ready();
        }
        // marking the system as ready
        instance._set('ready', true);
    },

    /**
     * Connects the bootstrap with the injection engine running in the parent window. This method
     * defines the hand-shake process between them. This method is meant to be called by
     * the bootstrap engine _init method to start the connection.
     *
     * @method _connect
     * @protected
     */
    _connect: function () {
        var guid = Y.config.guid, // injection engine guid value
            host = this.get(ATTR_HOST),
            pwin = host && host.config.win,
            // getting a reference to the parent window callback function to notify
            // to the injection engine that the bootstrap is ready
            callback = guid && pwin && pwin.YUI && pwin.YUI.Env[guid];

        Y.log ('Bootstrap connect', 'info', 'bootstrap');
        // connecting bootstrap with the injection engines
        return ( callback ? callback ( this ) : false );
    },

    /**
     * Basic initialization routine, usually to create markup, new objects and attributes, etc.
     * Overrides/Extends this prototype method to do your mojo.
     *
     * @method _init
     * @protected
     */
    _init: function () {
        Y.log ('Init bootstrap', 'info', 'bootstrap');
    },

    /**
     * Defines the binding logic for the bootstrap engine, listening for some attributes
     * that might change, and defining the set of events that can be exposed to the injection engine.
     * Overrides/Extends this prototype method to do your mojo.
     *
     * @method _bind
     * @protected
     */
    _bind: function () {
        Y.log ('Binding bootstrap', 'info', 'bootstrap');
    },

    /**
     * This method will be called only if the connect response with "true", you can use this
     * to control the state of the initialization from the injection engine since it might
     * take some time to load the stuff in the iframe, and the user might interact with the page
     * invalidating the initialization routine.
     * Overrides/Extends this prototype method to do your mojo.
     *
     * @method _ready
     * @protected
     */
    _ready : function () {
         Y.log ('Bootstrap is ready', 'info', 'bootstrap');
     },

     /**
      * The iframe that holds the bootstrap engine sometimes is used as a UI overlay.
      * In this case, you can style it through this method. By default, it will set
      * border, frameBorder, marginWidth, marginHeight, leftMargin and topMargin to
      * cero, and allowTransparency to true.
      *
      * @method _styleIframe
      * @protected
      */
     _styleIframe: function () {
         var iframe = this.get('iframe');
         // making the iframe optional to facilitate tests
         if (iframe) {
             Y.log ('Styling the iframe', 'info', 'bootstrap');
             Y.each (['border', 'marginWidth', 'marginHeight', 'leftMargin', 'topMargin'], function (name) {
                 iframe.setAttribute(name, 0);
             });
         }
     }

});

Y.BootstrapEngine = BootstrapEngine;


}, '@VERSION@' ,{requires:['node','base-base']});

YUI.add('gallery-bootstrap-collapse', function(Y) {

/**
A Plugin which provides collapsing/expanding behaviors on a Node with
compatible syntax and markup from Twitter's Bootstrap project.

@module gallery-bootstrap-collapse
**/

/**
A Plugin which provides collapsing and expanding behaviors on a Node with
compatible syntax and markup from Twitter's Bootstrap project.

It possible to have dynamic behaviors without incorporating any
JavaScript by setting <code>data-toggle=collapse</code> on any element.

However, it can be manually plugged into any node or node list.

@example

    var node = Y.one('.someNode');
    node.plug( Y.Bootstrap.Collapse, config );

    node.collapse.show();

@class Bootstrap.Collapse
**/

function CollapsePlugin() {
    CollapsePlugin.superclass.constructor.apply(this, arguments);
}

CollapsePlugin.NAME = 'Bootstrap.Collapse';
CollapsePlugin.NS   = 'collapse';

Y.extend(CollapsePlugin, Y.Plugin.Base, {
    defaults : {
        duration  : 0.25,
        easing    : 'ease-in',
        showClass : 'in',
        hideClass : 'out',

        groupSelector : '> .accordion-group > .in'
    },

    transitioning: false,

    initializer : function(config) {
        this._node = config.host;

        this.config = Y.mix( config, this.defaults );

        this.publish('show', { preventable : true, defaultFn : this.show });
        this.publish('hide', { preventable : true, defaultFn : this.hide });

        this._node.on('click', this.toggle, this);
    },

    _getTarget: function() {
        var node = this._node,
            container;

        if ( node.getData('target') ) {
            container = Y.one( node.getData('target') );
        }
        else if ( node.getAttribute('href').indexOf('#') >= 0 ) {
            Y.log('No target, looking at href: ' + node.getAttribute('href'), 'debug', 'Bootstrap.Collapse');
            container = Y.one( node.getAttribute('href').substr( node.getAttribute('href').indexOf('#') ) );
        }
        return container;
    },

    /**
    * @method hide
    * @description Hide the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    hide: function() {
        var node      = this._getTarget();

        if ( this.transitioning ) {
            return;
        }

        if ( node ) {
            this._hideElement(node);
        }
    },

    /**
    * @method show
    * @description Show the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    show: function() {
        var node      = this._getTarget(),
            host      = this._node,
            self      = this,
            parent,
            group_selector = this.config.groupSelector;

        if ( this.transitioning ) {
            return;
        }

        if ( host.getData('parent') ) {
            parent = Y.one( host.getData('parent') );
            if ( parent ) {
                parent.all(group_selector).each( function(el) {
                    Y.log('Hiding element: ' + el, 'debug', 'Bootstrap.Collapse');
                    self._hideElement(el);
                });
            }
        }
        this._showElement(node);
    },

    /**
    @method toggle
    @description Toggle the state of the collapsible target, specified
    by the host's <code>data-target</code> or <code>href</code>
    attribute. Calls the <code>show</code> or <code>hide</code> method.
    **/
    toggle : function(e) {
        if ( e && Y.Lang.isFunction(e.preventDefault) ) {
            e.preventDefault();
        }

        var target = this._getTarget();

        if ( target.hasClass( this.config.showClass ) ) {
            this.fire('hide');
        } else {
            this.fire('show');
        }
    },

    /**
    @method _transition
    @description Handles the transition between showing and hiding.
    @protected
    @param node {Node} node to apply transitions to
    @param method {String} 'hide' or 'show'
    **/
    _transition : function(node, method) {
        var self        = this,
            config      = this.config,
            duration    = config.duration,
            easing      = config.easing,
            // If we are hiding, then remove the show class.
            removeClass = method === 'hide' ? config.showClass : config.hideClass,
            // And if we are hiding, add the hide class.
            addClass    = method === 'hide' ? config.hideClass : config.showClass,

            to_height   = method === 'hide' ? 0 : null,
            event       = method === 'hide' ? 'hidden' : 'shown',

            complete = function() {
                node.removeClass(removeClass);
                node.addClass(addClass);
                self.transitioning = false;
                this.fire( event );
            };

        if ( to_height === null ) {
            to_height = 0;
            node.all('> *').each(function(el) {
                to_height += el.get('scrollHeight');
            });
        }

        this.transitioning = true;

        node.transition({
            height   : to_height +'px',
            duration : duration,
            easing   : easing
        }, complete);
    },

    /**
    @method _hideElement
    @description Calls the <code>_transition</code> method to hide a node.
    @protected
    @param node {Node} node to hide.
    **/
    _hideElement : function(node) {
        this._transition(node, 'hide');
/*
        var showClass = this.showClass,
            hideClass = this.hideClass;

        node.removeClass(showClass);
        node.addClass(hideClass);
*/
    },

    /**
    @method _showElement
    @description Calls the <code>_transition</code> method to show a node.
    @protected
    @param node {Node} node to show.
    **/
    _showElement : function(node) {
        this._transition(node, 'show');
/*
        var showClass = this.showClass,
            hideClass = this.hideClass;
        node.removeClass(hideClass);
        node.addClass(showClass);
*/
    }
});

Y.namespace('Bootstrap').Collapse = CollapsePlugin;



}, '@VERSION@' ,{requires:['plugin','transition','event','event-delegate']});

YUI.add('gallery-bootstrap-dropdown', function(Y) {

/**
A Plugin which provides dropdown behaviors for dropdown buttons and menu
groups. This utilizes the markup from the Twitter Bootstrap Project.

@module gallery-bootstrap-dropdown
**/

/**
A Plugin which provides dropdown behaviors for dropdown buttons and menu
groups. This utilizes the markup from the Twitter Bootstrap Project.

To automatically gain this functionality, you can simply add the
<code>data-toggle=dropdown</code> attribute to any element.

It can also be plugged into any node or node list.

@example

  var node = Y.one('.someNode');
  node.plug( Y.Bootstrap.Dropdown );
  node.dropdown.show();

@class Bootstrap.Dropdown
**/

var NS = Y.namespace('Bootstrap');

function DropdownPlugin() {
  DropdownPlugin.superclass.constructor.apply(this, arguments);
}

DropdownPlugin.NAME = 'Bootstrap.Dropdown';
DropdownPlugin.NS   = 'dropdown';

Y.extend( DropdownPlugin, Y.Plugin.Base, {
    defaults : {
        className : 'open',
        target    : 'target',
        selector  : ''
    },
    initializer : function(config) {
        this._node = config.host;

        this.config = Y.mix( config, this.defaults );

        this.publish('show', { preventable : true, defaultFn : this.show });
        this.publish('hide', { preventable : true, defaultFn : this.hide });

        this._node.on('click', this.toggle, this);
    },

    toggle : function() {
        var target    = this.getTarget(),
            className = this.config.className;

        target.toggleClass( className );
        target.once('clickoutside', function() {
            target.toggleClass( className );
        });
    },

    show : function() {
        this.getTarget().addClass( this.config.className );
    },
    hide : function() {
        this.getTarget().removeClass( this.config.className );
    },
    open : function() {
        this.getTarget().addClass( this.config.className );
    },
    close : function() {
        this.getTarget().removeClass( this.config.className );
    },

    /**
    @method getTarget
    @description Fetches a Y.NodeList or Y.Node that should be used to modify class names
    **/
    getTarget : function() {
        var node     = this._node,
            selector = node.getData( this.config.target ),
            target;

        if ( !selector ) {
            selector = node.getAttribute('href');
            selector = target && target.replace(/.*(?=#[^\s]*$)/, ''); //strip for ie7
        }

        target = Y.all(selector);
        if ( target.size() === 0 ) {
            target = node.get('parentNode');
        }

        return target;
    }
});

NS.Dropdown = DropdownPlugin;
NS.dropdown_delegation = function() {
    Y.delegate('click', function(e) {
        var target = e.currentTarget;
        e.preventDefault();

        if ( typeof e.target.dropdown === 'undefined' ) {
            target.plug( DropdownPlugin );
            target.dropdown.toggle();
        }
    }, document.body, '*[data-toggle=dropdown]' );
};


}, '@VERSION@' ,{requires:['plugin','event','event-outside']});
YUI.add('moodle-theme_bootstrapbase-bootstrap', function (Y, NAME) {

/**
The Moodle Bootstrap theme's bootstrap JavaScript

@namespace Moodle
@module theme_bootstrapbase-bootstrap
**/

/**
The Moodle Bootstrap theme's bootstrap JavaScript

@class Moodle.theme_bootstrapbase.bootstrap
@uses node
@uses selector-css3
@constructor
**/
var CSS = {
        ACTIVE: 'active'
    },
    SELECTORS = {
        NAVBAR_BUTTON: '.btn-navbar',
        // FIXME This is deliberately wrong because of a breaking issue in the upstream library.
        TOGGLECOLLAPSE: '*[data-disabledtoggle="collapse"]',
        NAV_COLLAPSE: '.nav-collapse'
    },
    NS = Y.namespace('Moodle.theme_bootstrapbase.bootstrap');

/**
 * Initialise the Moodle Bootstrap theme JavaScript
 *
 * @method init
 */
NS.init = function() {
    // We must use these here and *must not* add them to the list of dependencies until
    // Moodle fully supports the gallery.
    // When debugging is disabled and we seed the Loader with out configuration, if these
    // are in the requires array, then the Loader will try to load them from the CDN. It
    // does not know that we have added them to the module rollup.
    Y.use('gallery-bootstrap-dropdown',
            'gallery-bootstrap-collapse',
            'gallery-bootstrap-engine', function() {

        // Set up expandable and show.
        NS.setup_toggle_expandable();
        NS.setup_toggle_show();

        // Set up upstream dropdown delegation.
        Y.Bootstrap.dropdown_delegation();
    });
};

/**
 * Setup toggling of the Toggle Collapse
 *
 * @method setup_toggle_expandable
 * @private
 */
NS.setup_toggle_expandable = function() {
    Y.delegate('click', this.toggle_expandable, Y.config.doc, SELECTORS.TOGGLECOLLAPSE, this);
};

/**
 * Use the Y.Bootstrap.Collapse plugin to toggle collapse.
 *
 * @method toggle_expandable
 * @private
 * @param {EventFacade} e
 */
NS.toggle_expandable = function(e) {
    if (typeof e.currentTarget.collapse === 'undefined') {
        // Only plug if we haven't already.
        e.currentTarget.plug(Y.Bootstrap.Collapse);

        // The plugin will now catch the click and handle the toggle.
        // We only need to do this when we plug the node for the first
        // time.
        e.currentTarget.collapse.toggle();
        e.preventDefault();
    }
};

/**
 * Set up the show toggler for activating the navigation bar
 *
 * @method setup_toggle_show
 * @private
 */
NS.setup_toggle_show = function() {
    Y.delegate('click', this.toggle_show, Y.config.doc, SELECTORS.NAVBAR_BUTTON);
};

/**
 * Toggle hiding of the navigation bar
 *
 * @method toggle_show
 * @private
 * @param {EventFacade} e
 */
NS.toggle_show = function(e) {
    // Toggle the active class on both the clicked .btn-navbar and the .nav-collapse.
    // Our CSS will set the height for these.
    Y.one(SELECTORS.NAV_COLLAPSE).toggleClass(CSS.ACTIVE);
    e.currentTarget.toggleClass(CSS.ACTIVE);
};


}, '@VERSION@', {"requires": ["node", "selector-css3"]});
