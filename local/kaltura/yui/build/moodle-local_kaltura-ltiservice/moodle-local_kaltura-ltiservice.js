YUI.add('moodle-local_kaltura-ltiservice', function (Y, NAME) {

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
 * @method LTISERVICE
 */
var LTISERVICE = function() {
    LTISERVICE.superclass.constructor.apply(this, arguments);
};

Y.extend(LTISERVICE, Y.Base, {
        /**
         * Init function for triggering a custom event and setting attributes.  Also checks whether optional elements exist in the parent window.
         * @property params
         * @type {Object}
         */
        init : function(params) {
            var documentElement = window.opener ? window.opener.parent.document : window.parent.document;
            if (documentElement.getElementById('video_title')) {
                Y.one(documentElement.getElementById('video_title')).setAttribute('value', params.title);
            }

            if (documentElement.getElementById('entry_id')) {
                Y.one(documentElement.getElementById('entry_id')).setAttribute('value', params.entryid);
            }

            if (documentElement.getElementById('height')) {
                Y.one(documentElement.getElementById('height')).setAttribute('value', params.height);
            }

            if (documentElement.getElementById('width')) {
                Y.one(documentElement.getElementById('width')).setAttribute('value', params.width);
            }

            if (documentElement.getElementById('uiconf_id')) {
                Y.one(documentElement.getElementById('uiconf_id')).setAttribute('value', '1');
            }

            if (documentElement.getElementById('widescreen')) {
                Y.one(documentElement.getElementById('widescreen')).setAttribute('value', '1');
            }

            if (documentElement.getElementById('video_preview_frame')) {
                Y.one(documentElement.getElementById('video_preview_frame')).setAttribute('src', params.previewltilauncher);
            } else if (documentElement.getElementById('contentframe')) {
                Y.one(documentElement.getElementById('contentframe')).setAttribute('src', decodeURIComponent(params.iframeurl));
                Y.one(documentElement.getElementById('contentframe')).setStyle('width', params.width + 'px');
                Y.one(documentElement.getElementById('contentframe')).setStyle('height', params.height + 'px');
            }

            // This element must exist.
            Y.one(documentElement.getElementById('source')).setAttribute('value', decodeURIComponent(params.iframeurl));

            if (documentElement.getElementById('metadata')) {
                Y.one(documentElement.getElementById('metadata')).setAttribute('value', params.metadata);
            }

            if (window.parent.insertMedia) {
                window.parent.insertMedia();
                return;
            }

            if (documentElement.getElementById('closeltipanel')) {
                Y.one(documentElement.getElementById('closeltipanel')).simulate('click');
            }

            documentElement.body.dispatchEvent(documentElement.body.entrySelectedEvent);
        }
    },
    {
        NAME : 'moodle-local_kaltura-ltiservice',
        ATTRS : {
            iframeurl : {
                value: ''
            },
            width : {
                value: ''
            },
            height : {
                value: ''
            },
            entryid : {
                value: ''
            },
            title : {
                value: ''
            },
            metadata : {
                value: ''
            }
        }

    });
M.local_kaltura = M.local_kaltura || {};

/**
 * Entry point for ltiservice module
 * @param string params additional parameters.
 * @return object the ltiservice object
 */
M.local_kaltura.init = function(params) {
    return new LTISERVICE(params);
};


}, '@VERSION@', {"requires": ["base", "node", "node-event-simulate"]});
