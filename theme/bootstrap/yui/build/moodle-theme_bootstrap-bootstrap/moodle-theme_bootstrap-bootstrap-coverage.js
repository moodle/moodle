if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js",
    code: []
};
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"].code=["YUI.add('gallery-bootstrap-engine', function(Y) {","","/**"," * Bootstrap Engine for Plug and Play widgets. This class is meant to be used in"," * conjuntion with the Injection Engine (gallery-bootstrap-engine). It facilitates the use of"," * an iframe as a sandbox to execute certain tasks and/or a presention element."," *"," * @module gallery-bootstrap-engine"," * @requires node, base-base"," * @class Y.BootstrapEngine"," * @param config {Object} Configuration object"," * @extends Y.Base"," * @constructor"," */","","///////////////////////////////////////////////////////////////////////////","//","// Private shorthands, constants and variables","//","///////////////////////////////////////////////////////////////////////////","","var ATTR_HOST = 'host';","","///////////////////////////////////////////////////////////////////////////","//","// Class definition","//","///////////////////////////////////////////////////////////////////////////","","function BootstrapEngine () {","    BootstrapEngine.superclass.constructor.apply(this, arguments);","}","","Y.mix(BootstrapEngine, {","","    /**","     * The identity of the class.","     * @property BootstrapEngine.NAME","     * @type string","     * @static","     * @final","     * @readOnly","     * @default 'bootstrap'","     */","    NAME: 'bootstrap',","","    /**","     * Static property used to define the default attribute configuration of","     * the class.","     * @property BootstrapEngine.ATTRS","     * @type Object","     * @protected","     * @static","     */","    ATTRS: {","        /**","         * @attribute container","         * @type {Selector|Node}","         * @writeOnce","         * @description selector or node for the iframe's container. This is relative to the parent document.","         */","        container: {","             getter: function (v) {","                 var host = this.get(ATTR_HOST);","                 return host && host.one( v );","             }","        },","        /**","         * @attribute iframe","         * @type {Node}","         * @readyOnly","         * @description Node reference to the iframe on the parent document.","         */","        iframe: {","            getter: function () {","                var c = this.get('container');","                return c && c.one('iframe' );","            }","        },","        /**","         * @attribute host","         * @type {Object}","         * @readyOnly","         * @description A \"Y\" reference bound to the parent document.","         */","        host: {","            readyOnly: true","        },","        /**","         * @attribute ready","         * @type {Boolean}","         * @readyOnly","         * @description A \"Y\" reference bound to the parent document.","         */","        ready: {","            value: false,","            readyOnly: true","        }","    }","","});","","Y.extend(BootstrapEngine, Y.Base, {","    /**","     * Any extra YUI module that you want to use by default in HOST YUI instance.","     * \"node\" module will be added automatically since it's required by bootstrap.","     * @property EXTRAS","     * @type Array","     * @default []","     */","    EXTRAS: [],","","    /**","     * Construction logic executed during Bootstrap Engine instantiation.","     *","     * @method initializer","     * @param cfg {Object} Initial configuration","     * @protected","     */","    initializer: function () {","        var instance = this,","            parent, win, doc,","            use = Y.Array(instance.EXTRAS),","            host,","            callBootFn = function () {","                // finishing the initialization process async to facilitate","                // addons to hook into _boot/_init/_bind/_ready if needed.","                // todo: after migrating to 3.4 this is not longer needed, and we can use initializer and destroyer","                // in each extension","                Y.later(0, instance, function() {","                    instance._boot();","                });","            };","","        try {","            parent = Y.config.win.parent;","            win = parent && parent.window;","            doc = win && win.document;","        } catch(e) {","        }","","        // parent is optional to facilitate testing and headless execution","        if (parent && win && doc) {","            host = YUI({","                bootstrap: false,","                win: win,","                doc: doc","            });","            use.push('node', function() {","                callBootFn();","            });","","            // Creating a new YUI instance bound to the parent window","            instance._set(ATTR_HOST, host.use.apply(host, use));","        } else {","            callBootFn();","        }","    },","","    /**","     * Basic initialization routine, styling the iframe, binding events and","     * connecting the bootstrap engine with the injection engine.","     *","     * @method _boot","     * @protected","     */","    _boot: function () {","        var instance = this,","            auto;","        // connecting with the injection engine before doing anything else","        auto = instance._connect();","        // adjust the iframe container in preparation for the first display action","        instance._styleIframe();","        // create some objects and markup","        instance._init();","        // binding some extra events","        instance._bind();","        // if the connect process wants to automatically execute the _ready, it should returns true.","        if (auto) {","            // connecting the bootstrap with the injection engine","            instance._ready();","        }","        // marking the system as ready","        instance._set('ready', true);","    },","","    /**","     * Connects the bootstrap with the injection engine running in the parent window. This method","     * defines the hand-shake process between them. This method is meant to be called by","     * the bootstrap engine _init method to start the connection.","     *","     * @method _connect","     * @protected","     */","    _connect: function () {","        var guid = Y.config.guid, // injection engine guid value","            host = this.get(ATTR_HOST),","            pwin = host && host.config.win,","            // getting a reference to the parent window callback function to notify","            // to the injection engine that the bootstrap is ready","            callback = guid && pwin && pwin.YUI && pwin.YUI.Env[guid];","","        // connecting bootstrap with the injection engines","        return ( callback ? callback ( this ) : false );","    },","","    /**","     * Basic initialization routine, usually to create markup, new objects and attributes, etc.","     * Overrides/Extends this prototype method to do your mojo.","     *","     * @method _init","     * @protected","     */","    _init: function () {","    },","","    /**","     * Defines the binding logic for the bootstrap engine, listening for some attributes","     * that might change, and defining the set of events that can be exposed to the injection engine.","     * Overrides/Extends this prototype method to do your mojo.","     *","     * @method _bind","     * @protected","     */","    _bind: function () {","    },","","    /**","     * This method will be called only if the connect response with \"true\", you can use this","     * to control the state of the initialization from the injection engine since it might","     * take some time to load the stuff in the iframe, and the user might interact with the page","     * invalidating the initialization routine.","     * Overrides/Extends this prototype method to do your mojo.","     *","     * @method _ready","     * @protected","     */","    _ready : function () {","     },","","     /**","      * The iframe that holds the bootstrap engine sometimes is used as a UI overlay.","      * In this case, you can style it through this method. By default, it will set","      * border, frameBorder, marginWidth, marginHeight, leftMargin and topMargin to","      * cero, and allowTransparency to true.","      *","      * @method _styleIframe","      * @protected","      */","     _styleIframe: function () {","         var iframe = this.get('iframe');","         // making the iframe optional to facilitate tests","         if (iframe) {","             Y.each (['border', 'marginWidth', 'marginHeight', 'leftMargin', 'topMargin'], function (name) {","                 iframe.setAttribute(name, 0);","             });","         }","     }","","});","","Y.BootstrapEngine = BootstrapEngine;","","","}, '@VERSION@' ,{requires:['node','base-base']});",";","YUI.add('gallery-bootstrap-collapse', function(Y) {","","/**","A Plugin which provides collapsing/expanding behaviors on a Node with","compatible syntax and markup from Twitter's Bootstrap project.","","@module gallery-bootstrap-collapse","**/","","/**","A Plugin which provides collapsing and expanding behaviors on a Node with","compatible syntax and markup from Twitter's Bootstrap project.","","It possible to have dynamic behaviors without incorporating any","JavaScript by setting <code>data-toggle=collapse</code> on any element.","","However, it can be manually plugged into any node or node list.","","@example","","    var node = Y.one('.someNode');","    node.plug( Y.Bootstrap.Collapse, config );","","    node.collapse.show();","","@class Bootstrap.Collapse","**/","","function CollapsePlugin(config) {","    CollapsePlugin.superclass.constructor.apply(this, arguments);","}","","CollapsePlugin.NAME = 'Bootstrap.Collapse';","CollapsePlugin.NS   = 'collapse';","","Y.extend(CollapsePlugin, Y.Plugin.Base, {","    defaults : {","        duration  : 0.25,","        easing    : 'ease-in',","        showClass : 'in',","        hideClass : 'out',","","        groupSelector : '> .accordion-group > .in'","    },","","    transitioning: false,","","    initializer : function(config) {","        this._node = config.host;","","        this.config = Y.mix( config, this.defaults );","","        this.publish('show', { preventable : true, defaultFn : this.show });","        this.publish('hide', { preventable : true, defaultFn : this.hide });","","        this._node.on('click', this.toggle, this);","    },","","    _getTarget: function() {","        var node = this._node,","            container;","","        if ( node.getData('target') ) {","            container = Y.one( node.getData('target') );","        }","        else if ( node.getAttribute('href').indexOf('#') >= 0 ) {","            container = Y.one( node.getAttribute('href').substr( node.getAttribute('href').indexOf('#') ) );","        }","        return container;","    },","","    /**","    * @method hide","    * @description Hide the collapsible target, specified by the host's","    * <code>data-target</code> or <code>href</code> attribute.","    */","    hide: function() {","        var showClass = this.config.showClass,","            hideClass = this.config.hideClass,","            node      = this._getTarget();","","        if ( this.transitioning ) {","            return;","        }","","        if ( node ) {","            this._hideElement(node);","        }","    },","","    /**","    * @method show","    * @description Show the collapsible target, specified by the host's","    * <code>data-target</code> or <code>href</code> attribute.","    */","    show: function() {","        var showClass = this.config.showClass,","            hideClass = this.config.hideClass,","            node      = this._getTarget(),","            host      = this._node,","            self      = this,","            parent,","            group_selector = this.config.groupSelector;","","        if ( this.transitioning ) {","            return;","        }","","        if ( host.getData('parent') ) {","            parent = Y.one( host.getData('parent') );","            if ( parent ) {","                parent.all(group_selector).each( function(el) {","                    self._hideElement(el);","                });","            }","        }","        this._showElement(node);","    },","","    /**","    @method toggle","    @description Toggle the state of the collapsible target, specified","    by the host's <code>data-target</code> or <code>href</code>","    attribute. Calls the <code>show</code> or <code>hide</code> method.","    **/","    toggle : function(e) {","        if ( e && Y.Lang.isFunction(e.preventDefault) ) {","            e.preventDefault();","        }","","        var target = this._getTarget();","","        if ( target.hasClass( this.config.showClass ) ) {","            this.fire('hide');","        } else {","            this.fire('show');","        }","    },","","    /**","    @method _transition","    @description Handles the transition between showing and hiding.","    @protected","    @param node {Node} node to apply transitions to","    @param method {String} 'hide' or 'show'","    **/","    _transition : function(node, method) {","        var self        = this,","            config      = this.config,","            duration    = config.duration,","            easing      = config.easing,","            // If we are hiding, then remove the show class.","            removeClass = method === 'hide' ? config.showClass : config.hideClass,","            // And if we are hiding, add the hide class.","            addClass    = method === 'hide' ? config.hideClass : config.showClass,","","            to_height   = method === 'hide' ? 0 : null,","            event       = method === 'hide' ? 'hidden' : 'shown',","","            complete = function() {","                node.removeClass(removeClass);","                node.addClass(addClass);","                self.transitioning = false;","                this.fire( event );","            };","","        if ( to_height === null ) {","            to_height = 0;","            node.all('> *').each(function(el) {","                to_height += el.get('scrollHeight');","            });","        }","","        this.transitioning = true;","","        node.transition({","            height   : to_height +'px',","            duration : duration,","            easing   : easing","        }, complete);","    },","","    /**","    @method _hideElement","    @description Calls the <code>_transition</code> method to hide a node.","    @protected","    @param node {Node} node to hide.","    **/","    _hideElement : function(node) {","        this._transition(node, 'hide');","/*","        var showClass = this.showClass,","            hideClass = this.hideClass;","","        node.removeClass(showClass);","        node.addClass(hideClass);","*/","    },","","    /**","    @method _showElement","    @description Calls the <code>_transition</code> method to show a node.","    @protected","    @param node {Node} node to show.","    **/","    _showElement : function(node) {","        this._transition(node, 'show');","/*","        var showClass = this.showClass,","            hideClass = this.hideClass;","        node.removeClass(hideClass);","        node.addClass(showClass);","*/","    }","});","","Y.namespace('Bootstrap').Collapse = CollapsePlugin;","","","","}, '@VERSION@' ,{requires:['plugin','transition','event','event-delegate']});",";","YUI.add('gallery-bootstrap-dropdown', function(Y) {","","/**","A Plugin which provides dropdown behaviors for dropdown buttons and menu","groups. This utilizes the markup from the Twitter Bootstrap Project.","","@module gallery-bootstrap-dropdown","**/","","/**","A Plugin which provides dropdown behaviors for dropdown buttons and menu","groups. This utilizes the markup from the Twitter Bootstrap Project.","","To automatically gain this functionality, you can simply add the","<code>data-toggle=dropdown</code> attribute to any element.","","It can also be plugged into any node or node list.","","@example","","  var node = Y.one('.someNode');","  node.plug( Y.Bootstrap.Dropdown );","  node.dropdown.show();","","@class Bootstrap.Dropdown","**/","","var NS = Y.namespace('Bootstrap');","","function DropdownPlugin(config) {","  DropdownPlugin.superclass.constructor.apply(this, arguments);","}","","DropdownPlugin.NAME = 'Bootstrap.Dropdown';","DropdownPlugin.NS   = 'dropdown';","","Y.extend( DropdownPlugin, Y.Plugin.Base, {","    defaults : {","        className : 'open',","        target    : 'target',","        selector  : ''","    },","    initializer : function(config) {","        this._node = config.host;","","        this.config = Y.mix( config, this.defaults );","","        this.publish('show', { preventable : true, defaultFn : this.show });","        this.publish('hide', { preventable : true, defaultFn : this.hide });","","        this._node.on('click', this.toggle, this);","    },","","    toggle : function() {","        var target    = this.getTarget(),","            className = this.config.className;","","        target.toggleClass( className );","        target.once('clickoutside', function(e) {","            target.toggleClass( className );","        });","    },","","    show : function() {","        this.getTarget().addClass( this.config.className );","    },","    hide : function() {","        this.getTarget().removeClass( this.config.className );","    },","    open : function() {","        this.getTarget().addClass( this.config.className );","    },","    close : function() {","        this.getTarget().removeClass( this.config.className );","    },","","    /**","    @method getTarget","    @description Fetches a Y.NodeList or Y.Node that should be used to modify class names","    **/ ","    getTarget : function() {","        var node     = this._node,","            selector = node.getData( this.config.target ),","            target;","","        if ( !selector ) {","            selector = node.getAttribute('href');","            selector = target && target.replace(/.*(?=#[^\\s]*$)/, ''); //strip for ie7","        }","","        target = Y.all(selector);","        if ( target.size() === 0 ) {","            target = node.get('parentNode');","        }","","        return target;","    }","});","","NS.Dropdown = DropdownPlugin;","NS.dropdown_delegation = function() {","    Y.delegate('click', function(e) {","        var target = e.currentTarget;","        e.preventDefault();","","        if ( typeof e.target.dropdown === 'undefined' ) {","            target.plug( DropdownPlugin );","            target.dropdown.toggle();","        }","    }, document.body, '*[data-toggle=dropdown]' );","};","","","}, '@VERSION@' ,{requires:['plugin','event','event-outside']});",";YUI.add('moodle-theme_bootstrap-bootstrap', function (Y, NAME) {","","/**","The Moodle Bootstrap theme's bootstrap JavaScript","","@namespace Moodle","@module theme_bootstrap-bootstrap","**/","","/**","The Moodle Bootstrap theme's bootstrap JavaScript","","@class Moodle.theme_bootstrap.bootstrap","@uses node","@uses selector-css3","@constructor","**/","var CSS = {","        ACTIVE: 'active'","    },","    SELECTORS = {","        NAVBAR_BUTTON: '.btn-navbar',","        // FIXME This is deliberately wrong because of a breaking issue in the upstream library.","        TOGGLECOLLAPSE: '*[data-disabledtoggle=\"collapse\"]',","        NAV_COLLAPSE: '.nav-collapse'","    },","    NS = Y.namespace('Moodle.theme_bootstrap.bootstrap');","","/**"," * Initialise the Moodle Bootstrap theme JavaScript"," *"," * @method init"," */","NS.init = function() {","    // We must use these here and *must not* add them to the list of dependencies until","    // Moodle fully supports the gallery.","    // When debugging is disabled and we seed the Loader with out configuration, if these","    // are in the requires array, then the Loader will try to load them from the CDN. It","    // does not know that we have added them to the module rollup.","    Y.use('gallery-bootstrap-dropdown',","            'gallery-bootstrap-collapse',","            'gallery-bootstrap-engine', function() {","","        // Set up expandable and show.","        NS.setup_toggle_expandable();","        NS.setup_toggle_show();","","        // Set up upstream dropdown delegation.","        Y.Bootstrap.dropdown_delegation();","    });","};","","/**"," * Setup toggling of the Toggle Collapse"," *"," * @method setup_toggle_expandable"," * @private"," */","NS.setup_toggle_expandable = function() {","    Y.delegate('click', this.toggle_expandable, Y.config.doc, SELECTORS.TOGGLECOLLAPSE, this);","};","","/**"," * Use the Y.Bootstrap.Collapse plugin to toggle collapse."," *"," * @method toggle_expandable"," * @private"," * @param {EventFacade} e"," */","NS.toggle_expandable = function(e) {","    if (typeof e.currentTarget.collapse === 'undefined') {","        // Only plug if we haven't already.","        e.currentTarget.plug(Y.Bootstrap.Collapse);","","        // The plugin will now catch the click and handle the toggle.","        // We only need to do this when we plug the node for the first","        // time.","        e.currentTarget.collapse.toggle();","        e.preventDefault();","    }","};","","/**"," * Set up the show toggler for activating the navigation bar"," *"," * @method setup_toggle_show"," * @private"," */","NS.setup_toggle_show = function() {","    Y.delegate('click', this.toggle_show, Y.config.doc, SELECTORS.NAVBAR_BUTTON);","};","","/**"," * Toggle hiding of the navigation bar"," *"," * @method toggle_show"," * @private"," * @param {EventFacade} e"," */","NS.toggle_show = function(e) {","    // Toggle the active class on both the clicked .btn-navbar and the .nav-collapse.","    // Our CSS will set the height for these.","    Y.one(SELECTORS.NAV_COLLAPSE).toggleClass(CSS.ACTIVE);","    e.currentTarget.toggleClass(CSS.ACTIVE);","};","","","}, '@VERSION@', {\"requires\": [\"node\", \"selector-css3\"]});"];
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"].lines = {"1":0,"22":0,"30":0,"31":0,"34":0,"64":0,"65":0,"76":0,"77":0,"103":0,"121":0,"130":0,"131":0,"135":0,"136":0,"137":0,"138":0,"143":0,"144":0,"149":0,"150":0,"154":0,"156":0,"168":0,"171":0,"173":0,"175":0,"177":0,"179":0,"181":0,"184":0,"196":0,"204":0,"251":0,"253":0,"254":0,"255":0,"262":0,"266":0,"267":0,"295":0,"296":0,"299":0,"300":0,"302":0,"315":0,"317":0,"319":0,"320":0,"322":0,"326":0,"329":0,"330":0,"332":0,"333":0,"335":0,"344":0,"348":0,"349":0,"352":0,"353":0,"363":0,"371":0,"372":0,"375":0,"376":0,"377":0,"378":0,"379":0,"383":0,"393":0,"394":0,"397":0,"399":0,"400":0,"402":0,"414":0,"427":0,"428":0,"429":0,"430":0,"433":0,"434":0,"435":0,"436":0,"440":0,"442":0,"456":0,"473":0,"483":0,"488":0,"489":0,"516":0,"518":0,"519":0,"522":0,"523":0,"525":0,"532":0,"534":0,"536":0,"537":0,"539":0,"543":0,"546":0,"547":0,"548":0,"553":0,"556":0,"559":0,"562":0,"570":0,"574":0,"575":0,"576":0,"579":0,"580":0,"581":0,"584":0,"588":0,"589":0,"590":0,"591":0,"592":0,"594":0,"595":0,"596":0,"603":0,"620":0,"636":0,"642":0,"647":0,"648":0,"651":0,"661":0,"662":0,"672":0,"673":0,"675":0,"680":0,"681":0,"691":0,"692":0,"702":0,"705":0,"706":0};
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"].functions = {"BootstrapEngine:30":0,"getter:63":0,"getter:75":0,"(anonymous 2):130":0,"callBootFn:125":0,"(anonymous 3):149":0,"initializer:120":0,"_boot:167":0,"_connect:195":0,"(anonymous 4):254":0,"_styleIframe:250":0,"(anonymous 1):1":0,"CollapsePlugin:295":0,"initializer:314":0,"_getTarget:325":0,"hide:343":0,"(anonymous 6):378":0,"show:362":0,"toggle:392":0,"complete:426":0,"(anonymous 7):435":0,"_transition:413":0,"_hideElement:455":0,"_showElement:472":0,"(anonymous 5):267":0,"DropdownPlugin:518":0,"initializer:531":0,"(anonymous 9):547":0,"toggle:542":0,"show:552":0,"hide:555":0,"open:558":0,"close:561":0,"getTarget:569":0,"(anonymous 10):590":0,"dropdown_delegation:589":0,"(anonymous 8):489":0,"(anonymous 12):644":0,"init:636":0,"setup_toggle_expandable:661":0,"toggle_expandable:672":0,"setup_toggle_show:691":0,"toggle_show:702":0,"(anonymous 11):603":0};
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"].coveredLines = 146;
_yuitest_coverage["build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js"].coveredFunctions = 44;
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 1);
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

_yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 22);
var ATTR_HOST = 'host';

