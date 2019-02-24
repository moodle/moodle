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
 * Tests for class \core_customfield\category_controller.
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_customfield\category_controller;
use \core_customfield\field_controller;

/**
 * Functional test for class \core_customfield\category_controller.
 * @package    core_customfield
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_customfield_category_controller_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Get generator
     * @return core_customfield_generator
     */
    protected function get_generator() : core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    public function test_constructor() {
        $c = category_controller::create(0, (object)['component' => 'core_course', 'area' => 'course', 'itemid' => 0]);
        $handler = $c->get_handler();
        $this->assertTrue($c instanceof category_controller);

        $cat = $this->get_generator()->create_category();
        $c = category_controller::create($cat->get('id'));
        $this->assertTrue($c instanceof category_controller);

        $c = category_controller::create($cat->get('id'), null, $handler);
        $this->assertTrue($c instanceof category_controller);

        $c = category_controller::create(0, $cat->to_record());
        $this->assertTrue($c instanceof category_controller);

        $c = category_controller::create(0, $cat->to_record(), $handler);
        $this->assertTrue($c instanceof category_controller);
    }

    /**
     * Test for function \core_customfield\field_controller::create() in case of wrong parameters
     */
    public function test_constructor_errors() {
        global $DB;
        $cat = $this->get_generator()->create_category();
        $catrecord = $cat->to_record();

        // Both id and record give warning.
        $c = category_controller::create($catrecord->id, $catrecord);
        $debugging = $this->getDebuggingMessages();
        $this->assertEquals(1, count($debugging));
        $this->assertEquals('Too many parameters, either id need to be specified or a record, but not both.',
            $debugging[0]->message);
        $this->resetDebugging();
        $this->assertTrue($c instanceof category_controller);

        // Retrieve non-existing data.
        try {
            category_controller::create($catrecord->id + 1);
            $this->fail('Expected exception');
        } catch (moodle_exception $e) {
            $this->assertEquals('Category not found', $e->getMessage());
            $this->assertEquals(moodle_exception::class, get_class($e));
        }

        // Missing required elements.
        try {
            category_controller::create(0, (object)['area' => 'course', 'itemid' => 0]);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters ' .
                'to initialise category_controller - unknown component', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        // Missing required elements.
        try {
            category_controller::create(0, (object)['component' => 'core_course', 'itemid' => 0]);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters ' .
                'to initialise category_controller - unknown area', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        // Missing required elements.
        try {
            category_controller::create(0, (object)['component' => 'core_course', 'area' => 'course']);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters ' .
                'to initialise category_controller - unknown itemid', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        $handler = \core_course\customfield\course_handler::create();
        // Missing required elements.
        try {
            category_controller::create(0, (object)['component' => 'x', 'area' => 'course', 'itemid' => 0], $handler);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Component of the handler ' .
                'does not match the one from the record', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        try {
            category_controller::create(0, (object)['component' => 'core_course', 'area' => 'x', 'itemid' => 0], $handler);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Area of the handler ' .
                'does not match the one from the record', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        try {
            category_controller::create(0, (object)['component' => 'core_course', 'area' => 'course', 'itemid' => 1], $handler);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Itemid of the ' .
                'handler does not match the one from the record', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }

        try {
            $user = $this->getDataGenerator()->create_user();
            category_controller::create(0, (object)['component' => 'core_course', 'area' => 'course', 'itemid' => 0,
                'contextid' => context_user::instance($user->id)->id], $handler);
            $this->fail('Expected exception');
        } catch (coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Context of the ' .
                'handler does not match the one from the record', $e->getMessage());
            $this->assertEquals(coding_exception::class, get_class($e));
        }
    }

    /**
     * Tests for behaviour of:
     * \core_customfield\category_controller::save()
     * \core_customfield\category_controller::get()
     */
    public function test_create_category() {

        // Create the category.
        $lpg = $this->get_generator();
        $categorydata            = new stdClass();
        $categorydata->name      = 'Category1';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = context_system::instance()->id;
        $category = category_controller::create(0, $categorydata);
        $category->save();
        $this->assertNotEmpty($category->get('id'));

        // Confirm record exists.
        $this->assertTrue(\core_customfield\category::record_exists($category->get('id')));

        // Confirm that base data was inserted correctly.
        $category = category_controller::create($category->get('id'));
        $this->assertSame($category->get('name'), $categorydata->name);
        $this->assertSame($category->get('component'), $categorydata->component);
        $this->assertSame($category->get('area'), $categorydata->area);
        $this->assertSame((int)$category->get('itemid'), $categorydata->itemid);
    }

    /**
     * Tests for \core_customfield\category_controller::set() behaviour.
     */
    public function test_rename_category() {
        // Create the category.
        $params = ['component' => 'core_course', 'area' => 'course', 'itemid' => 0, 'name' => 'Cat1',
            'contextid' => context_system::instance()->id];
        $c1 = category_controller::create(0, (object)$params);
        $c1->save();
        $this->assertNotEmpty($c1->get('id'));

        // Checking new name are correct updated.
        $category = category_controller::create($c1->get('id'));
        $category->set('name', 'Cat2');
        $this->assertSame('Cat2', $category->get('name'));

        // Checking new name are correct updated after save.
        $category->save();

        $category = category_controller::create($c1->get('id'));
        $this->assertSame('Cat2', $category->get('name'));
    }

    /**
     * Tests for \core_customfield\category_controller::delete() behaviour.
     */
    public function test_delete_category() {
        // Create the category.
        $lpg = $this->get_generator();
        $category0 = $lpg->create_category();
        $id0 = $category0->get('id');

        $category1 = $lpg->create_category();
        $id1 = $category1->get('id');

        $category2 = $lpg->create_category();
        $id2 = $category2->get('id');

        // Confirm that exist in the database.
        $this->assertTrue(\core_customfield\category::record_exists($id0));

        // Delete and confirm that is deleted.
        $category0->delete();
        $this->assertFalse(\core_customfield\category::record_exists($id0));

        // Confirm correct order after delete.
        // Check order after re-fetch.
        $category1 = category_controller::create($id1);
        $category2 = category_controller::create($id2);

        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
    }
}
