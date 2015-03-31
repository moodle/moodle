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
 * @submodule textarea
 */

/**
 * Textarea functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorTextArea
 */

function EditorTextArea() {}

EditorTextArea.ATTRS= {
};

EditorTextArea.prototype = {

    /**
     * Return the appropriate empty content value for the current browser.
     *
     * Different browsers use a different content when they are empty and
     * we must set this reliable across the board.
     *
     * @method _getEmptyContent
     * @return String The content to use representing no user-provided content
     * @private
     */
    _getEmptyContent: function() {
        if (Y.UA.ie && Y.UA.ie < 10) {
            return '<p></p>';
        } else {
            return '<p><br></p>';
        }
    },

    /**
     * Copy and clean the text from the textarea into the contenteditable div.
     *
     * If the text is empty, provide a default paragraph tag to hold the content.
     *
     * @method updateFromTextArea
     * @chainable
     */
    updateFromTextArea: function() {
        // Clear it first.
        this.editor.setHTML('');

        // Copy text to editable div.
        this.editor.append(this.textarea.get('value'));

        // Clean it.
        this.cleanEditorHTML();

        // Insert a paragraph in the empty contenteditable div.
        if (this.editor.getHTML() === '') {
            this.editor.setHTML(this._getEmptyContent());
        }
    },

    /**
     * Copy the text from the contenteditable to the textarea which it replaced.
     *
     * @method updateOriginal
     * @chainable
     */
    updateOriginal : function() {
        // Get the previous and current value to compare them.
        var oldValue = this.textarea.get('value'),
            newValue = this.getCleanHTML();

        if (newValue === "" && this.isActive()) {
            // The content was entirely empty so get the empty content placeholder.
            newValue = this._getEmptyContent();
        }

        // Only call this when there has been an actual change to reduce processing.
        if (oldValue !== newValue) {
            // Insert the cleaned content.
            this.textarea.set('value', newValue);

            // Trigger the onchange callback on the textarea, essentially to notify moodle-core-formchangechecker.
            this.textarea.simulate('change');

            // Trigger handlers for this action.
            this.fire('change');
        }

        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorTextArea]);
