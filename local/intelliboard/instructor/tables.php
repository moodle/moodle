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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/gradelib.php');

class local_intelliboard_intelli_table extends table_sql {
    /**
     * Call this to pass the download type. Use :
     *         $download = optional_param('download', '', PARAM_ALPHA);
     * To get the download type. We assume that if you call this function with
     * params that this table's data is downloadable, so we call is_downloadable
     * for you (even if the param is '', which means no download this time.
     * Also you can call this method with no params to get the current set
     * download type.
     * @param string $download dataformat type. One of csv, xhtml, ods, etc
     * @param string $filename filename for downloads without file extension.
     * @param string $sheettitle title for downloaded data.
     */
    function is_downloading($download = null, $filename='', $sheettitle='', $action = '', $pagesize = 10) {
        $filename = str_replace(',', ' ', $filename);
        if ($download === 'pdf') {
            $this->setup();
            $this->query_db($pagesize, true);
            $this->download = $download;
            $header = array_map(function($item) {
                return (object) ["name" => $item];
            }, $this->headers);
            $body = array_map(function($item) {
                return array_values($this->format_row($item));
            }, $this->rawdata);

            $exportitem = (object) [
                "header" => $header, "body" => $body
            ];
            intelliboard_export_report(
                $exportitem, $filename, 'pdf', 1
            );
        } else {
            return parent::is_downloading($download, $filename, $sheettitle);
        }
    }
}

class intelliboard_courses_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $search = '', $download = 0) {
        global $PAGE, $DB, $USER;

        parent::__construct($uniqueid);

        $headers = array();
        $columns = array();
        $params = array();

        $grade_avg = intelliboard_grade_sql(true);
        $join_sql = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ra.courseid');
        $join_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('ra.courseid', 'ra.userid');
        $sql1 = intelliboard_instructor_getcourses('c.id', false, '', false, false);
        $sql2 = intelliboard_instructor_getcourses('ctx.instanceid', false, 'ra.userid', false, false);
        $sql3 = intelliboard_instructor_getcourses('e.courseid', false, 'ue.userid', false, false);
        $sql4 = intelliboard_instructor_getcourses('courseid', false, 'userid', false, false);
        $sql_columns = "";

        if(get_config('local_intelliboard', 'table_set_icg_c1')) {
            $columns[] =  'course';
            $headers[] =  get_string('course_name', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c2')) {
            $columns[] =  'course_shortname';
            $headers[] =  get_string('shortname');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c3')) {
            $columns[] =  'category';
            $headers[] =  get_string('category', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c4')) {
            $columns[] =  'learners';
            $headers[] =  get_string('enrolled_completed_learners', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c13')) {
            $columns[] =  'percentage_completed_learners';
            $headers[] =  get_string(
                'percentage_completed_learners', 'local_intelliboard'
            );
        }

        if (get_config('local_intelliboard', 'table_set_icg_c5')) {
            $columns[] =  'grade';
            $headers[] =  get_string('in21', 'local_intelliboard');

            $join_sql .= " \n LEFT JOIN {grade_items} gi ON gi.courseid = ra.courseid AND gi.itemtype = 'course'
                            LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = ra.userid AND g.finalgrade IS NOT NULL";
            $sql_columns .= ", $grade_avg AS grade";
        } else {
          $sql_columns .= ", '0' AS grade";
        }

        if(get_config('local_intelliboard', 'table_set_icg_c6')) {
            $columns[] =  'sections';
            $headers[] =  get_string('sections', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c7')) {
            $columns[] =  'modules';
            $headers[] =  get_string('activities_resources', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c8')) {
            $columns[] =  'visits';
            $headers[] =  get_string('visits', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c9')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c14')) {
            $columns[] =  'avg_visits';
            $headers[] =  get_string('avg_visits_per_stud', 'local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_icg_c15')) {
            $columns[] =  'avg_timespend';
            $headers[] =  get_string('avg_time_spent_per_stud', 'local_intelliboard');
        }

        if((get_config('local_intelliboard', 'table_set_icg_c11') or get_config('local_intelliboard', 'table_set_icg_c12')) && !$download) {
            $columns[] =  'actions';
            $headers[] =  get_string('actions', 'local_intelliboard');
        }

        if(!$columns) {
            return false;
        }

        if (get_config('local_intelliboard', 'table_set_icg_c8') or get_config('local_intelliboard', 'table_set_icg_c9')) {
            $join_sql .= "\n LEFT JOIN (SELECT userid, courseid, SUM(timespend) AS timespend, SUM(visits) AS visits
                                    FROM {local_intelliboard_tracking}
                                      WHERE courseid > 0 $sql4
                                        GROUP BY courseid, userid) lit ON lit.userid = ra.userid AND lit.courseid = ra.courseid";
            $sql_columns .= ", SUM(lit.timespend) AS timespend, SUM(lit.visits) AS visits";
        } else {
            $sql_columns .= ", '0' AS timespend, '0' AS visits";
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        list($sql_r, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);

        if ($sql_r) {
            $sql2 .= " AND ra.roleid $sql_r";
        }

        if ($search) {
            $sql1 .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }

        if (get_filter_usersql("u.")) {
            $join_sql .= "\n JOIN {user} u ON u.id = ra.userid " . get_filter_usersql("u.");
        }

        $fields = "
                  c.id,
                  c.fullname AS course,
                  c.shortname AS course_shortname,
                  c.enablecompletion,
                  ca.name AS category,
                  x.timespend,
                  x.visits,
                  x.learners,
                  x.completed,
                  x.grade,
                  '' AS avg_timespend,
                  '' AS avg_visits,
                  '' AS percentage_completed_learners,
                  '' AS actions,
                  (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND course = c.id) AS modules,
                  (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections";

        $from = "{course} c
                  JOIN {course_categories} ca ON ca.id = c.category
                  JOIN (SELECT ra.courseid,
                          COUNT(ra.userid) AS learners,
                          COUNT(DISTINCT cc.userid) AS completed
                          {$sql_columns}
                        FROM (SELECT ctx.instanceid AS courseid, ra.userid
                              FROM {role_assignments} ra, {context} ctx
                                WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 {$sql2}
                                  GROUP BY ctx.instanceid, ra.userid) ra
                        LEFT JOIN {course_completions} cc ON cc.course = ra.courseid AND cc.userid = ra.userid AND cc.timecompleted > 0
                        {$join_sql}
                      WHERE ra.courseid > 0
                    GROUP BY ra.courseid
                  ) x ON x.courseid = c.id";
        $where = "c.id > 0 $sql1";


        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);

        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }
    function col_visits($values) {
        return ($this->is_downloading()) ? $values->visits : html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
      return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_avg_timespend($values) {
        return ($values->timespend and $values->learners) ? seconds_to_time($values->timespend/$values->learners) : '-';
    }
    function col_avg_visits($values) {
        $avg = ($values->visits and $values->learners) ? intval($values->visits / $values->learners) : 0;
        return ($this->is_downloading()) ? $avg : html_writer::tag("span", $avg, array("class"=>"info-average"));
    }
    function col_learners($values) {
        $learners = intval($values->learners);
        $completed = intval($values->completed);
        $progress = ($learners and $completed)?(($completed/$learners) * 100): 0;
        $progress = round($progress, 0);
        $learnersstr = get_string('learners', 'local_intelliboard');
        $completedstr = get_string('completed', 'local_intelliboard');

        if($this->is_downloading()){
            return intval($values->learners)."/".intval($values->completed);
        }

        $html = html_writer::start_tag(
            "div",array("class"=>"intelliboard-tooltip","title"=>"{$learnersstr}: $learners | {$completedstr}: $completed")
        );
        $html .= html_writer::start_tag("div",array("class"=>"intelliboard-progress xxl"));
        $html .= html_writer::tag(
            "span", "&nbsp;&nbsp;" . intval($values->learners)."/".intval($values->completed) . "&nbsp;&nbsp;", array("style"=>"width:{$progress}%")
        );
        $html .= html_writer::end_tag("div");
        $html .= html_writer::end_tag("div");
        return $html;
    }

    function col_percentage_completed_learners($values) {
        $learners = intval($values->learners);
        $completed = intval($values->completed);
        $progress = ($learners and $completed)?(($completed/$learners) * 100): 0;
        $progress = round($progress, 0);

        if($this->is_downloading()){
            return $progress;
        }

        $html = html_writer::start_tag("div", array("class" => "grade"));
        $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => $progress));
        $html .= html_writer::end_tag("div");
        return $html;
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if($this->is_downloading()){
                return (int)$values->grade;
            }

            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_modules($values) {
        return intval($values->modules);
    }

    function col_course($values) {
        global $CFG;

        return ($this->is_downloading()) ? $values->course : html_writer::link(new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$values->id)), $values->course, array("target"=>"_blank"));
    }
    function col_actions($values) {
        global  $PAGE;

        $elements = [];
        $start = html_writer::start_tag("div",array("style"=>"width:200px; margin: 5px 0;"));

        if(get_config('local_intelliboard', 'table_set_icg_c11')) {
            $elements[] = html_writer::link(
                new moodle_url($PAGE->url, array('action'=>'learners', 'id'=>$values->id, 'search' => '')),
                get_string('learners','local_intelliboard'),
                array(
                    'class' =>'btn btn-default',
                    'title' => get_string('learners','local_intelliboard')
                )
            );
        }

        if(get_config('local_intelliboard', 'table_set_icg_c12')) {
            $elements[] = html_writer::link(
                new moodle_url($PAGE->url, array('action'=>'activities', 'id'=>$values->id, 'search' => '')),
                get_string('activities','local_intelliboard'),
                array(
                    'class' =>'btn btn-default',
                    'title' => get_string('activities','local_intelliboard')
                )
            );
        }

        return $start . implode("&nbsp", $elements) . html_writer::end_tag("div");
    }
}

class intelliboard_activities_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $courseid = 0, $search = '', $mod = 0, $module = 0, $download = 0) {
        global $CFG, $PAGE, $DB, $USER;

