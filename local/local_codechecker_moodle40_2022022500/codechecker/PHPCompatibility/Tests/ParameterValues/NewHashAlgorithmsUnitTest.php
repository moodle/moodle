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
 * Test the NewHashAlgorithms sniff.
 *
 * @group newHashAlgorithms
 * @group parameterValues
 * @group hashAlgorithms
 *
 * @covers \PHPCompatibility\Sniffs\ParameterValues\NewHashAlgorithmsSniff
 * @covers \PHPCompatibility\Sniff::getHashAlgorithmParameter
 *
 * @since 7.0.7
 */
class NewHashAlgorithmsUnitTest extends BaseSniffTest
{

    /**
     * testNewHashAlgorithms
     *
     * @dataProvider dataNewHashAlgorithms
     *
     * @param string $algorithm         Name of the algorithm.
     * @param string $lastVersionBefore The PHP version just *before* the algorithm was introduced.
     * @param array  $line              The line number in the test file on which an error should occur.
     * @param string $okVersion         A PHP version in which the algorithm was valid.
     *
     * @return void
     */
    public function testNewHashAlgorithms($algorithm, $lastVersionBefore, $line, $okVersion)
    {
        $file = $this->sniffFile(__FILE__, $lastVersionBefore);
        $this->assertError($file, $line, "The {$algorithm} hash algorithm is not present in PHP version {$lastVersionBefore} or earlier");

        $file = $this->sniffFile(__FILE__, $okVersion);
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNewHashAlgorithms()
     *
     * @return array
     */
    public function dataNewHashAlgorithms()
    {
        return array(
            array('md2', '5.2', 13, '5.3'),
            array('ripemd256', '5.2', 14, '5.3'),
            array('ripemd320', '5.2', 15, '5.3'),
            array('salsa10', '5.2', 16, '5.3'),
            array('salsa20', '5.2', 18, '5.3'),
            array('snefru256', '5.2', 19, '5.3'),
            array('sha224', '5.2', 20, '5.3'),
            array('joaat', '5.3', 22, '5.4'),
            array('fnv132', '5.3', 23, '5.4'),
            array('fnv164', '5.3', 24, '5.4'),
            array('gost-crypto', '5.5', 26, '5.6'),
            array('sha512/224', '7.0', 28, '7.1'),
            array('sha512/256', '7.0', 29, '7.1'),
            array('sha3-224', '7.0', 30, '7.1'),
            array('sha3-256', '7.0', 31, '7.1'),
            array('sha3-384', '7.0', 32, '7.1'),
            array('sha3-512', '7.0', 33, '7.1'),
            array('crc32c', '7.3', 34, '7.4'),
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
        $file = $this->sniffFile(__FILE__, '5.2'); // Low version below the first addition.
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
        $file = $this->sniffFile(__FILE__, '99.0');  // High version beyond newest addition.
        $this->assertNoViolation($file);
    }
}
