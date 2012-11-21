// Miscellaneous core Javascript functions for Moodle
// Global M object is initilised in inline javascript

/**
 * Add module to list of available modules that can be loaded from YUI.
 * @param {Array} modules
 */
M.yui.add_module = function(modules) {
    for (var modname in modules) {
        YUI_config.modules[modname] = modules[modname];
    }
};
/**
 * The gallery version to use when loading YUI modules from the gallery.
 * Will be changed every time when using local galleries.
 */
M.yui.galleryversion = '2010.04.21-21-51';

/**
 * Various utility functions
 */
M.util = M.util || {};

/**
 * Language strings - initialised from page footer.
 */
M.str = M.str || {};

/**
 * Returns url for images.
 * @param {String} imagename
 * @param {String} component
 * @return {String}
 */
M.util.image_url = function(imagename, component) {

    if (!component || component == '' || component == 'moodle' || component == 'core') {
        component = 'core';
    }

    var url = M.cfg.wwwroot + '/theme/image.php';
    if (M.cfg.themerev > 0 && M.cfg.slasharguments == 1) {
        if (!M.cfg.svgicons) {
            url += '/_s';
        }
        url += '/' + M.cfg.theme + '/' + component + '/' + M.cfg.themerev + '/' + imagename;
    } else {
        url += '?theme=' + M.cfg.theme + '&component=' + component + '&rev=' + M.cfg.themerev + '&image=' + imagename;
        if (!M.cfg.svgicons) {
            url += '&svg=0';
        }
    }

    return url;
};

M.util.in_array = function(item, array){
    for( var i = 0; i<array.length; i++){
        if(item==array[i]){
            return true;
        }
    }
    return false;
};

/**
 * Init a collapsible region, see print_collapsible_region in weblib.php
 * @param {YUI} Y YUI3 instance with all libraries loaded
 * @param {String} id the HTML id for the div.
 * @param {String} userpref the user preference that records the state of this box. false if none.
 * @param {String} strtooltip
 */
M.util.init_collapsible_region = function(Y, id, userpref, strtooltip) {
    Y.use('anim', function(Y) {
        new M.util.CollapsibleRegion(Y, id, userpref, strtooltip);
    });
};

/**
 * Object to handle a collapsible region : instantiate and forget styled object
 *
 * @class
 * @constructor
 * @param {YUI} Y YUI3 instance with all libraries loaded
 * @param {String} id The HTML id for the div.
 * @param {String} userpref The user preference that records the state of this box. false if none.
 * @param {String} strtooltip
 */
M.util.CollapsibleRegion = function(Y, id, userpref, strtooltip) {
    // Record the pref name
    this.userpref = userpref;

    // Find the divs in the document.
    this.div = Y.one('#'+id);

    // Get the caption for the collapsible region
    var caption = this.div.one('#'+id + '_caption');

    // Create a link
    var a = Y.Node.create('<a href="#"></a>');
    a.setAttribute('title', strtooltip);

    // Get all the nodes from caption, remove them and append them to <a>
    while (caption.hasChildNodes()) {
        child = caption.get('firstChild');
        child.remove();
        a.append(child);
    }
    caption.append(a);

    // Get the height of the div at this point before we shrink it if required
    var height = this.div.get('offsetHeight');
    var collapsedimage = 't/collapsed'; // ltr mode
    if (right_to_left()) {
        collapsedimage = 't/collapsed_rtl';
    } else {
        collapsedimage = 't/collapsed';
    }
    if (this.div.hasClass('collapsed')) {
        // Add the correct image and record the YUI node created in the process
        this.icon = Y.Node.create('<img src="'+M.util.image_url(collapsedimage, 'moodle')+'" alt="" />');
        // Shrink the div as it is collapsed by default
        this.div.setStyle('height', caption.get('offsetHeight')+'px');
    } else {
        // Add the correct image and record the YUI node created in the process
        this.icon = Y.Node.create('<img src="'+M.util.image_url('t/expanded', 'moodle')+'" alt="" />');
    }
    a.append(this.icon);

    // Create the animation.
    var animation = new Y.Anim({
        node: this.div,
        duration: 0.3,
        easing: Y.Easing.easeBoth,
        to: {height:caption.get('offsetHeight')},
        from: {height:height}
    });

    // Handler for the animation finishing.
    animation.on('end', function() {
        this.div.toggleClass('collapsed');
        var collapsedimage = 't/collapsed'; // ltr mode
        if (right_to_left()) {
            collapsedimage = 't/collapsed_rtl';
            } else {
            collapsedimage = 't/collapsed';
            }
        if (this.div.hasClass('collapsed')) {
            this.icon.set('src', M.util.image_url(collapsedimage, 'moodle'));
        } else {
            this.icon.set('src', M.util.image_url('t/expanded', 'moodle'));
        }
    }, this);

    // Hook up the event handler.
    a.on('click', function(e, animation) {
        e.preventDefault();
        // Animate to the appropriate size.
        if (animation.get('running')) {
            animation.stop();
        }
        animation.set('reverse', this.div.hasClass('collapsed'));
        // Update the user preference.
        if (this.userpref) {
            M.util.set_user_preference(this.userpref, !this.div.hasClass('collapsed'));
        }
        animation.run();
    }, this, animation);
};

/**
 * The user preference that stores the state of this box.
 * @property userpref
 * @type String
 */
M.util.CollapsibleRegion.prototype.userpref = null;

/**
 * The key divs that make up this
 * @property div
 * @type Y.Node
 */
M.util.CollapsibleRegion.prototype.div = null;

/**
 * The key divs that make up this
 * @property icon
 * @type Y.Node
 */
M.util.CollapsibleRegion.prototype.icon = null;

/**
 * Makes a best effort to connect back to Moodle to update a user preference,
 * however, there is no mechanism for finding out if the update succeeded.
 *
 * Before you can use this function in your JavsScript, you must have called
 * user_preference_allow_ajax_update from moodlelib.php to tell Moodle that
 * the udpate is allowed, and how to safely clean and submitted values.
 *
 * @param String name the name of the setting to udpate.
 * @param String the value to set it to.
 */
M.util.set_user_preference = function(name, value) {
    YUI().use('io', function(Y) {
        var url = M.cfg.wwwroot + '/lib/ajax/setuserpref.php?sesskey=' +
                M.cfg.sesskey + '&pref=' + encodeURI(name) + '&value=' + encodeURI(value);

        // If we are a developer, ensure that failures are reported.
        var cfg = {
                method: 'get',
                on: {}
            };
        if (M.cfg.developerdebug) {
            cfg.on.failure = function(id, o, args) {
                alert("Error updating user preference '" + name + "' using ajax. Clicking this link will repeat the Ajax call that failed so you can see the error: ");
            }
        }

        // Make the request.
        Y.io(url, cfg);
    });
};

/**
 * Prints a confirmation dialog in the style of DOM.confirm().
 * @param object event A YUI DOM event or null if launched manually
 * @param string message The message to show in the dialog
 * @param string url The URL to forward to if YES is clicked. Disabled if fn is given
 * @param function fn A JS function to run if YES is clicked.
 */
