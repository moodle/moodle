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
 * Test the ForbiddenToStringParameters sniff.
 *
 * @group newForbiddenToStringParameters
 * @group functionDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\ForbiddenToStringParametersSniff
 *
 * @since 9.2.0
 */
class ForbiddenToStringParametersUnitTest extends BaseSniffTest
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
     * testForbiddenToStringParameters.
     *
     * @dataProvider dataForbiddenToStringParameters
     *
     * @param int  $line    The line number where a warning is expected.
     * @param bool $isTrait Whether the test relates to a method in a trait.
     *
     * @return void
     */
    public function testForbiddenToStringParameters($line, $isTrait = false)
    {
        if ($isTrait === true && self::$recognizesTraits === false) {
            $this->markTestSkipped('Traits are not recognized on PHPCS < 2.4.0 in combination with PHP < 5.4');
            return;
        }

        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertError($file, $line, 'The __toString() magic method can no longer accept arguments since PHP 5.3');
    }

    /**
     * Data provider.
     *
     * @see testForbiddenToStringParameters()
     *
     * @return array
     */
    public function dataForbiddenToStringParameters()
    {
        return array(
            array(17),
            array(21),
            array(25),
            array(31, true),
            array(37),
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
        $file = $this->sniffFile(__FILE__, '5.3');
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
        // No errors expected on the first 15 lines.
        for ($line = 1; $line <= 15; $line++) {
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
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file);
    }
}
