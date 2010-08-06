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
 * needed to restore one workshopeval->best subplugin.
 */
class restore_workshopeval_best_subplugin extends restore_subplugin {

    /**
     * Returns the paths to be handled by the subplugin at workshop level
     */
    protected function define_workshop_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('workshopeval_best_setting');
        $elepath = $this->get_pathfor('/workshopeval_best_settings'); // we used get_recommended_name() so this works
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * This method processes the workshopeval_best_setting  element
     * inside one accumulative workshopeval (see best subplugin backup)
     */
    public function process_workshopeval_best_workshopeval_best_setting($data) {
        global $DB;

        $data = (object)$data;

        $data->workshopid = $this->get_new_parentid('workshop');

        $newitemid = $DB->insert_record('workshopeval_best_settings', $data);
    }
}
