<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Miscellaneous;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;

/**
 * Test the NewPHPOpenTagEOF sniff.
 *
 * @group newPHPOpenTagEOF
 * @group miscellaneous
 *
 * @covers \PHPCompatibility\Sniffs\Miscellaneous\NewPHPOpenTagEOFSniff
 *
 * @since 9.3.0
 */
class NewPHPOpenTagEOFUnitTest extends BaseSniffTest
{

    /**
     * Sprintf template for the names of the numbered test case files.
     *
     * @var string
     */
    const TEST_FILE = 'NewPHPOpenTagEOFUnitTest.%d.inc';


    /**
     * Whether or not the sniff can reliably run or if tests should be skipped.
     *
     * @var bool
     */
    protected static $shouldSkip = false;


    /**
     * Set up skip condition.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $phpcsVersion  = PHPCSHelper::getVersion();
        $shortOpenTags = (bool) ini_get('short_open_tag');

        /*
         * When using PHPCS 2.5.1 or lower combined with PHP 5.3 with the short_open_tag
         * INI setting set to `On`, we run into a PHPCS Tokenizer issue which will
         * cause the tests to fail.
         */
        if ($shortOpenTags === true
            && version_compare($phpcsVersion, '2.6.0', '<')
            && version_compare(\PHP_VERSION_ID, '50400', '<')
        ) {
            self::$shouldSkip = true;
        }

        parent::setUpBeforeClass();
    }


    /**
     * Set up skip condition.
     *
     * @return void
     */
    protected function setUp()
    {
        if (self::$shouldSkip === true) {
            $this->markTestSkipped('Tests can not be run on PHP 5.3 with short_open_tag=On in combination with PHPCS < 2.6.0');
            return;
        }

        parent::setUp();
    }


    /**
     * Test detection of stand alone PHP open tag at end of file.
     *
     * @dataProvider dataNewPHPOpenTagEOF
     *
     * @param int $fileNumber The number of the test case file.
     * @param int $line       The line number.
     *
     * @return void
     */
    public function testNewPHPOpenTagEOF($fileNumber, $line)
    {
        $fileName = __DIR__ . '/' . sprintf(self::TEST_FILE, $fileNumber);

        $file = $this->sniffFile($fileName, '7.3');
        $this->assertError($file, $line, 'A PHP open tag at the end of a file, without trailing newline, was not supported in PHP 7.3 or earlier and would result in a syntax error or be interpreted as a literal string');
    }

    /**
     * Data provider.
     *
     * @see testNewPHPOpenTagEOF()
     *
     * @return array
     */
    public function dataNewPHPOpenTagEOF()
    {
        return array(
            array(4, 1),
            array(5, 13),
            array(6, 4),
            array(7, 6),
        );
    }


    /**
     * Verify no false positives are thrown for non-violation open tags in a file
     * containing multiple open tags.
     *
     * @dataProvider dataNoFalsePositivesOnLine
     *
     * @param int $fileNumber The number of the test case file.
     * @param int $line       The line number.
     *
     * @return void
     */
    public function testNoFalsePositivesOnLine($fileNumber, $line)
    {
        $fileName = __DIR__ . '/' . sprintf(self::TEST_FILE, $fileNumber);

        $file = $this->sniffFile($fileName, '7.3');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesOnLine()
     *
     * @return array
     */
    public function dataNoFalsePositivesOnLine()
    {
        return array(
            array(5, 1),
            array(5, 8),
            array(6, 1),
            array(7, 1),
        );
    }


    /**
     * Verify no false positives are thrown for valid files.
     *
     * @dataProvider dataNoFalsePositivesOnFile
     *
     * @param int $fileNumber The number of the test case file.
     *
     * @return void
     */
    public function testNoFalsePositivesOnFile($fileNumber)
    {
        $fileName = __DIR__ . '/' . sprintf(self::TEST_FILE, $fileNumber);

        $file = $this->sniffFile($fileName, '7.3');
        $this->assertNoViolation($file);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositivesOnFile()
     *
     * @return array
     */
    public function dataNoFalsePositivesOnFile()
    {
        return array(
            array(1),
            array(2),
            array(3),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @dataProvider dataNoViolationsInFileOnValidVersion
     *
     * @param int $fileNumber The number of the test case file.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion($fileNumber)
    {
        $fileName = __DIR__ . '/' . sprintf(self::TEST_FILE, $fileNumber);

        $file = $this->sniffFile($fileName, '7.4');
        $this->assertNoViolation($file);
    }

    /**
     * Data provider.
     *
     * @see testNoViolationsInFileOnValidVersion()
     *
     * @return array
     */
    public function dataNoViolationsInFileOnValidVersion()
    {
        $data = array(
            array(1),
            array(2),
            array(3),
            array(5),
            array(6),
            array(7),
        );

        // In PHPCS 2.x, the `Internal.NoCodeFound` error will come through, even when unit testing.
        if (version_compare(PHPCSHelper::getVersion(), '3.0.0', '>')) {
            $data[] = array(4);
        }

        return $data;
    }
}