///////////////////////////////////////////////////////////////////////////
//
// Class definition
//
///////////////////////////////////////////////////////////////////////////

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 30);
function BootstrapEngine () {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "BootstrapEngine", 30);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 31);
BootstrapEngine.superclass.constructor.apply(this, arguments);
}

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 34);
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
                 _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "getter", 63);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 64);
var host = this.get(ATTR_HOST);
                 _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 65);
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
                _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "getter", 75);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 76);
var c = this.get('container');
                _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 77);
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

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 103);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "initializer", 120);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 121);
var instance = this,
            parent, win, doc,
            use = Y.Array(instance.EXTRAS),
            host,
            callBootFn = function () {
                // finishing the initialization process async to facilitate
                // addons to hook into _boot/_init/_bind/_ready if needed.
                // todo: after migrating to 3.4 this is not longer needed, and we can use initializer and destroyer
                // in each extension
                _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "callBootFn", 125);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 130);
Y.later(0, instance, function() {
                    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 2)", 130);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 131);
instance._boot();
                });
            };

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 135);
try {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 136);
parent = Y.config.win.parent;
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 137);
win = parent && parent.window;
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 138);
doc = win && win.document;
        } catch(e) {
        }

        // parent is optional to facilitate testing and headless execution
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 143);
if (parent && win && doc) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 144);
host = YUI({
                bootstrap: false,
                win: win,
                doc: doc
            });
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 149);
use.push('node', function() {
                _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 3)", 149);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 150);
callBootFn();
            });

            // Creating a new YUI instance bound to the parent window
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 154);
instance._set(ATTR_HOST, host.use.apply(host, use));
        } else {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 156);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_boot", 167);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 168);
