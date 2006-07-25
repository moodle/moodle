<?php

  /* The query page - accepts a user-entered query string and returns results.
   *
   * Queries are boolean-aware, e.g.:
   * 
   * '+'      term required
   * '-'      term must not be present
   * ''       (no modifier) term's presence increases rank, but isn't required
   * 'field:' search this field
   *
   * Examples:
   *
   * 'earthquake +author:michael'
   *   Searches for documents written by 'michael' that contain 'earthquake'
   *
   * 'earthquake +doctype:wiki'
   *   Search all wiki pages for 'earthquake'
   *
   * '+author:helen +author:foster'
   *   All articles written by Helen Foster
   *   
   * */
    
  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php"); 
    
  //check for php5, but don't die yet (see line 52)
  if ($check = search_check_php5()) {      
    require_once("$CFG->dirroot/search/querylib.php");    
    
    $query_string = optional_param('query_string', '', PARAM_CLEAN);
    $page_number  = optional_param('page', 1, PARAM_INT);
    
    if ($page_number < 1) {
      $page_number = 1;
    } //if
    
    $sq = new SearchQuery($query_string, $page_number, 10, true);  
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
  <input type="text" name="query_string" length="50" value="<?php print stripslashes(htmlentities($query_string)) ?>"/>
  &nbsp;<input type="submit" value="Search"/>&nbsp;&nbsp;<a href="query.php?advanced=yes">Advanced search</a>
  <a href="stats.php">Statistics</a>
</form>

<br>

<div align="center">
<?php
  print 'Searching: ';
  
  if ($sq->is_valid_index()) {
    print $sq->index_count();    
  } else {
    print "0";
  } //else
  
  print ' documents.';
  
  if (!$sq->is_valid_index() and isadmin()) {
    print "<br><br>Admin: There appears to be no index, click <a href='indexersplash.php'>here</a> to create one.";
  } //if
?>
</div>

<?php  
  print_simple_box_end();
  
  if ($sq->is_valid()) {
    print_simple_box_start('center', '50%', 'white', 10);
    
    search_stopwatch();              
    $hit_count = $sq->count();    
    
    print "<br>";

    print $hit_count." results returned for '".stripslashes($query_string)."'.";
    print "<br>";
      
    if ($hit_count > 0) {
      $page_links = $sq->page_numbers();
      $hits       = $sq->results();
        
      print "<ol>";
        
      foreach ($hits as $listing) {
        print "<li value='".($listing->number+1)."'><a href='".$listing->url."'>$listing->title</a><br>\n"
             ."<em>".search_shorten_url($listing->url, 70)."</em><br>\n"        
             ."Type: ".$listing->doctype.", score: ".round($listing->score, 3).", author: ".$listing->author."<br>\n"            
             ."<br></li>\n";
      } //for
      
      print "</ol>";
      print $page_links;
    } //if        
    
    print_simple_box_end();
?>

<div align="center">
  It took <?php search_stopwatch(); ?> to fetch these results.
</div>

<?php
  } //if (sq is valid)
  
  print_simple_box_end();
  print_footer();
?>