<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Generators;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewGeneratorReturn sniff.
 *
 * @group newGeneratorReturn
 * @group generators
 *
 * @covers \PHPCompatibility\Sniffs\Generators\NewGeneratorReturnSniff
 *
 * @since 8.2.0
 */
class NewGeneratorReturnUnitTest extends BaseSniffTest
{

    /**
     * testNewGeneratorReturn
     *
     * @dataProvider dataNewGeneratorReturn
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNewGeneratorReturn($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Returning a final expression from a generator was not supported in PHP 5.6 or earlier');
    }

    /**
     * Data provider dataNewGeneratorReturn.
     *
     * @see testNewGeneratorReturn()
     *
     * @return array
     */
    public function dataNewGeneratorReturn()
    {
        return array(
            array(30),
            array(35),
            array(39),
            array(64),
            array(83),
            array(101),
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
        $file = $this->sniffFile(__FILE__, '5.6');
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
            array(15),
            array(21),
            array(53),
            array(107),
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
