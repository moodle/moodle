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
 * Unit tests for {@see allow_temporary_caches}.
 *
 * @package core_cache
 * @category test
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_cache\allow_temporary_caches
 */
final class allow_temporary_caches_test extends \advanced_testcase {
    /**
     * Tests whether temporary caches are allowed.
     */
    public function test_is_allowed(): void {
        // Not allowed by default.
        $this->assertFalse(allow_temporary_caches::is_allowed());

        // Allowed if we have an instance.
        $frog = new allow_temporary_caches();
        $this->assertTrue(allow_temporary_caches::is_allowed());

        // Or two instances.
        $toad = new allow_temporary_caches();
        $this->assertTrue(allow_temporary_caches::is_allowed());

        // Get rid of the instances.
        unset($frog);
        $this->assertTrue(allow_temporary_caches::is_allowed());

        // Not allowed when we get back to no instances.
        unset($toad);
        $this->assertFalse(allow_temporary_caches::is_allowed());

        // Check it works to automatically free up the instance when variable goes out of scope.
        $this->inner_is_allowed();
        $this->assertFalse(allow_temporary_caches::is_allowed());
    }

    /**
     * Function call to demonstrate that you don't need to manually unset the variable.
     */
    protected function inner_is_allowed(): void {
        $gecko = new allow_temporary_caches();
        $this->assertTrue(allow_temporary_caches::is_allowed());
    }

    /**
     * Tests that the temporary caches actually work, including normal and versioned get and set.
     */
    public function test_temporary_cache(): void {
        $this->resetAfterTest();

        // Disable the cache.
        \cache_phpunit_factory::phpunit_disable();
        try {
            // Try using the cache now - it returns false/null for everything.
            $cache = \cache::make('core', 'coursemodinfo');
            $cache->set('frog', 'ribbit');
            $this->assertFalse($cache->get('frog'));
            $cache->set_versioned('toad', 2, 'croak');
            $this->assertFalse($cache->get_versioned('toad', 2));

            // But when we allow temporary caches, it should work as normal.
            $allow = new allow_temporary_caches();
            $cache = \cache::make('core', 'coursemodinfo');
            $cache->set('frog', 'ribbit');
            $this->assertEquals('ribbit', $cache->get('frog'));
            $cache->set_versioned('toad', 2, 'croak');
            $this->assertEquals('croak', $cache->get_versioned('toad', 2));

            // Let's actually use modinfo, to check it works with locking too.
            $course = $this->getDataGenerator()->create_course();
            get_fast_modinfo($course);
        } finally {
            // You have to do this after phpunit_disable or it breaks later tests.
            factory::reset();
            factory::instance(true);
        }
    }
}
