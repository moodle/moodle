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
 * Tests for the `getFQClassNameFromDoubleColonToken()` utility function.
 *
 * @group utilityGetFQClassNameFromDoubleColonToken
 * @group utilityFunctions
 *
 * @since 7.0.5
 */
class GetFQClassNameFromDoubleColonTokenUnitTest extends CoreMethodTestFrame
{

    /**
     * testGetFQClassNameFromDoubleColonToken
     *
     * @dataProvider dataGetFQClassNameFromDoubleColonToken
     *
     * @covers \PHPCompatibility\Sniff::getFQClassNameFromDoubleColonToken
     *
     * @param string $commentString The comment which prefaces the T_DOUBLE_COLON token in the test file.
     * @param string $expected      The expected fully qualified class name.
     *
     * @return void
     */
    public function testGetFQClassNameFromDoubleColonToken($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_DOUBLE_COLON);
        $result   = $this->helperClass->getFQClassNameFromDoubleColonToken($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFQClassNameFromDoubleColonToken
     *
     * @see testGetFQClassNameFromDoubleColonToken()
     *
     * @return array
     */
    public function dataGetFQClassNameFromDoubleColonToken()
    {
        return array(
            array('/* Case 1 */', '\DateTime'),
            array('/* Case 2 */', '\DateTime'),
            array('/* Case 3 */', '\DateTime'),
            array('/* Case 4 */', '\DateTime'),
            array('/* Case 5 */', '\DateTime'),
            array('/* Case 6 */', '\AnotherNS\DateTime'),
            array('/* Case 7 */', '\FQNS\DateTime'),
            array('/* Case 8 */', '\DateTime'),
            array('/* Case 9 */', '\AnotherNS\DateTime'),
            array('/* Case 10 */', '\Testing\DateTime'),
            array('/* Case 11 */', '\Testing\DateTime'),
            array('/* Case 12 */', '\Testing\DateTime'),
            array('/* Case 13 */', '\Testing\MyClass'),
            array('/* Case 14 */', ''),
            array('/* Case 15 */', ''),
            array('/* Case 16 */', '\MyClass'),
            array('/* Case 17 */', ''),
            array('/* Case 18 */', ''),
            array('/* Case 19 */', ''),
        );
    }
}
