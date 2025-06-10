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

namespace report_lsusql\local;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/report/lsusql/locallib.php');

/**
 * Tests for the report_lsusql\local\category.
 *
 * @package   report_lsusql
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class _category_test extends \advanced_testcase {
    /**
     * Test create category.
     */
    public function test_create_category() {
        $this->resetAfterTest();
        $fakerecord = (object) [
            'id' => 1,
            'name' => 'Category 1'
        ];

        $category = new category($fakerecord);

        $this->assertEquals(1, $category->get_id());
        $this->assertEquals('Category 1', $category->get_name());
        $this->assertStringContainsString('category.php?id=1', $category->get_url());
    }

    /**
     * Test create category.
     */
    public function test_load_queries_data() {
        $this->resetAfterTest();
        $fakerecord = (object) [
            'id' => 1,
            'name' => 'Category 1'
        ];

        $fakequeries = [
            (object) [
                'id' => 1,
                'displayname' => 'Q1',
                'runable' => 'manual'
            ],
            (object) [
                'id' => 2,
                'displayname' => 'Q2',
                'runable' => 'manual'
            ],
            (object) [
                'id' => 3,
                'displayname' => 'Q3',
                'runable' => 'daily'
            ]
        ];

        $category = new category($fakerecord);
        $category->load_queries_data($fakequeries);

        $this->assertEquals(2, $category->get_statistic()['manual']);
        $this->assertEquals(1, $category->get_statistic()['daily']);
        $this->assertEquals(0, $category->get_statistic()['weekly']);
        $this->assertEquals(0, $category->get_statistic()['monthly']);
        // The result contains 2 elements: manual and daily.
        $this->assertEquals(2, count($category->get_queries_data()[1]));
    }
}
