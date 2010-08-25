<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*/

/**
* includes and requires
*/
require_once($CFG->dirroot.'/search/Zend/Search/Lucene.php');

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
        $number,
        $courseid;
}


/**
* split this into Cache class and extend to SearchCache?
*/
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
    }

    /**
    * returns the search cache status
    * @return boolean
    */
    public function can_cache() {
        return $this->valid;
    }

    /**
    *
    *
    */
    public function cache($id = false, $object = false) {
        //see if there was a previous query
        $last_term = $this->fetch('search_last_term');

        //if this query is different from the last, clear out the last one
        if ($id != false && $last_term != $id) {
            $this->clear($last_term);
        }

        //store the new query if id and object are passed in
        if ($object && $id) {
            $this->store('search_last_term', $id);
            $this->store($id, $object);
            return true;
        //otherwise return the stored results
        } else if ($id && $this->exists($id)) {
            return $this->fetch($id);
        }
    }

    /**
    * do key exist in cache ?
    * @param id the object key
    * @return boolean
    */
    private function exists($id) {
        switch ($this->mode) {
            case 'session' :
            return isset($_SESSION[$id]);
        }
    }

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
        }
    }

    /**
    * fetches a cached object
    * @param id the object identifier
    * @return the object cached
    */
    private function fetch($id) {
        switch ($this->mode) {
            case 'session' :
                return ($this->exists($id)) ? unserialize($_SESSION[$id]) : false;
        }
    }

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
        }
    }
}

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
        }

        if (empty($this->term)) {
            $this->validquery = false;
        } else {
            $this->set_query($this->term);
        }
    }

    /**
    * determines state of query object depending on query entry and
    * tries to lauch search if all is OK
    * @return void (this is only a state changing trigger).
    */
    public function set_query($term = '') {
        if (!empty($term)) {
            $this->term = $term;
        }

        if (empty($this->term)) {
            $this->validquery = false;
        } else {
            $this->validquery = true;
        }

        if ($this->validquery and $this->validindex) {
            $this->results = $this->get_results();
        } else {
            $this->results = array();
        }
    }

    /**
    * accessor to the result table.
    * @return an array of result records
    */
    public function results() {
        return $this->results;
    }

    /**
    * do the effective collection of results
    * @param boolean $all
    * @uses USER
    */
    private function process_results($all=false) {
        global $USER;

        // unneeded since changing the default Zend Lexer
        // $term = mb_convert_case($this->term, MB_CASE_LOWER, 'UTF-8');
        $term = $this->term;
        $page = optional_param('page', 1, PARAM_INT);

        //experimental - return more results
        // $strip_arr = array('author:', 'title:', '+', '-', 'doctype:');
        // $stripped_term = str_replace($strip_arr, '', $term);

        // $search_string = $term." title:".$stripped_term." author:".$stripped_term;
        $search_string = $term;
        $hits = $this->index->find($search_string);
        //--

        $hitcount = count($hits);
        $this->total_results = $hitcount;

        if ($hitcount == 0) return array();

        $resultdoc  = new SearchResult();
        $resultdocs = array();
        $searchables = search_collect_searchables(false, false);

        $realindex = 0;

        /**
        if (!$all) {
            if ($finalresults < $this->results_per_page) {
                $this->pagenumber = 1;
            } elseif ($this->pagenumber > $totalpages) {
                $this->pagenumber = $totalpages;
            }

            $start = ($this->pagenumber - 1) * $this->results_per_page;
            $end = $start + $this->results_per_page;

            if ($end > $finalresults) {
                $end = $finalresults;
            }
        } else {
            $start = 0;
            $end = $finalresults;
        } */

        for ($i = 0; $i < min($hitcount, ($page) * $this->results_per_page); $i++) {
            $hit = $hits[$i];

            //check permissions on each result
            if ($this->can_display($USER, $hit->docid, $hit->doctype, $hit->course_id, $hit->group_id, $hit->path, $hit->itemtype, $hit->context_id, $searchables )) {
                if ($i >= ($page - 1) * $this->results_per_page){
                    $resultdoc->number  = $realindex;
                    $resultdoc->url     = $hit->url;
                    $resultdoc->title   = $hit->title;
                    $resultdoc->score   = $hit->score;
                    $resultdoc->doctype = $hit->doctype;
                    $resultdoc->author  = $hit->author;
                    $resultdoc->courseid = $hit->course_id;
                    $resultdoc->userid = $hit->user_id;

                    //and store it
                    $resultdocs[] = clone($resultdoc);
                }
                $realindex++;
            } else {
               // lowers total_results one unit
               $this->total_results--;
            }
        }

        $totalpages = ceil($this->total_results/$this->results_per_page);


        return $resultdocs;
    }

    /**
    * get results of a search query using a caching strategy if available
    * @return the result documents as an array of search objects
    */
    private function get_results() {
        $cache = new SearchCache();

        if ($this->cache && $cache->can_cache()) {
            if (!($resultdocs = $cache->cache($this->term))) {
                $resultdocs = $this->process_results();
                //cache the results so we don't have to compute this on every page-load
                $cache->cache($this->term, $resultdocs);
                //print "Using new results.";
            } else {
            //There was something in the cache, so we're using that to save time
            //print "Using cached results.";
            }
        } else {
            //no caching :(
            // print "Caching disabled!";
            $resultdocs = $this->process_results();
        }
        return $resultdocs;
    }

    /**
    * constructs the results paging links on results.
    * @return string the results paging links
    */
    public function page_numbers() {
      $pages  = $this->total_pages();
      $query = htmlentities($this->term,ENT_NOQUOTES,'utf-8');
      $page   = $this->pagenumber;
      $next   = get_string('next', 'search');
      $back   = get_string('back', 'search');

      $ret = "<div align='center' id='search_page_links'>";

      //Back is disabled if we're on page 1
      if ($page > 1) {
        $ret .= "<a href='query.php?query_string={$query}&page=".($page-1)."'>&lt; {$back}</a>&nbsp;";
      } else {
        $ret .= "&lt; {$back}&nbsp;";
      }

      //don't <a href> the current page
      for ($i = 1; $i <= $pages; $i++) {
        if ($page == $i) {
          $ret .= "($i)&nbsp;";
        } else {
          $ret .= "<a href='query.php?query_string={$query}&page={$i}'>{$i}</a>&nbsp;";
        }
      }

      //Next disabled if we're on the last page
      if ($page < $pages) {
        $ret .= "<a href='query.php?query_string={$query}&page=".($page+1)."'>{$next} &gt;</a>&nbsp;";
      } else {
        $ret .= "{$next} &gt;&nbsp;";
      }

      $ret .= "</div>";

      //shorten really long page lists, to stop table distorting width-ways
      if (strlen($ret) > 70) {
        $start = 4;
        $end = $page - 5;
        $ret = preg_replace("/<a\D+\d+\D+>$start<\/a>.*?<a\D+\d+\D+>$end<\/a>/", '...', $ret);

        $start = $page + 5;
        $end = $pages - 3;
        $ret = preg_replace("/<a\D+\d+\D+>$start<\/a>.*?<a\D+\d+\D+>$end<\/a>/", '...', $ret);
      }

      return $ret;
    }

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
    * @uses CFG
    * // TODO reorder parameters more consistently
    */
    private function can_display(&$user, $this_id, $doctype, $course_id, $group_id, $path, $item_type, $context_id, &$searchables) {
        global $CFG, $DB;

      /**
      * course related checks
      */
      // admins can see everything, anyway.
      if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))){
        return true;
      }

        // first check course compatibility against user : enrolled users to that course can see.
        $myCourses = enrol_get_users_courses($user->id, true);
        $unenroled = !in_array($course_id, array_keys($myCourses));

        // if guests are allowed, logged guest can see
        $isallowedguest = false; //TODO: this will be harder to do now because we do not have guest field in course table any more

        if ($unenroled && !$isallowedguest){
            return false;
        }

        // if user is enrolled or is allowed user and course is hidden, can he see it ?
        $visibility = $DB->get_field('course', 'visible', array('id' => $course_id));
        if ($visibility <= 0){
            if (!has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course_id))){
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
        $searchable_instance = $searchables[$doctype];
        if ($searchable_instance->location == 'internal'){
            include_once "{$CFG->dirroot}/search/documents/{$doctype}_document.php";
        } else {
            include_once "{$CFG->dirroot}/{$searchable_instance->location}/{$doctype}/search_document.php";
        }
        $access_check_function = "{$doctype}_check_text_access";

        if (function_exists($access_check_function)){
            $modulecheck = $access_check_function($path, $item_type, $this_id, $user, $group_id, $context_id);
            // echo "module said $modulecheck for item $doctype/$item_type/$this_id";
            return($modulecheck);
        }

        return true;
    }

    /**
    *
    */
    public function count() {
      return $this->total_results;
    } //count

    /**
    *
    */
    public function is_valid() {
      return ($this->validquery and $this->validindex);
    }

    /**
    *
    */
    public function is_valid_query() {
      return $this->validquery;
    }

    /**
    *
    */
    public function is_valid_index() {
      return $this->validindex;
    }

    /**
    *
    */
    public function total_pages() {
      return ceil($this->count()/$this->results_per_page);
    }

    /**
    *
    */
    public function get_pagenumber() {
      return $this->pagenumber;
    }

    /**
    *
    */
    public function get_results_per_page() {
      return $this->results_per_page;
    }
}
?>