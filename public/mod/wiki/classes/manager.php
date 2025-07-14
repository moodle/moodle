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

namespace mod_wiki;

use cm_info;
use context_module;
use stdClass;

/**
 * Class manager for wiki activity
 *
 * @package    mod_wiki
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** Module name. */
    public const MODULE = 'wiki';

    /** The plugin name. */
    public const PLUGINNAME = 'mod_wiki';

    /** @var context_module the current context. */
    private context_module $context;

    /** @var stdClass $course record. */
    private stdClass $course;

    /**
     * @var int $groupmode as defined in SEPARATEGROUPS, VISIBLEGROUPS, or NOGROUPS.
     */
    private int $groupmode;

    /**
     * Class constructor.
     *
     * @param cm_info $cm course module info object
     * @param stdClass $instance activity instance object.
     * @param \moodle_database $db the database instance.
     */
    public function __construct(
        /** @var cm_info $cm course_modules record. */
        private cm_info $cm,
        /** @var stdClass $instance course_module record. */
        private stdClass $instance,
        /** @var \moodle_database $db the database instance. */
        private readonly \moodle_database $db
    ) {
        $this->context = context_module::instance($cm->id);
        $this->course = $cm->get_course();
        $this->groupmode = groups_get_activity_groupmode($cm, $this->course);
    }

    /**
     * Create a manager instance from an instance record.
     *
     * @param stdClass $instance an activity record
     * @return manager
     */
    public static function create_from_instance(stdClass $instance): self {
        $cm = get_coursemodule_from_instance(self::MODULE, $instance->id);
        // Ensure that $this->cm is a cm_info object.
        $cm = cm_info::create($cm);
        $db = \core\di::get(\moodle_database::class);
        return new self($cm, $instance, $db);
    }

    /**
     * Create a manager instance from a course_modules record.
     *
     * @param stdClass|cm_info $cm an activity record
     * @return manager
     */
    public static function create_from_coursemodule(stdClass|cm_info $cm): self {
        // Ensure that $this->cm is a cm_info object.
        $cm = cm_info::create($cm);
        $db = \core\di::get(\moodle_database::class);
        $instance = $db->get_record(self::MODULE, ['id' => $cm->instance], '*', MUST_EXIST);
        return new self($cm, $instance, $db);
    }

    /**
     * Return the current context.
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->context;
    }

    /**
     * Return the current instance.
     *
     * @return stdClass the instance record
     */
    public function get_instance(): stdClass {
        return $this->instance;
    }

    /**
     * Return the current cm_info.
     *
     * @return cm_info the course module
     */
    public function get_coursemodule(): cm_info {
        return $this->cm;
    }

    /**
     * Return the current entries count for this wiki module, that the provided user.
     *
     * @param int $userid the current user id (for grouping purposes)
     * @return int the number of entries
     */
    public function get_all_entries_count(int $userid): int {
        $where = ' WHERE wsp.wikiid = :wikiid ';
        $params = ['wikiid' => $this->instance->id];
        $groupmemberjoin = '';
        // Individual wikis acts like a personal notebook, so we only count the pages of the current user.
        // However, for teachers, or in visible groups, the user also sees pages from other users.
        if (
            $this->get_wiki_mode() == wiki_mode::INDIVIDUAL
            && !has_capability('mod/wiki:managewiki', $this->context, $userid)
            && $this->cm->groupmode != VISIBLEGROUPS
        ) {
            $where .= 'AND wp.userid = :authoruserid';
            $params['authoruserid'] = $userid;
        } else {
            [
                'join' => $groupmemberjoin,
                'params' => $params,
                'where' => $where,
            ] = $this->get_group_member_join($userid, $this->instance->id);
        }

        return $this->db->count_records_sql(
            'SELECT COUNT(*) FROM {wiki_pages} wp
                    LEFT JOIN {wiki_subwikis} wsp ON wsp.id=wp.subwikiid'
            . $groupmemberjoin . $where,
            $params
        );
    }

    /**
     * Get the SQL join for group members based on the provided user's group.
     *
     * @param int $userid the current user id
     * @param int $wikiid the wiki id
     * @return array an array containing the SQL join string and parameters
     */
    private function get_group_member_join(int $userid, int $wikiid): array {
        $where = ' WHERE wsp.wikiid = :wikiid';
        $params = ['wikiid' => $wikiid];
        if (
            $this->groupmode == SEPARATEGROUPS
            && !has_capability('moodle/site:accessallgroups', $this->context, $userid)
        ) {
            $groups = groups_get_all_groups($this->course->id, $userid, 0, 'g.id');
            if (empty($groups)) {
                // No groups found for this user, return empty join but we show only records belonging to this user.
                $where .= ' AND wp.userid = :userid';
                $params['userid'] = $userid;
                return [
                    'join' => '',
                    'params' => $params,
                    'where' => $where,
                ];
            }
            // If not we will check both group from the subwiki and wiki pages user's.
            $groupids = array_column($groups, 'id');
            [$groupmembersql, $groupmemberparams] = groups_get_members_ids_sql($groupids, $this->context);
            $params = array_merge($params, $groupmemberparams);
            $groupmemberjoin = " JOIN ({$groupmembersql}) jg ON jg.id = wp.userid";
            [$wheregroup, $paramgroup] = $this->db->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'groupid');
            $where .= ' AND wsp.groupid ' . $wheregroup;
            $params = array_merge($params, $paramgroup);
        } else {
            $groupmemberjoin = '';
        }
        return ['join' => $groupmemberjoin, 'params' => $params, 'where' => $where];
    }

    /**
     * Return the number of entries for a given user.
     *
     * @param int $userid the user id. We will ignore subwikis and groups.
     * @return int the number of entries
     */
    public function get_user_entries_count(int $userid): int {
        $where = ' WHERE wsp.wikiid = :wikiid AND wp.userid = :userid';
        $params = [
            'wikiid' => $this->instance->id,
            'userid' => $userid,
        ];
        return $this->db->count_records_sql(
            'SELECT COUNT(*) FROM {wiki_pages} wp
                    LEFT JOIN {wiki_subwikis} wsp ON wsp.id=wp.subwikiid' . $where,
            $params
        );
    }

    /**
     * Get Wiki mode (Individual or Collaborative)
     *
     * @return wiki_mode the wiki current mode
     */
    public function get_wiki_mode(): wiki_mode {
        return wiki_mode::tryFrom($this->instance->wikimode) ?? wiki_mode::UNDEFINED;
    }

    /**
     * Get the main wiki page id for the current user, group and wiki.
     * This follow the routine in the view.php file taking info from the course module
     * id and the current group..
     *
     * @return int|null the wiki page id or null if not found
     */
    public function get_main_wiki_pageid(): ?int {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/mod/wiki/locallib.php');

        if (!$wiki = wiki_get_wiki($this->cm->instance)) {
            return null;
        }
        $currentgroup = groups_get_activity_group($this->cm);

        if (wiki_mode::tryFrom($wiki->wikimode) === wiki_mode::INDIVIDUAL) {
            $userid = $USER->id;
        } else {
            $userid = 0;
        }

        // Getting subwiki. If it does not exists, return null.
        if (!$subwiki = wiki_get_subwiki_by_group($wiki->id, $currentgroup, $userid)) {
            return null;
        }

        // Getting first page of the wiki.
        if (!$page = wiki_get_first_page($subwiki->id)) {
            return null;
        }
        return $page->id;
    }
}
