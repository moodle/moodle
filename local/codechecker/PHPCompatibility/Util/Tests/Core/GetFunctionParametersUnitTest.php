<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Util\Tests\Core;

use PHPCompatibility\Util\Tests\CoreMethodTestFrame;

/**
 * Tests for the `getFunctionCallParameters()` and `getFunctionCallParameter()` utility functions.
 *
 * @group utilityGetFunctionParameters
 * @group utilityFunctions
 *
 * @since 7.0.5
 */
class GetFunctionParametersUnitTest extends CoreMethodTestFrame
{

    /**
     * testGetFunctionCallParameters
     *
     * @dataProvider dataGetFunctionCallParameters
     *
     * @covers \PHPCompatibility\Sniff::getFunctionCallParameters
     *
     * @param string $commentString The comment which prefaces the target token in the test file.
     * @param string $expected      The expected parameter array.
     *
     * @return void
     */
    public function testGetFunctionCallParameters($commentString, $expected)
    {
        switch ($commentString[8]) {
            case 'S':
                $stackPtr = $this->getTargetToken($commentString, array(\T_STRING));
                break;
            case 'A':
                $stackPtr = $this->getTargetToken($commentString, array(\T_ARRAY, \T_OPEN_SHORT_ARRAY));
                break;
            case 'V':
                $stackPtr = $this->getTargetToken($commentString, array(\T_VARIABLE));
                break;
        }

        /*
         * Start/end token position values in the expected array are set as offsets
         * in relation to the target token.
         *
         * Change these to exact positions based on the retrieved stackPtr.
         */
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = $stackPtr + $value['start'];
            $expected[$key]['end']   = $stackPtr + $value['end'];
        }

