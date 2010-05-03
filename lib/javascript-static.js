// Miscellaneous core Javascript functions for Moodle
// Global M object is initilised in inline javascript

/**
 * Add module to list of available modules that can be laoded from YUI.
 * @param {Array} modules
 */
M.yui.add_module = function(modules) {
    for (var modname in modules) {
        M.yui.loader.modules[modname] = modules[modname];
    }
};


/**
 * Various utility functions
 */
M.util = {};

/**
 * Language strings - initialised from page footer.
 */
M.str = {};

/**
 * Returns url for images.
 * @param {String} imagename
 * @param {String} component
 * @return {String}
 */
M.util.image_url = function(imagename, component) {
    var url = M.cfg.wwwroot + '/theme/image.php?theme=' + M.cfg.theme + '&image=' + imagename;

    if (M.cfg.themerev > 0) {
        url = url + '&rev=' + M.cfg.themerev;
    }

    if (component && component != '' && component != 'moodle' && component != 'core') {
        url = url + '&component=' + component;
    }

    return url;
};

M.util.create_UFO_object = function (eid, FO) {
    UFO.create(FO, eid);
}

M.util.in_array = function(item, array){
    for( var i = 0; i<array.length; i++){
        if(item==array[i]){
            return true;
        }
    }
    return false;
}

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
    caption.setAttribute('title', strtooltip);

    // Create a link
    var a = Y.Node.create('<a href="#"></a>');
    // Create a local scoped lamba function to move nodes to a new link
    var movenode = function(node){
        node.remove();
        a.append(node);
    };
    // Apply the lamba function on each of the captions child nodes
    caption.get('children').each(movenode, this);
    caption.append(a);

    // Get the height of the div at this point before we shrink it if required
    var height = this.div.get('offsetHeight');
    if (this.div.hasClass('collapsed')) {
        // Add the correct image and record the YUI node created in the process
        this.icon = Y.Node.create('<img src="'+M.util.image_url('t/collapsed', 'moodle')+'" alt="" />');
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
        if (this.div.hasClass('collapsed')) {
            this.icon.set('src', M.util.image_url('t/collapsed', 'moodle'));
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
 * Finds all help icons on the page and initiates YUI tooltips for
 * each of them, which load a truncated version of the help's content
 * on-the-fly asynchronously
 */
M.util.init_help_icons = function(Y) {
    Y.use('io', 'yui2-dom', 'yui2-container', function(Y) {
        // remove all titles, they would obscure the YUI tooltip
        Y.all('span.helplink a').each(function(node) {
            node.set('title', '');
        });

        var iconspans = YAHOO.util.Dom.getElementsByClassName('helplink', 'span');
        var tooltip = new YAHOO.widget.Tooltip('help_icon_tooltip', {
            context: iconspans,
            showdelay: 1000,
            hidedelay: 150,
            autodismissdelay: 50000,
            underlay: 'none',
            zIndex: '1000'
        });

        tooltip.contextTriggerEvent.subscribe(
            function(type, args) {
                // Fetch help page contents asynchronously
                // Load spinner icon while content is loading
                var spinner = document.createElement('img');
                spinner.src = M.cfg.loadingicon;

                this.cfg.setProperty('text', spinner);

                var context = args[0];

                var link = context.getElementsByTagName('a')[0];
                var thistooltip = this;
                var ajaxurl = link.href + '&ajax=1';


                var cfg = {
                    method: 'get',
                    on: {
                        success: function(id, o, args) {
                            thistooltip.cfg.setProperty('text', o.responseText);
                        },
                        failure: function(id, o, args) {
                            var debuginfo = o.statusText;
                            if (M.cfg.developerdebug) {
                                o.statusText += ' (' + ajaxurl + ')';
                            }
                            thistooltip.cfg.setProperty('text', debuginfo);
                        }
                    }
                };

                var conn = Y.io(ajaxurl, cfg);
            }
        );
    });
};


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
    YUI(M.yui.loader).use('io', function(Y) {
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

    YUI(M.yui.loader).use('yui2-container', 'yui2-event', function(Y) {
        var simpledialog = new YAHOO.widget.SimpleDialog('confirmdialog',
            { width: '300px',
              fixedcenter: true,
              modal: true,
              visible: false,
              draggable: false
            }
        );

        simpledialog.setHeader(M.str.admin.confirmation);
        simpledialog.setBody(args.message);
        simpledialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);

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

            if (target.get('tagName').toLowerCase() == 'a') {
                window.location = target.get('href');
            } else if (target.get('tagName').toLowerCase() == 'input') {
                var parentelement = target.get('parentNode');
                while (parentelement.get('tagName').toLowerCase() != 'form' && parentelement.get('tagName').toLowerCase() != 'body') {
                    parentelement = parentelement.get('parentNode');
                }
                if (parentelement.get('tagName').toLowerCase() == 'form') {
                    parentelement.submit();
                }
            } else if (M.cfg.developerdebug) {
                alert("Element of type " + target.get('tagName') + " is not supported by the M.util.show_confirm_dialog function. Use A or INPUT");
            }
        };

        var buttons = [ { text: M.str.moodle.cancel, handler: handle_cancel, isDefault: true },
                        { text: M.str.moodle.yes, handler: handle_yes } ];

        simpledialog.cfg.queueProperty('buttons', buttons);

        simpledialog.render(document.body);
        simpledialog.show();
    });
}

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
        var val = el.getStyle(prop);
        if (val == 'auto') {
            val = el.getComputedStyle(prop);
        }
        return parseInt(val);
    };

    var resize_object = function() {
        obj.setStyle('width', '0px');
        obj.setStyle('height', '0px');
        var newwidth = get_htmlelement_size('content', 'width') - 15;

        if (newwidth > 600) {
            obj.setStyle('width', newwidth  + 'px');
        } else {
            obj.setStyle('width', '600px');
        }
        var pageheight = get_htmlelement_size('page', 'height');
        var objheight = get_htmlelement_size(obj, 'height');
        var newheight = objheight + parseInt(obj.get('winHeight')) - pageheight - 30;
        if (newheight > 400) {
            obj.setStyle('height', newheight + 'px');
        } else {
            obj.setStyle('height', '400px');
        }
    };

    resize_object();
    // fix layout if window resized too
    window.onresize = function() {
        resize_object();
    };
};

