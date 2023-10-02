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
 * Provides the information for restore.
 *
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class restore_bbbext_simple_subplugin extends restore_subplugin {
    /**
     * Returns the paths to be handled by the subplugin.
     *
     * @return array
     */
    protected function define_bigbluebuttonbn_subplugin_structure() {

        $paths = [];

        $elename = $this->get_namefor('bigbluebuttonbn');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/bbbext_simple');

        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Processes one subplugin instance additional parameter.
     *
     * @param mixed $data
     */
    public function process_bbbext_simple_bigbluebuttonbn($data) {
        global $DB;

        $data = (object) $data;
        $data->bigbluebuttonbnid = $this->get_new_parentid('bigbluebuttonbn');
        $DB->insert_record('bbbext_simple', $data);
    }
}
