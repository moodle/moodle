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

namespace core\task;

use stdClass;

/**
 * Unit tests for the file_temp_cleanup task.
 *
 * @package   core
 * @category  test
 * @copyright 2013 Tim Gusak <tim.gusak@remote-learner.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\task\file_temp_cleanup_task
 */
final class file_temp_cleanup_task_test extends \basic_testcase {
    /**
     * Data provider for cron_delete_from_temp.
     *
     * @return array Provider data
     */
    public static function cron_delete_from_temp_provider(): array {
        global $CFG;

        $tmpdir = realpath($CFG->tempdir);
        // This is a relative time.
        $time = 0;

        // Relative time stamps. Did you know data providers get executed during phpunit init?
        $lastweekstime = -($CFG->tempdatafoldercleanup * 3600); // This must match file_temp_cleanup_task.
        $beforelastweekstime = $lastweekstime - 3600 - 1; // At least 1h and 1s diff (make it DST immune).
        $afterlastweekstime = $lastweekstime + 3600 + 1; // At least 1h and 1s diff (make it DST immune).

        $nodes = array();
        // Really old directory to remove.
        $nodes[] = self::generate_test_path('/dir1/dir1_1/dir1_1_1/dir1_1_1_1/', true, $lastweekstime * 52, false);

        // New Directory to keep.
        $nodes[] = self::generate_test_path('/dir1/dir1_2/', true, $time, true);

        // Directory a little less than 1 week old, keep.
        $nodes[] = self::generate_test_path('/dir2/', true, $afterlastweekstime, true);

        // Directory older than 1 week old, remove.
        $nodes[] = self::generate_test_path('/dir3/', true, $beforelastweekstime, false);

        // File older than 1 week old, remove.
        $nodes[] = self::generate_test_path('/dir1/dir1_1/dir1_1_1/file1_1_1_1', false, $beforelastweekstime, false);

        // New File to keep.
        $nodes[] = self::generate_test_path('/dir1/dir1_1/dir1_1_1/file1_1_1_2', false, $time, true);

        // File older than 1 week old, remove.
        $nodes[] = self::generate_test_path('/dir1/dir1_2/file1_1_2_1', false, $beforelastweekstime, false);

        // New file to keep.
        $nodes[] = self::generate_test_path('/dir1/dir1_2/file1_1_2_2', false, $time, true);

        // New file to keep.
        $nodes[] = self::generate_test_path('/file1', false, $time, true);

        // File older than 1 week, keep.
        $nodes[] = self::generate_test_path('/file2', false, $beforelastweekstime, false);

        // Directory older than 1 week to keep.
        // Note: Since this directory contains a directory that contains a file that is also older than a week
        // the directory won't be deleted since it's mtime will be updated when the file is deleted.

        $nodes[] = self::generate_test_path('/dir4/dir4_1', true, $beforelastweekstime, true);

        $nodes[] = self::generate_test_path('/dir4/dir4_1/dir4_1_1/', true, $beforelastweekstime, true);

        // File older than 1 week to remove.
        $nodes[] = self::generate_test_path('/dir4/dir4_1/dir4_1_1/file4_1_1_1', false, $beforelastweekstime, false);

        $expectednodes = array();
        foreach ($nodes as $node) {
            if ($node->keep) {
                $path = $tmpdir;
                $pelements = preg_split('/\//', $node->path);
                foreach ($pelements as $pelement) {
                    if ($pelement === '') {
                        continue;
                    }
                    $path .= DIRECTORY_SEPARATOR . $pelement;
                    if (!in_array($path, $expectednodes)) {
                        $expectednodes[] = $path;
                    }
                }
            }
        }
        sort($expectednodes);

        $data = [
            [$nodes, $expectednodes],
            [[], []],
        ];

        return $data;
    }

    /**
     * Function to populate node array.
     *
     * @param string $path Path of directory or file
     * @param bool $isdir Is the node a directory
     * @param int $time modified time of the node in epoch
     * @param bool $keep Should the node exist after the delete function has run
     */
    private static function generate_test_path($path, $isdir = false, $time = 0, $keep = false): stdClass {
        return (object) [
            'path' => $path,
            'isdir' => $isdir,
            'time' => $time,
            'keep' => $keep,
        ];
    }
    /**
     * Test removing files and directories from tempdir.
     *
     * @dataProvider cron_delete_from_temp_provider
     * @param array $nodes List of files and directories
     * @param array $expected The expected results
     */
    public function test_cron_delete_from_temp($nodes, $expected): void {
        global $CFG;

        $tmpdir = realpath($CFG->tempdir);

        foreach ($nodes as $data) {
            if ($data->isdir) {
                mkdir($tmpdir.$data->path, $CFG->directorypermissions, true);
            }
        }
        // We need to iterate through again since adding a file to a directory will
        // update the modified time of the directory.
        foreach ($nodes as $data) {
            touch($tmpdir.$data->path, time() + $data->time);
        }

        $task = new \core\task\file_temp_cleanup_task();
        $task->execute();

        $dir = new \RecursiveDirectoryIterator($tmpdir);
        $iter = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

        $actual = array();
        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            $isvalid = true;
            $isvalid = $isvalid && !$iter->isDot();
            // Remove the default $CFG->tempdir/backup directory and $CFG->tempdir/.htaccess file from this comparison.
            $isvalid = $isvalid && !($iter->isDir() && ($iter->getRealpath() === $tmpdir . DIRECTORY_SEPARATOR . 'backup'));
            $isvalid = $isvalid && !($iter->isFile() && ($iter->getRealpath() === $tmpdir . DIRECTORY_SEPARATOR . '.htaccess'));
            if ($isvalid) {
                $actual[] = $iter->getRealPath();
            }
        }

        // Sort results to guarantee actual order.
        sort($actual);

        $this->assertEquals($expected, $actual);
    }
}
