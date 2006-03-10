<?php // $Id$

/*******************************************************************
 * Class to represent a blog entry.
 *
 * @copyright Copyright (C) 2003, Jason Buberel
 * @author Jason Buberel jason@buberel.org {@link http://www.buberel.org/}
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blog
 ******************************************************************/

global $CFG;
include_once($CFG->dirroot .'/blog/lib.php');

/**
 *
 * This class represents a single entry in a blog. Normally, you wouldn't
 * need to call the constructor directly...you would instead make use of
 * a BlogInfo object's entry retrieval methods in order to get an instance
 * of BlogEntry.
 *
 * To create a new blogEntry object use BlogInfo's insert_blog_entry() function.
 *
 * @todo Ultimately this class might to be expanded to include a factory
 * method for the creation of new BlogEntries (->create()).
 * Better yet:
 fix constructor to not have to take a record set - should be empty and use setters
 verify that everything has getters/setters
 make sure that both the ->save and ->update function operate properly on an entry that does not yet exist!
 this way one could:
 $newEntry = new BlogEntry();
 $newEntry->set_title('a title');
 $newEntry->set_body('a post here');
 $newEntry->set_userid(2);
 $newEntry->save();
 */
class BlogEntry {
    // member variables
    var $entryId; // post.id
    var $entryBody; // post.summary
    var $entryExtendedBody; // post.content
    var $entryTitle; // post.subject
    var $entryKarma; // post.rating
    var $entryFormat; // post.format
    var $entryuserid; // post.author    
    var $entryGroupId; // post.groupid
    var $entryCourseId; // post.courseid
    var $entryPublishState; // post.publishstate
    var $entryAuthorName; // blog_users.name
    var $entryAuthorEmail; // blog_users.email
    
    //Daryl Hawes note: entryCategoryIds should be phased out as entryCategories is an
    //associative array of $id => $name elements
    var $entryCategoryIds = array(); // post.id -> blog_categories_entries.categoryid
    var $entryCategories = array(); // post.id -> blog_categories_entries.categoryid -> blog_categories.catname
    
    var $entryLastModified; // last modification date post.lastmodified
    var $formattedEntryLastModified; // post.lastmodified
    var $entryCreated; // creation date post.created
    var $formattedEntryCreated; // post.created