/**
 * Attach handler to single_select
 */
M.util.init_select_autosubmit = function(Y, formid, selectid, nothing) {
    YUI(M.yui.loader).use('node', function(Y) {
        Y.on('change', function() {
        if ((nothing == false && Y.Lang.isBoolean(nothing)) || Y.one('#'+selectid).get('value') != nothing) {
            Y.one('#'+formid).submit();
        }
        },
        '#'+selectid);
    });
};

/**
 * Attach handler to url_select
 */
M.util.init_url_select = function(Y, formid, selectid, nothing) {
    YUI(M.yui.loader).use('node', function(Y) {
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

//=== old legacy JS code, hopefully to be replaced soon by M.xx.yy and YUI3 code ===

function popupchecker(msg) {
    var testwindow = window.open('', '', 'width=1,height=1,left=0,top=0,scrollbars=no');
    if (!testwindow) {
        alert(msg);
    } else {
        testwindow.close();
    }
}

function checkall() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            inputs[i].checked = true;
        }
    }
}

function checknone() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            inputs[i].checked = false;
        }
    }
}

function lockoptions(formid, master, subitems) {
  // Subitems is an array of names of sub items.
  // Optionally, each item in subitems may have a
  // companion hidden item in the form with the
  // same name but prefixed by "h".
  var form = document.forms[formid];

  if (eval("form."+master+".checked")) {
    for (i=0; i<subitems.length; i++) {
      unlockoption(form, subitems[i]);
    }
  } else {
    for (i=0; i<subitems.length; i++) {
      lockoption(form, subitems[i]);
    }
  }
  return(true);
}

function lockoption(form,item) {
  eval("form."+item+".disabled=true");/* IE thing */
  if(form.elements['h'+item]) {
    eval("form.h"+item+".value=1");
  }
}

function unlockoption(form,item) {
  eval("form."+item+".disabled=false");/* IE thing */
  if(form.elements['h'+item]) {
    eval("form.h"+item+".value=0");
  }
}

/**
 * Get the value of the 'virtual form element' with a particular name. That is,
 * abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @return the value of that element.
 */
function get_form_element_value(form, name) {
    var element = form[name];
    if (!element) {
        return null;
    }
    if (element.tagName) {
        // Ordinarly thing like a select box.
        return element.value;
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = element[j];
        if (el.checked) {
            return el.value;
        }
    }
    return null;
}


