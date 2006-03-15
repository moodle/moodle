<?php //$Id$

/**
 * Library of functions and constants for blog
 */
 
require_once($CFG->dirroot .'/blog/class.BlogInfo.php');
require_once($CFG->dirroot .'/blog/class.BlogEntry.php');
require_once($CFG->dirroot .'/blog/class.BlogFilter.php');
require_once($CFG->libdir .'/blocklib.php');
require_once($CFG->libdir .'/pagelib.php');
require_once($CFG->dirroot .'/blog/blogpage.php');

/* blog access level constant declaration */
define ('BLOG_USER_LEVEL', 1);
define ('BLOG_GROUP_LEVEL', 2);
define ('BLOG_COURSE_LEVEL', 3);
define ('BLOG_SITE_LEVEL', 4);
define ('BLOG_GLOBAL_LEVEL', 5);

/**
 * Definition of blogcourse page type (blog page with course id present).
 */
//not used at the moment, and may not need to be
define('PAGE_BLOG_COURSE_VIEW', 'blog_course-view');

$BLOG_YES_NO_MODES = array ( '0'  => get_string('no'),
                             '1' => get_string('yes') );

//set default setting for $CFG->blog_* vars used by blog's blocks
//if they are not already. Otherwise errors are thrown
//when an attempt is made to use an empty var.
if (empty($SESSION->blog_editing_enabled)) {
    $SESSION->blog_editing_enabled = false;
}
if (!(isset($CFG->blog_enable_trackback_in) )) {
    $CFG->blog_enable_trackback_in = 0; //default is 0 == do not allow for site
}
if (!(isset($CFG->blog_enable_moderation) )) {
    $CFG->blog_enable_moderation = 0; //default is 0 == do not enable blog moderation on this site
}
if (!(isset($CFG->blog_enable_pingback_in) )) {
    $CFG->blog_enable_pingback_in = 0; //default is 0 == do not allow for site
}
if (!(isset($CFG->blog_enable_trackback_out) )) {
    $CFG->blog_enable_trackback_out = 0; //default is 0 == do not allow for site
}
if (!(isset($CFG->blog_enable_pingback_out) )) {
    $CFG->blog_enable_pingback_out = 0; //default is 0 == do not allow for site
}
if (!(isset($CFG->blog_enable_moderation) )) {
    $CFG->blog_enable_moderation = 0; //default is 0 == do not turn on moderation for site
}
if (!(isset($CFG->blog_useweblog_rpc) )) {
    $CFG->blog_useweblog_rpc = 0;//default is 0 == do not publish to weblogs.com
}
if (empty($CFG->blog_ratename) ) {
    $CFG->blog_ratename = 'Rating'; //default name for entry ratings
}
if (empty($CFG->blog_default_title) ) {
    $CFG->blog_default_title = 'Moodle Blog'; //default blog title
}
if (empty($CFG->blog_blogurl) ) {
    $CFG->blog_blogurl = $CFG->wwwroot.'/blog/index.php';
}
if (!(isset($CFG->blog_enable_trackback) )) {
    $CFG->blog_enable_trackback = 0;
}
if (!(isset($CFG->blog_enable_pingback) )) {
    $CFG->blog_enable_pingback = 0;
}
if (empty($CFG->blog_default_fetch_num_entries) ) {
    $CFG->blog_default_fetch_num_entries = 8;
}

/**
 * blog_user_bloginfo
 *
 * returns a blogInfo object if the user has a blog in the acl table
 * This function stores the currently logged in user's bloginfo object
 * statically - do not release/unset the returned object.
 * added by Daryl Hawes for moodle integration
 * $userid - if no userid specified it will attempt to use the logged in user's id
 */
function blog_user_bloginfo($userid='') {
//Daryl Hawes note: not sure that this attempt at optimization is correct
//    static $bloginfosingleton; //store the logged in user's bloginfo in a static var

    if ($userid == '') {
        global $USER;
        if (!isset($USER) || !isset($USER->id)) {
            return;
        }
        $userid = $USER->id;
    }
    
/*    if (isset($USER) && $USER->id == $uid && !empty($bloginfosingleton)) {
        return $bloginfosingleton;
    }*/

    $thisbloginfo = new BlogInfo($userid);
/*        if (isset($USER) && $USER->id == $userid) {
            $bloginfosingleton = $thisbloginfo;
        }*/
    return $thisbloginfo;
}

/**
 * Verify that a user is logged in based on the session
 * @return bool True if user has a valid login session
 */
function blog_isLoggedIn() {
    global $USER;
    if (!isguest() && isset($USER) and isset($USER->id) and $USER->id) {
        return 1;
    }
    return 0;
}

/**
 * This function upgrades the blog's tables as needed.
 * It's called from moodle/admin/index.php
 */
function blog_upgrade_blog_db($continueto) {

    global $CFG, $db;

    require_once($CFG->dirroot.'/blog/version.php');  // Get code versions

    if (empty($CFG->blog_version)) { // Blog has never been installed.
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades,
                     '', '', false, '&nbsp;', '&nbsp;');

        $db->debug=true;
        if (modify_database($CFG->dirroot .'/blog/db/'. $CFG->dbtype .'.sql')) {
            $db->debug = false;
            if (set_config('blog_version', $blog_version)) {
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradeblog', 'blog', $blog_version));

                /// Added by Daryl Hawes - since blog has to be installed the first time before blog's blocks, and on first install creating default blocks when none exist is a bad idea, just reset blocks for blog index here
                require_once($CFG->libdir .'/pagelib.php');
                require_once($CFG->dirroot .'/blog/blogpage.php');                
                page_map_class(PAGE_BLOG_VIEW, 'page_blog');    
                // Now, create our page object.
                $page = page_create_object(PAGE_BLOG_VIEW, 0);
                // Add default blocks to the new page type
                blocks_repopulate_page($page);
                
                print_continue($continueto);
                exit;
            } else {
                error('Upgrade of blogging system failed! (Could not update version in config table)');
            }
        } else {
            error('blog tables could NOT be set up successfully!');
        }
    }


    if ($blog_version > $CFG->blog_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);

        require_once($CFG->dirroot. '/blog/db/'. $CFG->dbtype .'.php');

        $db->debug=true;
        if (blog_upgrade($CFG->blog_version)) {
            $db->debug=false;
            if (set_config('blog_version', $blog_version)) {
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradeblocks', '', $blog_version));
                print_continue($continueto);
                exit;
            } else {
                error('Upgrade of blogging system failed! (Could not update version in config table)');
            }
        } else {
            $db->debug=false;
            error('Upgrade failed!  See blog/version.php');
        }

    } else if ($blog_version < $CFG->blog_version) {
        notify('WARNING!!!  The blog code you are using is OLDER than the version that made these databases! ('. $version .' < '. $CFG->blog_version .')');
    }
}

