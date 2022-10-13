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

/**
 * Detect passing deprecated `$type` values to `iconv_get_encoding()`.
 *
 * "The iconv and mbstring configuration options related to encoding have been
 * deprecated in favour of default_charset."
 *
 * {@internal It is unclear which mbstring functions should be targetted, so for now,
 * only the iconv function is handled.}
 *
 * PHP version 5.6
 *
 * @link https://www.php.net/manual/en/migration56.deprecated.php#migration56.deprecated.iconv-mbstring-encoding
 * @link https://wiki.php.net/rfc/default_encoding
 *
 * @since 9.0.0
 */
class RemovedIconvEncodingSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'iconv_set_encoding' => true,
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.0.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsAbove('5.6') === false);
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.0.0
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
        if (isset($parameters[1]) === false) {
            return;
        }

        $phpcsFile->addWarning(
            'All previously accepted values for the $type parameter of iconv_set_encoding() have been deprecated since PHP 5.6. Found %s',
            $parameters[1]['start'],
            'DeprecatedValueFound',
            $parameters[1]['raw']
        );
    }
}
