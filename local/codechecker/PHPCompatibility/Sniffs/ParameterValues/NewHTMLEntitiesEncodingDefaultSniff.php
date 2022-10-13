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
 * As of PHP 5.4, the default character set for `htmlspecialchars()`, `htmlentities()`
 * and `html_entity_decode()` is now `UTF-8`, instead of `ISO-8859-1`.
 *
 * PHP version 5.4
 *
 * @link https://www.php.net/manual/en/migration54.other.php
 * @link https://www.php.net/manual/en/function.html-entity-decode.php#refsect1-function.html-entity-decode-changelog
 * @link https://www.php.net/manual/en/function.htmlentities.php#refsect1-function.htmlentities-changelog
 * @link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-changelog
 *
 * @since 9.3.0
 */
class NewHTMLEntitiesEncodingDefaultSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * Key is the function name, value the 1-based parameter position of
     * the $encoding parameter.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'html_entity_decode' => 3,
        'htmlentities'       => 3,
        'htmlspecialchars'   => 3,
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * Note: This sniff should only trigger errors when both PHP 5.3 or lower,
     * as well as PHP 5.4 or higher need to be supported within the application.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('5.3') === false || $this->supportsAbove('5.4') === false);
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

        $phpcsFile->addError(
            'The default value of the $encoding parameter for %s() was changed from ISO-8859-1 to UTF-8 in PHP 5.4. For cross-version compatibility, the $encoding parameter should be explicitly set.',
            $stackPtr,
            'NotSet',
            array($functionName)
        );
    }
}
