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
 * Test the NewConstantArraysUsingDefine sniff.
 *
 * @group newConstantArraysUsingDefine
 * @group initialValue
 *
 * @covers \PHPCompatibility\Sniffs\InitialValue\NewConstantArraysUsingDefineSniff
 *
 * @since 7.0.0
 */
class NewConstantArraysUsingDefineUnitTest extends BaseSniffTest
{

    /**
     * testConstantArraysUsingDefine
     *
     * @dataProvider dataConstantArraysUsingDefine
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testConstantArraysUsingDefine($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Constant arrays using define are not allowed in PHP 5.6 or earlier');
    }

    /**
     * Data provider dataConstantArraysUsingDefine.
     *
     * @see testConstantArraysUsingDefine()
     *
     * @return array
     */
    public function dataConstantArraysUsingDefine()
    {
        return array(
            array(3),
            array(9),
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
        $file = $this->sniffFile(__FILE__, '5.6');
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
            array(15),
            array(18),
            array(19),
            array(22),
            array(23),
            array(26),
            array(28),
            array(31),
            array(32),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file);
    }
}
