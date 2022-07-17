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
 * Test the NewLateStaticBinding sniff.
 *
 * @group newLateStaticBinding
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\NewLateStaticBindingSniff
 *
 * @since 7.0.3
 */
class NewLateStaticBindingUnitTest extends BaseSniffTest
{

    /**
     * testLateStaticBinding
     *
     * @dataProvider dataLateStaticBinding
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testLateStaticBinding($line)
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertError($file, $line, 'Late static binding is not supported in PHP 5.2 or earlier.');

        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testLateStaticBinding()
     *
     * @return array
     */
    public function dataLateStaticBinding()
    {
        return array(
            array(8),
            array(9),
        );
    }


    /**
     * testLateStaticBindingOutsideClassScope
     *
     * @dataProvider dataLateStaticBindingOutsideClassScope
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testLateStaticBindingOutsideClassScope($line)
    {
        $file = $this->sniffFile(__FILE__); // Message will be shown independently of testVersion.
        $this->assertError($file, $line, 'Late static binding is not supported outside of class scope.');
    }

    /**
     * Data provider.
     *
     * @see testLateStaticBindingOutsideClassScope()
     *
     * @return array
     */
    public function dataLateStaticBindingOutsideClassScope()
    {
        return array(
            array(19),
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
        $file = $this->sniffFile(__FILE__, '5.2');
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
            array(7),
            array(12),
            array(15),
            array(16),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw an error
     * independently of PHP version when late static binding is used outside of class scope.
     */
}
