<?php
/*
* Moodle global search engine
* This is a special externalized code for cron handling in PHP5.
* Should never be called by a php 4.3.0 implementation. 
*/
try{
    mtrace("<pre>Starting cron...\n");
    mtrace("--DELETE----");
    require_once("$CFG->dirroot/search/delete.php");
    mtrace("--UPDATE----");
    require_once("$CFG->dirroot/search/update.php");
    mtrace("--ADD-------");
    require_once("$CFG->dirroot/search/add.php");
    mtrace("------------");
    mtrace("cron finished.</pre>");
}
catch(Exception $ex){
    mtrace('Fatal exception from Lucene subsystem. Search engine may not have been updated.');
    mtrace($ex);
}
?>