YUI.add('moodle-atto_undo-button', function (Y, NAME) {

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
 * @component  atto_undo
 * @copyright  2014 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_undo-button
 */

var LOGNAME = 'moodle-atto_undo-button';

/**
 * Atto text editor undo plugin.
 *
 * @namespace M.atto_undo
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_undo').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * The maximum saved number of undo steps.
     *
     * @property _maxUndos
     * @type {Number} The maximum number of saved undos.
     * @default 40
     * @private
     */
    _maxUndos: 40,

    /**
     * History of edits.
     *
     * @property _undoStack
     * @type {Array} The elements of the array are the html strings that make a snapshot
     * @private
     */
    _undoStack: null,

    /**
     * History of edits.
     *
     * @property _redoStack
     * @type {Array} The elements of the array are the html strings that make a snapshot
     * @private
     */
    _redoStack: null,

    /**
     * Add the buttons to the toolbar
     *
     * @method initializer
     */
    initializer: function() {
        // Initialise the undo and redo stacks.
        this._undoStack = [];
        this._redoStack = [];

        this.addButton({
            icon: 'e/undo',
            callback: this._undoHandler,
            buttonName: 'undo',
            keys: 90
        });

        this.addButton({
            icon: 'e/redo',
            callback: this._redoHandler,
            buttonName: 'redo',
            keys: 89
        });

        // Enable the undo once everything has loaded.
        this.get('host').on('pluginsloaded', function() {
            // Adds the current value to the stack.
            this._addToUndo(this._getHTML());
            this.get('host').on('atto:selectionchanged', this._changeListener, this);
        }, this);

        this._updateButtonsStates();
    },

    /**
     * Adds an element to the redo stack.
     *
     * @method _addToRedo
     * @private
     * @param {String} html The HTML content to save.
     */
    _addToRedo: function(html) {
        this._redoStack.push(html);
    },

    /**
     * Adds an element to the undo stack.
     *
     * @method _addToUndo
     * @private
     * @param {String} html The HTML content to save.
     * @param {Boolean} [clearRedo=false] Whether or not we should clear the redo stack.
     */
    _addToUndo: function(html, clearRedo) {
        var last = this._undoStack[this._undoStack.length - 1];

        if (typeof clearRedo === 'undefined') {
            clearRedo = false;
        }

        if (last !== html) {
            this._undoStack.push(html);
            if (clearRedo) {
                this._redoStack = [];
            }
        }

        while (this._undoStack.length > this._maxUndos) {
            this._undoStack.shift();
        }
    },

    /**
     * Get the editor HTML.
     *
     * @method _getHTML
     * @private
     * @return {String} The HTML.
     */
    _getHTML: function() {
        return this.get('host').getCleanHTML();
    },

    /**
     * Get an element on the redo stack.
     *
     * @method _getRedo
     * @private
     * @return {String} The HTML to restore, or undefined.
     */
    _getRedo: function() {
        return this._redoStack.pop();
    },

    /**
     * Get an element on the undo stack.
     *
     * @method _getUndo
     * @private
     * @param {String} current The current HTML.
     * @return {String} The HTML to restore.
     */
    _getUndo: function(current) {
        if (this._undoStack.length === 1) {
            return this._undoStack[0];
        }

        last = this._undoStack.pop();
        if (last === current) {
            // Oops, the latest undo step is the current content, we should unstack once more.
            // There is no need to do that in a loop as the same stack should never contain duplicates.
            last = this._undoStack.pop();
        }

        // We always need to keep the first element of the stack.
        if (this._undoStack.length === 0) {
            this._addToUndo(last);
        }

        return last;
    },

    /**
     * Restore a value from a stack.
     *
     * @method _restoreValue
     * @private
     * @param {String} html The HTML to restore in the editor.
     */
    _restoreValue: function(html) {
        this.editor.setHTML(html);
        // We always add the restored value to the stack, otherwise an event could think that
        // the content has changed and clear the redo stack.
        this._addToUndo(html);
    },

    /**
     * Update the states of the buttons.
     *
     * @method _updateButtonsStates
     * @private
     */
    _updateButtonsStates: function() {
        if (this._undoStack.length > 1) {
            this.enableButtons('undo');
        } else {
            this.disableButtons('undo');
        }

        if (this._redoStack.length > 0) {
            this.enableButtons('redo');
        } else {
            this.disableButtons('redo');
        }
    },

    /**
     * Handle a click on undo
     *
     * @method _undoHandler
     * @param {Event} The click event
     * @private
     */
    _undoHandler: function(e) {
        e.preventDefault();
        var html = this._getHTML(),
            undo = this._getUndo(html);

        // Edge case, but that could happen. We do nothing when the content equals the undo step.
        if (html === undo) {
            this._updateButtonsStates();
            return;
        }

        // Restore the value.
        this._restoreValue(undo);

        // Add to the redo stack.
        this._addToRedo(html);

        // Update the button states.
        this._updateButtonsStates();
    },

    /**
     * Handle a click on redo
     *
     * @method _redoHandler
     * @param {Event} The click event
     * @private
     */
    _redoHandler: function(e) {
        e.preventDefault();
        var html = this._getHTML(),
            redo = this._getRedo();

        // Edge case, but that could happen. We do nothing when the content equals the redo step.
        if (html === redo) {
            this._updateButtonsStates();
            return;
        }
        // Restore the value.
        this._restoreValue(redo);

        // Update the button states.
        this._updateButtonsStates();
    },

    /**
     * If we are significantly different from the last saved version, save a new version.
     *
     * @method _changeListener
     * @param {EventFacade} The click event
     * @private
     */
    _changeListener: function(e) {
        if (e.event && e.event.type.indexOf('key') !== -1) {
            // These are the 4 arrow keys.
            if ((e.event.keyCode !== 39) &&
                    (e.event.keyCode !== 37) &&
                    (e.event.keyCode !== 40) &&
                    (e.event.keyCode !== 38)) {
                // Skip this event type. We only want focus/mouse/arrow events.
                return;
            }
        }

        this._addToUndo(this._getHTML(), true);
        this._updateButtonsStates();
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
