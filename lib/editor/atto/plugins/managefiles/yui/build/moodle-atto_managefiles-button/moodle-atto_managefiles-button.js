YUI.add('moodle-atto_managefiles-button', function (Y, NAME) {

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
 * @package    atto_managefiles
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto-managefiles-button
 */

/**
 * Atto text editor managefiles plugin.
 *
 * @namespace M.atto_link
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var LOGNAME = 'atto_managefiles',
    CAN_RECEIVE_FOCUS_SELECTOR = '.fp-navbar a:not([disabled])',
    FILE_MANAGER_SELECTOR = '#fitem_id_files_filemanager';

Y.namespace('M.atto_managefiles').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    initializer: function() {
        if (this.get('disabled')) {
            return;
        }

        var host = this.get('host'),
            area = this.get('area'),
            options = host.get('filepickeroptions');

        if (options.image && options.image.itemid) {
            area.itemid = options.image.itemid;
            this.set('area', area);
        } else {
            return;
        }

        this.addButton({
            icon: 'e/manage_files',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the manage files.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function(e) {
        e.preventDefault();

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('managefiles', LOGNAME),
            width: '800px',
            focusAfterHide: true
        });

        var iframe = Y.Node.create('<iframe></iframe>');
        // We set the height here because otherwise it is really small. That might not look
        // very nice on mobile devices, but we considered that enough for now.
        iframe.setStyles({
            height: '700px',
            border: 'none',
            width: '100%'
        });
        iframe.setAttribute('src', this._getIframeURL());

        // Focus on the first focusable element of the file manager after it is fully loaded.
        iframe.on('load', function(e, frame) {
            var fileManager = frame.getDOMNode().contentDocument.querySelector(FILE_MANAGER_SELECTOR);
            // The file manager component is loaded asynchronously after the page is loaded.
            // We check for the presence of .fm-loaded every 200 ms to determine if the file manager is loaded yet.
            var intervalId = setInterval(function() {
                if (fileManager.querySelector('.fm-loaded')) {
                    var firstFocusableElement = fileManager.querySelector(CAN_RECEIVE_FOCUS_SELECTOR);
                    if (firstFocusableElement) {
                        firstFocusableElement.focus();
                    }
                    clearInterval(intervalId);
                }
            }, 200);
        }, this, iframe);

        dialogue.set('bodyContent', iframe)
                .show();

        this.markUpdated();
    },

    /**
     * Returns the URL to the file manager.
     *
     * @param _getIframeURL
     * @return {String} URL
     * @private
     */
    _getIframeURL: function() {
        var args = Y.mix({
                    elementid: this.get('host').get('elementid')
                },
                this.get('area'));
        return M.cfg.wwwroot + '/lib/editor/atto/plugins/managefiles/manage.php?' +
                Y.QueryString.stringify(args);
    }
}, {
    ATTRS: {
        disabled: {
            value: true
        },
        area: {
            value: {}
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
