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
 * Test the NewArrayStringDereferencing sniff.
 *
 * @group newArrayStringDereferencing
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewArrayStringDereferencingSniff
 *
 * @since 7.1.4
 */
class NewArrayStringDereferencingUnitTest extends BaseSniffTest
{

    /**
     * testArrayStringDereferencing
     *
     * @dataProvider dataArrayStringDereferencing
     *
     * @param int    $line            The line number.
     * @param string $type            Whether this is an array or string dereferencing.
     * @param bool   $skipNoViolation Optional. Whether or not to test for no violation.
     *                                Defaults to false.
     *
     * @return void
     */
    public function testArrayStringDereferencing($line, $type, $skipNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, "Direct array dereferencing of {$type} is not present in PHP version 5.4 or earlier");

        if ($skipNoViolation === false) {
            $file = $this->sniffFile(__FILE__, '5.5');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider dataArrayStringDereferencing.
     *
     * @see testArrayStringDereferencing()
     *
     * @return array
     */
    public function dataArrayStringDereferencing()
    {
        return array(
            array(4, 'arrays'),
            array(5, 'arrays'),
            array(6, 'arrays'), // Error x 2.
            array(7, 'string literals'),
            array(8, 'string literals'),
            array(27, 'arrays', true),
            array(28, 'arrays', true),
        );
    }


    /**
     * testArrayStringDereferencingUsingCurlies
     *
     * @dataProvider dataArrayStringDereferencingUsingCurlies
     *
     * @param int    $line The line number.
     * @param string $type Whether this is an array or string dereferencing.
     *
     * @return void
     */
    public function testArrayStringDereferencingUsingCurlies($line, $type)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, "Direct array dereferencing of {$type} using curly braces is not present in PHP version 5.6 or earlier");
    }

    /**
     * Data provider.
     *
     * @see testArrayStringDereferencingUsingCurlies()
     *
     * @return array
     */
    public function dataArrayStringDereferencingUsingCurlies()
    {
        return array(
            array(20, 'arrays'),
            array(21, 'arrays'),
            array(22, 'arrays'), // Error x 2.
            array(23, 'string literals'),
            array(24, 'string literals'),
            array(27, 'arrays'),
            array(28, 'arrays'),
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
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file);
    }
}
