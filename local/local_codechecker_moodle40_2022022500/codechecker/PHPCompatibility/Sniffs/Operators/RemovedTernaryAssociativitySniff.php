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
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * The left-associativity of the ternary operator is deprecated in PHP 7.4 and
 * removed in PHP 8.0.
 *
 * PHP version 7.4
 * PHP version 8.0
 *
 * @link https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.nested-ternary
 * @link https://wiki.php.net/rfc/ternary_associativity
 * @link https://github.com/php/php-src/pull/4017
 *
 * @since 9.2.0
 */
class RemovedTernaryAssociativitySniff extends Sniff
{

    /**
     * List of tokens with a lower operator precedence than ternary.
     *
     * @since 9.2.0
     *
     * @var array
     */
    private $tokensWithLowerPrecedence = array(
        'T_YIELD_FROM'  => true,
        'T_YIELD'       => true,
        'T_LOGICAL_AND' => true,
        'T_LOGICAL_OR'  => true,
        'T_LOGICAL_XOR' => true,
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
        return array(\T_INLINE_THEN);
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
        if ($this->supportsAbove('7.4') === false) {
            return;
        }

        $tokens         = $phpcsFile->getTokens();
        $endOfStatement = PHPCSHelper::findEndOfStatement($phpcsFile, $stackPtr);
        if ($tokens[$endOfStatement]['code'] !== \T_SEMICOLON
            && $tokens[$endOfStatement]['code'] !== \T_COLON
            && $tokens[$endOfStatement]['code'] !== \T_COMMA
            && $tokens[$endOfStatement]['code'] !== \T_DOUBLE_ARROW
            && $tokens[$endOfStatement]['code'] !== \T_OPEN_TAG
            && $tokens[$endOfStatement]['code'] !== \T_CLOSE_TAG
        ) {
            // End of statement is last non-empty before close brace, so make sure we examine that token too.
            ++$endOfStatement;
        }

        $ternaryCount      = 0;
        $elseCount         = 0;
        $shortTernaryCount = 0;

        // Start at $stackPtr so we don't need duplicate code for short ternary determination.
        for ($i = $stackPtr; $i < $endOfStatement; $i++) {
            if (($tokens[$i]['code'] === \T_OPEN_SHORT_ARRAY
                || $tokens[$i]['code'] === \T_OPEN_SQUARE_BRACKET
                || $tokens[$i]['code'] === \T_OPEN_CURLY_BRACKET)
                && isset($tokens[$i]['bracket_closer'])
            ) {
                // Skip over short arrays, array access keys and curlies.
                $i = $tokens[$i]['bracket_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === \T_OPEN_PARENTHESIS
                && isset($tokens[$i]['parenthesis_closer'])
            ) {
                // Skip over anything between parentheses.
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            // Check for operators with lower operator precedence.
            if (isset(Tokens::$assignmentTokens[$tokens[$i]['code']])
                || isset($this->tokensWithLowerPrecedence[$tokens[$i]['code']])
            ) {
                break;
            }

            if ($tokens[$i]['code'] === \T_INLINE_THEN) {
                ++$ternaryCount;

                if ($this->isShortTernary($phpcsFile, $i) === true) {
                    ++$shortTernaryCount;
                }

                continue;
            }

            if ($tokens[$i]['code'] === \T_INLINE_ELSE) {
                if (($ternaryCount - $elseCount) >= 2) {
                    // This is the `else` for a ternary in the middle part of a previous ternary.
                    --$ternaryCount;
                } else {
                    ++$elseCount;
                }
                continue;
            }
        }

        if ($ternaryCount > 1 && $ternaryCount === $elseCount && $ternaryCount > $shortTernaryCount) {
            $message = 'The left-associativity of the ternary operator has been deprecated in PHP 7.4';
            $isError = false;
            if ($this->supportsAbove('8.0') === true) {
                $message .= ' and removed in PHP 8.0';
                $isError  = true;
            }

            $message .= '. Multiple consecutive ternaries detected. Use parenthesis to clarify the order in which the operations should be executed';

            $this->addMessage($phpcsFile, $message, $stackPtr, $isError);
        }
    }
}
