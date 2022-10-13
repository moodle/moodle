<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\InitialValue;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewConstantArraysUsingConst sniff.
 *
 * @group newConstantArraysUsingConst
 * @group initialValue
 *
 * @covers \PHPCompatibility\Sniffs\InitialValue\NewConstantArraysUsingConstSniff
 *
 * @since 7.1.4
 */
class NewConstantArraysUsingConstUnitTest extends BaseSniffTest
{

    /**
     * testConstantArraysUsingConst
     *
     * @dataProvider dataConstantArraysUsingConst
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testConstantArraysUsingConst($line)
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertError($file, $line, 'Constant arrays using the "const" keyword are not allowed in PHP 5.5 or earlier');
    }

    /**
     * Data provider dataConstantArraysUsingConst.
     *
     * @see testConstantArraysUsingConst()
     *
     * @return array
     */
    public function dataConstantArraysUsingConst()
    {
        return array(
            array(3),
            array(4),
            array(6),
            array(12),
            array(19),
            array(25),
            array(37),
            array(39),
            array(41),
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
        $file = $this->sniffFile(__FILE__, '5.5');
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
            array(31),
            array(33),
            array(36),
            array(38),
            array(40),
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
