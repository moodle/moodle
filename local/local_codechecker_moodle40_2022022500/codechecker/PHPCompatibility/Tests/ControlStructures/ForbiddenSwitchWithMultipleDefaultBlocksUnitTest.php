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
 * Test the ForbiddenSwitchWithMultipleDefaultBlocks sniff.
 *
 * @group forbiddenSwitchWithMultipleDefaultBlocks
 * @group controlStructures
 *
 * @covers \PHPCompatibility\Sniffs\ControlStructures\ForbiddenSwitchWithMultipleDefaultBlocksSniff
 *
 * @since 7.0.0
 */
class ForbiddenSwitchWithMultipleDefaultBlocksUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenSwitchWithMultipleDefaultBlocks
     *
     * @dataProvider dataForbiddenSwitchWithMultipleDefaultBlocks
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testForbiddenSwitchWithMultipleDefaultBlocks($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Switch statements can not have multiple default blocks since PHP 7.0');
    }

    /**
     * Data provider.
     *
     * @see testForbiddenSwitchWithMultipleDefaultBlocks()
     *
     * @return array
     */
    public function dataForbiddenSwitchWithMultipleDefaultBlocks()
    {
        return array(
            array(3),
            array(47),
            array(56),
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
        $file = $this->sniffFile(__FILE__, '7.0');
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
            array(14),
            array(23),
            array(43),
            array(67), // Live coding.
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
