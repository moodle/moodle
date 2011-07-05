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
 * Multi-answer question type upgrade code.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the multi-answer question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_multianswer_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2008050800) {
        //hey - no functions here in this file !!!!!!!

        $rs = $DB->get_recordset_sql("SELECT q.id, q.category, qma.sequence
                                        FROM {question} q
                                        JOIN {question_multianswer} qma ON q.id = qma.question");
        foreach ($rs as $q) {
            if (!empty($q->sequence)) {
                $DB->execute("UPDATE {question}
                                 SET parent = ?, category = ?
                               WHERE id IN ($q->sequence) AND parent <> 0",
                             array($q->id, $q->category));
            }
        }
        $rs->close();

        /// multianswer savepoint reached
        upgrade_plugin_savepoint(true, 2008050800, 'qtype', 'multianswer');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
