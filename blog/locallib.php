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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Blog_entry class. Represents an entry in a user's blog. Contains all methods for managing this entry.
 * This class does not contain any HTML-generating code. See blog_listing sub-classes for such code.
 * This class follows the Object Relational Mapping technique, its member variables being mapped to
 * the fields of the post table.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_entry implements renderable {
    // Public Database fields.
    public $id;
    public $userid;
    public $subject;
    public $summary;
    public $rating = 0;
    public $attachment;
    public $publishstate;

    // Locked Database fields (Don't touch these).
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

    // Other class variables.
    public $form;
    public $tags = array();

    /** @var StdClass Data needed to render the entry */
    public $renderable;

    /**
     * Constructor. If given an id, will fetch the corresponding record from the DB.
     *
     * @param mixed $idorparams A blog entry id if INT, or data for a new entry if array
     */
    public function __construct($id=null, $params=null, $form=null) {
        global $DB, $PAGE, $CFG;

        if (!empty($id)) {
            $object = $DB->get_record('post', array('id' => $id));
            foreach ($object as $var => $val) {
                $this->$var = $val;
            }
        } else if (!empty($params) && (is_array($params) || is_object($params))) {
            foreach ($params as $var => $val) {
                $this->$var = $val;
            }
        }

        if (!empty($CFG->useblogassociations)) {
            $associations = $DB->get_records('blog_association', array('blogid' => $this->id));
            foreach ($associations as $association) {
                $context = context::instance_by_id($association->contextid);
                if ($context->contextlevel == CONTEXT_COURSE) {
                    $this->courseassoc = $association->contextid;
                } else if ($context->contextlevel == CONTEXT_MODULE) {
                    $this->modassoc = $association->contextid;
                }
            }
        }

        $this->form = $form;
    }


    /**
     * Gets the required data to print the entry
     */
    public function prepare_render() {

        global $DB, $CFG, $PAGE;

        $this->renderable = new StdClass();

        $this->renderable->user = $DB->get_record('user', array('id' => $this->userid));

        // Entry comments.
        if (!empty($CFG->usecomments) and $CFG->blogusecomments) {
            require_once($CFG->dirroot . '/comment/lib.php');

            $cmt = new stdClass();
            $cmt->context = context_user::instance($this->userid);
            $cmt->courseid = $PAGE->course->id;
            $cmt->area = 'format_blog';
            $cmt->itemid = $this->id;
            $cmt->showcount = $CFG->blogshowcommentscount;
            $cmt->component = 'blog';
            $this->renderable->comment = new comment($cmt);
        }

        $this->summary = file_rewrite_pluginfile_urls($this->summary, 'pluginfile.php', SYSCONTEXTID, 'blog', 'post', $this->id);

        // External blog link.
        if ($this->uniquehash && $this->content) {
            if ($externalblog = $DB->get_record('blog_external', array('id' => $this->content))) {
                $urlparts = parse_url($externalblog->url);
                $this->renderable->externalblogtext = get_string('retrievedfrom', 'blog') . get_string('labelsep', 'langconfig');
                $this->renderable->externalblogtext .= html_writer::link($urlparts['scheme'] . '://' . $urlparts['host'],
                                                                         $externalblog->name);
            }
        }

        // Retrieve associations.
        $this->renderable->unassociatedentry = false;
        if (!empty($CFG->useblogassociations)) {

            // Adding the entry associations data.
            if ($associations = $associations = $DB->get_records('blog_association', array('blogid' => $this->id))) {

                // Check to see if the entry is unassociated with group/course level access.
                if ($this->publishstate == 'group' || $this->publishstate == 'course') {
                    $this->renderable->unassociatedentry = true;
                }

                foreach ($associations as $key => $assocrec) {

                    if (!$context = context::instance_by_id($assocrec->contextid, IGNORE_MISSING)) {
                        unset($associations[$key]);
                        continue;
                    }

                    // The renderer will need the contextlevel of the association.
                    $associations[$key]->contextlevel = $context->contextlevel;

                    // Course associations.
                    if ($context->contextlevel == CONTEXT_COURSE) {
                        // TODO: performance!!!!
                        $instancename = $DB->get_field('course', 'shortname', array('id' => $context->instanceid));

                        $associations[$key]->url = $assocurl = new moodle_url('/course/view.php',
                                                                              array('id' => $context->instanceid));
                        $associations[$key]->text = $instancename;
                        $associations[$key]->icon = new pix_icon('i/course', $associations[$key]->text);
                    }

                    // Mod associations.
                    if ($context->contextlevel == CONTEXT_MODULE) {

                        // Getting the activity type and the activity instance id.
                        $sql = 'SELECT cm.instance, m.name FROM {course_modules} cm
                                  JOIN {modules} m ON m.id = cm.module
                                 WHERE cm.id = :cmid';
                        $modinfo = $DB->get_record_sql($sql, array('cmid' => $context->instanceid));
                        // TODO: performance!!!!
                        $instancename = $DB->get_field($modinfo->name, 'name', array('id' => $modinfo->instance));

                        $associations[$key]->type = get_string('modulename', $modinfo->name);
                        $associations[$key]->url = new moodle_url('/mod/' . $modinfo->name . '/view.php',
                                                                  array('id' => $context->instanceid));
                        $associations[$key]->text = $instancename;
                        $associations[$key]->icon = new pix_icon('icon', $associations[$key]->text, $modinfo->name);
                    }
                }
            }
            $this->renderable->blogassociations = $associations;
        }

        // Entry attachments.
        $this->renderable->attachments = $this->get_attachments();

        $this->renderable->usercanedit = blog_user_can_edit_entry($this);
    }


    /**
     * Gets the entry attachments list
     * @return array List of blog_entry_attachment instances
     */
    public function get_attachments() {

        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $syscontext = context_system::instance();

        $fs = get_file_storage();
        $files = $fs->get_area_files($syscontext->id, 'blog', 'attachment', $this->id);

        // Adding a blog_entry_attachment for each non-directory file.
        $attachments = array();
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }
            $attachments[] = new blog_entry_attachment($file, $this->id);
        }

        return $attachments;
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

        if (!empty($CFG->useblogassociations)) {
            $this->add_associations();
        }

        core_tag_tag::set_item_tags('core', 'post', $this->id, context_user::instance($this->userid), $this->tags);

        // Trigger an event for the new entry.
        $event = \core\event\blog_entry_created::create(array(
            'objectid'      => $this->id,
            'relateduserid' => $this->userid
        ));
        $event->set_blog_entry($this);
        $event->trigger();
    }

    /**
     * Updates this entry in the database. Access control checks must be done by calling code.
     *
     * @param array       $params            Entry parameters.
     * @param moodleform  $form              Used for attachments.
     * @param array       $summaryoptions    Summary options.
     * @param array       $attachmentoptions Attachment options.
     *
     * @return void
     */
    public function edit($params=array(), $form=null, $summaryoptions=array(), $attachmentoptions=array()) {
        global $CFG, $DB;

        $sitecontext = context_system::instance();
        $entry = $this;

        $this->form = $form;
        foreach ($params as $var => $val) {
            $entry->$var = $val;
        }

        $entry = file_postupdate_standard_editor($entry, 'summary', $summaryoptions, $sitecontext, 'blog', 'post', $entry->id);
        $entry = file_postupdate_standard_filemanager($entry,
                                                      'attachment',
                                                      $attachmentoptions,
                                                      $sitecontext,
                                                      'blog',
                                                      'attachment',
                                                      $entry->id);

        if (!empty($CFG->useblogassociations)) {
            $entry->add_associations();
        }

        $entry->lastmodified = time();

        // Update record.
        $DB->update_record('post', $entry);
        core_tag_tag::set_item_tags('core', 'post', $entry->id, context_user::instance($this->userid), $entry->tags);

        $event = \core\event\blog_entry_updated::create(array(
            'objectid'      => $entry->id,
            'relateduserid' => $entry->userid
        ));
        $event->set_blog_entry($entry);
        $event->trigger();
    }

    /**
     * Deletes this entry from the database. Access control checks must be done by calling code.
     *
     * @return void
     */
    public function delete() {
        global $DB;

        $this->delete_attachments();
        $this->remove_associations();

        // Get record to pass onto the event.
        $record = $DB->get_record('post', array('id' => $this->id));
        $DB->delete_records('post', array('id' => $this->id));
        core_tag_tag::remove_all_item_tags('core', 'post', $this->id);

        $event = \core\event\blog_entry_deleted::create(array(
            'objectid'      => $this->id,
            'relateduserid' => $this->userid
            ));
        $event->add_record_snapshot("post", $record);
        $event->set_blog_entry($this);
        $event->trigger();
    }

    /**
     * Function to add all context associations to an entry.
     *
     * @param string $unused This does nothing, do not use it.
     */
    public function add_associations($unused = null) {

        if ($unused !== null) {
            debugging('Illegal argument used in blog_entry->add_associations()', DEBUG_DEVELOPER);
        }

        $this->remove_associations();

        if (!empty($this->courseassoc)) {
            $this->add_association($this->courseassoc);
        }

        if (!empty($this->modassoc)) {
            $this->add_association($this->modassoc);
        }
    }

    /**
     * Add a single association for a blog entry
     *
     * @param int $contextid - id of context to associate with the blog entry.
     * @param string $unused This does nothing, do not use it.
     */
    public function add_association($contextid, $unused = null) {
        global $DB;

        if ($unused !== null) {
            debugging('Illegal argument used in blog_entry->add_association()', DEBUG_DEVELOPER);
        }

        $assocobject = new StdClass;
        $assocobject->contextid = $contextid;
        $assocobject->blogid = $this->id;
        $id = $DB->insert_record('blog_association', $assocobject);

        // Trigger an association created event.
        $context = context::instance_by_id($contextid);
        $eventparam = array(
            'objectid' => $id,
            'other' => array('associateid' => $context->instanceid, 'subject' => $this->subject, 'blogid' => $this->id),
            'relateduserid' => $this->userid
        );
        if ($context->contextlevel == CONTEXT_COURSE) {
            $eventparam['other']['associatetype'] = 'course';

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $eventparam['other']['associatetype'] = 'coursemodule';
        }
        $event = \core\event\blog_association_created::create($eventparam);
        $event->trigger();
    }

    /**
     * remove all associations for a blog entry
     *
     * @return void
     */
    public function remove_associations() {
        global $DB;

        $associations = $DB->get_records('blog_association', array('blogid' => $this->id));
        foreach ($associations as $association) {

            // Trigger an association deleted event.
            $context = context::instance_by_id($association->contextid);
            $eventparam = array(
                'objectid' => $this->id,
                'other' => array('subject' => $this->subject, 'blogid' => $this->id),
                'relateduserid' => $this->userid
            );
            $event = \core\event\blog_association_deleted::create($eventparam);
            $event->add_record_snapshot('blog_association', $association);
            $event->trigger();

            // Now remove the association.
            $DB->delete_records('blog_association', array('id' => $association->id));
        }
    }

    /**
     * Deletes all the user files in the attachments area for an entry
     *
     * @return void
     */
    public function delete_attachments() {
        $fs = get_file_storage();
        $fs->delete_area_files(SYSCONTEXTID, 'blog', 'attachment', $this->id);
        $fs->delete_area_files(SYSCONTEXTID, 'blog', 'post', $this->id);
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

        $sitecontext = context_system::instance();

        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // Can edit any blog entry.
        }

        if ($this->userid == $userid && has_capability('moodle/blog:create', $sitecontext)) {
            return true; // Can edit own when having blog:create capability.
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
        $sitecontext = context_system::instance();

        if (empty($CFG->enableblogs) || !has_capability('moodle/blog:view', $sitecontext)) {
            return false; // Blog system disabled or user has no blog view capability.
        }

        if (isloggedin() && $USER->id == $targetuserid) {
            return true; // Can view own entries in any case.
        }

        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // Can manage all entries.
        }

        // Coming for 1 entry, make sure it's not a draft.
        if ($this->publishstate == 'draft' && !has_capability('moodle/blog:viewdrafts', $sitecontext)) {
            return false;  // Can not view draft of others.
        }

        // Coming for 1 entry, make sure user is logged in, if not a public blog.
        if ($this->publishstate != 'public' && !isloggedin()) {
            return false;
        }

        switch ($CFG->bloglevel) {
            case BLOG_GLOBAL_LEVEL:
                return true;
                break;

            case BLOG_SITE_LEVEL:
                if (isloggedin()) { // Not logged in viewers forbidden.
                    return true;
                }
                return false;
                break;

            case BLOG_USER_LEVEL:
            default:
                $personalcontext = context_user::instance($targetuserid);
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

        // Everyone gets draft access.
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
    public $entries = null;

    /**
     * Caches the total number of the entries.
     * @var int
     */
    public $totalentries = null;

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
        // Unset filters overridden by more specific filters.
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

        if ($this->entries === null) {
            if ($sqlarray = $this->get_entry_fetch_sql(false, 'created DESC')) {
                $this->entries = $DB->get_records_sql($sqlarray['sql'], $sqlarray['params'], $start, $limit);
                if (!$start && count($this->entries) < $limit) {
                    $this->totalentries = count($this->entries);
                }
            } else {
                return false;
            }
        }

        return $this->entries;
    }

    /**
     * Finds total number of blog entries
     *
     * @return int
     */
    public function count_entries() {
        global $DB;
        if ($this->totalentries === null) {
            if ($sqlarray = $this->get_entry_fetch_sql(true)) {
                $this->totalentries = $DB->count_records_sql($sqlarray['sql'], $sqlarray['params']);
            } else {
                $this->totalentries = 0;
            }
        }
        return $this->totalentries;
    }

    public function get_entry_fetch_sql($count=false, $sort='lastmodified DESC', $userid = false) {
        global $DB, $USER, $CFG;

        if (!$userid) {
            $userid = $USER->id;
        }

        $allnamefields = \user_picture::fields('u', null, 'useridalias');
        // The query used to locate blog entries is complicated.  It will be built from the following components:
        $requiredfields = "p.*, $allnamefields";  // The SELECT clause.
        $tables = array('p' => 'post', 'u' => 'user');   // Components of the FROM clause (table_id => table_name).
        // Components of the WHERE clause (conjunction).
        $conditions = array('u.deleted = 0', 'p.userid = u.id', '(p.module = \'blog\' OR p.module = \'blog_external\')');

        // Build up a clause for permission constraints.

        $params = array();

        // Fix for MDL-9165, use with readuserblogs capability in a user context can read that user's private blogs.
        // Admins can see all blogs regardless of publish states, as described on the help page.
        if (has_capability('moodle/user:readuserblogs', context_system::instance())) {
            // Don't add permission constraints.

        } else if (!empty($this->filters['user'])
                   && has_capability('moodle/user:readuserblogs',
                                     context_user::instance((empty($this->filters['user']->id) ? 0 : $this->filters['user']->id)))) {
            // Don't add permission constraints.

        } else {
            if (isloggedin() and !isguestuser()) {
                // Dont check association records if there aren't any.
                $assocexists = $DB->record_exists('blog_association', array());

                // Begin permission sql clause.
                $permissionsql = '(p.userid = ? ';
                $params[] = $userid;

                if ($CFG->bloglevel >= BLOG_SITE_LEVEL) { // Add permission to view site-level entries.
                    $permissionsql .= " OR p.publishstate = 'site' ";
                }

                if ($CFG->bloglevel >= BLOG_GLOBAL_LEVEL) { // Add permission to view global entries.
                    $permissionsql .= " OR p.publishstate = 'public' ";
                }

                $permissionsql .= ') ';   // Close permissions sql clause.
            } else {  // Default is access to public entries.
                $permissionsql = "p.publishstate = 'public'";
            }
            $conditions[] = $permissionsql;  // Add permission constraints.
        }

        foreach ($this->filters as $type => $blogfilter) {
            $conditions = array_merge($conditions, $blogfilter->conditions);
            $params = array_merge($params, $blogfilter->params);
            $tables = array_merge($tables, $blogfilter->tables);
        }

        $tablessql = '';  // Build up the FROM clause.
        foreach ($tables as $tablename => $table) {
            $tablessql .= ($tablessql ? ', ' : '').'{'.$table.'} '.$tablename;
        }

        $sql = ($count) ? 'SELECT COUNT(*)' : 'SELECT ' . $requiredfields;
        $sql .= " FROM $tablessql WHERE " . implode(' AND ', $conditions);
        $sql .= ($count) ? '' : " ORDER BY $sort";

        return array('sql' => $sql, 'params' => $params);
    }

    /**
     * Outputs all the blog entries aggregated by this blog listing.
     *
     * @return void
     */
    public function print_entries() {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;
        $sitecontext = context_system::instance();

        // Blog renderer.
        $output = $PAGE->get_renderer('blog');

        $page  = optional_param('blogpage', 0, PARAM_INT);
        $limit = optional_param('limit', get_user_preferences('blogpagesize', 10), PARAM_INT);
        $start = $page * $limit;

        $morelink = '<br />&nbsp;&nbsp;';

        $entries = $this->get_entries($start, $limit);
        $totalentries = $this->count_entries();
        $pagingbar = new paging_bar($totalentries, $page, $limit, $this->get_baseurl());
        $pagingbar->pagevar = 'blogpage';
        $blogheaders = blog_get_headers();

        echo $OUTPUT->render($pagingbar);

        if (has_capability('moodle/blog:create', $sitecontext)) {
            // The user's blog is enabled and they are viewing their own blog.
            $userid = optional_param('userid', null, PARAM_INT);

            if (empty($userid) || (!empty($userid) && $userid == $USER->id)) {

                $courseid = optional_param('courseid', null, PARAM_INT);
                $modid = optional_param('modid', null, PARAM_INT);

                $addurl = new moodle_url("$CFG->wwwroot/blog/edit.php");
                $urlparams = array('action' => 'add',
                                   'userid' => $userid,
                                   'courseid' => $courseid,
                                   'groupid' => optional_param('groupid', null, PARAM_INT),
                                   'modid' => $modid,
                                   'tagid' => optional_param('tagid', null, PARAM_INT),
                                   'tag' => optional_param('tag', null, PARAM_INT),
                                   'search' => optional_param('search', null, PARAM_INT));

                $urlparams = array_filter($urlparams);
                $addurl->params($urlparams);

                $addlink = '<div class="addbloglink">';
                $addlink .= '<a href="'.$addurl->out().'">'. $blogheaders['stradd'].'</a>';
                $addlink .= '</div>';
                echo $addlink;
            }
        }

        if ($entries) {
            $count = 0;
            foreach ($entries as $entry) {
                $blogentry = new blog_entry(null, $entry);

                // Get the required blog entry data to render it.
                $blogentry->prepare_render();
                echo $output->render($blogentry);

                $count++;
            }

            echo $OUTPUT->render($pagingbar);

            if (!$count) {
                print '<br /><div style="text-align:center">'. get_string('noentriesyet', 'blog') .'</div><br />';
            }

            print $morelink.'<br />'."\n";
            return;
        }
    }

    // Find the base url from $_GET variables, for print_paging_bar.
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
     * @var array $availabletypes
     */
    public $availabletypes = array();

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
                $classname = "blog_filter_$type";
                if (class_exists($classname)) {
                    return new $classname($id, $type);
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

        $this->availabletypes = array('site' => get_string('site'),
                                      'course' => get_string('course'),
                                      'module' => get_string('activity'),
                                      'context' => get_string('coresystem'));

        switch ($this->type) {
            case 'course': // Careful of site course!
                // Ignore course filter if blog associations are not enabled.
                if ($this->id != $SITE->id && !empty($CFG->useblogassociations)) {
                    $this->overrides = array('site', 'context');
                    $context = context_course::instance($this->id);
                    $this->tables['ba'] = 'blog_association';
                    $this->conditions[] = 'p.id = ba.blogid';
                    $this->conditions[] = 'ba.contextid = '.$context->id;
                    break;
                } else {
                    // We are dealing with the site course, do not break from the current case.
                }

            case 'site':
                // No special constraints.
                break;
            case 'module':
                if (!empty($CFG->useblogassociations)) {
                    $this->overrides = array('course', 'site', 'context');

                    $context = context_module::instance($this->id);
                    $this->tables['ba'] = 'blog_association';
                    $this->tables['p']  = 'post';
                    $this->conditions = array('p.id = ba.blogid', 'ba.contextid = ?');
                    $this->params = array($context->id);
                }
                break;
            case 'context':
                if ($id != context_system::instance()->id && !empty($CFG->useblogassociations)) {
                    $this->overrides = array('site');
                    $context = context::instance_by_id($this->id);
                    $this->tables['ba'] = 'blog_association';
                    $this->tables['ctx'] = 'context';
                    $this->conditions[] = 'p.id = ba.blogid';
                    $this->conditions[] = 'ctx.id = ba.contextid';
                    $this->conditions[] = 'ctx.path LIKE ?';
                    $this->params = array($context->path . '%');
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
        global $CFG, $DB, $USER;
        $this->availabletypes = array('user' => get_string('user'), 'group' => get_string('group'));

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

        } else if ($this->type == 'group') {
            $this->overrides = array('course', 'site');

            $this->tables['gm'] = 'groups_members';
            $this->conditions[] = 'p.userid = gm.userid';
            $this->conditions[] = 'gm.groupid = ?';
            $this->params[]     = $this->id;

            if (!empty($CFG->useblogassociations)) {  // Only show blog entries associated with this course.
                $coursecontext     = context_course::instance($DB->get_field('groups', 'courseid', array('id' => $this->id)));
                $this->tables['ba'] = 'blog_association';
                $this->conditions[] = 'gm.groupid = ?';
                $this->conditions[] = 'ba.contextid = ?';
                $this->conditions[] = 'ba.blogid = p.id';
                $this->params[]     = $this->id;
                $this->params[]     = $coursecontext->id;
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
                                  "ti.component = 'core'",
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
 * This filter restricts the results to a time interval in seconds up to time()
 */
class blog_filter_since extends blog_filter {
    public function __construct($interval) {
        $this->conditions[] = 'p.lastmodified >= ? AND p.lastmodified <= ?';
        $this->params[] = time() - $interval;
        $this->params[] = time();
    }
}

/**
 * Filter used to perform full-text search on an entry's subject, summary and content
 */
class blog_filter_search extends blog_filter {

    public function __construct($searchterm) {
        global $DB;
        $this->conditions = array("(".$DB->sql_like('p.summary', '?', false)." OR
                                    ".$DB->sql_like('p.content', '?', false)." OR
                                    ".$DB->sql_like('p.subject', '?', false).")");
        $this->params[] = "%$searchterm%";
        $this->params[] = "%$searchterm%";
        $this->params[] = "%$searchterm%";
    }
}


/**
 * Renderable class to represent an entry attachment
 */
class blog_entry_attachment implements renderable {

    public $filename;
    public $url;
    public $file;

    /**
     * Gets the file data
     *
     * @param stored_file $file
     * @param int $entryid Attachment entry id
     */
    public function __construct($file, $entryid) {

        global $CFG;

        $this->file = $file;
        $this->filename = $file->get_filename();
        $this->url = file_encode_url($CFG->wwwroot . '/pluginfile.php',
                                     '/' . SYSCONTEXTID . '/blog/attachment/' . $entryid . '/' . $this->filename);
    }

}
