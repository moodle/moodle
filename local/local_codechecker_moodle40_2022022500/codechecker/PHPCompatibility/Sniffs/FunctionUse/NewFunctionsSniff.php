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

/**
 * Detect calls to new native PHP functions.
 *
 * PHP version All
 *
 * @since 5.5
 * @since 5.6   Now extends the base `Sniff` class instead of the upstream
 *              `Generic.PHP.ForbiddenFunctions` sniff.
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 */
class NewFunctionsSniff extends AbstractNewFeatureSniff
{
    /**
     * A list of new functions, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the function appears.
     *
     * @since 5.5
     * @since 5.6   Visibility changed from `protected` to `public`.
     * @since 7.0.2 Visibility changed back from `public` to `protected`.
     *              The earlier change was made to be in line with the upstream sniff,
     *              but that sniff is no longer being extended.
     * @since 7.0.8 Renamed from `$forbiddenFunctions` to the more descriptive `$newFunctions`.
     *
     * @var array(string => array(string => bool))
     */
    protected $newFunctions = array(
        'iterator_count' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'iterator_to_array' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload_call' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload_extensions' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload_functions' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload_register' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload_unregister' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'spl_autoload' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'hash_hmac' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),
        'array_fill_keys' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'error_get_last' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'image_type_to_extension' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'memory_get_peak_usage' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'sys_get_temp_dir' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'timezone_abbreviations_list' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'timezone_identifiers_list' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'timezone_name_from_abbr' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'stream_socket_shutdown' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'imagegrabscreen' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'imagegrabwindow' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'libxml_disable_entity_loader' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'mb_stripos' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'mb_stristr' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'mb_strrchr' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'mb_strrichr' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'mb_strripos' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'ming_setSWFCompression' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'openssl_csr_get_public_key' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'openssl_csr_get_subject' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'openssl_pkey_get_details' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'spl_object_hash' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'iterator_apply' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'preg_last_error' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'pg_field_table' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'posix_initgroups' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'gmp_nextprime' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'xmlwriter_full_end_element' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'xmlwriter_write_raw' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'xmlwriter_start_dtd_entity' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'xmlwriter_end_dtd_entity' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'xmlwriter_write_dtd_entity' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_has_var' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_id' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_input_array' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_input' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_list' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_var_array' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter_var' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'json_decode' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'json_encode' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_close' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_close' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_compressedsize' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_compressionmethod' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_filesize' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_name' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_open' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_entry_read' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_open' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'zip_read' => array(
            '5.1' => false,
            '5.2' => true,
        ),

        'array_replace' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'array_replace_recursive' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'class_alias' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'forward_static_call' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'forward_static_call_array' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gc_collect_cycles' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gc_disable' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gc_enable' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gc_enabled' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'get_called_class' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gethostname' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'header_remove' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'lcfirst' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'parse_ini_string' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'quoted_printable_encode' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'str_getcsv' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'stream_context_set_default' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'stream_supports_lock' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'stream_context_get_params' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_add' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_create_from_format' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_diff' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_get_last_errors' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_parse_from_format' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'date_sub' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'timezone_version_get' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'gmp_testbit' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'hash_copy' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'imap_gc' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'imap_utf8_to_mutf7' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'imap_mutf7_to_utf8' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'json_last_error' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli_get_cache_stats' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli_fetch_all' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli_get_connection_status' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli_poll' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli_read_async_query' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'openssl_random_pseudo_bytes' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'pcntl_signal_dispatch' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'pcntl_sigprocmask' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'pcntl_sigtimedwait' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'pcntl_sigwaitinfo' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'preg_filter' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'msg_queue_exists' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'shm_has_vars' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'acosh' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'asinh' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'atanh' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'expm1' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'log1p' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_describe' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_dict_exists' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_free_dict' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_free' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_get_error' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_init' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_list_dicts' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_request_dict' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_request_pwl_dict' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_broker_set_ordering' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_add_to_personal' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_add_to_session' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_check' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_describe' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_get_error' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_is_in_session' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_quick_check' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_store_replacement' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'enchant_dict_suggest' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo_buffer' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo_close' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo_file' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo_open' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo_set_flags' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl_error_name' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl_get_error_code' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl_get_error_message' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl_is_failure' => array(
            '5.2' => false,
            '5.3' => true,
        ),

        'hex2bin' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'http_response_code' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'get_declared_traits' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'getimagesizefromstring' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'stream_set_chunk_size' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'socket_import_stream' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'trait_exists' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'header_register_callback' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'class_uses' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session_status' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session_register_shutdown' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqli_error_list' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqli_stmt_error_list' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'libxml_set_external_entity_loader' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'ldap_control_paged_result' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'ldap_control_paged_result_response' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_create' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_create_from_rules' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_create_inverse' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_get_error_code' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_get_error_message' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_list_ids' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'transliteral_transliterate' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'zlib_decode' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'zlib_encode' => array(
            '5.3' => false,
            '5.4' => true,
        ),

        'array_column' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'boolval' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'json_last_error_msg' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'password_get_info' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'password_hash' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'password_needs_rehash' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'password_verify' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'hash_pbkdf2' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'openssl_pbkdf2' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_escape' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_file_create' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_multi_setopt' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_multi_strerror' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_pause' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_reset' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_share_close' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_share_init' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_share_setopt' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_strerror' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'curl_unescape' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imageaffinematrixconcat' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imageaffinematrixget' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imagecrop' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imagecropauto' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imageflip' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imagepalettetotruecolor' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'imagescale' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'mysqli_begin_transaction' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'mysqli_release_savepoint' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'mysqli_savepoint' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'pg_escape_literal' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'pg_escape_identifier' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'socket_sendmsg' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'socket_recvmsg' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'socket_cmsg_space' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'cli_get_process_title' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'cli_set_process_title' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'datefmt_format_object' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'datefmt_get_calendar_object' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'datefmt_get_timezone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'datefmt_set_timezone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_create_instance' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_keyword_values_for_locale' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_now' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_available_locales' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_set_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_add' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_set_time_zone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_after' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_before' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_set' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_roll' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_clear' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_field_difference' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_actual_maximum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_actual_minumum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_day_of_week_type' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_first_day_of_week' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_greatest_minimum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_least_maximum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_locale' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_maximum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_minimal_days_in_first_week' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_minimum' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_time_zone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_type' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_weekend_transition' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_in_daylight_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_is_equivalent_to' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_is_lenient' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_equals' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_repeated_wall_time_option' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_skipped_wall_time_option' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_set_repeated_wall_time_option' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_set_skipped_wall_time_option' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_from_date_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_to_date_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_error_code' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlcal_get_error_message' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlgregcal_create_instance' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlgregcal_set_gregorian_change' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlgregcal_get_gregorian_change' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlgregcal_is_leap_year' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_create_time_zone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_create_default' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_id' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_gmt' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_unknown' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_create_enumeration' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_count_equivalent_ids' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_create_time_zone_id_enumeration' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_canonical_id' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_region' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_tz_data_version' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_equivalent_id' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_use_daylight_time' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_offset' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_raw_offset' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_has_same_rules' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_display_name' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_dst_savings' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_from_date_time_zone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_to_date_time_zone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_error_code' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'intlz_get_error_message' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache_compile_file' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache_get_configuration' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache_get_status' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache_invalidate' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache_reset' => array(
            '5.4' => false,
            '5.5' => true,
        ),

        'opcache_is_script_cached' => array(
            '5.5.10' => false,
            '5.5.11' => true,
        ),

        'gmp_root' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'gmp_rootrem' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'hash_equals' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'ldap_escape' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'ldap_modify_batch' => array(
            '5.4.25' => false,
            '5.5.9'  => false,
            '5.4.26' => true,
            '5.5.10' => true,
            '5.6.0'  => true,
        ),
        'mysqli_get_links_stats' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_get_cert_locations' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_x509_fingerprint' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_spki_new' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_spki_verify' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_spki_export_challenge' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'openssl_spki_export' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'pg_connect_poll' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'pg_consume_input' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'pg_flush' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'pg_lo_truncate' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'pg_socket' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'session_abort' => array(
            '5.5' => false,
            '5.6' => true,
        ),
        'session_reset' => array(
            '5.5' => false,
            '5.6' => true,
        ),

