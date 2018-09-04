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
 * Defines the task which removes old tmp files
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_hvp look for updates task class
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_tmpfiles extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('removetmpfiles', 'mod_hvp');
    }

    public function execute() {
        global $DB;
        $tmpfiles = $DB->get_records_sql(
                "SELECT f.id
                   FROM {hvp_tmpfiles} tf
                   JOIN {files} f ON f.id = tf.id
                  WHERE f.timecreated < ?",
                array(time() - 86400)
        );
        if (empty($tmpfiles)) {
            return; // Nothing to clean up.
        }

        $fs = get_file_storage();
        foreach ($tmpfiles as $tmpfile) {
            // Delete file.
            $file = $fs->get_file_by_id($tmpfile->id);
            $file->delete();

            // Remove tmpfile entry.
            $DB->delete_records('hvp_tmpfiles', array('id' => $tmpfile->id));
        }
    }
}
