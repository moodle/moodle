<?php

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");
  
  mtrace("<pre>Starting clean-up...\n");
  
  if ($mods = get_records_select('modules')) {
  foreach ($mods as $mod) {
    $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
    
    if (file_exists($class_file)) {
      mtrace("Checking $mod->name module for deletions.\n");
      
      $records = get_records_sql("select * from ".$CFG->prefix."log where module = '$mod->name' and action like '%delete%'");
      
      print_r($records);
    } //if    
  } //foreach
  } //if

  mtrace("</pre>");

?>