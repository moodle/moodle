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
 * Test the ForbiddenGetClassNull sniff.
 *
 * @group forbiddenGetClassNull
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\ForbiddenGetClassNullSniff
 *
 * @since 9.0.0
 */
class ForbiddenGetClassNullUnitTest extends BaseSniffTest
{

    /**
     * testGetClassNull
     *
     * @dataProvider dataGetClassNull
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testGetClassNull($line)
    {
        $file = $this->sniffFile(__FILE__, '7.2');
        $this->assertError($file, $line, 'Passing "null" as the $object to get_class() is not allowed since PHP 7.2.');
    }

    /**
     * dataGetClassNull
     *
     * @see testGetClassNull()
     *
     * @return array
     */
    public function dataGetClassNull()
    {
        return array(
            array(11),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.2');

        // No errors expected on the first 9 lines.
        for ($line = 1; $line <= 9; $line++) {
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
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file);
    }
}
