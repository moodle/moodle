// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/*
 * @package    atto_clickview
 * @copyright  2021 ClickView Pty. Limited <info@clickview.com.au>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_clickview-button
 */

/**
 * Atto text editor ClickView plugin.
 *
 * @namespace M.atto_clickview
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_clickview',
    TEMPLATE = '';

Y.namespace('M.atto_clickview').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function(params) {
        TEMPLATE = params.iframe;

        this._onlineUrl = params.hostlocation;
        this._iframeUrl = params.iframeurl;
        this._consumerKey = params.consumerkey;
        this._schoolId = params.schoolid;

        this.addButton({
            icon: 'icon',
            iconComponent: COMPONENTNAME,
            callback: this._displayDialogue,
            tags: 'iframe',
        });
    },

    /**
     * Display the embed tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        var self, dialogue, pluginFrame, eventsApi;

        self = this;

        dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            width: '816',
            focusAfterHide: true
        });

        dialogue.set('bodyContent', this._getDialogueContent()).show();

        pluginFrame = document.getElementById('clickview_iframe');
        eventsApi = new CVEventsApi(pluginFrame.contentWindow); /* global CVEventsApi */

        eventsApi.on('cv-lms-addvideo', function(event, detail) {
            self._insertVideo(detail.embedHtml);
            eventsApi.off('cv-lms-addvideo');
        }, true);

        dialogue.on('visibleChange', function(event) {
            if (event.newVal !== false) {
                return;
            }

            eventsApi.off('cv-lms-addvideo');
        });
    },

    /**
     * Insert the video.
     *
     * @method _insertVideo
     * @param {String} html the video html embed code
     * @private
     */
    _insertVideo: function(html) {
        var host = this.get('host');

        this.getDialogue({
            focusAfterHide: null
        }).hide();

        host.insertContentAtFocusPoint(html);

        this.markUpdated();
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function() {
        var url, template;

        url = this._onlineUrl + this._iframeUrl + '?consumerKey=' + this._consumerKey;

        if (this._schoolId) {
            url += '&schoolId=' + this._schoolId;
        }

        template = Y.Handlebars.compile(TEMPLATE);

        return Y.Node.create(template({
                component: COMPONENTNAME,
                url: url,
            }));
    }
});