        parent::__construct($uniqueid);

        $columns = array();
        $headers = array();

        if(get_config('local_intelliboard', 'table_set_iag_c1')) {
            $columns[] =  'activity';
            $headers[] =  get_string('activity_name','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag_c2')) {
            $columns[] =  'module';
            $headers[] =  get_string('type','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag_c3')) {
            $columns[] =  'completed';
            $headers[] =  get_string('in6','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag_c4')) {
            $columns[] =  'grade';
            $headers[] =  ucfirst(get_string('average_grade','local_intelliboard'));
        }

        if(get_config('local_intelliboard', 'table_set_iag_c5')) {
            $columns[] =  'visits';
            $headers[] =  get_string('visits','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag_c6')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag_c7') && !$download) {
            $columns[] =  'actions';
            $headers[] =  get_string('actions','local_intelliboard');
        }

        if(!$columns) {
            return false;
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('c1'=>$courseid, 'c2'=>$courseid, 'c3'=>$courseid, 'c4'=>$courseid, 'c5'=>$courseid, 'c6'=>$courseid);
        $sql = "";
        if ($mod) {
            $sql .= " AND cm.module IN (1,15,16,17,20,23)";
        }

        if ($module) {
            $sql .= " AND m.id = :moduleid";
            $params['moduleid'] = $module;
        }

        list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);

        $sql_columns = "";
        $modules = $DB->get_records_sql("SELECT m.id, m.name FROM {modules} m WHERE m.visible = 1");
        foreach($modules as $module){
            $sql_columns .= " WHEN m.name='{$module->name}' THEN (SELECT name FROM {".$module->name."} WHERE id = cm.instance)";
        }

        if ($search) {
            $sql .= sprintf(' AND (%s OR %s)',
                $DB->sql_like('m.name', ":activity", false, false),
                $DB->sql_like("CASE $sql_columns ELSE 'none' END", ":activity1", false, false)
            );
            $params['activity'] = "%$search%";
            $params['activity1'] = "%$search%";
        }

        $sql_columns =  ($sql_columns) ? ", CASE $sql_columns ELSE 'none' END AS activity" : "'' AS activity";
        $grade_avg = intelliboard_grade_sql(true);
        $completion = intelliboard_compl_sql("cm.", false);
        $join_group_sql = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'gi.courseid');
        $join_group_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('', 'g.userid');
        $join_group_sql2 = intelliboard_group_aggregation_sql('cm.userid', $USER->id, 'm.course');
        $join_group_sql3 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ctx.instanceid');


        $sql33 = intelliboard_instructor_getcourses('gi.courseid', false, 'g.userid', false, false);
        $sql44 = intelliboard_instructor_getcourses('m.course', false, 'cm.userid', false, false);
        $sql55 = intelliboard_instructor_getcourses('courseid', false, 'userid', false, false);

        $fields = 't.*';

