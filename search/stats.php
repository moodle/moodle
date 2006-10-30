<?php
  /* Prints some basic statistics about the current index.
   *
   * Does some diagnostics if you are logged in as an administrator.
   * */

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");

  if ($CFG->forcelogin) {
    require_login();
  }

  if (empty($CFG->enableglobalsearch)) {
    error('Global searching is not enabled.');
  }

  //check for php5, but don't die yet
  if ($check = search_check_php5()) {
    require_once("$CFG->dirroot/search/indexlib.php");

    $indexinfo = new IndexInfo();
  } //if

  if (!$site = get_site()) {
    redirect("index.php");
  } //if

  $strsearch = "Search"; //get_string();
  $strquery  = "Search statistics"; //get_string();

  print_header("$site->shortname: $strsearch: $strquery", "$site->fullname",
               "<a href=\"index.php\">$strsearch</a> -> $strquery");

  //keep things pretty, even if php5 isn't available
  if (!$check) {
    print_heading(search_check_php5(true));
    print_footer();
    exit(0);
  } //if

  print_simple_box_start('center', '100%', '', 20);
  print_heading($strquery);

  print_simple_box_start('center', '', '', 20);

  //this table is only for admins, shows index directory size and location
  if (isadmin()) {
    $admin_table->tablealign = "center";
    $admin_table->align = array ("right", "left");
    $admin_table->wrap = array ("nowrap", "nowrap");
    $admin_table->cellpadding = 5;
    $admin_table->cellspacing = 0;
    $admin_table->width = '500';

    $admin_table->data[] = array('<strong>Data directory</strong>', '<em><strong>'.$indexinfo->path.'</strong></em>');
    $admin_table->data[] = array('Files in index directory', $indexinfo->filecount);
    $admin_table->data[] = array('Total size', $indexinfo->size);

    if ($indexinfo->time > 0) {
      $admin_table->data[] = array('Created on', date('r', $indexinfo->time));
    } else {
      $admin_table->data[] = array('Created on', '-');
    } //else

    if (!$indexinfo->valid($errors)) {
      $admin_table->data[] = array('<strong>Errors</strong>', '&nbsp;');

      foreach ($errors as $key=>$value) {
        $admin_table->data[] = array($key.' ... ', $value);
      } //foreach

      $admin_table->data[] = array('<strong>Solutions</strong>', '&nbsp;');

      if (isset($errors['dir'])) {
        $admin_table->data[] = array('Check dir', 'Ensure the data directory exists and is writable.');
      } //if

      if (isset($errors['db'])) {
        $admin_table->data[] = array('Check DB', 'Check your database for any problems.');
      } //if

      $admin_table->data[] = array('Run indexer test', '<a href=\'tests/index.php\'>tests/index.php</a>');
      $admin_table->data[] = array('Run indexer', '<a href=\'indexersplash.php\'>indexersplash.php</a>');
    } //if
  } //if

  //this is the standard summary table for normal users, shows document counts
  $table->tablealign = "center";
  $table->align = array ("right", "left");
  $table->wrap = array ("nowrap", "nowrap");
  $table->cellpadding = 5;
  $table->cellspacing = 0;
  $table->width = '500';

  $table->data[] = array('<strong>Database</strong>', '<em><strong>search_documents<strong></em>');

  //add extra fields if we're admin
  if (isadmin()) {
    //don't want to confuse users if the two totals don't match (hint: they should)
    $table->data[] = array('Documents in index', $indexinfo->indexcount);

    //*cough* they should match if deletions were actually removed from the index,
    //as it turns out, they're only marked as deleted and not returned in search results
    $table->data[] = array('Deletions in index', (int)$indexinfo->indexcount - (int)$indexinfo->dbcount);
  } //if

  $table->data[] = array('Documents in database', $indexinfo->dbcount);

  foreach($indexinfo->types as $key => $value) {
    $table->data[] = array("'$key' documents", $value);
  } //foreach

  if (isadmin()) {
    print_table($admin_table);
    print_spacer(20);
  } //if

  print_table($table);

  print_simple_box_end();
  print_simple_box_end();
  print_footer();
?>