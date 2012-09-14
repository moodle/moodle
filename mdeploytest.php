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
 * PHPUnit tests for the mdeploy.php utility
 *
 * Because the mdeploy.php can't be part of the Moodle code itself, this tests must be
 * executed using something like:
 *
 *  $ phpunit --no-configuration mdeploytest
 *
 * @package     core
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/mdeploy.php');

/**
 * Provides testable input options.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class input_fake_provider extends input_provider {

    /** @var array */
    protected $fakeoptions = array();

    /**
     * Sets fake raw options.
     *
     * @param array $options
     */
    public function set_fake_options(array $options) {
        $this->fakeoptions = $options;
        $this->populate_options();
    }

    /**
     * Returns the explicitly set fake options.
     *
     * @return array
     */
    protected function parse_raw_options() {
        return $this->fakeoptions;
    }
}

/**
 * Testable subclass.
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_input_manager extends input_manager {

    /**
     * Provides access to the protected method so we can test it explicitly.
     */
    public function cast_value($raw, $type) {
        return parent::cast_value($raw, $type);
    }

    /**
     * Sets the fake input provider.
     */
    protected function initialize() {
        $this->inputprovider = input_fake_provider::instance();
    }
}


/**
 * Test cases for the mdeploy utility
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mdeploytest extends PHPUnit_Framework_TestCase {

    public function test_same_singletons() {
        $a = input_manager::instance();
        $b = input_manager::instance();
        $this->assertSame($a, $b);
    }

    /**
     * @dataProvider data_for_cast_value
     */
    public function test_cast_value($raw, $type, $result) {
        $input = testable_input_manager::instance();
        $this->assertSame($input->cast_value($raw, $type), $result);
    }

    public function data_for_cast_value() {
        return array(
            array('3', input_manager::TYPE_INT, 3),
            array(4, input_manager::TYPE_INT, 4),
            array('', input_manager::TYPE_INT, 0),

            array(true, input_manager::TYPE_FLAG, true),
            array(false, input_manager::TYPE_FLAG, true),
            array(0, input_manager::TYPE_FLAG, true),
            array('1', input_manager::TYPE_FLAG, true),
            array('0', input_manager::TYPE_FLAG, true),
            array('muhehe', input_manager::TYPE_FLAG, true),
        );
    }

    /**
     * @expectedException invalid_coding_exception
     */
    public function test_cast_array_argument() {
        $input = testable_input_manager::instance();
        $input->cast_value(array(1, 2, 3), input_manager::TYPE_INT); // must throw exception
    }

    /**
     * @expectedException invalid_coding_exception
     */
    public function test_cast_object_argument() {
        $input = testable_input_manager::instance();
        $o = new stdClass();
        $input->cast_value($o, input_manager::TYPE_INT); // must throw exception
    }

    public function test_has_option() {
        $provider = input_fake_provider::instance();

        $provider->set_fake_options(array());
        $this->assertFalse($provider->has_option('foo')); // foo not passed

        $provider->set_fake_options(array('foo' => 1));
        $this->assertFalse($provider->has_option('foo')); // foo passed but not a known option

        $provider->set_fake_options(array('foo' => 1, 'help' => false));
        $this->assertTrue($provider->has_option('help')); // help passed and it is a flag (value ignored)
        $this->assertTrue($provider->has_option('h')); // 'h' is a shortname for 'help'
    }
}