/**
 * Set the disabled state of the 'virtual form element' with a particular name.
 * This abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @param disabled the disabled state to set.
 */
function set_form_element_disabled(form, name, disabled) {
    var element = form[name];
    if (!element) {
        return;
    }
    if (element.tagName) {
        // Ordinarly thing like a select box.
        element.disabled = disabled;
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = element[j];
        el.disabled = disabled;
    }
}

/**
 * Set the hidden state of the 'virtual form element' with a particular name.
 * This abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @param hidden the hidden state to set.
 */
function set_form_element_hidden(form, name, hidden) {
    var element = form[name];
    if (!element) {
        return;
    }
    if (element.tagName) {
        var el = findParentNode(element, 'DIV', 'fitem', false);
        if (el!=null) {
            el.style.display = hidden ? 'none' : '';
            el.style.visibility = hidden ? 'hidden' : '';
        }
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = findParentNode(element[j], 'DIV', 'fitem', false);
        if (el!=null) {
            el.style.display = hidden ? 'none' : '';
            el.style.visibility = hidden ? 'hidden' : '';
        }
    }
}

function lockoptionsall(formid) {
    var form = document.forms[formid];
    var dependons = eval(formid + 'items');
    var tolock = [];
    var tohide = [];
    for (var dependon in dependons) {
        // change for MooTools compatibility
        if (!dependons.propertyIsEnumerable(dependon)) {
            continue;
        }
        if (!form[dependon]) {
            continue;
        }
        for (var condition in dependons[dependon]) {
            for (var value in dependons[dependon][condition]) {
                var lock;
                var hide = false;
                switch (condition) {
                  case 'notchecked':
                      lock = !form[dependon].checked; break;
                  case 'checked':
                      lock = form[dependon].checked; break;
                  case 'noitemselected':
                      lock = form[dependon].selectedIndex == -1; break;
                  case 'eq':
                      lock = get_form_element_value(form, dependon) == value; break;
                  case 'hide':
                      // hide as well as disable
                      hide = true; break;
                  default:
                      lock = get_form_element_value(form, dependon) != value; break;
                }
                for (var ei in dependons[dependon][condition][value]) {
                    var eltolock = dependons[dependon][condition][value][ei];
                    if (hide) {
                        tohide[eltolock] = true;
                    }
                    if (tolock[eltolock] != null) {
                        tolock[eltolock] = lock || tolock[eltolock];
                    } else {
                        tolock[eltolock] = lock;
                    }
                }
            }
        }
    }
    for (var el in tolock) {
        // change for MooTools compatibility
        if (!tolock.propertyIsEnumerable(el)) {
            continue;
        }
        set_form_element_disabled(form, el, tolock[el]);
        if (tohide.propertyIsEnumerable(el)) {
            set_form_element_hidden(form, el, tolock[el]);
    }
    }
    return true;
}

