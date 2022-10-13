<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionDeclarations;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the ForbiddenParametersWithSameName sniff.
 *
 * @group forbiddenParametersWithSameName
 * @group functionDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\ForbiddenParametersWithSameNameSniff
 *
 * @since 7.0.0
 */
class ForbiddenParametersWithSameNameUnitTest extends BaseSniffTest
{

    /**
     * testFunctionParametersWithSameName
     *
     * @dataProvider dataFunctionParametersWithSameName
     *
     * @param int $line Line number.
     *
     * @return void
     */
    public function testFunctionParametersWithSameName($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Functions can not have multiple parameters with the same name since PHP 7.0');
    }

    /**
     * dataFunctionParametersWithSameName
     *
     * @see testFunctionParametersWithSameName()
     *
     * @return array
     */
    public function dataFunctionParametersWithSameName()
    {
        return array(
            array(3),
            array(7),
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
            array(5),
            array(9),
            array(10),
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
