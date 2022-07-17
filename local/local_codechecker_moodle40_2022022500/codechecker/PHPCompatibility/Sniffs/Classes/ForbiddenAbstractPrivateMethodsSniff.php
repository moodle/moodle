<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Classes;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Abstract private methods are not allowed since PHP 5.1.
 *
 * Abstract private methods were supported between PHP 5.0.0 and PHP 5.0.4, but
 * were then disallowed on the grounds that the behaviours of `private` and `abstract`
 * are mutually exclusive.
 *
 * PHP version 5.1
 *
 * @link https://www.php.net/manual/en/migration51.oop.php#migration51.oop-methods
 *
 * @since 9.2.0
 */
class ForbiddenAbstractPrivateMethodsSniff extends Sniff
{

    /**
     * Valid scopes to check for abstract private methods.
     *
     * @since 9.2.0
     *
     * @var array
     */
    public $ooScopeTokens = array(
        'T_CLASS'      => true,
        'T_TRAIT'      => true,
        'T_ANON_CLASS' => true,
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.2.0
     *
     * @return array
     */
    public function register()
    {
        return array(\T_FUNCTION);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('5.1') === false) {
            return;
        }

        if ($this->validDirectScope($phpcsFile, $stackPtr, $this->ooScopeTokens) === false) {
            // Function, not method.
            return;
        }

        $properties = $phpcsFile->getMethodProperties($stackPtr);
        if ($properties['scope'] !== 'private' || $properties['is_abstract'] !== true) {
            return;
        }

        $phpcsFile->addError(
            'Abstract methods cannot be declared as private since PHP 5.1',
            $stackPtr,
            'Found'
        );
    }
}
