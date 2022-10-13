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
 * Tests for the `getFunctionCallParameterCount()` utility function.
 *
 * @group utilityGetFunctionParameterCount
 * @group utilityFunctions
 *
 * @since 7.1.3 These tests were previously included in the GetFunctionParametersTest class.
 */
class GetFunctionParameterCountUnitTest extends CoreMethodTestFrame
{

    /**
     * testGetFunctionCallParameterCount
     *
     * @dataProvider dataGetFunctionCallParameterCount
     *
     * @covers \PHPCompatibility\Sniff::getFunctionCallParameterCount
     *
     * @param string $commentString The comment which prefaces the target token in the test file.
     * @param string $expected      The expected parameter count.
     *
     * @return void
     */
    public function testGetFunctionCallParameterCount($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, array(\T_STRING, \T_ARRAY, \T_OPEN_SHORT_ARRAY));
        $result   = $this->helperClass->getFunctionCallParameterCount($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFunctionCallParameterCount
     *
     * @see testGetFunctionCallParameterCount()
     *
     * @return array
     */
    public function dataGetFunctionCallParameterCount()
    {
        return array(
            array('/* Case S1 */', 1),
            array('/* Case S2 */', 2),
            array('/* Case S3 */', 3),
            array('/* Case S4 */', 4),
            array('/* Case S5 */', 5),
            array('/* Case S6 */', 6),
            array('/* Case S7 */', 7),
            array('/* Case S8 */', 1),
            array('/* Case S9 */', 1),
            array('/* Case S10 */', 1),
            array('/* Case S11 */', 2),
            array('/* Case S12 */', 1),
            array('/* Case S13 */', 1),
            array('/* Case S14 */', 1),
            array('/* Case S15 */', 2),
            array('/* Case S16 */', 6),
            array('/* Case S17 */', 6),
            array('/* Case S18 */', 6),
            array('/* Case S19 */', 6),
            array('/* Case S20 */', 6),
            array('/* Case S21 */', 6),
            array('/* Case S22 */', 6),
            array('/* Case S23 */', 3),
            array('/* Case S24 */', 1),
            array('/* Case S25 */', 1),

            // Issue #211.
            array('/* Case S26 */', 1),
            array('/* Case S27 */', 1),
            array('/* Case S28 */', 1),
            array('/* Case S29 */', 1),
            array('/* Case S30 */', 1),
            array('/* Case S31 */', 1),
            array('/* Case S32 */', 1),
            array('/* Case S33 */', 1),
            array('/* Case S34 */', 1),
            array('/* Case S35 */', 1),
            array('/* Case S36 */', 1),
            array('/* Case S37 */', 1),
            array('/* Case S38 */', 1),
            array('/* Case S39 */', 1),
            array('/* Case S40 */', 1),
            array('/* Case S41 */', 1),
            array('/* Case S42 */', 1),
            array('/* Case S43 */', 1),
            array('/* Case S44 */', 1),
            array('/* Case S45 */', 1),
            array('/* Case S46 */', 1),
            array('/* Case S47 */', 1),

            // Long arrays.
            array('/* Case A1 */', 7),
            array('/* Case A2 */', 1),
            array('/* Case A3 */', 6),
            array('/* Case A4 */', 6),
            array('/* Case A5 */', 6),
            array('/* Case A6 */', 3),
            array('/* Case A7 */', 3),
            array('/* Case A8 */', 3),

            // Short arrays.
            array('/* Case A9 */', 7),
            array('/* Case A10 */', 1),
            array('/* Case A11 */', 6),
            array('/* Case A12 */', 6),
            array('/* Case A13 */', 6),
            array('/* Case A14 */', 3),
            array('/* Case A15 */', 3),
            array('/* Case A16 */', 3),
        );
    }
}
