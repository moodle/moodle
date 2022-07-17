<?php
// phpcs:ignoreFile
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * PHPCS cross-version compatibility helper.
 *
 * @package   local_codechecker
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 *
 * @since 8.0.0
 */

/*
 * Alias a number of PHPCS 3.x classes to their PHPCS 2.x equivalents.
 *
 * This file is auto-loaded by PHPCS 3.x before any sniffs are loaded
 * through the PHPCS 3.x `<autoload>` ruleset directive.
 *
 * {internal The PHPCS file have been reorganized in PHPCS 3.x, quite
 * a few "old" classes have been split and spread out over several "new"
 * classes. In other words, this will only work for a limited number
 * of classes.}
 *
 * {internal The `class_exists` wrappers are needed to play nice with other
 * external PHPCS standards creating cross-version compatibility in the same
 * manner.}
 */
if (defined('PHPCOMPATIBILITY_PHPCS_ALIASES_SET') === false) {
    if (interface_exists('\PHP_CodeSniffer_Sniff') === false) {
        class_alias('PHP_CodeSniffer\Sniffs\Sniff', '\PHP_CodeSniffer_Sniff');
    }
    if (class_exists('\PHP_CodeSniffer_File') === false) {
        class_alias('PHP_CodeSniffer\Files\File', '\PHP_CodeSniffer_File');
    }
    if (class_exists('\PHP_CodeSniffer_Tokens') === false) {
        class_alias('PHP_CodeSniffer\Util\Tokens', '\PHP_CodeSniffer_Tokens');
    }
    if (class_exists('\PHP_CodeSniffer_Exception') === false) {
        class_alias('PHP_CodeSniffer\Exceptions\RuntimeException', '\PHP_CodeSniffer_Exception');
    }
    if (class_exists('\PHP_CodeSniffer_Standards_AbstractScopeSniff') === false) {
        class_alias('PHP_CodeSniffer\Sniffs\AbstractScopeSniff', '\PHP_CodeSniffer_Standards_AbstractScopeSniff');
    }
    if (class_exists('\Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff') === false) {
        class_alias('PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\CamelCapsFunctionNameSniff', '\Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff');
    }

    define('PHPCOMPATIBILITY_PHPCS_ALIASES_SET', true);

    /*
     * Register an autoloader.
     *
     * {internal When `installed_paths` is set via the ruleset, this autoloader
     * is needed to run the sniffs.
     * Upstream issue: {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/1591} }
     *
     * @since 8.0.0
     */
    spl_autoload_register(function ($class) {
        // Only try & load our own classes.
        if (stripos($class, 'PHPCompatibility') !== 0) {
            return;
        }

        $file = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

        if (file_exists($file)) {
            include_once $file;
        }
    });
}
