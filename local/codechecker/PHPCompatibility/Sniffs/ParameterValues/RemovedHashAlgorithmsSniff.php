<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ParameterValues;

use PHPCompatibility\AbstractRemovedFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect the use of deprecated and removed hash algorithms.
 *
 * PHP version 5.4
 *
 * @link https://www.php.net/manual/en/function.hash-algos.php#refsect1-function.hash-algos-changelog
 *
 * @since 5.5
 * @since 7.1.0 Now extends the `AbstractRemovedFeatureSniff` instead of the base `Sniff` class.
 */
class RemovedHashAlgorithmsSniff extends AbstractRemovedFeatureSniff
{

    /**
     * A list of removed hash algorithms, which were present in older versions.
     *
     * The array lists : version number with false (deprecated) and true (removed).
     * If's sufficient to list the first version where the hash algorithm was deprecated/removed.
     *
     * @since 7.0.7
     *
     * @var array(string => array(string => bool))
     */
    protected $removedAlgorithms = array(
        'salsa10' => array(
            '5.4' => true,
        ),
        'salsa20' => array(
            '5.4' => true,
        ),
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
        return array(\T_STRING);
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
        $algo = $this->getHashAlgorithmParameter($phpcsFile, $stackPtr);
        if (empty($algo) || \is_string($algo) === false) {
            return;
        }

        // Bow out if not one of the algorithms we're targetting.
        if (isset($this->removedAlgorithms[$algo]) === false) {
            return;
        }

        $itemInfo = array(
            'name' => $algo,
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
        return $this->removedAlgorithms[$itemInfo['name']];
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
        return 'The %s hash algorithm is ';
    }
}