/**
 * course_has_blog_entries
 *   Given a course id return true if there are blog entries from any user related to that course
 * $courseid - the id for the course
 * Daryl Hawes note: When forum entries start using post table this function will no longer behave as expected
 * Since non blog posts will be associated with this course. Perhaps moduleid or another field could be wrangled 
 * into identifying the type of post?
 */
function blog_course_has_blog_entries($courseid) {
    $entries = get_records('post', 'courseid', $courseid);
    if (! empty($entries) ) {
        return true;
    }
    return false;
}

/**
 * Output a hidden html form used by text entry pages that require preview.php's functionality
 */
function blog_print_preview_form($userid=0, $categoryelement='<input type="hidden" name="categoryid[]">', $return=false) {

    $returnstring = "\n".'<div id="prev" style="visibility:hidden;z-index:-1;position:absolute;display:none;">';
    $returnstring .= "\n".'<form name="prev" action="preview.php" method="post" target="preview">';
    $returnstring .= "\n".'<input type="hidden" name="etitle" />';
    $returnstring .= "\n".'<input type="hidden" name="body" />';
    $returnstring .= "\n".'<input type="hidden" name="comm" />';
    $returnstring .= "\n".'<input type="hidden" name="tem" />';
    $returnstring .= "\n".'<input type="hidden" name="userid" value="'. $userid .'" />';

//    $returnstring .= '<input type="hidden" name="categoryid[]" value="'. $categoryid .'" />';
    $returnstring .= "\n". $categoryelement;
    $returnstring .= "\n".'<input type="hidden" name="format" />';
    $returnstring .= "\n".'</form>';
    $returnstring .= "\n".'</div>'."\n";
    
    if ($return) {
        return $returnstring; //return the form as a string if requested
    }
    print $returnstring; //else just print the string and exit the function
}

/**
 * blog_user_has_rights - returns true if user is the blog's owner or a moodle admin.
 *
 * @param BlogInfo blogInfo - a BlogInfo object passed by reference. This object represents the blog being accessed.
 * @param int uid - numeric user id of the user whose rights are being tested against this blogInfo. If no uid is specified then the uid of the currently logged in user will be used.
 */
function blog_user_has_rights(&$bloginfo, $uid='') {
    global $USER;
    if (isset($bloginfo) && isset($bloginfo->userid)) {
        if ($uid == '') {
            if ( isset($USER) && isset($USER->id) ) {
                $uid = $USER->id;
            }
        }        
        if ($uid == '') {
            //if uid is still empty then the user is not logged in
            return false;
        }
        if (blog_is_blog_admin($uid) || isadmin()) {
            return true;
        } 
    }
    return false;
}
  
/**
 * Determines whether a user is an admin for a blog
 * @param int $blog_userid The id of the blog being checked
 */
function blog_is_blog_admin($blog_userid) {
    global $USER;

    //moodle admins are admins
    if (isadmin()) {
        return true;
    }
    if ( empty($USER) || !isset($USER->id) ) {
        return false;
    }
    if ( empty($blog_userid)) {
        return true;
    }

    // Return true if the user is an admin for this blog
    if ($blog_userid == $USER->id) {
        return true;
    } else {
        return false;
    }
}

/**
 * Adaptation of isediting in moodlelib.php for blog module
 * @return bool
 */
function blog_isediting() {
    global $SESSION;
    if (! isset($SESSION->blog_editing_enabled)) {
        $SESSION->blog_editing_enabled = false;
    }
    return ($SESSION->blog_editing_enabled);
}

/**
 * Turns a php string into a string ready for insert into an rss xml feed
 */
function blog_rss_content($str) {
    $str = superhtmlentities($str);
    $content = "<![CDATA[$str]]>\n";

    return $content;
}

/**
 * function found when searching on problem of smart quotes
 * posted at http://daynah.php-princess.net/index.php?p=94
 */
function superhtmlentities($text) {
  $entities = array(128 => 'euro', 130 => 'sbquo', 
  131 => 'fnof', 132 => 'bdquo', 133 => 'hellip', 
  134 => 'dagger', 135 => 'Dagger', 136 => 'circ', 
  137 => 'permil', 138 => 'Scaron', 139 => 'lsaquo', 
  140 => 'OElig', 145 => 'lsquo', 146 => 'rsquo', 
  147 => 'ldquo', 148 => 'rdquo', 149 => 'bull', 
  150 => 'ndash', 151 => 'mdash', 152 => 'tilde', 
  153 => 'trade', 154 => 'scaron', 155 => 'rsaquo', 
  156 => 'oelig', 159 => 'Yuml');

  $new_text = '';
  for($i = 0; $i < strlen($text); $i++) {
    $num = ord($text{$i});
    if (array_key_exists($num, $entities)) {
        $new_text .= '&\\'.$entities[$num].';';
    } else if ($num < 127 || $num > 159) {
        $new_text .= $text{$i};
    }
  }
  return htmlentities($new_text);
}

/**
 * blog_get_recent_entries_byrange
 * not in blogInfo because entries being searched 
 * can be found in any number of blogs rather than just one.
 *
 * Returns specified range of entries since a given time.
 *
 * In using this function be aware for your where clause that the tables being 
 * read are post (e) and blog_categories_entries (c) and the returned values
 * will be e.*
 *
 * @param int $limit .
 * @param int $start .
 * @param string $where .
 * @param string $orderby .
 * @param bool $includeCategories .
 *
 * @return BlogEntries 
 */
