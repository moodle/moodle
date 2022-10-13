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
 * Test the ForbiddenBreakContinueVariableArguments sniff.
 *
 * @group forbiddenBreakContinueVariableArguments
 * @group controlStructures
 *
 * @covers \PHPCompatibility\Sniffs\ControlStructures\ForbiddenBreakContinueVariableArgumentsSniff
 *
 * @since 5.5
 */
class ForbiddenBreakContinueVariableArgumentsUnitTest extends BaseSniffTest
{

    /**
     * Error message snippet for the variable argument error.
     *
     * @var string
     */
    const ERROR_TYPE_VARIABLE = 'a variable argument';

    /**
     * Error message snippet for the zero argument error.
     *
     * @var string
     */
    const ERROR_TYPE_ZERO = '0 as an argument';

    /**
     * testBreakAndContinueVariableArgument
     *
     * @dataProvider dataBreakAndContinueVariableArgument
     *
     * @param int    $line      The line number.
     * @param string $errorType The error type.
     *
     * @return void
     */
    public function testBreakAndContinueVariableArgument($line, $errorType)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, "Using {$errorType} on break or continue is forbidden since PHP 5.4");
    }

    /**
     * Data provider.
     *
     * @see testBreakAndContinueVariableArgument()
     *
     * @return array
     */
    public function dataBreakAndContinueVariableArgument()
    {
        return array(
            array(53, self::ERROR_TYPE_VARIABLE),
            array(57, self::ERROR_TYPE_VARIABLE),
            array(62, self::ERROR_TYPE_VARIABLE),
            array(66, self::ERROR_TYPE_VARIABLE),
            array(71, self::ERROR_TYPE_VARIABLE),
            array(75, self::ERROR_TYPE_VARIABLE),
            array(80, self::ERROR_TYPE_VARIABLE),
            array(84, self::ERROR_TYPE_VARIABLE),
            array(89, self::ERROR_TYPE_VARIABLE),
            array(93, self::ERROR_TYPE_VARIABLE),
            array(98, self::ERROR_TYPE_VARIABLE),
            array(102, self::ERROR_TYPE_VARIABLE),
            array(107, self::ERROR_TYPE_ZERO),
            array(111, self::ERROR_TYPE_ZERO),
            array(118, self::ERROR_TYPE_ZERO),
            array(122, self::ERROR_TYPE_VARIABLE),
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
            array(8),
            array(12),
            array(17),
            array(21),
            array(26),
            array(30),
            array(35),
            array(39),
            array(44),
            array(48),
            array(126),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file);
    }
}
