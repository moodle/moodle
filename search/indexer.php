<?php
  /* The indexer logic -
   * Look through each installed module's search document class file (/search/documents)
   * for necessary search functions, and if they're present add the content to the index.
   * Repeat this for blocks.
   *
   * Because the iterator/retrieval functions are now stored in /search/documents/mod_document.php,
   * /mod/mod/lib.php doesn't have to be modified - and thus the search module becomes quite
   * self-sufficient. URL's are now stored in the index, stopping us from needing to require
   * the class files to generate a results page.
   *
   * Along with the index data, each document's summary gets stored in the database
   * and synchronised to the index (flat file) via the primary key ('id') which is mapped
   * to the 'db_id' field in the index
   * */

  //this'll take some time, set up the environment
  @set_time_limit(0);
  @ob_implicit_flush(true);
  @ob_end_flush();

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");

  //only administrators can index the moodle installation, because access to all pages is required
  require_login();

  if (empty($CFG->enableglobalsearch)) {
    error('Global searching is not enabled.');
  }

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
  //require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
  require_once("$CFG->dirroot/search/indexlib.php");

  mtrace('<pre>Server Time: '.date('r',time())."\n");

  if ($CFG->search_indexer_busy == '1') {
    //means indexing was not finished previously
    mtrace("Warning: Indexing was not successfully completed last time, restarting.\n");
  } //if

  //turn on busy flag
  set_config('search_indexer_busy', '1');

  //paths
  $index_path = SEARCH_INDEX_PATH;
  $index_db_file = "$CFG->dirroot/search/db/$CFG->dbtype.sql";
  $dbcontrol = new IndexDBControl();

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

  if (!$dbcontrol->checkDB()) {
    search_pexit("Database error. Please check settings/files.");
  } //if

  //begin timer
  search_stopwatch();
  mtrace("Starting activity modules\n");

  //the presence of the required search functions -
  // * mod_iterator
  // * mod_get_content_for_index
  //are the sole basis for including a module in the index at the moment.

  if ($mods = get_records_select('modules' /*'index this module?' where statement*/)) {
    //add virtual modules onto the back of the array
    $mods = array_merge($mods, search_get_additional_modules());

    foreach ($mods as $mod) {
      $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';

      if (file_exists($class_file)) {
        include_once($class_file);

        //build function names
        $iter_function = $mod->name.'_iterator';
        $index_function = $mod->name.'_get_content_for_index';

        $counter = 0;
        $doc = new stdClass;

        if (function_exists($index_function) && function_exists($iter_function)) {
          mtrace("Processing module function $index_function ...");

          foreach ($iter_function() as $i) {
            $documents = $index_function($i);

            //begin transaction

            foreach($documents as $document) {
              $counter++;

              //object to insert into db
              $dbid = $dbcontrol->addDocument($document);

              //synchronise db with index
              $document->addField(Zend_Search_Lucene_Field::Keyword('dbid', $dbid));

              //add document to index
              $index->addDocument($document);

              //commit every x new documents, and print a status message
              if (($counter%2000) == 0) {
                $index->commit();
                mtrace(".. $counter");
              } //if
            } //foreach

            //end transaction

          } //foreach

          //commit left over documents, and finish up
          $index->commit();

          mtrace("-- $counter documents indexed");
          mtrace("done.\n");
        } //if
      } //if
    } //foreach
  } //if

  //finished modules
  mtrace('Finished activity modules');
  search_stopwatch();

  //now blocks...
  //

  mtrace(".<br/><a href='index.php'>Back to query page</a>.");
  mtrace('</pre>');

  //finished, turn busy flag off
  set_config("search_indexer_busy", "0");

  //mark the time we last updated
  set_config("search_indexer_run_date", time());

  //and the index size
  set_config("search_index_size", (int)$index->count());

?>