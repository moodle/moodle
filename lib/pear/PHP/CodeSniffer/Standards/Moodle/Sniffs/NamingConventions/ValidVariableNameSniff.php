<?php
/**
 * Moodle_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Moodle_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of member variables.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Moodle_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{

    private $allowed_global_vars = array('CFG', 'SESSION', 'USER', 'COURSE', 'SITE', 'PAGE', 'DB', 'THEME');

    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $memberName     = ltrim($tokens[$stackPtr]['content'], '$');
        if (preg_match('/[A-Z]+/', $memberName)) {
            $error = "Member variable \"$memberName\" must be all lower-case";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // Must not be preceded by 'var' keyword
        $keyword = $phpcsFile->findPrevious(T_VAR, $stackPtr);
        if ($tokens[$keyword]['line'] == $tokens[$stackPtr]['line']) {
            $error = "The 'var' keyword is not permitted. Visibility must be explicitly declared with public, private or protected";
            $phpcsFile->addError($error, $stackPtr);
            return;
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
        $tokens = $phpcsFile->getTokens();
        $memberName     = ltrim($tokens[$stackPtr]['content'], '$');
        if (preg_match('/[A-Z]+/', $memberName)) {
            if (!in_array($memberName, $this->allowed_global_vars)) {
                $error = "Member variable \"$memberName\" must be all lower-case";
                $phpcsFile->addError($error, $stackPtr);
                return;
            }
        }

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
        $tokens = $phpcsFile->getTokens();
        $memberName     = ltrim($tokens[$stackPtr]['content'], '$');
        if (preg_match('/[A-Z]+/', $memberName)) {
            $error = "Member variable \"$memberName\" must be all lower-case";
            $phpcsFile->addError($error, $stackPtr);
            return;
        }
        return;

    }//end processVariableInString()


}//end class

?>