function lockoptionsallsetup(formid) {
    var form = document.forms[formid];
    var dependons = eval(formid+'items');
    for (var dependon in dependons) {
        // change for MooTools compatibility
        if (!dependons.propertyIsEnumerable(dependon)) {
            continue;
        }
        var masters = form[dependon];
        if (!masters) {
            continue;
        }
        if (masters.tagName) {
            // If master is radio buttons, we get an array, otherwise we don't.
            // Convert both cases to an array for convinience.
            masters = [masters];
        }
        for (var j = 0; j < masters.length; j++) {
            master = masters[j];
            master.formid = formid;
            master.onclick  = function() {return lockoptionsall(this.formid);};
            master.onblur   = function() {return lockoptionsall(this.formid);};
            master.onchange = function() {return lockoptionsall(this.formid);};
        }
    }
    for (var i = 0; i < form.elements.length; i++) {
        var formelement = form.elements[i];
        if (formelement.type=='reset') {
            formelement.formid = formid;
            formelement.onclick  = function() {this.form.reset();return lockoptionsall(this.formid);};
            formelement.onblur   = function() {this.form.reset();return lockoptionsall(this.formid);};
            formelement.onchange = function() {this.form.reset();return lockoptionsall(this.formid);};
        }
    }
    return lockoptionsall(formid);
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
/*
    elementSetHide (elements, hide)

    Adds or removes the "hide" class for the specified elements depending on boolean hide.
*/
function elementShowAdvanced(elements, show) {
    for (var elementIndex in elements) {
        element = elements[elementIndex];
        element.className = element.className.replace(new RegExp(' ?hide'), '')
        if(!show) {
            element.className += ' hide';
        }
    }
}

function showAdvancedInit(addBefore, nameAttr, buttonLabel, hideText, showText) {
    var showHideButton = document.createElement("input");
    showHideButton.type = 'button';
    showHideButton.value = buttonLabel;
    showHideButton.name = nameAttr;
    showHideButton.moodle = {
        hideLabel: M.str.form.hideadvanced,
        showLabel: M.str.form.showadvanced
    };
    YAHOO.util.Event.addListener(showHideButton, 'click', showAdvancedOnClick);
    el = document.getElementById(addBefore);
    el.parentNode.insertBefore(showHideButton, el);
}

function showAdvancedOnClick(e) {
    var button = e.target ? e.target : e.srcElement;

    var toSet=findChildNodes(button.form, null, 'advanced');
    var buttontext = '';
    if (button.form.elements['mform_showadvanced_last'].value == '0' ||  button.form.elements['mform_showadvanced_last'].value == '' ) {
        elementShowAdvanced(toSet, true);
        buttontext = button.moodle.hideLabel;
        button.form.elements['mform_showadvanced_last'].value = '1';
    } else {
        elementShowAdvanced(toSet, false);
        buttontext = button.moodle.showLabel;
        button.form.elements['mform_showadvanced_last'].value = '0';
    }
    var formelements = button.form.elements;
    // Fixed MDL-10506
    for (var i = 0; i < formelements.length; i++) {
        if (formelements[i] && formelements[i].name && (formelements[i].name=='mform_showadvanced')) {
            formelements[i].value = buttontext;
        }
    }
    //never submit the form if js is enabled.
    return false;
}

function unmaskPassword(id) {
  var pw = document.getElementById(id);
  var chb = document.getElementById(id+'unmask');

  try {
    // first try IE way - it can not set name attribute later
    if (chb.checked) {
      var newpw = document.createElement('<input type="text" name="'+pw.name+'">');
    } else {
      var newpw = document.createElement('<input type="password" name="'+pw.name+'">');
    }
    newpw.attributes['class'].nodeValue = pw.attributes['class'].nodeValue;
  } catch (e) {
    var newpw = document.createElement('input');
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

/**
 * Search a Moodle form to find all the fdate_time_selector and fdate_selector
 * elements, and add date_selector_calendar instance to each.
 */
function init_date_selectors(firstdayofweek) {
    var els = YAHOO.util.Dom.getElementsByClassName('fdate_time_selector', 'fieldset');
    for (var i = 0; i < els.length; i++) {
        new date_selector_calendar(els[i], firstdayofweek);
    }
    els = YAHOO.util.Dom.getElementsByClassName('fdate_selector', 'fieldset');
    for (i = 0; i < els.length; i++) {
        new date_selector_calendar(els[i], firstdayofweek);
    }
}

/**
 * Constructor for a JavaScript object that connects to a fdate_time_selector
 * or a fdate_selector in a Moodle form, and shows a popup calendar whenever
 * that element has keyboard focus.
 * @param el the fieldset class="fdate_time_selector" or "fdate_selector".
 */
function date_selector_calendar(el, firstdayofweek) {
    // Ensure that the shared div and calendar exist.
    if (!date_selector_calendar.panel) {
        date_selector_calendar.panel = new YAHOO.widget.Panel('date_selector_calendar_panel',
                {visible: false, draggable: false});
        var div = document.createElement('div');
        date_selector_calendar.panel.setBody(div);
        date_selector_calendar.panel.render(document.body);

        YAHOO.util.Event.addListener(document, 'click', date_selector_calendar.document_click);
        date_selector_calendar.panel.showEvent.subscribe(function() {
            date_selector_calendar.panel.fireEvent('changeContent');
        });
        date_selector_calendar.panel.hideEvent.subscribe(date_selector_calendar.release_current);

        date_selector_calendar.calendar = new YAHOO.widget.Calendar(div,
                {iframe: false, hide_blank_weeks: true, start_weekday: firstdayofweek});
        date_selector_calendar.calendar.renderEvent.subscribe(function() {
            date_selector_calendar.panel.fireEvent('changeContent');
            date_selector_calendar.delayed_reposition();
        });
    }

    this.fieldset = el;
    var controls = el.getElementsByTagName('select');
    for (var i = 0; i < controls.length; i++) {
        if (/\[year\]$/.test(controls[i].name)) {
            this.yearselect = controls[i];
        } else if (/\[month\]$/.test(controls[i].name)) {
            this.monthselect = controls[i];
        } else if (/\[day\]$/.test(controls[i].name)) {
            this.dayselect = controls[i];
        } else {
            YAHOO.util.Event.addFocusListener(controls[i], date_selector_calendar.cancel_any_timeout, this);
            YAHOO.util.Event.addBlurListener(controls[i], this.blur_event, this);
        }
    }
    if (!(this.yearselect && this.monthselect && this.dayselect)) {
        throw 'Failed to initialise calendar.';
    }
    YAHOO.util.Event.addFocusListener([this.yearselect, this.monthselect, this.dayselect], this.focus_event, this);
    YAHOO.util.Event.addBlurListener([this.yearselect, this.monthselect, this.dayselect], this.blur_event, this);

    this.enablecheckbox = el.getElementsByTagName('input')[0];
    if (this.enablecheckbox) {
        YAHOO.util.Event.addFocusListener(this.enablecheckbox, this.focus_event, this);
        YAHOO.util.Event.addListener(this.enablecheckbox, 'change', this.focus_event, this);
        YAHOO.util.Event.addBlurListener(this.enablecheckbox, this.blur_event, this);
    }
}

/** The pop-up calendar that contains the calendar. */
date_selector_calendar.panel = null;

/** The shared YAHOO.widget.Calendar used by all date_selector_calendars. */
date_selector_calendar.calendar = null;

/** The date_selector_calendar that currently owns the shared stuff. */
date_selector_calendar.currentowner = null;

/** Used as a timeout when hiding the calendar on blur - so we don't hide the calendar
 * if we are just jumping from on of our controls to another. */
date_selector_calendar.hidetimeout = null;

/** Timeout for repositioning after a delay after a change of months. */
date_selector_calendar.repositiontimeout = null;

/** Member variables. Pointers to various bits of the DOM. */
date_selector_calendar.prototype.fieldset = null;
date_selector_calendar.prototype.yearselect = null;
date_selector_calendar.prototype.monthselect = null;
date_selector_calendar.prototype.dayselect = null;
date_selector_calendar.prototype.enablecheckbox = null;

date_selector_calendar.cancel_any_timeout = function() {
    if (date_selector_calendar.hidetimeout) {
        clearTimeout(date_selector_calendar.hidetimeout);
        date_selector_calendar.hidetimeout = null;
    }
    if (date_selector_calendar.repositiontimeout) {
        clearTimeout(date_selector_calendar.repositiontimeout);
        date_selector_calendar.repositiontimeout = null;
    }
}

date_selector_calendar.delayed_reposition = function() {
    if (date_selector_calendar.repositiontimeout) {
        clearTimeout(date_selector_calendar.repositiontimeout);
        date_selector_calendar.repositiontimeout = null;
    }
    date_selector_calendar.repositiontimeout = setTimeout(date_selector_calendar.fix_position, 500);
}

date_selector_calendar.fix_position = function() {
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.panel.cfg.setProperty('context', [date_selector_calendar.currentowner.fieldset, 'bl', 'tl']);
    }
}

date_selector_calendar.release_current = function() {
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.currentowner.release_calendar();
    }
}

date_selector_calendar.prototype.focus_event = function(e, me) {
    date_selector_calendar.cancel_any_timeout();
    if (me.enablecheckbox == null || me.enablecheckbox.checked) {
        me.claim_calendar();
    } else {
        if (date_selector_calendar.currentowner) {
            date_selector_calendar.currentowner.release_calendar();
        }
    }
}

date_selector_calendar.prototype.blur_event = function(e, me) {
    date_selector_calendar.hidetimeout = setTimeout(date_selector_calendar.release_current, 300);
}

date_selector_calendar.prototype.handle_select_change = function(e, me) {
    me.set_date_from_selects();
}

date_selector_calendar.document_click = function(event) {
    if (date_selector_calendar.currentowner) {
        var currentcontainer = date_selector_calendar.currentowner.fieldset;
        var eventarget = YAHOO.util.Event.getTarget(event);
        if (YAHOO.util.Dom.isAncestor(currentcontainer, eventarget)) {
            setTimeout(function() {date_selector_calendar.cancel_any_timeout()}, 100);
        } else {
            date_selector_calendar.currentowner.release_calendar();
        }
    }
}

date_selector_calendar.prototype.claim_calendar = function() {
    date_selector_calendar.cancel_any_timeout();
    if (date_selector_calendar.currentowner == this) {
        return;
    }
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.currentowner.release_calendar();
    }

    if (date_selector_calendar.currentowner != this) {
        this.connect_handlers();
    }
    date_selector_calendar.currentowner = this;

    date_selector_calendar.calendar.cfg.setProperty('mindate', new Date(this.yearselect.options[0].value, 0, 1));
    date_selector_calendar.calendar.cfg.setProperty('maxdate', new Date(this.yearselect.options[this.yearselect.options.length - 1].value, 11, 31));
    this.fieldset.insertBefore(date_selector_calendar.panel.element, this.yearselect.nextSibling);
    this.set_date_from_selects();
    date_selector_calendar.panel.show();
    var me = this;
    setTimeout(function() {date_selector_calendar.cancel_any_timeout()}, 100);
}

