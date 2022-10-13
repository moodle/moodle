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
 * Test the NewDynamicAccessToStatic sniff.
 *
 * @group newDynamicAccessToStatic
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\NewDynamicAccessToStaticSniff
 *
 * @since 8.1.0
 */
class NewDynamicAccessToStaticUnitTest extends BaseSniffTest
{

    /**
     * testDynamicAccessToStatic
     *
     * @dataProvider dataDynamicAccessToStatic
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testDynamicAccessToStatic($line)
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertError($file, $line, 'Static class properties and methods, as well as class constants, could not be accessed using a dynamic (variable) classname in PHP 5.2 or earlier.');
    }

    /**
     * Data provider.
     *
     * @see testDynamicAccessToStatic()
     *
     * @return array
     */
    public function dataDynamicAccessToStatic()
    {
        return array(
            array(20),
            array(21),
            array(22),
            array(25),
            array(26),
            array(27),
            array(32),
            array(34),
            array(35),
            array(41),
            array(42),
            array(43),
            array(61),
            array(62),
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
        $file = $this->sniffFile(__FILE__, '5.2');
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
            array(14),
            array(15),
            array(16),
            array(50),
            array(51),
            array(53),
            array(54),
            array(57),
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
