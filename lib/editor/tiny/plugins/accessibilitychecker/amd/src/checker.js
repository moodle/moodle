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
 * @package    tiny_accessibilitychecker
 * @copyright  2022, Stevani Andolo  <stevani@hotmail.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {get_string as getString, get_strings as getStrings} from 'core/str';
import {component} from './common';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import ColorBase from './colorbase';
import {getPlaceholderSelectors} from 'editor_tiny/options';

/**
 * @typedef ProblemDetail
 * @type {object}
 * @param {string} description The description of the problem
 * @param {ProblemNode[]} problemNodes The list of affected nodes
 */

/**
 * @typedef ProblemNode
 * @type {object}
 * @param {string} nodeName The node name for the affected node
 * @param {string} nodeIndex The indexd of the node
 * @param {string} text A description of the issue
 * @param {string} src The source of the image
 */

export default class {

    constructor(editor) {
        this.editor = editor;
        this.colorBase = new ColorBase();
        this.modal = null;
        this.placeholderSelectors = null;
        const placeholders = getPlaceholderSelectors(this.editor);
        if (placeholders.length) {
            this.placeholderSelectors = placeholders.join(', ');
        }
    }

    destroy() {
        delete this.editor;
        delete this.colorBase;

        this.modal.destroy();
        delete this.modal;
    }

    async displayDialogue() {
        this.modal = await Modal.create({
            type: Modal.types.DEFAULT,
            large: true,
            title: getString('pluginname', component),
            body: this.getDialogueContent()
        });

        // Destroy the class when hiding the modal.
        this.modal.getRoot().on(ModalEvents.hidden, () => this.destroy());

        this.modal.getRoot()[0].addEventListener('click', (event) => {
            const faultLink = event.target.closest('[data-action="highlightfault"]');
            if (!faultLink) {
                return;
            }

            event.preventDefault();

            const nodeName = faultLink.dataset.nodeName;
            let selectedNode = null;
            if (nodeName) {
                if (nodeName.includes(',') || nodeName === 'body') {
                    selectedNode = this.editor.dom.select('body')[0];
                } else {
                    const nodeIndex = faultLink.dataset.nodeIndex ?? 0;
                    selectedNode = this.editor.dom.select(nodeName)[nodeIndex];
                }
            }

            if (selectedNode && selectedNode.nodeName.toUpperCase() !== 'BODY') {
                this.selectAndScroll(selectedNode);
            }

            this.modal.hide();
        });

        this.modal.show();
    }

    async getAllWarningStrings() {
        const keys = [
            'emptytext',
            'entiredocument',
            'imagesmissingalt',
            'needsmorecontrast',
            'needsmoreheadings',
            'tablesmissingcaption',
            'tablesmissingheaders',
            'tableswithmergedcells',
        ];

        const stringValues = await getStrings(keys.map((key) => ({key, component})));
        return new Map(keys.map((key, index) => ([key, stringValues[index]])));
    }

    /**
     * Return the dialogue content.
     *
     * @return {Promise<Array>} A template promise containing the rendered dialogue content.
     */
     async getDialogueContent() {
        const langStrings = await this.getAllWarningStrings();

        // Translate langstrings into real strings.
        const warnings = this.getWarnings().map((warning) => {
            if (warning.description) {
                if (warning.description.type === 'langstring') {
                    warning.description = langStrings.get(warning.description.value);
                } else {
                    warning.description = warning.description.value;
                }
            }

            warning.nodeData = warning.nodeData.map((problemNode) => {
                if (problemNode.text) {
                    if (problemNode.text.type === 'langstring') {
                        problemNode.text = langStrings.get(problemNode.text.value);
                    } else {
                        problemNode.text = problemNode.text.value;
                    }
                }

                return problemNode;
            });

            return warning;
        });

        return Templates.render('tiny_accessibilitychecker/warning_content', {
            warnings
        });
    }