        'random_bytes' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'random_int' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'error_clear_last' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'gmp_random_seed' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'intdiv' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'preg_replace_callback_array' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'gc_mem_caches' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'get_resources' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'posix_setrlimit' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'inflate_add' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'deflate_add' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'inflate_init' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'deflate_init' => array(
            '5.6' => false,
            '7.0' => true,
        ),

        'socket_export_stream' => array(
            '7.0.6' => false,
            '7.0.7' => true,
        ),

        'curl_multi_errno' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'curl_share_errno' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'curl_share_strerror' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'is_iterable' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'pcntl_async_signals' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'pcntl_signal_get_handler' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session_create_id' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session_gc' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sapi_windows_cp_set' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sapi_windows_cp_get' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sapi_windows_cp_is_utf8' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'sapi_windows_cp_conv' => array(
            '7.0' => false,
            '7.1' => true,
        ),

        'hash_hkdf' => array(
            '7.1.1' => false,
            '7.1.2' => true,
        ),
        'oci_register_taf_callback' => array(
            '7.1.6' => false,
            '7.1.7' => true,
        ),
        'oci_unregister_taf_callback' => array(
            '7.1.8' => false,
            '7.1.9' => true,
        ),

        'stream_isatty' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sapi_windows_vt100_support' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'ftp_append' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'hash_hmac_algos' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imagebmp' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imagecreatefrombmp' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imagegetclip' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imageopenpolygon' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imageresolution' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'imagesetclip' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'ldap_exop' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'ldap_exop_passwd' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'ldap_exop_whoami' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'ldap_parse_exop' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'mb_chr' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'mb_ord' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'mb_scrub' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'socket_addrinfo_lookup' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'socket_addrinfo_connect' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'socket_addrinfo_bind' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'socket_addrinfo_explain' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'spl_object_id' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_add' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_base642bin' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_bin2base64' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_bin2hex' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_compare' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_aes256gcm_decrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_aes256gcm_encrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_aes256gcm_is_available' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_aes256gcm_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_decrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_encrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_ietf_decrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_ietf_encrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_ietf_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_chacha20poly1305_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_xchacha20poly1305_ietf_decrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_aead_xchacha20poly1305_ietf_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_auth_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_auth_verify' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_auth' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_keypair_from_secretkey_and_publickey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_open' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_publickey_from_secretkey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_publickey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_seal_open' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_seal' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_secretkey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box_seed_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_box' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_generichash_final' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_generichash_init' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_generichash_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_generichash_update' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_generichash' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kdf_derive_from_key' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kdf_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_client_session_keys' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_publickey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_secretkey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_seed_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_kx_server_session_keys' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_scryptsalsa208sha256_str_verify' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_scryptsalsa208sha256_str' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_scryptsalsa208sha256' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_str_needs_rehash' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_str_verify' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash_str' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_pwhash' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_scalarmult_base' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_scalarmult' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretbox_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretbox_open' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretbox' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_init_pull' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_init_push' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_pull' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_push' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_secretstream_xchacha20poly1305_rekey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_shorthash_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_shorthash' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_detached' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_ed25519_pk_to_curve25519' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_ed25519_sk_to_curve25519' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_keypair_from_secretkey_and_publickey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_open' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_publickey_from_secretkey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_publickey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_secretkey' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_seed_keypair' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign_verify_detached' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_sign' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_stream_keygen' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_stream_xor' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_crypto_stream' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_hex2bin' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_increment' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_memcmp' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_memzero' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_pad' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        'sodium_unpad' => array(
            '7.1' => false,
            '7.2' => true,
        ),
        // Introduced in 7.2.14 and 7.3.1 simultanously.
        'oci_set_call_timeout' => array(
            '7.2.13' => false,
            '7.2.14' => true,
        ),
        // Introduced in 7.2.14 and 7.3.1 simultanously.
        'oci_set_db_operation' => array(
            '7.2.13' => false,
            '7.2.14' => true,
        ),

        'hrtime' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'is_countable' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'array_key_first' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'array_key_last' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'fpm_get_status' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'net_get_interfaces' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'gmp_binomial' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'gmp_lcm' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'gmp_perfect_power' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'gmp_kronecker' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_add_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_bind_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_delete_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_exop_refresh' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_mod_add_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_mod_replace_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_mod_del_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'ldap_rename_ext' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'normalizer_get_raw_decomposition' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'openssl_pkey_derive' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'socket_wsaprotocol_info_export' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'socket_wsaprotocol_info_import' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'socket_wsaprotocol_info_release' => array(
            '7.2' => false,
            '7.3' => true,
        ),

        'get_mangled_object_vars' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'imagecreatefromtga' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'mb_str_split' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'openssl_x509_verify' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'password_algos' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'pcntl_unshare' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'sapi_windows_set_ctrl_handler' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'sapi_windows_generate_ctrl_event' => array(
            '7.3' => false,
            '7.4' => true,
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
        // Handle case-insensitivity of function names.
        $this->newFunctions = $this->arrayKeysToLowercase($this->newFunctions);

        return array(\T_STRING);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
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

        } elseif ($tokens[$prevToken]['code'] === \T_NS_SEPARATOR && $tokens[$prevToken - 1]['code'] === \T_STRING) {
            // Namespaced function.
            return;
        }

        $function   = $tokens[$stackPtr]['content'];
        $functionLc = strtolower($function);

        if (isset($this->newFunctions[$functionLc]) === false) {
            return;
        }

        $itemInfo = array(
            'name'   => $function,
            'nameLc' => $functionLc,
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
        return $this->newFunctions[$itemInfo['nameLc']];
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
        return 'The function %s() is not present in PHP version %s or earlier';
    }
}
