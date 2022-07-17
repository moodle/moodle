<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Miscellaneous;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;

/**
 * Check for use of alternative PHP tags, support for which was removed in PHP 7.0.
 *
 * {@internal Based on `Generic.PHP.DisallowAlternativePHPTags` by Juliette Reinders Folmer
 * (with permission) which was merged into PHPCS 2.7.0.}
 *
 * PHP version 7.0
 *
 * @link https://wiki.php.net/rfc/remove_alternative_php_tags
 * @link https://www.php.net/manual/en/language.basic-syntax.phptags.php
 *
 * @since 7.0.4
 */
class RemovedAlternativePHPTagsSniff extends Sniff
{

    /**
     * Whether ASP tags are enabled or not.
     *
     * @since 7.0.4
     *
     * @var bool
     */
    private $aspTags = false;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.4
     *
     * @return array
     */
    public function register()
    {
        if (version_compare(\PHP_VERSION_ID, '70000', '<') === true) {
            // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.asp_tagsRemoved
            $this->aspTags = (bool) ini_get('asp_tags');
        }

        return array(
            \T_OPEN_TAG,
            \T_OPEN_TAG_WITH_ECHO,
            \T_INLINE_HTML,
        );
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0') === false) {
            return;
        }

        $tokens  = $phpcsFile->getTokens();
        $openTag = $tokens[$stackPtr];
        $content = trim($openTag['content']);

        if ($content === '' || $content === '<?php') {
            return;
        }

        if ($openTag['code'] === \T_OPEN_TAG || $openTag['code'] === \T_OPEN_TAG_WITH_ECHO) {

            if ($content === '<%' || $content === '<%=') {
                $data      = array(
                    'ASP',
                    $content,
                );
                $errorCode = 'ASPOpenTagFound';

            } elseif (strpos($content, '<script ') !== false) {
                $data      = array(
                    'Script',
                    $content,
                );
                $errorCode = 'ScriptOpenTagFound';
            } else {
                return;
            }
        }
        // Account for incorrect script open tags.
        // The "(?:<s)?" in the regex is to work-around a bug in the tokenizer in PHP 5.2.
        elseif ($openTag['code'] === \T_INLINE_HTML
            && preg_match('`((?:<s)?cript (?:[^>]+)?language=[\'"]?php[\'"]?(?:[^>]+)?>)`i', $content, $match) === 1
        ) {
            $found     = $match[1];
            $data      = array(
                'Script',
                $found,
            );
            $errorCode = 'ScriptOpenTagFound';
        }

        if (isset($errorCode, $data)) {
            $phpcsFile->addError(
                '%s style opening tags have been removed in PHP 7.0. Found "%s"',
                $stackPtr,
                $errorCode,
                $data
            );
            return;
        }

        // If we're still here, we can't be sure if what we found was really intended as ASP open tags.
        if ($openTag['code'] === \T_INLINE_HTML && $this->aspTags === false) {
            if (strpos($content, '<%') !== false) {
                $error   = 'Possible use of ASP style opening tags detected. ASP style opening tags have been removed in PHP 7.0. Found: %s';
                $snippet = $this->getSnippet($content, '<%');
                $data    = array('<%' . $snippet);

                $phpcsFile->addWarning($error, $stackPtr, 'MaybeASPOpenTagFound', $data);
            }
        }
    }


    /**
     * Get a snippet from a HTML token.
     *
     * @since 7.0.4
     *
     * @param string $content The content of the HTML token.
     * @param string $startAt Partial string to use as a starting point for the snippet.
     * @param int    $length  The target length of the snippet to get. Defaults to 25.
     *
     * @return string
     */
    protected function getSnippet($content, $startAt = '', $length = 25)
    {
        $startPos = 0;

        if ($startAt !== '') {
            $startPos = strpos($content, $startAt);
            if ($startPos !== false) {
                $startPos += \strlen($startAt);
            }
        }

        $snippet = substr($content, $startPos, $length);
        if ((\strlen($content) - $startPos) > $length) {
            $snippet .= '...';
        }

        return $snippet;
    }
}
