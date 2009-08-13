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
 * Javascript helper function for Resource module
 *
 * @package   mod-resource
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function resource_init_object() {
    YAHOO.util.Event.onDOMReady(function () {
        imscp_setup_object();
    });
}

function imscp_setup_object() {
    resource_resize_object();

    // fix layout if window resized too
    window.onresize = function() {
        resource_resize_object();
    };
}

function resource_resize_object() {
    var obj = YAHOO.util.Dom.get('resourceobject');
    obj.style.width = '0px';
    obj.style.height = '0px';
    var newwidth = resource_get_htmlelement_size('content', 'width') - 15;
    if (newwidth > 600) {
        obj.style.width = newwidth  + 'px';
    } else {
        obj.style.width = '600px';
    }
    var pageheight = resource_get_htmlelement_size('page', 'height');
    var objheight = resource_get_htmlelement_size(obj, 'height');
    var newheight = objheight + parseInt(YAHOO.util.Dom.getViewportHeight()) - pageheight - 30;
    if (newheight > 400) {
        obj.style.height = newheight + 'px';
    } else {
        obj.style.height = '400px';
    }
}


function resource_get_htmlelement_size(el, prop) {
    var val = YAHOO.util.Dom.getStyle(el, prop);
    if (val == 'auto') {
        if (el.get) {
            el = el.get('element'); // get real HTMLElement from YUI element
        }
        val = YAHOO.util.Dom.getComputedStyle(YAHOO.util.Dom.get(el), prop);
    }
    return parseInt(val);
}
