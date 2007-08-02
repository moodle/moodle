/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/
/**
 * Provides dynamic loading for the YUI library.  It includes the dependency
 * info for the library, and will automatically pull in dependencies for
 * the modules requested.  It supports rollup files (such as utilities.js
 * and yahoo-dom-event.js), and will automatically use these when
 * appropriate in order to minimize the number of http connections
 * required to load all of the dependencies.
 * 
 * @module yuiloader
 * @namespace YAHOO.util
 */

/**
 * YUILoader provides dynamic loading for YUI.
 * @class YAHOO.util.YUILoader
 * @todo
 *      version management, automatic sandboxing
 */
(function() {
 
    // Define YAHOO_config if it doesn't exist.  Only relevant if YAHOO is not
    // already on the page
    if (typeof YAHOO_config === "undefined") {
        YAHOO_config = {};
    }

    // YUI is locally scoped, only pieces of it will be referenced in YAHOO
    // after YAHOO has been loaded.
    var YUI = {

        /*
         * The library metadata for the current release  The is the default
         * value for YAHOO.util.YUILoader.moduleInfo
         * @property YUIInfo
         * @static
         */
        info: {

    'base': 'http://yui.yahooapis.com/2.3.0/build/',

    'skin': {
        'defaultSkin': 'sam',
        'base': 'assets/skins/',
        'path': 'skin.css',
        'rollup': 3
    },

    'moduleInfo': {

        'animation': {
            'type': 'js',
            'path': 'animation/animation-min.js',
            'requires': ['dom', 'event']
        },

        'autocomplete': {
            'type': 'js',
            'path': 'autocomplete/autocomplete-min.js',
            'requires': ['dom', 'event'],
            'optional': ['connection', 'animation'],
            'skinnable': true
        },

        'button': {
            'type': 'js',
            'path': 'button/button-beta-min.js',
            'requires': ['element'],
            'optional': ['menu'],
            'skinnable': true
        },

        'calendar': {
            'type': 'js',
            'path': 'calendar/calendar-min.js',
            'requires': ['event', 'dom'],
            'skinnable': true
        },

        'colorpicker': {
            'type': 'js',
            'path': 'colorpicker/colorpicker-beta-min.js',
            'requires': ['slider', 'element'],
            'optional': ['animation'],
            'skinnable': true
        },

        'connection': {
            'type': 'js',
            'path': 'connection/connection-min.js',
            'requires': ['event']
        },

        'container': {
            'type': 'js',
            'path': 'container/container-min.js',
            'requires': ['dom', 'event'],
            // button is optional, but creates a circular dep
            //'optional': ['dragdrop', 'animation', 'button'],
            'optional': ['dragdrop', 'animation'],
            'supersedes': ['containercore'],
            'skinnable': true
        },

        'containercore': {
            'type': 'js',
            'path': 'container/container_core-min.js',
            'requires': ['dom', 'event']
        },

        'datasource': {
            'type': 'js',
            'path': 'datasource/datasource-beta-min.js',
            'requires': ['event'],
            'optional': ['connection']
        },

        'datatable': {
            'type': 'js',
            'path': 'datatable/datatable-beta-min.js',
            'requires': ['element', 'datasource'],
            'optional': ['calendar', 'dragdrop'],
            'skinnable': true
        },

        'dom': {
            'type': 'js',
            'path': 'dom/dom-min.js',
            'requires': ['yahoo']
        },

        'dragdrop': {
            'type': 'js',
            'path': 'dragdrop/dragdrop-min.js',
            'requires': ['dom', 'event']
        },

        'editor': {
            'type': 'js',
            'path': 'editor/editor-beta-min.js',
            'requires': ['menu', 'container', 'element', 'button'],
            'optional': ['animation', 'dragdrop'],
            'skinnable': true
        },

        'element': {
            'type': 'js',
            'path': 'element/element-beta-min.js',
            'requires': ['dom', 'event']
        },

        'event': {
            'type': 'js',
            'path': 'event/event-min.js',
            'requires': ['yahoo']
        },

        'fonts': {
            'type': 'css',
            'path': 'fonts/fonts-min.css'
        },

        'grids': {
            'type': 'css',
            'path': 'grids/grids-min.css',
            'requires': ['fonts'],
            'optional': ['reset']
        },

        'history': {
            'type': 'js',
            'path': 'history/history-beta-min.js',
            'requires': ['event']
        },

        'imageloader': {
            'type': 'js',
            'path': 'imageloader/imageloader-experimental-min.js',
            'requires': ['event', 'dom']
        },

        'logger': {
            'type': 'js',
            'path': 'logger/logger-min.js',
            'requires': ['event', 'dom'],
            'optional': ['dragdrop'],
            'skinnable': true
        },

        'menu': {
            'type': 'js',
            'path': 'menu/menu-min.js',
            'requires': ['containercore'],
            'skinnable': true
        },

        'reset': {
            'type': 'css',
            'path': 'reset/reset-min.css'
        },

        'reset-fonts-grids': {
            'type': 'css',
            'path': 'reset-fonts-grids/reset-fonts-grids.css',
            'supersedes': ['reset', 'fonts', 'grids']
        },

        'slider': {
            'type': 'js',
            'path': 'slider/slider-min.js',
            'requires': ['dragdrop'],
            'optional': ['animation']
        },

        'tabview': {
            'type': 'js',
            'path': 'tabview/tabview-min.js',
            'requires': ['element'],
            'optional': ['connection'],
            'skinnable': true
        },

        'treeview': {
            'type': 'js',
            'path': 'treeview/treeview-min.js',
            'requires': ['event'],
            'skinnable': true
        },

        'utilities': {
            'type': 'js',
            'path': 'utilities/utilities.js',
            'supersedes': ['yahoo', 'event', 'dragdrop', 'animation', 'dom', 'connection', 'element', 'yahoo-dom-event'],
            'rollup': 6
        },

        'yahoo': {
            'type': 'js',
            'path': 'yahoo/yahoo-min.js'
        },

        'yahoo-dom-event': {
            'type': 'js',
            'path': 'yahoo-dom-event/yahoo-dom-event.js',
            'supersedes': ['yahoo', 'event', 'dom'],
            'rollup': 3
        },

        'yuiloader': {
            'type': 'js',
            'path': 'yuiloader/yuiloader-beta-min.js'
        },

        'yuitest': {
            'type': 'js',
            'path': 'yuitest/yuitest-beta-min.js',
            'requires': ['logger'],
            'skinnable': true
        }
    }
}
 , 

        // Simple utils since we can't count on YAHOO.lang being available.
        ObjectUtil: {
            appendArray: function(o, a) {
                if (a) {
                    for (var i=0; i<a.length; i=i+1) {
                        o[a[i]] = true;
                    }
                }
            },

            clone: function(o) {
                var c = {};
                for (var i in o) {
                    c[i] = o[i];
                }
                return c;
            },

            merge: function() {
                var o={}, a=arguments, i, j;
                for (i=0; i<a.length; i=i+1) {
                    
                    for (j in a[i]) {
                        o[j] = a[i][j];
                    }
                }
                return o;
            },

            keys: function(o, ordered) {
                var a=[], i;
                for (i in o) {
                    a.push(i);
                }

                return a;
            }
        },

        ArrayUtil: {

            appendArray: function(a1, a2) {
                Array.prototype.push.apply(a1, a2);
                /*
                for (var i=0; i<a2.length; i=i+1) {
                    a1.push(a2[i]);
                }
                */
            },

            indexOf: function(a, val) {
                for (var i=0; i<a.length; i=i+1) {
                    if (a[i] === val) {
                        return i;
                    }
                }

                return -1;
            },

            toObject: function(a) {
                var o = {};
                for (var i=0; i<a.length; i=i+1) {
                    o[a[i]] = true;
                }

                return o;
            },

            /*
             * Returns a unique array.  Does not maintain order, which is fine
             * for this application, and performs better than it would if it
             * did.
             */
            uniq: function(a) {
                return YUI.ObjectUtil.keys(YUI.ArrayUtil.toObject(a));
            }
        },


        // loader instances
        loaders: [],

        finishInit: function(yahooref) {

            // YAHOO has been loaded either in this window or passed 
            // from the sandbox routine.  Set up local references 
            // to the loader and module metadata in the YAHOO object
            // in question so additional modules can be loaded. 

            yahooref = yahooref || YAHOO;

            yahooref.env.YUIInfo=YUI.info;
            yahooref.util.YUILoader=YUI.YUILoader;

        },

        /*
         * Global handler for the module loaded event exposed by
         * YAHOO
         */
        onModuleLoaded: function(minfo) {

            var mname = minfo.name, m;

            for (var i=0; i<YUI.loaders.length; i=i+1) {
                YUI.loaders[i].loadNext(mname);
            }

            //console.log(YAHOO.lang.dump(minfo));

        },

        /*
         * Sets up the module metadata
         */
        init: function() {

            var c = YAHOO_config, o = c.load, 
                y_loaded = (typeof YAHOO !== "undefined" && YAHOO.env);


            // add our listener to the existing YAHOO.env.listeners stack
            if (y_loaded) {

                YAHOO.env.listeners.push(YUI.onModuleLoaded);

            // define a listener in YAHOO_config that YAHOO will pick up
            // when it is loaded.
            } else {

                if (c.listener) {
                    YUI.cachedCallback = c.listener;
                }

                c.listener = function(minfo) {
                    YUI.onModuleLoaded(minfo);
                    if (YUI.cachedCallback) {
                        YUI.cachedCallback(minfo);
                    }
                };
            }

            // Fetch the required modules immediately if specified
            // in YAHOO_config.  Otherwise detect YAHOO and fetch
            // it if it doesn't exist so we have a place to put
            // the loader.  The problem with this is that it will
            // prevent rollups from working
            if (o || !y_loaded) {

                o = o || {};

                var loader = new YUI.YUILoader(o);
                loader.onLoadComplete = function() {

                        YUI.finishInit();

                        if (o.onLoadComplete) {

                            loader._pushEvents();
                            o.onLoadComplete(loader);
                        }

                        
                    };

                // If no load was requested, we must load YAHOO
                // so we have a place to put the loader
                if (!y_loaded) {
                    loader.require("yahoo");
                }

                loader.insert(null, o);
            } else {
                YUI.finishInit();
            }
        }

    };

    YUI.YUILoader = function(o) {

        // Inform the library that it is being injected
        YAHOO_config.injecting = true;

        o = o || {};

        /**
         * Internal callback to handle multiple internal insert() calls
         * so that css is inserted prior to js
         * @property _internalCallback
         * @private
         */
        this._internalCallback = null;

        /**
         * Callback that will be executed when the loader is finished
         * with an insert
         * @method onLoadComplete
         * @type function
         */
        this.onLoadComplete = null;

        /**
         * The base directory.
         * @property base
         * @type string
         * @default build
         */
        this.base = ("base" in o) ? o.base : YUI.info.base;

        /**
         * Should we allow rollups
         * @property allowRollup
         * @type boolean
         * @default true
         */
        this.allowRollup = ("allowRollup" in o) ? o.allowRollup : true;

        /**
         * Filter to apply to result url
         * @property filter
         * @type string|object
         */
        this.filter = o.filter;

        /**
         * Create a sandbox rather than inserting into lib into.
         * the current context.  Not currently supported
         * property sandbox
         * @type boolean
         * @default false
         */
        this.sandbox = o.sandbox;

        /**
         * The list of requested modules
         * @property required
         * @type {string: boolean}
         */
        this.required = {};

        /**
         * The library metadata
         * @property moduleInfo
         */
        this.moduleInfo = o.moduleInfo || YUI.info.moduleInfo;

        /**
         * List of rollup files found in the library metadata
         * @property rollups
         */
        this.rollups = null;

        /**
         * Whether or not to load optional dependencies for 
         * the requested modules
         * @property loadOptional
         * @type boolean
         * @default false
         */
        this.loadOptional = o.loadOptional || false;

        /**
         * All of the derived dependencies in sorted order, which
         * will be populated when either calculate() or insert()
         * is called
         * @property sorted
         * @type string[]
         */
        this.sorted = [];

        /**
         * Set when beginning to compute the dependency tree. 
         * Composed of what YAHOO reports to be loaded combined
         * with what has been loaded by the tool
         * @propery loaded
         * @type {string: boolean}
         */
        this.loaded = {};

        /**
         * Flag to indicate the dependency tree needs to be recomputed
         * if insert is called again.
         * @property dirty
         * @type boolean
         * @default true
         */
        this.dirty = true;

        /**
         * List of modules inserted by the utility
         * @property inserted
         * @type {string: boolean}
         */
        this.inserted = {};


        /**
         * Provides the information used to skin the skinnable components.
         * The following skin definition would result in 'skin1' and 'skin2'
         * being loaded for calendar (if calendar was requested), and
         * 'sam' for all other skinnable components:
         *
         *   <code>
         *   skin: {
         *
         *      // The default skin, which is automatically applied if not
         *      // overriden by a component-specific skin definition.
         *      // Change this in to apply a different skin globally
         *      defaultSkin: 'sam', 
         *
         *      // This is combined with the loader base property to get
         *      // the default root directory for a skin. ex:
         *      // http://yui.yahooapis.com/2.3.0/build/assets/skins/sam/
         *      base: 'assets/skins/',
         *
         *      // The name of the rollup css file for the skin
         *      path: 'skin.css',
         *
         *      // The number of skinnable components requested that are
         *      // required before using the rollup file rather than the
         *      // individual component css files
         *      rollup: 3,
         *
         *      // Any component-specific overrides can be specified here,
         *      // making it possible to load different skins for different
         *      // components.  It is possible to load more than one skin
         *      // for a given component as well.
         *      overrides: {
         *          calendar: ['skin1', 'skin2']
         *      }
         *   }
         *   </code>
         *   @property skin
         */
        this.skin = o.skin || YUI.ObjectUtil.clone(YUI.info.skin); 


        if (o.require) {
            this.require(o.require);
        }

        YUI.loaders.push(this);
    };

    YUI.YUILoader.prototype = {

        FILTERS: {
            RAW: { 
                'searchExp': "-min\\.js", 
                'replaceStr': ".js"
            },
            DEBUG: { 
                'searchExp': "-min\\.js", 
                'replaceStr': "-debug.js"
            }
        },

        SKIN_PREFIX: "skin-",

        /** Add a new module to the component metadata.  The javascript 
         * component must also use YAHOO.register to notify the loader 
         * when it has been loaded, or a verifier function must be
         * provided
         * <dl>
         *     <dt>name:</dt>       <dd>required, the component name</dd>
         *     <dt>type:</dt>       <dd>required, the component type (js or css)</dd>
         *     <dt>path:</dt>       <dd>required, the path to the script from "base"</dd>
         *     <dt>requires:</dt>   <dd>the modules required by this component</dd>
         *     <dt>optional:</dt>   <dd>the optional modules for this component</dd>
         *     <dt>supersedes:</dt> <dd>the modules this component replaces</dd>
         *     <dt>rollup:</dt>     <dd>the number of superseded modules required for automatic rollup</dd>
         *     <dt>verifier:</dt>   <dd>a function that is executed to determine when the module is fully loaded</dd>
         *     <dt>fullpath:</dt>   <dd>If fullpath is specified, this is used instead of the configured base + path</dd>
         *     <dt>skinnable:</dt>  <dd>flag to determine if skin assets should automatically be pulled in</dd>
         * </dl>
         * @method addModule
         * @param o An object containing the module data
         * @return {boolean} true if the module was added, false if 
         * the object passed in did not provide all required attributes
         */
        addModule: function(o) {

            if (!o || !o.name || !o.type || (!o.path && !o.fullpath)) {
                return false;
            }

            this.moduleInfo[o.name] = o;
            this.dirty = true;

            return true;
        },

        /**
         * Add a requirement for one or more module
         * @method require
         * @param what {string[] | string*} the modules to load
         */
        require: function(what) {
            var a = (typeof what === "string") ? arguments : what;

            this.dirty = true;

            for (var i=0; i<a.length; i=i+1) {
                this.required[a[i]] = true;
                var s = this.parseSkin(a[i]);
                if (s) {
                    this._addSkin(s.skin, s.module);
                }
            }
            YUI.ObjectUtil.appendArray(this.required, a);
        },


        /**
         * Adds the skin def to the module info
         * @method _addSkin
         * @private
         */
        _addSkin: function(skin, mod) {

            // Add a module definition for the skin rollup css
            var name = this.formatSkin(skin);
            if (!this.moduleInfo[name]) {
                this.addModule({
                    'name': name,
                    'type': 'css',
                    'path': this.skin.base + skin + "/" + this.skin.path,
                    //'supersedes': '*',
                    'rollup': this.skin.rollup
                });
            }

            // Add a module definition for the module-specific skin css
            if (mod) {
                name = this.formatSkin(skin, mod);
                if (!this.moduleInfo[name]) {
                    this.addModule({
                        'name': name,
                        'type': 'css',
                        //'path': this.skin.base + skin + "/" + mod + ".css"
                        'path': mod + '/' + this.skin.base + skin + "/" + mod + ".css"
                    });
                }
            }
        },

        /**
         * Returns an object containing properties for all modules required
         * in order to load the requested module
         * @method getRequires
         * @param mod The module definition from moduleInfo
         */
        getRequires: function(mod) {
            if (!this.dirty && mod.expanded) {
                return mod.expanded;
            }

            mod.requires=mod.requires || [];
            var i, d=[], r=mod.requires, o=mod.optional, s=mod.supersedes, info=this.moduleInfo;
            for (i=0; i<r.length; i=i+1) {
                d.push(r[i]);
                YUI.ArrayUtil.appendArray(d, this.getRequires(info[r[i]]));
            }

            if (o && this.loadOptional) {
                for (i=0; i<o.length; i=i+1) {
                    d.push(o[i]);
                    YUI.ArrayUtil.appendArray(d, this.getRequires(info[o[i]]));
                }
            }

            mod.expanded = YUI.ArrayUtil.uniq(d);

            return mod.expanded;
        },

        /**
         * Returns an object literal of the modules the supplied module satisfies
         * @method getProvides
         * @param mod The module definition from moduleInfo
         * @return what this module provides
         */
        getProvides: function(name) {
            var mod = this.moduleInfo[name];

            var o = {};
            o[name] = true;
            s = mod && mod.supersedes;

            YUI.ObjectUtil.appendArray(o, s);

            // console.log(this.sorted + ", " + name + " provides " + YUI.ObjectUtil.keys(o));

            return o;
        },

        /**
         * Calculates the dependency tree, the result is stored in the sorted 
         * property
         * @method calculate
         * @param o optional options object
         */
        calculate: function(o) {
            if (this.dirty) {

                this._setup(o);
                this._explode();
                this._skin();
                if (this.allowRollup) {
                    this._rollup();
                }
                this._reduce();
                this._sort();

                this.dirty = false;
            }
        },

        /**
         * Investigates the current YUI configuration on the page.  By default,
         * modules already detected will not be loaded again unless a force
         * option is encountered.  Called by calculate()
         * @method _setup
         * @param o optional options object
         * @private
         */
        _setup: function(o) {

            o = o || {};
            this.loaded = YUI.ObjectUtil.clone(this.inserted); 
            
            if (!this.sandbox && typeof YAHOO !== "undefined" && YAHOO.env) {
                this.loaded = YUI.ObjectUtil.merge(this.loaded, YAHOO.env.modules);
            }

            // add the ignore list to the list of loaded packages
            if (o.ignore) {
                YUI.ObjectUtil.appendArray(this.loaded, o.ignore);
            }

            // remove modules on the force list from the loaded list
            if (o.force) {
                for (var i=0; i<o.force.length; i=i+1) {
                    if (o.force[i] in this.loaded) {
                        delete this.loaded[o.force[i]];
                    }
                }
            }
        },
        

        /**
         * Inspects the required modules list looking for additional 
         * dependencies.  Expands the required list to include all 
         * required modules.  Called by calculate()
         * @method _explode
         * @private
         */
        _explode: function() {

            var r=this.required, i, mod;

            for (i in r) {
                mod = this.moduleInfo[i];
                if (mod) {

                    var req = this.getRequires(mod);

                    if (req) {
                        YUI.ObjectUtil.appendArray(r, req);
                    }
                }
            }
        },

        /**
         * Sets up the requirements for the skin assets if any of the
         * requested modules are skinnable
         * @method _skin
         * @private
         */
        _skin: function() {

            var r=this.required, i, mod;

            for (i in r) {
                mod = this.moduleInfo[i];
                if (mod && mod.skinnable) {
                    var o=this.skin.override, j;
                    if (o && o[i]) {
                        for (j=0; j<o[i].length; j=j+1) {
                            this.require(this.formatSkin(o[i][j], i));
                        }
                    } else {
                        this.require(this.formatSkin(this.skin.defaultSkin, i));
                    }
                }
            }
        },

        /**
         * Returns the skin module name for the specified skin name.  If a
         * module name is supplied, the returned skin module name is 
         * specific to the module passed in.
         * @method formatSkin
         * @param skin {string} the name of the skin
         * @param mod {string} optional: the name of a module to skin
         * @return {string} the full skin module name
         */
        formatSkin: function(skin, mod) {
            var s = this.SKIN_PREFIX + skin;
            if (mod) {
                s = s + "-" + mod;
            }

            return s;
        },
        
        /**
         * Reverses <code>formatSkin</code>, providing the skin name and
         * module name if the string matches the pattern for skins.
         * @method parseSkin
         * @param mod {string} the module name to parse
         * @return {skin: string, module: string} the parsed skin name 
         * and module name, or null if the supplied string does not match
         * the skin pattern
         */
        parseSkin: function(mod) {
            
            if (mod.indexOf(this.SKIN_PREFIX) === 0) {
                var a = mod.split("-");
                return {skin: a[1], module: a[2]};
            } 

            return null;
        },

        /**
         * Look for rollup packages to determine if all of the modules a
         * rollup supersedes are required.  If so, include the rollup to
         * help reduce the total number of connections required.  Called
         * by calculate()
         * @method _rollup
         * @private
         */
        _rollup: function() {
            var i, j, m, s, rollups={}, r=this.required, roll;

            // find and cache rollup modules
            if (this.dirty || !this.rollups) {
                for (i in this.moduleInfo) {
                    m = this.moduleInfo[i];
                    //if (m && m.rollup && m.supersedes) {
                    if (m && m.rollup) {
                        rollups[i] = m;
                    }
                }

                this.rollups = rollups;
            }

            // make as many passes as needed to pick up rollup rollups
            for (;;) {
                var rolled = false;

                // go through the rollup candidates
                for (i in rollups) { 

                    // there can be only one
                    if (!r[i] && !this.loaded[i]) {
                        m =this.moduleInfo[i]; s = m.supersedes; roll=true;

                        if (!m.rollup) {
                            continue;
                        }


                        var skin = this.parseSkin(i), c = 0;
                        if (skin) {

                            for (j in r) {
                                if (i !== j && this.parseSkin(j)) {
                                    c++;
                                    roll = (c >= m.rollup);
                                    if (roll) {
                                        break;
                                    }
                                }
                            }


                        } else {

                            // require all modules to trigger a rollup (using the 
                            // threshold value has not proved worthwhile)
                            for (j=0;j<s.length;j=j+1) {

                                // if the superseded module is loaded, we can't load the rollup
                                if (this.loaded[s[j]]) {
                                    roll = false;
                                    break;
                                // increment the counter if this module is required.  if we are
                                // beyond the rollup threshold, we will use the rollup module
                                } else if (r[s[j]]) {
                                    c++;
                                    roll = (c >= m.rollup);
                                    if (roll) {
                                        break;
                                    }
                                }
                            }
                        }

                        if (roll) {
                            // add the rollup
                            r[i] = true;
                            rolled = true;

                            // expand the rollup's dependencies
                            this.getRequires(m);
                        }
                    }
                }

                // if we made it here w/o rolling up something, we are done
                if (!rolled) {
                    break;
                }
            }
        },

        /**
         * Remove superceded modules and loaded modules.  Called by
         * calculate() after we have the mega list of all dependencies
         * @method _reduce
         * @private
         */
        _reduce: function() {

            var i, j, s, m, r=this.required;
            for (i in r) {

                // remove if already loaded
                if (i in this.loaded) { 
                    delete r[i];

                // remove anything this module supersedes
                } else {

                    var skinDef = this.parseSkin(i);

                    if (skinDef) {
                        //console.log("skin found in reduce: " + skinDef.skin + ", " + skinDef.module);
                        // the skin rollup will not have a module name
                        if (!skinDef.module) {
                            var skin_pre = this.SKIN_PREFIX + skinDef.skin;
                            //console.log("skin_pre: " + skin_pre);
                            for (j in r) {
                                if (j !== i && j.indexOf(skin_pre) > -1) {
                                    //console.log ("removing component skin: " + j);
                                    delete r[j];
                                }
                            }
                        }
                    } else {

                         m = this.moduleInfo[i];
                         s = m && m.supersedes;
                         if (s) {
                             for (j=0;j<s.length;j=j+1) {
                                 if (s[j] in r) {
                                     delete r[s[j]];
                                 }
                             }
                         }
                    }
                }
            }
        },
        
        /**
         * Sorts the dependency tree.  The last step of calculate()
         * @method _sort
         * @private
         */
        _sort: function() {
            // create an indexed list
            var s=[], info=this.moduleInfo, loaded=this.loaded;

            // returns true if b is not loaded, and is required
            // directly or by means of modules it supersedes.
            var requires = function(aa, bb) {
                if (loaded[bb]) {
                    return false;
                }

                var ii, mm=info[aa], rr=mm && mm.expanded;

                if (rr && YUI.ArrayUtil.indexOf(rr, bb) > -1) {
                    return true;
                }

                var ss=info[bb] && info[bb].supersedes;
                if (ss) {
                    for (ii=0; ii<ss.length; ii=i+1) {
                        if (requires(aa, ss[ii])) {
                            return true;
                        }
                    }
                }

                return false;
            };

            // get the required items out of the obj into an array so we
            // can sort
            for (var i in this.required) {
                s.push(i);
            }

            // pointer to the first unsorted item
            var p=0; 

            // keep going until we make a pass without moving anything
            for (;;) {
               
                var l=s.length, a, b, j, k, moved=false;

                // start the loop after items that are already sorted
                for (j=p; j<l; j=j+1) {

                    // check the next module on the list to see if its
                    // dependencies have been met
                    a = s[j];

                    // check everything below current item and move if we
                    // find a requirement for the current item
                    for (k=j+1; k<l; k=k+1) {
                        if (requires(a, s[k])) {

                            // extract the dependency so we can move it up
                            b = s.splice(k, 1);

                            // insert the dependency above the item that 
                            // requires it
                            s.splice(j, 0, b[0]);

                            moved = true;
                            break;
                        }
                    }

                    // jump out of loop if we moved something
                    if (moved) {
                        break;
                    // this item is sorted, move our pointer and keep going
                    } else {
                        p = p + 1;
                    }
                }

                // when we make it here and moved is false, we are 
                // finished sorting
                if (!moved) {
                    break;
                }

            }

            this.sorted = s;
        },

        /**
         * inserts the requested modules and their dependencies.  
         * <code>type</code> can be "js" or "css".  Both script and 
         * css are inserted if type is not provided.
         * @method insert
         * @param callback {Function} a function to execute when the load
         * is complete.
         * @param o optional options object
         * @param type {string} the type of dependency to insert
         */
        insert: function(callback, o, type) {

            //if (!this.onLoadComplete) {
                //this.onLoadComplete = callback;
            //}

            if (!type) {
                var self = this;
                this._internalCallback = function() {
                            self._internalCallback = null;
                            self.insert(callback, o, "js");
                        };
                this.insert(null, o, "css");
                return;
            }

            o = o || {};

            // store the callback for when we are done
            this.onLoadComplete = callback || this.onLoadComplete;

            // store the optional filter
            var f = o && o.filter || null;

            if (typeof f === "string") {
                f = f.toUpperCase();

                // the logger must be available in order to use the debug
                // versions of the library
                if (f === "DEBUG") {
                    this.require("logger");
                }
            }

            this.filter = this.FILTERS[f] || f || this.FILTERS[this.filter] || this.filter;

            // store the options... not currently in use
            this.insertOptions = o;

            // build the dependency list
            this.calculate(o);

            // set a flag to indicate the load has started
            this.loading = true;

            // keep the loadType (js, css or undefined) cached
            this.loadType = type;

            // start the load
            this.loadNext();

        },

        /**
         * Executed every time a module is loaded, and if we are in a load
         * cycle, we attempt to load the next script.  Public so that it
         * is possible to call this if using a method other than
         * YAHOO.register to determine when scripts are fully loaded
         * @method loadNext
         * @param mname {string} optional the name of the module that has
         * been loaded (which is usually why it is time to load the next
         * one)
         */
        loadNext: function(mname) {

            // console.log("loadNext executing, just loaded " + mname);

            // The global handler that is called when each module is loaded
            // will pass that module name to this function.  Storing this
            // data to avoid loading the same module multiple times
            if (mname) {
                this.inserted[mname] = true;
                //var o = this.getProvides(mname);
                //this.inserted = YUI.ObjectUtil.merge(this.inserted, o);
            }

            // It is possible that this function is executed due to something
            // else one the page loading a YUI module.  Only react when we
            // are actively loading something
            if (!this.loading) {
                return;
            }

            // if the module that was just loaded isn't what we were expecting,
            // continue to wait
            if (mname && mname !== this.loading) {
                return;
            }
            
            var s=this.sorted, len=s.length, i, m, url;

            for (i=0; i<len; i=i+1) {

                // This.inserted keeps track of what the loader has loaded
                if (s[i] in this.inserted) {
                    // console.log(s[i] + " alread loaded ");
                    continue;
                }

                // Because rollups will cause multiple load notifications
                // from YAHOO, loadNext may be called multiple times for
                // the same module when loading a rollup.  We can safely
                // skip the subsequent requests
                if (s[i] === this.loading) {
                    // console.log("still loading " + s[i] + ", waiting");
                    return;
                }

                // log("inserting " + s[i]);

                m = this.moduleInfo[s[i]];

                // The load type is stored to offer the possibility to load
                // the css separately from the script.
                if (!this.loadType || this.loadType === m.type) {
                    this.loading = s[i];

                    // Insert the css node and continue.  It is possible
                    // that the css file will load out of order ... this
                    // may be a problem that needs to be addressed, but
                    // unlike the script files, there is no notification
                    // mechanism in place for the css files.
                    if (m.type === "css") {

                        url = m.fullpath || this._url(m.path);
                        
                        this.insertCss(url);
                        this.inserted[s[i]] = true;

                    // Scripts must be loaded in order, so we wait for the
                    // notification from YAHOO or a verifier function to 
                    // process the next script
                    } else {

                        url = m.fullpath || this._url(m.path);
                        this.insertScript(url);

                        // if a verifier was included for this module, execute
                        // it, passing the name of the module, and a callback
                        // that must be exectued when the verifier is done.
                        if (m.verifier) {
                            var self = this, name=s[i];
                            m.verifier(name, function() {
                                    self.loadNext(name);
                                });
                        }

                        return;
                    }
                }
            }

            // we are finished
            this.loading = null;


            // internal callback for loading css first
            if (this._internalCallback) {
                var f = this._internalCallback;
                this._internalCallback = null;
                f(this);
            } else if (this.onLoadComplete) {
                this._pushEvents();
                this.onLoadComplete(this);
            }

        },

        /**
         * In IE, the onAvailable/onDOMReady events need help when Event is
         * loaded dynamically
         * @method _pushEvents
         * @private
         */
        _pushEvents: function() {
            if (typeof YAHOO !== "undefined" && YAHOO.util && YAHOO.util.Event) {
                YAHOO.util.Event._load();
            }
        },

        /**
         * Generates the full url for a module
         * method _url
         * @param path {string} the path fragment
         * @return {string} the full url
         * @private
         */
        _url: function(path) {
            
            var u = this.base || "", f=this.filter;
            u = u + path;

            if (f) {
                // console.log("filter: " + f + ", " + f.searchExp + 
                // ", " + f.replaceStr);
                u = u.replace(new RegExp(f.searchExp), f.replaceStr);
            }

            // console.log(u);

            return u;
        },

        /**
         * Inserts a script node
         * @method insertScript
         * @param url {string} the full url for the script
         * @param win {Window} optional window to target
         */
        insertScript: function(url, win) {

            //console.log("inserting script " + url);
            var w = win || window, d=w.document, n=d.createElement("script"),
                h = d.getElementsByTagName("head")[0];

            n.src = url;
            n.type = "text/javascript";
            h.appendChild(n);
        },

        /**
         * Inserts a css link node
         * @method insertCss
         * @param url {string} the full url for the script
         * @param win {Window} optional window to target
         */
        insertCss: function(url, win) {
            // console.log("inserting css " + url);
            var w = win || window, d=w.document, n=d.createElement("link"),
                h = d.getElementsByTagName("head")[0];

            n.href = url;
            n.type = "text/css";
            n.rel = "stylesheet";
            h.appendChild(n);
        },
       
        /*
         * Interns the script for the requested modules.  The callback is
         * provided a reference to the sandboxed YAHOO object.  This only
         * applies to the script: css can not be sandboxed.  Not implemented.
         * @method sandbox
         * @param callback {Function} the callback to exectued when the load is
         *        complete.
         * @notimplemented
         */
        sandbox: function(callback) {
            // this.calculate({
                         //sandbox: true
                     //});
        }
    };

    YUI.init();

})();
