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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\models\user_filter;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the user filter helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\user_filter_manager
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_filter_manager_test extends advanced_testcase {
    /**
     * Data provider for {@see test_get}
     *
     * @return array
     */
    public static function get_provider(): array {
        return [
            'Small value' => ['foo'],
            'Large value' => [str_repeat('A', 4000)],
            'Empty value' => [''],
        ];
    }

    /**
     * Test getting filter values
     *
     * @param string $value
     *
     * @dataProvider get_provider
     */
    public function test_get(string $value): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $values = [
            'entity:filter_name' => $value,
        ];
        user_filter_manager::set($report->get('id'), $values);

        // Make sure we get the same value back.
        $this->assertEquals($values, user_filter_manager::get($report->get('id')));
    }

    /**
     * Test getting filter values that haven't been set
     */
    public function test_get_empty(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->assertEquals([], user_filter_manager::get($report->get('id')));
    }

    /**
     * Data provider for {@see test_reset}
     *
     * @return array
     */
    public static function reset_provider(): array {
        return [
            'Small value' => ['foo'],
            'Large value' => [str_repeat('A', 4000)],
            'Empty value' => [''],
        ];
    }

    /**
     * Test resetting all filter values
     *
     * @param string $value
     *
     * @dataProvider reset_provider
     */
    public function test_reset(string $value): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        user_filter_manager::set($report->get('id'), [
            'entity:filter_name' => $value,
        ]);

        $reset = user_filter_manager::reset($report->get('id'));
        $this->assertTrue($reset);

        // We should get an empty array back.
        $this->assertEquals([], user_filter_manager::get($report->get('id')));

        // All filter preferences should be removed.
        $this->assertFalse(user_filter::get_record(['reportid' => $report->get('id')]));
    }

    /**
     * Test resetting single filter values
     */
    public function test_reset_single(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        user_filter_manager::set($report->get('id'), [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:other_name' => 'baz',
            'entity:other_value' => 'bax',
        ]);

        $reset = user_filter_manager::reset_single($report->get('id'), 'entity:other');
        $this->assertDebuggingCalled();
        $this->assertTrue($reset);

        $this->assertEquals([
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
        ], user_filter_manager::get($report->get('id')));
    }

    /**
     * Test merging filter values
     */
    public function test_merge(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        user_filter_manager::set($report->get('id'), [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:filter2_name' => 'tree',
            'entity:filter2_value' => 'house',
        ]);

        // Make sure that both values have been changed and the other values have not been modified.
        user_filter_manager::merge($report->get('id'), [
            'entity:filter_name' => 'twotimesfoo',
            'entity:filter_value' => 'twotimesbar',
        ]);
        $this->assertDebuggingCalled();
        $this->assertEqualsCanonicalizing([
            'entity:filter_name' => 'twotimesfoo',
            'entity:filter_value' => 'twotimesbar',
            'entity:filter2_name' => 'tree',
            'entity:filter2_value' => 'house',
        ], user_filter_manager::get($report->get('id')));
    }

    /**
     * Test to get all filters from a given user
     */
    public function test_get_all_for_user(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $reportone = $generator->create_report(['name' => 'Report 1', 'source' => users::class]);
        $reporttwo = $generator->create_report(['name' => 'Report 2', 'source' => users::class]);

        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();

        $reportonefilter = [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:other_name' => 'baz',
            'entity:other_value' => 'bax',
        ];
        user_filter_manager::set($reportone->get('id'), $reportonefilter, (int) $userone->id);

        $reporttwofilter = [
            'entity:filter_name' => 'blue',
            'entity:filter_value' => 'red',
        ];
        user_filter_manager::set($reporttwo->get('id'), $reporttwofilter, (int) $userone->id);

        // First user has filters in two reports.
        $useronefilters = user_filter_manager::get_all_for_user((int) $userone->id);
        $this->assertDebuggingCalled();
        $this->assertEqualsCanonicalizing([$reportonefilter, $reporttwofilter], $useronefilters);

        // Check for a user with no filters.
        $usertwofilters = user_filter_manager::get_all_for_user((int) $usertwo->id);
        $this->assertDebuggingCalled();
        $this->assertEmpty($usertwofilters);
    }
}