function blog_get_recent_entries_byrange($limit, $start, $where='', $orderby='lastmodified DESC', $includeCategories=false) {
    global $CFG;

//    echo 'Debug: where clause in blog_get_recent_entries_byrange: $where<br />'; //debug

    if ($includeCategories) {
        $records = get_records_select('post e, '. $CFG->prefix .'blog_categories_entries c', $where, $orderby, '*', $start, $limit);
    } else {
        $records = get_records_select('post', $where, $orderby, '*', $start, $limit);
    }
//    print_object($records); //debug

    if (empty($records)) {
        return array();
    } else {
        $blogEntries = array();
        foreach($records as $record) {
            $blogEntry = new BlogEntry($record);
            //ensure that the user has rights to view this entry
            if ($blogEntry->user_can_view() ) {
                $blogEntries[] = $blogEntry;
            }
        }
        return $blogEntries;
    }
}

/**
 * returns a unix time stamp from year month and day
 * used to get start and end dates for comparing against timestamps in databases
 */
function blog_get_month_time($y, $m, $d, $firstday=true) {

    if ( !empty($y) && !empty($m) && !empty($d)) {
        $time = mktime(0, 0, 0, $m, $d, $y);
    } else if ( !empty($y) && !empty($m) ) {
        $day = blog_mk_getLastDayofMonth($m, $y);
        if ($firstday) {
            $day = 1;
        }
        $time = mktime(0, 0, 0, $m, $day, $y);
    } else {
        $time = '';
    }
    return $time;
}

/**
 * attempting to create a method useful for a popup form on the index
 * page that will allow a user to filter entries based upon date
 *
 * This function will return a list, in order, formatted as "MM-YYYY" for each year and month
 * in which this blog has entries.
 * @param BlogFilter $blogFilter - a BlogFilter object with the current filter settings for this page
 */
function blog_get_year_month_of_viewable_entries(&$blogFilter) {
    global $_SERVER;
    
    $entries = $blogFilter->get_filtered_entries();
    $datearray = array();
    $datearray['m=&amp;y='] = 'All Dates';
    
    if ( !empty($entries) ) {
        foreach ($entries as $entry) {
            if ( $entry->user_can_view() ) {
//                print_object($entry); //debug
                //user is allowed to see this entry, so return its year/month information
                $date = $entry->entryLastModified;
                $truncDate = date("F Y", $date); // this will return January 2004 for the date.
                $curmonth = date("m", $date); // this will return January 2004 for the date.
                $curyear = date("Y", $date); // this will return January 2004 for the date.
                $index = 'm='. $curmonth .'&amp;y='. $curyear;
                $datearray[$index] = $truncDate;
            }
        }
    }
//    print_object($datearray); //debug
        
    if (is_numeric($blogFilter->startmonth) && is_numeric($blogFilter->startyear)) {
        $selected = 'm='. $blogFilter->startmonth .'&amp;y='. $blogFilter->startyear;
    } else {
        $selected = '';
    }
    $unused = array('startmonth', 'startyear');
    $getvars = $blogFilter->get_getvars($unused);
    //attach a random number to the popup function's form name
    //becuase there may be multiple instances of this form on each page
    $self = basename($_SERVER['PHP_SELF']); 
    $form = popup_form($self . $getvars .'&amp;', $datearray, 'blog_date_selection'. mt_rand(), $selected, '', '', '', true);
    return str_replace('<form', '<form style="display: inline;"', $form);
}

/**
 * pretty close to a direct copy of calendar/view.php calendar_course_filter_selector function
 */
function blog_course_filter_selector(&$blogFilter) {
    global $USER, $_SERVER;
    
    $getvars = $blogFilter->get_getvars('courseid');

    if ( !isset($USER) || !isset($USER->id) ) {
        return;
    }

    if (isguest($USER->id)) {
        return '';
    }
    
    if (isadmin($USER->id)) {
        $courses = get_courses('all', 'c.shortname');
        
    } else {
        $courses = get_my_courses($USER->id, 'shortname');
    }
    
    unset($courses[1]);
    
    $courseoptions[1] = get_string('fulllistofcourses');
    foreach ($courses as $course) {
        // Verify that there are actually blog entries for this course before showing it as a selection.
        if ($entries = count_records('post', 'courseid', $course->id)) {
            $courseoptions[$course->id] = $course->shortname;
        }
    }

    //if there were no courses added then simply return
    if (count($courseoptions) == 1) {
        return;
    }
    
    if (is_numeric($blogFilter->courseid)) {
        $selected = $blogFilter->courseid;
    } else {
        $selected = '';
    }
    //attach a random number to the popup function's form name
    //because there may be multiple instances of this form on each page
    $self = basename($_SERVER['PHP_SELF']); 
    $form = popup_form($self . $getvars .'&amp;courseid=',
                       $courseoptions, 'blog_course_selection'. mt_rand(), $selected, '', '', '', true);
    
    return str_replace('<form', '<form style="display: inline;"', $form);
}

/**
 * build and return list of all member blogs
 *
 * @param stdObject $memberrecords An object of record entries as output from the get_member_list() function in BlogFilter (->id, ->title are the required variables).
 * @param int $format - 0, 1 or 2; 0 = hyperlinked list of members, 1 = select element, 2 = select element wrapped in entire form
 * @param bool $return  indicates whether the function should return the text 
 *   as a string or echo it directly to the page being rendered
 * @param BlogFilter $blogFilter - a BlogFilter object with the details of the members listed in $memberrecords.
 * @param string $hyperlink This is the target link to be used - there is a sensible default for each format.
 */
function blog_member_list(&$blogFilter, &$memberrecords, $format=1, $return=true, $hyperlink='') {
    global $CFG, $USER;
    
//echo "userid = $blogFilter->userid"; //debug
//print_object($memberrecords); //debug
    $returnstring = '';
    if (!empty($memberrecords)) {
        switch($format) {
            case '0':
                foreach($memberrecords as $record) {
                    if (empty($hyperlink)) {
                        $CFG->wwwroot .'/blog/index.php?userid='. $record->id;	
                    }
                    $returnstring .= '<a href="'. $hyperlink . $record->id .'">'. stripslashes_safe($record->title) .'</a><br />';	
                }
                break;
            case '2':
                $selected = '';
                $options = array('' => 'All Member Blogs');
                $formlink = $hyperlink; //TESTING
                if (empty($hyperlink)) {
                    $getvars = $blogFilter->get_getvars('userid');
                    $self = basename($_SERVER['PHP_SELF']); 
                    $formlink = $self . $getvars .'&amp;userid=';
                }                    
                foreach($memberrecords as $record) {
                    $id = $record->id;
                    if (blog_isLoggedIn() && $id == $USER->id ) {
                        $optiontitle = 'My Blog';
                    } else {
                        $optiontitle = stripslashes_safe($record->title);
                    }
                    $options[$id] = $optiontitle; //TESTING
                    if ( ($blogFilter->userid == $record->id) && ($blogFilter->userid != 0) ) {
                        $selected = $id;
                    }
                }
                
                //attach a random number to the popup function's form name
                //becuase there may be multiple instances of this form on each page
                $returnstring = popup_form($formlink,
                       $options, 'blog_member_list'. mt_rand(), $selected, '', '', '', true);
                $returnstring = str_replace('<form', '<form style="display: inline;"', $returnstring);
                break;
            
            case '1':
            default:
                $returnstring = '<select name="userid">';
                foreach($memberrecords as $record) {            
                    $returnstring .= '<option value="'. $record->id .'"';
                    if ( ($record->id == $blogFilter->userid) && ($blogFilter->userid != 0) ) {
                        $returnstring .= ' selected';
                    }
                    $returnstring .= '>'. stripslashes_safe($record->title) .'</option>';
                }
                $returnstring .= '</select>';
                break;
        }
    
    }
    if ($return) {
        return $returnstring;
    }
    print $returnstring;
    return;
}

