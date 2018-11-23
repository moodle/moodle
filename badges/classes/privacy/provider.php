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
 * @package    core_badges
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\privacy;

defined('MOODLE_INTERNAL') || die();

use badge;
use context;
use context_course;
use context_helper;
use context_system;
use context_user;
use core_text;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

require_once($CFG->libdir . '/badgeslib.php');

/**
 * Data provider class.
 *
 * @package    core_badges
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\subsystem\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_database_table('badge', [
            'usercreated' => 'privacy:metadata:badge:usercreated',
            'usermodified' => 'privacy:metadata:badge:usermodified',
            'timecreated' => 'privacy:metadata:badge:timecreated',
            'timemodified' => 'privacy:metadata:badge:timemodified',
        ], 'privacy:metadata:badge');

        $collection->add_database_table('badge_issued', [
            'userid' => 'privacy:metadata:issued:userid',
            'dateissued' => 'privacy:metadata:issued:dateissued',
            'dateexpire' => 'privacy:metadata:issued:dateexpire',
        ], 'privacy:metadata:issued');

        $collection->add_database_table('badge_criteria_met', [
            'userid' => 'privacy:metadata:criteriamet:userid',
            'datemet' => 'privacy:metadata:criteriamet:datemet',
        ], 'privacy:metadata:criteriamet');

        $collection->add_database_table('badge_manual_award', [
            'recipientid' => 'privacy:metadata:manualaward:recipientid',
            'issuerid' => 'privacy:metadata:manualaward:issuerid',
            'issuerrole' => 'privacy:metadata:manualaward:issuerrole',
            'datemet' => 'privacy:metadata:manualaward:datemet',
        ], 'privacy:metadata:manualaward');

        $collection->add_database_table('badge_backpack', [
            'userid' => 'privacy:metadata:backpack:userid',
            'email' => 'privacy:metadata:backpack:email',
            'backpackurl' => 'privacy:metadata:backpack:backpackurl',
            'backpackuid' => 'privacy:metadata:backpack:backpackuid',
            // The columns autosync and password are not used.
        ], 'privacy:metadata:backpack');

        $collection->add_external_location_link('backpacks', [
            'name' => 'privacy:metadata:external:backpacks:badge',
            'description' => 'privacy:metadata:external:backpacks:description',
            'image' => 'privacy:metadata:external:backpacks:image',
            'url' => 'privacy:metadata:external:backpacks:url',
            'issuer' => 'privacy:metadata:external:backpacks:issuer',
        ], 'privacy:metadata:external:backpacks');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        // Find the modifications we made on badges (course & system).
        $sql = "
            SELECT ctx.id
              FROM {badge} b
              JOIN {context} ctx
                ON (b.type = :typecourse AND b.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel)
                OR (b.type = :typesite AND ctx.id = :syscontextid)
             WHERE b.usermodified = :userid1
                OR b.usercreated = :userid2";
        $params = [
            'courselevel' => CONTEXT_COURSE,
            'syscontextid' => SYSCONTEXTID,
            'typecourse' => BADGE_TYPE_COURSE,
            'typesite' => BADGE_TYPE_SITE,
            'userid1' => $userid,
            'userid2' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Find where we've manually awarded a badge (recipient user context).
        $sql = "
            SELECT ctx.id
              FROM {badge_manual_award} bma
              JOIN {context} ctx
                ON ctx.instanceid = bma.recipientid
               AND ctx.contextlevel = :userlevel
             WHERE bma.issuerid = :userid";
        $params = [
            'userlevel' => CONTEXT_USER,
            'userid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        // Now find where there is real user data (user context).
        $sql = "
            SELECT ctx.id
              FROM {context} ctx
         LEFT JOIN {badge_manual_award} bma
                ON bma.recipientid = ctx.instanceid
         LEFT JOIN {badge_issued} bi
                ON bi.userid = ctx.instanceid
         LEFT JOIN {badge_criteria_met} bcm
                ON bcm.userid = ctx.instanceid
         LEFT JOIN {badge_backpack} bb
                ON bb.userid = ctx.instanceid
             WHERE ctx.contextlevel = :userlevel
               AND ctx.instanceid = :userid
               AND (bma.id IS NOT NULL
                OR bi.id IS NOT NULL
                OR bcm.id IS NOT NULL
                OR bb.id IS NOT NULL)";
        $params = [
            'userlevel' => CONTEXT_USER,
            'userid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        $allowedcontexts = [
            CONTEXT_COURSE,
            CONTEXT_SYSTEM,
            CONTEXT_USER
        ];

        if (!in_array($context->contextlevel, $allowedcontexts)) {
            return;
        }

        if ($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_SYSTEM) {
            // Find the modifications we made on badges (course & system).
            $params = [
                'courselevel' => CONTEXT_COURSE,
                'syscontextid' => SYSCONTEXTID,
                'typecourse' => BADGE_TYPE_COURSE,
                'typesite' => BADGE_TYPE_SITE,
                'contextid' => $context->id,
            ];

            $sql = "SELECT b.usermodified, b.usercreated
                      FROM {badge} b
                      JOIN {context} ctx
                           ON (b.type = :typecourse AND b.courseid = ctx.instanceid AND ctx.contextlevel = :courselevel)
                           OR (b.type = :typesite AND ctx.id = :syscontextid)
                     WHERE ctx.id = :contextid";

            $userlist->add_from_sql('usermodified', $sql, $params);
            $userlist->add_from_sql('usercreated', $sql, $params);
        }

        if ($context->contextlevel == CONTEXT_USER) {
            // Find where we've manually awarded a badge (recipient user context).
            $params = [
                'instanceid' => $context->instanceid
            ];

            $sql = "SELECT issuerid, recipientid
                      FROM {badge_manual_award}
                     WHERE recipientid = :instanceid";

            $userlist->add_from_sql('issuerid', $sql, $params);
            $userlist->add_from_sql('recipientid', $sql, $params);

            $sql = "SELECT userid
                      FROM {badge_issued}
                     WHERE userid = :instanceid";

            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT userid
                      FROM {badge_criteria_met}
                     WHERE userid = :instanceid";

            $userlist->add_from_sql('userid', $sql, $params);

            $sql = "SELECT userid
                      FROM {badge_backpack}
                     WHERE userid = :instanceid";

            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            $level = $context->contextlevel;
            if ($level == CONTEXT_USER || $level == CONTEXT_COURSE) {
                $carry[$level][] = $context->instanceid;
            } else if ($level == CONTEXT_SYSTEM) {
                $carry[$level] = SYSCONTEXTID;
            }
            return $carry;
        }, [
            CONTEXT_COURSE => [],
            CONTEXT_USER => [],
            CONTEXT_SYSTEM => null,
        ]);

        $path = [get_string('badges', 'core_badges')];
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');

        // Export the badges we've created or modified.
        if (!empty($contexts[CONTEXT_SYSTEM]) || !empty($contexts[CONTEXT_COURSE])) {
            $sqls = [];
            $params = [];

            if (!empty($contexts[CONTEXT_SYSTEM])) {
                $sqls[] = "b.type = :typesite";
                $params['typesite'] = BADGE_TYPE_SITE;
            }

            if (!empty($contexts[CONTEXT_COURSE])) {
                list($insql, $inparams) = $DB->get_in_or_equal($contexts[CONTEXT_COURSE], SQL_PARAMS_NAMED);
                $sqls[] = "(b.type = :typecourse AND b.courseid $insql)";
                $params = array_merge($params, ['typecourse' => BADGE_TYPE_COURSE], $inparams);
            }

            $sqlwhere = '(' . implode(' OR ', $sqls) . ')';
            $sql = "
                SELECT b.*, COALESCE(b.courseid, 0) AS normalisedcourseid
                  FROM {badge} b
                 WHERE (b.usermodified = :userid1 OR b.usercreated = :userid2)
                   AND $sqlwhere
              ORDER BY b.courseid, b.id";
            $params = array_merge($params, ['userid1' => $userid, 'userid2' => $userid]);
            $recordset = $DB->get_recordset_sql($sql, $params);
            static::recordset_loop_and_export($recordset, 'normalisedcourseid', [], function($carry, $record) use ($userid) {
                $carry[] = [
                    'name' => $record->name,
                    'created_on' => transform::datetime($record->timecreated),
                    'created_by_you' => transform::yesno($record->usercreated == $userid),
                    'modified_on' => transform::datetime($record->timemodified),
                    'modified_by_you' => transform::yesno($record->usermodified == $userid),
                ];
                return $carry;
            }, function($courseid, $data) use ($path) {
                $context = $courseid ? context_course::instance($courseid) : context_system::instance();
                writer::with_context($context)->export_data($path, (object) ['badges' => $data]);
            });
        }

        // Export the badges we've manually awarded.
        if (!empty($contexts[CONTEXT_USER])) {
            list($insql, $inparams) = $DB->get_in_or_equal($contexts[CONTEXT_USER], SQL_PARAMS_NAMED);
            $sql = "
                SELECT bma.id, bma.recipientid, bma.datemet, b.name, b.courseid,
                       r.id AS roleid,
                       r.name AS rolename,
                       r.shortname AS roleshortname,
                       r.archetype AS rolearchetype,
                       $ctxfields
                  FROM {badge_manual_award} bma
                  JOIN {badge} b
                    ON b.id = bma.badgeid
                  JOIN {role} r
                    ON r.id = bma.issuerrole
                  JOIN {context} ctx
                    ON (COALESCE(b.courseid, 0) > 0 AND ctx.instanceid = b.courseid AND ctx.contextlevel = :courselevel)
                    OR (COALESCE(b.courseid, 0) = 0 AND ctx.id = :syscontextid)
                 WHERE bma.recipientid $insql
                   AND bma.issuerid = :userid
              ORDER BY bma.recipientid, bma.id";
            $params = array_merge($inparams, [
                'courselevel' => CONTEXT_COURSE,
                'syscontextid' => SYSCONTEXTID,
                'userid' => $userid
            ]);
            $recordset = $DB->get_recordset_sql($sql, $params);
            static::recordset_loop_and_export($recordset, 'recipientid', [], function($carry, $record) use ($userid) {

                // The only reason we fetch the context and role is to format the name of the role, which could be
                // different to the standard name if the badge was created in a course.
                context_helper::preload_from_record($record);
                $context = $record->courseid ? context_course::instance($record->courseid) : context_system::instance();
                $role = (object) [
                    'id' => $record->roleid,
                    'name' => $record->rolename,
                    'shortname' => $record->roleshortname,
                    'archetype' => $record->rolearchetype,
                    // Mock those two fields as they do not matter.
                    'sortorder' => 0,
                    'description' => ''
                ];

                $carry[] = [
                    'name' => $record->name,
                    'issued_by_you' => transform::yesno(true),
                    'issued_on' => transform::datetime($record->datemet),
                    'issuer_role' => role_get_name($role, $context),
                ];
                return $carry;
            }, function($userid, $data) use ($path) {
                $context = context_user::instance($userid);
                writer::with_context($context)->export_related_data($path, 'manual_awards', (object) ['badges' => $data]);
            });
        }

        // Export our data.
        if (in_array($userid, $contexts[CONTEXT_USER])) {

            // Export the badges.
            $uniqueid = $DB->sql_concat_join("'-'", ['b.id', 'COALESCE(bc.id, 0)', 'COALESCE(bi.id, 0)',
                'COALESCE(bma.id, 0)', 'COALESCE(bcm.id, 0)']);
            $sql = "
                SELECT $uniqueid AS uniqueid, b.id,
                       bi.id AS biid, bi.dateissued, bi.dateexpire, bi.uniquehash,
                       bma.id AS bmaid, bma.datemet, bma.issuerid,
                       bcm.id AS bcmid,
                       c.fullname AS coursename,
                       $ctxfields
                  FROM {badge} b
             LEFT JOIN {badge_issued} bi
                    ON bi.badgeid = b.id
                   AND bi.userid = :userid1
             LEFT JOIN {badge_manual_award} bma
                    ON bma.badgeid = b.id
                   AND bma.recipientid = :userid2
             LEFT JOIN {badge_criteria} bc
                    ON bc.badgeid = b.id
             LEFT JOIN {badge_criteria_met} bcm
                    ON bcm.critid = bc.id
                   AND bcm.userid = :userid3
             LEFT JOIN {course} c
                    ON c.id = b.courseid
                   AND b.type = :typecourse
             LEFT JOIN {context} ctx
                    ON ctx.instanceid = c.id
                   AND ctx.contextlevel = :courselevel
                 WHERE bi.id IS NOT NULL
                    OR bma.id IS NOT NULL
                    OR bcm.id IS NOT NULL
              ORDER BY b.id";
            $params = [
                'userid1' => $userid,
                'userid2' => $userid,
                'userid3' => $userid,
                'courselevel' => CONTEXT_COURSE,
                'typecourse' => BADGE_TYPE_COURSE,
            ];
            $recordset = $DB->get_recordset_sql($sql, $params);
            static::recordset_loop_and_export($recordset, 'id', null, function($carry, $record) use ($userid) {
                $badge = new badge($record->id);

                // Export details of the badge.
                if ($carry === null) {
                    $carry = [
                        'name' => $badge->name,
                        'issued' => null,
                        'manual_award' => null,
                        'criteria_met' => []
                    ];

                    if ($badge->type == BADGE_TYPE_COURSE) {
                        context_helper::preload_from_record($record);
                        $carry['course'] = format_string($record->coursename, true, ['context' => $badge->get_context()]);
                    }

                    if (!empty($record->biid)) {
                        $carry['issued'] = [
                            'issued_on' => transform::datetime($record->dateissued),
                            'expires_on' => $record->dateexpire ? transform::datetime($record->dateexpire) : null,
                            'unique_hash' => $record->uniquehash,
                        ];
                    }

                    if (!empty($record->bmaid)) {
                        $carry['manual_award'] = [
                            'awarded_on' => transform::datetime($record->datemet),
                            'issuer' => transform::user($record->issuerid)
                        ];
                    }
                }

                // Export the details of the criteria met.
                // We only do that once, when we find that a least one criteria was met.
                // This is heavily based on the logic present in core_badges_renderer::render_issued_badge.
                if (!empty($record->bcmid) && empty($carry['criteria_met'])) {

                    $agg = $badge->get_aggregation_methods();
                    $evidenceids = array_map(function($record) {
                        return $record->critid;
                    }, $badge->get_criteria_completions($userid));

                    $criteria = $badge->criteria;
                    unset($criteria[BADGE_CRITERIA_TYPE_OVERALL]);

                    $items = [];
                    foreach ($criteria as $type => $c) {
                        if (in_array($c->id, $evidenceids)) {
                            $details = $c->get_details(true);
                            if (count($c->params) == 1) {
                                $items[] = get_string('criteria_descr_single_' . $type , 'core_badges') . ' ' . $details;
                            } else {
                                $items[] = get_string('criteria_descr_' . $type , 'core_badges',
                                    core_text::strtoupper($agg[$badge->get_aggregation_method($type)])) . ' ' . $details;
                            }
                        }
                    }
                    $carry['criteria_met'] = $items;
                }
                return $carry;
            }, function($badgeid, $data) use ($path, $userid) {
                $path = array_merge($path, ["{$data['name']} ({$badgeid})"]);
                $writer = writer::with_context(context_user::instance($userid));
                $writer->export_data($path, (object) $data);
                $writer->export_area_files($path, 'badges', 'userbadge', $badgeid);
            });

            // Export the backpacks.
            $data = [];
            $recordset = $DB->get_recordset_select('badge_backpack', 'userid = :userid', ['userid' => $userid]);
            foreach ($recordset as $record) {
                $data[] = [
                    'email' => $record->email,
                    'url' => $record->backpackurl,
                    'uid' => $record->backpackuid
                ];
            }
            $recordset->close();
            if (!empty($data)) {
                writer::with_context(context_user::instance($userid))->export_related_data($path, 'backpacks',
                    (object) ['backpacks' => $data]);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        // We cannot delete the course or system data as it is needed by the system.
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }

        // Delete all the user data.
        static::delete_user_data($context->instanceid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if (!in_array($context->instanceid, $userlist->get_userids())) {
            return;
        }

        if ($context->contextlevel == CONTEXT_USER) {
            // We can only delete our own data in the user context, nothing in course or system.
            static::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER && $context->instanceid == $userid) {
                // We can only delete our own data in the user context, nothing in course or system.
                static::delete_user_data($userid);
                break;
            }
        }
    }

    /**
     * Delete all the data for a user.
     *
     * @param int $userid The user ID.
     * @return void
     */
    protected static function delete_user_data($userid) {
        global $DB;

        // Delete the stuff.
        $DB->delete_records('badge_manual_award', ['recipientid' => $userid]);
        $DB->delete_records('badge_criteria_met', ['userid' => $userid]);
        $DB->delete_records('badge_issued', ['userid' => $userid]);

        // Delete the backpacks and related stuff.
        $backpackids = $DB->get_fieldset_select('badge_backpack', 'id', 'userid = :userid', ['userid' => $userid]);
        if (!empty($backpackids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($backpackids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('badge_external', "backpackid $insql", $inparams);
            $DB->delete_records_select('badge_backpack', "id $insql", $inparams);
        }
    }

    /**
     * Loop and export from a recordset.
     *
     * @param \moodle_recordset $recordset The recordset.
     * @param string $splitkey The record key to determine when to export.
     * @param mixed $initial The initial data to reduce from.
     * @param callable $reducer The function to return the dataset, receives current dataset, and the current record.
     * @param callable $export The function to export the dataset, receives the last value from $splitkey and the dataset.
     * @return void
     */
    protected static function recordset_loop_and_export(\moodle_recordset $recordset, $splitkey, $initial,
            callable $reducer, callable $export) {

        $data = $initial;
        $lastid = null;

        foreach ($recordset as $record) {
            if ($lastid !== null && $record->{$splitkey} != $lastid) {
                $export($lastid, $data);
                $data = $initial;
            }
            $data = $reducer($data, $record);
            $lastid = $record->{$splitkey};
        }
        $recordset->close();

        if ($lastid !== null) {
            $export($lastid, $data);
        }
    }
}
