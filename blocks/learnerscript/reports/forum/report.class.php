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
 * @subpackage learnerscript
 * @author: sreekanth
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;

defined('MOODLE_INTERNAL') || die();
class report_forum extends reportbase implements report {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report, $reportproperties);
        $this->parent = true;
        $this->columns = array('forumfield' => ['forumfield'],
                                'forum' => array('discussionscount', 'posts', 'replies', 'wordscount'));
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->courselevel = false;
        $this->filters = array('courses');
        $this->orderable = array('name', 'course', 'discussionscount', 'posts', 'replies', 'wordscount');
        $this->searchable = array('f.name', 'c.fullname', 'c.shortname');
        $this->defaultcolumn = 'f.id';
        $this->excludedroles = array("'student'");
    }
    public function count() {
        $this->sql = "SELECT COUNT(DISTINCT f.id)";
    }
    public function select() {
        $this->sql = "SELECT DISTINCT f.id, f.name AS name, c.id AS course ";
        parent::select();
    }
    public function from() {
        $this->sql .= " FROM {forum} f 
						LEFT JOIN {forum_discussions} fd ON fd.forum = f.id 
						JOIN {course} c ON c.id = f.course 
						JOIN {course_modules} cm ON cm.instance = f.id 
						JOIN {modules} m ON m.id = cm.module";
    }
    public function joins() {
        parent::joins();
    }
    public function where() {
        $coursesql  = (new querylib)->get_learners('', 'f.course');
        $this->sql .= " WHERE c.visible = 1 AND cm.visible = 1 AND c.id <> 1 AND m.name = :name AND f.type != 'news'";
        $this->params['name']= 'forum';
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND f.course IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }
    public function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }
    public function filters() {
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {
            $this->sql .= " AND f.course IN (:filter_courses) ";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
    }
    public function groupby() {
        
    }
    /**
     * [get_rows description]
     * @param  array  $users [description]
     * @return [type]        [description]
     */
    public function get_rows($forums = array()) {
        return $forums;
    }
    public function column_queries($columnname, $forumid, $courseid = null) {
        global $CFG;
        if($courseid){
            $learnersql  = (new querylib)->get_learners('', $courseid);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }
        $where = " AND %placeholder% = $forumid";
       
        switch ($columnname) {
            case 'discussionscount':
                $identy = 'fd.forum';
                $query = "SELECT COUNT(fd.id) AS discussionscount 
                				FROM {forum_discussions} fd WHERE 1 = 1 $where ";
                break;
            case 'posts':
                $identy = 'fd.forum';
                $courseid = 'fd.course';
                $query = "SELECT COUNT(fp.id) AS posts 
                				FROM {forum_posts} fp 
                				JOIN {forum_discussions} fd ON fp.discussion = fd.id 
                				WHERE 1 = 1 AND fp.userid IN ($learnersql) AND fp.parent = 0 $where  ";
            	break;
            case 'replies':
                $identy = 'fd.forum';
                $courseid = 'fd.course';
                $query = "SELECT COUNT(fp.id) AS replies 
                                FROM {forum_posts} fp 
                                JOIN {forum_discussions} fd ON fp.discussion = fd.id 
                                WHERE 1 = 1 AND fp.userid IN ($learnersql) AND fp.parent != 0  $where  ";
                break;
            case 'wordscount':
                $identy = 'fd.forum';
                $courseid = 'fd.course';
                if ($CFG->dbtype == 'sqlsrv') {
                    $query = "SELECT SUM(LEN(fp.message) - 
                                         LEN(REPLACE((fp.message), ' ', '')) + 1)  AS wordscount 
                                    FROM {forum_posts} fp 
                                    JOIN {forum_discussions} fd ON fp.discussion = fd.id 
                                    WHERE 1 = 1 AND fp.userid IN ($learnersql) $where  ";
                } else {
                    $query = "SELECT SUM(LENGTH(fp.message) - 
                                         LENGTH(REPLACE((fp.message), ' ', '')) + 1)  AS wordscount 
                                    FROM {forum_posts} fp 
                                    JOIN {forum_discussions} fd ON fp.discussion = fd.id 
                                    WHERE 1 = 1 AND fp.userid IN ($learnersql) $where  ";
                }
            break;
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%courseid%', $courseid, $query);
        return $query;
    }
}
