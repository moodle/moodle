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
 * Test the NewClassMemberAccess sniff.
 *
 * @group newClassMemberAccess
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewClassMemberAccessSniff
 *
 * @since 8.2.0
 */
class NewClassMemberAccessUnitTest extends BaseSniffTest
{

    /**
     * testNewClassMemberAccess
     *
     * @dataProvider dataNewClassMemberAccess
     *
     * @param int  $line            The line number.
     * @param bool $skipNoViolation Optional. Whether or not to test for no violation.
     *                              Defaults to false.
     *
     * @return void
     */
    public function testNewClassMemberAccess($line, $skipNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertError($file, $line, 'Class member access on object instantiation was not supported in PHP 5.3 or earlier');

        if ($skipNoViolation === false) {
            $file = $this->sniffFile(__FILE__, '5.4');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider dataNewClassMemberAccess.
     *
     * @see testNewClassMemberAccess()
     *
     * @return array
     */
    public function dataNewClassMemberAccess()
    {
        return array(
            array(41),
            array(42),
            array(43),
            array(45),
            array(47),
            array(48),
            array(49),
            array(51),
            array(52),
            array(54),
            array(58),
            array(60),
            array(61),
            array(62),
            array(65),
            array(70),
            array(76),
            array(79),
            array(82),
            array(87),
            array(91),
            array(96),
            array(117, true),
        );
    }


    /**
     * testNewClassMemberAccessUsingCurlies
     *
     * @dataProvider dataNewClassMemberAccessUsingCurlies
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNewClassMemberAccessUsingCurlies($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Class member access on object instantiation using curly braces was not supported in PHP 5.6 or earlier');
    }

    /**
     * Data provider.
     *
     * @see testNewClassMemberAccessUsingCurlies()
     *
     * @return array
     */
    public function dataNewClassMemberAccessUsingCurlies()
    {
        return array(
            array(111),
            array(112), // Error x 2.
            array(117),
        );
    }


    /**
     * testCloneClassMemberAccess
     *
     * @dataProvider dataCloneClassMemberAccess
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testCloneClassMemberAccess($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Class member access on object cloning was not supported in PHP 5.6 or earlier');
    }

    /**
     * Data provider dataCloneClassMemberAccess.
     *
     * @see testCloneClassMemberAccess()
     *
     * @return array
     */
    public function dataCloneClassMemberAccess()
    {
        return array(
            array(101),
            array(103),
            array(105),
            array(114),
            array(118), // Error x 2.
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.3');

        // No errors expected on the first 37 lines.
        for ($line = 1; $line <= 37; $line++) {
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
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file);
    }
}
