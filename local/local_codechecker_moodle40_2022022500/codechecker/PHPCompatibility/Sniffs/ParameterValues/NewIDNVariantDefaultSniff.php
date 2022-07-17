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
 * The default value for the `$variant` parameter has changed from `INTL_IDNA_VARIANT_2003`
 * to `INTL_IDNA_VARIANT_UTS46` in PHP 7.4.
 *
 * PHP version 7.4
 *
 * @link https://www.php.net/manual/en/migration74.incompatible.php#migration74.incompatible.intl
 * @link https://wiki.php.net/rfc/deprecate-and-remove-intl_idna_variant_2003
 * @link https://www.php.net/manual/en/function.idn-to-ascii.php
 * @link https://www.php.net/manual/en/function.idn-to-utf8.php
 *
 * @since 9.3.0
 */
class NewIDNVariantDefaultSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * Key is the function name, value the 1-based parameter position of
     * the $variant parameter.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'idn_to_ascii' => 3,
        'idn_to_utf8'  => 3,
    );

    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * Note: This sniff should only trigger errors when both PHP 7.3 or lower,
     * as well as PHP 7.4 or higher need to be supported within the application.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('7.3') === false || $this->supportsAbove('7.4') === false);
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
        if (isset($parameters[$this->targetFunctions[$functionLC]]) === true) {
            return;
        }

        $error = 'The default value of the %s() $variant parameter has changed from INTL_IDNA_VARIANT_2003 to INTL_IDNA_VARIANT_UTS46 in PHP 7.4. For optimal cross-version compatibility, the $variant parameter should be explicitly set.';
        $phpcsFile->addError(
            $error,
            $stackPtr,
            'NotSet',
            array($functionName)
        );
    }
}
