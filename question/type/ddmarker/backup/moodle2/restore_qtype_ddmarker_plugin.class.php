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
 * Restore code for qtype_ddmarker.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Restore plugin class for the ddmarker question type plugin.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_ddmarker_plugin extends restore_qtype_plugin {
    /**
     * Returns the qtype name.
     *
     * @return string The type name
     */
    protected static function qtype_name() {
        return 'ddmarker';
    }

    /**
     * Returns the paths to be handled by the plugin at question level.
     *
     * @return array
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // Add own qtype stuff.
        $elename = 'dds';
        $elepath = $this->get_pathfor('/'.self::qtype_name());
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'drag';
        $elepath = $this->get_pathfor('/drags/drag');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'drop';
        $elepath = $this->get_pathfor('/drops/drop');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/{qtypename} element.
     *
     * @param array|object $data Drag and drop data to work with.
     */
    public function process_dds($data) {
        global $DB;

        $prefix = 'qtype_'.self::qtype_name();

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore
        // we need to create its qtype_ddmarker too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record($prefix, $data);
            // Create mapping (needed for decoding links).
            $this->set_mapping($prefix, $oldid, $newitemid);
        }
    }

    /**
     * Process the qtype/drags/drag element.
     *
     * @param array|object $data Drag and drop drag data to work with.
     */
    public function process_drag($data) {
        global $DB;

        $prefix = 'qtype_'.self::qtype_name();

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        if ($questioncreated) {
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record("{$prefix}_drags", $data);
            // Create mapping (there are files and states based on this).
            $this->set_mapping("{$prefix}_drags", $oldid, $newitemid);

        }
    }

    /**
     * Process the qtype/drags/drop element.
     *
     * @param array|object $data Drag and drop drops data to work with.
     */
    public function process_drop($data) {
        global $DB;

        $prefix = 'qtype_'.self::qtype_name();

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        if ($questioncreated) {
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record("{$prefix}_drops", $data);
            // Create mapping (there are files and states based on this).
            $this->set_mapping("{$prefix}_drops", $oldid, $newitemid);
        }
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder
     *
     * @return array
     */
    public static function define_decode_contents() {

        $prefix = 'qtype_'.self::qtype_name();

        $contents = array();

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        $contents[] =
            new restore_decode_content($prefix, $fields, $prefix);

        return $contents;
    }
}
