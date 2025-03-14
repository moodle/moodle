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
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * restore plugin class that provides the necessary information
 * needed to restore one truefalse qtype plugin
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_truefalse_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'truefalse';
        $elepath = $this->get_pathfor('/truefalse'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/truefalse element
     */
    public function process_truefalse($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_truefalse too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            $data->trueanswer = $this->get_mappingid('question_answer', $data->trueanswer);
            $data->falseanswer = $this->get_mappingid('question_answer', $data->falseanswer);
            // Insert record.
            $newitemid = $DB->insert_record('question_truefalse', $data);
            // Create mapping.
            $this->set_mapping('question_truefalse', $oldid, $newitemid);
        }
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for truefalse questions
     *
     * if not empty, answer is one question_answers->id
     */
    public function recode_legacy_state_answer($state) {
        $answer = $state->answer;
        $result = '';
        if ($answer) {
            $result = $this->get_mappingid('question_answer', $answer);
        }
        return $result;
    }

    #[\Override]
    public function define_excluded_identity_hash_fields(): array {
        return [
            '/options/trueanswer',
            '/options/falseanswer',
            '/options/question',
        ];
    }
}
