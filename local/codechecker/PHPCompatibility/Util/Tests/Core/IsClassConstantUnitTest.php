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
 * Tests for the `isClassConstant()` utility function.
 *
 * @group utilityIsClassConstant
 * @group utilityFunctions
 *
 * @since 7.1.4
 */
class IsClassConstantUnitTest extends CoreMethodTestFrame
{

    /**
     * testIsClassConstant
     *
     * @dataProvider dataIsClassConstant
     *
     * @covers \PHPCompatibility\Sniff::isClassConstant
     * @covers \PHPCompatibility\Sniff::validDirectScope
     *
     * @param string $commentString The comment which prefaces the target token in the test file.
     * @param string $expected      The expected boolean return value.
     *
     * @return void
     */
    public function testIsClassConstant($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_CONST);
        $result   = $this->helperClass->isClassConstant($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataIsClassConstant
     *
     * @see testIsClassConstant()
     *
     * @return array
     */
    public function dataIsClassConstant()
    {
        return array(
            array('/* Case 1 */', false),
            array('/* Case 2 */', false),
            array('/* Case 3 */', true),
            array('/* Case 4 */', false),
            array('/* Case 5 */', true),
            array('/* Case 6 */', false),
            array('/* Case 7 */', true),
            array('/* Case 8 */', false),
            array('/* Case 9 */', false),
        );
    }
}
