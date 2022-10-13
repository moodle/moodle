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
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Nullable parameter type declarations and return types are available since PHP 7.1.
 *
 * PHP version 7.1
 *
 * @link https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.nullable-types
 * @link https://wiki.php.net/rfc/nullable_types
 * @link https://www.php.net/manual/en/functions.arguments.php#example-146
 *
 * @since 7.0.7
 */
class NewNullableTypesSniff extends Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * {@internal Not sniffing for T_NULLABLE which was introduced in PHPCS 2.7.2
     * as in that case we can't distinguish between parameter type hints and
     * return type hints for the error message.}
     *
     * @since 7.0.7
     *
     * @return array
     */
    public function register()
    {
        $tokens = array(
            \T_FUNCTION,
            \T_CLOSURE,
        );

        if (\defined('T_RETURN_TYPE')) {
            $tokens[] = \T_RETURN_TYPE;
        }

        return $tokens;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.7
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('7.0') === false) {
            return;
        }

        $tokens    = $phpcsFile->getTokens();
        $tokenCode = $tokens[$stackPtr]['code'];

        if ($tokenCode === \T_FUNCTION || $tokenCode === \T_CLOSURE) {
            $this->processFunctionDeclaration($phpcsFile, $stackPtr);

            // Deal with older PHPCS version which don't recognize return type hints
            // as well as newer PHPCS versions (3.3.0+) where the tokenization has changed.
            $returnTypeHint = $this->getReturnTypeHintToken($phpcsFile, $stackPtr);
            if ($returnTypeHint !== false) {
                $this->processReturnType($phpcsFile, $returnTypeHint);
            }
        } else {
            $this->processReturnType($phpcsFile, $stackPtr);
        }
    }


    /**
     * Process this test for function tokens.
     *
     * @since 7.0.7
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processFunctionDeclaration(File $phpcsFile, $stackPtr)
    {
        $params = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);

        if (empty($params) === false && \is_array($params)) {
            foreach ($params as $param) {
                if ($param['nullable_type'] === true) {
                    $phpcsFile->addError(
                        'Nullable type declarations are not supported in PHP 7.0 or earlier. Found: %s',
                        $param['token'],
                        'typeDeclarationFound',
                        array($param['type_hint'])
                    );
                }
            }
        }
    }


    /**
     * Process this test for return type tokens.
     *
     * @since 7.0.7
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processReturnType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[($stackPtr - 1)]['code']) === false) {
            return;
        }

        $previous = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        // Deal with namespaced class names.
        if ($tokens[$previous]['code'] === \T_NS_SEPARATOR) {
            $validTokens                  = Tokens::$emptyTokens;
            $validTokens[\T_STRING]       = true;
            $validTokens[\T_NS_SEPARATOR] = true;

            $stackPtr--;

            while (isset($validTokens[$tokens[($stackPtr - 1)]['code']]) === true) {
                $stackPtr--;
            }

            $previous = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        }

        // T_NULLABLE token was introduced in PHPCS 2.7.2. Before that it identified as T_INLINE_THEN.
        if ((\defined('T_NULLABLE') === true && $tokens[$previous]['type'] === 'T_NULLABLE')
            || (\defined('T_NULLABLE') === false && $tokens[$previous]['code'] === \T_INLINE_THEN)
        ) {
            $phpcsFile->addError(
                'Nullable return types are not supported in PHP 7.0 or earlier.',
                $stackPtr,
                'returnTypeFound'
            );
        }
    }
}
