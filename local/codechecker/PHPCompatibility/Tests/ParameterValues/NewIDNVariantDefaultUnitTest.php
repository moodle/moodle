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
 * Test the NewIDNVariantDefault sniff.
 *
 * @group newIDNVariantDefault
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewIDNVariantDefaultSniff
 *
 * @since 9.3.0
 */
class NewIDNVariantDefaultUnitTest extends BaseSniffTest
{

    /**
     * testNewIDNVariantDefault
     *
     * @dataProvider dataNewIDNVariantDefault
     *
     * @param int    $line     Line number where the error should occur.
     * @param string $function Function name.
     *
     * @return void
     */
    public function testNewIDNVariantDefault($line, $function)
    {
        $file  = $this->sniffFile(__FILE__, '7.3-');
        $error = 'The default value of the ' . $function . '() $variant parameter has changed from INTL_IDNA_VARIANT_2003 to INTL_IDNA_VARIANT_UTS46 in PHP 7.4.';

        $this->assertError($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testNewIDNVariantDefault()
     *
     * @return array
     */
    public function dataNewIDNVariantDefault()
    {
        return array(
            array(10, 'idn_to_ascii'),
            array(11, 'idn_to_ascii'),
            array(12, 'IDN_to_utf8'),
            array(13, 'idn_to_utf8'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.3-');

        // No errors expected on the first 8 lines.
        for ($line = 1; $line <= 8; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @dataProvider dataNoViolationsInFileOnValidVersion
     *
     * @param string $testVersion The testVersion to use.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion($testVersion)
    {
        $file = $this->sniffFile(__FILE__, $testVersion);
        $this->assertNoViolation($file);
    }

    /**
     * Data provider.
     *
     * @see testNoViolationsInFileOnValidVersion()
     *
     * @return array
     */
    public function dataNoViolationsInFileOnValidVersion()
    {
        return array(
            array('7.1-7.3'),
            array('7.4-'),
        );
    }
}
