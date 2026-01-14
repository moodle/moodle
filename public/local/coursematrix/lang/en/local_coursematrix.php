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
 * Language strings for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Course Matrix';
$string['coursematrix'] = 'Course Matrix';
$string['coursematrix:manage'] = 'Manage Course Matrix';
$string['coursematrix:viewdashboard'] = 'View Learning Plans Dashboard';
$string['coursematrix:assignplans'] = 'Assign Learning Plans to Users';
$string['coursematrix:receivereminders'] = 'Receive Learning Plan Reminders';
$string['department'] = 'Department';
$string['jobtitle'] = 'Job Title';
$string['courses'] = 'Courses';
$string['addnewrule'] = 'Add New Rule';
$string['editrule'] = 'Edit Rule';
$string['deleterule'] = 'Delete Rule';
$string['searchcourses'] = 'Search Courses';
$string['selectcourses'] = 'Select Courses';
$string['norules'] = 'No rules defined yet.';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['actions'] = 'Actions';
$string['matrixupdated'] = 'Matrix updated and enrollments processed.';

// Learning Plans.
$string['learningplans'] = 'Learning Plans';
$string['learningplan'] = 'Learning Plan';
$string['createplan'] = 'Create Learning Plan';
$string['editplan'] = 'Edit Learning Plan';
$string['deleteplan'] = 'Delete Learning Plan';
$string['planname'] = 'Plan Name';
$string['plandescription'] = 'Description';
$string['plancourses'] = 'Courses in Plan';
$string['plancourses_help'] = 'Select the courses that make up this learning plan. The order you select them determines the sequence users will progress through.';
$string['duedays'] = 'Days to Complete';
$string['reminders'] = 'Reminder Schedule';
$string['addreminder'] = 'Add Reminder';
$string['removereminder'] = 'Remove';
$string['daysbefore'] = 'Days Before Due';
$string['assigntoplan'] = 'Assign to Learning Plan';
$string['assignusers'] = 'Assign Users';
$string['selectusers'] = 'Select Users';
$string['selectplan'] = 'Select Learning Plan';
$string['selectlearningplans'] = 'Select Learning Plans';
$string['noplans'] = 'No learning plans defined yet.';
$string['plansaved'] = 'Learning plan saved successfully.';
$string['plandeleted'] = 'Learning plan deleted.';
$string['usersassigned'] = 'Users assigned to learning plan.';
$string['userunenrolled'] = 'User removed from learning plan.';
$string['unenroll'] = 'Remove';
$string['confirmunenroll'] = 'Are you sure you want to remove this user from the learning plan?';
$string['currentenrollments'] = 'Current Enrollments';
$string['noenrollments'] = 'No users are currently enrolled in learning plans.';
$string['alreadyassigned'] = 'User is already assigned to this plan.';

// Status.
$string['status'] = 'Status';
$string['status_active'] = 'In Progress';
$string['status_overdue'] = 'Overdue';
$string['status_completed'] = 'Completed';
$string['currentcourse'] = 'Current Course';
$string['startdate'] = 'Started';
$string['duedate'] = 'Due Date';

// Due date badges.
$string['daysremaining'] = '{$a} days remaining';
$string['dayremaining'] = '1 day remaining';
$string['overdue'] = 'OVERDUE';
$string['overduedays'] = '{$a} days overdue';

// Dashboard.
$string['dashboard'] = 'Dashboard';
$string['totalplans'] = 'Total Plans';
$string['totalusers'] = 'Total Users';
$string['activeusers'] = 'In Progress';
$string['overdueusers'] = 'Overdue';
$string['completedusers'] = 'Completed';
$string['planstatistics'] = 'Plan Statistics';
$string['userlist'] = 'User List';
$string['filterbyplan'] = 'Filter by Plan';
$string['filterbystatus'] = 'Filter by Status';
$string['allplans'] = 'All Plans';
$string['allstatuses'] = 'All Statuses';
$string['noplandata'] = 'No data available.';
$string['inprogress'] = 'In Progress';
$string['aging'] = 'Aging';
$string['viewusers'] = 'View Users';

// Reminder messages.
$string['task_sendreminders'] = 'Send learning plan reminders';
$string['messageprovider:planreminder'] = 'Learning plan reminder notifications';
$string['remindersubject'] = 'Reminder: Complete "{$a->coursename}" - {$a->daysremaining} days remaining';
$string['reminderbody'] = 'Hello {$a->username},

This is a reminder that you have {$a->daysremaining} day(s) remaining to complete the course "{$a->coursename}" as part of your learning plan "{$a->planname}".

Due date: {$a->duedate}

Please log in to Moodle and complete this course before the due date.

Thank you.';
