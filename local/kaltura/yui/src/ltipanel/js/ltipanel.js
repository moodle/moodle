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
 * @method LTIPANEL
 */
var LTIPANEL = function() {
    LTIPANEL.superclass.constructor.apply(this, arguments);
};

Y.extend(LTIPANEL, Y.Base, {
    /**
     * The name of the initiating module.
     * @property modulename
     * @type {String}
     * @default null
     */
    modulename: null,

    /**
     * The id value of the add media button.
     * @property addvidbtnid
     * @type {String}
     * @default null
     */
    addvidbtnid: null,

    /**
     * Init function for the checkboxselection module
     * @property params
     * @type {Object}
     */
    init : function(params) {
        // Check to make sure parameters are initialized
        if ('0' === params.addvidbtnid || '0' === params.ltilaunchurl || 0 === params.courseid || 0 === params.height || 0 === params.width) {
            alert('Some parameters were not initialized.');
            return;
        }

        this.modulename = params.modulename;
        this.addvidbtnid = params.addvidbtnid;

        var addvideobtn = Y.one('#'+params.addvidbtnid);
        addvideobtn.on('click', this.open_bse_popup_callback, this, params.ltilaunchurl, params.height, params.width);
    },

    /**
     * Event handler callback for when the add video button is clicked.  This function creates the a panel element.
     * @property e
     * @type {Object}
     */
    open_bse_popup_callback : function(e, url, height, width) {
        var w = 1200;
        var h = 700;
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var bsePopup = window.open(url, M.util.get_string("browse_and_embed", "local_kaltura"), 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        if (window.focus) {
            bsePopup.focus();
        }

        document.body.bsePopup = bsePopup;
        var entrySelectedEvent = this.createEntrySelectedEvent();
        document.body.entrySelectedEvent = entrySelectedEvent;
        document.body.addEventListener('entrySelected', this.close_popup_callback.bind(this), false);
    },

    createEntrySelectedEvent: function() {
        var entrySelectedEvent;
        if(typeof window.CustomEvent === 'function') {
            entrySelectedEvent = new CustomEvent('entrySelected');
        }
        else {
            // ie >= 9
            entrySelectedEvent = document.createEvent('CustomEvent');
            entrySelectedEvent.initCustomEvent('entrySelected', false, false, {});
        }

        return entrySelectedEvent;
    },



    /**
     * Event handler callback for when a simulated click event is triggered on a specifc element.
     */
    close_popup_callback : function() {
        // hide the thumbnail image.
        var imagenode = Y.one('img[id=video_thumbnail]');
        imagenode.setStyle('display', 'none');
        // Update the iframe element attributes
        var iframenode = Y.one('iframe[id=contentframe]');
        iframenode.setAttribute('width', Y.one('input[id=width]').getAttribute('value'));
        iframenode.setAttribute('height', Y.one('input[id=height]').getAttribute('value'));
        iframenode.setStyle('display', 'inline');

        // If the page is a Video presentation or resource execute a function to change the button caption KALDEV-579
        var element = Y.one('input[name=modulename]');

        if (undefined !== element && ('kalvidres' === this.modulename || 'kalvidpres' === this.modulename)) {
            this.lti_panel_change_add_media_button_caption();
        }

        document.body.bsePopup.close();
    },

    lti_panel_change_add_media_button_caption : function() {
        // Need to find a better way of doing this.  Change was made for KALDEV-579.
        var buttoncaption = M.util.get_string('replace_video', this.modulename);
        if (buttoncaption !== Y.one('#'+this.addvidbtnid).getAttribute('value')) {
            Y.one('#'+this.addvidbtnid).setAttribute('value', buttoncaption);
        }
    },
},
{
    NAME : 'moodle-local_kaltura-ltipanel',
    ATTRS : {
        addvidbtnid : {
            value: '0'
        },
        ltilaunchurl : {
            value: '0'
        },
        height : {
            value: 0
        },
        width : {
            value: 0
        },
        modulename : {
            value: ''
        }
    }
});

/**
 * This method calls the base class constructor.  The primary difference between LTIPANELMEDIAASSIGNMENT and LTIPANEL is that
 * LTIPANELMEDIAASSIGNMENT creates a node and appends it to the body tag of the page.  The reason for this is due to an issue with the Moodle
 * navbar covering up part of the YUI panel, if the panel markup is appended to a child element within the body tag.
 * @method LTIPANELMEDIAASSIGNMENT
 */
var LTIPANELMEDIAASSIGNMENT = function() {
    LTIPANELMEDIAASSIGNMENT.superclass.constructor.apply(this, arguments);
};

Y.extend(LTIPANELMEDIAASSIGNMENT, Y.Base, {
    /**
     * Init function for the checkboxselection module
     * @property params
     * @type {Object}
     */
    init : function(params) {
        // Check to make sure parameters are initialized
        if ('0' === params.addvidbtnid || '0' === params.ltilaunchurl || 0 === params.courseid || 0 === params.height || 0 === params.width) {
            return;
        }

        var addvideobtn = Y.one('#'+params.addvidbtnid);
        addvideobtn.on('click', this.open_bse_popup_callback, this, params.ltilaunchurl, params.height, params.width);
    },

    /**
     * Event handler callback for when the panel content is changed
     * @property e
     * @type {Object}
     */
    open_bse_popup_callback: function(e, url, height, width) {
        var w = 1200;
        var h = 700;
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var bsePopup = window.open(url, M.util.get_string("browse_and_embed", "local_kaltura"), 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        if (window.focus) {
            bsePopup.focus();
        }

        document.body.bsePopup = bsePopup;
        var entrySelectedEvent = this.createEntrySelectedEvent();
        document.body.entrySelectedEvent = entrySelectedEvent;
        document.body.addEventListener('entrySelected', this.close_popup_callback.bind(this), false);
    },

    createEntrySelectedEvent: function() {
        var entrySelectedEvent;
        if(typeof window.CustomEvent === 'function') {
            entrySelectedEvent = new CustomEvent('entrySelected');
        }
        else {
            // ie >= 9
            entrySelectedEvent = document.createEvent('CustomEvent');
            entrySelectedEvent.initCustomEvent('entrySelected', false, false, {});
        }

        return entrySelectedEvent;
    },

    close_popup_callback: function() {
        Y.one('input[id=submit_video]').removeAttribute('disabled');
        // hide the thumbnail image.
        var imagenode = Y.one('img[id=video_thumbnail]');
        imagenode.setStyle('display', 'none');
        // Update the iframe element attributes
        var iframenode = Y.one('iframe[id=contentframe]');
        iframenode.setAttribute('width', Y.one('input[id=width]').getAttribute('value'));
        iframenode.setAttribute('height', Y.one('input[id=height]').getAttribute('value'));
        iframenode.setStyle('display', 'inline');
        Y.one('#id_add_video').set('value', M.util.get_string('replacevideo', 'kalvidassign'));
        document.body.bsePopup.close();
    },

},
{
    NAME : 'moodle-local_kaltura-ltipanel',
    ATTRS : {
        addvidbtnid : {
            value: '0'
        },
        ltilaunchurl : {
            value: '0'
        },
        height : {
            value: 0
        },
        width : {
            value: 0
        }
    }
});

/**
 * This method calls the base class constructor.  This module renders a Panel for viewing media from multiple sources.
 * @method LTISUBMISSIONREVIEW
 */
var LTISUBMISSIONREVIEW = function() {
    LTISUBMISSIONREVIEW.superclass.constructor.apply(this, arguments);
};

Y.extend(LTISUBMISSIONREVIEW, Y.Base, {
    /**
     * An instance of the ltimediaassignment class.
     * @property ltimediaassignment
     * @type {Object}
     * @default null
     */
     ltimediaassignment: null,


    /**
     * Init function for the checkboxselection module
     * @property params
     * @type {Object}
     */
    init : function(ltimediaassignment) {
        this.ltimediaassignment = ltimediaassignment;
        Y.one('form[id=fastgrade]').delegate('click', this.review_submission, 'a[name=submission_source]', this);
    },

    /**
     * Callback function for when a user clicks on the review submission link.
     * @property e
     * @type {Object}
     */
    review_submission : function(e) {
        e.preventDefault();
        var source, height, width;
        // Test if the target is an anchor tag or img tag.
        if (e.target.test('a')) {
            source = e.target.getAttribute('href');
            height = e.target.ancestor('div[name=media_submission]').get('childNodes').filter('input[name=height]').get('value');
            width = e.target.ancestor('div[name=media_submission]').get('childNodes').filter('input[name=width]').get('value');
        } else {
            source = e.target.ancestor('a[name=submission_source]').getAttribute('href');
            height = e.target.ancestor('div[name=media_submission]').get('childNodes').filter('input[name=height]').get('value');
            width = e.target.ancestor('div[name=media_submission]').get('childNodes').filter('input[name=width]').get('value');
        }

        this.ltimediaassignment.open_bse_popup_callback(null, source, height, width);
    }
},
{
    NAME : 'moodle-local_kaltura-ltipanel'
});

M.local_kaltura = M.local_kaltura || {};

/**
 * Entry point for ltipanel module
 * @param string params additional parameters.
 * @return object the ltipanel object
 */
M.local_kaltura.init = function(params) {
    return new LTIPANEL(params);
};

/**
 * Entry point for ltipanelmediaassignment module
 * @param string params additional parameters.
 * @return object the ltipanel object
 */
M.local_kaltura.initmediaassignment = function(params) {
    return new LTIPANELMEDIAASSIGNMENT(params);
};

/**
 * Entry point for ltipanelmediaassignment module
 * @param string params additional parameters.
 * @return object the ltipanel object
 */
M.local_kaltura.initreviewsubmission = function() {
    var args = {
        addvidbtnid: '0',
        ltilaunchurl: '0',
        courseid: 0,
        height: 0,
        width: 0
    };
    var mediaassignment = new LTIPANELMEDIAASSIGNMENT(args);
    return new LTISUBMISSIONREVIEW(mediaassignment);
};
