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
        if (window.parent.document.getElementById('video_title')) {
            Y.one(window.parent.document.getElementById('video_title')).setAttribute('value', params.title);
        }

        if (window.parent.document.getElementById('entry_id')) {
            Y.one(window.parent.document.getElementById('entry_id')).setAttribute('value', params.entryid);
        }

        if (window.parent.document.getElementById('height')) {
            Y.one(window.parent.document.getElementById('height')).setAttribute('value', params.height);
        }

        if (window.parent.document.getElementById('width')) {
            Y.one(window.parent.document.getElementById('width')).setAttribute('value', params.width);
        }

        if (window.parent.document.getElementById('uiconf_id')) {
            Y.one(window.parent.document.getElementById('uiconf_id')).setAttribute('value', '1');
        }

        if (window.parent.document.getElementById('widescreen')) {
            Y.one(window.parent.document.getElementById('widescreen')).setAttribute('value', '1');
        }

        if (window.parent.document.getElementById('video_preview_frame')) {
            Y.one(window.parent.document.getElementById('video_preview_frame')).setAttribute('src', decodeURIComponent(params.iframeurl));
        } else if (window.parent.document.getElementById('contentframe')) {
            Y.one(window.parent.document.getElementById('contentframe')).setAttribute('src', decodeURIComponent(params.iframeurl));
        }

        // This element must exist.
        Y.one(window.parent.document.getElementById('source')).setAttribute('value', decodeURIComponent(params.iframeurl));

        if (window.parent.document.getElementById('metadata')) {
            Y.one(window.parent.document.getElementById('metadata')).setAttribute('value', params.metadata);
        }

        if (window.parent.document.getElementById('closeltipanel')) {
            Y.one(window.parent.document.getElementById('closeltipanel')).simulate('click');
        }
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