    /**
     * Class constructor that will build a new instance of the object
     * when given a reference to an object that contains
     * all of the keys from a row in the post table
     * Daryl Hawes note: constructor should be changed not to have to take in a database row!
     *
     * @param object $entrydetails reference to an object that contains
     *                              all of the keys from a row in the post table
     * @uses $CFG
     * @uses $db
     * @todo finish documenting this constructor
     */
    function BlogEntry(&$entrydetails) {
        global $db, $CFG;
//        print_object($entrydetails); //debug

        $this->entryId = $entrydetails->id;

        if (!empty($entrydetails->categoryid)) {
            if (is_array($entrydetails->categoryid)) {
                $this->entryCategoryIds = $entrydetails->categoryid;
            } else {
                $this->entryCategoryIds = array($entrydetails->categoryid);
            }
        } else {
            // load up all categories that this entry is associated with
            // cannot use moodle's get_records() here because this table does not conform well enough
            $sql = 'SELECT * FROM '. $CFG->prefix .'blog_categories_entries WHERE entryid='. $this->entryId;
            if($rs = $db->Execute($sql)) {
                while (!$rs->EOF) {
                    $this->entryCategoryIds[] = $rs->fields['categoryid'];
                    $rs->MoveNext();            
                }
            }
        }
        $this->entryCategoryIds = array_unique($this->entryCategoryIds);
//        print "Debug: entryId: $this->entryId"; //debug
//        print_object($this->entryCategoryIds); //debug
        
        $this->entryBody = ereg_replace('<tick>', "'", stripslashes_safe($entrydetails->summary));
        if (isset($entrydetails->extendedbody)) {
            $this->entryExtendedBody = ereg_replace('<tick>', "'", stripslashes_safe($entrydetails->extendedbody));
        } else {
            $this->entryExtendedBody = '';
        }
        
        $strftimedaydatetime = get_string('strftimedaydatetime');
        $this->entryLastModified = $entrydetails->lastmodified;
        $this->formattedEntryLastModified = userdate($this->entryLastModified, $strftimedaydatetime);
        $this->entryCreated = $entrydetails->created;
        $this->formattedEntryCreated = userdate($this->entryCreated, $strftimedaydatetime);
        
        $this->entryuserid = $entrydetails->userid;
        

        //added stripslashes_safe here for rss feeds. Will this conflict anywhere?
        
        
        $this->entryTitle = ereg_replace('<tick>', "'", stripslashes_safe($entrydetails->subject));   //subject, not title!
        
        $this->entryFormat = $entrydetails->format;

        //Daryl Hawes additions: course and group ids
        if (isset($entrydetails->groupid) ) {
            $this->entryGroupId = $entrydetails->groupid;
        }
        if (isset($entrydetails->courseid) ) {
            $this->entryCourseId = $entrydetails->courseid;
        }

        if (isset($entrydetails->publishstate) ) {
            $this->entryPublishState = $entrydetails->publishstate;
        } else {
            $this->entryPublishState = 'draft';
        }

        // need to get the email address of the author.
        if (! $rs = get_record('user', 'id', $this->entryuserid)) {
                error('Could not find user '. $this->entryuserid ."\n"); //Daryl Hawes note: needs localization
                die;
        }
        $this->entryAuthorName = $rs->firstname .' '. $rs->lastname;
        //need to make sure that email is actually just a link to our email sending page.
        $this->entryAuthorEmail = $rs->email;

        // then each category
        if (!empty($this->entryCategoryIds)) {
            foreach ($this->entryCategoryIds as $categoryid) {
                if (! $currcat = get_record('blog_categories', 'id', $categoryid)) {
                    print 'Could not find category id '. $categoryid ."\n";
                    $this->entryCategories[$categoryid] = '';
                } else {
                    $this->entryCategories[$categoryid] = $currcat->catname;
                }
            }
        }
    }

    /**
     * delete this entry
     *
     * @return bool Returns true on successful deletion
     */
    function delete() {
        if (! delete_records('post', 'userid', $this->entryuserid, 'id', $this->entryId)) {
                print 'Could not find blog entry matching author with user id '. $this->entryuserid .'  and entry with id '. $this->entryId ."\n";
                return false;
        }
        return true;
    }
    
    /**
     * get_formatted_karma_link
     *
     * @return string If allowed a link to set karma for this entry will be returned
     * @uses $USER
     * @uses $CFG
     */
    function get_formatted_karma_link() {
        global $USER, $CFG;
        $str = '';
        if (!empty($CFG->blog_ratename)) {
            $str .= $CFG->blog_ratename .': ';
        }
        $str .= $this->entryKarma;
        if ( !isguest() && blog_isLoggedIn()) {
            $str .= ' ( <a href="'. $CFG->wwwroot .'/blog/karma.php?op=add&amp;userid='. $this->entryuserid .'&amp;postid='. $this->entryId .'">+</a> / <a href="'. $CFG->wwwroot .'/blog/karma.php?op=sub&amp;userid='. $this->entryuserid .'&amp;postid='. $this->entryId .'">-</a> )';
        }
        return $str;
    }

