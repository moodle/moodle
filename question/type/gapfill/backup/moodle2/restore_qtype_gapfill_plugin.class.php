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
 * Gapfill question type restoration
 *
 * @package    qtype_gapfill
 * @subpackage backup-moodle2
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Information to restore a backup of a gapfill question
 *
 * Also used if you click the duplicate quiz button in a course.
 *
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_gapfill_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'gapfill';
        // We use get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/gapfill');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'gapsetting';
        $elepath = $this->get_pathfor('/gapsettings/gapsetting');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

     /**
      * Process the qtype/gapfill element
      *
      * @param array $data
      */
    public function process_gapfill($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid = $this->get_old_parentid('question');
        $newquestionid = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_gapfill too.
        if ($questioncreated) {
            // Adjust value to link back to the questions table.
            $data->question = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('question_gapfill', $data);
            // Create mapping (needed for decoding links).
            $this->set_mapping('question_gapfill', $oldid, $newitemid);
        }
    }

     /**
      * Process the settings for individual gaps
      *
      * @param array $data
      */
    public function process_gapsetting($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid = $this->get_old_parentid('question');
        $newquestionid = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_gapfill too.
        if ($questioncreated) {
            // Adjust value to link back to the questions table.
            $data->question = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('question_gapfill_settings', $data);
            // Create mapping (needed for decoding links).
            $this->set_mapping('question_gapfill_settings', $oldid, $newitemid);
        }

    }
    /**
     * Return the contents of this qtype to be processed by the links decoder
     */
    public static function define_decode_contents() {

        $contents = array();

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        $contents[] = new restore_decode_content('question_gapfill', $fields, 'question_gapfill');

        return $contents;
    }

    /**
     * Processes the answer element (question answers).  This has been copied in from
     * the parent restore_qtype class to allow the creation of duplicate
     * answers. These are a significant feature of this question type, see the
     * no duplicates feature in the documentation at
     * http://docs.moodle.org/en//question/type/gapfill#No_Duplicates_Mode
     *  Previously it was throwing a debug error. This has been 'fixed' by
     * the addition of the IGNORE_MULTIPLE parameter to the call to get_field_sql.
     * However the docs seem to frown on the use of this parameter.
     *
     * @param array $data
     * @throws restore_step_exception
     */
    public function process_question_answer($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid = $this->get_old_parentid('question');
        $newquestionid = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // In the past, there were some sloppily rounded fractions around. Fix them up.
        $changes = array(
            '-0.66666' => '-0.6666667',
            '-0.33333' => '-0.3333333',
            '-0.16666' => '-0.1666667',
            '-0.142857' => '-0.1428571',
            '0.11111' => '0.1111111',
            '0.142857' => '0.1428571',
            '0.16666' => '0.1666667',
            '0.33333' => '0.3333333',
            '0.333333' => '0.3333333',
            '0.66666' => '0.6666667',
        );
        if (array_key_exists($data->fraction, $changes)) {
            $data->fraction = $changes[$data->fraction];
        }

        // If the question has been created by restore, we need to create its question_answers too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            $data->answer = $data->answertext;
            // Insert record.
            $newitemid = $DB->insert_record('question_answers', $data);

            // The question existed, we need to map the existing question_answers.
        } else {
            // Look in question_answers by answertext matching.
            $sql = 'SELECT id
                      FROM {question_answers}
                     WHERE question = ?
                       AND ' . $DB->sql_compare_text('answer', 255) . ' = ' . $DB->sql_compare_text('?', 255);
            $params = array($newquestionid, $data->answertext);
            $newitemid = $DB->get_field_sql($sql, $params, IGNORE_MULTIPLE);

            // Not able to find the answer, let's try cleaning the answertext.
            // of all the question answers in DB as slower fallback. MDL-30018.
            if (!$newitemid) {
                $params = array('question' => $newquestionid);
                $answers = $DB->get_records('question_answers', $params, '', 'id, answer');
                foreach ($answers as $answer) {
                    $clean = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is', '', $answer->answer); // Clean CTRL chars.
                    $clean = preg_replace("/\r\n|\r/", "\n", $clean); // Normalize line ending.
                    if ($clean === $data->answertext) {
                        $newitemid = $data->id;
                    }
                }
            }

            /*If we haven't found the newitemid, something has gone really wrong, question in DB
             is missing answers, exception*/
            if (!$newitemid) {
                $info = new stdClass();
                $info->filequestionid = $oldquestionid;
                $info->dbquestionid = $newquestionid;
                $info->answer = $data->answertext;
                throw new restore_step_exception('error_question_answers_missing_in_db', $info);
            }
        }
        /* Create mapping (we'll use this intensively when restoring question_states. And also answerfeedback files) */
        $this->set_mapping('question_answer', $oldid, $newitemid);
    }

}
