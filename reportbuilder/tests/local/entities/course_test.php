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

namespace core_reportbuilder\local\entities;

use advanced_testcase;
use coding_exception;
use context_system;
use lang_string;
use ReflectionClass;
use core_reportbuilder\course_entity_report;
use core_reportbuilder\manager;
use core_reportbuilder\testable_system_report_table;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\helpers\user_filter_manager;

/**
 * Unit tests for course entity
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\entities\base
 * @covers      \core_reportbuilder\local\entities\course
 * @covers      \core_reportbuilder\local\helpers\custom_fields
 * @covers      \core_reportbuilder\local\report\base
 * @covers      \core_reportbuilder\system_report
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_test extends advanced_testcase {

    /**
     * Load required classes
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/course/lib.php");
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/testable_system_report_table.php");
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/course_entity_report.php");
    }

    /**
     * Generate courses for the tests
     */
    public function generate_courses(): array {
        $coursecategory1 = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course([
            'fullname' => 'Course 1',
            'shortname' => 'C1',
            'idnumber' => 'IDNumber1',
            'visible' => 1,
            'startdate' => 289308600,
            'enddate' => 3445023600,
            'category' => $coursecategory1->id,
            'groupmode' => NOGROUPS,
            'enablecompletion' => 1,
            'downloadcontent' => DOWNLOAD_COURSE_CONTENT_DISABLED,
            'format' => 'topics',
            'calendartype' => 'Gregorian',
            'theme' => 'afterburner',
            'lang' => 'en',
        ]);

        $coursecategory2 = $this->getDataGenerator()->create_category();
        $course2 = $this->getDataGenerator()->create_course([
            'fullname' => 'Course 2',
            'shortname' => 'C2',
            'idnumber' => 'IDNumber2',
            'visible' => 0,
            'startdate' => 1614726000,
            'enddate' => 1961881200,
            'category' => $coursecategory2->id,
            'groupmode' => SEPARATEGROUPS,
            'enablecompletion' => 0,
            'downloadcontent' => DOWNLOAD_COURSE_CONTENT_ENABLED,
            'format' => 'topics',
            'calendartype' => 'Gregorian',
            'theme' => 'afterburner',
            'lang' => 'es',
        ]);

        return [$coursecategory1, $course1, $coursecategory2, $course2];
    }

    /**
     * Test callbacks are correctly applied for those columns using them
     */
    public function test_get_columns_with_callbacks(): void {
        $this->resetAfterTest();

        [$coursecategory1, $course1] = $this->generate_courses();
        $testdate = time();

        // Add some customfields to the course.
        $cfgenerator = self::getDataGenerator()->get_plugin_generator('core_customfield');
        $params = [
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
            'contextid' => context_system::instance()->id
        ];
        $category = $cfgenerator->create_category($params);
        $field1 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Customfield text 1', 'shortname' => 'cf1']);
        $cfgenerator->add_instance_data($field1, (int)$course1->id, 'Do. Or do not. There is no try');
        $field2 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Customfield text 2', 'shortname' => 'cf2']);
        $cfgenerator->add_instance_data($field2, (int)$course1->id, 'Chewie, we are home');
        $field3 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'checkbox', 'name' => 'Customfield checkbox', 'shortname' => 'cf3']);
        $cfgenerator->add_instance_data($field3, (int)$course1->id, 1);
        $field4 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'date', 'name' => 'Customfield date', 'shortname' => 'cf4']);
        $cfgenerator->add_instance_data($field4, (int)$course1->id, $testdate);
        $field5 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'select', 'name' => 'Customfield menu', 'shortname' => 'cf5',
                'configdata' => ['defaultvalue' => 'Option A', 'options' => "Option A\nOption B\nOption C"]]);
        // Select option C for course1 (options are counted starting from one).
        $cfgenerator->add_instance_data($field5, (int)$course1->id, 3);

        $tablerows = $this->get_report_table_rows();
        $courserows = array_filter($tablerows, static function(array $row) use ($course1): bool {
            return $row['shortname'] === $course1->shortname;
        });
        $courserow = reset($courserows);

        $this->assertEquals('Course 1', $courserow['fullname']);
        $this->assertEquals('C1', $courserow['shortname']);
        $this->assertEquals('IDNumber1', $courserow['idnumber']);
        $this->assertEquals('Yes', $courserow['visible']);
        $this->assertEquals(userdate(289308600), $courserow['startdate']);
        $this->assertEquals(userdate(3445023600), $courserow['enddate']);
        $this->assertEquals('No groups', $courserow['groupmode']);
        $this->assertEquals('Yes', $courserow['enablecompletion']);
        $this->assertEquals('No', $courserow['downloadcontent']);
        $this->assertEquals('Topics format', $courserow['format']);
        $this->assertEquals('Gregorian', $courserow['calendartype']);
        $this->assertEquals('afterburner', $courserow['theme']);
        $this->assertEquals(get_string_manager()->get_list_of_translations()['en'], $courserow['lang']);
        $expected = '<a href="https://www.example.com/moodle/course/view.php?id=' . $course1->id . '">Course 1</a>';
        $this->assertEquals($expected, $courserow['coursefullnamewithlink']);
        $expected = '<a href="https://www.example.com/moodle/course/view.php?id=' . $course1->id . '">C1</a>';
        $this->assertEquals($expected, $courserow['courseshortnamewithlink']);
        $expected = '<a href="https://www.example.com/moodle/course/view.php?id=' . $course1->id . '">IDNumber1</a>';
        $this->assertEquals($expected, $courserow['courseidnumberewithlink']);
        $this->assertEquals('Do. Or do not. There is no try', $courserow['customfield_cf1']);
        $this->assertEquals('Chewie, we are home', $courserow['customfield_cf2']);
        $this->assertEquals('Yes', $courserow['customfield_cf3']);
        $this->assertEquals(userdate($testdate), $courserow['customfield_cf4']);
        $this->assertEquals('Option C', $courserow['customfield_cf5']);
    }

    /**
     * Test filtering report by course fields
     */
    public function test_filters(): void {
        $this->resetAfterTest();

        [$coursecategory1] = $this->generate_courses();

        // Filter by fullname field.
        $filtervalues = [
            'course:fullname_operator' => text::IS_EQUAL_TO,
            'course:fullname_value' => 'Course 1',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 1',
        ], array_column($tablerows, 'fullname'));

        // Filter by shortname field.
        $filtervalues = [
            'course:shortname_operator' => text::IS_EQUAL_TO,
            'course:shortname_value' => 'C1',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 1',
        ], array_column($tablerows, 'fullname'));

        // Filter by idnumber field.
        $filtervalues = [
            'course:idnumber_operator' => text::IS_EQUAL_TO,
            'course:idnumber_value' => 'IDNumber2',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 2',
        ], array_column($tablerows, 'fullname'));

        // Filter by visible field.
        $filtervalues = [
            'course:visible_operator' => boolean_select::NOT_CHECKED,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 2',
        ], array_column($tablerows, 'fullname'));

        // Filter by startdate field.
        $filtervalues = [
            'course:startdate_operator' => date::DATE_RANGE,
            'course:startdate_from' => 289135800,
            'course:startdate_to' => 289740600,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 1',
        ], array_column($tablerows, 'fullname'));

        // Filter by group mode field.
        $filtervalues = [
            'course:groupmode_operator' => select::EQUAL_TO,
            'course:groupmode_value' => SEPARATEGROUPS,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 2',
        ], array_column($tablerows, 'fullname'));

        // Filter by enable completion field.
        $filtervalues = [
            'course:enablecompletion_operator' => boolean_select::CHECKED,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Course 1',
        ], array_column($tablerows, 'fullname'));
    }

    /**
     * Test filtering report by course customfield
     */
    public function test_customfield_text_filter(): void {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course([
            'fullname' => 'Philosophy and Superheroes',
            'shortname' => 'PS1',
        ]);
        $course2 = $this->getDataGenerator()->create_course([
            'fullname' => 'The game of Mathematics',
            'shortname' => 'GM1',
        ]);

        // Add some customfields to the course.
        $cfgenerator = self::getDataGenerator()->get_plugin_generator('core_customfield');
        $params = [
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
            'contextid' => context_system::instance()->id
        ];
        $category = $cfgenerator->create_category($params);
        $field = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'text', 'name' => 'Customfield text 1', 'shortname' => 'cf']);
        $cfgenerator->add_instance_data($field, (int)$course1->id, 'Leia Organa');
        $cfgenerator->add_instance_data($field, (int)$course2->id, 'Han Solo');
        $field5 = $cfgenerator->create_field(
            ['categoryid' => $category->get('id'), 'type' => 'select', 'name' => 'Customfield menu', 'shortname' => 'cf5',
                'configdata' => ['defaultvalue' => 'Option A', 'options' => "Option A\nOption B\nOption C"]]);
        $cfgenerator->add_instance_data($field5, (int)$course1->id, 3);
        $cfgenerator->add_instance_data($field5, (int)$course2->id, 2);

        $filtervalues = [
            'course:customfield_cf_operator' => text::IS_EQUAL_TO,
            'course:customfield_cf_value' => 'Leia Organa',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Philosophy and Superheroes',
        ], array_column($tablerows, 'fullname'));

        $filtervalues = [
            'course:customfield_cf_operator' => text::IS_EQUAL_TO,
            'course:customfield_cf_value' => 'Han Solo',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'The game of Mathematics',
        ], array_column($tablerows, 'fullname'));

        // Filter by menu customfield.
        $filtervalues = [
            'course:customfield_cf5_operator' => select::EQUAL_TO,
            'course:customfield_cf5_value' => 3, // Option C.
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Philosophy and Superheroes',
        ], array_column($tablerows, 'fullname'));

        // Filter by course customfield that doesn't exist.
        $filtervalues = [
            'course:customfield_cf_operator' => text::IS_EQUAL_TO,
            'course:customfield_cf_value' => 'Luke Skywalker',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEmpty($tablerows);
    }

    /**
     * Helper method to create the report, and return it's rows
     *
     * @param array $filtervalues
     * @return array
     */
    private function get_report_table_rows(array $filtervalues = []): array {
        $report = manager::create_report_persistent((object) [
            'type' => course_entity_report::TYPE_SYSTEM_REPORT,
            'source' => course_entity_report::class,
        ]);

        user_filter_manager::set($report->get('id'), $filtervalues);

        return testable_system_report_table::create($report->get('id'), [])->get_table_rows();
    }

    /**
     * Test entity table alias
     */
    public function test_table_alias(): void {
        $courseentity = new course();

        $this->assertEquals('c', $courseentity->get_table_alias('course'));

        $courseentity->set_table_alias('course', 'newalias');
        $this->assertEquals('newalias', $courseentity->get_table_alias('course'));
    }

    /**
     * Test for invalid get table alias
     */
    public function test_get_table_alias_invalid(): void {
        $courseentity = new course();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid table name (nonexistingalias)');
        $courseentity->get_table_alias('nonexistingalias');
    }

    /**
     * Test invalid entity set table alias
     */
    public function test_set_table_alias_invalid(): void {
        $courseentity = new course();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: Invalid table name (nonexistent)');
        $courseentity->set_table_alias('nonexistent', 'newalias');
    }

    /**
     * Test entity name
     */
    public function test_name(): void {
        $courseentity = new course();

        $this->assertEquals('course', $courseentity->get_entity_name());

        $courseentity->set_entity_name('newentityname');
        $this->assertEquals('newentityname', $courseentity->get_entity_name());
    }

    /**
     * Test invalid entity name
     */
    public function test_name_invalid(): void {
        $courseentity = new course();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Entity name must be comprised of alphanumeric character, underscore or dash');
        $courseentity->set_entity_name('');
    }

    /**
     * Test entity title
     */
    public function test_title(): void {
        $courseentity = new course();

        $this->assertEquals(new lang_string('entitycourse', 'core_reportbuilder'), $courseentity->get_entity_title());

        $newtitle = new lang_string('fullname');
        $courseentity->set_entity_title($newtitle);
        $this->assertEquals($newtitle, $courseentity->get_entity_title());
    }

    /**
     * Test adding single join
     */
    public function test_add_join(): void {
        $courseentity = (new course())
            ->set_table_alias('course', 'c1');

        $tablejoin = "JOIN {course} c2 ON c2.id = c1.id";
        $courseentity->add_join($tablejoin);

        $method = (new ReflectionClass(course::class))->getMethod('get_joins');
        $method->setAccessible(true);
        $this->assertEquals([$tablejoin], $method->invoke($courseentity));
    }

    /**
     * Test adding multiple join
     */
    public function test_add_joins(): void {
        $courseentity = (new course())
            ->set_table_alias('course', 'c1');

        $tablejoins = [
            "JOIN {course} c2 ON c2.id = c1.id",
            "JOIN {course} c3 ON c3.id = c1.id",
        ];
        $courseentity->add_joins($tablejoins);

        $method = (new ReflectionClass(course::class))->getMethod('get_joins');
        $method->setAccessible(true);
        $this->assertEquals($tablejoins, $method->invoke($courseentity));
    }

    /**
     * Test for invalid get column
     */
    public function test_get_column_invalid(): void {
        $courseentity = new course();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid column name (nonexistingcolumn)');
        $courseentity->get_column('nonexistingcolumn');
    }

    /**
     * Test for invalid get filter
     */
    public function test_get_filter_invalid(): void {
        $courseentity = new course();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Coding error detected, it must be fixed by a programmer: ' .
            'Invalid filter name (nonexistingfilter)');
        $courseentity->get_filter('nonexistingfilter');
    }
}
