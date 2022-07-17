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

/**
 * Detect use of short array syntax which is available since PHP 5.4.
 *
 * PHP version 5.4
 *
 * @link https://wiki.php.net/rfc/shortsyntaxforarrays
 * @link https://www.php.net/manual/en/language.types.array.php#language.types.array.syntax
 *
 * @since 7.0.0
 * @since 9.0.0 Renamed from `ShortArraySniff` to `NewShortArraySniff`.
 */
class NewShortArraySniff extends Sniff
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
        return array(
            \T_OPEN_SHORT_ARRAY,
            \T_CLOSE_SHORT_ARRAY,
        );
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
        if ($this->supportsBelow('5.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        $error = '%s is not supported in PHP 5.3 or lower';
        $data  = array();

        if ($token['type'] === 'T_OPEN_SHORT_ARRAY') {
            $data[] = 'Short array syntax (open)';
        } elseif ($token['type'] === 'T_CLOSE_SHORT_ARRAY') {
            $data[] = 'Short array syntax (close)';
        }

        $phpcsFile->addError($error, $stackPtr, 'Found', $data);
    }
}
