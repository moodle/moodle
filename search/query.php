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
  
  
  //Result document class that contains all the display information we need
  class ResultDocument {
    public  $url,
            $title,
            $score,
            $doctype,
            $author;
  } //ResultDocument  

  //generates an HTML string of links to result pages
  function page_numbers($query, $hits, $page=1, $results_per_page=20) {
    //total result pages
    $pages = ceil($hits/$results_per_page);
    
    $ret = "<div align='center'>";    
    
    //Back is disabled if we're on page 1
    if ($page > 1) {
      $ret .= "<a href='query.php?query_string=$query&page=".($page-1)."'>< Back</a>&nbsp;";
    } else {
      $ret .= "< Back&nbsp;";
    } //else
    
    //don't <a href> the current page
    for ($i = 1; $i <= $pages; $i++) {
      if ($page == $i) {
        $ret .= "[$i]&nbsp;";
      } else {
        $ret .= "<a href='query.php?query_string=$query&page=$i'>$i</a>&nbsp;";
      } //else
    } //for
    
    //Next disabled if we're on the last page
    if ($page < $pages) {      
      $ret .= "<a href='query.php?query_string=$query&page=".($page+1)."'>Next ></a>&nbsp;";
    } else {
      $ret .= "Next >&nbsp;";
    } //else
    
    $ret .= "</div>";    
    
    //shorten really long page lists, to stop table distorting width-ways
    if (strlen($ret) > 70) {
      $start = 4;
      $end = $page - 5;     
      $ret = preg_replace("/<a\D+\d+\D+>$start<\/a>.*?<a\D+\d+\D+>$end<\/a>/", '...', $ret);

      $start = $page + 5;
      $end = $pages - 3;      
      $ret = preg_replace("/<a\D+\d+\D+>$start<\/a>.*?<a\D+\d+\D+>$end<\/a>/", '...', $ret);
    } //if
    
    return $ret;
  } //page_numbers
  
  //calculates whether a user is allowed to see this result
  function can_display(&$user, $course_id, $group_id) {
    return true;
  } //can_display
  
  //caches the results of the last query, deletes the previous one also
  function cache($id=false, &$object=false) {
    //see if there was a previous query
    $last_term = (isset($_SESSION['search_last_term'])) ? $_SESSION['search_last_term'] : false;
    
    //if this query is different from the last, clear out the last one
    if ($id != false and $last_term != $id) {
      unset($_SESSION[$last_term]);
      session_unregister($last_term);
    } //if
    
    //store the new query if id and object are passed in
    if ($object and $id) {
      $_SESSION['search_last_term'] = $id;
      $_SESSION[$id] = $object;
      return true;
    //otherwise return the stored results
    } else if ($id and isset($_SESSION[$id])) {
      return $_SESSION[$id];
    } //else
  } //cache
  
  
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
    
    //if the cache is empty
    if (!($hits = cache($query_string))) {
      $resultdocs = array();
      $resultdoc  = new ResultDocument;
      
      //generate a new result-set      
      $hits = $index->find(strtolower($query_string));
      
      foreach ($hits as $hit) {
        //check permissions on each result
        if (can_display($USER, $hit->course_id, $hit->group_id)) {
          $resultdoc->url     = $hit->url;
          $resultdoc->title   = $hit->title;
          $resultdoc->score   = $hit->score;
          $resultdoc->doctype = $hit->doctype;
          $resultdoc->author  = $hit->author;
          
          //and store it if it passes the test
          $resultdocs[] = clone($resultdoc);
        } //if
      } //foreach
      
      //cache the results so we don't have to compute this on every page-load
      cache($query_string, $resultdocs);
      
      //print "Using new results.";
    } else {
      //There was something in the cache, so we're using that to save time
      //print "Using cached results.";
    } //else
            
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

      $page_links = page_numbers($query_string, $hit_count, $page_number, $results_per_page);
        
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
      print $page_links;
    } //if        
    
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