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
 * Tests for the `isNumber()`, `isPositiveNumber()` and `isNegativeNumber()` utility functions.
 *
 * @group utilityIsNumber
 * @group utilityFunctions
 *
 * @since 8.2.0
 */
class IsNumberUnitTest extends CoreMethodTestFrame
{

    /**
     * testIsNumber
     *
     * @dataProvider dataIsNumber
     *
     * @covers \PHPCompatibility\Sniff::isNumber
     * @covers \PHPCompatibility\Sniff::isPositiveNumber
     * @covers \PHPCompatibility\Sniff::isNegativeNumber
     *
     * @param string     $commentString    The comment which prefaces the target snippet in the test file.
     * @param bool       $allowFloats      Testing the snippets for integers only or floats as well ?
     * @param float|bool $isNumber         The expected return value for isNumber().
     * @param bool       $isPositiveNumber The expected return value for isPositiveNumber().
     * @param bool       $isNegativeNumber The expected return value for isNegativeNumber().
     *
     * @return void
     */
    public function testIsNumber($commentString, $allowFloats, $isNumber, $isPositiveNumber, $isNegativeNumber)
    {
        $start = ($this->getTargetToken($commentString, \T_EQUAL) + 1);
        $end   = ($this->getTargetToken($commentString, \T_SEMICOLON) - 1);

        $result = $this->helperClass->isNumber($this->phpcsFile, $start, $end, $allowFloats);
        $this->assertSame($isNumber, $result);

        $result = $this->helperClass->isPositiveNumber($this->phpcsFile, $start, $end, $allowFloats);
        $this->assertSame($isPositiveNumber, $result);

        $result = $this->helperClass->isNegativeNumber($this->phpcsFile, $start, $end, $allowFloats);
        $this->assertSame($isNegativeNumber, $result);
    }

    /**
     * dataIsNumber
     *
     * @see testIsNumber()
     *
     * {@internal Case I13 is not tested here on purpose as the result depends on the
     * `testVersion` which we don't use in the utility tests.
     * For a `testVersion` with a minimum of PHP 7.0, the result will be false.
     * For a `testVersion` which includes any PHP 5 version, the result will be true.}
     *
     * @return array
     */
    public function dataIsNumber()
    {
        return array(
            array('/* Case 1 */', true, false, false, false),
            array('/* Case 2 */', true, false, false, false),
            array('/* Case 4 */', true, false, false, false),
            array('/* Case 5 */', true, false, false, false),
            array('/* Case 6 */', true, false, false, false),
            array('/* Case 7 */', true, false, false, false),
            array('/* Case 8 */', true, false, false, false),
            array('/* Case 9 */', true, false, false, false),
            array('/* Case 10 */', true, false, false, false),

            array('/* Case ZI1 */', false, 0, false, false),
            array('/* Case ZI2 */', false, 0, false, false),
            array('/* Case ZI3 */', false, -0, false, false),
            array('/* Case ZI4 */', false, 0, false, false),
            array('/* Case ZI5 */', false, -0, false, false),
            array('/* Case ZI6 */', false, 0, false, false),
            array('/* Case ZI7 */', false, 0, false, false),

            array('/* Case ZI1 */', true, 0.0, false, false),
            array('/* Case ZI2 */', true, 0.0, false, false),
            array('/* Case ZI3 */', true, -0.0, false, false),
            array('/* Case ZI4 */', true, 0.0, false, false),
            array('/* Case ZI5 */', true, -0.0, false, false),
            array('/* Case ZI6 */', true, 0.0, false, false),
            array('/* Case ZI7 */', true, 0.0, false, false),

            array('/* Case ZF1 */', false, false, false, false),
            array('/* Case ZF2 */', false, false, false, false),

            array('/* Case ZF1 */', true, 0.0, false, false),
            array('/* Case ZF2 */', true, -0.0, false, false),

            array('/* Case I1 */', false, 1, true, false),
            array('/* Case I2 */', false, -10, false, true),
            array('/* Case I3 */', false, 10, true, false),
            array('/* Case I4 */', false, -10, false, true),
            array('/* Case I5 */', false, 10, true, false),
            array('/* Case I6 */', false, 10, true, false),
            array('/* Case I7 */', false, 10, true, false),
            array('/* Case I8 */', false, -10, false, true),
            array('/* Case I9 */', false, 10, true, false),
            array('/* Case I10 */', false, -1, false, true),
            array('/* Case I11 */', false, 10, true, false),
            array('/* Case I12 */', false, 10, true, false),
            array('/* Case I14 */', false, -1, false, true),
            array('/* Case I15 */', false, 123, true, false),
            array('/* Case I16 */', false, 10, true, false),

            array('/* Case I1 */', true, 1.0, true, false),
            array('/* Case I2 */', true, -10.0, false, true),
            array('/* Case I3 */', true, 10.0, true, false),
            array('/* Case I4 */', true, -10.0, false, true),
            array('/* Case I5 */', true, 10.0, true, false),
            array('/* Case I6 */', true, 10.0, true, false),
            array('/* Case I7 */', true, 10.0, true, false),
            array('/* Case I8 */', true, -10.0, false, true),
            array('/* Case I9 */', true, 10.0, true, false),
            array('/* Case I10 */', true, -1.0, false, true),
            array('/* Case I11 */', true, 10.0, true, false),
            array('/* Case I12 */', true, 10.0, true, false),
            array('/* Case I14 */', true, -1.0, false, true),
            array('/* Case I15 */', true, 123.0, true, false),
            array('/* Case I16 */', true, 10.0, true, false),

            array('/* Case F1 */', false, false, false, false),
            array('/* Case F2 */', false, false, false, false),
            array('/* Case F3 */', false, false, false, false),
            array('/* Case F4 */', false, false, false, false),
            array('/* Case F5 */', false, false, false, false),
            array('/* Case F6 */', false, false, false, false),
            array('/* Case F7 */', false, false, false, false),
            array('/* Case F8 */', false, false, false, false),
            array('/* Case F9 */', false, false, false, false),
            array('/* Case F10 */', false, false, false, false),
            array('/* Case F11 */', false, false, false, false),

            array('/* Case F1 */', true, 1.23, true, false),
            array('/* Case F2 */', true, -10.123, false, true),
            array('/* Case F3 */', true, 10.123, true, false),
            array('/* Case F4 */', true, -10.123, false, true),
            array('/* Case F5 */', true, 10.123, true, false),
            array('/* Case F6 */', true, 10.123, true, false),
            array('/* Case F7 */', true, 10.123, true, false),
            array('/* Case F8 */', true, -10E3, false, true),
            array('/* Case F9 */', true, -10e8, false, true),
            array('/* Case F10 */', true, 10.123, true, false),
            array('/* Case F11 */', true, 0.123, true, false),
        );
    }
}
