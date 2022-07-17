<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Keywords;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Prohibits the use of reserved keywords invoked as functions.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/reserved.keywords.php
 *
 * @since 5.5
 */
class ForbiddenNamesAsInvokedFunctionsSniff extends Sniff
{

    /**
     * List of tokens to register.
     *
     * @since 5.5
     *
     * @var array
     */
    protected $targetedTokens = array(
        \T_ABSTRACT   => '5.0',
        \T_CALLABLE   => '5.4',
        \T_CATCH      => '5.0',
        \T_FINAL      => '5.0',
        \T_FINALLY    => '5.5',
        \T_GOTO       => '5.3',
        \T_IMPLEMENTS => '5.0',
        \T_INTERFACE  => '5.0',
        \T_INSTANCEOF => '5.0',
        \T_INSTEADOF  => '5.4',
        \T_NAMESPACE  => '5.3',
        \T_PRIVATE    => '5.0',
        \T_PROTECTED  => '5.0',
        \T_PUBLIC     => '5.0',
        \T_TRAIT      => '5.4',
        \T_TRY        => '5.0',

    );

    /**
     * T_STRING keywords to recognize as targetted tokens.
     *
     * Compatibility for PHP versions where the keyword is not yet recognized
     * as its own token and for PHPCS versions which change the token to
     * T_STRING when used in a method call.
     *
     * @since 5.5
     *
     * @var array
     */
    protected $targetedStringTokens = array(
        'abstract'   => '5.0',
        'callable'   => '5.4',
        'catch'      => '5.0',
        'final'      => '5.0',
        'finally'    => '5.5',
        'goto'       => '5.3',
        'implements' => '5.0',
        'interface'  => '5.0',
        'instanceof' => '5.0',
        'insteadof'  => '5.4',
        'namespace'  => '5.3',
        'private'    => '5.0',
        'protected'  => '5.0',
        'public'     => '5.0',
        'trait'      => '5.4',
        'try'        => '5.0',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     *
     * @return array
     */
    public function register()
    {
        $tokens   = array_keys($this->targetedTokens);
        $tokens[] = \T_STRING;

        return $tokens;
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
        $tokens         = $phpcsFile->getTokens();
        $tokenCode      = $tokens[$stackPtr]['code'];
        $tokenContentLc = strtolower($tokens[$stackPtr]['content']);
        $isString       = false;

        /*
         * For string tokens we only care if the string is a reserved word used
         * as a function. This only happens in older versions of PHP where the
         * token doesn't exist yet for that keyword or in later versions when the
         * token is used in a method invocation.
         */
        if ($tokenCode === \T_STRING
            && (isset($this->targetedStringTokens[$tokenContentLc]) === false)
        ) {
            return;
        }

        if ($tokenCode === \T_STRING) {
            $isString = true;
        }

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== \T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        // This sniff isn't concerned about function declarations.
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prev !== false && $tokens[$prev]['code'] === \T_FUNCTION) {
            return;
        }

        /*
         * Deal with PHP 7 relaxing the rules.
         * "As of PHP 7.0.0 these keywords are allowed as property, constant, and method names
         * of classes, interfaces and traits...", i.e. they can be invoked as a method call.
         *
         * Only needed for those keywords which we sniff out via T_STRING.
         */
        if (($tokens[$prev]['code'] === \T_OBJECT_OPERATOR || $tokens[$prev]['code'] === \T_DOUBLE_COLON)
            && $this->supportsBelow('5.6') === false
        ) {
            return;
        }

        // For the word catch, it is valid to have an open parenthesis
        // after it, but only if it is preceded by a right curly brace.
        if ($tokenCode === \T_CATCH) {
            if ($prev !== false && $tokens[$prev]['code'] === \T_CLOSE_CURLY_BRACKET) {
                // Ok, it's fine.
                return;
            }
        }

        if ($isString === true) {
            $version = $this->targetedStringTokens[$tokenContentLc];
        } else {
            $version = $this->targetedTokens[$tokenCode];
        }

        if ($this->supportsAbove($version)) {
            $error     = "'%s' is a reserved keyword introduced in PHP version %s and cannot be invoked as a function (%s)";
            $errorCode = $this->stringToErrorCode($tokenContentLc) . 'Found';
            $data      = array(
                $tokenContentLc,
                $version,
                $tokens[$stackPtr]['type'],
            );

            $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
        }
    }
}
