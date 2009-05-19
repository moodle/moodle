<?php
/**
 * Verifies that class members are spaced correctly.
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

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Verifies that class members are spaced correctly.
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
class Moodle_Sniffs_WhiteSpace_MemberVarSpacingSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // There needs to be 1 blank line before the var, not counting comments.
        $prevLineToken = null;
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$commentTokens) === true) {
                // Skip comments.
                continue;
            } else if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                // Not the end of the line.
                continue;
            } else {
                // If this is a WHITESPACE token, and the token right before
                // it is a DOC_COMMENT, then it is just the newline after the
                // member var's comment, and can be skipped.
                if ($tokens[$i]['code'] === T_WHITESPACE && in_array($tokens[($i - 1)]['code'], PHP_CodeSniffer_Tokens::$commentTokens) === true) {
                    continue;
                }

                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the member var.
            $foundLines = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(array(T_WHITESPACE, T_DOC_COMMENT), $prevLineToken, null, true);
            $foundLines  = ($tokens[$prevLineToken]['line'] - $tokens[$prevContent]['line']);
        }//end if

        if ($foundLines !== 1) {
            // $phpcsFile->addError("Expected 1 blank line before member var; $foundLines found", $stackPtr);
        }

    }//end processMemberVar()


    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We don't care about normal variables.
        return;

    }//end processVariable()


    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We don't care about normal variables.
        return;

    }//end processVariableInString()


}//end class

?>
