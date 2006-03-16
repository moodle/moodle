<?php // $Id$
/*******************************************************************
 * This class represents a set of active filters to be applied 
 * in searching for or presenting blog entries.
 * Retrieve  filtered entries by calling get_filtered_entries 
 * rather than directly accessing the array as
 * the function will fetch the entries for you if needed.
 *
 * @copyright 2003/2004/2005, Daryl Hawes ({@link http://www.cocoaobjects.com})
 * @author Daryl Hawes
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blog
 ******************************************************************/

include_once($CFG->dirroot.'/blog/lib.php');

/*******************************************************************
 * This class represents a set of active filters to be applied 
 * in searching for or presenting blog entries. 
 * Retrieve filtered entries by calling get_filtered_entries 
 * rather than directly accessing the array as
 * the function will fetch the entries for you if needed.
 ******************************************************************/
class BlogFilter {
    // member variables
    // you can use variable names directly to access properties.
    //ie. $blogFilter->month
    
    var $startmonth; 
    var $startday;
    var $startyear;
    var $endmonth; 
    var $endday;
    var $endyear;
    var $tstart; //start time, calculated from the values of startmonth, startday and startyear
    var $tend; //end time, calculated from the values of endmonth, endday and endyear
    var $fetchlimit; //max # of entries to read from database
    var $fetchstart; //entry # to start reading from database at
    var $max_entries; //maximum number of matching entries available in database
    var $sort; 
    var $courseid;
    var $userid; // moodle userid to specify a specific user's blog
    var $postid; //id of a single blog entry
    var $categoryid;
    var $groupid;
    var $blogtitle;
    var $blogtagline;
    var $blogtheme;
    var $blogInfo;
    var $memberlist; //do not access directly - use getter get_member_list()
    var $filtered_entries = array();
    var $baseurl;
    var $filtertype;
    var $filterselect;
    var $tag;
    var $keywords = NULL; //array of $keywordtype = $keywordstring

    /**
     * BlogFilter 
     * class constructor that will build a new instance of a BlogFilter object
     *
     * @param  int $userid = the blog that the entries are to be found in. If 0 then all blogs are searched.
     * @param  int $courseid = if needed the entries can be restricted to those associated with a given course.
     * @param  int $postid = a specific blog entry that is being sought
     */
    function BlogFilter($userid='', $postid='', $fetchlimit='', $fetchstart='', $filtertype='', $filterselect='', $tagid='', $tag ='', $sort='lastmodified DESC') {

        global $CFG;    //filter settings to be pass in for baseurl

        if (!empty($userid) && $userid != 0 && $userid != '') {
//            print "creating blogInfo object for user with id '$userid'<br />"; //debug
            $this->blogInfo =& new BlogInfo($userid);
        }
        if ( empty($this->blogInfo) || empty($this->blogInfo->userid)) {
            unset($this->blogInfo);
            $this->blogtitle = '';
            $this->blogtagline = '';
            $this->blogtheme = '';
        } else {

            $this->blogtitle = &$this->blogInfo->blogtitle;
            $this->blogtagline = &$this->blogInfo->blogtagline;
        }
        
        if (! is_numeric($userid) ) {
            $this->userid = 0;
        } else {
            $this->userid = $userid;
        }
        if (!is_numeric($fetchstart) ) {
            $this->fetchstart = 0;
        } else {
            $this->fetchstart = $fetchstart;
        }
        
        if (!is_numeric($fetchlimit) ) {
            $this->fetchlimit = $CFG->blog_default_fetch_num_entries;
        } else {
            $this->fetchlimit = $fetchlimit;
        }
        
        $this->postid = $postid;

        $this->sort = $sort;
        $this->filtertype = $filtertype;
        $this->filterselect = $filterselect;
        if ($tagid) {
            $this->tag = $tagid;
        } else if ($tag) {
            $tagrec = get_record('tags', 'text', $tag);
            $this->tag = $tagrec -> id;
        }
        // borrowed from Jon's table class
        if(empty($this->baseurl)) {

            $getcopy  = $_GET;
            unset($getcopy['blogpage']);
            
            $strippedurl = strip_querystring(qualified_me());
            if(!empty($getcopy)) {
                $first = false;
                $querystring = '';    
                foreach($getcopy as $var => $val) {
                    if(!$first) {
                        $first = true;
                        if ($var != 'filterselect' && $var != 'filtertype') {
                            $querystring .= '?'.$var.'='.$val;
                            $hasparam = true;
                        } else {
                            $querystring .= '?';
                        }
                    } else {
                        if ($var != 'filterselect' && $var != 'filtertype') {
                            $querystring .= '&amp;'.$var.'='.$val;
                            $hasparam = true;
                        }
                    }
                }
                if (isset($hasparam)) {
                    $querystring .= '&amp;';
                } else {
                    $querystring = '?';
                }
            } else {
                $querystring = '?';
            }
            
            $this->baseurl = strip_querystring(qualified_me()) . $querystring. 'filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;';

        }
    }

