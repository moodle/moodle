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

require_once($CFG->dirroot . '/question/type/opaque/locallib.php');


/**
 * restore plugin class that provides the necessary information
 * needed to restore one ddwtos qtype plugin
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_opaque_plugin extends restore_qtype_plugin {
    protected $enginemanager = null;

    /* Overridden. See parent class for docs. */
    protected function define_question_plugin_structure() {
        return array(
            new restore_path_element('opaque', $this->get_pathfor('/opaque'), true),
            new restore_path_element('engine', $this->get_pathfor('/opaque/engine')),
            new restore_path_element('server', $this->get_pathfor('/opaque/engine/server')),
        );
    }

    /**
     * Process the qtype/opaque element
     */
    public function process_opaque($data) {
        global $DB;

        $engine = (object) $data['engine'][0];
        $engine->questionengines = array();
        $engine->questionbanks = array();

        foreach ($data['engine'][0]['server'] as $server) {
            if ($server['type'] == 'qe') {
                $engine->questionengines[] = $server['url'];
            } else if ($server['type'] == 'qb') {
                $engine->questionbanks[] = $server['url'];
            }
        }
        if (empty($engine->questionengines)) {
            throw new coding_exception(
                    'Missing question engine URLs in an Opaque question backup.');
        }

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its question_ddwtos too
        if (!$questioncreated) {
            // New question, insert.
            $question = (object) $data;
            $question->engineid = $this->engine_manager()->find_or_create_engineid($engine);
            $question->questionid = $newquestionid;

            $DB->insert_record('question_opaque', $question);
        }
    }

    /**
     * Process the qtype/opaque/server element
     */
    public function process_engine($data) {
        // Do nothing. All the data is processed in process_opaque.
    }

    /**
     * Process the qtype/opaque/server element
     */
    public function process_server($data) {
        // Do nothing. All the data is processed in process_opaque.
    }

    /**
     * @return qtype_opaque_engine_manager an engine manager.
     */
    protected function engine_manager() {
        if (is_null($this->enginemanager)) {
            $this->enginemanager = new qtype_opaque_engine_manager();
        }
        return $this->enginemanager;
    }
}
