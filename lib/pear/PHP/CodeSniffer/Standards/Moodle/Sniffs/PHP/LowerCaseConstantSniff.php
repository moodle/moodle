<?php
/**
 * Moodle_Sniffs_PHP_LowerCaseConstantSniff.
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
 * Moodle_Sniffs_PHP_LowerCaseConstantSniff.
 *
 * Checks that all uses of true, false and null are lowerrcase.
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
class Moodle_Sniffs_PHP_LowerCaseConstantSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_TRUE,
                T_FALSE,
                T_NULL,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $keyword = $tokens[$stackPtr]['content'];
        if (strtolower($keyword) !== $keyword) {
            $error = 'TRUE, FALSE and NULL must be lowercase; expected "'.strtolower($keyword).'" but found "'.$keyword.'"';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
