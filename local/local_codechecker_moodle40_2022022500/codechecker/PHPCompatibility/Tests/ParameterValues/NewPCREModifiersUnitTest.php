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
 * Test the NewPCREModifiers sniff.
 *
 * @group newPCREModifiers
 * @group parameterValues
 * @group regexModifiers
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewPCREModifiersSniff
 *
 * @since 8.2.0
 */
class NewPCREModifiersUnitTest extends BaseSniffTest
{

    /**
     * testPCRENewModifier
     *
     * @dataProvider dataPCRENewModifier
     *
     * @param string $modifier          Regex modifier.
     * @param string $lastVersionBefore The PHP version just *before* the modifier was introduced.
     * @param array  $lines             The line numbers in the test file which apply to this modifier.
     * @param string $okVersion         A PHP version in which the modifier was ok to be used.
     * @param string $testVersion       Optional PHP version to use for testing the flagged case.
     *
     * @return void
     */
    public function testPCRENewModifier($modifier, $lastVersionBefore, $lines, $okVersion, $testVersion = null)
    {
        $errorVersion = (isset($testVersion)) ? $testVersion : $lastVersionBefore;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The PCRE regex modifier \"{$modifier}\" is not present in PHP version {$lastVersionBefore} or earlier";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * dataPCRENewModifier
     *
     * @see testPCRENewModifier()
     *
     * @return array
     */
    public function dataPCRENewModifier()
    {
        return array(
            array('J', '7.1', array(3, 4, 6, 17, 19, 25), '7.2'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number where no error should occur.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file, $line);
    }

    /**
     * dataNoFalsePositives
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(18),
            array(28),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '99.0');
        $this->assertNoViolation($file);
    }
}
