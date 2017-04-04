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
 * module_std_proxy tests.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\proxies\module_std_proxy;

/**
 * std_proxy testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_module_std_proxy_testcase extends advanced_testcase {
    /**
     * @var \stdClass[] $objects Array of objects to proxy.
     */
    public $objects;

    /**
     * Sets up the fixture. This method is called before a test is executed.
     */
    public function setUp() {
        $this->objects = [
            'somemodule_someinstance' => (object) [
                'member1' => 'Hello',
                'member2' => 1729,
                'member3' => 'Something else'
            ],
            'anothermodule_anotherinstance' => (object) [
                'member1' => 'Hej',
                'member2' => 87539319,
                'member3' => 'nagot annat'
            ]
        ];
    }

    /**
     * Test proxying.
     *
     * @dataProvider test_proxy_testcases()
     * @param string $modulename Object module name.
     * @param string $instance   Object instance.
     * @param string $member     Object member to retrieve.
     * @param mixed  $expected   Expected value of member.
     */
    public function test_proxy($modulename, $instance, $member, $expected) {
        $proxy = new module_std_proxy(
            $modulename,
            $instance,
            function($modulename, $instance) {
                return $this->objects[$modulename . '_' . $instance];
            }
        );

        $this->assertEquals($proxy->get($member), $expected);

        // Test changing the value.
        $proxy->set($member, 'something even more else');
        $this->assertEquals($proxy->get($member), 'something even more else');
    }

    /**
     * Test setting values with a base class.
     *
     * @dataProvider test_proxy_testcases()
     * @param string $modulename  Object module name.
     * @param string $instance    Object instance.
     * @param string $member      Object member to retrieve.
     * @param mixed  $storedvalue Value as would be stored externally.
     */
    public function test_base_values($modulename, $instance, $member, $storedvalue) {
        $proxy = new module_std_proxy(
            $modulename,
            $instance,
            function($module, $instance) {
                return $this->objects[$module . '_' . $instance];
            },
            (object)['member1' => 'should clobber 1']
        );

        $expected = $member == 'member1' ? 'should clobber 1' : $storedvalue;
        $this->assertEquals($proxy->get($member), $expected);
    }

    /**
     * Test getting a non existant member.
     *
     * @dataProvider test_get_set_testcases()
     * @param string $modulename Object module name.
     * @param string $instance   Object instance.
     */
    public function test_get_invalid_member($modulename, $instance) {
        $proxy = new module_std_proxy(
            $modulename,
            $instance,
            function($modulename, $instance) {
                return $this->objects[$modulename . '_' . $instance];
            }
        );

        $this->expectException('\core_calendar\local\event\exceptions\member_does_not_exist_exception');
        $proxy->get('thisdoesnotexist');
    }

    /**
     * Test setting a non existant member.
     *
     * @dataProvider test_get_set_testcases()
     * @param string $modulename Object module name.
     * @param string $instance   Object instance.
     */
    public function test_set_invalid_member($modulename, $instance) {
        $proxy = new module_std_proxy(
            $modulename,
            $instance,
            function($modulename, $instance) {
                return $this->objects[$modulename . '_' . $instance];
            }
        );

        $this->expectException('\core_calendar\local\event\exceptions\member_does_not_exist_exception');
        $proxy->set('thisdoesnotexist', 'should break');
    }

    /**
     * Test get proxied instance.
     *
     * @dataProvider test_get_set_testcases()
     * @param string $modulename Object module name.
     * @param string $instance   Object instance.
     */
    public function test_get_proxied_instance($modulename, $instance) {
        $proxy = new module_std_proxy(
            $modulename,
            $instance,
            function($modulename, $instance) {
                return $this->objects[$modulename . '_' . $instance];
            }
        );

        $this->assertEquals($proxy->get_proxied_instance(), $this->objects[$modulename . '_' . $instance]);
    }

    /**
     * Test cases for proxying test.
     */
    public function test_proxy_testcases() {
        return [
            'Object 1 member 1' => [
                'somemodule',
                'someinstance',
                'member1',
                'Hello'
            ],
            'Object 1 member 2' => [
                'somemodule',
                'someinstance',
                'member2',
                1729
            ],
            'Object 1 member 3' => [
                'somemodule',
                'someinstance',
                'member3',
                'Something else'
            ],
            'Object 2 member 1' => [
                'anothermodule',
                'anotherinstance',
                'member1',
                'Hej'
            ],
            'Object 2 member 2' => [
                'anothermodule',
                'anotherinstance',
                'member2',
                87539319
            ],
            'Object 3 member 3' => [
                'anothermodule',
                'anotherinstance',
                'member3',
                'nagot annat'
            ]
        ];
    }

    /**
     * Test cases for getting and setting tests.
     */
    public function test_get_set_testcases() {
        return [
            'Object 1' => ['somemodule', 'someinstance'],
            'Object 2' => ['anothermodule', 'anotherinstance']
        ];
    }
}
