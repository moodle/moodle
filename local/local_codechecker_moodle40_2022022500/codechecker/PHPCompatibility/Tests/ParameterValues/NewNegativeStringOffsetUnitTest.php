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
 * Test the NewNegativeStringOffset sniff.
 *
 * @group newNegativeStringOffset
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewNegativeStringOffsetSniff
 *
 * @since 9.0.0
 */
class NewNegativeStringOffsetUnitTest extends BaseSniffTest
{

    /**
     * testNegativeStringOffset
     *
     * @dataProvider dataNegativeStringOffset
     *
     * @param int    $line         Line number where the error should occur.
     * @param string $paramName    The name of the parameter being passed a negative offset.
     * @param string $functionName The name of the function which was called.
     *
     * @return void
     */
    public function testNegativeStringOffset($line, $paramName, $functionName)
    {
        $file  = $this->sniffFile(__FILE__, '7.0');
        $error = sprintf(
            'Negative string offsets were not supported for the $%1$s parameter in %2$s() in PHP 7.0 or lower.',
            $paramName,
            $functionName
        );
        $this->assertError($file, $line, $error);
    }

    /**
     * dataNegativeStringOffset
     *
     * @see testNegativeStringOffset()
     *
     * @return array
     */
    public function dataNegativeStringOffset()
    {
        return array(
            array(28, 'position', 'mb_ereg_search_setpos'),
            array(34, 'position', 'MB_ereg_search_setpos'),
            array(36, 'offset', 'file_get_contents'),
            array(37, 'start', 'grapheme_extract'),
            array(38, 'offset', 'grapheme_stripos'),
            array(39, 'offset', 'grapheme_strpos'),
            array(40, 'offset', 'iconv_strpos'),
            array(41, 'start', 'mb_strimwidth'),
            array(41, 'width', 'mb_strimwidth'),
            array(42, 'offset', 'mb_stripos'),
            array(43, 'offset', 'mb_strpos'),
            array(44, 'offset', 'stripos'),
            array(45, 'offset', 'strpos'),
            array(46, 'offset', 'substr_count'),
            array(46, 'length', 'substr_count'),
            array(47, 'offset', 'Substr_Count'),
            array(48, 'length', 'substr_count'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.0');

        // No errors expected on the first 26 lines.
        for ($line = 1; $line <= 26; $line++) {
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
