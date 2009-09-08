<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Classes for Blogs.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Blog_entry class. Represents an entry in a user's blog. Contains all methods for managing this entry.
 * This class does not contain any HTML-generating code. See blog_listing sub-classes for such code.
 * This class follows the Object Relational Mapping technique, its member variables being mapped to
 * the fields of the posts table.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_entry {
    // Public Database fields
    public $id;
    public $userid;
    public $subject;
    public $summary;
    public $rating = 0;
    public $attachment;
    public $publishstate;

    // Locked Database fields (Don't touch these)
    public $courseid = 0;
    public $groupid = 0;
    public $module = 'blog';
    public $moduleid = 0;
    public $coursemoduleid = 0;
    public $content;
    public $format = 1;
    public $uniquehash = '';
    public $lastmodified;
    public $created;
    public $usermodified;

    // Other class variables
    public $form;
    public $tags = array();

    // Methods
    /**
     * Constructor. If given an id, will fetch the corresponding record from the DB.
     *
     * @param mixed $id_or_params A blog entry id if INT, or data for a new entry if array
     */
    public function __construct($id_or_params=null, $form=null) {
        global $DB;

        if (!empty($id_or_params) && !is_array($id_or_params) && !is_object($id_or_params)) {
            $object = $DB->get_record('post', array('id' => $id_or_params));
            foreach ($object as $var => $val) {
                $this->$var = $val;
            }
        } else if (!empty($id_or_params) && (is_array($id_or_params) || is_object($id_or_params))) {
            foreach ($id_or_params as $var => $val) {
                $this->$var = $val;
            }
        }

        $this->form = $form;
    }

    /**
     * Prints or returns the HTML for this blog entry.
     *
     * @param bool $return
     * @return string
     */
    public function print_html($return=false) {

        global $USER, $CFG, $COURSE, $DB, $OUTPUT;

        // Comments
        $user = $DB->get_record('user', array('id'=>$this->userid));

        $template['body'] = format_text($this->summary, $this->format);
        $template['title'] = '<a id="b'. s($this->id) .'" />';
        //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
        $template['title'] .= '<span class="nolink">'. format_string($this->subject) .'</span>';
        $template['userid'] = $user->id;
        $template['author'] = fullname($user);
        $template['created'] = userdate($this->created);

        if($this->created != $this->lastmodified){
            $template['lastmod'] = userdate($this->lastmodified);
        }

        $template['publishstate'] = $this->publishstate;

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        //check to see if the entry is unassociated with group/course level access
        $unassociatedentry = false;
        if (!empty($CFG->useblogassociations) && ($this->publishstate == 'group' || $this->publishstate == 'course')) {
            if (!$DB->record_exists('blog_association', array('blogid' => $this->id))) {
                $unassociatedentry = true;
            }
        }

        /// Start printing of the blog
        $table = new html_table();
        $table->cellspacing = 0;
        $table->add_classes('forumpost blog_entry blog'. ($unassociatedentry ? 'draft' : $template['publishstate']));
        $table->width = '100%';

        $picturecell = new html_table_cell();
        $picturecell->add_classes('picture left');
        $picturecell->text = $OUTPUT->user_picture(moodle_user_picture::make($user, SITEID));

        $table->head[] = $picturecell;

        $topiccell = new html_table_cell();
        $topiccell->add_classes('topic starter');
        $topiccell->text = $OUTPUT->container($template['title'], 'subject');
        $topiccell->text .= $OUTPUT->container_start('author');

        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $COURSE->id)));
        $by = new object();
        $by->name =  $OUTPUT->link(html_link::make(new moodle_url($CFG->wwwroot.'/user/view.php', array('id' => $user->id, 'course' => $COURSE->id)), $fullname));
        $by->date = $template['created'];

        $topiccell->text .= get_string('bynameondate', 'forum', $by);
        $topiccell->text .= $OUTPUT->container_end();
        $topiccell->header = false;
        $table->head[] = $topiccell;

    /// Actual content
        $mainrow = new html_table_row();

        $leftsidecell = new html_table_cell();
        $leftsidecell->add_classes('left side');
        $mainrow->cells[] = $leftsidecell;

        $contentcell = new html_table_cell();
        $contentcell->add_class('content');

        if ($this->attachment) {
            $attachedimages = $OUTPUT->container($this->print_attachments(), 'attachments');
        } else {
            $attachedimages = '';
        }

        //retrieve associations in case they're needed early
        $blog_associations = $DB->get_records('blog_association', array('blogid' => $this->id));
        //determine text for publish state
        switch ($template['publishstate']) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
            break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
            break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
            break;
            default:
                $blogtype = '';
            break;

        }

        $contentcell->text .= $OUTPUT->container($blogtype, 'audience');

        $contentcell->text .= $template['body'];
        $contentcell->text .= $attachedimages;

        // Uniquehash is used as a link to an external blog
        if (!empty($this->uniquehash) && blog_is_valid_url($this->uniquehash)) {
            $contentcell->text .= $OUTPUT->container_start('externalblog');
            $contentcell->text .= $OUTPUT->link(html_link::make($this->uniquehash, get_string('linktooriginalentry', 'blog')));
            $contentcell->text .= $OUTPUT->container_end();
        }

        // Links to tags

        if (!empty($CFG->usetags) && ($blogtags = tag_get_tags_csv('post', $this->id)) ) {
            $contentcell->text .= $OUTPUT->container_start('tags');

            if ($blogtags) {
                $contentcell->text .= get_string('tags', 'tag') .': '. $blogtags;
            }
            $contentcell->text .= $OUTPUT->container_end();
        }

        //add associations
        if (!empty($CFG->useblogassociations) && $blog_associations) {
            $contentcell->text .= $OUTPUT->container_start('tags');
            $assoc_str = '';

            foreach ($blog_associations as $assoc_rec) {  //first find and show the associated course
                $context_rec = $DB->get_record('context', array('id' => $assoc_rec->contextid));
                if ($context_rec->contextlevel ==  CONTEXT_COURSE) {
                    $associcon = new moodle_action_icon();
                    $associcon->link->url = new moodle_url($CFG->wwwroot.'/course/view.php', array('id' => $context_rec->instanceid));
                    $associcon->image->src = $OUTPUT->old_icon_url('i/course');
                    $associcon->linktext = $DB->get_field('course', 'shortname', array('id' => $context_rec->instanceid));
                    $assoc_str .= $OUTPUT->action_icon($associcon);
                }
            }

            foreach ($blog_associations as $assoc_rec) {  //now show each mod association
                $context_rec = $DB->get_record('context', array('id' => $assoc_rec->contextid));

                if ($context_rec->contextlevel ==  CONTEXT_MODULE) {
                    $modinfo = $DB->get_record('course_modules', array('id' => $context_rec->instanceid));
                    $modname = $DB->get_field('modules', 'name', array('id' => $modinfo->module));

                    $associcon = new moodle_action_icon();
                    $associcon->link->url = new moodle_url($CFG->wwwroot.'/mod/'.$modname.'/view.php', array('id' => $modinfo->id));
                    $associcon->image->src = $OUTPUT->mod_icon_url('icon', $modname);
                    $associcon->linktext = $DB->get_field($modname, 'name', array('id' => $modinfo->instance));
                    $assoc_str .= $OUTPUT->action_icon($associcon);

                    $assoc_str .= ', ';
                    $assoc_str .= $OUTPUT->action_icon($associcon);
                }
            }
            $contentcell->text .= get_string('associations', 'blog') . ': '. $assoc_str;

            $contentcell->text .= $OUTPUT->container_end();
        }

        if ($unassociatedentry) {
            $contentcell->text .= $OUTPUT->container(get_string('associationunviewable', 'blog'), 'noticebox');
        }

    /// Commands

        $contentcell->text .= $OUTPUT->container_start('commands');

        if (blog_user_can_edit_entry($this)) {
            $contentcell->text .= $OUTPUT->link(html_link::make(new moodle_url($CFG->wwwroot.'/blog/edit.php', array('action' => 'edit', 'entryid' => $this->id)), $stredit)) . ' | ';
            if (!$DB->record_exists_sql('SELECT a.timedue, a.preventlate, a.emailteachers, a.var2, asub.grade
                                          FROM {assignment} a, {assignment_submissions} as asub WHERE
                                          a.id = asub.assignment AND userid = '.$USER->id.' AND a.assignmenttype = \'blog\'
                                          AND asub.data1 = \''.$this->id.'\'')) {

                $contentcell->text .= $OUTPUT->link(html_link::make(new moodle_url($CFG->wwwroot.'/blog/edit.php', array('action' => 'delete', 'entryid' => $this->id)), $strdelete)) . ' | ';
            }
        }

        $contentcell->text .= $OUTPUT->link(html_link::make(new moodle_url($CFG->wwwroot.'/blog/index.php', array('entryid' => $this->id)), get_string('permalink', 'blog')));

        $contentcell->text .= $OUTPUT->container_end();

        if (isset($template['lastmod']) ){
            $contentcell->text .= '<div style="font-size: 55%;">';
            $contentcell->text .= ' [ '.get_string('modified').': '.$template['lastmod'].' ]';
            $contentcell->text .= '</div>';
        }

        $mainrow->cells[] = $contentcell;
        $table->data = array($mainrow);

        if ($return) {
            return $OUTPUT->table($table);
        } else {
            echo $OUTPUT->table($table);
        }
    }

    /**
     * Inserts this entry in the database. Access control checks must be done by calling code.
     *
     * @param mform $form Used for attachments
     * @return void
     */
    public function process_attachment($form) {
        $this->form = $form;
    }

    /**
     * Inserts this entry in the database. Access control checks must be done by calling code.
     * TODO Set the publishstate correctly
     * @param mform $form Used for attachments
     * @return void
     */
    public function add() {
        global $CFG, $USER, $DB;

        unset($this->id);
        $this->module       = 'blog';
        $this->userid       = (empty($this->userid)) ? $USER->id : $this->userid;
        $this->lastmodified = time();
        $this->created      = time();

        // Insert the new blog entry.
        $this->id = $DB->insert_record('post', $this);

        // Add blog attachment
        if (!empty($this->form) && $this->form->get_new_filename('attachment')) {
            if ($this->form->save_stored_file('attachment', SYSCONTEXTID, 'blog', $this->id, '/', $this->form->get_new_filename('attachment'), $USER->id)) {
                $DB->set_field("post", "attachment", 1, array("id"=>$this->id));
            }
        }

        // Update tags.
        $this->add_tags_info();

        if (!empty($CFG->useblogassociations)) {
            $this->add_associations();
            add_to_log(SITEID, 'blog', 'add', 'index.php?userid='.$this->userid.'&entryid='.$this->id, $this->subject);

        }

        tag_set('post', $this->id, $this->tags);
    }

    /**
     * Updates this entry in the database. Access control checks must be done by calling code.
     *
     * @param mform $form Used for attachments
     * @return void
     */
    public function edit($params=array(), $form=null) {
        global $CFG, $USER, $DB;

        $this->form = $form;
        foreach ($params as $var => $val) {
            $this->$var = $val;
        }

        //check to see if it's part of a submitted blog assignment
        if ($blogassignment = $DB->get_record_sql("SELECT a.timedue, a.preventlate, a.emailteachers, a.var2, asi.grade, asi.id
                                                  FROM {assignment} a, {assignment_submissions} as asi WHERE
                                                  a.id = asi.assignment AND userid = ? AND a.assignmenttype = 'blog'
                                                  AND asi.data1 = ?", array($USER->id, $this->id))) {
            //email teachers if necessary
            if ($blogassignment->emailteachers) {
                email_teachers($DB->get_record('assignment_submissions', array('id'=>$blogassignment['id'])));
            }

        } else {
            //only update the attachment and associations if it is not a submitted assignment
            if (!empty($CFG->useblogassociations)) {
                $this->add_associations();
            }
        }

        $this->lastmodified = time();

        if (!empty($this->form) && $this->form->get_new_filename('attachment')) {
            $this->delete_attachments();
            if ($this->form->save_stored_file('attachment', SYSCONTEXTID, 'blog', $this->id, '/', false, $USER->id)) {
                $this->attachment = 1;
            } else {
                $this->attachment = 1;
            }
        }

        // Update record
        $DB->update_record('post', $this);
        tag_set('post', $this->id, $this->tags);

        add_to_log(SITEID, 'blog', 'update', 'index.php?userid='.$USER->id.'&entryid='.$this->id, $this->subject);
    }

    /**
     * Deletes this entry from the database. Access control checks must be done by calling code.
     *
     * @return void
     */
    public function delete() {
        global $DB, $USER;

        $returnurl = '';

        //check to see if it's part of a submitted blog assignment
        if ($blogassignment = $DB->get_record_sql("SELECT a.timedue, a.preventlate, a.emailteachers, asub.grade
                                                  FROM {assignment} a, {assignment_submissions} as asub WHERE
                                                  a.id = asub.assignment AND userid = ? AND a.assignmenttype = 'blog'
                                                  AND asub.data1 = ?", array($USER->id, $this->id))) {
            print_error('cantdeleteblogassignment', 'blog', $returnurl);
        }

        $this->delete_attachments();

        $DB->delete_records('post', array('id' => $this->id));
        tag_set('post', $this->id, array());

        add_to_log(SITEID, 'blog', 'delete', 'index.php?userid='. $this->userid, 'deleted blog entry with entry id# '. $this->id);
    }

    /**
     * function to add all context associations to an entry
     * @param int entry - data object processed to include all 'entry' fields and extra data from the edit_form object
     */
    public function add_associations() {
        global $DB, $USER;

        $allowaddcourseassoc = true;

        $this->remove_associations();

        if (!empty($this->courseassoc)) {
            $this->add_association($this->courseassoc);
            $allowaddcourseassoc = false;
        }

        if (!empty($this->modassoc)) {
            foreach ($this->modassoc as $modid) {
                $this->add_association($modid, $allowaddcourseassoc);
                $allowaddcourseassoc = false;   //let the course be added the first time
            }
        }
    }

    /**
     * add a single association for a blog entry
     * @param int contextid - id of context to associate with the blog entry
     */
    public function add_association($contextid) {
        global $DB;

        $assoc_object = new StdClass;
        $assoc_object->contextid = $contextid;
        $assoc_object->blogid = $this->id;
        $DB->insert_record('blog_association', $assoc_object);
    }

    /**
     * remove all associations for a blog entry
     * @return voic
     */
    public function remove_associations() {
        global $DB;
        $DB->delete_records('blog_association', array('blogid' => $this->id));
    }

    /**
     * Deletes all the user files in the attachments area for an entry
     *
     * @return void
     */
    public function delete_attachments() {
        $fs = get_file_storage();
        $fs->delete_area_files(SYSCONTEXTID, 'blog', $this->id);
    }

    /**
     * if return=html, then return a html string.
     * if return=text, then return a text-only string.
     * otherwise, print HTML for non-images, and return image HTML
     *
     * @param bool $return Whether to return or print the generated code
     * @return void
     */
    public function print_attachments($return=false) {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $fs = get_file_storage();
        $browser = get_file_browser();

        $files = $fs->get_area_files(SYSCONTEXTID, 'blog', $this->id);

        $imagereturn = "";
        $output = "";

        $strattachment = get_string("attachment", "forum");

        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            $filename = $file->get_filename();
            $ffurl    = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.SYSCONTEXTID.'/blog/'.$this->id.'/'.$filename);
            $type     = $file->get_mimetype();
            $icon     = mimeinfo_from_type("icon", $type);
            $type     = mimeinfo_from_type("type", $type);

            $image = "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"\" />";

            if ($return == "html") {
                $output .= "<a href=\"$ffurl\">$image</a> ";
                $output .= "<a href=\"$ffurl\">$filename</a><br />";

            } else if ($return == "text") {
                $output .= "$strattachment $filename:\n$ffurl\n";

            } else {
                if (in_array($type, array('image/gif', 'image/jpeg', 'image/png'))) {    // Image attachments don't get printed as links
                    $imagereturn .= "<br /><img src=\"$ffurl\" alt=\"\" />";
                } else {
                    $imagereturn .= "<a href=\"$ffurl\">$image</a> ";
                    $imagereturn .= filter_text("<a href=\"$ffurl\">$filename</a><br />");
                }
            }
        }

        if ($return) {
            return $output;
        }

        return $imagereturn;

    }

    /**
     * function to attach tags into an entry
     * @return void
     */
    public function add_tags_info() {

        $tags = array();

        if ($otags = optional_param('otags', '', PARAM_INT)) {
            foreach ($otags as $tagid) {
                // TODO : make this use the tag name in the form
                if ($tag = tag_get('id', $tagid)) {
                    $tags[] = $tag->name;
                }
            }
        }

        tag_set('post', $this->id, $tags);
    }

    /**
     * User can edit a blog entry if this is their own blog entry and they have
     * the capability moodle/blog:create, or if they have the capability
     * moodle/blog:manageentries.
     * This also applies to deleting of entries.
     *
     * @param int $userid Optional. If not given, $USER is used
     * @return boolean
     */
    public function can_user_edit($userid=null) {
        global $CFG, $USER;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can edit any blog entry
        }

        if ($this->userid == $userid && has_capability('moodle/blog:create', $sitecontext)) {
            return true; // can edit own when having blog:create capability
        }

        return false;
    }

    /**
     * Checks to see if a user can view the blogs of another user.
     * Only blog level is checked here, the capabilities are enforced
     * in blog/index.php
     *
     * @param int $targetuserid ID of the user we are checking
     *
     * @return bool
     */
    public function can_user_view($targetuserid) {
        global $CFG, $USER, $DB;

        if (empty($CFG->bloglevel)) {
            return false; // blog system disabled
        }

        if (!empty($USER->id) and $USER->id == $targetuserid) {
            return true; // can view own entries in any case
        }

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can manage all entries
        }

        // coming for 1 entry, make sure it's not a draft
        if ($this->publishstate == 'draft') {
            return false;  // can not view draft of others
        }

        // coming for 1 entry, make sure user is logged in, if not a public blog
        if ($this->publishstate != 'public' && !isloggedin()) {
            return false;
        }

        switch ($CFG->bloglevel) {
            case BLOG_GLOBAL_LEVEL:
                return true;
                break;

            case BLOG_SITE_LEVEL:
                if (!empty($USER->id)) { // not logged in viewers forbidden
                    return true;
                }
                return false;
                break;

            case BLOG_USER_LEVEL:
            default:
                $personalcontext = get_context_instance(CONTEXT_USER, $targetuserid);
                return has_capability('moodle/user:readuserblogs', $personalcontext);
                break;
        }
    }

    /**
     * Use this function to retrieve a list of publish states available for
     * the currently logged in user.
     *
     * @return array This function returns an array ideal for sending to moodles'
     *                choose_from_menu function.
     */

    public static function get_applicable_publish_states() {
        global $CFG;
        $options = array();

        // everyone gets draft access
        if ($CFG->bloglevel >= BLOG_USER_LEVEL) {
            $options['draft'] = get_string('publishtonoone', 'blog');
        }

        if ($CFG->bloglevel > BLOG_USER_LEVEL) {
            $options['site'] = get_string('publishtosite', 'blog');
        }

        if ($CFG->bloglevel >= BLOG_GLOBAL_LEVEL) {
            $options['public'] = get_string('publishtoworld', 'blog');
        }

        return $options;
    }
}

