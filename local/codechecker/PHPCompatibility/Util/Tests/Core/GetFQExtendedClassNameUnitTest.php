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
 * Tests for the `getFQExtendedClassName()` utility function.
 *
 * @group utilityGetFQExtendedClassName
 * @group utilityFunctions
 *
 * @since 7.0.3
 */
class GetFQExtendedClassNameUnitTest extends CoreMethodTestFrame
{

    /**
     * testGetFQExtendedClassName
     *
     * @dataProvider dataGetFQExtendedClassName
     *
     * @covers \PHPCompatibility\Sniff::getFQExtendedClassName
     *
     * @param string $commentString The comment which prefaces the T_CLASS token in the test file.
     * @param string $expected      The expected fully qualified class name.
     *
     * @return void
     */
    public function testGetFQExtendedClassName($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, array(\T_CLASS, \T_INTERFACE));
        $result   = $this->helperClass->getFQExtendedClassName($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFQExtendedClassName
     *
     * @see testGetFQExtendedClassName()
     *
     * @return array
     */
    public function dataGetFQExtendedClassName()
    {
        return array(
            array('/* Case 1 */', ''),
            array('/* Case 2 */', '\DateTime'),
            array('/* Case 3 */', '\MyTesting\DateTime'),
            array('/* Case 4 */', '\DateTime'),
            array('/* Case 5 */', '\MyTesting\anotherNS\DateTime'),
            array('/* Case 6 */', '\FQNS\DateTime'),
            array('/* Case 7 */', '\AnotherTesting\DateTime'),
            array('/* Case 8 */', '\DateTime'),
            array('/* Case 9 */', '\AnotherTesting\anotherNS\DateTime'),
            array('/* Case 10 */', '\FQNS\DateTime'),
            array('/* Case 11 */', '\DateTime'),
            array('/* Case 12 */', '\DateTime'),
            array('/* Case 13 */', '\Yet\More\Testing\DateTime'),
            array('/* Case 14 */', '\Yet\More\Testing\anotherNS\DateTime'),
            array('/* Case 15 */', '\FQNS\DateTime'),
            array('/* Case 16 */', '\SomeInterface'),
            array('/* Case 17 */', '\Yet\More\Testing\SomeInterface'),
        );
    }
}