var instance = this,
            auto;
        // connecting with the injection engine before doing anything else
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 171);
auto = instance._connect();
        // adjust the iframe container in preparation for the first display action
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 173);
instance._styleIframe();
        // create some objects and markup
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 175);
instance._init();
        // binding some extra events
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 177);
instance._bind();
        // if the connect process wants to automatically execute the _ready, it should returns true.
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 179);
if (auto) {
            // connecting the bootstrap with the injection engine
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 181);
instance._ready();
        }
        // marking the system as ready
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 184);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_connect", 195);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 196);
var guid = Y.config.guid, // injection engine guid value
            host = this.get(ATTR_HOST),
            pwin = host && host.config.win,
            // getting a reference to the parent window callback function to notify
            // to the injection engine that the bootstrap is ready
            callback = guid && pwin && pwin.YUI && pwin.YUI.Env[guid];

        // connecting bootstrap with the injection engines
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 204);
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
         _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_styleIframe", 250);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 251);
var iframe = this.get('iframe');
         // making the iframe optional to facilitate tests
         _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 253);
if (iframe) {
             _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 254);
Y.each (['border', 'marginWidth', 'marginHeight', 'leftMargin', 'topMargin'], function (name) {
                 _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 4)", 254);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 255);
iframe.setAttribute(name, 0);
             });
         }
     }

});

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 262);
Y.BootstrapEngine = BootstrapEngine;


}, '@VERSION@' ,{requires:['node','base-base']});
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 266);
;
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 267);
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

_yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 5)", 267);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 295);
function CollapsePlugin(config) {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "CollapsePlugin", 295);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 296);
CollapsePlugin.superclass.constructor.apply(this, arguments);
}

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 299);
CollapsePlugin.NAME = 'Bootstrap.Collapse';
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 300);
CollapsePlugin.NS   = 'collapse';

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 302);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "initializer", 314);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 315);
this._node = config.host;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 317);
this.config = Y.mix( config, this.defaults );

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 319);
this.publish('show', { preventable : true, defaultFn : this.show });
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 320);
this.publish('hide', { preventable : true, defaultFn : this.hide });

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 322);
this._node.on('click', this.toggle, this);
    },

    _getTarget: function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_getTarget", 325);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 326);
var node = this._node,
            container;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 329);
if ( node.getData('target') ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 330);
container = Y.one( node.getData('target') );
        }
        else {_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 332);
if ( node.getAttribute('href').indexOf('#') >= 0 ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 333);
container = Y.one( node.getAttribute('href').substr( node.getAttribute('href').indexOf('#') ) );
        }}
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 335);
return container;
    },

    /**
    * @method hide
    * @description Hide the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    hide: function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "hide", 343);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 344);
var showClass = this.config.showClass,
            hideClass = this.config.hideClass,
            node      = this._getTarget();

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 348);
if ( this.transitioning ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 349);
return;
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 352);
if ( node ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 353);
this._hideElement(node);
        }
    },

    /**
    * @method show
    * @description Show the collapsible target, specified by the host's
    * <code>data-target</code> or <code>href</code> attribute.
    */
    show: function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "show", 362);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 363);
