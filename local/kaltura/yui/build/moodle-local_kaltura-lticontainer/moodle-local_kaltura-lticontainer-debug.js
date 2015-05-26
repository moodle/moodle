YUI.add('moodle-local_kaltura-lticontainer', function (Y, NAME) {

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
 * YUI module used to resize the LTI launch container.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * This method calls the base class constructor
 * @method LTICONTAINER
 */
var LTICONTAINER = function() {
    LTICONTAINER.superclass.constructor.apply(this, arguments);
};

Y.extend(LTICONTAINER, Y.Base, {
    /**
     * The last known height of the element.
     * @property lastheight
     * @type {Integer}
     * @default null
     */
     lastheight: null,

    /**
     * Add padding to make the bottom of the iframe visible.  The iframe wasn't visible on some themes. Probably because of border widths, etc.
     * @property padding
     * @type {Integer}
     * @default 15
     */
    padding: 15,

    /**
     * Height of window.
     * @property viewportheight
     * @type {Integer}
     * @default 15
     */
    viewportheight: null,

    /**
     * Height of the entire document.
     * @property documentheight
     * @type {Integer}
     * @default null
     */
    documentheight: null,

    /**
     * Height of the body element.
     * @property documentheight
     * @type {Integer}
     * @default null
     */
    clientheight: null,

    /**
     * User video width size selection.
     * @property kalvidwidth
     * @type {Integer}
     * @default null
     */
    kalvidwidth: null,

    /**
     * The YUI node object for the iframe container.
     * @property ltiframe
     * @type {Object}
     * @default null
     */
     ltiframe: null,

    /**
     * The width of the entry
     * @property width
     * @type {int}
     * @default null
     */
    width: null,

    /**
     * The height of the entry
     * @property height
     * @type {int}
     * @default null
     */
    height: null,

    /**
     * Init function for the checkboxselection module
     * @property params
     * @type {Object}
     */
    init : function(params) {
        var bodynode = Y.one('body[class~='+params.bodyclass+']');

        if(params.height && params.width)
        {
            this.height = params.height;
            this.width = params.width;
        }

        this.lastheight = params.lastheight;
        this.padding = params.padding;
        this.viewportheight = bodynode.get('winHeight');
        this.documentheight = bodynode.get('docHeight');
        this.clientheight = bodynode.getDOMNode.clientHeight;
        this.ltiframe = Y.one('#contentframe');
        this.kalvidwidth = params.kalvidwidth;

        this.resize();
        this.timer = Y.later(250, this, this.resize);
    },

    /**
     * This function resizes the iframe height and width.
     */
    resize : function() {
        if (this.lastheight !== Math.min(this.documentheight, this.viewportheight)) {
            var newheight = this.viewportheight - this.ltiframe.getY() - this.padding+"px";
            this.ltiframe.setStyle('height', newheight);
            this.lastheight = Math.min(this.documentheight, this.viewportheight);
        }

        var kalvidcontent = Y.one('#kalvid_content');
        if (kalvidcontent !== null) {
            var maxwidth = kalvidcontent.get('offsetWidth');
            var allowedsize = maxwidth - this.padding;

            if (this.kalvidwidth !== null) {
                // Double current user's video width selection as requested by Kaltura.
                var newsize = this.kalvidwidth * 2;

                // If "newsize" if over allowed size then set it to the maximum allowed.
                if (newsize > allowedsize) {
                    this.ltiframe.setStyle('width', allowedsize+'px');
                } else {
                    this.ltiframe.setStyle('width', newsize+'px');
                }
            }
        }

        // if we have the entry's dimensions - use them to adjust the iframe size.
        if(this.height && this.width)
        {
            this.ltiframe.setStyle('width', this.width+'px');
            this.ltiframe.setStyle('height', this.height+'px');
        }
    }
},
{
    NAME : 'moodle-local_kaltura-lticontainer',
    ATTRS : {
        bodyclass : {
            value : null
        },
        lastheight : {
            value : null
        },
        padding: {
            value : 15
        }
    }
});
M.local_kaltura = M.local_kaltura || {};

/**
 * Entry point for lticontainer module
 * @param string params additional parameters.
 * @return object the lticontainer object
 */
M.local_kaltura.init = function(params) {
    return new LTICONTAINER(params);
};


}, '@VERSION@', {"requires": ["base", "node"]});
