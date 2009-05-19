<?php
/**
 * Moodle_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Moodle_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Moodle_Sniffs_WhiteSpace_ScopeClosingBraceSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$scopeOpeners;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = ($stackPtr - 1);
        for ($lineStart; $lineStart > 0; $lineStart--) {
            if (strpos($tokens[$lineStart]['content'], $phpcsFile->eolChar) !== false) {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $lineStart = $phpcsFile->findNext(array(T_WHITESPACE), ($lineStart + 1), null, true);

        $startColumn = $tokens[$lineStart]['column'];
        $scopeStart  = $tokens[$stackPtr]['scope_opener'];
        $scopeEnd    = $tokens[$stackPtr]['scope_closer'];

        // Check that the closing brace is on its own line.
        $lastContent = $phpcsFile->findPrevious(array(T_WHITESPACE), ($scopeEnd - 1), $scopeStart, true);
        if ($tokens[$lastContent]['line'] === $tokens[$scopeEnd]['line']) {
            $error = 'Closing brace must be on a line by itself';
            $phpcsFile->addError($error, $scopeEnd);
            return;
        }

        // Check now that the closing brace is lined up correctly.
        $braceIndent   = $tokens[$scopeEnd]['column'];
        $isBreakCloser = ($tokens[$scopeEnd]['code'] === T_BREAK);
        if (in_array($tokens[$stackPtr]['code'], array(T_CASE, T_DEFAULT)) === true && $isBreakCloser === true) {
            // BREAK statements should be indented 4 spaces from the
            // CASE or DEFAULT statement.
            if ($braceIndent !== ($startColumn + 4)) {
                $error = 'Break statement indented incorrectly; expected '.($startColumn + 3).' spaces, found '.($braceIndent - 1);
                $phpcsFile->addError($error, $scopeEnd);
            }
        } else {
            if ($braceIndent !== $startColumn) {
                $error = 'Closing brace indented incorrectly; expected '.($startColumn - 1).' spaces, found '.($braceIndent - 1);
                $phpcsFile->addError($error, $scopeEnd);
            }
        }

    }//end process()


}//end class

?>
