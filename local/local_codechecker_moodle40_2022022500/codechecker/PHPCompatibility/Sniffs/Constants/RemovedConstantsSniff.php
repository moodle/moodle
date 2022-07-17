<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Constants;

use PHPCompatibility\AbstractRemovedFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect use of deprecated and/or removed PHP native global constants.
 *
 * PHP version All
 *
 * @since 8.1.0
 */
class RemovedConstantsSniff extends AbstractRemovedFeatureSniff
{

    /**
     * A list of removed PHP Constants.
     *
     * The array lists : version number with false (deprecated) or true (removed).
     * If's sufficient to list the first version where the constant was deprecated/removed.
     *
     * Optional, the array can contain an `alternative` key listing an alternative constant
     * to be used instead.
     *
     * Note: PHP Constants are case-sensitive!
     *
     * @since 8.1.0
     *
     * @var array(string => array(string => bool|string))
     */
    protected $removedConstants = array(
        // Disabled since PHP 5.3.0 due to thread safety issues.
        'FILEINFO_COMPRESS' => array(
            '5.3' => true,
        ),

        'CURLOPT_CLOSEPOLICY' => array(
            '5.6' => true,
        ),
        'CURLCLOSEPOLICY_LEAST_RECENTLY_USED' => array(
            '5.6' => true,
        ),
        'CURLCLOSEPOLICY_LEAST_TRAFFIC' => array(
            '5.6' => true,
        ),
        'CURLCLOSEPOLICY_SLOWEST' => array(
            '5.6' => true,
        ),
        'CURLCLOSEPOLICY_CALLBACK' => array(
            '5.6' => true,
        ),
        'CURLCLOSEPOLICY_OLDEST' => array(
            '5.6' => true,
        ),

        'PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT' => array(
            '7.0' => true,
        ),
        'T_CHARACTER' => array(
            '7.0' => true,
        ),
        'T_BAD_CHARACTER' => array(
            '7.0' => true,
        ),

        'INTL_IDNA_VARIANT_2003' => array(
            '7.2' => false,
        ),

        'MCRYPT_MODE_ECB' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MODE_CBC' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MODE_CFB' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MODE_OFB' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MODE_NOFB' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MODE_STREAM' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_ENCRYPT' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_DECRYPT' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_DEV_RANDOM' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_DEV_URANDOM' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RAND' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_3DES' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_ARCFOUR_IV' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_ARCFOUR' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_BLOWFISH' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_CAST_128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_CAST_256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_CRYPT' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_DES' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_DES_COMPAT' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_ENIGMA' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_GOST' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_IDEA' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_LOKI97' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_MARS' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_PANAMA' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RIJNDAEL_128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RIJNDAEL_192' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RIJNDAEL_256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC2' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC4' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC6' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC6_128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC6_192' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_RC6_256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SAFER64' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SAFER128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SAFERPLUS' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SERPENT' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SERPENT_128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SERPENT_192' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SERPENT_256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_SKIPJACK' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TEAN' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_THREEWAY' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TRIPLEDES' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TWOFISH' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TWOFISH128' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TWOFISH192' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_TWOFISH256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_WAKE' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'MCRYPT_XTEA' => array(
            '7.1' => false,
            '7.2' => true,
        ),

        'PHPDBG_FILE' => array(
            '7.3' => true,
        ),
        'PHPDBG_METHOD' => array(
            '7.3' => true,
        ),
        'PHPDBG_LINENO' => array(
            '7.3' => true,
        ),
        'PHPDBG_FUNC' => array(
            '7.3' => true,
        ),
        'FILTER_FLAG_SCHEME_REQUIRED' => array(
            '7.3' => false,
        ),
        'FILTER_FLAG_HOST_REQUIRED' => array(
            '7.3' => false,
        ),

        'CURLPIPE_HTTP1' => array(
            '7.4' => false,
        ),
        'FILTER_SANITIZE_MAGIC_QUOTES' => array(
            '7.4'         => false,
            'alternative' => 'FILTER_SANITIZE_ADD_SLASHES',
        ),
        'IBASE_BKP_CONVERT' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_IGNORE_CHECKSUMS' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_IGNORE_LIMBO' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_METADATA_ONLY' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_NO_GARBAGE_COLLECT' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_NON_TRANSPORTABLE' => array(
            '7.4' => true,
        ),
        'IBASE_BKP_OLD_DESCRIPTIONS' => array(
            '7.4' => true,
        ),
        'IBASE_COMMITTED' => array(
            '7.4' => true,
        ),
        'IBASE_CONCURRENCY' => array(
            '7.4' => true,
        ),
        'IBASE_CONSISTENCY' => array(
            '7.4' => true,
        ),
        'IBASE_DEFAULT' => array(
            '7.4' => true,
        ),
        'IBASE_FETCH_ARRAYS' => array(
            '7.4' => true,
        ),
        'IBASE_FETCH_BLOBS' => array(
            '7.4' => true,
        ),
        'IBASE_NOWAIT' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_ACCESS_MODE' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_ACTIVATE' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_AM_READONLY' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_AM_READWRITE' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_DENY_NEW_ATTACHMENTS' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_DENY_NEW_TRANSACTIONS' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_DB_ONLINE' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_PAGE_BUFFERS' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_RES' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_RES_USE_FULL' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_RESERVE_SPACE' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_SET_SQL_DIALECT' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_SHUTDOWN_DB' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_SWEEP_INTERVAL' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_WM_ASYNC' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_WM_SYNC' => array(
            '7.4' => true,
        ),
        'IBASE_PRP_WRITE_MODE' => array(
            '7.4' => true,
        ),
        'IBASE_READ' => array(
            '7.4' => true,
        ),
        'IBASE_RES_CREATE' => array(
            '7.4' => true,
        ),
        'IBASE_RES_DEACTIVATE_IDX' => array(
            '7.4' => true,
        ),
        'IBASE_RES_NO_SHADOW' => array(
            '7.4' => true,
        ),
        'IBASE_RES_NO_VALIDITY' => array(
            '7.4' => true,
        ),
        'IBASE_RES_ONE_AT_A_TIME' => array(
            '7.4' => true,
        ),
        'IBASE_RES_REPLACE' => array(
            '7.4' => true,
        ),
        'IBASE_RES_USE_ALL_SPACE' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_CHECK_DB' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_FULL' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_IGNORE_CHECKSUM' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_KILL_SHADOWS' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_MEND_DB' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_SWEEP_DB' => array(
            '7.4' => true,
        ),
        'IBASE_RPR_VALIDATE_DB' => array(
            '7.4' => true,
        ),
        'IBASE_STS_DATA_PAGES' => array(
            '7.4' => true,
        ),
        'IBASE_STS_DB_LOG' => array(
            '7.4' => true,
        ),
        'IBASE_STS_HDR_PAGES' => array(
            '7.4' => true,
        ),
        'IBASE_STS_IDX_PAGES' => array(
            '7.4' => true,
        ),
        'IBASE_STS_SYS_RELATIONS' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_GET_ENV' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_GET_ENV_LOCK' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_GET_ENV_MSG' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_GET_USERS' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_IMPLEMENTATION' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_SERVER_VERSION' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_SVR_DB_INFO' => array(
            '7.4' => true,
        ),
        'IBASE_SVC_USER_DBPATH' => array(
            '7.4' => true,
        ),
        'IBASE_UNIXTIME' => array(
            '7.4' => true,
        ),
        'IBASE_WAIT' => array(
            '7.4' => true,
        ),
        'IBASE_WRITE' => array(
            '7.4' => true,
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 8.1.0
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
     * @since 8.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $constantName = $tokens[$stackPtr]['content'];

        if (isset($this->removedConstants[$constantName]) === false) {
            return;
        }

        if ($this->isUseOfGlobalConstant($phpcsFile, $stackPtr) === false) {
            return;
        }

        $itemInfo = array(
            'name' => $constantName,
        );
        $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
    }


    /**
     * Get the relevant sub-array for a specific item from a multi-dimensional array.
     *
     * @since 8.1.0
     *
     * @param array $itemInfo Base information about the item.
     *
     * @return array Version and other information about the item.
     */
    public function getItemArray(array $itemInfo)
    {
        return $this->removedConstants[$itemInfo['name']];
    }


    /**
     * Get the error message template for this sniff.
     *
     * @since 8.1.0
     *
     * @return string
     */
    protected function getErrorMsgTemplate()
    {
        return 'The constant "%s" is ';
    }
}
