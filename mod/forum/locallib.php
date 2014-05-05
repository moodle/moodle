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
 * Library of functions for forum outside of the core api
 */

require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once($CFG->libdir . '/portfolio/caller.php');

/**
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum_portfolio_caller extends portfolio_module_caller_base {

    protected $postid;
    protected $discussionid;
    protected $attachment;

    private $post;
    private $forum;
    private $discussion;
    private $posts;
    private $keyedfiles; // just using multifiles isn't enough if we're exporting a full thread

    /**
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'postid'       => false,
            'discussionid' => false,
            'attachment'   => false,
        );
    }
    /**
     * @param array $callbackargs
     */
    function __construct($callbackargs) {
        parent::__construct($callbackargs);
        if (!$this->postid && !$this->discussionid) {
            throw new portfolio_caller_exception('mustprovidediscussionorpost', 'forum');
        }
    }
    /**
     * @global object
     */
    public function load_data() {
        global $DB;

        if ($this->postid) {
            if (!$this->post = $DB->get_record('forum_posts', array('id' => $this->postid))) {
                throw new portfolio_caller_exception('invalidpostid', 'forum');
            }
        }

        $dparams = array();
        if ($this->discussionid) {
            $dbparams = array('id' => $this->discussionid);
        } else if ($this->post) {
            $dbparams = array('id' => $this->post->discussion);
        } else {
            throw new portfolio_caller_exception('mustprovidediscussionorpost', 'forum');
        }

        if (!$this->discussion = $DB->get_record('forum_discussions', $dbparams)) {
            throw new portfolio_caller_exception('invaliddiscussionid', 'forum');
        }

        if (!$this->forum = $DB->get_record('forum', array('id' => $this->discussion->forum))) {
            throw new portfolio_caller_exception('invalidforumid', 'forum');
        }

        if (!$this->cm = get_coursemodule_from_instance('forum', $this->forum->id)) {
            throw new portfolio_caller_exception('invalidcoursemodule');
        }

        $this->modcontext = context_module::instance($this->cm->id);
        $fs = get_file_storage();
        if ($this->post) {
            if ($this->attachment) {
                $this->set_file_and_format_data($this->attachment);
            } else {
                $attach = $fs->get_area_files($this->modcontext->id, 'mod_forum', 'attachment', $this->post->id, 'timemodified', false);
                $embed  = $fs->get_area_files($this->modcontext->id, 'mod_forum', 'post', $this->post->id, 'timemodified', false);
                $files = array_merge($attach, $embed);
                $this->set_file_and_format_data($files);
            }
            if (!empty($this->multifiles)) {
                $this->keyedfiles[$this->post->id] = $this->multifiles;
            } else if (!empty($this->singlefile)) {
                $this->keyedfiles[$this->post->id] = array($this->singlefile);
            }
        } else { // whole thread
            $fs = get_file_storage();
            $this->posts = forum_get_all_discussion_posts($this->discussion->id, 'p.created ASC');
            $this->multifiles = array();
            foreach ($this->posts as $post) {
                $attach = $fs->get_area_files($this->modcontext->id, 'mod_forum', 'attachment', $post->id, 'timemodified', false);
                $embed  = $fs->get_area_files($this->modcontext->id, 'mod_forum', 'post', $post->id, 'timemodified', false);
                $files = array_merge($attach, $embed);
                if ($files) {
                    $this->keyedfiles[$post->id] = $files;
                } else {
                    continue;
                }
                $this->multifiles = array_merge($this->multifiles, array_values($this->keyedfiles[$post->id]));
            }
        }
        if (empty($this->multifiles) && !empty($this->singlefile)) {
            $this->multifiles = array($this->singlefile); // copy_files workaround
        }
        // depending on whether there are files or not, we might have to change richhtml/plainhtml
        if (empty($this->attachment)) {
            if (!empty($this->multifiles)) {
                $this->add_format(PORTFOLIO_FORMAT_RICHHTML);
            } else {
                $this->add_format(PORTFOLIO_FORMAT_PLAINHTML);
            }
        }
    }

    /**
     * @global object
     * @return string
     */
    function get_return_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $this->discussion->id;
    }
    /**
     * @global object
     * @return array
     */
    function get_navigation() {
        global $CFG;

        $navlinks = array();
        $navlinks[] = array(
            'name' => format_string($this->discussion->name),
            'link' => $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $this->discussion->id,
            'type' => 'title'
        );
        return array($navlinks, $this->cm);
    }
    /**
     * either a whole discussion
     * a single post, with or without attachment
     * or just an attachment with no post
     *
     * @global object
     * @global object
     * @uses PORTFOLIO_FORMAT_RICH
     * @return mixed
     */
    function prepare_package() {
        global $CFG;

        // set up the leap2a writer if we need it
        $writingleap = false;
        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $this->exporter->get('format')->leap2a_writer();
            $writingleap = true;
        }
        if ($this->attachment) { // simplest case first - single file attachment
            $this->copy_files(array($this->singlefile), $this->attachment);
            if ($writingleap) { // if we're writing leap, make the manifest to go along with the file
                $entry = new portfolio_format_leap2a_file($this->singlefile->get_filename(), $this->singlefile);
                $leapwriter->add_entry($entry);
                return $this->exporter->write_new_file($leapwriter->to_xml(), $this->exporter->get('format')->manifest_name(), true);
            }

        } else if (empty($this->post)) {  // exporting whole discussion
            $content = ''; // if we're just writing HTML, start a string to add each post to
            $ids = array(); // if we're writing leap2a, keep track of all entryids so we can add a selection element
            foreach ($this->posts as $post) {
                $posthtml =  $this->prepare_post($post);
                if ($writingleap) {
                    $ids[] = $this->prepare_post_leap2a($leapwriter, $post, $posthtml);
                } else {
                    $content .= $posthtml . '<br /><br />';
                }
            }
            $this->copy_files($this->multifiles);
            $name = 'discussion.html';
            $manifest = ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH);
            if ($writingleap) {
                // add on an extra 'selection' entry
                $selection = new portfolio_format_leap2a_entry('forumdiscussion' . $this->discussionid,
                    get_string('discussion', 'forum') . ': ' . $this->discussion->name, 'selection');
                $leapwriter->add_entry($selection);
                $leapwriter->make_selection($selection, $ids, 'Grouping');
                $content = $leapwriter->to_xml();
                $name = $this->get('exporter')->get('format')->manifest_name();
            }
            $this->get('exporter')->write_new_file($content, $name, $manifest);

        } else { // exporting a single post
            $posthtml = $this->prepare_post($this->post);

            $content = $posthtml;
            $name = 'post.html';
            $manifest = ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH);

            if ($writingleap) {
                $this->prepare_post_leap2a($leapwriter, $this->post, $posthtml);
                $content = $leapwriter->to_xml();
                $name = $this->exporter->get('format')->manifest_name();
            }
            $this->copy_files($this->multifiles);
            $this->get('exporter')->write_new_file($content, $name, $manifest);
        }
    }

    /**
     * helper function to add a leap2a entry element
     * that corresponds to a single forum post,
     * including any attachments
     *
     * the entry/ies are added directly to the leapwriter, which is passed by ref
     *
     * @param portfolio_format_leap2a_writer $leapwriter writer object to add entries to
     * @param object $post                               the stdclass object representing the database record
     * @param string $posthtml                           the content of the post (prepared by {@link prepare_post}
     *
     * @return int id of new entry
     */
    private function prepare_post_leap2a(portfolio_format_leap2a_writer $leapwriter, $post, $posthtml) {
        $entry = new portfolio_format_leap2a_entry('forumpost' . $post->id,  $post->subject, 'resource', $posthtml);
        $entry->published = $post->created;
        $entry->updated = $post->modified;
        $entry->author = $post->author;
        if (is_array($this->keyedfiles) && array_key_exists($post->id, $this->keyedfiles) && is_array($this->keyedfiles[$post->id])) {
            $leapwriter->link_files($entry, $this->keyedfiles[$post->id], 'forumpost' . $post->id . 'attachment');
        }
        $entry->add_category('web', 'resource_type');
        $leapwriter->add_entry($entry);
        return $entry->id;
    }

    /**
     * @param array $files
     * @param mixed $justone false of id of single file to copy
     * @return bool|void
     */
    private function copy_files($files, $justone=false) {
        if (empty($files)) {
            return;
        }
        foreach ($files as $f) {
            if ($justone && $f->get_id() != $justone) {
                continue;
            }
            $this->get('exporter')->copy_existing_file($f);
            if ($justone && $f->get_id() == $justone) {
                return true; // all we need to do
            }
        }
    }
    /**
     * this is a very cut down version of what is in forum_make_mail_post
     *
     * @global object
     * @param int $post
     * @return string
     */
    private function prepare_post($post, $fileoutputextras=null) {
        global $DB;
        static $users;
        if (empty($users)) {
            $users = array($this->user->id => $this->user);
        }
        if (!array_key_exists($post->userid, $users)) {
            $users[$post->userid] = $DB->get_record('user', array('id' => $post->userid));
        }
        // add the user object on to the post so we can pass it to the leap writer if necessary
        $post->author = $users[$post->userid];
        $viewfullnames = true;
        // format the post body
        $options = portfolio_format_text_options();
        $format = $this->get('exporter')->get('format');
        $formattedtext = format_text($post->message, $post->messageformat, $options, $this->get('course')->id);
        $formattedtext = portfolio_rewrite_pluginfile_urls($formattedtext, $this->modcontext->id, 'mod_forum', 'post', $post->id, $format);

        $output = '<table border="0" cellpadding="3" cellspacing="0" class="forumpost">';

        $output .= '<tr class="header"><td>';// can't print picture.
        $output .= '</td>';

        if ($post->parent) {
            $output .= '<td class="topic">';
        } else {
            $output .= '<td class="topic starter">';
        }
        $output .= '<div class="subject">'.format_string($post->subject).'</div>';

        $fullname = fullname($users[$post->userid], $viewfullnames);
        $by = new stdClass();
        $by->name = $fullname;
        $by->date = userdate($post->modified, '', $this->user->timezone);
        $output .= '<div class="author">'.get_string('bynameondate', 'forum', $by).'</div>';

        $output .= '</td></tr>';

        $output .= '<tr><td class="left side" valign="top">';

        $output .= '</td><td class="content">';

        $output .= $formattedtext;

        if (is_array($this->keyedfiles) && array_key_exists($post->id, $this->keyedfiles) && is_array($this->keyedfiles[$post->id]) && count($this->keyedfiles[$post->id]) > 0) {
            $output .= '<div class="attachments">';
            $output .= '<br /><b>' .  get_string('attachments', 'forum') . '</b>:<br /><br />';
            foreach ($this->keyedfiles[$post->id] as $file) {
                $output .= $format->file_output($file)  . '<br/ >';
            }
            $output .= "</div>";
        }

        $output .= '</td></tr></table>'."\n\n";

        return $output;
    }
    /**
     * @return string
     */
    function get_sha1() {
        $filesha = '';
        try {
            $filesha = $this->get_sha1_file();
        } catch (portfolio_caller_exception $e) { } // no files

        if ($this->post) {
            return sha1($filesha . ',' . $this->post->subject . ',' . $this->post->message);
        } else {
            $sha1s = array($filesha);
            foreach ($this->posts as $post) {
                $sha1s[] = sha1($post->subject . ',' . $post->message);
            }
            return sha1(implode(',', $sha1s));
        }
    }

    function expected_time() {
        $filetime = $this->expected_time_file();
        if ($this->posts) {
            $posttime = portfolio_expected_time_db(count($this->posts));
            if ($filetime < $posttime) {
                return $posttime;
            }
        }
        return $filetime;
    }
    /**
     * @uses CONTEXT_MODULE
     * @return bool
     */
    function check_permissions() {
        $context = context_module::instance($this->cm->id);
        if ($this->post) {
            return (has_capability('mod/forum:exportpost', $context)
                || ($this->post->userid == $this->user->id
                    && has_capability('mod/forum:exportownpost', $context)));
        }
        return has_capability('mod/forum:exportdiscussion', $context);
    }
    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'forum');
    }

    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_LEAP2A);
    }
}


