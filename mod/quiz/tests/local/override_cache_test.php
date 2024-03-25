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

namespace mod_quiz\local;

/**
 * Cache manager tests for quiz overrides
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_quiz\local\override_cache
 */
final class override_cache_test extends \advanced_testcase {
    /**
     * Tests CRUD functions of the override_cache
     */
    public function test_crud(): void {
        // Cache is normally protected, but for testing we reflect it and put test data into it.
        $overridecache = new override_cache(0);
        $reflection = new \ReflectionClass($overridecache);

        $getcache = $reflection->getMethod('get_cache');
        $cache = $getcache->invoke($overridecache);

        $getuserkey = $reflection->getMethod('get_user_cache_key');

        $getgroupkey = $reflection->getMethod('get_group_cache_key');

        $dummydata = (object)[
            'userid' => 1234,
        ];

        // Set some data.
        $cache->set($getuserkey->invoke($overridecache, 123), $dummydata);
        $cache->set($getgroupkey->invoke($overridecache, 456), $dummydata);

        // Get the data back.
        $this->assertEquals($dummydata, $overridecache->get_cached_user_override(123));
        $this->assertEquals($dummydata, $overridecache->get_cached_group_override(456));

        // Delete.
        $overridecache->clear_for_user(123);
        $overridecache->clear_for_group(456);

        $this->assertEmpty($overridecache->get_cached_user_override(123));
        $this->assertEmpty($overridecache->get_cached_group_override(456));

        // Put some data back.
        $cache->set($getuserkey->invoke($overridecache, 123), $dummydata);
        $cache->set($getgroupkey->invoke($overridecache, 456), $dummydata);

        // Clear it.
        $overridecache->clear_for(123, 456);
        $this->assertEmpty($overridecache->get_cached_user_override(123));
        $this->assertEmpty($overridecache->get_cached_group_override(456));

        // Put some data back.
        $cache->set($getuserkey->invoke($overridecache, 123), 'testuser');
        $cache->set($getgroupkey->invoke($overridecache, 456), 'testgroup');

        // Purge it.
        \cache_helper::purge_by_event(override_cache::INVALIDATION_USERDATARESET);
        $this->assertEmpty($overridecache->get_cached_user_override(123));
        $this->assertEmpty($overridecache->get_cached_group_override(456));
    }
}
