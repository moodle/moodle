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
 * Ordering question type restore handler
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restore plugin class that provides the necessary information needed to restore one ordering qtype plugin
 *
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_ordering_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'ordering';
        $elepath = $this->get_pathfor('/ordering'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/ordering element
     *
     * @param array $data
     */
    public function process_ordering($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        // "question" is the XML tag name, not the DB field name.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');

        // If the question has been created by restore,
        // we need to create a "qtype_ordering_options" record
        // and create a mapping from the $oldid to the $newid.
        if ($this->get_mappingid('question_created', $oldquestionid)) {
            $data->questionid = $newquestionid;
            $newid = $DB->insert_record('qtype_ordering_options', $data);
            $this->set_mapping('qtype_ordering_options', $oldid, $newid);
        }
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for ordering questions.
     * If not empty, answer is one question_answers->id.
     *
     * @param object $state
     */
    public function recode_legacy_state_answer($state) {
        $answer = $state->answer;
        $result = '';
        if ($answer) {
            $result = $this->get_mappingid('question_answer', $answer);
        }
        return $result;
    }
}
