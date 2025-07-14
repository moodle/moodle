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
     * Do the job, given the target directory.
     *
     * @param string $tmpdir The directory hosting the candidate stale temp files.
     */
    protected function execute_on($tmpdir) {
        global $CFG;

        // Default to last weeks time.
        $time = time() - ($CFG->tempdatafoldercleanup * 3600);

        $dir = new \RecursiveDirectoryIterator($tmpdir);
        // Show all child nodes prior to their parent.
        $iter = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

        // An array of the full path (key) and date last modified.
        $modifieddateobject = array();

        // Get the time modified for each directory node. Nodes will be updated
        // once a file is deleted, so we need a list of the original values.
        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            $node = $iter->getRealPath();
            if (!is_readable($node)) {
                continue;
            }
            $modifieddateobject[$node] = $iter->getMTime();
        }

        // Now loop through again and remove old files and directories.
        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            $node = $iter->getRealPath();
            if (!isset($modifieddateobject[$node]) || !is_readable($node)) {
                continue;
            }

            // Check if file or directory is older than the given time.
            if ($modifieddateobject[$node] < $time) {
                if ($iter->isDir() && !$iter->isDot()) {
                    // Don't attempt to delete the directory if it isn't empty.
                    if (!glob($node. DIRECTORY_SEPARATOR . '*')) {
                        if (@rmdir($node) === false) {
                            mtrace("Failed removing directory '$node'.");
                        }
                    }
                }
                if ($iter->isFile()) {
                    if (@unlink($node) === false) {
                        mtrace("Failed removing file '$node'.");
                    }
                }
            } else {
                // Return the time modified to the original date only for real files.
                if ($iter->isDir() && !$iter->isDot()) {
                    try {
                        @touch($node, $modifieddateobject[$node]);
                    } catch (\Throwable $t) {
                        null;
                    }
                }
            }
        }
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        // The directories hosting the candidate stale temp files eventually are $CFG->tempdir and $CFG->backuptempdir.

        // Do the job on each of the directories above.
        // Let's start with $CFG->tempdir.
        $this->execute_on($CFG->tempdir);

        // Run on $CFG->backuptempdir too, if different from the default one, '$CFG->tempdir/backup'.
        if (realpath(dirname($CFG->backuptempdir)) !== realpath($CFG->tempdir)) {
            // The $CFG->backuptempdir setting is different from the default '$CFG->tempdir/backup'.
            $this->execute_on($CFG->backuptempdir);
        }
    }
}
