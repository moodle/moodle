<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Syntax;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewArrayUnpacking sniff.
 *
 * @group newArrayUnpacking
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewArrayUnpackingSniff
 *
 * @since 9.2.0
 */
class NewArrayUnpackingUnitTest extends BaseSniffTest
{

    /**
     * testNewArrayUnpacking
     *
     * @dataProvider dataNewArrayUnpacking
     *
     * @param array $line The line number on which the error should occur.
     *
     * @return void
     */
    public function testNewArrayUnpacking($line)
    {
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertError($file, $line, 'Array unpacking within array declarations using the spread operator is not supported in PHP 7.3 or earlier');
    }

    /**
     * Data provider.
     *
     * @see testNewArrayUnpacking()
     *
     * @return array
     */
    public function dataNewArrayUnpacking()
    {
        return array(
            array(11),
            array(14),
            array(15),
            array(17),
            array(18),
            array(22),
            array(23),
            array(27),
            array(28),
            array(33),
            array(34),
            array(35),
            array(38),
            array(42),
            array(43),
        );
    }


    /**
     * Verify the sniff doesn't throw false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.3');

        for ($line = 1; $line < 7; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertNoViolation($file);
    }
}
