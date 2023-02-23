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

    _codeMirror: null,

    initializer: function() {
        this.addButton({
            icon: 'e/source_code',
            callback: this._toggleHTML
        });

        // Attach a submit listener to the form.
        // This ensures that the codeMirror is converted back to the original text before form submission.
        var form = this.get('host').textarea.ancestor('form');
        if (form) {
            form.on('submit', function() {
                if (this.get('isHTML')) {
                    this._hideCodeMirror();
                }
            }, this);
        }
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

        if (!this.get('isHTML')) {
            this._hideCodeMirror();
        } else {
            this._showCodeMirror();
        }
    },

    /**
     * Show the code mirror.
     *
     * @method _showCodeMirror
     * @private
     */
    _showCodeMirror: function() {
        var host = this.get('host');

        // Disable all plugins.
        host.disablePlugins();

        // And then re-enable this one.
        host.enablePlugins(this.name);

        // Copy the text to the contenteditable div.
        host.updateOriginal();

        // Hide the editor, and show the textarea.
        var dimensions = {
            width: this.editor.getComputedStyle('width'),
            height: this.editor.getComputedStyle('height')
        };

        // Tidy up the content if possible.
        var beautified = Y.M.atto_html.beautify.html_beautify(host.textarea.get('value'), {
            'indent_size': 4,
            'indent_inner_html': true
        });
        host.textarea.set('value', beautified);

        // Create the codeMirror from the textarea.
        this._codeMirror = Y.M.atto_html.CodeMirror.fromTextArea(host.textarea.getDOMNode(), {
            lineNumbers: true,
            mode: 'htmlmixed',
            tabSize: 2,
            lineWrapping: true
        });
        this._codeMirror.on('change', function(cm) {
            cm.save();
        });

        // Hide the text area and ensure that the codeMirror is displayed.
        this.editor.hide();
        this._codeMirror.setSize(dimensions.width, dimensions.height);

        // Focus on the codeMirror.
        this._codeMirror.focus();
    },

    /**
     * Hide the code mirror.
     *
     * @method _hideCodeMirror
     * @private
     */
    _hideCodeMirror: function() {
        var host = this.get('host');

        // Disable placeholder for empty content.
        // This will prevent the editor to copy and submit the empty placeholder.
        host.disablePlaceholderForEmptyContent();

        // Enable all plugins.
        host.enablePlugins();

        // Copy the text to the contenteditable div.
        host.updateFromTextArea();

        // Hide the textarea, and show the editor.
        if (this._codeMirror) {
            Y.one(this._codeMirror.getWrapperElement()).hide();

            // Calling toTextArea will remove the editor and all of its handlers.
            this._codeMirror.toTextArea();

            // Detach any remaining handlers.
            Y.Object.each(this._codeMirror._handlers, function(handlers, eventName) {
                Y.Array.each(handlers, function(handler) {
                    this.off(eventName, handler);
                }, this);
            }, this._codeMirror);

            // Cleanup.
            delete this._codeMirror;
            this._codeMirror = null;
        }
        host.textarea.hide();
        this.editor.show();

        // Focus on the editor.
        host.focus();

        // And re-mark everything as updated.
        this.markUpdated();

        // Enable placeholder for empty content.
        host.enablePlaceholderForEmptyContent();
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
