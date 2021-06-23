YUI.add('moodle-atto_preview-button', function (Y, NAME) {

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
 * @package    atto_preview
 * @copyright  2015 onward Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_preview-button
 */

/**
 * Atto text editor preview plugin.
 *
 * @namespace M.atto_preview
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var PLUGINNAME = 'atto_preview',
    PREVIEW = 'preview',
    STATE = false;

Y.namespace('M.atto_preview').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var button = this.addButton({
            icon: 'e/preview',
            callback: this._toggle
        });
        button.set('title', M.util.get_string('pluginname', PLUGINNAME));
        // If there is an event that may resize the editor, adjust the size of the preview.
        Y.after('windowresize', Y.bind(this._fitToScreen, this));
        this.editor.on(['gesturemove', 'gesturemoveend'], Y.bind(this._fitToScreen, this), {
            standAlone: true
        }, this);
        this.toolbar.on('click', Y.bind(this._fitToScreen, this));

    },

    /**
     * Toggle preview and normal display mode
     *
     * @method _toggle
     * @param {EventFacade} e
     * @private
     */
    _toggle: function(e) {
        e.preventDefault();
        var button = this.buttons[PREVIEW];

        if (button.getData(STATE)) {
            this.unHighlightButtons(PREVIEW);
            this._setpreview(button);
        } else {
            this.highlightButtons(PREVIEW);
            this._setpreview(button, true);
        }

    },

    /**
     * Adjust editor to screen size
     *
     * @method _fitToScreen
     * @private
     */
    _fitToScreen: function() {
        var button = this.buttons[PREVIEW];
        if (!button.getData(STATE)) {
            return;
        }
        var host = this.get('host');
        this.preview.setStyles({
            position: "absolute",
            height: host.editor.getComputedStyle('height'),
            width: host.editor.getComputedStyle('width'),
            top: host.editor.getComputedStyle('top'),
            left: host.editor.getComputedStyle('left')
        });
        this.preview.setY(this.editor.getY());
    },

    /**
     * Change preview display state
     *
     * @method _setpreview
     * @param {Node} button The preview button
     * @param {Boolean} mode Whether the editor display preview * @private
     */
    _setpreview: function(button, mode) {
        var host = this.get('host');

        if (mode) {
            this.preview = Y.Node.create('<iframe src="'
                + this.get('previewurl') + '?sesskey='
                + this.get('sesskey')
                + '&contextid=' + this.get('contextid')
                + '&content=' + encodeURIComponent(host.textarea.get('value'))
                + '" srcdoc="" id="atto-preview"></iframe');
            this.preview.setStyles({
                backgroundColor: Y.one('body').getComputedStyle('backgroundColor'),
                backgroundImage: 'url(' + M.util.image_url('i/loading', 'core') + ')',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'center center'
            });
            host._wrapper.appendChild(this.preview);

            // Now we try this using the io module.
            var params = {
                    sesskey: this.get('sesskey'),
                    contextid: this.get('contextid'),
                    content: host.textarea.get('value')
                };

            // Fetch content and load asynchronously.
            Y.io(this.get('previewurl'), {
                    context: this,
                    data: params,
                    on: {
                            complete: this._loadContent
                        },
                    method: 'POST'
                });

            // Disable all plugins.
            host.disablePlugins();

            // And then re-enable this one.
            host.enablePlugins(this.name);

            // Enable fullscreen plugin if present.
            if (typeof Y.M.atto_fullscreen !== 'undefined') {
                host.enablePlugins('fullscreen');
            }

        } else {
            this.preview.remove(true);

            // Enable all plugins.
            host.enablePlugins();
        }
        button.setData(STATE, !!mode);
        this._fitToScreen();

    },

    /**
     * Load filtered content into iframe
     *
     * @param {String} id
     * @param {EventFacade} e
     * @method _loadPreview
     * @private
     */
    _loadContent: function(id, e) {
        var content = e.responseText;

        this.preview.setAttribute('srcdoc', content);
    }
}, {
    ATTRS: {
        /**
         * The url to use when loading the preview.
         *
         * @attribute previewurl
         * @type String
         */
        previewurl: {
            value: null
        },

        /**
         * The contextid to use when generating this preview.
         *
         * @attribute contextid
         * @type String
         */
        contextid: {
            value: null
        },

        /**
         * The sesskey to use when generating this preview.
         *
         * @attribute sesskey
         * @type String
         */
        sesskey: {
            value: null
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
