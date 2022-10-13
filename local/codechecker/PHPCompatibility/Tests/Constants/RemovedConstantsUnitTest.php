<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Constants;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedConstants sniff.
 *
 * @group removedConstants
 * @group constants
 *
 * @covers \PHPCompatibility\Sniffs\Constants\RemovedConstantsSniff
 *
 * @since 8.1.0
 */
class RemovedConstantsUnitTest extends BaseSniffTest
{

    /**
     * testDeprecatedConstant
     *
     * @dataProvider dataDeprecatedConstant
     *
     * @param string $constantName      Name of the PHP constant.
     * @param string $deprecatedIn      The PHP version in which the constant was deprecated.
     * @param array  $lines             The line numbers in the test file which apply to this constant.
     * @param string $okVersion         A PHP version in which the constant was still valid.
     * @param string $deprecatedVersion Optional PHP version to test deprecation message with -
     *                                  if different from the $deprecatedIn version.
     *
     * @return void
     */
    public function testDeprecatedConstant($constantName, $deprecatedIn, $lines, $okVersion, $deprecatedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($deprecatedVersion)) ? $deprecatedVersion : $deprecatedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The constant \"{$constantName}\" is deprecated since PHP {$deprecatedIn}";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedConstant()
     *
     * @return array
     */
    public function dataDeprecatedConstant()
    {
        return array(
            array('INTL_IDNA_VARIANT_2003', '7.2', array(16), '7.1'),
            array('FILTER_FLAG_SCHEME_REQUIRED', '7.3', array(73), '7.2'),
            array('FILTER_FLAG_HOST_REQUIRED', '7.3', array(74), '7.2'),
            array('CURLPIPE_HTTP1', '7.4', array(138), '7.3'),
        );
    }


    /**
     * testDeprecatedConstantWithAlternative
     *
     * @dataProvider dataDeprecatedConstantWithAlternative
     *
     * @param string $constantName      Name of the PHP constant.
     * @param string $deprecatedIn      The PHP version in which the constant was deprecated.
     * @param string $alternative       An alternative constant.
     * @param array  $lines             The line numbers in the test file which apply to this constant.
     * @param string $okVersion         A PHP version in which the constant was still valid.
     * @param string $deprecatedVersion Optional PHP version to test deprecation message with -
     *                                  if different from the $deprecatedIn version.
     *
     * @return void
     */
    public function testDeprecatedConstantWithAlternative($constantName, $deprecatedIn, $alternative, $lines, $okVersion, $deprecatedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($deprecatedVersion)) ? $deprecatedVersion : $deprecatedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The constant \"{$constantName}\" is deprecated since PHP {$deprecatedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedConstantWithAlternative()
     *
     * @return array
     */
    public function dataDeprecatedConstantWithAlternative()
    {
        return array(
            array('FILTER_SANITIZE_MAGIC_QUOTES', '7.4', 'FILTER_SANITIZE_ADD_SLASHES', array(137), '7.3'),
        );
    }


