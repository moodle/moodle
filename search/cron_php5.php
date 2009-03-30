<?php
/*
* Moodle global search engine
* This is a special externalized code for cron handling in PHP5.
* Should never be called by a php 4.3.0 implementation. 
*/

try{
    // overrides php limits
    ini_set('max_execution_time', 300);
    if (empty($CFG->extramemorylimit)) {
        raise_memory_limit('128M');
    } else {
        raise_memory_limit($CFG->extramemorylimit);
    }

    mtrace("\n--DELETE----");
    require_once("$CFG->dirroot/search/delete.php");
    mtrace("--UPDATE----");
    require_once("$CFG->dirroot/search/update.php");
    mtrace("--ADD-------");
    require_once("$CFG->dirroot/search/add.php");
    mtrace("------------");
    //mtrace("cron finished.</pre>");
    mtrace('done');
}
catch(Exception $ex){
    mtrace('Fatal exception from Lucene subsystem. Search engine may not have been updated.');
    mtrace($ex);
}
?>
