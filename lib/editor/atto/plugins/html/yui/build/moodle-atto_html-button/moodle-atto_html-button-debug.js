YUI.add('moodle-atto_html-button', function (Y, NAME) {

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
 * @package    atto_html
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module     moodle-atto_html-button
 */

/**
 * Atto text editor HTML plugin.
 *
 * @namespace M.atto_html
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_html').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            icon: 'e/source_code',
            callback: this._toggleHTML
        });
    },

    /**
     * Toggle the view between the content editable div, and the textarea,
     * updating the content as it goes.
     *
     * @method _toggleHTML
     * @private
     */
    _toggleHTML: function() {
        // Toggle the HTML status.
        this.set('isHTML', !this.get('isHTML'));

        // Now make the UI changes.
        this._showHTML();
    },

    /**
     * Set the current state of the textarea and contenteditable div
     * according to the isHTML property.
     *
     * @method _showHTML
     * @private
     */
    _showHTML: function() {
        var host = this.get('host');
        if (!this.get('isHTML')) {
            // Unhighlight icon.
            this.unHighlightButtons('html');

            // Enable all plugins.
            host.enablePlugins();

            // Copy the text to the contenteditable div.
            host.updateFromTextArea();

            // Hide the textarea, and show the editor.
            host.textarea.hide();
            this.editor.show();

            // Focus on the editor.
            host.focus();

            // And re-mark everything as updated.
            this.markUpdated();
        } else {
            // Highlight icon.
            this.highlightButtons('html');

            // Disable all plugins.
            host.disablePlugins();

            // And then re-enable this one.
            host.enablePlugins(this.name);

            // Copy the text to the contenteditable div.
            host.updateOriginal();

            // Get the width, padding, and margin of the editor.
            host.textarea.setStyles({
                'width': this.editor.getComputedStyle('width'),
                'height': this.editor.getComputedStyle('height'),
                'margin': this.editor.getComputedStyle('margin'),
                'padding': this.editor.getComputedStyle('padding')
            });

            // Hide the editor, and show the textarea.
            this.editor.hide();
            host.textarea.show();


            // Focus on the textarea.
            host.textarea.focus();
        }
    }
}, {
    ATTRS: {
        /**
         * The current state for the HTML view. If true, the HTML source is
         * shown in a textarea, otherwise the contenteditable area is
         * displayed.
         *
         * @attribute isHTML
         * @type Boolean
         * @default false
         */
        isHTML: {
            value: false
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin", "event-valuechange"]});
