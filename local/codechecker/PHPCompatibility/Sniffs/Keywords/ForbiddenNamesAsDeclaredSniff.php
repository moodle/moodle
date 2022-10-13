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
 * Detects the use of some reserved keywords to name a class, interface, trait or namespace.
 *
 * Emits errors for reserved words and warnings for soft-reserved words.
 *
 * PHP version 7.0+
 *
 * @link https://www.php.net/manual/en/reserved.other-reserved-words.php
 * @link https://wiki.php.net/rfc/reserve_more_types_in_php_7
 *
 * @since 7.0.8
 * @since 7.1.4 This sniff now throws a warning (soft reserved) or an error (reserved) depending
 *              on the `testVersion` set. Previously it would always throw an error.
 */
class ForbiddenNamesAsDeclaredSniff extends Sniff
{

    /**
     * List of tokens which can not be used as class, interface, trait names or as part of a namespace.
     *
     * @since 7.0.8
     *
     * @var array
     */
    protected $forbiddenTokens = array(
        \T_NULL  => '7.0',
        \T_TRUE  => '7.0',
        \T_FALSE => '7.0',
    );

    /**
     * T_STRING keywords to recognize as forbidden names.
     *
     * @since 7.0.8
     *
     * @var array
     */
    protected $forbiddenNames = array(
        'null'     => '7.0',
        'true'     => '7.0',
        'false'    => '7.0',
        'bool'     => '7.0',
        'int'      => '7.0',
        'float'    => '7.0',
        'string'   => '7.0',
        'iterable' => '7.1',
        'void'     => '7.1',
        'object'   => '7.2',
    );

    /**
     * T_STRING keywords to recognize as soft reserved names.
     *
     * Using any of these keywords to name a class, interface, trait or namespace
     * is highly discouraged since they may be used in future versions of PHP.
     *
     * @since 7.0.8
     *
     * @var array
     */
    protected $softReservedNames = array(
        'resource' => '7.0',
        'object'   => '7.0',
        'mixed'    => '7.0',
        'numeric'  => '7.0',
    );

    /**
     * Combined list of the two lists above.
     *
     * Used for quick check whether or not something is a reserved
     * word.
     * Set from the `register()` method.
     *
     * @since 7.0.8
     *
     * @var array
     */
    private $allForbiddenNames = array();


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.8
     *
     * @return array
     */
    public function register()
    {
        // Do the list merge only once.
        $this->allForbiddenNames = array_merge($this->forbiddenNames, $this->softReservedNames);

        $targets = array(
            \T_CLASS,
            \T_INTERFACE,
            \T_TRAIT,
            \T_NAMESPACE,
            \T_STRING, // Compat for PHPCS < 2.4.0 and PHP < 5.3.
        );

        return $targets;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.8
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0') === false) {
            return;
        }

        $tokens         = $phpcsFile->getTokens();
        $tokenCode      = $tokens[$stackPtr]['code'];
        $tokenType      = $tokens[$stackPtr]['type'];
        $tokenContentLc = strtolower($tokens[$stackPtr]['content']);

        // For string tokens we only care about 'trait' as that is the only one
        // which may not be correctly recognized as it's own token.
        // This only happens in older versions of PHP where the token doesn't exist yet as a keyword.
        if ($tokenCode === \T_STRING && $tokenContentLc !== 'trait') {
            return;
        }

        if (\in_array($tokenType, array('T_CLASS', 'T_INTERFACE', 'T_TRAIT'), true)) {
            // Check for the declared name being a name which is not tokenized as T_STRING.
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty !== false && isset($this->forbiddenTokens[$tokens[$nextNonEmpty]['code']]) === true) {
                $name = $tokens[$nextNonEmpty]['content'];
            } else {
                // Get the declared name if it's a T_STRING.
                $name = $phpcsFile->getDeclarationName($stackPtr);
            }
            unset($nextNonEmpty);

            if (isset($name) === false || \is_string($name) === false || $name === '') {
                return;
            }

            $nameLc = strtolower($name);
            if (isset($this->allForbiddenNames[$nameLc]) === false) {
                return;
            }

        } elseif ($tokenCode === \T_NAMESPACE) {
            $namespaceName = $this->getDeclaredNamespaceName($phpcsFile, $stackPtr);

            if ($namespaceName === false || $namespaceName === '') {
                return;
            }

            $namespaceParts = explode('\\', $namespaceName);
            foreach ($namespaceParts as $namespacePart) {
                $partLc = strtolower($namespacePart);
                if (isset($this->allForbiddenNames[$partLc]) === true) {
                    $name   = $namespacePart;
                    $nameLc = $partLc;
                    break;
                }
            }
        } elseif ($tokenCode === \T_STRING) {
            // Traits which are not yet tokenized as T_TRAIT.
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty === false) {
                return;
            }

            $nextNonEmptyCode = $tokens[$nextNonEmpty]['code'];

            if ($nextNonEmptyCode !== \T_STRING && isset($this->forbiddenTokens[$nextNonEmptyCode]) === true) {
                $name   = $tokens[$nextNonEmpty]['content'];
                $nameLc = strtolower($tokens[$nextNonEmpty]['content']);
            } elseif ($nextNonEmptyCode === \T_STRING) {
                $endOfStatement = $phpcsFile->findNext(array(\T_SEMICOLON, \T_OPEN_CURLY_BRACKET), ($stackPtr + 1));
                if ($endOfStatement === false) {
                    return;
                }

                do {
                    $nextNonEmptyLc = strtolower($tokens[$nextNonEmpty]['content']);

                    if (isset($this->allForbiddenNames[$nextNonEmptyLc]) === true) {
                        $name   = $tokens[$nextNonEmpty]['content'];
                        $nameLc = $nextNonEmptyLc;
                        break;
                    }

                    $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonEmpty + 1), $endOfStatement, true);
                } while ($nextNonEmpty !== false);
            }
            unset($nextNonEmptyCode, $nextNonEmptyLc, $endOfStatement);
        }

        if (isset($name, $nameLc) === false) {
            return;
        }

        // Still here, so this is one of the reserved words.
        // Build up the error message.
        $error     = "'%s' is a";
        $isError   = null;
        $errorCode = $this->stringToErrorCode($nameLc) . 'Found';
        $data      = array(
            $nameLc,
        );

        if (isset($this->softReservedNames[$nameLc]) === true
            && $this->supportsAbove($this->softReservedNames[$nameLc]) === true
        ) {
            $error  .= ' soft reserved keyword as of PHP version %s';
            $isError = false;
            $data[]  = $this->softReservedNames[$nameLc];
        }

        if (isset($this->forbiddenNames[$nameLc]) === true
            && $this->supportsAbove($this->forbiddenNames[$nameLc]) === true
        ) {
            if (isset($isError) === true) {
                $error .= ' and a';
            }
            $error  .= ' reserved keyword as of PHP version %s';
            $isError = true;
            $data[]  = $this->forbiddenNames[$nameLc];
        }

        if (isset($isError) === true) {
            $error .= ' and should not be used to name a class, interface or trait or as part of a namespace (%s)';
            $data[] = $tokens[$stackPtr]['type'];

            $this->addMessage($phpcsFile, $error, $stackPtr, $isError, $errorCode, $data);
        }
    }
}