M.util.show_confirm_dialog = function(e, args) {
    var target = e.target;
    if (e.preventDefault) {
        e.preventDefault();
    }

    YUI().use('yui2-container', 'yui2-event', function(Y) {
        var simpledialog = new Y.YUI2.widget.SimpleDialog('confirmdialog',
            {width: '300px',
              fixedcenter: true,
              modal: true,
              visible: false,
              draggable: false
            }
        );

        simpledialog.setHeader(M.str.admin.confirmation);
        simpledialog.setBody(args.message);
        simpledialog.cfg.setProperty('icon', Y.YUI2.widget.SimpleDialog.ICON_WARN);

        var handle_cancel = function() {
            simpledialog.hide();
        };

        var handle_yes = function() {
            simpledialog.hide();

            if (args.callback) {
                // args comes from PHP, so callback will be a string, needs to be evaluated by JS
                var callback = null;
                if (Y.Lang.isFunction(args.callback)) {
                    callback = args.callback;
                } else {
                    callback = eval('('+args.callback+')');
                }

                if (Y.Lang.isObject(args.scope)) {
                    var sc = args.scope;
                } else {
                    var sc = e.target;
                }

                if (args.callbackargs) {
                    callback.apply(sc, args.callbackargs);
                } else {
                    callback.apply(sc);
                }
                return;
            }

            var targetancestor = null,
                targetform = null;

            if (target.test('a')) {
                window.location = target.get('href');

            } else if ((targetancestor = target.ancestor('a')) !== null) {
                window.location = targetancestor.get('href');

            } else if (target.test('input')) {
                targetform = target.ancestor(function(node) { return node.get('tagName').toLowerCase() == 'form'; });
                // We cannot use target.ancestor('form') on the previous line
                // because of http://yuilibrary.com/projects/yui3/ticket/2531561
                if (!targetform) {
                    return;
                }
                if (target.get('name') && target.get('value')) {
                    targetform.append('<input type="hidden" name="' + target.get('name') +
                                    '" value="' + target.get('value') + '">');
                }
                targetform.submit();

            } else if (target.get('tagName').toLowerCase() == 'form') {
                // We cannot use target.test('form') on the previous line because of
                // http://yuilibrary.com/projects/yui3/ticket/2531561
                target.submit();

            } else if (M.cfg.developerdebug) {
                alert("Element of type " + target.get('tagName') + " is not supported by the M.util.show_confirm_dialog function. Use A, INPUT, or FORM");
            }
        };

        if (!args.cancellabel) {
            args.cancellabel = M.str.moodle.cancel;
        }
        if (!args.continuelabel) {
            args.continuelabel = M.str.moodle.yes;
        }

        var buttons = [
            {text: args.cancellabel,   handler: handle_cancel, isDefault: true},
            {text: args.continuelabel, handler: handle_yes}
        ];

        simpledialog.cfg.queueProperty('buttons', buttons);

        simpledialog.render(document.body);
        simpledialog.show();
    });
};

/** Useful for full embedding of various stuff */
M.util.init_maximised_embed = function(Y, id) {
    var obj = Y.one('#'+id);
    if (!obj) {
        return;
    }

    var get_htmlelement_size = function(el, prop) {
        if (Y.Lang.isString(el)) {
            el = Y.one('#' + el);
        }
        // Ensure element exists.
        if (el) {
            var val = el.getStyle(prop);
            if (val == 'auto') {
                val = el.getComputedStyle(prop);
            }
            return parseInt(val);
        } else {
            return 0;
        }
    };

    var resize_object = function() {
        obj.setStyle('width', '0px');
        obj.setStyle('height', '0px');
        var newwidth = get_htmlelement_size('maincontent', 'width') - 35;

        if (newwidth > 500) {
            obj.setStyle('width', newwidth  + 'px');
        } else {
            obj.setStyle('width', '500px');
        }

        var headerheight = get_htmlelement_size('page-header', 'height');
        var footerheight = get_htmlelement_size('page-footer', 'height');
        var newheight = parseInt(Y.one('body').get('winHeight')) - footerheight - headerheight - 100;
        if (newheight < 400) {
            newheight = 400;
        }
        obj.setStyle('height', newheight+'px');
    };

    resize_object();
    // fix layout if window resized too
    window.onresize = function() {
        resize_object();
    };
};

/**
 * Attach handler to single_select
 *
 * This code was deprecated in Moodle 2.4 and will be removed in Moodle 2.6
 *
 * Please see lib/yui/formautosubmit/formautosubmit.js for its replacement
 */
M.util.init_select_autosubmit = function(Y, formid, selectid, nothing) {
    if (M.cfg.developerdebug) {
        Y.log("You are using a deprecated function call (M.util.init_select_autosubmit). Please look at rewriting your call to use moodle-core-formautosubmit");
    }
    Y.use('event-key', function() {
        var select = Y.one('#'+selectid);
        if (select) {
            // Try to get the form by id
            var form = Y.one('#'+formid) || (function(){
                // Hmmm the form's id may have been overriden by an internal input
                // with the name id which will KILL IE.
                // We need to manually iterate at this point because if the case
                // above is true YUI's ancestor method will also kill IE!
                var form = select;
                while (form && form.get('nodeName').toUpperCase() !== 'FORM') {
                    form = form.ancestor();
                }
                return form;
            })();
            // Make sure we have the form
            if (form) {
                var buttonflag = 0;
                // Create a function to handle our change event
                var processchange = function(e, paramobject) {
                    if ((nothing===false || select.get('value') != nothing) && paramobject.lastindex != select.get('selectedIndex')) {
                        // chrome doesn't pick up on a click when selecting an element in a select menu, so we use
                        // the on change event to fire this function. This just checks to see if a button was
                        // first pressed before redirecting to the appropriate page.
                        if (Y.UA.os == 'windows' && Y.UA.chrome){
                            if (buttonflag == 1) {
                                buttonflag = 0;
                                this.submit();
                            }
                        } else {
                            this.submit();
                        }
                    }
                    if (e.button == 1) {
                        buttonflag = 1;
                    }
                    paramobject.lastindex = select.get('selectedIndex');
                };

                var changedown = function(e, paramobject) {
                    if ((nothing===false || select.get('value') != nothing) && paramobject.lastindex != select.get('selectedIndex')) {
                        if(e.keyCode == 13) {
                            form.submit();
                        }
                        paramobject.lastindex = select.get('selectedIndex');
                    }
                }

                var paramobject = new Object();
                paramobject.lastindex = select.get('selectedIndex');
                paramobject.eventchangeorblur = select.on('click', processchange, form, paramobject);
                // Bad hack to circumvent problems with different browsers on different systems.
                if (Y.UA.os == 'macintosh') {
                    if(Y.UA.webkit) {
                        paramobject.eventchangeorblur = select.on('change', processchange, form, paramobject);
                    }
                    paramobject.eventkeypress = Y.on('key', processchange, select, 'press:13', form, paramobject);
                } else {
                    if(Y.UA.os == 'windows' && Y.UA.chrome) {
                        paramobject.eventchangeorblur = select.on('change', processchange, form, paramobject);
                    }
                    paramobject.eventkeypress = Y.on('keydown', changedown, select, '', form, paramobject);
                }
            }
        }
    });
};

/**
 * Attach handler to url_select
 * Deprecated from 2.4 onwards.
 * Please use @see init_select_autosubmit() for redirecting to a url (above).
 * This function has accessability issues and also does not use the formid passed through as a parameter.
 */
M.util.init_url_select = function(Y, formid, selectid, nothing) {
    if (M.cfg.developerdebug) {
        Y.log("You are using a deprecated function call (M.util.init_url_select). Please look at rewriting your call to use moodle-core-formautosubmit");
    }
    YUI().use('node', function(Y) {
        Y.on('change', function() {
            if ((nothing == false && Y.Lang.isBoolean(nothing)) || Y.one('#'+selectid).get('value') != nothing) {
                window.location = M.cfg.wwwroot+Y.one('#'+selectid).get('value');
            }
        },
        '#'+selectid);
    });
};

/**
 * Breaks out all links to the top frame - used in frametop page layout.
 */
M.util.init_frametop = function(Y) {
    Y.all('a').each(function(node) {
        node.set('target', '_top');
    });
    Y.all('form').each(function(node) {
        node.set('target', '_top');
    });
};

