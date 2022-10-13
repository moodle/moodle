<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionUse;

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect use of new function parameters in calls to native PHP functions.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/doc.changelog.php
 *
 * @since 7.0.0
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 */
class NewFunctionParametersSniff extends AbstractNewFeatureSniff
{
    /**
     * A list of functions which have new parameters, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * The index is the location of the parameter in the parameter list, starting at 0 !
     * If's sufficient to list the first version where the function appears.
     *
     * @since 7.0.0
     * @since 7.0.2 Visibility changed from `public` to `protected`.
     *
     * @var array
     */
    protected $newFunctionParameters = array(
        'array_filter' => array(
            2 => array(
                'name' => 'flag',
                '5.5'  => false,
                '5.6'  => true,
            ),
        ),
        'array_slice' => array(
            1 => array(
                'name'  => 'preserve_keys',
                '5.0.1' => false,
                '5.0.2' => true,
            ),
        ),
        'array_unique' => array(
            1 => array(
                'name'  => 'sort_flags',
                '5.2.8' => false,
                '5.2.9' => true,
            ),
        ),
        'assert' => array(
            1 => array(
                'name'  => 'description',
                '5.4.7' => false,
                '5.4.8' => true,
            ),
        ),
        'base64_decode' => array(
            1 => array(
                'name' => 'strict',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'bcmod' => array(
            2 => array(
                'name' => 'scale',
                '7.1'  => false,
                '7.2'  => true,
            ),
        ),
        'class_implements' => array(
            1 => array(
                'name' => 'autoload',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'class_parents' => array(
            1 => array(
                'name' => 'autoload',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'clearstatcache' => array(
            0 => array(
                'name' => 'clear_realpath_cache',
                '5.2'  => false,
                '5.3'  => true,
            ),
            1 => array(
                'name' => 'filename',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'copy' => array(
            2 => array(
                'name' => 'context',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'curl_multi_info_read' => array(
            1 => array(
                'name' => 'msgs_in_queue',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'debug_backtrace' => array(
            0 => array(
                'name'  => 'options',
                '5.2.4' => false,
                '5.2.5' => true,
            ),
            1 => array(
                'name' => 'limit',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'debug_print_backtrace' => array(
            0 => array(
                'name'  => 'options',
                '5.3.5' => false,
                '5.3.6' => true,
            ),
            1 => array(
                'name' => 'limit',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'dirname' => array(
            1 => array(
                'name' => 'levels',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'dns_get_record' => array(
            4 => array(
                'name' => 'raw',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'fgetcsv' => array(
            4 => array(
                'name' => 'escape',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'fputcsv' => array(
            4 => array(
                'name'  => 'escape_char',
                '5.5.3' => false,
                '5.5.4' => true,
            ),
        ),
        'file_get_contents' => array(
            3 => array(
                'name' => 'offset',
                '5.0'  => false,
                '5.1'  => true,
            ),
            4 => array(
                'name' => 'maxlen',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'filter_input_array' => array(
            2 => array(
                'name' => 'add_empty',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'filter_var_array' => array(
            2 => array(
                'name' => 'add_empty',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'getenv' => array(
            1 => array(
                'name'   => 'local_only',
                '5.5.37' => false,
                '5.5.38' => true, // Also introduced in PHP 5.6.24 and 7.0.9.
            ),
        ),
        'getopt' => array(
            2 => array(
                'name' => 'optind',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'gettimeofday' => array(
            0 => array(
                'name' => 'return_float',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'get_defined_functions' => array(
            0 => array(
                'name'   => 'exclude_disabled',
                '7.0.14' => false,
                '7.0.15' => true,
            ),
        ),
        'get_headers' => array(
            2 => array(
                'name' => 'context',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'get_html_translation_table' => array(
            2 => array(
                'name'  => 'encoding',
                '5.3.3' => false,
                '5.3.4' => true,
            ),
        ),
        'get_loaded_extensions' => array(
            0 => array(
                'name'  => 'zend_extensions',
                '5.2.3' => false,
                '5.2.4' => true,
            ),
        ),
        'gzcompress' => array(
            2 => array(
                'name' => 'encoding',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'gzdeflate' => array(
            2 => array(
                'name' => 'encoding',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'htmlentities' => array(
            3 => array(
                'name'  => 'double_encode',
                '5.2.2' => false,
                '5.2.3' => true,
            ),
        ),
        'htmlspecialchars' => array(
            3 => array(
                'name'  => 'double_encode',
                '5.2.2' => false,
                '5.2.3' => true,
            ),
        ),
        'http_build_query' => array(
            2 => array(
                'name'  => 'arg_separator',
                '5.1.1' => false,
                '5.1.2' => true,
            ),
            3 => array(
                'name' => 'enc_type',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'idn_to_ascii' => array(
            2 => array(
                'name' => 'variant',
                '5.3'  => false,
                '5.4'  => true,
            ),
            3 => array(
                'name' => 'idna_info',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'idn_to_utf8' => array(
            2 => array(
                'name' => 'variant',
                '5.3'  => false,
                '5.4'  => true,
            ),
            3 => array(
                'name' => 'idna_info',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'imagecolorset' => array(
            5 => array(
                'name' => 'alpha',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'imagepng' => array(
            2 => array(
                'name'  => 'quality',
                '5.1.1' => false,
                '5.1.2' => true,
            ),
            3 => array(
                'name'  => 'filters',
                '5.1.2' => false,
                '5.1.3' => true,
            ),
        ),
        'imagerotate' => array(
            3 => array(
                'name' => 'ignore_transparent',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'imap_open' => array(
            4 => array(
                'name' => 'n_retries',
                '5.1'  => false,
                '5.2'  => true,
            ),
            5 => array(
                'name'  => 'params',
                '5.3.1' => false,
                '5.3.2' => true,
            ),
        ),
        'imap_reopen' => array(
            3 => array(
                'name' => 'n_retries',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'ini_get_all' => array(
            1 => array(
                'name' => 'details',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'is_a' => array(
            2 => array(
                'name'  => 'allow_string',
                '5.3.8' => false,
                '5.3.9' => true,
            ),
        ),
        'is_subclass_of' => array(
            2 => array(
                'name'  => 'allow_string',
                '5.3.8' => false,
                '5.3.9' => true,
            ),
        ),
        'iterator_to_array' => array(
            1 => array(
                'name'  => 'use_keys',
                '5.2.0' => false,
                '5.2.1' => true,
            ),
        ),
        'json_decode' => array(
            2 => array(
                'name' => 'depth',
                '5.2'  => false,
                '5.3'  => true,
            ),
            3 => array(
                'name' => 'options',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'json_encode' => array(
            1 => array(
                'name' => 'options',
                '5.2'  => false,
                '5.3'  => true,
            ),
            2 => array(
                'name' => 'depth',
                '5.4'  => false,
                '5.5'  => true,
            ),
        ),
        'ldap_add' => array(
            3 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_compare' => array(
            4 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_delete' => array(
            2 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_list' => array(
            8 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_mod_add' => array(
            3 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_mod_del' => array(
            3 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_mod_replace' => array(
            3 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_modify_batch' => array(
            3 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_parse_result' => array(
            6 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_read' => array(
            8 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_rename' => array(
            5 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'ldap_search' => array(
            8 => array(
                'name' => 'serverctrls',
                '7.2'  => false,
                '7.3'  => true,
            ),
        ),
        'memory_get_peak_usage' => array(
            0 => array(
                'name' => 'real_usage',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'memory_get_usage' => array(
            0 => array(
                'name' => 'real_usage',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'mb_encode_numericentity' => array(
            3 => array(
                'name' => 'is_hex',
                '5.3'  => false,
                '5.4'  => true,
            ),
        ),
        'mb_strrpos' => array(
            /*
             * Note: the actual position is 2, but the original 3rd
             * parameter 'encoding' was moved to the 4th position.
             * So the only way to detect if offset is used is when
             * both offset and encoding are set.
             */
            3 => array(
                'name' => 'offset',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'mssql_connect' => array(
            3 => array(
                'name' => 'new_link',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'mysqli_commit' => array(
            1 => array(
                'name' => 'flags',
                '5.4'  => false,
                '5.5'  => true,
            ),
            2 => array(
                'name' => 'name',
                '5.4'  => false,
                '5.5'  => true,
            ),
        ),
        'mysqli_rollback' => array(
            1 => array(
                'name' => 'flags',
                '5.4'  => false,
                '5.5'  => true,
            ),
            2 => array(
                'name' => 'name',
                '5.4'  => false,
                '5.5'  => true,
            ),
        ),
        'nl2br' => array(
            1 => array(
                'name' => 'is_xhtml',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'openssl_decrypt' => array(
            4 => array(
                'name'  => 'iv',
                '5.3.2' => false,
                '5.3.3' => true,
            ),
            5 => array(
                'name' => 'tag',
                '7.0'  => false,
                '7.1'  => true,
            ),
            6 => array(
                'name' => 'aad',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'openssl_encrypt' => array(
            4 => array(
                'name'  => 'iv',
                '5.3.2' => false,
                '5.3.3' => true,
            ),
            5 => array(
                'name' => 'tag',
                '7.0'  => false,
                '7.1'  => true,
            ),
            6 => array(
                'name' => 'aad',
                '7.0'  => false,
                '7.1'  => true,
            ),
            7 => array(
                'name' => 'tag_length',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'openssl_open' => array(
            4 => array(
                'name' => 'method',
                '5.2'  => false,
                '5.3'  => true,
            ),
            5 => array(
                'name' => 'iv',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'openssl_pkcs7_verify' => array(
            5 => array(
                'name' => 'content',
                '5.0'  => false,
                '5.1'  => true,
            ),
            6 => array(
                'name' => 'p7bfilename',
                '7.1'  => false,
                '7.2'  => true,
            ),
        ),
        'openssl_seal' => array(
            4 => array(
                'name' => 'method',
                '5.2'  => false,
                '5.3'  => true,
            ),
            5 => array(
                'name' => 'iv',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'openssl_verify' => array(
            3 => array(
                'name' => 'signature_alg',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'parse_ini_file' => array(
            2 => array(
                'name' => 'scanner_mode',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'parse_url' => array(
            1 => array(
                'name'  => 'component',
                '5.1.1' => false,
                '5.1.2' => true,
            ),
        ),
        'pg_fetch_all' => array(
            1 => array(
                'name' => 'result_type',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'pg_last_notice' => array(
            1 => array(
                'name' => 'option',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'pg_lo_create' => array(
            1 => array(
                'name' => 'object_id',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'pg_lo_import' => array(
            2 => array(
                'name' => 'object_id',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'pg_select' => array(
            4 => array(
                'name' => 'result_type',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'php_uname' => array(
            0 => array(
                'name' => 'mode',
                '4.2'  => false,
                '4.3'  => true,
            ),
        ),
        'preg_replace' => array(
            4 => array(
                'name' => 'count',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'preg_replace_callback' => array(
            4 => array(
                'name' => 'count',
                '5.0'  => false,
                '5.1'  => true,
            ),
            5 => array(
                'name' => 'flags',
                '7.3'  => false,
                '7.4'  => true,
            ),
        ),
        'preg_replace_callback_array' => array(
            4 => array(
                'name' => 'flags',
                '7.3'  => false,
                '7.4'  => true,
            ),
        ),
        'round' => array(
            2 => array(
                'name' => 'mode',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'sem_acquire' => array(
            1 => array(
                'name'  => 'nowait',
                '5.6'   => false,
                '5.6.1' => true,
            ),
        ),
        'session_regenerate_id' => array(
            0 => array(
                'name' => 'delete_old_session',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'session_set_cookie_params' => array(
            4 => array(
                'name' => 'httponly',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'session_set_save_handler' => array(
            6 => array(
                'name'  => 'create_sid',
                '5.5.0' => false,
                '5.5.1' => true,
            ),
            7 => array(
                'name' => 'validate_sid',
                '5.6'  => false,
                '7.0'  => true,
            ),
            8 => array(
                'name' => 'update_timestamp',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'session_start' => array(
            0 => array(
                'name' => 'options',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'setcookie' => array(
            6 => array(
                'name' => 'httponly',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'setrawcookie' => array(
            6 => array(
                'name' => 'httponly',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'simplexml_load_file' => array(
            4 => array(
                'name' => 'is_prefix',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'simplexml_load_string' => array(
            4 => array(
                'name' => 'is_prefix',
                '5.1'  => false,
                '5.2'  => true,
            ),
        ),
        'spl_autoload_register' => array(
            2 => array(
                'name' => 'prepend',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'stream_context_create' => array(
            1 => array(
                'name' => 'params',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'stream_copy_to_stream' => array(
            3 => array(
                'name' => 'offset',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'stream_get_contents' => array(
            2 => array(
                'name' => 'offset',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'stream_wrapper_register' => array(
            2 => array(
                'name'  => 'flags',
                '5.2.3' => false,
                '5.2.4' => true,
            ),
        ),
        'stristr' => array(
            2 => array(
                'name' => 'before_needle',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'strstr' => array(
            2 => array(
                'name' => 'before_needle',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'str_word_count' => array(
            2 => array(
                'name' => 'charlist',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'substr_count' => array(
            2 => array(
                'name' => 'offset',
                '5.0'  => false,
                '5.1'  => true,
            ),
            3 => array(
                'name' => 'length',
                '5.0'  => false,
                '5.1'  => true,
            ),
        ),
        'sybase_connect' => array(
            5 => array(
                'name' => 'new',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'timezone_transitions_get' => array(
            1 => array(
                'name' => 'timestamp_begin',
                '5.2'  => false,
                '5.3'  => true,
            ),
            2 => array(
                'name' => 'timestamp_end',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'timezone_identifiers_list' => array(
            0 => array(
                'name' => 'what',
                '5.2'  => false,
                '5.3'  => true,
            ),
            1 => array(
                'name' => 'country',
                '5.2'  => false,
                '5.3'  => true,
            ),
        ),
        'token_get_all' => array(
            1 => array(
                'name' => 'flags',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
        'ucwords' => array(
            1 => array(
                'name'   => 'delimiters',
                '5.4.31' => false,
                '5.5.15' => false,
                '5.4.32' => true,
                '5.5.16' => true,
            ),
        ),
        'unpack' => array(
            2 => array(
                'name' => 'offset',
                '7.0'  => false,
                '7.1'  => true,
            ),
        ),
        'unserialize' => array(
            1 => array(
                'name' => 'options',
                '5.6'  => false,
                '7.0'  => true,
            ),
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array
     */
    public function register()
    {
        // Handle case-insensitivity of function names.
        $this->newFunctionParameters = $this->arrayKeysToLowercase($this->newFunctionParameters);

        return array(\T_STRING);
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

        $ignore = array(
            \T_DOUBLE_COLON    => true,
            \T_OBJECT_OPERATOR => true,
            \T_FUNCTION        => true,
            \T_CONST           => true,
        );

        $prevToken = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($ignore[$tokens[$prevToken]['code']]) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function   = $tokens[$stackPtr]['content'];
        $functionLc = strtolower($function);

        if (isset($this->newFunctionParameters[$functionLc]) === false) {
            return;
        }

        $parameterCount = $this->getFunctionCallParameterCount($phpcsFile, $stackPtr);
        if ($parameterCount === 0) {
            return;
        }

        // If the parameter count returned > 0, we know there will be valid open parenthesis.
        $openParenthesis      = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true, null, true);
        $parameterOffsetFound = $parameterCount - 1;

        foreach ($this->newFunctionParameters[$functionLc] as $offset => $parameterDetails) {
            if ($offset <= $parameterOffsetFound) {
                $itemInfo = array(
                    'name'   => $function,
                    'nameLc' => $functionLc,
                    'offset' => $offset,
                );
                $this->handleFeature($phpcsFile, $openParenthesis, $itemInfo);
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
        return $this->newFunctionParameters[$itemInfo['nameLc']][$itemInfo['offset']];
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
        return array('name');
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
        $errorInfo              = parent::getErrorInfo($itemArray, $itemInfo);
        $errorInfo['paramName'] = $itemArray['name'];

        return $errorInfo;
    }


    /**
     * Get the item name to be used for the creation of the error code.
     *
     * @since 7.1.0
     *
     * @param array $itemInfo  Base information about the item.
     * @param array $errorInfo Detail information about an item.
     *
     * @return string
     */
    protected function getItemName(array $itemInfo, array $errorInfo)
    {
        return $itemInfo['name'] . '_' . $errorInfo['paramName'];
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
        return 'The function %s() does not have a parameter "%s" in PHP version %s or earlier';
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
        array_shift($data);
        array_unshift($data, $itemInfo['name'], $errorInfo['paramName']);
        return $data;
    }
}
