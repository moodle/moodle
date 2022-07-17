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
 * As of PHP 7.4, throwing exceptions from a `__toString()` method is allowed.
 *
 * PHP version 7.4
 *
 * @link https://wiki.php.net/rfc/tostring_exceptions
 * @link https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
 *
 * @since 9.2.0
 */
class NewExceptionsFromToStringSniff extends Sniff
{

    /**
     * Valid scopes for the __toString() method to live in.
     *
     * @since 9.2.0
     * @since 9.3.0 Visibility changed from `public` to `protected`.
     *
     * @var array
     */
    protected $ooScopeTokens = array(
        'T_CLASS'      => true,
        'T_TRAIT'      => true,
        'T_ANON_CLASS' => true,
    );

    /**
     * Tokens which should be ignored when they preface a function declaration
     * when trying to find the docblock (if any).
     *
     * Array will be added to in the register() method.
     *
     * @since 9.3.0
     *
     * @var array
     */
    private $docblockIgnoreTokens = array(
        \T_WHITESPACE => \T_WHITESPACE,
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
        // Enhance the array of tokens to ignore for finding the docblock.
        $this->docblockIgnoreTokens += Tokens::$methodPrefixes;
        if (isset(Tokens::$phpcsCommentTokens)) {
            $this->docblockIgnoreTokens += Tokens::$phpcsCommentTokens;
        }

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
        if ($this->supportsBelow('7.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            // Abstract function, interface function, live coding or parse error.
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

        /*
         * Examine the content of the function.
         */
        $error       = 'Throwing exceptions from __toString() was not allowed prior to PHP 7.4';
        $throwPtr    = $tokens[$stackPtr]['scope_opener'];
        $errorThrown = false;

        do {
            $throwPtr = $phpcsFile->findNext(\T_THROW, ($throwPtr + 1), $tokens[$stackPtr]['scope_closer']);
            if ($throwPtr === false) {
                break;
            }

            $conditions = $tokens[$throwPtr]['conditions'];
            $conditions = array_reverse($conditions, true);
            $inTryCatch = false;
            foreach ($conditions as $ptr => $type) {
                if ($type === \T_TRY) {
                    $inTryCatch = true;
                    break;
                }

                if ($ptr === $stackPtr) {
                    // Don't check the conditions outside the function scope.
                    break;
                }
            }

            if ($inTryCatch === false) {
                $phpcsFile->addError($error, $throwPtr, 'Found');
                $errorThrown = true;
            }
        } while (true);

        if ($errorThrown === true) {
            // We've already thrown an error for this method, no need to examine the docblock.
            return;
        }

        /*
         * Check whether the function has a docblock and if so, whether it contains a @throws tag.
         *
         * {@internal This can be partially replaced by the findCommentAboveFunction()
         *            utility function in due time.}
         */
        $commentEnd = $phpcsFile->findPrevious($this->docblockIgnoreTokens, ($stackPtr - 1), null, true);
        if ($commentEnd === false || $tokens[$commentEnd]['code'] !== \T_DOC_COMMENT_CLOSE_TAG) {
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] !== '@throws') {
                continue;
            }

            // Found a throws tag.
            $phpcsFile->addError($error, $stackPtr, 'ThrowsTagFoundInDocblock');
            break;
        }
    }
}
