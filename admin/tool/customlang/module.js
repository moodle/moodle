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
 * @package    tool
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @namespace
 */
M.tool_customlang = M.tool_customlang || {};

/**
 * YUI instance holder
 */
M.tool_customlang.Y = {};

/**
 * Initialize JS support for the edit.php
 *
 * @param {Object} Y YUI instance
 */
M.tool_customlang.init_editor = function(Y) {
    M.tool_customlang.Y = Y;

    Y.all('#translator .local textarea').each(function (textarea) {
        var cell = textarea.get('parentNode');
        textarea.setStyle('height', cell.getComputedStyle('height'));
    });
}
