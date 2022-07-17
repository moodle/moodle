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
 * Test the NewExceptionsFromToString sniff.
 *
 * @group newExceptionsFromToString
 * @group functionDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\NewExceptionsFromToStringSniff
 *
 * @since 9.2.0
 */
class NewExceptionsFromToStringUnitTest extends BaseSniffTest
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
     * testNewExceptionsFromToString.
     *
     * @dataProvider dataNewExceptionsFromToString
     *
     * @param int  $line    The line number where a warning is expected.
     * @param bool $isTrait Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testNewExceptionsFromToString($line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->sniffFile(__FILE__, '7.3');
        $this->assertError($file, $line, 'Throwing exceptions from __toString() was not allowed prior to PHP 7.4');
    }

    /**
     * Data provider.
     *
     * @see testNewExceptionsFromToString()
     *
     * @return array
     */
    public function dataNewExceptionsFromToString()
    {
        return array(
            array(39),
            array(48, true),
            array(57),
            array(80),
            array(83),
            array(92),
            array(130),
            array(141),
            array(152),
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
        $file = $this->sniffFile(__FILE__, '7.3');
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
        // No errors expected on the first 34 lines.
        for ($line = 1; $line <= 34; $line++) {
            $cases[] = array($line);
        }

        $cases[] = array(37);
        $cases[] = array(46);
        $cases[] = array(55);

        // No false positive for try/catch.
        for ($line = 64; $line <= 79; $line++) {
            $cases[] = array($line);
        }

        $cases[] = array(90);

        // No false positive for docblock check.
        for ($line = 103; $line <= 122; $line++) {
            $cases[] = array($line);
        }

        $cases[] = array(154);

        return $cases;
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.4');
        $this->assertNoViolation($file);
    }
}