    /**
     * get_formatted_category_link
     *
     * @return string unordered list of categories this entry is associated with
     * @uses $CFG
     */
    function get_formatted_category_link() {
        global $CFG;
        $returnstring = '<span class="post-category">';
        
        if (!empty($this->entryCategoryIds)) {
            $count = count($this->entryCategoryIds);
            foreach ($this->entryCategoryIds as $categoryid) {
                $returnstring .= '<a href="'. $CFG->wwwroot .'/blog/index.php?user='. $this->entryuserid .'&amp;categoryid='. $categoryid .'">'. $this->entryCategories[$categoryid] .'</a>';
                $count--;
                if ($count != 0) {
                    $returnstring .= ',&nbsp;';
                }
                $returnstring .= "\n";
            }
        }

        return $returnstring.'</span>' . "\n";
    }
    
    
    /**
     * get_formatted_course_link
     *
     * @return string Returns and unordered list of courses that this entry is associated with
     * @uses $CFG
     */
    function get_formatted_course_link() {
        global $CFG;
        $returnstring = '<span class="post-course">';
        $courseid = $this->entryCourseId;
        if ( !empty($courseid) && !($courseid == 0 || $courseid == '' || ! is_numeric($courseid) )) {
            if ($course = get_record('course', 'id', $courseid, '', '', '', '', 'fullname')) {
                $returnstring .= '<a href="'. $CFG->wwwroot .'/course/view.php?id='. $courseid .'">'. $course->fullname .'</a>' . "\n";
            }
        }
    
        return $returnstring.'</span>' . "\n";
    }

    /**
     * get_formatted_entry_link
     *
     * @return string Permalink URL wrapped in an HTML link
     */
    function get_formatted_entry_link() {
    
        // removed the word 'permalink' and replaced with 'Read More' to
        // further eliminate jargon from moodle blog
        // Daryl Hawes note: must localize this line now
        $str = '<a href="'. $this->get_entryurl() .'">Read More</a>';
        return $str;

    }

    /*
    * get_simple_entry_link - Just the link, with no extra html. 
    *
    * @return string Returns just a URL with no HTML.
    * (Daryl Hawes note: this function moved to class.Blogentry from lib.php)
    */
    function get_simple_entry_link() {
    
        $str = htmlspecialchars( $this->get_entryurl() );    
        return $str;
    
    }
    
    /**
     * get_blog_this_URL added by Daryl Hawes for moodle integration
     *
     * @param bool $showImage If true then the return string is an HTML image tag
     *                          If false then the return string is an HTML text link
     * @uses $CFG
     * @return string An HTML image tag or text link depending upon $showImage argument
     */
    function get_blog_this_URL($showImage=false) {
        $str = '';
        global $CFG;
        //ensure user is logged in and that they have a blog to edit
        if ( !isguest() && blog_isLoggedIn() ) {
            $blogThisString = '';
            if ($showImage) {
                $blogThisString = '<img src="'. $CFG->pixpath .'/blog/blog.gif" alt="'. get_string('blogthis', 'blog');
                $blogThisString .= '!" title="'. get_string('blogthis', 'blog') .'!" border="0" align="middle" />';
            } else {
                $blogThisString = get_string('blogthis', 'blog');
            }
            if (!$showImage) { 
                $str .= '('; 
            }
            $str .= '<a href="'. $this->get_entryblogthisurl() .'">'. $blogThisString .'</a>';
            if (!$showImage) { 
                $str .= ')'; 
            }
        }
        return $str;
    }

