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
 * @subpackage numerrors
 * @copyright  2010 onwards David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to backup numerrors grading strategy information
 */
class backup_workshopform_numerrors_subplugin extends backup_subplugin {

    /**
     * Returns the assessment form definition to attach to 'workshop' XML element
     */
    protected function define_workshop_subplugin_structure() {

        // XML nodes declaration
        $subplugin = $this->get_subplugin_element(); // virtual optigroup element
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subpluginmap = new backup_nested_element('workshopform_numerrors_map', array('id'), array(
            'nonegative', 'grade'));
        $subplugindimension = new backup_nested_element('workshopform_numerrors_dimension', array('id'), array(
            'sort', 'description', 'descriptionformat', 'grade0', 'grade1', 'weight'));

        // connect XML elements into the tree
        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subpluginmap);
        $subpluginwrapper->add_child($subplugindimension);

        // set source to populate the data
        $subpluginmap->set_source_table('workshopform_numerrors_map', array('workshopid' => backup::VAR_ACTIVITYID));
        $subplugindimension->set_source_table('workshopform_numerrors', array('workshopid' => backup::VAR_ACTIVITYID));

        // file annotations
        $subplugindimension->annotate_files('workshopform_numerrors', 'description', 'id');

        return $subplugin;
    }

    /**
     * Returns the dimension grades to attach to 'referenceassessment' XML element
     */
    protected function define_referenceassessment_subplugin_structure() {
        return $this->dimension_grades_structure('workshopform_numerrors_referencegrade');
    }

    /**
     * Returns the dimension grades to attach to 'exampleassessment' XML element
     */
    protected function define_exampleassessment_subplugin_structure() {
        return $this->dimension_grades_structure('workshopform_numerrors_examplegrade');
    }

    /**
     * Returns the dimension grades to attach to 'assessment' XML element
     */
    protected function define_assessment_subplugin_structure() {
        return $this->dimension_grades_structure('workshopform_numerrors_grade');
    }

    ////////////////////////////////////////////////////////////////////////////
    // internal private methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the structure of dimension grades
     *
     * @param string forst parameter of {@link backup_nested_element} constructor
     */
    private function dimension_grades_structure($elementname) {

        // create XML elements
        $subplugin = $this->get_subplugin_element(); // virtual optigroup element
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subplugingrade = new backup_nested_element($elementname, array('id'), array(
            'dimensionid', 'grade', 'peercomment', 'peercommentformat'));

        // connect XML elements into the tree
        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subplugingrade);

        // set source to populate the data
        $subplugingrade->set_source_sql(
            "SELECT id, dimensionid, grade, peercomment, peercommentformat
               FROM {workshop_grades}
              WHERE strategy = 'numerrors' AND assessmentid = ?",
              array(backup::VAR_PARENTID));

        return $subplugin;
    }
}
