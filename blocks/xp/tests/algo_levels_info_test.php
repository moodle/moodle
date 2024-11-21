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
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\local\xp\algo_levels_info;
use block_xp\local\xp\level_with_description;
use block_xp\local\xp\level_with_name;
use block_xp\tests\base_testcase;

/**
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class algo_levels_info_test extends base_testcase {

    /**
     * Test default.
     *
     * @covers \block_xp\local\xp\algo_levels_info::get_xp_with_algo
     */
    public function test_get_xp_with_algo_default(): void {
        $result = algo_levels_info::get_xp_with_algo(10, 120, 1.3);
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], $result);

        $result = algo_levels_info::get_xp_with_algo(10, 120, 1);
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 240,
            4 => 360,
            5 => 480,
            6 => 600,
            7 => 720,
            8 => 840,
            9 => 960,
            10 => 1080,
        ], $result);
    }

    /**
     * Test relative.
     *
     * @covers \block_xp\local\xp\algo_levels_info::get_xp_with_algo
     */
    public function test_get_xp_with_algo_relative_method(): void {
        $result = algo_levels_info::get_xp_with_algo(10, 120, 1.3, 'relative');
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], $result);

        $result = algo_levels_info::get_xp_with_algo(10, 120, 1, 'relative');
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 240,
            4 => 360,
            5 => 480,
            6 => 600,
            7 => 720,
            8 => 840,
            9 => 960,
            10 => 1080,
        ], $result);
    }

    /**
     * Test flat.
     *
     * @covers \block_xp\local\xp\algo_levels_info::get_xp_with_algo
     */
    public function test_get_xp_with_algo_flat_method(): void {
        $result = algo_levels_info::get_xp_with_algo(10, 120, 1, 'flat');
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 240,
            4 => 360,
            5 => 480,
            6 => 600,
            7 => 720,
            8 => 840,
            9 => 960,
            10 => 1080,
        ], $result);
        $result = algo_levels_info::get_xp_with_algo(5, 50, 1, 'flat');
        $this->assertEquals([
            1 => 0,
            2 => 50,
            3 => 100,
            4 => 150,
            5 => 200,
        ], $result);
    }

    /**
     * Test linear.
     *
     * @covers \block_xp\local\xp\algo_levels_info::get_xp_with_algo
     */
    public function test_get_xp_with_algo_linear_method(): void {
        $result = algo_levels_info::get_xp_with_algo(10, 100, 1, 'linear', 20);
        $this->assertEquals([
            1 => 0,
            2 => 100,
            3 => 220,
            4 => 360,
            5 => 520,
            6 => 700,
            7 => 900,
            8 => 1120,
            9 => 1360,
            10 => 1620,
        ], $result);
        $result = algo_levels_info::get_xp_with_algo(5, 500, 1, 'linear', 50);
        $this->assertEquals([
            1 => 0,
            2 => 500,
            3 => 1050,
            4 => 1650,
            5 => 2300,
        ], $result);
    }

    /**
     * Test make from defaults.
     *
     * @covers \block_xp\local\xp\algo_levels_info::make_from_defaults
     */
    public function test_make_from_defaults(): void {
        $levelsinfo = algo_levels_info::make_from_defaults();
        $this->assertEquals(10, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], $this->get_xp_by_levels($levelsinfo));
    }

    /**
     * Test make from defaults.
     *
     * @covers \block_xp\local\xp\algo_levels_info
     */
    public function test_data_parsing(): void {
        $sampledata = [
            'xp' => ['1' => 0, '2' => 120, '3' => 264, '4' => 437, '5' => 644, '6' => 893],
            'name' => [
                "1" => 'A',
                "2" => 'Level Too!',
                "3" => 'aaaa',
                "6" => 'X',
            ],
            'desc' => [
                '1' => 'a',
                '2' => 'bB',
                '3' => '3',
                '5' => 'five',
                '6' => 'xx',
            ],
            'base' => 120,
            'coef' => 1.2,
            'usealgo' => false,
        ];
        $levelsinfo = new algo_levels_info($sampledata);

        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.2, $levelsinfo->get_coef());
        $this->assertEquals(6, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 264,
            4 => 437,
            5 => 644,
            6 => 893,
        ], $this->get_xp_by_levels($levelsinfo));
        $this->assertEquals([
            1 => 'A',
            2 => 'Level Too!',
            3 => 'aaaa',
            4 => '',
            5 => '',
            6 => 'X',
        ], $this->get_name_by_levels($levelsinfo));
        $this->assertEquals([
            1 => 'a',
            2 => 'bB',
            3 => '3',
            4 => '',
            5 => 'five',
            6 => 'xx',
        ], $this->get_description_by_levels($levelsinfo));

        $sampledata = [
            'xp' => [
                '1' => 0,
                '2' => 120,
                '3' => 276,
                '4' => 479,
                '5' => 742,
                '6' => 1085,
                '7' => 1531,
                '8' => 2110,
                '9' => 2863,
                '10' => 3842,
            ],
            'name' => [],
            'desc' => [],
            'base' => 120,
            'coef' => 1.3,
            'usealgo' => true,
        ];
        $levelsinfo = new algo_levels_info($sampledata);

        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.3, $levelsinfo->get_coef());
        $this->assertEquals(10, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], $this->get_xp_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
        ], $this->get_name_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
        ], $this->get_description_by_levels($levelsinfo));
    }

    /**
     * Test make from defaults.
     *
     * @covers \block_xp\local\xp\algo_levels_info
     */
    public function test_data_parsing_with_inconsistencies(): void {
        $sampledata = [
            'xp' => ['1' => 0, '2' => 120, '3' => 264, '4' => 437, '5' => 644, '7' => 893], // Skipped 6 key.
            'name' => [
                "0" => 'A', // Invalid key.
                "2" => 'Level two!',
            ],
            'desc' => [
                '5' => 'Desc 5',
                '6' => 'Desc 6', // Key does not match level.
            ],
            // Missing algo.
        ];
        $levelsinfo = new algo_levels_info($sampledata);

        $this->assertEquals(algo_levels_info::DEFAULT_BASE, $levelsinfo->get_base());
        $this->assertEquals(algo_levels_info::DEFAULT_COEF, $levelsinfo->get_coef());
        $this->assertEquals(algo_levels_info::DEFAULT_INCR, $levelsinfo->get_incr());
        $this->assertEquals('relative', $levelsinfo->get_method());
        $this->assertEquals(6, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 264,
            4 => 437,
            5 => 644,
            6 => 893,
        ], $this->get_xp_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => 'Level two!',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
        ], $this->get_name_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => 'Desc 5',
            6 => '',
        ], $this->get_description_by_levels($levelsinfo));

        $sampledata = [
            'v' => 2,
            'xp' => ['1' => 0, '2' => 120, '3' => 264, '4' => 437, '5' => 644, '7' => 893], // Not indexed at 0 and skipped 6.
            'name' => [
                "0" => 'A', // Invalid key.
                "2" => 'Level two!',
            ],
            'desc' => [
                '5' => 'Desc 5',
                '6' => 'Desc 6', // Key does not match level.
            ],
            // Missing algo.
        ];
        $levelsinfo = new algo_levels_info($sampledata);

        $this->assertEquals(algo_levels_info::DEFAULT_BASE, $levelsinfo->get_base());
        $this->assertEquals(algo_levels_info::DEFAULT_COEF, $levelsinfo->get_coef());
        $this->assertEquals(algo_levels_info::DEFAULT_INCR, $levelsinfo->get_incr());
        $this->assertEquals('relative', $levelsinfo->get_method());
        $this->assertEquals(6, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 264,
            4 => 437,
            5 => 644,
            6 => 893,
        ], $this->get_xp_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => 'Level two!',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
        ], $this->get_name_by_levels($levelsinfo));
        $this->assertEquals([
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => 'Desc 5',
            6 => '',
        ], $this->get_description_by_levels($levelsinfo));
    }

    /**
     * Get the XP by levels.
     *
     * @param algo_levels_info $levelsinfo The levels info.
     */
    protected function get_xp_by_levels($levelsinfo) {
        return array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level->get_xp_required();
            return $carry;
        }, []);
    }

    /**
     * Get the description by levels.
     *
     * @param algo_levels_info $levelsinfo The levels info.
     */
    protected function get_description_by_levels($levelsinfo) {
        return array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level instanceof level_with_description ? $level->get_description() : '';
            return $carry;
        }, []);
    }

    /**
     * Get the names by levels.
     *
     * @param algo_levels_info $levelsinfo The levels info.
     */
    protected function get_name_by_levels($levelsinfo) {
        return array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level instanceof level_with_name ? $level->get_name() : '';
            return $carry;
        }, []);
    }

}
