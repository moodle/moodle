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
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Register the needed events
onload=function() {
    // Adjust the form on load
    disablePopupHeads();
}

/**
 * This function disables some elements from the command and from the fields/keys/indexes drop downs
 */
function disablePopupHeads() {
    var popup = document.getElementById("menucommand");
    var i = popup.length;
    while (i--) {
        option = popup[i];
        if (option.value == "Fields" || option.value == "Keys" || option.value == "Indexes") {
            popup[i].disabled = true;
        }
    }
    popup = document.getElementById("menufieldkeyindex");
    i = popup.length;
    while (i--) {
        option = popup[i];
        if (option.value == "fieldshead" || option.value == "keyshead" || option.value == "indexeshead") {
            popup[i].disabled = true;
        }
    }
}