/**
 * Abstract Blog_Listing class: used to gather blog entries and output them as listings. One of the subclasses must be used.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_listing {
    /**
     * Array of blog_entry objects.
     * @var array $entries
     */
    public $entries = array();

    /**
     * An array of blog_filter_* objects
     * @var array $filters
     */
    public $filters = array();

    /**
     * Constructor
     *
     * @param array $filters An associative array of filtername => filterid
     */
    public function __construct($filters=array()) {
        // Unset filters overridden by more specific filters
        foreach ($filters as $type => $id) {
            if (!empty($type) && !empty($id)) {
                $this->filters[$type] = blog_filter::get_instance($id, $type);
            }
        }

        foreach ($this->filters as $type => $filter) {
            foreach ($filter->overrides as $override) {
                if (array_key_exists($override, $this->filters)) {
                    unset($this->filters[$override]);
                }
            }
        }
    }

    /**
     * Fetches the array of blog entries.
     *
     * @return array
     */
    public function get_entries($start=0, $limit=10) {
        global $DB;

        if (empty($this->entries)) {
            if ($sql_array = $this->get_entry_fetch_sql()) {
                $this->entries = $DB->get_records_sql($sql_array['sql'] . " LIMIT $start, $limit", $sql_array['params']);
            } else {
                return false;
            }
        }

        return $this->entries;
    }

    public function get_entry_fetch_sql($count=false, $sort='lastmodified DESC', $userid = false) {
        global $DB, $USER, $CFG;

        if(!$userid) {
            $userid = $USER->id;
        }

        // The query used to locate blog entries is complicated.  It will be built from the following components:
        $requiredfields = "p.*, u.firstname, u.lastname, u.email";  // the SELECT clause
        $tables = array('p' => 'post', 'u' => 'user');   // components of the FROM clause (table_id => table_name)
        $conditions = array('u.deleted = 0', 'p.userid = u.id', 'p.module = \'blog\'');  // components of the WHERE clause (conjunction)

        // build up a clause for permission constraints

        $params = array();

        // fix for MDL-9165, use with readuserblogs capability in a user context can read that user's private blogs
        // admins can see all blogs regardless of publish states, as described on the help page
        if (has_capability('moodle/user:readuserblogs', get_context_instance(CONTEXT_SYSTEM))) {
            // don't add permission constraints

        } else if(!empty($this->filters['user']) && has_capability('moodle/user:readuserblogs',
                get_context_instance(CONTEXT_USER, (empty($this->filters['user']->id) ? 0 : $this->filters['user']->id)))) {
            // don't add permission constraints

        } else {
            if (isloggedin() && !has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM, SITEID), $userid, false)) {
                $assocexists = $DB->record_exists('blog_association', array());  //dont check association records if there aren't any

                //begin permission sql clause
                $permissionsql =  '(p.userid = ? ';
                $params[] = $userid;

                if ($CFG->bloglevel >= BLOG_SITE_LEVEL) { // add permission to view site-level entries
                    $permissionsql .= " OR p.publishstate = 'site' ";
                }

                if ($CFG->bloglevel >= BLOG_GLOBAL_LEVEL) { // add permission to view global entries
                    $permissionsql .= " OR p.publishstate = 'public' ";
                }

                $permissionsql .= ') ';   //close permissions sql clause
            } else {  // default is access to public entries
                $permissionsql = "p.publishstate = 'public'";
            }
            $conditions[] = $permissionsql;  //add permission constraints
        }

        foreach ($this->filters as $type => $blog_filter) {
            $conditions = array_merge($conditions, $blog_filter->conditions);
            $params = array_merge($params, $blog_filter->params);
            $tables = array_merge($tables, $blog_filter->tables);
        }

        $tablessql = '';  // build up the FROM clause
        foreach ($tables as $tablename => $table) {
            $tablessql .= ($tablessql ? ', ' : '').'{'.$table.'} '.$tablename;
        }

        $sql = ($count) ? 'SELECT COUNT(*)' : 'SELECT ' . $requiredfields;
        $sql .= " FROM $tablessql WHERE " . implode(' AND ', $conditions);
        $sql .= ($count) ? '' : " GROUP BY p.id ORDER BY $sort";

        return array('sql' => $sql, 'params' => $params);
    }

    /**
     * Outputs all the blog entries aggregated by this blog listing.
     *
     * @return void
     */
    public function print_entries() {
        global $CFG, $USER, $DB, $OUTPUT;
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        $page  = optional_param('blogpage', 0, PARAM_INT);
        $limit = optional_param('limit', get_user_preferences('blogpagesize', 10), PARAM_INT);
        $start = $page * $limit;

        $morelink = '<br />&nbsp;&nbsp;';

        if ($sql_array = $this->get_entry_fetch_sql(true)) {
            $totalentries = $DB->count_records_sql($sql_array['sql'], $sql_array['params']);
        } else {
            $totalentries = 0;
        }

        $entries = $this->get_entries($start, $limit);
        $pagingbar = moodle_paging_bar::make($totalentries, $page, $limit, $this->get_baseurl());
        $pagingbar->pagevar = 'blogpage';

        echo $OUTPUT->paging_bar($pagingbar);

        /* TODO RSS link
        if ($CFG->enablerssfeeds) {
            $this->blog_rss_print_link($filtertype, $filterselect, $tag);
        }
        */

        if (has_capability('moodle/blog:create', $sitecontext)) {
            //the user's blog is enabled and they are viewing their own blog
            $userid = optional_param('userid', null, PARAM_INT);

            if (empty($userid) || (!empty($userid) && $userid == $USER->id)) {
                $add_url = new moodle_url("$CFG->wwwroot/blog/edit.php");
                $url_params = array('action' => 'add',
                                    'userid' => $userid,
                                    'courseid' => optional_param('courseid', null, PARAM_INT),
                                    'groupid' => optional_param('groupid', null, PARAM_INT),
                                    'modid' => optional_param('modid', null, PARAM_INT),
                                    'tagid' => optional_param('tagid', null, PARAM_INT),
                                    'tag' => optional_param('tag', null, PARAM_INT),
                                    'search' => optional_param('search', null, PARAM_INT));

                foreach ($url_params as $var => $val) {
                    if (empty($val)) {
                        unset($url_params[$var]);
                    }
                }
                $add_url->params($url_params);

                $addlink = '<div class="addbloglink">';
                $addlink .= '<a href="'.$add_url->out().'">'. get_string('addnewentry', 'blog').'</a>';
                $addlink .= '</div>';
                echo $addlink;
            }
        }

        if ($entries) {
            $count = 0;

            foreach ($entries as $entry) {
                $blog_entry = new blog_entry($entry);
                $blog_entry->print_html();
                $count++;
            }

            echo $OUTPUT->paging_bar($pagingbar);

            if (!$count) {
                print '<br /><div style="text-align:center">'. get_string('noentriesyet', 'blog') .'</div><br />';
            }

            print $morelink.'<br />'."\n";
            return;
        }
    }

    /// Find the base url from $_GET variables, for print_paging_bar
    public function get_baseurl() {
        $getcopy  = $_GET;

        unset($getcopy['blogpage']);

        if (!empty($getcopy)) {
            $first = false;
            $querystring = '';

            foreach ($getcopy as $var => $val) {
                if (!$first) {
                    $first = true;
                    $querystring .= "?$var=$val";
                } else {
                    $querystring .= '&amp;'.$var.'='.$val;
                    $hasparam = true;
                }
            }
        } else {
            $querystring = '?';
        }

        return strip_querystring(qualified_me()) . $querystring;

    }
}

