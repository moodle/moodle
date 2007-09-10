<?php

/* cron script to perform all the periodic search tasks
*
* delete.php
*   updates the index by pruning deleted documents
*
* update.php
*   updates document info in the index if the document has been modified since indexing
*
* add.php
*   adds documents created since the last index run
*/

    require_once('../config.php');
    require_once("$CFG->dirroot/search/lib.php");

    if (!search_check_php5()) {
        $phpversion = phpversion();
        mtrace("Sorry, cannot cron global search as it requires PHP 5.0.0 or later (currently using version $phpversion)");
    } 
    else if (empty($CFG->enableglobalsearch)) {
        mtrace('Global searching is not enabled. Nothing performed by search.');
    }
    else{
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
    }
?>