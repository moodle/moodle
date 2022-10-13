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

use PHPCompatibility\AbstractRemovedFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect calls to deprecated/removed native PHP functions.
 *
 * Suggests alternative if available.
 *
 * PHP version All
 *
 * @since 5.5
 * @since 5.6   Now extends the base `Sniff` class instead of the upstream
 *              `Generic.PHP.ForbiddenFunctions` sniff.
 * @since 7.1.0 Now extends the `AbstractRemovedFeatureSniff` instead of the base `Sniff` class.
 * @since 9.0.0 Renamed from `DeprecatedFunctionsSniff` to `RemovedFunctionsSniff`.
 */
class RemovedFunctionsSniff extends AbstractRemovedFeatureSniff
{
    /**
     * A list of deprecated and removed functions with their alternatives.
     *
     * The array lists : version number with false (deprecated) or true (removed) and an alternative function.
     * If no alternative exists, it is NULL, i.e, the function should just not be used.
     *
     * @since 5.5
     * @since 5.6   Visibility changed from `protected` to `public`.
     * @since 7.0.2 Visibility changed back from `public` to `protected`.
     *              The earlier change was made to be in line with the upstream sniff,
     *              but that sniff is no longer being extended.
     * @since 7.0.8 Property renamed from `$forbiddenFunctions` to `$removedFunctions`.
     *
     * @var array(string => array(string => bool|string|null))
     */
    protected $removedFunctions = array(
        'php_check_syntax' => array(
            '5.0.5' => true,
            'alternative' => null,
        ),

        'pfpro_cleanup' => array(
            '5.1' => true,
            'alternative' => null,
        ),
        'pfpro_init' => array(
            '5.1' => true,
            'alternative' => null,
        ),
        'pfpro_process_raw' => array(
            '5.1' => true,
            'alternative' => null,
        ),
        'pfpro_process' => array(
            '5.1' => true,
            'alternative' => null,
        ),
        'pfpro_version' => array(
            '5.1' => true,
            'alternative' => null,
        ),

        'call_user_method' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'call_user_func()',
        ),
        'call_user_method_array' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'call_user_func_array()',
        ),
        'define_syslog_variables' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => null,
        ),
        'dl' => array(
            '5.3' => false,
            'alternative' => null,
        ),
        'ereg' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_match()',
        ),
        'ereg_replace' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_replace()',
        ),
        'eregi' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_match()',
        ),
        'eregi_replace' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_replace()',
        ),
        'imagepsbbox' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepsencodefont' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepsextendfont' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepsfreefont' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepsloadfont' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepsslantfont' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'imagepstext' => array(
            '7.0' => true,
            'alternative' => null,
        ),
        'import_request_variables' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => null,
        ),
        'ldap_sort' => array(
            '7.0' => false,
            'alternative' => null,
        ),
        'mcrypt_generic_end' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'mcrypt_generic_deinit()',
        ),
        'mysql_db_query' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'mysqli::select_db() and mysqli::query()',
        ),
        'mysql_escape_string' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'mysqli::real_escape_string()',
        ),
        'mysql_list_dbs' => array(
            '5.4' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'mysqli_bind_param' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt::bind_param()',
        ),
        'mysqli_bind_result' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt::bind_result()',
        ),
        'mysqli_client_encoding' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli::character_set_name()',
        ),
        'mysqli_fetch' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt::fetch()',
        ),
        'mysqli_param_count' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt_param_count()',
        ),
        'mysqli_get_metadata' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt::result_metadata()',
        ),
        'mysqli_send_long_data' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => 'mysqli_stmt::send_long_data()',
        ),
        'magic_quotes_runtime' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'session_register' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => '$_SESSION',
        ),
        'session_unregister' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => '$_SESSION',
        ),
        'session_is_registered' => array(
            '5.3' => false,
            '5.4' => true,
            'alternative' => '$_SESSION',
        ),
        'set_magic_quotes_runtime' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'set_socket_blocking' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'stream_set_blocking()',
        ),
        'split' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_split()',
        ),
        'spliti' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => 'preg_split()',
        ),
        'sql_regcase' => array(
            '5.3' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'php_logo_guid' => array(
            '5.5' => true,
            'alternative' => null,
        ),
        'php_egg_logo_guid' => array(
            '5.5' => true,
            'alternative' => null,
        ),
        'php_real_logo_guid' => array(
            '5.5' => true,
            'alternative' => null,
        ),
        'zend_logo_guid' => array(
            '5.5' => true,
            'alternative' => null,
        ),
        'datefmt_set_timezone_id' => array(
            '5.5' => false,
            '7.0' => true,
            'alternative' => 'IntlDateFormatter::setTimeZone()',
        ),
        'mcrypt_ecb' => array(
            '5.5' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'mcrypt_cbc' => array(
            '5.5' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'mcrypt_cfb' => array(
            '5.5' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'mcrypt_ofb' => array(
            '5.5' => false,
            '7.0' => true,
            'alternative' => null,
        ),
        'ocibindbyname' => array(
            '5.4' => false,
            'alternative' => 'oci_bind_by_name()',
        ),
        'ocicancel' => array(
            '5.4' => false,
            'alternative' => 'oci_cancel()',
        ),
        'ocicloselob' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::close()',
        ),
        'ocicollappend' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::append()',
        ),
        'ocicollassign' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::assign()',
        ),
        'ocicollassignelem' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::assignElem()',
        ),
        'ocicollgetelem' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::getElem()',
        ),
        'ocicollmax' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::max()',
        ),
        'ocicollsize' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::size()',
        ),
        'ocicolltrim' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::trim()',
        ),
        'ocicolumnisnull' => array(
            '5.4' => false,
            'alternative' => 'oci_field_is_null()',
        ),
        'ocicolumnname' => array(
            '5.4' => false,
            'alternative' => 'oci_field_name()',
        ),
        'ocicolumnprecision' => array(
            '5.4' => false,
            'alternative' => 'oci_field_precision()',
        ),
        'ocicolumnscale' => array(
            '5.4' => false,
            'alternative' => 'oci_field_scale()',
        ),
        'ocicolumnsize' => array(
            '5.4' => false,
            'alternative' => 'oci_field_size()',
        ),
        'ocicolumntype' => array(
            '5.4' => false,
            'alternative' => 'oci_field_type()',
        ),
        'ocicolumntyperaw' => array(
            '5.4' => false,
            'alternative' => 'oci_field_type_raw()',
        ),
        'ocicommit' => array(
            '5.4' => false,
            'alternative' => 'oci_commit()',
        ),
        'ocidefinebyname' => array(
            '5.4' => false,
            'alternative' => 'oci_define_by_name()',
        ),
        'ocierror' => array(
            '5.4' => false,
            'alternative' => 'oci_error()',
        ),
        'ociexecute' => array(
            '5.4' => false,
            'alternative' => 'oci_execute()',
        ),
        'ocifetch' => array(
            '5.4' => false,
            'alternative' => 'oci_fetch()',
        ),
        'ocifetchinto' => array(
            '5.4' => false,
            'alternative' => null,
        ),
        'ocifetchstatement' => array(
            '5.4' => false,
            'alternative' => 'oci_fetch_all()',
        ),
        'ocifreecollection' => array(
            '5.4' => false,
            'alternative' => 'OCI-Collection::free()',
        ),
        'ocifreecursor' => array(
            '5.4' => false,
            'alternative' => 'oci_free_statement()',
        ),
        'ocifreedesc' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::free()',
        ),
        'ocifreestatement' => array(
            '5.4' => false,
            'alternative' => 'oci_free_statement()',
        ),
        'ociinternaldebug' => array(
            '5.4' => false,
            'alternative' => 'oci_internal_debug()',
        ),
        'ociloadlob' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::load()',
        ),
        'ocilogoff' => array(
            '5.4' => false,
            'alternative' => 'oci_close()',
        ),
        'ocilogon' => array(
            '5.4' => false,
            'alternative' => 'oci_connect()',
        ),
        'ocinewcollection' => array(
            '5.4' => false,
            'alternative' => 'oci_new_collection()',
        ),
        'ocinewcursor' => array(
            '5.4' => false,
            'alternative' => 'oci_new_cursor()',
        ),
        'ocinewdescriptor' => array(
            '5.4' => false,
            'alternative' => 'oci_new_descriptor()',
        ),
        'ocinlogon' => array(
            '5.4' => false,
            'alternative' => 'oci_new_connect()',
        ),
        'ocinumcols' => array(
            '5.4' => false,
            'alternative' => 'oci_num_fields()',
        ),
        'ociparse' => array(
            '5.4' => false,
            'alternative' => 'oci_parse()',
        ),
        'ociplogon' => array(
            '5.4' => false,
            'alternative' => 'oci_pconnect()',
        ),
        'ociresult' => array(
            '5.4' => false,
            'alternative' => 'oci_result()',
        ),
        'ocirollback' => array(
            '5.4' => false,
            'alternative' => 'oci_rollback()',
        ),
        'ocirowcount' => array(
            '5.4' => false,
            'alternative' => 'oci_num_rows()',
        ),
        'ocisavelob' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::save()',
        ),
        'ocisavelobfile' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::import()',
        ),
        'ociserverversion' => array(
            '5.4' => false,
            'alternative' => 'oci_server_version()',
        ),
        'ocisetprefetch' => array(
            '5.4' => false,
            'alternative' => 'oci_set_prefetch()',
        ),
        'ocistatementtype' => array(
            '5.4' => false,
            'alternative' => 'oci_statement_type()',
        ),
        'ociwritelobtofile' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::export()',
        ),
        'ociwritetemporarylob' => array(
            '5.4' => false,
            'alternative' => 'OCI-Lob::writeTemporary()',
        ),
        'mysqli_get_cache_stats' => array(
            '5.4' => true,
            'alternative' => null,
        ),

        'mcrypt_create_iv' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'random_bytes() or OpenSSL',
        ),
        'mcrypt_decrypt' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_algorithms_name' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_block_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_iv_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_key_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_modes_name' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_get_supported_key_sizes' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_is_block_algorithm_mode' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_is_block_algorithm' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_is_block_mode' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_enc_self_test' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_encrypt' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_generic_deinit' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_generic_init' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_generic' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_get_block_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_get_cipher_name' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_get_iv_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_get_key_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_list_algorithms' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_list_modes' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_close' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_get_algo_block_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_get_algo_key_size' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_get_supported_key_sizes' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_is_block_algorithm_mode' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_is_block_algorithm' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_is_block_mode' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_open' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mcrypt_module_self_test' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'mdecrypt_generic' => array(
            '7.1' => false,
            '7.2' => true,
            'alternative' => 'OpenSSL',
        ),
        'jpeg2wbmp' => array(
            '7.2' => false,
            'alternative' => 'imagecreatefromjpeg() and imagewbmp()',
        ),
        'png2wbmp' => array(
            '7.2' => false,
            'alternative' => 'imagecreatefrompng() or imagewbmp()',
        ),
        'create_function' => array(
            '7.2' => false,
            'alternative' => 'an anonymous function',
        ),
        'each' => array(
            '7.2' => false,
            'alternative' => 'a foreach loop',
        ),
        'gmp_random' => array(
            '7.2' => false,
            'alternative' => 'gmp_random_bits() or gmp_random_range()',
        ),
        'read_exif_data' => array(
            '7.2' => false,
            'alternative' => 'exif_read_data()',
        ),

        'image2wbmp' => array(
            '7.3' => false,
            'alternative' => 'imagewbmp()',
        ),
        'mbregex_encoding' => array(
            '7.3' => false,
            'alternative' => 'mb_regex_encoding()',
        ),
        'mbereg' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg()',
        ),
        'mberegi' => array(
            '7.3' => false,
            'alternative' => 'mb_eregi()',
        ),
        'mbereg_replace' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_replace()',
        ),
        'mberegi_replace' => array(
            '7.3' => false,
            'alternative' => 'mb_eregi_replace()',
        ),
        'mbsplit' => array(
            '7.3' => false,
            'alternative' => 'mb_split()',
        ),
        'mbereg_match' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_match()',
        ),
        'mbereg_search' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search()',
        ),
        'mbereg_search_pos' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_pos()',
        ),
        'mbereg_search_regs' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_regs()',
        ),
        'mbereg_search_init' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_init()',
        ),
        'mbereg_search_getregs' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_getregs()',
        ),
        'mbereg_search_getpos' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_getpos()',
        ),
        'mbereg_search_setpos' => array(
            '7.3' => false,
            'alternative' => 'mb_ereg_search_setpos()',
        ),
        'fgetss' => array(
            '7.3' => false,
            'alternative' => null,
        ),
        'gzgetss' => array(
            '7.3' => false,
            'alternative' => null,
        ),

        'convert_cyr_string' => array(
            '7.4' => false,
            'alternative' => 'mb_convert_encoding(), iconv() or UConverter',
        ),
        'ezmlm_hash' => array(
            '7.4' => false,
            'alternative' => null,
        ),
        'get_magic_quotes_gpc' => array(
            '7.4' => false,
            'alternative' => null,
        ),
        'get_magic_quotes_runtime' => array(
            '7.4' => false,
            'alternative' => null,
        ),
        'hebrevc' => array(
            '7.4' => false,
            'alternative' => null,
        ),
        'is_real' => array(
            '7.4' => false,
            'alternative' => 'is_float()',
        ),
        'money_format' => array(
            '7.4' => false,
            'alternative' => 'NumberFormatter::formatCurrency()',
        ),
        'restore_include_path' => array(
            '7.4' => false,
            'alternative' => "ini_restore('include_path')",
        ),
        'ibase_add_user' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_affected_rows' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_backup' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_add' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_cancel' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_close' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_create' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_echo' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_get' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_import' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_info' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_blob_open' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_close' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_commit_ret' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_commit' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_connect' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_db_info' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_delete_user' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_drop_db' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_errcode' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_errmsg' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_execute' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_fetch_assoc' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_fetch_object' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_fetch_row' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_field_info' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_free_event_handler' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_free_query' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_free_result' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_gen_id' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_maintain_db' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_modify_user' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_name_result' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_num_fields' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_num_params' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_param_info' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_pconnect' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_prepare' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_query' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_restore' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_rollback_ret' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_rollback' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_server_info' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_service_attach' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_service_detach' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_set_event_handler' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_trans' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ibase_wait_event' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'ldap_control_paged_result_response' => array(
            '7.4' => false,
            'alternative' => 'ldap_search()',
        ),
        'ldap_control_paged_result' => array(
            '7.4' => false,
            'alternative' => 'ldap_search()',
        ),
        'recode_file' => array(
            '7.4' => true,
            'alternative' => 'the iconv or mbstring extension',
        ),
        'recode_string' => array(
            '7.4' => true,
            'alternative' => 'the iconv or mbstring extension',
        ),
        'recode' => array(
            '7.4' => true,
            'alternative' => 'the iconv or mbstring extension',
        ),
        'wddx_add_vars' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'wddx_deserialize' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'wddx_packet_end' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'wddx_packet_start' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'wddx_serialize_value' => array(
            '7.4' => true,
            'alternative' => null,
        ),
        'wddx_serialize_vars' => array(
            '7.4' => true,
            'alternative' => null,
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
        $this->removedFunctions = $this->arrayKeysToLowercase($this->removedFunctions);

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
            \T_CLASS           => true,
            \T_CONST           => true,
            \T_USE             => true,
            \T_NS_SEPARATOR    => true,
        );

        $prevToken = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($ignore[$tokens[$prevToken]['code']]) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function   = $tokens[$stackPtr]['content'];
        $functionLc = strtolower($function);

        if (isset($this->removedFunctions[$functionLc]) === false) {
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
        return $this->removedFunctions[$itemInfo['nameLc']];
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
        return 'Function %s() is ';
    }
}
