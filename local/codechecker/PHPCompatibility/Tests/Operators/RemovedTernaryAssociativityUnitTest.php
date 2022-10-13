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
 * Test the RemovedTernaryAssociativity sniff.
 *
 * @group removedTernaryAssociativity
 * @group operators
 *
 * @covers \PHPCompatibility\Sniffs\Operators\RemovedTernaryAssociativitySniff
 *
 * @since 9.2.0
 */
class RemovedTernaryAssociativityUnitTest extends BaseSniffTest
{

    /**
     * Total number of lines in the test case file (including blank lines at the end).
     *
     * @var int
     */
    protected $totalLines = 127;

    /**
     * Lines on which to expect errors.
     *
     * @var array
     */
    protected $problemLines = array(
        3,
        4,
        5,
        22,
        28,
        34,
        40,
        44,
        58,
        60, // x2.
        66,
        71,
        78,
        81,
        84,
        87,
        90,
        93,
        97,
        106,
        116,
        120,
        123,
        126,
    );


    /**
     * testRemovedTernaryAssociativity.
     *
     * @dataProvider dataRemovedTernaryAssociativity
     *
     * @param int $line The line number where a warning/error is expected.
     *
     * @return void
     */
    public function testRemovedTernaryAssociativity($line)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertWarning($file, $line, 'The left-associativity of the ternary operator has been deprecated in PHP 7.4. Multiple consecutive ternaries detected. Use parenthesis to clarify the order in which the operations should be executed');

        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertError($file, $line, 'The left-associativity of the ternary operator has been deprecated in PHP 7.4 and removed in PHP 8.0. Multiple consecutive ternaries detected. Use parenthesis to clarify the order in which the operations should be executed');
    }

    /**
     * Data provider.
     *
     * @see testRemovedTernaryAssociativity()
     *
     * @return array
     */
    public function dataRemovedTernaryAssociativity()
    {
        $cases = array();
        foreach ($this->problemLines as $line) {
            $cases[] = array($line);
        }

        return $cases;
    }


    /**
     * Verify the sniff doesn't throw false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file    = $this->sniffFile(__FILE__, '7.4');
        $exclude = array_flip($this->problemLines);

        for ($line = 1; $line <= $this->totalLines; $line++) {
            if (isset($exclude[$line])) {
                continue;
            }

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
