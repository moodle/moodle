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
 * File processor for Ally.
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use backup;

use context_course;

use core\event\base;

use core\event\course_created;
use core\event\course_updated;
use core\event\course_deleted;
use core\event\course_restored;

use core\event\course_module_created;
use core\event\course_module_updated;
use core\event\course_module_deleted;

use core\event\course_section_created;
use core\event\course_section_updated;
use core\event\course_section_deleted;

use core\event\group_created;
use core\event\group_deleted;
use core\event\group_updated;
use mod_forum\event\discussion_created;
use mod_forum\event\discussion_updated;
use mod_forum\event\discussion_deleted;
use mod_forum\event\post_updated;

use mod_hsuforum\event\discussion_created as hsu_discussion_created;
use mod_hsuforum\event\discussion_updated as hsu_discussion_updated;
use mod_hsuforum\event\discussion_deleted as hsu_discussion_deleted;
use mod_hsuforum\event\post_updated as hsu_post_updated;

use mod_glossary\event\entry_created;
use mod_glossary\event\entry_updated;
use mod_glossary\event\entry_deleted;

use mod_book\event\chapter_created;
use mod_book\event\chapter_updated;
use mod_book\event\chapter_deleted;

use mod_lesson\event\page_created;
use mod_lesson\event\page_updated;
use mod_lesson\event\page_deleted;

