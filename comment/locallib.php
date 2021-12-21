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
 * Functions and classes for comments management
 *
 * @package   core
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * comment_manager is helper class to manage moodle comments in admin page (Reports->Comments)
 *
 * @package   core
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment_manager {

    /** @var int The number of comments to display per page */
    private $perpage;

    /**
     * Constructs the comment_manage object
     */
    public function __construct() {
        global $CFG;
        $this->perpage = $CFG->commentsperpage;
    }

    /**
     * Return comments by pages
     *
     * @global moodle_database $DB
     * @param int $page
     * @return array An array of comments
     */
    function get_comments($page) {
        global $DB;

        if ($page == 0) {
            $start = 0;
        } else {
            $start = $page * $this->perpage;
        }
        $comments = array();

        $userfieldsapi = \core_user\fields::for_name();
        $usernamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $sql = "SELECT c.id, c.contextid, c.itemid, c.component, c.commentarea, c.userid, c.content, $usernamefields, c.timecreated
                  FROM {comments} c
                  JOIN {user} u
                       ON u.id=c.userid
              ORDER BY c.timecreated ASC";
        $rs = $DB->get_recordset_sql($sql, null, $start, $this->perpage);
        $formatoptions = array('overflowdiv' => true, 'blanktarget' => true);
        foreach ($rs as $item) {
            // Set calculated fields
            $item->fullname = fullname($item);
            $item->time = userdate($item->timecreated);
            $item->content = format_text($item->content, FORMAT_MOODLE, $formatoptions);
            // Unset fields not related to the comment
            foreach (\core_user\fields::get_name_fields() as $namefield) {
                unset($item->$namefield);
            }
            unset($item->timecreated);
            // Record the comment
            $comments[] = $item;
        }
        $rs->close();

        return $comments;
    }

    /**
     * Records the course object
     *
     * @global moodle_page $PAGE
     * @global moodle_database $DB
     * @param int $courseid
     */
    private function setup_course($courseid) {
        global $PAGE, $DB;
        if (!empty($this->course) && $this->course->id == $courseid) {
            // already set, stop
            return;
        }
        if ($courseid == $PAGE->course->id) {
            $this->course = $PAGE->course;
        } else if (!$this->course = $DB->get_record('course', array('id' => $courseid))) {
            $this->course = null;
        }
    }

    /**
     * Sets up the module or block information for a comment
     *
     * @global moodle_database $DB
     * @param stdClass $comment
     * @return bool
     */
    private function setup_plugin($comment) {
        global $DB;
        $this->context = context::instance_by_id($comment->contextid, IGNORE_MISSING);
        if (!$this->context) {
            return false;
        }
        switch ($this->context->contextlevel) {
            case CONTEXT_BLOCK:
                if ($block = $DB->get_record('block_instances', array('id' => $this->context->instanceid))) {
                    $this->plugintype = 'block';
                    $this->pluginname = $block->blockname;
                } else {
                    return false;
                }
                break;
            case CONTEXT_MODULE:
                $this->plugintype = 'mod';
                $this->cm = get_coursemodule_from_id('', $this->context->instanceid);
                $this->setup_course($this->cm->course);
                $this->modinfo = get_fast_modinfo($this->course);
                $this->pluginname = $this->modinfo->cms[$this->cm->id]->modname;
                break;
        }
        return true;
    }

    /**
     * Print comments
     * @param int $page
     * @return bool return false if no comments available
     */
    public function print_comments($page = 0) {
        global $OUTPUT, $CFG, $OUTPUT, $DB;

        $count = $DB->count_records('comments');
        $comments = $this->get_comments($page);
        if (count($comments) == 0) {
            echo $OUTPUT->notification(get_string('nocomments', 'moodle'));
            return false;
        }

        $table = new html_table();
        $table->head = array (
            html_writer::checkbox('selectall', '', false, get_string('selectall'), array('id' => 'comment_select_all',
                'class' => 'mr-1')),
            get_string('author', 'search'),
            get_string('content'),
            get_string('action')
        );
        $table->colclasses = array ('leftalign', 'leftalign', 'leftalign', 'leftalign');
        $table->attributes = array('class'=>'admintable generaltable');
        $table->id = 'commentstable';
        $table->data = array();

        $link = new moodle_url('/comment/index.php', array('action' => 'delete', 'sesskey' => sesskey()));
        foreach ($comments as $c) {
            $userdata = html_writer::link(new moodle_url('/user/profile.php', ['id' => $c->userid]), $c->fullname);
            $this->setup_plugin($c);
            if (!empty($this->plugintype)) {
                $context_url = plugin_callback($this->plugintype, $this->pluginname, 'comment', 'url', array($c));
            }
            $checkbox = html_writer::checkbox('comments', $c->id, false);
            $action = html_writer::link(new moodle_url($link, array('commentid' => $c->id)), get_string('delete'));
            if (!empty($context_url)) {
                $action .= html_writer::empty_tag('br');
                $action .= html_writer::link($context_url, get_string('commentincontext'), array('target'=>'_blank'));
            }
            $table->data[] = array($checkbox, $userdata, $c->content, $action);
        }
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($count, $page, $this->perpage, $CFG->wwwroot.'/comment/index.php');
        return true;
    }

    /**
     * Delete a comment
     *
     * @param int $commentid
     * @return bool
     */
    public function delete_comment($commentid) {
        global $DB;
        if ($DB->record_exists('comments', array('id' => $commentid))) {
            $DB->delete_records('comments', array('id' => $commentid));
            return true;
        }
        return false;
    }
    /**
     * Delete comments
     *
     * @param string $list A list of comment ids separated by hyphens
     * @return bool
     */
    public function delete_comments($list) {
        global $DB;
        $ids = explode('-', $list);
        foreach ($ids as $id) {
            $id = (int)$id;
            if ($DB->record_exists('comments', array('id' => $id))) {
                $DB->delete_records('comments', array('id' => $id));
            }
        }
        return true;
    }

    /**
     * Get comments created since a given time.
     *
     * @param  stdClass $course    course object
     * @param  stdClass $context   context object
     * @param  string $component   component name
     * @param  int $since          the time to check
     * @param  stdClass $cm        course module object
     * @return array list of comments db records since the given timelimit
     * @since Moodle 3.2
     */
    public function get_component_comments_since($course, $context, $component, $since, $cm = null) {
        global $DB;

        $commentssince = array();
        $where = 'contextid = ? AND component = ? AND timecreated > ?';
        $comments = $DB->get_records_select('comments', $where, array($context->id, $component, $since));
        // Check item by item if we have permissions.
        $managersviewstatus = array();
        foreach ($comments as $comment) {
            // Check if the manager for the item is cached.
            if (!isset($managersviewstatus[$comment->commentarea]) or
                    !isset($managersviewstatus[$comment->commentarea][$comment->itemid])) {

                $args = new stdClass;
                $args->area      = $comment->commentarea;
                $args->itemid    = $comment->itemid;
                $args->context   = $context;
                $args->course    = $course;
                $args->client_id = 0;
                $args->component = $component;
                if (!empty($cm)) {
                    $args->cm = $cm;
                }

                $manager = new comment($args);
                $managersviewstatus[$comment->commentarea][$comment->itemid] = $manager->can_view();
            }

            if ($managersviewstatus[$comment->commentarea][$comment->itemid]) {
                $commentssince[$comment->id] = $comment;
            }
        }
        return $commentssince;
    }
}
