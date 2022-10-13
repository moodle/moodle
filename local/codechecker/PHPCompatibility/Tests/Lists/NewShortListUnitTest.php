<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Lists;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewShortList sniff.
 *
 * @group newShortList
 * @group lists
 *
 * @covers \PHPCompatibility\Sniffs\Lists\NewShortListSniff
 *
 * @since 9.0.0
 */
class NewShortListUnitTest extends BaseSniffTest
{

    /**
     * testShortList
     *
     * @dataProvider dataShortList
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testShortList($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'The shorthand list syntax "[]" to destructure arrays is not available in PHP 7.0 or earlier.');
    }

    /**
     * dataShortList
     *
     * @see testShortList()
     *
     * @return array
     */
    public function dataShortList()
    {
        return array(
            array(17),
            array(18),
            array(19),
            array(21),
            array(23),
            array(25), // x2.
            array(28),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number with a valid list assignment.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file, $line);
    }

    /**
     * dataNoFalsePositives
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(6),
            array(8),
            array(10),
            array(12),
            array(31),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file);
    }
}
