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

namespace core_calendar;

use core_calendar\local\event\proxies\std_proxy;

/**
 * std_proxy testcase.
 *
 * @package core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class std_proxy_test extends \advanced_testcase {
    /**
     * @var \stdClass[] $objects Array of objects to proxy.
     */
    public $objects;

    public function setUp(): void {
        parent::setUp();
        $this->objects = [
            1 => (object) [
                'member1' => 'Hello',
                'member2' => 1729,
                'member3' => 'Something else'
            ],
            5 => (object) [
                'member1' => 'Hej',
                'member2' => 87539319,
                'member3' => 'nagot annat'
            ]
        ];
    }

    /**
     * Test proxying.
     *
     * @dataProvider proxy_testcases
     * @param int    $id       Object ID.
     * @param string $member   Object member to retrieve.
     * @param mixed  $expected Expected value of member.
     */
    public function test_proxy($id, $member, $expected): void {
        $proxy = new std_proxy($id, function($id) {
            return $this->objects[$id];
        });

        $this->assertEquals($proxy->get($member), $expected);
    }

    /**
     * Test setting values with a base class.
     *
     * @dataProvider proxy_testcases
     * @param int    $id          Object ID.
     * @param string $member      Object member to retrieve.
     * @param mixed  $storedvalue Value as would be stored externally.
     */
    public function test_base_values($id, $member, $storedvalue): void {
        $proxy = new std_proxy(
            $id,
            function($id) {
                return $this->objects[$id];
            },
            (object)['member1' => 'should clobber 1']
        );

        $expected = $member == 'member1' ? 'should clobber 1' : $storedvalue;
        $this->assertEquals($proxy->get($member), $expected);
    }

    /**
     * Test getting a non existant member.
     *
     * @dataProvider get_set_testcases
     * @param int $id ID of the object being proxied.
     */
    public function test_get_invalid_member($id): void {
        $proxy = new std_proxy($id, function($id) {
            return $this->objects[$id];
        });

        $this->expectException('\core_calendar\local\event\exceptions\member_does_not_exist_exception');
        $proxy->get('thisdoesnotexist');
    }

    /**
     * Test get proxied instance.
     *
     * @dataProvider get_set_testcases
     * @param int $id Object ID.
     */
    public function test_get_proxied_instance($id): void {
        $proxy = new std_proxy($id, function($id) {
            return $this->objects[$id];
        });

        $this->assertEquals($proxy->get_proxied_instance(), $this->objects[$id]);
    }

    /**
     * Test cases for proxying test.
     */
    public static function proxy_testcases(): array {
        return [
            'Object 1 member 1' => [
                1,
                'member1',
                'Hello'
            ],
            'Object 1 member 2' => [
                1,
                'member2',
                1729
            ],
            'Object 1 member 3' => [
                1,
                'member3',
                'Something else'
            ],
            'Object 2 member 1' => [
                5,
                'member1',
                'Hej'
            ],
            'Object 2 member 2' => [
                5,
                'member2',
                87539319
            ],
            'Object 3 member 3' => [
                5,
                'member3',
                'nagot annat'
            ]
        ];
    }

    /**
     * Test cases for getting and setting tests.
     */
    public static function get_set_testcases(): array {
        return [
            'Object 1' => [1],
            'Object 2' => [5]
        ];
    }
}
