<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\LanguageConstructs;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewEmptyNonVariable sniff.
 *
 * @group newEmptyNonVariable
 * @group languageConstructs
 *
 * @covers \PHPCompatibility\Sniffs\LanguageConstructs\NewEmptyNonVariableSniff
 * @covers \PHPCompatibility\Sniff::isVariable
 *
 * @since 7.0.4
 */
class NewEmptyNonVariableUnitTest extends BaseSniffTest
{

    /**
     * testEmptyNonVariable
     *
     * @dataProvider dataEmptyNonVariable
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testEmptyNonVariable($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'Only variables can be passed to empty() prior to PHP 5.5.');
    }

    /**
     * Data provider.
     *
     * @see testEmptyNonVariable()
     *
     * @return array
     */
    public function dataEmptyNonVariable()
    {
        return array(
            array(17),
            array(18),

            array(20),
            array(21),
            array(22),
            array(23),

            array(25),
            array(26),
            array(27),
            array(28),
            array(29),
            array(30),
            array(31),
            array(32),

            array(34),
            array(35),
            array(37),
            array(38),
            array(39),
            array(40),

            array(42),
            array(43),
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
            array(5),
            array(6),
            array(7),
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
            array(13),
            array(14),

            // Issue #210.
            array(47),
            array(48),
            array(49),
            array(50),
            array(51),
            array(52),
            array(53),
            array(54),
            array(55),
            array(56),
            array(57),
            array(58),
            array(59),
            array(60),
            array(61),

            // Live coding.
            array(65),
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
