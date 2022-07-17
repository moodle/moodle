<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility;

use PHP_CodeSniffer_Exception as PHPCS_Exception;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * PHPCS cross-version compatibility helper class.
 *
 * A number of PHPCS classes were split up into several classes in PHPCS 3.x
 * Those classes cannot be aliased as they don't represent the same object.
 * This class provides helper methods for functions which were contained in
 * one of these classes and which are used within the PHPCompatibility library.
 *
 * Additionally, this class contains some duplicates of PHPCS native methods.
 * These methods have received bug fixes or improved functionality between the
 * lowest supported PHPCS version and the latest PHPCS stable version and
 * to provide the same results cross-version, PHPCompatibility needs to use
 * the up-to-date versions of these methods.
 *
 * @since 8.0.0
 * @since 8.2.0 The duplicate PHPCS methods have been moved from the `Sniff`
 *              base class to this class.
 */
class PHPCSHelper
{

    /**
     * Get the PHPCS version number.
     *
     * @since 8.0.0
     *
     * @return string
     */
    public static function getVersion()
    {
        if (\defined('\PHP_CodeSniffer\Config::VERSION')) {
            // PHPCS 3.x.
            return \PHP_CodeSniffer\Config::VERSION;
        } else {
            // PHPCS 2.x.
            return \PHP_CodeSniffer::VERSION;
        }
    }


    /**
     * Pass config data to PHPCS.
     *
     * PHPCS cross-version compatibility helper.
     *
     * @since 8.0.0
     *
     * @param string      $key   The name of the config value.
     * @param string|null $value The value to set. If null, the config entry
     *                           is deleted, reverting it to the default value.
     * @param boolean     $temp  Set this config data temporarily for this script run.
     *                           This will not write the config data to the config file.
     *
     * @return void
     */
    public static function setConfigData($key, $value, $temp = false)
    {
        if (method_exists('\PHP_CodeSniffer\Config', 'setConfigData')) {
            // PHPCS 3.x.
            \PHP_CodeSniffer\Config::setConfigData($key, $value, $temp);
        } else {
            // PHPCS 2.x.
            \PHP_CodeSniffer::setConfigData($key, $value, $temp);
        }
    }


    /**
     * Get the value of a single PHPCS config key.
     *
     * @since 8.0.0
     *
     * @param string $key The name of the config value.
     *
     * @return string|null
     */
    public static function getConfigData($key)
    {
        if (method_exists('\PHP_CodeSniffer\Config', 'getConfigData')) {
            // PHPCS 3.x.
            return \PHP_CodeSniffer\Config::getConfigData($key);
        } else {
            // PHPCS 2.x.
            return \PHP_CodeSniffer::getConfigData($key);
        }
    }


    /**
     * Get the value of a single PHPCS config key.
     *
     * This config key can be set in the `CodeSniffer.conf` file, on the
     * command-line or in a ruleset.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param string                $key       The name of the config value.
     *
     * @return string|null
     */
    public static function getCommandLineData(File $phpcsFile, $key)
    {
        if (class_exists('\PHP_CodeSniffer\Config')) {
            // PHPCS 3.x.
            $config = $phpcsFile->config;
            if (isset($config->{$key})) {
                return $config->{$key};
            }
        } else {
            // PHPCS 2.x.
            $config = $phpcsFile->phpcs->cli->getCommandLineValues();
            if (isset($config[$key])) {
                return $config[$key];
            }
        }

        return null;
    }


    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * {@internal Duplicate of same method as contained in the `\PHP_CodeSniffer_File`
     * class and introduced in PHPCS 2.1.0 and improved in PHPCS 2.7.1.
     *
     * Once the minimum supported PHPCS version for this standard goes beyond
     * that, this method can be removed and calls to it replaced with
     * `$phpcsFile->findStartOfStatement($start, $ignore)` calls.
     *
     * Last synced with PHPCS version: PHPCS 3.3.2 at commit 6ad28354c04b364c3c71a34e4a18b629cc3b231e}
     *
     * @since 9.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile Instance of phpcsFile.
     * @param int                   $start     The position to start searching from in the token stack.
     * @param int|array             $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findStartOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        if (version_compare(self::getVersion(), '2.7.1', '>=') === true) {
            return $phpcsFile->findStartOfStatement($start, $ignore);
        }

        $tokens    = $phpcsFile->getTokens();
        $endTokens = Tokens::$blockOpeners;

