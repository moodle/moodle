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
 * Test the RemovedHashAlgorithms sniff.
 *
 * @group removedHashAlgorithms
 * @group parameterValues
 * @group hashAlgorithms
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\RemovedHashAlgorithmsSniff
 * @covers \PHPCompatibility\Sniff::getHashAlgorithmParameter
 *
 * @since 5.5
 */
class RemovedHashAlgorithmsUnitTest extends BaseSniffTest
{

    /**
     * testRemovedHashAlgorithms
     *
     * @dataProvider dataRemovedHashAlgorithms
     *
     * @param string $algorithm Name of the algorithm.
     * @param string $removedIn The PHP version in which the algorithm was removed.
     * @param array  $line      The line number on which the error should occur.
     * @param string $okVersion A PHP version in which the algorithm was still valid.
     *
     * @return void
     */
    public function testRemovedHashAlgorithms($algorithm, $removedIn, $line, $okVersion)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        $this->assertNoViolation($file, $line);

        $file = $this->sniffFile(__FILE__, $removedIn);
        $this->assertError($file, $line, "The {$algorithm} hash algorithm is removed since PHP {$removedIn}");
    }

    /**
     * Data provider.
     *
     * @see testRemovedHashAlgorithms()
     *
     * @return array
     */
    public function dataRemovedHashAlgorithms()
    {
        return array(
            array('salsa10', '5.4', 13, '5.3'),
            array('salsa20', '5.4', 14, '5.3'),
            array('salsa10', '5.4', 15, '5.3'),
            array('salsa20', '5.4', 16, '5.3'),
            array('salsa10', '5.4', 18, '5.3'),
            array('salsa20', '5.4', 19, '5.3'),
            array('salsa10', '5.4', 20, '5.3'),
            array('salsa10', '5.4', 22, '5.3'),
            array('salsa10', '5.4', 23, '5.3'),
            array('salsa20', '5.4', 25, '5.3'),
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
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest deprecation.
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
            array(6),
            array(7),
            array(8),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.3'); // Low version below the first removal.
        $this->assertNoViolation($file);
    }
}
