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
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restores the marking guide specific data from grading.xml file
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_gradingform_guide_plugin extends restore_gradingform_plugin {

    /**
     * Declares the marking guide XML paths attached to the form definition element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_definition_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_guide_criterion',
            $this->get_pathfor('/guidecriteria/guidecriterion'));

        $paths[] = new restore_path_element('gradingform_guide_comment',
            $this->get_pathfor('/guidecomments/guidecomment'));

        return $paths;
    }

    /**
     * Declares the marking guide XML paths attached to the form instance element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_instance_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradinform_guide_filling',
            $this->get_pathfor('/fillings/filling'));

        return $paths;
    }

    /**
     * Processes criterion element data
     *
     * Sets the mapping 'gradingform_guide_criterion' to be used later by
     * {@link self::process_gradinform_guide_filling()}
     *
     * @param array|stdClass $data
     */
    public function process_gradingform_guide_criterion($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->definitionid = $this->get_new_parentid('grading_definition');

        $newid = $DB->insert_record('gradingform_guide_criteria', $data);
        $this->set_mapping('gradingform_guide_criterion', $oldid, $newid);
    }

    /**
     * Processes comments element data
     *
     * @param array|stdClass $data The data to insert as a comment
     */
    public function process_gradingform_guide_comment($data) {
        global $DB;

        $data = (object)$data;
        $data->definitionid = $this->get_new_parentid('grading_definition');

        $DB->insert_record('gradingform_guide_comments', $data);
    }

    /**
     * Processes filling element data
     *
     * @param array|stdClass $data The data to insert as a filling
     */
    public function process_gradinform_guide_filling($data) {
        global $DB;

        $data = (object)$data;
        $data->instanceid = $this->get_new_parentid('grading_instance');
        $data->criterionid = $this->get_mappingid('gradingform_guide_criterion', $data->criterionid);

        $DB->insert_record('gradingform_guide_fillings', $data);
    }
}
