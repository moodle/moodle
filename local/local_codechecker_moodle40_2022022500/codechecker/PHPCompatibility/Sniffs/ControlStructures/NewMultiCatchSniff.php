<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ControlStructures;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Catching multiple exception types in one statement is available since PHP 7.1.
 *
 * PHP version 7.1
 *
 * @link https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.mulit-catch-exception-handling
 * @link https://wiki.php.net/rfc/multiple-catch
 * @link https://www.php.net/manual/en/language.exceptions.php#language.exceptions.catch
 *
 * @since 7.0.7
 */
class NewMultiCatchSniff extends Sniff
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
        return array(\T_CATCH);
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

        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        // Bow out during live coding.
        if (isset($token['parenthesis_opener'], $token['parenthesis_closer']) === false) {
            return;
        }

        $hasBitwiseOr = $phpcsFile->findNext(\T_BITWISE_OR, $token['parenthesis_opener'], $token['parenthesis_closer']);

        if ($hasBitwiseOr === false) {
            return;
        }

        $phpcsFile->addError(
            'Catching multiple exceptions within one statement is not supported in PHP 7.0 or earlier.',
            $hasBitwiseOr,
            'Found'
        );
    }
}
