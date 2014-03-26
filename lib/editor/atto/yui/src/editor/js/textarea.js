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
            if (Y.UA.ie && Y.UA.ie < 10) {
                this.editor.setHTML('<p></p>');
            } else {
                this.editor.setHTML('<p><br></p>');
            }
        }
    },

    /**
     * Copy the text from the contenteditable to the textarea which it replaced.
     *
     * @method updateOriginal
     * @chainable
     */
    updateOriginal : function() {
        // Insert the cleaned content.
        this.textarea.set('value', this.getCleanHTML());

        // Trigger the onchange callback on the textarea, essentially to notify moodle-core-formchangechecker.
        this.textarea.simulate('change');

        // Trigger handlers for this action.
        this.fire('change');
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorTextArea]);
