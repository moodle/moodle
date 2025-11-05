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
 *
 * Regexp question type restorationr
 *
 * @package    qtype_regexp
 * @subpackage backup-moodle2
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Information to restore a backup of a regexp question *
 *
 * Also used if you click the duplicate quiz button in a course.
 *
 * @copyright  2017 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_regexp_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = [];

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'regexp';
        $elepath = $this->get_pathfor('/regexp'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/regexp element
     *
     * @param array $data
     */
    public function process_regexp($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_regexp too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->questionid = $newquestionid;
            // Map sequence of question_answer ids.
            $answersarr = explode(',', $data->answers);
            foreach ($answersarr as $key => $answer) {
                // Postgresql does not handle empty strings as integer values.
                if ($answer == '') {
                    $answer = null;
                }
                $answersarr[$key] = $this->get_mappingid('question_answer', $answer);
            }
            $data->answers = implode(',', $answersarr);
            // Insert record.
            // JR changed table name to match new table name system in moodle 2.1 DEC 2011.
            $newitemid = $DB->insert_record('qtype_regexp', $data);
            // Create mapping.
            $this->set_mapping('qtype_regexp', $oldid, $newitemid);
        }
    }
}
