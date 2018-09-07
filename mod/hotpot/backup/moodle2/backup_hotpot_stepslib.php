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
 * Defines all the backup steps that will be used by {@link backup_hotpot_activity_task}
 *
 * @package    mod_hotpot
 * @subpackage backup-moodle2
 * @copyright  2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the complete hotpot structure for backup, with file and id annotations
 *
 * @see http://docs.moodle.org/en/Development:Hotpot for XML structure diagram
 */
class backup_hotpot_activity_structure_step extends backup_activity_structure_step {

    /** maximum number of questions to retrieve in one DB query */
    const GET_QUESTIONS_LIMIT = 100;

    /**
     * define_structure
     *
     * @return xxx
     */
    protected function define_structure()  {

        // are we including userinfo?
        $userinfo = $this->get_setting_value('userinfo');

        ////////////////////////////////////////////////////////////////////////
        // XML nodes declaration - non-user data
        ////////////////////////////////////////////////////////////////////////

        // root element describing hotpot instance
        $fieldnames = $this->get_fieldnames('hotpot', array('id', 'course'));
        $hotpot     = new backup_nested_element('hotpot', array('id'), $fieldnames);

        ////////////////////////////////////////////////////////////////////////
        // XML nodes declaration - user data
        ////////////////////////////////////////////////////////////////////////

        if ($userinfo) {

            // attempts at hotpots
            $attempts   = new backup_nested_element('attempts');
            $fieldnames = $this->get_fieldnames('hotpot_attempts', array('id', 'hotpotid'));
            $attempt    = new backup_nested_element('attempt', array('id'), $fieldnames);

            // questions in hotpots
            $questions  = new backup_nested_element('questions');
            $fieldnames = $this->get_fieldnames('hotpot_questions', array('id', 'hotpotid', 'md5key'));
            $question   = new backup_nested_element('question', array('id'), $fieldnames);

            // responses to questions
            $responses  = new backup_nested_element('responses');
            $fieldnames = $this->get_fieldnames('hotpot_responses', array('id', 'questionid'));
            $response   = new backup_nested_element('response', array('id'), $fieldnames);

             // strings used in questions and responses
            $strings    = new backup_nested_element('strings');
            $fieldnames = $this->get_fieldnames('hotpot_strings', array('id', 'md5key'));
            $string     = new backup_nested_element('string', array('id'), $fieldnames);
        }

        ////////////////////////////////////////////////////////////////////////
        // build the tree in the order needed for restore
        ////////////////////////////////////////////////////////////////////////

        if ($userinfo) {

            // strings
            $hotpot->add_child($strings);
            $strings->add_child($string);

            // attempts
            $hotpot->add_child($attempts);
            $attempts->add_child($attempt);

            // questions
            $hotpot->add_child($questions);
            $questions->add_child($question);

            // responses
            $question->add_child($responses);
            $responses->add_child($response);
        }

        ////////////////////////////////////////////////////////////////////////
        // data sources - non-user data
        ////////////////////////////////////////////////////////////////////////

        $hotpot->set_source_table('hotpot', array('id' => backup::VAR_ACTIVITYID));

        ////////////////////////////////////////////////////////////////////////
        // data sources - user related data
        ////////////////////////////////////////////////////////////////////////

        if ($userinfo) {

            // attempts
            $attempt->set_source_table('hotpot_attempts', array('hotpotid' => backup::VAR_PARENTID));

            // questions
            $question->set_source_table('hotpot_questions', array('hotpotid' => backup::VAR_PARENTID));

            // responses
            $response->set_source_table('hotpot_responses', array('questionid' => backup::VAR_PARENTID));

            // strings
            list($filter, $params) = $this->get_strings_sql();
            $string->set_source_sql("SELECT * FROM {hotpot_strings} WHERE id $filter", $params);
        }

        ////////////////////////////////////////////////////////////////////////
        // id annotations (foreign keys on non-parent tables)
        ////////////////////////////////////////////////////////////////////////

        $hotpot->annotate_ids('course_modules', 'entrycm');
        $hotpot->annotate_ids('course_modules', 'exitcm');

        if ($userinfo) {
            $attempt->annotate_ids('user', 'userid');
            $response->annotate_ids('hotpot_attempts', 'attemptid');
        }

        ////////////////////////////////////////////////////////////////////////
        // file annotations
        ////////////////////////////////////////////////////////////////////////

        $hotpot->annotate_files('mod_hotpot', 'sourcefile', null);
        $hotpot->annotate_files('mod_hotpot', 'entrytext',  null);
        $hotpot->annotate_files('mod_hotpot', 'exittext',   null);

        // return the root element (hotpot), wrapped into standard activity structure
        return $this->prepare_activity_structure($hotpot);
    }