    /**
     * testRemovedConstant
     *
     * @dataProvider dataRemovedConstant
     *
     * @param string $constantName   Name of the PHP constant.
     * @param string $removedIn      The PHP version in which the constant was removed.
     * @param array  $lines          The line numbers in the test file which apply to this constant.
     * @param string $okVersion      A PHP version in which the constant was still valid.
     * @param string $removedVersion Optional PHP version to test removed message with -
     *                               if different from the $removedIn version.
     *
     * @return void
     */
    public function testRemovedConstant($constantName, $removedIn, $lines, $okVersion, $removedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($removedVersion)) ? $removedVersion : $removedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The constant \"{$constantName}\" is removed since PHP {$removedIn}";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testRemovedConstant()
     *
     * @return array
     */
    public function dataRemovedConstant()
    {
        return array(
            array('FILEINFO_COMPRESS', '5.3', array(8), '5.2'),
            array('CURLOPT_CLOSEPOLICY', '5.6', array(9), '5.5'),
            array('CURLCLOSEPOLICY_LEAST_RECENTLY_USED', '5.6', array(10), '5.5'),
            array('CURLCLOSEPOLICY_LEAST_TRAFFIC', '5.6', array(11), '5.5'),
            array('CURLCLOSEPOLICY_SLOWEST', '5.6', array(12), '5.5'),
            array('CURLCLOSEPOLICY_CALLBACK', '5.6', array(13), '5.5'),
            array('CURLCLOSEPOLICY_OLDEST', '5.6', array(14), '5.5'),
            array('PGSQL_ATTR_DISABLE_NATIVE_PREPARED_STATEMENT', '7.0', array(15), '5.6'),
            array('T_CHARACTER', '7.0', array(139), '5.6'),
            array('T_BAD_CHARACTER', '7.0', array(140), '5.6'),
            array('PHPDBG_FILE', '7.3', array(69), '7.2'),
            array('PHPDBG_METHOD', '7.3', array(70), '7.2'),
            array('PHPDBG_LINENO', '7.3', array(71), '7.2'),
            array('PHPDBG_FUNC', '7.3', array(72), '7.2'),

            array('IBASE_DEFAULT', '7.4', array(75), '7.3'),
            array('IBASE_READ', '7.4', array(76), '7.3'),
            array('IBASE_WRITE', '7.4', array(77), '7.3'),
            array('IBASE_CONSISTENCY', '7.4', array(78), '7.3'),
            array('IBASE_CONCURRENCY', '7.4', array(79), '7.3'),
            array('IBASE_COMMITTED', '7.4', array(80), '7.3'),
            array('IBASE_WAIT', '7.4', array(81), '7.3'),
            array('IBASE_NOWAIT', '7.4', array(82), '7.3'),
            array('IBASE_FETCH_BLOBS', '7.4', array(83), '7.3'),
            array('IBASE_FETCH_ARRAYS', '7.4', array(84), '7.3'),
            array('IBASE_UNIXTIME', '7.4', array(85), '7.3'),
            array('IBASE_BKP_IGNORE_CHECKSUMS', '7.4', array(86), '7.3'),
            array('IBASE_BKP_IGNORE_LIMBO', '7.4', array(87), '7.3'),
            array('IBASE_BKP_METADATA_ONLY', '7.4', array(88), '7.3'),
            array('IBASE_BKP_NO_GARBAGE_COLLECT', '7.4', array(89), '7.3'),
            array('IBASE_BKP_OLD_DESCRIPTIONS', '7.4', array(90), '7.3'),
            array('IBASE_BKP_NON_TRANSPORTABLE', '7.4', array(91), '7.3'),
            array('IBASE_BKP_CONVERT', '7.4', array(92), '7.3'),
            array('IBASE_RES_DEACTIVATE_IDX', '7.4', array(93), '7.3'),
            array('IBASE_RES_NO_SHADOW', '7.4', array(94), '7.3'),
            array('IBASE_RES_NO_VALIDITY', '7.4', array(95), '7.3'),
            array('IBASE_RES_ONE_AT_A_TIME', '7.4', array(96), '7.3'),
            array('IBASE_RES_REPLACE', '7.4', array(97), '7.3'),
            array('IBASE_RES_CREATE', '7.4', array(98), '7.3'),
            array('IBASE_RES_USE_ALL_SPACE', '7.4', array(99), '7.3'),
            array('IBASE_PRP_PAGE_BUFFERS', '7.4', array(100), '7.3'),
            array('IBASE_PRP_SWEEP_INTERVAL', '7.4', array(101), '7.3'),
            array('IBASE_PRP_SHUTDOWN_DB', '7.4', array(102), '7.3'),
            array('IBASE_PRP_DENY_NEW_TRANSACTIONS', '7.4', array(103), '7.3'),
            array('IBASE_PRP_DENY_NEW_ATTACHMENTS', '7.4', array(104), '7.3'),
            array('IBASE_PRP_RESERVE_SPACE', '7.4', array(105), '7.3'),
            array('IBASE_PRP_RES_USE_FULL', '7.4', array(106), '7.3'),
            array('IBASE_PRP_RES', '7.4', array(107), '7.3'),
            array('IBASE_PRP_WRITE_MODE', '7.4', array(108), '7.3'),
            array('IBASE_PRP_WM_ASYNC', '7.4', array(109), '7.3'),
            array('IBASE_PRP_WM_SYNC', '7.4', array(110), '7.3'),
            array('IBASE_PRP_ACCESS_MODE', '7.4', array(111), '7.3'),
            array('IBASE_PRP_AM_READONLY', '7.4', array(112), '7.3'),
            array('IBASE_PRP_AM_READWRITE', '7.4', array(113), '7.3'),
            array('IBASE_PRP_SET_SQL_DIALECT', '7.4', array(114), '7.3'),
            array('IBASE_PRP_ACTIVATE', '7.4', array(115), '7.3'),
            array('IBASE_PRP_DB_ONLINE', '7.4', array(116), '7.3'),
            array('IBASE_RPR_CHECK_DB', '7.4', array(117), '7.3'),
            array('IBASE_RPR_IGNORE_CHECKSUM', '7.4', array(118), '7.3'),
            array('IBASE_RPR_KILL_SHADOWS', '7.4', array(119), '7.3'),
            array('IBASE_RPR_MEND_DB', '7.4', array(120), '7.3'),
            array('IBASE_RPR_VALIDATE_DB', '7.4', array(121), '7.3'),
            array('IBASE_RPR_FULL', '7.4', array(122), '7.3'),
            array('IBASE_RPR_SWEEP_DB', '7.4', array(123), '7.3'),
            array('IBASE_STS_DATA_PAGES', '7.4', array(124), '7.3'),
            array('IBASE_STS_DB_LOG', '7.4', array(125), '7.3'),
            array('IBASE_STS_HDR_PAGES', '7.4', array(126), '7.3'),
            array('IBASE_STS_IDX_PAGES', '7.4', array(127), '7.3'),
            array('IBASE_STS_SYS_RELATIONS', '7.4', array(128), '7.3'),
            array('IBASE_SVC_SERVER_VERSION', '7.4', array(129), '7.3'),
            array('IBASE_SVC_IMPLEMENTATION', '7.4', array(130), '7.3'),
            array('IBASE_SVC_GET_ENV', '7.4', array(131), '7.3'),
            array('IBASE_SVC_GET_ENV_LOCK', '7.4', array(132), '7.3'),
            array('IBASE_SVC_GET_ENV_MSG', '7.4', array(133), '7.3'),
            array('IBASE_SVC_USER_DBPATH', '7.4', array(134), '7.3'),
            array('IBASE_SVC_SVR_DB_INFO', '7.4', array(135), '7.3'),
            array('IBASE_SVC_GET_USERS', '7.4', array(136), '7.3'),
        );
    }


