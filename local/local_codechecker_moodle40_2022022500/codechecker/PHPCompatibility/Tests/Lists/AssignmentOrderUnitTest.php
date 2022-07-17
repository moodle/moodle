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
 * Test the AssignmentOrder sniff.
 *
 * @group assignmentOrder
 * @group lists
 *
 * @covers \PHPCompatibility\Sniffs\Lists\AssignmentOrderSniff
 *
 * @since 9.0.0
 */
class AssignmentOrderUnitTest extends BaseSniffTest
{

    /**
     * testAssignmentOrder
     *
     * @dataProvider dataAssignmentOrder
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testAssignmentOrder($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'list() will assign variable from left-to-right since PHP 7.0. Ensure all variables in list() are unique to prevent unexpected results.');
    }

    /**
     * dataAssignmentOrder
     *
     * @see testAssignmentOrder()
     *
     * @return array
     */
    public function dataAssignmentOrder()
    {
        return array(
            array(17),
            array(18),
            array(19),
            array(20),
            array(22),
            array(24),
            array(27),
            array(28),
            array(29),
            array(30),
            array(32),
            array(34),
            array(37),
            array(38),
            array(45),
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
            array(41),
            array(42),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file);
    }
}
