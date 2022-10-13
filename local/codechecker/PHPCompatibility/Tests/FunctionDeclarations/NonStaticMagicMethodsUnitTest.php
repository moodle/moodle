<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionDeclarations;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;

/**
 * Test the NonStaticMagicMethods sniff.
 *
 * @group nonStaticMagicMethods
 * @group functionDeclarations
 * @group magicMethods
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\NonStaticMagicMethodsSniff
 *
 * @since 5.5
 */
class NonStaticMagicMethodsUnitTest extends BaseSniffTest
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
            return $this->sniffFile(__DIR__ . '/NonStaticMagicMethodsUnitTest.1.inc', $testVersion);
        } else {
            return $this->sniffFile(__DIR__ . '/NonStaticMagicMethodsUnitTest.2.inc', $testVersion);
        }
    }


    /**
     * testWrongMethodVisibility
     *
     * @dataProvider dataWrongMethodVisibility
     *
     * @param string $methodName        Method name.
     * @param string $desiredVisibility The visibility the method should have.
     * @param string $testVisibility    The visibility the method actually has in the test.
     * @param int    $line              The line number.
     * @param bool   $isTrait           Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testWrongMethodVisibility($methodName, $desiredVisibility, $testVisibility, $line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->getTestFile($isTrait, '5.3-99.0');
        $this->assertError($file, $line, "Visibility for magic method {$methodName} must be {$desiredVisibility}. Found: {$testVisibility}");
    }

    /**
     * Data provider.
     *
     * @see testWrongMethodVisibility()
     *
     * @return array
     */
    public function dataWrongMethodVisibility()
    {
        return array(
            /*
             * File: NonStaticMagicMethodsUnitTest.1.inc.
             */
            // Class.
            array('__get', 'public', 'private', 32),
            array('__set', 'public', 'protected', 33),
            array('__isset', 'public', 'private', 34),
            array('__unset', 'public', 'protected', 35),
            array('__call', 'public', 'private', 36),
            array('__callStatic', 'public', 'protected', 37),
            array('__sleep', 'public', 'private', 38),
            array('__toString', 'public', 'protected', 39),

            // Alternative property order & stacked.
            array('__set', 'public', 'protected', 56),
            array('__isset', 'public', 'private', 57),
            array('__get', 'public', 'private', 65),

            // Interface.
            array('__get', 'public', 'protected', 98),
            array('__set', 'public', 'private', 99),
            array('__isset', 'public', 'protected', 100),
            array('__unset', 'public', 'private', 101),
            array('__call', 'public', 'protected', 102),
            array('__callStatic', 'public', 'private', 103),
            array('__sleep', 'public', 'protected', 104),
            array('__toString', 'public', 'private', 105),

            // Anonymous class.
            array('__get', 'public', 'private', 149),
            array('__set', 'public', 'protected', 150),
            array('__isset', 'public', 'private', 151),
            array('__unset', 'public', 'protected', 152),
            array('__call', 'public', 'private', 153),
            array('__callStatic', 'public', 'protected', 154),
            array('__sleep', 'public', 'private', 155),
            array('__toString', 'public', 'protected', 156),

            // PHP 7.4: __(un)serialize()
            array('__serialize', 'public', 'protected', 179),
            array('__unserialize', 'public', 'private', 180),

            // More magic methods.
            array('__destruct', 'public', 'private', 201),
            array('__debugInfo', 'public', 'protected', 202),
            array('__invoke', 'public', 'private', 203),
            array('__set_state', 'public', 'protected', 204),

            /*
             * File: NonStaticMagicMethodsUnitTest.2.inc.
             */
            // Trait.
            array('__get', 'public', 'private', 36, true),
            array('__set', 'public', 'protected', 37, true),
            array('__isset', 'public', 'private', 38, true),
            array('__unset', 'public', 'protected', 39, true),
            array('__call', 'public', 'private', 40, true),
            array('__callStatic', 'public', 'protected', 41, true),
            array('__sleep', 'public', 'private', 42, true),
            array('__toString', 'public', 'protected', 43, true),
            array('__serialize', 'public', 'private', 44, true),
            array('__unserialize', 'public', 'protected', 45, true),
        );
    }


    /**
     * testWrongStaticMethod
     *
     * @dataProvider dataWrongStaticMethod
     *
     * @param string $methodName Method name.
     * @param int    $line       The line number.
     * @param bool   $isTrait    Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testWrongStaticMethod($methodName, $line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->getTestFile($isTrait, '5.3-99.0');
        $this->assertError($file, $line, "Magic method {$methodName} cannot be defined as static.");
    }

    /**
     * Data provider.
     *
     * @see testWrongStaticMethod()
     *
     * @return array
     */
    public function dataWrongStaticMethod()
    {
        return array(
            /*
             * File: NonStaticMagicMethodsUnitTest.1.inc.
             */
            // Class.
            array('__get', 44),
            array('__set', 45),
            array('__isset', 46),
            array('__unset', 47),
            array('__call', 48),

            // Alternative property order & stacked.
            array('__get', 55),
            array('__set', 56),
            array('__isset', 57),
            array('__get', 65),

            // Interface.
            array('__get', 110),
            array('__set', 111),
            array('__isset', 112),
            array('__unset', 113),
            array('__call', 114),

            // Anonymous class.
            array('__get', 161),
            array('__set', 162),
            array('__isset', 163),
            array('__unset', 164),
            array('__call', 165),

            // PHP 7.4: __(un)serialize()
            array('__serialize', 185),
            array('__unserialize', 186),

            // More magic methods.
            array('__construct', 209),
            array('__destruct', 210),
            array('__clone', 211),
            array('__debugInfo', 212),
            array('__invoke', 213),
            /*
             * File: NonStaticMagicMethodsUnitTest.2.inc.
             */
            // Trait.
            array('__get', 50, true),
            array('__set', 51, true),
            array('__isset', 52, true),
            array('__unset', 53, true),
            array('__call', 54, true),
            array('__serialize', 57, true),
            array('__unserialize', 58, true),
        );
    }


    /**
     * testWrongNonStaticMethod
     *
     * @dataProvider dataWrongNonStaticMethod
     *
     * @param string $methodName Method name.
     * @param int    $line       The line number.
     * @param bool   $isTrait    Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testWrongNonStaticMethod($methodName, $line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->getTestFile($isTrait, '5.3-99.0');
        $this->assertError($file, $line, "Magic method {$methodName} must be defined as static.");
    }

    /**
     * Data provider.
     *
     * @see testWrongNonStaticMethod()
     *
     * @return array
     */
    public function dataWrongNonStaticMethod()
    {
        return array(
            /*
             * File: NonStaticMagicMethodsUnitTest.1.inc.
             */
            // Class.
            array('__callStatic', 49),
            array('__set_state', 50),

            // Interface.
            array('__callStatic', 115),
            array('__set_state', 116),

            // Anonymous class.
            array('__callStatic', 166),
            array('__set_state', 167),

            /*
             * File: NonStaticMagicMethodsUnitTest.2.inc.
             */
            // Trait.
            array('__callStatic', 55, true),
            array('__set_state', 56, true),

        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int  $line    The line number.
     * @param bool $isTrait Whether to load the class/interface test file or the trait test file.
     *
     * @return void
     */
    public function testNoFalsePositives($line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->getTestFile($isTrait, '5.3-99.0');
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
            /*
             * File: NonStaticMagicMethodsUnitTest.1.inc.
             */
            // Plain class.
            array(5),
            array(6),
            array(7),
            array(8),
            array(9),
            array(10),
            array(11),
            array(12),
            array(13),
            // Normal class.
            array(18),
            array(19),
            array(20),
            array(21),
            array(22),
            array(23),
            array(24),
            array(25),
            array(26),
            array(27),

            // Alternative property order & stacked.
            array(58),

            // Plain interface.
            array(71),
            array(72),
            array(73),
            array(74),
            array(75),
            array(76),
            array(77),
            array(78),
            array(79),
            // Normal interface.
            array(84),
            array(85),
            array(86),
            array(87),
            array(88),
            array(89),
            array(90),
            array(91),
            array(92),
            array(93),

            // Plain anonymous class.
            array(122),
            array(123),
            array(124),
            array(125),
            array(126),
            array(127),
            array(128),
            array(129),
            array(130),
            // Normal anonymous class.
            array(135),
            array(136),
            array(137),
            array(138),
            array(139),
            array(140),
            array(141),
            array(142),
            array(143),
            array(144),

            // PHP 7.4: __(un)serialize()
            array(173),
            array(174),

            // More magic methods.
            array(192),
            array(193),
            array(194),
            array(195),
            array(196),

            /*
             * File: NonStaticMagicMethodsUnitTest.2.inc.
             */
            // Plain trait.
            array(5, true),
            array(6, true),
            array(7, true),
            array(8, true),
            array(9, true),
            array(10, true),
            array(11, true),
            array(12, true),
            array(13, true),
            array(14, true),
            array(15, true),
            // Normal trait.
            array(20, true),
            array(21, true),
            array(22, true),
            array(23, true),
            array(24, true),
            array(25, true),
            array(26, true),
            array(27, true),
            array(28, true),
            array(29, true),
            array(30, true),
            array(31, true),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        // File: NonStaticMagicMethodsUnitTest.1.inc.
        $file = $this->getTestFile(false, '5.2');
        $this->assertNoViolation($file);

        // File: NonStaticMagicMethodsUnitTest.2.inc.
        $file = $this->getTestFile(true, '5.2');
        $this->assertNoViolation($file);
    }
}