    /**
     * borrowed from Jon's table class.
     */
    function define_baseurl($url) {
        if(!strpos($url, '?')) {
            $this->baseurl = $url.'?';
        }
        else {
            $this->baseurl = $url.'&amp;';
        }
    }
    
    /**
     *
     */
    function set_filtered_entries(&$blogentries) {
        $this->filtered_entries = $blogentries;
    }
    
    /**
     * @return array Blog entries based on current filters
     */
    function get_filtered_entries() {
        
        if ( empty($this->filtered_entries) ) {
            //no entries defined. try to fetch them.
            $this->fetch_entries();
        }
        
        if (!empty($this->filtered_entries)) {
            //we have entries - return them
            return $this->filtered_entries;
        }
        //still no entries - they must all be filtered away or there simply are none. return null.
        return NULL;
    }
        
    /**
     * Using the member variables build a where clause and sql statment
     * and fetch the correct blog entries from the database. The entries
     * are then stored in the filtered_entries member variable.
     *
     * @uses $CFG
     * @uses $USER
     * @limit, if limit is false, then return all records
     */
    function fetch_entries($limit=true) {
        global $CFG, $USER;
        
/*
        echo "<br />filter trying to do its job";
        echo "<br />filtertype = $this->filtertype";
        echo "<br />filterselect = $this->filterselect";
        */
        if (!isset($USER->id)) {
            $USER->id = 0;    //hack, for guests
        }
        
        if ($this->tag) {
            $tagtablesql = $CFG->prefix.'blog_tag_instance bt, ';
            $tagquerysql = ' AND bt.entryid = p.id AND bt.tagid = '.$this->tag.' ';
        } else {
            $tagtablesql = '';
            $tagquerysql = '';
        }
        
        /****************************************
         * depending on the type, there are 4   *
         * different possible sqls              *
         ****************************************/
        switch ($this->filtertype) {

            case 'site':
            
                if (!isguest() && isloggedin()) {

                    $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                            AND u.deleted = 0';

                } else {

                    $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND p.publishstate = \'public\'
                            AND u.deleted = 0';
                }
            
            break;
            
            case 'course':
                if ($this->filterselect != SITEID) {
                    $SQL = '(SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user_students u
                            WHERE p.userid = u.userid '.$tagquerysql.'
                            AND u.course = '.$this->filterselect.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.'))

                            UNION

                            (SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user_teachers u
                            WHERE p.userid = u.userid '.$tagquerysql.'
                            AND u.course = '.$this->filterselect.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.'))';    //this will break for postgres, i think
                } else {

                    if (isloggedin()) {

                        $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                                AND u.deleted = 0';

                    } else {

                        $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND p.publishstate = \'public\'
                                AND u.deleted = 0';
                    }

                }
            
            break;
            
            case 'group':

                $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'groups_members m
                        WHERE p.userid = m.userid '.$tagquerysql.'
                        AND m.groupid = '.$this->filterselect.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';
            
            break;
            
            case 'user':

                $SQL = 'SELECT p.* FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'user u
                        WHERE p.userid = u.id '.$tagquerysql.'
                        AND u.id = '.$this->filterselect.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';
            
            break;


        }
        
        if ($this->fetchstart !== '' && $limit) {    //this can be changed to use mysql_paging_limit
            $limit = sql_paging_limit($this->fetchstart, $this->fetchlimit);
        } else {
            $limit = '';
        }
        
        $orderby = ' ORDER BY '. $this->sort .' ';

        //echo 'Debug: BlogFilter fetch_entries() sql="'. $SQL . $orderby . $limit .'"<br />'. $this->categoryid; //debug

        $records = get_records_sql($SQL . $orderby . $limit);

