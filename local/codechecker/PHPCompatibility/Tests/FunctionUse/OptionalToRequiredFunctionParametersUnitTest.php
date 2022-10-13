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
 * Test the OptionalToRequiredFunctionParameters sniff.
 *
 * @group optionalToRequiredFunctionParameters
 * @group functionUse
 *
 * @covers \PHPCompatibility\Sniffs\FunctionUse\OptionalToRequiredFunctionParametersSniff
 *
 * @since 8.1.0
 */
class OptionalToRequiredFunctionParametersUnitTest extends BaseSniffTest
{

    /**
     * testOptionalRequiredParameterDeprecated
     *
     * @dataProvider dataOptionalRequiredParameterDeprecated
     *
     * @param string $functionName     Function name.
     * @param string $parameterName    Parameter name.
     * @param string $softRequiredFrom The last PHP version in which the parameter was still optional.
     * @param array  $lines            The line numbers in the test file which apply to this class.
     * @param string $okVersion        A PHP version in which to test for no violation.
     *
     * @return void
     */
    public function testOptionalRequiredParameterDeprecated($functionName, $parameterName, $softRequiredFrom, $lines, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $softRequiredFrom);
        $error = "The \"{$parameterName}\" parameter for function {$functionName}() is missing. Passing this parameter is no longer optional. The optional nature of the parameter is deprecated since PHP {$softRequiredFrom}";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testOptionalRequiredParameterDeprecated()
     *
     * @return array
     */
    public function dataOptionalRequiredParameterDeprecated()
    {
        return array(
            array('parse_str', 'result', '7.2', array(7), '7.1'),
        );
    }


    /**
     * testOptionalRecommendedParameter
     *
     * @dataProvider dataOptionalRecommendedParameter
     *
     * @param string $functionName        Function name.
     * @param string $parameterName       Parameter name.
     * @param string $softRecommendedFrom The PHP version in which the parameter became recommended.
     * @param array  $lines               The line numbers in the test file which apply to this class.
     * @param string $okVersion           A PHP version in which to test for no violation.
     *
     * @return void
     */
    public function testOptionalRecommendedParameter($functionName, $parameterName, $softRecommendedFrom, $lines, $okVersion)
    {
        $file  = $this->sniffFile(__FILE__, $softRecommendedFrom);
        $error = "The \"{$parameterName}\" parameter for function {$functionName}() is missing. Passing this parameter is strongly recommended since PHP {$softRecommendedFrom}";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testOptionalRecommendedParameter()
     *
     * @return array
     */
    public function dataOptionalRecommendedParameter()
    {
        return array(
            array('crypt', 'salt', '5.6', array(8), '5.5'),
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
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest required/optional change.
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
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.5'); // Version before earliest required/optional change.
        $this->assertNoViolation($file);
    }
}
