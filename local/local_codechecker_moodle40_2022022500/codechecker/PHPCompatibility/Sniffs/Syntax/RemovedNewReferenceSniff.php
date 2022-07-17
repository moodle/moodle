<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Syntax;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect the use of assigning the return value of `new` by reference.
 *
 * This syntax has been deprecated since PHP 5.3 and removed in PHP 7.0.
 *
 * PHP version 5.3
 * PHP version 7.0
 *
 * @link https://wiki.php.net/rfc/remove_deprecated_functionality_in_php7
 *
 * @since 5.5
 * @since 9.0.0 Renamed from `DeprecatedNewReferenceSniff` to `RemovedNewReferenceSniff`.
 */
class RemovedNewReferenceSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     *
     * @return array
     */
    public function register()
    {
        return array(\T_NEW);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('5.3') === false) {
            return;
        }

        $tokens       = $phpcsFile->getTokens();
        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prevNonEmpty === false || $tokens[$prevNonEmpty]['type'] !== 'T_BITWISE_AND') {
            return;
        }

        $error     = 'Assigning the return value of new by reference is deprecated in PHP 5.3';
        $isError   = false;
        $errorCode = 'Deprecated';

        if ($this->supportsAbove('7.0') === true) {
            $error    .= ' and has been removed in PHP 7.0';
            $isError   = true;
            $errorCode = 'Removed';
        }

        $this->addMessage($phpcsFile, $error, $stackPtr, $isError, $errorCode);
    }
}
