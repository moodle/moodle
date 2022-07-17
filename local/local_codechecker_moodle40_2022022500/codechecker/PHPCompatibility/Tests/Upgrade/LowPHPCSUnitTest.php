<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Upgrade;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;
use PHPCompatibility\Sniffs\Upgrade\LowPHPCSSniff;

/**
 * Test the LowPHPCS sniff.
 *
 * @group lowPHPCS
 * @group upgrade
 *
 * @covers \PHPCompatibility\Sniffs\Upgrade\LowPHPCSSniff
 *
 * @since 9.3.0
 */
class LowPHPCSUnitTest extends BaseSniffTest
{

    /**
     * Sniffed file
     *
     * @var \PHP_CodeSniffer_File
     */
    protected $sniffResult;

    /**
     * PHPCS version detected
     *
     * @var string
     */
    protected $phpcsVersion;


    /**
     * Set up the test file for this unit test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Sniff file without testVersion as all checks run independently of testVersion being set.
        $this->sniffResult  = $this->sniffFile(__FILE__);
        $this->phpcsVersion = PHPCSHelper::getVersion();
    }


    /**
     * Test throwing the PHPCS upgrade notice.
     *
     * @return void
     */
    public function testUpgradeNotice()
    {
        if (version_compare($this->phpcsVersion, LowPHPCSSniff::MIN_SUPPORTED_VERSION, '<')) {
            $this->assertError(
                $this->sniffResult,
                1,
                'Please be advised that the minimum PHP_CodeSniffer version the PHPCompatibility standard supports is ' . LowPHPCSSniff::MIN_SUPPORTED_VERSION
            );
        } elseif (version_compare($this->phpcsVersion, LowPHPCSSniff::MIN_RECOMMENDED_VERSION, '<')) {
            $this->assertWarning(
                $this->sniffResult,
                1,
                'Please be advised that for the most reliable PHPCompatibility results, PHP_CodeSniffer ' . LowPHPCSSniff::MIN_RECOMMENDED_VERSION . ' or higher should be used'
            );
        } else {
            $this->assertNoViolation($this->sniffResult);
        }
    }
}