        $from = "(SELECT cm.id,
                         cm.course,
                         m.name AS module,
                         cmc.completed,
                         g.grade,
                         l.visits,
                         l.timespend,
                         '' AS actions
                         $sql_columns
                    FROM {course_modules} cm
                    JOIN {modules} m ON m.id = cm.module
               LEFT JOIN (SELECT gi.iteminstance, gi.itemmodule, $grade_avg AS grade
                            FROM {grade_items} gi
                            JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL
                                 $join_group_sql
                           WHERE gi.itemtype = 'mod' AND gi.courseid = :c1 $sql33
                        GROUP BY gi.iteminstance, gi.itemmodule
                         ) g ON g.iteminstance = cm.instance AND g.itemmodule = m.name
               LEFT JOIN (SELECT cm.coursemoduleid, COUNT(cm.id) AS completed
                            FROM {course_modules_completion} cm
                            JOIN {course_modules} m ON m.id = cm.coursemoduleid AND m.course = :c6
                                 $join_group_sql2
                           WHERE $completion $sql44
                        GROUP BY cm.coursemoduleid
                         ) cmc ON cmc.coursemoduleid = cm.id
               LEFT JOIN (SELECT param, SUM(visits) AS visits, SUM(timespend) AS timespend
                            FROM {local_intelliboard_tracking}
                           WHERE userid IN (SELECT DISTINCT ra.userid
                                              FROM {context} ctx
                                              JOIN {role_assignments} ra ON ctx.id = ra.contextid AND ra.roleid $sql1
                                                   $join_group_sql3
                                             WHERE ctx.instanceid = :c4 AND ctx.contextlevel = 50
                                            ) AND
                                 page='module' $sql55 AND courseid = :c2
                        GROUP BY param
                         ) l ON l.param=cm.id
                   WHERE cm.instance > 0 AND cm.visible = 1 AND cm.course = :c3 $sql
                 ) t";

        $where = "t.id > 0";

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);

        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if($this->is_downloading()){
                return (int)$values->grade;
            }
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_module($values) {
      return get_string('modulename', $values->module);
    }
    function col_completed($values) {
      return intval($values->completed);
    }
    function col_visits($values) {
        return ($this->is_downloading()) ? intval($values->visits) : html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
      return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_activity($values) {
        global $CFG, $PAGE;

        return ($this->is_downloading()) ? $values->activity : html_writer::link(new moodle_url("$CFG->wwwroot/mod/$values->module/view.php", array('id'=>$values->id)), $values->activity, array("target"=>"_blank"));
    }
    function col_actions($values) {
        global  $PAGE;

        return html_writer::link(
            new moodle_url($PAGE->url, ['action' => 'activity', 'cmid' => $values->id, 'id' => $values->course, 'search' => '']),
            get_string('grades','local_intelliboard'),
            ['class' =>'btn btn-default', 'title' => get_string('grades','local_intelliboard')]
        );
    }

    function finish_html() {
        global $OUTPUT;
        if (!$this->started_output) {
            //no data has been added to the table.
            $this->print_nothing_to_display();

        } else {
            // Print empty rows to fill the table to the current pagesize.
            // This is done so the header aria-controls attributes do not point to
            // non existant elements.
            $emptyrow = array_fill(0, count($this->columns), '');
            while ($this->currentrow < $this->pagesize) {
                $this->print_row($emptyrow, 'emptyrow');
            }

            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
            echo html_writer::end_tag('div');
            $this->wrap_html_finish();

            // Paging bar
            if(in_array(TABLE_P_BOTTOM, $this->showdownloadbuttonsat)) {
                echo $this->download_buttons();
            }

            if($this->use_pages) {
                $pagingbar = new paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl);
                $pagingbar->pagevar = $this->request[TABLE_VAR_PAGE];
                $paginationhtml = $OUTPUT->render($pagingbar);
            }

            // pagesize
            $baseurl = $this->baseurl;
            $pagesize = $this->get_page_size();
            $options = [10, 25, 50, 100];

            $options = array_map(function($i) use ($baseurl, $pagesize) {
                $url = clone $baseurl;
                $url->params(['pagesize' => $i]);

                return [
                    'value' => $i,
                    'url' => $url->out(),
                    'selected' => $pagesize == $i,
                ];
            }, $options);

            echo $OUTPUT->render_from_template(
                'local_intelliboard/parts_table_bottom',
                ['page_size_options' => $options, 'pagination_html' => $paginationhtml]
            );
        }
    }
}

class intelliboard_activity_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $cmid = 0, $courseid = 0, $search = '') {
        global $CFG, $PAGE, $DB, $USER;

        parent::__construct($uniqueid);

        $columns = []; $headers = [];

        if(get_config('local_intelliboard', 'table_set_iag1_c1')) {
            $columns[] = 'learner';
            $headers[] = get_string('learner_name','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c2')) {
            $columns[] = 'email';
            $headers[] = get_string('email');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c3')) {
            $columns[] = 'timecompleted';
            $headers[] = get_string('status','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c4')) {
            $columns[] = 'grade';
            $headers[] = get_string('grade','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c5')) {
            $columns[] = 'graded';
            $headers[] = get_string('graded','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c6')) {
            $columns[] =  'visits';
            $headers[] =  get_string('visits','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_iag1_c7')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent','local_intelliboard');
        }

        if(!$columns) {
            return false;
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('cmid'=>$cmid, 'courseid'=>$courseid);
        $sql = "";
        if($search){
            $sql .= sprintf(
                ' AND (%s OR %s OR %s)',
                $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ":search1", false, false),
                $DB->sql_like("CONCAT(u.lastname, ' ', u.firstname)", ":search2", false, false),
                $DB->sql_like("u.email", ":search3", false, false)
            );
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }
        list($sql_roles, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        $grade_single = intelliboard_grade_sql();
        $join_group_sql = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'e.instanceid');
        $join_group_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql(' c.id', 'u.id');

        $sql55 = intelliboard_instructor_getcourses('c.id', false, 'u.id');

        $fields = "ra.id, ra.userid, c.id AS courseid,
            $grade_single AS grade,
            CASE WHEN g.timemodified > 0 THEN g.timemodified ELSE g.timecreated END AS graded,
            cc.timemodified AS timecompleted,
            cc.completionstate,
            u.email,
            CONCAT(u.firstname, ' ', u.lastname) as learner,
            l.timespend, l.visits";
        $from = "{role_assignments} ra
                LEFT JOIN {context} e ON e.id = ra.contextid AND e.contextlevel = 50
                $join_group_sql
                LEFT JOIN {user} u ON u.id = ra.userid
                LEFT JOIN {course} c ON c.id = e.instanceid
                LEFT JOIN {course_modules} cm ON cm.id = :cmid
                LEFT JOIN {modules} m ON m.id = cm.module
                LEFT JOIN {course_modules_completion} cc ON cc.coursemoduleid = cm.id AND cc.userid = ra.userid
                LEFT JOIN {grade_items} gi ON gi.itemtype = 'mod' AND gi.itemmodule = m.name AND gi.iteminstance = cm.instance
                LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
                LEFT JOIN {local_intelliboard_tracking} l ON l.userid = u.id AND l.param = cm.id AND l.page = 'module'
                ";
        $where = "ra.roleid $sql_roles $sql55 AND e.instanceid = :courseid $sql";

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if($this->is_downloading()){
                return (int)$values->grade;
            }
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_timecompleted($values) {
        if ($values->completionstate == 3) {
            return get_string('failed','local_intelliboard');
        } elseif ($values->completionstate == 2) {
           return get_string('passed','local_intelliboard');
        } elseif ($values->timecompleted and $values->completionstate == 1) {
            return get_string('completed_on','local_intelliboard', intelli_date($values->timecompleted));
        } else {
            return get_string('incomplete','local_intelliboard');
        }
    }
    function col_visits($values) {
        return ($this->is_downloading()) ? intval($values->visits) : html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
      return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_graded($values) {
      return ($values->graded) ? intelli_date($values->graded) : '';
    }
    function col_learner($values) {
        global $CFG, $PAGE;

        return ($this->is_downloading()) ? $values->learner : html_writer::link(new moodle_url($PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->courseid)), $values->learner);
    }
    function col_actions($values) {
        global  $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->courseid)), get_string('grades','local_intelliboard'), array('class' =>'btn btn-default', 'title' =>get_string('grades','local_intelliboard')));
    }
}


