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
 * Test the NewIconvMbstringCharsetDefault sniff.
 *
 * @group newIconvMbstringCharsetDefault
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewIconvMbstringCharsetDefaultSniff
 *
 * @since 9.3.0
 */
class NewIconvMbstringCharsetDefaultUnitTest extends BaseSniffTest
{

    /**
     * testNewIconvMbstringCharsetDefault
     *
     * @dataProvider dataNewIconvMbstringCharsetDefault
     *
     * @param int    $line      Line number where the error should occur.
     * @param string $function  The name of the function called.
     * @param string $paramName The name of parameter which is missing.
     *                          Defaults to `$encoding`.
     *
     * @return void
     */
    public function testNewIconvMbstringCharsetDefault($line, $function, $paramName = '$encoding')
    {
        $file  = $this->sniffFile(__FILE__, '5.4-7.0');
        $error = "The default value of the {$paramName} parameter for {$function}() was changed from ISO-8859-1 to UTF-8 in PHP 5.6";

        $this->assertError($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testNewIconvMbstringCharsetDefault()
     *
     * @return array
     */
    public function dataNewIconvMbstringCharsetDefault()
    {
        return array(
            array(44, 'iconv_mime_decode_headers', '$charset'),
            array(45, 'iconv_mime_decode', '$charset'),
            array(46, 'Iconv_Strlen', '$charset'),
            array(47, 'iconv_strpos', '$charset'),
            array(48, 'iconv_strrpos', '$charset'),
            array(49, 'iconv_substr', '$charset'),

            array(51, 'mb_check_encoding'),
            array(52, 'MB_chr'),
            array(53, 'mb_convert_case'),
            array(54, 'mb_convert_encoding', '$from_encoding'),
            array(55, 'mb_convert_kana'),
            array(56, 'mb_decode_numericentity'),
            array(57, 'mb_encode_numericentity'),
            array(58, 'mb_ord'),
            array(59, 'mb_scrub'),
            array(60, 'mb_strcut'),
            array(61, 'mb_stripos'),
            array(62, 'mb_stristr'),
            array(63, 'mb_strlen'),
            array(64, 'mb_strpos'),
            array(65, 'mb_strrchr'),
            array(66, 'mb_strrichr'),
            array(67, 'mb_strripos'),
            array(68, 'mb_strrpos'),
            array(69, 'mb_strstr'),
            array(70, 'mb_strtolower'),
            array(71, 'mb_strtoupper'),
            array(72, 'mb_strwidth'),
            array(73, 'mb_substr_count'),
            array(74, 'mb_substr'),
        );
    }


    /**
     * Test that there are no false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.4-7.0');

        // No errors expected on the first 40 lines.
        for ($line = 1; $line <= 40; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * Test the special handling of calls to iconv_mime_encode().
     *
     * @return void
     */
    public function testIconvMimeEncode()
    {
        $file  = $this->sniffFile(__FILE__, '5.4-7.0');
        $error = 'The default value of the %s parameter index for iconv_mime_encode() was changed from ISO-8859-1 to UTF-8 in PHP 5.6';

        $this->assertError($file, 91, sprintf($error, '$preferences[\'input/output-charset\']'));
        $this->assertWarning($file, 92, sprintf($error, '$preferences[\'input/output-charset\']'));
        $this->assertError($file, 96, sprintf($error, '$preferences[\'output-charset\']'));
        $this->assertError($file, 106, sprintf($error, '$preferences[\'input-charset\']'));
    }


    /**
     * Test that there are no false positives.
     *
     * @return void
     */
    public function testNoFalsePositivesIconvMimeEncode()
    {
        $file = $this->sniffFile(__FILE__, '5.4-7.0');

        // No errors expected on line 79 - 89.
        for ($line = 79; $line <= 89; $line++) {
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
            array('-5.5'),
            array('5.6-'),
        );
    }
}
