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
 * Unit tests for our utf-8 aware collator which is used for sorting.
 *
 * @package    core
 * @category   test
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use core_collator;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for our utf-8 aware collator which is used for sorting.
 *
 * @package    core
 * @category   test
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class collator_test extends \advanced_testcase {

    /**
     * @var string The initial lang, stored because we change it during testing
     */
    protected $initiallang = null;

    /**
     * @var string The last error that has occurred
     */
    protected $error = null;

    /**
     * Prepares things for this test case.
     */
    protected function setUp(): void {
        global $SESSION;
        if (isset($SESSION->lang)) {
            $this->initiallang = $SESSION->lang;
        }
        $SESSION->lang = 'en'; // Make sure we test en language to get consistent results, hopefully all systems have this locale.
        if (extension_loaded('intl')) {
            $this->error = 'Collation aware sorting not supported';
        } else {
            $this->error = 'Collation aware sorting not supported, PHP extension "intl" is not available.';
        }
        parent::setUp();
    }

    /**
     * Cleans things up after this test case has run.
     */
    protected function tearDown(): void {
        global $SESSION;
        parent::tearDown();
        if ($this->initiallang !== null) {
            $SESSION->lang = $this->initiallang;
            $this->initiallang = null;
        } else {
            unset($SESSION->lang);
        }
    }

    /**
     * Tests the static asort method.
     */
    public function test_asort(): void {
        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = core_collator::asort($arr);
        $this->assertSame(array('aa', 'ab', 'cc'), array_values($arr));
        $this->assertSame(array(1, 'b', 0), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = core_collator::asort($arr, core_collator::SORT_STRING);
        $this->assertSame(array('aa', 'ab', 'cc'), array_values($arr));
        $this->assertSame(array(1, 'b', 0), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => 'aac', 1 => 'Aac', 0 => 'cc');
        $result = core_collator::asort($arr, (core_collator::SORT_STRING | core_collator::CASE_SENSITIVE));
        $this->assertSame(array('Aac', 'aac', 'cc'), array_values($arr));
        $this->assertSame(array(1, 'b', 0), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => 'a1', 1 => 'a10', 0 => 'a3b');
        $result = core_collator::asort($arr);
        $this->assertSame(array('a1', 'a10', 'a3b'), array_values($arr));
        $this->assertSame(array('b', 1, 0), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => 'a1', 1 => 'a10', 0 => 'a3b');
        $result = core_collator::asort($arr, core_collator::SORT_NATURAL);
        $this->assertSame(array('a1', 'a3b', 'a10'), array_values($arr));
        $this->assertSame(array('b', 0, 1), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => '1.1.1', 1 => '1.2', 0 => '1.20.2');
        $result = core_collator::asort($arr, core_collator::SORT_NATURAL);
        $this->assertSame(array_values($arr), array('1.1.1', '1.2', '1.20.2'));
        $this->assertSame(array_keys($arr), array('b', 1, 0));
        $this->assertTrue($result);

        $arr = array('b' => '-1', 1 => 1000, 0 => -1.2, 3 => 1, 4 => false);
        $result = core_collator::asort($arr, core_collator::SORT_NUMERIC);
        $this->assertSame(array(-1.2, '-1', false, 1, 1000), array_values($arr));
        $this->assertSame(array(0, 'b', 4, 3, 1), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('b' => array(1), 1 => array(2, 3), 0 => 1);
        $result = core_collator::asort($arr, core_collator::SORT_REGULAR);
        $this->assertSame(array(1, array(1), array(2, 3)), array_values($arr));
        $this->assertSame(array(0, 'b', 1), array_keys($arr));
        $this->assertTrue($result);

        // Test sorting of array of arrays - first element should be used for actual comparison.
        $arr = array(0=>array('bb', 'z'), 1=>array('ab', 'a'), 2=>array('zz', 'x'));
        $result = core_collator::asort($arr, core_collator::SORT_REGULAR);
        $this->assertSame(array(1, 0, 2), array_keys($arr));
        $this->assertTrue($result);

        $arr = array('a' => 'áb', 'b' => 'ab', 1 => 'aa', 0=>'cc', 'x' => 'Áb');
        $result = core_collator::asort($arr);
        $this->assertSame(array('aa', 'ab', 'áb', 'Áb', 'cc'), array_values($arr), $this->error);
        $this->assertSame(array(1, 'b', 'a', 'x', 0), array_keys($arr), $this->error);
        $this->assertTrue($result);

        $a = array(2=>'b', 1=>'c');
        $c =& $a;
        $b =& $a;
        core_collator::asort($b);
        $this->assertSame($a, $b);
        $this->assertSame($c, $b);
    }

    /**
     * Tests the static asort_objects_by_method method.
     */
    public function test_asort_objects_by_method(): void {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        $result = core_collator::asort_objects_by_method($objects, 'get_protected_name');
        $this->assertSame(array(1, 'b', 0), array_keys($objects));
        $this->assertSame(array('aa', 'ab', 'cc'), $this->get_ordered_names($objects, 'get_protected_name'));
        $this->assertTrue($result);

        $objects = array(
            'b' => new string_test_class('a20'),
            1 => new string_test_class('a1'),
            0 => new string_test_class('a100')
        );
        $result = core_collator::asort_objects_by_method($objects, 'get_protected_name', core_collator::SORT_NATURAL);
        $this->assertSame(array(1, 'b', 0), array_keys($objects));
        $this->assertSame(array('a1', 'a20', 'a100'), $this->get_ordered_names($objects, 'get_protected_name'));
        $this->assertTrue($result);
    }

    /**
     * Tests the static asort_objects_by_method method.
     */
    public function test_asort_objects_by_property(): void {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        $result = core_collator::asort_objects_by_property($objects, 'publicname');
        $this->assertSame(array(1, 'b', 0), array_keys($objects));
        $this->assertSame(array('aa', 'ab', 'cc'), $this->get_ordered_names($objects, 'publicname'));
        $this->assertTrue($result);

        $objects = array(
            'b' => new string_test_class('a20'),
            1 => new string_test_class('a1'),
            0 => new string_test_class('a100')
        );
        $result = core_collator::asort_objects_by_property($objects, 'publicname', core_collator::SORT_NATURAL);
        $this->assertSame(array(1, 'b', 0), array_keys($objects));
        $this->assertSame(array('a1', 'a20', 'a100'), $this->get_ordered_names($objects, 'publicname'));
        $this->assertTrue($result);
    }

    /**
     * Tests the sorting of an array of arrays by key.
     */
    public function test_asort_array_of_arrays_by_key(): void {
        $array = array(
            'a' => array('name' => 'bravo'),
            'b' => array('name' => 'charlie'),
            'c' => array('name' => 'alpha')
        );
        $this->assertSame(array('a', 'b', 'c'), array_keys($array));
        $this->assertTrue(core_collator::asort_array_of_arrays_by_key($array, 'name'));
        $this->assertSame(array('c', 'a', 'b'), array_keys($array));

        $array = array(
            'a' => array('name' => 'b'),
            'b' => array('name' => 1),
            'c' => array('name' => 0)
        );
        $this->assertSame(array('a', 'b', 'c'), array_keys($array));
        $this->assertTrue(core_collator::asort_array_of_arrays_by_key($array, 'name'));
        $this->assertSame(array('c', 'b', 'a'), array_keys($array));

        $array = array(
            'a' => array('name' => 'áb'),
            'b' => array('name' => 'ab'),
            1   => array('name' => 'aa'),
            'd' => array('name' => 'cc'),
            0   => array('name' => 'Áb')
        );
        $this->assertSame(array('a', 'b', 1, 'd', 0), array_keys($array));
        $this->assertTrue(core_collator::asort_array_of_arrays_by_key($array, 'name'));
        $this->assertSame(array(1, 'b', 'a', 0, 'd'), array_keys($array));
        $this->assertSame(array(
            1   => array('name' => 'aa'),
            'b' => array('name' => 'ab'),
            'a' => array('name' => 'áb'),
            0   => array('name' => 'Áb'),
            'd' => array('name' => 'cc')
        ), $array);

    }

    /**
     * Returns an array of sorted names.
     * @param array $objects
     * @param string $methodproperty
     * @return array
     */
    protected function get_ordered_names($objects, $methodproperty = 'get_protected_name') {
        $return = array();
        foreach ($objects as $object) {
            if ($methodproperty == 'publicname') {
                $return[] = $object->publicname;
            } else {
                $return[] = $object->$methodproperty();
            }
        }
        return $return;
    }

    /**
     * Tests the static ksort method.
     */
    public function test_ksort(): void {
        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = core_collator::ksort($arr);
        $this->assertSame(array(0, 1, 'b'), array_keys($arr));
        $this->assertSame(array('cc', 'aa', 'ab'), array_values($arr));
        $this->assertTrue($result);

        $obj = new \stdClass();
        $arr = array('1.1.1'=>array(), '1.2'=>$obj, '1.20.2'=>null);
        $result = core_collator::ksort($arr, core_collator::SORT_NATURAL);
        $this->assertSame(array('1.1.1', '1.2', '1.20.2'), array_keys($arr));
        $this->assertSame(array(array(), $obj, null), array_values($arr));
        $this->assertTrue($result);

        $a = array(2=>'b', 1=>'c');
        $c =& $a;
        $b =& $a;
        core_collator::ksort($b);
        $this->assertSame($a, $b);
        $this->assertSame($c, $b);
    }

}


/**
 * Simple class used to work with the unit test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_test_class extends \stdClass {
    /**
     * @var string A public property
     */
    public $publicname;
    /**
     * @var string A protected property
     */
    protected $protectedname;
    /**
     * @var string A private property
     */
    private $privatename;
    /**
     * Constructs the test instance.
     * @param string $name
     */
    public function __construct($name) {
        $this->publicname = $name;
        $this->protectedname = $name;
        $this->privatename = $name;
    }
    /**
     * Returns the protected property.
     * @return string
     */
    public function get_protected_name() {
        return $this->protectedname;
    }
    /**
     * Returns the protected property.
     * @return string
     */
    public function get_private_name() {
        return $this->publicname;
    }
}
