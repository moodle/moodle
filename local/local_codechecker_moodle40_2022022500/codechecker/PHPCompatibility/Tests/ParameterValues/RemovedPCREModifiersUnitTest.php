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
 * Test the RemovedPCREModifiers sniff.
 *
 * @group removedPCREModifiers
 * @group parameterValues
 * @group regexModifiers
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedPCREModifiersSniff
 *
 * @since 5.6
 */
class RemovedPCREModifiersUnitTest extends BaseSniffTest
{

    /**
     * testDeprecatedEModifier
     *
     * @dataProvider dataDeprecatedEModifier
     *
     * @param int    $line         Line number where the error should occur.
     * @param string $functionName Function name.
     *
     * @return void
     */
    public function testDeprecatedEModifier($line, $functionName = 'preg_replace')
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertWarning($file, $line, "{$functionName}() - /e modifier is deprecated since PHP 5.5");

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, "{$functionName}() - /e modifier is deprecated since PHP 5.5 and removed since PHP 7.0");
    }

    /**
     * dataDeprecatedEModifier
     *
     * @see testDeprecatedEModifier()
     *
     * @return array
     */
    public function dataDeprecatedEModifier()
    {
        return array(
            // Function preg_replace().
            array(50),
            array(51),
            array(54),
            array(55),
            array(58),
            array(59),
            array(60),
            array(63),
            array(78),
            array(84),

            // Bracket delimiters.
            array(99),
            array(100),
            array(104),
            array(106),
            array(108),

            // Function preg_filter().
            array(114, 'preg_filter'),
            array(115, 'preg_filter'),
            array(118, 'preg_filter'),
            array(119, 'preg_filter'),
            array(122, 'preg_filter'),
            array(123, 'preg_filter'),
            array(124, 'preg_filter'),
            array(127, 'preg_filter'),
            array(142, 'preg_filter'),
            array(148, 'preg_filter'),

            // Array of patterns.
            array(162),
            array(163),
            array(164),
            array(165),
            array(166),

            array(173),
            array(174),
            array(175),
            array(176),
            array(177),

            array(182), // Three errors.

            // Interpolated variables.
            array(204),
            array(205),

            // Quote as a delimiter.
            array(211),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number where no error should occur.
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
            // No or only valid modifiers.
            array(9),
            array(10),
            array(13),
            array(14),
            array(17),
            array(18),
            array(21),
            array(24),
            array(39),
            array(45),

            // Untestable regex (variable, constant, function call).
            array(94),
            array(95),
            array(96),

            // Bracket delimiters.
            array(101),
            array(102),
            array(103),
            array(105),
            array(107),
            array(109),

            // Issue 265 - mixed string quotes.
            array(157),

            // Issues https://wordpress.org/support/topic/wrong-error-preg_replace-e-modifier-is-forbidden-since-php-7-0/
            array(167),
            array(178),
            array(187),
            array(201),

            // Interpolated variables.
            array(206),

            // Quote as a delimiter.
            array(210),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertNoViolation($file);
    }
}
