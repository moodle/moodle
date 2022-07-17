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
 * Test the NewPackFormat sniff.
 *
 * @group newPackFormat
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewPackFormatSniff
 *
 * @since 9.0.0
 */
class NewPackFormatUnitTest extends BaseSniffTest
{

    /**
     * testNewPackFormat
     *
     * @dataProvider dataNewPackFormat
     *
     * @param int    $line           Line number where the error should occur.
     * @param string $code           Format code which should be detected.
     * @param string $errorVersion   The PHP version to use to test for the error.
     * @param string $okVersion      A PHP version in which the code is valid.
     * @param string $displayVersion Optional PHP version which is shown in the error message
     *                               if different from the $errorVersion.
     *
     * @return void
     */
    public function testNewPackFormat($line, $code, $errorVersion, $okVersion, $displayVersion = null)
    {
        $file  = $this->sniffFile(__FILE__, $errorVersion);
        $error = sprintf(
            'Passing the $format(s) "%s" to pack() is not supported in PHP %s or lower.',
            $code,
            isset($displayVersion) ? $displayVersion : $errorVersion
        );
        $this->assertError($file, $line, $error);

        $file = $this->sniffFile(__FILE__, $okVersion);
        $this->assertNoViolation($file, $line);
    }

    /**
     * dataNewPackFormat
     *
     * @see testNewPackFormat()
     *
     * @return array
     */
    public function dataNewPackFormat()
    {
        return array(
            array(8, 'Z', '5.4', '5.5'),
            array(9, 'q', '5.6', '7.0', '5.6.2'),
            array(10, 'Q', '5.6', '7.0', '5.6.2'),
            array(11, 'J', '5.6', '7.0', '5.6.2'),
            array(12, 'P', '5.6', '7.0', '5.6.2'),
            array(13, 'e', '7.0', '7.1', '7.0.14'),
            array(14, 'E', '7.0', '7.1', '7.0.14'),
            array(15, 'g', '7.0', '7.1', '7.0.14'),
            array(16, 'G', '7.0', '7.1', '7.0.14'),
            array(18, 'Z', '5.4', '7.1'), // OK version set to beyond last error.
            array(18, 'J', '5.6', '7.1', '5.6.2'), // OK version set to beyond last error.
            array(18, 'E', '7.0', '7.1', '7.0.14'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.4');

        // No errors expected on the first 6 lines.
        for ($line = 1; $line <= 6; $line++) {
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