date_selector_calendar.prototype.set_date_from_selects = function() {
    var year = parseInt(this.yearselect.value);
    var month = parseInt(this.monthselect.value) - 1;
    var day = parseInt(this.dayselect.value);
    date_selector_calendar.calendar.select(new Date(year, month, day));
    date_selector_calendar.calendar.setMonth(month);
    date_selector_calendar.calendar.setYear(year);
    date_selector_calendar.calendar.render();
    date_selector_calendar.fix_position();
}

date_selector_calendar.prototype.set_selects_from_date = function(eventtype, args) {
    var date = args[0][0];
    var newyear = date[0];
    var newindex = newyear - this.yearselect.options[0].value;
    this.yearselect.selectedIndex = newindex;
    this.monthselect.selectedIndex = date[1] - this.monthselect.options[0].value;
    this.dayselect.selectedIndex = date[2] - this.dayselect.options[0].value;
}

date_selector_calendar.prototype.connect_handlers = function() {
    YAHOO.util.Event.addListener([this.yearselect, this.monthselect, this.dayselect], 'change', this.handle_select_change, this);
    date_selector_calendar.calendar.selectEvent.subscribe(this.set_selects_from_date, this, true);
}

date_selector_calendar.prototype.release_calendar = function() {
    date_selector_calendar.panel.hide();
    date_selector_calendar.currentowner = null;
    YAHOO.util.Event.removeListener([this.yearselect, this.monthselect, this.dayselect], this.handle_select_change);
    date_selector_calendar.calendar.selectEvent.unsubscribe(this.set_selects_from_date, this);
}

