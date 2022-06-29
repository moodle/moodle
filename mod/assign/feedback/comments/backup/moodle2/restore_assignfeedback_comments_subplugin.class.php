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
 * Restore subplugin class.
 *
 * Provides the necessary information needed to restore
 * one assign_submission subplugin.
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restore subplugin class.
 *
 * Provides the necessary information needed to restore
 * one assignfeedback subplugin.
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_assignfeedback_comments_subplugin extends restore_subplugin {

    /**
     * Returns the paths to be handled by the subplugin at workshop level
     * @return array
     */
    protected function define_grade_subplugin_structure() {

        $paths = array();

        $elename = $this->get_namefor('grade');
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/feedback_comments');

        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Processes one feedback_comments element.
     * @param mixed $data
     */
    public function process_assignfeedback_comments_grade($data) {
        global $DB;

        $data = (object)$data;
        $data->assignment = $this->get_new_parentid('assign');
        $oldgradeid = $data->grade;
        // The mapping is set in the restore for the core assign activity
        // when a grade node is processed.
        $data->grade = $this->get_mappingid('grade', $data->grade);

        $DB->insert_record('assignfeedback_comments', $data);

        $this->add_related_files(
            'assignfeedback_comments',
            'feedback',
            'grade',
            null,
            $oldgradeid
        );
    }
}
