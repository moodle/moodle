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
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_questionnaire_upgrade($oldversion=0) {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    $result = true;

    if ($oldversion < 2007120101) {
        $result &= questionnaire_upgrade_2007120101();

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2007120101, 'questionnaire');
    }

    if ($oldversion < 2007120102) {
        // Change enum values to lower case for all tables using them.
        $table = new xmldb_table('questionnaire_question');

        $field = new xmldb_field('required');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_question', 'required', 'y', array('required' => 'Y'));
        $DB->set_field('questionnaire_question', 'required', 'n', array('required' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'n');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        $field = new xmldb_field('deleted');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_question', 'deleted', 'y', array('deleted' => 'Y'));
        $DB->set_field('questionnaire_question', 'deleted', 'n', array('deleted' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'n');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        $field = new xmldb_field('public');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_question', 'public', 'y', array('public' => 'Y'));
        $DB->set_field('questionnaire_question', 'public', 'n', array('public' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'y');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        unset($table);

        $table = new xmldb_table('questionnaire_question_type');

        $field = new xmldb_field('has_choices');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_question_type', 'has_choices', 'y', array('has_choices' => 'Y'));
        $DB->set_field('questionnaire_question_type', 'has_choices', 'n', array('has_choices' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'y');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        unset($table);

        $table = new xmldb_table('questionnaire_response');

        $field = new xmldb_field('complete');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_response', 'complete', 'y', array('complete' => 'Y'));
        $DB->set_field('questionnaire_response', 'complete', 'n', array('complete' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'n');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        unset($table);

        $table = new xmldb_table('questionnaire_response_bool');

        $field = new xmldb_field('choice_id');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_response_bool', 'choice_id', 'y', array('choice_id' => 'Y'));
        $DB->set_field('questionnaire_response_bool', 'choice_id', 'n', array('choice_id' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'y');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        unset($table);

        $table = new xmldb_table('questionnaire_survey');

        $field = new xmldb_field('public');
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, false, null, 'n');
        $dbman->change_field_enum($table, $field);
        $DB->set_field('questionnaire_survey', 'public', 'y', array('public' => 'Y'));
        $DB->set_field('questionnaire_survey', 'public', 'n', array('public' => 'N'));
        $field->set_attributes(XMLDB_TYPE_CHAR, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, XMLDB_ENUM, array('y', 'n'), 'y');
        $dbman->change_field_enum($table, $field);
        $dbman->change_field_default($table, $field);
        unset($field);

        // Upgrade question_type table with corrected 'response_table' fields.
        $DB->set_field('questionnaire_question_type', 'response_table', 'resp_single',
                        array('response_table' => 'response_single'));
        $DB->set_field('questionnaire_question_type', 'response_table', 'resp_multiple',
                        array('response_table' => 'response_multiple'));

        // Questionnaire savepoint reached..
        upgrade_mod_savepoint(true, 2007120102, 'questionnaire');
    }

    if ($oldversion < 2008031902) {
        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', false, true, false, false, null, 0, 'navigate');
        $dbman->add_field($table, $field);

        unset($field);
        unset($table);
        $table = new xmldb_table('questionnaire_response');
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', false, true, false, false, null, 0, 'complete');
        $dbman->add_field($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008031902, 'questionnaire');
    }

    if ($oldversion < 2008031904) {
        $sql = "SELECT q.id, q.resp_eligible, q.resp_view, cm.id as cmid
                FROM {questionnaire} q, {course_modules} cm, {modules} m
                WHERE m.name='questionnaire' AND m.id=cm.module AND cm.instance=q.id";
        if ($rs = $DB->get_recordset_sql($sql)) {
            $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
            $editteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
            $teacherroleid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));
            $capview = 'mod/questionnaire:view';
            $capsubmit = 'mod/questionnaire:submit';

            foreach ($rs as $questionnaire) {
                $context = get_context_instance(CONTEXT_MODULE, $questionnaire->cmid);

                // Convert questionnaires with resp_eligible = 'all' so that students & teachers have view and submit.
                if ($questionnaire->resp_eligible == 'all') {
                    assign_capability($capsubmit, CAP_ALLOW, $editteacherroleid, $context->id, true);
                    assign_capability($capsubmit, CAP_ALLOW, $teacherroleid, $context->id, true);
                    // Convert questionnaires with resp_eligible = 'students' so that just students have view and submit.
                } else if ($questionnaire->resp_eligible == 'teachers') {
                    assign_capability($capsubmit, CAP_ALLOW, $editteacherroleid, $context->id, true);
                    assign_capability($capsubmit, CAP_ALLOW, $teacherroleid, $context->id, true);
                    assign_capability($capview, CAP_PREVENT, $studentroleid, $context->id, true);
                    assign_capability($capsubmit, CAP_PREVENT, $studentroleid, $context->id, true);
                }
            }
            $rs->close();
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008031904, 'questionnaire');
    }

    if ($oldversion < 2008031905) {
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('changed');
        $dbman->drop_field($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008031905, 'questionnaire');
    }

    if ($oldversion < 2008031906) {
        $table = new xmldb_table('questionnaire_response_rank');
        $field = new xmldb_field('rank');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, null, '0', 'choice_id');
        $field->setUnsigned(false);
        $dbman->change_field_unsigned($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008031906, 'questionnaire');
    }

    if ($oldversion < 2008060401) {
        $table = new xmldb_table('questionnaire_question');
        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null, 'survey_id');
        $field->setNotnull(false);
        $dbman->change_field_notnull($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008060401, 'questionnaire');
    }

    if ($oldversion < 2008070702) {
        $table = new xmldb_table('questionnaire_question_type');
        $field = new xmldb_field('response_table');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, null, 'has_choices');
        $field->setNotnull(false);
        $dbman->change_field_notnull($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008070702, 'questionnaire');
    }

    if ($oldversion < 2008070703) {
        $table = new xmldb_table('questionnaire_resp_multiple');
        $index = new xmldb_index('response_question');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('response_id', 'question_id', 'choice_id'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008070703, 'questionnaire');
    }

    if ($oldversion < 2008070704) {
        // CONTRIB-1542.
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('email');
        $field->set_attributes(XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, null, null, 'title');
        $field->setLength('255');
        $dbman->change_field_precision($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008070704, 'questionnaire');
    }

    if ($oldversion < 2008070705) {
        // Rename summary field to 'intro' to adhere to new Moodle standard.
        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('summary');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null, 'name');
        $dbman->rename_field($table, $field, 'intro');

        // Add 'introformat' to adhere to new Moodle standard.
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');
        $dbman->add_field($table, $field);
        // Set all existing records to HTML format.
        $DB->set_field('questionnaire', 'introformat', 1);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008070705, 'questionnaire');
    }

    if ($oldversion < 2008070706) {
        // CONTRIB-1153.
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('public');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('questionnaire_question');
        $field = new xmldb_field('public');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2008070706, 'questionnaire');
    }

    if ($oldversion < 2010110100) {
        // Drop list of values (enum) from field has_choices on table questionnaire_question_type.
        $table = new xmldb_table('questionnaire_question_type');
        $field = new xmldb_field('has_choices', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'y', 'type');

        // Launch drop of list of values from field has_choices.
        $dbman->drop_enum_from_field($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2010110100, 'questionnaire');
    }

    if ($oldversion < 2010110101) {
        // Drop list of values (enum) from field respondenttype on table questionnaire.
        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('respondenttype', XMLDB_TYPE_CHAR, '9', null, XMLDB_NOTNULL, null, 'fullname', 'qtype');
        // Launch drop of list of values from field respondenttype.
        $dbman->drop_enum_from_field($table, $field);
        // Drop list of values (enum) from field resp_eligible on table questionnaire.
        $field = new xmldb_field('resp_eligible', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, 'all', 'respondenttype');
        // Launch drop of list of values from field resp_eligible.
        $dbman->drop_enum_from_field($table, $field);

        // Drop list of values (enum) from field required on table questionnaire_question.
        $table = new xmldb_table('questionnaire_question');
        $field = new xmldb_field('required', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'n', 'content');
        // Launch drop of list of values from field required.
        $dbman->drop_enum_from_field($table, $field);
        // Drop list of values (enum) from field deleted on table questionnaire_question.
        $field = new xmldb_field('deleted', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'n', 'required');
        // Launch drop of list of values from field deleted.
        $dbman->drop_enum_from_field($table, $field);

        // Drop list of values (enum) from field complete on table questionnaire_response.
        $table = new xmldb_table('questionnaire_response');
        $field = new xmldb_field('complete', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'n', 'submitted');
        // Launch drop of list of values from field complete.
        $dbman->drop_enum_from_field($table, $field);

        // Drop list of values (enum) from field choice_id on table questionnaire_response_bool.
        $table = new xmldb_table('questionnaire_response_bool');
        $field = new xmldb_field('choice_id', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, 'y', 'question_id');
        // Launch drop of list of values from field choice_id.
        $dbman->drop_enum_from_field($table, $field);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2010110101, 'questionnaire');
    }

    if ($oldversion < 2012100800) {
        // Changing precision of field name on table questionnaire_survey to (255).

        // First drop the index.
        $table = new xmldb_table('questionnaire_survey');
        $index = new xmldb_index('name');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        $dbman->drop_index($table, $index);

        // Launch change of precision for field name.
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_precision($table, $field);

        // Add back in the index.
        $table = new xmldb_table('questionnaire_survey');
        $index = new xmldb_index('name');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        $dbman->add_index($table, $index);

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2012100800, 'questionnaire');
    }

    if ($oldversion < 2013062302) {
        // Adding completionsubmit field to table questionnaire.

        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('completionsubmit', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2013062302, 'questionnaire');
    }

    if ($oldversion < 2013062501) {
        // Skip logic new feature.
        // Define field dependquestion to be added to questionnaire_question table.
        $table = new xmldb_table('questionnaire_question');
        $field = new xmldb_field('dependquestion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'deleted');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('questionnaire_question');
        $field = new xmldb_field('dependchoice', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'dependquestion');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Replace the = separator with :: separator in quest_choice content.
        // This fixes radio button options using old "value"="display" formats.
        require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');
        $choices = $DB->get_recordset('questionnaire_quest_choice', null);
        $total = $DB->count_records('questionnaire_quest_choice');
        if ($total > 0) {
            $pbar = new progress_bar('convertchoicevalues', 500, true);
            $i = 1;
            foreach ($choices as $choice) {
                if (($choice->value == null || $choice->value == 'NULL')
                                && !preg_match("/^([0-9]{1,3}=.*|!other=.*)$/", $choice->content)) {
                    $content = questionnaire_choice_values($choice->content);
                    if (strpos($content->text, '=')) {
                        $newcontent = str_replace('=', '::', $content->text);
                        $choice->content = $newcontent;
                        $DB->update_record('questionnaire_quest_choice', $choice);
                    }
                }
                $pbar->update($i, $total, "Convert questionnaire choice value separator - $i/$total.");
                $i++;
            }
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2013062501, 'questionnaire');
    }

    if ($oldversion < 2013100500) {
        // Add autonumbering option for questions and pages.
        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('autonum', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '3', 'completionsubmit');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2013100500, 'questionnaire');
    }

    if ($oldversion < 2013122202) {
        // Personality test feature.

        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('feedbacksections', XMLDB_TYPE_INTEGER, '2', null, null, null, null, null);
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        unset($field);
        $field = new xmldb_field('feedbacknotes', XMLDB_TYPE_TEXT, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        unset($table);
        unset($field);

        // Define table questionnaire_fb_sections to be created.
        $table = new xmldb_table('questionnaire_fb_sections');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('survey_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('section', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('scorecalculation', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sectionlabel', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('sectionheading', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sectionheadingformat', XMLDB_TYPE_INTEGER, '2', null, null, null, '1');

        // Adding keys to table questionnaire_fb_sections.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for assign_user_mapping.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        unset($table);

        // Define table questionnaire_feedback to be created.
        $table = new xmldb_table('questionnaire_feedback');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('feedbacklabel', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('feedbacktext', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('feedbacktextformat', XMLDB_TYPE_INTEGER, '2', null, null, null, '1');
        $table->add_field('minscore', XMLDB_TYPE_NUMBER, '10,5', null, null, null, '0.00000');
        $table->add_field('maxscore', XMLDB_TYPE_NUMBER, '10,5', null, null, null, '101.00000');

        // Adding keys to table questionnaire_fb_sections.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for assign_user_mapping.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2013122202, 'questionnaire');
    }

    if ($oldversion < 2014010300) {
        // Personality test with chart.
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('chart_type', XMLDB_TYPE_CHAR, '64', null, null, null, null, null);

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('feedbackscores', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Questionnaire savepoint reached.
         upgrade_mod_savepoint(true, 2014010300, 'questionnaire');
    }

    if ($oldversion < 2015051101) {
        // Move the global config value for 'usergraph' to the plugin config setting instead.
        if (isset($CFG->questionnaire_usergraph)) {
            set_config('usergraph', $CFG->questionnaire_usergraph, 'questionnaire');
            unset_config('questionnaire_usergraph');
        }
        upgrade_mod_savepoint(true, 2015051101, 'questionnaire');
    }

    // Add index to reduce load on the questionnaire_quest_choice table.
    if ($oldversion < 2015051102) {
        // Conditionally add an index to the question_id field.
        $table = new xmldb_table('questionnaire_quest_choice');
        $index = new xmldb_index('quest_choice_quesidx', XMLDB_INDEX_NOTUNIQUE, array('question_id'));
        // Only add the index if it does not exist.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2015051102, 'questionnaire');
    }

    // Ensuring database matches XML state for some known anomalies.
    if ($oldversion < 2016020204) {
        // Ensure the feedbackscores field can be null (CONTRIB-6445).
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('feedbackscores', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $dbman->change_field_notnull($table, $field);

        // Ensure the feddbacklabel field is 50 characters (CONTRIB-6445).
        $table = new xmldb_table('questionnaire_feedback');
        $field = new xmldb_field('feedbacklabel', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $dbman->change_field_precision($table, $field);

        // Ensure the response field is text.
        $table = new xmldb_table('questionnaire_response_date');
        $field = new xmldb_field('response', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $dbman->change_field_precision($table, $field);

        // Questionnaire savepoint reached.
         upgrade_mod_savepoint(true, 2016020204, 'questionnaire');
    }

    // Ensuring database matches XML state for some known anomalies.
    if ($oldversion < 2016111105) {
        $table = new xmldb_table('questionnaire');
        $field = new xmldb_field('notifications', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'resp_view');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Questionnaire savepoint reached.
         upgrade_mod_savepoint(true, 2016111105, 'questionnaire');
    }

    // Redoing the 2017050100 upgrade in 2017050101. If it already completed in 2017050100, skip it.
    if ($oldversion < 2017050101) {
        // Changing type of field username from char to int.
        $table = new xmldb_table('questionnaire_response');
        $field = new xmldb_field('username', XMLDB_TYPE_INTEGER, '10');
        // If it already completed in 2017050100, skip it.
        if ($dbman->field_exists($table, $field)) {
            // Before we change the field 'username' to an int, ensure there are only numeric values there.
            $sql = 'SELECT qr.id, qr.username, qa.rid, qa.userid ' .
                   'FROM {questionnaire_response} qr ' .
                   'INNER JOIN {questionnaire_attempts} qa ON qr.id = qa.rid ' .
                   'WHERE qr.username = ?';
            $rs = $DB->get_recordset_sql($sql, ["Anonymous"]);
            // Set all "Anonymous" records to the userid in the matching attempt record.
            foreach ($rs as $record) {
                $DB->set_field('questionnaire_response', 'username', "{$record->userid}", ['id' => $record->id]);
            }
            // If there are any leftover "Anonymous" records, set them all to userid zero (there shouldn't be).
            $rs = $DB->get_recordset('questionnaire_response', ['username' => 'Anonymous']);
            foreach ($rs as $record) {
                $DB->set_field('questionnaire_response', 'username', '0', ['id' => $record->id]);
            }

            // Launch change of type for field username.
            $dbman->change_field_type($table, $field);

            // Change the name from username to userid.
            $dbman->rename_field($table, $field, 'userid');
        }

        // Changing type of field owner from char to int.
        $table = new xmldb_table('questionnaire_survey');
        $field = new xmldb_field('owner', XMLDB_TYPE_INTEGER, '10');
        // If it already completed in 2017050100, skip it.
        if ($dbman->field_exists($table, $field)) {
            // Drop the old 'owner' index before modifying the field.
            $index = new xmldb_index('owner', XMLDB_INDEX_NOTUNIQUE, ['owner']);
            $dbman->drop_index($table, $index);

            // Launch change of type for field owner.
            $dbman->change_field_type($table, $field);

            // Change the name from owner to courseid.
            $dbman->rename_field($table, $field, 'courseid');

            // Add the index back with the new name.
            $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2017050101, 'questionnaire');
    }

    // Converting to new dependency system.
    if ($oldversion < 2017111101) {
        // MOD Multiparent Advanceddependencies START.
        // Define table questionnaire_dependency to be created.
        $table = new xmldb_table('questionnaire_dependency');

        // Adding fields to table questionnaire_depenencies.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('surveyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dependquestionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dependchoiceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dependlogic', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('dependandor', XMLDB_TYPE_CHAR, '4', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table questionnaire_depenencies.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table questionnaire_dependency.
        $table->add_index('quest_dependency_quesidx', XMLDB_INDEX_NOTUNIQUE, array('questionid'));

        // Conditionally launch create table for questionnaire_dependencies.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);

            // Copy all existing branching data into new branching structure.
            $branchingrs = $DB->get_recordset_select('questionnaire_question', 'dependquestion > 0 AND deleted = \'n\'',
                null, '', 'id, survey_id, dependquestion, dependchoice');
            foreach ($branchingrs as $qid => $qinfo) {
                $newrec = new stdClass();
                $newrec->questionid = $qid;
                $newrec->surveyid = $qinfo->survey_id;
                $newrec->dependquestionid = $qinfo->dependquestion;
                $newrec->dependchoiceid = $qinfo->dependchoice;
                $newrec->dependlogic = 1; // Set to "answer given", previously the only option.
                $newrec->dependandor = 'and'; // Not used previously.
                $DB->insert_record('questionnaire_dependency', $newrec);
            }
            $branchingrs->close();

            // After copying all old data, remove the unused fields.
            $table = new xmldb_table('questionnaire_question');
            $field1 = new xmldb_field('dependquestion');
            $field2 = new xmldb_field('dependchoice');
            if ($dbman->field_exists($table, $field1)) {
                $dbman->drop_field($table, $field1);
            }
            if ($dbman->field_exists($table, $field2)) {
                $dbman->drop_field($table, $field2);
            }
            // MOD Multiparent Advanceddependencies END.

            // Add a new index for survey_id to the question table.
            $index = new xmldb_index('quest_question_sididx', XMLDB_INDEX_NOTUNIQUE, ['survey_id', 'deleted']);
            // Only add the index if it does not exist.
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }
        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2017111101, 'questionnaire');
    }

    // Converting to new dependency system.
    if ($oldversion < 2017111103) {

        // If these fields exist, possibly due to incorrect creation from a new install (see CONTRIB-7300), remove them.
        $table = new xmldb_table('questionnaire_question');
        $field1 = new xmldb_field('dependquestion');
        $field2 = new xmldb_field('dependchoice');
        if ($dbman->field_exists($table, $field1)) {
            $dbman->drop_field($table, $field1);
        }
        if ($dbman->field_exists($table, $field2)) {
            $dbman->drop_field($table, $field2);
        }

        // Questionnaire savepoint reached.
        upgrade_mod_savepoint(true, 2017111103, 'questionnaire');
    }

    return $result;
}

// Supporting functions used once.
function questionnaire_upgrade_2007120101() {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    $status = true;

    // Shorten table names to bring them in accordance with the XML DB schema.
    $qtable = new xmldb_table('questionnaire_question_choice');
    $dbman->rename_table($qtable, 'questionnaire_quest_choice', false);
    unset($qtable);

    $qtable = new xmldb_table('questionnaire_response_multiple');
    $dbman->rename_table($qtable, 'questionnaire_resp_multiple', false);
    unset($qtable);

    $qtable = new xmldb_table('questionnaire_response_single');
    $dbman->rename_table($qtable, 'questionnaire_resp_single', false);
    unset($qtable);

    // Upgrade the questionnaire_question_type table to use typeid.
    $table = new xmldb_table('questionnaire_question_type');
    $field = new xmldb_field('typeid');
    $field->set_attributes(XMLDB_TYPE_CHAR, '20', true, true, false, false, null, '0', 'id');
    $dbman->add_field($table, $field);
    if (($numrecs = $dbman->count_records('questionnaire_question_type')) > 0) {
        $recstart = 0;
        $recstoget = 100;
        while ($recstart < $numrecs) {
            if ($records = $dbman->get_records('questionnaire_question_type', array(), '', '*', $recstart, $recstoget)) {
                foreach ($records as $record) {
                    $dbman->set_field('questionnaire_question_type', 'typeid', $record->id, array('id' => $record->id));
                }
            }
            $recstart += $recstoget;
        }
    }

    return $status;
}
