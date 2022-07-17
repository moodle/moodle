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
 * Test the NewFunctionArrayDereferencing sniff.
 *
 * @group newFunctionArrayDereferencing
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewFunctionArrayDereferencingSniff
 *
 * @since 7.0.0
 */
class NewFunctionArrayDereferencingUnitTest extends BaseSniffTest
{

    /**
     * testArrayDereferencing
     *
     * @dataProvider dataArrayDereferencing
     *
     * @param int  $line            The line number.
     * @param bool $skipNoViolation Optional. Whether or not to test for no violation.
     *                              Defaults to false.
     *
     * @return void
     */
    public function testArrayDereferencing($line, $skipNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertError($file, $line, 'Function array dereferencing is not present in PHP version 5.3 or earlier');

        if ($skipNoViolation === false) {
            $file = $this->sniffFile(__FILE__, '5.4');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * dataArrayDereferencing
     *
     * @see testArrayDereferencing()
     *
     * @return array
     */
    public function dataArrayDereferencing()
    {
        return array(
            array(3),
            array(14),
            array(15),
            array(16),
            array(28, true),
            array(29, true),
        );
    }


    /**
     * testArrayDereferencingUsingCurlies
     *
     * @dataProvider dataArrayDereferencingUsingCurlies
     *
     * @param int $line Line number with valid code.
     *
     * @return void
     */
    public function testArrayDereferencingUsingCurlies($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Function array dereferencing using curly braces is not present in PHP version 5.6 or earlier');
    }

    /**
     * Data provider.
     *
     * @see testArrayDereferencingUsingCurlies()
     *
     * @return array
     */
    public function dataArrayDereferencingUsingCurlies()
    {
        return array(
            array(22),
            array(23),
            array(24),
            array(25),
            array(28),
            array(29),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number with valid code.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
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
            array(5),
            array(8),
            array(9),
            array(10),
            array(11),
            array(32),
            array(37),
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