/**
 * @param int courseid the selected course in popup
 */
function blog_get_course_selection_popup($courseid='') {
    global $USER;
    if ( !isset($USER) || !isset($USER->id) ) {
        return;
    }
    if ( isadmin() ) {
        $courses = get_courses(); //show admin users all courses
    } else {
        $courses = get_my_courses($USER->id) ; //get_my_courses is in datalib.php
    }
    //print_object($courses); //debug
    $courseoptions = array();
    foreach ($courses as $course) {
        $courseoptions[$course->id] = $course->shortname;
    }
    return choose_from_menu($courseoptions, 'course_selection', $courseid, '', '', '0', true);
}

/**
 *  This function is in lib and not in BlogInfo because entries being searched
 *   might be found in any number of blogs rather than just one.
 *
 *   $@param BlogFilter blogFilter - a BlogFilter object containing the settings for finding appropriate entries for display
 */
function blog_print_html_formatted_entries(&$blogFilter, $filtertype, $filterselect) {
    global $CFG, $USER;
    $blogpage = optional_param('blogpage', 0, PARAM_INT);
    $bloglimit = get_user_preferences('blogpagesize',8); // expose as user pref when MyMoodle comes around

    // First let's see if the batchpublish form has submitted data
    $post = data_submitted();
    if (!empty($post->batchpublish)) { //make sure we're processing the edit form here
//        print_object($post); //debug
        foreach ($post as $key => $publishto) {
            if ($key != 'batchpublish') {
                $useridandentryid = explode('-', $key);
                $userid = $useridandentryid[0];
                $entryid = $useridandentryid[1];
                $bloginfo = new BlogInfo($userid);
                $blogentry = $bloginfo->get_blog_entry_by_id($entryid);
                if ($blogentry->entryPublishState != $publishto) {
                    if (!$blogentry->set_publishstate($publishto)) {
                        echo 'Entry "'. $blogentry->entryTitle .'" could not be published.';
                    } else {
                        if ($error = $blogentry->save()) {
                            echo 'New publish setting for entry "'. $blogentry->entryTitle .'" could not be saved. ERROR:'. $error.':<br />';
                        }
                    }
                }
            }
        }
    }


    $morelink = '<br />&nbsp;&nbsp;';
    // show personal or general heading block as applicable
    echo '<div class="headingblock header blog">';
    //show blog title - blog tagline
    print "<br />";    //don't print title. blog_get_title_text();

    if ($blogpage != 0) {
        // modify the blog filter to fetch the entries we care about right now
        $oldstart = $blogFilter->fetchstart;
        $blogFilter->fetchstart = $blogpage * $bloglimit;
        unset($blogFilter->filtered_entries);
    }
    $blogEntries = $blogFilter->get_filtered_entries();
    // show page next/previous links if applicable
    print_paging_bar($blogFilter->get_viewable_entry_count(), $blogpage, $bloglimit, $blogFilter->baseurl, 'blogpage');
    print '</div>';
    if (isset($blogEntries) ) {

        if (blog_isLoggedIn() && blog_isediting() ) {
            print '<form name="batchpublishform" method="post" action="'. $blogFilter->baseurl .'" id="batchpublishform" enctype="multipart/form-data">';
        }

        $count = 0;
        foreach ($blogEntries as $blogEntry) {
            blog_print_entry($blogEntry, 'list', $filtertype, $filterselect); //print this entry.
            $count++;
        }
        if (!$count) {
            print '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';

            if (blog_isLoggedIn()) {
                $morelink = '<br />&nbsp;&nbsp;';
                $morelink .= $blogFilter->get_complete_link('<a href="'. $CFG->wwwroot .'/blog/edit.php', get_string('addnewentry', 'blog'))."\n";
                
            }
        }
/*
        if (blog_isLoggedIn() && blog_isediting() ) {
            //Daryl Hawes note: localize this submit button!
            print '<div align="center"><input type="submit" value="Save these publish settings" id="batchpublish" name="batchpublish" /></div>'."\n";
            print '</form>'."\n";
        }
*/
        //yu: testing code
        if (blog_isLoggedIn()) {
        //the user's blog is enabled and they are viewing their own blog
            $morelink .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/edit.php', get_string('addnewentry', 'blog'));
        }

        print $morelink.'<br />'."\n";

        if ($blogpage != 0) {
            //put the blogFilter back the way we found it
            $blogFilter->fetchstart = $oldstart;
            unset($blogFilter->filtered_entries);
            $blogFilter->fetch_entries();
        }

        return;
    }

    $output = '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';
    $userbloginfo = blog_user_bloginfo();
    
    if (blog_isLoggedIn()) {
        //the user's blog is enabled and they are viewing their own blog
        $output .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/edit.php', get_string('addnewentry', 'blog'));
    }
    print $output;
    unset($blogFilter->filtered_entries);
}

/**
 * What text should be displayed claiming ownership to the current blog entries?
 * @uses $PAGE
 * @return string
 */
