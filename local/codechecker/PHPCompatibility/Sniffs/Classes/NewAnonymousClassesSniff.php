<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Classes;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Anonymous classes are supported since PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/language.oop5.anonymous.php
 * @link https://wiki.php.net/rfc/anonymous_classes
 *
 * @since 7.0.0
 */
class NewAnonymousClassesSniff extends Sniff
{

    /**
     * Tokens which in various PHP versions indicate the `class` keyword.
     *
     * The dedicated anonymous class token is added from the `register()`
     * method if the token is available.
     *
     * @since 7.1.2
     *
     * @var array
     */
    private $indicators = array(
        \T_CLASS => \T_CLASS,
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array
     */
    public function register()
    {
        if (\defined('T_ANON_CLASS')) {
            $this->indicators[\T_ANON_CLASS] = \T_ANON_CLASS;
        }

        return array(\T_NEW);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.6') === false) {
            return;
        }

        $tokens       = $phpcsFile->getTokens();
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true, null, true);
        if ($nextNonEmpty === false || isset($this->indicators[$tokens[$nextNonEmpty]['code']]) === false) {
            return;
        }

        // Still here ? In that case, it is an anonymous class.
        $phpcsFile->addError(
            'Anonymous classes are not supported in PHP 5.6 or earlier',
            $stackPtr,
            'Found'
        );
    }
}
