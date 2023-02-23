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
 * Unit tests for the numerical questions answers processor.
 *
 * @package    qtype_numerical
 * @category   test
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_numerical;

use qtype_numerical_answer_processor;

/**
 * Unit test for the numerical questions answers processor.
 *
 * @package    qtype_numerical
 * @category   test
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_numerical_answer_processor
 */
class answerprocessor_test extends \advanced_testcase {
    /**
     * Test setup.
     */
    public function setUp(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/question/type/numerical/questiontype.php");
    }

    /**
     * Test the parse_response function.
     *
     * @covers ::parse_response
     * @dataProvider parse_response_provider
     * @param array $expected
     * @param mixed $args
     */
    public function test_parse_response(array $expected, $args): void {
        $ap = new qtype_numerical_answer_processor([
            'm' => 1,
            'cm' => 100,
        ], false, '.', ',');

        $rc = new \ReflectionClass($ap);
        $rcm = $rc->getMethod('parse_response');
        $rcm->setAccessible(true);

        $this->assertEquals($expected, $rcm->invoke($ap, $args));
    }

    /**
     * Data provider for the parse_response function.
     *
     * @return array
     */
    public function parse_response_provider(): array {
        return [
            [['3', '142', '', ''], '3.142'],
            [['', '2', '', ''], '.2'],
            [['1', '', '', ''], '1.'],
            [['1', '0', '', ''], '1.0'],
            [['-1', '', '', ''], '-1.'],
            [['+1', '0', '', ''], '+1.0'],

            [['1', '', '4', ''], '1e4'],
            [['3', '142', '-4', ''], '3.142E-4'],
            [['', '2', '+2', ''], '.2e+2'],
            [['1', '', '-1', ''], '1.e-1'],
            [['1', '0', '0', ''], '1.0e0'],

            [['3', '', '8', ''], '3x10^8'],
            [['3', '', '8', ''], '3×10^8'],
            [['3', '0', '8', ''], '3.0*10^8'],
            [['3', '00', '-8', ''], '3.00x10**-8'],
            [['0', '001', '7', ''], '0.001×10**7'],

            [['1', '', '', 'm'], '1m'],
            [['3', '142', '', 'm'], '3.142 m'],
            [['', '2', '', 'm'], '.2m'],
            [['1', '', '', 'cm'], '1.cm'],
            [['1', '0', '', 'cm'], '1.0   cm'],
            [['-1', '', '', 'm'], '-1.m'],
            [['+1', '0', '', 'cm'], '+1.0cm'],

            [['1', '', '4', 'm'], '1e4 m'],
            [['3', '142', '-4', 'cm'], '3.142E-4  cm'],
            [['', '2', '+2', 'm'], '.2e+2m'],
            [['1', '', '-1', 'm'], '1.e-1 m'],
            [['1', '0', '0', 'cm'], '1.0e0cm'],

            [['1000000', '', '', ''], '1,000,000'],
            [['1000', '00', '', 'm'], '1,000.00 m'],

            [[null, null, null, null], 'frog'],
            [['3', '', '', 'frogs'], '3 frogs'],
            [[null, null, null, null], '. m'],
            [[null, null, null, null], '.e8 m'],
            [[null, null, null, null], ','],
        ];
    }

    /**
     * Call apply_units and verify the value and units returned.
     *
     * @param int|float $exectedval
     * @param null|string $expectedunit
     * @param int|float $expectedmultiplier
     * @param qtype_numerical_answer_processor $ap
     * @param null|int|float $input
     * @param null|string $separateunit
     */
    protected function verify_value_and_unit(
        $exectedval,
        $expectedunit,
        $expectedmultiplier,
        qtype_numerical_answer_processor $ap,
        $input,
        $separateunit = null
    ): void {
        [$val, $unit, $multiplier] = $ap->apply_units($input, $separateunit);
        if (is_null($exectedval)) {
            $this->assertNull($val);
        } else {
            $this->assertEqualsWithDelta($exectedval, $val, 0.0001);
        }
        $this->assertEquals($expectedunit, $unit);
        if (is_null($expectedmultiplier)) {
            $this->assertNull($multiplier);
        } else {
            $this->assertEqualsWithDelta($expectedmultiplier, $multiplier, 0.0001);
        }
    }

