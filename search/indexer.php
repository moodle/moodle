<?php
  //this'll take some time, set up the environment
  @set_time_limit(0);
  @ob_implicit_flush(true);
  @ob_end_flush();  

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");  

  require_login();

  if (!isadmin()) {
    error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
  } //if
  
  $sure = strtolower(optional_param('areyousure', '', PARAM_ALPHA));
  
  if ($sure != 'yes') {
    mtrace("Sorry, you weren't sure enough (<a href='index.php'>back to query page</a>).");
    exit(0);
  } //if  
  
  //check for php5 (lib.php)
  if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
  } //if
    
  require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
  
  //begin timer
  search_stopwatch();    
  mtrace('<pre>Server Time: '.date('r',time())."\n");
  
  //paths
  $index_path = $CFG->dataroot.'/search';
  $index_db_file = "$CFG->dirroot/search/db/$CFG->dbtype.sql";  
  
  if (!file_exists($index_path)) {
    mtrace("Data directory ($index_path) does not exist, attempting to create.");
    if (!mkdir($index_path)) {
      search_pexit("Error creating data directory at: $index_path. Please correct.");
    } else {
      mtrace("Directory successfully created.");
    } //else
  } else {
    mtrace("Using $index_path as data directory.");
  } //else

  //stop accidental re-indexing (zzz)
  //search_pexit("Not indexing at this time.");

  $index = new Zend_Search_Lucene($index_path, true);
  
  //create the database tables
  $tables = $db->MetaTables();
    
  if (in_array($CFG->prefix.'search_documents', $tables)) {
    delete_records('search_documents');
  } else {        
    ob_start(); //turn output buffering on - to hide modify_database() output
    modify_database($index_db_file, '', false);
    ob_end_clean(); //chuck the buffer and resume normal operation
  } //else
  
  //empty database table goes here
  // delete * from search_documents;
  // set auto_increment back to 1
  
  //-------- debug stuff
  /*
  include_once("$CFG->dirroot/mod/wiki/lib.php");
  
  $wikis = get_all_instances_in_courses("wiki", get_courses());
  #search_pexit($wikis[1]);
  $entries = wiki_get_entries($wikis[1]);
  #search_pexit($entries);
    
  #$r = wiki_get_pages($entries[134]);
  $r = wiki_get_latest_pages($entries[95]);
  
  search_pexit($r);
  //ignore me --------*/
    
  mtrace('Starting activity modules');
  if ($mods = get_records_select('modules' /*'index this module?' where statement*/)) {
    foreach ($mods as $mod) {
      $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
      if (file_exists($libfile)) {
        include_once($libfile);
        
        $iter_function = $mod->name.'_iterator';
        $index_function = $mod->name.'_get_content_for_index';
        $include_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';        
        $c = 0;
        $doc = new stdClass;
                
        if (function_exists($index_function) && function_exists($iter_function)) {
          include_once($include_file);
          
          mtrace("Processing module function $index_function ...");
                     
          foreach ($iter_function() as $i) {
            $documents = $index_function($i);
            
            //begin transaction
            
            foreach($documents as $document) {
              $c++;
              
              //db sync increases indexing time from 55 sec to 73 (64 on Saturday?), so ~30%
              //therefore, let us make a custom insert function for this search module
              
              //data object for db
              $doc->type = $document->type;
              $doc->title = mysql_real_escape_string($document->title); //naughty
              $doc->update = time();
              $doc->permissions = 0;
              $doc->url = 'none';
              $doc->courseid = $document->courseid;
              $doc->userid = $document->userid;
              $doc->groupid = $document->groupid;
              
              //insert summary into db
              $id = insert_record('search_documents', $doc);
              
              //synchronise db with index
              $document->addField(Zend_Search_Lucene_Field::Keyword('dbid', $id));
              $index->addDocument($document);                  
                            
              //commit every 100 new documents, and print a status message                            
              if (($c%100) == 0) {
                $index->commit();
                mtrace(".. $c");                
              } //if
            } //foreach
            
            //end transaction
            
          } //foreach
                  
          //commit left over documents, and finish up  
          $index->commit();
          mtrace("-- $c documents indexed");
          mtrace('done.');          
        } //if
      } //if
    } //foreach
  } //if
  
  //done modules
  mtrace('Finished activity modules');
  search_stopwatch();
  mtrace(".<br><a href='index.php'>Back to query page</a>.");
  mtrace('</pre>');

?>