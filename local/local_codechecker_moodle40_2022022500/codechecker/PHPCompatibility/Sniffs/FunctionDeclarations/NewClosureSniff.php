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
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect closures and verify that the features used are supported.
 *
 * Version based checks:
 * - Closures are available since PHP 5.3.
 * - Closures can be declared as `static` since PHP 5.4.
 * - Closures can use the `$this` variable within a class context since PHP 5.4.
 * - Closures can use `self`/`parent`/`static` since PHP 5.4.
 *
 * Version independent checks:
 * - Static closures don't have access to the `$this` variable.
 * - Closures declared outside of a class context don't have access to the `$this`
 *   variable unless bound to an object.
 *
 * PHP version 5.3
 * PHP version 5.4
 *
 * @link https://www.php.net/manual/en/functions.anonymous.php
 * @link https://wiki.php.net/rfc/closures
 * @link https://wiki.php.net/rfc/closures/object-extension
 *
 * @since 7.0.0
 */
class NewClosureSniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array
     */
    public function register()
    {
        return array(\T_CLOSURE);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     * @since 7.1.4 - Added check for closure being declared as static < 5.4.
     *              - Added check for use of `$this` variable in class context < 5.4.
     *              - Added check for use of `$this` variable in static closures (unsupported).
     *              - Added check for use of `$this` variable outside class context (unsupported).
     * @since 8.2.0 Added check for use of `self`/`static`/`parent` < 5.4.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.2')) {
            $phpcsFile->addError(
                'Closures / anonymous functions are not available in PHP 5.2 or earlier',
                $stackPtr,
                'Found'
            );
        }

        /*
         * Closures can only be declared as static since PHP 5.4.
         */
        $isStatic = $this->isClosureStatic($phpcsFile, $stackPtr);
        if ($this->supportsBelow('5.3') && $isStatic === true) {
            $phpcsFile->addError(
                'Closures / anonymous functions could not be declared as static in PHP 5.3 or earlier',
                $stackPtr,
                'StaticFound'
            );
        }

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            // Live coding or parse error.
            return;
        }

        $scopeStart = ($tokens[$stackPtr]['scope_opener'] + 1);
        $scopeEnd   = $tokens[$stackPtr]['scope_closer'];
        $usesThis   = $this->findThisUsageInClosure($phpcsFile, $scopeStart, $scopeEnd);

        if ($this->supportsBelow('5.3')) {
            /*
             * Closures declared within classes only have access to $this since PHP 5.4.
             */
            if ($usesThis !== false) {
                $thisFound = $usesThis;
                do {
                    $phpcsFile->addError(
                        'Closures / anonymous functions did not have access to $this in PHP 5.3 or earlier',
                        $thisFound,
                        'ThisFound'
                    );

                    $thisFound = $this->findThisUsageInClosure($phpcsFile, ($thisFound + 1), $scopeEnd);

                } while ($thisFound !== false);
            }

            /*
             * Closures declared within classes only have access to self/parent/static since PHP 5.4.
             */
            $usesClassRef = $this->findClassRefUsageInClosure($phpcsFile, $scopeStart, $scopeEnd);

            if ($usesClassRef !== false) {
                do {
                    $phpcsFile->addError(
                        'Closures / anonymous functions could not use "%s::" in PHP 5.3 or earlier',
                        $usesClassRef,
                        'ClassRefFound',
                        array(strtolower($tokens[$usesClassRef]['content']))
                    );

                    $usesClassRef = $this->findClassRefUsageInClosure($phpcsFile, ($usesClassRef + 1), $scopeEnd);

                } while ($usesClassRef !== false);
            }
        }

        /*
         * Check for correct usage.
         */
        if ($this->supportsAbove('5.4') && $usesThis !== false) {

            $thisFound = $usesThis;

            do {
                /*
                 * Closures only have access to $this if not declared as static.
                 */
                if ($isStatic === true) {
                    $phpcsFile->addError(
                        'Closures / anonymous functions declared as static do not have access to $this',
                        $thisFound,
                        'ThisFoundInStatic'
                    );
                }

                /*
                 * Closures only have access to $this if used within a class context.
                 */
                elseif ($this->inClassScope($phpcsFile, $stackPtr, false) === false) {
                    $phpcsFile->addWarning(
                        'Closures / anonymous functions only have access to $this if used within a class or when bound to an object using bindTo(). Please verify.',
                        $thisFound,
                        'ThisFoundOutsideClass'
                    );
                }

                $thisFound = $this->findThisUsageInClosure($phpcsFile, ($thisFound + 1), $scopeEnd);

            } while ($thisFound !== false);
        }

        // Prevent double reporting for nested closures.
        return $scopeEnd;
    }


    /**
     * Check whether the closure is declared as static.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return bool
     */
    protected function isClosureStatic(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true, null, true);

        return ($prevToken !== false && $tokens[$prevToken]['code'] === \T_STATIC);
    }


    /**
     * Check if the code within a closure uses the $this variable.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile  The file being scanned.
     * @param int                   $startToken The position within the closure to continue searching from.
     * @param int                   $endToken   The closure scope closer to stop searching at.
     *
     * @return int|false The stackPtr to the first $this usage if found or false if
     *                   $this is not used.
     */
    protected function findThisUsageInClosure(File $phpcsFile, $startToken, $endToken)
    {
        // Make sure the $startToken is valid.
        if ($startToken >= $endToken) {
            return false;
        }

        return $phpcsFile->findNext(
            \T_VARIABLE,
            $startToken,
            $endToken,
            false,
            '$this'
        );
    }

    /**
     * Check if the code within a closure uses "self/parent/static".
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile  The file being scanned.
     * @param int                   $startToken The position within the closure to continue searching from.
     * @param int                   $endToken   The closure scope closer to stop searching at.
     *
     * @return int|false The stackPtr to the first classRef usage if found or false if
     *                   they are not used.
     */
    protected function findClassRefUsageInClosure(File $phpcsFile, $startToken, $endToken)
    {
        // Make sure the $startToken is valid.
        if ($startToken >= $endToken) {
            return false;
        }

        $tokens   = $phpcsFile->getTokens();
        $classRef = $phpcsFile->findNext(array(\T_SELF, \T_PARENT, \T_STATIC), $startToken, $endToken);

        if ($classRef === false || $tokens[$classRef]['code'] !== \T_STATIC) {
            return $classRef;
        }

        // T_STATIC, make sure it is used as a class reference.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($classRef + 1), $endToken, true);
        if ($next === false || $tokens[$next]['code'] !== \T_DOUBLE_COLON) {
            return false;
        }

        return $classRef;
    }
}
