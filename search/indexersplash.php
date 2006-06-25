<?php
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
  
  if (array_search('search_documents', $tables)) {  
    $db_count = count_records($CFG->prefix.'search_documents');
  } else {
    $db_count = 0;
  } //else    
  
  //elaborate on error messages, when db!=0 and index=0 -> corrupt, etc.
  if ($index_filecount != 0 or $db_count != 0) {    
    mtrace("<pre>The data directory ($index_path) contains $index_filecount files, and "
          ."there are $db_count records in the <em>search_documents</em> table.");    
    mtrace('');    
    mtrace("This indicates that you have already indexed this site - click the following "
          ."link if you're sure you want to continue: <a href='indexer.php?areyousure=yes'>Go!</a>");          
    mtrace('');          
    mtrace("<a href='index.php'>Back to query page</a>.");
    mtrace("</pre>");
  } else {
    header('Location: indexer.php?areyousure=yes');
  } //else    
?>