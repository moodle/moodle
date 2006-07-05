<?php

  //Move this stuff to lib/searchlib.php?
  // Author: Michael Champanis

  //document types that can be searched
  define('SEARCH_NO_TYPE', 'none');
  define('SEARCH_WIKI_TYPE', 'wiki');
  
  //returns all the document type constants
  function search_get_document_types() {
    $r = Array(SEARCH_WIKI_TYPE, SEARCH_NO_TYPE);
    return $r;
  } //search_get_document_types
  
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
      print $str."<br>";
    } //if
    
    exit(0);
  } //search_pexit

?>