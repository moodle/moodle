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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to delete temp files older than 1 week.
 */
class file_temp_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasktempfilecleanup', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        $tmpdir = $CFG->tempdir;
        // Default to last weeks time.
        $time = strtotime('-1 week');

        $dir = new \RecursiveDirectoryIterator($tmpdir);
        // Show all child nodes prior to their parent.
        $iter = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            $node = $iter->getRealPath();
            if (!is_readable($node)) {
                continue;
            }
            // Check if file or directory is older than the given time.
            if ($iter->getMTime() < $time) {
                if ($iter->isDir() && !$iter->isDot()) {
                    if (@rmdir($node) === false) {
                        mtrace("Failed removing directory '$node'.");
                    }
                }
                if ($iter->isFile()) {
                    if (@unlink($node) === false) {
                        mtrace("Failed removing file '$node'.");
                    }
                }
            }
        }

    }

}
