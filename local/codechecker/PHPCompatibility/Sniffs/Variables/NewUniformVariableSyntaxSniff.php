<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Variables;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * The interpretation of variable variables has changed in PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect
 * @link https://wiki.php.net/rfc/uniform_variable_syntax
 *
 * @since 7.1.2
 * @since 9.0.0 Renamed from `VariableVariablesSniff` to `NewUniformVariableSyntaxSniff`.
 */
class NewUniformVariableSyntaxSniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.1.2
     *
     * @return array
     */
    public function register()
    {
        return array(\T_VARIABLE);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.1.2
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Verify that the next token is a square open bracket. If not, bow out.
        $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);

        if ($nextToken === false || $tokens[$nextToken]['code'] !== \T_OPEN_SQUARE_BRACKET || isset($tokens[$nextToken]['bracket_closer']) === false) {
            return;
        }

        // The previous non-empty token has to be a $, -> or ::.
        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true, null, true);
        if ($prevToken === false || \in_array($tokens[$prevToken]['code'], array(\T_DOLLAR, \T_OBJECT_OPERATOR, \T_DOUBLE_COLON), true) === false) {
            return;
        }

        // For static object calls, it only applies when this is a function call.
        if ($tokens[$prevToken]['code'] === \T_DOUBLE_COLON) {
            $hasBrackets = $tokens[$nextToken]['bracket_closer'];
            while (($hasBrackets = $phpcsFile->findNext(Tokens::$emptyTokens, ($hasBrackets + 1), null, true, null, true)) !== false) {
                if ($tokens[$hasBrackets]['code'] === \T_OPEN_SQUARE_BRACKET) {
                    if (isset($tokens[$hasBrackets]['bracket_closer'])) {
                        $hasBrackets = $tokens[$hasBrackets]['bracket_closer'];
                        continue;
                    } else {
                        // Live coding.
                        return;
                    }

                } elseif ($tokens[$hasBrackets]['code'] === \T_OPEN_PARENTHESIS) {
                    // Caught!
                    break;

                } else {
                    // Not a function call, so bow out.
                    return;
                }
            }

            // Now let's also prevent false positives when used with self and static which still work fine.
            $classToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevToken - 1), null, true, null, true);
            if ($classToken !== false) {
                if ($tokens[$classToken]['code'] === \T_STATIC || $tokens[$classToken]['code'] === \T_SELF) {
                    return;
                } elseif ($tokens[$classToken]['code'] === \T_STRING && $tokens[$classToken]['content'] === 'self') {
                    return;
                }
            }
        }

        $phpcsFile->addError(
            'Indirect access to variables, properties and methods will be evaluated strictly in left-to-right order since PHP 7.0. Use curly braces to remove ambiguity.',
            $stackPtr,
            'Found'
        );
    }
}
