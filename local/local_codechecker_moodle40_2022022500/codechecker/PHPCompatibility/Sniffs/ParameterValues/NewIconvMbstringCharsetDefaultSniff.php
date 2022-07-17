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
 * Detect calls to Iconv and Mbstring functions with the optional `$charset`/`$encoding` parameter not set.
 *
 * The default value for the iconv `$charset` and the MbString  $encoding` parameters was changed
 * in PHP 5.6 to the value of `default_charset`, which defaults to `UTF-8`.
 *
 * Previously, the iconv functions would default to the value of `iconv.internal_encoding`;
 * The Mbstring functions would default to the return value of `mb_internal_encoding()`.
 * In both case, this would normally come down to `ISO-8859-1`.
 *
 * PHP version 5.6
 *
 * @link https://www.php.net/manual/en/migration56.new-features.php#migration56.new-features.default-encoding
 * @link https://www.php.net/manual/en/migration56.deprecated.php#migration56.deprecated.iconv-mbstring-encoding
 * @link https://wiki.php.net/rfc/default_encoding
 *
 * @since 9.3.0
 */
class NewIconvMbstringCharsetDefaultSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * Only those functions where the charset/encoding parameter is optional need to be listed.
     *
     * Key is the function name, value the 1-based parameter position of
     * the $charset/$encoding parameter.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'iconv_mime_decode_headers' => 3,
        'iconv_mime_decode'         => 3,
        'iconv_mime_encode'         => 3, // Special case.
        'iconv_strlen'              => 2,
        'iconv_strpos'              => 4,
        'iconv_strrpos'             => 3,
        'iconv_substr'              => 4,

        'mb_check_encoding'         => 2,
        'mb_chr'                    => 2,
        'mb_convert_case'           => 3,
        'mb_convert_encoding'       => 3,
        'mb_convert_kana'           => 3,
        'mb_decode_numericentity'   => 3,
        'mb_encode_numericentity'   => 3,
        'mb_ord'                    => 2,
        'mb_scrub'                  => 2,
        'mb_strcut'                 => 4,
        'mb_stripos'                => 4,
        'mb_stristr'                => 4,
        'mb_strlen'                 => 2,
        'mb_strpos'                 => 4,
        'mb_strrchr'                => 4,
        'mb_strrichr'               => 4,
        'mb_strripos'               => 4,
        'mb_strrpos'                => 4,
        'mb_strstr'                 => 4,
        'mb_strtolower'             => 2,
        'mb_strtoupper'             => 2,
        'mb_strwidth'               => 2,
        'mb_substr_count'           => 3,
        'mb_substr'                 => 4,
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * Note: This sniff should only trigger errors when both PHP 5.5 or lower,
     * as well as PHP 5.6 or higher need to be supported within the application.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('5.5') === false || $this->supportsAbove('5.6') === false);
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
        $functionLC = strtolower($functionName);
        if ($functionLC === 'iconv_mime_encode') {
            // Special case the iconv_mime_encode() function.
            return $this->processIconvMimeEncode($phpcsFile, $stackPtr, $functionName, $parameters);
        }

        if (isset($parameters[$this->targetFunctions[$functionLC]]) === true) {
            return;
        }

        $paramName = '$encoding';
        if (strpos($functionLC, 'iconv_') === 0) {
            $paramName = '$charset';
        } elseif ($functionLC === 'mb_convert_encoding') {
            $paramName = '$from_encoding';
        }

        $error = 'The default value of the %1$s parameter for %2$s() was changed from ISO-8859-1 to UTF-8 in PHP 5.6. For cross-version compatibility, the %1$s parameter should be explicitly set.';
        $data  = array(
            $paramName,
            $functionName,
        );

        $phpcsFile->addError($error, $stackPtr, 'NotSet', $data);
    }

    /**
     * Process the parameters of a matched call to the iconv_mime_encode() function.
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
    public function processIconvMimeEncode(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        $error = 'The default value of the %s parameter index for iconv_mime_encode() was changed from ISO-8859-1 to UTF-8 in PHP 5.6. For cross-version compatibility, the %s should be explicitly set.';

        $functionLC = strtolower($functionName);
        if (isset($parameters[$this->targetFunctions[$functionLC]]) === false) {
            $phpcsFile->addError(
                $error,
                $stackPtr,
                'PreferencesNotSet',
                array(
                    '$preferences[\'input/output-charset\']',
                    '$preferences[\'input-charset\'] and $preferences[\'output-charset\'] indexes',
                )
            );

            return;
        }

        $tokens        = $phpcsFile->getTokens();
        $targetParam   = $parameters[$this->targetFunctions[$functionLC]];
        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $targetParam['start'], ($targetParam['end'] + 1), true);
        if ($firstNonEmpty === false) {
            // Parse error or live coding.
            return;
        }

        if ($tokens[$firstNonEmpty]['code'] === \T_ARRAY
            || $tokens[$firstNonEmpty]['code'] === \T_OPEN_SHORT_ARRAY
        ) {
            $hasInputCharset  = preg_match('`([\'"])input-charset\1\s*=>`', $targetParam['raw']);
            $hasOutputCharset = preg_match('`([\'"])output-charset\1\s*=>`', $targetParam['raw']);
            if ($hasInputCharset === 1 && $hasOutputCharset === 1) {
                // Both input as well as output charset are set.
                return;
            }

            if ($hasInputCharset !== 1) {
                $phpcsFile->addError(
                    $error,
                    $firstNonEmpty,
                    'InputPreferenceNotSet',
                    array(
                        '$preferences[\'input-charset\']',
                        '$preferences[\'input-charset\'] index',
                    )
                );
            }

            if ($hasOutputCharset !== 1) {
                $phpcsFile->addError(
                    $error,
                    $firstNonEmpty,
                    'OutputPreferenceNotSet',
                    array(
                        '$preferences[\'output-charset\']',
                        '$preferences[\'output-charset\'] index',
                    )
                );
            }

            return;
        }

        // The $preferences parameter was passed, but it was a variable/constant/output of a function call.
        $phpcsFile->addWarning(
            $error,
            $firstNonEmpty,
            'Undetermined',
            array(
                '$preferences[\'input/output-charset\']',
                '$preferences[\'input-charset\'] and $preferences[\'output-charset\'] indexes',
            )
        );
    }
}
