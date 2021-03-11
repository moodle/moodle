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
 * Area category unit tests.
 *
 * @package    core_search
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Area category unit tests.
 *
 * @package    core_search
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_area_category_testcase extends advanced_testcase {

    /**
     * A helper function to get a mocked search area.
     * @param string $areaid
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function get_mocked_area($areaid) {
        $builder = $this->getMockBuilder('\core_search\base');
        $builder->disableOriginalConstructor();
        $builder->onlyMethods(array('get_area_id'));
        $area = $builder->getMockForAbstractClass();
        $area->method('get_area_id')->willReturn($areaid);

        return $area;
    }

    /**
     * A helper function to get a list of search areas.
     *
     * @return array
     */
    protected function get_areas() {
        $areas = [];
        $areas[] = $this->get_mocked_area('area1');
        $areas[] = 'String';
        $areas[] = 1;
        $areas[] = '12';
        $areas[] = true;
        $areas[] = false;
        $areas[] = null;
        $areas[] = [$this->get_mocked_area('area2')];
        $areas[] = $this;
        $areas[] = new stdClass();
        $areas[] = $this->get_mocked_area('area3');
        $areas[] = $this->get_mocked_area('area4');

        return $areas;
    }

    /**
     * Test default values.
     */
    public function test_default_values() {
        $category = new \core_search\area_category('test_name', 'test_visiblename');

        $this->assertEquals('test_name', $category->get_name());
        $this->assertEquals('test_visiblename', $category->get_visiblename());
        $this->assertEquals(0, $category->get_order());
        $this->assertEquals([], $category->get_areas());
    }

    /**
     * Test that all get functions work as expected.
     */
    public function test_getters() {
        $category = new \core_search\area_category('test_name', 'test_visiblename', 4, $this->get_areas());

        $this->assertEquals('test_name', $category->get_name());
        $this->assertEquals('test_visiblename', $category->get_visiblename());
        $this->assertEquals(4, $category->get_order());

        $this->assertTrue(is_array($category->get_areas()));
        $this->assertCount(3, $category->get_areas());
        $this->assertTrue(key_exists('area1', $category->get_areas()));
        $this->assertTrue(key_exists('area3', $category->get_areas()));
        $this->assertTrue(key_exists('area4', $category->get_areas()));
    }

    /**
     * Test that a list of areas could be set correctly.
     */
    public function test_list_of_areas_could_be_set() {
        $category = new \core_search\area_category('test_name', 'test_visiblename');
        $this->assertEquals([], $category->get_areas());

        $category->set_areas($this->get_areas());

        $this->assertTrue(is_array($category->get_areas()));
        $this->assertCount(3, $category->get_areas());
        $this->assertTrue(key_exists('area1', $category->get_areas()));
        $this->assertTrue(key_exists('area3', $category->get_areas()));
        $this->assertTrue(key_exists('area4', $category->get_areas()));
    }

}
