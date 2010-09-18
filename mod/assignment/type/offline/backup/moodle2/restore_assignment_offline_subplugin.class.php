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
 * needed to restore one assignment->offline subplugin.
 *
 * Note: Offline assignments really haven't any special subplugin
 * information to backup/restore, hence code below is skipped (return false)
 * but it's a good example of subplugins supported at different
 * elements (assignment and submission)
 */
class restore_assignment_offline_subplugin extends restore_subplugin {

    /**
     * Returns the paths to be handled by the subplugin at assignment level
     */
    protected function define_assignment_subplugin_structure() {

        return false; // This subplugin restore is only one example. Skip it.

        $paths = array();

        $elename = $this->get_namefor('config');
        $elepath = $this->get_pathfor('/config'); // because we used get_recommended_name() in backup this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Returns the paths to be handled by the subplugin at submission level
     */
    protected function define_submission_subplugin_structure() {

        return false; // This subplugin restore is only one example. Skip it.

        $paths = array();

        $elename = $this->get_namefor('submission_config');
        $elepath = $this->get_pathfor('/submission_config'); // because we used get_recommended_name() in backup this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * This method processes the config element inside one offline assignment (see offline subplugin backup)
     */
    public function process_assignment_offline_config($data) {
        $data = (object)$data;
        print_object($data); // Nothing to do, just print the data

        // Just to check that the whole API is available here
        $this->set_mapping('assignment_offline_config', 1, 1, true);
        $this->add_related_files('mod_assignment', 'intro', 'assignment_offline_config');
        print_object($this->get_mappingid('assignment_offline_config', 1));
        print_object($this->get_old_parentid('assignment'));
        print_object($this->get_new_parentid('assignment'));
        print_object($this->get_mapping('assignment', $this->get_old_parentid('assignment')));
        print_object($this->apply_date_offset(1));
        print_object($this->task->get_courseid());
        print_object($this->task->get_contextid());
        print_object($this->get_restoreid());
    }

    /**
     * This method processes the submission_config element inside one offline assignment (see offline subplugin backup)
     */
    public function process_assignment_offline_submission_config($data) {
        $data = (object)$data;
        print_object($data); // Nothing to do, just print the data
    }
}
