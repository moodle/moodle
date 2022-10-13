<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Util\Tests;

use PHPUnit_Framework_TestCase as PHPUnit_TestCase;
use PHPCompatibility\PHPCSHelper;
use PHPCompatibility\Util\Tests\TestHelperPHPCompatibility;
use PHP_CodeSniffer_File as File;

/**
 * Base class to use when testing utility methods.
 *
 * Set up and Tear down methods for testing methods in the Sniff.php file.
 *
 * @since 7.0.3
 * @since 7.0.5 Renamed from `BaseAbstractClassMethodTest` to `CoreMethodTestFrame`.
 * @since 7.1.2 No longer extends the `BaseSniffTest` class.
 */
abstract class CoreMethodTestFrame extends PHPUnit_TestCase
{

    /**
     * The \PHP_CodeSniffer_File object containing parsed contents of this file.
     *
     * @since 7.0.3
     *
     * @var \PHP_CodeSniffer_File
     */
    protected $phpcsFile;

    /**
     * A wrapper for the abstract PHPCompatibility sniff.
     *
     * @since 7.0.3
     *
     * @var \PHPCompatibility\Sniff
     */
    protected $helperClass;


    /**
     * Sets up this unit test.
     *
     * @since 7.0.3
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->helperClass = new TestHelperPHPCompatibility();

        $FQClassName = \get_class($this);
        $parts       = explode('\\', $FQClassName);
        $className   = array_pop($parts);
        $subDir      = array_pop($parts);
        $filename    = realpath(__DIR__) . \DIRECTORY_SEPARATOR . $subDir . \DIRECTORY_SEPARATOR . $className . '.inc';
        $contents    = file_get_contents($filename);

        if (version_compare(PHPCSHelper::getVersion(), '2.99.99', '>')) {
            // PHPCS 3.x.
            $config            = new \PHP_Codesniffer\Config();
            $config->standards = array('PHPCompatibility');

            $ruleset = new \PHP_CodeSniffer\Ruleset($config);

            $this->phpcsFile = new \PHP_CodeSniffer\Files\DummyFile($contents, $ruleset, $config);
            $this->phpcsFile->process();

        } else {
            // PHPCS 2.x.
            $phpcs           = new \PHP_CodeSniffer();
            $this->phpcsFile = new File(
                $filename,
                array(),
                array(),
                $phpcs
            );

            $this->phpcsFile->start($contents);
        }
    }

    /**
     * Clean up after finished test.
     *
     * @since 7.0.3
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile, $this->helperClass);
    }


    /**
     * Get the token pointer for a target token based on a specific comment found on the line before.
     *
     * @since 7.1.3
     * @since 8.1.0 New `$tokenContent` parameter.
     *
     * @param string    $commentString The comment to look for.
     * @param int|array $tokenType     The type of token(s) to look for.
     * @param string    $tokenContent  Optional. The token content for the target token.
     *
     * @return int
     */
    public function getTargetToken($commentString, $tokenType, $tokenContent = null)
    {
        $start   = ($this->phpcsFile->numTokens - 1);
        $comment = $this->phpcsFile->findPrevious(
            \T_COMMENT,
            $start,
            null,
            false,
            $commentString
        );

        $tokens = $this->phpcsFile->getTokens();
        $end    = $start;

        // Limit the token finding to between this and the next case comment.
        for ($i = ($comment + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] !== \T_COMMENT) {
                continue;
            }

            if (stripos($tokens[$i]['content'], '/* Case') === 0) {
                $end = $i;
                break;
            }
        }

        $target = $this->phpcsFile->findNext(
            $tokenType,
            ($comment + 1),
            $end,
            false,
            $tokenContent
        );

        if ($target === false) {
            $this->assertFalse(true, 'Failed to find test target token.');
        }

        return $target;
    }
}
