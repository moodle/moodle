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
 * Definition of core event handler and description of all events throws from core.
 *
 * The handlers defined on this file are processed and registered into
 * the Moodle DB after any install or upgrade operation. All plugins
 * support this.
 *
 * For more information, take a look to the documentation available:
 *     - Events API: {@link http://docs.moodle.org/dev/Events_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package   core
 * @category  event
 * @copyright 2007 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* List of handlers */

$handlers = array(

/*
 * portfolio queued event - for non interactive file transfers
 * NOTE: this is a HACK, please do not add any more things like this here
 *       (it is just abusing cron to do very time consuming things which is wrong any way)
 *
 * TODO: this has to be moved into separate queueing framework....
 */
    'portfolio_send' => array (
        'handlerfile'      => '/lib/portfolio.php',
        'handlerfunction'  => 'portfolio_handle_event',    // argument to call_user_func(), could be an array
        'schedule'         => 'cron',
        'internal'         => 0,
    ),
    'course_completed' => array (
        'handlerfile'      => '/lib/badgeslib.php',
        'handlerfunction'  => 'badges_award_handle_course_criteria_review',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'activity_completion_changed' => array (
        'handlerfile'      => '/lib/badgeslib.php',
        'handlerfunction'  => 'badges_award_handle_activity_criteria_review',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'user_updated' => array (
        'handlerfile'      => '/lib/badgeslib.php',
        'handlerfunction'  => 'badges_award_handle_profile_criteria_review',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

/* no more here please, core should not consume any events!!!!!!! */
);




/* List of events thrown from Moodle core

==== user related events ====

user_created - object user table record
user_updated - object user table record
user_deleted - object user table record
user_logout - full $USER object

==== course related events ====

course_category_updated - object course_categories table record
course_category_created - object course_categories table record
course_category_deleted - object course_categories table record

course_created - object course table record
course_updated - object course table record
course_content_removed - object course table record + context property
course_deleted - object course table record + context property
course_restored - custom object with courseid, userid and restore information

user_enrolled - object record from user_enrolments table + courseid,enrol
user_enrol_modified - object record from user_enrolments table + courseid,enrol
user_unenrolled - object record from user_enrolments table + courseid,enrol,lastenrol

==== cohort related events ===


cohort_added - object cohort table record
cohort_updated - object cohort table record
cohort_deleted - object cohort table record

cohort_member_added - object cohortid, userid properties
cohort_member_removed - object cohortid, userid properties

==== group related events ====

groups_group_created - object groups_group table record
groups_group_updated - object groups_group table record
groups_group_deleted - object groups_group table record

groups_member_added   - object userid, groupid properties
groups_member_removed - object userid, groupid properties

groups_grouping_created - object groups_grouping table record
groups_grouping_updated - object groups_grouping table record
groups_grouping_deleted - object groups_grouping table record

groups_members_removed          - object courseid+userid - removed all users (or one user) from all groups in course
groups_groupings_groups_removed - int course id - removed all groups from all groupings in course
groups_groups_deleted           - int course id - deleted all course groups
groups_groupings_deleted        - int course id - deleted all course groupings

==== role related events ====

role_assigned         - object role_assignments table record
role_unassigned       - object role_assignments table record

==== activity module events ====

mod_deleted - int courseid, int cmid, text modulename - happens when a module is deleted
mod_created - int courseid, int cmid, text modulename - happens when a module is created
mod_updated - int courseid, int cmid, text modulename - happens when a module is updated

=== blog events

blog_entry_added - blog post object
blog_entry_edited - blog post object
blog_entry_deleteded - blog post object

*/
