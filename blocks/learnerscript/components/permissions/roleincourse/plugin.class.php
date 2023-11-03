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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\ls;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\querylib;
use block_learnerscript\local\permissionslib;
use context_helper;

class plugin_roleincourse extends pluginbase {

    public $role;

    public function init() {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('roleincourse', 'block_learnerscript');
        $this->reporttypes = array( 'courses', 'sql', 'users', 'statistics', 'timeline', 'categories', 'activitystatus', 'listofactivities', '  coursesoverview', 'usercourses', 'grades','scorm_activities_course', 'competencycompletion', 'myassignments', 'useractivities',
                                    'assignments', 'userassignments', 'resources', 'myscorm', 'quizzes', 'userquizzes',
                                    'assignment', 'myquizs', 'topic_wise_performance', 'courseaverage', 'uniquelogins',
                                    'popularresources','userbadges', 'scorm', 'usersresources', 'usersscorm', 'badges',
                                    'pageresourcetimespent', 'coursewisetimespent', 'gradedactivity', 'userprofile',
                                    'resources_accessed', 'timespent','noofviews', 'forum', 'assignstatus', 'myforums', 'courseprofile', 'userattendance', 'attendanceoverview', 'courseactivities', 'courseviews','coursesoverview','quizzparticipation','assignmentparticipation','weeklysessions', 'monthlysessions', 'dailysessions','upcomingactivities','pendingactivities','needgrading','scormparticipation','cohortusers','bigbluebutton','activestudents');
    }

    public function summary($data) {
        global $DB;
        $rolename = $DB->get_field('role', 'shortname', array('id' => $data->roleid));
        $contextname = context_helper::get_level_name($data->contextlevel);
        return $rolename . ' at ' . $contextname .' level';
    }

    public function execute($userid, $context, $data) {
        global $CFG, $DB;
        
        $permissions = (isset($this->reportclass->componentdata['permissions'])) ? $this->reportclass->componentdata['permissions'] : array();

        if (!empty($this->role)) {

            $currentroleid = $DB->get_field('role', 'id', array('shortname' => $this->role));
       
            $rolepermissions = array();
            $return = [];
            foreach ($permissions['elements'] as $p) {
                $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);
                if ($p['pluginname'] == 'roleincourse' && isset($p['formdata']->contextlevel) && $p['formdata']->roleid == $currentroleid) {
                   $permissionslib = new permissionslib($p['formdata']->contextlevel, $p['formdata']->roleid, $userid);
                   if($permissionslib->has_permission()){
                        $return[] = true;
                   }
                }
            }
            return in_array(true, $return);
        }
        return false;
    }
}