/**
 * Finds all nodes that match the given CSS selector and attaches events to them
 * so that they toggle a given classname when clicked.
 *
 * @param {YUI} Y
 * @param {string} id An id containing elements to target
 * @param {string} cssselector A selector to use to find targets
 * @param {string} toggleclassname A classname to toggle
 */
M.util.init_toggle_class_on_click = function(Y, id, cssselector, toggleclassname, togglecssselector) {

    if (togglecssselector == '') {
        togglecssselector = cssselector;
    }

    var node = Y.one('#'+id);
    node.all(cssselector).each(function(n){
        n.on('click', function(e){
            e.stopPropagation();
            if (e.target.test(cssselector) && !e.target.test('a') && !e.target.test('img')) {
                if (this.test(togglecssselector)) {
                    this.toggleClass(toggleclassname);
                } else {
                    this.ancestor(togglecssselector).toggleClass(toggleclassname);
            }
            }
        }, n);
    });
    // Attach this click event to the node rather than all selectors... will be much better
    // for performance
    node.on('click', function(e){
        if (e.target.hasClass('addtoall')) {
            this.all(togglecssselector).addClass(toggleclassname);
        } else if (e.target.hasClass('removefromall')) {
            this.all(togglecssselector+'.'+toggleclassname).removeClass(toggleclassname);
        }
    }, node);
};

/**
 * Initialises a colour picker
 *
 * Designed to be used with admin_setting_configcolourpicker although could be used
 * anywhere, just give a text input an id and insert a div with the class admin_colourpicker
 * above or below the input (must have the same parent) and then call this with the
 * id.
 *
 * This code was mostly taken from my [Sam Hemelryk] css theme tool available in
 * contrib/blocks. For better docs refer to that.
 *
 * @param {YUI} Y
 * @param {int} id
 * @param {object} previewconf
 */
M.util.init_colour_picker = function(Y, id, previewconf) {
    /**
     * We need node and event-mouseenter
     */
    Y.use('node', 'event-mouseenter', function(){
        /**
         * The colour picker object
         */
        var colourpicker = {
            box : null,
            input : null,
            image : null,
            preview : null,
            current : null,
            eventClick : null,
            eventMouseEnter : null,
            eventMouseLeave : null,
            eventMouseMove : null,
            width : 300,
            height :  100,
            factor : 5,
            /**
             * Initalises the colour picker by putting everything together and wiring the events
             */
            init : function() {
                this.input = Y.one('#'+id);
                this.box = this.input.ancestor().one('.admin_colourpicker');
                this.image = Y.Node.create('<img alt="" class="colourdialogue" />');
                this.image.setAttribute('src', M.util.image_url('i/colourpicker', 'moodle'));
                this.preview = Y.Node.create('<div class="previewcolour"></div>');
                this.preview.setStyle('width', this.height/2).setStyle('height', this.height/2).setStyle('backgroundColor', this.input.get('value'));
                this.current = Y.Node.create('<div class="currentcolour"></div>');
                this.current.setStyle('width', this.height/2).setStyle('height', this.height/2 -1).setStyle('backgroundColor', this.input.get('value'));
                this.box.setContent('').append(this.image).append(this.preview).append(this.current);

                if (typeof(previewconf) === 'object' && previewconf !== null) {
                    Y.one('#'+id+'_preview').on('click', function(e){
                        if (Y.Lang.isString(previewconf.selector)) {
                            Y.all(previewconf.selector).setStyle(previewconf.style, this.input.get('value'));
                        } else {
                            for (var i in previewconf.selector) {
                                Y.all(previewconf.selector[i]).setStyle(previewconf.style, this.input.get('value'));
                            }
                        }
                    }, this);
                }

                this.eventClick = this.image.on('click', this.pickColour, this);
                this.eventMouseEnter = Y.on('mouseenter', this.startFollow, this.image, this);
            },
            /**
             * Starts to follow the mouse once it enter the image
             */
            startFollow : function(e) {
                this.eventMouseEnter.detach();
                this.eventMouseLeave = Y.on('mouseleave', this.endFollow, this.image, this);
                this.eventMouseMove = this.image.on('mousemove', function(e){
                    this.preview.setStyle('backgroundColor', this.determineColour(e));
                }, this);
            },
            /**
             * Stops following the mouse
             */
            endFollow : function(e) {
                this.eventMouseMove.detach();
                this.eventMouseLeave.detach();
                this.eventMouseEnter = Y.on('mouseenter', this.startFollow, this.image, this);
            },
            /**
             * Picks the colour the was clicked on
             */
            pickColour : function(e) {
                var colour = this.determineColour(e);
                this.input.set('value', colour);
                this.current.setStyle('backgroundColor', colour);
            },
            /**
             * Calculates the colour fromthe given co-ordinates
             */
            determineColour : function(e) {
                var eventx = Math.floor(e.pageX-e.target.getX());
                var eventy = Math.floor(e.pageY-e.target.getY());

                var imagewidth = this.width;
                var imageheight = this.height;
                var factor = this.factor;
                var colour = [255,0,0];

                var matrices = [
                    [  0,  1,  0],
                    [ -1,  0,  0],
                    [  0,  0,  1],
                    [  0, -1,  0],
                    [  1,  0,  0],
                    [  0,  0, -1]
                ];

                var matrixcount = matrices.length;
                var limit = Math.round(imagewidth/matrixcount);
                var heightbreak = Math.round(imageheight/2);

                for (var x = 0; x < imagewidth; x++) {
                    var divisor = Math.floor(x / limit);
                    var matrix = matrices[divisor];

                    colour[0] += matrix[0]*factor;
                    colour[1] += matrix[1]*factor;
                    colour[2] += matrix[2]*factor;

                    if (eventx==x) {
                        break;
                    }
                }

                var pixel = [colour[0], colour[1], colour[2]];
                if (eventy < heightbreak) {
                    pixel[0] += Math.floor(((255-pixel[0])/heightbreak) * (heightbreak - eventy));
                    pixel[1] += Math.floor(((255-pixel[1])/heightbreak) * (heightbreak - eventy));
                    pixel[2] += Math.floor(((255-pixel[2])/heightbreak) * (heightbreak - eventy));
                } else if (eventy > heightbreak) {
                    pixel[0] = Math.floor((imageheight-eventy)*(pixel[0]/heightbreak));
                    pixel[1] = Math.floor((imageheight-eventy)*(pixel[1]/heightbreak));
                    pixel[2] = Math.floor((imageheight-eventy)*(pixel[2]/heightbreak));
                }

                return this.convert_rgb_to_hex(pixel);
            },
            /**
             * Converts an RGB value to Hex
             */
            convert_rgb_to_hex : function(rgb) {
                var hex = '#';
                var hexchars = "0123456789ABCDEF";
                for (var i=0; i<3; i++) {
                    var number = Math.abs(rgb[i]);
                    if (number == 0 || isNaN(number)) {
                        hex += '00';
                    } else {
                        hex += hexchars.charAt((number-number%16)/16)+hexchars.charAt(number%16);
                    }
                }
                return hex;
            }
        };
        /**
         * Initialise the colour picker :) Hoorah
         */
        colourpicker.init();
    });
};

