<?php //$Id$
    /**
     * class.BlogInfo.php
     * Author: Jason Buberel
     * Copyright (C) 2003, Jason Buberel
     * jason@buberel.org
     * http://www.buberel.org/
     *
     *******************************************************************
     * This program is free software; you can redistribute it and/or modify it
     * under the terms of the GNU General Public License as published by the
     * Free Software Foundation; either version 2 of the License, or (at your
     * option) any later version.
     * 
     * This program is distributed in the hope that it will be useful, but
     * WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
     * General Public License for more details.
     * 
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
     *******************************************************************
     *
     * This class is used to represent a single weblog. It gives the
     * developer access to all of the normal properties of the weblog.
     * Through the use of the required BlogEntry class, you should be able
     * to access all the data related to this particular weblog.
     *
     * To use BlogInfo, you create a new instance using the provided
     * constructor:
     *  include_once("class.BlogInfo.php");
     *  $userid = 2;
     *  $myBlog = new BlogInfo($userid);
     *
     * Once instantiated, the BlogInfo instance can be used to obtain
     * information about the blog:
     *
     *  $myTitle = $myBlog->get_blog_title();
     *
     * The three most useful methods are those used to retrieve BlogEntries:
     *
     *  $someBlogEntry = $myBlog->get_blog_entry_by_id(200); // fetch the 200th blog entry.
     *  $blogEntryList = $myBlog->get_last_N_entries(10); // fetch the 10 most recent
     *                                                 // blog entries.
     *  foreach ($blogEntryList as $blogEntry) {
     *      print "Blog Entry Title: ($blogEntry->entryId) $blogEntry->get_blog_title()<br/>";
     *  }
     */

global $CFG;
include_once($CFG->dirroot.'/blog/lib.php');
include_once($CFG->dirroot.'/blog/class.BlogEntry.php');

class BlogInfo {
    // member variables
    var $userid; // moodle userid
    var $blogtitle; // user preference blog_title
    var $blogtagline; // user preference blog_tagline
    var $blogtheme; // user preference blog_theme - id of the template being used for this blog.
    
    // lazy loading member variables
    // DO NOT directly refer to these member vars. Instead use their
    // getter functions to ensure they are loaded properly
    var $blogadminuser = NULL; // moodle user object for this userid
    var $blogadminname = NULL; // userid -> blog_users.name
    var $blogadminemail = NULL; // userid -> blog_users.email
    var $blogadminurl = NULL; // userid -> blog_users.url
    var $blogEntries = NULL; // an array of entries for this blog. empty by default.

    /**
     * constructor- used to create the BlogInfo instance
     * and populate it with information about the blog.
     */
    function BlogInfo($userid) {
        global $CFG;

        if ($userid == 0 || empty($userid)) {
            return NULL;
        }
        
        $this->blogEntries = array();
        $this->userid = $userid;
        $this->blogtitle = stripslashes_safe(get_user_preferences('blogtitle', $CFG->blog_default_title, $userid));
        $this->blogtagline = stripslashes_safe(get_user_preferences('blogtagline', '', $userid));
        $this->bloguseextendedbody = get_user_preferences('bloguseextendedbody', false, $userid);
        $this->blogtheme = get_user_preferences('blogtheme', 0, $userid); //Daryl Hawes note: investigate blogtheme usage again
/*        if ($this->userid == 0) { 
            $this->userid = 1;
        }*/
    }

////////// getters and setters ///////////////
    
    /**
     * Use this function to get a single numbered BlogEntry object
     * for this blog.
     * @todo perhaps a member array could be used to store these fetched BlogEntry objects
     *      in case the same entry is requested from this same bloginfo object later
     */
    function get_blog_entry_by_id($entryId) {
        global $CFG;
    
        foreach ($this->blogEntries as $cachedentry) {
            if ($cachedentry->entryId == $entryId) {
                return $cachedentry;
            }
        }
        $record = get_record('post', 'id', $entryId);    //teachers should be able to edit people's blogs, right?
        //$record = get_record('post', 'author', $this->userid, 'id', $entryId);
        // may have zero entries. in that case, return null.
        if (empty($record)) {
            // the result set is empty. return null.
            return NULL;
        } else {
            // create the new blog entry object...
            $blogEntry = new BlogEntry($record);
            //cache the blogEntry in member var for future use if needed
            $this->blogEntries[] = $blogEntry;
            $this->blogEntries = array_unique($this->blogEntries);
        }
        return $blogEntry;
    }

    /**
     * This function will remove the specified blog entry. It will
     * perform any of the security checks necessary to ensure that the
     * user is authorized to remove the entry. It will return false
     * if there was an error trying to delete the entry.
     * @param int $entryID The blog entry to delete by id
     */
    function delete_blog_entry_by_id($entryId) {
        // figure out who the currently logged in user is.
        global $USER;
        if ( !isset($USER) || empty($USER) || !isset($USER->id) ) {
            return false;
        }
        $uid = $USER->id;
        // retrieve the entry.
        $blogEntry = $this->get_blog_entry_by_id($entryId);
        
        if (empty($blogEntry) ) {
            return false;
        }

        if (($uid == $blogEntry->entryuserid) || (blog_is_blog_admin($this->userid)) || (isadmin())) {
            // yes, they are authorized, so remove the entry.
            if ( $blogEntry->delete() ) {
                unset($this->blogEntries[$blogEntry]);
                return true;
            }
        }
        // must not have worked...
        return false;
    }
    

