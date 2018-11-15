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
 * Controls the favourites section of the overview page in the message drawer.
 *
 * @module     core_message/message_drawer_view_overview_section_favourites
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core_message/message_drawer_view_overview_section'
],
function(
    $,
    Section
) {
    // All conversation types.
    var CONVERSATION_TYPE = null;
    var INCLUDE_FAVOURITES = true;

    /**
     * Show the overview page conversations.
     *
     * @param {Object} root Overview messages container element.
     */
    var show = function(root) {
        Section.show($(root), CONVERSATION_TYPE, INCLUDE_FAVOURITES);
    };

    return {
        show: show,
    };
});
