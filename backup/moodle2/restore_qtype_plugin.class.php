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

    /*
     * A simple answer to id cache for a single questions answers.
     * @var array
     */
    private $questionanswercache = array();

    /*
     * The id of the current question in the questionanswercache.
     * @var int
     */
    private $questionanswercacheid = null;

    /**
     * @var array List of fields to exclude form hashing during restore.
     */
    protected array $excludedhashfields = [];

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
        $this->exclude_identity_hash_fields([
            '/options/answers/id',
            '/options/answers/question',
        ]);
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
        $this->exclude_identity_hash_fields([
            '/options/units/id',
            '/options/units/question',
        ]);
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
        $this->exclude_identity_hash_fields(['/options/question']);
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
        $this->exclude_identity_hash_fields([
            '/options/datasets/id',
            '/options/datasets/question',
            '/options/datasets/category',
            '/options/datasets/type',
            '/options/datasets/items/id',
            // The following fields aren't included in the backup or DB structure, but are parsed from the options field.
            '/options/datasets/status',
            '/options/datasets/distribution',
            '/options/datasets/minimum',
            '/options/datasets/maximum',
            '/options/datasets/decimals',
            // This field is set dynamically from the count of items in the dataset, it is not backed up.
            '/options/datasets/number_of_items',
        ]);
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
            // Have we cached the current question?
            if ($this->questionanswercacheid !== $newquestionid) {
                // The question changed, purge and start again!
                $this->questionanswercache = array();
                $params = array('question' => $newquestionid);
                $answers = $DB->get_records('question_answers', $params, '', 'id, answer');
                $this->questionanswercacheid = $newquestionid;
                // Cache all cleaned answers for a simple text match.
                foreach ($answers as $answer) {
                    $clean = core_text::trim_ctrl_chars($answer->answer); // Clean CTRL chars.
                    $clean = preg_replace("/\r\n|\r/", "\n", $clean); // Normalize line ending.
                    $this->questionanswercache[$clean] = $answer->id;
                }
            }

            $rules = restore_course_task::define_decode_rules();
            $rulesactivity = restore_quiz_activity_task::define_decode_rules();
            $rules = array_merge($rules, $rulesactivity);

            $decoder = $this->task->get_decoder();
            foreach ($rules as $rule) {
                $decoder->add_rule($rule);
            }

            $contentdecoded = $decoder->decode_content($data->answertext);
            if ($contentdecoded) {
                $data->answertext = $contentdecoded;
            }

            if (!isset($this->questionanswercache[$data->answertext])) {
                // If we haven't found the matching answer, something has gone really wrong, the question in the DB
                // is missing answers, throw an exception.
                $info = new stdClass();
                $info->filequestionid = $oldquestionid;
                $info->dbquestionid   = $newquestionid;
                $info->answer         = s($data->answertext);
                throw new restore_step_exception('error_question_answers_missing_in_db', $info);
            }
            $newitemid = $this->questionanswercache[$data->answertext];
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
     * - question_answers: text and feedback
     * - question_hints: hint
     *
     * Note each qtype will have, if needed, its own define_decode_contents method
     */
    public static function define_plugin_decode_contents() {

        $contents = array();

        $contents[] = new restore_decode_content('question', ['questiontext', 'generalfeedback'], 'question_created');
        $contents[] = new restore_decode_content('question_answers', ['answer', 'feedback'], 'question_answer');
        $contents[] = new restore_decode_content('question_hints', ['hint'], 'question_hint');

        return $contents;
    }

    /**
     * Add fields to the list of fields excluded from hashing.
     *
     * This allows common methods to add fields to the exclusion list.
     *
     * @param array $fields
     * @return void
     */
    private function exclude_identity_hash_fields(array $fields): void {
        $this->excludedhashfields = array_merge($this->excludedhashfields, $fields);
    }

    /**
     * Return fields to be excluded from hashing during restores.
     *
     * @return array
     */
    final public function get_excluded_identity_hash_fields(): array {
        return array_unique(array_merge(
            $this->excludedhashfields,
            $this->define_excluded_identity_hash_fields(),
        ));
    }

    /**
     * Return a list of paths to fields to be removed from questiondata before creating an identity hash.
     *
     * Fields that should be excluded from common elements such as answers or numerical units that are used by the plugin will
     * be excluded automatically. This method just needs to define any specific to this plugin, such as foreign keys used in the
     * plugin's tables.
     *
     * The returned array should be a list of slash-delimited paths to locate the fields to be removed from the questiondata object.
     * For example, if you want to remove the field `$questiondata->options->questionid`, the path would be '/options/questionid'.
     * If a field in the path is an array, the rest of the path will be applied to each object in the array. So if you have
     * `$questiondata->options->answers[]`, the path '/options/answers/id' will remove the 'id' field from each element of the
     * 'answers' array.
     *
     * @return array
     */
    protected function define_excluded_identity_hash_fields(): array {
        return [];
    }

    /**
     * Convert the backup structure of this question type into a structure matching its question data
     *
     * This should take the hierarchical array of tags from the question's backup structure, and return a structure that matches
     * that returned when calling {@see get_question_options()} for this question type.
     * See https://docs.moodle.org/dev/Question_data_structures#Representation_1:_%24questiondata for an explanation of this
     * structure.
     *
     * This data will then be used to produce an identity hash for comparison with questions in the database.
     *
     * This base implementation deals with all common backup elements created by the add_question_*_options() methods in this class,
     * plus elements added by ::define_question_plugin_structure() named for the qtype. The question type will need to extend
     * this function if ::define_question_plugin_structure() adds any other elements to the backup.
     *
     * @param array $backupdata The hierarchical array of tags from the backup.
     * @return \stdClass The questiondata object.
     */
    public static function convert_backup_to_questiondata(array $backupdata): \stdClass {
        // Create an object from the top-level fields.
        $questiondata = (object) array_filter($backupdata, fn($tag) => !is_array($tag));
        $qtype = $questiondata->qtype;
        $questiondata->options = new stdClass();
        if (isset($backupdata["plugin_qtype_{$qtype}_question"][$qtype])) {
            $questiondata->options = (object) $backupdata["plugin_qtype_{$qtype}_question"][$qtype][0];
        }
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['answers'])) {
            $questiondata->options->answers = array_map(
                fn($answer) => (object) $answer,
                $backupdata["plugin_qtype_{$qtype}_question"]['answers']['answer'],
            );
        }
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['numerical_options'])) {
            $questiondata->options = (object) array_merge(
                (array) $questiondata->options,
                $backupdata["plugin_qtype_{$qtype}_question"]['numerical_options']['numerical_option'][0],
            );
        }
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['numerical_units'])) {
            $questiondata->options->units = array_map(
                fn($unit) => (object) $unit,
                $backupdata["plugin_qtype_{$qtype}_question"]['numerical_units']['numerical_unit'],
            );
        }
        if (isset($backupdata["plugin_qtype_{$qtype}_question"]['dataset_definitions'])) {
            $questiondata->options->datasets = array_map(
                fn($dataset) => (object) $dataset,
                $backupdata["plugin_qtype_{$qtype}_question"]['dataset_definitions']['dataset_definition'],
            );
        }
        if (isset($questiondata->options->datasets)) {
            foreach ($questiondata->options->datasets as $dataset) {
                if (isset($dataset->dataset_items)) {
                    $dataset->items = array_map(
                        fn($item) => (object) $item,
                        $dataset->dataset_items['dataset_item'],
                    );
                    unset($dataset->dataset_items);
                }
            }
        }
        if (isset($backupdata['question_hints'])) {
            $questiondata->hints = array_map(
                fn($hint) => (object) $hint,
                $backupdata['question_hints']['question_hint'],
            );
        }

        return $questiondata;
    }

    /**
     * Remove excluded fields from the questiondata structure.
     *
     * This removes fields that will not match or not be present in the question data structure produced by
     * {@see self::convert_backup_to_questiondata()} and {@see get_question_options()} (such as IDs), so that the remaining data can
     * be used to produce an identity hash for comparing the two.
     *
     * For plugins, it should be sufficient to override {@see self::define_excluded_identity_hash_fields()} with a list of paths
     * specific to the plugin type. Overriding this method is only necessary if the plugin's
     * {@see question_type::get_question_options()} method adds additional data to the question that is not included in the backup.
     *
     * @param stdClass $questiondata
     * @param array $excludefields Paths to the fields to exclude.
     * @return stdClass The $questiondata with excluded fields removed.
     */
    public static function remove_excluded_question_data(stdClass $questiondata, array $excludefields = []): stdClass {
        // All questions will need to exclude 'id' (used by question and other tables), 'questionid' (used by hints and options),
        // 'createdby' and 'modifiedby' (since they won't map between sites).
        $defaultexcludes = [
            '/id',
            '/createdby',
            '/modifiedby',
            '/hints/id',
            '/hints/questionid',
            '/options/id',
            '/options/questionid',
        ];
        $excludefields = array_unique(array_merge($excludefields, $defaultexcludes));

        foreach ($excludefields as $excludefield) {
            $pathparts = explode('/', ltrim($excludefield, '/'));
            $questiondata = self::unset_excluded_fields($questiondata, $pathparts);
        }

        return $questiondata;
    }

    /**
     * Iterate through the elements of path to an excluded field, and unset the final element.
     *
     * If any of the elements in the path is an array, this is called recursively on each element in the array to unset fields
     * in each child of the array.
     *
     * @param stdClass|array $data The questiondata structure, or a subsection of it.
     * @param array $pathparts The remaining elements in the path to the excluded field.
     * @return stdClass|array The $data structure with excluded fields removed.
     */
    private static function unset_excluded_fields(stdClass|array $data, array $pathparts): stdClass|array {
        $element = array_shift($pathparts);
        $unset = false;
        // Get the current element from the data structure.
        if (is_object($data)) {
            if (!property_exists($data, $element)) {
                // This element is not present in the data structure, nothing to unset.
                return $data;
            }
            $dataelement = $data->{$element};
        } else { // It's an array.
            if (!array_key_exists($element, $data)) {
                return $data;
            }
            $dataelement = $data[$element];
        }
        // Check if we need to recur, or unset this element.
        if (is_object($dataelement)) {
            $dataelement = self::unset_excluded_fields($dataelement, $pathparts);
        } else if (is_array($dataelement)) {
            foreach ($dataelement as $key => $item) {
                if (is_object($item) || is_array($item)) {
                    // This is an array of objects or arrays, recur.
                    $dataelement[$key] = self::unset_excluded_fields($item, $pathparts);
                } else {
                    // This is an associative array of values, check if they should be removed.
                    $subelement = reset($pathparts);
                    if ($key == $subelement) {
                        unset($dataelement[$key]);
                    }
                }
            }
        } else if (empty($pathparts)) {
            // This is the last element of the path and it's a scalar value, unset it.
            $unset = true;
        }
        // Write the modified element back to the data structure, or unset it.
        if (is_object($data)) {
            if ($unset) {
                unset($data->{$element});
            } else {
                $data->{$element} = $dataelement;
            }
        } else {
            if ($unset) {
                unset($data[$element]);
            } else {
                $data[$element] = $dataelement;
            }
        }
        return $data;
    }
}
