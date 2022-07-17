<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Variables;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the ForbiddenThisUseContexts sniff.
 *
 * @group forbiddenThisUseContexts
 * @group variables
 *
 * @covers \PHPCompatibility\Sniffs\Variables\ForbiddenThisUseContextsSniff
 *
 * @since 9.1.0
 */
class ForbiddenThisUseContextsUnitTest extends BaseSniffTest
{

    /**
     * Test $this being used as a parameter.
     *
     * @dataProvider dataIncompatibleThisUsageParam
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageParam($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used as a parameter since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsageParam()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageParam()
    {
        return array(
            array(16),
        );
    }


    /**
     * Test $this being used as a closure parameter.
     *
     * @dataProvider dataIncompatibleThisUsageClosureParam
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageClosureParam($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used as a closure parameter since PHP 7.0.7.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsageClosureParam()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageClosureParam()
    {
        return array(
            array(17), // x2.
            array(21),
        );
    }


    /**
     * Test against false positives: $this as param.
     *
     * @dataProvider dataNoFalsePositivesParam
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesParam($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesParam()
     *
     * @return array
     */
    public function dataNoFalsePositivesParam()
    {
        return array(
            array(6),
            array(7),
            array(11),
        );
    }


    /**
     * Test $this being used as global variable.
     *
     * @dataProvider dataIncompatibleThisUsageGlobal
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageGlobal($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used with the "global" keyword since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsageGlobal()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageGlobal()
    {
        return array(
            array(30),
            array(33),
            array(40),
        );
    }


    /**
     * Test against false positives: $this as global.
     *
     * @dataProvider dataNoFalsePositivesGlobal
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesGlobal($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesGlobal()
     *
     * @return array
     */
    public function dataNoFalsePositivesGlobal()
    {
        return array(
            array(38),
            array(39),
            array(41),
        );
    }


    /**
     * Test $this being used as catch variable.
     *
     * @dataProvider dataIncompatibleThisUsageCatch
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageCatch($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used as a catch variable since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsageCatch()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageCatch()
    {
        return array(
            array(54),
            array(56),
        );
    }


    /**
     * Test against false positives: $this as catch var.
     *
     * @dataProvider dataNoFalsePositivesCatch
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesCatch($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesCatch()
     *
     * @return array
     */
    public function dataNoFalsePositivesCatch()
    {
        return array(
            array(52),
        );
    }


    /**
     * Test $this being used as foreach value var.
     *
     * @dataProvider dataIncompatibleThisUsageForeach
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageForeach($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used as value variable in a foreach control structure since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsage()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageForeach()
    {
        return array(
            array(75),
            array(76),
        );
    }


    /**
     * Test against false positives: $this as foreach value var.
     *
     * @dataProvider dataNoFalsePositivesForeach
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesForeach($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesForeach()
     *
     * @return array
     */
    public function dataNoFalsePositivesForeach()
    {
        return array(
            array(63),
            array(67),
            array(72),
        );
    }


    /**
     * Test $this being unset.
     *
     * @dataProvider dataIncompatibleThisUsageUnset
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageUnset($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be unset since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsageUnset()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageUnset()
    {
        return array(
            array(97),
            array(98), // x2.
            array(102),
        );
    }


    /**
     * Test against false positives: $this in unset.
     *
     * @dataProvider dataNoFalsePositivesUnset
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesUnset($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesUnset()
     *
     * @return array
     */
    public function dataNoFalsePositivesUnset()
    {
        return array(
            array(83),
            array(86),
            array(92),
            array(100),
            array(101),
        );
    }


    /**
     * Test $this being used in plain functions.
     *
     * @dataProvider dataIncompatibleThisUsageOutsideObjectContext
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testIncompatibleThisUsageOutsideObjectContext($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertError($file, $line, '"$this" can no longer be used in a plain function or method since PHP 7.1.');
    }

    /**
     * Data provider.
     *
     * @see testIncompatibleThisUsage()
     *
     * @return array
     */
    public function dataIncompatibleThisUsageOutsideObjectContext()
    {
        return array(
            array(146),
            array(151),
        );
    }


    /**
     * Test against false positives: $this in plain functions.
     *
     * @dataProvider dataNoFalsePositivesOutsideObjectContext
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesOutsideObjectContext($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesOutsideObjectContext()
     *
     * @return array
     */
    public function dataNoFalsePositivesOutsideObjectContext()
    {
        return array(
            array(109),
            array(112),
            array(117),
            array(121),
            array(126),
            array(133),
            array(138),
            array(202), // Exception to the rule / static __call() magic method.
        );
    }


    /**
     * Test against false positives: uncovered cases.
     *
     * @return void
     */
    public function testNoFalsePositivesUncovered()
    {
        $file = $this->sniffFile(__FILE__, '7.1');

        // No errors expected on the first 14 lines.
        for ($line = 156; $line <= 205; $line++) {
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
