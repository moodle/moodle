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
 * Test the NewFunctionCallTrailingComma sniff.
 *
 * @group newFunctionCallTrailingComma
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewFunctionCallTrailingCommaSniff
 *
 * @since 8.2.0
 */
class NewFunctionCallTrailingCommaUnitTest extends BaseSniffTest
{

    /**
     * testTrailingComma
     *
     * @dataProvider dataTrailingComma
     *
     * @param int    $line The line number.
     * @param string $type The type detected.
     *
     * @return void
     */
    public function testTrailingComma($line, $type = 'function calls')
    {
        $file = $this->sniffFile(__FILE__, '7.2');
        $this->assertError($file, $line, "Trailing comma's are not allowed in {$type} in PHP 7.2 or earlier");
    }

    /**
     * Data provider.
     *
     * @see testTrailingComma()
     *
     * @return array
     */
    public function dataTrailingComma()
    {
        return array(
            array(15, 'calls to unset()'),
            array(16, 'calls to isset()'),
            array(21, 'calls to unset()'),
            array(27), // x2.
            array(33),
            array(36),
            array(38),
            array(40),
            array(44),
            array(47),
            array(49),
            array(52),
            array(62),
            array(65),
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
        $file = $this->sniffFile(__FILE__, '7.2');
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
            array(6),
            array(7),
            array(8),
            array(9),
            array(51),
            array(58),
            array(59),
            array(68),
            array(71),
        );
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
