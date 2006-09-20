<?php
  require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");

  class SearchResult {
    public  $url,
            $title,
            $doctype,
            $author,
            $score,
            $number;
  } //SearchResult


  //split this into Cache class and extend to SearchCache?
  class SearchCache {
    private $mode,
            $valid;

    public function __construct($mode='session') {
      $accepted_modes = array('session');

      if (in_array($mode, $accepted_modes)) {
        $this->mode = $mode;
      } else {
        $this->mode = 'session';
      } //else

      $this->valid = true;
    } //constructor

    public function can_cache() {
      return $this->valid;
    } //can_cache

    public function cache($id=false, $object=false) {
      //see if there was a previous query
      $last_term = $this->fetch('search_last_term');

      //if this query is different from the last, clear out the last one
      if ($id != false and $last_term != $id) {
        $this->clear($last_term);
      } //if

      //store the new query if id and object are passed in
      if ($object and $id) {
        $this->store('search_last_term', $id);
        $this->store($id, $object);
        return true;
      //otherwise return the stored results
      } else if ($id and $this->exists($id)) {
        return $this->fetch($id);
      } //else
    } //cache

    private function exists($id) {
      switch ($this->mode) {
        case 'session' :
          return isset($_SESSION[$id]);
      } //switch
    } //exists

    private function clear($id) {
      switch ($this->mode) {
        case 'session' :
          unset($_SESSION[$id]);
          session_unregister($id);
          return;
      } //switch
    } //clear

    private function fetch($id) {
      switch ($this->mode) {
        case 'session' :
          return ($this->exists($id)) ? unserialize($_SESSION[$id]) : false;
      } //switch
    } //fetch

    private function store($id, $object) {
      switch ($this->mode) {
        case 'session' :
          $_SESSION[$id] = serialize($object);
          return;
      } //switch
    } //store
  } //SearchCache


  class SearchQuery {
    private $index,
            $term,
            $pagenumber,
            $cache,
            $validquery,
            $validindex,
            $results,
            $results_per_page,
            $total_results;

    public function __construct($term='', $page=1, $results_per_page=10, $cache=false) {
      global $CFG;

      $this->term       = $term;
      $this->pagenumber = $page;
      $this->cache      = $cache;
      $this->validquery = true;
      $this->validindex = true;
      $this->results_per_page = $results_per_page;

      $index_path = SEARCH_INDEX_PATH;

      try {
        $this->index = new Zend_Search_Lucene($index_path, false);
      } catch(Exception $e) {
        $this->validindex = false;
        return;
      } //catch

      if (empty($this->term)) {
        $this->validquery = false;
      } else {
        $this->set_query($this->term);
      } //else
    } //constructor

    public function set_query($term='') {
      if (!empty($term)) {
        $this->term = $term;
      } //if

      if (empty($this->term)) {
        $this->validquery = false;
      } else {
        $this->validquery = true;
      } //else

      if ($this->validquery and $this->validindex) {
        $this->results = $this->get_results();
      } else {
        $this->results = array();
      } //else
    } //set_query

    public function results() {
      return $this->results;
    } //results

    private function process_results($all=false) {
      global $USER;

      $term = strtolower($this->term);

      //experimental - return more results
      $strip_arr = array('author:', 'title:', '+', '-', 'doctype:');
      $stripped_term = str_replace($strip_arr, '', $term);

      $hits = $this->index->find($term." title:".$stripped_term." author:".$stripped_term);
      //--

      $hitcount = count($hits);
      $this->total_results = $hitcount;

      if ($hitcount == 0) return array();

      $totalpages = ceil($hitcount/$this->results_per_page);

      if (!$all) {
        if ($hitcount < $this->results_per_page) {
          $this->pagenumber = 1;
        } else if ($this->pagenumber > $totalpages) {
          $this->pagenumber  =$totalpages;
        } //if

        $start = ($this->pagenumber - 1) * $this->results_per_page;
        $end = $start + $this->results_per_page;

        if ($end > $hitcount) {
          $end = $hitcount;
        } //if
      } else {
        $start = 0;
        $end = $hitcount;
      } //else

      $resultdoc  = new SearchResult();
      $resultdocs = array();

      for ($i = $start; $i < $end; $i++) {
        $hit = $hits[$i];

        //check permissions on each result
        if ($this->can_display($USER, $hit->id, $hit->doctype, $hit->course_id, $hit->group_id)) {
          $resultdoc->number  = $i;
          $resultdoc->url     = $hit->url;
          $resultdoc->title   = $hit->title;
          $resultdoc->score   = $hit->score;
          $resultdoc->doctype = $hit->doctype;
          $resultdoc->author  = $hit->author;

          //and store it
          $resultdocs[] = clone($resultdoc);
        } //if
      } //foreach

      return $resultdocs;
    } //process_results

    private function get_results() {
      $cache = new SearchCache();

      if ($this->cache and $cache->can_cache()) {
        if (!($resultdocs = $cache->cache($this->term))) {
          $resultdocs = $this->process_results();
          //cache the results so we don't have to compute this on every page-load
          $cache->cache($this->term, $resultdocs);
          //print "Using new results.";
        } else {
          //There was something in the cache, so we're using that to save time
          //print "Using cached results.";
        } //else
      } else {
        //no caching :(
        //print "Caching disabled!";
        $resultdocs = $this->process_results();
      } //else

      return $resultdocs;
    } //get_results

    public function page_numbers() {
      $pages  = $this->total_pages();
      $query  = htmlentities($this->term);
      $page   = $this->pagenumber;
      $next   = "Next";
      $back   = "Back";

      $ret = "<div align='center' id='search_page_links'>";

      //Back is disabled if we're on page 1
      if ($page > 1) {
        $ret .= "<a href='query.php?query_string=$query&page=".($page-1)."'>< $back</a>&nbsp;";
      } else {
        $ret .= "< $back&nbsp;";
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
        $ret .= "<a href='query.php?query_string=$query&page=".($page+1)."'>$next ></a>&nbsp;";
      } else {
        $ret .= "$next >&nbsp;";
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

    //can the user see this result?
    private function can_display(&$user, $this_id, $doctype, $course_id, $group_id) {
      //this function should return true/false depending on
      //whether or not a user can see this resource
      //..
      //if one of you nice moodlers see this, feel free to
      //implement it for me .. :-P
      return true;
    } //can_display

    public function count() {
      return $this->total_results;
    } //count

    public function is_valid() {
      return ($this->validquery and $this->validindex);
    } //is_valid

    public function is_valid_query() {
      return $this->validquery;
    } //is_valid_query

    public function is_valid_index() {
      return $this->validindex;
    } //is_valid_index

    public function total_pages() {
      return ceil($this->count()/$this->results_per_page);
    } //pages

    public function get_pagenumber() {
      return $this->pagenumber;
    } //get_pagenumber

    public function get_results_per_page() {
      return $this->results_per_page;
    } //get_results_per_page
  } //SearchQuery

?>