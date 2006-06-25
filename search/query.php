<?php

  require_once('../config.php');  
  require_once("$CFG->dirroot/search/lib.php");    
    
  //check for php5, but don't die yet (see line 27)
  if ($check = search_check_php5()) {  
    require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
    require_once("$CFG->dirroot/search/documents/wiki_document.php");
    
    $query_string = optional_param('query_string', '', PARAM_CLEAN);  
    $index_path = "$CFG->dataroot/search";
    $no_index = false; //optimism!
    
    try {
      $index = new Zend_Search_Lucene($index_path, false);
    } catch(Exception $e) {
      //print $e;
      $no_index = true;
    } //catch
  } //if  
  
  if (!$site = get_site()) {
    redirect("index.php");
  } //if
  
  $strsearch = "Search"; //get_string();
  $strquery  = "Enter your search query"; //get_string();

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
?>

<form name="query" method="get" action="query.php">
  <input type="text" name="query_string" length="50" value="<?php print $query_string ?>"/>
  &nbsp;<input type="submit" value="Search"/>&nbsp;&nbsp;<a href="query.php?advanced=yes">Advanced search</a>
  <a href="stats.php">Statistics</a>
</form>

<br>

<div align="center">
<?php
echo 'Searching: ';

if ($no_index) {
  print "0";
} else {
  print $index->count();
} //else

print ' documents.';

if ($no_index and isadmin()) {
  print "<br><br>Admin: There appears to be no index, click <a href='indexersplash.php'>here</a> to create one.";
} //if
?>
</div>

<?php  
  print_simple_box_end();
  
  if (!empty($query_string) and !$no_index) {
    print_simple_box_start('center', '50%', 'white', 10);
    
    search_stopwatch();
    $hits = $index->find(strtolower($query_string));      
    
    if (count($hits) > 0) {
      $link_function = $hits[0]->type.'_make_link';
    } //if    
    
    print "<br>";

    print count($hits)." results returned for '".$query_string."'.";
    print "<br><br>";
    
    print "<ol>";    
        
    foreach ($hits as $listing) {
      print "<li><a href='".$link_function($listing)."'>$listing->title</a><br>\n"
           ."<em>".search_shorten_url($link_function($listing), 70)."</em><br>\n"        
           ."Type: ".$listing->type.", score: ".round($listing->score, 3)."<br>\n"            
           ."<br></li>\n";
    } //foreach
    
    print "</ol>";
    
    print_simple_box_end();
  } //if
  
  if (!empty($query_string) and !$no_index) {
?>

<div align="center">
  It took <?php search_stopwatch(); ?> to fetch these results.
</div>

<?php
  } //if
  
  print_simple_box_end();
  print_footer();
?>