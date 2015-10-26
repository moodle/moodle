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

require_once($CFG->dirroot . '/question/type/multianswer/questiontype.php');

/**
 * Upgrade code for the multi-answer question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_multianswer_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015100201) {
        $rs = $DB->get_recordset_sql("SELECT q.id, q.category, qma.sequence
                 FROM {question} q
                 JOIN {question_multianswer} qma ON q.id = qma.question");
        foreach ($rs as $q) {
            if (!empty($q->sequence)) {
                // Get relevant data indexed by positionkey from the multianswers table.
                $wrappedquestions = $DB->get_records_list('question', 'id',
                        explode(',', $q->sequence), 'id ASC');
                foreach ($wrappedquestions as $wrapped) {
                    if ($wrapped->qtype == 'multichoice') {
                        $options = $DB->get_record('qtype_multichoice_options', array('questionid' => $wrapped->id), '*');
                        if (isset($options->shuffleanswers)) {
                            preg_match('/'.ANSWER_REGEX.'/s', $wrapped->questiontext, $answerregs);
                            if (isset($answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE]) &&
                                    $answerregs[ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE] !== '') {
                                $DB->set_field('qtype_multichoice_options', 'shuffleanswers', '0',
                                        array('id' => $options->id) );
                            }
                        } else {
                            $newrecord = new stdClass();
                            $newrecord->questionid = $wrapped->id;
                            $newrecord->correctfeedback = '';
                            $newrecord->partiallycorrectfeedback = '';
                            $newrecord->incorrectfeedback = '';
                            $DB->insert_record('qtype_multichoice_options', $newrecord);
                        }
                    }
                }
            }
        }
        $rs->close();
        // Multianswer savepoint reached.
        upgrade_plugin_savepoint(true, 2015100201, 'qtype', 'multianswer');
    }

    return true;
}
