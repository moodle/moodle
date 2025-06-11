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

/**
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

class block_quickmail_cache_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_gets_values() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('foo');

        $this->assertEquals('bar', $value);

        $value = block_quickmail_cache::store('qm_msg_deliv_count')->get('things');

        $this->assertEquals([
            'foo',
            'bar',
        ], $value);
    }

    public function test_returns_missing_values_as_null() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('missing');

        $this->assertNull($value);
    }

    public function test_gets_values_with_default() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('missing');

        $this->assertNull($value);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('missing', 'this!');

        $this->assertEquals('this!', $value);
    }

    public function test_gets_values_with_closure_default() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('missing', function() {
            $one = 1;
            $two = 2;
            $three = 3;

            return $one + $two + $three;
        });

        $this->assertEquals(6, $value);
    }

    public function test_checks_whether_exists_in_cache() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');

        $this->assertFalse($value);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->check('foo');

        $this->assertTrue($value);
    }

    public function test_puts_values_in_cache() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertFalse($result);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->put('missing', 'something');
        $this->assertEquals('something', $value);

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertTrue($result);
    }

    public function test_puts_closure_result_values_in_cache() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertFalse($result);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->put('missing', function() {
            $n = 0;

            foreach (range(1, 4) as $i) {
                $n += $i;
            }

            return $n;
        });
        $this->assertEquals(10, $value);

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertTrue($result);
    }

    public function test_adds_values_only_if_not_exist() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('foo');
        $this->assertEquals('bar', $value);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->add('foo', 'different');
        $this->assertEquals('bar', $value);
    }

    public function test_remembers_if_doesnt_have() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->remember('foo', 'this');

        $this->assertEquals('bar', $value);

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertFalse($result);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->remember('missing', 'this!');

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertTrue($result);

        $this->assertEquals('this!', $value);
    }

    public function test_remembers_closure_values_if_doesnt_have() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->remember('foo', 'this');

        $this->assertEquals('bar', $value);

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertFalse($result);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->remember('missing', function() {
            $n = 0;

            foreach (range(1, 4) as $i) {
                $n += $i;
            }

            return $n;
        });

        $result = block_quickmail_cache::store('qm_msg_recip_count')->check('missing');
        $this->assertTrue($result);

        $this->assertEquals(10, $value);
    }

    public function test_removes_value_from_cache() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->get('foo');
        $this->assertEquals('bar', $value);

        block_quickmail_cache::store('qm_msg_recip_count')->forget('foo');

        $value = block_quickmail_cache::store('qm_msg_recip_count')->check('foo');
        $this->assertFalse($value);
    }

    public function test_pulls_from_cache_and_returns() {
        $this->resetAfterTest(true);

        $this->build_test_caches();

        $value = block_quickmail_cache::store('qm_msg_recip_count')->pull('foo');
        $this->assertEquals('bar', $value);

        $value = block_quickmail_cache::store('qm_msg_recip_count')->check('foo');
        $this->assertFalse($value);
    }

    private function build_test_caches() {
        // Build 'qm_msg_recip_count' store.
        $cache = cache::make('block_quickmail', 'qm_msg_recip_count');
        $cache->set('foo', 'bar');
        $cache->set('baz', 'other');

        // Build 'qm_msg_deliv_count' store.
        $cache = cache::make('block_quickmail', 'qm_msg_deliv_count');
        $cache->set('red', 'fish');
        $cache->set('fish', 'blue');
        $cache->set('1', 'one');
        $cache->set('2', 'two');
        $cache->set('things', [
            'foo',
            'bar',
        ]);
    }

}
