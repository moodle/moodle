<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\LanguageConstructs;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Verify that nothing but variables are passed to empty().
 *
 * Prior to PHP 5.5, `empty()` only supported variables; anything else resulted in a parse error.
 *
 * PHP version 5.5
 *
 * @link https://wiki.php.net/rfc/empty_isset_exprs
 * @link https://www.php.net/manual/en/function.empty.php
 *
 * @since 7.0.4
 * @since 9.0.0 The "is the parameter a variable" determination has been abstracted out
 *              and moved to a separate method `Sniff::isVariable()`.
 * @since 9.0.0 Renamed from `EmptyNonVariableSniff` to `NewEmptyNonVariableSniff`.
 */
class NewEmptyNonVariableSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.4
     *
     * @return array
     */
    public function register()
    {
        return array(\T_EMPTY);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.4
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

        $open = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
        if ($open === false
            || $tokens[$open]['code'] !== \T_OPEN_PARENTHESIS
            || isset($tokens[$open]['parenthesis_closer']) === false
        ) {
            return;
        }

        $close = $tokens[$open]['parenthesis_closer'];

        $nestingLevel = 0;
        if ($close !== ($open + 1) && isset($tokens[$open + 1]['nested_parenthesis'])) {
            $nestingLevel = \count($tokens[$open + 1]['nested_parenthesis']);
        }

        if ($this->isVariable($phpcsFile, ($open + 1), $close, $nestingLevel) === true) {
            return;
        }

        $phpcsFile->addError(
            'Only variables can be passed to empty() prior to PHP 5.5.',
            $stackPtr,
            'Found'
        );
    }
}
