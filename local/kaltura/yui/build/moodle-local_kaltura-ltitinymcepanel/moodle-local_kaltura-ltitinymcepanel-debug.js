YUI.add('moodle-local_kaltura-ltitinymcepanel', function (Y, NAME) {

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
 * YUI module for displaying an LTI launch within a YUI panel.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * This method calls the base class constructor
 * @method LTITINYMCEPANEL
 */
var LTITINYMCEPANEL = function() {
    LTITINYMCEPANEL.superclass.constructor.apply(this, arguments);
};

Y.extend(LTITINYMCEPANEL, Y.Base, {
        /**
         * The context id the editor was launched in.
         * @property contextid
         * @type {Integer}
         * @default null
         */
        contextid: 0,

        /**
         * Init function for the checkboxselection module
         * @property {Object} params Data to help initialize the YUI module.
         */
        init : function(params) {
            // Check to make sure parameters are initialized.
            if ('' === params.ltilaunchurl || '' === params.objecttagheight || '' === params.objecttagid || '' === params.previewiframeid) {
                alert('Some parameters were not initialized.');
                return;
            }

            // Initialize the the browse when the window is initially rendered.
            this.load_lti_content(params.ltilaunchurl, params.objecttagid, params.objecttagheight);

            // Listen to simulated click event send from local/kaltura/service.php
            Y.one('#closeltipanel').on('click', this.user_selected_video_callback, this, params.objecttagid, params.previewiframeid, params.objecttagheight);

            if (null !== Y.one('#page-footer')) {
                Y.one('#page-footer').setStyle('display', 'none');
            }
        },

        /**
         * A funciton to load the LTI content.  This is called when the YUI module is first initialized.
         * @property {String} url LTI launch URL.
         * @property {String} iframeid iframe tag id.
         * @property {String} iframeheight iframe tag height.
         */
        load_lti_content : function(url, iframeid, iframeheight) {
            if (0 === this.contextid) {
                this.contextid = Y.one('#lti_launch_context_id').get('value');
            }

            var content = '<iframe id="lti_view_element" height="'+iframeheight+'px" width="100%" src="'+url+'&amp;contextid='+this.contextid+'" allow="autoplay *; fullscreen *; encrypted-media *; camera *; microphone *;"></iframe>';
            Y.one('#'+iframeid).setContent(content);
        },

        /**
         * This function serves as a call back method for when the closeltipanel div has been clicked.  It means that the user has
         * selected a video for embedding into the TinyMCE edotor.  Enabling the insert button, removing the contents LTI launch element and
         * adding content to the media preview element.
         * @property {Object} e Event object.
         * @property {String} objecttagid Object tag id.
         * @property {String} previewiframeid Preview iframe tag id.
         * @property {String} height Height of the iframe.
         */
        user_selected_video_callback : function(e, objecttagid, previewiframeid, height) {
            Y.one('#'+objecttagid).setContent('');

            var center = Y.Node.create('<center></center>');
            var iframe = Y.Node.create('<iframe></iframe>');
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('width', Y.one('#width').get('value')+'px');
            iframe.setAttribute('height', height+'px');
            iframe.setAttribute('src', Y.one('#video_preview_frame').getAttribute('src'));

            center.append(iframe);
            Y.one('#'+previewiframeid).append(center);
        }
    },
    {
        NAME : 'moodle-local_kaltura-ltitinymcepanel',
        ATTRS : {
            ltilaunchurl : {
                value : ''
            },
            objecttagheight : {
                value : ''
            },
            objecttagid : {
                value : ''
            },
            previewiframeid : {
                value : ''
            }
        }
    });
M.local_kaltura = M.local_kaltura || {};

/**
 * Entry point for ltipanel module
 * @param {Object} params Additional parameters.
 * @return {Object} the ltipanel object
 */
M.local_kaltura.init = function(params) {
    return new LTITINYMCEPANEL(params);
};


}, '@VERSION@', {"requires": ["base", "node", "panel", "node-event-simulate"]});
