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
 * Test the RemovedIconvEncoding sniff.
 *
 * @group removedIconvEncoding
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedIconvEncodingSniff
 *
 * @since 9.0.0
 */
class RemovedIconvEncodingUnitTest extends BaseSniffTest
{

    /**
     * testIconvEncoding
     *
     * @dataProvider dataIconvEncoding
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testIconvEncoding($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertWarning($file, $line, 'All previously accepted values for the $type parameter of iconv_set_encoding() have been deprecated since PHP 5.6.');
    }

    /**
     * dataIconvEncoding
     *
     * @see testIconvEncoding()
     *
     * @return array
     */
    public function dataIconvEncoding()
    {
        return array(
            array(14),
            array(15),
            array(16),
            array(17),
            array(18),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.6');

        // No errors expected on the first 10 lines.
        for ($line = 1; $line <= 10; $line++) {
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
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertNoViolation($file);
    }
}