function filterByParent(elCollection, parentFinder) {
    var filteredCollection = [];
    for (var i = 0; i < elCollection.length; ++i) {
        var findParent = parentFinder(elCollection[i]);
        if (findParent.nodeName.toUpperCase != 'BODY') {
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
            if(nodes[i].className.indexOf("sideblock") != -1 ) {
                if(width < nodes[i].offsetWidth) {
                    width = nodes[i].offsetWidth;
                }
            }
        }

        for(i = 0; i < nodes.length; ++i) {
            if(nodes[i].className.indexOf("sideblock") != -1 ) {
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

function openpopup(event, args) {

    YAHOO.util.Event.preventDefault(event);
   
    if (M.help_content.instance !== null) {       
        var id = '#' + args.id;        
        M.help_content.instance.load_from_openpopup(event, args);
    } else {        
        var fullurl = args.url;
        if (!args.url.match(/https?:\/\//)) {
            fullurl = M.cfg.wwwroot + args.url;
        }
        var windowobj = window.open(fullurl,args.name,args.options);
        if (!windowobj) {
            return true;
        }
        if (args.fullscreen) {
            windowobj.moveTo(0,0);
            windowobj.resizeTo(screen.availWidth,screen.availHeight);
        }
        windowobj.focus();
        return false;
    }
}

/* This is only used on a few help pages. */
emoticons_help = {
    inputarea: null,

    init: function(formname, fieldname, listid) {
        if (!opener || !opener.document.forms[formname]) {
            return;
        }
        emoticons_help.inputarea = opener.document.forms[formname][fieldname];
        if (!emoticons_help.inputarea) {
            return;
        }
        var emoticons = document.getElementById(listid).getElementsByTagName('li');
        for (var i = 0; i < emoticons.length; i++) {
            var text = emoticons[i].getElementsByTagName('img')[0].alt;
            YAHOO.util.Event.addListener(emoticons[i], 'click', emoticons_help.inserttext, text);
        }
    },

    inserttext: function(e, text) {
        text = ' ' + text + ' ';
        if (emoticons_help.inputarea.createTextRange && emoticons_help.inputarea.caretPos) {
            var caretPos = emoticons_help.inputarea.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
        } else {
            emoticons_help.inputarea.value  += text;
        }
        emoticons_help.inputarea.focus();
    }
}

/**
 * Oject to handle expanding and collapsing blocks when an icon is clicked on.
 * @constructor
 * @param String id the HTML id for the div.
 * @param String userpref the user preference that records the state of this block.
 * @param String visibletooltip tool tip/alt to show when the block is visible.
 * @param String hiddentooltip tool tip/alt to show when the block is hidden.
 * @param String visibleicon URL of the icon to show when the block is visible.
 * @param String hiddenicon URL of the icon to show when the block is hidden.
 */
function block_hider(id, userpref, visibletooltip, hiddentooltip, visibleicon, hiddenicon) {
    // Find the elemen that is the block.
    this.block = document.getElementById(id);
    var title_div = YAHOO.util.Dom.getElementsByClassName('title', 'div', this.block);
    if (!title_div || !title_div[0]) {
        return this;
    }
    title_div = title_div[0];
    this.ishidden = YAHOO.util.Dom.hasClass(this.block, 'hidden');

    // Record the pref name
    this.userpref = userpref;
    this.visibletooltip = visibletooltip;
    this.hiddentooltip = hiddentooltip;
    this.visibleicon = visibleicon;
    this.hiddenicon = hiddenicon;

    // Add the icon.
    this.icon = document.createElement('input');
    this.icon.type = 'image';
    this.update_state();

    var blockactions = YAHOO.util.Dom.getElementsByClassName('block_action', 'div', title_div);
    if (blockactions && blockactions[0]) {
        blockactions[0].insertBefore(this.icon, blockactions[0].firstChild);
    }

    // Hook up the event handler.
    YAHOO.util.Event.addListener(this.icon, 'click', this.handle_click, null, this);
}

/** Handle click on a block show/hide icon. */
block_hider.prototype.handle_click = function(e) {
    YAHOO.util.Event.stopEvent(e);
    this.ishidden = !this.ishidden;
    this.update_state();
    M.util.set_user_preference(this.userpref, this.ishidden);
}

/** Set the state of the block show/hide icon to this.ishidden. */
block_hider.prototype.update_state = function () {
    if (this.ishidden) {
        YAHOO.util.Dom.addClass(this.block, 'hidden');
        this.icon.alt = this.hiddentooltip;
        this.icon.title = this.hiddentooltip;
        this.icon.src = this.hiddenicon;
    } else {
        YAHOO.util.Dom.removeClass(this.block, 'hidden');
        this.icon.alt = this.visibletooltip;
        this.icon.title = this.visibletooltip;
        this.icon.src = this.visibleicon;
    }
}

/** Close the current browser window. */
function close_window(e) {
    YAHOO.util.Event.preventDefault(e);
    self.close();
}

/**
 * Close the current browser window, forcing the window/tab that opened this
 * popup to reload itself. */
function close_window_reloading_opener() {
    if (window.opener) {
        window.opener.location.reload(1);
        close_window();
        // Intentionally, only try to close the window if there is some evidence we are in a popup.
    }
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
    return list.join('&');
}

function stripHTML(str) {
    var re = /<\S[^><]*>/g;
    var ret = str.replace(re, "");
    return ret;
}

Number.prototype.fixed=function(n){
    with(Math)
        return round(Number(this)*pow(10,n))/pow(10,n);
}
function update_progress_bar (id, width, pt, msg, es){
    var percent = pt*100;
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
        if (es == Infinity){
            time_es.innerHTML = "Initializing...";
        }else {
            time_es.innerHTML = es.fixed(2)+" sec";
            time_es.style.display
                = "block";
        }
    }
    progress_bar.style.width = width + "px";

}

function frame_breakout(e, properties) {
    this.setAttribute('target', properties.framename);
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

M.help_content = {
    instance : null,
    init : function(Y) {                
        Y.use('overlay', 'io', 'event-mouseenter', 'node', "*", function(Y) {

            var help_content_overlay = {
                overlay : null,
                init : function() {
                    var xy = Y.one('#helpcontent').getXY(); 

                    // Create an overlay from markup
                    this.overlay = new Y.Overlay({
                        srcNode : "#helpcontent",
                        contentBox: '#helpcontent',
                        width:'400px',
                        xy:[xy[0] + 200, xy[1] + 300]
                    });
                    this.overlay.render();
                    this.overlay.hide();

                    Y.on("click", Y.bind(this.overlay.show, this.overlay), "#show");
                    Y.on("click", Y.bind(this.overlay.hide, this.overlay), "#hide");
                    
                    var menuButton = Y.one(".helplink"),
                    overlaycontent = this;

                    var boundingBox = this.overlay.get("boundingBox");

                    //  Hide the menu if the user clicks outside of its content 
                    boundingBox.get("ownerDocument").on("mousedown", function (event) {
                        var oTarget = event.target;

                        if (!oTarget.compareTo(menuButton) &&
                            !menuButton.contains(oTarget) &&
                            !oTarget.compareTo(boundingBox) &&
                            !boundingBox.contains(oTarget)) {
                            overlaycontent.overlay.hide();
                        }
                    });                    
                },
                
                load_from_openpopup : function(event, node) {
                    var spinner = document.createElement('img');
                    spinner.src = M.cfg.loadingicon;
                    this.overlay.set('bodyContent', spinner);

                    var positionX = 0;
                    var positionY = 0;

                    if (event.pageX && !Y.UA.chrome) {
                        positionX = event.pageX;
                        positionY = event.pageY;
                    } else if (event.pageX && !Y.UA.chrome) {
                        positionX = document.getElementById(node['id']).offsetLeft + 200;
                        positionY = document.getElementById(node['id']).offsetTop + 500;
                    } else { //chrome browser
                        positionX = event.clientX + document.body.scrollLeft ;
                        positionY = document.body.scrollTop;
                        if (event.clientX > document.body.scrollTop) {
                            positionY += event.clientY;
                        }
                    }

                    if (Y.UA.chrome) {
                        positionX = event.clientX + document.body.scrollLeft ;
                        positionY = document.body.scrollTop;
                        if (event.clientX > document.body.scrollTop) {
                            positionY += event.clientY;
                        }                        
                    }

                    var contentwidth = parseInt(this.overlay.get('width'));
                    var overlayalign = 'right';
                    if (document.getElementById('page').offsetWidth <= (positionX + contentwidth )) {
                        overlayalign = 'left';                        
                    }

                    var WidgetPositionAlign = Y.WidgetPositionAlign;
                    if (overlayalign == 'right') {
                        this.overlay.set("align", {node:"#" + node['id'],
                          points:[WidgetPositionAlign.TL, WidgetPositionAlign.RC]});
                    } else {
                        this.overlay.set("align", {node:"#" + node['id'],
                          points:[WidgetPositionAlign.TR, WidgetPositionAlign.LC]});
                    }
                    
                    var fullurl = node.url;
                    if (!node.url.match(/https?:\/\//)) {
                        fullurl = M.cfg.wwwroot + node.url;
                    }
                    
                    var ajaxurl = fullurl + '&ajax=1';
                    thishelpcontent = this;
                    
                    var cfg = {
                        method: 'get',
                        on: {
                            success: function(id, o, node) {
                                thishelpcontent.load_from_openpopup_callback(o.responseText);                                
                            },
                            failure: function(id, o, node) {
                                var debuginfo = o.statusText;
                                if (M.cfg.developerdebug) {
                                    o.statusText += ' (' + ajaxurl + ')';
                                }                                
                                thishelpcontent.load_from_openpopup_callback('bodyContent',debuginfo);
                            }
                        }
                    };
                    
                    var conn = Y.io(ajaxurl, cfg);
                    this.overlay.show();
                },
                
                load_from_openpopup_callback : function(content) {                                                            
                    this.overlay.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
                },
                
                hideContent : function() {
                    help = this;
                    help.overlay.hide();
                }                
            }
            help_content_overlay.init();
            M.help_content.instance = help_content_overlay;
            
        });
    }
}
