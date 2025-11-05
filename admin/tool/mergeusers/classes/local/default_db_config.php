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
 * Default database-related configuration.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use tool_mergeusers\local\cli\cli_gathering;
use tool_mergeusers\local\merger\assign_submission_table_merger;
use tool_mergeusers\local\merger\generic_table_merger;
use tool_mergeusers\local\merger\quiz_attempts_table_merger;
use tool_mergeusers\local\merger\grade_grades_table_merger;

/**
 * Default database-related configuration.
 *
 * It contains what config/config.php provided before.
 * These settings are, beforehand, sufficient for a normal operation of the merge users tool.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_db_config {
    /** @var string[] Default database-related settings from this plugin. */
    public static array $config = [
        // The gathering tool.
        'gathering' => cli_gathering::class,

        // Database tables to be excluded from normal processing.
        // You normally will add tables. Be very cautious if you delete any of them.
        'exceptions' => [
            'user_preferences',
            'user_private_key',
            'user_info_data',
            'my_pages',
        ],

        // List of compound indexes.
        //
        // This list may vary from Moodle instance to another, given that the Moodle version,
        // local changes and non-core plugins may add new special cases to be processed.
        // Place in 'userfield' all column names related to a user (i.e., user.id).
        // Place all the rest column names into 'otherfields'. It may be empty.
        // Table names must be without $CFG->prefix.
        // You can use the cli/listuserfields.php CLI script to help detect other cases for your Moodle instance.
        // The result of unique compound indexes %user%-related by field name may present false positives.
        'compoundindexes' => [
            // Unique indexes with matching user-related field with foreign key to user.id.
            'auth_lti_linked_login' => [
                // For index 'mdl_authltilinklogi_useis3_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['issuer256', 'sub256'],
            ],
            'auth_oauth2_linked_login' => [
                // For index 'mdl_authoautlinklogi_useis_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['issuerid', 'username'],
            ],
            'badge_backpack' => [
                // For index 'mdl_badgback_useext_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['externalbackpackid'],
            ],
            'badge_issued' => [
                // For index 'badgeuser'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['badgeid'],
            ],
            'block_recentlyaccesseditems' => [
                // For index 'userid-courseid-cmid'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['cmid', 'courseid'],
            ],
            'cohort_members' => [
                // For index 'cohortid-userid'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: enforce unique index.
                'userfield' => ['userid'],
                'otherfields' => ['cohortid'],
            ],
            'favourite' => [
                // For index 'uniqueuserfavouriteitem'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['component', 'contextid', 'itemid', 'itemtype'],
            ],
            'forum_digests' => [
                // For index 'mdl_forudige_forusemai_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['forum', 'maildigest'],
            ],
            'forum_discussion_subs' => [
                // For index 'mdl_forudiscsubs_usedis_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['discussion'],
            ],
            'grade_grades' => [
                // For index 'mdl_gradgrad_useite_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['itemid'],
            ],
            'groups_members' => [
                // For index 'mdl_groumemb_usegro_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['groupid'],
            ],
            'message_contact_requests' => [
                // For index 'userid-requesteduserid'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['requesteduserid', 'userid'],
                'otherfields' => [],
            ],
            'message_contacts' => [
                // For index 'userid-contactid'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['contactid', 'userid'],
                'otherfields' => [],
            ],
            'message_user_actions' => [
                // For index 'userid_messageid_action'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['action', 'messageid'],
            ],
            'message_users_blocked' => [
                // For index 'userid-blockeduserid'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['blockeduserid', 'userid'],
                'otherfields' => [],
            ],
            'oauth2_refresh_token' => [
                // For index 'userid-issuerid-scopehash'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['issuerid', 'scopehash'],
            ],
            'quiz_attempts' => [
                // For index 'quiz-userid-attempt'.
                // Type of index: unique; type of matching: by foreign key.
                'userfield' => ['userid'],
                'otherfields' => ['attempt', 'quiz'],
            ],
            'tool_policy_acceptances' => [
                // For index 'mdl_toolpoliacce_poluse_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['policyversionid'],
            ],
            'user_devices' => [
                // For index 'mdl_userdevi_pususe_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['pushid'],
            ],
            'workshop_aggregations' => [
                // For index 'mdl_workaggr_woruse_uix'.
                // Type of index: unique; type of matching: by foreign key.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['workshopid'],
            ],


            // Unique indexes with matching user-related field by name.
            'assign_grades' => [
                // For index 'uniqueattemptgrade'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: This is a grade for a unique attempt.
                'userfield' => ['userid'],
                'otherfields' => ['assignment', 'attemptnumber'],
            ],
            'assign_submission' => [
                // For index 'uniqueattemptsubmission'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['assignment', 'attemptnumber', 'groupid'],
            ],
            'competency_usercomp' => [
                // For index 'useridcompetency'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['competencyid'],
            ],
            'competency_usercompcourse' => [
                // For index 'useridcoursecomp'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['competencyid', 'courseid'],
            ],
            'competency_usercompplan' => [
                // For index 'usercompetencyplan'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['competencyid', 'planid'],
            ],
            'course_completion_crit_compl' => [
                // For index 'useridcoursecriteriaid'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['course', 'criteriaid'],
            ],
            'course_completions' => [
                // For index 'useridcourse'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['course'],
            ],
            'course_modules_completion' => [
                // For index 'userid-coursemoduleid'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['coursemoduleid'],
            ],
            'course_modules_viewed' => [
                // For index 'userid-coursemoduleid'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['coursemoduleid'],
            ],
            'editor_atto_autosave' => [
                // For index 'mdl_editattoauto_eleconuse_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['contextid', 'elementid', 'pagehash'],
            ],
            'forum_grades' => [
                // For index 'forumusergrade'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['forum', 'itemnumber'],
            ],
            'forum_subscriptions' => [
                // For index 'mdl_forusubs_usefor_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['forum'],
            ],
            'h5pactivity_attempts' => [
                // For index 'mdl_h5paatte_h5puseatt_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['attempt', 'h5pactivityid'],
            ],
            'post' => [
                // For index 'id-userid'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['id'],
            ],
            'tag_instance' => [
                // For index 'taggeditem'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['tiuserid'],
                'otherfields' => ['component', 'contextid', 'itemid', 'itemtype', 'tagid'],
            ],
            'tiny_autosave' => [
                // For index 'mdl_tinyauto_eleconusepag_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['contextid', 'elementid', 'pagehash'],
            ],
            'tool_cohortroles' => [
                // For index 'cohortuserrole'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['cohortid', 'roleid'],
            ],
            'tool_monitor_history' => [
                // For index 'sid_userid_timesent'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['sid', 'timesent'],
            ],
            'user_info_data' => [
                // For index 'userfieldidx'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['fieldid'],
            ],
            'user_lastaccess' => [
                // For index 'userid-courseid'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['courseid'],
            ],
            'user_preferences' => [
                // For index 'userid-name'.
                // Type of index: unique; type of matching: by %user%-related column name.
                'userfield' => ['userid'],
                'otherfields' => ['name'],
            ],
            'wiki_pages' => [
                // For index 'mdl_wikipage_subtituse_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['subwikiid', 'title'],
            ],
            'wiki_subwikis' => [
                // For index 'mdl_wikisubw_wikgrouse_uix'.
                // Type of index: unique; type of matching: by %user%-related column name.
                // Index comment: Index in database but not in XML definition.
                'userfield' => ['userid'],
                'otherfields' => ['groupid', 'wikiid'],
            ],

            // If we need to add non-unique indexes is because they are treated in Moodle as unique, actually.
            // Non-unique indexes with matching user-related field by foreign key.
            'role_assignments' => [
                // For index 'usercontextrole'.
                // Type of index: non-unique; type of matching: by foreign key.
                // Index comment: Index on userid, contextid and roleid.
                'userfield' => ['userid'],
                'otherfields' => ['contextid', 'roleid'],
            ],
            'user_enrolments' => [
                // For index 'enrolid-userid'.
                // Type of index: non-unique; type of matching: by foreign key.
                // Index comment: Only one enrolment per plugin allowed.
                'userfield' => ['userid'],
                'otherfields' => ['enrolid'],
            ],

            // Manually added to ensure proper Moodle operation.
            // These indexes are candidate to be added on Moodle core XML schema definition.
            // They are actually a unique key from PHP viewpoint, but not in DDL.
            'assign_user_flags' => [
                'userfield' => ['userid'],
                'otherfields' => ['assignment'],
            ],
            'assign_user_mapping' => [
                'userfield' => ['userid'],
                'otherfields' => ['assignment'],
            ],

            // Manually added for third-party plugins.
            // These indexes are candidate to be added by those plugins themselves.
            // By implementing this plugin's hook named 'add_settings_before_merging'.
            'journal_entries' => [
                'userfield' => ['userid'],
                'otherfields' => ['journal'],
            ],
            'certif_completion' => [
                // From mdl_certcomp_ceruse_uix (unique).
                'userfield' => ['userid'],
                'otherfields' => ['certifid'],
            ],
            'customcert_issues' => [
                'userfield' => ['userid'],
                'otherfields' => ['customcertid'],
            ],
        ],

        // List of column names per table, where columns' content is related to user.id.
        // These are necessary for matching passed by userids in these column names.
        // In other words, only column names given below will be searching for matching user ids.
        // The key 'default' will be applied for any non-matching table name.
        // You can use the cli/listuserfields.php CLI script to help detect other cases for your Moodle instance.
        'userfieldnames' => [
            'badge_manual_award' => ['issuerid', 'recipientid'],
            'competency_evidence' => ['actionuserid', 'usermodified'],
            'external_tokens' => ['creatorid', 'userid'],
            'grade_import_values' => ['importer', 'userid'],
            'grade_import_newitem' => ['importer'],
            'grading_instances' => ['raterid'],
            'logstore_standard_log' => ['userid', 'relateduserid', 'realuserid'],
            'message_contacts' => ['contactid', 'userid'],
            'message_contact_requests' => ['userid', 'requesteduserid'],
            'message_users_blocked' => ['blockeduserid', 'userid'],
            'question' => ['createdby', 'modifiedby'],
            'reportbuilder_schedule' => ['usercreated', 'usermodified', 'userviewas'],
            'role_capabilities' => ['modifierid'],
            'search_simpledb_index' => ['owneruserid', 'userid'],
            'sms_messages' => ['recipientuserid'],
            'tool_mergeusers' => ['mergedbyuserid'], // Only this column. Others must be kept as is.
            'tool_dataprivacy_request' => ['dp4o', 'requestedby', 'userid', 'usermodified'],
            'user_enrolments' => ['modifierid', 'userid'],
            'workshop_assessments' => ['gradinggradeoverby', 'reviewerid'],
            'workshop_submissions' => ['authorid', 'gradeoverby'],
            'default' => [
                'authorid',
                'id_user',
                'loggeduser',
                'reviewerid',
                'user',
                'user_id',
                'usercreated',
                'userid',
                'useridfrom',
                'useridto',
                'usermodified',
            ],
        ],

        // The table_mergers to process each database table.
        // The 'default' is applied when no specific table_merger is specified.
        'tablemergers' => [
            'default' => generic_table_merger::class,
            'quiz_attempts' => quiz_attempts_table_merger::class,
            'assign_submission' => assign_submission_table_merger::class,
            'grade_grades' => grade_grades_table_merger::class,
        ],

        'alwaysrollback' => false,
        'debugdb' => false,
    ];
}
