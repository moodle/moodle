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
 * Testcase class for the tool_ally\componentsupport\course_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\course_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_course_component_test extends abstract_testcase {
    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var context_course
     */
    private $coursecontext;

    /**
     * @var stdClass[]
     */
    private $sections = [];

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->course = $gen->create_course(['summaryformat' => FORMAT_HTML]);
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->sections[] = $gen->create_course_section(['section' => 0, 'course' => $this->course->id]);
    }

    /**
     * Check that files are properly excluded or included based usage.
     */
    public function test_file_in_use() {
        $context = $this->coursecontext;

        $usedfiles = [];
        $unusedfiles = [];

        // Check the intro.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'course', $this->course->id,
            'course', 'summary');

        // Add some course image files that are always in use.
        list($file1, $file2) = $this->setup_check_files($context, 'course', 'overviewfiles', $this->course->id);
        $usedfiles[] = $file1; // Silly workaround for PHP code checker.
        $usedfiles[] = $file2;

        // Now a course section summary.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'course', $this->sections[0]->id,
            'course_sections', 'summary');

        // This will double check that file iterator is working as expected.
        $this->check_file_iterator_exclusion($context, $usedfiles, $unusedfiles);
    }
}