    /**
     * get_fieldnames
     *
     * @param string $tablename the name of the Moodle table (without prefix)
     * @param array $excluded_fieldnames these field names will be excluded
     * @return array of field names
     */
    protected function get_fieldnames($tablename, array $excluded_fieldnames)   {
        global $DB;
        $fieldnames = array_keys($DB->get_columns($tablename));
        return array_diff($fieldnames, $excluded_fieldnames);
    }

    /**
     * get_strings_sql
     *
     * we want all the strings used in responses and questions for the current HotPot
     * - hotpot_questions.text    : a single hotpot_strings.id
     * - hotpot_responses.correct : a comma-separated list of hotpot_strings.id's
     * - hotpot_responses.wrong   : a comma-separated list of hotpot_strings.id's
     * - hotpot_responses.ignored : a comma-separated list of hotpot_strings.id's
     *
     * @return array ($filter, $params) to extract strings used in this HotPot
     */
    protected function get_strings_sql() {
        global $DB;

        // array to store the string ids
        $stringids = array();

        // the response fields that contain string ids
        $stringfields = array('correct', 'wrong', 'ignored');

        // the id of the current hotpot
        $hotpotid = $this->get_setting_value(backup::VAR_ACTIVITYID);

        // get questions in this hotpot
        if ($questions = $DB->get_records('hotpot_questions', array('hotpotid' => $hotpotid), '', 'id, text')) {

            // extract string ids in the "text" field of these questions
            foreach ($questions as $question) {
                if ($id = intval(trim($question->text))) {
                    $stringids[$id] = true;
                }
            }

            $questions = array_keys($questions);
            while (($questionids = array_splice($questions, 0, self::GET_QUESTIONS_LIMIT)) && count($questionids)) {

                // get the responses to these questions
                list($filter, $params) = $DB->get_in_or_equal($questionids);
                if ($responses = $DB->get_records_select('hotpot_responses', "questionid $filter", $params)) {

                    // extract string ids from the string fields of these responses
                    foreach ($responses as $response) {
                        foreach ($stringfields as $stringfield) {
                            $ids = explode(',', trim($response->$stringfield));
                            foreach ($ids as $id) {
                                if ($id = intval($id)) {
                                    $stringids[$id] = true;
                                }
                            }
                        }
                    }
                } // end if $responses
            }

        } // end if $questions

        // get the distinct string ids
        $stringids = array_keys($stringids);

        switch (count($stringids)) {
            case 0:  $filter = '< 0'; break;
            case 1:  $filter = '='.$stringids[0]; break;
            default: $filter = 'IN ('.implode(',', $stringids).')';
        }

        // Note: we don't put the ids into $params like this
        // - return $DB->get_in_or_equal($stringids);
        // because Moodle 2.0 backup expects only backup::VAR_xxx
        // constants, which are all negative, in $params, and will
        // throw an exception for any positive values in $params
        // - baseelementincorrectfinalorattribute
        //   backup/util/structure/base_final_element.class.php
        return array($filter, array());
    }
}
