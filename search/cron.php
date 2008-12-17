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

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    require_once("$CFG->dirroot/search/lib.php");

    if (empty($CFG->enableglobalsearch)) {
        mtrace('Global searching is not enabled. Nothing performed by search.');
    }
    else{
       include("{$CFG->dirroot}/search/cron_php5.php");
    }
?>