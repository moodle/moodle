<?php
/** 
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* General function library
*
* This file must not contain any PHP 5, because it is used to test for PHP 5
* itself, and needs to be able to be executed on PHP 4 installations.
*
*/

/*
// function reference
function search_get_document_types($prefix = 'SEARCH_TYPE_') {
function search_get_additional_modules() {
function search_shorten_url($url, $length=30) {
function search_escape_string($str) {
function search_check_php5($feedback = false) {
function search_stopwatch($cli = false) {
function search_pexit($str = "") {
*/

define('SEARCH_INDEX_PATH', "$CFG->dataroot/search");
define('SEARCH_DATABASE_TABLE', 'block_search_documents');

//document types that can be searched
//define('SEARCH_TYPE_NONE', 'none');
define('SEARCH_TYPE_WIKI', 'wiki');
define('PATH_FOR_SEARCH_TYPE_WIKI', 'mod/wiki');
define('SEARCH_TYPE_FORUM', 'forum');
define('PATH_FOR_SEARCH_TYPE_FORUM', 'mod/forum');
define('SEARCH_TYPE_GLOSSARY', 'glossary');
define('PATH_FOR_SEARCH_TYPE_GLOSSARY', 'mod/glossary');
define('SEARCH_TYPE_RESOURCE', 'resource');
define('PATH_FOR_SEARCH_TYPE_RESOURCE', 'mod/resource');
define('SEARCH_TYPE_TECHPROJECT', 'techproject');
define('PATH_FOR_SEARCH_TYPE_TECHPROJECT', 'mod/techproject');
define('SEARCH_TYPE_DATA', 'data');
define('PATH_FOR_SEARCH_TYPE_DATA', 'mod/data');
define('SEARCH_TYPE_CHAT', 'chat');
define('PATH_FOR_SEARCH_TYPE_CHAT', 'mod/chat');
define('SEARCH_TYPE_LESSON', 'lesson');
define('PATH_FOR_SEARCH_TYPE_LESSON', 'mod/lesson');

/**
* returns all the document type constants
* @param prefix a pattern for recognizing constants
* @return an array of type labels
*/
function search_get_document_types($prefix = 'SEARCH_TYPE_') {
    $ret = array();
    foreach (get_defined_constants() as $key => $value) {
        if (preg_match("/^{$prefix}/", $key)){
            $ret[$key] = $value;
        } 
    } 
    sort($ret);
    return $ret;
} //search_get_document_types

/**
* additional virtual modules to index
*
* By adding 'moo' to the extras array, an additional document type
* documents/moo_document.php will be indexed - this allows for
* virtual modules to be added to the index, i.e. non-module specific
* information.
*/
function search_get_additional_modules() {
    $extras = array(/* additional keywords go here */);
    $ret = array();
    foreach($extras as $extra) {
        $temp->name = $extra;
        $ret[] = clone($temp);
    } 
    return $ret;
} //search_get_additional_modules

/**
* shortens a url so it can fit on the results page
* @param url the url
* @param length the size limit we want
*/
function search_shorten_url($url, $length=30) {
    return substr($url, 0, $length)."...";
} //search_shorten_url

/**
* a local function for escaping
* @param str the string to escape
* @return the escaped string
*/
function search_escape_string($str) {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'mysql':
            $s = mysql_real_escape_string($str);
            break;
        case 'postgres':
            $s = pg_escape_string($str);
            break;
        default:
            $s = addslashes($str);
    }
    return $s;
} //search_escape_string

/**
* get a real php 5 version number, using 5.0.0 arbitrarily
* @param feedback if true, prints a feedback message to output.
* @return true if version of PHP is high enough
*/
function search_check_php5($feedback = false) {
    if (!check_php_version("5.0.0")) {
        if ($feedback) {
            print_heading(get_string('versiontoolow', 'search'));
        }
        return false;
    } 
    else {
      return true;
    } 
} //search_check_php5

/**
* simple timer function, on first call, records a current microtime stamp, outputs result on 2nd call
* @param cli an output formatting switch
* @return void
*/
function search_stopwatch($cli = false) {
    if (!empty($GLOBALS['search_script_start_time'])) {
        if (!$cli) print '<em>';
        print round(microtime(true) - $GLOBALS['search_script_start_time'], 6).' '.get_string('seconds', 'search');
        if (!$cli) print '</em>';
        unset($GLOBALS['search_script_start_time']);
    } 
    else {
        $GLOBALS['search_script_start_time'] = microtime(true);
    } 
} //search_stopwatch

/**
* print and exit (for debugging)
* @param str a variable to explore
* @return void
*/
function search_pexit($str = "") {
    if (is_array($str) or is_object($str)) {
        print_r($str);
    } else if ($str) {
        print $str."<br/>";
    }
    exit(0);
} //search_pexit

?>
