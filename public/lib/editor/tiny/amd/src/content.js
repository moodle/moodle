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
 * Module to assist with creation and management of content.
 *
 * @module     editor_tiny/content
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add MathML support to the editor.
 *
 * @param {TinyMCE} editor
 */
export const addMathMLSupport = (editor) => {
    const getNodeType = (node) => {
        const style = node.attr('style');
        if (style?.includes('display')) {
            if (style.match(/display:[^;]*inline/)) {
                return 'tiny-math-span';
            }
        }
        return 'tiny-math-block';
    };


    editor.on('PreInit', () => {
        editor.schema.addCustomElements({
            // Add support for MathML by defining some tiny-math blocks which extends SPAN/DIV.
            // Note: This is blind support and does not check the child content.
            // Any invalid markup will be accepted.
            // Note: We use the same names as the Tiny Premium Math plugin to avoid conflicts if both are enabled.
            math: {
                'extends': 'div',
            },
            'tiny-math-span': {
                'extends': "span",
            },
            'tiny-math-block': {
                'extends': "div",
            },
        });

        // Add a Parser filter to wrap math nodes in a tiny-math-[block|span] element.
        editor.parser.addNodeFilter('math', (nodes) => nodes.forEach((node) => {
            if (node.parent) {
                if (node.parent.name === 'tiny-math-block' || node.parent.name === 'tiny-math-span') {
                    // Already wrapped.
                    return;
                }
            }

            const displayMode = getNodeType(node);
            node.wrap(editor.editorManager.html.Node.create(displayMode, {
                contenteditable: 'false',
            }));
        }));

        // Add a Serializer filter to remove the tiny-math-[block|span] wrapper.
        editor.serializer.addNodeFilter('tiny-math-span, tiny-math-block', (nodes, name) => nodes.forEach((node) => {
            const displayMode = name.replace('tiny-math-', '');
            node.children().forEach((child) => {
                const currentStyle = child.attr('style');
                if (currentStyle) {
                    child.attr('style', `${currentStyle};display: ${displayMode}`);
                } else {
                    child.attr('style', `display: ${displayMode}`);
                }
            });
            node.unwrap();
        }));
    });
};

/**
 * Add SVG support to the editor.
 *
 * @param {TinyMCE} editor
 */
export const addSVGSupport = (editor) => {
    editor.on('PreInit', () => {
        editor.schema.addCustomElements({
            // Add support for SVG by defining an SVG tag which extends DIV.
            // Note: This is blind support and does not check the child content.
            // Any invalid markup will be accepted.
            svg: {
                'extends': "div",
            },
            'tiny-svg-block': {
                'extends': "div",
            },
        });

        editor.parser.addNodeFilter('svg', (nodes) => nodes.forEach((node) => {
            node.wrap(editor.editorManager.html.Node.create('tiny-svg-block', {
                contenteditable: 'false',
            }));
        }));
        editor.serializer.addNodeFilter('tiny-svg-block', (nodes) => nodes.forEach((node) => {
            node.unwrap();
        }));
    });
};
