<?php
require_once("{$CFG->dirroot}/search/Zend/Search/Lucene.php");

define('DEFAULT_POPUP_SETTINGS', "\"menubar=0,location=0,scrollbars,resizable,width=600,height=450\"");

/**
* a class that represents a single result record of the search engine
*/    
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

    // foresees other caching locations
    public function __construct($mode = 'session') {
        $accepted_modes = array('session');

        if (in_array($mode, $accepted_modes)) {
            $this->mode = $mode;
        } else {
            $this->mode = 'session';
        } //else

        $this->valid = true;
    } //constructor

    /**
    * returns the search cache status
    * @return boolean
    */
    public function can_cache() {
        return $this->valid;
    } //can_cache

    /**
    *
    *
    */
    public function cache($id = false, $object = false) {
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
        } 
        else if ($id and $this->exists($id)) {
            return $this->fetch($id);
        } //else
    } //cache

    /**
    * do key exist in cache ?
    * @param id the object key
    * @return boolean
    */
    private function exists($id) {
        switch ($this->mode) {
            case 'session' :
            return isset($_SESSION[$id]);
        } //switch
    } //exists

    /**
    * clears a cached object in cache
    * @param the object key to clear
    * @return void
    */
    private function clear($id) {
        switch ($this->mode) {
            case 'session' :
                unset($_SESSION[$id]);
                session_unregister($id);
            return;
        } //switch
    } //clear

    /**
    * fetches a cached object
    * @param id the object identifier
    * @return the object cached
    */
    private function fetch($id) {
        switch ($this->mode) {
            case 'session' :
                return ($this->exists($id)) ? unserialize($_SESSION[$id]) : false;
        } //switch
    } //fetch

    /**
    * put an object in cache
    * @param id the key for that object
    * @param object the object to cache as a serialized value
    * @return void
    */
    private function store($id, $object) {
        switch ($this->mode) {
            case 'session' :
                $_SESSION[$id] = serialize($object);
            return;
        } //switch
    } //store
} //SearchCache

