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
 * @package    workshopform
 * @subpackage comments
 * @copyright  2010 onwards David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * restore subplugin class that provides the necessary information
 * needed to restore one workshopform_comments subplugin.
 */
class restore_workshopform_comments_subplugin extends restore_subplugin {

    ////////////////////////////////////////////////////////////////////////////
    // mappings of XML paths to the processable methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the paths to be handled by the subplugin at workshop level
     */
    protected function define_workshop_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('dimension');
        $elepath = $this->get_pathfor('/workshopform_comments_dimension'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at referenceassessment level
     */
    protected function define_referenceassessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('referencegrade');
        $elepath = $this->get_pathfor('/workshopform_comments_referencegrade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at exampleassessment level
     */
    protected function define_exampleassessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('examplegrade');
        $elepath = $this->get_pathfor('/workshopform_comments_examplegrade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at assessment level
     */
    protected function define_assessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('grade');
        $elepath = $this->get_pathfor('/workshopform_comments_grade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    ////////////////////////////////////////////////////////////////////////////
    // defined path elements are dispatched to the following methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Processes the workshopform_comments_dimension element
     */
    public function process_workshopform_comments_dimension($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->workshopid = $this->get_new_parentid('workshop');

        $newitemid = $DB->insert_record('workshopform_comments', $data);
        $this->set_mapping($this->get_namefor('dimension'), $oldid, $newitemid, true);

        // Process files for this workshopform_comments->id only
        $this->add_related_files('workshopform_comments', 'description', $this->get_namefor('dimension'), null, $oldid);
    }

    /**
     * Processes the workshopform_comments_referencegrade element
     */
    public function process_workshopform_comments_referencegrade($data) {
        $this->process_dimension_grades_structure('workshop_referenceassessment', $data);
    }

    /**
     * Processes the workshopform_comments_examplegrade element
     */
    public function process_workshopform_comments_examplegrade($data) {
        $this->process_dimension_grades_structure('workshop_exampleassessment', $data);
    }

    /**
     * Processes the workshopform_comments_grade element
     */
    public function process_workshopform_comments_grade($data) {
        $this->process_dimension_grades_structure('workshop_assessment', $data);
    }

    ////////////////////////////////////////////////////////////////////////////
    // internal private methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Process the dimension grades linked with the given type of assessment
     *
     * Populates the workshop_grades table with new records mapped to the restored
     * instances of assessments.
     *
     * @param mixed $elementname the name of the assessment element
     * @param array $data parsed xml data
     */
    private function process_dimension_grades_structure($elementname, $data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assessmentid = $this->get_new_parentid($elementname);
        $data->strategy = 'comments';
        $data->grade = 100.00000;
        $data->dimensionid = $this->get_mappingid($this->get_namefor('dimension'), $data->dimensionid);

        $DB->insert_record('workshop_grades', $data);
    }
}
