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

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015100201) {

        // Upgrade steps need to be isolated from our APIs, so we should prevent using constants that
        // can change over time. This is a simple copy & paste from question/type/multianswer/questiontype.php
        // with the following changes, the idea is to prevent regressions as much as possible.
        // * All constants prefixed with UPGRADE_2015100201_
        // * Removed all constants not required by UPGRADE_2015100201_ANSWER_REGEX
        if (!defined('UPGRADE_2015100201_ANSWER_REGEX')) {
            define('UPGRADE_2015100201_ANSWER_ALTERNATIVE_FRACTION_REGEX',
                   '=|%(-?[0-9]+)%');
            define('UPGRADE_2015100201_ANSWER_ALTERNATIVE_ANSWER_REGEX',
                    '.+?(?<!\\\\|&|&amp;)(?=[~#}]|$)');
            define('UPGRADE_2015100201_ANSWER_ALTERNATIVE_FEEDBACK_REGEX',
                    '.*?(?<!\\\\)(?=[~}]|$)');
            define('UPGRADE_2015100201_ANSWER_ALTERNATIVE_REGEX',
                   '(' . UPGRADE_2015100201_ANSWER_ALTERNATIVE_FRACTION_REGEX .')?' .
                   '(' . UPGRADE_2015100201_ANSWER_ALTERNATIVE_ANSWER_REGEX . ')' .
                   '(#(' . UPGRADE_2015100201_ANSWER_ALTERNATIVE_FEEDBACK_REGEX .'))?');

            // Remaining ANSWER regexes.
            define('UPGRADE_2015100201_ANSWER_TYPE_DEF_REGEX',
                    '(NUMERICAL|NM)|(MULTICHOICE|MC)|(MULTICHOICE_V|MCV)|(MULTICHOICE_H|MCH)|' .
                    '(SHORTANSWER|SA|MW)|(SHORTANSWER_C|SAC|MWC)|' .
                    '(MULTICHOICE_S|MCS)|(MULTICHOICE_VS|MCVS)|(MULTICHOICE_HS|MCHS)');
            define('UPGRADE_2015100201_ANSWER_START_REGEX',
                   '\{([0-9]*):(' . UPGRADE_2015100201_ANSWER_TYPE_DEF_REGEX . '):');

            define('UPGRADE_2015100201_ANSWER_REGEX',
                    UPGRADE_2015100201_ANSWER_START_REGEX
                    . '(' . UPGRADE_2015100201_ANSWER_ALTERNATIVE_REGEX
                    . '(~'
                    . UPGRADE_2015100201_ANSWER_ALTERNATIVE_REGEX
                    . ')*)\}');

            define('UPGRADE_2015100201_ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE', 4);
        }

        // Detect the exact table/field we want to use, coz can be different
        // depending of the version we are upgrading from. See MDL-52291 and MDL-52298.
        $multichoicetable = 'qtype_multichoice_options';
        $multichoicefield = 'questionid';
        if (!$dbman->table_exists($multichoicetable)) {
            // Multichoice not upgraded yet, let's use old names.
            $multichoicetable = 'question_multichoice';
            $multichoicefield = 'question';
        }
        $rs = $DB->get_recordset_sql("SELECT q.id, q.category, qma.sequence
                 FROM {question} q
                 JOIN {question_multianswer} qma ON q.id = qma.question");
        foreach ($rs as $q) {
            $sequence = preg_split('/,/', $q->sequence, -1, PREG_SPLIT_NO_EMPTY);
            if ($sequence) {
                // Get relevant data indexed by positionkey from the multianswers table.
                $wrappedquestions = $DB->get_records_list('question', 'id', $sequence, 'id ASC');
                foreach ($wrappedquestions as $wrapped) {
                    if ($wrapped->qtype == 'multichoice') {
                        $options = $DB->get_record($multichoicetable, array($multichoicefield => $wrapped->id), '*');
                        if (isset($options->shuffleanswers)) {
                            preg_match('/'.UPGRADE_2015100201_ANSWER_REGEX.'/s', $wrapped->questiontext, $answerregs);
                            if (isset($answerregs[UPGRADE_2015100201_ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE]) &&
                                    $answerregs[UPGRADE_2015100201_ANSWER_REGEX_ANSWER_TYPE_MULTICHOICE] !== '') {
                                $DB->set_field($multichoicetable, 'shuffleanswers', '0',
                                        array('id' => $options->id) );
                            }
                        } else {
                            $newrecord = new stdClass();
                            $newrecord->$multichoicefield = $wrapped->id;
                            $newrecord->correctfeedback = '';
                            $newrecord->partiallycorrectfeedback = '';
                            $newrecord->incorrectfeedback = '';
                            $DB->insert_record($multichoicetable, $newrecord);
                        }
                    }
                }
            }
        }
        $rs->close();
        // Multianswer savepoint reached.
        upgrade_plugin_savepoint(true, 2015100201, 'qtype', 'multianswer');
    }

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
