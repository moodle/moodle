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

namespace core_customfield;

/**
 * Functional test for class \core_customfield\api
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_test extends \advanced_testcase {

    /**
     * Get generator.
     *
     * @return core_customfield_generator
     */
    protected function get_generator(): \core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Help to assert that the given property in an array of object has the expected value
     *
     * @param array $expected
     * @param array $array array of objects with "get($property)" method
     * @param string $propertyname
     */
    protected function assert_property_in_array($expected, $array, $propertyname) {
        $this->assertEquals($expected, array_values(array_map(function($a) use ($propertyname) {
            return $a->get($propertyname);
        }, $array)));
    }

    /**
     * Tests for \core_customfield\api::move_category() behaviour.
     *
     * This replicates what is happening when categories are moved
     * in the interface using drag-drop.
     */
    public function test_move_category() {
        $this->resetAfterTest();

        // Create the categories.
        $params = ['component' => 'core_course', 'area' => 'course', 'itemid' => 0];
        $id0 = $this->get_generator()->create_category($params)->get('id');
        $id1 = $this->get_generator()->create_category($params)->get('id');
        $id2 = $this->get_generator()->create_category($params)->get('id');
        $id3 = $this->get_generator()->create_category($params)->get('id');
        $id4 = $this->get_generator()->create_category($params)->get('id');
        $id5 = $this->get_generator()->create_category($params)->get('id');

        // Check order after re-fetch.
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id2, $id3, $id4, $id5], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');

        // Move up 1 position.
        api::move_category(category_controller::create($id3), $id2);
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id3, $id2, $id4, $id5], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');

        // Move down 1 position.
        api::move_category(category_controller::create($id2), $id3);
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id2, $id3, $id4, $id5], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');

        // Move up 2 positions.
        api::move_category(category_controller::create($id4), $id2);
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id4, $id2, $id3, $id5], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');

        // Move down 2 positions.
        api::move_category(category_controller::create($id4), $id5);
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id2, $id3, $id4, $id5], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');

        // Move to the end of the list.
        api::move_category(category_controller::create($id2));
        $categories = api::get_categories_with_fields($params['component'], $params['area'], $params['itemid']);
        $this->assertEquals([$id0, $id1, $id3, $id4, $id5, $id2], array_keys($categories));
        $this->assert_property_in_array([0, 1, 2, 3, 4, 5], $categories, 'sortorder');
    }

    /**
     * Tests for \core_customfield\api::get_categories_with_fields() behaviour.
     */
    public function test_get_categories_with_fields() {
        $this->resetAfterTest();

        // Create the categories.
        $options = [
            'component' => 'core_course',
            'area'      => 'course',
            'itemid'    => 0,
            'contextid' => \context_system::instance()->id
        ];
        $category0 = $this->get_generator()->create_category(['name' => 'aaaa'] + $options);
        $category1 = $this->get_generator()->create_category(['name' => 'bbbb'] + $options);
        $category2 = $this->get_generator()->create_category(['name' => 'cccc'] + $options);
        $category3 = $this->get_generator()->create_category(['name' => 'dddd'] + $options);
        $category4 = $this->get_generator()->create_category(['name' => 'eeee'] + $options);
        $category5 = $this->get_generator()->create_category(['name' => 'ffff'] + $options);

        // Let's test counts.
        $this->assertCount(6, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category5);
        $this->assertCount(5, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category4);
        $this->assertCount(4, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category3);
        $this->assertCount(3, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category2);
        $this->assertCount(2, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category1);
        $this->assertCount(1, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
        api::delete_category($category0);
        $this->assertCount(0, api::get_categories_with_fields($options['component'], $options['area'], $options['itemid']));
    }

    /**
     * Test for functions api::save_category() and rename_category)
     */
    public function test_save_category() {
        $this->resetAfterTest();

        $params = ['component' => 'core_course', 'area' => 'course', 'itemid' => 0, 'name' => 'Cat1',
            'contextid' => \context_system::instance()->id];
        $c1 = category_controller::create(0, (object)$params);
        api::save_category($c1);
        $this->assertNotEmpty($c1->get('id'));

        $c1 = category_controller::create($c1->get('id'));
        $expected = $params + ['sortorder' => 0, 'id' => $c1->get('id'), 'description' => '', 'descriptionformat' => 0];
        $actual = array_intersect_key((array)$c1->to_record(), $expected); // Ignore timecreated, timemodified.
        ksort($expected);
        ksort($actual);
        $this->assertEquals($expected, $actual);

        // Create new category and check that the sortorder will be 1.
        $params['name'] = 'Cat2';
        $c2 = category_controller::create(0, (object)$params);
        api::save_category($c2);
        $this->assertNotEmpty($c2->get('id'));
        $this->assertEquals(1, $c2->get('sortorder'));
        $c2 = category_controller::create($c2->get('id'));
        $this->assertEquals(1, $c2->get('sortorder'));

        // Rename a category.
        $c1->set('name', 'Cat3');
        $c1->save();
        $c1 = category_controller::create($c1->get('id'));
        $this->assertEquals('Cat3', $c1->get('name'));
    }

    /**
     * Test for function handler::create_category
     */
    public function test_create_category() {
        $this->resetAfterTest();

        $handler = \core_course\customfield\course_handler::create();
        $c1id = $handler->create_category();
        $c1 = $handler->get_categories_with_fields()[$c1id];
        $this->assertEquals('Other fields', $c1->get('name'));
        $this->assertEquals($handler->get_component(), $c1->get('component'));
        $this->assertEquals($handler->get_area(), $c1->get('area'));
        $this->assertEquals($handler->get_itemid(), $c1->get('itemid'));
        $this->assertEquals($handler->get_configuration_context()->id, $c1->get('contextid'));

        // Generate more categories and make sure they have different names.
        $c2id = $handler->create_category();
        $c3id = $handler->create_category();
        $c2 = $handler->get_categories_with_fields()[$c2id];
        $c3 = $handler->get_categories_with_fields()[$c3id];
        $this->assertEquals('Other fields 1', $c2->get('name'));
        $this->assertEquals('Other fields 2', $c3->get('name'));
    }

    /**
     * Tests for \core_customfield\api::delete_category() behaviour.
     */
    public function test_delete_category_with_fields() {
        $this->resetAfterTest();

        global $DB;
        // Create two categories with fields and data.
        $options = [
            'component' => 'core_course',
            'area'      => 'course',
            'itemid'    => 0,
            'contextid' => \context_system::instance()->id
        ];
        $lpg = $this->get_generator();
        $course = $this->getDataGenerator()->create_course();
        $dataparams = ['instanceid' => $course->id, 'contextid' => \context_course::instance($course->id)->id];
        $category0 = $lpg->create_category($options);
        $category1 = $lpg->create_category($options);
        for ($i = 0; $i < 6; $i++) {
            $f = $lpg->create_field(['categoryid' => $category0->get('id')]);
            \core_customfield\data_controller::create(0, (object)$dataparams, $f)->save();
            $f = $lpg->create_field(['categoryid' => $category1->get('id')]);
            \core_customfield\data_controller::create(0, (object)$dataparams, $f)->save();
        }

        // Check that each category have fields and store ids for future checks.
        list($category0, $category1) = array_values(api::get_categories_with_fields($options['component'],
            $options['area'], $options['itemid']));
        $category0fieldsids = array_keys($category0->get_fields());
        $category1fieldsids = array_keys($category1->get_fields());

        // There are 6 records in field table and 6 records in data table for each category.
        list($sql, $p) = $DB->get_in_or_equal($category0fieldsids);
        $this->assertCount(6, $DB->get_records_select(\core_customfield\field::TABLE, 'id '.$sql, $p));
        $this->assertCount(6, $DB->get_records_select(\core_customfield\data::TABLE, 'fieldid '.$sql, $p));

        list($sql, $p) = $DB->get_in_or_equal($category1fieldsids);
        $this->assertCount(6, $DB->get_records_select(\core_customfield\field::TABLE, 'id '.$sql, $p));
        $this->assertCount(6, $DB->get_records_select(\core_customfield\data::TABLE, 'fieldid '.$sql, $p));

        // Delete one category.
        $this->assertTrue($category0->get_handler()->delete_category($category0));

        // Check that the category fields and data were deleted.
        list($sql, $p) = $DB->get_in_or_equal($category0fieldsids);
        $this->assertEmpty($DB->get_records_select(\core_customfield\field::TABLE, 'id '.$sql, $p));
        $this->assertEmpty($DB->get_records_select(\core_customfield\data::TABLE, 'fieldid '.$sql, $p));

        // Check that fields and data for the other category remain.
        list($sql, $p) = $DB->get_in_or_equal($category1fieldsids);
        $this->assertCount(6, $DB->get_records_select(\core_customfield\field::TABLE, 'id '.$sql, $p));
        $this->assertCount(6, $DB->get_records_select(\core_customfield\data::TABLE, 'fieldid '.$sql, $p));
    }
}
