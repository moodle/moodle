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

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Helper class to facilitate testing of the methods within the abstract \PHPCompatibility\Sniff class.
 *
 * @since 7.0.3
 */
class TestHelperPHPCompatibility extends Sniff
{
    /**
     * Dummy method to bypass the abstract method implementation requirements.
     *
     * @since 7.0.3
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Dummy method to bypass the abstract method implementation requirements.
     *
     * @since 7.0.3
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
    }

    /**
     * Wrapper to make the protected parent::isNumber() method testable.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                   $start       Start of the snippet (inclusive), i.e. this
     *                                           token will be examined as part of the snippet.
     * @param int                   $end         End of the snippet (inclusive), i.e. this
     *                                           token will be examined as part of the snippet.
     * @param bool                  $allowFloats Whether to only consider integers, or also floats.
     *
     * @return int|float|bool
     */
    public function isNumber(File $phpcsFile, $start, $end, $allowFloats = false)
    {
        return parent::isNumber($phpcsFile, $start, $end, $allowFloats);
    }

    /**
     * Wrapper to make the protected parent::isNumericCalculation() method testable.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $start     Start of the snippet (inclusive), i.e. this
     *                                         token will be examined as part of the snippet.
     * @param int                   $end       End of the snippet (inclusive), i.e. this
     *                                         token will be examined as part of the snippet.
     *
     * @return bool
     */
    public function isNumericCalculation(File $phpcsFile, $start, $end)
    {
        return parent::isNumericCalculation($phpcsFile, $start, $end);
    }
}