class intelliboard_learners_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;
    private $course_avg_visits;
    private $course_avg_timespent;

    function __construct($uniqueid, $courseid = 0, $search = '', $download = 0) {
        global $CFG, $PAGE, $DB, $USER;

        parent::__construct($uniqueid);

        $columns = [];
        $headers = [];

        $this->no_sorting('avg_visits');
        $this->no_sorting('avg_timespend');

        if(get_config('local_intelliboard', 'table_set_ilg_c1')) {
            $columns[] = 'learner';
            $headers[] = get_string('learner_name','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c2')) {
            $columns[] = 'email';
            $headers[] = get_string('email');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c3')) {
            $columns[] = 'enrolled';
            $headers[] = get_string('enrolled','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c4')) {
            $columns[] = 'timeaccess';
            $headers[] = get_string('in16','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c5')) {
            $columns[] = 'timecompleted';
            $headers[] = get_string('status','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c6')) {
            $columns[] = 'grade';
            $headers[] = get_string('grade','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c7')) {
            $columns[] = 'progress';
            $headers[] = get_string(
                'completed_activities_resources','local_intelliboard'
            );
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c8')) {
            $columns[] =  'visits';
            $headers[] =  get_string('visits','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c9')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c11')) {
            $columns[] =  'avg_visits';
            $headers[] =  get_string('avg_visits_per_stud','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c12')) {
            $columns[] =  'avg_timespend';
            $headers[] =  get_string('avg_time_spent_per_stud','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg_c10') && !$download) {
            $columns[] =  'actions';
            $headers[] =  get_string('actions','local_intelliboard');
        }

        if(!$columns) {
            return false;
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array(
            'c1' => $courseid, 'c2' => $courseid, 'c3' => $courseid, 'c4' => $courseid, 'c5' => $courseid,
            'c6' => $courseid
        );
        $sql = "";
        if($search){
            $sql .= sprintf(
                ' AND (%s OR %s OR %s)',
                $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ":search1", false, false),
                $DB->sql_like("CONCAT(u.lastname, ' ', u.firstname)", ":search2", false, false),
                $DB->sql_like("u.email", ":search3", false, false)
            );
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }
        list($sqlroles, $sql_params) = $DB->get_in_or_equal(
            explode(',', get_config('local_intelliboard', 'filter11')),
            SQL_PARAMS_NAMED,
            'r'
        );
        $params = array_merge($params,$sql_params);
        $grade_single = intelliboard_grade_sql();
        $completion = intelliboard_compl_sql("cmc.");
        $join_group_sql = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ra.courseid');
        $join_group_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('c.id', 'u.id', 'e.courseid = :c5');
        $join_group_sql1 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ra.course_id');

        $sql33 = intelliboard_instructor_getcourses('c.id', false, 'u.id', false, false);
        $sql .= get_filter_usersql("u.");
        $mainsql = '';

        if (get_config('local_intelliboard', 'instructor_hide_suspended_enrollments')) {
            $mainsql = ' AND enr.status = 0';
        }

        $params = array_merge($params, $sql_params);
        list($sql6, $sql_params) = $DB->get_in_or_equal(
            explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r6'
        );
        $params = array_merge($params, $sql_params);

        $params = array_merge($params, $sql_params);
        list($sql7, $sql_params) = $DB->get_in_or_equal(
            explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r7'
        );
        $params = array_merge($params,$sql_params);

        $fields = "t.*";
        $from = "(SELECT CONCAT(ra.userid,'_',c.id) as id,
                         ra.userid AS userid,
                         c.id as courseid,
                         ra.timemodified as enrolled,
                         ul.timeaccess AS timeaccess,
                         $grade_single AS grade,
                         cc.timecompleted AS timecompleted,
                         CONCAT(u.firstname, ' ', u.lastname) AS learner,
                         u.email,
                         u.firstname AS firstname,
                         u.lastname AS lastname,
                         u.alternatename AS alternatename,
                         u.middlename AS middlename,
                         u.lastnamephonetic AS lastnamephonetic,
                         u.firstnamephonetic AS firstnamephonetic,
                         l.timespend AS timespend,
                         l.visits AS visits,
                         cmc.progress AS progress,
                         '' as actions,
                         '' as avg_timespend,
                         '' as avg_visits
                    FROM (SELECT ra.userid,
                                 cx.instanceid AS courseid,
                                 MIN(ra.timemodified) AS timemodified
                            FROM {role_assignments} ra
                            JOIN {context} cx ON cx.id = ra.contextid AND cx.contextlevel = 50
                           WHERE cx.instanceid = :c6 AND ra.roleid $sqlroles
                        GROUP BY ra.userid, cx.instanceid
                         ) ra
                    JOIN {user} u ON u.id = ra.userid
                    JOIN {course} c ON c.id = ra.courseid
               LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = c.id
               LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = u.id
               LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ra.userid
               LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
               LEFT JOIN (SELECT cmc.userid, COUNT(DISTINCT cmc.id) as progress
                            FROM {course_modules_completion} cmc
                            JOIN {course_modules} cm ON cm.visible = 1 AND cmc.coursemoduleid = cm.id AND
                                                        cm.completion > 0 AND cm.course = :c1
                           WHERE cm.id > 0 {$completion}
                        GROUP BY cmc.userid
               ) cmc ON cmc.userid = u.id

               LEFT JOIN (SELECT t.userid,t.courseid, sum(t.timespend) as timespend, sum(t.visits) as visits
                            FROM {local_intelliboard_tracking} t
                        GROUP BY t.courseid, t.userid
               ) l ON l.courseid = c.id AND l.userid = u.id
                         $join_group_sql
                   WHERE ra.courseid > 0 $sql $sql33) t";
        $where = "1>0"; //for MariaDB

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');

        /** Count course avg visits and avg time spent */
        if (get_config('local_intelliboard', 'table_set_ilg_c12')
            or get_config('local_intelliboard', 'table_set_ilg_c11')
        ) {
            $cache = cache::make('local_intelliboard', 'instructor_course_data');

            if ($cache->has("avg_{$courseid}")) {
                $data = json_decode($cache->get("avg_{$courseid}"));
            } else {
                $data = $DB->get_record_sql(
                    "SELECT c.id,
                        course_avg.spent AS avg_timespend,
                        course_avg.visits AS avg_visits
                   FROM {course} c
                   JOIN (SELECT course_total.courseid,
                                AVG(course_total.spent) AS spent,
                                AVG(course_total.visits) AS visits
                           FROM (SELECT l.courseid, l.userid,
                                        CASE WHEN SUM(l.timespend) IS NULL THEN 0 ELSE SUM(l.timespend) END AS spent,
                                        CASE WHEN SUM(l.visits) IS NULL THEN 0 ELSE SUM(l.visits) END AS visits
                                   FROM (SELECT ue.userid, MIN(ue.status) AS status
                                           FROM {user_enrolments} ue
                                           JOIN {enrol} e ON ue.enrolid = e.id AND e.courseid = :c1
                                       GROUP BY ue.userid
                                        ) enr
                                   JOIN (SELECT ra1.userid, ctx.instanceid AS course_id
                                           FROM {role_assignments} ra1
                                           JOIN {context} ctx ON ctx.id = ra1.contextid AND ctx.contextlevel = 50 AND ctx.instanceid = :c2
                                          WHERE ra1.id > 0 AND ra1.roleid {$sql6}
                                       GROUP BY ra1.userid, ctx.instanceid
                                        ) ra ON ra.userid = enr.userid
                                   JOIN {local_intelliboard_tracking} l ON enr.userid = l.userid
                                   {$join_group_sql1}
                                  WHERE l.courseid = :c3 {$mainsql}
                               GROUP BY l.courseid, l.userid
                                ) course_total
                       GROUP BY course_total.courseid
                        ) course_avg ON course_avg.courseid = c.id
                  WHERE c.id = :c4",
                    $params
                );

                if ($data) {
                    $cachedata = json_encode(['avg_timespend' => $data->avg_timespend, 'avg_visits' => $data->avg_visits]);
                } else {
                    $cachedata = json_encode(['avg_timespend' => 0, 'avg_visits' => 0]);
                }

                $cache->set("avg_{$courseid}", $cachedata);
            }

            if ($data) {
                $this->course_avg_timespent = $data->avg_timespend;
                $this->course_avg_visits = $data->avg_visits;
            } else {
                $this->course_avg_timespent = 0;
                $this->course_avg_visits = 0;
            }
        }
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if($this->is_downloading()){
                return (int)$values->grade;
            }
            $html = html_writer::start_tag("div",array("class"=>"grade"));
            $html .= html_writer::tag("div", "", array("class"=>"circle-progress", "data-percent"=>(int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
     function col_progress($values) {
        return intval($values->progress);
    }
    function col_visits($values) {
        return ($this->is_downloading()) ? intval($values->visits) : html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
      return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_avg_timespend($values) {
        return ($this->course_avg_timespent) ? seconds_to_time($this->course_avg_timespent) : '-';
    }
    function col_avg_visits($values) {
        return ($this->is_downloading()) ? intval($this->course_avg_visits) : html_writer::tag("span", intval($this->course_avg_visits), array("class"=>"info-average"));
    }
    function col_timecompleted($values) {
      return ($values->timecompleted) ? get_string('completed_on','local_intelliboard', intelli_date($values->timecompleted)) : get_string('incomplete','local_intelliboard');
    }
    function col_enrolled($values) {
      return ($values->enrolled) ? intelli_date($values->enrolled) : '';
    }
    function col_timeaccess($values) {
      return ($values->timeaccess) ? intelli_date($values->timeaccess) : '';
    }
    function col_learner($values) {
        global $CFG, $PAGE;

        if($this->is_downloading()){
            return fullname($values);
        }

        return html_writer::link(new moodle_url(
            $PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->courseid)
        ), fullname($values));
    }
    function col_actions($values) {
        global  $PAGE;


        return html_writer::link(new moodle_url($PAGE->url, array('search'=>'','action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->courseid)), get_string('grades','local_intelliboard'), array('class' =>'btn btn-default', 'title' =>get_string('grades','local_intelliboard')));
    }

    function finish_html() {
        global $OUTPUT;
        if (!$this->started_output) {
            //no data has been added to the table.
            $this->print_nothing_to_display();

        } else {
            // Print empty rows to fill the table to the current pagesize.
            // This is done so the header aria-controls attributes do not point to
            // non existant elements.
            $emptyrow = array_fill(0, count($this->columns), '');
            while ($this->currentrow < $this->pagesize) {
                $this->print_row($emptyrow, 'emptyrow');
            }

            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
            echo html_writer::end_tag('div');
            $this->wrap_html_finish();

            // Paging bar
            if(in_array(TABLE_P_BOTTOM, $this->showdownloadbuttonsat)) {
                echo $this->download_buttons();
            }

            if($this->use_pages) {
                $pagingbar = new paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl);
                $pagingbar->pagevar = $this->request[TABLE_VAR_PAGE];
                $paginationhtml = $OUTPUT->render($pagingbar);
            }

            // pagesize
            $baseurl = $this->baseurl;
            $pagesize = $this->get_page_size();
            $options = [10, 25, 50, 100];

            $options = array_map(function($i) use ($baseurl, $pagesize) {
                $url = clone $baseurl;
                $url->params(['pagesize' => $i]);

                return [
                    'value' => $i,
                    'url' => $url->out(),
                    'selected' => $pagesize == $i,
                ];
            }, $options);

            echo $OUTPUT->render_from_template(
                'local_intelliboard/parts_table_bottom',
                ['page_size_options' => $options, 'pagination_html' => $paginationhtml]
            );
        }
    }
}
class intelliboard_learner_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $userid = 0, $courseid = 0, $search = '', $mod = 0, $module = 0) {
        global $CFG, $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns = []; $headers = [];

        if(get_config('local_intelliboard', 'table_set_ilg1_c1')) {
            $columns[] = 'activity';
            $headers[] = get_string('activity_name','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c2')) {
            $columns[] = 'module';
            $headers[] = get_string('type','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c3')) {
            $columns[] = 'grade';
            $headers[] = get_string('grade','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c4')) {
            $columns[] =  'graded';
            $headers[] =  get_string('graded','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c5')) {
            $columns[] = 'timecompleted';
            $headers[] = get_string('status','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c6')) {
            $columns[] =  'visits';
            $headers[] =  get_string('visits','local_intelliboard');
        }

        if(get_config('local_intelliboard', 'table_set_ilg1_c7')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent','local_intelliboard');
        }

        if(!$columns) {
            return false;
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array(
            'u1'=>$userid,
            'u2'=>$userid,
            'u3'=>$userid,
            'c1'=>$courseid,
            'c2'=>$courseid
        );
        $sql = "";

        if ($mod) {
            $sql .= " AND cm.module IN (1,15,16,17,20,23)";
        }

        if ($module) {
            $sql .= " AND m.id = :moduleid";
            $params['moduleid'] = $module;
        }

        $enrolmentjoin = intelliboard_instructor_hide_suspended_enrollments_joinsql('gi.courseid', 'g.userid');

        $sql_columns = "";
        $modules = $DB->get_records_sql("SELECT m.id, m.name FROM {modules} m WHERE m.visible = 1");
        foreach($modules as $module){
            $sql_columns .= " WHEN m.name='{$module->name}' THEN (SELECT name FROM {".$module->name."} WHERE id = cm.instance)";
        }

        if ($search) {
            $sql .= sprintf(' AND (%s OR %s)',
                $DB->sql_like('m.name', ":activity", false, false),
                $DB->sql_like("CASE $sql_columns ELSE 'none' END", ":activity1", false, false)
            );
            $params['activity'] = "%$search%";
            $params['activity1'] = "%$search%";
        }

        $sql_columns =  ($sql_columns) ? ", CASE $sql_columns ELSE 'none' END AS activity" : "'' AS activity";
        $grade_single = intelliboard_grade_sql();
        $completion = intelliboard_compl_sql("cmc.");

        $fields = 't.*';
        $from = "(SELECT cm.id,
                        m.name AS module,
                        cmc.timemodified AS timecompleted,
                        $grade_single AS grade,
                        CASE WHEN g.timemodified > 0 THEN g.timemodified ELSE g.timecreated END AS graded,
                        l.visits,
                        l.timespend
                        $sql_columns
                   FROM {course_modules} cm
              LEFT JOIN {modules} m ON m.id = cm.module
              LEFT JOIN {grade_items} gi ON gi.iteminstance = cm.instance AND gi.itemmodule = m.name AND gi.itemtype = 'mod'
              LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :u1
              LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id $completion AND cmc.userid = :u2
              LEFT JOIN {local_intelliboard_tracking} l ON l.param=cm.id AND l.page='module' AND l.courseid=:c1 AND l.userid= :u3
                        $enrolmentjoin
                  WHERE cm.instance > 0 AND cm.visible = 1 AND cm.course = :c2 $sql) t";
        $where = 't.id > 0';

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_module($values) {
        return get_string('modulename', $values->module);
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if($this->is_downloading()){
                return (int)$values->grade;
            }
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }

    function col_visits($values) {
        return ($this->is_downloading()) ? intval($values->visits) : html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
      return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_timecompleted($values) {
      return ($values->timecompleted) ? get_string('completed_on','local_intelliboard', intelli_date($values->timecompleted)) : get_string('incomplete','local_intelliboard');
    }
    function col_graded($values) {
      return ($values->graded) ? intelli_date($values->graded) : '';
    }
}

class intelliboard_sessions_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $search = '') {
        global $CFG, $PAGE, $DB, $USER;

        parent::__construct($uniqueid);

        $columns = array('session_name');
        $headers = array(get_string('session_name','local_intelliboard'));

        $columns[] = 'session_time';
        $headers[] = get_string('session_time','local_intelliboard');

        $columns[] = 'course';
        $headers[] = get_string('course_name','local_intelliboard');

        $columns[] =  'category';
        $headers[] =  get_string('category','local_intelliboard');

        $columns[] =  'learners';
        $headers[] =  get_string('enrolled_completed_learners','local_intelliboard');

        $columns[] =  'grade';
        $headers[] =  get_string('in21','local_intelliboard');

        $columns[] =  'sections';
        $headers[] =  get_string('sections','local_intelliboard');

        $columns[] =  'modules';
        $headers[] =  get_string('activities_resources','local_intelliboard');

        $columns[] =  'visits';
        $headers[] =  get_string('visits','local_intelliboard');

        $columns[] =  'timespend';
        $headers[] =  get_string('time_spent','local_intelliboard');

        $columns[] =  'actions';
        $headers[] =  get_string('actions','local_intelliboard');

        $this->define_headers($headers);
        $this->define_columns($columns);

        $sql = intelliboard_instructor_getcourses();
        $params = array('userid'=>$USER->id, 'suserid'=>$USER->id);

        if($search){
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }

        list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        list($sql2, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter10')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);

        list($sql3, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        list($sql4, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        list($sql5, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        $grade_avg = intelliboard_grade_sql(true);

        $usersin = "SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = c.id AND il.sessionid = s.id";

        $fields = "s.id, s.name as session_name, s.timestart as session_time, s.timeend,
                c.id as courseid, c.fullname as course,
                c.enablecompletion,
                ca.name AS category,
                (SELECT SUM(l.timespend) FROM {local_intelliboard_tracking} l WHERE l.courseid = c.id AND l.userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.instanceid = c.id AND ctx.contextlevel = 50 AND ra.roleid $sql3)) AS timespend,
                 (SELECT SUM(l.visits) FROM {local_intelliboard_tracking} l WHERE l.courseid = c.id AND l.userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND l.userid IN ($usersin) AND ctx.instanceid = c.id AND ctx.contextlevel = 50 AND ra.roleid $sql4)) AS visits,
                (SELECT COUNT(DISTINCT ra.userid) FROM {role_assignments} ra
                    LEFT JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50
                    WHERE ra.roleid $sql1 AND ctx.instanceid = c.id AND ra.userid IN ($usersin)) AS learners,
                (SELECT $grade_avg
                    FROM {grade_items} gi, {grade_grades} g
                    WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid IN ($usersin)) AS grade,
                (SELECT COUNT(DISTINCT userid) FROM {course_completions} WHERE timecompleted > 0 AND course = c.id AND userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.instanceid = c.id AND ctx.contextlevel = 50  AND userid IN ($usersin) AND ra.roleid $sql5)) AS completed,
                (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND course = c.id) AS modules,
                (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections,
                '' as actions";
        $from = " {local_intellicart_sessions} s
                LEFT JOIN {local_intellicart_products} ip ON ip.id = s.productid
                LEFT JOIN {local_intellicart_relations} ic ON ic.productid = ip.id AND ic.type = 'course'
                LEFT JOIN {course} c ON c.id = ic.instanceid AND ic.type = 'course'
                LEFT JOIN {course_categories} ca ON ca.id = c.category";
        $where = "s.id > 0 AND s.id IN (SELECT instanceid FROM {local_intellicart_users} WHERE userid = :suserid AND type = 'sessioninstructor' AND status = 1) $sql";
        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);

        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }
    function col_visits($values) {
        return html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_session_time($values) {

        if (userdate($values->session_time, '%Y/%m/%d') != userdate($values->timeend, '%Y/%m/%d')) {
            $time = userdate($values->session_time, get_string('strftimedate', 'langconfig')) . ', ' .
                    userdate($values->session_time, get_string('strftimetime', 'langconfig')) . ' - ' .
                    userdate($values->timeend, get_string('strftimedate', 'langconfig')) . ', ' .
                    userdate($values->timeend, get_string('strftimetime', 'langconfig'));
        } else {
            $time = userdate($values->session_time, get_string('strftimedate', 'langconfig')) . ', ' .
                    userdate($values->session_time, get_string('strftimetime', 'langconfig')) . ' - ' .
                    userdate($values->timeend, get_string('strftimetime', 'langconfig'));
        }

        return $time;
    }
    function col_timespend($values) {
        return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_learners($values) {
        $learners = intval($values->learners);
        $completed = intval($values->completed);
        $progress = ($learners and $completed)?(($completed/$learners) * 100): 0;
        $progress = round($progress, 0);

        $html = html_writer::start_tag("div",array("class"=>"intelliboard-tooltip","title"=>"Learners: $learners | Completed: $completed"));
        $html .= html_writer::start_tag("div",array("class"=>"intelliboard-progress xxl"));
        $html .= html_writer::tag("span", "&nbsp;&nbsp;" . intval($values->learners)."/".intval($values->completed) . "&nbsp;&nbsp;", array("style"=>"width:{$progress}%"));
        $html .= html_writer::end_tag("div");
        $html .= html_writer::end_tag("div");
        return $html;
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_modules($values) {
        return intval($values->modules);
    }

    function col_course($values) {
        global $CFG;

        return html_writer::link(new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$values->id)), $values->course, array("target"=>"_blank"));
    }
    function col_actions($values) {
        global  $PAGE;

        $html = html_writer::start_tag("div",array("style"=>"width:200px; margin: 5px 0;"));
        $html .= html_writer::link(new moodle_url($PAGE->url, array('action'=>'learners', 'id'=>$values->id, 'courseid'=>$values->courseid)), get_string('learners','local_intelliboard'), array('class' =>'btn btn-default', 'title' => get_string('learners','local_intelliboard')));
        $html .= "&nbsp";
        $html .= html_writer::link(new moodle_url($PAGE->url, array('action'=>'activities', 'id'=>$values->id, 'courseid'=>$values->courseid)), get_string('activities','local_intelliboard'), array('class' =>'btn btn-default', 'title' => get_string('activities','local_intelliboard')));
        $html .= html_writer::end_tag("div");
        return $html;
    }
}

class intelliboard_sessions_activities_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $sessionid = 0, $courseid = 0, $search = '', $mod = 0) {
        global $CFG, $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns = array('activity');
        $headers = array(get_string('activity_name','local_intelliboard'));

        $columns[] =  'module';
        $headers[] =  get_string('type','local_intelliboard');

        $columns[] =  'completed';
        $headers[] =  get_string('in6','local_intelliboard');

        $columns[] =  'grade';
        $headers[] =  ucfirst(get_string('average_grade','local_intelliboard'));

        $columns[] =  'visits';
        $headers[] =  get_string('visits','local_intelliboard');

        $columns[] =  'timespend';
        $headers[] =  get_string('time_spent','local_intelliboard');

        $columns[] =  'actions';
        $headers[] =  get_string('actions','local_intelliboard');

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('c1'=>$courseid, 'c2'=>$courseid, 'c3'=>$courseid, 'c4'=>$courseid);
        $sql = "";
        if ($search) {
            $sql .= " AND " . $DB->sql_like('m.name', ":activity", false, false);
            $params['activity'] = "%$search%";
        }
        if ($mod) {
            $sql .= " AND cm.module IN (1,15,16,17,20,23)";
        }
        list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);

        $params['sid1'] = $sessionid;
        $params['csid1'] = $courseid;
        $params['sid2'] = $sessionid;
        $params['csid2'] = $courseid;
        $params['sid3'] = $sessionid;
        $params['csid3'] = $courseid;

        $sql_columns = "";
        $modules = $DB->get_records_sql("SELECT m.id, m.name FROM {modules} m WHERE m.visible = 1");
        foreach($modules as $module){
            $sql_columns .= " WHEN m.name='{$module->name}' THEN (SELECT name FROM {".$module->name."} WHERE id = cm.instance)";
        }
        $sql_columns =  ($sql_columns) ? ", CASE $sql_columns ELSE 'none' END AS activity" : "'' AS activity";
        $grade_avg = intelliboard_grade_sql(true);
        $completion = intelliboard_compl_sql("", false);


        $usersin = "SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid AND il.sessionid = :sid";

        $fields = "
                cm.id,
                cm.course,
                m.name as module,
                cmc.completed,
                g.grade,
                l.visits,
                l.timespend,
                '' as actions
                $sql_columns";

        $from = "{course_modules} cm
                LEFT JOIN {modules} m ON m.id = cm.module
                LEFT JOIN (SELECT gi.iteminstance, gi.itemmodule, $grade_avg AS grade FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'mod' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = :c1 AND g.userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid1 AND il.sessionid = :sid1) GROUP BY gi.iteminstance, gi.itemmodule) as g ON g.iteminstance = cm.instance AND g.itemmodule = m.name
                LEFT JOIN (SELECT coursemoduleid, COUNT(id) AS completed FROM {course_modules_completion} WHERE $completion AND userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid2 AND il.sessionid = :sid2) GROUP BY coursemoduleid) cmc ON cmc.coursemoduleid = cm.id
                LEFT JOIN (SELECT param, SUM(visits) AS visits, SUM(timespend) AS timespend FROM {local_intelliboard_tracking} WHERE page='module' AND courseid = :c2 AND userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.instanceid = :c4 AND ctx.contextlevel = 50 AND ra.roleid $sql1) AND userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid3 AND il.sessionid = :sid3) GROUP BY param) l ON l.param=cm.id";
        $where = "cm.visible = 1 AND cm.course = :c3 $sql";

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);

        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_module($values) {
        return get_string('modulename', $values->module);
    }
    function col_completed($values) {
        return intval($values->completed);
    }
    function col_visits($values) {
        return html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
        return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_activity($values) {
        global $CFG, $PAGE;

        return html_writer::link(new moodle_url("$CFG->wwwroot/mod/$values->module/view.php", array('id'=>$values->id)), $values->activity, array("target"=>"_blank"));
    }
    function col_actions($values) {
        global  $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('action'=>'activity', 'cmid'=>$values->id, 'id'=>$values->course)), get_string('grades','local_intelliboard'), array('class' =>'btn btn-default', 'title' =>get_string('grades','local_intelliboard')));
    }
}

