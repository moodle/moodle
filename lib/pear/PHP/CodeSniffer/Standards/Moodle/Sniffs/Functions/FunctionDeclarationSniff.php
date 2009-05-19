<?php
/**
 * Moodle_Sniffs_Functions_FunctionDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @copyright 2006 Moodle Pty Ltd (ABN 77 084 670 600)
 * @license   http://www.gnu.org/copyleft/gpl.html GPL 
 * @version   CVS: $Id:
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Moodle_Sniffs_Functions_FunctionDeclarationSniff.
 *
 * Checks the function declaration is correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @copyright 2006 Moodle Pty Ltd (ABN 77 084 670 600)
 * @license http://www.gnu.org/copyleft/gpl.html GPL 
 * @version   CVS: $Id:
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Moodle_Sniffs_Functions_FunctionDeclarationSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{


    /**
     * Returns an array of patterns to check are correct.
     *
     * @return array
     */
    protected function getPatterns()
    {
        return array(
                'function abc(...) {',
                'abstract function abc(...);'
               );

    }//end getPatterns()


}//end class

?>
