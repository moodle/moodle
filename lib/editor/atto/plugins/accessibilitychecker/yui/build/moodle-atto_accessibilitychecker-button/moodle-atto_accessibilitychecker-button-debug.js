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

/*
 * @package    atto_accessibilitychecker
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_accessibilitychecker-button
 */

/**
 * Accessibility Checking tool for the Atto editor.
 *
 * @namespace M.atto_accessibilitychecker
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENT = 'atto_accessibilitychecker';

Y.namespace('M.atto_accessibilitychecker').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    initializer: function() {
        this.addButton({
            icon: 'e/accessibility_checker',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the Accessibility Checker tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENT),
            width: '500px',
            focusAfterHide: true
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
    },

    /**
     * Return the dialogue content for the tool.
     *
     * @method _getDialogueContent
     * @private
     * @return {Node} The content to place in the dialogue.
     */
    _getDialogueContent: function() {
        var content = Y.Node.create('<div style="word-wrap: break-word;"></div>');
        content.append(this._getWarnings());

        // Add ability to select problem areas in the editor.
        content.delegate('click', function(e) {
            e.preventDefault();

            var host = this.get('host'),
                node = e.currentTarget.getData('sourceNode'),
                dialogue = this.getDialogue();

            if (node) {
                // Focus on the editor as we hide the dialogue.
                dialogue.set('focusAfterHide', this.editor).hide();

                // Then set the selection.
                host.setSelection(host.getSelectionFromNode(node));
            } else {
                // Hide the dialogue.
                dialogue.hide();
            }
        }, 'a', this);

        return content;
    },

    /**
     * Find all problems with the content editable region.
     *
     * @method _getWarnings
     * @return {Node} A complete list of all warnings and problems.
     * @private
     */
    _getWarnings: function() {
        var problemNodes,
            list = Y.Node.create('<div></div>');

        // Images with no alt text or dodgy alt text.
        problemNodes = [];
        this.editor.all('img').each(function(img) {
            var alt = img.getAttribute('alt');
            if (typeof alt === 'undefined' || alt === '') {
                if (img.getAttribute('role') !== 'presentation') {
                    problemNodes.push(img);
                }
            }
        }, this);
        this._addWarnings(list, M.util.get_string('imagesmissingalt', COMPONENT), problemNodes, true);

        problemNodes = [];
        this.editor.all('*').each(function(node) {
            var foreground,
                background,
                ratio,
                lum1,
                lum2;

            // Check for non-empty text.
            if (node.hasChildNodes() && Y.Lang.trim(node._node.childNodes[0].nodeValue) !== '') {
                foreground = Y.Color.fromArray(
                    this._getComputedBackgroundColor(node, node.getComputedStyle('color')),
                    Y.Color.TYPES.RGBA
                );
                background = Y.Color.fromArray(this._getComputedBackgroundColor(node), Y.Color.TYPES.RGBA);

                lum1 = this._getLuminanceFromCssColor(foreground);
                lum2 = this._getLuminanceFromCssColor(background);

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
                    var i = 0;
                    var found = false;
                    for (i = 0; i < problemNodes.length; i++) {
                        if (node.ancestors('*').indexOf(problemNodes[i]) !== -1) {
                            // Do not add node - it already has a parent in the list.
                            found = true;
                            break;
                        } else if (problemNodes[i].ancestors('*').indexOf(node) !== -1) {
                            // Replace the existing node with this one because it is higher up the DOM.
                            problemNodes[i] = node;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        problemNodes.push(node);
                    }
                }
            }
        }, this);
        this._addWarnings(list, M.util.get_string('needsmorecontrast', COMPONENT), problemNodes, false);

        // Check for lots of text with no headings.
        if (this.editor.get('text').length > 1000 && !this.editor.one('h3, h4, h5')) {
            this._addWarnings(list, M.util.get_string('needsmoreheadings', COMPONENT), [this.editor], false);
        }

        // Check for tables with no captions.
        problemNodes = [];
        this.editor.all('table').each(function(table) {
            var caption = table.one('caption');
            if (caption === null || caption.get('text').trim() === '') {
                problemNodes.push(table);
            }
        }, this);
        this._addWarnings(list, M.util.get_string('tablesmissingcaption', COMPONENT), problemNodes, false);

        // Check for tables with merged cells.
        problemNodes = [];
        this.editor.all('table').each(function(table) {
            var caption = table.one('[colspan],[rowspan]');
            if (caption !== null) {
                problemNodes.push(table);
            }
        }, this);
        this._addWarnings(list, M.util.get_string('tableswithmergedcells', COMPONENT), problemNodes, false);

        // Check for tables with no row/col headers
        problemNodes = [];
        this.editor.all('table').each(function(table) {
            if (table.one('tr').one('td')) {
                // First row has a non-header cell, so all rows must have at least one header.
                table.all('tr').some(function(row) {
                    var header = row.one('th');
                    if (!header || (header.get('text').trim() === '')) {
                        problemNodes.push(table);
                        return true;
                    }
                    return false;
                }, this);
            } else {
                // First row must have at least one header then.
                var hasHeader = false;
                table.one('tr').all('th').some(function(header) {
                    hasHeader = true;
                    if (header.get('text').trim() === '') {
                        problemNodes.push(table);
                        return true;
                    }
                    return false;
                });
                if (!hasHeader) {
                    problemNodes.push(table);
                }
            }
        }, this);
        this._addWarnings(list, M.util.get_string('tablesmissingheaders', COMPONENT), problemNodes, false);

        if (!list.hasChildNodes()) {
            list.append('<p>' + M.util.get_string('nowarnings', COMPONENT) + '</p>');
        }

        // Return the list of current warnings.
        return list;
    },

    /**
     * Generate the HTML that lists the found warnings.
     *
     * @method _addWarnings
     * @param {Node} list Node to append the html to.
     * @param {String} description Description of this failure.
     * @param {array} nodes An array of failing nodes.
     * @param {boolean} imagewarnings true if the warnings are related to images, false if text.
     */
    _addWarnings: function(list, description, nodes, imagewarnings) {
        var warning, fails, i, src, textfield, li, link, text;

        if (nodes.length > 0) {
            warning = Y.Node.create('<p>' + description + '</p>');
            fails = Y.Node.create('<ol class="accessibilitywarnings"></ol>');
            i = 0;
            for (i = 0; i < nodes.length; i++) {
                li = Y.Node.create('<li></li>');
                if (imagewarnings) {
                    src = nodes[i].getAttribute('src');
                    link = Y.Node.create('<a href="#"><img src="' + src + '" /> ' + src + '</a>');
                } else {
                    textfield = ('innerText' in nodes[i]) ? 'innerText' : 'textContent';
                    text = nodes[i].get(textfield).trim();
                    if (text === '') {
                        text = M.util.get_string('emptytext', COMPONENT);
                    }
                    if (nodes[i] === this.editor) {
                        text = M.util.get_string('entiredocument', COMPONENT);
                    }
                    link = Y.Node.create('<a href="#">' + text + '</a>');
                }
                link.setData('sourceNode', nodes[i]);
                li.append(link);
                fails.append(li);
            }

            warning.append(fails);
            list.append(warning);
        }
    },

    /**
     * Convert a CSS color to a luminance value.
     *
     * @method _getLuminanceFromCssColor
     * @param {String} colortext The Hex value for the colour
     * @return {Number} The luminance value.
     * @private
     */
    _getLuminanceFromCssColor: function(colortext) {
        var color;

        if (colortext === 'transparent') {
            colortext = '#ffffff';
        }
        color = Y.Color.toArray(Y.Color.toRGB(colortext));

        // Algorithm from "http://www.w3.org/TR/WCAG20-GENERAL/G18.html".
        var part1 = function(a) {
            a = parseInt(a, 10) / 255.0;
            if (a <= 0.03928) {
                a = a / 12.92;
            } else {
                a = Math.pow(((a + 0.055) / 1.055), 2.4);
            }
            return a;
        };

        var r1 = part1(color[0]),
            g1 = part1(color[1]),
            b1 = part1(color[2]);

        return 0.2126 * r1 + 0.7152 * g1 + 0.0722 * b1;
    },

    /**
     * Get the computed RGB converted to full alpha value, considering the node hierarchy.
     *
     * @method _getComputedBackgroundColor
     * @param {Node} node
     * @param {String} color The initial colour. If not specified, fetches the backgroundColor from the node.
     * @return {Array} Colour in Array form (RGBA)
     * @private
     */
    _getComputedBackgroundColor: function(node, color) {
        color = color || node.getComputedStyle('backgroundColor');

        if (color.toLowerCase() === 'transparent') {
            // Y.Color doesn't handle 'transparent' properly.
            color = 'rgba(1, 1, 1, 0)';
        }

        // Convert the colour to its constituent parts in RGBA format, then fetch the alpha.
        var colorParts = Y.Color.toArray(color);
        var alpha = colorParts[3];

        if (alpha === 1) {
            // If the alpha of the background is already 1, then the parent background colour does not change anything.
            return colorParts;
        }

        // Fetch the computed background colour of the parent and use it to calculate the RGB of this item.
        var parentColor = this._getComputedBackgroundColor(node.get('parentNode'));
        return [
            // RGB = (alpha * R|G|B) + (1 - alpha * solid parent colour).
            (1 - alpha) * parentColor[0] + alpha * colorParts[0],
            (1 - alpha) * parentColor[1] + alpha * colorParts[1],
            (1 - alpha) * parentColor[2] + alpha * colorParts[2],
            // We always return a colour with full alpha.
            1
        ];
    }
});


}, '@VERSION@', {"requires": ["color-base", "moodle-editor_atto-plugin"]});
