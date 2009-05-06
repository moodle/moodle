<?php  // $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
* Definition of blog page type.
 */
define('PAGE_BLOG_VIEW', 'blog-view');

// Blog class derived from moodle's page class
class page_blog extends page_base {

    var $editing = false;
    var $filtertype = NULL;
    var $filterselect = NULL;
    var $tagid = NULL;

    // Do any validation of the officially recognized bits of the data and forward to parent.
    // Do NOT load up "expensive" resouces (e.g. SQL data) here!
    function init_quick($data) {
        parent::init_quick($data);
        if (empty($data->pageid)) {
            //if no pageid then the user is viewing a collection of blog entries
            $this->id = 0; //set blog id to 0
        }
    }

    /**
     * Here you should load up all heavy-duty data for your page. Basically everything that
     * does not NEED to be loaded for the class to make basic decisions should NOT be loaded
     * in init_quick() and instead deferred here. Of course this function had better recognize
     * $this->full_init_done to prevent wasteful multiple-time data retrieval.
     */
    function init_full() {
        global $DB;

        if ($this->full_init_done) {
            return;
        }
        // I need to determine how best to utilize this function. Most init
        // is already done before we get here in blogFilter and blogInfo

        if ($this->courseid == 0 || $this->courseid == 1 || !is_numeric($this->courseid) ) {
            $this->courseid = '';
        }
        $this->full_init_done = true;
    }

    // For this test page, only admins are going to be allowed editing (for simplicity).
    function user_allowed_editing() {
        if (isloggedin() && !isguest()) {
            return true;
        }
        return false;
    }

    // Also, admins are considered to have "always on" editing (I wanted to avoid duplicating
    // the code that turns editing on/off here; you can roll your own or copy course/view.php).
    function user_is_editing() {
        global $SESSION;

        if (isloggedin() && !isguest()) {
            $this->editing = !empty($SESSION->blog_editing_enabled);
            return $this->editing;
        }
        return false;
    }

    //over-ride parent method's print_header because blog already passes more than just the title along
    function print_header($pageTitle='', $pageHeading='', $pageNavigation='', $pageFocus='', $pageMeta='') {
        global $USER;

        $this->init_full();
        $extraheader = '';
        if (!empty($USER) && !empty($USER->id)) {
            $extraheader = $this->get_extra_header_string();
        }
        print_header($pageTitle, $pageHeading, $pageNavigation, $pageFocus, $pageMeta, true, $extraheader );
    }

    // This should point to the script that displays us
    function url_get_path() {
        global $CFG;

        return $CFG->wwwroot .'/blog/index.php';
    }

    function url_get_parameters() {
        
        $array = array();
        if (!$this->full_init_done) {
            $array['userid'] = $this->id;
            return $array;
        }

        if (!empty($this->course->id)) {
            $array['courseid'] = $this->course->id;
        }
        if (!empty($this->filtertype)) {
            $array['filtertype'] = $this->filtertype;
        }
        if (!empty($this->filterselect)) {
            $array['filterselect'] = $this->filterselect;
        }
        if (!empty($this->tagid)) {
            $array['tagid'] = $this->tagid;  
        }
        return $array;
    }

    /////////// Blog page specific functions
    function get_extra_header_string() {
        global $SESSION, $CFG, $USER;

        $editformstring = '';
        if ($this->user_allowed_editing()) {
            if (!empty($SESSION->blog_editing_enabled)) {
                $editingString = get_string('turneditingoff');
            } else {
                $editingString = get_string('turneditingon');
            }

            $params = $this->url_get_parameters();
            $params['edit'] = empty($SESSION->blog_editing_enabled) ? 1 : 0;
            $paramstring = '';
            foreach ($params as $key=>$val) {
                $paramstring .= '<input type="hidden" name="'.$key.'" value="'.s($val).'" />';
            }

            $editformstring = '<form '.$CFG->frametarget.' method="get" action="'.$this->url_get_path().'"><div>'
                             .$paramstring.'<input type="submit" value="'.$editingString.'" /></div></form>';
        }

        return $editformstring;
    }
}
?>
