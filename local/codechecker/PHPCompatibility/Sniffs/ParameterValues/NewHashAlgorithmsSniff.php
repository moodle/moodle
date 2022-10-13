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

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect the use of newly introduced hash algorithms.
 *
 * PHP version 5.2+
 *
 * @link https://www.php.net/manual/en/function.hash-algos.php#refsect1-function.hash-algos-changelog
 *
 * @since 7.0.7
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 */
class NewHashAlgorithmsSniff extends AbstractNewFeatureSniff
{
    /**
     * A list of new hash algorithms, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the hash algorithm appears.
     *
     * @since 7.0.7
     *
     * @var array(string => array(string => bool))
     */
    protected $newAlgorithms = array(
        'md2' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'ripemd256' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'ripemd320' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'salsa10' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'salsa20' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'snefru256' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'sha224' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'joaat' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'fnv132' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'fnv164' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'gost-crypto' => array(
            '5.5' => false,
            '5.6' => true,
        ),

        'sha512/224' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sha512/256' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sha3-224' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sha3-256' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sha3-384' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sha3-512' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'crc32c' => array(
            '7.3' => false,
            '7.4' => true,
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.7
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
     * @since 7.0.7
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
        if (isset($this->newAlgorithms[$algo]) === false) {
            return;
        }

        // Check if the algorithm used is new.
        $itemInfo = array(
            'name'   => $algo,
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
        return $this->newAlgorithms[$itemInfo['name']];
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
        return 'The %s hash algorithm is not present in PHP version %s or earlier';
    }
}
