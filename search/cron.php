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

  if (empty($CFG->enableglobalsearch)) {
    mtrace('Global searching is not enabled.');
    return;
  }

  mtrace("<pre>Starting cron...\n");

  mtrace("--DELETE----");
  require_once("$CFG->dirroot/search/delete.php");
  mtrace("--UPDATE----");
  require_once("$CFG->dirroot/search/update.php");
  mtrace("--ADD-------");
  require_once("$CFG->dirroot/search/add.php");
  mtrace("------------");

  mtrace("cron finished.</pre>");

?>
