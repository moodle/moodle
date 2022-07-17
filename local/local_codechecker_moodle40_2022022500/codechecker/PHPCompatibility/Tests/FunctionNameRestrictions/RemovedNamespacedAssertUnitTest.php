<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionNameRestrictions;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;

/**
 * Test the RemovedNamespacedAssert sniff.
 *
 * @group removedNamespacedAssert
 * @group functionNameRestrictions
 *
 * @covers \PHPCompatibility\Sniffs\FunctionNameRestrictions\RemovedNamespacedAssertSniff
 * @covers \PHPCompatibility\Sniff::determineNamespace
 *
 * @since 9.0.0
 */
class RemovedNamespacedAssertUnitTest extends BaseSniffTest
{

    /**
     * The name of the primary test case file containing code in the global namespace.
     *
     * @var string
     */
    const TEST_FILE = 'RemovedNamespacedAssertUnitTest.1.inc';

    /**
     * The name of a secondary test case file containing code in a unscoped namespace.
     *
     * @var string
     */
    const TEST_FILE_NAMESPACED = 'RemovedNamespacedAssertUnitTest.2.inc';

    /**
     * Whether or not traits and interfaces will be properly scoped in PHPCS.
     *
     * @var bool
     */
    protected static $recognizesTraitsAndInterfaces = true;

    /**
     * Set up skip condition.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        // When using PHPCS 2.3.4 or lower combined with PHP 5.3 or lower, traits are
        // not recognized and the scope condition for interfaces will not be set.
        if (version_compare(PHPCSHelper::getVersion(), '2.4.0', '<') && version_compare(\PHP_VERSION_ID, '50400', '<')) {
            self::$recognizesTraitsAndInterfaces = false;
        }
        parent::setUpBeforeClass();
    }

    /**
     * Test deprecation of namespaced free-standing assert() function declaration.
     *
     * @dataProvider dataIsDeprecated
     *
     * @param string $testFile The file to test.
     * @param int    $line     The line number where the error should occur.
     *
     * @return void
     */
    public function testIsDeprecated($testFile, $line)
    {
        $file = $this->sniffFile(__DIR__ . '/' . $testFile, '7.3');
        $this->assertWarning($file, $line, 'Declaring a free-standing function called assert() is deprecated since PHP 7.3');
    }

    /**
     * dataIsDeprecated
     *
     * @see testIsDeprecated()
     *
     * @return array
     */
    public function dataIsDeprecated()
    {
        return array(
            array(self::TEST_FILE, 22),
            array(self::TEST_FILE_NAMESPACED, 5),
        );
    }

    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param string $testFile  The file to test.
     * @param int    $line      The line number.
     * @param bool   $maybeSkip Whether this test needs to be skipped on old PHP/PHPCS combis.
     *
     * @return void
     */
    public function testNoFalsePositives($testFile, $line, $maybeSkip = false)
    {
        if ($maybeSkip === true && self::$recognizesTraitsAndInterfaces === false) {
            $this->markTestSkipped('Traits are not recognized and interface scope conditions not added on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->sniffFile(__DIR__ . '/' . $testFile, '7.3');
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
            array(self::TEST_FILE, 3),
            array(self::TEST_FILE, 6),
            array(self::TEST_FILE, 10),
            array(self::TEST_FILE, 14),
            array(self::TEST_FILE, 18),
            array(self::TEST_FILE_NAMESPACED, 8),
            array(self::TEST_FILE_NAMESPACED, 12, true),
            array(self::TEST_FILE_NAMESPACED, 16, true),
            array(self::TEST_FILE_NAMESPACED, 20),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__DIR__ . '/' . self::TEST_FILE, '7.2');
        $this->assertNoViolation($file);
    }
}
