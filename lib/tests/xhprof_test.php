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

namespace core;

/**
 * Unit tests for the xhprof class.
 *
 * @package   core
 * @category  test
 * @copyright 2019 Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhprof_test extends \advanced_testcase {

    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');
    }

    /**
     * Data provider for string matches
     *
     * @return  array
     */
    public static function profiling_string_matches_provider(): array {
        return [
            ['/index.php',              '/index.php',           true],
            ['/some/dir/index.php',     '/index.php',           false],
            ['/course/view.php',        '/course/view.php',     true],
            ['/view.php',               '/course/view.php',     false],
            ['/mod/forum',              '/mod/forum/*',         false],
            ['/mod/forum/',             '/mod/forum/*',         true],
            ['/mod/forum/index.php',    '/mod/forum/*',         true],
            ['/mod/forum/foo.php',      '/mod/forum/*',         true],
            ['/mod/forum/view.php',     '/mod/*/view.php',      true],
            ['/mod/one/two/view.php',   '/mod/*/view.php',      true],
            ['/view.php',               '*/view.php',           true],
            ['/mod/one/two/view.php',   '*/view.php',           true],
            ['/foo.php',                '/foo.php,/bar.php',    true],
            ['/bar.php',                '/foo.php,/bar.php',    true],
            ['/foo/bar.php',            "/foo.php,/bar.php",    false],
            ['/foo/bar.php',            "/foo.php,*/bar.php",   true],
            ['/foo/bar.php',            "/foo*.php,/bar.php",   true],
            ['/foo.php',                "/foo.php\n/bar.php",   true],
            ['/bar.php',                "/foo.php\n/bar.php",   true],
            ['/foo/bar.php',            "/foo.php\n/bar.php",   false],
            ['/foo/bar.php',            "/foo.php\n*/bar.php",  true],
            ['/foo/bar.php',            "/foo*.php\n/bar.php",  true],
        ];
    }

    /**
     * Test the matching syntax
     *
     * @covers ::profiling_string_matches
     * @dataProvider profiling_string_matches_provider
     * @param   string $string
     * @param   string $patterns
     * @param   bool   $expected
     */
    public function test_profiling_string_matches($string, $patterns, $expected) {
        $result = profiling_string_matches($string, $patterns);
        $this->assertSame($result, $expected);
    }

    /**
     * Data provider for both the topological sort and the data reduction tests.
     *
     * @return array
     */
    public static function run_data_provider(): array {
        // This data corresponds to the runs used as example @ MDL-79285.
        return [
            'sorted_case' => [
                'rundata' => array_flip([
                    'A',
                    'A==>B',
                    'A==>C',
                    'A==>__Mustache4',
                    'B==>__Mustache1',
                    '__Mustache1==>__Mustache2',
                    '__Mustache4==>__Mustache2',
                    '__Mustache4==>E',
                    'E==>F',
                    'C==>F',
                    '__Mustache2==>F',
                    '__Mustache2==>D',
                    'D==>__Mustache3',
                    '__Mustache3==>F',
                ]),
                'expectations' => [
                    'topofirst' => 'A',
                    'topolast' => '__Mustache3==>F',
                    'topocount' => 14,
                    'topoorder' => [
                        // Before and after pairs to verify they are ordered.
                        ['before' => 'A==>C', 'after' => 'C==>F'],
                        ['before' => 'D==>__Mustache3', 'after' => '__Mustache3==>F'],
                    ],
                    'reducecount' => 8,
                    'reduceremoved' => [
                        // Elements that will be removed by the reduction.
                        '__Mustache1==>__Mustache2',
                        '__Mustache4==>__Mustache2',
                        '__Mustache2==>F',
                        '__Mustache2==>D',
                        '__Mustache2==>D',
                        '__Mustache3==>F',
                    ],
                ],
            ],
            'unsorted_case' => [
                'rundata' => array_flip([
                    'A==>__Mustache4',
                    '__Mustache3==>F',
                    'A==>B',
                    'A==>C',
                    'B==>__Mustache1',
                    '__Mustache1==>__Mustache2',
                    '__Mustache4==>__Mustache2',
                    '__Mustache4==>E',
                    'E==>F',
                    'C==>F',
                    '__Mustache2==>F',
                    '__Mustache2==>D',
                    'D==>__Mustache3',
                    'A',
                ]),
                'expectations' => [
                    'topofirst' => 'A',
                    'topolast' => '__Mustache3==>F',
                    'topocount' => 14,
                    'topoorder' => [
                        // Before and after pairs to verify they are ordered.
                        ['before' => 'A==>C', 'after' => 'C==>F'],
                        ['before' => 'D==>__Mustache3', 'after' => '__Mustache3==>F'],
                    ],
                    'reducecount' => 8,
                    'reduceremoved' => [
                        // Elements that will be removed by the reduction.
                        '__Mustache1==>__Mustache2',
                        '__Mustache4==>__Mustache2',
                        '__Mustache2==>F',
                        '__Mustache2==>D',
                        '__Mustache2==>D',
                        '__Mustache3==>F',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test that topologically sorting the run data works as expected
     *
     * @covers \moodle_xhprofrun::xhprof_topo_sort
     * @dataProvider run_data_provider
     *
     * @param array $rundata The run data to be sorted.
     * @param array $expectations The expected results.
     */
    public function test_xhprof_topo_sort(array $rundata, array $expectations) {
        // Make sure all the examples in the provider are the same size.
        $this->assertSame($expectations['topocount'], count($rundata));

        // Make moodle_xhprofrun::xhprof_topo_sort() accessible.
        $reflection = new \ReflectionClass('\moodle_xhprofrun');
        $method = $reflection->getMethod('xhprof_topo_sort');
        // Sort the data.
        $result = $method->invokeArgs(new \moodle_xhprofrun(), [$rundata]);
        $this->assertIsArray($result);
        $this->assertSame($expectations['topocount'], count($result));
        // Convert the array to a list of keys, so we can assert values by position.
        $resultkeys = array_keys($result);

        // This is the elements that should be first.
        $this->assertSame($expectations['topofirst'], $resultkeys[0]);
        // This is the element that should be last.
        $this->assertSame($expectations['topolast'], $resultkeys[$expectations['topocount'] - 1]);
        // This relative ordering should be respected.
        foreach ($expectations['topoorder'] as $order) {
            // All the elements in the expectations should be present.
            $this->assertArrayHasKey($order['before'], $result);
            $this->assertArrayHasKey($order['after'], $result);
            // And they should be in the correct relative order.
            $this->assertGreaterThan(
                array_search($order['before'], $resultkeys),
                array_search($order['after'], $resultkeys)
            );
        }

        // Final check, if we sort it again, nothing changes (it's already topologically sorted).
        $result2 = $method->invokeArgs(new \moodle_xhprofrun(), [$result]);
        $this->assertSame($result, $result2);
    }

    /**
     * Test that reducing the data complexity works as expected
     *
     * @covers \moodle_xhprofrun::reduce_run_data
     * @dataProvider run_data_provider
     *
     * @param array $rundata The run data to be reduced.
     * @param array $expectations The expected results.
     */
    public function test_reduce_run_data(array $rundata, array $expectations) {
        // Make sure that the expected keys that will be removed are present.
        foreach ($expectations['reduceremoved'] as $key) {
            $this->assertArrayHasKey($key, $rundata);
        }

        // Make moodle_xhprofrun::reduce_run_data() accessible.
        $reflection = new \ReflectionClass('\moodle_xhprofrun');
        $method = $reflection->getMethod('reduce_run_data');
        // Reduce the data.
        $result = $method->invokeArgs(new \moodle_xhprofrun(), [$rundata]);
        $this->assertIsArray($result);
        $this->assertSame($expectations['reducecount'], count($result));
        // These have been the removed elements.
        foreach ($expectations['reduceremoved'] as $key) {
            $this->assertArrayNotHasKey($key, $result);
        }

        // Final check, if we reduce it again, nothing changes (it's already reduced).
        $result2 = $method->invokeArgs(new \moodle_xhprofrun(), [$result]);
        $this->assertSame($result, $result2);
    }
}

