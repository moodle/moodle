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
 * Test the RemovedMbStrrposEncodingThirdParam sniff.
 *
 * @group removedMbStrrposEncodingThirdParam
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedMbStrrposEncodingThirdParamSniff
 *
 * @since 9.3.0
 */
class RemovedMbStrrposEncodingThirdParamUnitTest extends BaseSniffTest
{

    /**
     * testRemovedMbStrrposEncodingThirdParam
     *
     * @dataProvider dataRemovedMbStrrposEncodingThirdParam
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testRemovedMbStrrposEncodingThirdParam($line)
    {
        $file  = $this->sniffFile(__FILE__, '5.2');
        $error = 'Passing the encoding to mb_strrpos() as third parameter is soft deprecated since PHP 5.2';
        $this->assertWarning($file, $line, $error);

        $file   = $this->sniffFile(__FILE__, '7.4');
        $error .= ' and hard deprecated since PHP 7.4.';
        $this->assertWarning($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testRemovedMbStrrposEncodingThirdParam()
     *
     * @return array
     */
    public function dataRemovedMbStrrposEncodingThirdParam()
    {
        return array(
            array(22),
            array(23),
            array(24),
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

        // No errors expected on the first 20 lines.
        for ($line = 1; $line <= 20; $line++) {
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
        $file = $this->sniffFile(__FILE__, '5.1');
        $this->assertNoViolation($file);
    }
}
