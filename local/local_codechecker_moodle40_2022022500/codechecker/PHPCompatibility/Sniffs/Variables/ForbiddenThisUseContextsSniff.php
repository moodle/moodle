<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Variables;

use PHPCompatibility\Sniff;
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect using `$this` in incompatible contexts.
 *
 * "Whilst `$this` is considered a special variable in PHP, it lacked proper checks
 *  to ensure it wasn't used as a variable name or reassigned. This has now been
 *  rectified to ensure that `$this` cannot be a user-defined variable, reassigned
 *  to a different value, or be globalised."
 *
 * This sniff only addresses those situations which did *not* throw an error prior
 * to PHP 7.1, either at all or only in PHP 7.0.
 * In other words, the following situation, while mentioned in the RFC, will NOT
 * be sniffed for:
 * - Using $this as static variable. (error _message_ change only).
 *
 * Also, the changes with relation to assigning `$this` dynamically can not be
 * sniffed for reliably, so are not covered by this sniff.
 * - Disable ability to re-assign `$this` indirectly through `$$`.
 * - Disable ability to re-assign `$this` indirectly through reference.
 * - Disable ability to re-assign `$this` indirectly through `extract()` and `parse_str()`.
 *
 * Other changes not (yet) covered:
 * - `get_defined_vars()` always doesn't show value of variable `$this`.
 * - Always show true `$this` value in magic method `__call()`.
 *   {@internal This could possibly be covered. Similar logic as "outside object context",
 *   but with function name check and supportsBelow('7.0').}
 *
 * PHP version 7.1
 *
 * @link https://www.php.net/manual/en/migration71.other-changes.php#migration71.other-changes.inconsistency-fixes-to-this
 * @link https://wiki.php.net/rfc/this_var
 *
 * @since 9.1.0
 */
class ForbiddenThisUseContextsSniff extends Sniff
{

    /**
     * OO scope tokens.
     *
     * Duplicate of Tokens::$ooScopeTokens array in PHPCS which was added in 3.1.0.
     *
     * @since 9.1.0
     *
     * @var array
     */
    private $ooScopeTokens = array(
        'T_CLASS'     => \T_CLASS,
        'T_INTERFACE' => \T_INTERFACE,
        'T_TRAIT'     => \T_TRAIT,
    );

    /**
     * Scopes to skip over when examining the contents of functions.
     *
     * @since 9.1.0
     *
     * @var array
     */
    private $skipOverScopes = array(
        'T_FUNCTION' => true,
        'T_CLOSURE'  => true,
    );

    /**
     * Valid uses of $this in plain functions or methods outside object context.
     *
     * @since 9.1.0
     *
     * @var array
     */
    private $validUseOutsideObject = array(
        \T_ISSET => true,
        \T_EMPTY => true,
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.1.0
     *
     * @return array
     */
    public function register()
    {
        if (\defined('T_ANON_CLASS')) {
            $this->ooScopeTokens['T_ANON_CLASS'] = \T_ANON_CLASS;
        }

        $this->skipOverScopes += $this->ooScopeTokens;

        return array(
            \T_FUNCTION,
            \T_CLOSURE,
            \T_GLOBAL,
            \T_CATCH,
            \T_FOREACH,
            \T_UNSET,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.1') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['code']) {
            case \T_FUNCTION:
                $this->isThisUsedAsParameter($phpcsFile, $stackPtr);
                $this->isThisUsedOutsideObjectContext($phpcsFile, $stackPtr);
                break;

            case \T_CLOSURE:
                $this->isThisUsedAsParameter($phpcsFile, $stackPtr);
                break;

            case \T_GLOBAL:
                /*
                 * $this can no longer be imported using the `global` keyword.
                 * This worked in PHP 7.0, though in PHP 5.x, it would throw a
                 * fatal "Cannot re-assign $this" error.
                 */
                $endOfStatement = $phpcsFile->findNext(array(\T_SEMICOLON, \T_CLOSE_TAG), ($stackPtr + 1));
                if ($endOfStatement === false) {
                    // No semi-colon - live coding.
                    return;
                }

                for ($i = ($stackPtr + 1); $i < $endOfStatement; $i++) {
                    if ($tokens[$i]['code'] !== \T_VARIABLE || $tokens[$i]['content'] !== '$this') {
                        continue;
                    }

                    $phpcsFile->addError(
                        '"$this" can no longer be used with the "global" keyword since PHP 7.1.',
                        $i,
                        'Global'
                    );
                }

                break;

            case \T_CATCH:
                /*
                 * $this can no longer be used as a catch variable.
                 */
                if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === false) {
                    return;
                }

                $varPtr = $phpcsFile->findNext(
                    \T_VARIABLE,
                    ($tokens[$stackPtr]['parenthesis_opener'] + 1),
                    $tokens[$stackPtr]['parenthesis_closer']
                );

                if ($varPtr === false || $tokens[$varPtr]['content'] !== '$this') {
                    return;
                }

                $phpcsFile->addError(
                    '"$this" can no longer be used as a catch variable since PHP 7.1.',
                    $varPtr,
                    'Catch'
                );

                break;

            case \T_FOREACH:
                /*
                 * $this can no longer be used as a foreach *value* variable.
                 * This worked in PHP 7.0, though in PHP 5.x, it would throw a
                 * fatal "Cannot re-assign $this" error.
                 */
                if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === false) {
                    return;
                }

                $stopPtr = $phpcsFile->findPrevious(
                    array(\T_AS, \T_DOUBLE_ARROW),
                    ($tokens[$stackPtr]['parenthesis_closer'] - 1),
                    $tokens[$stackPtr]['parenthesis_opener']
                );
                if ($stopPtr === false) {
                    return;
                }

                $valueVarPtr = $phpcsFile->findNext(
                    \T_VARIABLE,
                    ($stopPtr + 1),
                    $tokens[$stackPtr]['parenthesis_closer']
                );
                if ($valueVarPtr === false || $tokens[$valueVarPtr]['content'] !== '$this') {
                    return;
                }

                $afterThis = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($valueVarPtr + 1),
                    $tokens[$stackPtr]['parenthesis_closer'],
                    true
                );

