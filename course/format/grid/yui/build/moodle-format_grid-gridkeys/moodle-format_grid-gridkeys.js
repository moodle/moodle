YUI.add('gallery-event-nav-keys', function(Y) {

var keys = {
        enter    : 13,
        esc      : 27,
        backspace: 8,
        tab      : 9,
        pageUp   : 33,
        pageDown : 34,
        left     : 37,
        up       : 38,
        right    : 39,
        down     : 40
    };

Y.Object.each(keys, function (keyCode, name) {
    Y.Event.define({
        type: name,

        on: function (node, sub, notifier, filter) {
            var method = (filter) ? 'delegate' : 'on';

            sub._handle = node[method]('keydown', function (e) {
                if (e.keyCode === keyCode) {
                    notifier.fire(e);
                }
            }, filter);
        },

        delegate: function () {
            this.on.apply(this, arguments);
        },

        detach: function (node, sub) {
            sub._handle.detach();
        },

        detachDelegate: function () {
            this.detach.apply(this, arguments);
        }
    });
});


}, 'gallery-2011.02.02-21-07' ,{requires:['event-synthetic']});
YUI.add('moodle-format_grid-gridkeys', function (Y, NAME) {

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
 * Grid Format - A topics based format that uses a grid of user selectable images to popup a light box of the section.
 *
 * @package    course/format
 * @subpackage grid
 * @copyright  &copy; 2013 onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.format_grid = M.format_grid || {};
M.format_grid.gridkeys = M.format_grid.gridkeys || {};
M.format_grid.gridkeys = {
    init: function() {
        Y.on('esc', function (e) {
            e.preventDefault();
            M.format_grid.icon_toggle(e);
        });
        Y.on('left', function (e) {
            e.preventDefault();
            M.format_grid.arrow_left(e);
        });
        Y.on('right', function (e) {
            e.preventDefault();
            M.format_grid.arrow_right(e);
        });
        /* Deferred functionality - see CONTRIB-3240...
        Y.on('enter', function (e) {
            //var ae = document.activeElement;
            if (M.format_grid.shadebox.shadebox_open == false) {
                e.preventDefault();
                M.format_grid.icon_toggle(e);
            } else if (e.shiftKey) {
                e.preventDefault();
                M.format_grid.icon_toggle(e);
            }
        });
        Y.on('tab', function (e) {
            //var ae = document.activeElement;
            if (M.format_grid.shadebox.shadebox_open == false) {
                e.preventDefault();
                if (e.shiftKey) {
                    M.format_grid.arrow_left(e);
                } else {
                    M.format_grid.arrow_right(e);
                }
            }
        });
        */
    }
};

}, '@VERSION@', {"requires": ["gallery-event-nav-keys"]});