M.util.init_block_hider = function(Y, config) {
    Y.use('base', 'node', function(Y) {
        M.util.block_hider = M.util.block_hider || (function(){
            var blockhider = function() {
                blockhider.superclass.constructor.apply(this, arguments);
            };
            blockhider.prototype = {
                initializer : function(config) {
                    this.set('block', '#'+this.get('id'));
                    var b = this.get('block'),
                        t = b.one('.title'),
                        a = null;
                    if (t && (a = t.one('.block_action'))) {
                        var hide = Y.Node.create('<img class="block-hider-hide" tabindex="0" alt="'+config.tooltipVisible+'" title="'+config.tooltipVisible+'" />');
                        hide.setAttribute('src', this.get('iconVisible')).on('click', this.updateState, this, true);
                        hide.on('keypress', this.updateStateKey, this, true);
                        var show = Y.Node.create('<img class="block-hider-show" tabindex="0" alt="'+config.tooltipHidden+'" title="'+config.tooltipHidden+'" />');
                        show.setAttribute('src', this.get('iconHidden')).on('click', this.updateState, this, false);
                        show.on('keypress', this.updateStateKey, this, false);
                        a.insert(show, 0).insert(hide, 0);
                    }
                },
                updateState : function(e, hide) {
                    M.util.set_user_preference(this.get('preference'), hide);
                    if (hide) {
                        this.get('block').addClass('hidden');
                    } else {
                        this.get('block').removeClass('hidden');
                    }
                },
                updateStateKey : function(e, hide) {
                    if (e.keyCode == 13) { //allow hide/show via enter key
                        this.updateState(this, hide);
                    }
                }
            };
            Y.extend(blockhider, Y.Base, blockhider.prototype, {
                NAME : 'blockhider',
                ATTRS : {
                    id : {},
                    preference : {},
                    iconVisible : {
                        value : M.util.image_url('t/switch_minus', 'moodle')
                    },
                    iconHidden : {
                        value : M.util.image_url('t/switch_plus', 'moodle')
                    },
                    block : {
                        setter : function(node) {
                            return Y.one(node);
                        }
                    }
                }
            });
            return blockhider;
        })();
        new M.util.block_hider(config);
    });
};

/**
 * Returns a string registered in advance for usage in JavaScript
 *
 * If you do not pass the third parameter, the function will just return
 * the corresponding value from the M.str object. If the third parameter is
 * provided, the function performs {$a} placeholder substitution in the
 * same way as PHP get_string() in Moodle does.
 *
 * @param {String} identifier string identifier
 * @param {String} component the component providing the string
 * @param {Object|String} a optional variable to populate placeholder with
 */
M.util.get_string = function(identifier, component, a) {
    var stringvalue;

    if (M.cfg.developerdebug) {
        // creating new instance if YUI is not optimal but it seems to be better way then
        // require the instance via the function API - note that it is used in rare cases
        // for debugging only anyway
        // To ensure we don't kill browser performance if hundreds of get_string requests
        // are made we cache the instance we generate within the M.util namespace.
        // We don't publicly define the variable so that it doesn't get abused.
        if (typeof M.util.get_string_yui_instance === 'undefined') {
            M.util.get_string_yui_instance = new YUI({ debug : true });
        }
        var Y = M.util.get_string_yui_instance;
    }

    if (!M.str.hasOwnProperty(component) || !M.str[component].hasOwnProperty(identifier)) {
        stringvalue = '[[' + identifier + ',' + component + ']]';
        if (M.cfg.developerdebug) {
            Y.log('undefined string ' + stringvalue, 'warn', 'M.util.get_string');
        }
        return stringvalue;
    }

    stringvalue = M.str[component][identifier];

    if (typeof a == 'undefined') {
        // no placeholder substitution requested
        return stringvalue;
    }

    if (typeof a == 'number' || typeof a == 'string') {
        // replace all occurrences of {$a} with the placeholder value
        stringvalue = stringvalue.replace(/\{\$a\}/g, a);
        return stringvalue;
    }

    if (typeof a == 'object') {
        // replace {$a->key} placeholders
        for (var key in a) {
            if (typeof a[key] != 'number' && typeof a[key] != 'string') {
                if (M.cfg.developerdebug) {
                    Y.log('invalid value type for $a->' + key, 'warn', 'M.util.get_string');
                }
                continue;
            }
            var search = '{$a->' + key + '}';
            search = search.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
            search = new RegExp(search, 'g');
            stringvalue = stringvalue.replace(search, a[key]);
        }
        return stringvalue;
    }

    if (M.cfg.developerdebug) {
        Y.log('incorrect placeholder type', 'warn', 'M.util.get_string');
    }
    return stringvalue;
};

/**
 * Set focus on username or password field of the login form
 */
M.util.focus_login_form = function(Y) {
    var username = Y.one('#username');
    var password = Y.one('#password');

    if (username == null || password == null) {
        // something is wrong here
        return;
    }

    var curElement = document.activeElement
    if (curElement == 'undefined') {
        // legacy browser - skip refocus protection
    } else if (curElement.tagName == 'INPUT') {
        // user was probably faster to focus something, do not mess with focus
        return;
    }

    if (username.get('value') == '') {
        username.focus();
    } else {
        password.focus();
    }
}

/**
 * Adds lightbox hidden element that covers the whole node.
 *
 * @param {YUI} Y
 * @param {Node} the node lightbox should be added to
 * @retun {Node} created lightbox node
 */
M.util.add_lightbox = function(Y, node) {
    var WAITICON = {'pix':"i/loading_small",'component':'moodle'};

    // Check if lightbox is already there
    if (node.one('.lightbox')) {
        return node.one('.lightbox');
    }

    node.setStyle('position', 'relative');
    var waiticon = Y.Node.create('<img />')
    .setAttrs({
        'src' : M.util.image_url(WAITICON.pix, WAITICON.component)
    })
    .setStyles({
        'position' : 'relative',
        'top' : '50%'
    });

    var lightbox = Y.Node.create('<div></div>')
    .setStyles({
        'opacity' : '.75',
        'position' : 'absolute',
        'width' : '100%',
        'height' : '100%',
        'top' : 0,
        'left' : 0,
        'backgroundColor' : 'white',
        'textAlign' : 'center'
    })
    .setAttribute('class', 'lightbox')
    .hide();

    lightbox.appendChild(waiticon);
    node.append(lightbox);
    return lightbox;
}

/**
 * Appends a hidden spinner element to the specified node.
 *
 * @param {YUI} Y
 * @param {Node} the node the spinner should be added to
 * @return {Node} created spinner node
 */
M.util.add_spinner = function(Y, node) {
    var WAITICON = {'pix':"i/loading_small",'component':'moodle'};

    // Check if spinner is already there
    if (node.one('.spinner')) {
        return node.one('.spinner');
    }

    var spinner = Y.Node.create('<img />')
        .setAttribute('src', M.util.image_url(WAITICON.pix, WAITICON.component))
        .addClass('spinner')
        .addClass('iconsmall')
        .hide();

    node.append(spinner);
    return spinner;
}

//=== old legacy JS code, hopefully to be replaced soon by M.xx.yy and YUI3 code ===

function checkall() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            if (inputs[i].disabled || inputs[i].readOnly) {
                continue;
            }
            inputs[i].checked = true;
        }
    }
}

function checknone() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            if (inputs[i].disabled || inputs[i].readOnly) {
                continue;
            }
            inputs[i].checked = false;
        }
    }
}

/**
 * Either check, or uncheck, all checkboxes inside the element with id is
 * @param id the id of the container
 * @param checked the new state, either '' or 'checked'.
 */
function select_all_in_element_with_id(id, checked) {
    var container = document.getElementById(id);
    if (!container) {
        return;
    }
    var inputs = container.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; ++i) {
        if (inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
            inputs[i].checked = checked;
        }
    }
}

function select_all_in(elTagName, elClass, elId) {
    var inputs = document.getElementsByTagName('input');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elClass, elId);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
            inputs[i].checked = 'checked';
        }
    }
}

function deselect_all_in(elTagName, elClass, elId) {
    var inputs = document.getElementsByTagName('INPUT');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elClass, elId);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
            inputs[i].checked = '';
        }
    }
}

