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
 * Test the ReservedFunctionNames sniff.
 *
 * @group reservedFunctionNames
 * @group functionNameRestrictions
 *
 * @covers \PHPCompatibility\Sniffs\FunctionNameRestrictions\ReservedFunctionNamesSniff
 *
 * @since 8.2.0
 */
class ReservedFunctionNamesUnitTest extends BaseSniffTest
{

    /**
     * testReservedFunctionNames
     *
     * @dataProvider dataReservedFunctionNames
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testReservedFunctionNames($line)
    {
        $file = $this->sniffFile(__FILE__);
        $this->assertWarning($file, $line, ' is discouraged; PHP has reserved all method names with a double underscore prefix for future use.');
    }

    /**
     * Data provider.
     *
     * @see testReservedFunctionNames()
     *
     * @return array
     */
    public function dataReservedFunctionNames()
    {
        return array(
            array(20),
            array(21),
            array(22),

            array(25),
            array(26),
            array(27),
            array(28),
            array(29),
            array(30),
            array(31),
            array(32),
            array(33),
            array(34),
            array(35),
            array(37),
            array(38),
            array(39),
            array(41),
            array(42),

            array(92),
            array(93),
            array(94),

            array(107),
            array(109),
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
        $file = $this->sniffFile(__FILE__);
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
            array(5),
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
            array(17),
            array(18),
            array(19),

            array(40),
            array(50),
            array(51),
            array(52),
            array(54),

            array(58),
            array(63),
            array(66),
            array(69),
            array(72),

            array(77),
            array(78),
            array(79),
            array(80),
            array(81),
            array(82),
            array(83),
            array(84),
            array(85),
            array(86),
            array(87),
            array(88),
            array(89),
            array(90),
            array(91),

            array(98),
            array(101),
            array(102),

            array(124),
            array(135),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff operates
     *  independently of the testVersion.
     */
}