    /**
     * Test the apply_units function with various parameters.
     *
     * @covers \qtype_numerical_answer_processor::apply_units
     * @dataProvider apply_units_provider
     * @param mixed $expectedvalue
     * @param string|null $expectedunit
     * @param float|int|null $expectedmultiplier
     * @param string|null $input
     */
    public function test_apply_units(
        $expectedvalue,
        $expectedunit,
        $expectedmultiplier,
        $input
    ): void {
        $ap = new qtype_numerical_answer_processor(
            [
                'm/s' => 1,
                'c' => 3.3356409519815E-9,
                'mph' => 2.2369362920544
            ],
            false,
            '.',
            ','
        );

        $this->verify_value_and_unit(
            $expectedvalue,
            $expectedunit,
            $expectedmultiplier,
            $ap,
            $input
        );
    }

    /**
     * Data provider for apply_units tests.
     *
     * @return array
     */
    public function apply_units_provider(): array {
        return [
            [3e8, 'm/s', 1, '3x10^8 m/s'],
            [3e8, '', null, '3x10^8'],
            [1, 'c', 299792458, '1c'],
            [1, 'mph', 0.44704, '0001.000 mph'],

            [1, 'frogs', null, '1 frogs'],
            [null, null, null, '. m/s'],
            [null, null, null, null],
            [null, null, null, ''],
            [null, null, null, '    '],
        ];
    }

    /**
     * Test the apply_units function with various parameters and different units.
     *
     * @covers \qtype_numerical_answer_processor::apply_units
     * @dataProvider apply_units_provider_with_units
     * @param mixed $expectedvalue
     * @param string|null $expectedunit
     * @param float|int|null $expectedmultiplier
     * @param string|null $input
     * @param string $units
     */
    public function test_apply_units_with_unit(
        $expectedvalue,
        $expectedunit,
        $expectedmultiplier,
        $input,
        $units
    ): void {
        $ap = new qtype_numerical_answer_processor(
            [
                'm/s' => 1,
                'c' => 3.3356409519815E-9,
                'mph' => 2.2369362920544
            ],
            false,
            '.',
            ','
        );

        $this->verify_value_and_unit(
            $expectedvalue,
            $expectedunit,
            $expectedmultiplier,
            $ap,
            $input,
            $units
        );
    }

    /**
     * Data provider for apply_units with different units.
     *
     * @return array
     */
    public function apply_units_provider_with_units(): array {
        return [
            [3e8, 'm/s', 1, '3x10^8', 'm/s'],
            [3e8, '', null, '3x10^8', ''],
            [1, 'c', 299792458, '1', 'c'],
            [1, 'mph', 0.44704, '0001.000', 'mph'],

            [1, 'frogs', null, '1', 'frogs'],
            [null, null, null, '.', 'm/s'],
        ];
    }

    /**
     * Test apply_units with a comma float unit.
     *
     * @covers \qtype_numerical_answer_processor::apply_units
     * @dataProvider euro_provider
     * @param array $expected
     * @param string $params
     */
    public function test_euro_style(array $expected, string $params): void {
        $ap = new qtype_numerical_answer_processor([], false, ',', ' ');
        $this->assertEquals($expected, $ap->apply_units($params));
    }

    /**
     * Data provider for apply_units with euro float separators.
     *
     * return array
     */
    public function euro_provider(): array {
        return [
            [[-1000, '', null], '-1 000'],
            [[3.14159, '', null], '3,14159'],
        ];
    }

    /**
     * Test apply_units with percentage values.
     *
     * @covers \qtype_numerical_answer_processor::apply_units
     * @dataProvider percent_provider
     * @param array $expected
     * @param string $params
     */
    public function test_percent(array $expected, string $params): void {
        $ap = new qtype_numerical_answer_processor(['%' => 100], false, '.', ',');
        $this->assertEquals($expected, $ap->apply_units($params));
    }

    /**
     * Data provider for apply_units with percentages.
     *
     * @return array
     */
    public function percent_provider(): array {
        return [
            [['3', '%', 0.01], '3%'],
            [['1e-6', '%', 0.01], '1e-6 %'],
            [['100', '', null], '100'],
        ];
    }

    /**
     * Test apply_units with currency values.
     *
     * @covers \qtype_numerical_answer_processor::apply_units
     * @dataProvider currency_provider
     * @param array $expected
     * @param string $params
     */
    public function test_currency(array $expected, string $params): void {
        $ap = new qtype_numerical_answer_processor([
            '$' => 1,
            '£' => 1,
        ], true, '.', ',');
        $this->assertEquals($expected, $ap->apply_units($params));
    }

    /**
     * Data provider for apply_units with currency values.
     *
     * @return array
     */
    public function currency_provider(): array {
        return [
            [['1234.56', '£', 1], '£1,234.56'],
            [['100', '$', 1], '$100'],
            [['100', '$', 1], '$100.'],
            [['100.00', '$', 1], '$100.00'],
            [['100', '', null], '100'],
            [['100', 'frog', null], 'frog 100'],
        ];
    }
}
