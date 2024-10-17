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

namespace core_reportbuilder\local\report;

use advanced_testcase;
use coding_exception;
use context_system;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\system_report_available;
use core_reportbuilder\system_report_factory;
use lang_string;
use ReflectionClass;

/**
 * Unit tests for report base class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\report\base
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class base_test extends advanced_testcase {

    /**
     * Load required class
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");
        parent::setUpBeforeClass();
    }

    /**
     * Test for add_base_condition_simple
     */
    public function test_add_base_condition_simple(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->add_base_condition_simple('username', 'admin');
        [$where, $params] = $systemreport->get_base_condition();
        $this->assertStringMatchesFormat('username = :%a', $where);
        $this->assertEqualsCanonicalizing(['admin'], array_values($params));
    }

    /**
     * Test for add_base_condition_simple null
     */
    public function test_add_base_condition_simple_null(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->add_base_condition_simple('username', null);
        [$where, $params] = $systemreport->get_base_condition();
        $this->assertEquals('username IS NULL', $where);
        $this->assertEmpty($params);
    }

    /**
     * Test for adding SQL base condition to a report
     */
    public function test_add_base_condition_sql(): void {
        $this->resetAfterTest();

        $parameter = database::generate_param_name();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->add_base_condition_sql("username = :{$parameter}", [$parameter => 'admin']);

        [$where, $params] = $systemreport->get_base_condition();
        $this->assertEquals("username = :{$parameter}", $where);
        $this->assertEquals([$parameter => 'admin'], $params);
    }

    /**
     * Test for adding multiple SQL base condition to a report
     */
    public function test_add_base_condition_sql_multiple(): void {
        $this->resetAfterTest();

        [$paramusername, $paramemail] = database::generate_param_names(2);

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->add_base_condition_sql("username = :{$paramusername}", [$paramusername => 'admin']);
        $systemreport->add_base_condition_sql("email = :{$paramemail}", [$paramemail => 'admin@example.com']);

        [$where, $params] = $systemreport->get_base_condition();
        $this->assertEquals("username = :{$paramusername} AND email = :{$paramemail}", $where);
        $this->assertEquals([$paramusername => 'admin', $paramemail => 'admin@example.com'], $params);
    }

    /**
     * Test for adding empty SQL base condition to a report
     */
    public function test_add_base_condition_sql_empty_clause(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->add_base_condition_sql('username IS NOT NULL');
        $systemreport->add_base_condition_sql('');

        [$where, $params] = $systemreport->get_base_condition();
        $this->assertEquals("username IS NOT NULL", $where);
        $this->assertEmpty($params);
    }

    /**
     * Test for adding SQL base condition to a report with invalid parameter
     */
    public function test_add_base_condition_sql_invalid_parameter(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid parameter names');
        $systemreport->add_base_condition_sql("username = :param", ['param' => 'admin']);
    }

    /**
     * Test getting report base conditions, where none have been set
     */
    public function test_get_base_condition_default(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        [$where, $params] = $systemreport->get_base_condition();
        $this->assertEmpty($where);
        $this->assertEmpty($params);
    }

    /**
     * Test for get_filter_instances
     */
    public function test_get_filter_instances(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance(),
            '', '', 0, ['withfilters' => true]);
        $filters = $systemreport->get_filter_instances();
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(\core_reportbuilder\local\filters\text::class, reset($filters));
    }

    /**
     * Test for set_downloadable
     */
    public function test_set_downloadable(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->set_downloadable(true, 'testfilename');
        $this->assertTrue($systemreport->is_downloadable());
        $this->assertEquals('testfilename', $systemreport->get_downloadfilename());

        $systemreport->set_downloadable(false, 'anothertestfilename');
        $this->assertFalse($systemreport->is_downloadable());
        $this->assertEquals('anothertestfilename', $systemreport->get_downloadfilename());
    }

    /**
     * Test for get_context
     */
    public function test_get_context(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $this->assertEquals(context_system::instance(), $systemreport->get_context());

        $course = $this->getDataGenerator()->create_course();
        $contextcourse = \context_course::instance($course->id);
        $systemreport2 = system_report_factory::create(system_report_available::class, $contextcourse);
        $this->assertEquals($contextcourse, $systemreport2->get_context());
    }

    /**
     * Test entity annotation
     */
    public function test_annotate_entity(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        $method = (new ReflectionClass($systemreport))->getMethod('annotate_entity');

        $method->invoke($systemreport, 'test', new lang_string('yes'));
        $this->assertEquals(new lang_string('yes'), $systemreport->get_entity_title('test'));
    }

    /**
     * Test entity annotation for invalid entity name
     */
    public function test_annotate_entity_invalid(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        $method = (new ReflectionClass($systemreport))->getMethod('annotate_entity');

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Entity name must be comprised of alphanumeric character, underscore or dash');
        $method->invoke($systemreport, '', new lang_string('yes'));
    }

    /**
     * Test entity annotation for duplicated entity name
     */
    public function test_annotate_entity_duplicate(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());

        $method = (new ReflectionClass($systemreport))->getMethod('annotate_entity');

        $method->invoke($systemreport, 'test', new lang_string('yes'));

        // Adding a second time with the same name should trigger exception.
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Duplicate entity name (test)');
        $method->invoke($systemreport, 'test', new lang_string('no'));
    }

    /**
     * Test for get_column
     */
    public function test_get_column(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $column = $systemreport->get_column('user:username');
        $this->assertInstanceOf(column::class, $column);

        $column = $systemreport->get_column('user:nonexistingcolumn');
        $this->assertNull($column);
    }

    /**
     * Test for get_filter
     */
    public function test_get_filter(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance(),
            '', '', 0, ['withfilters' => true]);
        $filter = $systemreport->get_filter('user:username');
        $this->assertInstanceOf(filter::class, $filter);

        $filter = $systemreport->get_filter('user:nonexistingfilter');
        $this->assertNull($filter);
    }

    /**
     * Test for get_report_persistent
     */
    public function test_get_report_persistent(): void {
        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $persistent = $systemreport->get_report_persistent();
        $this->assertEquals(system_report_available::class, $persistent->get('source'));
    }
}
