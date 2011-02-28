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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * restore plugin class that provides the necessary information
 * needed to restore one ddwtos qtype plugin
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_ddwtos_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them
        $this->add_question_question_answers($paths);

        // Add own qtype stuff
        $elename = 'ddwtos';
        $elepath = $this->get_pathfor('/ddwtos'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);


        return $paths; // And we return the interesting paths
    }

    /**
     * Process the qtype/ddwtos element
     */
    public function process_ddwtos($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_ddwtos too
        if ($questioncreated) {
            // Adjust some columns
            $data->questionid = $newquestionid;
            // Insert record
            $newitemid = $DB->insert_record('question_ddwtos', $data);
            // Create mapping (needed for decoding links)
            $this->set_mapping('question_ddwtos', $oldid, $newitemid);
        } else {
            // Nothing to remap if the question already existed
        }
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for ddwtos questions
     *
     * answer are two (hypen speparated) lists of comma separated question_answers
     * the first to specify the order of the answers and the second to specify the
     * responses. Note the order list (the first one) can be optional
     */
    public function recode_state_answer($state) {
        $answer = $state->answer;
        $orderarr = array();
        $responsesarr = array();
        $lists = explode(':', $answer);
        // if only 1 list, answer is missing the order list, adjust
        if (count($lists) == 1) {
            $lists[1] = $lists[0]; // here we have the responses
            $lists[0] = '';        // here we have the order
        }
        // Map order
        foreach (explode(',', $lists[0]) as $id) {
            if (!empty($id) && $newid = $this->get_mappingid('question_answer', $id)) {
                $orderarr[] = $newid;
            }
        }
        // Map responses
        foreach (explode(',', $lists[1]) as $id) {
            if (!empty($id) && $newid = $this->get_mappingid('question_answer', $id)) {
                $responsesarr[] = $newid;
            }
        }
        // Build the final answer, if not order, only responses
        $result = '';
        if (empty($orderarr)) {
            $result = implode(',', $responsesarr);
        } else {
            $result = implode(',', $orderarr) . ':' . implode(',', $responsesarr);
        }
        return $result;
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder
     */
    static public function define_decode_contents() {

        $contents = array();

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        $contents[] = new restore_decode_content('question_ddwtos', $fields, 'question_ddwtos');

        return $contents;
    }
}
