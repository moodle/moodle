<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Constants;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewMagicClassConstant sniff.
 *
 * @group newMagicClassConstant
 * @group constants
 *
 * @covers \PHPCompatibility\Sniffs\Constants\NewMagicClassConstantSniff
 *
 * @since 7.1.4
 */
class NewMagicClassConstantUnitTest extends BaseSniffTest
{

    /**
     * testNewMagicClassConstant
     *
     * @dataProvider dataNewMagicClassConstant
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNewMagicClassConstant($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'The magic class constant ClassName::class was not available in PHP 5.4 or earlier');
    }

    /**
     * Data provider dataNewMagicClassConstant.
     *
     * @see testNewMagicClassConstant()
     *
     * @return array
     */
    public function dataNewMagicClassConstant()
    {
        return array(
            array(6),
            array(12),
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
        $file = $this->sniffFile(__FILE__, '5.4');
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
            array(4),
            array(10),
            array(18),
            array(19),
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
