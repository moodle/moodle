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
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;

/**
 * Detect and verify the use of parameter type declarations in function declarations.
 *
 * Parameter type declarations - class/interface names only - is available since PHP 5.0.
 * - Since PHP 5.1, the `array` keyword can be used.
 * - Since PHP 5.2, `self` and `parent` can be used. Previously, those were interpreted as
 *   class names.
 * - Since PHP 5.4, the `callable` keyword.
 * - Since PHP 7.0, scalar type declarations are available.
 * - Since PHP 7.1, the `iterable` pseudo-type is available.
 * - Since PHP 7.2, the generic `object` type is available.
 *
 * Additionally, this sniff does a cursory check for typical invalid type declarations,
 * such as:
 * - `boolean` (should be `bool`), `integer` (should be `int`) and `static`.
 * - `self`/`parent` as type declaration used outside class context throws a fatal error since PHP 7.0.
 *
 * PHP version 5.0+
 *
 * @link https://www.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration
 * @link https://wiki.php.net/rfc/callable
 * @link https://wiki.php.net/rfc/scalar_type_hints_v5
 * @link https://wiki.php.net/rfc/iterable
 * @link https://wiki.php.net/rfc/object-typehint
 *
 * @since 7.0.0
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class.
 * @since 9.0.0 Renamed from `NewScalarTypeDeclarationsSniff` to `NewParamTypeDeclarationsSniff`.
 */
class NewParamTypeDeclarationsSniff extends AbstractNewFeatureSniff
{

    /**
     * A list of new types.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the keyword appears.
     *
     * @since 7.0.0
     * @since 7.0.3 Now lists all param type declarations, not just the PHP 7+ scalar ones.
     *
     * @var array(string => array(string => bool))
     */
    protected $newTypes = array(
        'array' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'self' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'parent' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'callable' => array(
            '5.3' => false,
            '5.4' => true,
        ),
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
        'iterable' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'object' => array(
            '7.1' => false,
            '7.2' => true,
        ),
    );


    /**
     * Invalid types
     *
     * The array lists : the invalid type hint => what was probably intended/alternative.
     *
     * @since 7.0.3
     *
     * @var array(string => string)
     */
    protected $invalidTypes = array(
        'static'  => 'self',
        'boolean' => 'bool',
        'integer' => 'int',
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     * @since 7.1.3 Now also checks closures.
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
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     * @since 7.0.3 - Added check for non-scalar type declarations.
     *              - Added check for invalid type declarations.
     *              - Added check for usage of `self` type declaration outside
     *                class scope.
     * @since 8.2.0 Added check for `parent` type declaration outside class scope.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Get all parameters from method signature.
        $paramNames = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);
        if (empty($paramNames)) {
            return;
        }

        $supportsPHP4 = $this->supportsBelow('4.4');

        foreach ($paramNames as $param) {
            if ($param['type_hint'] === '') {
                continue;
            }

            // Strip off potential nullable indication.
            $typeHint = ltrim($param['type_hint'], '?');

            if ($supportsPHP4 === true) {
                $phpcsFile->addError(
                    'Type declarations were not present in PHP 4.4 or earlier.',
                    $param['token'],
                    'TypeHintFound'
                );

            } elseif (isset($this->newTypes[$typeHint])) {
                $itemInfo = array(
                    'name' => $typeHint,
                );
                $this->handleFeature($phpcsFile, $param['token'], $itemInfo);

                // As of PHP 7.0, using `self` or `parent` outside class scope throws a fatal error.
                // Only throw this error for PHP 5.2+ as before that the "type hint not supported" error
                // will be thrown.
                if (($typeHint === 'self' || $typeHint === 'parent')
                    && $this->inClassScope($phpcsFile, $stackPtr, false) === false
                    && $this->supportsAbove('5.2') !== false
                ) {
                    $phpcsFile->addError(
                        "'%s' type cannot be used outside of class scope",
                        $param['token'],
                        ucfirst($typeHint) . 'OutsideClassScopeFound',
                        array($typeHint)
                    );
                }
            } elseif (isset($this->invalidTypes[$typeHint])) {
                $error = "'%s' is not a valid type declaration. Did you mean %s ?";
                $data  = array(
                    $typeHint,
                    $this->invalidTypes[$typeHint],
                );

                $phpcsFile->addError($error, $param['token'], 'InvalidTypeHintFound', $data);
            }
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
        return "'%s' type declaration is not present in PHP version %s or earlier";
    }
}
