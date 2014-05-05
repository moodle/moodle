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
 * Scrollview for grader table.
 *
 * @package   gradereport_grader
 * @copyright 2013 NetSpot Pty Ltd
 * @author    Adam Olley <adam.olley@netspot.com.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.gradereport_grader = M.gradereport_grader || {};
M.gradereport_grader.scrollview = {

    /** Selectors. */
    SELECTORS: {
        CONTAINER: '.gradeparent',
        STATIC:    '.gradeparent .right_scroller',
        GRADETABLE: '#user-grades'
    },

    container : null,

    /**
     * Initialise the scrollview code.
     */
    init: function() {
        this.container = Y.one(this.SELECTORS.CONTAINER);
        if (!this.container) {
            Y.log('No grade container found.');
            return;
        }

        var topscroll = Y.Node.create('<div class="right_scroller topscroll"><div class="topscrollcontent"></div></div>');

        var src = this.SELECTORS.CONTAINER;
        if (Y.one(this.SELECTORS.STATIC)) {
            src = this.SELECTORS.STATIC;
        }

        var node = Y.one(src).insert(topscroll, 'before');

        if (!Y.one(this.SELECTORS.STATIC)) {
            node = Y.one('.topscroll');
        }

        Y.on('domready', function () {
            this.resize();
        }, this);

        Y.one(src).on('scroll', function() {
            node.set('scrollLeft', Y.one(src).get('scrollLeft'));
        });

        node.on('scroll', function() {
            Y.one(src).set('scrollLeft', node.get('scrollLeft'));
        });

    },

    resize: function() {
        var width = Y.one(this.SELECTORS.GRADETABLE).get('offsetWidth');
        Y.one('.topscrollcontent').setStyle('width', width + 'px');
    }
};
