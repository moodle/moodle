<?php
  /* The indexer logic -
   * Look through each installed module's lib file for necessary search functions,
   * and if they're present (and the module search document class file), add the
   * content to the index. Repeat this for blocks.
   *
   * Along with the index data, each document's summary gets stored in the database
   * and synchronised to the index (flat file) via the primary key ('id') which is mapped
   * to the 'dbid' field in the index
   * */

  //this'll take some time, set up the environment
  @set_time_limit(0);
  @ob_implicit_flush(true);
  @ob_end_flush();  

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");  

  //only administrators can index the moodle installation, because access to all pages is required   
  require_login();

  if (!isadmin()) {
    error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
  } //if
  
  //confirmation flag to prevent accidental reindexing (indexersplash.php is the correct entry point)
  $sure = strtolower(optional_param('areyousure', '', PARAM_ALPHA));
  
  if ($sure != 'yes') {
    mtrace("<pre>Sorry, you need to confirm indexing via <a href='indexersplash.php'>indexersplash.php</a>"
          .". (<a href='index.php'>Back to query page</a>).</pre>");
          
    exit(0);
  } //if  
  
  //check for php5 (lib.php)
  if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
  } //if
    
  //php5 found, continue including php5-only files
  require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
  
  //begin timer
  search_stopwatch();    
  mtrace('<pre>Server Time: '.date('r',time())."\n");
  
  //paths
  $index_path = $CFG->dataroot.'/search';
  $index_db_file = "$CFG->dirroot/search/db/$CFG->dbtype.sql";  
  
  //setup directory in data root
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
    
  mtrace('Starting activity modules');
  
  //the presence of the required search functions -
  // * mod_iterator
  // * mod_get_content_for_index
  //are the sole basis for including a module in the index at the moment.
  
  if ($mods = get_records_select('modules' /*'index this module?' where statement*/)) {
    foreach ($mods as $mod) {
      $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
      
      if (file_exists($libfile)) {
        include_once($libfile);
        
        $iter_function = $mod->name.'_iterator';
        $index_function = $mod->name.'_get_content_for_index';
        
        //specific module search document class
        $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
        
        $counter = 0;
        $doc = new stdClass;
                
        if (file_exists($class_file) && function_exists($index_function) && function_exists($iter_function)) {
          include_once($class_file);
          
          mtrace("Processing module function $index_function ...");
                     
          foreach ($iter_function() as $i) {
            $documents = $index_function($i);
            
            //begin transaction
            
            foreach($documents as $document) {
              $counter++;
                            
              //data object for db
              $doc->type = $document->type;
              $doc->title = search_escape_string($document->title);
              $doc->update = time();              
              $doc->url = 'none';
              $doc->courseid = $document->courseid;              
              $doc->groupid = $document->groupid;
              
              //insert summary into db
              $id = insert_record('search_documents', $doc);
              
              //synchronise db with index
              $document->addField(Zend_Search_Lucene_Field::Keyword('dbid', $id));
              
              //add document to index
              $index->addDocument($document);                  
                            
              //commit every x new documents, and print a status message                            
              if (($counter%200) == 0) {
                $index->commit();
                mtrace(".. $counter");                
              } //if
            } //foreach
            
            //end transaction
            
          } //foreach
                  
          //commit left over documents, and finish up  
          $index->commit();
          
          mtrace("-- $counter documents indexed");
          mtrace('done.');          
        } //if
      } //if
    } //foreach
  } //if
  
  //finished modules
  mtrace('Finished activity modules');
  search_stopwatch();
  
  //now blocks...
  //
  
  mtrace(".<br><a href='index.php'>Back to query page</a>.");
  mtrace('</pre>');

?>