<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Keywords;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the ForbiddenNamesAsInvokedFunctions sniff.
 *
 * @group forbiddenNamesAsInvokedFunctions
 * @group keywords
 *
 * @covers \PHPCompatibility\Sniffs\Keywords\ForbiddenNamesAsInvokedFunctionsSniff
 *
 * @since 5.5
 */
class ForbiddenNamesAsInvokedFunctionsUnitTest extends BaseSniffTest
{

    /**
     * testReservedKeyword
     *
     * @dataProvider dataReservedKeyword
     *
     * @param string $keyword       Reserved keyword.
     * @param array  $linesFunction The line numbers in the test file which apply to this keyword as a function call.
     * @param array  $linesMethod   The line numbers in the test file which apply to this keyword as a method call.
     * @param string $introducedIn  The PHP version in which the keyword became a reserved word.
     * @param string $okVersion     A PHP version in which the keyword was not yet reserved.
     *
     * @return void
     */
    public function testReservedKeyword($keyword, $linesFunction, $linesMethod, $introducedIn, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $introducedIn);
        $error = "'{$keyword}' is a reserved keyword introduced in PHP version {$introducedIn} and cannot be invoked as a function";
        $lines = array_merge($linesFunction, $linesMethod);
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        if (empty($linesMethod) === true) {
            return;
        }

        // Test that method calls do not throw an error for PHP 7.0+.
        $file = $this->sniffFile(__FILE__, '7.0-');
        foreach ($linesMethod as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testReservedKeyword()
     *
     * @return array
     */
    public function dataReservedKeyword()
    {
        return array(
            array('abstract', array(6), array(53), '5.0', '4.4'),
            array('callable', array(7), array(54), '5.4', '5.3'),
            array('catch', array(8), array(55), '5.0', '4.4'),
            array('final', array(10), array(56), '5.0', '4.4'),
            array('finally', array(11), array(57), '5.5', '5.4'),
            array('goto', array(12), array(58), '5.3', '5.2'),
            array('implements', array(13), array(59), '5.0', '4.4'),
            array('interface', array(14), array(60), '5.0', '4.4'),
            array('instanceof', array(15), array(61), '5.0', '4.4'),
            array('insteadof', array(16), array(62), '5.4', '5.3'),
            array('namespace', array(17), array(63), '5.3', '5.2'),
            array('private', array(18), array(64), '5.0', '4.4'),
            array('protected', array(19), array(65), '5.0', '4.4'),
            array('public', array(20), array(66), '5.0', '4.4'),
            array('trait', array(22), array(67), '5.4', '5.3'),
            array('try', array(23), array(68), '5.0', '4.4'),
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
        $file = $this->sniffFile(__FILE__, '99.0'); // High number beyond any newly introduced keywords.
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
            array(34),
            array(35),
            array(36),
            array(37),
            array(38),
            array(39),
            array(40),
            array(41),
            array(42),
            array(43),
            array(44),
            array(45),
            array(46),
            array(47),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '4.4'); // Low version below the first introduced reserved word.
        $this->assertNoViolation($file);
    }
}
