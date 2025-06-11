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
 * This file contains the class for backup of this submission plugin
 *
 * @package assignsubmission_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */

/**
 * Provides the information to backup submission files
 * This just adds its filearea to the annotations and records the number of files
 *
 * @package assignsubmission_onenote
 */
class backup_assignsubmission_onenote_subplugin extends backup_subplugin {

    /**
     * Returns the subplugin information to attach to submission element.
     *
     * @return backup_subplugin_element
     */
    protected function define_submission_subplugin_structure() {
        // Create XML elements.
        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subpluginelement = new backup_nested_element('submission_onenote', null, ['numfiles', 'submission']);

        // Connect XML elements into the tree.
        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subpluginelement);

        // Set source to populate the data.
        $subpluginelement->set_source_table('assignsubmission_onenote', ['submission' => backup::VAR_PARENTID]);

        // The parent is the submission.
        $subpluginelement->annotate_files('assignsubmission_onenote', 'submission_onenote', 'submission');
        return $subplugin;
    }

}
