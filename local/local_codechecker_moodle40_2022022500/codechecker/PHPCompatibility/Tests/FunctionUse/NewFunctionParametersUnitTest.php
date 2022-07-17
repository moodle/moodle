<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionUse;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewFunctionParameters sniff.
 *
 * @group newFunctionParameters
 * @group functionUse
 *
 * @covers \PHPCompatibility\Sniffs\FunctionUse\NewFunctionParametersSniff
 *
 * @since 7.0.0
 */
class NewFunctionParametersUnitTest extends BaseSniffTest
{

    /**
     * testInvalidParameter
     *
     * @dataProvider dataInvalidParameter
     *
     * @param string $functionName      Function name.
     * @param string $parameterName     Parameter name.
     * @param string $lastVersionBefore The PHP version just *before* the parameter was introduced.
     * @param array  $lines             The line numbers in the test file which apply to this function parameter.
     * @param string $okVersion         A PHP version in which the parameter was ok to be used.
     * @param string $testVersion       Optional PHP version to use for testing the flagged case.
     *
     * @return void
     */
    public function testInvalidParameter($functionName, $parameterName, $lastVersionBefore, $lines, $okVersion, $testVersion = null)
    {
        $errorVersion = (isset($testVersion)) ? $testVersion : $lastVersionBefore;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The function {$functionName}() does not have a parameter \"{$parameterName}\" in PHP version {$lastVersionBefore} or earlier";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testInvalidParameter()
     *
     * @return array
     */
    public function dataInvalidParameter()
    {
        return array(
            array('array_filter', 'flag', '5.5', array(11), '5.6'),
            array('array_slice', 'preserve_keys', '5.0.1', array(12), '5.1', '5.0'),
            array('array_unique', 'sort_flags', '5.2.8', array(13), '5.3', '5.2'),
            array('assert', 'description', '5.4.7', array(14), '5.5', '5.4'),
            array('base64_decode', 'strict', '5.1', array(15), '5.2'),
            array('bcmod', 'scale', '7.1', array(96), '7.2'),
            array('class_implements', 'autoload', '5.0', array(16), '5.1'),
            array('class_parents', 'autoload', '5.0', array(17), '5.1'),
            array('clearstatcache', 'clear_realpath_cache', '5.2', array(18), '5.3'),
            array('clearstatcache', 'filename', '5.2', array(18), '5.3'),
            array('copy', 'context', '5.2', array(19), '5.3'),
            array('curl_multi_info_read', 'msgs_in_queue', '5.1', array(20), '5.2'),
            array('debug_backtrace', 'options', '5.2.4', array(21), '5.4', '5.2'),
            array('debug_backtrace', 'limit', '5.3', array(21), '5.4'),
            array('debug_print_backtrace', 'options', '5.3.5', array(22), '5.4', '5.3'),
            array('debug_print_backtrace', 'limit', '5.3', array(22), '5.4'),
            array('dirname', 'levels', '5.6', array(23), '7.0'),
            array('dns_get_record', 'raw', '5.3', array(24), '5.4'),
            array('fgetcsv', 'escape', '5.2', array(25), '5.3'),
            array('fputcsv', 'escape_char', '5.5.3', array(26), '5.6', '5.5'),
            array('file_get_contents', 'offset', '5.0', array(27), '5.1'),
            array('file_get_contents', 'maxlen', '5.0', array(27), '5.1'),
            array('filter_input_array', 'add_empty', '5.3', array(28), '5.4'),
            array('filter_var_array', 'add_empty', '5.3', array(29), '5.4'),
            array('getenv', 'local_only', '5.5.37', array(105), '5.6', '5.5'),
            array('getopt', 'optind', '7.0', array(98), '7.1'),
            array('gettimeofday', 'return_float', '5.0', array(30), '5.1'),
            array('get_defined_functions', 'exclude_disabled', '7.0.14', array(95), '7.1', '7.0'),
            array('get_headers', 'context', '7.0', array(97), '7.1'),
            array('get_html_translation_table', 'encoding', '5.3.3', array(31), '5.4', '5.3'),
            array('get_loaded_extensions', 'zend_extensions', '5.2.3', array(32), '5.3', '5.2'),
            array('gzcompress', 'encoding', '5.3', array(33), '5.4'),
            array('gzdeflate', 'encoding', '5.3', array(34), '5.4'),
            array('htmlentities', 'double_encode', '5.2.2', array(35), '5.3', '5.2'),
            array('htmlspecialchars', 'double_encode', '5.2.2', array(36), '5.3', '5.2'),
            array('http_build_query', 'arg_separator', '5.1.1', array(37), '5.4', '5.1'),
            array('http_build_query', 'enc_type', '5.3', array(37), '5.4'),
            array('idn_to_ascii', 'variant', '5.3', array(38), '5.4'),
            array('idn_to_ascii', 'idna_info', '5.3', array(38), '5.4'),
            array('idn_to_utf8', 'variant', '5.3', array(39), '5.4'),
            array('idn_to_utf8', 'idna_info', '5.3', array(39), '5.4'),
            array('imagecolorset', 'alpha', '5.3', array(40), '5.4'),
            array('imagepng', 'quality', '5.1.1', array(41), '5.2', '5.1'),
            array('imagepng', 'filters', '5.1.2', array(41), '5.2', '5.1'),
            array('imagerotate', 'ignore_transparent', '5.0', array(42), '5.1'),
            array('imap_open', 'n_retries', '5.1', array(43), '5.4'),
            array('imap_open', 'params', '5.3.1', array(43), '5.4', '5.3'),
            array('imap_reopen', 'n_retries', '5.1', array(44), '5.2'),
            array('ini_get_all', 'details', '5.2', array(45), '5.3'),
            array('is_a', 'allow_string', '5.3.8', array(46), '5.4', '5.3'),
            array('is_subclass_of', 'allow_string', '5.3.8', array(47), '5.4', '5.3'),
            array('iterator_to_array', 'use_keys', '5.2.0', array(48), '5.3', '5.2'), // Function introduced in 5.2.1.
            array('json_decode', 'depth', '5.2', array(49), '5.4'), // OK version > version in which last parameter was added to the function.
            array('json_decode', 'options', '5.3', array(49), '5.4'),
            array('json_encode', 'options', '5.2', array(50), '5.5'), // OK version > version in which last parameter was added to the function.
            array('json_encode', 'depth', '5.4', array(50), '5.5'),
            array('ldap_add', 'serverctrls', '7.2', array(106), '7.3'),
            array('ldap_compare', 'serverctrls', '7.2', array(107), '7.3'),
            array('ldap_delete', 'serverctrls', '7.2', array(108), '7.3'),
            array('ldap_list', 'serverctrls', '7.2', array(109), '7.3'),
            array('ldap_mod_add', 'serverctrls', '7.2', array(110), '7.3'),
            array('ldap_mod_del', 'serverctrls', '7.2', array(111), '7.3'),
            array('ldap_mod_replace', 'serverctrls', '7.2', array(112), '7.3'),
            array('ldap_modify_batch', 'serverctrls', '7.2', array(113), '7.3'),
            array('ldap_parse_result', 'serverctrls', '7.2', array(114), '7.3'),
            array('ldap_read', 'serverctrls', '7.2', array(115), '7.3'),
            array('ldap_rename', 'serverctrls', '7.2', array(116), '7.3'),
            array('ldap_search', 'serverctrls', '7.2', array(117), '7.3'),
            array('memory_get_peak_usage', 'real_usage', '5.1', array(51), '5.2'),
            array('memory_get_usage', 'real_usage', '5.1', array(52), '5.2'),
            array('mb_encode_numericentity', 'is_hex', '5.3', array(53), '5.4'),
            array('mb_strrpos', 'offset', '5.1', array(54), '5.2'),
            array('mssql_connect', 'new_link', '5.0', array(55), '5.1'),
            array('mysqli_commit', 'flags', '5.4', array(56), '5.5'),
            array('mysqli_commit', 'name', '5.4', array(56), '5.5'),
            array('mysqli_rollback', 'flags', '5.4', array(57), '5.5'),
            array('mysqli_rollback', 'name', '5.4', array(57), '5.5'),
            array('nl2br', 'is_xhtml', '5.2', array(58), '5.3'),
            array('openssl_decrypt', 'iv', '5.3.2', array(59), '7.1', '5.3'), // OK version > version in which last parameter was added to the function.
            array('openssl_decrypt', 'tag', '7.0', array(59), '7.1'),
            array('openssl_decrypt', 'aad', '7.0', array(59), '7.1'),
            array('openssl_encrypt', 'iv', '5.3.2', array(60), '7.1', '5.3'), // OK version > version in which last parameter was added to the function.
            array('openssl_encrypt', 'tag', '7.0', array(60), '7.1'),
            array('openssl_encrypt', 'aad', '7.0', array(60), '7.1'),
            array('openssl_encrypt', 'tag_length', '7.0', array(60), '7.1'),
            array('openssl_open', 'method', '5.2', array(103), '7.0'), // OK version > version in which last parameter was added to the function.
            array('openssl_open', 'iv', '5.6', array(103), '7.0'),
            array('openssl_pkcs7_verify', 'content', '5.0', array(61), '7.2'), // OK version > version in which last parameter was added to the function.
            array('openssl_pkcs7_verify', 'p7bfilename', '7.1', array(61), '7.2'),
            array('openssl_seal', 'method', '5.2', array(62), '7.0'), // OK version > version in which last parameter was added to the function.
            array('openssl_seal', 'iv', '5.6', array(62), '7.0'),
            array('openssl_verify', 'signature_alg', '5.1', array(63), '5.2'),
            array('parse_ini_file', 'scanner_mode', '5.2', array(64), '5.3'),
            array('parse_url', 'component', '5.1.1', array(65), '5.2', '5.1'),
            array('pg_fetch_all', 'result_type', '7.0', array(99), '7.1'),
            array('pg_last_notice', 'option', '7.0', array(100), '7.1'),
            array('pg_lo_create', 'object_id', '5.2', array(66), '5.3'),
            array('pg_lo_import', 'object_id', '5.2', array(67), '5.3'),
            array('pg_select', 'result_type', '7.0', array(101), '7.1'),
            array('php_uname', 'mode', '4.2', array(104), '4.3'),
            array('preg_replace', 'count', '5.0', array(68), '5.1'),
            array('preg_replace_callback', 'count', '5.0', array(69), '7.4'), // OK version > version in which last parameter was added to the function.
            array('preg_replace_callback', 'flags', '7.3', array(69), '7.4'),
            array('preg_replace_callback_array', 'flags', '7.3', array(118), '7.4'),
            array('round', 'mode', '5.2', array(70), '5.3'),
            array('sem_acquire', 'nowait', '5.6', array(71), '7.0'),
            array('session_regenerate_id', 'delete_old_session', '5.0', array(72), '5.1'),
            array('session_set_cookie_params', 'httponly', '5.1', array(73), '5.2'),
            array('session_set_save_handler', 'create_sid', '5.5.0', array(74), '7.0', '5.5'), // OK version > version in which last parameter was added to the function.
            array('session_set_save_handler', 'validate_sid', '5.6', array(74), '7.0'),
            array('session_set_save_handler', 'update_timestamp', '5.6', array(74), '7.0'),
            array('session_start', 'options', '5.6', array(75), '7.0'),
            array('setcookie', 'httponly', '5.1', array(76), '5.2'),
            array('setrawcookie', 'httponly', '5.1', array(77), '5.2'),
            array('simplexml_load_file', 'is_prefix', '5.1', array(78), '5.2'),
            array('simplexml_load_string', 'is_prefix', '5.1', array(79), '5.2'),
            array('spl_autoload_register', 'prepend', '5.2', array(80), '5.3'),
            array('stream_context_create', 'params', '5.2', array(81), '5.3'),
            array('stream_copy_to_stream', 'offset', '5.0', array(82), '5.1'),
            array('stream_get_contents', 'offset', '5.0', array(83), '5.1'),
            array('stream_wrapper_register', 'flags', '5.2.3', array(84), '5.3', '5.2'),
            array('stristr', 'before_needle', '5.2', array(85), '5.3'),
            array('strstr', 'before_needle', '5.2', array(86), '5.3'),
            array('str_word_count', 'charlist', '5.0', array(87), '5.1'),
            array('substr_count', 'offset', '5.0', array(88), '5.1'),
            array('substr_count', 'length', '5.0', array(88), '5.1'),
            array('sybase_connect', 'new', '5.2', array(89), '5.3'),
            array('timezone_transitions_get', 'timestamp_begin', '5.2', array(90), '5.3'),
            array('timezone_transitions_get', 'timestamp_end', '5.2', array(90), '5.3'),
            array('timezone_identifiers_list', 'what', '5.2', array(91), '5.3'),
            array('timezone_identifiers_list', 'country', '5.2', array(91), '5.3'),
            array('token_get_all', 'flags', '5.6', array(92), '7.0'),
            array('ucwords', 'delimiters', '5.4.31', array(93), '5.6', '5.4'), // Function introduced in 5.4.31 and 5.5.15.
            array('unpack', 'offset', '7.0', array(102), '7.1'),
            array('unserialize', 'options', '5.6', array(94), '7.0'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.0'); // Low version below the first addition.
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
            array(4),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond newest addition.
        $this->assertNoViolation($file);
    }
}
