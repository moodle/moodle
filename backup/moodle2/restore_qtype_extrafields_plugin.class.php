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
 * Defines restore_qtype_extrafields_plugin class
 *
 * @package    core_backup
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class extending restore_qtype_plugin in order to use extra fields method
 *
 * See qtype_shortanswer for an example
 *
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_extrafields_plugin extends restore_qtype_plugin {

    /**
     * Question type class for a particular question type
     * @var question_type
     */
    protected $qtypeobj;

    /**
     * Constructor
     *
     * @param string $plugintype plugin type
     * @param string $pluginname plugin name
     * @param restore_step $step step
     */
    public function __construct($plugintype, $pluginname, $step) {
        parent::__construct($plugintype, $pluginname, $step);
        $this->qtypeobj = question_bank::get_qtype($this->pluginname);
    }

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {
        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elepath = $this->get_pathfor('/' . $this->qtypeobj->name());
        $paths[] = new restore_path_element($this->qtypeobj->name(), $elepath);

        $elepath = $this->get_pathfor('/answers/answer/extraanswerdata');
        $paths[] = new restore_path_element('extraanswerdata', $elepath);

        return $paths;
    }

    /**
     * Processes the extra answer data
     *
     * @param array $data extra answer data
     */
    public function process_extraanswerdata($data) {
        global $DB;

        $extra = $this->qtypeobj->extra_answer_fields();
        $tablename = array_shift($extra);

        $oldquestionid = $this->get_old_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        if ($questioncreated) {
            $data['answerid'] = $this->get_mappingid('question_answer', $data['id']);
            $DB->insert_record($tablename, $data);
        } else {
            $DB->update_record($tablename, $data);
        }
    }

    /**
     * Process the qtype/... element.
     *
     * @param array $data question data
     */
    public function really_process_extra_question_fields($data) {
        global $DB;

        $oldid = $data['id'];

        // Detect if the question is created or mapped.
        $oldquestionid = $this->get_old_parentid('question');
        $newquestionid = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_... too.
        if ($questioncreated) {
            $extraquestionfields = $this->qtypeobj->extra_question_fields();
            $tablename = array_shift($extraquestionfields);

            // Adjust some columns.
            $qtfield = $this->qtypeobj->questionid_column_name();
            $data[$qtfield] = $newquestionid;

            // Insert record.
            $newitemid = $DB->insert_record($tablename, $data);

            // Create mapping.
            $this->set_mapping($tablename, $oldid, $newitemid);
        }
    }
}
