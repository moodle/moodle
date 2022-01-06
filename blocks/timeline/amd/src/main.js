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
 * Javascript to initialise the timeline block.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'block_timeline/view_nav',
    'block_timeline/view'
],
function(
    $,
    ViewNav,
    View
) {

    var SELECTORS = {
        TIMELINE_VIEW: '[data-region="timeline-view"]'
    };

    /**
     * Initialise all of the modules for the timeline block.
     *
     * @param {object} root The root element for the timeline block.
     */
    var init = function(root) {
        root = $(root);
        var viewRoot = root.find(SELECTORS.TIMELINE_VIEW);

        // Initialise the timeline navigation elements.
        ViewNav.init(root, viewRoot);
        // Initialise the timeline view modules.
        View.init(viewRoot);
    };

    return {
        init: init
    };
});
