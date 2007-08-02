/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/
/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
*/
/**
 * @description <p>Creates a rich Toolbar widget based on Button. Primarily used with the Rich Text Editor</p>
 * @class Toolbar
 * @namespace YAHOO.widget
 * @requires yahoo, dom, element, event
 * @optional container, menu, button, dragdrop
 * @beta
 */
(function() {
    /**
    * @private
    **/
var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    Lang = YAHOO.lang;

    /**
     * Provides a rich toolbar widget based on the button and menu widgets
     * @constructor
     * @param {String/HTMLElement} el The element to turn into a toolbar.
     * @param {Object} attrs Object liternal containing configuration parameters.
    */
    YAHOO.widget.Toolbar = function(el, attrs) {
        YAHOO.log('Toolbar Initalizing', 'info', 'Toolbar');
        YAHOO.log(arguments.length + ' arguments passed to constructor', 'info', 'Toolbar');
        
        if (Lang.isObject(arguments[0]) && !Dom.get(el).nodeType) {
            var attrs = el;
        }
        var local_attrs = (attrs || {});

        var oConfig = {
            element: null,
            attributes: local_attrs
        }
        
        
        if (Lang.isString(el) && Dom.get(el)) {
            oConfig.element = Dom.get(el);
        } else if (Lang.isObject(el) && Dom.get(el) && Dom.get(el).nodeType) {  
            oConfig.element = Dom.get(el);
        }
        

        if (!oConfig.element) {
            YAHOO.log('No element defined, creating toolbar container', 'warn', 'Toolbar');
            oConfig.element = document.createElement('DIV');
            oConfig.element.id = Dom.generateId();
            
            if (local_attrs.container && Dom.get(local_attrs.container)) {
                YAHOO.log('Container found in config appending to it (' + Dom.get(local_attrs.container).id + ')', 'info', 'Toolbar');
                Dom.get(local_attrs.container).appendChild(oConfig.element);
            }
        }
        

        if (!oConfig.element.id) {
            oConfig.element.id = ((Lang.isString(el)) ? el : Dom.generateId());
            YAHOO.log('No element ID defined for toolbar container, creating..', 'warn', 'Toolbar');
        }
        YAHOO.log('Initing toolbar with id: ' + oConfig.element.id, 'info', 'Toolbar');
        
        var cont = document.createElement('DIV');
        oConfig.attributes.cont = cont;
        Dom.addClass(cont, 'yui-toolbar-subcont')
        oConfig.element.appendChild(cont);
        
        oConfig.attributes.element = oConfig.element;
        oConfig.attributes.id = oConfig.element.id;

        YAHOO.widget.Toolbar.superclass.constructor.call(this, oConfig.element, oConfig.attributes);
        
        
    }

    /**
    * @method _addMenuClasses
    * @private
    * @description This method is called from Menu's renderEvent to add a few more classes to the menu items
    * @param {String} ev The event that fired.
    * @param {Array} na Array of event information.
    * @param {Object} o Button config object. 
    */

    function _addMenuClasses(ev, na, o) {
        Dom.addClass(this.element, 'yui-toolbar-' + o.get('value') + '-menu');
        if (Dom.hasClass(o._button.parentNode.parentNode, 'yui-toolbar-select')) {
            Dom.addClass(this.element, 'yui-toolbar-select-menu');
        }
        var items = this.getItems();
        for (var i = 0; i < items.length; i++) {
            Dom.addClass(items[i].element, 'yui-toolbar-' + o.get('value') + '-' + ((items[i].value) ? items[i].value.replace(/ /g, '-').toLowerCase() : items[i]._oText.nodeValue.replace(/ /g, '-').toLowerCase()));
            Dom.addClass(items[i].element, 'yui-toolbar-' + o.get('value') + '-' + ((items[i].value) ? items[i].value.replace(/ /g, '-') : items[i]._oText.nodeValue.replace(/ /g, '-')));
        }
        this._setWidth();
    };

    YAHOO.extend(YAHOO.widget.Toolbar, YAHOO.util.Element, {
        /** 
        * @property dd
        * @description The DragDrop instance associated with the Toolbar
        * @type Object
        */
        dd: null,
        /** 
        * @property _colorData
        * @description Object reference containing colors hex and text values.
        * @type Object
        */
        _colorData: {
/* {{{ _colorData */
    '#111111': 'Obsidian',
    '#2D2D2D': 'Dark Gray',
    '#434343': 'Shale',
    '#5B5B5B': 'Flint',
    '#737373': 'Gray',
    '#8B8B8B': 'Concrete',
    '#A2A2A2': 'Gray',
    '#B9B9B9': 'Titanium',
    '#000000': 'Black',
    '#D0D0D0': 'Light Gray',
    '#E6E6E6': 'Silver',
    '#FFFFFF': 'White',
    '#BFBF00': 'Pumpkin',
    '#FFFF00': 'Yellow',
    '#FFFF40': 'Banana',
    '#FFFF80': 'Pale Yellow',
    '#FFFFBF': 'Butter',
    '#525330': 'Raw Siena',
    '#898A49': 'Mildew',
    '#AEA945': 'Olive',
    '#7F7F00': 'Paprika',
    '#C3BE71': 'Earth',
    '#E0DCAA': 'Khaki',
    '#FCFAE1': 'Cream',
    '#60BF00': 'Cactus',
    '#80FF00': 'Chartreuse',
    '#A0FF40': 'Green',
    '#C0FF80': 'Pale Lime',
    '#DFFFBF': 'Light Mint',
    '#3B5738': 'Green',
    '#668F5A': 'Lime Gray',
    '#7F9757': 'Yellow',
    '#407F00': 'Clover',
    '#8A9B55': 'Pistachio',
    '#B7C296': 'Light Jade',
    '#E6EBD5': 'Breakwater',
    '#00BF00': 'Spring Frost',
    '#00FF80': 'Pastel Green',
    '#40FFA0': 'Light Emerald',
    '#80FFC0': 'Sea Foam',
    '#BFFFDF': 'Sea Mist',
    '#033D21': 'Dark Forrest',
    '#438059': 'Moss',
    '#7FA37C': 'Medium Green',
    '#007F40': 'Pine',
    '#8DAE94': 'Yellow Gray Green',
    '#ACC6B5': 'Aqua Lung',
    '#DDEBE2': 'Sea Vapor',
    '#00BFBF': 'Fog',
    '#00FFFF': 'Cyan',
    '#40FFFF': 'Turquoise Blue',
    '#80FFFF': 'Light Aqua',
    '#BFFFFF': 'Pale Cyan',
    '#033D3D': 'Dark Teal',
    '#347D7E': 'Gray Turquoise',
    '#609A9F': 'Green Blue',
    '#007F7F': 'Seaweed',
    '#96BDC4': 'Green Gray',
    '#B5D1D7': 'Soapstone',
    '#E2F1F4': 'Light Turquoise',
    '#0060BF': 'Summer Sky',
    '#0080FF': 'Sky Blue',
    '#40A0FF': 'Electric Blue',
    '#80C0FF': 'Light Azure',
    '#BFDFFF': 'Ice Blue',
    '#1B2C48': 'Navy',
    '#385376': 'Biscay',
    '#57708F': 'Dusty Blue',
    '#00407F': 'Sea Blue',
    '#7792AC': 'Sky Blue Gray',
    '#A8BED1': 'Morning Sky',
    '#DEEBF6': 'Vapor',
    '#0000BF': 'Deep Blue',
    '#0000FF': 'Blue',
    '#4040FF': 'Cerulean Blue',
    '#8080FF': 'Evening Blue',
    '#BFBFFF': 'Light Blue',
    '#212143': 'Deep Indigo',
    '#373E68': 'Sea Blue',
    '#444F75': 'Night Blue',
    '#00007F': 'Indigo Blue',
    '#585E82': 'Dockside',
    '#8687A4': 'Blue Gray',
    '#D2D1E1': 'Light Blue Gray',
    '#6000BF': 'Neon Violet',
    '#8000FF': 'Blue Violet',
    '#A040FF': 'Violet Purple',
    '#C080FF': 'Violet Dusk',
    '#DFBFFF': 'Pale Lavender',
    '#302449': 'Cool Shale',
    '#54466F': 'Dark Indigo',
    '#655A7F': 'Dark Violet',
    '#40007F': 'Violet',
    '#726284': 'Smoky Violet',
    '#9E8FA9': 'Slate Gray',
    '#DCD1DF': 'Violet White',
    '#BF00BF': 'Royal Violet',
    '#FF00FF': 'Fuchsia',
    '#FF40FF': 'Magenta',
    '#FF80FF': 'Orchid',
    '#FFBFFF': 'Pale Magenta',
    '#4A234A': 'Dark Purple',
    '#794A72': 'Medium Purple',
    '#936386': 'Cool Granite',
    '#7F007F': 'Purple',
    '#9D7292': 'Purple Moon',
    '#C0A0B6': 'Pale Purple',
    '#ECDAE5': 'Pink Cloud',
    '#BF005F': 'Hot Pink',
    '#FF007F': 'Deep Pink',
    '#FF409F': 'Grape',
    '#FF80BF': 'Electric Pink',
    '#FFBFDF': 'Pink',
    '#451528': 'Purple Red',
    '#823857': 'Purple Dino',
    '#A94A76': 'Purple Gray',
    '#7F003F': 'Rose',
    '#BC6F95': 'Antique Mauve',
    '#D8A5BB': 'Cool Marble',
    '#F7DDE9': 'Pink Granite',
    '#C00000': 'Apple',
    '#FF0000': 'Fire Truck',
    '#FF4040': 'Pale Red',
    '#FF8080': 'Salmon',
    '#FFC0C0': 'Warm Pink',
    '#441415': 'Sepia',
    '#82393C': 'Rust',
    '#AA4D4E': 'Brick',
    '#800000': 'Brick Red',
    '#BC6E6E': 'Mauve',
    '#D8A3A4': 'Shrimp Pink',
    '#F8DDDD': 'Shell Pink',
    '#BF5F00': 'Dark Orange',
    '#FF7F00': 'Orange',
    '#FF9F40': 'Grapefruit',
    '#FFBF80': 'Canteloupe',
    '#FFDFBF': 'Wax',
    '#482C1B': 'Dark Brick',
    '#855A40': 'Dirt',
    '#B27C51': 'Tan',
    '#7F3F00': 'Nutmeg',
    '#C49B71': 'Mustard',
    '#E1C4A8': 'Pale Tan',
    '#FDEEE0': 'Marble'
/* }}} */
        },
        /** 
        * @property _colorPicker
        * @description The HTML Element containing the colorPicker
        * @type HTMLElement
        */
        _colorPicker: null,
        /** 
        * @property STR_COLLAPSE
        * @description String for Toolbar Collapse Button
        * @type String
        */
        STR_COLLAPSE: 'Collapse Toolbar',
        /** 
        * @property STR_SPIN_LABEL
        * @description String for spinbutton dynamic label. Note the {VALUE} will be replaced with YAHOO.lang.substitute
        * @type String
        */
        STR_SPIN_LABEL: 'Spin Button with value {VALUE}. Use Control Shift Up Arrow and Control Shift Down arrow keys to increase or decrease the value.',
        /** 
        * @property STR_SPIN_UP
        * @description String for spinbutton up
        * @type String
        */
        STR_SPIN_UP: 'Click to increase the value of this input',
        /** 
        * @property STR_SPIN_DOWN
        * @description String for spinbutton down
        * @type String
        */
        STR_SPIN_DOWN: 'Click to decrease the value of this input',
        /** 
        * @property _titlebar
        * @description Object reference to the titlebar
        * @type HTMLElement
        */
        _titlebar: null,
        /** 
        * @property _disabled
        * @description Object to track button status when enabling/disabling the toolbar
        * @type Object
        */
        _disabled: null,
        /** 
        * @property browser
        * @description Standard browser detection
        * @type Object
        */
        browser: YAHOO.env.ua,
        /**
        * @protected
        * @property _buttonList
        * @description Internal property list of current buttons in the toolbar
        * @type Array
        */
        _buttonList: null,
        /**
        * @protected
        * @property _buttonGroupList
        * @description Internal property list of current button groups in the toolbar
        * @type Array
        */
        _buttonGroupList: null,
        /**
        * @protected
        * @property _sep
        * @description Internal reference to the separator HTML Element for cloning
        * @type HTMLElement
        */
        _sep: null,
        /**
        * @protected
        * @property _sepCount
        * @description Internal refernce for counting separators, so we can give them a useful class name for styling
        * @type Number
        */
        _sepCount: null,
        /**
        * @protected
        * @property draghandle
        * @type HTMLElement
        */
        _dragHandle: null,
        /**
        * @protected
        * @property _toolbarConfigs
        * @type Object
        */
        _toolbarConfigs: {
            renderer: true
        },
        /**
        * @protected
        * @property CLASS_CONTAINER
        * @description Default CSS class to apply to the toolbar container element
        * @type String
        */
        CLASS_CONTAINER: 'yui-toolbar-container',
        /**
        * @protected
        * @property CLASS_DRAGHANDLE
        * @description Default CSS class to apply to the toolbar's drag handle element
        * @type String
        */
        CLASS_DRAGHANDLE: 'yui-toolbar-draghandle',
        /**
        * @protected
        * @property CLASS_SEPARATOR
        * @description Default CSS class to apply to all separators in the toolbar
        * @type String
        */
        CLASS_SEPARATOR: 'yui-toolbar-separator',
        /**
        * @protected
        * @property CLASS_DISABLED
        * @description Default CSS class to apply when the toolbar is disabled
        * @type String
        */
        CLASS_DISABLED: 'yui-toolbar-disabled',
        /**
        * @protected
        * @property CLASS_PREFIX
        * @description Default prefix for dynamically created class names
        * @type String
        */
        CLASS_PREFIX: 'yui-toolbar',
        /** 
        * @method init
        * @description The Toolbar class's initialization method
        */
        init: function(p_oElement, p_oAttributes) {
            YAHOO.widget.Toolbar.superclass.init.call(this, p_oElement, p_oAttributes);
        },
        /**
        * @method initAttributes
        * @description Initializes all of the configuration attributes used to create 
        * the toolbar.
        * @param {Object} attr Object literal specifying a set of 
        * configuration attributes used to create the toolbar.
        */
        initAttributes: function(attr) {
            YAHOO.widget.Toolbar.superclass.initAttributes.call(this, attr);
            var el = this.get('element');
            this.addClass(this.CLASS_CONTAINER);


            /**
            * @config buttons
            * @description Object specifying the buttons to include in the toolbar
            * Example:
            * <code><pre>
            * {
            *   { id: 'b3', type: 'button', label: 'Underline', value: 'underline' },
            *   { type: 'separator' },
            *   { id: 'b4', type: 'menu', label: 'Align', value: 'align',
            *       menu: [
            *           { text: "Left", value: 'alignleft' },
            *           { text: "Center", value: 'aligncenter' },
            *           { text: "Right", value: 'alignright' }
            *       ]
            *   }
            * }
            * </pre></code>
            * @type Object
            */
            
            this.setAttributeConfig('buttons', {
                value: [],
                writeOnce: true,
                method: function(data) {
                    for (var i in data) {
                        if (Lang.hasOwnProperty(data, i)) {
                            if (data[i].type == 'separator') {
                                this.addSeparator();
                            } else if (data[i].group != undefined) {
                                this.addButtonGroup(data[i]);
                            } else {
                                this.addButton(data[i]);
                            }
                        }
                    }
                }
            });

            /**
            * @config disabled
            * @description Boolean indicating if the toolbar should be disabled. It will also disable the draggable attribute if it is on.
            * @default false
            * @type Boolean
            */
            this.setAttributeConfig('disabled', {
                value: false,
                method: function(disabled) {
                    if (!Lang.isObject(this._disabled)) {
                        this._disabled = {};
                    }
                    if (disabled) {
                        this.addClass(this.CLASS_DISABLED);
                        this.set('draggable', false);
                    } else {
                        this.removeClass(this.CLASS_DISABLED);
                        if (this._configs.draggable._initialConfig.value) {
                            //Draggable by default, set it back
                            this.set('draggable', true);
                        }
                    }
                    var len = this._buttonList.length;
                    for (var i = 0; i < len; i++) {
                        if (disabled) {
                            //If it's already disabled, flag it
                            if (this._buttonList[i].get('disabled')) {
                                this._disabled[i] = true;
                            } else {
                                this._disabled[i] = null;
                            }
                            this.disableButton(this._buttonList[i].get('id'));
                        } else {
                            //Check to see if it was disabled by default and skip it
                            var _button = this._buttonList[i];
                            var _check = _button._configs.disabled._initialConfig.value;
                            if (this._disabled[i]) {
                                _check = true;
                            }
                            if (!_check) {
                                this.enableButton(_button.get('id'));
                            }
                        }
                    }
                }
            });

            /**
            * @config grouplabels
            * @description Boolean indicating if the toolbar should show the group label's text string.
            * @default true
            * @type Boolean
            */
            this.setAttributeConfig('grouplabels', {
                value: true,
                writeOnce: true
            });

            /**
            * @config cont
            * @description Boolean indicating if the toolbar should show the group label's text string.
            * @default true
            * @type Boolean
            */
            this.setAttributeConfig('cont', {
                value: attr.cont,
                readOnly: true
            });

            /**
            * @config collapse
            * @description Boolean indicating if the the titlebar should have a collapse button.
            * The collapse button will not remove the toolbar, it will minimize it to the titlebar
            * @default false
            * @type Boolean
            */
            this.setAttributeConfig('collapse', {
                value: false
            });
            /**
            * @config titlebar
            * @description Boolean indicating if the toolbar should have a titlebar. If
            * passed a string, it will use that as the titlebar text
            * @default false
            * @type Boolean or String
            */
            this.setAttributeConfig('titlebar', {
                value: false,
                method: function(titlebar) {
                    if (titlebar) {
                        if (this._titlebar && this._titlebar.parentNode) {
                            this._titlebar.parentNode.removeChild(this._titlebar);
                        }
                        this._titlebar = document.createElement('DIV');
                        Dom.addClass(this._titlebar, this.CLASS_PREFIX + '-titlebar');
                        if (Lang.isString(titlebar)) {
                            var h2 = document.createElement('h2');
                            h2.tabIndex = '-1';
                            h2.innerHTML = titlebar;
                            this._titlebar.appendChild(h2);
                        }
                        if (this.get('collapse')) {
                            var collapse = document.createElement('SPAN');
                            collapse.innerHTML = 'X';
                            collapse.title = this.STR_COLLAPSE;

                            Dom.addClass(collapse, 'collapse');
                            this._titlebar.appendChild(collapse);
                            Event.addListener(collapse, 'click', function() {
                                if (Dom.getStyle(this.get('cont'), 'display') == 'none') {
                                    Dom.setStyle(this.get('cont'), 'display', 'block');
                                    Dom.removeClass(collapse, 'collapsed');
                                    this.fireEvent('toolbarExpanded', { type: 'toolbarExpanded', target: this });
                                } else {
                                    Dom.setStyle(this.get('cont'), 'display', 'none');
                                    Dom.addClass(collapse, 'collapsed');
                                    this.fireEvent('toolbarCollapsed', { type: 'toolbarCollapsed', target: this });
                                }
                            }, this, true);
                        }
                        if (this.get('draggable')) {
                            this.dd = new YAHOO.util.DD(this.get('id'));
                            this.dd.setHandleElId(this._titlebar);
                            Dom.addClass(this._titlebar, 'draggable');
                        }
                        if (this.get('firstChild')) {
                            this.insertBefore(this._titlebar, this.get('firstChild'));
                        } else {
                            this.appendChild(this._titlebar);
                        }
                    } else if (this._titlebar) {
                        if (this._titlebar && this._titlebar.parentNode) {
                            this._titlebar.parentNode.removeChild(this._titlebar);
                        }
                    }
                }
            });


            /**
            * @config draggable
            * @description Boolean indicating if the toolbar should be draggable.  
            * @default false
            * @type Boolean
            */

            this.setAttributeConfig('draggable', {
                value: (attr.draggable || false),
                method: function(draggable) {
                    var el = this.get('element');

                    if (draggable && !this.get('titlebar')) {
                        YAHOO.log('Dragging enabled', 'info', 'Toolbar');
                        if (!this._dragHandle) {
                            this._dragHandle = document.createElement('SPAN');
                            this._dragHandle.innerHTML = '|';
                            this._dragHandle.setAttribute('title', 'Click to drag the toolbar');
                            this._dragHandle.id = this.get('id') + '_draghandle';
                            Dom.addClass(this._dragHandle, this.CLASS_DRAGHANDLE);
                            if (this.get('cont').hasChildNodes()) {
                                this.get('cont').insertBefore(this._dragHandle, this.get('cont').firstChild);
                            } else {
                                this.get('cont').appendChild(this._dragHandle);
                            }
                            /**
                            * @property dd
                            * @description The DragDrop instance associated with the Toolbar
                            * @type Object
                            */
                            this.dd = new YAHOO.util.DD(this.get('id'));
                            this.dd.setHandleElId(this._dragHandle.id);
                            
                        }
                    } else {
                        YAHOO.log('Dragging disabled', 'info', 'Toolbar');
                        if (this._dragHandle) {
                            this._dragHandle.parentNode.removeChild(this._dragHandle);
                            this._dragHandle = null;
                            this.dd = null;
                        }
                    }
                    if (this._titlebar) {
                        if (draggable) {
                            this.dd = new YAHOO.util.DD(this.get('id'));
                            this.dd.setHandleElId(this._titlebar);
                            Dom.addClass(this._titlebar, 'draggable');
                        } else {
                            Dom.removeClass(this._titlebar, 'draggable');
                            if (this.dd) {
                                this.dd.unreg();
                                this.dd = null;
                            }
                        }
                    }
                },
                validator: function(value) {
                    var ret = true;
                    if (!YAHOO.util.DD) {
                        ret = false;
                    }
                    return ret;
                }
            });

        },
        /**
        * @method addButtonGroup
        * @description Add a new button group to the toolbar. (uses addButton)
        * @param {Object} oGroup Object literal reference to the Groups Config (contains an array of button configs)
        */
        addButtonGroup: function(oGroup) {
            if (!this.get('element')) {
                this._queue[this._queue.length] = ['addButtonGroup', arguments];
                return false;
            }
            
            if (!this.hasClass(this.CLASS_PREFIX + '-grouped')) {
                this.addClass(this.CLASS_PREFIX + '-grouped');
            }
            var div = document.createElement('DIV');
            Dom.addClass(div, this.CLASS_PREFIX + '-group');
            Dom.addClass(div, this.CLASS_PREFIX + '-group-' + oGroup.group);
            if (oGroup.label && this.get('grouplabels')) {
                var label = document.createElement('h3');
                label.innerHTML = oGroup.label;
                div.appendChild(label);
            }

            this.get('cont').appendChild(div);

            //For accessibility, let's put all of the group buttons in an Unordered List
            var ul = document.createElement('ul');
            div.appendChild(ul);

            if (!this._buttonGroupList) {
                this._buttonGroupList = {};
            }
            
            this._buttonGroupList[oGroup.group] = ul;

            for (var i = 0; i < oGroup.buttons.length; i++) {
                var li = document.createElement('li');
                ul.appendChild(li);
                if ((oGroup.buttons[i].type != undefined) && oGroup.buttons[i].type == 'separator') {
                    this.addSeparator(li);
                } else {
                    oGroup.buttons[i].container = li;
                    this.addButton(oGroup.buttons[i]);
                }
            }
        },
        /**
        * @method addButtonToGroup
        * @description Add a new button to a toolbar group. Buttons supported:
        *   push, split, menu, select, color, spin
        * @param {Object} oButton Object literal reference to the Button's Config
        * @param {String} group The Group identifier passed into the initial config
        * @param {HTMLElement} after Optional HTML element to insert this button after in the DOM.
        */
        addButtonToGroup: function(oButton, group, after) {
            var groupCont = this._buttonGroupList[group];
            var li = document.createElement('li');
            oButton.container = li;
            this.addButton(oButton, after);
            groupCont.appendChild(li);
        },
        /**
        * @method addButton
        * @description Add a new button to the toolbar. Buttons supported:
        *   push, split, menu, select, color, spin
        * @param {Object} oButton Object literal reference to the Button's Config
        * @param {HTMLElement} after Optional HTML element to insert this button after in the DOM.
        */
        addButton: function(oButton, after) {
            if (!this.get('element')) {
                this._queue[this._queue.length] = ['addButton', arguments];
                return false;
            }
            if (!this._buttonList) {
                this._buttonList = [];
            }
            //Add to .get('buttons') manually
            this._configs.buttons.value[this._configs.buttons.value.length] = oButton;
            YAHOO.log('Adding button of type: ' + oButton.type, 'info', 'Toolbar');
            if (!oButton.container) {
                oButton.container = this.get('cont');
            }

            if ((oButton.type == 'menu') || (oButton.type == 'split') || (oButton.type == 'select')) {
                if (Lang.isArray(oButton.menu)) {
                    for (var i in oButton.menu) {
                        if (Lang.hasOwnProperty(oButton.menu, i)) {
                            var funcObject = {
                                fn: function(ev, x, oMenu) {
                                    if (!oButton.menucmd) {
                                        oButton.menucmd = oButton.value;
                                    }
                                    oButton.value = ((oMenu.value) ? oMenu.value : oMenu._oText.nodeValue);
                                    //This line made Opera fire the click event and the mousedown,
                                    //  so events for menus where firing twice.
                                    //this._buttonClick('click', oButton);
                                },
                                scope: this
                            }
                            oButton.menu[i].onclick = funcObject;
                        }
                    }
                }
            }
            var _oButton = {};
            for (var i in oButton) {
                if (Lang.hasOwnProperty(oButton, i)) {
                    if (!this._toolbarConfigs[i]) {
                        _oButton[i] = oButton[i];
                    }
                }
            }
            if (oButton.type == 'select') {
                _oButton.type = 'menu';
            }
            if (oButton.type == 'spin') {
                _oButton.type = 'push';
            }
            if (_oButton.type == 'color') {
                _oButton = this._makeColorButton(_oButton);
            }
            if (_oButton.menu) {
                if (oButton.menu instanceof YAHOO.widget.Overlay) {
                    oButton.menu.showEvent.subscribe(function() {
                        this._button = _oButton;
                    });
                } else {
                    for (var i = 0; i < _oButton.menu.length; i++) {
                        if (!_oButton.menu[i].value) {
                            _oButton.menu[i].value = _oButton.menu[i].text;
                        }
                    }
                    if (this.browser.webkit) {
                        _oButton.focusmenu = false;
                    }
                }
            }
            var tmp = new YAHOO.widget.Button(_oButton);
            if (this.get('disabled')) {
                //Toolbar is disabled, disable the new button too!
                tmp.set('disabled', true);
            }
            if (!oButton.id) {
                oButton.id = tmp.get('id');
            }
            YAHOO.log('Button created (' + oButton.type + ')', 'info', 'Toolbar');
            
            if (after) {
                var el = tmp.get('element');
                var nextSib = null;
                if (after.get) {
                    nextSib = after.get('element').nextSibling;
                } else if (after.nextSibling) {
                    nextSib = after.nextSibling;
                }
                if (nextSib) {
                    nextSib.parentNode.insertBefore(el, nextSib);
                }
            }
            tmp.addClass(this.CLASS_PREFIX + '-' + tmp.get('value'));
            var icon = document.createElement('span');
            icon.className = this.CLASS_PREFIX + '-icon';
            tmp.get('element').insertBefore(icon, tmp.get('firstChild'));
            //Replace the Button HTML Element with an a href
            var a = document.createElement('a');
            a.innerHTML = tmp._button.innerHTML;
            a.href = '#';
            Event.on(a, 'click', function(ev) {
                Event.stopEvent(ev);
            });
            tmp._button.parentNode.replaceChild(a, tmp._button);
            tmp._button = a;

            if (oButton.type == 'select') {
                tmp.addClass(this.CLASS_PREFIX + '-select');
            }
            if (oButton.type == 'spin') {
                if (!Lang.isArray(oButton.range)) {
                    oButton.range = [ 10, 100 ];
                }
                this._makeSpinButton(tmp, oButton);
            }

            tmp.get('element').setAttribute('title', tmp.get('label'));

            if (oButton.type != 'spin') {
                if (_oButton.menu instanceof YAHOO.widget.Overlay) {
                    var showPicker = function(ev) {
                        var exec = true;
                        if (ev.keyCode && (ev.keyCode == 9)) {
                            exec = false;
                        }
                        if (exec) {
                            this._colorPicker._button = oButton.value;
                            var menuEL = tmp.getMenu().element;
                            if (menuEL.style.visibility == 'hidden') {
                                tmp.getMenu().show();
                            } else {
                                tmp.getMenu().hide();
                            }
                        }
                        YAHOO.util.Event.stopEvent(ev);
                    };
                    tmp.on('mousedown', showPicker, oButton, this);
                    tmp.on('keydown', showPicker, oButton, this);
                    
                } else if ((oButton.type != 'menu') && (oButton.type != 'select')) {
                    tmp.on('keypress', this._buttonClick, oButton, this);
                    tmp.on('mousedown', function(ev) {
                        this._buttonClick(ev, oButton);
                        YAHOO.util.Event.stopEvent(ev);
                    }, oButton, this);
                } else {
                    //Stop the mousedown event so we can trap the selection in the editor!
                    tmp.on('mousedown', function(ev) {
                        YAHOO.util.Event.stopEvent(ev);
                    });
                    tmp.on('click', function(ev) {
                        YAHOO.util.Event.stopEvent(ev);
                    });
                    var self = this;
                    //Hijack the mousedown event in the menu and make it fire a button click..
                    tmp.getMenu().mouseDownEvent.subscribe(function(ev, args) {
                        YAHOO.log('mouseDownEvent', 'warn', 'Toolbar');
                        var oMenu = args[1];
                        YAHOO.util.Event.stopEvent(args[0]);
                        tmp._onMenuClick(args[0], tmp);
                        if (!oButton.menucmd) {
                            oButton.menucmd = oButton.value;
                        }
                        oButton.value = ((oMenu.value) ? oMenu.value : oMenu._oText.nodeValue);
                        self._buttonClick.call(self, args[1], oButton);
                        tmp._hideMenu();
                        return false;
                    });
                    tmp.getMenu().clickEvent.subscribe(function(ev, args) {
                        YAHOO.log('clickEvent', 'warn', 'Toolbar');
                        YAHOO.util.Event.stopEvent(args[0]);
                    });
                }
            } else {
                //Stop the mousedown event so we can trap the selection in the editor!
                tmp.on('mousedown', function(ev) {
                    YAHOO.util.Event.stopEvent(ev);
                });
                tmp.on('click', function(ev) {
                    YAHOO.util.Event.stopEvent(ev);
                });
            }
            if (this.browser.ie) {
                //Add a couple of new events for IE
                tmp.DOM_EVENTS.focusin = true;
                tmp.DOM_EVENTS.focusout = true;
                
                //Stop them so we don't loose focus in the Editor
                tmp.on('focusin', function(ev) {
                    YAHOO.util.Event.stopEvent(ev);
                }, oButton, this);
                
                tmp.on('focusout', function(ev) {
                    YAHOO.util.Event.stopEvent(ev);
                }, oButton, this);
                tmp.on('click', function(ev) {
                    YAHOO.util.Event.stopEvent(ev);
                }, oButton, this);
            }
            if (this.browser.webkit) {
                //This will keep the document from gaining focus and the editor from loosing it..
                //Forcefully remove the focus calls in button!
                tmp.hasFocus = function() {
                    return true;
                }
            }
            this._buttonList[this._buttonList.length] = tmp;
            if ((oButton.type == 'menu') || (oButton.type == 'split') || (oButton.type == 'select')) {
                if (Lang.isArray(oButton.menu)) {
                    YAHOO.log('Button type is (' + oButton.type + '), doing extra renderer work.', 'info', 'Toolbar');
                    var menu = tmp.getMenu();
                    menu.renderEvent.subscribe(_addMenuClasses, tmp);
                    if (oButton.renderer) {
                        menu.renderEvent.subscribe(oButton.renderer, tmp);
                    }
                }
            }
            return oButton;
        },
        /**
        * @method addSeparator
        * @description Add a new button separator to the toolbar.
        * @param {HTMLElement} cont Optional HTML element to insert this button into.
        * @param {HTMLElement} after Optional HTML element to insert this button after in the DOM.
        */
        addSeparator: function(cont, after) {
            if (!this.get('element')) {
                this._queue[this._queue.length] = ['addSeparator', arguments];
                return false;
            }
            var sepCont = ((cont) ? cont : this.get('cont'));
            if (!this.get('element')) {
                this._queue[this._queue.length] = ['addSeparator', arguments];
                return false;
            }
            if (this._sepCount == null) {
                this._sepCount = 0;
            }
            if (!this._sep) {
                YAHOO.log('Separator does not yet exist, creating', 'info', 'Toolbar');
                this._sep = document.createElement('SPAN');
                Dom.addClass(this._sep, this.CLASS_SEPARATOR);
                this._sep.innerHTML = '|';
            }
            YAHOO.log('Separator does exist, cloning', 'info', 'Toolbar');
            var _sep = this._sep.cloneNode(true);
            this._sepCount++;
            Dom.addClass(_sep, this.CLASS_SEPARATOR + '-' + this._sepCount);
            if (after) {
                var nextSib = null;
                if (after.get) {
                    nextSib = after.get('element').nextSibling;
                } else if (after.nextSibling) {
                    nextSib = after.nextSibling;
                } else {
                    nextSib = after;
                }
                if (nextSib) {
                    if (nextSib == after) {
                        nextSib.parentNode.appendChild(_sep);
                    } else {
                        nextSib.parentNode.insertBefore(_sep, nextSib);
                    }
                }
            } else {
                sepCont.appendChild(_sep);
            }
            return _sep;
        },
        /**
        * @method _createColorPicker
        * @private
        * @description Creates the core DOM reference to the color picker menu item.
        * @param {String} id the id of the toolbar to prefix this DOM container with.
        */
        _createColorPicker: function(id) {
            if (Dom.get(id + '_colors')) {
               Dom.get(id + '_colors').parentNode.removeChild(Dom.get(id + '_colors'));
            }
            var picker = document.createElement('div');
            picker.className = 'yui-toolbar-colors';
            picker.id = id + '_colors';
            picker.style.display = 'none';
            Event.on(window, 'load', function() {
                document.body.appendChild(picker);
            }, this, true);

            this._colorPicker = picker;

            var html = '';
            for (var i in this._colorData) {
                if (Lang.hasOwnProperty(this._colorData, i)) {
                    html += '<a style="background-color: ' + i + '" href="#">' + i.replace('#', '') + '</a>';
                }
            }
            html += '<span><em>X</em><strong></strong></span>';
            picker.innerHTML = html;
            var em = picker.getElementsByTagName('em')[0];
            var strong = picker.getElementsByTagName('strong')[0];

            Event.on(picker, 'mouseover', function(ev) {
                var tar = Event.getTarget(ev);
                if (tar.tagName.toLowerCase() == 'a') {
                    em.style.backgroundColor = tar.style.backgroundColor;
                    strong.innerHTML = this._colorData['#' + tar.innerHTML] + '<br>' + tar.innerHTML;
                }
            }, this, true);
            Event.on(picker, 'focus', function(ev) {
                Event.stopEvent(ev);
            });
            Event.on(picker, 'click', function(ev) {
                Event.stopEvent(ev);
            });
            Event.on(picker, 'mousedown', function(ev) {
                Event.stopEvent(ev);
                var tar = Event.getTarget(ev);
                if (tar.tagName.toLowerCase() == 'a') {
                    this.fireEvent('colorPickerClicked', { type: 'colorPickerClicked', target: this, button: this._colorPicker._button, color: tar.innerHTML, colorName: this._colorData['#' + tar.innerHTML] } );
                    this.getButtonByValue(this._colorPicker._button).getMenu().hide();
                }
            }, this, true);
        },
        /**
        * @method _resetColorPicker
        * @private
        * @description Clears the currently selected color or mouseover color in the color picker.
        */
        _resetColorPicker: function() {
            var em = this._colorPicker.getElementsByTagName('em')[0];
            var strong = this._colorPicker.getElementsByTagName('strong')[0];
            em.style.backgroundColor = 'transparent';
            strong.innerHTML = '';
        },
        /**
        * @method _makeColorButton
        * @private
        * @description Called to turn a "color" button into a menu button with an Overlay for the menu.
        * @param {Object} _oButton <a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a> reference
        */
        _makeColorButton: function(_oButton) {
            if (!this._colorPicker) {   
                this._createColorPicker(this.get('id'));
            }
            _oButton.type = 'color';
            _oButton.menu = new YAHOO.widget.Overlay(this.get('id') + '_' + _oButton.value + '_menu', { visbile: false, position: 'absolute' });
            _oButton.menu.setBody('');
            _oButton.menu.render(this.get('cont'));
            _oButton.menu.beforeShowEvent.subscribe(function() {
                _oButton.menu.cfg.setProperty('zindex', 5); //Re Adjust the overlays zIndex.. not sure why.
                _oButton.menu.cfg.setProperty('context', [this.getButtonById(_oButton.id).get('element'), 'tl', 'bl']); //Re Adjust the overlay.. not sure why.
                //Move the DOM reference of the color picker to the Overlay that we are about to show.
                this._resetColorPicker();
                var _p = this._colorPicker;
                if (_p.parentNode) {
                    //if (_p.parentNode != _oButton.menu.body) {
                        _p.parentNode.removeChild(_p);
                    //}
                }
                _oButton.menu.setBody('');
                _oButton.menu.appendToBody(_p);
                this._colorPicker.style.display = 'block';
            }, this, true);
            return _oButton;
        },
        /**
        * @private
        * @method _makeSpinButton
        * @description Create a button similar to an OS Spin button.. It has an up/down arrow combo to scroll through a range of int values.
        * @param {Object} _button <a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a> reference
        * @param {Object} oButton Object literal containing the buttons initial config
        */
        _makeSpinButton: function(_button, oButton) {
            _button.addClass(this.CLASS_PREFIX + '-spinbutton');
            var self = this,
                _par = _button._button.parentNode.parentNode, //parentNode of Button Element for appending child
                range = oButton.range,
                _b1 = document.createElement('a'),
                _b2 = document.createElement('a');
                _b1.href = '#';
                _b2.href = '#';
            
            //Setup the up and down arrows
            _b1.className = 'up';
            _b1.title = this.STR_SPIN_UP;
            _b1.innerHTML = this.STR_SPIN_UP;
            _b2.className = 'down';
            _b2.title = this.STR_SPIN_DOWN;
            _b2.innerHTML = this.STR_SPIN_DOWN;

            //Append them to the container
            _par.appendChild(_b1);
            _par.appendChild(_b2);
            
            var label = YAHOO.lang.substitute(this.STR_SPIN_LABEL, { VALUE: _button.get('label') });
            _button.set('title', label);

            var cleanVal = function(value) {
                value = ((value < range[0]) ? range[0] : value);
                value = ((value > range[1]) ? range[1] : value);
                return value;
            };

            var br = this.browser;
            var tbar = false;
            var strLabel = this.STR_SPIN_LABEL;
            if (this._titlebar && this._titlebar.firstChild) {
                tbar = this._titlebar.firstChild;
            }
            
            var _intUp = function(ev) {
                YAHOO.util.Event.stopEvent(ev);
                if (!_button.get('disabled') && (ev.keyCode != 9)) {
                    var value = parseInt(_button.get('label'));
                    value++;
                    value = cleanVal(value);
                    _button.set('label', ''+value);
                    var label = YAHOO.lang.substitute(strLabel, { VALUE: _button.get('label') });
                    _button.set('title', label);
                    if (!br.webkit && tbar) {
                        //tbar.focus(); //We do this for accessibility, on the re-focus of the element, a screen reader will re-read the title that was just changed
                        //_button.focus();
                    }
                    self._buttonClick(ev, oButton);
                }
            };

            var _intDown = function(ev) {
                YAHOO.util.Event.stopEvent(ev);
                if (!_button.get('disabled') && (ev.keyCode != 9)) {
                    var value = parseInt(_button.get('label'));
                    value--;
                    value = cleanVal(value);

                    _button.set('label', ''+value);
                    var label = YAHOO.lang.substitute(strLabel, { VALUE: _button.get('label') });
                    _button.set('title', label);
                    if (!br.webkit && tbar) {
                        //tbar.focus(); //We do this for accessibility, on the re-focus of the element, a screen reader will re-read the title that was just changed
                        //_button.focus();
                    }
                    self._buttonClick(ev, oButton);
                }
            };

            var _intKeyUp = function(ev) {
                if (ev.keyCode == 38) {
                    _intUp(ev);
                } else if (ev.keyCode == 40) {
                    _intDown(ev);
                } else if (ev.keyCode == 107 && ev.shiftKey) {  //Plus Key
                    _intUp(ev);
                } else if (ev.keyCode == 109 && ev.shiftKey) {  //Minus Key
                    _intDown(ev);
                }
            };

            //Handle arrow keys..
            _button.on('keydown', _intKeyUp, this, true);

            //Listen for the click on the up button and act on it
            //Listen for the click on the down button and act on it
            Event.on(_b1, 'mousedown',function(ev) {
                Event.stopEvent(ev);
            }, this, true);
            Event.on(_b2, 'mousedown', function(ev) {
                Event.stopEvent(ev);
            }, this, true);
            Event.on(_b1, 'click', _intUp, this, true);
            Event.on(_b2, 'click', _intDown, this, true);
        },
        /**
        * @protected
        * @method _buttonClick
        * @description Click handler for all buttons in the toolbar.
        * @param {String} ev The event that was passed in.
        * @param {Object} info Object literal of information about the button that was clicked.
        */
        _buttonClick: function(ev, info) {
            var doEvent = true;
            
            if (ev && ev.type == 'keypress') {
                if (ev.keyCode == 9) {
                    doEvent = false;
                } else if ((ev.keyCode == 13) || (ev.keyCode == 0) || (ev.keyCode == 32)) {
                } else {
                    doEvent = false;
                }
            }

            if (doEvent) {
                if (info.value) {
                    YAHOO.log('fireEvent::' + info.value + 'Click', 'info', 'Toolbar');
                    this.fireEvent(info.value + 'Click', { type: info.value + 'Click', target: this.get('element'), button: info });
                }
                
                if (info.menucmd) {
                    YAHOO.log('fireEvent::' + info.menucmd + 'Click', 'info', 'Toolbar');
                    this.fireEvent(info.menucmd + 'Click', { type: info.menucmd + 'Click', target: this.get('element'), button: info });
                }
                
                YAHOO.log('fireEvent::buttonClick', 'info', 'Toolbar');
                this.fireEvent('buttonClick', { type: 'buttonClick', target: this.get('element'), button: info });

                if (info.type == 'select') {
                    var button = this.getButtonById(info.id);
                    var txt = info.value;
                    for (var i = 0; i < info.menu.length; i++) {
                        if (info.menu[i].value == info.value) {
                            txt = info.menu[i].text;
                            break;
                        }
                    }
                    button.set('label', '<span class="yui-toolbar-' + info.menucmd + '-' + (info.value).replace(/ /g, '-').toLowerCase() + '">' + txt + '</span>');
                    var _items = button.getMenu().getItems();
                    for (var m = 0; m < _items.length; m++) {
                        if (_items[m].value.toLowerCase() == info.value.toLowerCase()) {
                            _items[m].cfg.setProperty('checked', true);
                        } else {
                            _items[m].cfg.setProperty('checked', false);
                        }
                    }
                }
            }
            if (ev) {
                Event.stopEvent(ev);
            }
        },
        /**
        * @method getButtonById
        * @description Gets a button instance from the toolbar by is Dom id.
        * @param {String} id The Dom id to query for.
        * @return {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>}
        */
        getButtonById: function(id) {
            var len = this._buttonList.length;
            for (var i = 0; i < len; i++) {
                if (this._buttonList[i].get('id') == id) {
                    return this._buttonList[i];
                }
            }
            return false;
        },
        /**
        * @method getButtonByValue
        * @description Gets a button instance or a menuitem instance from the toolbar by it's value.
        * @param {String} value The button value to query for.
        * @return {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a> or <a href="YAHOO.widget.MenuItem.html">YAHOO.widget.MenuItem</a>}
        */
        getButtonByValue: function(value) {
            var _buttons = this.get('buttons');
            var len = _buttons.length;
            for (var i = 0; i < len; i++) {
                if (_buttons[i].group != undefined) {
                    for (var m = 0; m < _buttons[i].buttons.length; m++) {
                        if ((_buttons[i].buttons[m].value == value) || (_buttons[i].buttons[m].menucmd == value)) {
                            return this.getButtonById(_buttons[i].buttons[m].id);
                        }
                        if (_buttons[i].buttons[m].menu) { //Menu Button, loop through the values
                            for (var s = 0; s < _buttons[i].buttons[m].menu.length; s++) {
                                if (_buttons[i].buttons[m].menu[s].value == value) {
                                    return this.getButtonById(_buttons[i].buttons[m].id);
                                }
                            }
                        }
                    }
                } else {
                    if ((_buttons[i].value == value) || (_buttons[i].menucmd == value)) {
                        return this.getButtonById(_buttons[i].id);
                    }
                    if (_buttons[i].menu) { //Menu Button, loop through the values
                        for (var s = 0; s < _buttons[i].menu.length; s++) {
                            if (_buttons[i].menu[s].value == value) {
                                return this.getButtonById(_buttons[i].id);
                            }
                        }
                    }
                }
            }
            return false;
        },
        /**
        * @method getButtonByIndex
        * @description Gets a button instance from the toolbar by is index in _buttonList.
        * @param {Number} index The index of the button in _buttonList.
        * @return {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>}
        */
        getButtonByIndex: function(index) {
            if (this._buttonList[index]) {
                return this._buttonList[index];
            } else {
                return false;
            }
        },
        /**
        * @method getButtons
        * @description Returns an array of buttons in the current toolbar
        * @return {Array}
        */
        getButtons: function() {
            return this._buttonList;
        },
        /**
        * @method disableButton
        * @description Disables a button in the toolbar.
        * @param {String/Number} button Disable a button by it's id or index.
        * @return {Boolean}
        */
        disableButton: function(button) {
            if (Lang.isString(button)) {
                var button = this.getButtonById(button);
            }
            if (Lang.isNumber(button)) {
                var button = this.getButtonByIndex(button);
            }
            if (button instanceof YAHOO.widget.Button) {
                button.set('disabled', true);
            } else {
                return false;
            }
        },
        /**
        * @method enableButton
        * @description Enables a button in the toolbar.
        * @param {String/Number} button Enable a button by it's id or index.
        * @return {Boolean}
        */
        enableButton: function(button) {
            if (Lang.isString(button)) {
                var button = this.getButtonById(button);
            }
            if (Lang.isNumber(button)) {
                var button = this.getButtonByIndex(button);
            }
            if (button instanceof YAHOO.widget.Button) {
                button.set('disabled', false);
            } else {
                return false;
            }
        },
        /**
        * @method selectButton
        * @description Selects a button in the toolbar.
        * @param {String/Number} button select a button by it's id or index.
        * @return {Boolean}
        */
        selectButton: function(button, value) {
            if (button) {
                if (Lang.isString(button)) {
                    var button = this.getButtonById(button);
                }
                if (Lang.isNumber(button)) {
                    var button = this.getButtonByIndex(button);
                }
                if (button instanceof YAHOO.widget.Button) {
                    button.addClass('yui-button-selected');
                    button.addClass('yui-button-' + button.get('value') + '-selected');
                    if (value) {
                        var _items = button.getMenu().getItems();
                        for (var m = 0; m < _items.length; m++) {
                            if (_items[m].value == value) {
                                _items[m].cfg.setProperty('checked', true);
                                button.set('label', '<span class="yui-toolbar-' + button.get('value') + '-' + (value).replace(/ /g, '-').toLowerCase() + '">' + _items[m]._oText.nodeValue + '</span>');
                            } else {
                                _items[m].cfg.setProperty('checked', false);
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        },
        /**
        * @method deselectButton
        * @description Deselects a button in the toolbar.
        * @param {String/Number} button Deselect a button by it's id or index.
        * @return {Boolean}
        */
        deselectButton: function(button) {
            if (Lang.isString(button)) {
                var button = this.getButtonById(button);
            }
            if (Lang.isNumber(button)) {
                var button = this.getButtonByIndex(button);
            }
            if (button instanceof YAHOO.widget.Button) {
                button.removeClass('yui-button-selected');
                button.removeClass('yui-button-' + button.get('value') + '-selected');
                button.removeClass('yui-button-hover');
            } else {
                return false;
            }
        },
        /**
        * @method deselectAllButtons
        * @description Deselects all buttons in the toolbar.
        * @return {Boolean}
        */
        deselectAllButtons: function() {
            var len = this._buttonList.length;
            for (var i = 0; i < len; i++) {
                this.deselectButton(this._buttonList[i]);
            }
        },
        /**
        * @method destroyButton
        * @description Destroy a button in the toolbar.
        * @param {String/Number} button Destroy a button by it's id or index.
        * @return {Boolean}
        */
        destroyButton: function(button) {
            if (Lang.isString(button)) {
                var button = this.getButtonById(button);
            }
            if (Lang.isNumber(button)) {
                var button = this.getButtonByIndex(button);
            }
            if (button instanceof YAHOO.widget.Button) {
                var id = button.get('id');
                button.destroy();

                var len = this._buttonList.length;
                for (var i = 0; i < len; i++) {
                    if (this._buttonList[i].get('id') == id) {
                        this._buttonList[i] = null;
                    }
                }
            } else {
                return false;
            }

        },
        /**
        * @method destroy
        * @description Destroys the toolbar, all of it's elements and objects.
        * @return {Boolean}
        */
        destroy: function() {
            this.get('element').innerHTML = '';
            this.get('element').className = '';
            //Brutal Object Destroy
            for (var i in this) {
                if (Lang.hasOwnProperty(this, i)) {
                    this[i] = null;
                }
            }
            return true;
        },
        /**
        * @method toString
        * @description Returns a string representing the toolbar.
        * @return {String}
        */
        toString: function() {
            return 'Toolbar (#' + this.get('element').id + ') with ' + this._buttonList.length + ' buttons.';
        }
    });
/**
* @event buttonClick
* @param {Object} o The object passed to this handler is the button config used to create the button.
* @description Fires when any botton receives a click event. Passes back a single object representing the buttons config object. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event valueClick
* @param {Object} o The object passed to this handler is the button config used to create the button.
* @description This is a special dynamic event that is created and dispatched based on the value property
* of the button config. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* Example:
* <code><pre>
* buttons : [
*   { type: 'button', value: 'test', value: 'testButton' }
* ]</pre>
* </code>
* With the valueClick event you could subscribe to this buttons click event with this:
* tbar.in('testButtonClick', function() { alert('test button clicked'); })
* @type YAHOO.util.CustomEvent
*/
/**
* @event toolbarExpanded
* @description Fires when the toolbar is expanded via the collapse button. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event toolbarCollapsed
* @description Fires when the toolbar is collapsed via the collapse button. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
})();
/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
*/
/**
 * @module editor
 * @description <p>The Rich Text Editor is a UI control that replaces a standard HTML textarea; it allows for the rich formatting of text content, including common structural treatments like lists, formatting treatments like bold and italic text, and drag-and-drop inclusion and sizing of images. The Rich Text Editor's toolbar is extensible via a plugin architecture so that advanced implementations can achieve a high degree of customization.</p>
 * @namespace YAHOO.widget
 * @requires yahoo, dom, element, event, toolbar, container, menu, button
 * @optional dragdrop, animation
 * @beta
 */

(function() {
var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    Lang = YAHOO.lang,
    DD = YAHOO.util.DD,
    Toolbar = YAHOO.widget.Toolbar;


    /**
     * The Rich Text Editor is a UI control that replaces a standard HTML textarea; it allows for the rich formatting of text content, including common structural treatments like lists, formatting treatments like bold and italic text, and drag-and-drop inclusion and sizing of images. The Rich Text Editor's toolbar is extensible via a plugin architecture so that advanced implementations can achieve a high degree of customization.
     * @constructor
     * @class Editor
     * @extends YAHOO.util.Element
     * @param {String/HTMLElement} el The textarea element to turn into an editor.
     * @param {Object} attrs Object liternal containing configuration parameters.
    */
    
    YAHOO.widget.Editor = function(el, attrs) {
        YAHOO.log('Editor Initalizing', 'info', 'Editor');

        var oConfig = {
            element: null,
            attributes: (attrs || {})
        }

        if (Lang.isString(el)) {
            oConfig.attributes.textarea = Dom.get(el);
        }
        
        var element_cont = document.createElement('DIV');
        oConfig.attributes.element_cont = new YAHOO.util.Element(element_cont, {
            id: oConfig.attributes.textarea.id + '_container'
        });
        oConfig.attributes.element_cont.setStyle('display', 'none');

        oConfig.element = oConfig.attributes.textarea;

        var div = document.createElement('div');
        oConfig.attributes.element_cont.appendChild(div);
        
        if (!oConfig.attributes.toolbar_cont) {
            oConfig.attributes.toolbar_cont = document.createElement('DIV');
            oConfig.attributes.toolbar_cont.id = oConfig.attributes.textarea.id + '_toolbar';
            div.appendChild(oConfig.attributes.toolbar_cont);
        }
        
        if (!oConfig.attributes.iframe) {
            oConfig.attributes.iframe = _createIframe(oConfig.attributes.textarea.id);
            var editorWrapper = document.createElement('DIV');
            editorWrapper.appendChild(oConfig.attributes.iframe.get('element'));
            div.appendChild(editorWrapper);
        }

        Event.onDOMReady(function() {
            this.DOMReady = true;
            this.fireQueue();
        }, this, true);

        YAHOO.widget.Editor.superclass.constructor.call(this, oConfig.element, oConfig.attributes);
    }

    /**
    * @private _cleanClassName
    * @description Makes a useable classname from dynamic data, by dropping it to lowercase and replacing spaces with -'s.
    * @param {String} str The classname to clean up
    * @returns {String}
    */
    function _cleanClassName(str) {
        return str.replace(/ /g, '-').toLowerCase();
    }

    /**
    * @private _createIframe
    * @description Creates the DOM and YUI Element for the iFrame editor area.
    * @param {String} id The string ID to prefix the iframe with
    * @returns {Object} iFrame object
    */
    function _createIframe(id) {
        var ifrmID = id + '_editor';
        var ifrmDom = document.createElement('iframe');
        ifrmDom.id = ifrmID;
        var config = {
            border: '0',
            frameBorder: '0',
            marginWidth: '0',
            marginHeight: '0',
            leftMargin: '0',
            topMargin: '0',
            allowTransparency: 'true',
            width: '100%',
            src: 'javascript:false'
        }
        for (var i in config) {
            if (Lang.hasOwnProperty(config, i)) {
                ifrmDom.setAttribute(i, config[i]);
            }
        }

        var ifrm = new YAHOO.util.Element(ifrmDom);
        ifrm.setStyle('zIndex', '-1');
        return ifrm;
    }

    YAHOO.extend(YAHOO.widget.Editor, YAHOO.util.Element, {
        /**
        * @property DOMReady
        * @private
        * @description Flag to determine if DOM is ready or not
        * @type Boolean
        */
        DOMReady: null,
        /**
        * @property _selection
        * @private
        * @description Holder for caching iframe selections
        * @type Object
        */
        _selection: null,
        /**
        * @property _mask
        * @private
        * @description DOM Element holder for the editor Mask when disabled
        * @type Object
        */
        _mask: null,
        /**
        * @property _showingHiddenElements
        * @private
        * @description Status of the hidden elements button
        * @type Boolean
        */
        _showingHiddenElements: null,
        /**
        * @property currentWindow
        * @description A reference to the currently open EditorWindow
        * @type Object
        */
        currentWindow: null,
        /**
        * @property currentEvent
        * @description A reference to the current editor event
        * @type Event
        */
        currentEvent: null,
        /**
        * @property operaEvent
        * @private
        * @description setTimeout holder for Opera and Image DoubleClick event..
        * @type Object
        */
        operaEvent: null,
        /**
        * @property currentFont
        * @description A reference to the last font selected from the Toolbar
        * @type HTMLElement
        */
        currentFont: null,
        /**
        * @property currentElement
        * @description A reference to the current working element in the editor
        * @type Array
        */
        currentElement: [],
        /**
        * @property dompath
        * @description A reference to the dompath container for writing the current working dom path to.
        * @type HTMLElement
        */
        dompath: null,
        /**
        * @property beforeElement
        * @description A reference to the H2 placed before the editor for Accessibilty.
        * @type HTMLElement
        */
        beforeElement: null,
        /**
        * @property afterElement
        * @description A reference to the H2 placed after the editor for Accessibilty.
        * @type HTMLElement
        */
        afterElement: null,
        /**
        * @property invalidHTML
        * @description Contains a list of HTML elements that are invalid inside the editor. They will be removed when they are found.
        * @type Object
        */
        invalidHTML: {
            form: true,
            input: true,
            button: true,
            select: true,
            link: true,
            html: true,
            body: true,
            script: true,
            style: true,
            textarea: true
        },
        /**
        * @property toolbar
        * @description Local property containing the <a href="YAHOO.widget.Toolbar.html">YAHOO.widget.Toolbar</a> instance
        * @type <a href="YAHOO.widget.Toolbar.html">YAHOO.widget.Toolbar</a>
        */
        toolbar: null,
        /**
        * @private
        * @property _contentTimer
        * @description setTimeout holder for documentReady check
        */
        _contentTimer: null,
        /**
        * @private
        * @property _contentTimerCounter
        * @description Counter to check the number of times the body is polled for before giving up
        * @type Number
        */
        _contentTimerCounter: 0,
        /**
        * @private
        * @property _disabled
        * @description The Toolbar items that should be disabled if there is no selection present in the editor.
        * @type Array
        */
        _disabled: [ 'createlink', 'forecolor', 'backcolor', 'fontname', 'fontsize', 'superscript', 'subscript', 'removeformat', 'heading', 'indent', 'outdent' ],
        /**
        * @private
        * @property _alwaysDisabled
        * @description The Toolbar items that should ALWAYS be disabled event if there is a selection present in the editor.
        * @type Object
        */
        _alwaysDisabled: { },
        /**
        * @private
        * @property _alwaysEnabled
        * @description The Toolbar items that should ALWAYS be enabled event if there isn't a selection present in the editor.
        * @type Object
        */
        _alwaysEnabled: { hiddenelements: true },
        /**
        * @private
        * @property _semantic
        * @description The Toolbar commands that we should attempt to make tags out of instead of using styles.
        * @type Object
        */
        _semantic: { 'bold': true, 'italic' : true, 'underline' : true },
        /**
        * @private
        * @property _tag2cmd
        * @description A tag map of HTML tags to convert to the different types of commands so we can select the proper toolbar button.
        * @type Object
        */
        _tag2cmd: {
            'b': 'bold',
            'strong': 'bold',
            'i': 'italic',
            'em': 'italic',
            'u': 'underline',
            'blockquote': 'formatblock',
            'sup': 'superscript',
            'sub': 'subscript',
            'img': 'insertimage',
            'a' : 'createlink',
            'ul' : 'insertunorderedlist',
            'ol' : 'insertorderedlist',
            'indent' : 'indent',
            'outdent' : 'outdent'
        },
        /**
        * @private
        * @method _getDoc
        * @description Get the Document of the IFRAME
        * @return {Object}
        */
        _getDoc: function() {
            //if (this.get && this.get('iframe') && this.get('iframe').get && this.get('iframe').get('element') && this.get('iframe').get('element').contentWindow && this.get('iframe').get('element').contentWindow.document) {
            var value = false;
            if (this.get) {
                if (this.get('iframe')) {
                    if (this.get('iframe').get) {
                        if (this.get('iframe').get('element')) {
                            try {
                                if (this.get('iframe').get('element').contentWindow) {
                                    if (this.get('iframe').get('element').contentWindow.document) {
                                        value = this.get('iframe').get('element').contentWindow.document;
                                    }
                                }
                            } catch (e) {}
                        }
                    }
                }
            }
            return value;
        },
        /**
        * @private
        * @method _getWindow
        * @description Get the Window of the IFRAME
        * @return {Object}
        */
        _getWindow: function() {
            return this.get('iframe').get('element').contentWindow;
        },
        /**
        * @private
        * @method _focusWindow
        * @description Attempt to set the focus of the iframes window.
        * @param {Boolean} onLoad Safari needs some special care to set the cursor in the iframe
        */
        _focusWindow: function(onLoad) {
            if (this.browser.webkit) {
                if (onLoad) {
                    /**
                    * @knownissue Safari Cursor Position
                    * @browser Safari 2.x
                    * @description Can't get Safari to place the cursor at the beginning of the text..
                    * This workaround at least set's the toolbar into the proper state.
                    */
                    this._getSelection().setBaseAndExtent(this._getDoc().body, 0, this._getDoc().body, 1);
                    this._getSelection().collapse(false);   
                } else {
                    this._getSelection().setBaseAndExtent(this._getDoc().body, 1, this._getDoc().body, 1);
                    this._getSelection().collapse(false);   
                }
                this._getWindow().focus();
                //Check for.webkit3
                if (this._getDoc().queryCommandEnabled('insertimage')) {
                    this.browser.webkit3 = true;
                }
            } else {
                this._getWindow().focus();
            }
        },
        /**
        * @private
        * @method _hasSelection
        * @description Determines if there is a selection in the editor document.
        * @returns {Boolean}
        */
        _hasSelection: function() {
            var sel = this._getSelection();
            var range = this._getRange();
            var hasSel = false;

            //Internet Explorer
            if (this.browser.ie || this.browser.opera) {
                if (range.text) {
                    hasSel = true;
                }
                if (range.html) {
                    hasSel = true;
                }
            } else {
                if ((sel != '') && (sel != undefined)) {
                    hasSel = true;
                }
            }
            return hasSel;
        },
        /**
        * @private
        * @method _getSelection
        * @description Handles the different selection objects across the A-Grade list.
        * @returns {Object} Selection Object
        */
        _getSelection: function() {
            var _sel = null;
            if (this._getDoc() && this._getWindow()) {
                if (this._getDoc().selection) {
                    _sel = this._getDoc().selection;
                } else {
                    _sel = this._getWindow().getSelection();
                }
                //Handle Safari's lack of Selection Object
                if (this.browser.webkit) {
                    if (_sel.baseNode) {
                            this._selection = new Object();
                            this._selection.baseNode = _sel.baseNode;
                            this._selection.baseOffset = _sel.baseOffset;
                            this._selection.extentNode = _sel.extentNode;
                            this._selection.extentOffset = _sel.extentOffset;
                    } else if (this._selection != null) {
                        _sel = this._getWindow().getSelection();
                        _sel.setBaseAndExtent(
                            this._selection.baseNode,
                            this._selection.baseOffset,
                            this._selection.extentNode,
                            this._selection.extentOffset
                        );
                        this._selection = null;
                    }
                }
            }
            return _sel;
        },
        /**
        * @private
        * @method _getRange
        * @description Handles the different range objects across the A-Grade list.
        * @returns {Object} Range Object
        */
        _getRange: function(sel) {
            var sel = this._getSelection();

            if (sel == null) {
                return null;
            }

            if (this.browser.webkit && !sel.getRangeAt) {
                var _range = this._getDoc().createRange();
                try {
                    _range.setStart(sel.anchorNode, sel.anchorOffset);
                    _range.setEnd(sel.focusNode, sel.focusOffset);
                } catch (e) {
                    _range = this._getWindow().getSelection()+'';
                }
                return _range;
            }

            if (this.browser.ie || this.browser.opera) {
                return sel.createRange();
            }

            if (sel.rangeCount > 0) {
                return sel.getRangeAt(0);
            }
            return null;
        },
        /**
        * @private
        * @method _setDesignMode
        * @description Sets the designMode of the iFrame document.
        * @param {String} state This should be either on or off
        */
        _setDesignMode: function(state) {
            try {
                this._getDoc().designMode = state;
            } catch(e) { }
        },
        /**
        * @private
        * @method _toggleDesignMode
        * @description Toggles the designMode of the iFrame document on and off.
        * @returns {String} The state that it was set to.
        */
        _toggleDesignMode: function() {
            var _dMode = this._getDoc().designMode,
                _state = 'on';
            if (_dMode == 'on') {
                _state = 'off';
            }
            this._setDesignMode(_state);
            return _state;
        },
        /**
        * @private
        * @method _initEditor
        * @description This method is fired from _checkLoaded when the document is ready. It turns on designMode and set's up the listeners.
        */
        _initEditor: function() {
            YAHOO.log('editorLoaded', 'info', 'Editor');
            if (this.browser.ie) {
                this._getDoc().body.style.margin = '0';
            }
            this._setDesignMode('on');
            
            this.toolbar.on('buttonClick', this._handleToolbarClick, this, true);
            //Setup Listeners on iFrame
            Event.addListener(this._getDoc(), 'mouseup', this._handleMouseUp, this, true);
            Event.addListener(this._getDoc(), 'mousedown', this._handleMouseDown, this, true);
            Event.addListener(this._getDoc(), 'click', this._handleClick, this, true);
            Event.addListener(this._getDoc(), 'dblclick', this._handleDoubleClick, this, true);
            Event.addListener(this._getDoc(), 'keypress', this._handleKeyPress, this, true);
            Event.addListener(this._getDoc(), 'keyup', this._handleKeyUp, this, true);
            Event.addListener(this._getDoc(), 'keydown', this._handleKeyDown, this, true);
            this.toolbar.set('disabled', false);
            this.fireEvent('editorContentLoaded', { type: 'editorLoaded', target: this });
            if (this.get('dompath')) {
                var self = this;
                window.setTimeout(function() {
                    self._writeDomPath.call(self);
                }, 150);
            }
        },
        /**
        * @private
        * @method _checkLoaded
        * @description Called from a setTimeout loop to check if the iframes body.onload event has fired, then it will init the editor.
        */
        _checkLoaded: function() {
            this._contentTimerCounter++;
            if (this._contentTimer) {
                window.clearTimeout(this._contentTimer);
            }
            if (this._contentTimerCounter > 250) {
                alert('ERROR: Body Did Not load');
                return false;
            }
            if (this._getDoc() && this._getDoc().body && (this._getDoc().body._rteLoaded == true)) {
                //The onload event has fired, clean up after ourselves and fire the _initEditor method
                /*
                if (!this.browser.ie) {
                    //IE Doesn't like this..
                    delete this._getDoc().body._rteLoaded;
                    this._getDoc().body.removeAttribute('onload');
                }
                */
                this._initEditor();
            } else {
                var self = this;
                this._contentTimer = window.setTimeout(function() {
                    self._checkLoaded.call(self);
                }, 20);
            }
        },
        /**
        * @private
        * @method _setInitialContent
        * @description This method will open the iframes content document and write the textareas value into it, then start the body.onload checking.
        */
        _setInitialContent: function() {
            YAHOO.log('Body of editor populated with contents of the text area', 'info', 'Editor');
            var title = this.STR_TITLE;
            var html = this.get('html');
            html = html.replace('{TITLE}', title);
            html = html.replace('{CONTENT}', this.get('textarea').value);
            html = html.replace('{CSS}', this.get('css'));
            html = html.replace('{HIDDEN_CSS}', this.get('hiddencss'));

            this._getDoc().open();
            this._getDoc().write(html);
            this._getDoc().close();
            
            this._checkLoaded();   
        },
        /**
        * @private
        * @method _setMarkupType
        * @param {String} action The action to take. Possible values are: css, default or semantic
        * @description This method will turn on/off the useCSS execCommand.
        */
        _setMarkupType: function(action) {
            switch (this.get('markup')) {
                case 'css':
                    this._setEditorStyle(true);
                    break;
                case 'default':
                    this._setEditorStyle(false);
                    break;
                case 'semantic':
                    if (this._semantic[action]) {
                        this._setEditorStyle(false);
                    } else {
                        this._setEditorStyle(true);
                    }
                    break;
            }
        },
        /**
        * Set the editor to use CSS instead of HTML
        * @param {Booleen} stat True/False
        */
        _setEditorStyle: function(stat) {
            try {
                this._getDoc().execCommand('useCSS', false, !stat);
            } catch (ex) {
            }
        },
        /**
        * @private
        * @method _getSelectedElement
        * @description This method will attempt to locate the element that was last interacted with, either via selection, location or event.
        * @returns {HTMLElement} The currently selected element.
        */
        _getSelectedElement: function() {
            var doc = this._getDoc();
            if (this.browser.ie) {
                var range = this._getRange(), elm = null;
                if (range) {
                    elm = range.item ? range.item(0) : range.parentElement();
                    if (elm == doc.body) {
                        elm = null;
                    }
                }
            } else {
                var sel = this._getSelection(),
                    range = this._getRange(),
                    elm = null;
                if (!sel || !range) {
                    return null;
                }
                if (sel != '') {
                    if (sel.anchorNode && (sel.anchorNode.nodeType == 3)) {
                        if (sel.anchorNode.parentNode) { //next check parentNode
                            elm = sel.anchorNode.parentNode;
                        }
                        if (sel.anchorNode.nextSibling != sel.focusNode.nextSibling) {
                            elm = sel.anchorNode.nextSibling;
                        }
                    }
                    
                    if (elm && elm.tagName && (elm.tagName.toLowerCase() == 'br')) {
                        elm = null;
                    }
                
                    if (!elm) {
                        elm = range.commonAncestorContainer;
                        if (!range.collapsed) {
                            if (range.startContainer == range.endContainer) {
                                if (range.startOffset - range.endOffset < 2) {
                                    if (range.startContainer.hasChildNodes()) {
                                        elm = range.startContainer.childNodes[range.startOffset];
                                    }
                                }
                            }
                        }
                    }
                    //Safari Fix
                    if (!elm) {
                        if (this.currentEvent) {
                            //elm = Event.getTarget(this.currentEvent);
                        }
                    }
                }
            }
            if (!elm && (this.currentElement[0] || this.currentEvent)) {
                if (this.currentEvent && (this.currentEvent.keyCode == undefined) && Event.getTarget(this.currentEvent)) {
                    elm = Event.getTarget(this.currentEvent);
                } else if (this.currentEvent && (this.currentEvent.keyCode != undefined) && Event.getTarget(this.currentEvent)) {
                } else {
                    elm = this.currentElement[0];
                }
            } else if ((elm == this._getDoc().body) && this.currentElement[0] && !this._hasSelection()) {
                elm = this.currentElement[0];
            }

            if (this.browser.opera || this.browser.webkit) {
                if (this.currentEvent && !elm) {
                    elm = Event.getTarget(this.currentEvent);
                }
            }

            if (!elm || !elm.tagName) {
                elm = doc.body;
            }
            if (elm && elm.tagName && elm.tagName.toLowerCase() == 'html') {
                //Safari sometimes gives us the HTML node back..
                elm = doc.body;
            }
            
            return elm;
        },
        /**
        * @private
        * @method _getDomPath
        * @description This method will attempt to build the DOM path from the currently selected element.
        * @returns {Array} An array of node references that will create the DOM Path.
        */
        _getDomPath: function() {
			var el = this._getSelectedElement();
			var domPath = [];
			
			while (el!= null) {
                if (el.ownerDocument != this._getDoc()) {
                    return false;
                }
                //Check to see if we get el.nodeName and nodeType
                if (el.nodeName && (el.nodeType == 1)) {
                    domPath[domPath.length] = el;
                }

                if (el.nodeName.toUpperCase() == "BODY") {
				    break;
                }

				el = el.parentNode;
			}
            if (domPath.length == 0) {
                if (this._getDoc() && this._getDoc().body) {
                    domPath[0] = this._getDoc().body;
                }
            }
            return domPath.reverse();
        },
        /**
        * @private
        * @method _writeDomPath
        * @description Write the current DOM path out to the dompath container below the editor.
        */
        _writeDomPath: function() { 
            var path = this._getDomPath(),
                pathArr = [];
            for (var i = 0; i < path.length; i++) {
                var tag = path[i].tagName.toLowerCase();
                if ((tag == 'ol') && (path[i].type)) {
                    tag += ':' + path[i].type;
                }
                if (Dom.hasClass(path[i], 'yui-tag')) {
                    tag = path[i].getAttribute('tag');
                }
                if ((this.get('markup') == 'semantic')) {
                    switch (tag) {
                        case 'b': tag = 'strong'; break;
                        case 'i': tag = 'em'; break;
                    }
                }
                if (!Dom.hasClass(path[i], 'yui-non')) {
                    if (Dom.hasClass(path[i], 'yui-tag')) {
                        var pathStr = tag;
                        if (tag == 'a') {
                            if (path[i].getAttribute('href')) {
                                pathStr += ':' + path[i].getAttribute('href').replace('mailto:', '').replace('http:/'+'/', '').replace('https:/'+'/', ''); //May need to add others here ftp
                            }
                        }
                    } else {
                        var classPath = ((path[i].className != '') ? '.' + path[i].className.replace(/ /g, '.') : '');
                        if ((classPath.indexOf('yui') != -1) || (classPath.toLowerCase().indexOf('apple-style-span') != -1)) {
                            classPath = '';
                        }
                        var pathStr = tag + ((path[i].id) ? '#' + path[i].id : '') + classPath;
                    }
                    if (pathStr.length > 10) {
                        pathStr = pathStr.substring(0, 10) + '...';
                    }
                    pathArr[pathArr.length] = pathStr;
                }
            }
            var str = pathArr.join(' ' + this.SEP_DOMPATH + ' ');
            //Prevent flickering
            if (this.dompath.innerHTML != str) {
                this.dompath.innerHTML = str;
            }
        },
        /**
        * @private
        * @method _fixNodes
        * @description Fix href and imgs as well as remove invalid HTML.
        */
        _fixNodes: function() {
            for (var i in this.invalidHTML) {
                if (Lang.hasOwnProperty(this.invalidHTML, i)) {
                    var tags = this._getDoc().body.getElementsByTagName(i);
                    for (var h = 0; h < tags.length; h++) {
                        if (tags[h].parentNode) {
                            tags[h].parentNode.removeChild(tags[h]);
                        }
                    }
                }
            }
            var as = this._getDoc().body.getElementsByTagName('a');
            if (as.length) {
                YAHOO.log('Found an A tag in the document, converting to span holder', 'info', 'Editor');
                for (var i = 0; i < as.length; i++) {
                    var el = this._getDoc().createElement('span');
                    Dom.addClass(el, 'yui-tag-a');
                    Dom.addClass(el, 'yui-tag');
                    el.innerHTML = as[i].innerHTML;
                    el.setAttribute('tag', 'a');
                    el.setAttribute('href', as[i].getAttribute('href'));
                    if (as[i].getAttribute('target') != null) {
                        el.setAttribute('target', as[i].getAttribute('target'));
                    }
                    as[i].parentNode.replaceChild(el, as[i]);
                    as[i] = null;
                }
            }
            var imgs = this._getDoc().getElementsByTagName('img');
            Dom.addClass(imgs, 'yui-img');

            for (var i = 0; i < imgs.length; i++) {
                if (imgs[i].getAttribute('href', 2)) {
                    var url = imgs[i].getAttribute('src', 2);
                    if ((url != '') && ((url.indexOf('file:/') != -1) || (url.indexOf(':\\') != -1))) {
                        Dom.addClass(imgs[i], this.CLASS_LOCAL_FILE);
                    } else {
                        Dom.removeClass(imgs[i], this.CLASS_LOCAL_FILE);
                    }
                }
            }

            var fakeAs = this._getDoc().body.getElementsByTagName('span');
            for (var i = 0; i < fakeAs.length; i++) {
                if (fakeAs[i].getAttribute('href', 2)) {
                    var url = fakeAs[i].getAttribute('href', 2);
                    if ((url != '') && ((url.indexOf('file:/') != -1) || (url.indexOf(':\\') != -1))) {
                        Dom.addClass(fakeAs[i], this.CLASS_LOCAL_FILE);
                    } else {
                        Dom.removeClass(fakeAs[i], this.CLASS_LOCAL_FILE);
                    }
                }
            }
        },
        /**
        * @private
        * @method _showHidden
        * @description Toggle on/off the hidden.css file.
        */
        _showHidden: function() {
            if (this._showingHiddenElements) {
                YAHOO.log('Enabling hidden CSS File', 'info', 'Editor');
                this._showingHiddenElements = false;
                this.toolbar.deselectButton(this.toolbar.getButtonByValue('hiddenelements'));
                Dom.removeClass(this._getDoc().body, this.CLASS_HIDDEN);
            } else {
                YAHOO.log('Disabling hidden CSS File', 'info', 'Editor');
                this._showingHiddenElements = true;
                Dom.addClass(this._getDoc().body, this.CLASS_HIDDEN);
                this.toolbar.selectButton(this.toolbar.getButtonByValue('hiddenelements'));
            }
        },
        /**
        * @private
        * @method _setCurrentEvent
        * @param {Event} ev The event to cache
        * @description Sets the current event property
        */
        _setCurrentEvent: function(ev) {
            if (ev && ev.type) {
                YAHOO.log('Event: ' + ev.type, 'info', 'Editor');
            }
            this.currentEvent = ev;
        },
        /**
        * @private
        * @method _handleClick
        * @param {Event} ev The event we are working on.
        * @description Handles all click events inside the iFrame document.
        */
        _handleClick: function(ev) {
            this._setCurrentEvent(ev);
            if (this.currentWindow) {
                this.closeWindow();
            }
            if (!this.browser.webkit) {
                this.nodeChange();
            }
        },
        /**
        * @private
        * @method _handleMouseUp
        * @param {Event} ev The event we are working on.
        * @description Handles all mouseup events inside the iFrame document.
        */
        _handleMouseUp: function(ev) {
            this._setCurrentEvent(ev);
            if (this.browser.opera) {
                /**
                * @knownissue Opera appears to stop the MouseDown, Click and DoubleClick events on an image inside of a document with designMode on..
                * @browser Opera
                * @description This work around traps the MouseUp event and sets a timer to check if another MouseUp event fires in so many seconds. If another event is fired, they we internally fire the DoubleClick event.
                */
                var sel = Event.getTarget(ev);
                if (sel && sel.tagName && (sel.tagName.toLowerCase() == 'img')) {
                    this.nodeChange();
                    var self = this;
                    if (this.operaEvent) {
                        clearTimeout(this.operaEvent);
                        this.operaEvent = null;
                        this._handleDoubleClick(ev);
                    } else {
                        this.operaEvent = window.setTimeout(function() {
                            self.operaEvent = false;
                        }, 200);
                    }
                }
            }
            //This will stop Safari from selecting the entire document if you select all the text in the editor
            if (this.browser.webkit || this.browser.opera) {
                if (this.browser.webkit) {
                    Event.stopEvent(ev);
                }
            }            
            this.nodeChange();
            this.fireEvent('editorMouseUp', { type: 'editorMouseUp', target: this, ev: ev });
        },
        /**
        * @private
        * @method _handleMouseDown
        * @param {Event} ev The event we are working on.
        * @description Handles all mousedown events inside the iFrame document.
        */
        _handleMouseDown: function(ev) {
            this._setCurrentEvent(ev);
            var sel = Event.getTarget(ev);
            if (sel && sel.tagName && (sel.tagName.toLowerCase() == 'img')) {
                if (this.browser.webkit) {
                    this.nodeChange();
                    Event.stopEvent(ev);
                }
            }
            //this.nodeChange();
            this.fireEvent('editorMouseDown', { type: 'editorMouseDown', target: this, ev: ev });
        },
        /**
        * @private
        * @method _handleDoubleClick
        * @param {Event} ev The event we are working on.
        * @description Handles all doubleclick events inside the iFrame document.
        */
        _handleDoubleClick: function(ev) {
            this._setCurrentEvent(ev);
            var sel = Event.getTarget(ev);
            if (sel && sel.tagName && (sel.tagName.toLowerCase() == 'img')) {
                this.currentElement[0] = sel;
                this.toolbar.fireEvent('insertimageClick', { type: 'insertimageClick', target: this.toolbar });
                this.fireEvent('afterExecCommand', { type: 'afterExecCommand', target: this });
            } else if (sel && sel.getAttribute && sel.getAttribute('tag') && (sel.getAttribute('tag').toLowerCase() == 'a')) {
                this.currentElement[0] = sel;
                this.toolbar.fireEvent('createlinkClick', { type: 'createlinkClick', target: this.toolbar });
                this.fireEvent('afterExecCommand', { type: 'afterExecCommand', target: this });
            }
            this.nodeChange();
            this.fireEvent('editorDoubleClick', { type: 'editorDoubleClick', target: this, ev: ev });
        },
        /**
        * @private
        * @method _handleKeyUp
        * @param {Event} ev The event we are working on.
        * @description Handles all keyup events inside the iFrame document.
        */
        _handleKeyUp: function(ev) {
            this._setCurrentEvent(ev);
            switch (ev.keyCode) {
                case 37: //Left Arrow
                case 38: //Up Arrow
                case 39: //Right Arrow
                case 40: //Down Arrow
                case 46: //Forward Delete
                case 8: //Delete
                case 65: //The letter a (for ctrl + a and cmd + a)
                case 27: //Escape key if window is open
                    if ((ev.keyCode == 27) && this.currentWindow) {
                        this.closeWindow();
                    }
                    this.nodeChange();
                    break;
            }
            this.fireEvent('editorKeyUp', { type: 'editorKeyUp', target: this, ev: ev });
        },
        /**
        * @private
        * @method _handleKeyPress
        * @param {Event} ev The event we are working on.
        * @description Handles all keypress events inside the iFrame document.
        */
        _handleKeyPress: function(ev) {
            this._setCurrentEvent(ev);
            this.fireEvent('editorKeyPress', { type: 'editorKeyPress', target: this, ev: ev });
        },
        /**
        * @private
        * @method _handleKeyDown
        * @param {Event} ev The event we are working on.
        * @description Handles all keydown events inside the iFrame document.
        */
        _handleKeyDown: function(ev) {
            this._setCurrentEvent(ev);
            if (this.currentWindow) {
                this.closeWindow();
            }
            var doExec = false;
            var action = null;
            //if (ev.ctrlKey) {
            if (ev.shiftKey && ev.ctrlKey) {
                doExec = true;
            }
            switch (ev.keyCode) {
                case 84: //Focus Toolbar Header -- Ctrl + Shift + T
                    if (ev.shiftKey && ev.ctrlKey) {
                        this.toolbar._titlebar.firstChild.focus();
                        Event.stopEvent(ev);
                        doExec = false;
                    }
                    break;
                case 27: //Focus After Element - Ctrl + Shift + Esc
                    if (ev.shiftKey) {
                        this.afterElement.focus();
                        Event.stopEvent(ev);
                        exec = false;
                    }
                    break;
                case 219: //Left
                    action = 'justifyleft';
                    break;
                case 220: //Center
                    action = 'justifycenter';
                    break;
                case 221: //Right
                    action = 'justifyright';
                    break;
                case 76: //L
                    if (this._hasSelection()) {
                        this.execCommand('createlink', '');
                        this.toolbar.fireEvent('createlinkClick', { type: 'createlinkClick', target: this.toolbar });
                        this.fireEvent('afterExecCommand', { type: 'afterExecCommand', target: this });
                        doExec = false;
                    }
                    break;
                case 66: //B
                    action = 'bold';
                    break;
                case 73: //I
                    action = 'italic';
                    break;
                case 85: //U
                    action = 'underline';
                    break;
                case 9: //Tab Key
                    if (this.browser.safari) {
                        this._getDoc().execCommand('inserttext', false, '\t');
                        Event.stopEvent(ev);
                    }
                    break;
                case 13:
                    if (this.browser.ie) {
                        //Insert a <br> instead of a <p></p> in Internet Explorer
                        var _range = this._getRange();
                        var tar = this._getSelectedElement();
                        if (tar && tar.tagName && (tar.tagName.toLowerCase() != 'li')) {
                            if (_range) {
                                _range.pasteHTML('<br>');
                                _range.collapse(false);
                                _range.select();
                            }
                            Event.stopEvent(ev);
                        }
                    }
            }
            if (doExec && action) {
                this.execCommand(action, null);
                Event.stopEvent(ev);
                this.nodeChange();
            }
            this.fireEvent('editorKeyDown', { type: 'editorKeyDown', target: this, ev: ev });
        },
        /**
        * @method nodeChange
        * @description Handles setting up the toolbar buttons, getting the Dom path, fixing nodes.
        */
        nodeChange: function() {
            this._fixNodes();

            this.fireEvent('beforeNodeChange', { type: 'beforeNodeChange', target: this });
            if (this.get('dompath')) {
                this._writeDomPath();
            }
            //Check to see if we are disabled before continuing
            if (!this.get('disabled')) {
                if (this.STOP_NODE_CHANGE) {
                    //Reset this var for next action
                    this.STOP_NODE_CHANGE = false;
                    return false;
                } else {
                    var sel = this._getSelection();
                    var range = this._getRange();

                    //Handle disabled buttons
                    for (var i = 0; i < this._disabled.length; i++) {
                        var _button = this.toolbar.getButtonByValue(this._disabled[i]);
                        if (_button && _button.get) {
                            if (!this._hasSelection()) {
                                //No Selection - disable
                                this.toolbar.disableButton(_button.get('id'));
                            } else {
                                if (!this._alwaysDisabled[this._disabled[i]]) {
                                    this.toolbar.enableButton(_button.get('id'));
                                }
                            }
                            if (!this._alwaysEnabled[this._disabled[i]]) {
                                this.toolbar.deselectButton(_button);
                            }
                        }
                    }
                    //Handle updating the toolbar with active buttons
                    for (var i = 0; i < this.toolbar._buttonList.length; i++) {
                        if (!this._alwaysEnabled[this.toolbar._buttonList[i].get('value')]) {
                            this.toolbar.deselectButton(this.toolbar._buttonList[i]);
                        }
                    }
                    var path = this._getDomPath();
                    var olType = null;
                    for (var i = 0; i < path.length; i++) {
                        var tag = path[i].tagName.toLowerCase();
                        if (path[i].getAttribute('tag')) {
                            var tag = path[i].getAttribute('tag').toLowerCase();
                        }
                        var cmd = this._tag2cmd[tag];

                        //Bold and Italic styles
                        if (path[i].style.fontWeight.toLowerCase() == 'bold') {
                            cmd = 'bold';
                        }
                        if (path[i].style.fontStyle.toLowerCase() == 'italic') {
                            cmd = 'italic';
                        }
                        if (path[i].style.textDecoration.toLowerCase() == 'underline') {
                            cmd = 'underline';
                        }
                        if (tag == 'ol') {
                            if (path[i].type) {
                                olType = path[i].type;
                            } else {
                                olType = 'A';
                            }
                        }
                        if (cmd) {
                            if (!Lang.isArray(cmd)) {
                                cmd = [cmd];
                            }
                            for (var j = 0; j < cmd.length; j++) {
                                var button = this.toolbar.getButtonByValue(cmd[j]);
                                this.toolbar.selectButton(button);
                                this.toolbar.enableButton(button);
                            }
                        }
                        //Handle Alignment
                        switch (path[i].style.textAlign.toLowerCase()) {
                            case 'left':
                            case 'right':
                            case 'center':
                            case 'justify':
                                var alignType = path[i].style.textAlign.toLowerCase();
                                if (path[i].style.textAlign.toLowerCase() == 'justify') {
                                    alignType = 'full';
                                }
                                var button = this.toolbar.getButtonByValue('justify' + alignType);
                                this.toolbar.selectButton(button);
                                this.toolbar.enableButton(button);
                                break;
                        }
                        //Handle Ordered List Drop Down - it will reset if olType is null
                        //this._updateMenuChecked('insertorderedlist', olType);
                    }
                    //After for loop

                    //Reset Font Family and Size to the inital configs
                    var fn_button = this.toolbar.getButtonByValue('fontname');
                    if (fn_button) {
                        var family = fn_button._configs.label._initialConfig.value;
                        fn_button.set('label', '<span class="yui-toolbar-fontname-' + _cleanClassName(family) + '">' + family + '</span>');
                        this._updateMenuChecked('fontname', family);
                    }

                    var fs_button = this.toolbar.getButtonByValue('fontsize');
                    if (fs_button) {
                        fs_button.set('label', fs_button._configs.label._initialConfig.value);
                    }

                    var hd_button = this.toolbar.getButtonByValue('heading');
                    if (hd_button) {
                        hd_button.set('label', hd_button._configs.label._initialConfig.value);
                        this._updateMenuChecked('heading', 'none');
                    }
                }
            }

            this.fireEvent('afterNodeChange', { type: 'afterNodeChange', target: this });
        },
        /**
        * @private
        * @method _updateMenuChecked
        * @param {Object} button The command identifier of the button you want to check
        * @param {String} value The value of the menu item you want to check
        * @param {<a href="YAHOO.widget.Toolbar.html">YAHOO.widget.Toolbar</a>} The Toolbar instance the button belongs to (defaults to this.toolbar) 
        * @description Gets the menu from a button instance, if the menu is not rendered it will render it. It will then search the menu for the specified value, unchecking all other items and checking the specified on.
        */
        _updateMenuChecked: function(button, value, tbar) {
            if (!tbar) {
                tbar = this.toolbar;
            }
            var _button = tbar.getButtonByValue(button);
            var _menuItems = _button.getMenu().getItems();
            if (_menuItems.length == 0) {
                _button.getMenu()._onBeforeShow();
                _menuItems = _button.getMenu().getItems();
            }
            for (var i = 0; i < _menuItems.length; i++) {
                _menuItems[i].cfg.setProperty('checked', false);
                if (_menuItems[i].value == value) {
                    _menuItems[i].cfg.setProperty('checked', true);
                }
            }
        },
        /**
        * @private
        * @method _handleToolbarClick
        * @param {Event} ev The event that triggered the button click
        * @description This is an event handler attached to the Toolbar's buttonClick event. It will fire execCommand with the command identifier from the Toolbar Button.
        */
        _handleToolbarClick: function(ev) {
            var value = '';
            var str = '';
            var cmd = ev.button.value;
            if (ev.button.menucmd) {
                value = cmd;
                cmd = ev.button.menucmd;
            }
            if (this.STOP_EXEC_COMMAND) {
                YAHOO.log('execCommand skipped because we found the STOP_EXEC_COMMAND flag set to true', 'warn', 'Editor');
                YAHOO.log('NOEXEC::execCommand::(' + cmd + '), (' + value + ')', 'warn', 'Editor');
                this.STOP_EXEC_COMMAND = false;
                return false;
            } else {
                this.execCommand(cmd, value);
            }
            Event.stopEvent(ev);
        },
        /**
        * @private
        * @method _setupAfterElement
        * @description Creates the accessibility h2 header and places it after the iframe in the Dom for navigation.
        */
        _setupAfterElement: function() {
            if (!this.afterElement) {
                this.afterElement = document.createElement('h2');
                this.afterElement.className = 'yui-editor-skipheader';
                this.afterElement.tabIndex = '-1';
                this.afterElement.innerHTML = this.STR_LEAVE_EDITOR;
                this.get('element_cont').get('firstChild').appendChild(this.afterElement);
            }
        },
        /**
        * @property EDITOR_PANEL_ID
        * @description HTML id to give the properties window in the DOM.
        * @type String
        */
        EDITOR_PANEL_ID: 'yui-editor-panel',
        /**
        * @property SEP_DOMPATH
        * @description The value to place in between the Dom path items
        * @type String
        */
        SEP_DOMPATH: '<',
        /**
        * @property STR_LEAVE_EDITOR
        * @description The accessibility string for the element after the iFrame
        * @type String
        */
        STR_LEAVE_EDITOR: 'You have left the Rich Text Editor.',
        /**
        * @property STR_BEFORE_EDITOR
        * @description The accessibility string for the element before the iFrame
        * @type String
        */
        STR_BEFORE_EDITOR: 'This text field can contain stylized text and graphics. To cycle through all formatting options, use the keyboard shortcut Control + Shift + T to place focus on the toolbar and navigate between option heading names. <h4>Common formatting keyboard shortcuts:</h4><ul><li>Control Shift B sets text to bold</li> <li>Control Shift I sets text to italic</li> <li>Control Shift U underlines text</li> <li>Control Shift [ aligns text left</li> <li>Control Shift | centers text</li> <li>Control Shift ] aligns text right</li> <li>Control Shift L adds an HTML link</li> <li>To exit this text editor use the keyboard shortcut Control Shift ESC.</li></ul>',
        /**
        * @property STR_CLOSE_WINDOW
        * @description The Title of the close button in the Editor Window
        * @type String
        */
        STR_CLOSE_WINDOW: 'Close Window',
        /**
        * @property STR_CLOSE_WINDOW_NOTE
        * @description A note appearing in the Editor Window to tell the user that the Escape key will close the window
        * @type String
        */
        STR_CLOSE_WINDOW_NOTE: 'To close this window use the Escape key',
        /**
        * @property STR_TITLE
        * @description The Title of the HTML document that is created in the iFrame
        * @type String
        */
        STR_TITLE: 'Rich Text Area.',
        /**
        * @property STR_IMAGE_HERE
        * @description The text to place in the URL textbox when using the blankimage.
        * @type String
        */
        STR_IMAGE_HERE: 'Image Url Here',
        /**
        * @property STR_IMAGE_PROP_TITLE
        * @description The title for the Image Property Editor Window
        * @type String
        */
        STR_IMAGE_PROP_TITLE: 'Image Options',
        /**
        * @property STR_IMAGE_URL
        * @description The label string for Image URL
        * @type String
        */
        STR_IMAGE_URL: 'Image Url',
        /**
        * @property STR_IMAGE_TITLE
        * @description The label string for Image Description
        * @type String
        */
        STR_IMAGE_TITLE: 'Description',
        /**
        * @property STR_IMAGE_SIZE
        * @description The label string for Image Size
        * @type String
        */
        STR_IMAGE_SIZE: 'Size',
        /**
        * @property STR_IMAGE_ORIG_SIZE
        * @description The label string for Original Image Size
        * @type String
        */
        STR_IMAGE_ORIG_SIZE: 'Original Size',
        /**
        * @property STR_IMAGE_COPY
        * @description The label string for the image copy and paste message for Opera and Safari
        * @type String
        */
        STR_IMAGE_COPY: '<span class="tip"><span class="icon icon-info"></span><strong>Note:</strong>To move this image just highlight it, cut, and paste where ever you\'d like.</span>',
        /**
        * @property STR_IMAGE_PADDING
        * @description The label string for the image padding.
        * @type String
        */
        STR_IMAGE_PADDING: 'Padding',
        /**
        * @property STR_IMAGE_BORDER
        * @description The label string for the image border.
        * @type String
        */
        STR_IMAGE_BORDER: 'Border',
        /**
        * @property STR_IMAGE_TEXTFLOW
        * @description The label string for the image text flow.
        * @type String
        */
        STR_IMAGE_TEXTFLOW: 'Text Flow',
        /**
        * @property STR_LOCAL_FILE_WARNING
        * @description The label string for the local file warning.
        * @type String
        */
        STR_LOCAL_FILE_WARNING: '<span class="tip"><span class="icon icon-warn"></span><strong>Note:</strong>This image/link points to a file on your computer and will not be accessible to others on the internet.</span>',
        /**
        * @property STR_LINK_PROP_TITLE
        * @description The label string for the Link Property Editor Window.
        * @type String
        */
        STR_LINK_PROP_TITLE: 'Link Options',
        /**
        * @property STR_LINK_PROP_REMOVE
        * @description The label string for the Remove link from text link inside the property editor.
        * @type String
        */
        STR_LINK_PROP_REMOVE: 'Remove link from text',
        /**
        * @property STR_LINK_URL
        * @description The label string for the Link URL.
        * @type String
        */
        STR_LINK_URL: 'Link URL',
        /**
        * @property STR_LINK_NEW_WINDOW
        * @description The string for the open in a new window label.
        * @type String
        */
        STR_LINK_NEW_WINDOW: 'Open in a new window.',
        /**
        * @property STR_LINK_TITLE
        * @description The string for the link description.
        * @type String
        */
        STR_LINK_TITLE: 'Description',
        /**
        * @protected
        * @property STOP_EXEC_COMMAND
        * @description Set to true when you want the default execCommand function to not process anything
        * @type Boolean
        */
        STOP_EXEC_COMMAND: false,
        /**
        * @protected
        * @property STOP_NODE_CHANGE
        * @description Set to true when you want the default nodeChange function to not process anything
        * @type Boolean
        */
        STOP_NODE_CHANGE: false,
        /**
        * @protected
        * @property CLASS_HIDDEN
        * @description CSS class applied to the body when the hiddenelements button is pressed.
        * @type String
        */
        CLASS_HIDDEN: 'hidden',
        /**
        * @protected
        * @property CLASS_LOCAL_FILE
        * @description CSS class applied to an element when it's found to have a local url.
        * @type String
        */
        CLASS_LOCAL_FILE: 'warning-localfile',
        /**
        * @protected
        * @property CLASS_CONTAINER
        * @description Default CSS class to apply to the editors container element
        * @type String
        */
        CLASS_CONTAINER: 'yui-editor-container',
        /**
        * @protected
        * @property CLASS_EDITABLE
        * @description Default CSS class to apply to the editors iframe element
        * @type String
        */
        CLASS_EDITABLE: 'yui-editor-editable',
        /**
        * @protected
        * @property CLASS_EDITABLE_CONT
        * @description Default CSS class to apply to the editors iframe's parent element
        * @type String
        */
        CLASS_EDITABLE_CONT: 'yui-editor-editable-container',
        /**
        * @protected
        * @property CLASS_PREFIX
        * @description Default prefix for dynamically created class names
        * @type String
        */
        CLASS_PREFIX: 'yui-editor',
        /** 
        * @property browser
        * @description Standard browser detection
        * @type Object
        */
        browser: YAHOO.env.ua,
        /** 
        * @method init
        * @description The Editor class' initialization method
        */
        init: function(p_oElement, p_oAttributes) {
            YAHOO.widget.Editor.superclass.init.call(this, p_oElement, p_oAttributes);
            this.get('element_cont').addClass(this.CLASS_CONTAINER);
            Dom.addClass(this.get('iframe').get('parentNode'), this.CLASS_EDITABLE_CONT);
            this.get('iframe').addClass(this.CLASS_EDITABLE);
        },
        /**
        * @method initAttributes
        * @description Initializes all of the configuration attributes used to create 
        * the editor.
        * @param {Object} attr Object literal specifying a set of 
        * configuration attributes used to create the editor.
        */
        initAttributes: function(attr) {
            YAHOO.widget.Editor.superclass.initAttributes.call(this, attr);
            var self = this;

            /**
            * @private
            * @config textarea
            * @description A reference to the textarea element that we are replacing
            * @default null
            * @type Boolean
            */            
            this.setAttributeConfig('textarea', {
                value: attr.textarea,
                writeOnce: true
            });
            /**
            * @config height
            * @description The height of the editor iframe container, not including the toolbar..
            * @default Best guessed size of the textarea, for best results use CSS to style the height of the textarea or pass it in as an argument
            * @type String
            */
            this.setAttributeConfig('height', {
                value: attr.height || Dom.getStyle(self.get('textarea'), 'height'),
                writeOnce: true
            });
            /**
            * @config width
            * @description The width of the editor container.
            * @default Best guessed size of the textarea, for best results use CSS to style the width of the textarea or pass it in as an argument
            * @type String
            */            
            this.setAttributeConfig('width', {
                value: attr.width || Dom.getStyle(this.get('textarea'), 'width'),
                writeOnce: true
            });
                        
            /**
            * @config blankimage
            * @description The CSS used to show/hide hidden elements on the page
            * @default 'assets/blankimage.png'
            * @type String
            */            
            this.setAttributeConfig('blankimage', {
                value: attr.blankimage || this._getBlankImage()
            });
            /**
            * @config hiddencss
            * @description The CSS used to show/hide hidden elements on the page, these rules must be prefixed with the class provided in <code>this.CLASS_HIDDEN</code>
            * @default <code><pre>
            .hidden div, .hidden p, .hidden span, .hidden img {
                border: 1px dotted #ccc;
            }
            .hidden .yui-non {
                border: none;
            }
            .hidden img {
                padding: 2px;
            }</pre></code>
            * @type String
            */            
            this.setAttributeConfig('hiddencss', {
                value: attr.hiddencss || '.hidden div,.hidden p,.hidden span,.hidden img { border: 1px dotted #ccc; } .hidden .yui-non { border: none; } .hidden img { padding: 2px; }',
                writeOnce: true
            });
            /**
            * @config css
            * @description The Base CSS used to format the content of the editor
            * @default <code><pre>body {
                padding: 7px; background-color: #fff; font:13px/1.22 arial,helvetica,clean,sans-serif;*font-size:small;*font:x-small;
            }
            span.yui-tag-a {
                color: blue; text-decoration: underline;
            }
            span.yui-tag-blockquote {
                margin: 1em; display: block;
            }
            span.yui-tag-indent {
                margin-left: 1em; display: block;
            }
            .warning-localfile {
                border-bottom: 1px dashed red !important;
            }</pre></code>
            * @type String
            */            
            this.setAttributeConfig('css', {
                value: attr.css || 'body { padding: 7px; background-color: #fff; font:13px/1.22 arial,helvetica,clean,sans-serif;*font-size:small;*font:x-small; } span.yui-tag-a { color: blue; text-decoration: underline; } span.yui-tag-blockquote { margin: 1em; display: block; } span.yui-tag-indent { margin-left: 1em; display: block; } .warning-localfile { border-bottom: 1px dashed red !important; }',
                writeOnce: true
            });
            /**
            * @config html
            * @description The default HTML to be written to the iframe document before the contents are loaded
            * @default This HTML requires a few things if you are to override:
                <p><code>{TITLE}, {CSS}, {HIDDEN_CSS}</code> and <code>{CONTENT}</code> need to be there, they are passed to YAHOO.lang.substitute to be replace with other strings.<p>
                <p><code>onload="document.body._rteLoaded = true;"</code> : the onload statement must be there or the editor will not finish loading.</p>
                <code>
                <pre>
                &lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"&gt;
                &lt;html&gt;
                    &lt;head&gt;
                        &lt;title&gt;{TITLE}&lt;/title&gt;
                        &lt;meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /&gt;
                        &lt;style&gt;
                        {CSS}
                        &lt;/style&gt;
                        &lt;style&gt;
                        {HIDDEN_CSS}
                        &lt;/style&gt;
                    &lt;/head&gt;
                &lt;body onload="document.body._rteLoaded = true;"&gt;
                {CONTENT}
                &lt;/body&gt;
                &lt;/html&gt;
                </pre>
                </code>
            * @type String
            */            
            this.setAttributeConfig('html', {
                value: attr.html || '<!DOCTYPE HTML PUBLIC "-/'+'/W3C/'+'/DTD HTML 4.01/'+'/EN" "http:/'+'/www.w3.org/TR/html4/strict.dtd"><html><head><title>{TITLE}</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><style>{CSS}</style><style>{HIDDEN_CSS}</style></head><body onload="document.body._rteLoaded = true;">{CONTENT}</body></html>',
                writeOnce: true
            });

            /**
            * @config handleSubmit
            * @description Config handles if the editor will attach itself to the textareas parent form's submit handler.
            If it is set to true, the editor will attempt to attach a submit listener to the textareas parent form.
            Then it will trigger the editors save handler and place the new content back into the text area before the form is submitted.
            * @default false
            * @type Boolean
            */            
            this.setAttributeConfig('handleSubmit', {
                value: false,
                writeOnce: true,
                method: function(exec) {
                    if (exec) {
                        var ta = this.get('textarea');
                        if (ta.form) {
                            Event.addListener(ta.form, 'submit', function() {
                                this.saveHTML();
                            }, this, true);
                        }
                    }
                }
            });
            /**
            * @private
            * @config iframe
            * @description Internal config for holding the iframe element.
            * @default null
            * @type Boolean
            */
            this.setAttributeConfig('iframe', {
                value: null,
                writeOnce: true
            });
            /**
            * @config disabled
            * @description This will toggle the editor's disabled state. When the editor is disabled, designMode is turned off and a mask is placed over the iframe so no interaction can take place.
            All Toolbar buttons are also disabled so they cannot be used.
            * @default false
            * @type Boolean
            */

            this.setAttributeConfig('disabled', {
                value: false,
                method: function(disabled) {
                    if (disabled) {
                        if (!this._mask) {
                            this._setDesignMode('off');
                            this.toolbar.set('disabled', true);
                            this._mask = document.createElement('DIV');
                            Dom.setStyle(this._mask, 'height', '100%');
                            Dom.setStyle(this._mask, 'width', '100%');
                            Dom.setStyle(this._mask, 'position', 'absolute');
                            Dom.setStyle(this._mask, 'top', '0');
                            Dom.setStyle(this._mask, 'left', '0');
                            Dom.setStyle(this._mask, 'opacity', '.5');
                            Dom.addClass(this._mask, 'yui-editor-masked');
                            this.get('iframe').get('parentNode').appendChild(this._mask);
                        }
                    } else {
                        if (this._mask) {
                            this._mask.parentNode.removeChild(this._mask);
                            this._mask = null;
                            this.toolbar.set('disabled', false);
                            this._setDesignMode('on');
                            this._focusWindow();
                        }
                    }
                }
            });
            /**
            * @config element_cont
            * @description Internal config for the editors container
            * @default false
            * @type Boolean
            */
            this.setAttributeConfig('element_cont', {
                value: null,
                writeOnce: true
            });
            /**
            * @config toolbar_cont
            * @description Internal config for the toolbars container
            * @default false
            * @type Boolean
            */
            this.setAttributeConfig('toolbar_cont', {
                value: null,
                writeOnce: true
            });
            /**
            * @config toolbar
            * @description The default toolbar config.
            * @default This config is too large to display here, view the code to see it: <a href="editor.js.html"></a>
            * @type Object
            */            
            this.setAttributeConfig('toolbar', {
                value: attr.toolbar || {
                    /* {{{ Defaut Toolbar Config */
                    collapse: true,
                    titlebar: 'Text Editing Tools',
                    draggable: false,
                    buttons: [
                        { group: 'fontstyle', label: 'Font Name and Size',
                            buttons: [
                                { type: 'select', label: 'Arial', value: 'fontname', disabled: true,
                                    menu: [
                                        { text: 'Arial', checked: true },
                                        { text: 'Arial Black' },
                                        { text: 'Comic Sans MS' },
                                        { text: 'Courier New' },
                                        { text: 'Lucida Console' },
                                        { text: 'Tahoma' },
                                        { text: 'Times New Roman' },
                                        { text: 'Trebuchet MS' },
                                        { text: 'Verdana' }
                                    ]
                                },
                                { type: 'spin', label: '13', value: 'fontsize', range: [ 9, 75 ], disabled: true }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'textstyle', label: 'Font Style',
                            buttons: [
                                { type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
                                { type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
                                { type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
                                { type: 'separator' },
                                { type: 'push', label: 'Subscript', value: 'subscript', disabled: true },
                                { type: 'push', label: 'Superscript', value: 'superscript', disabled: true },
                                { type: 'separator' },
                                { type: 'color', label: 'Font Color', value: 'forecolor', disabled: true },
                                { type: 'color', label: 'Background Color', value: 'backcolor', disabled: true },
                                { type: 'separator' },
                                { type: 'push', label: 'Remove Formatting', value: 'removeformat', disabled: true },
                                { type: 'push', label: 'Hidden Elements', value: 'hiddenelements' }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'alignment', label: 'Alignment',
                            buttons: [
                                { type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
                                { type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
                                { type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
                                { type: 'push', label: 'Justify', value: 'justifyfull' }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'parastyle', label: 'Paragraph Style',
                            buttons: [
                            { type: 'select', label: 'Normal', value: 'heading', disabled: true,
                                menu: [
                                    { text: 'Normal', value: 'none', checked: true },
                                    { text: 'Header 1', value: 'h1' },
                                    { text: 'Header 2', value: 'h2' },
                                    { text: 'Header 3', value: 'h3' },
                                    { text: 'Header 4', value: 'h4' },
                                    { text: 'Header 5', value: 'h5' },
                                    { text: 'Header 6', value: 'h6' }
                                ]
                            }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'indentlist', label: 'Indenting and Lists',
                            buttons: [
                                { type: 'push', label: 'Indent', value: 'indent', disabled: true },
                                { type: 'push', label: 'Outdent', value: 'outdent', disabled: true },
                                { type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
                                { type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
                                /*
                                { type: 'menu', label: 'Create an Ordered List', value: 'insertorderedlist',
                                    menu: [
                                        { text: '1,2,3,4', value: '1', checked: true },
                                        { text: 'A,B,C,D', value: 'A' },
                                        { text: 'a,b,c,d', value: 'a' },
                                        { text: 'I,II,III,IV', value: 'I' },
                                        { text: 'i,ii,iii,iv', value: 'i' }
                                    ]
                                }
                                */
                            ]
                        },
                        { type: 'separator' },
                        { group: 'insertitem', label: 'Insert Item',
                            buttons: [
                                { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
                                { type: 'push', label: 'Insert Image', value: 'insertimage' }
                            ]
                        }
                    ]
                    /* }}} */
                },
                writeOnce: true,
                method: function(toolbar) {
                }
            });
            /**
            * @config animate
            * @description Should the editor animate window movements
            * @default false unless Animation is found, then true
            * @type Boolean
            */            
            this.setAttributeConfig('animate', {
                value: false,
                validator: function(value) {
                    var ret = true;
                    if (!YAHOO.util.Anim) {
                        ret = false;
                    }
                    return ret;
                }               
            });
            /**
            * @config panel
            * @description A reference to the panel we are using for windows.
            * @default false
            * @type Boolean
            */            
            this.setAttributeConfig('panel', {
                value: null,
                writeOnce: true,
                validator: function(value) {
                    var ret = true;
                    if (!YAHOO.widget.Panel) {
                        ret = false;
                    }
                    return ret;
                }               
            });
            /**
            * @config localFileWarning
            * @description Should we throw the warning if we detect a file that is local to their machine?
            * @default true
            * @type Boolean
            */            
            this.setAttributeConfig('localFileWarning', {
                value: true
            });
            /**
            * @config dompath
            * @description Toggle the display of the current Dom path below the editor
            * @default false
            * @type Boolean
            */            
            this.setAttributeConfig('dompath', {
                value: false,
                method: function(dompath) {
                    if (dompath && !this.dompath) {
                        this.dompath = document.createElement('DIV');
                        this.dompath.id = this.get('id') + '_dompath';
                        Dom.addClass(this.dompath, 'dompath');
                        this.get('element_cont').get('firstChild').appendChild(this.dompath);
                        if (this.get('iframe')) {
                            this._writeDomPath();
                        }
                    } else if (!dompath && this.dompath) {
                        this.dompath.parentNode.removeChild(this.dompath);
                        this.dompath = null;
                    }
                    this._setupAfterElement();
                }
            });
            /**
            * @config markup
            * @description Should we try to adjust the markup for the following types: semantic, css or default
            * @default "semantic"
            * @type Boolean
            */            
            this.setAttributeConfig('markup', {
                value: 'semantic',
                validator: function(markup) {
                    switch (markup.toLowerCase()) {
                        case 'semantic':
                        case 'css':
                        case 'default':
                        return true;
                        break;
                    }
                    return false;
                }
            });

            this.on('afterRender', function() {
                this._renderPanel();
            });
        },
        /**
        * @private
        * @method _getBlankImage
        * @description Retrieves the full url of the image to use as the blank image.
        * @returns {String} The URL to the blank image
        */
        _getBlankImage: function() {
            if (!this.DOMReady) {
                this._queue[this._queue.length] = ['_getBlankImage', arguments];
                return '';
            }      
            var div = document.createElement('div');
            div.style.position = 'absolute';
            div.style.top = '-9999px';
            div.style.left = '-9999px';
            div.className = this.CLASS_PREFIX + '-blankimage';
            document.body.appendChild(div);
            var img = YAHOO.util.Dom.getStyle(div, 'background-image');
            img = img.replace('url(', '').replace(')', '').replace(/"/g, '');
            this.set('blankimage', img);            
            return img;
        },
        /**
        * @private
        * @method _handleFontSize
        * @description Handles the font size button in the toolbar.
        * @param {Object} o Object returned from Toolbar's buttonClick Event
        */
        _handleFontSize: function(o) {
            var button = this.toolbar.getButtonById(o.button.id);
            var value = button.get('label') + 'px';
            this.execCommand('fontsize', value);
            this.STOP_EXEC_COMMAND = true;
        },
        /**
        * @private
        * @method _handleColorPicker
        * @description Handles the colorpicker buttons in the toolbar.
        * @param {Object} o Object returned from Toolbar's buttonClick Event
        */
        _handleColorPicker: function(o) {
            var cmd = o.button;
            var value = '#' + o.color;
            if ((cmd == 'forecolor') || (cmd == 'backcolor')) {
                this.execCommand(cmd, value);
            }
        },
        /**
        * @private
        * @method _handleAlign
        * @description Handles the alignment buttons in the toolbar.
        * @param {Object} o Object returned from Toolbar's buttonClick Event
        */
        _handleAlign: function(o) {
            var button = this.toolbar.getButtonById(o.button.id);
            var cmd = null;
            for (var i = 0; i < o.button.menu.length; i++) {
                if (o.button.menu[i].value == o.button.value) {
                    cmd = o.button.menu[i].value;
                }
            }
            var value = this._getSelection();

            this.execCommand(cmd, value);
            this.STOP_EXEC_COMMAND = true;
        },
        /**
        * @private
        * @method _handleAfterNodeChange
        * @description Fires after a nodeChange happens to setup the things that where reset on the node change (button state).
        */
        _handleAfterNodeChange: function() {
            var path = this._getDomPath();
            for (var i = 0; i < path.length; i++) {
                var elm = path[i],
                    tag = elm.tagName.toLowerCase(),
                    family = null,
                    fontsize = null,
                    validFont = false;

                if (elm.getAttribute('tag')) {
                    tag = elm.getAttribute('tag');
                }

                family = elm.getAttribute('face');
                if (Dom.getStyle(elm, 'font-family')) {
                    family = Dom.getStyle(elm, 'font-family');
                }
                var fn_button = this.toolbar.getButtonByValue('fontname');
                if (fn_button) {
                    for (var b = 0; b < fn_button._configs.menu.value.length; b++) {
                        if (family && fn_button._configs.menu.value[b].text.toLowerCase() == family.toLowerCase()) {
                            validFont = true;
                            family = fn_button._configs.menu.value[b].text; //Put the proper menu name in the button
                        }
                    }
                    if (!validFont) {
                        family = fn_button._configs.label._initialConfig.value;
                    }
                    fn_button.set('label', '<span class="yui-toolbar-fontname-' + _cleanClassName(family) + '">' + family + '</span>');
                    this._updateMenuChecked('fontname', family);
                }

                var fs_button = this.toolbar.getButtonByValue('fontsize');
                if (fs_button) {
                    fontsize = parseInt(Dom.getStyle(elm, 'fontSize'));
                    if ((fontsize == null) || isNaN(fontsize)) {
                        fontsize = fs_button._configs.label._initialConfig.value;
                    }
                    fs_button.set('label', ''+fontsize);
                }

                if (tag.substring(0, 1) == 'h') {
                    var hd_button = this.toolbar.getButtonByValue('heading');
                    if (hd_button) {
                        for (var b = 0; b < hd_button._configs.menu.value.length; b++) {
                            if (hd_button._configs.menu.value[b].value.toLowerCase() == tag) {
                                hd_button.set('label', hd_button._configs.menu.value[b].text);
                            }
                        }
                        this._updateMenuChecked('heading', tag);
                    }
                }
            }
            
            if (elm && elm.tagName && (elm.tagName.toLowerCase() != 'body')) {
                this.toolbar.enableButton(fn_button);
                this.toolbar.enableButton(fs_button);
            }
            
        },
        /**
        * @private
        * @method _handleInsertImageClick
        * @description Opens the Image Properties Window when the insert Image button is clicked or an Image is Double Clicked.
        */
        _handleInsertImageClick: function() {
            //this.toolbar.disableButton(this.toolbar.getButtonByValue('insertimage'));
            this.on('afterExecCommand', function() {
                var el = this.currentElement[0],
                    title = '',
                    src = '',
                    align = '',
                    height = 75,
                    width = 75,
                    padding = 0,
                    blankimage = false,
                    win = new YAHOO.widget.EditorWindow('insertimage', {
                        width: '415px'
                    });

                if (!el) {
                    el = this._getSelectedElement();
                }
                if (el) {
                    if (el.getAttribute('src')) {
                        src = el.getAttribute('src', 2);
                        if (src.indexOf(this.get('blankimage')) != -1) {
                            src = this.STR_IMAGE_HERE;
                            blankimage = true;
                        }
                    }
                    if (el.getAttribute('alt', 2)) {
                        title = el.getAttribute('alt', 2);
                    }
                    if (el.getAttribute('title', 2)) {
                        title = el.getAttribute('title', 2);
                    }
                    height = parseInt(el.height);
                    width = parseInt(el.width);
                    if (el.style.height) {
                        height = parseInt(el.style.height);
                    }
                    if (el.style.width) {
                        width = parseInt(el.style.width);
                    }
                    if (el.style.margin) {
                        padding = parseInt(el.style.margin);
                    }
                    if (!el._height) {
                        el._height = height;
                    }
                    if (!el._width) {
                        el._width = width;
                    }
                    var oheight = el._height;
                    var owidth = el._width;
                }
                if (!win.cache) {
                    var str = '<label for="insertimage_url"><strong>' + this.STR_IMAGE_URL + ':</strong> <input type="text" id="insertimage_url" value="' + src + '" size="40"></label>';
                    var body = document.createElement('div');
                    body.innerHTML = str;

                    var tbarCont = document.createElement('div');
                    tbarCont.id = 'img_toolbar';
                    body.appendChild(tbarCont);

                    var str2 = '<label for="insertimage_title"><strong>' + this.STR_IMAGE_TITLE + ':</strong> <input type="text" id="insertimage_title" value="' + title + '" size="40"></label>';
                    var div = document.createElement('div');
                    div.innerHTML = str2;
                    body.appendChild(div);
                    win.cache = body;
                } else {
                    body = win.cache;
                }

                var tbar = new YAHOO.widget.Toolbar(tbarCont, {
                    /* {{{ */ 
                    buttons: [
                        { group: 'padding', label: this.STR_IMAGE_PADDING + ':',
                            buttons: [
                                { type: 'spin', label: ''+padding, value: 'padding', range: [0, 50] }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'border', label: this.STR_IMAGE_BORDER + ':',
                            buttons: [
                                { type: 'select', label: 'Border Size', value: 'bordersize',
                                    menu: [
                                        { text: 'none', value: '0', checked: true },
                                        { text: '----', value: '1' },
                                        { text: '----', value: '2' },
                                        { text: '----', value: '3' },
                                        { text: '----', value: '4' },
                                        { text: '----', value: '5' }
                                    ]
                                },
                                { type: 'select', label: 'Border Type', value: 'bordertype', disabled: true,
                                    menu: [
                                        { text: '----', value: 'solid', checked: true },
                                        { text: '----', value: 'dashed' },
                                        { text: '----', value: 'dotted' }
                                    ]
                                },
                                { type: 'color', label: 'Border Color', value: 'bordercolor', disabled: true }
                            ]
                        },
                        { type: 'separator' },
                        { group: 'textflow', label: this.STR_IMAGE_TEXTFLOW + ':',
                            buttons: [
                                { type: 'push', label: 'Left', value: 'left' },
                                { type: 'push', label: 'Inline', value: 'inline' },
                                { type: 'push', label: 'Block', value: 'block' },
                                { type: 'push', label: 'Right', value: 'right' }
                            ]
                        }
                    ]
                    /* }}} */
                });
                
                var bsize = '0';
                var btype = 'solid';
                if (el.style.borderLeftWidth) {
                    bsize = parseInt(el.style.borderLeftWidth);
                }
                if (el.style.borderLeftStyle) {
                    btype = el.style.borderLeftStyle;
                }
                var bs_button = tbar.getButtonByValue('bordersize');
                var bSizeStr = ((parseInt(bsize) > 0) ? '----' : 'none');
                bs_button.set('label', '<span class="yui-toolbar-bordersize-' + bsize + '">'+bSizeStr+'</span>');
                this._updateMenuChecked('bordersize', bsize, tbar);

                var bs_button = tbar.getButtonByValue('bordertype');
                bs_button.set('label', '<span class="yui-toolbar-bordertype-' + btype + '">----</span>');
                this._updateMenuChecked('bordertype', btype, tbar);
                if (parseInt(bsize) > 0) {
                    tbar.enableButton(tbar.getButtonByValue('bordertype'));
                    tbar.enableButton(tbar.getButtonByValue('bordercolor'));
                }

                var cont = tbar.get('cont');
                var hw = document.createElement('div');
                hw.className = 'yui-toolbar-group yui-toolbar-group-padding height-width';
                hw.innerHTML = '<h3>' + this.STR_IMAGE_SIZE + ':</h3>';
                var orgSize = '';
                if ((height != oheight) || (width != owidth)) {
                    orgSize = '<span class="info">' + this.STR_IMAGE_ORIG_SIZE + '<br>'+ owidth +' x ' + oheight + '</span>';
                }
                hw.innerHTML += '<span><input type="text" size="3" value="'+width+'" id="insertimage_width"> x <input type="text" size="3" value="'+height+'" id="insertimage_height"></span>' + orgSize;
                cont.insertBefore(hw, cont.firstChild);

                Event.onAvailable('insertimage_width', function() {
                    Event.on('insertimage_width', 'blur', function() {
                        var value = parseInt(Dom.get('insertimage_width').value);
                        if (value > 5) {
                            el.style.width = value + 'px';
                            this.moveWindow();
                        }
                    }, this, true);
                }, this, true);
                Event.onAvailable('insertimage_height', function() {
                    Event.on('insertimage_height', 'blur', function() {
                        var value = parseInt(Dom.get('insertimage_height').value);
                        if (value > 5) {
                            el.style.height = value + 'px';
                            this.moveWindow();
                        }
                    }, this, true);
                }, this, true);

                if (el.align == 'right') {
                    tbar.selectButton(tbar.getButtonByValue('right'));
                } else if (el.align == 'left') {
                    tbar.selectButton(tbar.getButtonByValue('left'));
                } else if (el.style.display == 'block') {
                    tbar.selectButton(tbar.getButtonByValue('block'));
                } else {
                    tbar.selectButton(tbar.getButtonByValue('inline'));
                }
                if (parseInt(el.style.marginLeft) > 0) {
                     tbar.getButtonByValue('padding').set('label', ''+parseInt(el.style.marginLeft));
                }
                if (el.style.borderSize) {
                    tbar.selectButton(tbar.getButtonByValue('bordersize'));
                    tbar.selectButton(tbar.getButtonByValue(parseInt(el.style.borderSize)));
                }

                tbar.on('colorPickerClicked', function(o) {
                    var size = '1', type = 'solid', color = 'black';

                    if (el.style.borderLeftWidth) {
                        size = parseInt(el.style.borderLeftWidth);
                    }
                    if (el.style.borderLeftStyle) {
                        type = el.style.borderLeftStyle;
                    }
                    if (el.style.borderLeftColor) {
                        color = el.style.borderLeftColor;
                    }
                    var borderString = size + 'px ' + type + ' #' + o.color;
                    el.style.border = borderString;
                }, this.toolbar, true);

                tbar.on('buttonClick', function(o) {
                    var value = o.button.value;
                    if (o.button.menucmd) {
                        value = o.button.menucmd
                    }
                    var size = '1', type = 'solid', color = 'black';

                    /* All border calcs are done on the left border
                        since our default interface only supports
                        one border size/type and color */
                    if (el.style.borderLeftWidth) {
                        size = parseInt(el.style.borderLeftWidth);
                    }
                    if (el.style.borderLeftStyle) {
                        type = el.style.borderLeftStyle;
                    }
                    if (el.style.borderLeftColor) {
                        color = el.style.borderLeftColor;
                    }
                    switch(value) {
                        case 'bordersize':
                            var borderString = parseInt(o.button.value) + 'px ' + type + ' ' + color;
                            el.style.border = borderString;
                            if (parseInt(o.button.value) > 0) {
                                tbar.enableButton(tbar.getButtonByValue('bordertype'));
                                tbar.enableButton(tbar.getButtonByValue('bordercolor'));
                            } else {
                                tbar.disableButton(tbar.getButtonByValue('bordertype'));
                                tbar.disableButton(tbar.getButtonByValue('bordercolor'));
                            }
                            break;
                        case 'bordertype':
                            var borderString = size + 'px ' + o.button.value + ' ' + color;
                            el.style.border = borderString;
                            break;
                        case 'right':
                        case 'left':
                            tbar.deselectAllButtons();
                            el.style.display = '';
                            el.align = o.button.value;
                            break;
                        case 'inline':
                            tbar.deselectAllButtons();
                            el.style.display = '';
                            el.align = '';
                            break;
                        case 'block':
                            tbar.deselectAllButtons();
                            el.style.display = 'block';
                            el.align = 'center';
                            break;
                        case 'padding':
                            var _button = tbar.getButtonById(o.button.id);
                            el.style.margin = _button.get('label') + 'px';
                            break;
                    }
                    tbar.selectButton(tbar.getButtonByValue(o.button.value));
                    this.moveWindow();
                }, this, true);

                win.setHeader(this.STR_IMAGE_PROP_TITLE);
                win.setBody(body);
                if ((this.browser.webkit && !this.browser.webkit3) || this.browser.opera) {
                    var str = this.STR_IMAGE_COPY;
                    win.setFooter(str);
                }
                this.openWindow(win);


                //Set event after openWindow..
                Event.onAvailable('insertimage_url', function() {
                    window.setTimeout(function() {
                        YAHOO.util.Dom.get('insertimage_url').focus();
                        if (blankimage) {
                            YAHOO.util.Dom.get('insertimage_url').select();
                        }
                    }, 50);
                    
                    if (this.get('localFileWarning')) {
                        Event.on('insertimage_url', 'blur', function() {
                            var url = Dom.get('insertimage_url');
                            if ((url.value != '') && ((url.value.indexOf('file:/') != -1) || (url.value.indexOf(':\\') != -1))) {
                                //Local File throw Warning
                                Dom.addClass(url, 'warning');
                                YAHOO.log('Local file reference found, show local warning', 'warn', 'Editor');
                                var str = this.STR_LOCAL_FILE_WARNING;
                                this.get('panel').setFooter(str);
                            } else {
                                Dom.removeClass(url, 'warning');
                                this.get('panel').setFooter(' ');
                                if ((this.browser.webkit && !this.browser.webkit3) || this.browser.opera) {
                                    var str = this.STR_IMAGE_COPY;
                                    this.get('panel').setFooter(str);
                                }
                                
                                if (url && url.value && (url.value != this.STR_IMAGE_HERE)) {
                                    this.currentElement[0].setAttribute('src', url.value);
                                    var img = new Image();
                                    var self = this;
                                    window.setTimeout(function() {
                                        YAHOO.util.Dom.get('insertimage_height').value = img.height;
                                        YAHOO.util.Dom.get('insertimage_width').value = img.width;
                                        if (!self.currentElement[0]._height) {
                                            self.currentElement[0]._height = img.height;
                                        }
                                        if (!self.currentElement[0]._width) {
                                            self.currentElement[0]._width = img.width;
                                        }
                                        self.moveWindow();
                                    }, 200);

                                    img.src = url.value;
                                }
                            }
                        }, this, true);
                    }
                }, this, true);
            });
        },
        /**
        * @private
        * @method _handleInsertImageWindowClose
        * @description Handles the closing of the Image Properties Window.
        */
        _handleInsertImageWindowClose: function() {
            var url = Dom.get('insertimage_url');
            var title = Dom.get('insertimage_title');
            var el = this.currentElement[0];
            if (url && url.value && (url.value != this.STR_IMAGE_HERE)) {
                el.setAttribute('src', url.value);
                el.setAttribute('title', title.value);
                el.setAttribute('alt', title.value);
                //this.toolbar.enableButton(this.toolbar.getButtonByValue('insertimage'));
            } else {
                //No url/src given, remove the node from the document
                el.parentNode.removeChild(el);
            }
        },
        /**
        * @private
        * @method _handleCreateLinkClick
        * @description Handles the opening of the Link Properties Window when the Create Link button is clicked or an href is doubleclicked.
        */
        _handleCreateLinkClick: function() {
            this.on('afterExecCommand', function() {

                var win = new YAHOO.widget.EditorWindow('createlink', {
                    width: '300px'
                });
                
                var el = this.currentElement[0],
                    url = '',
                    title = '',
                    target = '',
                    localFile = false;
                if (el) {
                    if (el.getAttribute('href') != null) {
                        url = el.getAttribute('href');
                        if ((url != '') && ((url.indexOf('file:/') != -1) || (url.indexOf(':\\') != -1))) {
                            //Local File throw Warning
                            YAHOO.log('Local file reference found, show local warning', 'warn', 'Editor');
                            var str = this.STR_LOCAL_FILE_WARNING;
                            win.setFooter(str);
                            localFile = true;
                        } else {
                            win.setFooter(' ');
                        }
                    }
                    if (el.getAttribute('title') != null) {
                        title = el.getAttribute('title');
                    }
                    if (el.getAttribute('target') != null) {
                        target = el.getAttribute('target');
                    }
                }
                var str = '<label for="createlink_url"><strong>' + this.STR_LINK_URL + ':</strong> <input type="text" name="createlink_url" id="createlink_url" value="' + url + '"' + ((localFile) ? ' class="warning"' : '') + '></label>';
                str += '<label for="createlink_target"><strong>&nbsp;</strong><input type="checkbox" name="createlink_target_" id="createlink_target" value="_blank"' + ((target) ? ' checked' : '') + '> ' + this.STR_LINK_NEW_WINDOW + '</label>';
                str += '<label for="createlink_title"><strong>' + this.STR_LINK_TITLE + ':</strong> <input type="text" name="createlink_title" id="createlink_title" value="' + title + '"></label>';
                
                var body = document.createElement('div');
                body.innerHTML = str;

                var unlinkCont = document.createElement('div');
                unlinkCont.className = 'removeLink';
                var unlink = document.createElement('a');
                unlink.href = '#';
                unlink.innerHTML = this.STR_LINK_PROP_REMOVE;
                unlink.title = this.STR_LINK_PROP_REMOVE;
                Event.on(unlink, 'click', function(ev) {
                    Event.stopEvent(ev);
                    this.execCommand('unlink');
                    this.closeWindow();
                }, this, true);
                unlinkCont.appendChild(unlink);
                body.appendChild(unlinkCont);

                win.setHeader(this.STR_LINK_PROP_TITLE);
                win.setBody(body);

                Event.onAvailable('createlink_url', function() {
                    window.setTimeout(function() {
                        try {
                            YAHOO.util.Dom.get('createlink_url').focus();
                        } catch (e) {}
                    }, 50);
                    Event.on('createlink_url', 'blur', function() {
                        var url = Dom.get('createlink_url');
                        if ((url.value != '') && ((url.value.indexOf('file:/') != -1) || (url.value.indexOf(':\\') != -1))) {
                            //Local File throw Warning
                            Dom.addClass(url, 'warning');
                            YAHOO.log('Local file reference found, show local warning', 'warn', 'Editor');
                            var str = this.STR_LOCAL_FILE_WARNING;
                            this.get('panel').setFooter(str);
                        } else {
                            Dom.removeClass(url, 'warning');
                            this.get('panel').setFooter(' ');
                        }
                    }, this, true);
                }, this, true);

                this.openWindow(win);
            });
        },
        /**
        * @private
        * @method _handleCreateLinkWindowClose
        * @description Handles the closing of the Link Properties Window.
        */
        _handleCreateLinkWindowClose: function() {
            var url = Dom.get('createlink_url');
            var target = Dom.get('createlink_target');
            var title = Dom.get('createlink_title');
            var el = this.currentElement[0];
            if (url && url.value) {
                var urlValue = url.value;
                if ((urlValue.indexOf(':/'+'/') == -1) && (urlValue.substring(0,1) != '/') && (urlValue.substring(0, 6).toLowerCase() != 'mailto')) {
                    if ((urlValue.indexOf('@') != -1) && (urlValue.substring(0, 6).toLowerCase() != 'mailto')) {
                        //Found an @ sign, prefix with mailto:
                        urlValue = 'mailto:' + urlValue;
                    } else {
                        /* :// not found adding */
                        urlValue = 'http:/'+'/' + urlValue;
                    }
                }
                el.setAttribute('href', urlValue);
                if (target.checked) {
                    el.setAttribute('target', target.value);
                } else {
                    el.setAttribute('target', '');
                }
                el.setAttribute('title', ((title.value) ? title.value : ''));

            } else {
                el.removeAttribute('tag');
                Dom.removeClass(el, 'yui-tag-a');
                Dom.removeClass(el, 'yui-tag');
                Dom.addClass(el, 'yui-non');
            }
            this.nodeChange();
        },
        /**
        * @method render
        * @description Causes the toolbar and the editor to render and replace the textarea.
        */
        render: function() {
            if (!this.DOMReady) {
                this._queue[this._queue.length] = ['render', arguments];
                return false;
            }
            var self = this;
            var tbarConf = this.get('toolbar');
            //Set the toolbar to disabled until content is loaded
            tbarConf.disabled = true;
            //Create Toolbar instance
            this.toolbar = new Toolbar(this.get('toolbar_cont'), tbarConf);
            YAHOO.log('fireEvent::toolbarLoaded', 'info', 'Editor');
            this.fireEvent('toolbarLoaded', { type: 'toolbarLoaded', target: this.toolbar });

            
            this.toolbar.on('toolbarCollapsed', function() {
                if (this.currentWindow) {
                    this.moveWindow();
                }
            }, this, true);
            this.toolbar.on('toolbarExpanded', function() {
                if (this.currentWindow) {
                    this.moveWindow();
                }
            }, this, true);
            this.toolbar.on('fontsizeClick', function(o) {
                this._handleFontSize(o);
            }, this, true);
            
            this.toolbar.on('colorPickerClicked', function(o) {
                this._handleColorPicker(o);
            }, this, true);

            this.toolbar.on('alignClick', function(o) {
                this._handleAlign(o);
            }, this, true);
            this.on('afterNodeChange', function() {
                this._handleAfterNodeChange();
            }, this, true);
            this.toolbar.on('insertimageClick', function() {
                this._handleInsertImageClick();
            }, this, true);
            this.on('windowinsertimageClose', function() {
                this._handleInsertImageWindowClose();
            }, this, true);
            this.toolbar.on('createlinkClick', function() {
                this._handleCreateLinkClick();
            }, this, true);
            this.on('windowcreatelinkClose', function() {
                this._handleCreateLinkWindowClose();
            }, this, true);


            //Replace Textarea with editable area
            
            this.get('parentNode').replaceChild(this.get('element_cont').get('element'), this.get('element'));


            if (!this.beforeElement) {
                this.beforeElement = document.createElement('h2');
                this.beforeElement.className = 'yui-editor-skipheader';
                this.beforeElement.tabIndex = '-1';
                this.beforeElement.innerHTML = this.STR_BEFORE_EDITOR;
                this.get('element_cont').get('firstChild').insertBefore(this.beforeElement, this.toolbar.get('nextSibling'));
            }
            
            Dom.setStyle(this.get('textarea'), 'display', 'none');
            this.get('element_cont').appendChild(this.get('element'));
            this.get('element_cont').setStyle('display', 'block');


            //Set height and width of editor container
            this.get('element_cont').setStyle('width', this.get('width'));
            Dom.setStyle(this.get('iframe').get('parentNode'), 'height', this.get('height'));

            this.get('iframe').setStyle('width', '100%'); //WIDTH
            //this.get('iframe').setStyle('_width', '99%'); //WIDTH
            this.get('iframe').setStyle('height', '100%');


            var self = this;
            window.setTimeout(function() {
                self._setInitialContent.call(self);
            }, 10);
            
            this.fireEvent('afterRender', { type: 'afterRender', target: this });
        },
        /**
        * @method execCommand
        * @param {String} action The "execCommand" action to try to execute (Example: bold, insertimage, inserthtml)
        * @param {String} value (optional) The value for a given action such as action: fontname value: 'Verdana'
        * @description This method attempts to try and level the differences in the various browsers and their support for execCommand actions
        */
        execCommand: function(action, value) {
            this.fireEvent('beforeExecCommand', { type: 'beforeExecCommand', target: this, args: arguments });
            if (this.STOP_EXEC_COMMAND) {
                this.STOP_EXEC_COMMAND = false;
                return false;
            }
            this._setMarkupType(action);
            if (this.browser.ie) {
                this._getWindow().focus();
            }
            var exec = true;
            var _sel = this._getSelection();
            var _range = this._getRange();
            var _selEl = this._getSelectedElement();
            if (_selEl) {
                _sel = _selEl;
            }
            switch (action.toLowerCase()) {
                case 'heading':
                    if (this.browser.ie) {
                        action = 'formatblock';
                    }
                    if (value == 'none') {
                        if ((_sel && _sel.tagName && (_sel.tagName.toLowerCase().substring(0,1) == 'h')) || (_sel && _sel.parentNode && _sel.parentNode.tagName && (_sel.parentNode.tagName.toLowerCase().substring(0,1) == 'h'))) {
                            if (_sel.parentNode.tagName.toLowerCase().substring(0,1) == 'h') {
                                _sel = _sel.parentNode;
                            }
                            var _span = this._getDoc().createElement('span');
                            _span.className = 'yui-non';
                            _span.innerHTML = _sel.innerHTML;
                            _sel.parentNode.replaceChild(_span, _sel);
                        }
                        exec = false;
                    } else {
                        if (this.browser.ie || this.browser.webkit || this.browser.opera) {
                            this._createCurrentElement(value);
                            exec = false;
                        }
                    }
                    break;
                case 'backcolor':
                    if (this.browser.gecko || this.browser.opera) {
                        this._setEditorStyle(true);
                        action = 'hilitecolor';
                    }
                    break;
                case 'hiddenelements':
                    this._showHidden();
                    exec = false;
                    break;
                case 'unlink':
                    //var el = this._getSelectedElement();
                    var el = this.currentElement[0];
                    el.removeAttribute('title');
                    el.removeAttribute('tag');
                    el.removeAttribute('target');
                    el.removeAttribute('href');
                    YAHOO.util.Dom.addClass(el, 'yui-non');
                    YAHOO.util.Dom.removeClass(el, 'yui-tag-a');
                    YAHOO.util.Dom.removeClass(el, 'yui-tag');
                    exec = false;
                    break;
                case 'createlink':
                    var el = this._getSelectedElement();
                    if (!el || (el.getAttribute('tag') != 'a')) {
                        this._createCurrentElement('a');
                    } else {
                        this.currentElement[0] = el;
                    }
                    exec = false;
                    break;
                case 'insertimage':
                    if (value == '') {
                        value = this.get('blankimage');
                    }
                    /**
                    * @knownissue
                    * @browser Safari 2.x
                    * @description The issue here is that we have no way of knowing where the cursor position is
                    * inside of the iframe, so we have to place the newly inserted data in the best place that we can.
                    */
                    
                    var el = this._getSelectedElement();

                    if (el && el.tagName && (el.tagName.toLowerCase() == 'img')) {
                        this.currentElement[0] = el;
                        exec = false;
                    } else {
                        if (this._getDoc().queryCommandEnabled(action)) {
                            this._getDoc().execCommand('insertimage', false, value);
                            var imgs = this._getDoc().getElementsByTagName('img');
                            for (var i = 0; i < imgs.length; i++) {
                                if (!YAHOO.util.Dom.hasClass(imgs[i], 'yui-img')) {
                                    YAHOO.util.Dom.addClass(imgs[i], 'yui-img');
                                    this.currentElement[0] = imgs[i];
                                }
                            }
                            exec = false;
                        } else {
                            this._createCurrentElement('img');
                            var _img = this._getDoc().createElement('img');
                            _img.setAttribute('src', value);
                            YAHOO.util.Dom.addClass(_img, 'yui-img');
                            this.currentElement[0].parentNode.replaceChild(_img, this.currentElement[0]);
                            this.currentElement[0] = _img;
                            exec = false;
                        }
                    }
                    
                    break;
                case 'inserthtml':
                    /**
                    * @knownissue
                    * @browser Safari 2.x
                    * @description The issue here is that we have no way of knowing where the cursor position is
                    * inside of the iframe, so we have to place the newly inserted data in the best place that we can.
                    */
                    if (this.browser.webkit && !this._getDoc().queryCommandEnabled(action)) {
                        YAHOO.log('More Safari DOM tricks (inserthtml)', 'info', 'EditorSafari');
                        this._createCurrentElement('img');
                        var _span = this._getDoc().createElement('span');
                        _span.innerHTML = value;
                        this.currentElement[0].parentNode.replaceChild(_span, this.currentElement[0]);
                        exec = false;
                    } else if (this.browser.ie) {
                        var _range = this._getRange();
                        if (_range.item) {
                            _range.item(0).outerHTML = value;
                        } else {
                            _range.pasteHTML(value);
                        }
                        exec = false;                    
                    }
                    break;
                case 'removeformat':
                    /**
                    * @knownissue Remove Format issue
                    * @browser Safari 2.x
                    * @description There is an issue here with Safari, that it may not always remove the format of the item that is selected.
                    * Due to the way that Safari 2.x handles ranges, it is very difficult to determine what the selection holds.
                    * So here we are making the best possible guess and acting on it.
                    */
                    if (this.browser.webkit && !this._getDoc().queryCommandEnabled(action)) {
                        this._createCurrentElement('span');
                        YAHOO.util.Dom.addClass(this.currentElement[0], 'yui-non');
                        var re= /<\S[^><]*>/g;
                        var str = this.currentElement[0].innerHTML.replace(re, '');
                        var _txt = this._getDoc().createTextNode(str);
                        this.currentElement[0].parentNode.parentNode.replaceChild(_txt, this.currentElement[0].parentNode);
                        
                        exec = false;
                    }
                    break;
                case 'superscript':
                case 'subscript':
                    if (this.browser.webkit) {
                        YAHOO.log('Safari dom fun again (' + action + ')..', 'info', 'EditorSafari');
                        var tag = action.toLowerCase().substring(0, 3);
                        this._createCurrentElement(tag);
                        if (this.currentElement[0].parentNode.tagName.toLowerCase() == tag) {
                            YAHOO.log('we are a child of tag (' + tag + '), reverse process', 'info', 'EditorSafari');
                            var span = this._getDoc().createElement('span');
                            span.innerHTML = this.currentElement[0].innerHTML;
                            YAHOO.util.Dom.addClass(span, 'yui-non');
                            this.currentElement[0].parentNode.parentNode.replaceChild(span, this.currentElement[0].parentNode);

                        } else {
                            var _sub = this._getDoc().createElement(tag);
                            _sub.innerHTML = this.currentElement[0].innerHTML;
                            this.currentElement[0].parentNode.replaceChild(_sub, this.currentElement[0]);
                        }
                        exec = false;
                    }
                    break;
                case 'formatblock':
                    value = 'blockquote';
                    if (this.browser.webkit) {
                        this._createCurrentElement('blockquote');
                        if (YAHOO.util.Dom.hasClass(this.currentElement[0].parentNode, 'yui-tag-blockquote')) {
                            var span = this._getDoc().createElement('span');
                            span.innerHTML = this.currentElement[0].innerHTML;
                            YAHOO.util.Dom.addClass(span, 'yui-non');
                            this.currentElement[0].parentNode.parentNode.replaceChild(span, this.currentElement[0].parentNode);
                        }
                        exec = false;
                    } else {
                        var tar = Event.getTarget(this.currentEvent);
                        if (tar && tar.tagName && (tar.tagName.toLowerCase() == 'blockquote')) {
                            var span = this._getDoc().createElement('span');
                            span.innerHTML = tar.innerHTML;
                            YAHOO.util.Dom.addClass(span, 'yui-non');
                            tar.parentNode.replaceChild(span, tar);
                            exec = false;
                        }
                    }
                    break;
                case 'indent':
                case 'outdent':
                    this._createCurrentElement(action.toLowerCase());
                    if (this.currentElement[0].parentNode) {
                        if (action.toLowerCase() == 'outdent') {
                            if (YAHOO.util.Dom.hasClass(this.currentElement[0].parentNode, 'yui-tag-indent')) {
                                var span = this._getDoc().createElement('span');
                                span.innerHTML = this.currentElement[0].innerHTML;
                                YAHOO.util.Dom.addClass(span, 'yui-non');
                                this.currentElement[0].parentNode.parentNode.replaceChild(span, this.currentElement[0].parentNode);
                            }
                        }
                    }
                    exec = false;
                    break;
                case 'insertorderedlist':
                case 'insertunorderedlist':
                    /**
                    * @knownissue Safari 2.+ doesn't support ordered and unordered lists
                    * @browser Safari 2.x
                    * The issue with this workaround is that when applied to a set of text
                    * that has BR's in it, Safari may or may not pick up the individual items as
                    * list items. This is fixed in WebKit (Safari 3)
                    */
                    var tag = ((action.toLowerCase() == 'insertorderedlist') ? 'ol' : 'ul');
                    //if ((this.browser.webkit && !this._getDoc().queryCommandEnabled(action)) || this.browser.opera) {
                    if ((this.browser.webkit && !this._getDoc().queryCommandEnabled(action))) {
                        var selEl = this._getSelectedElement();
                        if ((selEl.tagName.toLowerCase() == 'li') && (selEl.parentNode.tagName.toLowerCase() == tag)) {
                            YAHOO.log('We already have a list, undo it', 'info', 'Editor');
                            var el = selEl.parentNode;
                            var list = this._getDoc().createElement('span');
                            YAHOO.util.Dom.addClass(list, 'yui-non');
                            var str = '';
                            var lis = el.getElementsByTagName('li');
                            for (var i = 0; i < lis.length; i++) {
                                str += lis[i].innerHTML + '<br>';
                            }
                            list.innerHTML = str;

                        } else {
                            YAHOO.log('Create list item', 'info', 'Editor');
                            this._createCurrentElement(tag.toLowerCase());
                            var el = this.currentElement[0];
                            var list = this._getDoc().createElement(tag);
                            if (tag == 'ol') {
                                list.type = value;
                            }
                            var li = this._getDoc().createElement('li');
                            li.innerHTML = el.innerHTML + '&nbsp;';
                            list.appendChild(li);
                        }
                        el.parentNode.replaceChild(list, el);
                        exec = false;
                    } else {
                        var el = this._getSelectedElement();
                        if ((el.tagName.toLowerCase() == 'li') && (el.parentNode.tagName.toLowerCase() == tag) || (this.browser.ie && this._getRange().parentElement && this._getRange().parentElement.tagName && (this._getRange().parentElement.tagName.toLowerCase() == 'li'))) { //we are in a list..
                            YAHOO.log('We already have a list, undo it', 'info', 'Editor');
                            if (this.browser.ie) {
                                YAHOO.log('Undo IE', 'info', 'Editor');
                                exec = false;
                                var str = '';
                                var lis = el.parentNode.getElementsByTagName('li');
                                for (var i = 0; i < lis.length; i++) {
                                    str += lis[i].innerHTML + '<br>';
                                }
                                var newEl = this._getDoc().createElement('span');
                                newEl.innerHTML = str;
                                el.parentNode.parentNode.replaceChild(newEl, el.parentNode);
                            } else {
                                this.nodeChange();
                                this._getDoc().execCommand(action, '', el.parentNode);
                                this.nodeChange();
                            }
                        }
                        if (this.browser.opera) {
                            var self = this;
                            window.setTimeout(function() {
                                var lis = self._getDoc().getElementsByTagName('li');
                                for (var i = 0; i < lis.length; i++) {
                                    if (lis[i].innerHTML.toLowerCase() == '<br>') {
                                        lis[i].parentNode.parentNode.removeChild(lis[i].parentNode);
                                    }
                                }
                            },30);
                        }
                        if (this.browser.ie && exec) {
                            var html = '';
                            if (this._getRange().html) {
                                html = '<li>' + this._getRange().html+ '</li>';
                            } else {
                                html = '<li>' + this._getRange().text + '</li>';
                            }

                            this._getRange().pasteHTML('<' + tag + '>' + html + '</' + tag + '>');
                            exec = false;
                        }
                    }
                    break;
                case 'fontname':
                    var selEl = this._getSelectedElement();
                    this.currentFont = value;
                    if (selEl && selEl.tagName && !this._hasSelection()) {
                        YAHOO.util.Dom.setStyle(selEl, 'font-family', value);
                        exec = false;
                    }
                    break;
                case 'fontsize':
                    if ((this.currentElement.length > 0) && (!this._hasSelection())) {
                        YAHOO.util.Dom.setStyle(this.currentElement, 'fontSize', value);
                    } else {
                        this._createCurrentElement('span', {'fontSize': value });
                    }
                    exec = false;
                    break;
            }
            if (exec) {
                YAHOO.log('execCommand::(' + action + '), (' + value + ')', 'info', 'Editor');
                try {
                    this._getDoc().execCommand(action, false, value);
                } catch(e) {
                    YAHOO.log('execCommand Failed', 'error', 'Editor');
                }
            } else {
                YAHOO.log('OVERRIDE::execCommand skipped', 'warn', 'Editor');
            }
            this.on('afterExecCommand', function() {
                this.unsubscribeAll('afterExecCommand');
                this.nodeChange();
            });
            this.fireEvent('afterExecCommand', { type: 'afterExecCommand', target: this });
            
        },
        /**
        * @private
        * @method _createCurrentElement
        * @param {String} tagName (optional defaults to a) The tagname of the element that you wish to create
        * @param {Object} tagStyle (optional) Object literal containing styles to apply to the new element.
        * @description This is a work around for the various browser issues with execCommand. This method will run <code>execCommand('fontname', false, 'yui-tmp')</code> on the given selection.
        * It will then search the document for a span with the font-family set to <strong>yui-tmp</strong> and replace that with another span that has other information in it, then assign the new span to
        * <code>this.currentElement</code>, so we now have an element reference to the element that was just modified. At this point we can use standard DOM manipulation to change it as we see fit.
        */
        _createCurrentElement: function(tagName, tagStyle) {
            var tagName = ((tagName) ? tagName : 'a'),
                sel = this._getSelection(),
                tar = null,
                el = [],
                _doc = this._getDoc();
            
            if (this.currentFont) {
                if (!tagStyle) {
                    tagStyle = {};
                }
                tagStyle.fontFamily = this.currentFont;
                this.currentFont = null;
            }
            this.currentElement = [];

            var _elCreate = function() {
                switch (tagName) {
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                        var el = _doc.createElement(tagName);
                        break;
                    default:
                        var el = _doc.createElement('span');
                        YAHOO.util.Dom.addClass(el, 'yui-tag-' + tagName);
                        YAHOO.util.Dom.addClass(el, 'yui-tag');
                        el.setAttribute('tag', tagName);
                        el.tabIndex = 1;

                        for (var i in tagStyle) {
                            if (YAHOO.util.Lang.hasOwnProperty(tagStyle, i)) {
                                el.style[i] = tagStyle[i];
                            }
                        }
                        break;
                }
                return el;
            };

            if (!this._hasSelection()) {
                if (this._getDoc().queryCommandEnabled('insertimage')) {
                    this._getDoc().execCommand('insertimage', false, 'yui-tmp-img');
                    var imgs = this._getDoc().getElementsByTagName('img');
                    for (var i = 0; i < imgs.length; i++) {
                        if (imgs[i].getAttribute('src', 2) == 'yui-tmp-img') {
                            el = _elCreate();
                            imgs[i].parentNode.replaceChild(el, imgs[i]);
                            this.currentElement[this.currentElement.length] = el;
                            //this.currentElement = el;
                        }
                    }
                } else {
                    if (this.currentEvent) {
                        tar = YAHOO.util.Event.getTarget(this.currentEvent);
                    } else {
                        //For Safari..
                        tar = this._getDoc().body;                        
                    }
                }
                if (tar) {
                    /**
                    * @knownissue
                    * @browser Safari 2.x
                    * @description The issue here is that we have no way of knowing where the cursor position is
                    * inside of the iframe, so we have to place the newly inserted data in the best place that we can.
                    */
                    el = _elCreate();
                    if (tar.tagName.toLowerCase() == 'body') {
                        tar.appendChild(el);
                    } else if (tar.nextSibling) {
                        tar.parentNode.insertBefore(el, tar.nextSibling);
                    } else {
                        tar.parentNode.appendChild(el);
                    }
                    //this.currentElement = el;
                    this.currentElement[this.currentElement.length] = el;
                    this.currentEvent = null;
                    if (this.browser.webkit) {
                        //Force Safari to focus the new element
                        this._getSelection().setBaseAndExtent(el, 0, el, 0);
                        this._getSelection().collapse(true);   
                    }
                }
            } else {
                //Force CSS Styling for this action...
                this._setEditorStyle(true);
                this._getDoc().execCommand('fontname', false, 'yui-tmp');
                var _tmp = [];
                /* TODO: This needs to be cleaned up.. */
                var _tmp1 = this._getDoc().getElementsByTagName('font');
                var _tmp2 = this._getDoc().getElementsByTagName(this._getSelectedElement().tagName);
                var _tmp3 = this._getDoc().getElementsByTagName('span');
                var _tmp4 = this._getDoc().getElementsByTagName('i');
                var _tmp5 = this._getDoc().getElementsByTagName('b');
                for (var e = 0; e < _tmp1.length; e++) {
                    _tmp[_tmp.length] = _tmp1[e];
                }
                for (var e = 0; e < _tmp2.length; e++) {
                    _tmp[_tmp.length] = _tmp2[e];
                }
                for (var e = 0; e < _tmp3.length; e++) {
                    _tmp[_tmp.length] = _tmp3[e];
                }
                for (var e = 0; e < _tmp4.length; e++) {
                    _tmp[_tmp.length] = _tmp4[e];
                }
                for (var e = 0; e < _tmp5.length; e++) {
                    _tmp[_tmp.length] = _tmp5[e];
                }
                for (var i = 0; i < _tmp.length; i++) {
                    if ((YAHOO.util.Dom.getStyle(_tmp[i], 'font-family') == 'yui-tmp') || (_tmp[i].face && (_tmp[i].face == 'yui-tmp'))) {
                        var el = _elCreate();
                        el.innerHTML = _tmp[i].innerHTML;
                        if (_tmp[i].parentNode) {
                            _tmp[i].parentNode.replaceChild(el, _tmp[i]);
                            //this.currentElement = el;
                            this.currentElement[this.currentElement.length] = el;
                            this.currentEvent = null;
                            if (this.browser.webkit) {
                                //Force Safari to focus the new element
                                this._getSelection().setBaseAndExtent(el, 0, el, 0);
                                this._getSelection().collapse(true);   
                            }
                            if (this.browser.ie && tagStyle && tagStyle.fontSize) {
                                this._getSelection().empty();
                            }
                            if (this.browser.gecko) {
                                this._getSelection().collapseToStart();
                            }
                        }
                    }
                }
                var len = this.currentElement.length;
                for (var i = 0; i < len; i++) {
                    if ((i + 1) != len) { //Skip the last one in the list
                        if (this.currentElement[i] && this.currentElement[i].nextSibling) {
                            if (this.currentElement[i].tagName && (this.currentElement[i].tagName.toLowerCase() != 'br')) {
                                this.currentElement[this.currentElement.length] = this.currentElement[i].nextSibling;
                            }
                        }
                    }
                }
            }
        },
        /**
        * @method saveHTML
        * @description Cleans the HTML with the cleanHTML method then places that string back into the textarea.
        */
        saveHTML: function() {
            var html = this.cleanHTML();
            this.get('textarea').value = html;
            return html;
        },
        /**
        * @method setEditorHTML
        * @param {String} html The html content to load into the editor
        * @description Loads HTML into the editors body
        */
        setEditorHTML: function(html) {
            this._getDoc().body.innerHTML = html;
            this.nodeChange();
        },
        /**
        * @method getEditorHTML
        * @description Gets the unprocessed/unfiltered HTML from the editor
        */
        getEditorHTML: function() {
            return this._getDoc().body.innerHTML;
        },
        /**
        * @method cleanHTML
        * @param {String} html The unfiltered HTML
        * @description Process the HTML with a few regexes to clean it up and stabilize the output
        * @returns {String} The filtered HTML
        */
        cleanHTML: function(html) {
            //Start Filtering Output
            //Begin RegExs..
            if (!html) { 
                var html = this.getEditorHTML();
            }
            //Make some backups...
		    html = html.replace(/<div><br><\/div>/gi, '<YUI_BR>');
		    html = html.replace(/<p>(&nbsp;|&#160;)<\/p>/g, '<YUI_BR>');            
		    html = html.replace(/<p><br>&nbsp;<\/p>/gi, '<YUI_BR>');
		    html = html.replace(/<p>&nbsp;<\/p>/gi, '<YUI_BR>');
		    html = html.replace(/<br class="khtml-block-placeholder">/gi, '<YUI_BR>');
		    html = html.replace(/<br>/gi, '<YUI_BR>');
		    html = html.replace(/<br\/>/gi, '<YUI_BR>');
		    html = html.replace(/<img([^>]*)>/gi, '<YUI_IMG$1>');
		    html = html.replace(/<ul([^>]*)>/gi, '<YUI_UL$1>');
		    html = html.replace(/<\/ul>/gi, '<\/YUI_UL>');

            //Convert b and i tags to strong and em tags
		    html = html.replace(/<i([^>]*)>/gi, '<em$1>');
		    html = html.replace(/<\/i>/gi, '</em>');
		    html = html.replace(/<b([^>]*)>/gi, '<strong$1>');
		    html = html.replace(/<\/b>/gi, '</strong>');

		    html = html.replace(/<font/gi, '<font');
		    html = html.replace(/<\/font>/gi, '</font>');
		    html = html.replace(/<span/gi, '<span');
		    html = html.replace(/<\/span>/gi, '</span>');
		    html = html.replace(/<u/gi, '<u');
		    html = html.replace(/\/u>/gi, '/u>');

		    html = html.replace(/<ol([^>]*)>/gi, '<ol$1>');
		    html = html.replace(/\/ol>/gi, '/ol>');
		    html = html.replace(/<li/gi, '<li');
		    html = html.replace(/\/li>/gi, '/li>');

            //Handle the sudo A tags
            html = html.replace(new RegExp('<span ([^>]*) tag="a" ([^>]*)>([^>]*)<\/span>', 'gi'), '<a $1 $2>$3</a>');

            //Safari only regexes
            if (this.browser.webkit) {
                //<DIV><SPAN class="Apple-style-span" style="line-height: normal;">Test THis</SPAN></DIV>
                html = html.replace(/Apple-style-span/gi, '');
                html = html.replace(/style="line-height: normal;"/gi, '');
            }

            //yui-tag-a yui-tag yui-non yui-img
		    html = html.replace(/yui-tag-a/gi, '');
		    html = html.replace(/yui-tag-span/gi, '');
		    html = html.replace(/yui-tag/gi, '');
		    html = html.replace(/yui-non/gi, '');
		    html = html.replace(/yui-img/gi, '');
		    html = html.replace(/ tag="span"/gi, '');
		    html = html.replace(/ class=""/gi, '');
		    html = html.replace(/ class=" "/gi, '');
		    html = html.replace(/ class="  "/gi, '');
		    html = html.replace(/ target=""/gi, '');
		    html = html.replace(/ title=""/gi, '');

            //Other string cleanup
		    html = html.replace(/<br><li/gi, '<li');
            //<P><br>&nbsp;</P>
		    //html = html.replace(/<span >([^>]*)<\/span>/gi, '$1');
		    //html = html.replace(/<div>([^>]*)<\/div>/gi, '$1');
            
            
            //Replace our backups with the real thing
		    html = html.replace(/<YUI_BR>/g, '<br>');
		    html = html.replace(/<YUI_IMG([^>]*)>/g, '<img$1>');
		    html = html.replace(/<YUI_UL([^>]*)>/g, '<ul$1>');
		    html = html.replace(/<\/YUI_UL>/g, '<\/ul>');

            return html;
        },
        /**
        * @method clearEditorDoc
        * @description Clear the doc of the Editor
        */
        clearEditorDoc: function() {
            this._getDoc().body.innerHTML = '&nbsp;';
        },
        /**
        * @private
        * @method _renderPanel
        * @description Renders the panel used for Editor Windows to the document so we can start using it..
        * @returns {<a href="YAHOO.widget.Panel.html">YAHOO.widget.Panel</a>}
        */
        _renderPanel: function() {
            if (!YAHOO.widget.EditorInfo.panel) {
                var panel = new YAHOO.widget.Panel(this.EDITOR_PANEL_ID, {
                    width: '300px',
                    iframe: true,
                    visible: false,
                    underlay: 'none',
                    draggable: false,
                    close: false
                });
                YAHOO.widget.EditorInfo.panel = panel;
            } else {
                var panel = YAHOO.widget.EditorInfo.panel;
            }
            this.set('panel', panel);

            this.get('panel').setBody('---');
            this.get('panel').setHeader(' ');
            this.get('panel').setFooter(' ');
            if (this.DOMReady) {
                this.get('panel').render(document.body);
                Dom.addClass(this.get('panel').element, 'yui-editor-panel');
            } else {
                Event.onDOMReady(function() {
                    this.get('panel').render(document.body);
                    Dom.addClass(this.get('panel').element, 'yui-editor-panel');
                }, this, true);
            }
            this.get('panel').showEvent.subscribe(function() {
                YAHOO.util.Dom.setStyle(this.element, 'display', 'block');
            });
            return this.get('panel');
        },
        /**
        * @method openWindow
        * @param {<a href="YAHOO.widget.EditorWindow.html">YAHOO.widget.EditorWindow</a>} win A <a href="YAHOO.widget.EditorWindow.html">YAHOO.widget.EditorWindow</a> instance
        * @description Opens a new "window/panel"
        */
        openWindow: function(win) {
            this.toolbar.set('disabled', true); //Disable the toolbar when an editor window is open..
            Event.addListener(document, 'keypress', this._closeWindow, this, true);
            if (YAHOO.widget.EditorInfo.window.win && YAHOO.widget.EditorInfo.window.scope) {
                YAHOO.widget.EditorInfo.window.scope.closeWindow.call(YAHOO.widget.EditorInfo.window.scope);
            }
            YAHOO.widget.EditorInfo.window.win = win;
            YAHOO.widget.EditorInfo.window.scope = this;

            var self = this,
                xy = Dom.getXY(this.currentElement[0]),
                elXY = Dom.getXY(this.get('iframe').get('element')),
                panel = this.get('panel'),
                newXY = [(xy[0] + elXY[0] - 20), (xy[1] + elXY[1] + 10)],
                wWidth = (parseInt(win.attrs.width) / 2),
                align = 'center';

            this.fireEvent('beforeOpenWindow', { type: 'beforeOpenWindow', win: win, panel: panel });

            body = document.createElement('div');
            body.className = this.CLASS_PREFIX + '-body-cont';

            var _note = document.createElement('h3');
            _note.className = 'yui-editor-skipheader';
            _note.innerHTML = this.STR_CLOSE_WINDOW_NOTE;
            body.appendChild(_note);
            form = document.createElement('form');
            form.setAttribute('method', 'GET');
            var windowName = win.name;
            Event.addListener(form, 'submit', function(ev) {
                var evName = 'window' + windowName + 'Submit';
                self.fireEvent(evName, { type: evName, target: this });
                Event.stopEvent(ev);
            }, this, true);
            body.appendChild(form);

            Dom.setStyle(panel.element.firstChild, 'width', win.attrs.width);
            if (Lang.isObject(win.body)) { //Assume it's a reference
                form.appendChild(win.body);
            } else { //Assume it's a string
                var _tmp = document.createElement('div');
                _tmp.innerHTML = win.body;
                form.appendChild(_tmp);
            }
            var _close = document.createElement('span');
            _close.innerHTML = 'X';
            _close.title = this.STR_CLOSE_WINDOW;
            _close.className = 'close';
            Event.addListener(_close, 'click', function() {
                this.closeWindow();
            }, this, true);
            var _knob = document.createElement('span');
            _knob.innerHTML = '^';
            _knob.className = 'knob';
            win._knob = _knob;

            var _header = document.createElement('h3');
            _header.innerHTML = win.header;

            panel.cfg.setProperty('width', win.attrs.width);
            panel.setHeader(' '); //Clear the current header
            panel.appendToHeader(_header);
            _header.appendChild(_close);
            _header.appendChild(_knob);
            panel.setBody(' '); //Clear the current body
            panel.setFooter(' '); //Clear the current footer
            if (win.footer != null) {
                panel.setFooter(win.footer);
            }
            panel.appendToBody(body); //Append the new DOM node to it
            panel.showEvent.subscribe(function() {
                Event.addListener(panel.element, 'click', function(ev) {
                    Event.stopPropagation(ev);
                });
            }, this, true);
            panel.hideEvent.subscribe(function() {
                this.currentWindow = null;
                var evName = 'window' + windowName + 'Close';
                this.fireEvent(evName, { type: evName, target: this });

            }, this, true);
            this.currentWindow = win;
            this.moveWindow(true);
            panel.show();
            this.fireEvent('afterOpenWindow', { type: 'afterOpenWindow', win: win, panel: panel });
        },
        /**
        * @method moveWindow
        * @param {Boolean} force Boolean to tell it to move but not use any animation (Usually done the first time the window is loaded.)
        * @description Realign the window with the currentElement and reposition the knob above the panel.
        */
        moveWindow: function(force) {
            if (!this.currentWindow) {
                return false;
            }
            var win = this.currentWindow,
                xy = Dom.getXY(this.currentElement[0]),
                elXY = Dom.getXY(this.get('iframe').get('element')),
                panel = this.get('panel'),
                //newXY = [(xy[0] + elXY[0] - 20), (xy[1] + elXY[1] + 10)],
                newXY = [(xy[0] + elXY[0]), (xy[1] + elXY[1])],
                wWidth = (parseInt(win.attrs.width) / 2),
                align = 'center',
                orgXY = panel.cfg.getProperty('xy'),
                _knob = win._knob;

            newXY[0] = ((newXY[0] - wWidth) + 20);
            //Account for the Scroll bars in a scrolled editor window.
            newXY[0] = newXY[0] - Dom.getDocumentScrollLeft(this._getDoc());
            newXY[1] = newXY[1] - Dom.getDocumentScrollTop(this._getDoc());
            


            if (this.currentElement[0].tagName && (this.currentElement[0].tagName.toLowerCase() == 'img')) {
                if (this.currentElement[0].src.indexOf(this.get('blankimage')) != -1) {
                    newXY[0] = (newXY[0] + (75 / 2)); //Placeholder size
                    newXY[1] = (newXY[1] + 75); //Placeholder sizea
                } else {
                    var w = parseInt(this.currentElement[0].width);
                    var h = parseInt(this.currentElement[0].height);
                    newXY[0] = (newXY[0] + (w / 2));
                    newXY[1] = (newXY[1] + h);
                }
                newXY[1] = newXY[1] + 15;
            } else {
                if (Dom.getStyle(this.currentElement[0], 'fontSize').indexOf('px') != -1) {
                    newXY[1] = newXY[1] + parseInt(Dom.getStyle(this.currentElement[0], 'fontSize')) + 5;
                } else {
                    newXY[1] = newXY[1] + 20;
                }
            }
            if (newXY[0] < elXY[0]) {
                newXY[0] = elXY[0] + 5;
                align = 'left';
            }

            if ((newXY[0] + (wWidth * 2)) > (elXY[0] + parseInt(this.get('iframe').get('element').clientWidth))) {
                newXY[0] = ((elXY[0] + parseInt(this.get('iframe').get('element').clientWidth)) - (wWidth * 2) - 5);
                align = 'right';
            }
            
            try {
                var xDiff = (newXY[0] - orgXY[0]);
                var yDiff = (newXY[1] - orgXY[1]);
            } catch (e) {
                var xDiff = 0;
                var yDiff = 0;
            }
            
            //Convert negative numbers to positive so we can get the difference in distance
            xDiff = ((xDiff < 0) ? (xDiff * -1) : xDiff);
            yDiff = ((yDiff < 0) ? (yDiff * -1) : yDiff);

            if (((xDiff > 10) || (yDiff > 10)) || force) { //Only move the window if it's supposed to move more than 10px or force was passed (new window)
                var _knobLeft = 0,
                    elW = 0;

                if (this.currentElement[0].width) {
                    elW = (parseInt(this.currentElement[0].width) / 2);
                }

                var leftOffset = xy[0] + elXY[0] + elW;
                _knobLeft = leftOffset - newXY[0];
                //Check to see if the knob will go off either side & reposition it
                if (_knobLeft > (parseInt(win.attrs.width) - 40)) {
                    _knobLeft = parseInt(win.attrs.width) - 40;
                } else if (_knobLeft < 40) {
                    _knobLeft = 40;
                }
                if (isNaN(_knobLeft)) {
                    _knobLeft = 40;
                }
                if (force) {
                    if (_knob) {
                        _knob.style.left = _knobLeft + 'px';
                    }
                    if (this.get('animate')) {
                        Dom.setStyle(panel.element, 'opacity', '0');
                        var anim = new YAHOO.util.Anim(panel.element, {
                            opacity: {
                                from: 0,
                                to: 1
                            }
                        }, .1, YAHOO.util.Easing.easeOut);
                        panel.cfg.setProperty('xy', newXY);
                        anim.onComplete.subscribe(function() {
                            if (this.browser.ie) {
                                panel.element.style.filter = 'none';
                            }
                        }, this, true);
                        anim.animate();
                    } else {
                        panel.cfg.setProperty('xy', newXY);
                    }
                } else {
                    if (this.get('animate')) {
                        var anim = new YAHOO.util.Anim(panel.element, {}, .5, YAHOO.util.Easing.easeOut);
                        anim.attributes = {
                            top: {
                                to: newXY[1]
                            },
                            left: {
                                to: newXY[0]
                            }
                        }
                        anim.onComplete.subscribe(function() {
                            panel.cfg.setProperty('xy', newXY);
                        });
                        //We have to animate the iframe shim at the same time as the panel or we get scrollbar bleed ..
                        var iframeAnim = new YAHOO.util.Anim(panel.iframe, anim.attributes, .5, YAHOO.util.Easing.easeOut)

                        var _knobAnim = new YAHOO.util.Anim(_knob, {
                            left: {
                                to: _knobLeft
                            }
                        }, .75, YAHOO.util.Easing.easeOut);
                        anim.animate();
                        iframeAnim.animate();
                        _knobAnim.animate();
                    } else {
                        _knob.style.left = _knobLeft + 'px';
                        panel.cfg.setProperty('xy', newXY);
                    }
                }
            }
        },
        /**
        * @private
        * @method _closeWindow
        * @description Close the currently open EditorWindow with the Escape key.
        * @param {Event} ev The keypress Event that we are trapping
        */
        _closeWindow: function(ev) {
            if (ev.keyCode == 27) {
                if (this.currentWindow) {
                    this.closeWindow();
                }
            }
        },
        /**
        * @method closeWindow
        * @description Close the currently open EditorWindow.
        */
        closeWindow: function() {
            YAHOO.widget.EditorInfo.window = {};
            this.fireEvent('closeWindow', { type: 'closeWindow', win: this.currentWindow });
            this.currentWindow = null;
            this.get('panel').hide();
            this.get('panel').cfg.setProperty('xy', [-900,-900]);
            this.get('panel').syncIframe(); //Needed to move the iframe with the hidden panel
            this.unsubscribeAll('afterExecCommand');
            this.toolbar.set('disabled', false); //enable the toolbar now that the window is closed
            this._focusWindow();
            Event.removeListener(document, 'keypress', this._closeWindow);
        },
        /**
        * @method destroy
        * @description Destroys the editor, all of it's elements and objects.
        * @return {Boolean}
        */
        destroy: function() {
            this.saveHTML();
            this.toolbar.destroy();
            Dom.setStyle(this.get('textarea'), 'display', 'block');
            var textArea = this.get('textarea');
            this.get('element_cont').get('parentNode').replaceChild(textArea, this.get('element_cont').get('element'));
            this.get('element_cont').get('element').innerHTML = '';
            //Brutal Object Destroy
            for (var i in this) {
                if (Lang.hasOwnProperty(this, i)) {
                    this[i] = null;
                }
            }
            return true;
        },        
        /**
        * @method toString
        * @description Returns a string representing the editor.
        * @return {String}
        */
        toString: function() {
            var str = 'Editor';
            if (this.get && this.get('element_cont')) {
                str = 'Editor (#' + this.get('element_cont').get('id') + ')' + ((this.get('disabled') ? ' Disabled' : ''));
            }
            return str;
        }
    });

/**
* @event toolbarLoaded
* @description Event is fired during the render process directly after the Toolbar is loaded. Allowing you to attach events to the toolbar. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event afterRender
* @description Event is fired after the render process finishes. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorContentLoaded
* @description Event is fired after the editor iframe's document fully loads and fires it's onload event. From here you can start injecting your own things into the document. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorMouseUp
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorMouseDown
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorDoubleClick
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorKeyUp
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorKeyPress
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event editorKeyDown
* @param {Event} ev The DOM Event that occured
* @description Passed through HTML Event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event beforeNodeChange
* @description Event fires at the beginning of the nodeChange process. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event afterNodeChange
* @description Event fires at the end of the nodeChange process. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event beforeExecCommand
* @description Event fires at the beginning of the execCommand process. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event afterExecCommand
* @description Event fires at the end of the execCommand process. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event beforeOpenWindow
* @param {<a href="YAHOO.widget.EditorWindow.html">EditorWindow</a>} win The EditorWindow object
* @param {Overlay} panel The Overlay object that is used to create the window.
* @description Event fires before an Editor Window is opened. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event afterOpenWindow
* @param {<a href="YAHOO.widget.EditorWindow.html">EditorWindow</a>} win The EditorWindow object
* @param {Overlay} panel The Overlay object that is used to create the window.
* @description Event fires after an Editor Window is opened. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event closeWindow
* @param {<a href="YAHOO.widget.EditorWindow.html">EditorWindow</a>} win The EditorWindow object
* @description Event fires after an Editor Window is closed. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event windowCMDOpen
* @param {<a href="YAHOO.widget.EditorWindow.html">EditorWindow</a>} win The EditorWindow object
* @param {Overlay} panel The Overlay object that is used to create the window.
* @description Dynamic event fired when an <a href="YAHOO.widget.EditorWindow.html">EditorWindow</a> is opened.. The dynamic event is based on the name of the window. Example Window: createlink, opening this window would fire the windowcreatelinkOpen event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/
/**
* @event windowCMDClose
* @param {<a href="YAHOO.widget.EditorWindow.html">EditorWindow</a>} win The EditorWindow object
* @param {Overlay} panel The Overlay object that is used to create the window.
* @description Dynamic event fired when an <a href="YAHOO.widget.EditorWindow.html">EditorWindow</a> is closed.. The dynamic event is based on the name of the window. Example Window: createlink, opening this window would fire the windowcreatelinkClose event. See <a href="YAHOO.util.Element.html#addListener">Element.addListener</a> for more information on listening for this event.
* @type YAHOO.util.CustomEvent
*/

/**
     * @description Singleton object used to track the open window objects and panels across the various open editors
     * @class EditorInfo
     * @static
    */
    YAHOO.widget.EditorInfo = {
        /**
        * @private
        * @property window
        * @description A reference to the currently open window object in any editor on the page.
        * @type Object <a href="YAHOO.widget.EditorWindow.html">YAHOO.widget.EditorWindow</a>
        */
        window: {},
        /**
        * @private
        * @property panel
        * @description A reference to the currently open panel in any editor on the page.
        * @type Object <a href="YAHOO.widget.Panel.html">YAHOO.widget.Panel</a>
        */
        panel: null
    }

    /**
     * @description Class to hold Window information between uses. We use the same panel to show the windows, so using this will allow you to configure a window before it is shown.
     * This is what you pass to Editor.openWindow();. These parameters will not take effect until the openWindow() is called in the editor.
     * @class EditorWindow
     * @param {String} name The name of the window.
     * @param {Object} attrs Attributes for the window. Current attributes used are : height and width
    */
    YAHOO.widget.EditorWindow = function(name, attrs) {
        /**
        * @private
        * @property name
        * @description A unique name for the window
        */
        this.name = name.replace(' ', '_');
        /**
        * @private
        * @property attrs
        * @description The window attributes
        */
        this.attrs = attrs;
    }

    YAHOO.widget.EditorWindow.prototype = {
        /**
        * @private
        * @property _cache
        * @description Holds a cache of the DOM for the window so we only have to build it once..
        */
        _cache: null,
        /**
        * @private
        * @property header
        * @description Holder for the header of the window, used in Editor.openWindow
        */
        header: null,
        /**
        * @private
        * @property body
        * @description Holder for the body of the window, used in Editor.openWindow
        */
        body: null,
        /**
        * @private
        * @property footer
        * @description Holder for the footer of the window, used in Editor.openWindow
        */
        footer: null,
        /**
        * @method setHeader
        * @description Sets the header for the window.
        * @param {String/HTMLElement} str The string or DOM reference to be used as the windows header.
        */
        setHeader: function(str) {
            this.header = str;
        },
        /**
        * @method setBody
        * @description Sets the body for the window.
        * @param {String/HTMLElement} str The string or DOM reference to be used as the windows body.
        */
        setBody: function(str) {
            this.body = str;
        },
        /**
        * @method setFooter
        * @description Sets the footer for the window.
        * @param {String/HTMLElement} str The string or DOM reference to be used as the windows footer.
        */
        setFooter: function(str) {
            this.footer = str;
        },
        /**
        * @method toString
        * @description Returns a string representing the EditorWindow.
        * @return {String}
        */
        toString: function() {
            return 'Editor Window (' + this.name + ')';
        }
    };


    
})();
YAHOO.register("editor", YAHOO.widget.Editor, {version: "2.3.0", build: "442"});
