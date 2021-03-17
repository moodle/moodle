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
 * Tests for class core_course_category methods invoking hooks.
 *
 * @package    core_course
 * @category   test
 * @copyright  2020 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\core_course;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/tests/fixtures/mock_hooks.php');

use PHPUnit\Framework\MockObject\MockObject;

/**
 * Functional test for class core_course_category methods invoking hooks.
 */
class core_course_category_hooks_testcase extends \advanced_testcase {

    protected function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Provides mocked category configured for named callback function.
     *
     * get_plugins_callback_function will return callable prefixed with `tool_unittest_`,
     * the actual callbacks are defined in mock_hooks.php fixture file.
     *
     * @param core_course_category $category Category to mock
     * @param string $callback Callback function used in method we test.
     * @return MockObject
     */
    public function get_mock_category(\core_course_category $category, string $callback = '') : MockObject {
        // Setup mock object for \core_course_category.
        // Disable original constructor, since we can't use it directly since it is private.
        $mockcategory = $this->getMockBuilder(\core_course_category::class)
            ->onlyMethods(['get_plugins_callback_function'])
            ->disableOriginalConstructor()
            ->getMock();

        // Define get_plugins_callback_function use and return value.
        if (!empty($callback)) {
            $mockcategory->method('get_plugins_callback_function')
                ->with($this->equalTo($callback))
                ->willReturn(['tool_unittest_' . $callback]);
        }

        // Modify constructor visibility and invoke mock object with real object.
        // This is used to overcome private constructor.
        $reflected = new \ReflectionClass(\core_course_category::class);
        $constructor = $reflected->getConstructor();
        $constructor->setAccessible(true);
        $constructor->invoke($mockcategory, $category->get_db_record());

        return $mockcategory;
    }

    public function test_can_course_category_delete_hook() {
        $category1 = \core_course_category::create(array('name' => 'Cat1'));
        $category2 = \core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = \core_course_category::create(array('name' => 'Cat3'));

        $mockcategory2 = $this->get_mock_category($category2, 'can_course_category_delete');

        // Add course to mocked clone of category2.
        $course1 = $this->getDataGenerator()->create_course(array('category' => $mockcategory2->id));

        // Now configure fixture to return false for the callback.
        mock_hooks::set_can_course_category_delete_return(false);
        $this->assertFalse($mockcategory2->can_delete_full($category3->id));

        // Now configure fixture to return true for the callback.
        mock_hooks::set_can_course_category_delete_return(true);
        $this->assertTrue($mockcategory2->can_delete_full($category3->id));

        // Verify passed arguments.
        $arguments = mock_hooks::get_calling_arguments();
        $this->assertCount(1, $arguments);

        // Argument 1 is the same core_course_category instance.
        $argument = array_shift($arguments);
        $this->assertSame($mockcategory2, $argument);
    }

    public function test_can_course_category_delete_move_hook() {
        $category1 = \core_course_category::create(array('name' => 'Cat1'));
        $category2 = \core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = \core_course_category::create(array('name' => 'Cat3'));

        $mockcategory2 = $this->get_mock_category($category2, 'can_course_category_delete_move');

        // Add course to mocked clone of category2.
        $course1 = $this->getDataGenerator()->create_course(array('category' => $mockcategory2->id));

        // Now configure fixture to return false for the callback.
        mock_hooks::set_can_course_category_delete_move_return(false);
        $this->assertFalse($mockcategory2->can_move_content_to($category3->id));

        // Now configure fixture to return true for the callback.
        mock_hooks::set_can_course_category_delete_move_return(true);
        $this->assertTrue($mockcategory2->can_move_content_to($category3->id));

        // Verify passed arguments.
        $arguments = mock_hooks::get_calling_arguments();
        $this->assertCount(2, $arguments);

        // Argument 1 is the same core_course_category instance.
        $argument = array_shift($arguments);
        $this->assertSame($mockcategory2, $argument);

        // Argument 2 is referring to category 3.
        $argument = array_shift($arguments);
        $this->assertInstanceOf(\core_course_category::class, $argument);
        $this->assertEquals($category3->id, $argument->id);
    }

    public function test_pre_course_category_delete_hook() {
        $category1 = \core_course_category::create(array('name' => 'Cat1'));
        $category2 = \core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));

        $mockcategory2 = $this->get_mock_category($category2, 'pre_course_category_delete');
        $mockcategory2->delete_full();

        // Verify passed arguments.
        $arguments = mock_hooks::get_calling_arguments();
        $this->assertCount(1, $arguments);

        // Argument 1 is the category object.
        $argument = array_shift($arguments);
        $this->assertEquals($mockcategory2->get_db_record(), $argument);
    }

    public function test_pre_course_category_delete_move_hook() {
        $category1 = \core_course_category::create(array('name' => 'Cat1'));
        $category2 = \core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = \core_course_category::create(array('name' => 'Cat3'));

        $mockcategory2 = $this->get_mock_category($category2, 'pre_course_category_delete_move');

        // Add course to mocked clone of category2.
        $course1 = $this->getDataGenerator()->create_course(array('category' => $mockcategory2->id));

        $mockcategory2->delete_move($category3->id);

        // Verify passed arguments.
        $arguments = mock_hooks::get_calling_arguments();
        $this->assertCount(2, $arguments);

        // Argument 1 is the same core_course_category instance.
        $argument = array_shift($arguments);
        $this->assertSame($mockcategory2, $argument);

        // Argument 2 is referring to category 3.
        $argument = array_shift($arguments);
        $this->assertInstanceOf(\core_course_category::class, $argument);
        $this->assertEquals($category3->id, $argument->id);
    }

    public function test_get_course_category_contents_hook() {
        $category1 = \core_course_category::create(array('name' => 'Cat1'));
        $category2 = \core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));

        $mockcategory2 = $this->get_mock_category($category2);

        // Define get_plugins_callback_function use in the mock, it is called twice for different callback in the form.
        $mockcategory2->expects($this->exactly(2))
            ->method('get_plugins_callback_function')
            ->withConsecutive(
                [$this->equalTo('can_course_category_delete')],
                [$this->equalTo('get_course_category_contents')]
            )
            ->willReturn(
                ['tool_unittest_can_course_category_delete'],
                ['tool_unittest_get_course_category_contents']
            );

        // Now configure fixture to return string for the callback.
        $content = 'Bunch of test artefacts';
        mock_hooks::set_get_course_category_contents_return($content);

        $mform = new \core_course_deletecategory_form(null, $mockcategory2);
        $this->expectOutputRegex("/<li>$content<\/li>/");
        $mform->display();

        // Verify passed arguments.
        $arguments = mock_hooks::get_calling_arguments();
        $this->assertCount(1, $arguments);

        // Argument 1 is the same core_course_category instance.
        $argument = array_shift($arguments);
        $this->assertSame($mockcategory2, $argument);
    }
}
