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
 $newEntry->set_body('a post here');
 $newEntry->set_userid(2);
 $newEntry->save();
 */
class BlogEntry {
    // member variables
    var $entryId; // post.id
    var $entryBody; // post.summary
    var $entryTitle; // post.subject
    var $entryFormat; // post.format
    var $entryuserid; // post.author    
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

//        print "Debug: entryId: $this->entryId"; //debug
//        print_object($this->entryCategoryIds); //debug
        
        $this->entryBody = ereg_replace('<tick>', "'", stripslashes_safe($entrydetails->summary));
        
        $strftimedaydatetime = get_string('strftimedaydatetime');
        $this->entryLastModified = $entrydetails->lastmodified;
        $this->formattedEntryLastModified = userdate($this->entryLastModified, $strftimedaydatetime);
        $this->entryCreated = $entrydetails->created;
        $this->formattedEntryCreated = userdate($this->entryCreated, $strftimedaydatetime);
        
        $this->entryuserid = $entrydetails->userid;
        
        //added stripslashes_safe here for rss feeds. Will this conflict anywhere?
        
        $this->entryTitle = ereg_replace('<tick>', "'", stripslashes_safe($entrydetails->subject));   //subject, not title!
        
        $this->entryFormat = $entrydetails->format;

        if (isset($entrydetails->publishstate) ) {
            $this->entryPublishState = $entrydetails->publishstate;
        } else {
            $this->entryPublishState = 'draft';
        }
        $this->entryAuthorName = fullname($entrydetails);  // firstname and lastname defined
        $this->entryAuthorEmail = $entrydetails->email;

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
     * @param string $format Moodle format_text format type.
     */
    function set_format($format) {
        $this->entryFormat = $format;
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
            $options = blog_applicable_publish_states();
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

        // next update the entry itself
        if (update_record('post', $dataobject)) {
            return true;
        }
        //failure
        return false;
    }

}//end class BlogEntry
?>
