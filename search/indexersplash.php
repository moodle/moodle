<?php
  /* This file serves as a splash-screen (entry page) to the indexer script -
   * it is in place to prevent accidental reindexing which can lead to a loss
   * of time, amongst other things.
   * */
  
  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");  

  require_login();

  if (!isadmin()) {
    error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
  } //if
  
  //check for php5 (lib.php)
  if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
  } //if  
  
  $index_path = "$CFG->dataroot/search";  
  $index_dir  = get_directory_list($index_path, '', false, false);
  $index_filecount = count($index_dir);
  
  //check if the table exists in the db
  $tables = $db->MetaTables();
  
  if (in_array($CFG->prefix.'search_documents', $tables)) {  
    $db_count = count_records('search_documents');
  } else {
    $db_count = 0;
  } //else    
  
  //TODO: elaborate on error messages, when db!=0 and index=0 -> corrupt, etc.
  if ($index_filecount != 0 or $db_count != 0) {    
    mtrace("<pre>The data directory ($index_path) contains $index_filecount files, and\n"
          ."there are $db_count records in the <em>search_documents</em> table.\n"
          ."\n"
          ."This indicates that you have already succesfully indexed this site, or at least\n"
          ."started and cancelled an indexing session. Follow the link if you are sure that\n"
          ."you want to continue indexing - this will replace any existing index data (no\n"
          ."Moodle data is affected).\n"
          ."\n"          
          ."<a href='indexer.php?areyousure=yes'>Continue indexing</a> or <a href='index.php'>Back to query page</a>."
          ."</pre>");
  } else {
    header('Location: indexer.php?areyousure=yes');
  } //else    
?>