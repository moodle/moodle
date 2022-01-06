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
 * Data provider.
 *
 * @package    mod_wiki
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_wiki\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use context_user;
use context;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Data provider class.
 *
 * @package    mod_wiki
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table('wiki_subwikis', [
            'userid' => 'privacy:metadata:wiki_subwikis:userid',
            'groupid' => 'privacy:metadata:wiki_subwikis:groupid',
        ], 'privacy:metadata:wiki_subwikis');

        $collection->add_database_table('wiki_pages', [
            'userid' => 'privacy:metadata:wiki_pages:userid',
            'title' => 'privacy:metadata:wiki_pages:title',
            'cachedcontent' => 'privacy:metadata:wiki_pages:cachedcontent',
            'timecreated' => 'privacy:metadata:wiki_pages:timecreated',
            'timemodified' => 'privacy:metadata:wiki_pages:timemodified',
            'timerendered' => 'privacy:metadata:wiki_pages:timerendered',
            'pageviews' => 'privacy:metadata:wiki_pages:pageviews',
            'readonly' => 'privacy:metadata:wiki_pages:readonly',
        ], 'privacy:metadata:wiki_pages');

        $collection->add_database_table('wiki_versions', [
            'userid' => 'privacy:metadata:wiki_versions:userid',
            'content' => 'privacy:metadata:wiki_versions:content',
            'contentformat' => 'privacy:metadata:wiki_versions:contentformat',
            'version' => 'privacy:metadata:wiki_versions:version',
            'timecreated' => 'privacy:metadata:wiki_versions:timecreated',
        ], 'privacy:metadata:wiki_versions');

        $collection->add_database_table('wiki_locks', [
            'userid' => 'privacy:metadata:wiki_locks:userid',
            'sectionname' => 'privacy:metadata:wiki_locks:sectionname',
            'lockedat' => 'privacy:metadata:wiki_locks:lockedat',
        ], 'privacy:metadata:wiki_locks');

        $collection->link_subsystem('core_files', 'privacy:metadata:core_files');
        $collection->link_subsystem('core_tag', 'privacy:metadata:core_tag');
        $collection->link_subsystem('core_comment', 'privacy:metadata:core_comment');

        // We do not report on wiki, wiki_synonyms, wiki_links because this is just context-related data.

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        $contextlist->add_from_sql('SELECT ctx.id
            FROM {modules} m
            JOIN {course_modules} cm ON cm.module = m.id AND m.name = :modname
            JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
            JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
            LEFT JOIN {wiki_pages} p ON p.subwikiid = s.id
            LEFT JOIN {wiki_versions} v ON v.pageid = p.id AND v.userid = :userid3
            LEFT JOIN {wiki_locks} l ON l.pageid = p.id AND l.userid = :userid4
            LEFT JOIN {comments} com ON com.itemid = p.id AND com.commentarea = :commentarea
                AND com.contextid = ctx.id AND com.userid = :userid5
            WHERE s.userid = :userid1 OR p.userid = :userid2 OR v.id IS NOT NULL OR l.id IS NOT NULL OR com.id IS NOT NULL',
            ['modname' => 'wiki', 'contextlevel' => CONTEXT_MODULE, 'userid1' => $userid, 'userid2' => $userid,
                'userid3' => $userid, 'userid4' => $userid, 'commentarea' => 'wiki_page', 'userid5' => $userid]);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $params = [
            'modname' => 'wiki',
            'contextlevel' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $sql = "
          SELECT s.userid
            FROM {modules} m
            JOIN {course_modules} cm ON cm.module = m.id AND m.name = :modname
            JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
            JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
            WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "
          SELECT p.userid
            FROM {modules} m
            JOIN {course_modules} cm ON cm.module = m.id AND m.name = :modname
            JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
            JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
            JOIN {wiki_pages} p ON p.subwikiid = s.id
            WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "
          SELECT v.userid
            FROM {modules} m
            JOIN {course_modules} cm ON cm.module = m.id AND m.name = :modname
            JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
            JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
            JOIN {wiki_pages} p ON p.subwikiid = s.id
            JOIN {wiki_versions} v ON v.pageid = p.id
            WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "
          SELECT l.userid
            FROM {modules} m
            JOIN {course_modules} cm ON cm.module = m.id AND m.name = :modname
            JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel
            JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
            JOIN {wiki_pages} p ON p.subwikiid = s.id
            JOIN {wiki_locks} l ON l.pageid = p.id
            WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
        \core_comment\privacy\provider::get_users_in_context_from_sql($userlist, 'com', 'mod_wiki', 'wiki_page', $context->id);
    }

    /**
     * Add one subwiki to the export
     *
     * Each page is added as related data because all pages in one subwiki share the same filearea
     *
     * @param stdClass $user
     * @param context $context
     * @param array $subwiki
     * @param string $wikimode
     */
    protected static function export_subwiki($user, context $context, $subwiki, $wikimode) {
        if (empty($subwiki)) {
            return;
        }
        $subwikiid = key($subwiki);
        $pages = $subwiki[$subwikiid]['pages'];
        unset($subwiki[$subwikiid]['pages']);
        writer::with_context($context)->export_data([$subwikiid], (object)$subwiki[$subwikiid]);
        $allfiles = $wikimode === 'individual'; // Whether to export all files or only the ones that are used.

        $alltexts = ''; // Store all texts that reference files to search which files are used.
        foreach ($pages as $page => $entry) {
            // Preprocess current page contents.
            if (!$allfiles && self::text_has_files($entry['page']['cachedcontent'])) {
                $alltexts .= $entry['page']['cachedcontent'];
            }
            $entry['page']['cachedcontent'] = format_text(writer::with_context($context)
                ->rewrite_pluginfile_urls([$subwikiid], 'mod_wiki', 'attachments',
                    $subwikiid, $entry['page']['cachedcontent']), FORMAT_HTML, ['context' => $context]);
            // Add page tags.
            $pagetags = \core_tag_tag::get_item_tags_array('mod_wiki', 'page', $entry['page']['id']);
            if ($pagetags) {
                $entry['page']['tags'] = $pagetags;
            }

            // Preprocess revisions.
            if (!empty($entry['revisions'])) {
                // For each revision this user has made preprocess the contents.
                foreach ($entry['revisions'] as &$revision) {
                    if ((!$allfiles && self::text_has_files($revision['content']))) {
                        $alltexts .= $revision['content'];
                    }
                    $revision['content'] = writer::with_context($context)
                        ->rewrite_pluginfile_urls([$subwikiid], 'mod_wiki', 'attachments', $subwikiid, $revision['content']);
                }
            }
            $comments = self::get_page_comments($user, $context, $entry['page']['id'], !array_key_exists('userid', $entry['page']));
            if ($comments) {
                $entry['page']['comments'] = $comments;
            }
            writer::with_context($context)->export_related_data([$subwikiid], $page, $entry);
        }

        if ($allfiles) {
            // Export all files.
            writer::with_context($context)->export_area_files([$subwikiid], 'mod_wiki', 'attachments', $subwikiid);
        } else {
            // Analyze which files are used in the texts.
            self::export_used_files($context, $subwikiid, $alltexts);
        }
    }

    /**
     * Retrieves page comments
     *
     * We can not use \core_comment\privacy\provider::export_comments() because it expects each item to have a separate
     * subcontext and we store wiki pages as related data to subwiki because the files are shared between pages.
     *
     * @param stdClass $user
     * @param \context $context
     * @param int $pageid
     * @param bool $onlyforthisuser
     * @return array
     */
    protected static function get_page_comments($user, \context $context, $pageid, $onlyforthisuser = true) {
        global $USER, $DB;
        $params = [
            'contextid' => $context->id,
            'commentarea' => 'wiki_page',
            'itemid' => $pageid
        ];
        $sql = "SELECT c.id, c.content, c.format, c.timecreated, c.userid
                  FROM {comments} c
                 WHERE c.contextid = :contextid AND
                       c.commentarea = :commentarea AND
                       c.itemid = :itemid";
        if ($onlyforthisuser) {
            $sql .= " AND c.userid = :userid";
            $params['userid'] = $USER->id;
        }
        $sql .= " ORDER BY c.timecreated DESC";

        $rs = $DB->get_recordset_sql($sql, $params);
        $comments = [];
        foreach ($rs as $record) {
            if ($record->userid != $user->id) {
                // Clean HTML in comments that were added by other users.
                $comment = ['content' => format_text($record->content, $record->format, ['context' => $context])];
            } else {
                // Export comments made by this user as they are stored.
                $comment = ['content' => $record->content, 'contentformat' => $record->format];
            }
            $comment += [
                'time' => transform::datetime($record->timecreated),
                'userid' => transform::user($record->userid),
            ];
            $comments[] = (object)$comment;
        }
        $rs->close();
        return $comments;
    }

    /**
     * Check if text has embedded files
     *
     * @param string $str
     * @return bool
     */
    protected static function text_has_files($str) {
        return strpos($str, '@@PLUGINFILE@@') !== false;
    }

    /**
     * Analyze which files are used in the texts and export
     * @param context $context
     * @param int $subwikiid
     * @param string $alltexts
     * @return int|void
     */
    protected static function export_used_files($context, $subwikiid, $alltexts) {
        if (!self::text_has_files($alltexts)) {
            return;
        }
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_wiki', 'attachments', $subwikiid,
            'filepath, filename', false);
        if (empty($files)) {
            return;
        }
        usort($files, function($file1, $file2) {
            return strcmp($file2->get_filepath(), $file1->get_filename());
        });
        foreach ($files as $file) {
            $filepath = $file->get_filepath() . $file->get_filename();
            $needles = ['@@PLUGINFILE@@' . s($filepath),
                '@@PLUGINFILE@@' . $filepath,
                '@@PLUGINFILE@@' . str_replace(' ', '%20', $filepath),
                '@@PLUGINFILE@@' . s($filepath),
                '@@PLUGINFILE@@' . s(str_replace(' ', '%20', $filepath))
            ];
            $needles = array_unique($needles);
            $newtext = str_replace($needles, '', $alltexts);
            if ($newtext !== $alltexts) {
                $alltexts = $newtext;
                writer::with_context($context)->export_file([$subwikiid], $file);
                if (!self::text_has_files($alltexts)) {
                    return;
                }
            }
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            $user = $contextlist->get_user();

            $rs = $DB->get_recordset_sql('SELECT w.wikimode, s.id AS subwikiid,
                    s.groupid AS subwikigroupid, s.userid AS subwikiuserid,
                    p.id AS pageid, p.userid AS pageuserid, p.title, p.cachedcontent, p.timecreated AS pagetimecreated,
                    p.timemodified AS pagetimemodified, p.timerendered AS pagetimerendered, p.pageviews, p.readonly,
                    v.id AS versionid, v.content, v.contentformat, v.version, v.timecreated AS versiontimecreated,
                    l.id AS lockid, l.sectionname, l.lockedat
                FROM {course_modules} cm
                JOIN {wiki} w ON w.id = cm.instance
                JOIN {wiki_subwikis} s ON cm.instance = s.wikiid
                LEFT JOIN {wiki_pages} p ON p.subwikiid = s.id
                LEFT JOIN {wiki_versions} v ON v.pageid = p.id AND v.userid = :user4
                LEFT JOIN {wiki_locks} l ON l.pageid = p.id AND l.userid = :user5
                WHERE cm.id = :cmid AND (s.userid = :user1 OR p.userid = :user2 OR v.userid = :user3 OR l.userid = :user6 OR
                     EXISTS (SELECT 1 FROM {comments} com WHERE com.itemid = p.id AND com.commentarea = :commentarea
                          AND com.contextid = :ctxid AND com.userid = :user7)
                )
                ORDER BY s.id, p.id, v.id',
                ['cmid' => $context->instanceid,
                    'user1' => $user->id, 'user2' => $user->id, 'user3' => $user->id, 'user4' => $user->id,
                    'user5' => $user->id, 'user6' => $user->id, 'user7' => $user->id, 'commentarea' => 'wiki_page',
                    'ctxid' => $context->id]);

            if (!$rs->current()) {
                $rs->close();
                continue;
            }

            $subwiki = [];
            $wikimode = null;
            foreach ($rs as $record) {
                if ($wikimode === null) {
                    $wikimode = $record->wikimode;
                }
                if (!isset($subwiki[$record->subwikiid])) {
                    self::export_subwiki($user, $context, $subwiki, $wikimode);
                    $subwiki = [$record->subwikiid => [
                        'groupid' => $record->subwikigroupid,
                        'userid' => $record->subwikiuserid ? transform::user($record->subwikiuserid) : 0,
                        'pages' => []
                    ]];
                }

                if (!$record->pageid) {
                    // This is an empty individual wiki.
                    continue;
                }

                // Prepend page title with the page id to guarantee uniqueness.
                $pagetitle = format_string($record->title, true, ['context' => $context]);
                $page = $record->pageid . ' ' . $pagetitle;
                if (!isset($subwiki[$record->subwikiid]['pages'][$page])) {
                    // Export basic details about the page.
                    $subwiki[$record->subwikiid]['pages'][$page] = ['page' => [
                        'id' => $record->pageid,
                        'title' => $pagetitle,
                        'cachedcontent' => $record->cachedcontent,
                    ]];
                    if ($record->pageuserid == $user->id) {
                        // This page belongs to this user. Export all details.
                        $subwiki[$record->subwikiid]['pages'][$page]['page'] += [
                            'userid' => transform::user($user->id),
                            'timecreated' => transform::datetime($record->pagetimecreated),
                            'timemodified' => transform::datetime($record->pagetimemodified),
                            'timerendered' => transform::datetime($record->pagetimerendered),
                            'pageviews' => $record->pageviews,
                            'readonly' => $record->readonly,
                        ];

                        $subwiki[$record->subwikiid]['pages'][$page]['page']['userid'] = transform::user($user->id);
                    }
                }

                if ($record->versionid) {
                    $subwiki[$record->subwikiid]['pages'][$page]['revisions'][$record->versionid] = [
                        'content' => $record->content,
                        'contentformat' => $record->contentformat,
                        'version' => $record->version,
                        'timecreated' => transform::datetime($record->versiontimecreated)
                    ];
                }

                if ($record->lockid) {
                    $subwiki[$record->subwikiid]['pages'][$page]['locks'][$record->lockid] = [
                        'sectionname' => $record->sectionname,
                        'lockedat' => transform::datetime($record->lockedat),
                    ];
                }

            }
            self::export_subwiki($user, $context, $subwiki, $wikimode);

            if ($subwiki) {
                // Export wiki itself.
                $contextdata = helper::get_context_data($context, $user);
                helper::export_context_files($context, $user);
                writer::with_context($context)->export_data([], $contextdata);
            }

            $rs->close();
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $subwikis = $DB->get_fieldset_sql('SELECT s.id
              FROM {course_modules} cm
              JOIN {modules} m ON m.name = :wiki AND cm.module = m.id
              JOIN {wiki_subwikis} s ON s.wikiid = cm.instance
             WHERE cm.id = :cmid',
            ['cmid' => $context->instanceid, 'wiki' => 'wiki']);
        if (!$subwikis) {
            return;
        }

        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_wiki', 'attachments');

        \core_tag\privacy\provider::delete_item_tags($context, 'mod_wiki', 'page');

        \core_comment\privacy\provider::delete_comments_for_all_users($context, 'mod_wiki', 'wiki_page');

        list($sql, $params) = $DB->get_in_or_equal($subwikis);
        $DB->delete_records_select('wiki_locks', 'pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid '.$sql.')', $params);
        $DB->delete_records_select('wiki_versions', 'pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid '.$sql.')', $params);
        $DB->delete_records_select('wiki_synonyms', 'subwikiid '.$sql, $params);
        $DB->delete_records_select('wiki_links', 'subwikiid '.$sql, $params);
        $DB->delete_records_select('wiki_pages', 'subwikiid '.$sql, $params);
        $DB->delete_records_select('wiki_subwikis', 'id '.$sql, $params);

        $DB->delete_records('tag_instance', ['contextid' => $context->id, 'component' => 'mod_wiki', 'itemtype' => 'page']);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $contextids = $contextlist->get_contextids();

        if (!$contextids) {
            return;
        }

        // Remove only individual subwikis. Contributions to collaborative wikis is not considered personal contents.
        list($ctxsql, $ctxparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $subwikis = $DB->get_records_sql_menu('SELECT s.id, ctx.id AS ctxid
              FROM {context} ctx
              JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextmod
              JOIN {modules} m ON m.name = :wiki AND cm.module = m.id
              JOIN {wiki_subwikis} s ON s.wikiid = cm.instance AND s.userid = :userid
             WHERE ctx.id ' . $ctxsql,
            ['userid' => (int)$contextlist->get_user()->id, 'wiki' => 'wiki', 'contextmod' => CONTEXT_MODULE] + $ctxparams);

        if ($subwikis) {
            // We found individual subwikis that need to be deleted completely.

            $fs = get_file_storage();
            foreach ($subwikis as $subwikiid => $contextid) {
                $fs->delete_area_files($contextid, 'mod_wiki', 'attachments', $subwikiid);
                \core_comment\privacy\provider::delete_comments_for_all_users_select(context::instance_by_id($contextid),
                    'mod_wiki', 'wiki_page', "IN (SELECT id FROM {wiki_pages} WHERE subwikiid=:subwikiid)",
                    ['subwikiid' => $subwikiid]);
            }

            list($sql, $params) = $DB->get_in_or_equal(array_keys($subwikis), SQL_PARAMS_NAMED);

            $DB->execute("DELETE FROM {tag_instance} WHERE component=:component AND itemtype=:itemtype AND itemid IN
                (SELECT id FROM {wiki_pages} WHERE subwikiid $sql)",
                ['component' => 'mod_wiki', 'itemtype' => 'page'] + $params);

            $DB->delete_records_select('wiki_locks', 'pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid ' . $sql . ')',
                $params);
            $DB->delete_records_select('wiki_versions', 'pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid ' . $sql . ')',
                $params);
            $DB->delete_records_select('wiki_synonyms', 'subwikiid ' . $sql, $params);
            $DB->delete_records_select('wiki_links', 'subwikiid ' . $sql, $params);
            $DB->delete_records_select('wiki_pages', 'subwikiid ' . $sql, $params);
            $DB->delete_records_select('wiki_subwikis', 'id ' . $sql, $params);
        }

        // Remove comments made by this user on all other wiki pages.
        \core_comment\privacy\provider::delete_comments_for_user($contextlist, 'mod_wiki', 'wiki_page');
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Remove only individual subwikis. Contributions to collaborative wikis is not considered personal contents.
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = [
            'wiki' => 'wiki',
            'contextmod' => CONTEXT_MODULE,
            'contextid' => $context->id,
        ];

        $params = array_merge($inparams, $params);
        $sql = "SELECT s.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextmod
                  JOIN {modules} m ON m.name = :wiki AND cm.module = m.id
                  JOIN {wiki_subwikis} s ON s.wikiid = cm.instance
                 WHERE ctx.id = :contextid
                   AND s.userid {$insql}";

        $subwikis = $DB->get_fieldset_sql($sql, $params);

        if ($subwikis) {
            // We found individual subwikis that need to be deleted completely.

            $fs = get_file_storage();
            foreach ($subwikis as $subwikiid) {
                $fs->delete_area_files($context->id, 'mod_wiki', 'attachments', $subwikiid);
                \core_comment\privacy\provider::delete_comments_for_all_users_select(context::instance_by_id($context->id),
                    'mod_wiki', 'wiki_page', "IN (SELECT id FROM {wiki_pages} WHERE subwikiid=:subwikiid)",
                    ['subwikiid' => $subwikiid]);
            }

            list($insql, $inparams) = $DB->get_in_or_equal($subwikis, SQL_PARAMS_NAMED);
            $params = ['component' => 'mod_wiki', 'itemtype' => 'page'];
            $params = array_merge($inparams, $params);
            $sql = "DELETE FROM {tag_instance}
                          WHERE component=:component
                            AND itemtype=:itemtype
                            AND itemid IN
                                (SELECT id
                                FROM {wiki_pages}
                                WHERE subwikiid $insql)";

            $DB->execute($sql, $params);

            $DB->delete_records_select('wiki_locks', "pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid {$insql})", $params);
            $DB->delete_records_select('wiki_versions', "pageid IN (SELECT id FROM {wiki_pages} WHERE subwikiid {$insql})",
                    $params);
            $DB->delete_records_select('wiki_synonyms', "subwikiid {$insql}", $params);
            $DB->delete_records_select('wiki_links', "subwikiid {$insql}", $params);
            $DB->delete_records_select('wiki_pages', "subwikiid {$insql}", $params);
            $DB->delete_records_select('wiki_subwikis', "id {$insql}", $params);
        }

        // Remove comments made by this user on all other wiki pages.
        \core_comment\privacy\provider::delete_comments_for_users($userlist, 'mod_wiki', 'wiki_page');
    }
}
