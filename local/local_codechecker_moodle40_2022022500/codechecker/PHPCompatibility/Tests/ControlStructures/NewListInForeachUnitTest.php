<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\ControlStructures;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewListInForeach sniff.
 *
 * @group newListInForeach
 * @group controlStructures
 *
 * @covers \PHPCompatibility\Sniffs\ControlStructures\NewListInForeachSniff
 *
 * @since 9.0.0
 */
class NewListInForeachUnitTest extends BaseSniffTest
{

    /**
     * testNewListInForeach
     *
     * @dataProvider dataNewListInForeach
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testNewListInForeach($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'Unpacking nested arrays with list() in a foreach is not supported in PHP 5.4 or earlier.');
    }

    /**
     * dataNewListInForeach
     *
     * @see testNewListInForeach()
     *
     * @return array
     */
    public function dataNewListInForeach()
    {
        return array(
            array(14),
            array(17),
            array(18),
            array(19),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number with a valid list assignment.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
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
            array(6),
            array(7),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertNoViolation($file);
    }
}