function blog_get_title_text() {
    global $PAGE; //hackish

    if (isset($PAGE) && isset($PAGE->bloginfo)) {
        $blogInfo = &$PAGE->bloginfo;
        $title = $blogInfo->get_blog_title();
        if($title != '') {
            $displaytitle = $title;
            $tagline = $blogInfo->get_blog_tagline();
            if ($tagline != '') {
                $displaytitle .= ' - '. $tagline;
            }
        }
    }
    if (isset($displaytitle)) {
        return $displaytitle;
    } else {
        // Daryl Hawes - better wording would be good here, localize this line once the wording is selected.
        return 'Combined Blog '. get_string('entries', 'blog') .'<br />';
    }
}

/**
 * blog_get_moodle_pix_path
 *
 * Returns the directory path to the current theme's pix folder.
 * @return string
 */
function blog_get_moodle_pix_path(){
    global $CFG, $THEME;
    if (empty($THEME->custompix)) {
        return $CFG->wwwroot.'/pix';
    } else {
        return $CFG->themedir.current_theme().'/pix';
    }
}

/**
 *  This function is in lib and not in BlogInfo because entries being searched
 *   might be found in any number of blogs rather than just one.
 *
 * This function builds an array which can be used by the included
 * template file, making predefined and nicely formatted variables available
 * to the template. Template creators will not need to become intimate
 * with the internal objects and vars of moodle blog nor will they need to worry
 * about properly formatting their data
 *
 *   @param BlogEntry blogEntry - a hopefully fully populated BlogEntry object
 *   @param string viewtype Default is 'full'. If 'full' then display this blog entry
 *     in its complete form (eg. archive page). If anything other than 'full'
 *     display the entry in its abbreviated format (eg. index page)
 */
function blog_print_entry(&$blogEntry, $viewtype='full', $filtertype='', $filterselect='') {
    global $CFG, $THEME, $USER;
    static $bloginfoarray;

    if (isset($bloginfoarray) && $bloginfocache[$blogEntry->entryuserid]) {
        $bloginfo = $bloginfocache[$blogEntry->entryuserid];
    } else {
        $bloginfocache[$blogEntry->entryuserid] = new BlogInfo($blogEntry->entryuserid);
        $bloginfo = $bloginfocache[$blogEntry->entryuserid];
    }
    
    $template['body'] = $blogEntry->get_formatted_entry_body();
    $template['countofextendedbody'] = 0;
    
    $template['title'] = '<a name="'. $blogEntry->entryId .'"></a>';
    //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
    $template['title'] .= '<span class="nolink">'. stripslashes_safe($blogEntry->entryTitle);
    $template['title'] .= '</span>';

    // add editing controls if allowed
    $template['courseid'] = $blogEntry->entryCourseId;
    $template['userid'] = $blogEntry->entryuserid;
    $template['authorviewurl'] = $CFG->wwwroot .'/user/view.php?course=1&amp;id='. $template['userid'];
    $template['moodlepix'] = blog_get_moodle_pix_path();
    $template['author'] = $blogEntry->entryAuthorName;
    $template['lastmod'] = $blogEntry->formattedEntryLastModified;
    $template['created'] = $blogEntry->formattedEntryCreated;
    $template['publishtomenu'] = $blogEntry->get_publish_to_menu(true, true);
    $template['groupid'] = $blogEntry->entryGroupId;
    //forum style printing of blogs
    blog_print_entry_content ($template, $blogEntry->entryId, $filtertype, $filterselect);

}