//        print_object($records); //debug
       
        if (empty($records)) {
            return array();
        } else {
            $blogEntries = array();
            foreach($records as $record) {
                $blogEntry = new BlogEntry($record);
                $blogEntries[] = $blogEntry;
            }
        }

//        echo 'Debug: blog entries retrieved in fetch_entries function  of BlogFilter class:<br />'; //debug
//        print_object($blogEntries); //debug

        $this->filtered_entries = $blogEntries;

        return $this->filtered_entries;
    }

    /**
     * get the count of viewable entries, easiest way is to count fetch_entries
     * this is used for print_paging_bar
     */
    function get_viewable_entry_count($where='', $hascats=false) {
        $blogEntries = $this->fetch_entries(false);
        return count($blogEntries);
    }

    /**
     * get count of entries as they have been fetched from the fully filtered query
     */
    function get_filtered_entry_count() {
        global $CFG;
        //might need to use a count_records_sql.
        $entries = $this->get_filtered_entries();
        return count($entries);
    }
        
    /**
     * Use this function to retrieve a link to a blog page (typically not the one 
     * you are currently processing) which contains the correct blog filter information
     * to maintain the user's filtered view when progressing from page to page.
     *
     * The unused param is defined as either
     * <code>
     * $unused = array('userid', 'courseid', 'groupid');
     * </code>
     * or
     * <code>
     * $unused = 'startyear';
     * </code>
     * @param string $baseurl The url to be added to the full href before the getvars
     * @param array|string $unused Can be an array of ivar names or a single variable name
     * @return string A link to the specified baseurl along with the correct getvars for this filter.
     */
    function get_complete_link($baseurl, $linktext, $unused='') {
        $getargs = $this->get_getvars($unused);
        $link = '<a href="'. $baseurl;
        $link .= $getargs . '">';
        $link .= $linktext . '</a>';
        return $link;
    }

    /**
    * The unused param is defined as either
    * <code>
    * $unused = array('userid', 'courseid', 'groupid');
    * </code>
    * or
    * <code>
    * $unused = 'startyear';
    * </code>
    * @param array|string $unused Can be an array of ivar names or a single variable name
    */
    function get_getvars($unused) {
        $getargs = '?';
        if(!is_array($unused)) {
            $unused = array($unused);
        }
        if (!is_array($unused)) {
            //argument is not an array, hopefully it's a string. wrap it in an array for comparisons below.
            $unused = array($unused);
        }
        if (!in_array('startmonth', $unused)) {
            $getargs .= '&amp;m=' . $this->startmonth;
        }
        if (!in_array('startday', $unused)) {
            $getargs .= '&amp;d=' . $this->startday;
        }
        if (!in_array('startyear', $unused)) {
            $getargs .= '&amp;y=' . $this->startyear;
        }
        if (!in_array('limit', $unused)) {
            $getargs .= '&amp;limit=' . $this->fetchlimit;
        }
        if (!in_array('formstart', $unused)) {
            $getargs .= '&amp;formstart=' . $this->fetchstart;
        }
        if (!in_array('courseid', $unused)) {
            $getargs .= '&amp;courseid=' . $this->courseid;
        }
        if (!in_array('userid', $unused)) {
            $getargs .= '&amp;userid=' . $this->userid;
        }
        if (!in_array('categoryid', $unused)) {
            $getargs .= '&amp;categoryid=' . $this->categoryid;
        }
        if (!in_array('groupid', $unused)) {
            $getargs .= '&amp;groupid=' . $this->groupid;
        }
        return $getargs;
    }

}    //end class BlogFilter
?>