/**
* Represents a single query with results
*
*/
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

    /**
    * constructor records query parameters
    *
    */
    public function __construct($term = '', $page = 1, $results_per_page = 10, $cache = false) {
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
    
    /**
    * determines state of query object depending on query entry and 
    * tries to lauch search if all is OK
    * @return void (this is only a state changing trigger).
    */
    public function set_query($term = '') {
        if (!empty($term)) {
            $this->term = $term;
        } //if

        if (empty($this->term)) {
            $this->validquery = false;
        } 
        else {
            $this->validquery = true;
        } //else

        if ($this->validquery and $this->validindex) {
            $this->results = $this->get_results();
        } 
        else {
            $this->results = array();
        } //else
    } //set_query

    /**
    * accessor to the result table.
    * @return an array of result records
    */
    public function results() {
        return $this->results;
    } //results

    /**
    * do the effective collection of results
    *
    */
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
            } 
            else if ($this->pagenumber > $totalpages) {
                $this->pagenumber = $totalpages;
            } //if

            $start = ($this->pagenumber - 1) * $this->results_per_page;
            $end = $start + $this->results_per_page;

            if ($end > $hitcount) {
                $end = $hitcount;
            } //if
        } 
        else {
            $start = 0;
            $end = $hitcount;
        } //else

        $resultdoc  = new SearchResult();
        $resultdocs = array();

        for ($i = $start; $i < $end; $i++) {
            $hit = $hits[$i];

            //check permissions on each result
            if ($this->can_display($USER, $hit->docid, $hit->doctype, $hit->course_id, $hit->group_id, $hit->path, $hit->itemtype, $hit->context_id )) {
                $resultdoc->number  = $i;
                $resultdoc->url     = $hit->url;
                $resultdoc->title   = $hit->title;
                $resultdoc->score   = $hit->score;
                $resultdoc->doctype = $hit->doctype;
                $resultdoc->author  = $hit->author;

                //and store it
                $resultdocs[] = clone($resultdoc);
            } //if
            else{
               // lowers total_results one unit
               $this->total_results--;
            }
        } //foreach

        return $resultdocs;
    } //process_results

    /**
    * get results of a search query using a caching strategy if available
    * @return the result documents as an array of search objects
    */
    private function get_results() {
        $cache = new SearchCache();

        if ($this->cache and $cache->can_cache()) {
            if (!($resultdocs = $cache->cache($this->term))) {
                $resultdocs = $this->process_results();
                //cache the results so we don't have to compute this on every page-load
                $cache->cache($this->term, $resultdocs);
                //print "Using new results.";
            } 
            else {
            //There was something in the cache, so we're using that to save time
            //print "Using cached results.";
            } //else
        } 
        else {
            //no caching :(
            //print "Caching disabled!";
            $resultdocs = $this->process_results();
        } //else

        return $resultdocs;
    } //get_results

    /**
    * constructs the results paging links on results.
    * @return string the results paging links
    */
    public function page_numbers() {
      $pages  = $this->total_pages();
      $query  = htmlentities($this->term);
      $page   = $this->pagenumber;
      $next   = get_string('next', 'search');
      $back   = get_string('back', 'search');

      $ret = "<div align='center' id='search_page_links'>";

      //Back is disabled if we're on page 1
      if ($page > 1) {
        $ret .= "<a href='query.php?query_string={$query}&page=".($page-1)."'>&lt; {$back}</a>&nbsp;";
      } else {
        $ret .= "&lt; {$back}&nbsp;";
      } //else

      //don't <a href> the current page
      for ($i = 1; $i <= $pages; $i++) {
        if ($page == $i) {
          $ret .= "($i)&nbsp;";
        } else {
          $ret .= "<a href='query.php?query_string={$query}&page={$i}'>{$i}</a>&nbsp;";
        } //else
      } //for

      //Next disabled if we're on the last page
      if ($page < $pages) {
        $ret .= "<a href='query.php?query_string={$query}&page=".($page+1)."'>{$next} &gt;</a>&nbsp;";
      } else {
        $ret .= "{$next} &gt;&nbsp;";
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

    /**
    * can the user see this result ?
    * @param user a reference upon the user to be checked for access
    * @param this_id the item identifier
    * @param doctype the search document type. MAtches the module or block or 
    * extra search source definition
    * @param course_id the course reference of the searched result
    * @param group_id the group identity attached to the found resource
    * @param path the path that routes to the local lib.php of the searched 
    * surrounding object fot that document
    * @param item_type a subclassing information for complex module data models
    * // TODO reorder parameters more consistently
    */
    private function can_display(&$user, $this_id, $doctype, $course_id, $group_id, $path, $item_type, $context_id) {
        global $CFG;
       
      /**
      * course related checks
      */
      // admins can see everything, anyway.
      if (isadmin()){
        return true;
      }
            
      // first check course compatibility against user : enrolled users to that course can see. 
      $myCourses = get_my_courses($user->id);
      $unenroled = !in_array($course_id, array_keys($myCourses));
      
      // if guests are allowed, logged guest can see
      $isallowedguest = (isguest()) ? get_field('course', 'guest', 'id', $course_id) : false ;
      
      if ($unenroled && !$isallowedguest){
         return false;
      }

      // if user is enrolled or is allowed user and course is hidden, can he see it ?
      $visibility = get_field('course', 'visible', 'id', $course_id);
      if ($visibility <= 0){
          if (!has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id))){
              return false;
          }
      }

      /**
      * prerecorded capabilities
      */
      // get context caching information and tries to discard unwanted records here

      
      /**
      * final checks
      */
      // then give back indexing data to the module for local check
      include_once "{$CFG->dirroot}/search/documents/{$doctype}_document.php";
      $access_check_function = "{$doctype}_check_text_access";
      
      if (function_exists($access_check_function)){
          $modulecheck = $access_check_function($path, $item_type, $this_id, $user, $group_id, $context_id);
          // echo "module said $modulecheck for item $doctype/$item_type/$this_id";
          return($modulecheck);
      }
        
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