        $endTokens[\T_COLON]            = true;
        $endTokens[\T_COMMA]            = true;
        $endTokens[\T_DOUBLE_ARROW]     = true;
        $endTokens[\T_SEMICOLON]        = true;
        $endTokens[\T_OPEN_TAG]         = true;
        $endTokens[\T_CLOSE_TAG]        = true;
        $endTokens[\T_OPEN_SHORT_ARRAY] = true;

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                if (isset($endTokens[$code]) === true) {
                    unset($endTokens[$code]);
                }
            }
        }

        $lastNotEmpty = $start;

        for ($i = $start; $i >= 0; $i--) {
            if (isset($endTokens[$tokens[$i]['code']]) === true) {
                // Found the end of the previous statement.
                return $lastNotEmpty;
            }

            if (isset($tokens[$i]['scope_opener']) === true
                && $i === $tokens[$i]['scope_closer']
            ) {
                // Found the end of the previous scope block.
                return $lastNotEmpty;
            }

            // Skip nested statements.
            if (isset($tokens[$i]['bracket_opener']) === true
                && $i === $tokens[$i]['bracket_closer']
            ) {
                $i = $tokens[$i]['bracket_opener'];
            } elseif (isset($tokens[$i]['parenthesis_opener']) === true
                && $i === $tokens[$i]['parenthesis_closer']
            ) {
                $i = $tokens[$i]['parenthesis_opener'];
            }

            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return 0;
    }


    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * {@internal Duplicate of same method as contained in the `\PHP_CodeSniffer_File`
     * class and introduced in PHPCS 2.1.0 and improved in PHPCS 2.7.1 and 3.3.0.
     *
     * Once the minimum supported PHPCS version for this standard goes beyond
     * that, this method can be removed and calls to it replaced with
     * `$phpcsFile->findEndOfStatement($start, $ignore)` calls.
     *
     * Last synced with PHPCS version: PHPCS 3.3.0-alpha at commit f5d899dcb5c534a1c3cca34668624517856ba823}
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile Instance of phpcsFile.
     * @param int                   $start     The position to start searching from in the token stack.
     * @param int|array             $ignore    Token types that should not be considered stop points.
     *
     * @return int
     */
    public static function findEndOfStatement(File $phpcsFile, $start, $ignore = null)
    {
        if (version_compare(self::getVersion(), '3.3.0', '>=') === true) {
            return $phpcsFile->findEndOfStatement($start, $ignore);
        }

        $tokens    = $phpcsFile->getTokens();
        $endTokens = array(
            \T_COLON                => true,
            \T_COMMA                => true,
            \T_DOUBLE_ARROW         => true,
            \T_SEMICOLON            => true,
            \T_CLOSE_PARENTHESIS    => true,
            \T_CLOSE_SQUARE_BRACKET => true,
            \T_CLOSE_CURLY_BRACKET  => true,
            \T_CLOSE_SHORT_ARRAY    => true,
            \T_OPEN_TAG             => true,
            \T_CLOSE_TAG            => true,
        );

        if ($ignore !== null) {
            $ignore = (array) $ignore;
            foreach ($ignore as $code) {
                if (isset($endTokens[$code]) === true) {
                    unset($endTokens[$code]);
                }
            }
        }

        $lastNotEmpty = $start;

        for ($i = $start; $i < $phpcsFile->numTokens; $i++) {
            if ($i !== $start && isset($endTokens[$tokens[$i]['code']]) === true) {
                // Found the end of the statement.
                if ($tokens[$i]['code'] === \T_CLOSE_PARENTHESIS
                    || $tokens[$i]['code'] === \T_CLOSE_SQUARE_BRACKET
                    || $tokens[$i]['code'] === \T_CLOSE_CURLY_BRACKET
                    || $tokens[$i]['code'] === \T_CLOSE_SHORT_ARRAY
                    || $tokens[$i]['code'] === \T_OPEN_TAG
                    || $tokens[$i]['code'] === \T_CLOSE_TAG
                ) {
                    return $lastNotEmpty;
                }

                return $i;
            }

            // Skip nested statements.
            if (isset($tokens[$i]['scope_closer']) === true
                && ($i === $tokens[$i]['scope_opener']
                || $i === $tokens[$i]['scope_condition'])
            ) {
                if ($i === $start && isset(Tokens::$scopeOpeners[$tokens[$i]['code']]) === true) {
                    return $tokens[$i]['scope_closer'];
                }

                $i = $tokens[$i]['scope_closer'];
            } elseif (isset($tokens[$i]['bracket_closer']) === true
                && $i === $tokens[$i]['bracket_opener']
            ) {
                $i = $tokens[$i]['bracket_closer'];
            } elseif (isset($tokens[$i]['parenthesis_closer']) === true
                && $i === $tokens[$i]['parenthesis_opener']
            ) {
                $i = $tokens[$i]['parenthesis_closer'];
            }

            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return ($phpcsFile->numTokens - 1);
    }


    /**
     * Returns the name of the class that the specified class extends
     * (works for classes, anonymous classes and interfaces).
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * {@internal Duplicate of same method as contained in the `\PHP_CodeSniffer_File`
     * class, but with some improvements which have been introduced in
     * PHPCS 2.8.0.
     * {@link https://github.com/squizlabs/PHP_CodeSniffer/commit/0011d448119d4c568e3ac1f825ae78815bf2cc34}.
     *
     * Once the minimum supported PHPCS version for this standard goes beyond
     * that, this method can be removed and calls to it replaced with
     * `$phpcsFile->findExtendedClassName($stackPtr)` calls.
     *
     * Last synced with PHPCS version: PHPCS 3.1.0-alpha at commit a9efcc9b0703f3f9f4a900623d4e97128a6aafc6}
     *
     * @since 7.1.4
     * @since 8.2.0 Moved from the `Sniff` class to this class.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile Instance of phpcsFile.
     * @param int                   $stackPtr  The position of the class token in the stack.
     *
     * @return string|false
     */
    public static function findExtendedClassName(File $phpcsFile, $stackPtr)
    {
        if (version_compare(self::getVersion(), '3.1.0', '>=') === true) {
            return $phpcsFile->findExtendedClassName($stackPtr);
        }

        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_CLASS
            && $tokens[$stackPtr]['type'] !== 'T_ANON_CLASS'
            && $tokens[$stackPtr]['type'] !== 'T_INTERFACE'
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classCloserIndex = $tokens[$stackPtr]['scope_closer'];
        $extendsIndex     = $phpcsFile->findNext(\T_EXTENDS, $stackPtr, $classCloserIndex);
        if ($extendsIndex === false) {
            return false;
        }

        $find = array(
            \T_NS_SEPARATOR,
            \T_STRING,
            \T_WHITESPACE,
        );

        $end  = $phpcsFile->findNext($find, ($extendsIndex + 1), $classCloserIndex, true);
        $name = $phpcsFile->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        return $name;
    }


    /**
     * Returns the name(s) of the interface(s) that the specified class implements.
     *
     * Returns FALSE on error or if there are no implemented interface names.
     *
     * {@internal Duplicate of same method as introduced in PHPCS 2.7.
     * This method also includes an improvement we use which was only introduced
     * in PHPCS 2.8.0, so only defer to upstream for higher versions.
     * Once the minimum supported PHPCS version for this sniff library goes beyond
     * that, this method can be removed and calls to it replaced with
     * `$phpcsFile->findImplementedInterfaceNames($stackPtr)` calls.}
     *
     * @since 7.0.3
     * @since 8.2.0 Moved from the `Sniff` class to this class.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the class token.
     *
     * @return array|false
     */
    public static function findImplementedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        if (version_compare(self::getVersion(), '2.7.1', '>') === true) {
            return $phpcsFile->findImplementedInterfaceNames($stackPtr);
        }

        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_CLASS
            && $tokens[$stackPtr]['type'] !== 'T_ANON_CLASS'
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classOpenerIndex = $tokens[$stackPtr]['scope_opener'];
        $implementsIndex  = $phpcsFile->findNext(\T_IMPLEMENTS, $stackPtr, $classOpenerIndex);
        if ($implementsIndex === false) {
            return false;
        }

        $find = array(
            \T_NS_SEPARATOR,
            \T_STRING,
            \T_WHITESPACE,
            \T_COMMA,
        );

        $end  = $phpcsFile->findNext($find, ($implementsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $phpcsFile->getTokensAsString(($implementsIndex + 1), ($end - $implementsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        } else {
            $names = explode(',', $name);
            $names = array_map('trim', $names);
            return $names;
        }
    }


    /**
     * Returns the method parameters for the specified function token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'token'             => integer, // The stack pointer to the variable name.
     *         'content'           => string,  // The full content of the variable definition.
     *         'pass_by_reference' => boolean, // Is the variable passed by reference?
     *         'variable_length'   => boolean, // Is the param of variable length through use of `...` ?
     *         'type_hint'         => string,  // The type hint for the variable.
     *         'type_hint_token'   => integer, // The stack pointer to the type hint
     *                                         // or false if there is no type hint.
     *         'nullable_type'     => boolean, // Is the variable using a nullable type?
     *        )
     * </code>
     *
     * Parameters with default values have an additional array index of
     * 'default' with the value of the default as a string.
     *
     * {@internal Duplicate of same method as contained in the `\PHP_CodeSniffer_File`
     * class.
     *
     * Last synced with PHPCS version: PHPCS 3.3.0-alpha at commit 53a28408d345044c0360c2c1b4a2aaebf4a3b8c9}
     *
     * @since 7.0.3
     * @since 8.2.0 Moved from the `Sniff` class to this class.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile Instance of phpcsFile.
     * @param int                   $stackPtr  The position in the stack of the
     *                                         function token to acquire the
     *                                         parameters for.
     *
     * @return array|false
     * @throws \PHP_CodeSniffer_Exception If the specified $stackPtr is not of
     *                                    type T_FUNCTION or T_CLOSURE.
     */
    public static function getMethodParameters(File $phpcsFile, $stackPtr)
    {
        if (version_compare(self::getVersion(), '3.3.0', '>=') === true) {
            return $phpcsFile->getMethodParameters($stackPtr);
        }

        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_FUNCTION
            && $tokens[$stackPtr]['code'] !== \T_CLOSURE
        ) {
            throw new PHPCS_Exception('$stackPtr must be of type T_FUNCTION or T_CLOSURE');
        }

        $opener = $tokens[$stackPtr]['parenthesis_opener'];
        $closer = $tokens[$stackPtr]['parenthesis_closer'];

        $vars            = array();
        $currVar         = null;
        $paramStart      = ($opener + 1);
        $defaultStart    = null;
        $paramCount      = 0;
        $passByReference = false;
        $variableLength  = false;
        $typeHint        = '';
        $typeHintToken   = false;
        $nullableType    = false;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis or bracket opener. If it does
            // it's likely to be an array which might have arguments in it. This
            // could cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['parenthesis_closer']) {
                    $i = ($tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            if (isset($tokens[$i]['bracket_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['bracket_closer']) {
                    $i = ($tokens[$i]['bracket_closer'] + 1);
                }
            }

            switch ($tokens[$i]['type']) {
                case 'T_BITWISE_AND':
                    if ($defaultStart === null) {
                        $passByReference = true;
                    }
                    break;
                case 'T_VARIABLE':
                    $currVar = $i;
                    break;
                case 'T_ELLIPSIS':
                    $variableLength = true;
                    break;
                case 'T_ARRAY_HINT': // Pre-PHPCS 3.3.0.
                case 'T_CALLABLE':
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint .= $tokens[$i]['content'];
                    break;
                case 'T_SELF':
                case 'T_PARENT':
                case 'T_STATIC':
                    // Self and parent are valid, static invalid, but was probably intended as type hint.
                    if (isset($defaultStart) === false) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint .= $tokens[$i]['content'];
                    }
                    break;
                case 'T_STRING':
                    // This is a string, so it may be a type hint, but it could
                    // also be a constant used as a default value.
                    $prevComma = false;
                    for ($t = $i; $t >= $opener; $t--) {
                        if ($tokens[$t]['code'] === \T_COMMA) {
                            $prevComma = $t;
                            break;
                        }
                    }

                    if ($prevComma !== false) {
                        $nextEquals = false;
                        for ($t = $prevComma; $t < $i; $t++) {
                            if ($tokens[$t]['code'] === \T_EQUAL) {
                                $nextEquals = $t;
                                break;
                            }
                        }

                        if ($nextEquals !== false) {
                            break;
                        }
                    }

                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint .= $tokens[$i]['content'];
                    }
                    break;
                case 'T_NS_SEPARATOR':
                    // Part of a type hint or default value.
                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint .= $tokens[$i]['content'];
                    }
                    break;
                case 'T_NULLABLE':
                case 'T_INLINE_THEN': // Pre-PHPCS 2.8.0.
                    if ($defaultStart === null) {
                        $nullableType = true;
                        $typeHint    .= $tokens[$i]['content'];
                    }
                    break;
                case 'T_CLOSE_PARENTHESIS':
                case 'T_COMMA':
                    // If it's null, then there must be no parameters for this
                    // method.
                    if ($currVar === null) {
                        break;
                    }

                    $vars[$paramCount]            = array();
                    $vars[$paramCount]['token']   = $currVar;
                    $vars[$paramCount]['name']    = $tokens[$currVar]['content'];
                    $vars[$paramCount]['content'] = trim($phpcsFile->getTokensAsString($paramStart, ($i - $paramStart)));

                    if ($defaultStart !== null) {
                        $vars[$paramCount]['default'] = trim(
                            $phpcsFile->getTokensAsString(
                                $defaultStart,
                                ($i - $defaultStart)
                            )
                        );
                    }

                    $vars[$paramCount]['pass_by_reference'] = $passByReference;
                    $vars[$paramCount]['variable_length']   = $variableLength;
                    $vars[$paramCount]['type_hint']         = $typeHint;
                    $vars[$paramCount]['type_hint_token']   = $typeHintToken;
                    $vars[$paramCount]['nullable_type']     = $nullableType;

                    // Reset the vars, as we are about to process the next parameter.
                    $defaultStart    = null;
                    $paramStart      = ($i + 1);
                    $passByReference = false;
                    $variableLength  = false;
                    $typeHint        = '';
                    $typeHintToken   = false;
                    $nullableType    = false;

                    $paramCount++;
                    break;
                case 'T_EQUAL':
                    $defaultStart = ($i + 1);
                    break;
            }//end switch
        }//end for

        return $vars;
    }
}