function confirm_if(expr, message) {
    if(!expr) {
        return true;
    }
    return confirm(message);
}


/*
    findParentNode (start, elementName, elementClass, elementID)

    Travels up the DOM hierarchy to find a parent element with the
    specified tag name, class, and id. All conditions must be met,
    but any can be ommitted. Returns the BODY element if no match
    found.
*/
function findParentNode(el, elName, elClass, elId) {
    while (el.nodeName.toUpperCase() != 'BODY') {
        if ((!elName || el.nodeName.toUpperCase() == elName) &&
            (!elClass || el.className.indexOf(elClass) != -1) &&
            (!elId || el.id == elId)) {
            break;
        }
        el = el.parentNode;
    }
    return el;
}
/*
    findChildNode (start, elementName, elementClass, elementID)

    Travels down the DOM hierarchy to find all child elements with the
    specified tag name, class, and id. All conditions must be met,
    but any can be ommitted.
    Doesn't examine children of matches.
*/
function findChildNodes(start, tagName, elementClass, elementID, elementName) {
    var children = new Array();
    for (var i = 0; i < start.childNodes.length; i++) {
        var classfound = false;
        var child = start.childNodes[i];
        if((child.nodeType == 1) &&//element node type
                  (elementClass && (typeof(child.className)=='string'))) {
            var childClasses = child.className.split(/\s+/);
            for (var childClassIndex in childClasses) {
                if (childClasses[childClassIndex]==elementClass) {
                    classfound = true;
                    break;
                }
            }
        }
        if(child.nodeType == 1) { //element node type
            if  ( (!tagName || child.nodeName == tagName) &&
                (!elementClass || classfound)&&
                (!elementID || child.id == elementID) &&
                (!elementName || child.name == elementName))
            {
                children = children.concat(child);
            } else {
                children = children.concat(findChildNodes(child, tagName, elementClass, elementID, elementName));
            }
        }
    }
    return children;
}

function unmaskPassword(id) {
  var pw = document.getElementById(id);
  var chb = document.getElementById(id+'unmask');

  try {
    // first try IE way - it can not set name attribute later
    if (chb.checked) {
      var newpw = document.createElement('<input type="text" autocomplete="off" name="'+pw.name+'">');
    } else {
      var newpw = document.createElement('<input type="password" autocomplete="off" name="'+pw.name+'">');
    }
    newpw.attributes['class'].nodeValue = pw.attributes['class'].nodeValue;
  } catch (e) {
    var newpw = document.createElement('input');
    newpw.setAttribute('autocomplete', 'off');
    newpw.setAttribute('name', pw.name);
    if (chb.checked) {
      newpw.setAttribute('type', 'text');
    } else {
      newpw.setAttribute('type', 'password');
    }
    newpw.setAttribute('class', pw.getAttribute('class'));
  }
  newpw.id = pw.id;
  newpw.size = pw.size;
  newpw.onblur = pw.onblur;
  newpw.onchange = pw.onchange;
  newpw.value = pw.value;
  pw.parentNode.replaceChild(newpw, pw);
}

function filterByParent(elCollection, parentFinder) {
    var filteredCollection = [];
    for (var i = 0; i < elCollection.length; ++i) {
        var findParent = parentFinder(elCollection[i]);
        if (findParent.nodeName.toUpperCase() != 'BODY') {
            filteredCollection.push(elCollection[i]);
        }
    }
    return filteredCollection;
}

/*
    All this is here just so that IE gets to handle oversized blocks
    in a visually pleasing manner. It does a browser detect. So sue me.
*/

function fix_column_widths() {
    var agt = navigator.userAgent.toLowerCase();
    if ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1)) {
        fix_column_width('left-column');
        fix_column_width('right-column');
    }
}

function fix_column_width(colName) {
    if(column = document.getElementById(colName)) {
        if(!column.offsetWidth) {
            setTimeout("fix_column_width('" + colName + "')", 20);
            return;
        }

        var width = 0;
        var nodes = column.childNodes;

        for(i = 0; i < nodes.length; ++i) {
            if(nodes[i].className.indexOf("block") != -1 ) {
                if(width < nodes[i].offsetWidth) {
                    width = nodes[i].offsetWidth;
                }
            }
        }

        for(i = 0; i < nodes.length; ++i) {
            if(nodes[i].className.indexOf("block") != -1 ) {
                nodes[i].style.width = width + 'px';
            }
        }
    }
}


/*
   Insert myValue at current cursor position
 */
function insertAtCursor(myField, myValue) {
    // IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    // Mozilla/Netscape support
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}


/*
        Call instead of setting window.onload directly or setting body onload=.
        Adds your function to a chain of functions rather than overwriting anything
        that exists.
*/
function addonload(fn) {
    var oldhandler=window.onload;
    window.onload=function() {
        if(oldhandler) oldhandler();
            fn();
    }
}
/**
 * Replacement for getElementsByClassName in browsers that aren't cool enough
 *
 * Relying on the built-in getElementsByClassName is far, far faster than
 * using YUI.
 *
 * Note: the third argument used to be an object with odd behaviour. It now
 * acts like the 'name' in the HTML5 spec, though the old behaviour is still
 * mimicked if you pass an object.
 *
 * @param {Node} oElm The top-level node for searching. To search a whole
 *                    document, use `document`.
 * @param {String} strTagName filter by tag names
 * @param {String} name same as HTML5 spec
 */