    /**
     * testDeprecatedRemovedConstant
     *
     * @dataProvider dataDeprecatedRemovedConstant
     *
     * @param string $constantName      Name of the PHP constant.
     * @param string $deprecatedIn      The PHP version in which the constant was deprecated.
     * @param string $removedIn         The PHP version in which the constant was removed.
     * @param array  $lines             The line numbers in the test file which apply to this constant.
     * @param string $okVersion         A PHP version in which the constant was still valid.
     * @param string $deprecatedVersion Optional PHP version to test deprecation message with -
     *                                  if different from the $deprecatedIn version.
     * @param string $removedVersion    Optional PHP version to test removed message with -
     *                                  if different from the $removedIn version.
     *
     * @return void
     */
    public function testDeprecatedRemovedConstant($constantName, $deprecatedIn, $removedIn, $lines, $okVersion, $deprecatedVersion = null, $removedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($deprecatedVersion)) ? $deprecatedVersion : $deprecatedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The constant \"{$constantName}\" is deprecated since PHP {$deprecatedIn}";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $errorVersion = (isset($removedVersion)) ? $removedVersion : $removedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The constant \"{$constantName}\" is deprecated since PHP {$deprecatedIn} and removed since PHP {$removedIn}";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedRemovedConstant()
     *
     * @return array
     */
    public function dataDeprecatedRemovedConstant()
    {
        return array(
            array('MCRYPT_MODE_ECB', '7.1', '7.2', array(17), '7.0'),
            array('MCRYPT_MODE_CBC', '7.1', '7.2', array(18), '7.0'),
            array('MCRYPT_MODE_CFB', '7.1', '7.2', array(19), '7.0'),
            array('MCRYPT_MODE_OFB', '7.1', '7.2', array(20), '7.0'),
            array('MCRYPT_MODE_NOFB', '7.1', '7.2', array(21), '7.0'),
            array('MCRYPT_MODE_STREAM', '7.1', '7.2', array(22), '7.0'),
            array('MCRYPT_ENCRYPT', '7.1', '7.2', array(23), '7.0'),
            array('MCRYPT_DECRYPT', '7.1', '7.2', array(24), '7.0'),
            array('MCRYPT_DEV_RANDOM', '7.1', '7.2', array(25), '7.0'),
            array('MCRYPT_DEV_URANDOM', '7.1', '7.2', array(26), '7.0'),
            array('MCRYPT_RAND', '7.1', '7.2', array(27), '7.0'),
            array('MCRYPT_3DES', '7.1', '7.2', array(28), '7.0'),
            array('MCRYPT_ARCFOUR_IV', '7.1', '7.2', array(29), '7.0'),
            array('MCRYPT_ARCFOUR', '7.1', '7.2', array(30), '7.0'),
            array('MCRYPT_BLOWFISH', '7.1', '7.2', array(31), '7.0'),
            array('MCRYPT_CAST_128', '7.1', '7.2', array(32), '7.0'),
            array('MCRYPT_CAST_256', '7.1', '7.2', array(33), '7.0'),
            array('MCRYPT_CRYPT', '7.1', '7.2', array(34), '7.0'),
            array('MCRYPT_DES', '7.1', '7.2', array(35), '7.0'),
            array('MCRYPT_DES_COMPAT', '7.1', '7.2', array(36), '7.0'),
            array('MCRYPT_ENIGMA', '7.1', '7.2', array(37), '7.0'),
            array('MCRYPT_GOST', '7.1', '7.2', array(38), '7.0'),
            array('MCRYPT_IDEA', '7.1', '7.2', array(39), '7.0'),
            array('MCRYPT_LOKI97', '7.1', '7.2', array(40), '7.0'),
            array('MCRYPT_MARS', '7.1', '7.2', array(41), '7.0'),
            array('MCRYPT_PANAMA', '7.1', '7.2', array(42), '7.0'),
            array('MCRYPT_RIJNDAEL_128', '7.1', '7.2', array(43), '7.0'),
            array('MCRYPT_RIJNDAEL_192', '7.1', '7.2', array(44), '7.0'),
            array('MCRYPT_RIJNDAEL_256', '7.1', '7.2', array(45), '7.0'),
            array('MCRYPT_RC2', '7.1', '7.2', array(46), '7.0'),
            array('MCRYPT_RC4', '7.1', '7.2', array(47), '7.0'),
            array('MCRYPT_RC6', '7.1', '7.2', array(48), '7.0'),
            array('MCRYPT_RC6_128', '7.1', '7.2', array(49), '7.0'),
            array('MCRYPT_RC6_192', '7.1', '7.2', array(50), '7.0'),
            array('MCRYPT_RC6_256', '7.1', '7.2', array(51), '7.0'),
            array('MCRYPT_SAFER64', '7.1', '7.2', array(52), '7.0'),
            array('MCRYPT_SAFER128', '7.1', '7.2', array(53), '7.0'),
            array('MCRYPT_SAFERPLUS', '7.1', '7.2', array(54), '7.0'),
            array('MCRYPT_SERPENT', '7.1', '7.2', array(55), '7.0'),
            array('MCRYPT_SERPENT_128', '7.1', '7.2', array(56), '7.0'),
            array('MCRYPT_SERPENT_192', '7.1', '7.2', array(57), '7.0'),
            array('MCRYPT_SERPENT_256', '7.1', '7.2', array(58), '7.0'),
            array('MCRYPT_SKIPJACK', '7.1', '7.2', array(59), '7.0'),
            array('MCRYPT_TEAN', '7.1', '7.2', array(60), '7.0'),
            array('MCRYPT_THREEWAY', '7.1', '7.2', array(61), '7.0'),
            array('MCRYPT_TRIPLEDES', '7.1', '7.2', array(62), '7.0'),
            array('MCRYPT_TWOFISH', '7.1', '7.2', array(63), '7.0'),
            array('MCRYPT_TWOFISH128', '7.1', '7.2', array(64), '7.0'),
            array('MCRYPT_TWOFISH192', '7.1', '7.2', array(65), '7.0'),
            array('MCRYPT_TWOFISH256', '7.1', '7.2', array(66), '7.0'),
            array('MCRYPT_WAKE', '7.1', '7.2', array(67), '7.0'),
            array('MCRYPT_XTEA', '7.1', '7.2', array(68), '7.0'),
        );
    }


    /**
     * Test constants that shouldn't be flagged by this sniff.
     *
     * These are either userland constants or namespaced constants.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest deprecation.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(3),
            array(4),
            array(5),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.0'); // Low version below the first deprecation.
        $this->assertNoViolation($file);
    }
}
