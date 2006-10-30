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
  $deletion_count = 0;

  mtrace('<pre>Starting clean-up of removed records...');
  mtrace('Index size before: '.$CFG->search_index_size."\n");

  if ($mods = get_records_select('modules')) {
  $mods = array_merge($mods, search_get_additional_modules());

  foreach ($mods as $mod) {
    //build function names
    $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
    $delete_function = $mod->name.'_delete';
    $db_names_function = $mod->name.'_db_names';
    $deletions = array();

    if (file_exists($class_file)) {
      require_once($class_file);

      if (function_exists($delete_function) and function_exists($db_names_function)) {
        mtrace("Checking $mod->name module for deletions.");
        $values = $db_names_function();

        $sql = "select id, docid from ".SEARCH_DATABASE_TABLE.
                " where doctype like '$mod->name'".
                " and docid not in".
                " (select ".$values[0]." from ".$values[1].")";

        $records = get_records_sql($sql);

        //build an array of all the deleted records
        if (is_array($records)) {
          foreach($records as $record) {
            $deletions[] = $delete_function($record->docid);
          } //foreach
        } //if

        foreach ($deletions as $delete) {
          //find the specific document in the index, using it's docid and doctype as keys
          $doc = $index->find("+docid:$delete +doctype:$mod->name");

          //get the record, should only be one
          foreach ($doc as $thisdoc) {
            ++$deletion_count;
            mtrace("  Delete: $thisdoc->title (database id = $thisdoc->dbid, index id = $thisdoc->id, moodle instance id = $thisdoc->docid)");

            //remove it from index and database table
            $dbcontrol->delDocument($thisdoc);
            $index->delete($thisdoc->id);
          } //foreach
        } //foreach

        mtrace("Finished $mod->name.\n");
      } //if
    } //if
  } //foreach
  } //if

  //commit changes
  $index->commit();

  //update index date and index size
  set_config("search_indexer_run_date", time());
  set_config("search_index_size", (int)$CFG->search_index_size - (int)$deletion_count);

  mtrace("Finished $deletion_count removals.");
  mtrace('Index size after: '.$index->count().'</pre>');

?>