<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Operators;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewOperators sniff.
 *
 * @group newOperators
 * @group operators
 *
 * @covers \PHPCompatibility\Sniffs\Operators\NewOperatorsSniff
 *
 * @since 9.0.0 Detection of new operators was originally included in the
 *              NewLanguageConstructSniff (since 5.6).
 */
class NewOperatorsUnitTest extends BaseSniffTest
{

    /**
     * testPow
     *
     * @return void
     */
    public function testPow()
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertError($file, 3, 'power operator (**) is not present in PHP version 5.5 or earlier');

        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file, 3);
    }

    /**
     * testPowEquals
     *
     * @return void
     */
    public function testPowEquals()
    {
        $file = $this->sniffFile(__FILE__, '5.5');
        $this->assertError($file, 4, 'power assignment operator (**=) is not present in PHP version 5.5 or earlier');

        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file, 4);
    }

    /**
     * testSpaceship
     *
     * @return void
     */
    public function testSpaceship()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, 9, 'spaceship operator (<=>) is not present in PHP version 5.6 or earlier');

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file, 9);
    }

    /**
     * Coalescing operator
     *
     * @return void
     */
    public function testCoalescing()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, 6, 'null coalescing operator (??) is not present in PHP version 5.6 or earlier');

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file, 6);
    }

    /**
     * Coalesce equal operator
     *
     * @return void
     */
    public function testCoalesceEquals()
    {
        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertError($file, 7, 'null coalesce equal operator (??=) is not present in PHP version 7.3 or earlier');

        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertNoViolation($file, 7);
    }

    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond newest addition.
        $this->assertNoViolation($file);
    }
}