/**
 * Class representing the virtual node with all itemids in the file browser
 *
 * @category  files
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum_file_info_container extends file_info {
    /** @var file_browser */
    protected $browser;
    /** @var stdClass */
    protected $course;
    /** @var stdClass */
    protected $cm;
    /** @var string */
    protected $component;
    /** @var stdClass */
    protected $context;
    /** @var array */
    protected $areas;
    /** @var string */
    protected $filearea;

    /**
     * Constructor (in case you did not realize it ;-)
     *
     * @param file_browser $browser
     * @param stdClass $course
     * @param stdClass $cm
     * @param stdClass $context
     * @param array $areas
     * @param string $filearea
     */
    public function __construct($browser, $course, $cm, $context, $areas, $filearea) {
        parent::__construct($browser, $context);
        $this->browser = $browser;
        $this->course = $course;
        $this->cm = $cm;
        $this->component = 'mod_forum';
        $this->context = $context;
        $this->areas = $areas;
        $this->filearea = $filearea;
    }

    /**
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array(
            'contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea,
            'itemid' => null,
            'filepath' => null,
            'filename' => null,
        );
    }

    /**
     * Can new files or directories be added via the file browser
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Should this node be considered as a folder in the file browser
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns localised visible name of this node
     *
     * @return string
     */
    public function get_visible_name() {
        return $this->areas[$this->filearea];
    }

    /**
     * Returns list of children nodes
     *
     * @return array of file_info instances
     */
    public function get_children() {
        return $this->get_filtered_children('*', false, true);
    }
    /**
     * Help function to return files matching extensions or their count
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param bool|int $countonly if false returns the children, if an int returns just the
     *    count of children but stops counting when $countonly number of children is reached
     * @param bool $returnemptyfolders if true returns items that don't have matching files inside
     * @return array|int array of file_info instances or the count
     */
    private function get_filtered_children($extensions = '*', $countonly = false, $returnemptyfolders = false) {
        global $DB;
        $params = array('contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea);
        $sql = 'SELECT DISTINCT itemid
                    FROM {files}
                    WHERE contextid = :contextid
                    AND component = :component
                    AND filearea = :filearea';
        if (!$returnemptyfolders) {
            $sql .= ' AND filename <> :emptyfilename';
            $params['emptyfilename'] = '.';
        }
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $sql .= ' '.$sql2;
        $params = array_merge($params, $params2);
        if ($countonly !== false) {
            $sql .= ' ORDER BY itemid DESC';
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $children = array();
        foreach ($rs as $record) {
            if (($child = $this->browser->get_file_info($this->context, 'mod_forum', $this->filearea, $record->itemid))
                    && ($returnemptyfolders || $child->count_non_empty_children($extensions))) {
                $children[] = $child;
            }
            if ($countonly !== false && count($children) >= $countonly) {
                break;
            }
        }
        $rs->close();
        if ($countonly !== false) {
            return count($children);
        }
        return $children;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        return $this->get_filtered_children($extensions, false);
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
