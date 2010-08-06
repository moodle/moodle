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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * restore subplugin class that provides the necessary information
 * needed to restore one workshopform->accumulative subplugin.
 */
class restore_workshopform_accumulative_subplugin extends restore_subplugin {

    /**
     * Returns the paths to be handled by the subplugin at workshop level
     */
    protected function define_workshop_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('workshopform_accumulative_dimension');
        $elepath = $this->get_pathfor('/workshopform_accumulative_dimension'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at referenceassessment level
     */
    protected function define_referenceassessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('workshopform_accumulative_referencegrade');
        $elepath = $this->get_pathfor('/workshopform_accumulative_referencegrade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at exampleassessment level
     */
    protected function define_exampleassessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('workshopform_accumulative_examplegrade');
        $elepath = $this->get_pathfor('/workshopform_accumulative_examplegrade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at assessment level
     */
    protected function define_assessment_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('workshopform_accumulative_grade');
        $elepath = $this->get_pathfor('/workshopform_accumulative_grade'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * This method processes the workshopform_accumulative_dimension  element
     * inside one accumulative workshopform (see accumulative subplugin backup)
     */
    public function process_workshopform_accumulative_workshopform_accumulative_dimension($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->workshopid = $this->get_new_parentid('workshop');
        if ($data->grade < 0) { // scale found, get mapping
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        $newitemid = $DB->insert_record('workshopform_accumulative', $data);
        $this->set_mapping('workshopform_accumulative', $oldid, $newitemid, true);

        // Process files for this workshopform_accumulative->id only
        $this->add_related_files('workshopform_accumulative', 'description', 'workshopform_accumulative', null, $oldid);
    }

    /**
     * This method processes the workshopform_accumulative_referencegrade element
     * inside one accumulative workshopform (see accumulative subplugin backup)
     */
    public function process_workshopform_accumulative_workshopform_accumulative_referencegrade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assessmentid = $this->get_new_parentid('workshop_referenceassessment');
        $data->strategy = 'accumulative';
        $data->dimensionid = $this->get_mappingid('workshopform_accumulative', $data->dimensionid);

        $newitemid = $DB->insert_record('workshop_grades', $data);
    }

    /**
     * This method processes the workshopform_accumulative_examplegrade  element
     * inside one accumulative workshopform (see accumulative subplugin backup)
     */
    public function process_workshopform_accumulative_workshopform_accumulative_examplegrade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assessmentid = $this->get_new_parentid('workshop_exampleassessment');
        $data->strategy = 'accumulative';
        $data->dimensionid = $this->get_mappingid('workshopform_accumulative', $data->dimensionid);

        $newitemid = $DB->insert_record('workshop_grades', $data);
    }

    /**
     * This method processes the workshopform_accumulative_grade  element
     * inside one accumulative workshopform (see accumulative subplugin backup)
     */
    public function process_workshopform_accumulative_workshopform_accumulative_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assessmentid = $this->get_new_parentid('workshop_assessment');
        $data->strategy = 'accumulative';
        $data->dimensionid = $this->get_mappingid('workshopform_accumulative', $data->dimensionid);

        $newitemid = $DB->insert_record('workshop_grades', $data);
    }
}