class intelliboard_sessions_learners_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $sessionid = 0, $courseid = 0, $search = '') {
        global $CFG, $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns[] = 'learner';
        $headers[] = get_string('learner_name','local_intelliboard');

        $columns[] = 'email';
        $headers[] = get_string('email');

        $columns[] = 'enrolled';
        $headers[] = get_string('enrolled','local_intelliboard');

        $columns[] = 'timeaccess';
        $headers[] = get_string('in16','local_intelliboard');

        $columns[] = 'timecompleted';
        $headers[] = get_string('status','local_intelliboard');

        $columns[] = 'grade';
        $headers[] = get_string('grade','local_intelliboard');

        $columns[] = 'progress';
        $headers[] = get_string('completed_activities_resources','local_intelliboard');

        $columns[] =  'visits';
        $headers[] =  get_string('visits','local_intelliboard');

        $columns[] =  'timespend';
        $headers[] =  get_string('time_spent','local_intelliboard');

        $columns[] =  'actions';
        $headers[] =  get_string('actions','local_intelliboard');

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('c1'=>$courseid, 'c2'=>$courseid, 'sessionid' => $sessionid);
        $sql = "";
        if($search){
            $sql .= " AND " . $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ":learner", false, false);
            $params['learner'] = "%$search%";
        }
        list($sql_roles, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        $grade_single = intelliboard_grade_sql();
        $completion = intelliboard_compl_sql("cmc.");

        $fields = "ra.id, ra.userid, c.id as courseid,
            ra.timemodified as enrolled,
            ul.timeaccess,
            $grade_single AS grade,
            cc.timecompleted,
            u.email,
            CONCAT(u.firstname, ' ', u.lastname) as learner, l.timespend, l.visits, cmc.progress,
            il.sessionid, '' as actions";
        $from = "{role_assignments} ra
                LEFT JOIN {context} e ON e.id = ra.contextid AND e.contextlevel = 50
                LEFT JOIN {user} u ON u.id = ra.userid
                LEFT JOIN {course} c ON c.id = e.instanceid
                LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = u.id
                LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ra.userid
                LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = c.id
                LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
                LEFT JOIN (SELECT cmc.userid, COUNT(DISTINCT cmc.id) as progress FROM {course_modules_completion} cmc, {course_modules} cm WHERE cm.visible = 1 AND cmc.coursemoduleid = cm.id $completion AND cm.completion > 0 AND cm.course = :c1 GROUP BY cmc.userid) cmc ON cmc.userid = u.id
                LEFT JOIN {enrol_intellicart} ei ON ei.courseid = c.id AND ei.userid = u.id AND ei.unenroldate = 0
                LEFT JOIN {local_intellicart_logs} il ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid AND il.type = 'product'
                LEFT JOIN (SELECT t.userid,t.courseid, sum(t.timespend) as timespend, sum(t.visits) as visits FROM
                    {local_intelliboard_tracking} t GROUP BY t.courseid, t.userid) l ON l.courseid = c.id AND l.userid = u.id";
        $where = "ra.roleid $sql_roles AND e.instanceid = :c2 AND il.sessionid = :sessionid $sql";

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            $html = html_writer::start_tag("div",array("class"=>"grade"));
            $html .= html_writer::tag("div", "", array("class"=>"circle-progress", "data-percent"=>(int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_progress($values) {
        return intval($values->progress);
    }
    function col_visits($values) {
        return html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
        return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_timecompleted($values) {
        return ($values->timecompleted) ? get_string('completed_on','local_intelliboard', intelli_date($values->timecompleted)) : get_string('incomplete','local_intelliboard');
    }
    function col_enrolled($values) {
        return ($values->enrolled) ? intelli_date($values->enrolled) : '';
    }
    function col_timeaccess($values) {
        return ($values->timeaccess) ? intelli_date($values->timeaccess) : '';
    }
    function col_learner($values) {
        global $CFG, $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'courseid'=>$values->courseid, 'id'=>$values->sessionid)), $values->learner);
    }
    function col_actions($values) {
        global  $PAGE;


        return html_writer::link(new moodle_url($PAGE->url, array('search'=>'','action'=>'learner', 'userid'=>$values->userid, 'courseid'=>$values->courseid, 'id'=>$values->sessionid)), get_string('grades','local_intelliboard'), array('class' =>'btn btn-default', 'title' =>get_string('grades','local_intelliboard')));
    }
}

class intelliboard_sessions_activity_grades_table extends local_intelliboard_intelli_table {
    public $scale_real;

    function __construct($uniqueid, $cmid = 0, $sessionid = 0, $courseid = 0, $search = '') {
        global $CFG, $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns[] = 'learner';
        $headers[] = get_string('learner_name','local_intelliboard');

        $columns[] = 'email';
        $headers[] = get_string('email');

        $columns[] = 'timecompleted';
        $headers[] = get_string('status','local_intelliboard');

        $columns[] = 'grade';
        $headers[] = get_string('grade','local_intelliboard');

        $columns[] = 'graded';
        $headers[] = get_string('graded','local_intelliboard');

        $columns[] =  'visits';
        $headers[] =  get_string('visits','local_intelliboard');

        $columns[] =  'timespend';
        $headers[] =  get_string('time_spent','local_intelliboard');

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('cmid'=>$cmid, 'sessionid'=>$sessionid, 'courseid'=>$courseid);
        $sql = "";
        if($search){
            $sql .= " AND " . $DB->sql_like('u.firstname', ":firstname", false, false);
            $params['firstname'] = "%$search%";
        }
        list($sql_roles, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
        $params = array_merge($params,$sql_params);
        $grade_single = intelliboard_grade_sql();

        $fields = "ra.id, ra.userid, c.id AS courseid,
            $grade_single AS grade,
            CASE WHEN g.timemodified > 0 THEN g.timemodified ELSE g.timecreated END AS graded,
            cc.timemodified AS timecompleted,
            cc.completionstate,
            u.email,
            CONCAT(u.firstname, ' ', u.lastname) as learner,
            l.timespend, l.visits, il.sessionid";
        $from = "{role_assignments} ra
                LEFT JOIN {context} e ON e.id = ra.contextid AND e.contextlevel = 50
                LEFT JOIN {user} u ON u.id = ra.userid
                LEFT JOIN {course} c ON c.id = e.instanceid
                LEFT JOIN {course_modules} cm ON cm.id = :cmid
                LEFT JOIN {modules} m ON m.id = cm.module
                LEFT JOIN {course_modules_completion} cc ON cc.coursemoduleid = cm.id AND cc.userid = ra.userid
                LEFT JOIN {grade_items} gi ON gi.itemtype = 'mod' AND gi.itemmodule = m.name AND gi.iteminstance = cm.instance
                LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
                LEFT JOIN {local_intelliboard_tracking} l ON l.userid = u.id AND l.param = cm.id AND l.page = 'module'
                LEFT JOIN {enrol_intellicart} ei ON ei.courseid = c.id AND ei.userid = u.id AND ei.unenroldate = 0
                LEFT JOIN {local_intellicart_logs} il ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid AND il.type = 'product' ";
        $where = "ra.roleid $sql_roles AND e.instanceid = :courseid AND il.sessionid = :sessionid $sql";

        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            $html = html_writer::start_tag("div", array("class" => "grade"));
            $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
            $html .= html_writer::end_tag("div");
            return $html;
        }
    }
    function col_timecompleted($values) {
        if ($values->completionstate == 3) {
            return get_string('failed','local_intelliboard');
        } elseif ($values->completionstate == 2) {
            return get_string('passed','local_intelliboard');
        } elseif ($values->timecompleted and $values->completionstate == 1) {
            return get_string('completed_on','local_intelliboard', intelli_date($values->timecompleted));
        } else {
            return get_string('incomplete','local_intelliboard');
        }
    }
    function col_visits($values) {
        return html_writer::tag("span", intval($values->visits), array("class"=>"info-average"));
    }
    function col_timespend($values) {
        return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }
    function col_graded($values) {
        return ($values->graded) ? intelli_date($values->graded) : '';
    }
    function col_learner($values) {
        global $CFG, $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->sessionid, 'courseid'=>$values->courseid)), $values->learner);
    }
    function col_actions($values) {
        global  $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('action'=>'learner', 'userid'=>$values->userid, 'id'=>$values->sessionid, 'courseid'=>$values->courseid)), get_string('grades','local_intelliboard'), array('class' =>'btn btn-default', 'title' =>get_string('grades','local_intelliboard')));
    }
}
