<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Keywords;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect usage of `self`, `parent` and `static` and verify they are lowercase.
 *
 * Prior to PHP 5.5, cases existed where the `self`, `parent`, and `static` keywords
 * were treated in a case sensitive fashion.
 *
 * PHP version 5.5
 *
 * @link https://www.php.net/manual/en/migration55.incompatible.php#migration55.incompatible.self-parent-static
 *
 * @since 7.1.4
 */
class CaseSensitiveKeywordsSniff extends Sniff
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
            \T_SELF,
            \T_STATIC,
            \T_PARENT,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.4') === false) {
            return;
        }

        $tokens         = $phpcsFile->getTokens();
        $tokenContentLC = strtolower($tokens[$stackPtr]['content']);

        if ($tokenContentLC !== $tokens[$stackPtr]['content']) {
            $phpcsFile->addError(
                'The keyword \'%s\' was treated in a case-sensitive fashion in certain cases in PHP 5.4 or earlier. Use the lowercase version for consistent support.',
                $stackPtr,
                'NonLowercaseFound',
                array($tokenContentLC)
            );
        }
    }
}
