<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\MethodUse;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * As of PHP 5.3, the `__toString()` magic method can no longer be passed arguments.
 *
 * Sister-sniff to `PHPCompatibility.FunctionDeclarations.ForbiddenToStringParameters`.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/migration53.incompatible.php
 * @link https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
 *
 * @since 9.2.0
 */
class ForbiddenToStringParametersSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.2.0
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_DOUBLE_COLON,
            \T_OBJECT_OPERATOR,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('5.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_STRING) {
            /*
             * Not a method call.
             *
             * Note: This disregards method calls with the method name in a variable, like:
             *   $method = '__toString';
             *   $obj->$method();
             * However, that would be very hard to examine reliably anyway.
             */
            return;
        }

        if (strtolower($tokens[$nextNonEmpty]['content']) !== '__tostring') {
            // Not a call to the __toString() method.
            return;
        }

        $openParens = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonEmpty + 1), null, true);
        if ($openParens === false || $tokens[$openParens]['code'] !== \T_OPEN_PARENTHESIS) {
            // Not a method call.
            return;
        }

        $closeParens = $phpcsFile->findNext(Tokens::$emptyTokens, ($openParens + 1), null, true);
        if ($closeParens === false || $tokens[$closeParens]['code'] === \T_CLOSE_PARENTHESIS) {
            // Not a method call.
            return;
        }

        // If we're still here, then this is a call to the __toString() magic method passing parameters.
        $phpcsFile->addError(
            'The __toString() magic method will no longer accept passed arguments since PHP 5.3',
            $stackPtr,
            'Passed'
        );
    }
}
