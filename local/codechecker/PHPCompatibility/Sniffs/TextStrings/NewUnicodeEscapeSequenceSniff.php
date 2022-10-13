<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\TextStrings;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_Exception as PHPCS_Exception;
use PHP_CodeSniffer_File as File;

/**
 * PHP 7.0 introduced a Unicode codepoint escape sequence.
 *
 * Strings containing a literal `\u{` followed by an invalid sequence will cause a
 * fatal error as of PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.unicode-codepoint-escape-syntax
 * @link https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.strings.unicode-escapes
 * @link https://wiki.php.net/rfc/unicode_escape
 * @link https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.double
 *
 * @since 9.3.0
 */
class NewUnicodeEscapeSequenceSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.3.0
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_CONSTANT_ENCAPSED_STRING,
            \T_DOUBLE_QUOTED_STRING,
            \T_HEREDOC,
        );
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.3.0
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

        // Check whether this is a single quoted or double quoted string.
        if ($tokens[$stackPtr]['code'] === \T_CONSTANT_ENCAPSED_STRING) {

            // Find the start of the - potentially multi-line - text string.
            $start = $stackPtr;
            for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                if ($tokens[$i]['code'] === \T_WHITESPACE) {
                    continue;
                }

                if ($tokens[$i]['code'] === \T_CONSTANT_ENCAPSED_STRING) {
                    $start = $i;
                    continue;
                }

                break;
            }

            try {
                $textString = $this->getCompleteTextString($phpcsFile, $start, false);
            } catch (PHPCS_Exception $e) {
                // Something went wrong determining the start of the text string.
                return;
            }

            $startQuote = $textString[0];
            $endQuote   = substr($textString, -1);
            if (($startQuote === "'" && $endQuote === "'")
                || $startQuote !== $endQuote
            ) {
                // Single quoted string, not our concern.
                return;
            }
        }

        $content = $this->stripQuotes($tokens[$stackPtr]['content']);
        $count   = preg_match_all('`(?<!\\\\)\\\\u\{([^}\n\r]*)(\})?`', $content, $matches, \PREG_SET_ORDER);
        if ($count === false || $count === 0) {
            return;
        }

        foreach ($matches as $match) {
            $valid = false; // If the close curly is missing, we have an incomplete escape sequence.
            if (isset($match[2])) {
                $valid = $this->isValidUnicodeEscapeSequence($match[1]);
            }

            if ($this->supportsBelow('5.6') === true && $valid === true) {
                $phpcsFile->addError(
                    'Unicode codepoint escape sequences are not supported in PHP 5.6 or earlier. Found: %s',
                    $stackPtr,
                    'Found',
                    array($match[0])
                );
            }

            if ($this->supportsAbove('7.0') === true && $valid === false) {
                $phpcsFile->addError(
                    'Strings containing a literal \u{ followed by an invalid unicode codepoint escape sequence will cause a fatal error in PHP 7.0 and above. Escape the leading backslash to prevent this. Found: %s',
                    $stackPtr,
                    'Invalid',
                    array($match[0])
                );
            }
        }
    }


    /**
     * Verify if the codepoint in a unicode escape sequence is valid.
     *
     * @since 9.3.0
     *
     * @param string $codepoint The codepoint as a string.
     *
     * @return bool
     */
    protected function isValidUnicodeEscapeSequence($codepoint)
    {
        if (trim($codepoint) === '') {
            return false;
        }

        // Check if it's a valid hex codepoint.
        if (preg_match('`^[0-9A-F]+$`iD', $codepoint, $match) !== 1) {
            return false;
        }

        if (hexdec($codepoint) > 1114111) {
            // Outside of the maximum permissable range.
            return false;
        }

        return true;
    }
}
