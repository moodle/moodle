<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Classes;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect use of late static binding as introduced in PHP 5.3.
 *
 * Checks for:
 * - Late static binding as introduced in PHP 5.3.
 * - Late static binding being used outside of class scope (unsupported).
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/language.oop5.late-static-bindings.php
 * @link https://wiki.php.net/rfc/lsb_parentself_forwarding
 *
 * @since 7.0.3
 * @since 9.0.0 Renamed from `LateStaticBindingSniff` to `NewLateStaticBindingSniff`.
 */
class NewLateStaticBindingSniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.3
     *
     * @return array
     */
    public function register()
    {
        return array(\T_STATIC);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.3
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true, null, true);
        if ($nextNonEmpty === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if ($tokens[$nextNonEmpty]['code'] !== \T_DOUBLE_COLON) {
            return;
        }

        $inClass = $this->inClassScope($phpcsFile, $stackPtr, false);

        if ($inClass === true && $this->supportsBelow('5.2') === true) {
            $phpcsFile->addError(
                'Late static binding is not supported in PHP 5.2 or earlier.',
                $stackPtr,
                'Found'
            );
        }

        if ($inClass === false) {
            $phpcsFile->addError(
                'Late static binding is not supported outside of class scope.',
                $stackPtr,
                'OutsideClassScope'
            );
        }
    }
}
