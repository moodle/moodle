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
 * JavaScript belonging to question_bank_view.
 *
 * This script is included by question_bank_view and other parts of question/editlib.php.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


question_bank = {
    strselectall: '',
    strdeselectall: '',
    headercheckbox: null,
    firstcheckbox: null,

    init_checkbox_column: function(Y, strselectall, strdeselectall, firstcbid) {
        question_bank.strselectall = strselectall;
        question_bank.strdeselectall = strdeselectall;

        // Find the header checkbox, and initialise it.
        question_bank.headercheckbox = document.getElementById('qbheadercheckbox');
        question_bank.headercheckbox.disabled = false;
        question_bank.headercheckbox.title = strselectall;

        // Find the first real checkbox.
        question_bank.firstcheckbox = document.getElementById(firstcbid);

        // Add the event handler.
        Y.YUI2.util.Event.addListener(question_bank.headercheckbox, 'click', question_bank.header_checkbox_click);
    },

    header_checkbox_click: function() {
        if (question_bank.firstcheckbox.checked) {
            select_all_in_element_with_id('categoryquestions', '');
            question_bank.headercheckbox.title = question_bank.strselectall;
        } else {
            select_all_in_element_with_id('categoryquestions', 'checked');
            question_bank.headercheckbox.title = question_bank.strdeselectall;
        }
        question_bank.headercheckbox.checked = false;
    }
};
