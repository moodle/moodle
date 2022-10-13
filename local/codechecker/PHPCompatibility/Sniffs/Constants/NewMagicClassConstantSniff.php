<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Constants;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect usage of the magic `::class` constant introduced in PHP 5.5.
 *
 * The special `ClassName::class` constant is available as of PHP 5.5.0, and allows
 * for fully qualified class name resolution at compile time.
 *
 * PHP version 5.5
 *
 * @link https://wiki.php.net/rfc/class_name_scalars
 * @link https://www.php.net/manual/en/language.oop5.constants.php#example-186
 *
 * @since 7.1.4
 * @since 7.1.5 Removed the incorrect checks against invalid usage of the constant.
 */
class NewMagicClassConstantSniff extends Sniff
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
        return array(\T_STRING);
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

        $tokens = $phpcsFile->getTokens();

        if (strtolower($tokens[$stackPtr]['content']) !== 'class') {
            return;
        }

        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true, null, true);
        if ($prevToken === false || $tokens[$prevToken]['code'] !== \T_DOUBLE_COLON) {
            return;
        }

        $phpcsFile->addError(
            'The magic class constant ClassName::class was not available in PHP 5.4 or earlier',
            $stackPtr,
            'Found'
        );
    }
}
