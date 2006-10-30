<?php

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");

  require_login();

  if (empty($CFG->enableglobalsearch)) {
    error('Global searching is not enabled.');
  }

  if (!isadmin()) {
    error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
  } //if

  //check for php5 (lib.php)
  if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
  } //if

  require_once("$CFG->dirroot/search/indexlib.php");

  $index = new Zend_Search_Lucene(SEARCH_INDEX_PATH);
  $dbcontrol = new IndexDBControl();
  $addition_count = 0;

  $indexdate = $CFG->search_indexer_run_date;

  mtrace('<pre>Starting index update (additions)...');
  mtrace('Index size before: '.$CFG->search_index_size."\n");

  //get all modules
  if ($mods = get_records_select('modules')) {
  //append virtual modules onto array
  $mods = array_merge($mods, search_get_additional_modules());

  foreach ($mods as $mod) {
    //build include file and function names
    $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
    $db_names_function = $mod->name.'_db_names';
    $get_document_function = $mod->name.'_single_document';
    $additions = array();

    if (file_exists($class_file)) {
      require_once($class_file);

      //if both required functions exist
      if (function_exists($db_names_function) and function_exists($get_document_function)) {
        mtrace("Checking $mod->name module for additions.");
        $values = $db_names_function();
        $where = (isset($values[4])) ? $values[4] : '';

        //select records in MODULE table, but not in SEARCH_DATABASE_TABLE
        $sql =  "select id, ".$values[0]." as docid from ".$values[1].
                " where id not in".
                " (select docid from ".SEARCH_DATABASE_TABLE." where doctype like '$mod->name')".
                " and ".$values[2]." > $indexdate".
                " $where";

        $records = get_records_sql($sql);

        //foreach record, build a module specific search document using the get_document function
        if (is_array($records)) {
          foreach($records as $record) {
            $additions[] = $get_document_function($record->id);
          } //foreach
        } //if

        //foreach document, add it to the index and database table
        foreach ($additions as $add) {
          ++$addition_count;

          //object to insert into db
          $dbid = $dbcontrol->addDocument($add);

          //synchronise db with index
          $add->addField(Zend_Search_Lucene_Field::Keyword('dbid', $dbid));

          mtrace("  Add: $add->title (database id = $add->dbid, moodle instance id = $add->docid)");

          $index->addDocument($add);
        } //foreach

        mtrace("Finished $mod->name.\n");
      } //if
    } //if
  } //foreach
  } //if

  //commit changes
  $index->commit();

  //update index date and size
  set_config("search_indexer_run_date", time());
  set_config("search_index_size", (int)$CFG->search_index_size + (int)$addition_count);

  //print some additional info
  mtrace("Added $addition_count documents.");
  mtrace('Index size after: '.$index->count().'</pre>');

?>