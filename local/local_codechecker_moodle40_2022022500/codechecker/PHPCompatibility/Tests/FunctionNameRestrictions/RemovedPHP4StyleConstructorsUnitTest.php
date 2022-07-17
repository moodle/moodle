<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionNameRestrictions;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedPHP4StyleConstructors sniff.
 *
 * @group removedPHP4StyleConstructors
 * @group functionNameRestrictions
 *
 * @covers \PHPCompatibility\Sniffs\FunctionNameRestrictions\RemovedPHP4StyleConstructorsSniff
 * @covers \PHPCompatibility\Sniff::determineNamespace
 *
 * @since 7.0.0
 */
class RemovedPHP4StyleConstructorsUnitTest extends BaseSniffTest
{

    /**
     * Test PHP4 style constructors.
     *
     * @dataProvider dataIsDeprecated
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testIsDeprecated($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertWarning($file, $line, 'Use of deprecated PHP4 style class constructor is not supported since PHP 7');
    }

    /**
     * dataIsDeprecated
     *
     * @see testIsDeprecated()
     *
     * @return array
     */
    public function dataIsDeprecated()
    {
        return array(
            array(3),
            array(18),
            array(33),
            array(66),
        );
    }


    /**
     * Test valid methods with the same name as the class.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number where the error should occur.
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
            array(9),
            array(12),
            array(26),
            array(37),
            array(41),
            array(42),
            array(47),
            array(51),
            array(53),
            array(65),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file);
    }
}
