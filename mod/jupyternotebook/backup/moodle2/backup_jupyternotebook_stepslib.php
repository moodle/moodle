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
 * Jupyternotebook backup definition
 *
 * @package   mod_jupyternotebook
 * @copyright 2021 DNE - Ministere de l'Education Nationale 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete url structure for backup, with file and id annotations
 */
class backup_jupyternotebook_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $jupyternotebook = new backup_nested_element('jupyternotebook', array('id'), array(
            'name', 'intro', 'introformat', 'showdescription', 'displayoptions',
            'serverurl', 'jpcourseid', 'jpnotebookid', 'iframeheight', 'timemodified'));

        // Define sources
        $jupyternotebook->set_source_table('jupyternotebook', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $jupyternotebook->annotate_files('mod_jupyternotebook', 'intro', null); // This file area hasn't itemid

        // Return the root element (label), wrapped into standard activity structure
        return $this->prepare_activity_structure($jupyternotebook);
    }

}
