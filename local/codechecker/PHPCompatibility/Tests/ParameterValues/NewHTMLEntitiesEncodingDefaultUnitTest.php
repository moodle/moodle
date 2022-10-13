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
 * Test the NewHTMLEntitiesEncoding sniff.
 *
 * @group newHTMLEntitiesEncodingDefault
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewHTMLEntitiesEncodingDefaultSniff
 *
 * @since 9.3.0
 */
class NewHTMLEntitiesEncodingDefaultUnitTest extends BaseSniffTest
{

    /**
     * testNewHTMLEntitiesEncodingDefault
     *
     * @dataProvider dataNewHTMLEntitiesEncodingDefault
     *
     * @param int    $line     Line number where the error should occur.
     * @param string $function The name of the function called.
     *
     * @return void
     */
    public function testNewHTMLEntitiesEncodingDefault($line, $function)
    {
        $file  = $this->sniffFile(__FILE__, '5.3-5.4');
        $error = "The default value of the \$encoding parameter for {$function}() was changed from ISO-8859-1 to UTF-8 in PHP 5.4";

        $this->assertError($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testNewHTMLEntitiesEncodingDefault()
     *
     * @return array
     */
    public function dataNewHTMLEntitiesEncodingDefault()
    {
        return array(
            array(9, 'htmlentities'),
            array(10, 'htmlspecialchars'),
            array(11, 'HTML_entity_decode'),
        );
    }


    /**
     * Test that there are no false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.3-5.4');

        // No errors expected on the first 7 lines.
        for ($line = 1; $line <= 7; $line++) {
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
            array('5.0-5.3'),
            array('5.4-'),
        );
    }
}
