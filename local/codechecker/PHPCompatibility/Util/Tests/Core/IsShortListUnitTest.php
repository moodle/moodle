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
 * Tests for the `isShortList()` utility function.
 *
 * @group utilityIsShortList
 * @group utilityFunctions
 *
 * @since 8.2.0
 */
class IsShortListUnitTest extends CoreMethodTestFrame
{

    /**
     * testIsShortList
     *
     * @dataProvider dataIsShortList
     *
     * @covers \PHPCompatibility\Sniff::isShortList
     *
     * @param string    $commentString The comment which prefaces the target token in the test file.
     * @param string    $expected      The expected boolean return value.
     * @param int|array $targetToken   The token(s) to test with.
     *
     * @return void
     */
    public function testIsShortList($commentString, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($commentString, $targetToken);
        $result   = $this->helperClass->isShortList($this->phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * dataIsShortList
     *
     * @see testIsShortList()
     *
     * @return array
     */
    public function dataIsShortList()
    {
        return array(
            array('/* Case 1 */', false, \T_ARRAY),
            array('/* Case 2 */', false, \T_LIST),
            array('/* Case 3 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case 4 */', true),
            array('/* Case 5 */', true, \T_CLOSE_SHORT_ARRAY),
            array('/* Case 6 */', true),
            array('/* Case 7 */', true),
            array('/* Case 8 */', true),
            array('/* Case 9 */', true),
            array('/* Case 10 */', true),
            array('/* Case 11 */', true, \T_CLOSE_SHORT_ARRAY),
            array('/* Case 12 */', true),
            array('/* Case 13 */', true),
            array('/* Case 14 */', true),
            array('/* Case 15 */', true),
            array('/* Case 16 */', true),
            array('/* Case 17 */', true),
            array('/* Case 18 */', true),
            array('/* Case 19 */', true),
            array('/* Case 20 */', true),
            array('/* Case 21 */', true),
            array('/* Case 22 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case 23 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case 24 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case 25 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case 26 */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
            array('/* Case final */', false, array(\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET)),
        );
    }
}