                if ($afterThis !== false
                    && ($tokens[$afterThis]['code'] === \T_OBJECT_OPERATOR
                        || $tokens[$afterThis]['code'] === \T_DOUBLE_COLON)
                ) {
                    return;
                }

                $phpcsFile->addError(
                    '"$this" can no longer be used as value variable in a foreach control structure since PHP 7.1.',
                    $valueVarPtr,
                    'ForeachValueVar'
                );

                break;

            case \T_UNSET:
                /*
                 * $this can no longer be unset.
                 */
                $openParenthesis = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
                if ($openParenthesis === false
                    || $tokens[$openParenthesis]['code'] !== \T_OPEN_PARENTHESIS
                    || isset($tokens[$openParenthesis]['parenthesis_closer']) === false
                ) {
                    return;
                }

                for ($i = ($openParenthesis + 1); $i < $tokens[$openParenthesis]['parenthesis_closer']; $i++) {
                    if ($tokens[$i]['code'] !== \T_VARIABLE || $tokens[$i]['content'] !== '$this') {
                        continue;
                    }

                    $afterThis = $phpcsFile->findNext(
                        Tokens::$emptyTokens,
                        ($i + 1),
                        $tokens[$openParenthesis]['parenthesis_closer'],
                        true
                    );

                    if ($afterThis !== false
                        && ($tokens[$afterThis]['code'] === \T_OBJECT_OPERATOR
                            || $tokens[$afterThis]['code'] === \T_DOUBLE_COLON
                            || $tokens[$afterThis]['code'] === \T_OPEN_SQUARE_BRACKET)
                    ) {
                        $i = $afterThis;
                        continue;
                    }

                    $phpcsFile->addError(
                        '"$this" can no longer be unset since PHP 7.1.',
                        $i,
                        'Unset'
                    );
                }

                break;
        }
    }

    /**
     * Check if $this is used as a parameter in a function declaration.
     *
     * $this can no longer be used as a parameter in a *global* function.
     * Use as a parameter in a method was already an error prior to PHP 7.1.
     *
     * @since 9.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    protected function isThisUsedAsParameter(File $phpcsFile, $stackPtr)
    {
        if ($this->validDirectScope($phpcsFile, $stackPtr, $this->ooScopeTokens) !== false) {
            return;
        }

        $params = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);
        if (empty($params)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        foreach ($params as $param) {
            if ($param['name'] !== '$this') {
                continue;
            }

            if ($tokens[$stackPtr]['code'] === \T_FUNCTION) {
                $phpcsFile->addError(
                    '"$this" can no longer be used as a parameter since PHP 7.1.',
                    $param['token'],
                    'FunctionParam'
                );
            } else {
                $phpcsFile->addError(
                    '"$this" can no longer be used as a closure parameter since PHP 7.0.7.',
                    $param['token'],
                    'ClosureParam'
                );
            }
        }
    }

    /**
     * Check if $this is used in a plain function or method.
     *
     * Prior to PHP 7.1, this would result in an "undefined variable" notice
     * and execution would continue with $this regarded as `null`.
     * As of PHP 7.1, this throws an exception.
     *
     * Note: use within isset() and empty() to check object context is still allowed.
     * Note: $this can still be used within a closure.
     *
     * @since 9.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    protected function isThisUsedOutsideObjectContext(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        if ($this->validDirectScope($phpcsFile, $stackPtr, $this->ooScopeTokens) !== false) {
            $methodProps = $phpcsFile->getMethodProperties($stackPtr);
            if ($methodProps['is_static'] === false) {
                return;
            } else {
                $methodName = $phpcsFile->getDeclarationName($stackPtr);
                if ($methodName === '__call') {
                    /*
                     * This is an exception.
                     * @link https://wiki.php.net/rfc/this_var#always_show_true_this_value_in_magic_method_call
                     */
                    return;
                }
            }
        }

        for ($i = ($tokens[$stackPtr]['scope_opener'] + 1); $i < $tokens[$stackPtr]['scope_closer']; $i++) {
            if (isset($this->skipOverScopes[$tokens[$i]['type']])) {
                if (isset($tokens[$i]['scope_closer']) === false) {
                    // Live coding or parse error, will only lead to inaccurate results.
                    return;
                }

                // Skip over nested structures.
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== \T_VARIABLE || $tokens[$i]['content'] !== '$this') {
                continue;
            }

            if (isset($tokens[$i]['nested_parenthesis']) === true) {
                $nestedParenthesis     = $tokens[$i]['nested_parenthesis'];
                $nestedOpenParenthesis = array_keys($nestedParenthesis);
                $lastOpenParenthesis   = array_pop($nestedOpenParenthesis);

                $previousNonEmpty = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    ($lastOpenParenthesis - 1),
                    null,
                    true,
                    null,
                    true
                );

                if (isset($this->validUseOutsideObject[$tokens[$previousNonEmpty]['code']])) {
                    continue;
                }
            }

            $phpcsFile->addError(
                '"$this" can no longer be used in a plain function or method since PHP 7.1.',
                $i,
                'OutsideObjectContext'
            );
        }
    }
}
