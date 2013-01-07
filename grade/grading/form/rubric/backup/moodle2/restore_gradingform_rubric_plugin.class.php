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
 * Support for restore API
 *
 * @package    gradingform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restores the rubric specific data from grading.xml file
 *
 * @package    gradingform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_gradingform_rubric_plugin extends restore_gradingform_plugin {

    /**
     * Declares the rubric XML paths attached to the form definition element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_definition_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_rubric_criterion',
            $this->get_pathfor('/criteria/criterion'));

        $paths[] = new restore_path_element('gradingform_rubric_level',
            $this->get_pathfor('/criteria/criterion/levels/level'));

        return $paths;
    }

    /**
     * Declares the rubric XML paths attached to the form instance element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_instance_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradinform_rubric_filling',
            $this->get_pathfor('/fillings/filling'));

        return $paths;
    }

    /**
     * Processes criterion element data
     *
     * Sets the mapping 'gradingform_rubric_criterion' to be used later by
     * {@link self::process_gradinform_rubric_filling()}
     *
     * @param stdClass|array $data
     */
    public function process_gradingform_rubric_criterion($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->definitionid = $this->get_new_parentid('grading_definition');

        $newid = $DB->insert_record('gradingform_rubric_criteria', $data);
        $this->set_mapping('gradingform_rubric_criterion', $oldid, $newid);
    }

    /**
     * Processes level element data
     *
     * Sets the mapping 'gradingform_rubric_level' to be used later by
     * {@link self::process_gradinform_rubric_filling()}
     *
     * @param stdClass|array $data
     */
    public function process_gradingform_rubric_level($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->criterionid = $this->get_new_parentid('gradingform_rubric_criterion');

        $newid = $DB->insert_record('gradingform_rubric_levels', $data);
        $this->set_mapping('gradingform_rubric_level', $oldid, $newid);
    }

    /**
     * Processes filling element data
     *
     * @param stdClass|array $data
     */
    public function process_gradinform_rubric_filling($data) {
        global $DB;

        $data = (object)$data;
        $data->instanceid = $this->get_new_parentid('grading_instance');
        $data->criterionid = $this->get_mappingid('gradingform_rubric_criterion', $data->criterionid);
        $data->levelid = $this->get_mappingid('gradingform_rubric_level', $data->levelid);

        if (!empty($data->criterionid)) {
            $DB->insert_record('gradingform_rubric_fillings', $data);
        }

    }
}