/**
 * File processor for Ally.
 * Can be used to process individual or groups of files.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class event_handlers {

    const API_RICH_CNT_CREATED = 'rich_content_created';
    const API_RICH_CNT_UPDATED = 'rich_content_updated';
    const API_RICH_CNT_DELETED = 'rich_content_deleted';
    const API_COURSE_UPDATED = 'course_updated';
    const API_COURSE_DELETED = 'course_deleted';
    const API_COURSE_COPIED = 'course_copied';
    const API_COURSE_IMPORTED = 'course_imported';

    /**
     * @param course_created $event
     */
    public static function course_created(course_created $event) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $courseid = $event->courseid;
        $contents = local_content::get_html_content($courseid, 'course', 'course', 'summary', $courseid);
        content_processor::push_content_update($contents, self::API_RICH_CNT_CREATED);
        course_processor::push_course_event(
            self::API_COURSE_UPDATED,
            $event->timecreated,
            $courseid);
    }

    /**
     * @param course_updated $event
     */
    public static function course_updated(course_updated $event) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $courseid = $event->courseid;
        $contents = local_content::get_html_content($courseid, 'course', 'course', 'summary', $courseid);
        files_in_use::set_context_needs_updating($event->get_context());
        content_processor::push_content_update($contents, self::API_RICH_CNT_UPDATED);
        course_processor::push_course_event(
            self::API_COURSE_UPDATED,
            $event->timecreated,
            $courseid);
    }

    /**
     * @param course_deleted $event
     */
    public static function course_deleted(course_deleted $event) {
        $courseid = $event->courseid;
        local_content::queue_delete($courseid, $courseid, 'course', 'course', 'summary');
        course_processor::push_course_event(
            self::API_COURSE_DELETED,
            $event->timecreated,
            $courseid);
        files_in_use::delete_course_records($courseid);
    }

    /**
     * @param course_restored $event
     */
    public static function course_restored(course_restored $event) {
        $destcourseid = $event->courseid;

        $sourcecourseid = $event->other['originalcourseid'] ?? null;
        $mode = $event->other['mode'] ?? null;
        $target = $event->other['target'] ?? null;

        // Specifically catch course copy events.
        if ($mode === backup::MODE_COPY && $target === backup::TARGET_NEW_COURSE && $sourcecourseid) {
            course_processor::push_course_event(
                self::API_COURSE_COPIED,
                $event->timecreated,
                $destcourseid,
                $sourcecourseid);
        }

        // Specifically catch course import events.
        if ($mode === backup::MODE_IMPORT && $sourcecourseid) {
            course_processor::push_course_event(
                self::API_COURSE_IMPORTED,
                $event->timecreated,
                $destcourseid,
                $sourcecourseid);
        }

        // Can intercept more types of restores here if we want.
    }

    /**
     * @param base $event
     * @param string $apieventname
     * @throws \dml_exception
     */
    private static function course_section_crud(base $event, $apieventname) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $sectionid = $event->objectid;
        $courseid = $event->courseid;

        if ($event instanceof course_section_deleted) {
            local_content::queue_delete($courseid, $sectionid, 'course', 'course_sections', 'summary');
            return;
        }
        files_in_use::set_context_needs_updating(context_course::instance($courseid));
        $content = local_content::get_html_content($sectionid, 'course', 'course_sections', 'summary', $courseid);

        content_processor::push_content_update([$content], $apieventname);
    }

    /**
     * @param course_section_created $event
     */
    public static function course_section_created(course_section_created $event) {
        self::course_section_crud($event, self::API_RICH_CNT_CREATED);
    }

    /**
     * @param course_section_updated $event
     * @throws \dml_exception
     */
    public static function course_section_updated(course_section_updated $event) {
        files_in_use::set_context_needs_updating($event->get_context());
        self::course_section_crud($event, self::API_RICH_CNT_UPDATED);
    }

    /**
     * @param course_section_deleted $event
     * @throws \dml_exception
     */
    public static function course_section_deleted(course_section_deleted $event) {
        files_in_use::set_context_needs_updating($event->get_context());
        self::course_section_crud($event, self::API_RICH_CNT_DELETED);
    }

    /**
     * @param group_created $event
     */
    public static function group_created(group_created $event) {
        files_in_use::set_group_needs_updating($event->objectid, $event->contextid);
    }

    /**
     * @param group_updated $event
     */
    public static function group_updated(group_updated $event) {
        files_in_use::set_group_needs_updating($event->objectid, $event->contextid);
    }

    /**
     * @param base $event
     * @param $apieventname
     */
    private static function course_module_crud(base $event, $apieventname) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $module = $event->other['modulename'];
        $id = $event->other['instanceid'];

        if ($apieventname == self::API_RICH_CNT_UPDATED) {
            // We only need to do this on update.
            files_in_use::set_context_needs_updating($event->get_context());
        }

        $contents = local_content::get_all_html_content($id, $module);
        if (empty($contents)) {
            return;
        }
        content_processor::push_content_update($contents, $apieventname);
    }

    /**
     * @param course_module_created $event
     */
    public static function course_module_created(course_module_created $event) {
        self::course_module_crud($event, self::API_RICH_CNT_CREATED);
    }

    /**
     * @param course_module_updated $event
     */
    public static function course_module_updated(course_module_updated $event) {
        self::course_module_crud($event, self::API_RICH_CNT_UPDATED);
    }

    /**
     * @param course_module_deleted $event
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function course_module_deleted(course_module_deleted $event) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $module = $event->other['modulename'];
        $id = $event->other['instanceid'];

        files_in_use::delete_context_records($event->get_context()->id);

        if (!local_content::component_supports_html_content($module)) {
            return;
        }

        $component = local::get_component_instance($module);
        $fields = $component->get_table_fields($module);
        if (empty($fields)) {
            $fields = ['intro'];
        }

        foreach ($fields as $field) {
            local_content::queue_delete($event->courseid, $id, $module, $module, $field);
        }

    }

    /**
     * @param base $event
     * @param string $eventname
     * @param string $forumtype
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private static function forum_discussion_crud(base $event, $eventname, $forumtype = 'forum') {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $module = $forumtype;
        $component = local_content::component_instance($module);
        $userid = $event->userid;
        // Don't go any further if user is not a teacher / manager / admin, etc..
        if (!$component->user_is_approved_author_type($userid, $event->get_context())) {
            return;
        }

        if ($eventname == self::API_RICH_CNT_UPDATED) {
            files_in_use::set_context_needs_updating($event->get_context());
        }

        // Get the forum post id from the discussion without hitting the DB!
        $recordsnapshot = $event->get_record_snapshot($forumtype.'_discussions', $event->objectid);
        $postid = $recordsnapshot->firstpost;

        $table = $forumtype.'_posts';
        if ($event instanceof discussion_deleted || $event instanceof hsu_discussion_deleted) {
            $content = local_content::get_html_content_deleted($postid, $module, $table, 'message', $event->courseid);
        } else {
            $content = local_content::get_html_content($postid, $module, $table, 'message', $event->courseid);
        }
        if (!$content) {
            $ident = local_content::urlident($module, $table, 'message', $postid);
            throw new \moodle_exception('error:componentcontentnotfound', 'tool_ally', '', $ident);
        }
        content_processor::push_content_update([$content], $eventname);
    }

    /**
     * @param discussion_created $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function forum_discussion_created(discussion_created $event, $forumtype = 'forum') {
        self::forum_discussion_crud($event, self::API_RICH_CNT_CREATED, $forumtype);
    }

    /**
     * @param discussion_updated $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function forum_discussion_updated(discussion_updated $event, $forumtype = 'forum') {
        self::forum_discussion_crud($event, self::API_RICH_CNT_UPDATED, $forumtype);
    }

    /**
     * @param discussion_deleted $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function forum_discussion_deleted(discussion_deleted $event, $forumtype = 'forum') {
        self::forum_discussion_crud($event, self::API_RICH_CNT_DELETED, $forumtype);
    }

    /**
     * Note - although we are only interested in discussions, if we alter a discussions message we are in fact altering
     * the corersponding post.
     * @param post_updated $event
     * @param string $forumtype
     */
    public static function forum_post_updated(base $event, $forumtype = 'forum') {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $module = $forumtype;
        $component = local_content::component_instance($module);
        $userid = $event->userid;
        // Don't go any further if user is not a teacher / manager / admin, etc..
        if (!$component->user_is_approved_author_type($userid, $event->get_context())) {
            return;
        }
        $discussionid = $event->other['discussionid'];
        $postid = $event->objectid;
        $table = $forumtype.'_posts';
        files_in_use::set_context_needs_updating($event->get_context());

        $recordsnapshot = $event->get_record_snapshot($forumtype.'_discussions', $discussionid);
        if (intval($recordsnapshot->firstpost) === intval($postid)) {
            // This is a discussion post, let's go!
            $content = local_content::get_html_content($postid, $module, $table, 'message', $event->courseid);
            if (!$content) {
                $ident = local_content::urlident($module, $table, 'message', $postid);
                throw new \moodle_exception('error:componentcontentnotfound', 'tool_ally', '', $ident);
            }
            content_processor::push_content_update([$content], self::API_RICH_CNT_UPDATED);
        }
    }

    /**
     * @param discussion_created $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function hsuforum_discussion_created(hsu_discussion_created $event) {
        self::forum_discussion_crud($event, self::API_RICH_CNT_CREATED, 'hsuforum');
    }

    /**
     * @param discussion_updated $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function hsuforum_discussion_updated(hsu_discussion_updated $event) {
        self::forum_discussion_crud($event, self::API_RICH_CNT_UPDATED, 'hsuforum');
    }

    /**
     * @param discussion_deleted $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function hsuforum_discussion_deleted(hsu_discussion_deleted $event) {
        self::forum_discussion_crud($event, self::API_RICH_CNT_DELETED, 'hsuforum');
    }

    /**
     * Note - although we are only interested in discussions, if we alter a discussions message we are in fact altering
     * the corersponding post.
     * @param post_updated $event
     */
    public static function hsuforum_post_updated(hsu_post_updated $event) {
        self::forum_post_updated($event, 'hsuforum');
    }


    /**
     * General method for dealing with crud for sub tables of modules - e.g. glossary entries.
     * NOTE: forum is too complicated to use here.
     * @param base $event
     * @param string $eventname
     * @param string $contentfield
     * @param null|string $table
     * @param null|int $id
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private static function module_item_crud(base $event, $eventname, $contentfield, $table = null, $id = null) {
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);
        $module = local::clean_component_string($event->component);
        $component = local_content::component_instance($module);
        $userid = $event->userid;
        // Don't go any further if user is not a teacher / manager / admin, etc..
        if (!$component->user_is_approved_author_type($userid, $event->get_context())) {
            return;
        }

        if ($eventname !== self::API_RICH_CNT_CREATED) {
            files_in_use::set_context_needs_updating($event->get_context());
        }

        if ($table === null) {
            $table = $event->objecttable;
        }
        if ($id === null) {
            $id = $event->objectid;
        }

        if ($eventname === self::API_RICH_CNT_DELETED) {
            $content = local_content::get_html_content_deleted($id, $module, $table, $contentfield, $event->courseid);
        } else {
            $content = local_content::get_html_content($id, $module, $table, $contentfield, $event->courseid);
        }
        if (!$content) {
            $ident = local_content::urlident($module, $table, $contentfield, $event->courseid);
            throw new \moodle_exception('error:componentcontentnotfound', 'tool_ally', '', $ident);
        }

        content_processor::push_content_update([$content], $eventname);
    }

    /**
     * @param entry_created $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function glossary_entry_created(entry_created $event) {
        self::module_item_crud($event, self::API_RICH_CNT_CREATED, 'definition');
    }

    /**
     * @param entry_updated $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function glossary_entry_updated(entry_updated $event) {
        self::module_item_crud($event, self::API_RICH_CNT_UPDATED, 'definition');
    }

    /**
     * @param entry_deleted $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function glossary_entry_deleted(entry_deleted $event) {
        self::module_item_crud($event, self::API_RICH_CNT_DELETED, 'definition');
    }

    /**
     * @param chapter_created $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function book_chapter_created(chapter_created $event) {
        self::module_item_crud($event, self::API_RICH_CNT_CREATED, 'content');
    }

    /**
     * @param chapter_updated $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function book_chapter_updated(chapter_updated $event) {
        self::module_item_crud($event, self::API_RICH_CNT_UPDATED, 'content');
    }

    /**
     * @param chapter_deleted $event
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function book_chapter_deleted(chapter_deleted $event) {
        self::module_item_crud($event, self::API_RICH_CNT_DELETED, 'content');
    }

    /**
     * @param base $event
     * @param $eventname
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private static function lesson_page_crud(base $event, $eventname) {
        global $DB;
        \cache::make('tool_ally', 'annotationmaps')->delete($event->courseid);

        self::module_item_crud($event, $eventname, 'contents');
        // Get answers for page.
        $rs = $DB->get_records('lesson_answers', ['pageid' => $event->objectid]);

        foreach ($rs as $row) {
            if (!empty($row->answer) && ($row->answerformat === FORMAT_HTML)) {
                self::module_item_crud($event, $eventname, 'answer', 'lesson_answers', $row->id);
            }
            if (!empty($row->response) && ($row->responseformat === FORMAT_HTML)) {
                self::module_item_crud($event, $eventname, 'response', 'lesson_answers', $row->id);
            }
        }
    }

    /**
     * @param page_created $event
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function lesson_page_created(page_created $event) {
        self::lesson_page_crud($event, self::API_RICH_CNT_CREATED);
    }

    /**
     * @param page_updated $event
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function lesson_page_updated(page_updated $event) {
        self::lesson_page_crud($event, self::API_RICH_CNT_UPDATED);
    }

    /**
     * @param page_deleted $event
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function lesson_page_deleted(page_deleted $event) {
        self::lesson_page_crud($event, self::API_RICH_CNT_DELETED);
    }
}
