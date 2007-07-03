<?php

require_once("../../../config.php");
require_once($CFG->dirroot.'/grade/export/lib.php');
require_once('grade_export_txt.php');
 
$id = required_param('id', PARAM_INT); // course id
$itemids = explode(",", required_param('itemids', PARAM_RAW));
$feedback = optional_param('feedback', '', PARAM_ALPHA); 
 
// print all the exported data here
$export = new grade_export_txt($id, $itemids);
$export->set_separator(optional_param('separator'));
$export->print_grades($feedback);
    
?>