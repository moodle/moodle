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
 * Discussion list renderer.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\renderers;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\factories\legacy_data_mapper as legacy_data_mapper_factory;
use mod_forum\local\factories\exporter as exporter_factory;
use mod_forum\local\factories\vault as vault_factory;
use mod_forum\local\factories\url as url_factory;
use mod_forum\local\managers\capability as capability_manager;
use mod_forum\local\vaults\discussion_list as discussion_list_vault;
use renderer_base;
use stdClass;
use core\output\notification;

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * The discussion list renderer.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_list {
    /** @var forum_entity The forum being rendered */
    private $forum;

    /** @var stdClass The DB record for the forum being rendered */
    private $forumrecord;

    /** @var renderer_base The renderer used to render the view */
    private $renderer;

    /** @var legacy_data_mapper_factory $legacydatamapperfactory Legacy data mapper factory */
    private $legacydatamapperfactory;

    /** @var exporter_factory $exporterfactory Exporter factory */
    private $exporterfactory;

    /** @var vault_factory $vaultfactory Vault factory */
    private $vaultfactory;

    /** @var capability_manager $capabilitymanager Capability manager */
    private $capabilitymanager;

    /** @var url_factory $urlfactory URL factory */
    private $urlfactory;

    /** @var array $notifications List of notification HTML */
    private $notifications;

    /**
     * Constructor for a new discussion list renderer.
     *
     * @param   forum_entity        $forum The forum entity to be rendered
     * @param   renderer_base       $renderer The renderer used to render the view
     * @param   legacy_data_mapper_factory $legacydatamapperfactory The factory used to fetch a legacy record
     * @param   exporter_factory    $exporterfactory The factory used to fetch exporter instances
     * @param   vault_factory       $vaultfactory The factory used to fetch the vault instances
     * @param   capability_manager  $capabilitymanager The managed used to check capabilities on the forum
     * @param   url_factory         $urlfactory The factory used to create URLs in the forum
     * @param   notification[]      $notifications A list of any notifications to be displayed within the page
     */
    public function __construct(
        forum_entity $forum,
        renderer_base $renderer,
        legacy_data_mapper_factory $legacydatamapperfactory,
        exporter_factory $exporterfactory,
        vault_factory $vaultfactory,
        capability_manager $capabilitymanager,
        url_factory $urlfactory,
        array $notifications = []
    ) {
        $this->forum = $forum;
        $this->renderer = $renderer;
        $this->legacydatamapperfactory = $legacydatamapperfactory;
        $this->exporterfactory = $exporterfactory;
        $this->vaultfactory = $vaultfactory;
        $this->capabilitymanager = $capabilitymanager;
        $this->urlfactory = $urlfactory;
        $this->notifications = $notifications;

        $forumdatamapper = $this->legacydatamapperfactory->get_forum_data_mapper();
        $this->forumrecord = $forumdatamapper->to_legacy_object($forum);
    }

    /**
     * Render for the specified user.
     *
     * @param   stdClass    $user The user to render for
     * @param   cm_info     $cm The course module info for this discussion list
     * @param   int         $groupid The group to render
     * @param   int         $sortorder The sort order to use when selecting the discussions in the list
     * @param   int         $pageno The zero-indexed page number to use
     * @param   int         $pagesize The number of discussions to show on the page
     * @return  string      The rendered content for display
     */
    public function render(stdClass $user, \cm_info $cm, ?int $groupid, ?int $sortorder, ?int $pageno, ?int $pagesize) : string {
        $capabilitymanager = $this->capabilitymanager;
        $forum = $this->forum;

        $pagesize = $this->get_page_size($pagesize);
        $pageno = $this->get_page_number($pageno);

        $groupids = $this->get_groups_from_groupid($user, $groupid);
        $forumexporter = $this->exporterfactory->get_forum_exporter(
            $user,
            $this->forum,
            $groupid
        );

        $forumview = array_merge(
                [
                    'notifications' => $this->get_notifications($user, $groupid),
                    'forum' => (array) $forumexporter->export($this->renderer),
                    'groupchangemenu' => groups_print_activity_menu(
                        $cm,
                        $this->urlfactory->get_forum_view_url_from_forum($forum),
                        true
                    ),
                ],
                (array) $this->get_exported_discussions($user, $groupids, $sortorder, $pageno, $pagesize)
            );

        return $this->renderer->render_from_template($this->get_template(), $forumview);
    }

    /**
     * Get the list of groups to show based on the current user and requested groupid.
     *
     * @param   stdClass    $user The user viewing
     * @param   int         $groupid The groupid requested
     * @return  array       The list of groups to show
     */
    private function get_groups_from_groupid(stdClass $user, ?int $groupid) : ?array {
        $forum = $this->forum;
        $effectivegroupmode = $forum->get_effective_group_mode();
        if (empty($effectivegroupmode)) {
            // This forum is not in a group mode. Show all posts always.
            return null;
        }

        if (null == $groupid) {
            // No group was specified.
            $showallgroups = (VISIBLEGROUPS == $effectivegroupmode);
            $showallgroups = $showallgroups || $this->capabilitymanager->can_access_all_groups($user);
            if ($showallgroups) {
                // Return null to show all groups.
                return null;
            } else {
                // No group was specified. Only show the users current groups.
                return array_keys(
                    groups_get_all_groups(
                        $forum->get_course_id(),
                        $user->id,
                        $forum->get_course_module_record()->groupingid
                    )
                );
            }
        } else {
            // A group was specified. Just show that group.
            return [$groupid];
        }
    }

    /**
     * Fetch the data used to display the discussions on the current page.
     *
     * @param   stdClass    $user The user to render for
     * @param   int[]|null  $groupids The group ids for this list of discussions
     * @param   int|null    $sortorder The sort order to use when selecting the discussions in the list
     * @param   int|null    $pageno The zero-indexed page number to use
     * @param   int|null    $pagesize The number of discussions to show on the page
     * @return  stdClass    The data to use for display
     */
    private function get_exported_discussions(stdClass $user, ?array $groupids, ?int $sortorder, ?int $pageno, ?int $pagesize) {
        $forum = $this->forum;
        $discussionvault = $this->vaultfactory->get_discussions_in_forum_vault();
        if (null === $groupids) {
            $discussions = $discussionvault->get_from_forum_id(
                $forum->get_id(),
                $this->capabilitymanager->can_view_hidden_posts($user),
                $user->id,
                $sortorder,
                $this->get_page_size($pagesize),
                $this->get_page_number($pageno));
        } else {
            $discussions = $discussionvault->get_from_forum_id_and_group_id(
                $forum->get_id(),
                $groupids,
                $this->capabilitymanager->can_view_hidden_posts($user),
                $user->id,
                $sortorder,
                $this->get_page_size($pagesize),
                $this->get_page_number($pageno));
        }

        $discussionids = array_keys($discussions);

        $discussioncount = count($discussionids);
        if ($discussioncount >= $pagesize) {
            if (null === $groupids) {
                $discussioncount = $discussionvault->get_total_discussion_count_from_forum_id(
                    $forum->get_id(),
                    $this->capabilitymanager->can_view_hidden_posts($user),
                    $user->id);
            } else {
                $discussioncount = $discussionvault->get_total_discussion_count_from_forum_id_and_group_id(
                    $forum->get_id(),
                    $groupids,
                    $this->capabilitymanager->can_view_hidden_posts($user),
                    $user->id);
            }
        }

        $pagedcontent = new \core\external\paged_content_exporter(
            $pagesize,
            $pageno,
            $discussioncount,
            function ($pageno, $pagelimit) : \moodle_url {
                return $this->urlfactory->get_forum_view_url_from_forum($this->forum, $pageno);
            }
        );

        $postvault = $this->vaultfactory->get_post_vault();
        $posts = $postvault->get_from_discussion_ids($discussionids);
        $groupsbyid = $this->get_groups_available_in_forum();
        $groupsbyauthorid = $this->get_author_groups_from_posts($posts);

        $replycounts = $postvault->get_reply_count_for_discussion_ids($discussionids);
        $latestposts = $postvault->get_latest_post_id_for_discussion_ids($discussionids);

        $unreadcounts = [];
        if (forum_tp_can_track_forums($this->forumrecord)) {
            $unreadcounts = $postvault->get_unread_count_for_discussion_ids($user, $discussionids);
        }

        $summaryexporter = $this->exporterfactory->get_discussion_summaries_exporter(
            $user,
            $forum,
            $discussions,
            $groupsbyid,
            $groupsbyauthorid,
            $replycounts,
            $unreadcounts,
            $latestposts
        );

        return $summaryexporter->export($this->renderer);
    }

    /**
     * Fetch the page size to use when displaying the page.
     *
     * @param   int         $pagesize The number of discussions to show on the page
     * @return  int         The normalised page size
     */
    private function get_page_size(?int $pagesize) : int {
        if (null === $pagesize || $pagesize <= 0) {
            $pagesize = discussion_list_vault::PAGESIZE_DEFAULT;
        }

        return $pagesize;
    }

    /**
     * Fetch the current page number (zero-indexed).
     *
     * @param   int         $pageno The zero-indexed page number to use
     * @return  int         The normalised page number
     */
    private function get_page_number(?int $pageno) : int {
        if (null === $pageno || $pageno < 0) {
            $pageno = 0;
        }

        return $pageno;
    }

    /**
     * Fetch the name of the template to use for the current forum and view modes.
     *
     * @return  string
     */
    private function get_template() : string {
        switch ($this->forum->get_type()) {
            case 'news':
                return 'mod_forum/news_discussion_list';
                break;
            case 'blog':
                return 'mod_forum/blog_discussion_list';
                break;
            case 'qanda':
                return 'mod_forum/qanda_discussion_list';
                break;
            default:
                return 'mod_forum/discussion_list';
        }
    }

    /**
     * Get the groups details for all groups available to the forum.
     *
     * @return  stdClass[]
     */
    private function get_groups_available_in_forum() : array {
        $course = $this->forum->get_course_record();
        $coursemodule = $this->forum->get_course_module_record();

        return groups_get_all_groups($course->id, 0, $coursemodule->groupingid);
    }

    /**
     * Get the author's groups for a list of posts.
     *
     * @param post_entity[] $posts The list of posts
     * @return array Author groups indexed by author id
     */
    private function get_author_groups_from_posts(array $posts) : array {
        $course = $this->forum->get_course_record();
        $coursemodule = $this->forum->get_course_module_record();
        $authorids = array_reduce($posts, function($carry, $post) {
            $carry[$post->get_author_id()] = true;
            return $carry;
        }, []);
        $authorgroups = groups_get_all_groups(
            $course->id,
            array_keys($authorids),
            $coursemodule->groupingid,
            'g.*, gm.id, gm.groupid, gm.userid'
        );

        $authorgroups = array_reduce($authorgroups, function($carry, $group) {
            // Clean up data returned from groups_get_all_groups.
            $userid = $group->userid;
            $groupid = $group->groupid;

            unset($group->userid);
            unset($group->groupid);
            $group->id = $groupid;

            if (!isset($carry[$userid])) {
                $carry[$userid] = [$group];
            } else {
                $carry[$userid][] = $group;
            }

            return $carry;
        }, []);

        foreach (array_diff(array_keys($authorids), array_keys($authorgroups)) as $authorid) {
            $authorgroups[$authorid] = [];
        }

        return $authorgroups;
    }

    /**
     * Get the list of notification for display.
     *
     * @param stdClass $user The viewing user
     * @param int|null $groupid The forum's group id
     * @return      array
     */
    private function get_notifications(stdClass $user, ?int $groupid) : array {
        $notifications = $this->notifications;
        $forum = $this->forum;
        $renderer = $this->renderer;
        $capabilitymanager = $this->capabilitymanager;

        if ($forum->has_blocking_enabled()) {
            $notifications[] = (new notification(
                get_string('thisforumisthrottled', 'forum', [
                    'blockafter' => $forum->get_block_after(),
                    'blockperiod' => get_string('secondstotime' . $forum->get_block_period())
                ])
            ))->set_show_closebutton();
        }

        if ($forum->is_in_group_mode()) {
            if (
                ($groupid === null && !$capabilitymanager->can_access_all_groups($user)) ||
                ($groupid !== null && !$capabilitymanager->can_access_group($user, $groupid))
            ) {
                // Cannot post to the current group.
                $notifications[] = (new notification(
                    get_string('cannotadddiscussion', 'mod_forum'),
                    \core\output\notification::NOTIFY_WARNING
                ))->set_show_closebutton();
            }
        }

        if ('qanda' === $forum->get_type() && !$capabilitymanager->can_manage_forum($user)) {
            $notifications[] = (new notification(
                get_string('qandanotify', 'forum'),
                notification::NOTIFY_INFO
            ))->set_show_closebutton();
        }

        if ('eachuser' === $forum->get_type()) {
            $notifications[] = (new notification(
                get_string('allowsdiscussions', 'forum'),
                notification::NOTIFY_INFO)
            )->set_show_closebutton();
        }

        return array_map(function($notification) {
            return $notification->export_for_template($this->renderer);
        }, $notifications);
    }
}
