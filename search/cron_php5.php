<?php
/*
* Moodle global search engine
* This is a special externalized code for cron handling in PHP5.
* Should never be called by a php 4.3.0 implementation. 
*/

try{
    // overrides php limits
    $maxtimelimit = ini_get('max_execution_time');
    ini_set('max_execution_time', 300);
    $maxmemoryamount = ini_get('memory_limit');
    ini_set('memory_limit', '48M');

    mtrace("\n--DELETE----");
    require_once("$CFG->dirroot/search/delete.php");
    mtrace("--UPDATE----");
    require_once("$CFG->dirroot/search/update.php");
    mtrace("--ADD-------");
    require_once("$CFG->dirroot/search/add.php");
    mtrace("------------");
    //mtrace("cron finished.</pre>");
    mtrace('done');

    // set back normal values for php limits
    ini_set('max_execution_time', $maxtimelimit);
    ini_set('memory_limit', $maxmemoryamount);
}
catch(Exception $ex){
    mtrace('Fatal exception from Lucene subsystem. Search engine may not have been updated.');
    mtrace($ex);
}
?>