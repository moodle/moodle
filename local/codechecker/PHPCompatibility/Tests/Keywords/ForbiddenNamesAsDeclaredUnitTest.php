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
 * Test the ForbiddenNamesAsDeclared sniff.
 *
 * @group forbiddenNamesAsDeclared
 * @group keywords
 *
 * @covers \PHPCompatibility\Sniffs\Keywords\ForbiddenNamesAsDeclaredSniff
 * @covers \PHPCompatibility\Sniff::getDeclaredNamespaceName
 *
 * @since 7.0.8
 */
class ForbiddenNamesAsDeclaredUnitTest extends BaseSniffTest
{

    /**
     * testReservedKeyword
     *
     * @dataProvider dataReservedKeyword
     *
     * @param string $keyword      Reserved keyword.
     * @param array  $lines        The line numbers in the test file which apply to this keyword.
     * @param string $introducedIn The PHP version in which the keyword became a reserved word.
     * @param string $okVersion    A PHP version in which the keyword was not yet reserved.
     *
     * @return void
     */
    public function testReservedKeyword($keyword, $lines, $introducedIn, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $introducedIn);
        $error = "'{$keyword}' is a reserved keyword as of PHP version {$introducedIn} and should not be used to name a class, interface or trait or as part of a namespace";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
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
            array('null', array(22, 36, 50, 64, 79, 94), '7.0', '5.6'),
            array('true', array(23, 37, 51, 65, 80, 95), '7.0', '5.6'),
            array('false', array(24, 38, 52, 66, 81, 96), '7.0', '5.6'),
            array('bool', array(25, 39, 53, 67, 82, 97), '7.0', '5.6'),
            array('int', array(26, 40, 54, 68, 83, 98), '7.0', '5.6'),
            array('float', array(27, 41, 55, 69, 84, 99), '7.0', '5.6'),
            array('string', array(28, 42, 56, 70, 85, 100), '7.0', '5.6'),
            array('iterable', array(33, 47, 61, 75, 90, 105), '7.1', '7.0'),
            array('void', array(34, 48, 62, 76, 91, 106), '7.1', '7.0'),
        );
    }


    /**
     * testSoftReservedKeyword
     *
     * @dataProvider dataSoftReservedKeyword
     *
     * @param string $keyword      Soft reserved keyword.
     * @param array  $lines        The line numbers in the test file which apply to this keyword.
     * @param string $introducedIn The PHP version in which the keyword became a reserved word.
     * @param string $okVersion    A PHP version in which the keyword was not yet reserved.
     *
     * @return void
     */
    public function testSoftReservedKeyword($keyword, $lines, $introducedIn, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $introducedIn);
        $error = "'{$keyword}' is a soft reserved keyword as of PHP version {$introducedIn} and should not be used to name a class, interface or trait or as part of a namespace";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testSoftReservedKeyword()
     *
     * @return array
     */
    public function dataSoftReservedKeyword()
    {
        return array(
            array('resource', array(29, 43, 57, 71, 86, 101), '7.0', '5.6'),
            array('mixed', array(31, 45, 59, 73, 88, 103), '7.0', '5.6'),
            array('numeric', array(32, 46, 60, 74, 89, 104), '7.0', '5.6'),
        );
    }


    /**
     * testSoftHardReservedKeyword
     *
     * @dataProvider dataSoftHardReservedKeyword
     *
     * @param string $keyword      Soft reserved keyword.
     * @param array  $lines        The line numbers in the test file which apply to this keyword.
     * @param string $introducedIn The PHP version in which the keyword became a reserved word.
     * @param string $okVersion    A PHP version in which the keyword was not yet reserved.
     * @param string $soft2Hard    The PHP version in which the keyword status changed from
     *                             soft reserved to (hard) reserved.
     *
     * @return void
     */
    public function testSoftHardReservedKeyword($keyword, $lines, $introducedIn, $okVersion, $soft2Hard)
    {
        // Test Soft reserved message and Ok version.
        $this->testSoftReservedKeyword($keyword, $lines, $introducedIn, $okVersion);

        // Test hard reserved message.
        $file  = $this->sniffFile(__FILE__, $soft2Hard);
        $error = "'{$keyword}' is a soft reserved keyword as of PHP version {$introducedIn} and a reserved keyword as of PHP version {$soft2Hard} and should not be used to name a class, interface or trait or as part of a namespace";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testSoftHardReservedKeyword()
     *
     * @return array
     */
    public function dataSoftHardReservedKeyword()
    {
        return array(
            array('object', array(30, 44, 58, 72, 87, 102), '7.0', '5.6', '7.2'),
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
        $file = $this->sniffFile(__FILE__, '99.0'); // High number beyond any newly introduced reserved words.
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
            array(6),
            array(7),
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
            array(13),
            array(14),
            array(15),
            array(16),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.6'); // Low version below the first introduced reserved word.
        $this->assertNoViolation($file);
    }
}
