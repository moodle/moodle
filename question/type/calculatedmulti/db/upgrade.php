<?php
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
 * Calculated multiple-choice question type upgrade code.
 *
 * @package    qtype_calculatedmulti
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the calculatedmulti question type.
 * @param int $oldversion the version we are upgrading from.
 * @return bool
 */
function xmldb_qtype_calculatedmulti_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2024011700) {
        // In earlier versions, the answer options (choices) for a calculated multiple choice
        // question could not use HTML and all text was rendered verbatim. However, the texts
        // were stored in the DB with answerformat == FORMAT_HTML. This value was then overridden
        // during initialisation of the question.
        // From this version on, answer options may use HTML, so the answerformat does now have a
        // meaning. For backwards compatibility, all existing answer options for this question
        // type must have their answerformat set to FORMAT_PLAIN.
        $DB->execute("UPDATE {question_answers}
                              SET answerformat = '" . FORMAT_PLAIN . "'
                            WHERE question IN (
                                SELECT id
                                  FROM {question}
                                 WHERE qtype = 'calculatedmulti'
                                 )"
        );

        // Calculatedmulti savepoint reached.
        upgrade_plugin_savepoint(true, 2024011700, 'qtype', 'calculatedmulti');
    }

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
