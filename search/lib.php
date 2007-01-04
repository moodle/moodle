<?php

  /* Move this stuff to lib/searchlib.php?
   * Author: Michael Champanis
   *
   * This file must not contain any PHP 5, because it is used to test for PHP 5
   * itself, and needs to be able to be executed on PHP 4 installations.
   * */

  define('SEARCH_INDEX_PATH', "$CFG->dataroot/search");
  define('SEARCH_DATABASE_TABLE', 'search_documents');

  //document types that can be searched
  //define('SEARCH_TYPE_NONE', 'none');
  define('SEARCH_TYPE_WIKI', 'wiki');
  define('SEARCH_TYPE_FORUM', 'forum');
  define('SEARCH_TYPE_GLOSSARY', 'glossary');
  define('SEARCH_TYPE_RESOURCE', 'resource');

  //returns all the document type constants
  function search_get_document_types($prefix='SEARCH_TYPE') {
    $ret = array();

    foreach (get_defined_constants() as $key=>$value) {
      if (substr($key, 0, strlen($prefix)) == $prefix) {
        $ret[$key] = $value;
      } //if
    } //foreach

    sort($ret);

    return $ret;
  } //search_get_document_types

  // additional virtual modules to index
  //
  // By adding 'moo' to the extras array, an additional document type
  // documents/moo_document.php will be indexed - this allows for
  // virtual modules to be added to the index, i.e. non-module specific
  // information.
  function search_get_additional_modules() {
    $extras = array(/* additional keywords go here */);
    $ret = array();

    foreach($extras as $extra) {
      $temp->name = $extra;
      $ret[] = clone($temp);
    } //foreach

    return $ret;
  } //search_get_additional_modules

  //shortens a url so it can fit on the results page
  function search_shorten_url($url, $length=30) {
    return substr($url, 0, $length)."...";
  } //search_shorten_url

  function search_escape_string($str) {
    global $CFG;

    switch ($CFG->dbtype) {
      case 'mysql':
        $s = mysql_real_escape_string($str);
        break;
      case 'postgres7':
        $s = pg_escape_string($str);
        break;
      default:
        $s = addslashes($str);
    } //switch

    return $s;
  } //search_escape_string

  //get a real php 5 version number, using 5.0.0 arbitrarily
  function search_check_php5($feedback=false) {
    if (!check_php_version("5.0.0")) {
      if ($feedback) {
        $phpversion = phpversion();
        print_heading("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
      } //if

      return false;
    } else {
      return true;
    } //else
  } //search_check_php5

  //simple timer function, outputs result on 2nd call
  function search_stopwatch($cli = false) {
    if (!empty($GLOBALS['search_script_start_time'])) {
      if (!$cli) print '<em>';
      print round(microtime(true) - $GLOBALS['search_script_start_time'], 6).' seconds';
      if (!$cli) print '</em>';

      unset($GLOBALS['search_script_start_time']);
    } else {
      $GLOBALS['search_script_start_time'] = microtime(true);
    } //else
  } //search_stopwatch

  //print and exit (for debugging)
  function search_pexit($str = "") {
    if (is_array($str) or is_object($str)) {
      print_r($str);
    } else if ($str) {
      print $str."<br/>";
    } //if

    exit(0);
  } //search_pexit

?>