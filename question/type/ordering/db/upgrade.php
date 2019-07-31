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
 * Ordering question type db upgrade script
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the ordering question type.
 *
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_ordering_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    $newversion = 2013062800;
    if ($oldversion < $newversion) {
        $select = 'qn.*, qo.id AS questionorderingid';
        $from   = '{question} qn LEFT JOIN {question_ordering} qo ON qn.id = qo.question';
        $where  = 'qn.qtype = ? AND qo.id IS NULL';
        $params = array('ordering');
        if ($questions = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {
            foreach ($questions as $question) {
                if ($answers = $DB->get_records('question_answers', array('question' => $question->id))) {
                    // Add "options" for this ordering question.
                    $questionordering = (object) array(
                        'question'   => $question->id,
                        'logical'    => 1,
                        'studentsee' => min(6, count($answers)),
                        'correctfeedback' => '',
                        'partiallycorrectfeedback' => '',
                        'incorrectfeedback' => ''
                    );
                    $questionordering->id = $DB->insert_record('question_ordering', $questionordering);
                } else {
                    // This is a faulty ordering question - remove it.
                    $DB->delete_records('question', array('id' => $question->id));
                    if ($dbman->table_exists('quiz_question_instances')) {
                        $DB->delete_records('quiz_question_instances', array('question' => $question->id));
                    }
                    if ($dbman->table_exists('reader_question_instances')) {
                        $DB->delete_records('reader_question_instances', array('question' => $question->id));
                    }
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2015011915;
    if ($oldversion < $newversion) {

        // Rename "ordering" table for Moodle >= 2.5.
        $oldname = 'question_ordering';
        $newname = 'qtype_ordering_options';

        if ($dbman->table_exists($oldname)) {
            $oldtable = new xmldb_table($oldname);
            if ($dbman->table_exists($newname)) {
                $dbman->drop_table($oldtable);
            } else {
                $dbman->rename_table($oldtable, $newname);
            }
        }

        // Remove index on question(id) field (because we want to modify the field).
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array('question', 'questionid');
        foreach ($fields as $field) {
            if ($dbman->field_exists($table, $field)) {
                $index = new xmldb_index('qtypordeopti_que_uix', XMLDB_INDEX_UNIQUE, array($field));
                if ($dbman->index_exists($table, $index)) {
                    $dbman->drop_index($table, $index);
                }
            }
        }

        // Rename "question"   -> "questionid".
        // Rename "logical"    -> "selecttype".
        // Rename "studentsee" -> "selectcount".
        // Add    "(xxx)feedbackformat" fields.
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array(
            'questionid' => new xmldb_field('question', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'id'),
            'selecttype' => new xmldb_field('logical', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'questionid'),
            'selectcount' => new xmldb_field('studentsee', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'selecttype'),
            'correctfeedbackformat' => new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null,
                    '0', 'correctfeedback'),
            'incorrectfeedbackformat' => new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL,
                    null, '0', 'incorrectfeedback'),
            'partiallycorrectfeedbackformat' => new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                    XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback')
        );
        foreach ($fields as $newname => $field) {
            $oldexists = $dbman->field_exists($table, $field);
            $newexists = $dbman->field_exists($table, $newname);
            if ($field->getName() != $newname && $oldexists) {
                if ($newexists) {
                    $dbman->drop_field($table, $field);
                } else {
                    $dbman->rename_field($table, $field, $newname);
                    $newexists = true;
                }
                $oldexists = false;
            }
            $field->setName($newname);
            if ($newexists) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }

        // Make sure there are no duplicate "questionid" fields in "qtype_ordering_options" table.
        $select = 'questionid, COUNT(*) AS countduplicates, MAX(id) AS maxid';
        $from   = '{qtype_ordering_options}';
        $group  = 'questionid';
        $having = 'countduplicates > ?';
        $params = array(1);
        if ($records = $DB->get_records_sql("SELECT $select FROM $from GROUP BY $group HAVING $having", $params)) {
            foreach ($records as $record) {
                $select = 'id <> ? AND questionid = ?';
                $params = array($record->maxid, $record->questionid);
                $DB->delete_records_select('qtype_ordering_options', $select, $params);
            }
        }

        // Restore index on questionid field.
        $table = new xmldb_table('qtype_ordering_options');
        $index = new xmldb_index('qtypordeopti_que_uix', XMLDB_INDEX_UNIQUE, array('questionid'));
        if (! $dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2015110725;
    if ($oldversion < $newversion) {
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array(
            new xmldb_field('layouttype', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'questionid'),
            new xmldb_field('selecttype', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'layouttype')
        );
        foreach ($fields as $field) {
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2015121734;
    if ($oldversion < $newversion) {
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array(
            new xmldb_field('gradingtype', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'selectcount')
        );
        foreach ($fields as $field) {
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
                // When adding this field to existing records,
                // the gradingtype is set to whatever the selecttype is.
                $DB->execute('UPDATE {qtype_ordering_options} SET gradingtype = selecttype', array());
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2016032949;
    if ($oldversion < $newversion) {
        if ($dbman->table_exists('reader_question_instances')) {
            $select = 'rqi.question, COUNT(*) AS countquestion';
            $from   = '{reader_question_instances} rqi '.
                      'LEFT JOIN {question} q ON rqi.question = q.id';
            $where  = 'q.qtype = ?';
            $group  = 'rqi.question';
            $params = array('ordering');
            if ($questions = $DB->get_records_sql("SELECT $select FROM $from WHERE $where GROUP BY $group", $params)) {
                $questions = array_keys($questions);
                list($select, $params) = $DB->get_in_or_equal($questions);
                $select = "questionid $select";
                $table = 'qtype_ordering_options';
                $DB->set_field_select($table, 'layouttype',  0, $select, $params); // VERTICAL.
                $DB->set_field_select($table, 'selecttype',  1, $select, $params); // RANDOM.
                $DB->set_field_select($table, 'gradingtype', 1, $select, $params); // RELATIVE.

                // For selectcount, we only fix the value, if it is zero (=ALL)
                // because Ordering questions for some low level books use 4.
                $select .= ' AND selectcount = ?';
                $params[] = 0;
                $DB->set_field_select($table, 'selectcount', 6, $select, $params); // Six.
            }
        }
    }

    $newversion = 2016081655;
    if ($oldversion < $newversion) {
        $table = new xmldb_table('qtype_ordering_options');
        $field = new xmldb_field('showgrading', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 1, 'gradingtype');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_type($table, $field);
        } else {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2019071191;
    if ($oldversion < $newversion) {

        // Add field "numberingstyle" to table "qtype_ordering_options".
        // This field was briefly called "answernumbering".
        $table = new xmldb_table('qtype_ordering_options');
        $field = new xmldb_field('answernumbering', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'none', 'showgrading');
        $newname = 'numberingstyle';

        $oldexists = $dbman->field_exists($table, $field);
        $newexists = $dbman->field_exists($table, $newname);
        if ($oldexists) {
            if ($newexists) {
                $dbman->drop_field($table, $field);
            } else {
                $dbman->rename_field($table, $field, $newname);
                $newexists = true;
            }
            $oldexists = false;
        }
        $field->setName($newname);
        if ($newexists) {
            $dbman->change_field_type($table, $field);
        } else {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = '2019073193';
    if ($oldversion < $newversion) {
        $table = 'qtype_ordering_options';
        $field = 'numberingstyle';
        $select = "$field = ? OR $field = ?";
        $params = array('III', 'ABC');
        if ($options = $DB->get_records_select($table, $select, $params, $field, "id,$field")) {
            foreach ($options as $option) {
                switch ($option->numberingstyle) {
                    case 'ABC':
                        $DB->set_field($table, $field, 'ABCD', array('id' => $option->id));
                        break;
                    case 'III':
                        $DB->set_field($table, $field, 'IIII', array('id' => $option->id));
                        break;
                    // Ignore "abc", "iii", and anything else.
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    return true;
}


