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
 * Defines restore_qtype_plugin class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class extending standard restore_plugin in order to implement some
 * helper methods related with the questions (qtype plugin)
 *
 * TODO: Finish phpdocs
 */
abstract class restore_qtype_plugin extends restore_plugin {

    /**
     * Add to $paths the restore_path_elements needed
     * to handle question_answers for a given question
     * Used by various qtypes (calculated, essay, multianswer,
     * multichoice, numerical, shortanswer, truefalse)
     */
    protected function add_question_question_answers(&$paths) {
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }

        $elename = 'question_answer';
        $elepath = $this->get_pathfor('/answers/answer'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);
    }

    /**
     * Add to $paths the restore_path_elements needed
     * to handle question_numerical_units for a given question
     * Used by various qtypes (calculated, numerical)
     */
    protected function add_question_numerical_units(&$paths) {
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }

        $elename = 'question_numerical_unit';
        $elepath = $this->get_pathfor('/numerical_units/numerical_unit'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);
    }

    /**
     * Add to $paths the restore_path_elements needed
     * to handle question_numerical_options for a given question
     * Used by various qtypes (calculated, numerical)
     */
    protected function add_question_numerical_options(&$paths) {
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }

        $elename = 'question_numerical_option';
        $elepath = $this->get_pathfor('/numerical_options/numerical_option'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);
    }

    /**
     * Add to $paths the restore_path_elements needed
     * to handle question_datasets (defs and items) for a given question
     * Used by various qtypes (calculated, numerical)
     */
    protected function add_question_datasets(&$paths) {
        // Check $paths is one array
        if (!is_array($paths)) {
            throw new restore_step_exception('paths_must_be_array', $paths);
        }

        $elename = 'question_dataset_definition';
        $elepath = $this->get_pathfor('/dataset_definitions/dataset_definition'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'question_dataset_item';
        $elepath = $this->get_pathfor('/dataset_definitions/dataset_definition/dataset_items/dataset_item');
        $paths[] = new restore_path_element($elename, $elepath);
    }

    /**
     * Processes the answer element (question answers). Common for various qtypes.
     * It handles both creation (if the question is being created) and mapping
     * (if the question already existed and is being reused)
     */
    public function process_question_answer($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // In the past, there were some sloppily rounded fractions around. Fix them up.
        $changes = array(
            '-0.66666'  => '-0.6666667',
            '-0.33333'  => '-0.3333333',
            '-0.16666'  => '-0.1666667',
            '-0.142857' => '-0.1428571',
             '0.11111'  =>  '0.1111111',
             '0.142857' =>  '0.1428571',
             '0.16666'  =>  '0.1666667',
             '0.33333'  =>  '0.3333333',
             '0.333333' =>  '0.3333333',
             '0.66666'  =>  '0.6666667',
        );
        if (array_key_exists($data->fraction, $changes)) {
            $data->fraction = $changes[$data->fraction];
        }

        // If the question has been created by restore, we need to create its question_answers too
        if ($questioncreated) {
            // Adjust some columns
            $data->question = $newquestionid;
            $data->answer = $data->answertext;
            // Insert record
            $newitemid = $DB->insert_record('question_answers', $data);

        // The question existed, we need to map the existing question_answers
        } else {
            // Look in question_answers by answertext matching
            $sql = 'SELECT id
                      FROM {question_answers}
                     WHERE question = ?
                       AND ' . $DB->sql_compare_text('answer', 255) . ' = ' . $DB->sql_compare_text('?', 255);
            $params = array($newquestionid, $data->answertext);
            $newitemid = $DB->get_field_sql($sql, $params);

            // Not able to find the answer, let's try cleaning the answertext
            // of all the question answers in DB as slower fallback. MDL-30018.
            if (!$newitemid) {
                $params = array('question' => $newquestionid);
                $answers = $DB->get_records('question_answers', $params, '', 'id, answer');
                foreach ($answers as $answer) {
                    // Clean in the same way than {@link xml_writer::xml_safe_utf8()}.
                    $clean = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is','', $answer->answer); // Clean CTRL chars.
                    $clean = preg_replace("/\r\n|\r/", "\n", $clean); // Normalize line ending.
                    if ($clean === $data->answertext) {
                        $newitemid = $data->id;
                    }
                }
            }

            // If we haven't found the newitemid, something has gone really wrong, question in DB
            // is missing answers, exception
            if (!$newitemid) {
                $info = new stdClass();
                $info->filequestionid = $oldquestionid;
                $info->dbquestionid   = $newquestionid;
                $info->answer         = $data->answertext;
                throw new restore_step_exception('error_question_answers_missing_in_db', $info);
            }
        }
        // Create mapping (we'll use this intensively when restoring question_states. And also answerfeedback files)
        $this->set_mapping('question_answer', $oldid, $newitemid);
    }

    /**
     * Processes the numerical_unit element (question numerical units). Common for various qtypes.
     * It handles both creation (if the question is being created) and mapping
     * (if the question already existed and is being reused)
     */
    public function process_question_numerical_unit($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_numerical_units too
        if ($questioncreated) {
            // Adjust some columns
            $data->question = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_numerical_units', $data);
        }
    }

    /**
     * Processes the numerical_option element (question numerical options). Common for various qtypes.
     * It handles both creation (if the question is being created) and mapping
     * (if the question already existed and is being reused)
     */
    public function process_question_numerical_option($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_numerical_options too
        if ($questioncreated) {
            // Adjust some columns
            $data->question = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_numerical_options', $data);
            // Create mapping (not needed, no files nor childs nor states here)
            //$this->set_mapping('question_numerical_option', $oldid, $newitemid);
        }
    }

    /**
     * Processes the dataset_definition element (question dataset definitions). Common for various qtypes.
     * It handles both creation (if the question is being created) and mapping
     * (if the question already existed and is being reused)
     */
    public function process_question_dataset_definition($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question is mapped, nothing to do
        if (!$questioncreated) {
            return;
        }

        // Arrived here, let's see if the question_dataset_definition already exists in category or no
        // (by category, name, type and enough items). Only for "shared" definitions (category != 0).
        // If exists, reuse it, else, create it as "not shared" (category = 0)
        $data->category = $this->get_mappingid('question_category', $data->category);
        // If category is shared, look for definitions
        $founddefid = null;
        if ($data->category) {
            $candidatedefs = $DB->get_records_sql("SELECT id, itemcount
                                                     FROM {question_dataset_definitions}
                                                    WHERE category = ?
                                                      AND name = ?
                                                      AND type = ?", array($data->category, $data->name, $data->type));
            foreach ($candidatedefs as $candidatedef) {
                if ($candidatedef->itemcount >= $data->itemcount) { // Check it has enough items
                    $founddefid = $candidatedef->id;
                    break; // end loop, shared definition match found
                }
            }
            // If there were candidates but none fulfilled the itemcount condition, create definition as not shared
            if ($candidatedefs && !$founddefid) {
                $data->category = 0;
            }
        }
        // If haven't found any shared definition match, let's create it
        if (!$founddefid) {
            $newitemid = $DB->insert_record('question_dataset_definitions', $data);
            // Set mapping, so dataset items will know if they must be created
            $this->set_mapping('question_dataset_definition', $oldid, $newitemid);

        // If we have found one shared definition match, use it
        } else {
            $newitemid = $founddefid;
            // Set mapping to 0, so dataset items will know they don't need to be created
            $this->set_mapping('question_dataset_definition', $oldid, 0);
        }

        // Arrived here, we have one $newitemid (create or reused). Create the question_datasets record
        $questiondataset = new stdClass();
        $questiondataset->question = $newquestionid;
        $questiondataset->datasetdefinition = $newitemid;
        $DB->insert_record('question_datasets', $questiondataset);
    }

    /**
     * Processes the dataset_item element (question dataset items). Common for various qtypes.
     * It handles both creation (if the question is being created) and mapping
     * (if the question already existed and is being reused)
     */
    public function process_question_dataset_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question is mapped, nothing to do
        if (!$questioncreated) {
            return;
        }

        // Detect if the question_dataset_definition is being created
        $newdefinitionid = $this->get_new_parentid('question_dataset_definition');

        // If the definition is reused, nothing to do
        if (!$newdefinitionid) {
            return;
        }

        // let's create the question_dataset_items
        $data->definition = $newdefinitionid;
        $data->itemnumber = $data->number;
        $DB->insert_record('question_dataset_items', $data);
    }

    /**
     * Do any re-coding necessary in the student response.
     * @param int $questionid the new id of the question
     * @param int $sequencenumber of the step within the qusetion attempt.
     * @param array the response data from the backup.
     * @return array the recoded response.
     */
    public function recode_response($questionid, $sequencenumber, array $response) {
        return $response;
    }

    /**
     * Decode legacy question_states.answer for this qtype. Used when restoring
     * 2.0 attempt data.
     */
    public function recode_legacy_state_answer($state) {
        // By default, return answer unmodified, qtypes needing recode will override this
        return $state->answer;
    }

    /**
     * Return the contents of the questions stuff that must be processed by the links decoder
     *
     * Only common stuff to all plugins, in this case:
     * - question: text and feedback
     * - question_answers: text and feedbak
     *
     * Note each qtype will have, if needed, its own define_decode_contents method
     */
    static public function define_plugin_decode_contents() {

        $contents = array();

        $contents[] = new restore_decode_content('question', array('questiontext', 'generalfeedback'), 'question_created');
        $contents[] = new restore_decode_content('question_answers', array('answer', 'feedback'), 'question_answer');

        return $contents;
    }
}
