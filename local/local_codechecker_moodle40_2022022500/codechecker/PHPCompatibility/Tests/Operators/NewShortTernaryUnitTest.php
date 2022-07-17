<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Operators;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewShortTernary sniff.
 *
 * @group newShortTernary
 * @group operators
 *
 * @covers \PHPCompatibility\Sniffs\Operators\NewShortTernarySniff
 *
 * @since 7.0.0
 */
class NewShortTernaryUnitTest extends BaseSniffTest
{

    /**
     * testElvisOperator
     *
     * @dataProvider dataElvisOperator
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testElvisOperator($line)
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertError($file, $line, 'Middle may not be omitted from ternary operators in PHP < 5.3');
    }


    /**
     * dataElvisOperator
     *
     * @see testElvisOperator()
     *
     * @return array
     */
    public function dataElvisOperator()
    {
        return array(
            array(8),
            array(10),
        );
    }


    /**
     * Test ternary operators that are acceptable in all PHP versions.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file, 5);
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file);
    }
}
