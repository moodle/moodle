<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Syntax;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedNewReference sniff.
 *
 * @group removedNewReference
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\RemovedNewReferenceSniff
 *
 * @since 5.5
 */
class RemovedNewReferenceUnitTest extends BaseSniffTest
{

    /**
     * testDeprecatedNewReference
     *
     * @dataProvider dataDeprecatedNewReference
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testDeprecatedNewReference($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertWarning($file, $line, 'Assigning the return value of new by reference is deprecated in PHP 5.3');

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Assigning the return value of new by reference is deprecated in PHP 5.3 and has been removed in PHP 7.0');
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedNewReference()
     *
     * @return array
     */
    public function dataDeprecatedNewReference()
    {
        return array(
            array(9),
            array(10),
            array(11),
            array(12),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @return void
     */
    public function testNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file, 8);
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file);
    }
}