    /**
     * Use this method to insert/create a new entry in the post table for
     * this blog. The entry id of the new blog entry will be returned if the
     * insertion is successful.
     * @param string $title .
     * @param string $body .
     * @param string $extendedbody .
     * @param int $userid .
     * @param int $formatId .
     * @param string $publishstate 'draft', 'teacher', 'course', 'group', 'site', 'public'
     * @param int $courseid .
     * @param int $groupid .
     * @return int
     */
    function insert_blog_entry($title, $body, $userid, $formatId, $publishstate='draft', $courseid='', $groupid='') {
        global $CFG;
        
        // first, make sure the title and body are safe for insert.
        $title = ereg_replace("'", '<tick>', $title);
        $body = ereg_replace("'", '<tick>', $body);
        // The wysiwyg html editor adds a <br /> tag to the extendedbody.
        // cleanup the extendedbody first
        
        $title = addslashes($title);
        $body = addslashes($body);

        // come up with a new timestamp to insert.
        // now insert the new entry.
        $dataobject->summary = $body;
        $dataobject->userid = $userid;
        $dataobject->subject = $title;
        $dataobject->format = $formatId;
        
        $timenow = time();
        $dataobject->lastmodified = $timenow;        
        $dataobject->created = $timenow;
        $dataobject->publishstate = $publishstate;

        $newentryid = insert_record('post', $dataobject);

        if ($newentryid) {
            // entry was created and $newentryid is its entryid.
            
            // create a unique hash for this id that will be its alternate identifier
            unset($dataobject);
            $dataobject->id = $newentryid;
            $dataobject->uniquehash = md5($userid.$CFG->wwwroot.$newentryid);
            update_record('post', $dataobject);
            
            // now create category entries
            if (!empty($categoryids)) {
                foreach ($categoryids as $categoryid) {
                    $cat->entryid = $newentryid;
                    $cat->categoryid = $categoryid;
                    insert_record('blog_categories_entries', $cat);
                }
            }
            // insert lastmodified into user pref so that recently modified blogs can be identified easily without joining tables
            set_user_preference('bloglastmodified', $timenow, $this->userid);
            return $newentryid;
        }
        return null;
    }

    /**
     * Discovers the number of entries for this blog
     * @return int Entry count
     */
    function get_entry_count() {
        return count_records('post', 'userid', $this->userid);
    }

    /**
        * returns the N most recent BlogEntry objects
     * for this blog
     */
    function get_last_N_entries($n) {
        return $this->get_blog_entries_by_range($n, 0);
    }
    
    /**
     *
     */
    function get_blog_entries_by_range($limit, $start) {
        global $USER;
    
        $sqlsnippet = 'userid='. $this->userid;
        $sort = 'id DESC';
        $records = get_records_select('post', $sqlsnippet, $sort, '*', $start, $limit);
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
//            print_object($blogEntries); //debug
            //cache the blogEntries in member var for future use if needed
            $this->blogEntries = array_merge($this->blogEntries, $blogEntries);
            $this->blogEntries = array_unique($this->blogEntries);

            return $blogEntries;
        }
    }

        /**
        * update_blog_entry_by_id
        *
        * this funciton will update the selected blog entry after performing
        * security checks to make sure the user is authorized to perform the update.
        * Used by api.php
        * @uses USER
        */
    function update_blog_entry_by_id($entryId, $title, $body, $formatId, $categoryId, $publishstate='draft', $courseid='', $groupid='') {
        // figure out who the currently logged in user is.
        global $USER;

        if ( !isset($USER) || empty($USER) || !isset($USER->id) ) {
            return false;
        } else {
            $uid = $USER->id;
        }
        $body = ereg_replace("'", '<tick>', $body);
        $extendedbody = ereg_replace("'", '<tick>', $extendedbody);
        $title = ereg_replace("'", '<tick>', $title);
        $title = addslashes($title);
        $body = addslashes($body);
        $extendedbody = addslashes($extendedbody);

        // retrieve the entry
        $blogEntry = $this->get_blog_entry_by_id($entryId);
        //check if the user is authorized to make this change
        if ( ($uid == $blogEntry->entryUserId) || (blog_is_blog_admin($this->userid)) || (isadmin()) ) {
            // Yes they are authorized so update the entry.
            if ( $blogEntry->update($title, $body, $extendedbody, $formatId, $categoryId, $publishstate, $courseid, $groupid) ) {
                return true;
            }
        }
        // must not have worked...
        return false;
        
    }
    
}
?>
