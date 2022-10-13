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
 * Test the NewPasswordAlgoConstantValues sniff.
 *
 * @group newPasswordAlgoConstantValues
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewPasswordAlgoConstantValuesSniff
 *
 * @since 9.3.0
 */
class NewPasswordAlgoConstantValuesUnitTest extends BaseSniffTest
{

    /**
     * testNewPasswordAlgoConstantValues
     *
     * @dataProvider dataNewPasswordAlgoConstantValues
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testNewPasswordAlgoConstantValues($line)
    {
        $file  = $this->sniffFile(__FILE__, '7.4');
        $error = 'The value of the password hash algorithm constants has changed in PHP 7.4';

        $this->assertWarning($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testNewPasswordAlgoConstantValues()
     *
     * @return array
     */
    public function dataNewPasswordAlgoConstantValues()
    {
        return array(
            array(20),
            array(21),
            array(22),
            array(23),
            array(24),
            array(25),
            array(26),
        );
    }


    /**
     * Test that there are no false positives.
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.4');

        // No errors expected on the first 18 lines.
        for ($line = 1; $line <= 18; $line++) {
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
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertNoViolation($file);
    }
}
