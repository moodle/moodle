<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\MethodUse;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewDirectCallsToClone sniff.
 *
 * @group newDirectCallsToClone
 * @group methodUse
 *
 * @covers \PHPCompatibility\Sniffs\MethodUse\NewDirectCallsToCloneSniff
 *
 * @since 9.1.0
 */
class NewDirectCallsToCloneUnitTest extends BaseSniffTest
{

    /**
     * Test detecting direct calls to clone.
     *
     * @dataProvider dataDirectCallToClone
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testDirectCallToClone($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, $line, 'Direct calls to the __clone() magic method are not allowed in PHP 5.6 or earlier.');
    }

    /**
     * Data provider.
     *
     * @see testDirectCallToClone()
     *
     * @return array
     */
    public function dataDirectCallToClone()
    {
        return array(
            array(33),
            array(34),
        );
    }


    /**
     * Test against false positives.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.6');
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
        $cases = array();
        // No errors expected on the first 29 lines.
        for ($line = 1; $line <= 29; $line++) {
            $cases[] = array($line);
        }

        return $cases;
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file);
    }
}
