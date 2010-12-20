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
 * This file the Moodle question engine.
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Initialise JavaScript-specific parts of the question preview popup.
 */
function question_preview_init(caption, addto) {
    // Add a close button to the window.
    var button = document.createElement('input');
    button.type = 'button';
    button.value = caption;

    YAHOO.util.Event.addListener(button, 'click', function() { window.close() });

    var container = document.getElementById(addto);
    container.appendChild(button);

    // Make changint the settings disable all submit buttons, like clicking one of the
    // question buttons does.
    var form = document.getElementById('mform1');
    YAHOO.util.Event.addListener(form, 'submit',
            question_prevent_repeat_submission, document.body);
}