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
 * Tests for the `isNumericCalculation()` utility function.
 *
 * @group utilityIsNumericCalculation
 * @group utilityFunctions
 *
 * @since 9.0.0
 */
class IsNumericCalculationUnitTest extends CoreMethodTestFrame
{

    /**
     * testIsNumericCalculation
     *
     * @dataProvider dataIsNumericCalculation
     *
     * @covers \PHPCompatibility\Sniff::isNumericCalculation
     *
     * @param string $commentString The comment which prefaces the target snippet in the test file.
     * @param bool   $isCalc        The expected return value for isNumericCalculation().
     *
     * @return void
     */
    public function testIsNumericCalculation($commentString, $isCalc)
    {
        $start = ($this->getTargetToken($commentString, \T_EQUAL) + 1);
        $end   = ($this->getTargetToken($commentString, \T_SEMICOLON) - 1);

        $result = $this->helperClass->isNumericCalculation($this->phpcsFile, $start, $end);
        $this->assertSame($isCalc, $result);
    }

    /**
     * dataIsNumericCalculation
     *
     * @see testIsNumericCalculation()
     *
     * @return array
     */
    public function dataIsNumericCalculation()
    {
        return array(
            array('/* Case A1 */', false),
            array('/* Case A2 */', false),
            array('/* Case A3 */', false),
            array('/* Case A4 */', false),
            array('/* Case A5 */', false),

            array('/* Case B1 */', true),
            array('/* Case B2 */', true),
            array('/* Case B3 */', true),
            array('/* Case B4 */', true),
            array('/* Case B5 */', true),
            array('/* Case B6 */', true),
            array('/* Case B7 */', true),
        );
    }
}
