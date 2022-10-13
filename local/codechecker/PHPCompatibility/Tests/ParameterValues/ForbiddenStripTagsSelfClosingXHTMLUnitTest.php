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
 * Test the ForbiddenStripTagsSelfClosingXHTML sniff.
 *
 * @group forbiddenStripTagsSelfClosingXHTML
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\ForbiddenStripTagsSelfClosingXHTMLSniff
 *
 * @since 9.3.0
 */
class ForbiddenStripTagsSelfClosingXHTMLUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenStripTagsSelfClosingXHTML
     *
     * @dataProvider dataForbiddenStripTagsSelfClosingXHTML
     *
     * @param int  $line       Line number where the error should occur.
     * @param bool $paramValue The parameter value detected.
     *
     * @return void
     */
    public function testForbiddenStripTagsSelfClosingXHTML($line, $paramValue)
    {
        $file  = $this->sniffFile(__FILE__, '5.4');
        $error = 'Self-closing XHTML tags are ignored. Only non-self-closing tags should be used in the strip_tags() $allowable_tags parameter since PHP 5.3.4. Found: ' . $paramValue;

        $this->assertError($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testForbiddenStripTagsSelfClosingXHTML()
     *
     * @return array
     */
    public function dataForbiddenStripTagsSelfClosingXHTML()
    {
        return array(
            array(14, "'<br/>'"),
            array(15, "'<img/><br/>' . '<meta/><input/>'"),
        );
    }


    /**
     * Test the sniff does not throw false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.4');

        // No errors expected on the first 12 lines.
        for ($line = 1; $line <= 12; $line++) {
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
