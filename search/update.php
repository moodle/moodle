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
  $update_count = 0;

  $indexdate = $CFG->search_indexer_run_date;

  mtrace("<pre>Starting index update (updates)...\n");

  if ($mods = get_records_select('modules')) {
  $mods = array_merge($mods, search_get_additional_modules());

  foreach ($mods as $mod) {
    $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
    $get_document_function = $mod->name.'_single_document';
    $delete_function = $mod->name.'_delete';
    $db_names_function = $mod->name.'_db_names';
    $updates = array();

    if (file_exists($class_file)) {
      require_once($class_file);

      if (function_exists($delete_function) and function_exists($db_names_function) and function_exists($get_document_function)) {
        mtrace("Checking $mod->name module for updates.");
        $values = $db_names_function();

        //TODO: check 'in' syntax with other RDBMS' (add and update.php as well)
        $sql = "select id, ".$values[0]." as docid from ".$values[1].
               " where ".$values[3]." > $indexdate".
               " and id in (select docid from ".SEARCH_DATABASE_TABLE.")";

        $records = get_records_sql($sql);

        if (is_array($records)) {
          foreach($records as $record) {
            $updates[] = $delete_function($record->docid);
          } //foreach
        } //if

        foreach ($updates as $update) {
          ++$update_count;

          //delete old document
          $doc = $index->find("+docid:$update +doctype:$mod->name");

          //get the record, should only be one
          foreach ($doc as $thisdoc) {
            mtrace("  Delete: $thisdoc->title (database id = $thisdoc->dbid, index id = $thisdoc->id, moodle instance id = $thisdoc->docid)");

            $dbcontrol->delDocument($thisdoc);
            $index->delete($thisdoc->id);
          } //foreach

          //add new modified document back into index
          $add = $get_document_function($update);

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

  //update index date
  set_config("search_indexer_run_date", time());

  mtrace("Finished $update_count updates.</pre>");

?>