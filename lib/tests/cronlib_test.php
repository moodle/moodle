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
 * Unit tests for the cron.
 *
 * @package   core
 * @category  test
 * @copyright 2013 Tim Gusak <tim.gusak@remote-learner.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/cronlib.php');

class cronlib_testcase extends basic_testcase {

    /**
     * Data provider for cron_delete_from_temp.
     *
     * @return array Provider data
     */
    public function cron_delete_from_temp_provider() {
        global $CFG;

        $tmpdir = realpath($CFG->tempdir);
        $time = time();

        $weekstime = $time - strtotime('1 week');
        $beforeweekstime = $time - strtotime('1 week') - 1;
        $afterweekstime = $time + strtotime('1 week') + 1;

        $node1 = new stdClass();
        $node1->path = '/dir1/dir1_1/dir1_2/dir1_3/';
        $node1->time = 1;
        $node1->isdir = true;

        $node2 = new stdClass();
        $node2->path = '/dir1/dir1_4/';
        $node2->time = $time;
        $node2->isdir = true;

        $node3 = new stdClass();
        $node3->path = '/dir2/';
        $node3->isdir = true;
        $node3->time = $time - $weekstime;

        $node4 = new stdClass();
        $node4->path = '/dir3/';
        $node4->isdir = true;
        $node4->time = $time - $afterweekstime;

        $node5 = new stdClass();
        $node5->path = '/dir1/dir1_1/dir1_2/file1';
        $node5->isdir = false;
        $node5->time = $beforeweekstime;

        $node6 = new stdClass();
        $node6->path = '/dir1/dir1_1/dir1_2/file2';
        $node6->isdir = false;
        $node6->time = $time;

        $node7 = new stdClass();
        $node7->path = '/dir1/dir1_4/file1';
        $node7->isdir = false;
        $node7->time = $time - $afterweekstime;

        $node8 = new stdClass();
        $node8->path = '/dir1/dir1_4/file2';
        $node8->isdir = false;
        $node8->time = $time;

        $node9 = new stdClass();
        $node9->path = '/file1';
        $node9->isdir = false;
        $node9->time = $time;

        $node10 = new stdClass();
        $node10->path = '/file2';
        $node10->isdir = false;
        $node10->time = $time - $afterweekstime;

        $data = array(
                array(
                    array($node1, $node2, $node3, $node4, $node5, $node6, $node7, $node8, $node9, $node10),
                    array(
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1'.DIRECTORY_SEPARATOR.'dir1_1',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1'.DIRECTORY_SEPARATOR.'dir1_1'.DIRECTORY_SEPARATOR.'dir1_2',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1'.DIRECTORY_SEPARATOR.'dir1_1'.DIRECTORY_SEPARATOR.'dir1_2'.DIRECTORY_SEPARATOR.'file2',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1'.DIRECTORY_SEPARATOR.'dir1_4',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir1'.DIRECTORY_SEPARATOR.'dir1_4'.DIRECTORY_SEPARATOR.'file2',
                        $tmpdir.DIRECTORY_SEPARATOR.'dir2',
                        $tmpdir.DIRECTORY_SEPARATOR.'file1',
                    )
                ),
                array(
                    array(),
                    array()
                )
        );

        return $data;
    }

    /**
     * Test removing files and directories from tempdir.
     *
     * @dataProvider cron_delete_from_temp_provider
     * @param array $nodes List of files and directories
     * @param array $expected The expected results
     */
    public function test_cron_delete_from_temp($nodes, $expected) {
        global $CFG;

        $tmpdir = $CFG->tempdir;

        foreach ($nodes as $data) {
            if ($data->isdir) {
                mkdir($tmpdir.$data->path, $CFG->directorypermissions, true);
            }
            touch($tmpdir.$data->path, $data->time);
        }

        $task = new \core\task\file_temp_cleanup_task();
        $task->execute();

        $dir = new RecursiveDirectoryIterator($tmpdir);
        $iter = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        $actual = array();
        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            if (!$iter->isDot()) {
                $actual[] = $iter->getRealPath();
            }
        }

        // Sort results to guarantee actual order.
        sort($actual);

        $this->assertEquals($expected, $actual);
    }
}
