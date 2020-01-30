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
 * core_customfield test data generator test.
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * core_customfield test data generator testcase.
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_customfield_generator_testcase extends advanced_testcase {

    /**
     * Get generator
     * @return core_customfield_generator
     */
    protected function get_generator(): core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Test creating category
     */
    public function test_create_category() {
        $this->resetAfterTest(true);

        $lpg = $this->get_generator();
        $category = $lpg->create_category();

        $this->assertInstanceOf('\core_customfield\category_controller', $category);
        $this->assertTrue(\core_customfield\category::record_exists($category->get('id')));
    }

    /**
     * Test creating field
     */
    public function test_create_field() {
        $this->resetAfterTest(true);

        $lpg = $this->get_generator();
        $category = $lpg->create_category();
        $field = $lpg->create_field(['categoryid' => $category->get('id')]);

        $this->assertInstanceOf('\core_customfield\field_controller', $field);
        $this->assertTrue(\core_customfield\field::record_exists($field->get('id')));

        $category = core_customfield\category_controller::create($category->get('id'));
        $category = \core_customfield\api::get_categories_with_fields($category->get('component'),
            $category->get('area'), $category->get('itemid'))[$category->get('id')];
        $this->assertCount(1, $category->get_fields());
    }

    /**
     * Test for function add_instance_data()
     */
    public function test_add_instance_data() {
        $this->resetAfterTest(true);

        $lpg = $this->get_generator();
        $c1 = $lpg->create_category();
        $course1 = $this->getDataGenerator()->create_course();

        $f11 = $this->get_generator()->create_field(['categoryid' => $c1->get('id'), 'type' => 'checkbox']);
        $f12 = $this->get_generator()->create_field(['categoryid' => $c1->get('id'), 'type' => 'date']);
        $f13 = $this->get_generator()->create_field(['categoryid' => $c1->get('id'),
            'type' => 'select', 'configdata' => ['options' => "a\nb\nc"]]);
        $f14 = $this->get_generator()->create_field(['categoryid' => $c1->get('id'), 'type' => 'text']);
        $f15 = $this->get_generator()->create_field(['categoryid' => $c1->get('id'), 'type' => 'textarea']);

        $this->get_generator()->add_instance_data($f11, $course1->id, 1);
        $this->get_generator()->add_instance_data($f12, $course1->id, 1546300800);
        $this->get_generator()->add_instance_data($f13, $course1->id, 2);
        $this->get_generator()->add_instance_data($f14, $course1->id, 'Hello');
        $this->get_generator()->add_instance_data($f15, $course1->id, ['text' => '<p>Hi there</p>', 'format' => FORMAT_HTML]);

        $handler = $c1->get_handler();
        list($data1, $data2, $data3, $data4, $data5) = array_values($handler->get_instance_data($course1->id));
        $this->assertNotEmpty($data1->get('id'));
        $this->assertEquals(1, $data1->get_value());
        $this->assertNotEmpty($data2->get('id'));
        $this->assertEquals(1546300800, $data2->get_value());
        $this->assertNotEmpty($data3->get('id'));
        $this->assertEquals(2, $data3->get_value());
        $this->assertNotEmpty($data4->get('id'));
        $this->assertEquals('Hello', $data4->get_value());
        $this->assertNotEmpty($data5->get('id'));
        $this->assertEquals('<p>Hi there</p>', $data5->get_value());
    }
}