/**
 * Abstract class for blog_filter objects.
 * A set of core filters are implemented here. To write new filters, you need to subclass
 * blog_filter and give it the name of the type you want (for example, blog_filter_entry).
 * The blog_filter abstract class will automatically use it when the filter is added to the
 * URL. The first parameter of the constructor is the ID of your filter, but it can be a string
 * or have any other meaning you wish it to have. The second parameter is called $type and is
 * used as a sub-type for filters that have a very similar implementation (see blog_filter_context for an example)
 */
abstract class blog_filter {
    /**
     * An array of strings representing the available filter types for each blog_filter.
     * @var array $available_types
     */
    public $available_types = array();

    /**
     * The type of filter (for example, types of blog_filter_context are site, course and module)
     * @var string $type
     */
    public $type;

    /**
     * The unique ID for a filter's associated record
     * @var int $id
     */
    public $id;

    /**
     * An array of table aliases that are used in the WHERE conditions
     * @var array $tables
     */
    public $tables = array();

    /**
     * An array of WHERE conditions
     * @var array $conditions
     */
    public $conditions = array();

    /**
     * An array of SQL params
     * @var array $params
     */
    public $params = array();

    /**
     * An array of filter types which this particular filter type overrides: their conditions will not be evaluated
     */
    public $overrides = array();

