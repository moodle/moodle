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
 * Test the ChangedConcatOperatorPrecedence sniff.
 *
 * @group changedConcatOperatorPrecedence
 * @group operators
 *
 * @covers \PHPCompatibility\Sniffs\Operators\ChangedConcatOperatorPrecedenceSniff
 *
 * @since 9.2.0
 */
class ChangedConcatOperatorPrecedenceUnitTest extends BaseSniffTest
{

    /**
     * testChangedConcatOperatorPrecedence
     *
     * @dataProvider dataChangedConcatOperatorPrecedence
     *
     * @param array $line The line number on which the warning/error should occur.
     *
     * @return void
     */
    public function testChangedConcatOperatorPrecedence($line)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertWarning($file, $line, 'Using an unparenthesized expression containing a "." before a "+" or "-" has been deprecated in PHP 7.4');

        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertError($file, $line, 'Using an unparenthesized expression containing a "." before a "+" or "-" has been deprecated in PHP 7.4 and removed in PHP 8.0');
    }

    /**
     * Data provider.
     *
     * @see testChangedConcatOperatorPrecedence()
     *
     * @return array
     */
    public function dataChangedConcatOperatorPrecedence()
    {
        return array(
            array(59),
            array(60),
            array(61),
            array(68),
            array(74),
            array(85),
            array(92),
            array(95),
        );
    }


    /**
     * Verify the sniff doesn't throw false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.4');

        for ($line = 1; $line < 57; $line++) {
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
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertNoViolation($file);
    }
}
