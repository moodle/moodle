<?php

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");
  
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