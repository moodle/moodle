<?php

require_once("../../../config.php");
require_once($CFG->dirroot.'/grade/export/lib.php');
require_once('grade_export_xml.php');
 
$id = required_param('id', PARAM_INT); // course id
$itemids = required_param('itemids', PARAM_NOTAGS);
$feedback = optional_param('feedback', '', PARAM_ALPHA); 
 
// print all the exported data here
$export = new grade_export_xml($id, $itemids);
$export->print_grades($feedback);
    
?>