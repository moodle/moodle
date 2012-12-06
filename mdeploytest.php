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
 * Testable subclass
 *
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_worker extends worker {

    /**
     * Provides access to the protected method.
     */
    public function move_directory($source, $target, $keepsourceroot = false) {
        return parent::move_directory($source, $target, $keepsourceroot);
    }

    /**
     * Provides access to the protected method.
     */
    public function remove_directory($path, $keeppathroot = false) {
        return parent::remove_directory($path, $keeppathroot);
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

            array('C:\\WINDOWS\\user.dat', input_manager::TYPE_PATH, 'C/WINDOWS/user.dat'),
            array('../../../etc/passwd', input_manager::TYPE_PATH, '/etc/passwd'),
            array('///////.././public_html/test.php', input_manager::TYPE_PATH, '/public_html/test.php'),

            array("!@#$%|/etc/qwerty\n\n\t\n\r", input_manager::TYPE_RAW, "!@#$%|/etc/qwerty\n\n\t\n\r"),

            array("\nrock'n'roll.mp3\t.exe", input_manager::TYPE_FILE, 'rocknroll.mp3.exe'),

            array('http://localhost/moodle/dev/plugin.zip', input_manager::TYPE_URL, 'http://localhost/moodle/dev/plugin.zip'),
            array(
                'https://moodle.org/plugins/download.php/1292/mod_stampcoll_moodle23_2012062201.zip',
                input_manager::TYPE_URL,
                'https://moodle.org/plugins/download.php/1292/mod_stampcoll_moodle23_2012062201.zip'
            ),

            array('5e8d2ea4f50d154730100b1645fbad67', input_manager::TYPE_MD5, '5e8d2ea4f50d154730100b1645fbad67'),
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

    /**
     * @expectedException invalid_option_exception
     */
    public function test_cast_invalid_url_value() {
        $input = testable_input_manager::instance();
        $invalid = 'file:///etc/passwd';
        $input->cast_value($invalid, input_manager::TYPE_URL); // must throw exception
    }

    /**
     * @expectedException invalid_option_exception
     */
    public function test_cast_invalid_md5_value() {
        $input = testable_input_manager::instance();
        $invalid = 'this is not a valid md5 hash';
        $input->cast_value($invalid, input_manager::TYPE_MD5); // must throw exception
    }

    /**
     * @expectedException invalid_option_exception
     */
    public function test_cast_tilde_in_path() {
        $input = testable_input_manager::instance();
        $invalid = '~/public_html/moodle_dev';
        $input->cast_value($invalid, input_manager::TYPE_PATH); // must throw exception
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

    public function test_get_option() {
        $input = testable_input_manager::instance();
        $provider = input_fake_provider::instance();

        $provider->set_fake_options(array('help' => false, 'passfile' => '_mdeploy.123456'));
        $this->assertTrue($input->get_option('h'));
        $this->assertEquals($input->get_option('passfile'), '_mdeploy.123456');
        $this->assertEquals($input->get_option('password', 'admin123'), 'admin123');
        try {
            $this->assertEquals($input->get_option('password'), 'admin123'); // must throw exception (not passed but required)
            $this->assertTrue(false);
        } catch (missing_option_exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_moving_and_removing_directories() {
        $worker = testable_worker::instance();

        $root = sys_get_temp_dir().'/'.uniqid('mdeploytest', true);
        mkdir($root.'/a', 0777, true);
        touch($root.'/a/a.txt');

        $this->assertTrue(file_exists($root.'/a/a.txt'));
        $this->assertFalse(file_exists($root.'/b/a.txt'));
        $this->assertTrue($worker->move_directory($root.'/a', $root.'/b'));
        $this->assertFalse(is_dir($root.'/a'));
        $this->assertTrue(file_exists($root.'/b/a.txt'));
        $this->assertTrue($worker->move_directory($root.'/b', $root.'/c', true));
        $this->assertTrue(file_exists($root.'/c/a.txt'));
        $this->assertFalse(file_exists($root.'/b/a.txt'));
        $this->assertTrue(is_dir($root.'/b'));
        $this->assertTrue($worker->remove_directory($root.'/c', true));
        $this->assertFalse(file_exists($root.'/c/a.txt'));
        $this->assertTrue($worker->remove_directory($root.'/c'));
        $this->assertFalse(is_dir($root.'/c'));
    }
}
