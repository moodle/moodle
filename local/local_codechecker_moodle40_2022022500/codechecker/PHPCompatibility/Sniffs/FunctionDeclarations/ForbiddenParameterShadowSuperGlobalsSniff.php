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

/**
 * Detect the use of superglobals as parameters for functions, support for which was removed in PHP 5.4.
 *
 * {@internal List of superglobals is maintained in the parent class.}
 *
 * PHP version 5.4
 *
 * @link https://www.php.net/manual/en/migration54.incompatible.php
 *
 * @since 7.0.0
 */
class ForbiddenParameterShadowSuperGlobalsSniff extends Sniff
{

    /**
     * Register the tokens to listen for.
     *
     * @since 7.0.0
     * @since 7.1.3 Allows for closures.
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_FUNCTION,
            \T_CLOSURE,
        );
    }

    /**
     * Processes the test.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('5.4') === false) {
            return;
        }

        // Get all parameters from function signature.
        $parameters = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);
        if (empty($parameters) || \is_array($parameters) === false) {
            return;
        }

        foreach ($parameters as $param) {
            if (isset($this->superglobals[$param['name']]) === true) {
                $error     = 'Parameter shadowing super global (%s) causes fatal error since PHP 5.4';
                $errorCode = $this->stringToErrorCode(substr($param['name'], 1)) . 'Found';
                $data      = array($param['name']);

                $phpcsFile->addError($error, $param['token'], $errorCode, $data);
            }
        }
    }
}
