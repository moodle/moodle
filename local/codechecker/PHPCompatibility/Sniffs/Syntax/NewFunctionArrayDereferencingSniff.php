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
 * Detect function array dereferencing as introduced in PHP 5.4.
 *
 * PHP 5.4 supports direct array dereferencing on the return of a method/function call.
 *
 * As of PHP 7.0, this also works when using curly braces for the dereferencing.
 * While unclear, this most likely has to do with the Uniform Variable Syntax changes.
 *
 * PHP version 5.4
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/language.types.array.php#example-63
 * @link https://www.php.net/manual/en/migration54.new-features.php
 * @link https://wiki.php.net/rfc/functionarraydereferencing
 * @link https://wiki.php.net/rfc/uniform_variable_syntax
 *
 * {@internal The reason for splitting the logic of this sniff into different methods is
 *            to allow re-use of the logic by the PHP 7.4 RemovedCurlyBraceArrayAccess sniff.}
 *
 * @since 7.0.0
 * @since 9.3.0 Now also detects dereferencing using curly braces.
 */
class NewFunctionArrayDereferencingSniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array
     */
    public function register()
    {
        return array(\T_STRING);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.6') === false) {
            return;
        }

        $dereferencing = $this->isFunctionArrayDereferencing($phpcsFile, $stackPtr);
        if (empty($dereferencing)) {
            return;
        }

        $tokens     = $phpcsFile->getTokens();
        $supports53 = $this->supportsBelow('5.3');

        foreach ($dereferencing as $openBrace => $closeBrace) {
            if ($supports53 === true
                && $tokens[$openBrace]['type'] === 'T_OPEN_SQUARE_BRACKET'
            ) {
                $phpcsFile->addError(
                    'Function array dereferencing is not present in PHP version 5.3 or earlier',
                    $openBrace,
                    'Found'
                );

                continue;
            }

            // PHP 7.0 function array dereferencing using curly braces.
            if ($tokens[$openBrace]['type'] === 'T_OPEN_CURLY_BRACKET') {
                $phpcsFile->addError(
                    'Function array dereferencing using curly braces is not present in PHP version 5.6 or earlier',
                    $openBrace,
                    'FoundUsingCurlies'
                );
            }
        }
    }


    /**
     * Check if the return of a function/method call is being dereferenced.
     *
     * @since 9.3.0 Logic split off from the process method.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return array Array containing stack pointers to the open/close braces
     *               involved in the function dereferencing;
     *               or an empty array if no function dereferencing was detected.
     */
    public function isFunctionArrayDereferencing(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Next non-empty token should be the open parenthesis.
        $openParenthesis = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
        if ($openParenthesis === false || $tokens[$openParenthesis]['code'] !== \T_OPEN_PARENTHESIS) {
            return array();
        }

        // Don't throw errors during live coding.
        if (isset($tokens[$openParenthesis]['parenthesis_closer']) === false) {
            return array();
        }

        // Is this T_STRING really a function or method call ?
        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prevToken !== false
            && \in_array($tokens[$prevToken]['code'], array(\T_DOUBLE_COLON, \T_OBJECT_OPERATOR), true) === false
        ) {
            if ($tokens[$prevToken]['code'] === \T_BITWISE_AND) {
                // This may be a function declared by reference.
                $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevToken - 1), null, true);
            }

            $ignore = array(
                \T_FUNCTION  => true,
                \T_CONST     => true,
                \T_USE       => true,
                \T_NEW       => true,
                \T_CLASS     => true,
                \T_INTERFACE => true,
            );

            if (isset($ignore[$tokens[$prevToken]['code']]) === true) {
                // Not a call to a PHP function or method.
                return array();
            }
        }

        $current = $tokens[$openParenthesis]['parenthesis_closer'];
        $braces  = array();

        do {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($current + 1), null, true, null, true);
            if ($nextNonEmpty === false) {
                break;
            }

            if ($tokens[$nextNonEmpty]['type'] === 'T_OPEN_SQUARE_BRACKET'
                || $tokens[$nextNonEmpty]['type'] === 'T_OPEN_CURLY_BRACKET' // PHP 7.0+.
            ) {
                if (isset($tokens[$nextNonEmpty]['bracket_closer']) === false) {
                    // Live coding or parse error.
                    break;
                }

                $braces[$nextNonEmpty] = $tokens[$nextNonEmpty]['bracket_closer'];

                // Continue, just in case there is nested array access, i.e. `echo $foo->bar()[0][2];`.
                $current = $tokens[$nextNonEmpty]['bracket_closer'];
                continue;
            }

            // If we're still here, we've reached the end of the function call.
            break;

        } while (true);

        return $braces;
    }
}
