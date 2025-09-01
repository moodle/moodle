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

namespace core\navigation;

/**
 * Tests for navigation_cache.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(navigation_cache::class)]
final class navigation_cache_test extends \advanced_testcase {
    public function test_cache__get(): void {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $this->assertTrue($cache->anysetvariable);
        $this->assertEquals($cache->notasetvariable, null);
    }

    public function test_cache__set(): void {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache->myname = 'Sam Hemelryk';
        $this->assertTrue($cache->cached('myname'));
        $this->assertSame('Sam Hemelryk', $cache->myname);
    }

    public function test_cache_cached(): void {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $this->assertTrue($cache->cached('anysetvariable'));
        $this->assertFalse($cache->cached('notasetvariable'));
    }

    public function test_cache_clear(): void {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache = clone($cache);
        $this->assertTrue($cache->cached('anysetvariable'));
        $cache->clear();
        $this->assertFalse($cache->cached('anysetvariable'));
    }

    public function test_cache_set(): void {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache->set('software', 'Moodle');
        $this->assertTrue($cache->cached('software'));
        $this->assertEquals($cache->software, 'Moodle');
    }
}
