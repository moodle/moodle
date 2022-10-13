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
 * Detect array and string literal dereferencing.
 *
 * As of PHP 5.5, array and string literals can now be dereferenced directly to
 * access individual elements and characters.
 *
 * As of PHP 7.0, this also works when using curly braces for the dereferencing.
 * While unclear, this most likely has to do with the Uniform Variable Syntax changes.
 *
 * PHP version 5.5
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration55.new-features.php#migration55.new-features.const-dereferencing
 * @link https://wiki.php.net/rfc/constdereference
 * @link https://wiki.php.net/rfc/uniform_variable_syntax
 * @link https://www.php.net/manual/en/language.types.array.php#example-63
 *
 * {@internal The reason for splitting the logic of this sniff into different methods is
 *            to allow re-use of the logic by the PHP 7.4 `RemovedCurlyBraceArrayAccess` sniff.}
 *
 * @since 7.1.4
 * @since 9.3.0 Now also detects dereferencing using curly braces.
 */
class NewArrayStringDereferencingSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.1.4
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_ARRAY,
            \T_OPEN_SHORT_ARRAY,
            \T_CONSTANT_ENCAPSED_STRING,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.1.4
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

        $dereferencing = $this->isArrayStringDereferencing($phpcsFile, $stackPtr);
        if (empty($dereferencing)) {
            return;
        }

        $tokens     = $phpcsFile->getTokens();
        $supports54 = $this->supportsBelow('5.4');

        foreach ($dereferencing['braces'] as $openBrace => $closeBrace) {
            if ($supports54 === true
                && ($tokens[$openBrace]['type'] === 'T_OPEN_SQUARE_BRACKET'
                    || $tokens[$openBrace]['type'] === 'T_OPEN_SHORT_ARRAY') // Work around bug #1381 in PHPCS 2.8.1 and lower.
            ) {
                $phpcsFile->addError(
                    'Direct array dereferencing of %s is not present in PHP version 5.4 or earlier',
                    $openBrace,
                    'Found',
                    array($dereferencing['type'])
                );

                continue;
            }

            // PHP 7.0 Array/string dereferencing using curly braces.
            if ($tokens[$openBrace]['type'] === 'T_OPEN_CURLY_BRACKET') {
                $phpcsFile->addError(
                    'Direct array dereferencing of %s using curly braces is not present in PHP version 5.6 or earlier',
                    $openBrace,
                    'FoundUsingCurlies',
                    array($dereferencing['type'])
                );
            }
        }
    }


    /**
     * Check if this string/array is being dereferenced.
     *
     * @since 9.3.0 Logic split off from the process method.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return array Array containing the type of access and stack pointers to the
     *               open/close braces involved in the array/string dereferencing;
     *               or an empty array if no array/string dereferencing was detected.
     */
    public function isArrayStringDereferencing(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['code']) {
            case \T_CONSTANT_ENCAPSED_STRING:
                $type = 'string literals';
                $end  = $stackPtr;
                break;

            case \T_ARRAY:
                if (isset($tokens[$stackPtr]['parenthesis_closer']) === false) {
                    // Live coding.
                    return array();
                } else {
                    $type = 'arrays';
                    $end  = $tokens[$stackPtr]['parenthesis_closer'];
                }
                break;

            case \T_OPEN_SHORT_ARRAY:
                if (isset($tokens[$stackPtr]['bracket_closer']) === false) {
                    // Live coding.
                    return array();
                } else {
                    $type = 'arrays';
                    $end  = $tokens[$stackPtr]['bracket_closer'];
                }
                break;
        }

        if (isset($type, $end) === false) {
            // Shouldn't happen, but for some reason did.
            return array();
        }

        $braces = array();

        do {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true, null, true);
            if ($nextNonEmpty === false) {
                break;
            }

            if ($tokens[$nextNonEmpty]['type'] === 'T_OPEN_SQUARE_BRACKET'
                || $tokens[$nextNonEmpty]['type'] === 'T_OPEN_CURLY_BRACKET' // PHP 7.0+.
                || $tokens[$nextNonEmpty]['type'] === 'T_OPEN_SHORT_ARRAY' // Work around bug #1381 in PHPCS 2.8.1 and lower.
            ) {
                if (isset($tokens[$nextNonEmpty]['bracket_closer']) === false) {
                    // Live coding or parse error.
                    break;
                }

                $braces[$nextNonEmpty] = $tokens[$nextNonEmpty]['bracket_closer'];

                // Continue, just in case there is nested array access, i.e. `array(1, 2, 3)[$i][$j];`.
                $end = $tokens[$nextNonEmpty]['bracket_closer'];
                continue;
            }

            // If we're still here, we've reached the end of the variable.
            break;

        } while (true);

        if (empty($braces)) {
            return array();
        }

        return array(
            'type'   => $type,
            'braces' => $braces,
        );
    }
}
