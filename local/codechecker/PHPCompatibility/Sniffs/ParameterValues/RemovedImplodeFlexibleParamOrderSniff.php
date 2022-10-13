<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ParameterValues;

use PHPCompatibility\AbstractFunctionCallParameterSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Passing the `$glue` and `$pieces` parameters to `implode()` in reverse order has
 * been deprecated in PHP 7.4.
 *
 * PHP version 7.4
 *
 * @link https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.implode-reverse-parameters
 * @link https://wiki.php.net/rfc/deprecations_php_7_4#implode_parameter_order_mix
 * @link https://php.net/manual/en/function.implode.php
 *
 * @since 9.3.0
 */
class RemovedImplodeFlexibleParamOrderSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'implode' => true,
        'join'    => true,
    );

    /**
     * List of PHP native constants which should be recognized as text strings.
     *
     * @since 9.3.0
     *
     * @var array
     */
    private $constantStrings = array(
        'DIRECTORY_SEPARATOR' => true,
        'PHP_EOL'             => true,
    );

    /**
     * List of PHP native functions which should be recognized as returning an array.
     *
     * Note: The array_*() functions will always be taken into account.
     *
     * @since 9.3.0
     *
     * @var array
     */
    private $arrayFunctions = array(
        'compact' => true,
        'explode' => true,
        'range'   => true,
    );

    /**
     * List of PHP native array functions which should *not* be recognized as returning an array.
     *
     * @since 9.3.0
     *
     * @var array
     */
    private $arrayFunctionExceptions = array(
        'array_key_exists'     => true,
        'array_key_first'      => true,
        'array_key_last'       => true,
        'array_multisort'      => true,
        'array_pop'            => true,
        'array_product'        => true,
        'array_push'           => true,
        'array_search'         => true,
        'array_shift'          => true,
        'array_sum'            => true,
        'array_unshift'        => true,
        'array_walk_recursive' => true,
        'array_walk'           => true,
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsAbove('7.4') === false);
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the stack.
     * @param string                $functionName The token content (function name) which was matched.
     * @param array                 $parameters   Array with information about the parameters.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function processParameters(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        if (isset($parameters[2]) === false) {
            // Only one parameter, this must be $pieces. Bow out.
            return;
        }

        $tokens = $phpcsFile->getTokens();

        /*
         * Examine the first parameter.
         * If there is any indication that this is an array declaration, we have an error.
         */

        $targetParam = $parameters[1];
        $start       = $targetParam['start'];
        $end         = ($targetParam['end'] + 1);
        $isOnlyText  = true;

        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $start, $end, true);
        if ($firstNonEmpty === false) {
            // Parse error. Shouldn't be possible.
            return;
        }

        if ($tokens[$firstNonEmpty]['code'] === \T_OPEN_PARENTHESIS) {
            $start = ($firstNonEmpty + 1);
            $end   = $tokens[$firstNonEmpty]['parenthesis_closer'];
        }

        $hasTernary = $phpcsFile->findNext(\T_INLINE_THEN, $start, $end);
        if ($hasTernary !== false
            && isset($tokens[$start]['nested_parenthesis'], $tokens[$hasTernary]['nested_parenthesis'])
            && count($tokens[$start]['nested_parenthesis']) === count($tokens[$hasTernary]['nested_parenthesis'])
        ) {
            $start = ($hasTernary + 1);
        }

        for ($i = $start; $i < $end; $i++) {
            $tokenCode = $tokens[$i]['code'];

            if (isset(Tokens::$emptyTokens[$tokenCode])) {
                continue;
            }

            if ($tokenCode === \T_STRING && isset($this->constantStrings[$tokens[$i]['content']])) {
                continue;
            }

            if ($hasTernary !== false && $tokenCode === \T_INLINE_ELSE) {
                continue;
            }

            if (isset(Tokens::$stringTokens[$tokenCode]) === false) {
                $isOnlyText = false;
            }

            if ($tokenCode === \T_ARRAY || $tokenCode === \T_OPEN_SHORT_ARRAY || $tokenCode === \T_ARRAY_CAST) {
                $this->throwNotice($phpcsFile, $stackPtr, $functionName);
                return;
            }

            if ($tokenCode === \T_STRING) {
                /*
                 * Check for specific functions which return an array (i.e. $pieces).
                 */
                $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), $end, true);
                if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_OPEN_PARENTHESIS) {
                    continue;
                }

                $nameLc = strtolower($tokens[$i]['content']);
                if (isset($this->arrayFunctions[$nameLc]) === false
                    && (strpos($nameLc, 'array_') !== 0
                    || isset($this->arrayFunctionExceptions[$nameLc]) === true)
                ) {
                    continue;
                }

                // Now make sure it's the PHP native function being called.
                $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($i - 1), $start, true);
                if ($tokens[$prevNonEmpty]['code'] === \T_DOUBLE_COLON
                    || $tokens[$prevNonEmpty]['code'] === \T_OBJECT_OPERATOR
                ) {
                    // Method call, not a call to the PHP native function.
                    continue;
                }

                if ($tokens[$prevNonEmpty]['code'] === \T_NS_SEPARATOR
                    && $tokens[$prevNonEmpty - 1]['code'] === \T_STRING
                ) {
                    // Namespaced function.
                    continue;
                }

                // Ok, so we know that there is an array function in the first param.
                // 99.9% chance that this is $pieces, not $glue.
                $this->throwNotice($phpcsFile, $stackPtr, $functionName);
                return;
            }
        }

        if ($isOnlyText === true) {
            // First parameter only contained text string tokens, i.e. glue.
            return;
        }

        /*
         * Examine the second parameter.
         */

        $targetParam = $parameters[2];
        $start       = $targetParam['start'];
        $end         = ($targetParam['end'] + 1);

        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $start, $end, true);
        if ($firstNonEmpty === false) {
            // Parse error. Shouldn't be possible.
            return;
        }

        if ($tokens[$firstNonEmpty]['code'] === \T_OPEN_PARENTHESIS) {
            $start = ($firstNonEmpty + 1);
            $end   = $tokens[$firstNonEmpty]['parenthesis_closer'];
        }

        $hasTernary = $phpcsFile->findNext(\T_INLINE_THEN, $start, $end);
        if ($hasTernary !== false
            && isset($tokens[$start]['nested_parenthesis'], $tokens[$hasTernary]['nested_parenthesis'])
            && count($tokens[$start]['nested_parenthesis']) === count($tokens[$hasTernary]['nested_parenthesis'])
        ) {
            $start = ($hasTernary + 1);
        }

        for ($i = $start; $i < $end; $i++) {
            $tokenCode = $tokens[$i]['code'];

            if (isset(Tokens::$emptyTokens[$tokenCode])) {
                continue;
            }

            if ($tokenCode === \T_ARRAY || $tokenCode === \T_OPEN_SHORT_ARRAY || $tokenCode === \T_ARRAY_CAST) {
                // Found an array, $pieces is second.
                return;
            }

            if ($tokenCode === \T_STRING && isset($this->constantStrings[$tokens[$i]['content']])) {
                // One of the special cased, PHP native string constants found.
                $this->throwNotice($phpcsFile, $stackPtr, $functionName);
                return;
            }

            if ($tokenCode === \T_STRING || $tokenCode === \T_VARIABLE) {
                // Function call, constant or variable encountered.
                // No matter what this is combined with, we won't be able to reliably determine the value.
                return;
            }

            if ($tokenCode === \T_CONSTANT_ENCAPSED_STRING
                || $tokenCode === \T_DOUBLE_QUOTED_STRING
                || $tokenCode === \T_HEREDOC
                || $tokenCode === \T_NOWDOC
            ) {
                $this->throwNotice($phpcsFile, $stackPtr, $functionName);
                return;
            }
        }
    }


    /**
     * Throw the error/warning.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the stack.
     * @param string                $functionName The token content (function name) which was matched.
     *
     * @return void
     */
    protected function throwNotice(File $phpcsFile, $stackPtr, $functionName)
    {
        $message   = 'Passing the $glue and $pieces parameters in reverse order to %s has been deprecated since PHP 7.4';
        $isError   = false;
        $errorCode = 'Deprecated';
        $data      = array($functionName);

        /*
        Support for the deprecated behaviour is expected to be removed in PHP 8.0.
        Once this has been implemented, this section should be uncommented.
        if ($this->supportsAbove('8.0') === true) {
            $message  .= ' and is removed since PHP 8.0';
            $isError   = true;
            $errorCode = 'Removed';
        }
        */

        $message .= '; $glue should be the first parameter and $pieces the second';

        $this->addMessage($phpcsFile, $message, $stackPtr, $isError, $errorCode, $data);
    }
}
