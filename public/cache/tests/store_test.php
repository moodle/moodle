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

namespace core_cache;

/**
 * Unit tests for \core_cache\store functionality.
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_cache\store
 */
final class store_test extends \advanced_testcase {
    /**
     * Tests the default implementation of cache_size_details, which does some
     * complicated statistics.
     */
    public function test_cache_size_details(): void {
        // Fill a store with 100 entries of varying size.
        $store = self::create_static_store();
        for ($i = 0; $i < 100; $i++) {
            $store->set('key' . $i, str_repeat('x', $i));
        }

        // Do the statistics for 10 random picks.
        $details = $store->cache_size_details(10);
        $this->assertTrue($details->supported);
        $this->assertEquals(100, $details->items);

        // Min/max possible means if it picks the smallest/largest 10.
        $this->assertGreaterThan(22, $details->mean);
        $this->assertLessThan(115, $details->mean);
        // Min/max possible SD.
        $this->assertLessThan(49, $details->sd);
        $this->assertGreaterThan(2.8, $details->sd);
        // Lowest possible confidence margin is about 1.74.
        $this->assertGreaterThan(1.7, $details->margin);

        // Repeat the statistics for a pick of all 100 entries (exact).
        $details = $store->cache_size_details(100);
        $this->assertTrue($details->supported);
        $this->assertEquals(100, $details->items);
        $this->assertEqualsWithDelta(69.3, $details->mean, 0.1);
        $this->assertEqualsWithDelta(29.2, $details->sd, 0.1);
        $this->assertEquals(0, $details->margin);
    }

    /**
     * Creates a static store for testing.
     *
     * @return \cachestore_static Store
     */
    protected static function create_static_store(): \cachestore_static {
        require_once(__DIR__ . '/../stores/static/lib.php');
        $store = new \cachestore_static('frog');
        $definition = definition::load('zombie', [
            'mode' => store::MODE_REQUEST,
            'component' => 'phpunit',
            'area' => 'store_test',
            'simplekeys' => true,
        ]);
        $store->initialise($definition);
        return $store;
    }
}
