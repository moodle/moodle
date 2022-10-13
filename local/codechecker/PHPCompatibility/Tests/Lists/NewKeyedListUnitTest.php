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
 * Test the NewKeyedList sniff.
 *
 * @group newKeyedList
 * @group lists
 *
 * @covers \PHPCompatibility\Sniffs\Lists\NewKeyedListSniff
 *
 * @since 9.0.0
 */
class NewKeyedListUnitTest extends BaseSniffTest
{

    /**
     * testNewKeyedList
     *
     * @dataProvider dataNewKeyedList
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testNewKeyedList($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Specifying keys in list constructs is not supported in PHP 7.0 or earlier.');
    }

    /**
     * dataNewKeyedList
     *
     * @see testNewKeyedList()
     *
     * @return array
     */
    public function dataNewKeyedList()
    {
        return array(
            array(15), // x3.
            array(16), // x2.
            array(17), // x2.
            array(18),
            array(19), // x2.
            array(20), // x2.
            array(22), // x3.
            array(23), // x2.
            array(28),
            array(29),
            array(30),
            array(31),
            array(36), // x2.
            array(37), // x2.
            array(41), // x2.
            array(42), // x2.
            array(46),
            array(48),
            array(58),
            array(62),
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
            array(27),
            array(35),
            array(40),
            array(45),
            array(47),
            array(49),
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
