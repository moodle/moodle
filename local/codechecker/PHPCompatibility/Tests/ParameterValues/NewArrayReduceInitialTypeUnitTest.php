<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\ParameterValues;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewArrayReduceInitialType sniff.
 *
 * @group newArrayReduceInitialType
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewArrayReduceInitialTypeSniff
 *
 * @since 9.0.0
 */
class NewArrayReduceInitialTypeUnitTest extends BaseSniffTest
{

    /**
     * testArrayReduceInitialType
     *
     * @dataProvider dataArrayReduceInitialType
     *
     * @param int  $line    Line number where the error should occur.
     * @param bool $isError Whether an error or a warning is expected.
     *                      Defaults to `true` (= error).
     *
     * @return void
     */
    public function testArrayReduceInitialType($line, $isError = true)
    {
        $file  = $this->sniffFile(__FILE__, '5.2');
        $error = 'Passing a non-integer as the value for $initial to array_reduce() is not supported in PHP 5.2 or lower.';

        if ($isError === true) {
            $this->assertError($file, $line, $error);
        } else {
            $this->assertWarning($file, $line, $error);
        }
    }

    /**
     * dataArrayReduceInitialType
     *
     * @see testArrayReduceInitialType()
     *
     * @return array
     */
    public function dataArrayReduceInitialType()
    {
        return array(
            array(16),

            array(19, false),
            array(20, false),
            array(21, false),
            array(22, false),
            array(23, false),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.2');

        // No errors expected on the first 14 lines.
        for ($line = 1; $line <= 14; $line++) {
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
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file);
    }
}
