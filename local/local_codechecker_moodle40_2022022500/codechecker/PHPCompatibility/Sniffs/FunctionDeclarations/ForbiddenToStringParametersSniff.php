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
 * As of PHP 5.3, the __toString() magic method can no longer accept arguments.
 *
 * Sister-sniff to `PHPCompatibility.MethodUse.ForbiddenToStringParameters`.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/migration53.incompatible.php
 * @link https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
 *
 * @since 9.2.0
 */
class ForbiddenToStringParametersSniff extends Sniff
{

    /**
     * Valid scopes for the __toString() method to live in.
     *
     * @since 9.2.0
     * @since 9.3.2 Visibility changed from `public` to `protected`.
     *
     * @var array
     */
    protected $ooScopeTokens = array(
        'T_CLASS'      => true,
        'T_INTERFACE'  => true,
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
        if ($this->supportsAbove('5.3') === false) {
            return;
        }

        $functionName = $phpcsFile->getDeclarationName($stackPtr);
        if (strtolower($functionName) !== '__tostring') {
            // Not the right function.
            return;
        }

        if ($this->validDirectScope($phpcsFile, $stackPtr, $this->ooScopeTokens) === false) {
            // Function, not method.
            return;
        }

        $params = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);
        if (empty($params)) {
            // Function declared without parameters.
            return;
        }

        $phpcsFile->addError(
            'The __toString() magic method can no longer accept arguments since PHP 5.3',
            $stackPtr,
            'Declared'
        );
    }
}
