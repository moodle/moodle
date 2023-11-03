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
 * Version details
 *
 * LearnerScript Reports - A Moodle block for creating customizable reports
 *
 * @package     block_learnerscript
 * @author:     eAbyas Info Solutions
 * @date:       2017
 *
 * @copyright  eAbyas Info Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_block_learnerscript_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();
 
    if ($oldversion < 2020061502) {
        $table = new xmldb_table('block_learnerscript');
        $field = new xmldb_field('summary');  

        // Conditionally launch add field enddate.
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            $reportdetails = $DB->get_records_sql("SELECT * FROM {block_learnerscript}");
            if (empty($reportdetails)) {
                return true;
            }
            foreach ($reportdetails as $reportdetail) {
                $reportid = $reportdetail->id;
                $reportname = $reportdetail->name;
                $reporttype = $reportdetail->type;
                $reportdetail1 = new stdClass();
                if($reporttype == 'statistics' || $reporttype == 'sql'){
                     if($reportname == 'Active learners'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of students active on the LMS for the last two hours.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                     }
                     if($reportname == 'Active users'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of users (including teachers, students and others) active on the LMS for the last two hours.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                     } 
                     if($reportname == 'Activities'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Total:</strong>&nbsp;The total number of activities created from the all courses on your system, within a timeline.</p><p></p><p><strong>Active:&nbsp;</strong>The number of activities available (active/inactive status) &nbsp;from all the courses for learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                     } 
                     if($reportname == 'Assignment completions'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of assignments completed by the learners from all the courses, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Assignment status'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report shows the stats about the assignment status.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Assignments'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Total:</strong>&nbsp;The total number of assignments created from the all courses on your system, within a timeline.</p><p><strong>Active:&nbsp;</strong>The number of assignments available (active/inactive status) from all the courses for learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Average time spent on LMS'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The average time-spent on the system by students/teachers since the time of LearnerScript installation. The average time-spent on LMS by the admin will have ‘Zero’.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Avg time spent by learners and teachers on course'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report shows the average time spent by learners and teachers alike on a particular course.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Browser statistics'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report shows you the browser statistics like which browser is popularly used to access your LMS site – the browsers such as Internet Explorer, Safari, Chrome, Firefox, etc.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Country wise registrations'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report gives you the inputs about the country-wise registrations for a course.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Course participation'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Enrollments:</strong>&nbsp;The number of students/learners enrolled to all the available courses in the system (within the time duration)</p><p><strong>Completions:</strong>&nbsp;The number of learner completions from all the available courses in the system (within the time duration).</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Courses'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Total:</strong>&nbsp;The total number of courses on your system, within a timeline.</p><p></p><p><strong>Active:&nbsp;</strong>The number of courses available active&nbsp; for learners, within a timeline.</p><p><b>Inactive:&nbsp;</b>&nbsp;The number of courses available inactive&nbsp; for learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Device statistics'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report provides you with the stats about devices used to access the LMS – mobile, tablet, and desktop.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Enrolled courses'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of courses enrolled to by a student/teacher.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Logins'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Active:</strong>&nbsp;The number of learners active for the last two hours (even if you log in, stay on the system and log out after a few minutes, you will be considered as active with in that time).</p><p><strong>Unique:</strong>&nbsp;The number of unique/new visits to your LMS site irrespective of roles, within the time duration.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Maximum time spent in activity level'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The highest amount of time-spent on an activity.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Maximum time spent in course'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The highest amount of time-spent on anything in a particular course.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Maximum time spent on LMS'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The maximum amount of time-spent on the LMS by all users.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Modules'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><b>Total:</b> The number of activities available overall.</p><p><b>Resources: </b>The number of resources available overall.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Most accessed'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Device type:&nbsp;</strong>The device from which you get a greater number of visits to your LMS site, within a timeline.</p><p><strong>Total:</strong>&nbsp;The total number of accesses corresponding to the device (the most accessed device), within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'My activities'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Activities:</strong>&nbsp;The number of activities available to you from the courses you enrolled to as a student/learner, within a timeline.</p><p><strong>Completions:</strong>&nbsp;The number of activities you completed as a student from the courses you enrolled to, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'My assignments'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Assignments:</strong>&nbsp;The number of available to you to complete from the courses you enrolled to as a student, within a timeline.</p><p><strong>Completions:</strong>&nbsp;The number of assignments you have completed so far from the courses you enrolled to, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'My course participation'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Enrollments:</strong>&nbsp;The number of courses you, as a student, enrolled to within a timeline.</p><p><strong>Completions:</strong>&nbsp;The number of courses you completed as a student from the enrolled courses, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'My quizzes'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Quizzes:</strong>&nbsp;The number of quizzes available for you from the courses you enrolled to as a student, within a timeline.</p><p><strong>Completions:</strong>&nbsp;The number of quizzes you completed as a student from the courses you enrolled to, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'My resources'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The resources available to you at present from the courses you enrolled to, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Need grading'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report shows the pending assignments that need grading along with the number of due days.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'No login courses'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><b>Total Courses:</b> The total number of courses a student/teacher enrolled to.</p><p><span><b>No Login Courses:</b></span> The number of no login courses by a student/teacher.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Quiz completions'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of quizzes (from all courses) completed by the learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Quizzes overview'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Total:</strong>&nbsp;The total number of quizzes created from the all courses on your system, within a timeline.</p><p><strong>Active:&nbsp;</strong>The number of quizzes available (active/inactive status) &nbsp;from all the courses for learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Resource views'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of Resource type activity (from all courses) visits, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'SCORM completions'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of Scorms (from all courses) completed by the learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'SCORM overview'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Total:</strong>&nbsp;The total number of Scorms created from the all courses on your system, within a timeline.</p><p><strong>Active:&nbsp;</strong>The number of Scorms available (active/inactive status) from all the courses for learners, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Site visits'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The number of site views by students &amp; teachers on the LMS.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Teacher activities'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><b>Total: </b>The total number of activities available for a teacher from his/her enrolled courses.</p><p><b>Active: </b>The number of activities active from the total activities on the system.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Top access country'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Country:&nbsp;</strong>The country from which you get a greater number of visits to your LMS site, within a timeline.</p><p><strong>Total:</strong>&nbsp;The total number of accesses corresponding to the country (the top access location), within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    } 
                    if($reportname == 'Top learner'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The learner who spent more time to learn something on your LMS.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Top registrations'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Country:&nbsp;</strong>The country from which you have more registrations, within a timeline.</p><p><strong>Registrations:</strong>&nbsp;The total number of registrations corresponding to the country , within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    } 
                    if($reportname == 'Total timespent'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The amount of time-spent on the LMS by learners &amp; teachers. For the admin, the time-spent will be displayed as ‘ZERO’.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    } 
                    if($reportname == 'Trending activity'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The activity which has a great number of visits from your learners.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending assignment'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The assignment which is accessed a great number of time by your learners.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending browser'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Browser:&nbsp;</strong>The browser from which your users access your LMS site more, within a timeline</p><p><strong>No. of Users:</strong>&nbsp;The total number of users who access your LMS site from the corresponding to the trending browser, within a timeline.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending course'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The course more people accessing to on your LMS.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending page resource'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The page which is accessed a great number of time by your learners.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending quiz'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The quiz which is accessed a great number of times by your learners.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending resource'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>The resource which has a great number of hits/access time.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Trending SCORM'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><span>T</span>he SCORM which is accessed a great number of time by your learners.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'User accessed platform'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report helps you get the stats about the user accessed platform such as iOS, Windows, Android, etc.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'User access location'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p>This report tells you the user access location stats, the location from where your users access your LMS site.</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }
                    if($reportname == 'Users'){
                        $reportdetail1->id = $reportid;
                        $reportdetail1->name =  $reportname;
                        $reportdetail1->summary =  "<p><strong>Learners:</strong>&nbsp;The number of learners who has started learning at least one course. (User who is already a learner is not considered as a new learner here, within the time duration)</p><p><strong>Instructor:</strong>&nbsp;The number of instructors who has started teaching at least one course. (User, who is already an instructor is not considered as a new instructor here, within the time duration).</p>";
                        $DB->update_record('block_learnerscript', $reportdetail1);
                    }

                }

            }

        }

        $taskname = '\block_learnerscript\task\userquiztimespent';
        $task = \core\task\manager::get_scheduled_task($taskname);
        if(!empty($task)){
           $crontime = $task->get_last_run_time();
           set_config('userquiztimespent', $crontime,'block_learnerscript'); 
        }

        $taskname1 = '\block_learnerscript\task\userscormtimespent';
        $task1 = \core\task\manager::get_scheduled_task($taskname1);
        if(!empty($task1)){ 
           $crontime1 = $task1->get_last_run_time();
           set_config('userscormtimespent', $crontime1,'block_learnerscript');
        } 

        $table1 = new xmldb_table('block_ls_coursetimestats');
        $table2 = new xmldb_table('block_ls_modtimestats'); 

        $index1 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid')); 
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }
        $index1 = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid')); 
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }
        $index1 = new xmldb_index('timespent', XMLDB_INDEX_NOTUNIQUE, array('timespent')); 
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        } 


        $index2 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid')); 
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }
        $index2 = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid')); 
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }
        $index2 = new xmldb_index('timespent', XMLDB_INDEX_NOTUNIQUE, array('timespent')); 
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        } 
        $index2 = new xmldb_index('activityid', XMLDB_INDEX_NOTUNIQUE, array('activityid')); 
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }
        $index2 = new xmldb_index('instanceid', XMLDB_INDEX_NOTUNIQUE, array('instanceid')); 
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }

        upgrade_plugin_savepoint(true, 2020061502, 'block', 'learnerscript');
    }
    if ($oldversion < 2021051704.09) {
        // Changing type of field thisfield on table roleid to int
        $table = new xmldb_table('block_ls_schedule');
        $field = new xmldb_field('roleid', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, '0');

        // Launch change of type for field roleid
        $dbman->change_field_type($table, $field);

        // Quiz savepoint reached.
        upgrade_plugin_savepoint(true, 2021051704.09, 'block', 'learnerscript');
    }    
    if ($oldversion < 2021051704.10) {
        // Changing type of field thisfield on table roleid to int
        $table = new xmldb_table('block_ls_schedule');
        $field = new xmldb_field('nextschedule', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Launch change of type for field roleid
        $dbman->change_field_type($table, $field);

        // Quiz savepoint reached.
        upgrade_plugin_savepoint(true, 2021051704.10, 'block', 'learnerscript');
    }        
    return true;
}
