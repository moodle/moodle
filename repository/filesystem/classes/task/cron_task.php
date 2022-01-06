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

namespace repository_filesystem\task;
defined('MOODLE_INTERNAL') || die();

/**
 * A schedule task for file system repository cron.
 *
 * @package   repository_filesystem
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'repository_filesystem');
    }

    /**
     * Run file system repository cron.
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        $fs = get_file_storage();
        // Find all generated thumbnails and group them in array by itemid (itemid == repository instance id).
        $allfiles = array_merge(
            $fs->get_area_files(SYSCONTEXTID, 'repository_filesystem', 'thumb'),
            $fs->get_area_files(SYSCONTEXTID, 'repository_filesystem', 'icon')
        );
        $filesbyitem = array();
        foreach ($allfiles as $file) {
            if (!isset($filesbyitem[$file->get_itemid()])) {
                $filesbyitem[$file->get_itemid()] = array();
            }
            $filesbyitem[$file->get_itemid()][] = $file;
        }
        // Find all instances of repository_filesystem.
        $instances = \repository::get_instances(array('type' => 'filesystem'));
        // Loop through all itemids of generated thumbnails.
        foreach ($filesbyitem as $itemid => $files) {
            if (!isset($instances[$itemid]) || !($instances[$itemid] instanceof repository_filesystem)) {
                // Instance was deleted.
                $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'thumb', $itemid);
                $fs->delete_area_files(SYSCONTEXTID, 'repository_filesystem', 'icon', $itemid);
                mtrace(" instance $itemid does not exist: deleted all thumbnails");
            } else {
                // Instance has some generated thumbnails, check that they are not outdated.
                $instances[$itemid]->remove_obsolete_thumbnails($files);
            }
        }
    }
}
