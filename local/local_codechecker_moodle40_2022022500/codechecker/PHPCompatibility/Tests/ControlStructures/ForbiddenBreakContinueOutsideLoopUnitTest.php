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
 * Test the ForbiddenBreakContinueOutsideLoop sniff.
 *
 * @group forbiddenBreakContinueOutsideLoop
 * @group controlStructures
 *
 * @covers \PHPCompatibility\Sniffs\ControlStructures\ForbiddenBreakContinueOutsideLoopSniff
 *
 * @since 7.0.7
 */
class ForbiddenBreakContinueOutsideLoopUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenBreakContinueOutsideLoop
     *
     * @dataProvider dataBreakContinueOutsideLoop
     *
     * @param int    $line  The line number.
     * @param string $found Either 'break' or 'continue'.
     *
     * @return void
     */
    public function testBreakContinueOutsideLoop($line, $found)
    {
        $file = $this->sniffFile(__FILE__, '5.4'); // Arbitrary pre-PHP7 version.
        $this->assertWarning($file, $line, "Using '{$found}' outside of a loop or switch structure is invalid");

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, "Using '{$found}' outside of a loop or switch structure is invalid and will throw a fatal error since PHP 7.0");
    }

    /**
     * Data provider.
     *
     * @see testBreakContinueOutsideLoop()
     *
     * @return array
     */
    public function dataBreakContinueOutsideLoop()
    {
        return array(
            array(116, 'continue'),
            array(118, 'continue'),
            array(120, 'break'),
            array(124, 'continue'),
            array(128, 'break'),
            array(131, 'continue'),
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
            array(11),
            array(17),
            array(20),
            array(26),
            array(29),
            array(36),
            array(39),
            array(47),
            array(51),
            array(54),
            array(60),
            array(63),
            array(69),
            array(72),
            array(78),
            array(81),
            array(89),
            array(93),
            array(96),
            array(103),
            array(106),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw a warning
     * on invalid use of the construct in pre-PHP 7 versions.
     */
}