var showClass = this.config.showClass,
            hideClass = this.config.hideClass,
            node      = this._getTarget(),
            host      = this._node,
            self      = this,
            parent,
            group_selector = this.config.groupSelector;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 371);
if ( this.transitioning ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 372);
return;
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 375);
if ( host.getData('parent') ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 376);
parent = Y.one( host.getData('parent') );
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 377);
if ( parent ) {
                _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 378);
parent.all(group_selector).each( function(el) {
                    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 6)", 378);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 379);
self._hideElement(el);
                });
            }
        }
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 383);
this._showElement(node);
    },

    /**
    @method toggle
    @description Toggle the state of the collapsible target, specified
    by the host's <code>data-target</code> or <code>href</code>
    attribute. Calls the <code>show</code> or <code>hide</code> method.
    **/
    toggle : function(e) {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "toggle", 392);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 393);
if ( e && Y.Lang.isFunction(e.preventDefault) ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 394);
e.preventDefault();
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 397);
var target = this._getTarget();

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 399);
if ( target.hasClass( this.config.showClass ) ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 400);
this.fire('hide');
        } else {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 402);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_transition", 413);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 414);
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
                _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "complete", 426);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 427);
node.removeClass(removeClass);
                _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 428);
node.addClass(addClass);
                _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 429);
