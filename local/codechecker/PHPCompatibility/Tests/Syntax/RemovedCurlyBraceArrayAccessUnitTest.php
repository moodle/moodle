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
 * Test the RemovedCurlyBraceArrayAccess sniff.
 *
 * @group removedCurlyBraceArrayAccess
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\RemovedCurlyBraceArrayAccessSniff
 *
 * @since 9.3.0
 */
class RemovedCurlyBraceArrayAccessUnitTest extends BaseSniffTest
{

    /**
     * testRemovedCurlyBraceArrayAccess
     *
     * @dataProvider dataRemovedCurlyBraceArrayAccess
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testRemovedCurlyBraceArrayAccess($line)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertWarning($file, $line, 'Curly brace syntax for accessing array elements and string offsets has been deprecated in PHP 7.4.');
    }

    /**
     * Data provider.
     *
     * @see testRemovedCurlyBraceArrayAccess()
     *
     * @return array
     */
    public function dataRemovedCurlyBraceArrayAccess()
    {
        return array(
            array(53),
            array(54),
            array(56),
            array(57),
            array(58),
            array(60), // x2.
            array(63),
            array(64),
            array(65), // x2.
            array(68),
            array(69),
            array(71),
            array(74),
            array(79),
            array(80),
            array(84),
            array(85),
            array(90),
            array(91),
            array(92), // x2.
            array(93),
            array(95),
            array(96),
            array(98), // x2.
            array(99), // x2.
            array(100), // x2.
            array(105),
            array(106),
            array(107), // x2.
            array(108),
            array(109),
            array(110),
            array(115),
            array(116), // x2.
            array(117),
            array(120),
            array(121),
            array(126),
            array(127), // x2.
            array(128),
            array(129),
            array(132),
            array(133),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.4');

        // No errors expected on the first 49 lines.
        for ($line = 1; $line <= 49; $line++) {
            $this->assertNoViolation($file, $line);
        }

        // ...and on the last few lines.
        for ($line = 135; $line <= 137; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertNoViolation($file);
    }
}
