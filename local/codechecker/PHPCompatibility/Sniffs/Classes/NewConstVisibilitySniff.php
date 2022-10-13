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
 * Visibility for class constants is available since PHP 7.1.
 *
 * PHP version 7.1
 *
 * @link https://wiki.php.net/rfc/class_const_visibility
 * @link https://www.php.net/manual/en/language.oop5.constants.php#language.oop5.basic.class.this
 *
 * @since 7.0.7
 */
class NewConstVisibilitySniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.7
     *
     * @return array
     */
    public function register()
    {
        return array(\T_CONST);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.7
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('7.0') === false) {
            return;
        }

        $tokens    = $phpcsFile->getTokens();
        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true, null, true);

        // Is the previous token a visibility indicator ?
        if ($prevToken === false || isset(Tokens::$scopeModifiers[$tokens[$prevToken]['code']]) === false) {
            return;
        }

        // Is this a class constant ?
        if ($this->isClassConstant($phpcsFile, $stackPtr) === false) {
            // This may be a constant declaration in the global namespace with visibility,
            // but that would throw a parse error, i.e. not our concern.
            return;
        }

        $phpcsFile->addError(
            'Visibility indicators for class constants are not supported in PHP 7.0 or earlier. Found "%s const"',
            $stackPtr,
            'Found',
            array($tokens[$prevToken]['content'])
        );
    }
}