self.transitioning = false;
                _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 430);
this.fire( event );
            };

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 433);
if ( to_height === null ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 434);
to_height = 0;
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 435);
node.all('> *').each(function(el) {
                _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 7)", 435);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 436);
to_height += el.get('scrollHeight');
            });
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 440);
this.transitioning = true;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 442);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_hideElement", 455);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 456);
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
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "_showElement", 472);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 473);
this._transition(node, 'show');
/*
        var showClass = this.showClass,
            hideClass = this.hideClass;
        node.removeClass(hideClass);
        node.addClass(showClass);
*/
    }
});

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 483);
Y.namespace('Bootstrap').Collapse = CollapsePlugin;



}, '@VERSION@' ,{requires:['plugin','transition','event','event-delegate']});
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 488);
;
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 489);
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

_yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 8)", 489);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 516);
var NS = Y.namespace('Bootstrap');

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 518);
function DropdownPlugin(config) {
  _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "DropdownPlugin", 518);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 519);
DropdownPlugin.superclass.constructor.apply(this, arguments);
}

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 522);
DropdownPlugin.NAME = 'Bootstrap.Dropdown';
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 523);
DropdownPlugin.NS   = 'dropdown';

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 525);
Y.extend( DropdownPlugin, Y.Plugin.Base, {
    defaults : {
        className : 'open',
        target    : 'target',
        selector  : ''
    },
    initializer : function(config) {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "initializer", 531);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 532);
this._node = config.host;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 534);
this.config = Y.mix( config, this.defaults );

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 536);
this.publish('show', { preventable : true, defaultFn : this.show });
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 537);
this.publish('hide', { preventable : true, defaultFn : this.hide });

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 539);
this._node.on('click', this.toggle, this);
    },

    toggle : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "toggle", 542);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 543);