//forum style printing of blogs
function blog_print_entry_content ($template, $entryid, $filtertype='', $filterselect='') {
    global $USER, $CFG, $course, $ME;

    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    $user = get_record('user','id',$template['userid']);

    echo '<div align="center"><table cellspacing="0" class="forumpost" width="100%">';

    echo '<tr class="header"><td class="picture left">';
    print_user_picture($template['userid'], $template['courseid'], $user->picture);
    echo '</td>';

    echo '<td class="topic starter"><div class="subject">'.$template['title'].'</div><div class="author">';
    $fullname = fullname($user, isteacher($template['userid']));
    $by->name =  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                $user->id.'&amp;course='.$course->id.'">'.$fullname.'</a>';
    $by->date = $template['lastmod'];
    print_string('bynameondate', 'forum', $by);
    echo '</div></td></tr>';

    echo '<tr><td class="left side">';
    if ($group = get_record('groups','id',$template['groupid'])) {
        print_group_picture($group, $course->id, false, false, true);
    } else {
        echo '&nbsp;';
    }

/// Actual content

    echo '</td><td class="content">'."\n";

    // Print whole message
    echo format_text($template['body']);

/// Links to tags

    if ($blogtags = get_records_sql('SELECT t.* FROM '.$CFG->prefix.'tags t, '.$CFG->prefix.'blog_tag_instance ti
                                 WHERE t.id = ti.tagid
                                 AND ti.entryid = '.$entryid)) {
        echo '<p />';
        print_string('tags');
        echo ': ';
        foreach ($blogtags as $blogtag) {
            echo '<a href="index.php?courseid='.$course->id.'&amp;filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;tagid='.$blogtag->id.'">'.$blogtag->text.'</a>, ';
        }
    }
    
/// Commands

    echo '<div class="commands">';

    if (isset($USER->id)) {
        if (($template['userid'] == $USER->id) or isteacher($course->id)) {
                echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?editid='.$entryid.'&amp;sesskey='.sesskey().'">'.$stredit.'</a>';
        }

        if (($template['userid'] == $USER->id) or isteacher($course->id)) {
            echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?act=del&amp;postid='.$entryid.'&amp;sesskey='.sesskey().'">'.$strdelete.'</a>';
        }
    }

    echo '</div>';

    echo '</td></tr></table></div>'."\n\n";
}

/**
 * Use this function to retrieve a list of publish states available for 
 * the currently logged in user.
 *
 * @return array This function returns an array ideal for sending to moodles'
 *                choose_from_menu function.
 */
function blog_applicable_publish_states($courseid='') {
    global $CFG;
    
    // everyone gets draft access
    $options = array ( 'draft' => get_string('publishtonoone', 'blog') );
    if (is_numeric($courseid) && $courseid != SITEID && $course = get_record('course', 'id', $courseid, '', '', '', '', 'shortname') ) {
        require_login($courseid);
        // if we're viewing a course allow publishing to course teachers
        $options['teacher'] = get_string('publishtoteachers', 'blog', $course->shortname);
        if (!$CFG->blog_enable_moderation || isadmin() || isteacher($courseid) ) {
            // only admins and teachers can publish to course members when moderation is enabled
            $options['course'] = get_string('publishtocourse', 'blog', $course->shortname);
        }
    }
    /*
     //groups not supported quite yet - pseudocode:
     if (isset($post->groupid) && $post->groupid != '') {
         $options['group'] = 'Fellow group members and teachers can view';
     }*/
    if (!$CFG->blog_enable_moderation || isadmin() || (is_numeric($courseid) && isteacher($courseid)) ) {
        // only admins and teachers can see site and public options when moderation is enabled
        $options['site'] = get_string('publishtosite', 'blog');
        $options['public'] = get_string('publishtoworld', 'blog');
    }
    return $options;
}

/**
 * blog_get_entries_by_category - not in blogInfo because entries being searched 
 *   can be found in any number of blogs rather than just one.
 * catids can be an array of category ids or a single category id
 * defined as either
 * $catids = array(1, 2, 3, 4, 5);
 * or
 * $catids = 2;
 *
 * Used by rss.php
 */
function blog_get_entries_by_category($catids, $courseid=0, $limit=8, $start=0) {
    $catsearch = ' e.id=c.entryid AND '; //only gets categories whose postid matches entries retrieved
    if (blog_array_count($catids) > 0) {
        $count = 0;
        foreach ($catids as $catid) {
            $catsearch .= 'c.categoryid='. $catid .' ';
            $count += 1;
            if (count($catids) != $count) {
                $catsearch .= 'OR ';
            }
        } 
    } else {
        $catsearch .= 'c.categoryid='. $catids;
    }
    $wherecourse = '';
    if (is_numeric($courseid) && $courseid != 0 && $courseid != 1) {
        $wherecourse = ' AND e.courseid='. $courseid;
    }
    $where = $catsearch.$wherecourse;
//    echo 'Debug: where clause for blog_get_entries_by_category: '. $where; //debug
    return blog_get_recent_entries_byrange($limit, $start, $where, '', true);
}

/**
 * builds calendar with links to filter entries by date
 * modified by Daryl Hawes to build links even when no userid is specified
 * and to return the html as a string if desired
 */
function blog_draw_calendar(&$blogFilter, $return=false)
{
    global $CFG;
//    print_object($blogFilter);

    if (!empty($blogFilter->startmonth)) {
        $m = $blogFilter->startmonth;
    } else {
        $m = date('n', mktime());
        $blogFilter->startmonth = $m;
    }
    if (!empty($blogFilter->startyear)) {
        $y = $blogFilter->startyear;
    } else {
        $y = date('Y', mktime());
        $blogFilter->startyear = $y;
    }
    $userid = $blogFilter->userid;
    
    //create a string to represent a URL argument with userid info. If no userid then string is empty.
    $useridString = '&amp;userid='. $userid;
    if ($userid == 0 || $userid == '') {
        $useridString = '';
    }

   // calculate the weekday the first of the month is on
   $tmpd = getdate(mktime(0, 0, 0, $m, 1, $y));
   $monthname = $tmpd['month'];
   $firstwday= $tmpd['wday'];
   $today = date('Ymd', mktime());

   $lastday = blog_mk_getLastDayofMonth($m, $y);

   //determine next and previous month
   if (($m - 1) < 1) { $pm = 12; } else { $pm = $m - 1; }
   if (($m + 1) > 12) { $nm = 1; } else { $nm = $m + 1; }

   if (strlen($pm) == 1) { $pm = '0'. $pm; };
   if (strlen($nm) == 1) { $nm = '0'. $nm; };

   $returnstring = "\n".'<table class="generaltable"><tr>'."\n";
   $returnstring .= '<td style="text-align: left; width: 12%;">'."\n";

   $currentyear = $y;
   $currentmonth = $m;

   if (($m - 1) < 1) {
       $blogFilter->startyear = $y - 1;
   } else {
       $blogFilter->startyear = $y;
   }

   $blogFilter->startmonth = $pm;
   $self = basename($_SERVER['PHP_SELF']); 
   $returnstring .= $blogFilter->get_complete_link( $self, '&lt;&lt;' , array('startday'))."\n";

   $blogFilter->startyear = $currentyear;
   $blogFilter->startmonth = $currentmonth;

   $returnstring .= '</td><td style="text-align: center;">'."\n";
//   $returnstring .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/archive.php', $monthname .' '. $y, array('startday'));
   $returnstring .= $blogFilter->get_complete_link($self, $monthname .' '. $y, array('startday'));

   if (($m + 1) > 12) {
       $blogFilter->startyear = $blogFilter->startyear + 1;
   } else {
        $blogFilter->startyear = $y;
   }

   $blogFilter->startmonth = $nm;
   $returnstring .= '</td><td style="text-align: right; width: 12%;">'."\n";
   $returnstring .= $blogFilter->get_complete_link( $self, '&gt;&gt;', array('startday'))."\n";
   $returnstring .= '</td></tr>'."\n";

   $blogFilter->startyear = $currentyear;
   $blogFilter->startmonth = $currentmonth;

   $returnstring .= '<tr><td colspan="3">'."\n";
   $returnstring .= '<table class="calendarmini"><thead>'."\n";
   $returnstring .= '<tr><td width="19" align="center" class="calday">'. get_string('calsun', 'blog') .'</td>'."\n";
   $returnstring .= '<td width="19" align="center" class="calday">'. get_string('calmon', 'blog') .'</td>'."\n";
   $returnstring .= '<td width="19" align="center" class="calday">'. get_string('caltue', 'blog') .'</td>'."\n";
   $returnstring .= '<td width="19" align="center" class="calday">'. get_string('calwed', 'blog') .'</td>'."\n";
    $returnstring .= '<td width="19" align="center" class="calday">'. get_string('calthu', 'blog') .'</td>'."\n";
    $returnstring .= '<td width="19" align="center" class="calday">'. get_string('calfri', 'blog') .'</td>'."\n";
    $returnstring .= '<td width="19" align="center" class="calday">'. get_string('calsat', 'blog') .'</td></tr></thead><tbody>'."\n";

    $d = 1;
    $wday = $firstwday;
    $firstweek = true;

    // loop through all the days of the month
    while ( $d <= $lastday)
    {
        // set up blank days for first week
        if ($firstweek) {
        $returnstring .= '<tr>'."\n";
        for ($i=1; $i <= $firstwday; $i++) {
            $returnstring .= '<td>&nbsp;</td>'."\n";
        }
        $firstweek = false;
        }

       // Sunday start week with <tr>
       if ($wday==0) {
           $returnstring .= '<tr>'."\n";
        }

       $mo = $m;
       if ($mo < 10) {
           if (!preg_match("/0\d/", $mo)) {
                $mo = '0'. $mo;
           }
       }

        // Look for blog entries for this day
       $tstart = blog_get_month_time($y, $m, $d, true);
       $tend = blog_get_month_time($y, $m, $d + 1, false);
       $where = " lastmodified >= $tstart AND lastmodified <= $tend ";

        if ($userid != 0 && $userid != '') {
            $where .= ' AND author = '. $userid .' ';
        }

      $count = count_records_select('post', $where);

//echo 'Where clause: '. $where .' | count:'. $count. '<br />'."\n"; //debug
        $da = $d;
        if($da < 10) {
            if(!preg_match("/0\d/", $da)) {
                $da = "0". $da;
            }
        }        
      // check for event
      $showdate = $y . $mo . $da;

      $returnstring .= '<td align=center';
      if ($showdate == $today) {
           $returnstring .= ' class="cal_today"';
      }
      if ($wday == 6 || $wday == 0) { 
          $returnstring .= ' class="cal_weekend"';
      }
      $returnstring .= '>'."\n";

      // if entries are found, output link to that day's entries
      if ($count > 0) {
          $blogFilter->startday = $d;
          $returnstring .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/index.php', $d);
      } else {
           $returnstring .= $d."\n";
      }
      $returnstring .= '</td>'."\n";

      // Saturday end week with </tr>
      if ($wday == 6) { 
          $returnstring .= '</tr>'."\n"; 
      }
 
      $wday++;
      $wday = $wday % 7;
      $d++;
    }

    if ($wday != 0) {
        for($i = $wday; $i < 7; $i++) {
            $returnstring .= '<td>&nbsp;</td>'."\n";
        }
        $returnstring .= '</tr>'."\n";
    }
    
    $returnstring .= '</table>'."\n";

    $returnstring .= '</td></tr></tbody></table>'."\n";

    if ($return) {
        return $returnstring;
    }
    print $returnstring;
// end blog_draw_calendar function
}

/**
 * get the last day of the month
 */
function blog_mk_getLastDayofMonth($mon, $year)
{
    for ($tday=28; $tday <= 31; $tday++)
    {
        $tdate = getdate(mktime(0, 0, 0, $mon, $tday, $year));
        if ($tdate['mon'] != $mon)
            { break; }

    }
    $tday--;

    return $tday;
}

/**
 * blog_safeHTML
 *   Clean up user input (currently unused, moodle's format_text() is preferable)
 */
function blog_safeHTML($html, $tags = 'b|br|i|u|ul|ol|li|p|a|blockquote|em|strong') {
// removes all tags that are considered unsafe
// Adapted from a function posted in the comments about the strip_tags()
// function on the php.net web site.
//
// This function is not perfect! It can be bypassed!
// Remove any nulls from the input
    $html = preg_replace('/\0/', '', $html);
 
    // convert the ampersands to null characters (to save for later)
    $html = preg_replace('/&/', '\0', $html);
  
    // convert the sharp brackets to their html code and escape special characters such as "
    $html=htmlspecialchars($html);
   
    // restore the tags that are considered safe
    if ($tags) {
        // Fix start tags
        $html = preg_replace("/&lt;(($tags).*?)&gt;/i", '<$1>', $html);
        // Fix end tags
        $html = preg_replace("/&lt;\/($tags)&gt;/i", '</$1>', $html);
        // Fix quotes
        $html = preg_replace("/&quot;/", '"', $html);
        $html = addslashes($html);
        // Don't allow, e.g. <a href="javascript:evil_code">
        $html = preg_replace("/<($tags)([^>]*)>/ie", "'<$1' . stripslashes_safe(str_replace('javascript', 'hackerscript', '$2')) .'>'", $html);
        // Don't allow, e.g. <img src="foo.gif" onmouseover="evil_javascript">
        $html = preg_replace("/<($tags)([^>]*)>/ie", "'<$1' . stripslashes_safe(str_replace(' on', ' off', '$2')) .'>'", $html);
        $html = stripslashes_safe($html);
                                                                            
    }
                                                                                                     
    // restore the ampersands
    $html = preg_replace('/\0/', '&', $html);
          
    return($html);
} // safeHTML

// I don't like how the PHP count() function returns 1 if
// you pass it a scalar. So this is my custom function that
// will return 0 if the argument isn't an array.
function blog_array_count($arr) {
    if (!is_array($arr)) {
        return 0;
    }
    
    return count($arr);
}

/**
* check_dir_exists
 *   Function to check if a directory exists
 *    and, optionally, create it
 *    copied from moodle/backup/lib.php
 */
if (! function_exists('check_dir_exists')) {
    function check_dir_exists($dir, $create=false) {
        
        global $CFG; 
        
        $status = true;
        if (!is_dir($dir)) {
            if (!$create) {
                $status = false;
            } else {
                umask(0000);
                $status = mkdir ($dir, $CFG->directorypermissions);
            }
        }
        return $status;
    }
}

/////////////// Time and Date display functions ///////////////

/*
 * Returns the current time as a readable date string
 * using moodle's chosen full date display format from admin configuration.
 */
function blog_now() {
    $strftimedaydatetime = get_string('strftimedaydatetime');
    $date = userdate(time(), $strftimedaydatetime);
    return $date;
}

/**
* UNIX timestamp to a readable format.
 * using moodle's chosen date format from admin configuration.
 */
function blog_format_date($datetime) {
    $strftimedate = get_string('strftimedate');
    $date = userdate($datetime, $strftimedate);
    return $date;
}

/**
* converts unix timestamp to just a date
 * using moodle's chosen short date format from admin configuration.
 */
function blog_short_date($datetime) {
    $strftimedateshort = get_string('strftimedateshort');
    $date = userdate($datetime, $strftimedateshort);
    return $date;     
}

/**
* converts unix timestamp to just a date
 * using moodle's chosen time format from admin configuration.
 */
function blog_short_time($datetime) {
    $strftimetime = get_string('strftimetime');
    $time = userdate($datetime, $strftimedateshort);
    return $time;
}

/////////////// Trackback functions ///////////////

// Note: trackback specification
// http://www.movabletype.org/docs/mttrackback.html


/**
 * generate rdf for trackback autodiscovery
 */
function blog_get_trackback_rdf_string($blogEntry) {
    
    global $CFG;
    $userid = $blogEntry->entryuserid;
    $entryid = $blogEntry->entryId;
    $blogInfo = new BlogInfo($userid);
    
//    echo 'in blog_get_trackback_rdf_string blogEntry:<br />'."\n"; //debug
//    print_object($blogEntry); //debug
    
    $rdf = "\n".'<!-- //RDF for trackback autodiscovery
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="'. htmlentities($CFG->wwwroot .'/blog/archive.php?userid='. $userid .'&amp;postid='. $entryid) .'"
    dc:identifier="'. htmlentities($CFG->wwwroot .'/blog/archive.php?userid='. $userid .'&amp;postid='. $entryid) .'"
    dc:title="'. $blogInfo->blogtitle .'" 
    trackback:ping="';
    if ($CFG->slasharguments) {
		$rdf .= $CFG->wwwroot .'/blog/tb.php/'. $entryid;
    } else {
        $rdf .= $CFG->wwwroot .'/blog/tb.php?file=/'. $entryid;
    }
    $rdf .='" />
</rdf:RDF>
-->'."\n";
    unset($blogInfo); //clean up after ourselves
    return $rdf;    
}


/**
 * Return a list of trackbacks for a particular id
 */
function blog_list_trackbacks($postid) {
    global $db, $CFG;

    //Daryl Hawes note: convert this sql statement to a moodle function call
    $sql = 'SELECT * FROM '. $CFG->prefix .'blog_trackback WHERE entryid = '. $postid;
    $res = $db->Execute($sql);

    //iterate results
    $list = array();
    while( !$res->EOF && isset($res->fields) ) {
        $list[] = $res->fields;
        $res->MoveNext();
    }

    return $list;
}


/////////////////////// CATEGORY MANAGEMENT ////////////////////////////////////



  
/**
 *called by blog_category_list
 * @return string  html links for each category
 */
function blog_get_html_display_for_categories(&$blogFilter, $shownumentries, &$records, $showseparators, $title, $section) {
    global $CFG, $editing;
    $returnstring ='';
    if ($showseparators) {
        //first show a separator if requested
        $returnstring .= $title .'<br />'."\n";
    }
    $editallowed = false;

    if ($editing) {
        $isteacher = false;
        if (isset($blogFilter->courseid)) {
            $isteacher = isteacher($blogFilter->courseid);
        }
        if ( isadmin() ) {
            // admin is allowed to edit any categories on the site
            $editallowed = true;
        } else if ( ($section == 'course' || $section == 'group') && $isteacher ) {
            // teacher of course can modify course categories and group categories
            $editallowed = true;
        } else if ($section == 'personal' && blog_is_blog_admin($blogFilter->userid) ) {
            // user can modify their own blog categories
            $editallowed = true;
        }
    }

    if (!isset($records) ) {
        return;
    }
    
    foreach($records as $record) {
        $catcount = '';
        $categoryid = $record->id;
        $categoryname = $record->catname;
        if ($shownumentries) {
            $tempfilter =& new BlogFilter('', $categoryid);
            $catcount = ' (';
            $catcount .= $tempfilter->get_filtered_entry_count();
            $catcount .= ')';
        }
        $blogFilter->categoryid = $categoryid;
        $returnstring .= $blogFilter->get_complete_link($CFG->wwwroot .'/blog/index.php', stripslashes_safe($categoryname) . $catcount);
        if ($editallowed) {
            // note that only the 'act' and 'categoryid' vars are needed here because the me() function includes the 
            // existing query string
            $returnstring .= '&nbsp;<a href="'. me() .'&amp;act=editcategory&amp;categoryid='. $categoryid .'">';
            $returnstring .= '<img src="'. $CFG->pixpath .'/t/edit.gif" alt="'. get_string('edit');
            $returnstring .= '" title="'. get_string('edit') .'" align="absmiddle" height="16" width="16" border="0" /></a>'."\n";
            if ($categoryid != 1) { //do not remove "General" sitewide category
                $returnstring .= '&nbsp;<a href="'. $CFG->wwwroot .'/blog/admin.php?act=delcategory&amp;categoryid='. $categoryid;
                $returnstring .= '&amp;userid='. $blogFilter->userid .'&amp;courseid='. $blogFilter->courseid .'&amp;groupid='. $blogFilter->groupid .'" onClick="return confirm(\''. get_string('confirmcategorydelete', 'blog') .'\');">';
                $returnstring .= '<img src="'. $CFG->pixpath .'/t/delete.gif" ALT="'. get_string('delete');
$returnstring .= '" title="'. get_string('delete') .'" align="absmiddle" border="0" /></a>'."\n";
            }
        }
        $returnstring .= '<br />'."\n";
    }
    return $returnstring;
}

/**
 *
 */
function blog_get_popup_display_for_categories(&$blogFilter, $format, $shownumentries, &$records) {
    global $CFG;

    if (!isset($records) ) {
        return;
    }
    $returnstring = '';
    
    foreach ($records as $record) {
        if ($format == 2) {
            $value = $CFG->wwwroot .'/blog/index.php?categoryid='. $record->id;
            $value .= '&amp;userid='. $blogFilter->userid .'&amp;courseid='. $blogFilter->courseid .'&amp;groupid='. $blogFilter->groupid;
        } else {
            $value = $record->id;
        }
        
        $returnstring .= '<option value="'. $value .'"';
        if ($record->id == $blogFilter->categoryid) {
            $returnstring .= ' selected';
        }
        $catcount = '';
        $categoryid = $record->id;
        $categoryname = $record->catname;
        if ($shownumentries) {
            $tempfilter =& new BlogFilter('', $categoryid);
            $tempfilter->userid = $blogFilter->userid;
            $catcount = ' (';
            //if we had an array of blogentry objects we could avoid a database call
            //and instead simply ask the blogentry objects to tell us which apply
            $catcount .= $tempfilter->get_filtered_entry_count();
            $catcount .= ')';
        }
        $returnstring .= '>' ."\n". stripslashes_safe($categoryname) . $catcount ."\n";
        $returnstring .= '</option>';
    }
    return $returnstring;
}
?>
