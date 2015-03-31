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
 * @submodule selection
 */

/**
 * Selection functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorSelection
 */

function EditorSelection() {}

EditorSelection.ATTRS= {
};

EditorSelection.prototype = {

    /**
     * List of saved selections per editor instance.
     *
     * @property _selections
     * @private
     */
    _selections: null,

    /**
     * A unique identifier for the last selection recorded.
     *
     * @property _lastSelection
     * @param lastselection
     * @type string
     * @private
     */
    _lastSelection: null,

    /**
     * Whether focus came from a click event.
     *
     * This is used to determine whether to restore the selection or not.
     *
     * @property _focusFromClick
     * @type Boolean
     * @default false
     * @private
     */
    _focusFromClick: false,

    /**
     * Set up the watchers for selection save and restoration.
     *
     * @method setupSelectionWatchers
     * @chainable
     */
    setupSelectionWatchers: function() {
        // Save the selection when a change was made.
        this.on('atto:selectionchanged', this.saveSelection, this);

        this.editor.on('focus', this.restoreSelection, this);

        // Do not restore selection when focus is from a click event.
        this.editor.on('mousedown', function() {
            this._focusFromClick = true;
        }, this);

        // Copy the current value back to the textarea when focus leaves us and save the current selection.
        this.editor.on('blur', function() {
            // Clear the _focusFromClick value.
            this._focusFromClick = false;

            // Update the original text area.
            this.updateOriginal();
        }, this);

        this.editor.on(['keyup', 'focus'], function(e) {
                Y.soon(Y.bind(this._hasSelectionChanged, this, e));
            }, this);

        // To capture both mouseup and touchend events, we need to track the gesturemoveend event in standAlone mode. Without
        // standAlone, it will only fire if we listened to a gesturemovestart too.
        this.editor.on('gesturemoveend', function(e) {
                Y.soon(Y.bind(this._hasSelectionChanged, this, e));
            }, {
                standAlone: true
            }, this);

        return this;
    },

    /**
     * Work out if the cursor is in the editable area for this editor instance.
     *
     * @method isActive
     * @return {boolean}
     */
    isActive: function() {
        var range = rangy.createRange(),
            selection = rangy.getSelection();

        if (!selection.rangeCount) {
            // If there was no range count, then there is no selection.
            return false;
        }

        // We can't be active if the editor doesn't have focus at the moment.
        if (!document.activeElement ||
                !(this.editor.compareTo(document.activeElement) || this.editor.contains(document.activeElement))) {
            return false;
        }

        // Check whether the range intersects the editor selection.
        range.selectNode(this.editor.getDOMNode());
        return range.intersectsRange(selection.getRangeAt(0));
    },

    /**
     * Create a cross browser selection object that represents a YUI node.
     *
     * @method getSelectionFromNode
     * @param {Node} YUI Node to base the selection upon.
     * @return {[rangy.Range]}
     */
    getSelectionFromNode: function(node) {
        var range = rangy.createRange();
        range.selectNode(node.getDOMNode());
        return [range];
    },

    /**
     * Save the current selection to an internal property.
     *
     * This allows more reliable return focus, helping improve keyboard navigation.
     *
     * Should be used in combination with {{#crossLink "M.editor_atto.EditorSelection/restoreSelection"}}{{/crossLink}}.
     *
     * @method saveSelection
     */
    saveSelection: function() {
        if (this.isActive()) {
            this._selections = this.getSelection();
        }
    },

    /**
     * Restore any stored selection when the editor gets focus again.
     *
     * Should be used in combination with {{#crossLink "M.editor_atto.EditorSelection/saveSelection"}}{{/crossLink}}.
     *
     * @method restoreSelection
     */
    restoreSelection: function() {
        if (!this._focusFromClick) {
            if (this._selections) {
                this.setSelection(this._selections);
            }
        }
        this._focusFromClick = false;
    },

    /**
     * Get the selection object that can be passed back to setSelection.
     *
     * @method getSelection
     * @return {array} An array of rangy ranges.
     */
    getSelection: function() {
        return rangy.getSelection().getAllRanges();
    },

    /**
     * Check that a YUI node it at least partly contained by the current selection.
     *
     * @method selectionContainsNode
     * @param {Node} The node to check.
     * @return {boolean}
     */
    selectionContainsNode: function(node) {
        return rangy.getSelection().containsNode(node.getDOMNode(), true);
    },

    /**
     * Runs a filter on each node in the selection, and report whether the
     * supplied selector(s) were found in the supplied Nodes.
     *
     * By default, all specified nodes must match the selection, but this
     * can be controlled with the requireall property.
     *
     * @method selectionFilterMatches
     * @param {String} selector
     * @param {NodeList} [selectednodes] For performance this should be passed. If not passed, this will be looked up each time.
     * @param {Boolean} [requireall=true] Used to specify that "any" match is good enough.
     * @return {Boolean}
     */
    selectionFilterMatches: function(selector, selectednodes, requireall) {
        if (typeof requireall === 'undefined') {
            requireall = true;
        }
        if (!selectednodes) {
            // Find this because it was not passed as a param.
            selectednodes = this.getSelectedNodes();
        }
        var allmatch = selectednodes.size() > 0,
            anymatch = false;

        var editor = this.editor,
            stopFn = function(node) {
                // The function getSelectedNodes only returns nodes within the editor, so this test is safe.
                return node === editor;
            };

        // If we do not find at least one match in the editor, no point trying to find them in the selection.
        if (!editor.one(selector)) {
            return false;
        }

        selectednodes.each(function(node){
            // Check each node, if it doesn't match the tags AND is not within the specified tags then fail this thing.
            if (requireall) {
                // Check for at least one failure.
                if (!allmatch || !node.ancestor(selector, true, stopFn)) {
                    allmatch = false;
                }
            } else {
                // Check for at least one match.
                if (!anymatch && node.ancestor(selector, true, stopFn)) {
                    anymatch = true;
                }
            }
        }, this);
        if (requireall) {
            return allmatch;
        } else {
            return anymatch;
        }
    },

    /**
     * Get the deepest possible list of nodes in the current selection.
     *
     * @method getSelectedNodes
     * @return {NodeList}
     */
    getSelectedNodes: function() {
        var results = new Y.NodeList(),
            nodes,
            selection,
            range,
            node,
            i;

        selection = rangy.getSelection();

        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        } else {
            // Empty range.
            range = rangy.createRange();
        }

        if (range.collapsed) {
            // We do not want to select all the nodes in the editor if we managed to
            // have a collapsed selection directly in the editor.
            // It's also possible for the commonAncestorContainer to be the document, which selectNode does not handle
            // so we must filter that out here too.
            if (range.commonAncestorContainer !== this.editor.getDOMNode()
                    && range.commonAncestorContainer !== Y.config.doc) {
                range = range.cloneRange();
                range.selectNode(range.commonAncestorContainer);
            }
        }

        nodes = range.getNodes();

        for (i = 0; i < nodes.length; i++) {
            node = Y.one(nodes[i]);
            if (this.editor.contains(node)) {
                results.push(node);
            }
        }
        return results;
    },

    /**
     * Check whether the current selection has changed since this method was last called.
     *
     * If the selection has changed, the atto:selectionchanged event is also fired.
     *
     * @method _hasSelectionChanged
     * @private
     * @param {EventFacade} e
     * @return {Boolean}
     */
    _hasSelectionChanged: function(e) {
        var selection = rangy.getSelection(),
            range,
            changed = false;

        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        } else {
            // Empty range.
            range = rangy.createRange();
        }

        if (this._lastSelection) {
            if (!this._lastSelection.equals(range)) {
                changed = true;
                return this._fireSelectionChanged(e);
            }
        }
        this._lastSelection = range;
        return changed;
    },

    /**
     * Fires the atto:selectionchanged event.
     *
     * When the selectionchanged event is fired, the following arguments are provided:
     *   - event : the original event that lead to this event being fired.
     *   - selectednodes :  an array containing nodes that are entirely selected of contain partially selected content.
     *
     * @method _fireSelectionChanged
     * @private
     * @param {EventFacade} e
     */
    _fireSelectionChanged: function(e) {
        this.fire('atto:selectionchanged', {
            event: e,
            selectedNodes: this.getSelectedNodes()
        });
    },

    /**
     * Get the DOM node representing the common anscestor of the selection nodes.
     *
     * @method getSelectionParentNode
     * @return {Element|boolean} The DOM Node for this parent, or false if no seletion was made.
     */
    getSelectionParentNode: function() {
        var selection = rangy.getSelection();
        if (selection.rangeCount) {
            return selection.getRangeAt(0).commonAncestorContainer;
        }
        return false;
    },

    /**
     * Set the current selection. Used to restore a selection.
     *
     * @method selection
     * @param {array} ranges A list of rangy.range objects in the selection.
     */
    setSelection: function(ranges) {
        var selection = rangy.getSelection();
        selection.setRanges(ranges);
    },

    /**
     * Inserts the given HTML into the editable content at the currently focused point.
     *
     * @method insertContentAtFocusPoint
     * @param {String} html
     * @return {Node} The YUI Node object added to the DOM.
     */
    insertContentAtFocusPoint: function(html) {
        var selection = rangy.getSelection(),
            range,
            node = Y.Node.create(html);
        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        }
        if (range) {
            range.deleteContents();
            range.insertNode(node.getDOMNode());
        }
        return node;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorSelection]);
