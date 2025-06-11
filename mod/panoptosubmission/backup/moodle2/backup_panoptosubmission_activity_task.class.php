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
 * Panopto Student Submission backup activity task class.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/panoptosubmission/backup/moodle2/backup_panoptosubmission_stepslib.php');

/**
 * Panopto Student Submission backup activity task class.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_panoptosubmission_activity_task extends backup_activity_task {

    /**
     * Defines settings
     */
    protected function define_my_settings() {
    }

    /**
     * Defines steps
     */
    protected function define_my_steps() {
        $this->add_step(
            new backup_panoptosubmission_activity_structure_step('panoptosubmission_structure', 'panoptosubmission.xml')
        );
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links.
     *
     * @param mixed $content the content being encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        $search = "/(".$base."\/mod\/panoptosubmission\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@PANOPTOSUBMISSIONINDEX*$2@$', $content);

        $search = "/(".$base."\/mod\/panoptosubmission\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@PANOPTOSUBMISSIONVIEWBYID*$2@$', $content);

        return $content;
    }
}