    /**
     * get_formatted_edit_URL added by Daryl Hawes for moodle integration
     * An empty string is returned if the user is a guest, the user is not logged in,
     * or the user is not currently editing their blog page (turn editing on button)
     * we will only show edit link if the entry is in draft status or the user is an admin
     * note: teacher should not be allowed to edit or delete - only demote back to draft
     *
     * @param bool $showImage If false a text link is printed. If true a linked edit icon is printed.
     * @uses $USER
     * @uses $CFG
     * @todo get_formatted_delete_URL and get_formatted_edit_URL should be merged into a single function
     */
    function get_formatted_edit_URL($showImage=false) {
        global $USER, $CFG;
        $str = '';

        if ( !isguest() && blog_isLoggedIn() && blog_isediting() && blog_is_blog_admin($this->entryuserid) 
             && (!$CFG->blog_enable_moderation || isadmin() || $blogEntry->entryPublishState == 'draft') ) {            
            $str = '<div class="blogedit">';

            //check if user is in blog's acl
            //instead of creating a new BlogInfo object might a BlogInfo pointer in BlogEntry constructor be better? Does php have singleton objects? if not then a bloginfo reference as an argument to the constructor of BlogEntry would be a good idea. (The only problem here is in pages with multiple bloginfo objects represented - aggregate pages.)
            $bloginfo = new BlogInfo($this->entryuserid);
            //if so then show them an edit link
            if (blog_user_has_rights($bloginfo)) {
                $editString = '';
                if ($showImage) {
                    $editString = '<img src="'. $CFG->pixpath .'/t/edit.gif" alt="'. get_string('edit');
                    $editString .= '" title="'. get_string('edit') .'" align="absmiddle" height="16" width="16" border="0" />';
                } else {
                    $editString = get_string('edit');
                }
                if (!$showImage) { 
                    $str .= '('; 
                }
                $str .= '<a title="'. get_string('edit') .'" href="'. $this->get_entryediturl() .'">'. $editString .'</a>';
                if (!$showImage) { 
                    $str .= ')'; 
                }
            }
            $str .= '</div>';
            unset($blogInfo); //clean up after ourselves        
        }
        return $str;
    }
    
    /**
     * get_formatted_delete_URL added by Daryl Hawes for moodle integration
     * An empty string is returned if the user is a guest, the user is not logged in,
     * or the user is not currently editing their blog page (turn editing on button)
     * we will only show edit link if the entry is in draft status or the user is an admin
     * note: teacher should not be allowed to edit or delete - only demote back to draft
     *
     * @uses $USER
     * @uses $CFG
     * @param bool $showImage If false a text link is printed. If true a linked delete icon is printed.
     * @todo get_formatted_delete_URL and get_formatted_edit_URL should be merged into a single function
     */
    function get_formatted_delete_URL($showImage=false) {
        global $USER, $CFG;
        $str = '';
        
        if ( !isguest() && blog_isLoggedIn() && blog_isediting() && blog_is_blog_admin($this->entryuserid)
             && (!$CFG->blog_enable_moderation || isadmin() || $blogEntry->entryPublishState == 'draft') ) {
            
            $str = '<div class="blogdelete">';
            
            //check if user is in blog's acl
            //instead of creating a new BlogInfo object might a BlogInfo pointer in BlogEntry constructor be better? Does php have singleton objects? if not then a bloginfo reference as an argument to the constructor of BlogEntry would be a good idea.
            $bloginfo =& new BlogInfo($this->entryuserid);
            //if so then show them an edit link
            if (blog_user_has_rights($bloginfo)) {
                $deleteString = '';
                if ($showImage) {
                    $deleteString = '<img src="'. $CFG->pixpath .'/t/delete.gif" alt="'. get_string('delete');
                    $deleteString .= '" title="'. get_string('delete') .'" align="absmiddle" border="0" />';
                } else {
                    $deleteString = get_string('delete');
                }
                if (!$showImage) {
                    $str .= '(';
                }
                $str .= '<a title="'. get_string('delete') .'" href="'. $this->get_entrydeleteurl() .'">'. $deleteString .'</a>';
                if (!$showImage) { 
                    $str .= ')'; 
                }
            }
            $str .= '</div>';
            unset($blogInfo); //clean up after ourselves
        }
        return $str;
    }

    /**
     * get_formatted_entry_body
     * getter for ->entryBody.
     *
     * @uses $CFG
     * @return string Entry body/summary run through moodle's format_text formatter and 
     *                  with slashes stripped from database entry
     */
    function get_formatted_entry_body() {
        global $CFG;
        include_once($CFG->libdir .'/weblib.php');
        if ( isset($this->entryFormat) ) {
            return format_text($this->entryBody, $this->entryFormat);
        }
        return stripslashes_safe($this->entryBody);
    }
    
