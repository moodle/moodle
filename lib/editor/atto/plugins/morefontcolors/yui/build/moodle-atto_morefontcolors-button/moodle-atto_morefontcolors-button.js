YUI.add('moodle-atto_morefontcolors-button', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Atto text editor integration version file.
 *
 * @package    atto_morefontcolors
 * @copyright  2014 Université de Lausanne
 * @author     Nicolas Dunand <nicolas.dunand@unil.ch>
 * @author     Rossiani Wijaya  <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_morefontcolors-button
 */

/**
 * Atto text editor morefontcolors plugin.
 *
 * @namespace M.atto_morefontcolors
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var TEMPLATE = '' +
    '<form id="atto_morefontcolors_dialogue">' +
        '<div class="picker">' +
            '<div id="atto_morefontcolors_dialogue_hue-dial"></div>' +
            '<div class="sliders">' +
                '<div id="atto_morefontcolors_dialogue_sat-slider">' +
    '<strong>{{get_string "saturation" component}}<span></span></strong></div>' +
                '<div id="atto_morefontcolors_dialogue_lum-slider">' +
    '<strong>{{get_string "luminance" component}}<span></span></strong></div>' +
            '</div>' +
            '<div class="color"></div>' +
        '</div>' +
        '<div class="yui3-g picker-output">' +
            '<div class="yui3-u-1-3">' +
                '<strong>{{get_string "hexadecimal" component}}</strong>' +
                '<input type="text" id="atto_morefontcolors_dialogue_hex-output">' +
            '</div>' +
            '<div class="yui3-u-1-3">' +
               '<strong>{{get_string "rgb" component}}</strong>' +
               '<input type="text" id="atto_morefontcolors_dialogue_rgb-output">' +
            '</div>' +
            '<div class="yui3-u-1-3">' +
                '<strong>{{get_string "hsl" component}}</strong>' +
                '<input type="text" id="atto_morefontcolors_dialogue_hsl-output">' +
            '</div>' +
        '</div>' +
        '<div class="mdl-align">' +
            '<button type="submit" class="submit">{{get_string "submit" component}}</button>' +
        '</div>' +
    '</form>';
Y.namespace('M.atto_morefontcolors').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    /**
     * A reference to the HTMl of the dialuge content
     *
     * @property _content
     * @type String
     * @private
     */
    _content: null,

    initializer: function(config) {
        var items = [];
        var colors = this.get('colors');
        Y.Array.each(colors, function(colors) {
            if (colors.trim()) {
                var color_array = colors.split(/\s+/);
                var stringOfDiv = "";
                for (var i = 0; i < color_array.length; i++) {
                    var color = color_array[i].trim();
                    if (/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color)) {
                        stringOfDiv = stringOfDiv +
                        '<div style="width: 20px; margin-right: 5px; height: 20px; border: 1px solid #CCC; background-color: ' +
                        color +
                        '" data-color="' + color + '"></div>';
                    }
                }
                items.push({
                    text: stringOfDiv,
                    callback: this._changeStyle
                });
            }
        });
        if (config.allowcustom === '1') {
            items.push({
                text: '<div style="width: 20px; height: 20px; border: 1px solid #CCC;" id="atto_morefontcolors_customicon"></div>',
                callbackArgs: 'custom',
                callback: this._changeStyle
            });
        }
        this.addToolbarMenu({
            icon: 'e/text_color',
            overlayWidth: '4',
            menuColor: '#333333',
            globalItemConfig: {
                inlineFormat: true,
                callback: this._changeStyle
            },
            items: items
        });
    },

    /**
     * Change the font color to the specified color.
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} color The new font color
     * @private
     */
    _changeStyle: function(e, color) {
        if (color === 'custom') {
            this._customColor();
        } else {
            this.get('host').formatSelectionInlineStyle({
                color: e.target.getAttribute("data-color")
            });
        }
    },

    /**
     * Change the font color to the custom color
     *
     * @method _customColor
     * @param {EventFacade} e
     * @private
     */
    _customColor: function() {
        var template = Y.Handlebars.compile(TEMPLATE),
            dialogue = this.getDialogue({
                headerContent: M.util.get_string('customcolor', "atto_morefontcolors"),
                width: '470px',
                focusAfterHide: true
            });

        this._currentSelection = this.get('host').getSelection();

        this._content = Y.Node.create(template({
            component: "atto_morefontcolors"
        }));

        dialogue.set('bodyContent', this._content).show();

        this._initiateColorPicker();

        this._content.one('.submit').on('click', function(e) {
            e.preventDefault();
            // Hide the dialogue.
            this.getDialogue({
                focusAfterHide: null
            }).hide();

            var color = Y.one('#' + dialogue.get('id') +' #atto_morefontcolors_dialogue_rgb-output').get('value');

            this.get('host').setSelection(this._currentSelection);

            this.get('host').formatSelectionInlineStyle({
                color: color
            });
        }, this);
    },

    /**
     * Initialize the color picker dialogue
     *
     * @method _initalizeColorPicker
     * @private
     */
    _initiateColorPicker: function() {
        var elementid = this.getDialogue().get('id'),
            dialogueselector = '#' + elementid + ' #atto_morefontcolors_dialogue';
        YUI().use('dial', 'slider', 'event-valuechange', 'color', function (Y) {
            Y.one(dialogueselector + ' .picker').addClass('yui3-skin-sam');
            var hue = new Y.Dial({
                min: 0,
                max: 360,
                stepsPerRevolution: 360,
                continuous: true,
                centerButtonDiameter: 0.4,
                render: dialogueselector + ' #atto_morefontcolors_dialogue_hue-dial'
            }),
            sat = new Y.Slider({
                min: 0,
                max: 100,
                value: 100,
                render: dialogueselector + ' #atto_morefontcolors_dialogue_sat-slider'
            }),
            lum = new Y.Slider({
                min: 0,
                max: 100,
                value: 50,
                render: dialogueselector + ' #atto_morefontcolors_dialogue_lum-slider'
            }),
            satValue = Y.one(dialogueselector + ' #atto_morefontcolors_dialogue_sat-slider span'),
            lumValue = Y.one(dialogueselector + ' #atto_morefontcolors_dialogue_lum-slider span'),
            color = Y.one(dialogueselector + ' .color'),
            hexOutput = Y.one(dialogueselector + ' #atto_morefontcolors_dialogue_hex-output'),
            rgbOutput = Y.one(dialogueselector + ' #atto_morefontcolors_dialogue_rgb-output'),
            hslOutput = Y.one(dialogueselector + ' #atto_morefontcolors_dialogue_hsl-output'),
            focused = null,
            setFocused = function(e) {
                focused = e.currentTarget;
            },
            unsetFocused = function (e) {
                if (focused === e.currentTarget) {
                    focused = null;
                }
            },
            updateOutput = function(hslString) {
                if (hexOutput !== focused) {
                    hexOutput.set('value', Y.Color.toHex(hslString));
                }
                if (rgbOutput !== focused) {
                    rgbOutput.set('value', Y.Color.toRGB(hslString));
                }
                if (hslOutput !== focused) {
                    hslOutput.set('value', hslString);
                }
            },
            setPickerUI = function(hsl) {
                if (typeof hsl.h !== 'undefined') {
                    hue.set('value', +hsl.h);
                }
                if (typeof hsl.s !== 'undefined') {
                    sat.set('value', +hsl.s);
                }
               if (typeof hsl.l !== 'undefined') {
                    lum.set('value', +hsl.l);
                }
            },
            updatePickerFromValue = function(e) {
                var val = e.newVal,
                    hsl = [];
                if (Y.Color.toArray(val)) {
                    hsl = Y.Color.toArray(Y.Color.toHSL(val));
                    setPickerUI({
                        h: hsl[0],
                        s: hsl[1],
                        l: hsl[2]
                    });
               }
            },
            updatePickerUI = function() {
                var h = hue.get('value'),
                    s = sat.get('value'),
                    l = lum.get('value'),
                    hslString = Y.Color.fromArray([h, s, l], Y.Color.TYPES.HSL),
                    hexString = Y.Color.toHex(hslString);
                satValue.set('text', s + '%');
                lumValue.set('text', l + '%');
                color.setStyle('backgroundColor', hexString);
                updateOutput(hslString);
            };

        hexOutput.on('focus', setFocused);
        hexOutput.on('blur', unsetFocused);
        hexOutput.on('valueChange', updatePickerFromValue);

        rgbOutput.on('focus', setFocused);
        rgbOutput.on('blur', unsetFocused);
        rgbOutput.on('valueChange', updatePickerFromValue);

        hslOutput.on('focus', setFocused);
        hslOutput.on('blur', unsetFocused);
        hslOutput.on('valueChange', updatePickerFromValue);

        hue.after('valueChange', function() {
            updatePickerUI();
        });

        sat.after('thumbMove', function() {
            updatePickerUI();
        });

        lum.after('thumbMove', function() {
            lumValue.set('text', lum.get('value') + '%');
            updatePickerUI();
        });

        updatePickerUI();
    });
    }
}, {
    ATTRS: {
        /**
         * The list of available colors
         *
         * @attribute colors
         * @type array
         * @default {}
         */
        colors: {
            value: {}
        }
    }
}, {
    ATTRS: {
        /**
         * The list of available colors
         *
         * @attribute colors
         * @type array
         * @default {}
         */
        colors: {
            value: {}
        }
    }
});


}, '@VERSION@');
