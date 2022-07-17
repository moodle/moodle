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
 * Test the ForbiddenCallTimePassByReference sniff.
 *
 * @group forbiddenCallTimePassByReference
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\ForbiddenCallTimePassByReferenceSniff
 *
 * @since 5.5
 */
class ForbiddenCallTimePassByReferenceUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenCallTimePassByReference
     *
     * @dataProvider dataForbiddenCallTimePassByReference
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testForbiddenCallTimePassByReference($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertWarning($file, $line, 'Using a call-time pass-by-reference is deprecated since PHP 5.3');

        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'Using a call-time pass-by-reference is deprecated since PHP 5.3 and prohibited since PHP 5.4');
    }

    /**
     * dataForbiddenCallTimePassByReference
     *
     * @see testForbiddenCallTimePassByReference()
     *
     * @return array
     */
    public function dataForbiddenCallTimePassByReference()
    {
        return array(
            array(10), // Bad: call time pass by reference.
            array(14), // Bad: call time pass by reference with multi-parameter call.
            array(17), // Bad: nested call time pass by reference.
            array(25), // Bad: call time pass by reference with space.
            array(44), // Bad: call time pass by reference.
            array(45), // Bad: call time pass by reference.
            array(49), // Bad: multiple call time pass by reference.
            array(71), // Bad: call time pass by reference.
            array(93), // Bad: call time pass by reference.
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
            array(4), // OK: Declaring a parameter by reference.
            array(9), // OK: Call time passing without reference.

            // OK: Bitwise operations as parameter.
            array(20),
            array(21),
            array(22),
            array(23),
            array(24),
            array(39),
            array(40),
            array(41),

            array(51), // OK: No variables.
            array(53), // OK: Outside scope of this sniff.

            // Assign by reference within function call.
            array(56),
            array(57),
            array(58),
            array(59),
            array(60),
            array(61),
            array(62),
            array(63),
            array(64),
            array(65),
            array(66),
            array(67),
            array(68),
            array(69),

            // Comparison with reference.
            array(74),
            array(75),

            // Issue #39 - Bitwise operations with (class) constants.
            array(78),
            array(79),
            array(80),

            // References in combination with closures.
            array(83),
            array(85),
            array(90),

            // Reference within an array argument.
            array(96),
            array(97),
            array(99),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file);
    }
}
