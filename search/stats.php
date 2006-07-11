<?php
  /* Prints some basic statistics about the current index.
   * Allows the administrator to create an index if none exists.
   * */
  
  require_once('../config.php');  
  require_once("$CFG->dirroot/search/lib.php");  

  //check for php5, but don't die yet
  if ($check = search_check_php5()) {          
    //filesystem stats
    $index_path = "$CFG->dataroot/search";
    $index_size = display_size(get_directory_size($index_path));
    $index_dir  = get_directory_list($index_path, '', false, false);
    $index_filecount = count($index_dir);
    
    //indexed documents stats (via db)
    $db_exists = false;
    $admin_tables = $db->MetaTables();
    
    if (in_array($CFG->prefix.'search_documents', $admin_tables)) {
      $db_exists = true;
      $types = search_get_document_types();
      sort($types);
    
      //total documents
      $type_counts['Total'] = count_records('search_documents');

      foreach($types as $type) {
        $c = count_records('search_documents', 'doctype', $type);
        $type_counts[$type] = (int)$c;
      } //foreach
    } else {
      $type_counts['Total'] = 0;
    } //else      
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
  
    $admin_table->data[] = array('<strong>Data directory</strong>', '<em><strong>'.$index_path.'</strong></em>');
    $admin_table->data[] = array('Files in index directory', $index_filecount);
    $admin_table->data[] = array('Total size', $index_size);
    
    if ($index_filecount == 0 or !$db_exists) {
      $admin_table->data[] = array('Click to create index', "<a href='indexersplash.php'>Indexer</a>");
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
  foreach($type_counts as $key => $value) {
    $table->data[] = array($key, $value);
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