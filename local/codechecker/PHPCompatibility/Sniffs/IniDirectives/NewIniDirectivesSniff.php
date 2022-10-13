<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\IniDirectives;

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect the use of new INI directives through `ini_set()` or `ini_get()`.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/ini.list.php
 * @link https://www.php.net/manual/en/ini.core.php
 *
 * @since 5.5
 * @since 7.0.7 When a new directive is used with `ini_set()`, the sniff will now throw an error
 *              instead of a warning.
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 */
class NewIniDirectivesSniff extends AbstractNewFeatureSniff
{
    /**
     * A list of new INI directives
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the ini directive appears.
     *
     * @since 5.5
     * @since 7.0.3 Support for 'alternative' has been added.
     *
     * @var array(string)
     */
    protected $newIniDirectives = array(
        'auto_globals_jit' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'com.code_page' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'date.default_latitude' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'date.default_longitude' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'date.sunrise_zenith' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'date.sunset_zenith' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'ibase.default_charset' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'ibase.default_db' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mail.force_extra_parameters' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mime_magic.debug' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.max_links' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.default_port' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.default_socket' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.default_host' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.default_user' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'mysqli.default_pw' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'report_zend_debug' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'session.hash_bits_per_character' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'session.hash_function' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'soap.wsdl_cache_dir' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'soap.wsdl_cache_enabled' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'soap.wsdl_cache_ttl' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'sqlite.assoc_case' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'tidy.clean_output' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'tidy.default_config' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'zend.ze1_compatibility_mode' => array(
            '4.4' => false,
            '5.0' => true,
        ),

        'date.timezone' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'detect_unicode' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'fbsql.batchsize' => array(
            '5.0'         => false,
            '5.1'         => true,
            'alternative' => 'fbsql.batchSize',
        ),
        'realpath_cache_size' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'realpath_cache_ttl' => array(
            '5.0' => false,
            '5.1' => true,
        ),

