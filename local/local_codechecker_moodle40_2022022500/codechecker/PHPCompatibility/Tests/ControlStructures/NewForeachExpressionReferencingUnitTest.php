<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\ControlStructures;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewForeachExpressionReferencing sniff.
 *
 * @group newForeachExpressionReferencing
 * @group controlStructures
 *
 * @covers \PHPCompatibility\Sniffs\ControlStructures\NewForeachExpressionReferencingSniff
 * @covers \PHPCompatibility\Sniff::isVariable
 *
 * @since 9.0.0
 */
class NewForeachExpressionReferencingUnitTest extends BaseSniffTest
{

    /**
     * testNewForeachExpressionReferencing
     *
     * @dataProvider dataNewForeachExpressionReferencing
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testNewForeachExpressionReferencing($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'Referencing $value is only possible if the iterated array is a variable in PHP 5.4 or earlier.');
    }

    /**
     * dataNewForeachExpressionReferencing
     *
     * @see testNewForeachExpressionReferencing()
     *
     * @return array
     */
    public function dataNewForeachExpressionReferencing()
    {
        return array(
            array(17),
            array(18),
            array(20),
            array(21),
            array(23),
            array(24),
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
        $file = $this->sniffFile(__FILE__, '5.4');
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
            array(7),
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertNoViolation($file);
    }
}
