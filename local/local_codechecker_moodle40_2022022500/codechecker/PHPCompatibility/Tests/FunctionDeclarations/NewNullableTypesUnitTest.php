<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionDeclarations;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewNullableTypes sniff.
 *
 * @group nullableTypes
 * @group functionDeclarations
 * @group typeDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\NewNullableTypesSniff
 * @covers \PHPCompatibility\Sniff::getReturnTypeHintToken
 *
 * @since 7.0.7
 */
class NewNullableTypesUnitTest extends BaseSniffTest
{

    /**
     * testNewNullableReturnTypes
     *
     * @dataProvider dataNewNullableReturnTypes
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNewNullableReturnTypes($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Nullable return types are not supported in PHP 7.0 or earlier.');
    }

    /**
     * Data provider.
     *
     * @see testNewNullableReturnTypes()
     *
     * @return array
     */
    public function dataNewNullableReturnTypes()
    {
        return array(
            array(21),
            array(22),
            array(23),
            array(24),
            array(25),
            array(26),
            array(27),
            array(28),
            array(29),

            array(63),
            array(77),
        );
    }


    /**
     * testNewNullableTypeHints
     *
     * @dataProvider dataNewNullableTypeHints
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNewNullableTypeHints($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Nullable type declarations are not supported in PHP 7.0 or earlier.');
    }

    /**
     * Data provider.
     *
     * @see testNewNullableTypeHints()
     *
     * @return array
     */
    public function dataNewNullableTypeHints()
    {
        return array(
            array(48),
            array(49),
            array(50),
            array(51),
            array(52),
            array(53),
            array(54),
            array(55),
            array(56),

            array(59), // Three errors of the same.

            array(64),
            array(68),
            array(74),
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
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
            array(13),
            array(14),
            array(15),
            array(16),

            array(35),
            array(36),
            array(37),
            array(38),
            array(39),
            array(40),
            array(41),
            array(42),
            array(43),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file);
    }
}
