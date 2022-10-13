<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Operators;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect code affected by the change in operator precedence of concatenation in PHP 8.0.
 *
 * In PHP < 8.0 the operator precedence of `.`, `+` and `-` are the same.
 * As of PHP 8.0, the operator precedence of the concatenation operator will be
 * lowered to be right below the `<<` and `>>` operators.
 *
 * As of PHP 7.4, a deprecation warning will be thrown upon encountering an
 * unparenthesized expression containing an `.` before a `+` or `-`.
 *
 * PHP version 7.4
 * PHP version 8.0
 *
 * @link https://wiki.php.net/rfc/concatenation_precedence
 * @link https://www.php.net/manual/en/language.operators.precedence.php
 *
 * @since 9.2.0
 */
class ChangedConcatOperatorPrecedenceSniff extends Sniff
{

    /**
     * List of tokens with a lower operator precedence than concatenation in PHP >= 8.0.
     *
     * @since 9.2.0
     *
     * @var array
     */
    private $tokensWithLowerPrecedence = array(
        'T_BITWISE_AND' => true,
        'T_BITWISE_XOR' => true,
        'T_BITWISE_OR'  => true,
        'T_COALESCE'    => true,
        'T_INLINE_THEN' => true,
        'T_INLINE_ELSE' => true,
        'T_YIELD_FROM'  => true,
        'T_YIELD'       => true,
    );


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
            \T_PLUS,
            \T_MINUS,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.4') === false) {
            return;
        }

        if ($this->isUnaryPlusMinus($phpcsFile, $stackPtr) === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        for ($i = ($stackPtr - 1); $stackPtr >= 0; $i--) {
            if ($tokens[$i]['code'] === \T_STRING_CONCAT) {
                // Found one.
                break;
            }

            if ($tokens[$i]['code'] === \T_SEMICOLON
                || $tokens[$i]['code'] === \T_OPEN_CURLY_BRACKET
                || $tokens[$i]['code'] === \T_OPEN_TAG
                || $tokens[$i]['code'] === \T_OPEN_TAG_WITH_ECHO
                || $tokens[$i]['code'] === \T_COMMA
                || $tokens[$i]['code'] === \T_COLON
                || $tokens[$i]['code'] === \T_CASE
            ) {
                // If we reached any of the above tokens, we've reached the end of
                // the statement without encountering a concatenation operator.
                return;
            }

            if ($tokens[$i]['code'] === \T_OPEN_CURLY_BRACKET
                && isset($tokens[$i]['bracket_closer'])
                && $tokens[$i]['bracket_closer'] > $stackPtr
            ) {
                // No need to look any further, this is plus/minus within curly braces
                // and we've reached the open curly.
                return;
            }

            if ($tokens[$i]['code'] === \T_OPEN_PARENTHESIS
                && isset($tokens[$i]['parenthesis_closer'])
                && $tokens[$i]['parenthesis_closer'] > $stackPtr
            ) {
                // No need to look any further, this is plus/minus within parenthesis
                // and we've reached the open parenthesis.
                return;
            }

            if (($tokens[$i]['code'] === \T_OPEN_SHORT_ARRAY
                || $tokens[$i]['code'] === \T_OPEN_SQUARE_BRACKET)
                && isset($tokens[$i]['bracket_closer'])
                && $tokens[$i]['bracket_closer'] > $stackPtr
            ) {
                // No need to look any further, this is plus/minus within a short array
                // or array key square brackets and we've reached the opener.
                return;
            }

            if ($tokens[$i]['code'] === \T_CLOSE_CURLY_BRACKET) {
                if (isset($tokens[$i]['scope_owner'])) {
                    // Different scope, we've passed the start of the statement.
                    return;
                }

                if (isset($tokens[$i]['bracket_opener'])) {
                    $i = $tokens[$i]['bracket_opener'];
                }

                continue;
            }

            if ($tokens[$i]['code'] === \T_CLOSE_PARENTHESIS
                && isset($tokens[$i]['parenthesis_opener'])
            ) {
                // Skip over statements in parenthesis, including long arrays.
                $i = $tokens[$i]['parenthesis_opener'];
                continue;
            }

            if (($tokens[$i]['code'] === \T_CLOSE_SQUARE_BRACKET
                || $tokens[$i]['code'] === \T_CLOSE_SHORT_ARRAY)
                && isset($tokens[$i]['bracket_opener'])
            ) {
                // Skip over array keys and short arrays.
                $i = $tokens[$i]['bracket_opener'];
                continue;
            }

            // Check for chain being broken by a token with a lower precedence.
            if (isset(Tokens::$booleanOperators[$tokens[$i]['code']]) === true
                || isset(Tokens::$assignmentTokens[$tokens[$i]['code']]) === true
            ) {
                return;
            }

            if (isset($this->tokensWithLowerPrecedence[$tokens[$i]['type']]) === true) {
                if ($tokens[$i]['code'] === \T_BITWISE_AND
                    && $phpcsFile->isReference($i) === true
                ) {
                    continue;
                }

                return;
            }
        }

        $message = 'Using an unparenthesized expression containing a "." before a "+" or "-" has been deprecated in PHP 7.4';
        $isError = false;
        if ($this->supportsAbove('8.0') === true) {
            $message .= ' and removed in PHP 8.0';
            $isError  = true;
        }

        $this->addMessage($phpcsFile, $message, $i, $isError);
    }
}
