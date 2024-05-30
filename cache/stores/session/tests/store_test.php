<?php
// This session is part of Moodle - http://moodle.org/
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

namespace cachestore_session;

use cache_store;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/session/lib.php');

/**
 * Session unit test class.
 *
 * @package    cachestore_session
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends \cachestore_tests {
    /**
     * Returns the session class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_session';
    }

    /**
     * Test the maxsize option.
     */
    public function test_maxsize(): void {
        $config = \cache_config_testing::instance();
        $config->phpunit_add_definition('phpunit/one', array(
            'mode' => cache_store::MODE_SESSION,
            'component' => 'phpunit',
            'area' => 'one',
            'maxsize' => 3
        ));

        $config->phpunit_add_definition('phpunit/two', array(
            'mode' => cache_store::MODE_SESSION,
            'component' => 'phpunit',
            'area' => 'two',
            'maxsize' => 3
        ));

        $cacheone = \cache::make('phpunit', 'one');

        $this->assertTrue($cacheone->set('key1', 'value1'));
        $this->assertTrue($cacheone->set('key2', 'value2'));
        $this->assertTrue($cacheone->set('key3', 'value3'));

        $this->assertTrue($cacheone->has('key1'));
        $this->assertTrue($cacheone->has('key2'));
        $this->assertTrue($cacheone->has('key3'));

        $this->assertTrue($cacheone->set('key4', 'value4'));
        $this->assertTrue($cacheone->set('key5', 'value5'));

        $this->assertFalse($cacheone->has('key1'));
        $this->assertFalse($cacheone->has('key2'));
        $this->assertTrue($cacheone->has('key3'));
        $this->assertTrue($cacheone->has('key4'));
        $this->assertTrue($cacheone->has('key5'));

        $this->assertFalse($cacheone->get('key1'));
        $this->assertFalse($cacheone->get('key2'));
        $this->assertEquals('value3', $cacheone->get('key3'));
        $this->assertEquals('value4', $cacheone->get('key4'));
        $this->assertEquals('value5', $cacheone->get('key5'));

        // Test adding one more.
        $this->assertTrue($cacheone->set('key6', 'value6'));
        $this->assertFalse($cacheone->get('key3'));

        // Test reducing and then adding to make sure we don't lost one.
        $this->assertTrue($cacheone->delete('key6'));
        $this->assertTrue($cacheone->set('key7', 'value7'));
        $this->assertEquals('value4', $cacheone->get('key4'));

        // Set the same key three times to make sure it doesn't count overrides.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($cacheone->set('key8', 'value8'));
        }
        $this->assertEquals('value7', $cacheone->get('key7'), 'Overrides are incorrectly incrementing size');

        // Test adding many.
        $this->assertEquals(3, $cacheone->set_many(array(
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC'
        )));
        $this->assertEquals(array(
            'key4' => false,
            'key5' => false,
            'key6' => false,
            'key7' => false,
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC'
        ), $cacheone->get_many(array(
            'key4', 'key5', 'key6', 'key7', 'keyA', 'keyB', 'keyC'
        )));

        $cachetwo = \cache::make('phpunit', 'two');

        // Test adding many.
        $this->assertEquals(3, $cacheone->set_many(array(
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC'
        )));

        $this->assertEquals(3, $cachetwo->set_many(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        )));

        $this->assertEquals(array(
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC'
        ), $cacheone->get_many(array(
            'keyA', 'keyB', 'keyC'
        )));

        $this->assertEquals(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ), $cachetwo->get_many(array(
            'key1', 'key2', 'key3'
        )));

        // Test that that cache deletes element that was least recently accessed.
        $this->assertEquals('valueA', $cacheone->get('keyA'));
        $cacheone->set('keyD', 'valueD');
        $this->assertEquals('valueA', $cacheone->get('keyA'));
        $this->assertFalse($cacheone->get('keyB'));
        $this->assertEquals(array('keyD' => 'valueD', 'keyC' => 'valueC'), $cacheone->get_many(array('keyD', 'keyC')));
        $cacheone->set('keyE', 'valueE');
        $this->assertFalse($cacheone->get('keyB'));
        $this->assertFalse($cacheone->get('keyA'));
        $this->assertEquals(array('keyA' => false, 'keyE' => 'valueE', 'keyD' => 'valueD', 'keyC' => 'valueC'),
                $cacheone->get_many(array('keyA', 'keyE', 'keyD', 'keyC')));
        // Overwrite keyE (moves it to the end of array), and set keyF.
        $cacheone->set_many(array('keyE' => 'valueE', 'keyF' => 'valueF'));
        $this->assertEquals(array('keyC' => 'valueC', 'keyE' => 'valueE', 'keyD' => false, 'keyF' => 'valueF'),
                $cacheone->get_many(array('keyC', 'keyE', 'keyD', 'keyF')));
    }

    public function test_ttl(): void {
        $config = \cache_config_testing::instance();
        $config->phpunit_add_definition('phpunit/three', array(
            'mode' => cache_store::MODE_SESSION,
            'component' => 'phpunit',
            'area' => 'three',
            'maxsize' => 3,
            'ttl' => 3
        ));

        $cachethree = \cache::make('phpunit', 'three');

        // Make sure that when cache with ttl is full the elements that were added first are deleted first regardless of access time.
        $cachethree->set('key1', 'value1');
        $cachethree->set('key2', 'value2');
        $cachethree->set('key3', 'value3');
        $cachethree->set('key4', 'value4');
        $this->assertFalse($cachethree->get('key1'));
        $this->assertEquals('value4', $cachethree->get('key4'));
        $cachethree->set('key5', 'value5');
        $this->assertFalse($cachethree->get('key2'));
        $this->assertEquals('value4', $cachethree->get('key4'));
        $cachethree->set_many(array('key6' => 'value6', 'key7' => 'value7'));
        $this->assertEquals(array('key3' => false, 'key4' => false, 'key5' => 'value5', 'key6' => 'value6', 'key7' => 'value7'),
                $cachethree->get_many(array('key3', 'key4', 'key5', 'key6', 'key7')));
    }
}
