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
 * Test the NewProcOpenCmdArray sniff.
 *
 * @group newProcOpenCmdArray
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewProcOpenCmdArraySniff
 *
 * @since 9.3.0
 */
class NewProcOpenCmdArrayUnitTest extends BaseSniffTest
{

    /**
     * testNewProcOpenCmdArray
     *
     * @dataProvider dataNewProcOpenCmdArray
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testNewProcOpenCmdArray($line)
    {
        $file  = $this->sniffFile(__FILE__, '7.3');
        $error = 'The proc_open() function did not accept $cmd to be passed in array format in PHP 7.3 and earlier.';

        $this->assertError($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testNewProcOpenCmdArray()
     *
     * @return array
     */
    public function dataNewProcOpenCmdArray()
    {
        return array(
            array(18),
            array(20),
            array(30),
            array(32),
        );
    }


    /**
     * testInvalidProcOpenCmdArray
     *
     * @dataProvider dataInvalidProcOpenCmdArray
     *
     * @param int  $line      Line number where the error should occur.
     * @param bool $itemValue The parameter value detected.
     *
     * @return void
     */
    public function testInvalidProcOpenCmdArray($line, $itemValue)
    {
        $file  = $this->sniffFile(__FILE__, '7.4');
        $error = 'When passing proc_open() the $cmd parameter as an array, PHP will take care of any necessary argument escaping. Found: ' . $itemValue;

        $this->assertWarning($file, $line, $error);
    }

    /**
     * Data provider.
     *
     * @see testInvalidProcOpenCmdArray()
     *
     * @return array
     */
    public function dataInvalidProcOpenCmdArray()
    {
        return array(
            array(30, 'escapeshellarg($echo)'),
            array(34, '\'--standard=\' . escapeshellarg($standard)'),
            array(35, '\'./path/to/\' . escapeshellarg($file)'),
        );
    }


    /**
     * Test the sniff does not throw false positives.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param string $testVersion The testVersion to use.
     *
     * @return void
     */
    public function testNoFalsePositives($testVersion)
    {
        $file = $this->sniffFile(__FILE__, $testVersion);

        // No errors expected on the first 16 lines.
        for ($line = 1; $line <= 16; $line++) {
            $this->assertNoViolation($file, $line);
        }
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
            array('7.3'),
            array('7.4'),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw warnings/errors
     * about independently of the testVersion.
     */
}
