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
;