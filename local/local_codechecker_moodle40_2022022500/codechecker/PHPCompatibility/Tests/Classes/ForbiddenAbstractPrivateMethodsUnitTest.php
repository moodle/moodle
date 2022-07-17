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
use PHPCompatibility\PHPCSHelper;

/**
 * Test the ForbiddenAbstractPrivateMethods sniff.
 *
 * @group newForbiddenAbstractPrivateMethods
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\ForbiddenAbstractPrivateMethodsSniff
 *
 * @since 9.2.0
 */
class ForbiddenAbstractPrivateMethodsUnitTest extends BaseSniffTest
{

    /**
     * Set up skip condition.
     *
     * @return void
     */
    public function setUp()
    {
        // When using PHPCS 2.3.4 or lower combined with PHP 5.3 or lower, both abstract classes
        // as well as traits are not recognized, so the tests would never pass.
        if (version_compare(PHPCSHelper::getVersion(), '2.4.0', '<') && version_compare(\PHP_VERSION_ID, '50400', '<')) {
            $this->markTestSkipped('Traits and abstract classes are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        parent::setUp();
    }


    /**
     * testForbiddenAbstractPrivateMethods.
     *
     * @dataProvider dataForbiddenAbstractPrivateMethods
     *
     * @param int $line The line number where an error is expected.
     *
     * @return void
     */
    public function testForbiddenAbstractPrivateMethods($line)
    {
        $file = $this->sniffFile(__FILE__, '5.1');
        $this->assertError($file, $line, 'Abstract methods cannot be declared as private since PHP 5.1');
    }

    /**
     * Data provider.
     *
     * @see testForbiddenAbstractPrivateMethods()
     *
     * @return array
     */
    public function dataForbiddenAbstractPrivateMethods()
    {
        return array(
            array(28),
            array(29),
            array(34),
            array(35),
            array(39),
            array(40),
        );
    }


    /**
     * testNoFalsePositives.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.1');
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
        // No errors expected on the first 24 lines.
        for ($line = 1; $line <= 24; $line++) {
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
        $file = $this->sniffFile(__FILE__, '5.0');
        $this->assertNoViolation($file);
    }
}
