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

EditorStyling.ATTRS = {
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
        var classname = toggleclasses.join(" ");
        var cssApplier = rangy.createClassApplier(classname, {normalize: true});

        cssApplier.toggleSelection();
    },

    /**
     * Change the formatting for the current selection.
     *
     * This will set inline styles on the current selection.
     *
     * @method formatSelectionInlineStyle
     * @param {Array} styles - Style attributes to set on the nodes.
     */
    formatSelectionInlineStyle: function(styles) {
        var classname = this.PLACEHOLDER_CLASS;
        var cssApplier = rangy.createClassApplier(classname, {normalize: true});

        cssApplier.applyToSelection();

        this.editor.all('.' + classname).each(function(node) {
            node.removeClass(classname).setStyles(styles);
        }, this);

    },

    /**
     * Change the formatting for the current selection.
     *
     * Also changes the selection to the newly formatted block (allows applying multiple styles to a block).
     *
     * @method formatSelectionBlock
     * @param {String} [blocktag] Change the block level tag to this. Empty string, means do not change the tag.
     * @param {Object} [attributes] The keys and values for attributes to be added/changed in the block tag.
     * @return {Node|boolean} The Node that was formatted if a change was made, otherwise false.
     */
    formatSelectionBlock: function(blocktag, attributes) {
        // First find the nearest ancestor of the selection that is a block level element.
        var selectionparentnode = this.getSelectionParentNode(),
            boundary,
            cell,
            nearestblock,
            newcontent,
            match,
            replacement;

        if (!selectionparentnode) {
            // No selection, nothing to format.
            return false;
        }

        boundary = this.editor;

        selectionparentnode = Y.one(selectionparentnode);

        // If there is a table cell in between the selectionparentnode and the boundary,
        // move the boundary to the table cell.
        // This is because we might have a table in a div, and we select some text in a cell,
        // want to limit the change in style to the table cell, not the entire table (via the outer div).
        cell = selectionparentnode.ancestor(function(node) {
            var tagname = node.get('tagName');
            if (tagname) {
                tagname = tagname.toLowerCase();
            }
            return (node === boundary) ||
                   (tagname === 'td') ||
                   (tagname === 'th');
        }, true);

        if (cell) {
            // Limit the scope to the table cell.
            boundary = cell;
        }

        nearestblock = selectionparentnode.ancestor(this.BLOCK_TAGS.join(', '), true);
        if (nearestblock) {
            // Check that the block is contained by the boundary.
            match = nearestblock.ancestor(function(node) {
                return node === boundary;
            }, false);

            if (!match) {
                nearestblock = false;
            }
        }

        // No valid block element - make one.
        if (!nearestblock) {
            var alignment;
            if (this.coreDirection === 'rtl') {
                alignment = 'style="text-align: right;"';
            } else {
                alignment = 'style="text-align: left;"';
            }
            // There is no block node in the content, wrap the content in a p and use that.
            newcontent = Y.Node.create('<p dir="' + this.coreDirection + '" ' + alignment + '></p>');
            boundary.get('childNodes').each(function(child) {
                newcontent.append(child.remove());
            });
            boundary.append(newcontent);
            nearestblock = newcontent;
        }

        // Guaranteed to have a valid block level element contained in the contenteditable region.
        // Change the tag to the new block level tag.
        if (blocktag && blocktag !== '') {
            // Change the block level node for a new one.
            replacement = Y.Node.create('<' + blocktag + '></' + blocktag + '>');
            // Copy all attributes.
            replacement.setAttrs(nearestblock.getAttrs());
            // Copy all children.
            nearestblock.get('childNodes').each(function(child) {
                child.remove();
                replacement.append(child);
            });

            nearestblock.replace(replacement);
            nearestblock = replacement;
        }

        // Set the attributes on the block level tag.
        if (attributes) {
            nearestblock.setAttrs(attributes);
        }

        // Change the selection to the modified block. This makes sense when we might apply multiple styles
        // to the block.
        var selection = this.getSelectionFromNode(nearestblock);
        this.setSelection(selection);

        return nearestblock;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorStyling]);
