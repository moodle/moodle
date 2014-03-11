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
 * @module moodle-editor_atto-editor
 * @submodule styling
 */

/**
 * Editor styling functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorStyling
 */

function EditorStyling() {}

EditorStyling.ATTRS= {
};

EditorStyling.prototype = {
    /**
     * Disable CSS styling.
     *
     * @method disableCssStyling
     */
    disableCssStyling: function() {
        try {
            document.execCommand("styleWithCSS", 0, false);
        } catch (e1) {
            try {
                document.execCommand("useCSS", 0, true);
            } catch (e2) {
                try {
                    document.execCommand('styleWithCSS', false, false);
                } catch (e3) {
                    // We did our best.
                }
            }
        }
    },

    /**
     * Enable CSS styling.
     *
     * @method enableCssStyling
     */
    enableCssStyling: function() {
        try {
            document.execCommand("styleWithCSS", 0, true);
        } catch (e1) {
            try {
                document.execCommand("useCSS", 0, false);
            } catch (e2) {
                try {
                    document.execCommand('styleWithCSS', false, true);
                } catch (e3) {
                    // We did our best.
                }
            }
        }
    },

    /**
     * Change the formatting for the current selection.
     *
     * This will wrap the selection in span tags, adding the provided classes.
     *
     * If the selection covers multiple block elements, multiple spans will be inserted to preserve the original structure.
     *
     * @method toggleInlineSelectionClass
     * @param {Array} toggleclasses - Class names to be toggled on or off.
     */
    toggleInlineSelectionClass: function(toggleclasses) {
        var selectionparentnode = this.getSelectionParentNode(),
            nodes,
            items = [],
            parentspan,
            currentnode,
            newnode,
            i = 0;

        if (!selectionparentnode) {
            // No selection, nothing to format.
            return;
        }

        // Add a bogus fontname as the browsers handle inserting fonts into multiple blocks correctly.
        document.execCommand('fontname', false, this.PLACEHOLDER_FONTNAME);
        nodes = this.editor.all(this.ALL_NODES_SELECTOR);

        // Create a list of all nodes that have our bogus fontname.
        nodes.each(function(node, index) {
            if (node.getStyle(this.FONT_FAMILY) === this.PLACEHOLDER_FONTNAME) {
                node.setStyle(this.FONT_FAMILY, '');
                if (!node.compareTo(this.editor)) {
                    items.push(Y.Node.getDOMNode(nodes.item(index)));
                }
            }
        });

        // Replace the fontname tags with spans
        for (i = 0; i < items.length; i++) {
            currentnode = Y.one(items[i]);

            // Check for an existing span to add the nolink class to.
            parentspan = currentnode.ancestor('.editor_atto_content span');
            if (!parentspan) {
                newnode = Y.Node.create('<span></span>');
                newnode.append(items[i].innerHTML);
                currentnode.replace(newnode);

                currentnode = newnode;
            } else {
                currentnode = parentspan;
            }

            // Toggle the classes on the spans.
            for (var j = 0; j < toggleclasses.length; j++) {
                currentnode.toggleClass(toggleclasses[j]);
            }
        }
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorStyling]);