function getElementsByClassName(oElm, strTagName, name) {
    // for backwards compatibility
    if(typeof name == "object") {
        var names = new Array();
        for(var i=0; i<name.length; i++) names.push(names[i]);
        name = names.join('');
    }
    // use native implementation if possible
    if (oElm.getElementsByClassName && Array.filter) {
        if (strTagName == '*') {
            return oElm.getElementsByClassName(name);
        } else {
            return Array.filter(oElm.getElementsByClassName(name), function(el) {
                return el.nodeName.toLowerCase() == strTagName.toLowerCase();
            });
        }
    }
    // native implementation unavailable, fall back to slow method
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var arrRegExpClassNames = new Array();
    var names = name.split(' ');
    for(var i=0; i<names.length; i++) {
        arrRegExpClassNames.push(new RegExp("(^|\\s)" + names[i].replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<arrElements.length; j++) {
        oElement = arrElements[j];
        bMatchesAll = true;
        for(var k=0; k<arrRegExpClassNames.length; k++) {
            if(!arrRegExpClassNames[k].test(oElement.className)) {
                bMatchesAll = false;
                break;
            }
        }
        if(bMatchesAll) {
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}

/**
 * Return whether we are in right to left mode or not.
 *
 * @return boolean
 */
function right_to_left() {
    var body = Y.one('body');
    var rtl = false;
    if (body && body.hasClass('dir-rtl')) {
        rtl = true;
    }
    return rtl;
}

function openpopup(event, args) {

    if (event) {
        if (event.preventDefault) {
            event.preventDefault();
        } else {
            event.returnValue = false;
        }
    }

    // Make sure the name argument is set and valid.
    var nameregex = /[^a-z0-9_]/i;
    if (typeof args.name !== 'string') {
        args.name = '_blank';
    } else if (args.name.match(nameregex)) {
        // Cleans window name because IE does not support funky ones.
        args.name = args.name.replace(nameregex, '_');
        if (M.cfg.developerdebug) {
            alert('DEVELOPER NOTICE: Invalid \'name\' passed to openpopup()');
        }
    }

    var fullurl = args.url;
    if (!args.url.match(/https?:\/\//)) {
        fullurl = M.cfg.wwwroot + args.url;
    }
    if (args.fullscreen) {
        args.options = args.options.
                replace(/top=\d+/, 'top=0').
                replace(/left=\d+/, 'left=0').
                replace(/width=\d+/, 'width=' + screen.availWidth).
                replace(/height=\d+/, 'height=' + screen.availHeight);
    }
    var windowobj = window.open(fullurl,args.name,args.options);
    if (!windowobj) {
        return true;
    }

    if (args.fullscreen) {
        // In some browser / OS combinations (E.g. Chrome on Windows), the
        // window initially opens slighly too big. The width and heigh options
        // seem to control the area inside the browser window, so what with
        // scroll-bars, etc. the actual window is bigger than the screen.
        // Therefore, we need to fix things up after the window is open.
        var hackcount = 100;
        var get_size_exactly_right = function() {
            windowobj.moveTo(0, 0);
            windowobj.resizeTo(screen.availWidth, screen.availHeight);

            // Unfortunately, it seems that in Chrome on Ubuntu, if you call
            // something like windowobj.resizeTo(1280, 1024) too soon (up to
            // about 50ms) after the window is open, then it actually behaves
            // as if you called windowobj.resizeTo(0, 0). Therefore, we need to
            // check that the resize actually worked, and if not, repeatedly try
            // again after a short delay until it works (but with a limit of
            // hackcount repeats.
            if (hackcount > 0 && (windowobj.innerHeight < 10 || windowobj.innerWidth < 10)) {
                hackcount -= 1;
                setTimeout(get_size_exactly_right, 10);
            }
        }
        setTimeout(get_size_exactly_right, 0);
    }
    windowobj.focus();

    return false;
}

/** Close the current browser window. */
function close_window(e) {
    if (e.preventDefault) {
        e.preventDefault();
    } else {
        e.returnValue = false;
    }
    window.close();
}

/**
 * Used in a couple of modules to hide navigation areas when using AJAX
 */

function show_item(itemid) {
    var item = document.getElementById(itemid);
    if (item) {
        item.style.display = "";
    }
}

function destroy_item(itemid) {
    var item = document.getElementById(itemid);
    if (item) {
        item.parentNode.removeChild(item);
    }
}
/**
 * Tranfer keyboard focus to the HTML element with the given id, if it exists.
 * @param controlid the control id.
 */
function focuscontrol(controlid) {
    var control = document.getElementById(controlid);
    if (control) {
        control.focus();
    }
}

/**
 * Transfers keyboard focus to an HTML element based on the old style style of focus
 * This function should be removed as soon as it is no longer used
 */
function old_onload_focus(formid, controlname) {
    if (document.forms[formid] && document.forms[formid].elements && document.forms[formid].elements[controlname]) {
        document.forms[formid].elements[controlname].focus();
    }
}

function build_querystring(obj) {
    return convert_object_to_string(obj, '&');
}

function build_windowoptionsstring(obj) {
    return convert_object_to_string(obj, ',');
}

function convert_object_to_string(obj, separator) {
    if (typeof obj !== 'object') {
        return null;
    }
    var list = [];
    for(var k in obj) {
        k = encodeURIComponent(k);
        var value = obj[k];
        if(obj[k] instanceof Array) {
            for(var i in value) {
                list.push(k+'[]='+encodeURIComponent(value[i]));
            }
        } else {
            list.push(k+'='+encodeURIComponent(value));
        }
    }
    return list.join(separator);
}

function stripHTML(str) {
    var re = /<\S[^><]*>/g;
    var ret = str.replace(re, "");
    return ret;
}

Number.prototype.fixed=function(n){
    with(Math)
        return round(Number(this)*pow(10,n))/pow(10,n);
};
function update_progress_bar (id, width, pt, msg, es){
    var percent = pt;
    var status = document.getElementById("status_"+id);
    var percent_indicator = document.getElementById("pt_"+id);
    var progress_bar = document.getElementById("progress_"+id);
    var time_es = document.getElementById("time_"+id);
    status.innerHTML = msg;
    percent_indicator.innerHTML = percent.fixed(2) + '%';
    if(percent == 100) {
        progress_bar.style.background = "green";
        time_es.style.display = "none";
    } else {
        progress_bar.style.background = "#FFCC66";
        if (es == '?'){
            time_es.innerHTML = "";
        }else {
            time_es.innerHTML = es.fixed(2)+" sec";
            time_es.style.display
                = "block";
        }
    }
    progress_bar.style.width = width + "px";

}


// ===== Deprecated core Javascript functions for Moodle ====
//       DO NOT USE!!!!!!!
// Do not put this stuff in separate file because it only adds extra load on servers!

/**
 * Used in a couple of modules to hide navigation areas when using AJAX
 */
function hide_item(itemid) {
    // use class='hiddenifjs' instead
    var item = document.getElementById(itemid);
    if (item) {
        item.style.display = "none";
    }
}

M.util.help_popups = {
    setup : function(Y) {
        Y.one('body').delegate('click', this.open_popup, 'a.helplinkpopup', this);
    },
    open_popup : function(e) {
        // Prevent the default page action
        e.preventDefault();

        // Grab the anchor that was clicked
        var anchor = e.target.ancestor('a', true);
        var args = {
            'name'          : 'popup',
            'url'           : anchor.getAttribute('href'),
            'options'       : ''
        };
        var options = [
            'height=600',
            'width=800',
            'top=0',
            'left=0',
            'menubar=0',
            'location=0',
            'scrollbars',
            'resizable',
            'toolbar',
            'status',
            'directories=0',
            'fullscreen=0',
            'dependent'
        ]
        args.options = options.join(',');

        openpopup(e, args);
    }
}

M.util.help_icon = {
    Y : null,
    instance : null,
    initialised : false,
    setup : function(Y) {
        if (this.initialised) {
            // Exit early if we have already completed setup
            return;
        }
        this.Y = Y;
        Y.one('body').delegate('click', this.display, 'span.helplink a.tooltip', this);
        this.initialised = true;
    },
    add : function(Y, properties) {
        this.setup(Y);
    },
    display : function(event) {
        event.preventDefault();
        if (M.util.help_icon.instance === null) {
            var Y = M.util.help_icon.Y;
            Y.use('overlay', 'io-base', 'event-mouseenter', 'node', 'event-key', 'escape', function(Y) {
                var help_content_overlay = {
                    helplink : null,
                    overlay : null,
                    init : function() {

                        var strclose = Y.Escape.html(M.str.form.close);
                        var footerbtn = Y.Node.create('<button class="closebtn">'+strclose+'</button>');
                        // Create an overlay from markup
                        this.overlay = new Y.Overlay({
                            footerContent: footerbtn,
                            bodyContent: '',
                            id: 'helppopupbox',
                            width:'400px',
                            visible : false,
                            constrain : true
                        });
                        this.overlay.render(Y.one(document.body));

                        footerbtn.on('click', this.overlay.hide, this.overlay);

                        var boundingBox = this.overlay.get("boundingBox");

                        //  Hide the menu if the user clicks outside of its content
                        boundingBox.get("ownerDocument").on("mousedown", function (event) {
                            var oTarget = event.target;
                            var menuButton = this.helplink;

                            if (!oTarget.compareTo(menuButton) &&
                                !menuButton.contains(oTarget) &&
                                !oTarget.compareTo(boundingBox) &&
                                !boundingBox.contains(oTarget)) {
                                this.overlay.hide();
                            }
                        }, this);
                    },

                    close : function(e) {
                        e.preventDefault();
                        this.helplink.focus();
                        this.overlay.hide();
                    },

                    display : function(event) {
                        var overlayPosition;
                        this.helplink = event.target.ancestor('span.helplink a', true);
                        if (Y.one('html').get('dir') === 'rtl') {
                            overlayPosition = [Y.WidgetPositionAlign.TR, Y.WidgetPositionAlign.LC];
                        } else {
                            overlayPosition = [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.RC];
                        }

                        this.overlay.set('bodyContent', Y.Node.create('<img src="'+M.cfg.loadingicon+'" class="spinner" />'));
                        this.overlay.set("align", {node:this.helplink, points: overlayPosition});

                        var cfg = {
                            method: 'get',
                            context : this,
                            data : {
                                ajax : 1
                            },
                            on: {
                                success: function(id, o, node) {
                                    this.display_callback(o.responseText);
                                },
                                failure: function(id, o, node) {
                                    var debuginfo = o.statusText;
                                    if (M.cfg.developerdebug) {
                                        o.statusText += ' (' + ajaxurl + ')';
                                    }
                                    this.display_callback('bodyContent',debuginfo);
                                }
                            }
                        };

                        Y.io(this.helplink.get('href'), cfg);
                        this.overlay.show();
                    },

                    display_callback : function(content) {
                        content = '<div role="alert">' + content + '</div>';
                        this.overlay.set('bodyContent', content);
                    },

                    hideContent : function() {
                        help = this;
                        help.overlay.hide();
                    }
                };
                help_content_overlay.init();
                M.util.help_icon.instance = help_content_overlay;
                M.util.help_icon.instance.display(event);
            });
        } else {
            M.util.help_icon.instance.display(event);
        }
    },
    init : function(Y) {
        this.Y = Y;
    }
};

/**
 * Custom menu namespace
 */
M.core_custom_menu = {
    /**
     * This method is used to initialise a custom menu given the id that belongs
     * to the custom menu's root node.
     *
     * @param {YUI} Y
     * @param {string} nodeid
     */
    init : function(Y, nodeid) {
        var node = Y.one('#'+nodeid);
        if (node) {
            Y.use('node-menunav', function(Y) {
                // Get the node
                // Remove the javascript-disabled class.... obviously javascript is enabled.
                node.removeClass('javascript-disabled');
                // Initialise the menunav plugin
                node.plug(Y.Plugin.NodeMenuNav);
            });
        }
    }
};

/**
 * Used to store form manipulation methods and enhancments
 */
M.form = M.form || {};

/**
 * Converts a nbsp indented select box into a multi drop down custom control much
 * like the custom menu. It also selectable categories on or off.
 *
 * $form->init_javascript_enhancement('elementname','smartselect', array('selectablecategories'=>true|false, 'mode'=>'compact'|'spanning'));
 *
 * @param {YUI} Y
 * @param {string} id
 * @param {Array} options
 */
M.form.init_smartselect = function(Y, id, options) {
    if (!id.match(/^id_/)) {
        id = 'id_'+id;
    }
    var select = Y.one('select#'+id);
    if (!select) {
        return false;
    }
    Y.use('event-delegate',function(){
        var smartselect = {
            id : id,
            structure : [],
            options : [],
            submenucount : 0,
            currentvalue : null,
            currenttext : null,
            shownevent : null,
            cfg : {
                selectablecategories : true,
                mode : null
            },
            nodes : {
                select : null,
                loading : null,
                menu : null
            },
            init : function(Y, id, args, nodes) {
                if (typeof(args)=='object') {
                    for (var i in this.cfg) {
                        if (args[i] || args[i]===false) {
                            this.cfg[i] = args[i];
                        }
                    }
                }

                // Display a loading message first up
                this.nodes.select = nodes.select;

                this.currentvalue = this.nodes.select.get('selectedIndex');
                this.currenttext = this.nodes.select.all('option').item(this.currentvalue).get('innerHTML');

                var options = Array();
                options[''] = {text:this.currenttext,value:'',depth:0,children:[]};
                this.nodes.select.all('option').each(function(option, index) {
                    var rawtext = option.get('innerHTML');
                    var text = rawtext.replace(/^(&nbsp;)*/, '');
                    if (rawtext === text) {
                        text = rawtext.replace(/^(\s)*/, '');
                        var depth = (rawtext.length - text.length ) + 1;
                    } else {
                        var depth = ((rawtext.length - text.length )/12)+1;
                    }
                    option.set('innerHTML', text);
                    options['i'+index] = {text:text,depth:depth,index:index,children:[]};
                }, this);

                this.structure = [];
                var structcount = 0;
                for (var i in options) {
                    var o = options[i];
                    if (o.depth == 0) {
                        this.structure.push(o);
                        structcount++;
                    } else {
                        var d = o.depth;
                        var current = this.structure[structcount-1];
                        for (var j = 0; j < o.depth-1;j++) {
                            if (current && current.children) {
                                current = current.children[current.children.length-1];
                            }
                        }
                        if (current && current.children) {
                            current.children.push(o);
                        }
                    }
                }

                this.nodes.menu = Y.Node.create(this.generate_menu_content());
                this.nodes.menu.one('.smartselect_mask').setStyle('opacity', 0.01);
                this.nodes.menu.one('.smartselect_mask').setStyle('width', (this.nodes.select.get('offsetWidth')+5)+'px');
                this.nodes.menu.one('.smartselect_mask').setStyle('height', (this.nodes.select.get('offsetHeight'))+'px');

                if (this.cfg.mode == null) {
                    var formwidth = this.nodes.select.ancestor('form').get('offsetWidth');
                    if (formwidth < 400 || this.nodes.menu.get('offsetWidth') < formwidth*2) {
                        this.cfg.mode = 'compact';
                    } else {
                        this.cfg.mode = 'spanning';
                    }
                }

                if (this.cfg.mode == 'compact') {
                    this.nodes.menu.addClass('compactmenu');
                } else {
                    this.nodes.menu.addClass('spanningmenu');
                    this.nodes.menu.delegate('mouseover', this.show_sub_menu, '.smartselect_submenuitem', this);
                }

                Y.one(document.body).append(this.nodes.menu);
                var pos = this.nodes.select.getXY();
                pos[0] += 1;
                this.nodes.menu.setXY(pos);
                this.nodes.menu.on('click', this.handle_click, this);

                Y.one(window).on('resize', function(){
                     var pos = this.nodes.select.getXY();
                    pos[0] += 1;
                    this.nodes.menu.setXY(pos);
                 }, this);
            },
            generate_menu_content : function() {
                var content = '<div id="'+this.id+'_smart_select" class="smartselect">';
                content += this.generate_submenu_content(this.structure[0], true);
                content += '</ul></div>';
                return content;
            },
            generate_submenu_content : function(item, rootelement) {
                this.submenucount++;
                var content = '';
                if (item.children.length > 0) {
                    if (rootelement) {
                        content += '<div class="smartselect_mask" href="#ss_submenu'+this.submenucount+'">&nbsp;</div>';
                        content += '<div id="ss_submenu'+this.submenucount+'" class="smartselect_menu">';
                        content += '<div class="smartselect_menu_content">';
                    } else {
                        content += '<li class="smartselect_submenuitem">';
                        var categoryclass = (this.cfg.selectablecategories)?'selectable':'notselectable';
                        content += '<a class="smartselect_menuitem_label '+categoryclass+'" href="#ss_submenu'+this.submenucount+'" value="'+item.index+'">'+item.text+'</a>';
                        content += '<div id="ss_submenu'+this.submenucount+'" class="smartselect_submenu">';
                        content += '<div class="smartselect_submenu_content">';
                    }
                    content += '<ul>';
                    for (var i in item.children) {
                        content += this.generate_submenu_content(item.children[i],false);
                    }
                    content += '</ul>';
                    content += '</div>';
                    content += '</div>';
                    if (rootelement) {
                    } else {
                        content += '</li>';
                    }
                } else {
                    content += '<li class="smartselect_menuitem">';
                    content += '<a class="smartselect_menuitem_content selectable" href="#" value="'+item.index+'">'+item.text+'</a>';
                    content += '</li>';
                }
                return content;
            },
            select : function(e) {
                var t = e.target;
                e.halt();
                this.currenttext = t.get('innerHTML');
                this.currentvalue = t.getAttribute('value');
                this.nodes.select.set('selectedIndex', this.currentvalue);
                this.hide_menu();
            },
            handle_click : function(e) {
                var target = e.target;
                if (target.hasClass('smartselect_mask')) {
                    this.show_menu(e);
                } else if (target.hasClass('selectable') || target.hasClass('smartselect_menuitem')) {
                    this.select(e);
                } else if (target.hasClass('smartselect_menuitem_label') || target.hasClass('smartselect_submenuitem')) {
                    this.show_sub_menu(e);
                }
            },
            show_menu : function(e) {
                e.halt();
                var menu = e.target.ancestor().one('.smartselect_menu');
                menu.addClass('visible');
                this.shownevent = Y.one(document.body).on('click', this.hide_menu, this);
            },
            show_sub_menu : function(e) {
                e.halt();
                var target = e.target;
                if (!target.hasClass('smartselect_submenuitem')) {
                    target = target.ancestor('.smartselect_submenuitem');
                }
                if (this.cfg.mode == 'compact' && target.one('.smartselect_submenu').hasClass('visible')) {
                    target.ancestor('ul').all('.smartselect_submenu.visible').removeClass('visible');
                    return;
                }
                target.ancestor('ul').all('.smartselect_submenu.visible').removeClass('visible');
                target.one('.smartselect_submenu').addClass('visible');
            },
            hide_menu : function() {
                this.nodes.menu.all('.visible').removeClass('visible');
                if (this.shownevent) {
                    this.shownevent.detach();
                }
            }
        };
        smartselect.init(Y, id, options, {select:select});
    });
};

/** List of flv players to be loaded */
M.util.video_players = [];
/** List of mp3 players to be loaded */
M.util.audio_players = [];

/**
 * Add video player
 * @param id element id
 * @param fileurl media url
 * @param width
 * @param height
 * @param autosize true means detect size from media
 */
M.util.add_video_player = function (id, fileurl, width, height, autosize) {
    M.util.video_players.push({id: id, fileurl: fileurl, width: width, height: height, autosize: autosize, resized: false});
};

/**
 * Add audio player.
 * @param id
 * @param fileurl
 * @param small
 */
M.util.add_audio_player = function (id, fileurl, small) {
    M.util.audio_players.push({id: id, fileurl: fileurl, small: small});
};

/**
 * Initialise all audio and video player, must be called from page footer.
 */
M.util.load_flowplayer = function() {
    if (M.util.video_players.length == 0 && M.util.audio_players.length == 0) {
        return;
    }
    if (typeof(flowplayer) == 'undefined') {
        var loaded = false;

        var embed_function = function() {
            if (loaded || typeof(flowplayer) == 'undefined') {
                return;
            }
            loaded = true;

            var controls = {
                    autoHide: true
            }
            /* TODO: add CSS color overrides for the flv flow player */

            for(var i=0; i<M.util.video_players.length; i++) {
                var video = M.util.video_players[i];
                if (video.width > 0 && video.height > 0) {
                    var src = {src: M.cfg.wwwroot + '/lib/flowplayer/flowplayer-3.2.14.swf', width: video.width, height: video.height};
                } else {
                    var src = M.cfg.wwwroot + '/lib/flowplayer/flowplayer-3.2.14.swf';
                }
                flowplayer(video.id, src, {
                    plugins: {controls: controls},
                    clip: {
                        url: video.fileurl, autoPlay: false, autoBuffering: true, scaling: 'fit', mvideo: video,
                        onMetaData: function(clip) {
                            if (clip.mvideo.autosize && !clip.mvideo.resized) {
                                clip.mvideo.resized = true;
                                //alert("metadata!!! "+clip.width+' '+clip.height+' '+JSON.stringify(clip.metaData));
                                if (typeof(clip.metaData.width) == 'undefined' || typeof(clip.metaData.height) == 'undefined') {
                                    // bad luck, we have to guess - we may not get metadata at all
                                    var width = clip.width;
                                    var height = clip.height;
                                } else {
                                    var width = clip.metaData.width;
                                    var height = clip.metaData.height;
                                }
                                var minwidth = 300; // controls are messed up in smaller objects
                                if (width < minwidth) {
                                    height = (height * minwidth) / width;
                                    width = minwidth;
                                }

                                var object = this._api();
                                object.width = width;
                                object.height = height;
                            }
                        }
                    }
                });
            }
            if (M.util.audio_players.length == 0) {
                return;
            }
            var controls = {
                    autoHide: false,
                    fullscreen: false,
                    next: false,
                    previous: false,
                    scrubber: true,
                    play: true,
                    pause: true,
                    volume: true,
                    mute: false,
                    backgroundGradient: [0.5,0,0.3]
                };

            var rule;
            for (var j=0; j < document.styleSheets.length; j++) {

                // To avoid javascript security violation accessing cross domain stylesheets
                var allrules = false;
                try {
                    if (typeof (document.styleSheets[j].rules) != 'undefined') {
                        allrules = document.styleSheets[j].rules;
                    } else if (typeof (document.styleSheets[j].cssRules) != 'undefined') {
                        allrules = document.styleSheets[j].cssRules;
                    } else {
                        // why??
                        continue;
                    }
                } catch (e) {
                    continue;
                }

                // On cross domain style sheets Chrome V8 allows access to rules but returns null
                if (!allrules) {
                    continue;
                }

                for(var i=0; i<allrules.length; i++) {
                    rule = '';
                    if (/^\.mp3flowplayer_.*Color$/.test(allrules[i].selectorText)) {
                        if (typeof(allrules[i].cssText) != 'undefined') {
                            rule = allrules[i].cssText;
                        } else if (typeof(allrules[i].style.cssText) != 'undefined') {
                            rule = allrules[i].style.cssText;
                        }
                        if (rule != '' && /.*color\s*:\s*([^;]+).*/gi.test(rule)) {
                            rule = rule.replace(/.*color\s*:\s*([^;]+).*/gi, '$1');
                            var colprop = allrules[i].selectorText.replace(/^\.mp3flowplayer_/, '');
                            controls[colprop] = rule;
                        }
                    }
                }
                allrules = false;
            }

            for(i=0; i<M.util.audio_players.length; i++) {
                var audio = M.util.audio_players[i];
                if (audio.small) {
                    controls.controlall = false;
                    controls.height = 15;
                    controls.time = false;
                } else {
                    controls.controlall = true;
                    controls.height = 25;
                    controls.time = true;
                }
                flowplayer(audio.id, M.cfg.wwwroot + '/lib/flowplayer/flowplayer-3.2.14.swf', {
                    plugins: {controls: controls, audio: {url: M.cfg.wwwroot + '/lib/flowplayer/flowplayer.audio-3.2.10.swf'}},
                    clip: {url: audio.fileurl, provider: "audio", autoPlay: false}
                });
            }
        }

        if (M.cfg.jsrev == -1) {
            var jsurl = M.cfg.wwwroot + '/lib/flowplayer/flowplayer-3.2.11.js';
        } else {
            var jsurl = M.cfg.wwwroot + '/lib/javascript.php?jsfile=/lib/flowplayer/flowplayer-3.2.11.min.js&rev=' + M.cfg.jsrev;
        }
        var fileref = document.createElement('script');
        fileref.setAttribute('type','text/javascript');
        fileref.setAttribute('src', jsurl);
        fileref.onload = embed_function;
        fileref.onreadystatechange = embed_function;
        document.getElementsByTagName('head')[0].appendChild(fileref);
    }
};