    /**
     * Set the selection and scroll to the selected element.
     *
     * @param {node} node
     */
    selectAndScroll(node) {
        this.editor.selection.select(node).scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    /**
     * Find all problems with the content editable region.
     *
     * @return {ProblemDetail[]} A complete list of all warnings and problems.
     */
    getWarnings() {
        const warnings = [];

        // Check Images with no alt text or dodgy alt text.
        warnings.push(this.createWarnings('imagesmissingalt', this.checkImage(), true));
        warnings.push(this.createWarnings('needsmorecontrast', this.checkOtherElements(), false));

        // Check for no headings.
        if (this.editor.getContent({format: 'text'}).length > 1000 && this.editor.dom.select('h3,h4,h5').length < 1) {
            warnings.push(this.createWarnings('needsmoreheadings', [this.editor], false));
        }

        // Check for tables with no captions.
        warnings.push(this.createWarnings('tablesmissingcaption', this.checkTableCaption(), false));

        // Check for tables with merged cells.
        warnings.push(this.createWarnings('tableswithmergedcells', this.checkTableMergedCells(), false));

        // Check for tables with no row/col headers.
        warnings.push(this.createWarnings('tablesmissingheaders', this.checkTableHeaders(), false));

        return warnings.filter((warning) => warning.nodeData.length > 0);
    }

    /**
     * Generate the data that describes the issues found.
     *
     * @param {String} description Description of this failure.
     * @param {HTMLElement[]} nodes An array of failing nodes.
     * @param {boolean} isImageType Whether the warnings are related to image type checks
     * @return {ProblemDetail[]} A set of problem details
     */
    createWarnings(description, nodes, isImageType) {
        const getTextValue = (node) => {
            if (node === this.editor) {
                return {
                    type: 'langstring',
                    value: 'entiredocument',
                };
            }

            const emptyStringValue = {
                type: 'langstring',
                value: 'emptytext',
            };
            if ('innerText' in node) {
                const value = node.innerText.trim();
                return value.length ? {type: 'raw', value} : emptyStringValue;
            } else if ('textContent' in node) {
                const value = node.textContent.trim();
                return value.length ? {type: 'raw', value} : emptyStringValue;
            }

            return {type: 'raw', value: node.nodeName};
        };

        const getEventualNode = (node) => {
            if (node !== this.editor) {
                return node;
            }
            const childNodes = node.dom.select('body')[0].childNodes;
            if (childNodes.length) {
                return document.body;
            } else {
                return childNodes;
            }
        };

        const warning = {
            description: {
                type: 'langstring',
                value: description,
            },
            nodeData: [],
        };

        warning.nodeData = [...nodes].filter((node) => {
            // If the failed node is a placeholder element. We should remove it from the list.
            if (node !== this.editor && this.placeholderSelectors) {
                return node.matches(this.placeholderSelectors) === false;
            }

            return node;
        }).map((node) => {
            const describedNode = getEventualNode(node);

            // Find the index of the node within the type of node.
            // This is used to select the correct node when the user selects it.
            const nodeIndex = this.editor.dom.select(describedNode.nodeName).indexOf(describedNode);
            const warning = {
                src: null,
                text: null,
                nodeName: describedNode.nodeName,
                nodeIndex,
            };

            if (isImageType) {
                warning.src = node.getAttribute('src');
            } else {
                warning.text = getTextValue(node);
            }

            return warning;
        });

        return warning;
    }

    /**
     * Check accessiblity issue only for img type.
     *
     * @return {Node} A complete list of all warnings and problems.
     */
    checkImage() {
        const problemNodes = [];
        this.editor.dom.select('img').forEach((img) => {
            const alt = img.getAttribute('alt');
            if (!alt && img.getAttribute('role') !== 'presentation') {
                problemNodes.push(img);
            }
        });
        return problemNodes;
    }

    /**
     * Look for any table without a caption.
     *
     * @return {Node} A complete list of all warnings and problems.
     */
    checkTableCaption() {
        const problemNodes = [];
        this.editor.dom.select('table').forEach((table) => {
            const caption = table.querySelector('caption');
            if (!caption?.textContent.trim()) {
                problemNodes.push(table);
            }
        });

        return problemNodes;
    }

    /**
     * Check accessiblity issue for not img and table only.
     *
     * @return {Node} A complete list of all warnings and problems.
     * @private
     */
    checkOtherElements() {
        const problemNodes = [];

        const getRatio = (lum1, lum2) => {
            // Algorithm from "http://www.w3.org/TR/WCAG20-GENERAL/G18.html".
            if (lum1 > lum2) {
                return (lum1 + 0.05) / (lum2 + 0.05);
            } else {
                return (lum2 + 0.05) / (lum1 + 0.05);
            }
        };

        this.editor.dom.select('body *')
            .filter((node) => node.hasChildNodes() && node.childNodes[0].nodeValue !== null)
            .forEach((node) => {
                const foreground = this.colorBase.fromArray(
                    this.getComputedBackgroundColor(
                        node,
                        window.getComputedStyle(node, null).getPropertyValue('color')
                    ),
                    this.colorBase.TYPES.RGBA
                );
                const background = this.colorBase.fromArray(
                    this.getComputedBackgroundColor(
                        node
                    ),
                    this.colorBase.TYPES.RGBA
                );

                const lum1 = this.getLuminanceFromCssColor(foreground);
                const lum2 = this.getLuminanceFromCssColor(background);
                const ratio = getRatio(lum1, lum2);

                if (ratio <= 4.5) {
                    window.console.log(`
                        Contrast ratio is too low: ${ratio}
                        Colour 1: ${foreground}
                        Colour 2: ${background}
                        Luminance 1: ${lum1}
                        Luminance 2: ${lum2}
                    `);

                    // We only want the highest node with dodgy contrast reported.
                    if (!problemNodes.find((existingProblemNode) => existingProblemNode.contains(node))) {
                        problemNodes.push(node);
                    }
                }
            });
        return problemNodes;
    }

    /**
     * Check accessiblity issue only for table with merged cells.
     *
     * @return {Node} A complete list of all warnings and problems.
     * @private
     */
    checkTableMergedCells() {
        const problemNodes = [];
        this.editor.dom.select('table').forEach((table) => {
            const rowcolspan = table.querySelectorAll('[colspan], [rowspan]');
            if (rowcolspan.length) {
                problemNodes.push(table);
            }
        });
        return problemNodes;
    }

    /**
     * Check accessiblity issue only for table with no headers.
     *
     * @return {Node} A complete list of all warnings and problems.
     * @private
     */
    checkTableHeaders() {
        const problemNodes = [];

        this.editor.dom.select('table').forEach((table) => {
            if (table.querySelector('tr').querySelector('td')) {
                // The first row has a non-header cell, so all rows must have at least one header.
                const missingHeader = [...table.querySelectorAll('tr')].some((row) => {
                    const header = row.querySelector('th');
                    if (!header) {
                        return true;
                    }

                    if (!header.textContent.trim()) {
                        return true;
                    }

                    return false;
                });
                if (missingHeader) {
                    // At least one row is missing the header, or it is empty.
                    problemNodes.push(table);
                }
            } else {
                // Every header must have some content.
                if ([...table.querySelectorAll('tr th')].some((header) => !header.textContent.trim())) {
                    problemNodes.push(table);
                }
            }
        });
        return problemNodes;
    }

    /**
     * Convert a CSS color to a luminance value.
     *
     * @param {String} colortext The Hex value for the colour
     * @return {Number} The luminance value.
     * @private
     */
    getLuminanceFromCssColor(colortext) {
        if (colortext === 'transparent') {
            colortext = '#ffffff';
        }
        const color = this.colorBase.toArray(this.colorBase.toRGB(colortext));

        // Algorithm from "http://www.w3.org/TR/WCAG20-GENERAL/G18.html".
        const part1 = (a) => {
            a = parseInt(a, 10) / 255.0;
            if (a <= 0.03928) {
                a = a / 12.92;
            } else {
                a = Math.pow(((a + 0.055) / 1.055), 2.4);
            }
            return a;
        };

        const r1 = part1(color[0]);
        const g1 = part1(color[1]);
        const b1 = part1(color[2]);

        return 0.2126 * r1 + 0.7152 * g1 + 0.0722 * b1;
    }

    /**
     * Get the computed RGB converted to full alpha value, considering the node hierarchy.
     *
     * @param {Node} node
     * @param {String} color The initial colour. If not specified, fetches the backgroundColor from the node.
     * @return {Array} Colour in Array form (RGBA)
     * @private
     */
    getComputedBackgroundColor(node, color) {
        if (!node.parentNode) {
            // This is the document node and has no colour.
            // We cannot use window.getComputedStyle on the document.
            // If we got here, then the document has no background colour. Fall back to white.
            return this.colorBase.toArray('rgba(255, 255, 255, 1)');
        }
        color = color ? color : window.getComputedStyle(node, null).getPropertyValue('background-color');

        if (color.toLowerCase() === 'rgba(0, 0, 0, 0)' || color.toLowerCase() === 'transparent') {
            color = 'rgba(1, 1, 1, 0)';
        }

        // Convert the colour to its constituent parts in RGBA format, then fetch the alpha.
        const colorParts = this.colorBase.toArray(color);
        const alpha = colorParts[3];

        if (alpha === 1) {
            // If the alpha of the background is already 1, then the parent background colour does not change anything.
            return colorParts;
        }

        // Fetch the computed background colour of the parent and use it to calculate the RGB of this item.
        const parentColor = this.getComputedBackgroundColor(node.parentNode);
        return [
            // RGB = (alpha * R|G|B) + (1 - alpha * solid parent colour).
            (1 - alpha) * parentColor[0] + alpha * colorParts[0],
            (1 - alpha) * parentColor[1] + alpha * colorParts[1],
            (1 - alpha) * parentColor[2] + alpha * colorParts[2],
            // We always return a colour with full alpha.
            1
        ];
    }
}
