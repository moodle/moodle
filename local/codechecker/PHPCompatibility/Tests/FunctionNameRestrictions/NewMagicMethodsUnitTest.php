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
 * Test the NewMagicMethods sniff.
 *
 * @group newMagicMethods
 * @group functionNameRestrictions
 * @group magicMethods
 *
 * @covers \PHPCompatibility\Sniffs\FunctionNameRestrictions\NewMagicMethodsSniff
 *
 * @since 7.0.4
 */
class NewMagicMethodsUnitTest extends BaseSniffTest
{
    /**
     * Whether or not traits will be recognized in PHPCS.
     *
     * @var bool
     */
    protected static $recognizesTraits = true;


    /**
     * Set up skip condition.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        // When using PHPCS 2.3.4 or lower combined with PHP 5.3 or lower, traits are not recognized.
        if (version_compare(PHPCSHelper::getVersion(), '2.4.0', '<') && version_compare(\PHP_VERSION_ID, '50400', '<')) {
            self::$recognizesTraits = false;
        }

        parent::setUpBeforeClass();
    }


    /**
     * Get the correct test file.
     *
     * (@internal
     * The test file has been split into two:
     * - one covering classes and interfaces
     * - one covering traits
     *
     * This is to avoid test failing because PHPCS < 2.4.0 gets confused about the scope
     * openers/closers when run on PHP 5.3 or lower.
     * In a 'normal' situation you won't often find classes, interfaces and traits all
     * mixed in one file anyway, so this issue for which this is a work-around,
     * should not cause real world issues anyway.}
     *
     * @param bool   $isTrait     Whether to load the class/interface test file or the trait test file.
     * @param string $testVersion Value of 'testVersion' to set on PHPCS object.
     *
     * @return \PHP_CodeSniffer_File File object|false
     */
    protected function getTestFile($isTrait, $testVersion = null)
    {
        if ($isTrait === false) {
            return $this->sniffFile(__DIR__ . '/NewMagicMethodsUnitTest.1.inc', $testVersion);
        } else {
            return $this->sniffFile(__DIR__ . '/NewMagicMethodsUnitTest.2.inc', $testVersion);
        }
    }


    /**
     * testNewMagicMethod
     *
     * @dataProvider dataNewMagicMethod
     *
     * @param string $methodName        Name of the method.
     * @param string $lastVersionBefore The PHP version just *before* the method became magic.
     * @param array  $lines             The line numbers in the test file which apply to this method.
     * @param string $okVersion         A PHP version in which the method was magic.
     * @param bool   $isTrait           Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testNewMagicMethod($methodName, $lastVersionBefore, $lines, $okVersion, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file  = $this->getTestFile($isTrait, $lastVersionBefore);
        $error = "The method {$methodName}() was not magical in PHP version {$lastVersionBefore} and earlier. The associated magic functionality will not be invoked.";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $file = $this->getTestFile($isTrait, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewMagicMethod()
     *
     * @return array
     */
    public function dataNewMagicMethod()
    {
        return array(
            // File: NewMagicMethodsUnitTest.1.inc.
            array('__construct', '4.4', array(20), '5.0'),
            array('__destruct', '4.4', array(21), '5.0'),
            array('__get', '4.4', array(22, 34, 61), '5.0'),
            array('__isset', '5.0', array(23, 35, 62), '5.1'),
            array('__unset', '5.0', array(24, 36, 63), '5.1'),
            array('__set_state', '5.0', array(25, 37, 64), '5.1'),
            array('__callStatic', '5.2', array(27, 39, 66), '5.3'),
            array('__invoke', '5.2', array(28, 40, 67), '5.3'),
            array('__debugInfo', '5.5', array(29, 41, 68), '5.6'),
            array('__serialize', '7.3', array(78), '7.4'),
            array('__unserialize', '7.3', array(79), '7.4'),

            // File: NewMagicMethodsUnitTest.2.inc.
            array('__get', '4.4', array(5), '5.0', true),
            array('__isset', '5.0', array(6), '5.1', true),
            array('__unset', '5.0', array(7), '5.1', true),
            array('__set_state', '5.0', array(8), '5.1', true),
            array('__callStatic', '5.2', array(10), '5.3', true),
            array('__invoke', '5.2', array(11), '5.3', true),
            array('__debugInfo', '5.5', array(12), '5.6', true),
            array('__serialize', '7.3', array(13), '7.4', true),
            array('__unserialize', '7.3', array(14), '7.4', true),
            array('__construct', '4.4', array(15), '5.0', true),
            array('__destruct', '4.4', array(16), '5.0', true),
        );
    }


    /**
     * testChangedToStringMethod
     *
     * @dataProvider dataChangedToStringMethod
     *
     * @param int  $line    The line number.
     * @param bool $isTrait Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testChangedToStringMethod($line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->getTestFile($isTrait, '5.1');
        $this->assertWarning($file, $line, 'The method __toString() was not truly magical in PHP version 5.1 and earlier. The associated magic functionality will only be called when directly combined with echo or print.');

        $file = $this->getTestFile($isTrait, '5.2');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testChangedToStringMethod()
     *
     * @return array
     */
    public function dataChangedToStringMethod()
    {
        return array(
            // File: NewMagicMethodsUnitTest.1.inc.
            array(26),
            array(38),
            array(65),

            // File: NewMagicMethodsUnitTest.2.inc.
            array(9, true),
        );
    }


    /**
     * Test magic methods that shouldn't be flagged by this sniff.
     *
     * @dataProvider dataMagicMethodsThatShouldntBeFlagged
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testMagicMethodsThatShouldntBeFlagged($line)
    {
        $file = $this->getTestFile(false, '4.4'); // Low version below the first addition.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testMagicMethodsThatShouldntBeFlagged()
     *
     * @return array
     */
    public function dataMagicMethodsThatShouldntBeFlagged()
    {
        return array(
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
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
        $file = $this->getTestFile(false, '4.4'); // Low version below the first addition.
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
            // Functions of same name outside class context.
            array(47),
            array(48),
            array(49),
            array(50),
            array(51),
            array(52),
            array(53),
            array(54),
            array(74),
            array(75),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        // File: NewMagicMethodsUnitTest.1.inc.
        $file = $this->getTestFile(false, '99.0'); // High version beyond newest addition.
        $this->assertNoViolation($file);

        // File: NewMagicMethodsUnitTest.2.inc.
        $file = $this->getTestFile(true, '99.0'); // High version beyond newest addition.
        $this->assertNoViolation($file);
    }
}
