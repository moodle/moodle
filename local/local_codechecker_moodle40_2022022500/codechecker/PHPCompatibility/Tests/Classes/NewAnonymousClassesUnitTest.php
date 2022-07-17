<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Classes;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewAnonymousClasses sniff.
 *
 * @group newAnonymousClasses
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\NewAnonymousClassesSniff
 *
 * @since 7.0.0
 */
class NewAnonymousClassesUnitTest extends BaseSniffTest
{

    /**
     * Test anonymous classes
     *
     * @return void
     */
    public function testAnonymousClasses()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertError($file, 4, 'Anonymous classes are not supported in PHP 5.6 or earlier');
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file, 3);
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
