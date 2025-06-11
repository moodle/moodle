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
 * Essay question type upgrade code.
 *
 * @package    qtype
 * @subpackage essayautograde
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the essayautograde question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_essayautograde_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    $plugintype = 'qtype';
    $pluginname = 'essayautograde';
    $plugin = $plugintype.'_'.$pluginname;
    $pluginoptionstable = $plugin.'_options';

    $newversion = 2017020203;
    if ($oldversion < $newversion) {
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2017020305;
    if ($oldversion < $newversion) {
        $select = 'qeo.*';
        $from   = '{qtype_essay_options} qeo JOIN {question} q ON qeo.questionid = q.id';
        $where  = 'q.qtype = :qtype';
        $params = array('qtype' => 'essayautograde');
        if ($records = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {
            foreach ($records as $record) {
                $DB->delete_records('qtype_essay_options', array('id' => $record->id));
                $record->enableautograde = 1;
                $record->itemtype        = 2; // 2=words
                $record->itemcount       = 100;
                if ($record->id = $DB->get_field($pluginoptionstable, 'id', array('questionid' => $record->questionid))) {
                    $DB->update_record($pluginoptionstable, $record);
                } else {
                    unset($record->id);
                    $DB->insert_record($pluginoptionstable, $record);
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2017020914;
    if ($oldversion < $newversion) {
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2017021217;
    if ($oldversion < $newversion) {
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable);
        $table = new xmldb_table($pluginoptionstable);
        if ($dbman->field_exists($table, 'textstatitems')) {
            $field = 'autofeedback';
            if ($dbman->field_exists($table, $field)) {
                $select = "$field IS NOT NULL AND $field <> ?";
                $DB->set_field_select($pluginoptionstable, 'showtextstats', 2, $select, array(''));
                $DB->execute('UPDATE {'.$pluginoptionstable.'} SET textstatitems = '.$field);
            }
            if ($dbman->field_exists($table, $field)) {
                $field = new xmldb_field($field);
                $dbman->drop_field($table, $field);
            }
            $field = 'textstatitems';
            if ($records = $DB->get_records_select($pluginoptionstable, $DB->sql_like($field, '?'), array('%hardword%'))) {
                foreach ($records as $record) {
                    $value = str_replace('hardword', 'longword', $record->$field);
                    $DB->set_field($pluginoptionstable, $field, $value, array('id' => $record->id));
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2017040931;
    if ($oldversion < $newversion) {
        $table = new xmldb_table($pluginoptionstable);
        $field = 'allowoverride';
        if ($dbman->field_exists($table, $field)) {
            $field = new xmldb_field($field);
            $dbman->drop_field($table, $field);
        }
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2018061535;
    if ($oldversion < $newversion) {
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, 'showfeedback');
        // increment constant values to make space for new SHOW_STUDENTS_ONLY (=1)
        // SHOW_TEACHERS_AND_STUDENTS (2 => 3)
        // SHOW_TEACHERS_ONLY (1 => 2)
        $fields = array('showcalculation', 'showtextstats', 'showgradebands', 'showtargetphrases');
        foreach ($fields as $field) {
            $DB->set_field_select($pluginoptionstable, $field, 3, "$field = ?", array(2));
            $DB->set_field_select($pluginoptionstable, $field, 2, "$field = ?", array(1));
        }
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2018073044;
    if ($oldversion < $newversion) {
        // Add "filetypeslist" column to save the allowed file types.
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, 'filetypeslist');
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2019030358;
    if ($oldversion < $newversion) {
        // Add fields for sample reponse and error glossary/database.
        $fieldnames = 'responsesample, responsesampleformat, errorcmid, errorpercent';
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, $fieldnames);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2021061000;
    if ($oldversion < $newversion) {
        // Add new fields for Moodle >= 3.10.
        $fieldnames = array('minwordlimit', 'maxwordlimit', 'maxbytes');
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, $fieldnames);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2021071205;
    if ($oldversion < $newversion) {
        // Add new fields for more granular matching of entries in the Glossary of common errors
        $fieldnames = array('errorfullmatch', 'errorcasesensitive', 'errorignorebreaks');
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, $fieldnames);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2022092117;
    if ($oldversion < $newversion) {
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2023040723;
    if ($oldversion < $newversion) {
        // Add "allowsimilarity" column to denote maximum allowable level of similarity.
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, 'allowsimilarity');
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2025030434;
    if ($oldversion < $newversion) {
        // Add AI fields: "aiassistant" and "aipercent".
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, 'aiassistant, aipercent');
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2025032237;
    if ($oldversion < $newversion) {

        $select = 'qasd.*';

        $from = '{question} q,'.
                '{question_attempts} qa,'.
                '{question_attempt_steps} qas,'.
                '{question_attempt_step_data} qasd';

        $where = '(q.qtype = ? OR q.qtype = ?) AND '.
                 'q.id = qa.questionid AND '.
                 'qa.id = qas.questionattemptid AND '.
                 'qas.id = qasd.attemptstepid AND '.
                 $DB->sql_like('qasd.name', '?');

        $params = ['essayautograde', 'speakautograde', '-ai%'];

        if ($records = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {
            foreach ($records as $id => $record) {
                $record->name = '_'.substr($record->name, 1);
                $DB->update_record('question_attempt_step_data', $record);
            }
        }

        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2025040342;
    if ($oldversion < $newversion) {
        // Align the default value of the "allowsimilarity" field with its value in install.xml.
        xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, 'allowsimilarity');
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    return true;
}

/**
 * Upgrade code for the essayautograde question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_essayautograde_addfields($dbman, $pluginoptionstable, $fieldnames=null) {

    static $allfieldsadded = false;

    if ($allfieldsadded) {
        return true;
    }

    if ($fieldnames===null) {
        $allfieldsadded = true;
    }

    if (is_string($fieldnames)) {
        $fieldnames = explode(',', $fieldnames);
        $fieldnames = array_map('trim', $fieldnames);
        $fieldnames = array_filter($fieldnames);
    }

    $table = new xmldb_table($pluginoptionstable);
    $fields = array(

        // Fields that are inherited from the "Essay" question type.
        // We omit "id" and "questionid" because they are indexed fields and therefore hard to update.
        // We include "allowsimilarity", because it relates to the template and sample.
        new xmldb_field('responseformat',         XMLDB_TYPE_CHAR,   16, null, XMLDB_NOTNULL, null, 'editor'),
        new xmldb_field('responserequired',       XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 1),
        new xmldb_field('responsefieldlines',     XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 15),
        new xmldb_field('minwordlimit',           XMLDB_TYPE_INTEGER, 10),
        new xmldb_field('maxwordlimit',           XMLDB_TYPE_INTEGER, 10),
        new xmldb_field('attachments',            XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('attachmentsrequired',    XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('graderinfo',             XMLDB_TYPE_TEXT),
        new xmldb_field('graderinfoformat',       XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        // AI fields added in Moodle 4.5.
        new xmldb_field('aiassistant',            XMLDB_TYPE_CHAR,  255, null, XMLDB_NOTNULL),
        new xmldb_field('aipercent',              XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        // Note: graderinfo is used as prompt for the AI assistant
        new xmldb_field('responsetemplate',       XMLDB_TYPE_TEXT),
        new xmldb_field('responsetemplateformat', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('responsesample',         XMLDB_TYPE_TEXT),
        new xmldb_field('responsesampleformat',   XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('allowsimilarity',        XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 10),
        new xmldb_field('maxbytes',               XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('filetypeslist',          XMLDB_TYPE_TEXT),

        // Fields that are specific to the "Essay (auto-grade)" question type.
        new xmldb_field('enableautograde',        XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 1),
        new xmldb_field('itemtype',               XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('itemcount',              XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showfeedback',           XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showcalculation',        XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtextstats',          XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('textstatitems',          XMLDB_TYPE_CHAR,  255, null, XMLDB_NOTNULL),
        new xmldb_field('showgradebands',         XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('addpartialgrades',       XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtargetphrases',      XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorcmid',              XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorpercent',           XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorfullmatch',         XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorcasesensitive',     XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorignorebreaks',      XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),

        // Feedback fields that are common to other automatically graded question types (e.g. "Short answer").
        new xmldb_field('correctfeedback',        XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL),
        new xmldb_field('correctfeedbackformat',  XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('incorrectfeedback',      XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL),
        new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL),
        new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0)
    );

    $previousfield = 'questionid';
    foreach ($fields as $field) {
        $currentfield = $field->getName();
        if ($fieldnames===null || in_array($currentfield, $fieldnames)) {
            if ($dbman->field_exists($table, $previousfield)) {
                $field->setPrevious($previousfield);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }
        $previousfield = $currentfield;
    }
}
