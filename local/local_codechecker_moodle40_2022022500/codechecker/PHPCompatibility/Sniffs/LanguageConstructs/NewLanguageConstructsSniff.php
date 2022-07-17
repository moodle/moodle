<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\LanguageConstructs;

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect use of new PHP language constructs.
 *
 * PHP version All
 *
 * @link https://wiki.php.net/rfc/namespaceseparator
 * @link https://wiki.php.net/rfc/variadics
 * @link https://wiki.php.net/rfc/argument_unpacking
 *
 * @since 5.6
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 * @since 9.0.0 Detection for new operator tokens has been moved to the `NewOperators` sniff.
 */
class NewLanguageConstructsSniff extends AbstractNewFeatureSniff
{

    /**
     * A list of new language constructs, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the keyword appears.
     *
     * @since 5.6
     *
     * @var array(string => array(string => bool|string))
     */
    protected $newConstructs = array(
        'T_NS_SEPARATOR' => array(
            '5.2' => false,
            '5.3' => true,
            'description' => 'the \ operator (for namespaces)',
        ),
        'T_ELLIPSIS' => array(
            '5.5' => false,
            '5.6' => true,
            'description' => 'the ... spread operator',
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.6
     *
     * @return array
     */
    public function register()
    {
        $tokens = array();
        foreach ($this->newConstructs as $token => $versions) {
            $tokens[] = constant($token);
        }
        return $tokens;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.6
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

        $itemInfo = array(
            'name' => $tokenType,
        );
        $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
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
        return $this->newConstructs[$itemInfo['name']];
    }


    /**
     * Get an array of the non-PHP-version array keys used in a sub-array.
     *
     * @since 7.1.0
     *
     * @return array
     */
    protected function getNonVersionArrayKeys()
    {
        return array('description');
    }


    /**
     * Retrieve the relevant detail (version) information for use in an error message.
     *
     * @since 7.1.0
     *
     * @param array $itemArray Version and other information about the item.
     * @param array $itemInfo  Base information about the item.
     *
     * @return array
     */
    public function getErrorInfo(array $itemArray, array $itemInfo)
    {
        $errorInfo                = parent::getErrorInfo($itemArray, $itemInfo);
        $errorInfo['description'] = $itemArray['description'];

        return $errorInfo;
    }


    /**
     * Allow for concrete child classes to filter the error data before it's passed to PHPCS.
     *
     * @since 7.1.0
     *
     * @param array $data      The error data array which was created.
     * @param array $itemInfo  Base information about the item this error message applies to.
     * @param array $errorInfo Detail information about an item this error message applies to.
     *
     * @return array
     */
    protected function filterErrorData(array $data, array $itemInfo, array $errorInfo)
    {
        $data[0] = $errorInfo['description'];
        return $data;
    }
}
