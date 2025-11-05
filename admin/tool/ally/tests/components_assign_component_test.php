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
 * Testcase class for the tool_ally\componentsupport\assign_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\componentsupport\assign_component;
use tool_ally\local_content;

defined('MOODLE_INTERNAL') || die();

require_once('abstract_testcase.php');

/**
 * Testcase class for the tool_ally\componentsupport\assign_component class.
 *
 * @package   tool_ally
 * @author    Eric Merrill
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class components_assign_component_test extends abstract_testcase {
    /**
     * @var stdClass
     */
    private $admin;

    /**
     * @var stdClass
     */
    private $course;

    /**
     * @var context_course
     */
    private $coursecontext;

    /**
     * @var stdClass
     */
    private $assign;

    /**
     * @var assign_component
     */
    private $component;

    public function setUp(): void {
        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $this->admin = get_admin();
        $this->course = $gen->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->assign = $gen->create_module('assign',
            [
                'course' => $this->course->id,
                'introformat' => FORMAT_HTML,
                'intro' => 'Text in intro'
            ]
        );

        $this->component = local_content::component_instance('assign');
    }

    /**
     * Test if file in use detection is working with this module.
     */
    public function test_check_file_in_use() {
        $context = \context_module::instance($this->assign->cmid);

        $usedfiles = [];
        $unusedfiles = [];

        // Check the intro.
        list($usedfiles[], $unusedfiles[]) = $this->check_html_files_in_use($context, 'mod_assign', $this->assign->id,
            'assign', 'intro');

        list($file1, $file2) = $this->setup_check_files($context, 'mod_assign', 'introattachment', 0);
        $usedfiles[] = $file1; // Silly workaround for PHP code checker.
        $usedfiles[] = $file2;

        // This will double check that file iterator is working as expected.
        $this->check_file_iterator_exclusion($context, $usedfiles, $unusedfiles);
    }
}
