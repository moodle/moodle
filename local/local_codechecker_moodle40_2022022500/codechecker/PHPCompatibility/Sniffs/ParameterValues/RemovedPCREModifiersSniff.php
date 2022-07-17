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
 * Check for the use of deprecated and removed regex modifiers for PCRE regex functions.
 *
 * Initially just checks for the `e` modifier, which was deprecated since PHP 5.5
 * and removed as of PHP 7.0.
 *
 * {@internal If and when this sniff would need to start checking for other modifiers, a minor
 * refactor will be needed as all references to the `e` modifier are currently hard-coded.}
 *
 * PHP version 5.5
 * PHP version 7.0
 *
 * @link https://wiki.php.net/rfc/remove_preg_replace_eval_modifier
 * @link https://wiki.php.net/rfc/remove_deprecated_functionality_in_php7
 * @link https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
 *
 * @since 5.6
 * @since 7.0.8 This sniff now throws a warning (deprecated) or an error (removed) depending
 *              on the `testVersion` set. Previously it would always throw an error.
 * @since 8.2.0 Now extends the `AbstractFunctionCallParameterSniff` instead of the base `Sniff` class.
 * @since 9.0.0 Renamed from `PregReplaceEModifierSniff` to `RemovedPCREModifiersSniff`.
 */
class RemovedPCREModifiersSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 7.0.1
     * @since 8.2.0 Renamed from `$functions` to `$targetFunctions`.
     *
     * @var array
     */
    protected $targetFunctions = array(
        'preg_replace' => true,
        'preg_filter'  => true,
    );

    /**
     * Regex bracket delimiters.
     *
     * @since 7.0.5 This array was originally contained within the `process()` method.
     *
     * @var array
     */
    protected $doublesSeparators = array(
        '{' => '}',
        '[' => ']',
        '(' => ')',
        '<' => '>',
    );


    /**
     * Process the parameters of a matched function.
     *
     * @since 5.6
     * @since 8.2.0 Renamed from `process()` to `processParameters()` and removed
     *              logic superfluous now the sniff extends the abstract.
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
        // Check the first parameter in the function call as that should contain the regex(es).
        if (isset($parameters[1]) === false) {
            return;
        }

        $tokens         = $phpcsFile->getTokens();
        $functionNameLc = strtolower($functionName);
        $firstParam     = $parameters[1];

        // Differentiate between an array of patterns passed and a single pattern.
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $firstParam['start'], ($firstParam['end'] + 1), true);
        if ($nextNonEmpty !== false && ($tokens[$nextNonEmpty]['code'] === \T_ARRAY || $tokens[$nextNonEmpty]['code'] === \T_OPEN_SHORT_ARRAY)) {
            $arrayValues = $this->getFunctionCallParameters($phpcsFile, $nextNonEmpty);
            if ($functionNameLc === 'preg_replace_callback_array') {
                // For preg_replace_callback_array(), the patterns will be in the array keys.
                foreach ($arrayValues as $value) {
                    $hasKey = $phpcsFile->findNext(\T_DOUBLE_ARROW, $value['start'], ($value['end'] + 1));
                    if ($hasKey === false) {
                        continue;
                    }

                    $value['end'] = ($hasKey - 1);
                    $value['raw'] = trim($phpcsFile->getTokensAsString($value['start'], ($hasKey - $value['start'])));
                    $this->processRegexPattern($value, $phpcsFile, $value['end'], $functionName);
                }

            } else {
                // Otherwise, the patterns will be in the array values.
                foreach ($arrayValues as $value) {
                    $hasKey = $phpcsFile->findNext(\T_DOUBLE_ARROW, $value['start'], ($value['end'] + 1));
                    if ($hasKey !== false) {
                        $value['start'] = ($hasKey + 1);
                        $value['raw']   = trim($phpcsFile->getTokensAsString($value['start'], (($value['end'] + 1) - $value['start'])));
                    }

                    $this->processRegexPattern($value, $phpcsFile, $value['end'], $functionName);
                }
            }

        } else {
            $this->processRegexPattern($firstParam, $phpcsFile, $stackPtr, $functionName);
        }
    }


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 8.2.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsAbove('5.5') === false);
    }


    /**
     * Analyse a potential regex pattern for use of the /e modifier.
     *
     * @since 7.1.2 This logic was originally contained within the `process()` method.
     *
     * @param array                 $pattern      Array containing the start and end token
     *                                            pointer of the potential regex pattern and
     *                                            the raw string value of the pattern.
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the
     *                                            stack passed in $tokens.
     * @param string                $functionName The function which contained the pattern.
     *
     * @return void
     */
    protected function processRegexPattern($pattern, File $phpcsFile, $stackPtr, $functionName)
    {
        $tokens = $phpcsFile->getTokens();

        /*
         * The pattern might be build up of a combination of strings, variables
         * and function calls. We are only concerned with the strings.
         */
        $regex = '';
        for ($i = $pattern['start']; $i <= $pattern['end']; $i++) {
            if (isset(Tokens::$stringTokens[$tokens[$i]['code']]) === true) {
                $content = $this->stripQuotes($tokens[$i]['content']);
                if ($tokens[$i]['code'] === \T_DOUBLE_QUOTED_STRING) {
                    $content = $this->stripVariables($content);
                }

                $regex .= trim($content);
            }
        }

        // Deal with multi-line regexes which were broken up in several string tokens.
        if ($tokens[$pattern['start']]['line'] !== $tokens[$pattern['end']]['line']) {
            $regex = $this->stripQuotes($regex);
        }

        if ($regex === '') {
            // No string token found in the first parameter, so skip it (e.g. if variable passed in).
            return;
        }

        $regexFirstChar = substr($regex, 0, 1);

        // Make sure that the character identified as the delimiter is valid.
        // Otherwise, it is a false positive caused by the string concatenation.
        if (preg_match('`[a-z0-9\\\\ ]`i', $regexFirstChar) === 1) {
            return;
        }

        if (isset($this->doublesSeparators[$regexFirstChar])) {
            $regexEndPos = strrpos($regex, $this->doublesSeparators[$regexFirstChar]);
        } else {
            $regexEndPos = strrpos($regex, $regexFirstChar);
        }

        if ($regexEndPos !== false) {
            $modifiers = substr($regex, $regexEndPos + 1);
            $this->examineModifiers($phpcsFile, $stackPtr, $functionName, $modifiers);
        }
    }


    /**
     * Examine the regex modifier string.
     *
     * @since 8.2.0 Split off from the `processRegexPattern()` method.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the
     *                                            stack passed in $tokens.
     * @param string                $functionName The function which contained the pattern.
     * @param string                $modifiers    The regex modifiers found.
     *
     * @return void
     */
    protected function examineModifiers(File $phpcsFile, $stackPtr, $functionName, $modifiers)
    {
        if (strpos($modifiers, 'e') !== false) {
            $error     = '%s() - /e modifier is deprecated since PHP 5.5';
            $isError   = false;
            $errorCode = 'Deprecated';
            $data      = array($functionName);

            if ($this->supportsAbove('7.0')) {
                $error    .= ' and removed since PHP 7.0';
                $isError   = true;
                $errorCode = 'Removed';
            }

            $this->addMessage($phpcsFile, $error, $stackPtr, $isError, $errorCode, $data);
        }
    }
}
