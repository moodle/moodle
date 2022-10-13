<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionDeclarations;

use PHPCompatibility\Sniff;
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect variable names forbidden to be used in closure `use` statements.
 *
 * Variables bound to a closure via the `use` construct cannot use the same name
 * as any superglobals, `$this`, or any parameter since PHP 7.1.
 *
 * PHP version 7.1
 *
 * @link https://www.php.net/manual/en/migration71.incompatible.php#migration71.incompatible.lexical-names
 * @link https://www.php.net/manual/en/functions.anonymous.php
 *
 * @since 7.1.4
 */
class ForbiddenVariableNamesInClosureUseSniff extends Sniff
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
        return array(\T_USE);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.1') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Verify this use statement is used with a closure - if so, it has to have parenthesis before it.
        $previousNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true, null, true);
        if ($previousNonEmpty === false || $tokens[$previousNonEmpty]['code'] !== \T_CLOSE_PARENTHESIS
            || isset($tokens[$previousNonEmpty]['parenthesis_opener']) === false
        ) {
            return;
        }

        // ... and (a variable within) parenthesis after it.
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
        if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_OPEN_PARENTHESIS) {
            return;
        }

        if (isset($tokens[$nextNonEmpty]['parenthesis_closer']) === false) {
            // Live coding.
            return;
        }

        $closurePtr = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($tokens[$previousNonEmpty]['parenthesis_opener'] - 1), null, true);
        if ($closurePtr === false || $tokens[$closurePtr]['code'] !== \T_CLOSURE) {
            return;
        }

        // Get the parameters declared by the closure.
        $closureParams = PHPCSHelper::getMethodParameters($phpcsFile, $closurePtr);

        $errorMsg = 'Variables bound to a closure via the use construct cannot use the same name as superglobals, $this, or a declared parameter since PHP 7.1. Found: %s';

        for ($i = ($nextNonEmpty + 1); $i < $tokens[$nextNonEmpty]['parenthesis_closer']; $i++) {
            if ($tokens[$i]['code'] !== \T_VARIABLE) {
                continue;
            }

            $variableName = $tokens[$i]['content'];

            if ($variableName === '$this') {
                $phpcsFile->addError($errorMsg, $i, 'FoundThis', array($variableName));
                continue;
            }

            if (isset($this->superglobals[$variableName]) === true) {
                $phpcsFile->addError($errorMsg, $i, 'FoundSuperglobal', array($variableName));
                continue;
            }

            // Check whether it is one of the parameters declared by the closure.
            if (empty($closureParams) === false) {
                foreach ($closureParams as $param) {
                    if ($param['name'] === $variableName) {
                        $phpcsFile->addError($errorMsg, $i, 'FoundShadowParam', array($variableName));
                        continue 2;
                    }
                }
            }
        }
    }
}