        'mbstring.strict_detection' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),
        'mssql.charset' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),

        'gd.jpeg_ignore_warning' => array(
            '5.1.2' => false,
            '5.1.3' => true,
        ),

        'fbsql.show_timestamp_decimals' => array(
            '5.1.4' => false,
            '5.1.5' => true,
        ),
        'soap.wsdl_cache' => array(
            '5.1.4' => false,
            '5.1.5' => true,
        ),
        'soap.wsdl_cache_limit' => array(
            '5.1.4' => false,
            '5.1.5' => true,
        ),

        'allow_url_include' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter.default' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'filter.default_flags' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'pcre.backtrack_limit' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'pcre.recursion_limit' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'session.cookie_httponly' => array(
            '5.1' => false,
            '5.2' => true,
        ),

        'cgi.check_shebang_line' => array(
            '5.2.0' => false,
            '5.2.1' => true,
        ),

        'max_input_nesting_level' => array(
            '5.2.2' => false,
            '5.2.3' => true,
        ),

        'mysqli.allow_local_infile' => array(
            '5.2.3' => false,
            '5.2.4' => true,
        ),

        'max_file_uploads' => array(
            '5.2.11' => false,
            '5.2.12' => true,
        ),

        'cgi.discard_path' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'exit_on_timeout' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl.default_locale' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'intl.error_level' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mail.add_x_header' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mail.log' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mbstring.http_output_conv_mimetype' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli.allow_persistent' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli.cache_size' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqli.max_persistent' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqlnd.collect_memory_statistics' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqlnd.collect_statistics' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqlnd.debug' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'mysqlnd.net_read_buffer_size' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'odbc.default_cursortype' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'request_order' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'user_ini.cache_ttl' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'user_ini.filename' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'zend.enable_gc' => array(
            '5.2' => false,
            '5.3' => true,
        ),

        'curl.cainfo' => array(
            '5.3.6' => false,
            '5.3.7' => true,
        ),

        'max_input_vars' => array(
            '5.3.8' => false,
            '5.3.9' => true,
        ),

        'sqlite3.extension_dir' => array(
            '5.3.10' => false,
            '5.3.11' => true,
        ),

        'cli.pager' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'cli.prompt' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'cli_server.color' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'enable_post_data_reading' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqlnd.mempool_default_size' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqlnd.net_cmd_buffer_size' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqlnd.net_read_timeout' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'phar.cache_list' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.enabled' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.cleanup' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.name' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.freq' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.min_freq' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'session.upload_progress.prefix' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'windows_show_crt_warning' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'zend.detect_unicode' => array(
            '5.3'         => false,
            '5.4'         => true,
            'alternative' => 'detect_unicode',
        ),
        'zend.multibyte' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'zend.script_encoding' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'zend.signal_check' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'mysqlnd.log_mask' => array(
            '5.3' => false,
            '5.4' => true,
        ),

        'intl.use_exceptions' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'mysqlnd.sha256_server_public_key' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'mysqlnd.trace_alloc' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'sys_temp_dir' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'xsl.security_prefs' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.enable' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.enable_cli' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.memory_consumption' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.interned_strings_buffer' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.max_accelerated_files' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.max_wasted_percentage' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.use_cwd' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.validate_timestamps' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.revalidate_freq' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.revalidate_path' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.save_comments' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.load_comments' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.fast_shutdown' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.enable_file_override' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.optimization_level' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.inherited_hack' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.dups_fix' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.blacklist_filename' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.max_file_size' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.consistency_checks' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.force_restart_timeout' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.error_log' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.log_verbosity_level' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.preferred_memory_model' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.protect_memory' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.mmap_base' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.restrict_api' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.file_update_protection' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.huge_code_pages' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.lockfile_path' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'opcache.opt_debug_level' => array(
            '5.4' => false,
            '5.5' => true,
        ),

        'session.use_strict_mode' => array(
            '5.5.1' => false,
            '5.5.2' => true,
        ),

        'mysqli.rollback_on_cached_plink' => array(
            '5.5' => false,
            '5.6' => true,
        ),

        'assert.exception' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'pcre.jit' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'session.lazy_write' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'zend.assertions' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'opcache.file_cache' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'opcache.file_cache_only' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'opcache.file_cache_consistency_checks' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'opcache.file_cache_fallback' => array(
            '5.6' => false,
            '7.0' => true,
        ), // Windows only.

        'opcache.validate_permission' => array(
            '7.0.13' => false,
            '7.0.14' => true,
        ),
        'opcache.validate_root' => array(
            '7.0.13' => false,
            '7.0.14' => true,
        ),

        'hard_timeout' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session.sid_length' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session.sid_bits_per_character' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session.trans_sid_hosts' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'session.trans_sid_tags' => array(
            '7.0' => false,
            '7.1' => true,
        ),
        'url_rewriter.hosts' => array(
            '7.0' => false,
            '7.1' => true,
        ),

        // Introduced in PHP 7.1.25, 7.2.13, 7.3.0.
        'imap.enable_insecure_rsh' => array(
            '7.1.24' => false,
            '7.1.25' => true,
        ),

        'syslog.facility' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'syslog.filter' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'syslog.ident' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'session.cookie_samesite' => array(
            '7.2' => false,
            '7.3' => true,
        ),

        'ffi.enable' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'ffi.preload' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'opcache.cache_id' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'opcache.preload' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'zend.exception_ignore_args' => array(
            '7.3' => false,
            '7.4' => true,
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

        $functionLc = strtolower($tokens[$stackPtr]['content']);
        if (isset($this->iniFunctions[$functionLc]) === false) {
            return;
        }

        $iniToken = $this->getFunctionCallParameter($phpcsFile, $stackPtr, $this->iniFunctions[$functionLc]);
        if ($iniToken === false) {
            return;
        }

        $filteredToken = $this->stripQuotes($iniToken['raw']);
        if (isset($this->newIniDirectives[$filteredToken]) === false) {
            return;
        }

        $itemInfo = array(
            'name'       => $filteredToken,
            'functionLc' => $functionLc,
        );
        $this->handleFeature($phpcsFile, $iniToken['end'], $itemInfo);
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
        return $this->newIniDirectives[$itemInfo['name']];
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
        return array('alternative');
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
        $errorInfo['alternative'] = '';

        if (isset($itemArray['alternative']) === true) {
            $errorInfo['alternative'] = $itemArray['alternative'];
        }

        // Lower error level to warning if the function used was ini_get.
        if ($errorInfo['error'] === true && $itemInfo['functionLc'] === 'ini_get') {
            $errorInfo['error'] = false;
        }

        return $errorInfo;
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
        return "INI directive '%s' is not present in PHP version %s or earlier";
    }


    /**
     * Allow for concrete child classes to filter the error message before it's passed to PHPCS.
     *
     * @since 7.1.0
     *
     * @param string $error     The error message which was created.
     * @param array  $itemInfo  Base information about the item this error message applies to.
     * @param array  $errorInfo Detail information about an item this error message applies to.
     *
     * @return string
     */
    protected function filterErrorMsg($error, array $itemInfo, array $errorInfo)
    {
        if ($errorInfo['alternative'] !== '') {
            $error .= ". This directive was previously called '%s'.";
        }

        return $error;
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
        if ($errorInfo['alternative'] !== '') {
            $data[] = $errorInfo['alternative'];
        }

        return $data;
    }
}