    /**
     * get_unformatted_entry_body
     * getter for ->entryBody
     *
     * @return string Entry body/summary - raw string from database
     */
    function get_unformatted_entry_body() {
        return $this->entryBody;
    }

    /**
     * get_formatted_entry_extended_body
     * getter for ->entryExtendedBody
     *
     * @uses $CFG
     * @return string Entry extended body/content run through moodle's format_text formatter and 
     *                  with slashes stripped from database entry
     */
    function get_formatted_entry_extended_body() {
        global $CFG;
        include_once($CFG->libdir .'/weblib.php');
        if ( isset($this->entryFormat) ) {
            return format_text($this->entryExtendedBody, $this->entryFormat);
        }
        return stripslashes_safe($this->entryExtendedBody);
    }

    /**
     * get_unformatted_entry_extended_body
     * getter for ->entryExtendedBody
     *
     * @return string Entry extended body/content - raw string from database
     */
    function get_unformatted_entry_extended_body() {
        return $this->entryExtendedBody;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param string $title New entry subject
     */
    function set_title($title) {
        $this->entryTitle = $title;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param string $body New entry summary
     */
    function set_body($body) {
        $this->entryBody = $body;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param string $extendedbody New entry content
     */
    function set_extendedbody($extendedbody) {
        $this->entryExtendedBody = $extendedbody;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param string $format Moodle format_text format type.
     */
    function set_format($format) {
        $this->entryFormat = $format;
    }
    
    /**
     *
     * @return string
     */
    function get_entryurl() {
        global $CFG;
        return $CFG->wwwroot .'/blog/archive.php?userid='. $this->entryuserid .'&amp;postid='. $this->entryId;
    }

    /**
     *  The url of the news feed containing this item. Uses global admin config to determin what feed type to point to.
     * @return string
     */
    function get_entryfeedurl() {
        global $CFG;
        return $CFG->wwwroot .'/blog/rss.php?userid='. $this->entryuserid;
    }

    /**
     *  
     * @return string
     */
    function get_entryediturl() {
        global $CFG;
        return $CFG->wwwroot .'/blog/edit.php?userid='. $this->entryuserid .'&amp;editid='. $this->entryId;
    }


    /**
     *  
     * @return string
     */
    function get_entrydeleteurl() {
        global $CFG;
        return 'javascript:del(\''. $CFG->wwwroot .'/blog/\', '. $this->entryId .', '. $this->entryuserid .')';
    }

    /**
     *  
     * @return string
     */
    function get_entryblogthisurl() {
        global $CFG;
        return $CFG->wwwroot .'/blog/blogthis.php?userid='. $this->entryuserid .'&amp;act=use&amp;postid='. $this->entryId;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param int $userid The new author/owner's moodle user id
     */
    function set_userid($userid) {
        $this->entryuserid = $userid;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param string $publishstate A new publish state for this entry. One of: 
     *                  enum('draft','teacher','course','group','site','public') 
     * @return bool True if new state is allowed (and applied), false if not.
     */
    function set_publishstate($publishstate) {
        $applicablestates = array_keys(blog_applicable_publish_states($this->entryCourseId));
        if (in_array($publishstate, $applicablestates)) {
            $this->entryPublishState = $publishstate;
            return true;
        }
        return false;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param int $courseid The course by id that this entry should be associated with.
     */
    function set_courseid($courseid) {
        $this->entryCourseId = $courseid;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param int $groupid The groupid that this entry should be associated with.
     */
    function set_groupid($groupid) {
        $this->entryGroupId = $groupid;
    }

    /**
     * BlogEntry setters do not save to the database.
     * To save changes call the BlogEntry->save() function when ready.
     *
     * @param array $catids An array of category ids to associate this entry with. 
     */
    function set_categoryids($catids) {
        $this->entryCategoryIds = $catids;

        if (!empty($this->entryCategoryIds)) {
            if (!is_array($this->entryCategoryIds)) {
                $this->entryCategoryIds = array($this->entryCategoryIds);
            }
            $this->entryCategoryIds = array_unique($this->entryCategoryIds);
        }
        
        // now populate the entryCategories array
        if (!empty($this->entryCategoryIds)) {
            foreach ($this->entryCategoryIds as $categoryid) {
                if (! $currcat = get_record('blog_categories', 'id', $categoryid)) {
                    print 'Could not find category id '. $categoryid ."\n";
                    $this->entryCategories[$categoryid] = '';
                } else {
                    $this->entryCategories[$categoryid] = $currcat->catname;
                }
            }
        }
    }

    /**
     * This function will determine if the user is logged in and
     * able to make changes to the publish state of this entry
     *
     * @return bool True if user is allowed to change publish state
     */
    function user_can_change_publish_state() {
        // figure out who the currently logged in user is.
        // to change any publish state one must be logged in
        global $USER;
        if ( !isset($USER) || empty($USER) || !isset($USER->id) ) {
            // only site members are allowed to edit entries
            return 'Only site members are allowed to edit entries';
        } else {
            $uid = $USER->id;
        }
        if ( ($uid == $this->entryuserid) || (blog_is_blog_admin($this->entryuserid)) || (isadmin()) 
             || (isset($this->entryCourseId) && isteacher($this->entryCourseId)) ) {
            return true;
        }
        return false;
    }

    /**
     * added by Daryl Hawes for moodle integration
     *
     * @param int $uid The user attempting to view this entry
     * @return bool
     */
    function user_can_view($uid='') {
        global $USER;

        //first allow access to any post for admin users
        if ( isadmin() ) {
            return true;
        }
        
        //get the logged in user's id if needed
        if ($uid == '') {
            if ( isset($USER) && isset($USER->id)) {
                $uid = $USER->id;
            }
        }

        if ($this->entryPublishState == 'public') {
            return true;
        } else if ($this->entryPublishState == 'draft') {
            //only the owner is allowed to see their own draft message
            if ($uid == $this->entryuserid) {
                return true;
            } 
        } else if ($this->entryPublishState == 'site') {
            //user has a valid member id and user is not a guest of the site
            if ( ! $uid == '' && ! isguest() ) {
                return true;
            }
        } else if ($this->entryPublishState == 'course') {
            //there is a courseid and the user is a member of that course
            if ( isset($this->entryCourseId) && (isteacher($this->entryCourseId, $uid) || isstudent($this->entryCourseId, $uid) ) ) {
                return true;
            }
        } else if ($this->entryPublishState == 'teacher') {
            if ( isset($this->entryCourseId) && isteacher($this->entryCourseId, $uid) )  {
                return true;
            }
        } else if ($this->entryPublishState == 'group') {
            if ( isset($this->entryGroupId) && ismember($this->entryGroupId, $uid) )  {
                return true;
            }            
        }

        //nothing qualified - the user requesting access is not allowed to view this entry!
        return false;
    }
    
    
    /**
     * @param bool $return If true a string value is returned. If this variable is set to false
     *                      Then this function will print out the menu code and exit.
     * @param bool $includehelp If true a help button linking to the batch_publish page
     *                      will be included in the returned string
     * @return string|nil  If the $return param is set to true a string is returned.
     */
    function get_publish_to_menu($return=true, $includehelp=true) {
        $menu = '';
        if ($this->user_can_change_publish_state() && blog_isediting() ) {
            $menu .= '<div class="publishto">'. get_string('publishto', 'blog').': ';
            $options = blog_applicable_publish_states($this->entryCourseId);
            $menu .= choose_from_menu($options, $this->entryuserid .'-'. $this->entryId, $this->entryPublishState, '', '', '0', true);
            $menu .= "\n".'</div>'."\n";
            if ($includehelp) {
                $menu .= helpbutton('batch_publish', get_string('batchpublish', 'blog'), 'blog', true, false, '', true);
            }
        }
        if ($return) {
            return $menu;
        }
        print $menu;
    }
        
    /**
     * Save this entry to the database. 
     * This function can be used outside the BlogEntry class.
     * ex:
     * <code>
     * $myBlogEntry = blogInfo->get_blog_entry_by_id(100);
     * $myBlogEntry->set_title('New Title');
     * $myBlogEntry->save();
     * </code>
     * This function will handle all of the security and data integrity checking for you
     * @return null|string Error string returned when an error is encountered.
     */
    function save() {
        //check if the user is authorized to make this change
        // either they own this entry, are in the blog acl for this entry,
        // are an admin user or they teach the course this entry is associated with
        if ($this->user_can_change_publish_state()) {
            $applicablestates = array_keys(blog_applicable_publish_states($this->entryCourseId));
            if (in_array($this->entryPublishState, $applicablestates)) {
                // Yes they are authorized so update the entry.
                if ( $this->_update() ) {
                    //print_object($this); //debug:

                    //add a timestamp to the user preference for this userid to mark it updated
                    set_user_preference('bloglastmodified', time(), $this->entryuserid);
                    return;
                } else {
                    $error = 'An error occured saving this entry';
                }
            } else {
                $error = 'Publish state '. $this->entryPublishState .' is not available for this user';
            }
        } else {
            $error = 'User not allowed to edit this entry';
        }
        // must not have worked...
        return $error;
    }

    /**
     * _update
     *
     * This function is internal to the BlogEntry class and should not be used elsewhere.
     * It takes the currently set member variables and writes them to the database.
     * @return boolean
     */
    function _update() {
    
        global $db, $CFG;
        // generate the modification date
        $timenow = time();

        //load up the data object with the latest data
        $dataobject->id = intval($this->entryId);
        $dataobject->summary = $this->entryBody;
        $dataobject->content = $this->entryExtendedBody;
        $dataobject->subject = $this->entryTitle;
        $dataobject->format = intval($this->entryFormat);
        $dataobject->userid = intval($this->entryuserid);
        $dataobject->publishstate = $this->entryPublishState;

        if ($this->entryCourseId) {
            $dataobject->courseid = $this->entryCourseId;
        } else {
            $dataobject->courseid = SITEID;    //yu: in case change to all course
        }
        if ($this->entryGroupId) {
            $dataobject->groupid = $this->entryGroupId;
        } else {
            $dataobject->groupid = 0;    //yu: in case we change to all groups
        }
        $dataobject->lastmodified = $timenow;

        $dataobject->summary = ereg_replace("'", '<tick>', $dataobject->summary);
        // The wysiwyg html editor adds a <br /> tag to the extendedbody.
        // cleanup the extendedbody first
        if ($dataobject->content == '<br />') {
            $dataobject->content = '';
        }
        $dataobject->content= ereg_replace("'", '<tick>', $dataobject->content);
        $dataobject->subject = ereg_replace("'", '<tick>', $dataobject->subject);
        $dataobject->subject = addslashes($dataobject->subject);
        $dataobject->summary = addslashes($dataobject->summary);
        $dataobject->content = addslashes($dataobject->content);

        // First update the entry's categories. Remove all, then add back those passed in
        $sql = 'DELETE FROM '. $CFG->prefix .'blog_categories_entries WHERE entryid='. $this->entryId;
        $rs = $db->Execute($sql);

        if (!empty($this->entryCategoryIds)) {
            if (!is_array($this->entryCategoryIds)) {
                $this->entryCategoryIds = array($this->entryCategoryIds);
            }
            $this->entryCategoryIds = array_unique($this->entryCategoryIds);
            foreach ($this->entryCategoryIds as $categoryid) {
                $cat->entryid = $this->entryId;
                $cat->categoryid = $categoryid;
                insert_record('blog_categories_entries', $cat);
            }
        }

        // next update the entry itself
        if (update_record('post', $dataobject)) {
            return true;
        }
        //failure
        return false;
    }

}//end class BlogEntry
?>
