<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\InitialValue;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;

/**
 * Test the NewHeredoc sniff.
 *
 * @group newHeredoc
 * @group initialValue
 *
 * @covers \PHPCompatibility\Sniffs\InitialValue\NewHeredocSniff
 *
 * @since 7.1.4
 */
class NewHeredocUnitTest extends BaseSniffTest
{

    /**
     * Whether or not traits will be recognized in PHPCS.
     *
     * @var bool
     */
    protected static $recognizesTraits = true;

    /**
     * Whether or not the tests are run on PHPCS 2.5.1.
     *
     * @var bool
     */
    protected static $isPHPCS251 = false;

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

        if (version_compare(PHPCSHelper::getVersion(), '2.5.1', '=')) {
            self::$isPHPCS251 = true;
        }

        parent::setUpBeforeClass();
    }


    /**
     * testHeredocInitialize
     *
     * @dataProvider dataHeredocInitialize
     *
     * @param int    $line    The line number.
     * @param string $type    Error type.
     * @param bool   $isTrait Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testHeredocInitialize($line, $type, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        if (self::$isPHPCS251 === true) {
            $this->markTestSkipped('PHPCS 2.5.1 contains a bug in the Tokenizer class affecting this sniff');
            return;
        }

        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertError($file, $line, "Initializing {$type} using the Heredoc syntax was not supported in PHP 5.2 or earlier");
    }

    /**
     * Data provider dataHeredocInitialize.
     *
     * @see testHeredocInitialize()
     *
     * @return array
     */
    public function dataHeredocInitialize()
    {
        return array(
            array(5, 'static variables'),
            array(13, 'constants'),
            array(19, 'class properties'),
            array(27, 'constants'),
            array(31, 'class properties'),
            array(39, 'constants'),
            array(47, 'class properties', true),
            array(52, 'constants'),
            array(60, 'constants'),
            array(87, 'static variables'),
            array(90, 'static variables'),
            array(97, 'constants'),
            array(100, 'constants'),
            array(104, 'class properties'),
            array(107, 'class properties'),
            array(115, 'default parameter values'),
            array(121, 'default parameter values'),
            array(124, 'default parameter values'),
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
            array(70),
            array(75),
            array(79),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertNoViolation($file);
    }
}
