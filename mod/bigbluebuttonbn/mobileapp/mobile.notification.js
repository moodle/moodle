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
 * This file is part of the Moodle apps support for the choicegroup plugin.
 * Defines the function to be used from the mobile course view template.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

// That will be the global this (ionic convention).
var that = this;

var TIMEOUTCHECK = 20000;

that.onCanJoinReturns = function (data) {
    if (data && data.can_join) {
        that.openContent('', {'cmid': data.cmid}, 'mod_bigbluebuttonbn', 'mobile_course_view');
    } else {
        setTimeout(function () {
            that.refreshContent(true);
        }, TIMEOUTCHECK);
    }
};