        $result = $this->helperClass->getFunctionCallParameters($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFunctionCallParameters
     *
     * @see testGetFunctionCallParameters()
     *
     * @return array
     */
    public function dataGetFunctionCallParameters()
    {
        return array(
            array(
                '/* Case S1 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ),
                    2 => array(
                        'start' => 5,
                        'end'   => 6,
                        'raw'   => '2',
                    ),
                    3 => array(
                        'start' => 8,
                        'end'   => 9,
                        'raw'   => '3',
                    ),
                    4 => array(
                        'start' => 11,
                        'end'   => 12,
                        'raw'   => '4',
                    ),
                    5 => array(
                        'start' => 14,
                        'end'   => 15,
                        'raw'   => '5',
                    ),
                    6 => array(
                        'start' => 17,
                        'end'   => 18,
                        'raw'   => '6',
                    ),
                    7 => array(
                        'start' => 20,
                        'end'   => 22,
                        'raw'   => 'true',
                    ),
                ),
            ),
            array(
                '/* Case S2 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 9,
                        'raw'   => 'dirname( __FILE__ )',
                    ),
                ),
            ),
            array(
                '/* Case S3 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$stHour',
                    ),
                    2 => array(
                        'start' => 4,
                        'end'   => 5,
                        'raw'   => '0',
                    ),
                    3 => array(
                        'start' => 7,
                        'end'   => 8,
                        'raw'   => '0',
                    ),
                    4 => array(
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => '$arrStDt[0]',
                    ),
                    5 => array(
                        'start' => 16,
                        'end'   => 20,
                        'raw'   => '$arrStDt[1]',
                    ),
                    6 => array(
                        'start' => 22,
                        'end'   => 26,
                        'raw'   => '$arrStDt[2]',
                    ),
                ),

            ),
            array(
                '/* Case S4 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 5,
                        'raw'   => 'array()',
                    ),
                ),
            ),
            array(
                '/* Case S5 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 34,
                        'raw'   => '[\'a\' => $a,] + (isset($b) ? [\'b\' => $b,] : [])',
                    ),
                ),
            ),
            array(
                '/* Case S6 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 90,
                        'raw'   => '/* Case A7 */
    [
        \'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        },
        \'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        },
    ]',
                    ),
                    2 => array(
                        'start' => 92,
                        'end'   => 95,
                        'raw'   => '$subject',
                    ),
                ),
            ),

            // Long array.
            array(
                '/* Case A1 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 8,
                        'raw'   => 'some_call(5, 1)',
                    ),
                    2 => array(
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => 'another(1)',
                    ),
                    3 => array(
                        'start' => 16,
                        'end'   => 26,
                        'raw'   => 'why(5, 1, 2)',
                    ),
                    4 => array(
                        'start' => 28,
                        'end'   => 29,
                        'raw'   => '4',
                    ),
                    5 => array(
                        'start' => 31,
                        'end'   => 32,
                        'raw'   => '5',
                    ),
                    6 => array(
                        'start' => 34,
                        'end'   => 35,
                        'raw'   => '6',
                    ),
                ),
            ),

            // Short array.
            array(
                '/* Case A2 */',
                array(
                    1 => array(
                        'start' => 1,
                        'end'   => 1,
                        'raw'   => '0',
                    ),
                    2 => array(
                        'start' => 3,
                        'end'   => 4,
                        'raw'   => '0',
                    ),
                    3 => array(
                        'start' => 6,
                        'end'   => 10,
                        'raw'   => 'date(\'s\')',
                    ),
                    4 => array(
                        'start' => 12,
                        'end'   => 16,
                        'raw'   => 'date(\'m\')',
                    ),
                    5 => array(
                        'start' => 18,
                        'end'   => 22,
                        'raw'   => 'date(\'d\')',
                    ),
                    6 => array(
                        'start' => 24,
                        'end'   => 28,
                        'raw'   => 'date(\'Y\')',
                    ),
                ),
            ),

            // Array containing closure.
            array(
                '/* Case A7 */',
                array(
                    1 => array(
                        'start' => 1,
                        'end'   => 38,
                        'raw'   => '\'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        }',
                    ),
                    2 => array(
                        'start' => 40,
                        'end'   => 79,
                        'raw'   => '\'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        }',
                    ),
                ),
            ),

            // Function calling closure in variable.
            array(
                '/* Case V1 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '&$a',
                    ),
                    2 => array(
                        'start' => 5,
                        'end'   => 12,
                        'raw'   => '(1 + 20)',
                    ),
                    3 => array(
                        'start' => 14,
                        'end'   => 20,
                        'raw'   => '$a & $b',
                    ),
                ),
            ),
            array(
                '/* Case V2 */',
                array(
                    1 => array(
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$a->property',
                    ),
                    2 => array(
                        'start' => 6,
                        'end'   => 12,
                        'raw'   => '$b->call()',
                    ),
                ),
            ),
        );
    }


    /**
     * testGetFunctionCallParameter
     *
     * @dataProvider dataGetFunctionCallParameter
     *
     * @covers \PHPCompatibility\Sniff::getFunctionCallParameter
     *
     * @param string $commentString The comment which prefaces the target token in the test file.
     * @param int    $paramPosition The position of the parameter we want to retrieve the details for.
     * @param string $expected      The expected array for the specific parameter.
     *
     * @return void
     */
    public function testGetFunctionCallParameter($commentString, $paramPosition, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, array(\T_STRING, \T_ARRAY, \T_OPEN_SHORT_ARRAY));
        /*
         * Start/end token position values in the expected array are set as offsets
         * in relation to the target token.
         *
         * Change these to exact positions based on the retrieved stackPtr.
         */
        $expected['start'] += $stackPtr;
        $expected['end']   += $stackPtr;

        $result = $this->helperClass->getFunctionCallParameter($this->phpcsFile, $stackPtr, $paramPosition);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFunctionCallParameter
     *
     * @see testGetFunctionCallParameter()
     *
     * @return array
     */
    public function dataGetFunctionCallParameter()
    {
        return array(
            array(
                '/* Case S1 */',
                4,
                array(
                    'start' => 11,
                    'end'   => 12,
                    'raw'   => '4',
                ),
            ),
            array(
                '/* Case S2 */',
                1,
                array(
                    'start' => 2,
                    'end'   => 9,
                    'raw'   => 'dirname( __FILE__ )',
                ),
            ),
            array(
                '/* Case S3 */',
                1,
                array(
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '$stHour',
                ),
            ),
            array(
                '/* Case S3 */',
                6,
                array(
                    'start' => 22,
                    'end'   => 26,
                    'raw'   => '$arrStDt[2]',
                ),
            ),
            array(
                '/* Case A3 */',
                1,
                array(
                    'start' => 2,
                    'end'   => 3,
                    'raw'   => '1',
                ),
            ),
            array(
                '/* Case A3 */',
                7,
                array(
                    'start' => 20,
                    'end'   => 22,
                    'raw'   => 'true',
                ),
            ),
            array(
                '/* Case A1 */',
                3,
                array(
                    'start' => 16,
                    'end'   => 26,
                    'raw'   => 'why(5, 1, 2)',
                ),
            ),
            array(
                '/* Case A4 */',
                2,
                array(
                    'start' => 8,
                    'end'   => 13,
                    'raw'   => '\'b\' => $b',
                ),
            ),
            array(
                '/* Case A5 */',
                1,
                array(
                    'start' => 1,
                    'end'   => 13,
                    'raw'   => 'str_replace("../", "/", trim($value))',
                ),
            ),
            array(
                '/* Case A6 */',
                3,
                array(
                    'start' => 14,
                    'end'   => 36,
                    'raw'   => '(isset($c) ? 6 => $c : 6 => null)',
                ),
            ),
        );
    }
}
