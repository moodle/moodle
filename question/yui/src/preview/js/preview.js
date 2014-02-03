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
 * JavaScript required by the question preview pop-up.
 *
 * @module moodle-question-preview
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.question = M.question || {};

/**
 * Initialise JavaScript-specific parts of the question preview popup.
 */
M.question.preview.init = function(Y) {
    M.core_question_engine.init_form(Y, '#responseform');

    // Add a close button to the window.
    var closebutton = Y.Node.create('<input type="button" />');
    closebutton.set('value', M.str.question.closepreview);
    Y.one('#previewcontrols').append(closebutton);
    Y.on('click', function() { window.close(); }, closebutton);

    // Stop a question form being submitted more than once.
    Y.on('submit', M.core_question_engine.prevent_repeat_submission, '#mform1', null, Y);
};
