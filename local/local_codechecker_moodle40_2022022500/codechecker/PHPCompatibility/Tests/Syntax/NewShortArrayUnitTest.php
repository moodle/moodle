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
 * Test the NewShortArray sniff.
 *
 * @group newShortArray
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewShortArraySniff
 *
 * @since 7.0.0
 */
class NewShortArrayUnitTest extends BaseSniffTest
{

    /**
     * testViolation
     *
     * @dataProvider dataViolation
     *
     * @param int $lineOpen  The line number for the short array opener.
     * @param int $lineClose The line number for the short array closer.
     *
     * @return void
     */
    public function testViolation($lineOpen, $lineClose)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertError($file, $lineOpen, 'Short array syntax (open) is not supported in PHP 5.3 or lower');
        $this->assertError($file, $lineClose, 'Short array syntax (close) is not supported in PHP 5.3 or lower');
    }

    /**
     * Data provider.
     *
     * @see testViolation()
     *
     * @return array
     */
    public function dataViolation()
    {
        return array(
            array(12, 12),
            array(13, 13),
            array(14, 14),
            array(16, 19),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(5),
            array(6),
            array(7),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertNoViolation($file);
    }
}
