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
 * Using the spread operator for unpacking arrays in array expressions is available since PHP 7.4.
 *
 * PHP version 7.4
 *
 * @link https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.core.unpack-inside-array
 * @link https://wiki.php.net/rfc/spread_operator_for_array
 *
 * @since 9.2.0
 */
class NewArrayUnpackingSniff extends Sniff
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
            \T_ARRAY,
            \T_OPEN_SHORT_ARRAY,
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
        if ($this->supportsBelow('7.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        /*
         * Determine the array opener & closer.
         */
        $closer = $phpcsFile->numTokens;
        if ($tokens[$stackPtr]['code'] === \T_ARRAY) {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === false) {
                return;
            }

            $opener = $tokens[$stackPtr]['parenthesis_opener'];

            if (isset($tokens[$opener]['parenthesis_closer'])) {
                $closer = $tokens[$opener]['parenthesis_closer'];
            }
        } else {
            // Short array syntax.
            $opener = $stackPtr;

            if (isset($tokens[$stackPtr]['bracket_closer'])) {
                $closer = $tokens[$stackPtr]['bracket_closer'];
            }
        }

        $nestingLevel = 0;
        if (isset($tokens[($opener + 1)]['nested_parenthesis'])) {
            $nestingLevel = count($tokens[($opener + 1)]['nested_parenthesis']);
        }

        for ($i = $opener; $i < $closer;) {
            $i = $phpcsFile->findNext(array(\T_ELLIPSIS, \T_OPEN_SHORT_ARRAY, \T_ARRAY), ($i + 1), $closer);
            if ($i === false) {
                return;
            }

            if ($tokens[$i]['code'] === \T_OPEN_SHORT_ARRAY) {
                if (isset($tokens[$i]['bracket_closer']) === false) {
                    // Live coding, unfinished nested array, handle this when the array opener
                    // of the nested array is passed.
                    return;
                }

                // Skip over nested short arrays. These will be handled when the array opener
                // of the nested array is passed.
                $i = $tokens[$i]['bracket_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === \T_ARRAY) {
                if (isset($tokens[$i]['parenthesis_closer']) === false) {
                    // Live coding, unfinished nested array, handle this when the array opener
                    // of the nested array is passed.
                    return;
                }

                // Skip over nested long arrays. These will be handled when the array opener
                // of the nested array is passed.
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            // Ensure this is not function call variable unpacking.
            if (isset($tokens[$i]['nested_parenthesis'])
                && count($tokens[$i]['nested_parenthesis']) > $nestingLevel
            ) {
                continue;
            }

            // Ok, found one.
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
            $snippet      = trim($phpcsFile->getTokensAsString($i, (($nextNonEmpty - $i) + 1)));
            $phpcsFile->addError(
                'Array unpacking within array declarations using the spread operator is not supported in PHP 7.3 or earlier. Found: %s',
                $i,
                'Found',
                array($snippet)
            );
        }
    }
}
