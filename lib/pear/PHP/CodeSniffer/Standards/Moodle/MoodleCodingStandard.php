<?php
/**
 * Moodle Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Moodle Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_Moodle_MoodleCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard {
    public function getIncludedSniffs() {
        return array();
    }

    public function getExcludedSniffs() {
        return array('Moodle/Sniffs/CodeAnalysis');
    }
}//end class
?>
