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
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get required qtype class
require_once($CFG->dirroot.'/question/type/essayautograde/questiontype.php');

/**
 * restore plugin class that provides the necessary information
 * needed to restore one essayautograde qtype plugin
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_essayautograde_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {
        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $name = 'essayautograde';
        $path = $this->get_pathfor('/essayautograde');
        $paths[] = new restore_path_element($name, $path);

        return $paths;
    }

    /**
     * Process the qtype/essayautograde element
     */
    public function process_essayautograde($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        if (! isset($data->responsetemplate)) {
            $data->responsetemplate = '';
        }
        if (! isset($data->responsetemplateformat)) {
            $data->responsetemplateformat = FORMAT_HTML;
        }
        if (! isset($data->responsesample)) {
            $data->responsesample = '';
        }
        if (! isset($data->responsesampleformat)) {
            $data->responsesampleformat = FORMAT_HTML;
        }
        if (! isset($data->responserequired)) {
            $data->responserequired = 1;
        }
        if (! isset($data->attachmentsrequired)) {
            $data->attachmentsrequired = 0;
        }

        // Detect if the question is created or mapped
        // "question" is the XML tag name, not the DB field name.
        $oldquestionid = $this->get_old_parentid('question');
        $newquestionid = $this->get_new_parentid('question');

        // If the question has been created by restore,
        // we need to create a "qtype_essayautograde_options" record
        // and create a mapping from the $oldid to the $newid.
        if ($this->get_mappingid('question_created', $oldquestionid)) {
            $data->questionid = $newquestionid;
            $newid = $DB->insert_record('qtype_essayautograde_options', $data);
            $this->set_mapping('qtype_essayautograde_options', $oldid, $newid);
        }
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder
     *
     * @return array
     */
    public static function define_decode_contents() {
        $fields = array('graderinfo',
                        'responsetemplate',
                        'responsesample',
                        'correctfeedback',
                        'incorrectfeedback',
                        'partiallycorrectfeedback');
        return array(
            new restore_decode_content('qtype_essayautograde_options', $fields, 'qtype_essayautograde')
        );
    }
    /**
     * When restoring old data, that does not have the essayautograde options information
     * in the XML, supply defaults.
     */
    protected function after_execute_question() {
        global $DB;

        // select all Essay (auto-grade) questions that have no options
        $sql = 'SELECT 1 FROM {qtype_essayautograde_options} options WHERE options.questionid = q.id';
        $sql = 'SELECT * FROM {question} q WHERE q.qtype = ?'." AND NOT EXISTS ($sql)";
        $questions = $DB->get_records_sql($sql, array('essayautograde'));

        foreach ($questions as $q) {
            $options = qtype_essayautograde::get_default_values($q->id, true);
            $DB->insert_record('qtype_essayautograde_options', $options);
        }
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for essayautograde questions.
     * If not empty, answer is one question_answers->id.
     *
     * @param object $state
     */
    public function recode_legacy_state_answer($state) {
        if (empty($state->answer)) {
            return '';
        }
        return $this->get_mappingid('question_answer', $state->answer);
    }

    /**
     * This function, executed after all the tasks in the plan
     * have been executed, will perform the recode of the
     * target activity ids for this block.
     * This must be done here and not in normal execution steps
     * because the activities can be restored after the block.
     */
    public function after_restore_question() {
        global $DB;
        $restoreid = $this->get_restoreid();
        $rs = $DB->get_recordset_sql('SELECT options.id, options.errorcmid
                                        FROM {qtype_essayautograde_options} options
                                        JOIN {backup_ids_temp} bi ON bi.newitemid = options.questionid
                                       WHERE bi.backupid = ?
                                         AND bi.itemname = ?', array($restoreid, 'question'));
        foreach ($rs as $option) {
            if ($cmid = $option->errorcmid) {
                if ($map = restore_dbops::get_backup_ids_record($restoreid, 'course_module', $cmid)) {
                    $cmid = $map->newitemid;
                } else {
                    $cmid = 0;
                }
                $DB->set_field('qtype_essayautograde_options', 'errorcmid', $cmid, array('id' => $option->id));
            }
        }
        $rs->close();
    }
}
