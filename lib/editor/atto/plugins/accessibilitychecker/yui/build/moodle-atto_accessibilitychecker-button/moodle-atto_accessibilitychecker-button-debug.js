YUI.add('moodle-atto_accessibilitychecker-button', function (Y, NAME) {

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
 * Atto text editor accessibilitychecker plugin.
 *
 * This plugin adds some functions to do things that screen readers do not do well.
 * Specifically, listing the active styles for the selected text,
 * listing the images in the page, listing the links in the page.
 *
 * @package    atto_accessibilitychecker
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_accessibilitychecker = M.atto_accessibilitychecker || {
    /**
     * The window used to display the accessibility ui.
     *
     * @property dialogue
     * @type M.core.dialogue
     * @default null
     */
    dialogue : null,

    /**
     * Array of nodes that have an accessibility problem
     *
     * @property displayedwarnings
     * @type Array
     * @default null
     */
    displayedwarnings: [],

    /**
     * Display the ui dialogue.
     *
     * @method init
     * @param Event e
     * @param string elementid
     */
    display_ui : function(e, elementid) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        var dialogue;
        if (!M.atto_accessibilitychecker.dialogue) {
            dialogue = new M.core.dialogue({
                visible: false,
                modal: true,
                close: true,
                draggable: true,
                width: '800px'
            });
            dialogue.set('headerContent', M.util.get_string('pluginname', 'atto_accessibilitychecker'));
            dialogue.render();
        } else {
            dialogue = M.atto_accessibilitychecker.dialogue;
            // Clear the array of previously displayed warnings.
            M.atto_accessibilitychecker.displayedwarnings = [];
        }

        dialogue.set('bodyContent', M.atto_accessibilitychecker.get_report(elementid));
        dialogue.centerDialogue();

        // Add ability to select problem areas in the editor.
        Y.all('.accessibilitywarnings li').on('click', function(e) {
            e.preventDefault();

            var index = e.target.getAttribute("data-index");
            var node = M.atto_accessibilitychecker.displayedwarnings[index];

            M.atto_accessibilitychecker.dialogue.hide();
            if (node) {
                M.editor_atto.set_selection(M.editor_atto.get_selection_from_node(node));
            }
        });

        dialogue.show();
        M.atto_accessibilitychecker.dialogue = dialogue;
    },

    /**
     * Add this button to the form.
     *
     * @method init
     * @param {Object} params
     */
    init : function(params) {
        var iconurl = M.util.image_url('e/visual_blocks', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'accessibilitychecker', iconurl, params.group, this.display_ui);
    },

    /**
     * Generate the HTML that lists the found warnings.
     *
     * @method add_warnings
     * @param Y.Node list - node to append the html to.
     * @param String description - description of this failure.
     * @param Y.Node[] nodes - list of failing nodes.
     * @param boolean imagewarnings - true if the warnings are related to images, false if text.
     */
    add_warnings : function(list, description, nodes, imagewarnings) {
        var warning, fails, i, key, src, textfield;

        if (nodes.length > 0) {
            warning = Y.Node.create('<p>' + description + '</p>');
            fails = Y.Node.create('<ol class="accessibilitywarnings"></ol>');
            i = 0;
            for (i = 0; i < nodes.length; i++) {
                if (imagewarnings) {
                    key = 'image_'+i;
                    src = nodes[i].getAttribute('src');

                    fails.append(Y.Node.create('<li><a data-index="'+key+'" href="#"><img data-index="'+key+'" src="' + src + '" /> '+src+'</a></li>'));
                } else {
                    key = 'text_'+i;

                    textfield = ('innerText' in nodes[i])? 'innerText' : 'textContent';
                    fails.append(Y.Node.create('<li><a href="#" data-index="'+key+'">' + nodes[i].get(textfield) + '</a></li>'));
                }
                M.atto_accessibilitychecker.displayedwarnings[key] = nodes[i];
            }

            warning.append(fails);
            list.append(warning);
        }
    },

    /**
     * Convert a css color to a luminance value.
     *
     * @method get_luminance_from_css_color
     * @param {String} colortext
     * @return {Integer}
     */
    get_luminance_from_css_color : function(colortext) {
        var color;

        if (colortext === 'transparent') {
            colortext = '#ffffff';
        }
        color = Y.Color.toArray(Y.Color.toRGB(colortext));

        // Algorithm from "http://www.w3.org/TR/WCAG20-GENERAL/G18.html".
        var part1 = function(a) {
            a = parseInt(a, 10) / 255.0;
            if (a <= 0.03928) {
                a = a/12.92;
            } else {
                a = Math.pow(((a + 0.055)/1.055), 2.4);
            }
            return a;
        };

        var r1 = part1(color[0]),
            g1 = part1(color[1]),
            b1 = part1(color[2]);

        return 0.2126 * r1 + 0.7152 * g1 + 0.0722 * b1;
    },

    /**
     * List the accessibility warnings for the current editor
     *
     * @method list_warnings
     * @param string elementid
     * @return String
     */
    list_warnings : function(elementid) {

        var list = Y.Node.create('<div></div>');

        var editable = M.editor_atto.get_editable_node(elementid);

        var problemnodes = [];

        // Images with no alt text or dodgy alt text.
        var alt;
        editable.all('img').each(function (img) {
            alt = img.getAttribute('alt');
            if (typeof alt === 'undefined' || alt === '') {
                if (img.getAttribute('role') !== 'presentation') {
                    problemnodes.push(img);
                }
            }
        }, this);

        this.add_warnings(list, M.util.get_string('imagesmissingalt', 'atto_accessibilitychecker'), problemnodes, true);

        // Contrast ratios.
        problemnodes = [];
        var foreground, background, lum1, lum2, ratio;
        editable.all('*').each(function (node) {
            // Check for non-empty text.
            if (Y.Lang.trim(node.get('text')) !== '') {
                foreground = node.getComputedStyle('color');
                background = node.getComputedStyle('backgroundColor');

                lum1 = this.get_luminance_from_css_color(foreground);
                lum2 = this.get_luminance_from_css_color(background);

                // Algorithm from "http://www.w3.org/TR/WCAG20-GENERAL/G18.html".
                if (lum1 > lum2) {
                    ratio = (lum1 + 0.05) / (lum2 + 0.05);
                } else {
                    ratio = (lum2 + 0.05) / (lum1 + 0.05);
                }
                if (ratio <= 4.5) {
                    Y.log('Contrast ratio is too low: ' + ratio +
                          ' Colour 1: ' + foreground +
                          ' Colour 2: ' + background +
                          ' Luminance 1: ' + lum1 +
                          ' Luminance 2: ' + lum2);

                    // We only want the highest node with dodgy contrast reported.
                    var i = 0, found = false;
                    for (i = 0; i < problemnodes.length; i++) {
                        if (node.ancestors('*').indexOf(problemnodes[i]) !== -1) {
                            // Do not add node - it already has a parent in the list.
                            found = true;
                            break;
                        } else if (problemnodes[i].ancestors('*').indexOf(node) !== -1) {
                            // Replace the existing node with this one because it is higher up the DOM.
                            problemnodes[i] = node;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        problemnodes.push(node);
                    }
                }
            }
        }, this);

        this.add_warnings(list, M.util.get_string('needsmorecontrast', 'atto_accessibilitychecker'), problemnodes, false);

        if (!list.hasChildNodes()) {
            list.append('<p>' + M.util.get_string('nowarnings', 'atto_accessibilitychecker') + '</p>');
        }
        // Append the list of current styles.
        return list;
    },

    /**
     * Return the HTML of the form to show in the dialogue.
     *
     * @method get_report
     * @param string elementid
     * @return string
     */
    get_report : function(elementid) {
        // Current styles.
        var html = '<div style="word-wrap: break-word;"></div>';

        var content = Y.Node.create(html);

        content.append(this.list_warnings(elementid));

        return content;
    }

};


}, '@VERSION@', {"requires": ["node", "escape", "color-base"]});
