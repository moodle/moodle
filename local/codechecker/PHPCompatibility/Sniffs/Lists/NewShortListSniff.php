<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Lists;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect short list syntax for symmetric array destructuring.
 *
 * "The shorthand array syntax (`[]`) may now be used to destructure arrays for
 * assignments (including within `foreach`), as an alternative to the existing
 * `list()` syntax, which is still supported."
 *
 * PHP version 7.1
 *
 * @link https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
 * @link https://wiki.php.net/rfc/short_list_syntax
 *
 * @since 9.0.0
 */
class NewShortListSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.0.0
     *
     * @return array
     */
    public function register()
    {
        return array(\T_OPEN_SHORT_ARRAY);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('7.0') === false) {
            return;
        }

        if ($this->isShortList($phpcsFile, $stackPtr) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $closer = $tokens[$stackPtr]['bracket_closer'];

        $hasVariable = $phpcsFile->findNext(\T_VARIABLE, ($stackPtr + 1), $closer);
        if ($hasVariable === false) {
            // List syntax is only valid if there are variables in it.
            return;
        }

        $phpcsFile->addError(
            'The shorthand list syntax "[]" to destructure arrays is not available in PHP 7.0 or earlier.',
            $stackPtr,
            'Found'
        );

        return ($closer + 1);
    }
}