    public function __construct($id, $type=null) {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * TODO This is poor design. A parent class should not know anything about its children.
     * The default case helps to resolve this design issue
     */
    public static function get_instance($id, $type) {

        switch ($type) {
            case 'site':
            case 'course':
            case 'module':
                return new blog_filter_context($id, $type);
                break;

            case 'group':
            case 'user':
                return new blog_filter_user($id, $type);
                break;

            case 'tag':
                return new blog_filter_tag($id);
                break;

            default:
                $class_name = "blog_filter_$type";
                if (class_exists($class_name)) {
                    return new $class_name($id, $type);
                }
        }
    }
}

/**
 * This filter defines the context level of the blog entries being searched: site, course, module
 */
class blog_filter_context extends blog_filter {
    /**
     * Constructor
     *
     * @param string $type
     * @param int    $id
     */
    public function __construct($id=null, $type='site') {
        global $SITE, $CFG, $DB;

        if (empty($id)) {
            $this->type = 'site';
        } else {
            $this->id = $id;
            $this->type = $type;
        }

        $this->available_types = array('site' => get_string('site'), 'course' => get_string('course'), 'module' => get_string('module'));

        switch ($this->type) {
            case 'course': // Careful of site course!
                // Ignore course filter if blog associations are not enabled
                if ($this->id != $SITE->id && !empty($CFG->useblogassociations)) {
                    $this->overrides = array('site');
                    $context = get_context_instance(CONTEXT_COURSE, $this->id);
                    $this->tables['ba'] = 'blog_association';
                    $this->conditions[] = 'p.id = ba.blogid';
                    $this->conditions[] = 'ba.contextid = '.$context->id;
                    break;
                } else {
                    // We are dealing with the site course, do not break from the current case
                }

            case 'site':
                // No special constraints
                break;
            case 'module':
                if (!empty($CFG->useblogassociations)) {
                    $this->overrides = array('course', 'site');

                    $context = get_context_instance(CONTEXT_MODULE, $this->id);
                    $this->tables['ba'] = 'blog_association';
                    $this->tables['p']  = 'post';
                    $this->conditions = array('p.id = ba.blogid', 'ba.contextid = ?');
                    $this->params = array($context->id);
                }
                break;
        }
    }
}

/**
 * This filter defines the user level of the blog entries being searched: a userid or a groupid.
 * It can be combined with a context filter in order to refine the search.
 */
class blog_filter_user extends blog_filter {
    public $tables = array('u' => 'user');

