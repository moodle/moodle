<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\TypeCasts;

use PHPCompatibility\AbstractRemovedFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect use of deprecated/removed type casts.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/language.types.type-juggling.php#language.types.typecasting
 * @link https://wiki.php.net/rfc/deprecations_php_7_2#unset_cast
 * @link https://wiki.php.net/rfc/deprecations_php_7_4#the_real_type
 *
 * @since 8.0.1
 * @since 9.0.0 Renamed from `DeprecatedTypeCastsSniff` to `RemovedTypeCastsSniff`.
 */
class RemovedTypeCastsSniff extends AbstractRemovedFeatureSniff
{
    /**
     * A list of deprecated and removed type casts with their alternatives.
     *
     * The array lists : version number with false (deprecated) or true (removed) and an alternative function.
     * If no alternative exists, it is NULL, i.e, the function should just not be used.
     *
     * @since 8.0.1
     *
     * @var array(string => array(string => bool|string))
     */
    protected $deprecatedTypeCasts = array(
        'T_UNSET_CAST' => array(
            '7.2'         => false,
            'alternative' => 'unset()',
            'description' => 'unset',
        ),
        'T_DOUBLE_CAST' => array(
            '7.4'         => false,
            'alternative' => '(float)',
            'description' => 'real',
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 8.0.1
     *
     * @return array
     */
    public function register()
    {
        $tokens = array();
        foreach ($this->deprecatedTypeCasts as $token => $versions) {
            $tokens[] = constant($token);
        }

        return $tokens;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 8.0.1
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $tokenType = $tokens[$stackPtr]['type'];

        // Special case `T_DOUBLE_CAST` as the same token is used for (float) and (double) casts.
        if ($tokenType === 'T_DOUBLE_CAST' && strpos($tokens[$stackPtr]['content'], 'real') === false) {
            // Float/double casts, not (real) cast.
            return;
        }

        $itemInfo = array(
            'name'        => $tokenType,
            'description' => $this->deprecatedTypeCasts[$tokenType]['description'],
        );
        $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
    }


    /**
     * Get an array of the non-PHP-version array keys used in a sub-array.
     *
     * @since 8.0.1
     *
     * @return array
     */
    protected function getNonVersionArrayKeys()
    {
        return array('description', 'alternative');
    }

    /**
     * Get the relevant sub-array for a specific item from a multi-dimensional array.
     *
     * @since 8.0.1
     *
     * @param array $itemInfo Base information about the item.
     *
     * @return array Version and other information about the item.
     */
    public function getItemArray(array $itemInfo)
    {
        return $this->deprecatedTypeCasts[$itemInfo['name']];
    }


    /**
     * Get the error message template for this sniff.
     *
     * @since 8.0.1
     *
     * @return string
     */
    protected function getErrorMsgTemplate()
    {
        return 'The %s cast is ';
    }


    /**
     * Filter the error data before it's passed to PHPCS.
     *
     * @since 8.0.1
     *
     * @param array $data      The error data array which was created.
     * @param array $itemInfo  Base information about the item this error message applies to.
     * @param array $errorInfo Detail information about an item this error message applies to.
     *
     * @return array
     */
    protected function filterErrorData(array $data, array $itemInfo, array $errorInfo)
    {
        $data[0] = $itemInfo['description'];
        return $data;
    }
}
