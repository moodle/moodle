<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\InitialValue;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect declaration of constants using `define()` with a (constant) array value
 * as supported since PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.define-array
 * @link https://www.php.net/manual/en/language.constants.syntax.php
 *
 * @since 7.0.0
 * @since 9.0.0 Renamed from `ConstantArraysUsingDefineSniff` to `NewConstantArraysUsingDefineSniff`.
 */
class NewConstantArraysUsingDefineSniff extends Sniff
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
        return array(\T_STRING);
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
        if ($this->supportsBelow('5.6') !== true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $ignore = array(
            \T_DOUBLE_COLON    => true,
            \T_OBJECT_OPERATOR => true,
            \T_FUNCTION        => true,
            \T_CONST           => true,
        );

        $prevToken = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($ignore[$tokens[$prevToken]['code']]) === true) {
            // Not a call to a PHP function.
            return;
        }

        $functionLc = strtolower($tokens[$stackPtr]['content']);
        if ($functionLc !== 'define') {
            return;
        }

        $secondParam = $this->getFunctionCallParameter($phpcsFile, $stackPtr, 2);
        if (isset($secondParam['start'], $secondParam['end']) === false) {
            return;
        }

        $targetNestingLevel = 0;
        if (isset($tokens[$secondParam['start']]['nested_parenthesis'])) {
            $targetNestingLevel = \count($tokens[$secondParam['start']]['nested_parenthesis']);
        }

        $array = $phpcsFile->findNext(array(\T_ARRAY, \T_OPEN_SHORT_ARRAY), $secondParam['start'], ($secondParam['end'] + 1));
        if ($array !== false) {
            if ((isset($tokens[$array]['nested_parenthesis']) === false && $targetNestingLevel === 0) || \count($tokens[$array]['nested_parenthesis']) === $targetNestingLevel) {
                $phpcsFile->addError(
                    'Constant arrays using define are not allowed in PHP 5.6 or earlier',
                    $array,
                    'Found'
                );
            }
        }
    }
}