    /**
     * Constructor
     *
     * @param string $type
     * @param int    $id
     */
    public function __construct($id=null, $type='user') {
        $this->available_types = array('user' => get_string('user'), 'group' => get_string('group'));

        if (empty($id)) {
            $this->id = $USER->id;
            $this->type = 'user';
        } else {
            $this->id = $id;
            $this->type = $type;
        }

        if ($this->type == 'user') {
            $this->conditions = array('u.id = ?');
            $this->params = array($this->id);
            $this->overrides = array('group');

        } elseif ($this->type == 'group') {
            $this->overrides = array('course', 'site');

            $this->tables['gm'] = 'groups_members';
            $this->conditions[] = 'p.userid = gm.userid';
            $this->conditions[] = 'gm.groupid = ?';
            $this->params[]     = $this->id;

            if (!empty($CFG->useblogassociations)) {  // only show blog entries associated with this course
                $course_context     = get_context_instance(CONTEXT_COURSE, $DB->get_field('groups', 'courseid', array('id' => $this->id)));
                $this->tables['ba'] = 'blog_association';
                $this->conditions[] = 'gm.groupid = ?';
                $this->conditions[] = 'ba.contextid = ?';
                $this->conditions[] = 'ba.blogid = p.id';
                $this->params[]     = $this->id;
                $this->params[]     = $course_context->id;
            }
        }
    }
}

/**
 * This filter defines a tag by which blog entries should be searched.
 */
class blog_filter_tag extends blog_filter {
    public $tables = array('t' => 'tag', 'ti' => 'tag_instance', 'p' => 'post');

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($id) {
        global $DB;
        $this->id = $id;

        $this->conditions = array('ti.tagid = t.id',
                                  "ti.itemtype = 'post'",
                                  'ti.itemid = p.id',
                                  't.id = ?');
        $this->params = array($this->id);
    }
}

/**
 * This filter defines a specific blog entry id.
 */
class blog_filter_entry extends blog_filter {
    public $conditions = array('p.id = ?');
    public $overrides  = array('site', 'course', 'module', 'group', 'user', 'tag');

    public function __construct($id) {
        $this->id = $id;
        $this->params[] = $this->id;
    }
}

/**
 * Filter used to perform full-text search on an entry's subject, summary and content
 */
class blog_filter_search extends blog_filter {

    public function __construct($search_term) {
        global $DB;
        $ilike = $DB->sql_ilike();
        $this->conditions = array("(p.summary $ilike '%$search_term%' OR
                                    p.content $ilike '%$search_term%' OR
                                    p.subject $ilike '%$search_term%')");
    }
}
