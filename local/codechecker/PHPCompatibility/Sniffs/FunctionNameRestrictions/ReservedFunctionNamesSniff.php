<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionNameRestrictions;

use Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff as PHPCS_CamelCapsFunctionNameSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Standards_AbstractScopeSniff as PHPCS_AbstractScopeSniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * All function and method names starting with double underscore are reserved by PHP.
 *
 * PHP version All
 *
 * {@internal Extends an upstream sniff to benefit from the properties contained therein.
 *            The properties are lists of valid PHP magic function and method names, which
 *            should be ignored for the purposes of this sniff.
 *            As this sniff is not PHP version specific, we don't need access to the utility
 *            methods in the PHPCompatibility\Sniff, so extending the upstream sniff is fine.
 *            As the upstream sniff checks the same (and more, but we don't need the rest),
 *            the logic in this sniff is largely the same as used upstream.
 *            Extending the upstream sniff instead of including it via the ruleset, however,
 *            prevents hard to debug issues of errors not being reported from the upstream sniff
 *            if this library is used in combination with other rulesets.}
 *
 * @link https://www.php.net/manual/en/language.oop5.magic.php
 *
 * @since 8.2.0 This was previously, since 7.0.3, checked by the upstream sniff.
 * @since 9.3.2 The sniff will now ignore functions marked as `@deprecated` by design.
 */
class ReservedFunctionNamesSniff extends PHPCS_CamelCapsFunctionNameSniff
{

    /**
     * Overload the constructor to work round various PHPCS cross-version compatibility issues.
     *
     * @since 8.2.0
     */
    public function __construct()
    {
        $scopeTokens = array(\T_CLASS, \T_INTERFACE, \T_TRAIT);
        if (\defined('T_ANON_CLASS')) {
            $scopeTokens[] = \T_ANON_CLASS;
        }

        // Call the grand-parent constructor directly.
        PHPCS_AbstractScopeSniff::__construct($scopeTokens, array(\T_FUNCTION), true);

        // Make sure debuginfo is included in the array. Upstream only includes it since 2.5.1.
        $this->magicMethods['debuginfo'] = true;
    }


    /**
     * Processes the tokens within the scope.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                   $stackPtr  The position where this token was
     *                                         found.
     * @param int                   $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        /*
         * Determine if this is a function which needs to be examined.
         * The `processTokenWithinScope()` is called for each valid scope a method is in,
         * so for nested classes, we need to make sure we only examine the token for
         * the lowest level valid scope.
         */
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        $deepestScope = key($conditions);
        if ($deepestScope !== $currScope) {
            return;
        }

        if ($this->isFunctionDeprecated($phpcsFile, $stackPtr) === true) {
            /*
             * Deprecated functions don't have to comply with the naming conventions,
             * otherwise functions deprecated in favour of a function with a compliant
             * name would still trigger an error.
             */
            return;
        }

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        // Is this a magic method. i.e., is prefixed with "__" ?
        if (preg_match('|^__[^_]|', $methodName) > 0) {
            $magicPart = strtolower(substr($methodName, 2));
            if (isset($this->magicMethods[$magicPart]) === false
                && isset($this->methodsDoubleUnderscore[$magicPart]) === false
            ) {
                $className         = '[anonymous class]';
                $scopeNextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($currScope + 1), null, true);
                if ($scopeNextNonEmpty !== false && $tokens[$scopeNextNonEmpty]['code'] === \T_STRING) {
                    $className = $tokens[$scopeNextNonEmpty]['content'];
                }

                $phpcsFile->addWarning(
                    'Method name "%s" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.',
                    $stackPtr,
                    'MethodDoubleUnderscore',
                    array($className . '::' . $methodName)
                );
            }
        }
    }


    /**
     * Processes the tokens outside the scope.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                   $stackPtr  The position where this token was
     *                                         found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        if ($this->isFunctionDeprecated($phpcsFile, $stackPtr) === true) {
            /*
             * Deprecated functions don't have to comply with the naming conventions,
             * otherwise functions deprecated in favour of a function with a compliant
             * name would still trigger an error.
             */
            return;
        }

        $functionName = $phpcsFile->getDeclarationName($stackPtr);
        if ($functionName === null) {
            // Ignore closures.
            return;
        }

        // Is this a magic function. i.e., it is prefixed with "__".
        if (preg_match('|^__[^_]|', $functionName) > 0) {
            $magicPart = strtolower(substr($functionName, 2));
            if (isset($this->magicFunctions[$magicPart]) === false) {
                $phpcsFile->addWarning(
                    'Function name "%s" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.',
                    $stackPtr,
                    'FunctionDoubleUnderscore',
                    array($functionName)
                );
            }
        }
    }


    /**
     * Check whether a function has been marked as deprecated via a @deprecated tag
     * in the function docblock.
     *
     * @since 9.3.2
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of a T_FUNCTION
     *                                               token in the stack.
     *
     * @return bool
     */
    private function isFunctionDeprecated(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $find   = Tokens::$methodPrefixes;
        $find[] = \T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== \T_DOC_COMMENT_CLOSE_TAG) {
            // Function doesn't have a doc comment or is using the wrong type of comment.
            return false;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@deprecated') {
                return true;
            }
        }

        return false;
    }
}
