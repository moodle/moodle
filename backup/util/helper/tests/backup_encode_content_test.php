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

namespace core_backup;

use backup_course_task;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_course_task.class.php');

/**
 * Tests for encoding content links in backup_course_task.
 *
 * The code that this tests is actually in backup/moodle2/backup_course_task.class.php,
 * but there is no place for unit tests near there, and perhaps one day it will
 * be refactored so it becomes more generic.
 *
 * @package    core_backup
 * @category   test
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_encode_content_test extends \basic_testcase {

    /**
     * Test the encode_content_links method for course.
     */
    public function test_course_encode_content_links() {
        global $CFG;
        $httpsroot = "https://moodle.org";
        $httproot = "http://moodle.org";
        $oldroot = $CFG->wwwroot;

        // HTTPS root and links of both types in content.
        $CFG->wwwroot = $httpsroot;
        $encoded = backup_course_task::encode_content_links(
                $httproot . '/course/view.php?id=123, ' .
                $httpsroot . '/course/view.php?id=123, ' .
                $httpsroot . '/grade/index.php?id=123, ' .
                $httpsroot . '/grade/report/index.php?id=123, ' .
                $httpsroot . '/badges/view.php?type=2&id=123 and ' .
                $httpsroot . '/user/index.php?id=123.');
        $this->assertEquals('$@COURSEVIEWBYID*123@$, $@COURSEVIEWBYID*123@$, $@GRADEINDEXBYID*123@$, ' .
                '$@GRADEREPORTINDEXBYID*123@$, $@BADGESVIEWBYID*123@$ and $@USERINDEXVIEWBYID*123@$.', $encoded);

        // HTTP root and links of both types in content.
        $CFG->wwwroot = $httproot;
        $encoded = backup_course_task::encode_content_links(
            $httproot . '/course/view.php?id=123, ' .
            $httpsroot . '/course/view.php?id=123, ' .
            $httproot . '/grade/index.php?id=123, ' .
            $httproot . '/grade/report/index.php?id=123, ' .
            $httproot . '/badges/view.php?type=2&id=123 and ' .
            $httproot . '/user/index.php?id=123.');
        $this->assertEquals('$@COURSEVIEWBYID*123@$, $@COURSEVIEWBYID*123@$, $@GRADEINDEXBYID*123@$, ' .
            '$@GRADEREPORTINDEXBYID*123@$, $@BADGESVIEWBYID*123@$ and $@USERINDEXVIEWBYID*123@$.', $encoded);
        $CFG->wwwroot = $oldroot;
    }
}
