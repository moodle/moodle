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
 * Test the RemovedMagicAutoload sniff.
 *
 * @group removedMagicAutoload
 * @group functionNameRestrictions
 *
 * @covers \PHPCompatibility\Sniffs\FunctionNameRestrictions\RemovedMagicAutoloadSniff
 * @covers \PHPCompatibility\Sniff::determineNamespace
 *
 * @since 8.1.0
 */
class RemovedMagicAutoloadUnitTest extends BaseSniffTest
{

    /**
     * The name of the main test case file.
     *
     * @var string
     */
    const TEST_FILE = 'RemovedMagicAutoloadUnitTest.1.inc';

    /**
     * The name of a secondary test case file to test against false positives
     * for namespaced function declarations.
     *
     * @var string
     */
    const TEST_FILE_NAMESPACED = 'RemovedMagicAutoloadUnitTest.2.inc';

    /**
     * Whether or not traits and interfaces will be recognized in PHPCS.
     *
     * @var bool
     */
    protected static $recognizesTraitsOrInterfaces = true;

    /**
     * Set up skip condition.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        // When using PHPCS 2.3.4 or lower combined with PHP 5.3 or lower, traits are not recognized.
        if (version_compare(PHPCSHelper::getVersion(), '2.4.0', '<') && version_compare(\PHP_VERSION_ID, '50400', '<')) {
            self::$recognizesTraitsOrInterfaces = false;
        }
        parent::setUpBeforeClass();
    }

    /**
     * Test __autoload deprecation not causing issue in 7.1.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__DIR__ . '/' . self::TEST_FILE, '7.1');
        $this->assertNoViolation($file);
    }

    /**
     * Test __autoload deprecation.
     *
     * @dataProvider dataIsDeprecated
     *
     * @param int $line The line number where the error should occur.
     *
     * @return void
     */
    public function testIsDeprecated($line)
    {
        $file = $this->sniffFile(__DIR__ . '/' . self::TEST_FILE, '7.2');
        $this->assertWarning($file, $line, 'Use of __autoload() function is deprecated since PHP 7.2');
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
            array(3),
        );
    }

    /**
     * Test not affected __autoload declarations.
     *
     * @dataProvider dataIsNotAffected
     *
     * @param string $testFile The file to test.
     * @param int    $line     The line number where the error should occur.
     * @param bool   $isTrait  Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testIsNotAffected($testFile, $line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraitsOrInterfaces === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->sniffFile(__DIR__ . '/' . $testFile, '7.2');
        $this->assertNoViolation($file, $line);
    }

    /**
     * dataIsNotAffected
     *
     * @see testIsNotAffected()
     *
     * @return array
     */
    public function dataIsNotAffected()
    {
        return array(
            array(self::TEST_FILE, 8),
            array(self::TEST_FILE, 14, true),
            array(self::TEST_FILE, 18, true),
            array(self::TEST_FILE, 24),
            array(self::TEST_FILE_NAMESPACED, 5),
            array(self::TEST_FILE_NAMESPACED, 10),
            array(self::TEST_FILE_NAMESPACED, 16),
            array(self::TEST_FILE_NAMESPACED, 20),
            array(self::TEST_FILE_NAMESPACED, 26),
        );
    }
}
