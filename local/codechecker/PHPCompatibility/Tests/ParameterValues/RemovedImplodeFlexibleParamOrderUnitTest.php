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
 * Test the RemovedImplodeFlexibleParamOrder sniff.
 *
 * @group removedImplodeFlexibleParamOrder
 * @group parameterValues
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedImplodeFlexibleParamOrderSniff
 *
 * @since 9.3.0
 */
class RemovedImplodeFlexibleParamOrderUnitTest extends BaseSniffTest
{

    /**
     * testRemovedImplodeFlexibleParamOrder
     *
     * @dataProvider dataRemovedImplodeFlexibleParamOrder
     *
     * @param int    $line     Line number where the error should occur.
     * @param string $function The function name.
     *
     * @return void
     */
    public function testRemovedImplodeFlexibleParamOrder($line, $function)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertWarning($file, $line, 'Passing the $glue and $pieces parameters in reverse order to ' . $function . ' has been deprecated since PHP 7.4; $glue should be the first parameter and $pieces the second');
    }

    /**
     * dataRemovedImplodeFlexibleParamOrder
     *
     * @see testRemovedImplodeFlexibleParamOrder()
     *
     * @return array
     */
    public function dataRemovedImplodeFlexibleParamOrder()
    {
        return array(
            array(29, 'implode'),
            array(30, 'join'),
            array(32, 'implode'),
            array(33, 'join'),
            array(35, 'implode'),
            array(36, 'join'),
            array(37, 'implode'),
            array(38, 'implode'),
            array(40, 'join'),
            array(46, 'implode'),
            array(47, 'implode'),
            array(48, 'implode'),
            array(49, 'implode'),
            array(52, 'implode'),
            array(53, 'implode'),
            array(68, 'implode'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line Line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '7.4');
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
        $data = array();

        // No errors expected on the first 27 lines.
        for ($line = 1; $line <= 27; $line++) {
            $data[] = array($line);
        }

        $data[] = array(57);
        $data[] = array(64);
        $data[] = array(67);

        return $data;
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
