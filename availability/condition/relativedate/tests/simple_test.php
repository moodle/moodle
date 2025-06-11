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
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_relativedate;

use availability_relativedate\condition;

/**
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_relativedate\condition
 */
final class simple_test extends \basic_testcase {
    /**
     * Tests the constructor including error conditions.
     * @covers \availability_relativedate\condition
     */
    public function test_constructor(): void {
        $structure = (object)['type' => 'relativedate'];
        $cond = new condition($structure);
        $newcond = $cond->save();
        $this->assertNotEqualsCanonicalizing($structure, $newcond);
        $this->assertEquals($newcond, (object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 1, 'm' => 0]);

        $structure->n = 1;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->n = 'a';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure = (object)['type' => 'relativedate'];
        $structure->d = 2;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->d = 'b';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure = (object)['type' => 'relativedate'];
        $structure->c = 3;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->c = 'c';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure = (object)['type' => 'relativedate'];
        $structure->e = 4;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->e = 'd';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure = (object)['type' => 'relativedate'];
        $structure->s = 4;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->s = 'd';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure = (object)['type' => 'relativedate'];
        $structure->n = 5;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->n = 'e';
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());

        $structure->c = 1111;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
        $structure->s = 1;
        $cond = new condition($structure);
        $this->assertNotEqualsCanonicalizing($structure, $cond->save());
    }

    /**
     * Tests the save() function.
     * @covers \availability_relativedate\condition
     */
    public function test_save(): void {
        $structure = (object)['n' => 1, 'd' => 2, 's' => 1, 'm' => 1];
        $cond = new condition($structure);
        $structure->type = 'relativedate';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests static methods.
     * @covers \availability_relativedate\condition
     */
    public function test_static(): void {
        $this->assertCount(5, condition::options_dwm());
        $expected = [0 => 'minute', 1 => 'hour', 2 => 'day', 3 => 'week', 4 => 'month'];
        $this->assertEquals($expected, condition::options_dwm(1));
        $this->assertEquals('minute', condition::option_dwm(0));
        $this->assertEquals('hour', condition::option_dwm(1));
        $this->assertEquals('day', condition::option_dwm(2));
        $this->assertEquals('week', condition::option_dwm(3));
        $this->assertEquals('month', condition::option_dwm(4));
        $this->assertEquals('', condition::option_dwm(5));
        $this->assertEquals('', condition::option_dwm(6));
        $this->assertEquals($expected, condition::options_dwm());
        $expected = [0 => 'minutes', 1 => 'hours', 2 => 'days', 3 => 'weeks', 4 => 'months'];
        $this->assertEquals($expected, condition::options_dwm(2));
        $this->assertEquals(condition::options_dwm(4), condition::options_dwm(3));

        $this->assertEquals('', condition::options_start(0));
        $this->assertEquals('after course start date', condition::options_start(1));
        $this->assertEquals('before course end date', condition::options_start(2));
        $this->assertEquals('after user enrolment date', condition::options_start(3));
        $this->assertEquals('after enrolment method end date', condition::options_start(4));
        $this->assertEquals('after course end date', condition::options_start(5));
        $this->assertEquals('before course start date', condition::options_start(6));
        $this->assertEquals('after completion of activity', condition::options_start(7));
        $this->assertEquals('', condition::options_start(8));
        $this->assertEquals('', condition::options_start(9));
    }

    /**
     * Test debug string.
     *
     * @dataProvider debug_provider
     * @param array $cond
     * @param string $result
     * @covers \availability_relativedate\condition
     */
    public function test_debug($cond, $result): void {
        $name = 'availability_relativedate\condition';
        $condition = new condition((object)$cond);
        $callresult = \phpunit_util::call_internal_method($condition, 'get_debug_string', [], $name);
        $this->assertEquals($result, $callresult);
    }

    /**
     * Relative dates debug provider.
     */
    public static function debug_provider(): array {
        $daybefore = ' 1 ' . get_string('day', 'availability_relativedate') . ' ';
        return [
            'After start course' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 1, 'm' => 999999],
                $daybefore . get_string('datestart', 'availability_relativedate'), ],
            'Before end course' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 2, 'm' => 999999],
                $daybefore . get_string('dateend', 'availability_relativedate'), ],
            'After end enrol' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 3, 'm' => 999999],
                $daybefore . get_string('dateenrol', 'availability_relativedate'), ],
            'After end method' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 4, 'm' => 999999],
                $daybefore . get_string('dateendenrol', 'availability_relativedate'), ],
            'After end course' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 5, 'm' => 999999],
                $daybefore . get_string('dateendafter', 'availability_relativedate'), ],
            'Before start course' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 6, 'm' => 999999],
                $daybefore . get_string('datestartbefore', 'availability_relativedate'), ],
            'After invalid module' => [
                ['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 999, 'm' => 999999],
                $daybefore, ],
            'Weeks after start course' => [
                ['type' => 'relativedate', 'n' => 2, 'd' => 3, 's' => 1, 'm' => 999999],
                ' 2 weeks after course start date', ],
        ];
    }
}
