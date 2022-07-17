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
use PHPCompatibility\Sniffs\Upgrade\LowPHPSniff;

/**
 * Test the LowPHP sniff.
 *
 * @group lowPHP
 * @group upgrade
 *
 * @covers \PHPCompatibility\Sniffs\Upgrade\LowPHPSniff
 *
 * @since 9.3.0
 */
class LowPHPUnitTest extends BaseSniffTest
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
    protected $phpVersion;


    /**
     * Set up the test file for this unit test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Sniff file without testVersion as all checks run independently of testVersion being set.
        $this->sniffResult = $this->sniffFile(__FILE__);
        $this->phpVersion  = phpversion();
    }


    /**
     * Test throwing the PHP upgrade notice.
     *
     * @return void
     */
    public function testUpgradeNotice()
    {
        if (version_compare($this->phpVersion, LowPHPSniff::MIN_SUPPORTED_VERSION, '<')) {
            $this->assertError(
                $this->sniffResult,
                1,
                'Please be advised that the minimum PHP version the PHPCompatibility standard supports is ' . LowPHPSniff::MIN_SUPPORTED_VERSION
            );
        } elseif (version_compare($this->phpVersion, LowPHPSniff::MIN_RECOMMENDED_VERSION, '<')) {
            $this->assertWarning(
                $this->sniffResult,
                1,
                'Please be advised that for the most reliable PHPCompatibility results, PHP ' . LowPHPSniff::MIN_RECOMMENDED_VERSION . ' or higher should be used'
            );
        } else {
            $this->assertNoViolation($this->sniffResult);
        }
    }
}
