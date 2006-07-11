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
    
  //check for php5, but don't die yet (see line 27)
  if ($check = search_check_php5()) {  
    require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
    
    $query_string = optional_param('query_string', '', PARAM_CLEAN);
    $page_number  = optional_param('page', 1, PARAM_INT);
    
    if ($page_number < 1) {
      $page_number = 1;
    } //if
        
    $index_path = "$CFG->dataroot/search";
    $no_index = false; //optimism!
    $results_per_page = 10;
    
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
  <input type="text" name="query_string" length="50" value="<?php print stripslashes(htmlentities($query_string)) ?>"/>
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
    $hit_count = count($hits);
    
    print "<br>";

    print $hit_count." results returned for '".stripslashes($query_string)."'.";
    print "<br>";
      
    if ($hit_count > 0) {                        
      if ($hit_count < $results_per_page) {
        $page_number = 1;
      } else if ($page_number > ceil($hit_count/$results_per_page)) {
        $page_number = $hit_count/$results_per_page;
      } //if
    
      $start = ($page_number - 1)*$results_per_page;
      $end = $start + $results_per_page;
        
      print "<ol>";
        
      for ($i = $start; $i < $end; $i++) {
        if ($i >= $hit_count) {
          break;
        } //if
      
        $listing = $hits[$i];
      
        print "<li value='".($i+1)."'><a href='".$listing->url."'>$listing->title</a><br>\n"
             ."<em>".search_shorten_url($listing->url, 70)."</em><br>\n"        
             ."Type: ".$listing->doctype.", score: ".round($listing->score, 3).", author: ".$listing->author."<br>\n"            
             ."<br></li>\n";
      } //for
      
      print "</ol>";
    } //if
    
    print "<div align='center'>";
    
    if ($page_number > 1) {
      print "<a href='query.php?query_string=$query_string&page=".($page_number-1)."'>< Back</a>&nbsp;";
    } else {
      print "< Back&nbsp;";
    } //else
    
    for ($i = 1; $i <= ceil($hit_count/$results_per_page); $i++) {
      if ($page_number == $i) {
        print "[$i]&nbsp;";
      } else {
        print "<a href='query.php?query_string=$query_string&page=$i'>$i</a>&nbsp;";
      } //else
    } //for
    
    if ($page_number < ceil($hit_count/$results_per_page)) {      
      print "<a href='query.php?query_string=$query_string&page=".($page_number+1)."'>Next ></a>&nbsp;";
    } else {
      print "Next >&nbsp;";
    } //else
    
    print "</div>";
    
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