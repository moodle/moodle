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

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect and verify the use of return type declarations in function declarations.
 *
 * Return type declarations are available since PHP 7.0.
 * - Since PHP 7.1, the `iterable` and `void` pseudo-types are available.
 * - Since PHP 7.2, the generic `object` type is available.
 *
 * PHP version 7.0+
 *
 * @link https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.return-type-declarations
 * @link https://www.php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration
 * @link https://wiki.php.net/rfc/return_types
 * @link https://wiki.php.net/rfc/iterable
 * @link https://wiki.php.net/rfc/void_return_type
 * @link https://wiki.php.net/rfc/object-typehint
 *
 * @since 7.0.0
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class.
 * @since 7.1.2 Renamed from `NewScalarReturnTypeDeclarationsSniff` to `NewReturnTypeDeclarationsSniff`.
 */
class NewReturnTypeDeclarationsSniff extends AbstractNewFeatureSniff
{

    /**
     * A list of new types
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the keyword appears.
     *
     * @since 7.0.0
     *
     * @var array(string => array(string => bool))
     */
    protected $newTypes = array(
        'int' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'float' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'bool' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'string' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'array' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'callable' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'parent' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'self' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'Class name' => array(
            '5.6' => false,
            '7.0' => true,
        ),

        'iterable' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'void' => array(
            '7.0' => false,
            '7.1' => true,
        ),

        'object' => array(
            '7.1' => false,
            '7.2' => true,
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     * @since 7.1.2 Now also checks based on the function and closure keywords.
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
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Deal with older PHPCS version which don't recognize return type hints
        // as well as newer PHPCS versions (3.3.0+) where the tokenization has changed.
        if ($tokens[$stackPtr]['code'] === \T_FUNCTION || $tokens[$stackPtr]['code'] === \T_CLOSURE) {
            $returnTypeHint = $this->getReturnTypeHintToken($phpcsFile, $stackPtr);
            if ($returnTypeHint !== false) {
                $stackPtr = $returnTypeHint;
            }
        }

        if (isset($this->newTypes[$tokens[$stackPtr]['content']]) === true) {
            $itemInfo = array(
                'name' => $tokens[$stackPtr]['content'],
            );
            $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
        }
        // Handle class name based return types.
        elseif ($tokens[$stackPtr]['code'] === \T_STRING
            || (\defined('T_RETURN_TYPE') && $tokens[$stackPtr]['code'] === \T_RETURN_TYPE)
        ) {
            $itemInfo = array(
                'name'   => 'Class name',
            );
            $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
        }
    }


    /**
     * Get the relevant sub-array for a specific item from a multi-dimensional array.
     *
     * @since 7.1.0
     *
     * @param array $itemInfo Base information about the item.
     *
     * @return array Version and other information about the item.
     */
    public function getItemArray(array $itemInfo)
    {
        return $this->newTypes[$itemInfo['name']];
    }


    /**
     * Get the error message template for this sniff.
     *
     * @since 7.1.0
     *
     * @return string
     */
    protected function getErrorMsgTemplate()
    {
        return '%s return type is not present in PHP version %s or earlier';
    }
}
