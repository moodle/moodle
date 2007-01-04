<?php
  /**
   * The query page - accepts a user-entered query string and returns results.
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
   */

  require_once('../config.php');
  require_once("$CFG->dirroot/search/lib.php");

  if ($CFG->forcelogin) {
    require_login();
  }

  if (empty($CFG->enableglobalsearch)) {
    error('Global searching is not enabled.');
  }

  $adv = new Object();

  //check for php5, but don't die yet (see line 52)
  if ($check = search_check_php5()) {
    require_once("$CFG->dirroot/search/querylib.php");

    $page_number  = optional_param('page', -1, PARAM_INT);
    $pages        = ($page_number == -1) ? false : true;
    $advanced     = (optional_param('a', '0', PARAM_INT) == '1') ? true : false;
    $query_string = optional_param('query_string', '', PARAM_CLEAN);

    if ($pages && isset($_SESSION['search_advanced_query'])) {
      //if both are set, then we are busy browsing through the result pages of an advanced query
      $adv = unserialize($_SESSION['search_advanced_query']);
    } else if ($advanced) {
      //otherwise we are dealing with a new advanced query
      unset($_SESSION['search_advanced_query']);
      session_unregister('search_advanced_query');

      //chars to strip from strings (whitespace)
      $chars = " \t\n\r\0\x0B,-+";

      //retrieve advanced query variables
      $adv->mustappear  = trim(optional_param('mustappear', '', PARAM_CLEAN), $chars);
      $adv->notappear   = trim(optional_param('notappear', '', PARAM_CLEAN), $chars);
      $adv->canappear   = trim(optional_param('canappear', '', PARAM_CLEAN), $chars);
      $adv->module      = optional_param('module', '', PARAM_CLEAN);
      $adv->title       = trim(optional_param('title', '', PARAM_CLEAN), $chars);
      $adv->author      = trim(optional_param('author', '', PARAM_CLEAN), $chars);
    } //else

    if ($advanced) {
      //parse the advanced variables into a query string
      //TODO: move out to external query class (QueryParse?)

      $query_string = '';

      //get all available module types
      $module_types = array_merge(array('All'), array_values(search_get_document_types()));
      $adv->module = in_array($adv->module, $module_types) ? $adv->module : 'All';

      //convert '1 2' into '+1 +2' for required words field
      if (strlen(trim($adv->mustappear)) > 0) {
        $query_string  = ' +'.implode(' +', preg_split("/[\s,;]+/", $adv->mustappear));
      } //if

      //convert '1 2' into '-1 -2' for not wanted words field
      if (strlen(trim($adv->notappear)) > 0) {
        $query_string .= ' -'.implode(' -', preg_split("/[\s,;]+/", $adv->notappear));
      } //if

      //this field is left untouched, apart from whitespace being stripped
      if (strlen(trim($adv->canappear)) > 0) {
        $query_string .= ' '.implode(' ', preg_split("/[\s,;]+/", $adv->canappear));
      } //if

      //add module restriction
      if ($adv->module != 'All') {
        $query_string .= ' +doctype:'.$adv->module;
      } //if

      //create title search string
      if (strlen(trim($adv->title)) > 0) {
        $query_string .= ' +title:'.implode(' +title:', preg_split("/[\s,;]+/", $adv->title));
      } //if

      //create author search string
      if (strlen(trim($adv->author)) > 0) {
        $query_string .= ' +author:'.implode(' +author:', preg_split("/[\s,;]+/", $adv->author));
      } //if

      //save our options if the query is valid
      if (!empty($query_string)) {
        $_SESSION['search_advanced_query'] = serialize($adv);
      } //if
    } //if

    //normalise page number
    if ($page_number < 1) {
      $page_number = 1;
    } //if

    //run the query against the index
    $sq = new SearchQuery($query_string, $page_number, 10, false);
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

  $vars = get_object_vars($adv);

  if (isset($vars)) {
    foreach ($vars as $key => $value) {
      $adv->$key = stripslashes(htmlentities($value));
    } //foreach
  }
?>

<form id="query" method="get" action="query.php">
  <?php if (!$advanced) { ?>
    <input type="text" name="query_string" length="50" value="<?php print stripslashes(htmlentities($query_string)) ?>" />
    &nbsp;<input type="submit" value="Search" /> &nbsp;
    <a href="query.php?a=1">Advanced search</a> |
    <a href="stats.php">Statistics</a>
  <?php } else {
    print_simple_box_start('center', '', 'white', 10);
  ?>
    <input type="hidden" name="a" value="<?php print $advanced; ?>"/>

    <table border="0" cellpadding="3" cellspacing="3">

    <tr>
      <td width="240">These words must appear:</td>
      <td><input type="text" name="mustappear" length="50" value="<?php print $adv->mustappear; ?>" /></td>
    </tr>

    <tr>
      <td>These words must not appear:</td>
      <td><input type="text" name="notappear" length="50" value="<?php print $adv->notappear; ?>" /></td>
    </tr>

    <tr>
      <td>These words help improve rank:</td>
      <td><input type="text" name="canappear" length="50" value="<?php print $adv->canappear; ?>" /></td>
    </tr>

    <tr>
      <td>Which modules to search?:</td>
      <td>
        <select name="module">
          <?php foreach($module_types as $mod) {
            if ($mod == $adv->module) {
              print "<option value='$mod' selected>$mod</option>\n";
            } else {
              print "<option value='$mod'>$mod</option>\n";
            } //else
          } ?>
        </select>
      </td>
    </tr>

    <tr>
      <td>Words in title:</td>
      <td><input type="text" name="title" length="50" value="<?php print $adv->title; ?>" /></td>
    </tr>

    <tr>
      <td>Author name:</td>
      <td><input type="text" name="author" length="50" value="<?php print $adv->author; ?>" /></td>
    </tr>

    <tr>
      <td colspan="3" align="center"><br /><input type="submit" value="Search" /></td>
    </tr>

    <tr>
      <td colspan="3" align="center">
        <table border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td><a href="query.php">Normal search</a> |</td>
            <td>&nbsp;<a href="stats.php">Statistics</a></td>
          </tr>
        </table>
      </td>
    </tr>
    </table>
  <?php
    print_simple_box_end();
  } //if
  ?>
</form>

<br/>

<?php

  print '<div align="center">';
  print 'Searching: ';

  if ($sq->is_valid_index()) {
    //use cached variable to show up-to-date index size (takes deletions into account)
    print $CFG->search_index_size;
  } else {
    print "0";
  } //else

  print ' documents.';

  if (!$sq->is_valid_index() and isadmin()) {
    print "<p>Admin: There appears to be no search index. Please <a href='indexersplash.php'>create an index</a>.</p>\n";
  } //if

  print '</div>';

  print_simple_box_end();

  if ($sq->is_valid()) {
    print_simple_box_start('center', '50%', 'white', 10);

    search_stopwatch();
    $hit_count = $sq->count();

    print "<br />";

    print $hit_count." results returned for '".stripslashes($query_string)."'.";
    print "<br />";

    if ($hit_count > 0) {
      $page_links = $sq->page_numbers();
      $hits       = $sq->results();

      if ($advanced) {
        //if in advanced mode, search options are saved in the session, so
        //we can remove the query string var from the page links, and replace
        //it with a=1 (Advanced = on) instead
        $page_links = preg_replace("/query_string=[^&]+/", 'a=1', $page_links);
      } //if

      print "<ol>";

      foreach ($hits as $listing) {
        print "<li value='".($listing->number+1)."'><a href='".$listing->url."'>$listing->title</a><br />\n"
             ."<em>".search_shorten_url($listing->url, 70)."</em><br />\n"
             ."Type: ".$listing->doctype.", score: ".round($listing->score, 3).", author: ".$listing->author."\n"
             ."</li>\n";
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