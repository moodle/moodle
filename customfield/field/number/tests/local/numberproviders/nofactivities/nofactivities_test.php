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

declare(strict_types=1);

namespace customfield_number\local\numberproviders\nofactivities;

use advanced_testcase;
use customfield_number\local\numberproviders\nofactivities;
use customfield_number\provider_base;

/**
 * Tests for the number of activities
 *
 * @package    customfield_number
 * @covers     \customfield_number\local\numberproviders\nofactivities
 * @copyright  2024 Ilya Tregubov <ilya.tregubov@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class nofactivities_test extends advanced_testcase {

    /**
     * Test that we can automatically calculate number of activities in courses.
     */
    public function test_recalculate(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Add some activities to the courses.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 0]);

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quizgenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $quizgenerator->create_instance(['course' => $course1->id, 'visible' => 0]);
        $quizgenerator->create_instance(['course' => $course1->id, 'visible' => 0]);

        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $forumgenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $forumgenerator->create_instance(['course' => $course1->id, 'visible' => 0]);
        $forumgenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        $assigngenerator->create_instance(['course' => $course2->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course2->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course2->id, 'visible' => 1]);

        /** @var \core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        // Create a category and field.
        $category = $generator->create_category();
        $field = $generator->create_field([
            'categoryid' => $category->get('id'),
            'type' => 'number',
            'configdata' => [
                'fieldtype' => 'customfield_number\local\numberproviders\nofactivities',
                "activitytypes" => ["assign", "forum"],
            ],
        ]);

        // Test if the provider has been added correctly.
        $providers = provider_base::get_all_providers($field);
        $this->assertNotEmpty($providers);
        $this->assertInstanceOf(nofactivities::class, $providers[0]);

        // Calculate only in course1.
        $providers[0]->recalculate((int)$course1->id);
        $course1customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course1->id]);
        $course2customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course2->id]);

        $this->assertEquals(4.0000, $course1customfield);
        $this->assertEquals(false, $course2customfield);

        // Calculate in all courses.
        $providers[0]->recalculate();
        $course1customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course1->id]);
        $course2customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course2->id]);

        $this->assertEquals(4.0000, $course1customfield);
        $this->assertEquals(3.0000, $course2customfield);

        // Delete some assign module.
        $cm = get_coursemodule_from_instance('assign', $assign1->id);
        course_delete_module($cm->id);
        $providers[0]->recalculate((int)$course1->id);
        $course1customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course1->id]);
        // Module is marked as deleted.
        $this->assertEquals(3.0000, $course1customfield);

        // Now, run the course module deletion adhoc task.
        \phpunit_util::run_all_adhoc_tasks();
        $providers[0]->recalculate((int)$course1->id);
        $course1customfield = $DB->get_field('customfield_data', 'decvalue', ['instanceid' => $course1->id]);
        $this->assertEquals(3.0000, $course1customfield);
    }

    /**
     * Test that the data record is updated/deleted when the value is recalculated
     *
     * Also test that export_value() is correct
     *
     * @return void
     */
    public function test_recalculate_change_value(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course with one activity.
        $course1 = $this->getDataGenerator()->create_course();
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        /** @var \core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        // Create a category and two fields, one with displaywhenzero, another one without.
        $category = $generator->create_category();
        $field1 = $generator->create_field([
            'categoryid' => $category->get('id'),
            'type' => 'number',
            'configdata' => [
                'fieldtype' => nofactivities::class,
                'activitytypes' => ['assign'],
                'displaywhenzero' => '0',
            ],
        ]);
        $field2 = $generator->create_field([
            'categoryid' => $category->get('id'),
            'type' => 'number',
            'configdata' => [
                'fieldtype' => nofactivities::class,
                'activitytypes' => ['assign'],
                'displaywhenzero' => '',
            ],
        ]);
        $getdata = fn(\customfield_number\field_controller $field): \customfield_number\data_controller =>
            \core_customfield\api::get_instance_fields_data([$field->get('id') => $field], (int)$course1->id)[$field->get('id')];

        // Recalculate the value of the field and assert it is set to 1 (one activity in the course).
        (new \customfield_number\task\cron())->execute();
        $data = $getdata($field1);
        $this->assertEquals(1, $data->get('decvalue'));
        $this->assertSame('1', $data->export_value());
        $data = $getdata($field2);
        $this->assertEquals(1, $data->get('decvalue'));
        $this->assertSame('1', $data->export_value());

        // Add another module, recalculate and assert the value of the field is set to 2 (two activities in the course).
        $assign2 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        (new \customfield_number\task\cron())->execute();
        $data = $getdata($field1);
        $this->assertEquals(2, $data->get('decvalue'));
        $this->assertSame('2', $data->export_value());
        $data = $getdata($field2);
        $this->assertEquals(2, $data->get('decvalue'));
        $this->assertSame('2', $data->export_value());

        // Delete both modules, recalculate.
        course_delete_module($assign1->cmid);
        course_delete_module($assign2->cmid);
        (new \customfield_number\task\cron())->execute();
        // Field1 (displaywhenzero='0') has the value zero.
        $data = $getdata($field1);
        $this->assertNotEmpty($data->get('id'));
        $this->assertEquals(0, $data->get('decvalue'));
        $this->assertSame('0', $data->export_value());
        // Field2 (displaywhenzero='') no longer has a data record and it is not displayed.
        $data = $getdata($field2);
        $this->assertEmpty($data->get('id'));
        $this->assertEquals(null, $data->get('decvalue'));
        $this->assertSame(null, $data->export_value());
    }
}
