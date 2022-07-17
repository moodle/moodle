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
use PHP_CodeSniffer_File as File;

/**
 * Verifies the use of the correct visibility and static properties of magic methods.
 *
 * The requirements have always existed, but as of PHP 5.3, a warning will be thrown
 * when magic methods have the wrong modifiers.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/language.oop5.magic.php
 *
 * @since 5.5
 * @since 5.6 Now extends the base `Sniff` class.
 */
class NonStaticMagicMethodsSniff extends Sniff
{

    /**
     * A list of PHP magic methods and their visibility and static requirements.
     *
     * Method names in the array should be all *lowercase*.
     * Visibility can be either 'public', 'protected' or 'private'.
     * Static can be either 'true' - *must* be static, or 'false' - *must* be non-static.
     * When a method does not have a specific requirement for either visibility or static,
     * do *not* add the key.
     *
     * @since 5.5
     * @since 5.6 The array format has changed to allow the sniff to also verify the
     *            use of the correct visibility for a magic method.
     *
     * @var array(string)
     */
    protected $magicMethods = array(
        '__construct' => array(
            'static' => false,
        ),
        '__destruct' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__clone' => array(
            'static'     => false,
        ),
        '__get' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__set' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__isset' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__unset' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__call' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__callstatic' => array(
            'visibility' => 'public',
            'static'     => true,
        ),
        '__sleep' => array(
            'visibility' => 'public',
        ),
        '__tostring' => array(
            'visibility' => 'public',
        ),
        '__set_state' => array(
            'visibility' => 'public',
            'static'     => true,
        ),
        '__debuginfo' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__invoke' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__serialize' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
        '__unserialize' => array(
            'visibility' => 'public',
            'static'     => false,
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     * @since 5.6   Now also checks traits.
     * @since 7.1.4 Now also checks anonymous classes.
     *
     * @return array
     */
    public function register()
    {
        $targets = array(
            \T_CLASS,
            \T_INTERFACE,
            \T_TRAIT,
        );

        if (\defined('T_ANON_CLASS')) {
            $targets[] = \T_ANON_CLASS;
        }

        return $targets;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Should be removed, the requirement was previously also there, 5.3 just started throwing a warning about it.
        if ($this->supportsAbove('5.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $classScopeCloser = $tokens[$stackPtr]['scope_closer'];
        $functionPtr      = $stackPtr;

        // Find all the functions in this class or interface.
        while (($functionToken = $phpcsFile->findNext(\T_FUNCTION, $functionPtr, $classScopeCloser)) !== false) {
            /*
             * Get the scope closer for this function in order to know how
             * to advance to the next function.
             * If no body of function (e.g. for interfaces), there is
             * no closing curly brace; advance the pointer differently.
             */
            if (isset($tokens[$functionToken]['scope_closer'])) {
                $scopeCloser = $tokens[$functionToken]['scope_closer'];
            } else {
                $scopeCloser = ($functionToken + 1);
            }

            $methodName   = $phpcsFile->getDeclarationName($functionToken);
            $methodNameLc = strtolower($methodName);
            if (isset($this->magicMethods[$methodNameLc]) === false) {
                $functionPtr = $scopeCloser;
                continue;
            }

            $methodProperties = $phpcsFile->getMethodProperties($functionToken);
            $errorCodeBase    = $this->stringToErrorCode($methodNameLc);

            if (isset($this->magicMethods[$methodNameLc]['visibility']) && $this->magicMethods[$methodNameLc]['visibility'] !== $methodProperties['scope']) {
                $error     = 'Visibility for magic method %s must be %s. Found: %s';
                $errorCode = $errorCodeBase . 'MethodVisibility';
                $data      = array(
                    $methodName,
                    $this->magicMethods[$methodNameLc]['visibility'],
                    $methodProperties['scope'],
                );

                $phpcsFile->addError($error, $functionToken, $errorCode, $data);
            }

            if (isset($this->magicMethods[$methodNameLc]['static']) && $this->magicMethods[$methodNameLc]['static'] !== $methodProperties['is_static']) {
                $error     = 'Magic method %s cannot be defined as static.';
                $errorCode = $errorCodeBase . 'MethodStatic';
                $data      = array($methodName);

                if ($this->magicMethods[$methodNameLc]['static'] === true) {
                    $error     = 'Magic method %s must be defined as static.';
                    $errorCode = $errorCodeBase . 'MethodNonStatic';
                }

                $phpcsFile->addError($error, $functionToken, $errorCode, $data);
            }

            // Advance to next function.
            $functionPtr = $scopeCloser;
        }
    }
}
