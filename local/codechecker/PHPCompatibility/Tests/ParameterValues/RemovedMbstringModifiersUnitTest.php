<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\ParameterValues;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedMbstringModifiers sniff.
 *
 * @group removedMbstringModifiers
 * @group parameterValues
 * @group regexModifiers
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedMbstringModifiersSniff
 *
 * @since 7.0.5
 */
class RemovedMbstringModifiersUnitTest extends BaseSniffTest
{

    /**
     * testMbstringEModifier
     *
     * @dataProvider dataMbstringEModifier
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testMbstringEModifier($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3-7.1');
        $this->assertWarning($file, $line, 'The Mbstring regex "e" modifier is deprecated since PHP 7.1.');

        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertWarning($file, $line, 'The Mbstring regex "e" modifier is deprecated since PHP 7.1. Use mb_ereg_replace_callback() instead (PHP 5.4.1+).');
    }

    /**
     * Data provider.
     *
     * @see testMbstringEModifier()
     *
     * @return array
     */
    public function dataMbstringEModifier()
    {
        return array(
            array(14),
            array(15),
            array(16),
            array(24),
            array(25),
            array(26),
            array(29),
            array(30),
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
        $file = $this->sniffFile(__FILE__, '7.1');
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
            array(9),
            array(10),
            array(11),
            array(19),
            array(20),
            array(21),
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