var target    = this.getTarget(),
            className = this.config.className;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 546);
target.toggleClass( className );
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 547);
target.once('clickoutside', function(e) {
            _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 9)", 547);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 548);
target.toggleClass( className );
        });
    },

    show : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "show", 552);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 553);
this.getTarget().addClass( this.config.className );
    },
    hide : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "hide", 555);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 556);
this.getTarget().removeClass( this.config.className );
    },
    open : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "open", 558);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 559);
this.getTarget().addClass( this.config.className );
    },
    close : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "close", 561);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 562);
this.getTarget().removeClass( this.config.className );
    },

    /**
    @method getTarget
    @description Fetches a Y.NodeList or Y.Node that should be used to modify class names
    **/ 
    getTarget : function() {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "getTarget", 569);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 570);
var node     = this._node,
            selector = node.getData( this.config.target ),
            target;

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 574);
if ( !selector ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 575);
selector = node.getAttribute('href');
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 576);
selector = target && target.replace(/.*(?=#[^\s]*$)/, ''); //strip for ie7
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 579);
target = Y.all(selector);
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 580);
if ( target.size() === 0 ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 581);
target = node.get('parentNode');
        }

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 584);
return target;
    }
});

_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 588);
NS.Dropdown = DropdownPlugin;
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 589);
NS.dropdown_delegation = function() {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "dropdown_delegation", 589);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 590);
Y.delegate('click', function(e) {
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 10)", 590);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 591);
var target = e.currentTarget;
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 592);
e.preventDefault();

        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 594);
