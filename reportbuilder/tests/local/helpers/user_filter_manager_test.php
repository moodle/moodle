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

/**
 * Unit tests for the user filter helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\user_filter_manager
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_manager_test extends advanced_testcase {

    /**
     * Helper method to return all user preferences for filters - based on the current storage backend using the same
     *
     * @return array
     */
    private function get_filter_preferences(): array {
        return array_filter(get_user_preferences(), static function(string $key): bool {
            return strpos($key, 'reportbuilder-report-') === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

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

        $values = [
            'entity:filter_name' => $value,
        ];
        user_filter_manager::set(5, $values);

        // Make sure we get the same value back.
        $this->assertEquals($values, user_filter_manager::get(5));
    }

    /**
     * Test getting filter values that once spanned multiple chunks
     */
    public function test_get_large_to_small(): void {
        $this->resetAfterTest();

        // Set a large initial filter value.
        user_filter_manager::set(5, [
            'longvalue' => str_repeat('ABCD', 1000),
        ]);

        // Sanity check, there should be 4 (because 4000 characters plus some JSON encoding requires that many chunks).
        $preferences = $this->get_filter_preferences();
        $this->assertCount(4, $preferences);

        $values = [
            'longvalue' => 'ABCD',
        ];
        user_filter_manager::set(5, $values);

        // Make sure we get the same value back.
        $this->assertEquals($values, user_filter_manager::get(5));

        // Everything should now fit in a single filter preference.
        $preferences = $this->get_filter_preferences();
        $this->assertCount(1, $preferences);
    }

    /**
     * Test getting filter values that haven't been set
     */
    public function test_get_empty(): void {
        $this->assertEquals([], user_filter_manager::get(5));
    }

    /**
     * Data provider for {@see test_reset_all}
     *
     * @return array
     */
    public static function reset_all_provider(): array {
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
     * @dataProvider reset_all_provider
     */
    public function test_reset_all(string $value): void {
        $this->resetAfterTest();

        user_filter_manager::set(5, [
            'entity:filter_name' => $value
        ]);

        $reset = user_filter_manager::reset_all(5);
        $this->assertTrue($reset);

        // We should get an empty array back.
        $this->assertEquals([], user_filter_manager::get(5));

        // All filter preferences should be removed.
        $this->assertEmpty($this->get_filter_preferences());
    }

    /**
     * Test resetting single filter values
     */
    public function test_reset_single(): void {
        $this->resetAfterTest();

        user_filter_manager::set(5, [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:other_name' => 'baz',
            'entity:other_value' => 'bax',
        ]);

        $reset = user_filter_manager::reset_single(5, 'entity:other');
        $this->assertTrue($reset);

        $this->assertEquals([
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
        ], user_filter_manager::get(5));
    }

    /**
     * Test merging filter values
     */
    public function test_merge(): void {
        $this->resetAfterTest();

        $values = [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:filter2_name' => 'tree',
            'entity:filter2_value' => 'house',
        ];

        // Make sure we get the same value back.
        user_filter_manager::set(5, $values);
        $this->assertEqualsCanonicalizing($values, user_filter_manager::get(5));

        user_filter_manager::merge(5, [
            'entity:filter_name' => 'twotimesfoo',
            'entity:filter_value' => 'twotimesbar',
        ]);

        // Make sure that both values have been changed and the other values have not been modified.
        $expected = [
            'entity:filter_name' => 'twotimesfoo',
            'entity:filter_value' => 'twotimesbar',
            'entity:filter2_name' => 'tree',
            'entity:filter2_value' => 'house',
        ];
        $this->assertEqualsCanonicalizing($expected, user_filter_manager::get(5));
    }

    /**
     * Test to get all filters from a given user
     */
    public function test_get_all_for_user(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $filtervalues1 = [
            'entity:filter_name' => 'foo',
            'entity:filter_value' => 'bar',
            'entity:other_name' => 'baz',
            'entity:other_value' => 'bax',
        ];
        user_filter_manager::set(5, $filtervalues1);

        $filtervalues2 = [
            'entity:filter_name' => 'blue',
            'entity:filter_value' => 'red',
        ];
        user_filter_manager::set(9, $filtervalues2);

        $this->setAdminUser();
        $values = user_filter_manager::get_all_for_user((int)$user->id);
        $this->assertEqualsCanonicalizing([$filtervalues1, $filtervalues2], [reset($values), end($values)]);

        // Check for a user with no filters.
        $user2 = $this->getDataGenerator()->create_user();
        $values = user_filter_manager::get_all_for_user((int)$user2->id);
        $this->assertEmpty($values);
    }
}
