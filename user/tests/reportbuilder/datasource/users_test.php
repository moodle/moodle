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

namespace core_user\reportbuilder\datasource;

use core_collator;
use core_reportbuilder_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\tags;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for users datasource
 *
 * @package     core_user
 * @covers      \core_user\reportbuilder\datasource\users
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'Charles']);
        $user3 = $this->getDataGenerator()->create_user(['firstname' => 'Brian']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        // Default columns are fullname, username, email. Results are sorted by the fullname.
        [$adminrow, $userrow1, $userrow2] = array_map('array_values', $content);

        $this->assertEquals(['Admin User', 'admin', 'admin@example.com'], $adminrow);
        $this->assertEquals([fullname($user3), $user3->username, $user3->email], $userrow1);
        $this->assertEquals([fullname($user2), $user2->username, $user2->email], $userrow2);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Zoe', 'interests' => ['Horses']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // User.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);

        // Tags.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:namewithlink']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        // Consistent order by firstname, just in case.
        core_collator::asort_array_of_arrays_by_key($content, 'c0_firstname');
        $content = array_values($content);

        [$adminrow, $userrow] = array_map('array_values', $content);

        $this->assertEquals('Admin', $adminrow[0]);
        $this->assertEmpty($adminrow[1]);
        $this->assertEmpty($adminrow[2]);

        $this->assertEquals('Zoe', $userrow[0]);
        $this->assertEquals('Horses', $userrow[1]);
        $this->assertStringContainsString('Horses', $userrow[2]);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public function datasource_filters_provider(): array {
        return [
            // Tags.
            'Filter tag name' => ['tag:name', [
                'tag:name_operator' => tags::EQUAL_TO,
                'tag:name_value' => [-1],
            ], false],
            'Filter tag name not empty' => ['tag:name', [
                'tag:name_operator' => tags::NOT_EMPTY,
            ], true],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(
        string $filtername,
        array $filtervalues,
        bool $expectmatch
    ): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['interests' => ['Horses']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Tasks', 'source' => users::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals($user->username, reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test datasource
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text', 'name' => 'Hi', 'shortname' => 'hi']);
        $user = $this->getDataGenerator()->create_user(['profile_field_hi' => 'Hello']);

        $this->datasource_stress_test_columns(users::class);
        $this->datasource_stress_test_columns_aggregation(users::class);
        $this->datasource_stress_test_conditions(users::class, 'user:username');
    }
}