if ( typeof e.target.dropdown === 'undefined' ) {
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 595);
target.plug( DropdownPlugin );
            _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 596);
target.dropdown.toggle();
        }
    }, document.body, '*[data-toggle=dropdown]' );
};


}, '@VERSION@' ,{requires:['plugin','event','event-outside']});
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 603);
;YUI.add('moodle-theme_bootstrap-bootstrap', function (Y, NAME) {

/**
The Moodle Bootstrap theme's bootstrap JavaScript

@namespace Moodle
@module theme_bootstrap-bootstrap
**/

/**
The Moodle Bootstrap theme's bootstrap JavaScript

@class Moodle.theme_bootstrap.bootstrap
@uses node
@uses selector-css3
@constructor
**/
_yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 11)", 603);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 620);
var CSS = {
        ACTIVE: 'active'
    },
    SELECTORS = {
        NAVBAR_BUTTON: '.btn-navbar',
        // FIXME This is deliberately wrong because of a breaking issue in the upstream library.
        TOGGLECOLLAPSE: '*[data-disabledtoggle="collapse"]',
        NAV_COLLAPSE: '.nav-collapse'
    },
    NS = Y.namespace('Moodle.theme_bootstrap.bootstrap');

/**
 * Initialise the Moodle Bootstrap theme JavaScript
 *
 * @method init
 */
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 636);
NS.init = function() {
    // We must use these here and *must not* add them to the list of dependencies until
    // Moodle fully supports the gallery.
    // When debugging is disabled and we seed the Loader with out configuration, if these
    // are in the requires array, then the Loader will try to load them from the CDN. It
    // does not know that we have added them to the module rollup.
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "init", 636);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 642);
Y.use('gallery-bootstrap-dropdown',
            'gallery-bootstrap-collapse',
            'gallery-bootstrap-engine', function() {

        // Set up expandable and show.
        _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "(anonymous 12)", 644);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 647);
NS.setup_toggle_expandable();
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 648);
NS.setup_toggle_show();

        // Set up upstream dropdown delegation.
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 651);
Y.Bootstrap.dropdown_delegation();
    });
};

/**
 * Setup toggling of the Toggle Collapse
 *
 * @method setup_toggle_expandable
 * @private
 */
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 661);
NS.setup_toggle_expandable = function() {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "setup_toggle_expandable", 661);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 662);
Y.delegate('click', this.toggle_expandable, Y.config.doc, SELECTORS.TOGGLECOLLAPSE, this);
};

/**
 * Use the Y.Bootstrap.Collapse plugin to toggle collapse.
 *
 * @method toggle_expandable
 * @private
 * @param {EventFacade} e
 */
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 672);
NS.toggle_expandable = function(e) {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "toggle_expandable", 672);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 673);
if (typeof e.currentTarget.collapse === 'undefined') {
        // Only plug if we haven't already.
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 675);
e.currentTarget.plug(Y.Bootstrap.Collapse);

        // The plugin will now catch the click and handle the toggle.
        // We only need to do this when we plug the node for the first
        // time.
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 680);
e.currentTarget.collapse.toggle();
        _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 681);
e.preventDefault();
    }
};

/**
 * Set up the show toggler for activating the navigation bar
 *
 * @method setup_toggle_show
 * @private
 */
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 691);
NS.setup_toggle_show = function() {
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "setup_toggle_show", 691);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 692);
Y.delegate('click', this.toggle_show, Y.config.doc, SELECTORS.NAVBAR_BUTTON);
};

/**
 * Toggle hiding of the navigation bar
 *
 * @method toggle_show
 * @private
 * @param {EventFacade} e
 */
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 702);
NS.toggle_show = function(e) {
    // Toggle the active class on both the clicked .btn-navbar and the .nav-collapse.
    // Our CSS will set the height for these.
    _yuitest_coverfunc("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", "toggle_show", 702);
_yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 705);
Y.one(SELECTORS.NAV_COLLAPSE).toggleClass(CSS.ACTIVE);
    _yuitest_coverline("build/moodle-theme_bootstrap-bootstrap/moodle-theme_bootstrap-bootstrap.js", 706);
e.currentTarget.toggleClass(CSS.ACTIVE);
};


}, '@VERSION@', {"requires": ["node", "selector-css3"]});
