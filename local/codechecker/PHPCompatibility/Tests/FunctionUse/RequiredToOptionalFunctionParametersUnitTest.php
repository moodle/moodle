<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionUse;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RequiredToOptionalFunctionParameters sniff.
 *
 * @group requiredToOptionalFunctionParameters
 * @group functionUse
 *
 * @covers \PHPCompatibility\Sniffs\FunctionUse\RequiredToOptionalFunctionParametersSniff
 *
 * @since 7.0.3
 */
class RequiredToOptionalFunctionParametersUnitTest extends BaseSniffTest
{

    /**
     * testRequiredOptionalParameter
     *
     * @dataProvider dataRequiredOptionalParameter
     *
     * @param string $functionName  Function name.
     * @param string $parameterName Parameter name.
     * @param string $requiredUpTo  The last PHP version in which the parameter was still required.
     * @param array  $lines         The line numbers in the test file which apply to this class.
     * @param string $okVersion     A PHP version in which to test for no violation.
     *
     * @return void
     */
    public function testRequiredOptionalParameter($functionName, $parameterName, $requiredUpTo, $lines, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $requiredUpTo);
        $error = "The \"{$parameterName}\" parameter for function {$functionName}() is missing, but was required for PHP version {$requiredUpTo} and lower";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testRequiredOptionalParameter()
     *
     * @return array
     */
    public function dataRequiredOptionalParameter()
    {
        return array(
            array('preg_match_all', 'matches', '5.3', array(8), '5.4'),
            array('stream_socket_enable_crypto', 'crypto_type', '5.5', array(9), '5.6'),
            array('bcscale', 'scale', '7.2', array(12), '7.3'),
            array('getenv', 'varname', '7.0', array(15), '7.1'),
            array('array_push', 'element to push', '7.2', array(18), '7.3'),
            array('array_unshift', 'element to prepend', '7.2', array(21), '7.3'),
            array('ftp_fget', 'mode', '7.2', array(24), '7.3'),
            array('ftp_fput', 'mode', '7.2', array(25), '7.3'),
            array('ftp_get', 'mode', '7.2', array(26), '7.3'),
            array('ftp_nb_fget', 'mode', '7.2', array(27), '7.3'),
            array('ftp_nb_fput', 'mode', '7.2', array(28), '7.3'),
            array('ftp_nb_get', 'mode', '7.2', array(29), '7.3'),
            array('ftp_nb_put', 'mode', '7.2', array(30), '7.3'),
            array('ftp_put', 'mode', '7.2', array(31), '7.3'),
            array('array_merge', 'array(s) to merge', '7.3', array(35), '7.4'),
            array('array_merge_recursive', 'array(s) to merge', '7.3', array(36), '7.4'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3'); // Version before earliest required/optional change.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(4),
            array(5),
            array(11),
            array(14),
            array(17),
            array(20),
            array(23),
            array(32),
            array(34),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest required/optional change.
        $this->assertNoViolation($file);
    }
}
