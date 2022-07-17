<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionUse;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect usage of `func_get_args()`, `func_get_arg()` and `func_num_args()` in invalid context.
 *
 * Checks for:
 * - Prior to PHP 5.3, these functions could not be used as a function call parameter.
 * - Calling these functions from the outermost scope of a file which has been included by
 *   calling `include` or `require` from within a function in the calling file, worked
 *   prior to PHP 5.3. As of PHP 5.3, this will generate a warning and will always return false/-1.
 *   If the file was called directly or included in the global scope, calls to these
 *   functions would already generate a warning prior to PHP 5.3.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/migration53.incompatible.php
 *
 * @since 8.2.0
 */
class ArgumentFunctionsUsageSniff extends Sniff
{

    /**
     * The target functions for this sniff.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'func_get_args' => true,
        'func_get_arg'  => true,
        'func_num_args' => true,
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 8.2.0
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
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens     = $phpcsFile->getTokens();
        $functionLc = strtolower($tokens[$stackPtr]['content']);
        if (isset($this->targetFunctions[$functionLc]) === false) {
            return;
        }

        // Next non-empty token should be the open parenthesis.
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
        if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_OPEN_PARENTHESIS) {
            return;
        }

        $ignore = array(
            \T_DOUBLE_COLON    => true,
            \T_OBJECT_OPERATOR => true,
            \T_FUNCTION        => true,
            \T_NEW             => true,
        );

        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset($ignore[$tokens[$prevNonEmpty]['code']]) === true) {
            // Not a call to a PHP function.
            return;
        } elseif ($tokens[$prevNonEmpty]['code'] === \T_NS_SEPARATOR && $tokens[$prevNonEmpty - 1]['code'] === \T_STRING) {
            // Namespaced function.
            return;
        }

        $data = $tokens[$stackPtr]['content'];

        /*
         * Check for use of the functions in the global scope.
         *
         * As PHPCS can not determine whether a file is included from within a function in
         * another file, so always throw a warning/error.
         */
        if ($phpcsFile->hasCondition($stackPtr, array(\T_FUNCTION, \T_CLOSURE)) === false) {
            $isError = false;
            $message = 'Use of %s() outside of a user-defined function is only supported if the file is included from within a user-defined function in another file prior to PHP 5.3.';

            if ($this->supportsAbove('5.3') === true) {
                $isError  = true;
                $message .= ' As of PHP 5.3, it is no longer supported at all.';
            }

            $this->addMessage($phpcsFile, $message, $stackPtr, $isError, 'OutsideFunctionScope', $data);
        }

        /*
         * Check for use of the functions as a parameter in a function call.
         */
        if ($this->supportsBelow('5.2') === false) {
            return;
        }

        if (isset($tokens[$stackPtr]['nested_parenthesis']) === false) {
            return;
        }

        $throwError = false;

        $closer = end($tokens[$stackPtr]['nested_parenthesis']);
        if (isset($tokens[$closer]['parenthesis_owner'])
            && $tokens[$tokens[$closer]['parenthesis_owner']]['type'] === 'T_CLOSURE'
        ) {
            $throwError = true;
        } else {
            $opener       = key($tokens[$stackPtr]['nested_parenthesis']);
            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);
            if ($tokens[$prevNonEmpty]['code'] !== \T_STRING) {
                return;
            }

            $prevPrevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevNonEmpty - 1), null, true);
            if ($tokens[$prevPrevNonEmpty]['code'] === \T_FUNCTION) {
                return;
            }

            $throwError = true;
        }

        if ($throwError === false) {
            return;
        }

        $phpcsFile->addError(
            '%s() could not be used in parameter lists prior to PHP 5.3.',
            $stackPtr,
            'InParameterList',
            $data
        );
    }
}
