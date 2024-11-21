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
 * @website    http://intelliboard.net/
 */

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/gradelib.php');

class intelliboard_courses_grades_table extends table_sql {

    function __construct($uniqueid, $userid = 0, $search = '') {
        global $PAGE, $DB;

        parent::__construct($uniqueid);

        $headers = array(get_string('course_name', 'local_intelliboard'));
        $columns = array('course');
        if(get_config('local_intelliboard', 't23')){
            $columns[] =  'startdate';
            $headers[] =  get_string('course_start_date', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't24')){
           $columns[] =  'timemodified';
           $headers[] =  get_string('enrolled_date', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't25')){
           $columns[] =  'average';
           $headers[] =  get_string('progress', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't26')){
           $columns[] =  'letter';
           $headers[] =  get_string('letter', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't27')){
           $columns[] =  'completedmodules';
           $headers[] =  get_string('completed_activities', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't28')){
           $columns[] =  'grade';
           $headers[] =  get_string('score', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't29')){
           $columns[] =  'timecompleted';
           $headers[] =  get_string('course_completion_status', 'local_intelliboard');
        }
        if (!optional_param('download', '', PARAM_ALPHA)) {
          $columns[] =  'actions';
          $headers[] =  get_string('activity_grades', 'local_intelliboard');
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $params = array('userid'=>$userid);
        $sql = "";
        if($search){
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }
        $grade_single = intelliboard_grade_sql(false, null, 'g.',0, 'gi.',true);
        $grade_avg = intelliboard_grade_sql(true, null, 'g.',0, 'gi.',true);
        $completion = intelliboard_compl_sql("cmc.");
        $sql2 = (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";
        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $sql2 .=  " AND c.containertype = 'container_course'";
        }
        $fields = "c.id, c.fullname AS course, c.timemodified, c.startdate, c.enablecompletion,
                   cri.gradepass, {$grade_single} AS grade, gc.average, cc.timecompleted, m.modules,
                   cm.completedmodules, '' AS actions, '' AS letter";

        $from = "(SELECT DISTINCT c.id, c.fullname, c.startdate, c.enablecompletion, MIN(ue.timemodified) AS timemodified, ue.userid
                    FROM {user_enrolments} ue, {enrol} e, {course} c
                   WHERE ue.userid = :userid  AND ue.status = 0 AND e.id = ue.enrolid AND e.status = 0 AND c.id = e.courseid $sql2
                GROUP BY c.id, ue.userid
                 ) c
       LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = c.userid
       LEFT JOIN (SELECT course, count(id) as modules
                    FROM {course_modules}
                   WHERE visible = 1 AND completion > 0
                GROUP BY course
                 ) m ON m.course = c.id
       LEFT JOIN (SELECT cm.course, cmc.userid, count(cmc.id) as completedmodules
                    FROM {course_modules} cm, {course_modules_completion} cmc
                   WHERE cm.id = cmc.coursemoduleid {$completion} AND cm.visible = 1 AND cm.completion > 0 AND cm.instance > 0
                GROUP BY cm.course, cmc.userid
                 ) cm ON cm.course = c.id AND cm.userid = c.userid
       LEFT JOIN {course_completion_criteria} as cri ON cri.course = c.id AND cri.criteriatype = 6
       LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
       LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = c.userid
       LEFT JOIN (SELECT gi.courseid, {$grade_avg} AS average
                    FROM {grade_items} gi, {grade_grades} g
                   WHERE gi.hidden <> 1 AND
                         gi.itemtype = 'course' AND g.itemid = gi.id AND
                         g.finalgrade IS NOT NULL GROUP BY gi.courseid
                 ) as gc ON gc.courseid = c.id";
        $where = "c.id > 0 {$sql}";
        $this->set_sql($fields, $from, $where, $params);
        $this->define_baseurl($PAGE->url);
    }
    function col_average($values) {
        $gade = intval($values->grade);
        $average = intval($values->average);
        $goal = intval($values->gradepass);

        if (!optional_param('download', '', PARAM_ALPHA)) {
          $title = get_string('current_grade', 'local_intelliboard') . ":$gade";
          if (get_config('local_intelliboard', 't40')) {
              $title .= " | " . get_string('class_average', 'local_intelliboard') . ":$average";
          }
          if(get_config('local_intelliboard', 't39')) {
              $title .= " | " . get_string('goal_grade', 'local_intelliboard') . ":$goal";
          }
          $html = html_writer::start_tag("div",array("class"=>"info-progress","title"=>$title));
          $html .= html_writer::tag("span", get_string('current_grade', 'local_intelliboard') . ":$gade |", array("class"=>"current","style"=>"width:$gade%"));
          if($average and get_config('local_intelliboard', 't40')){
              $html .= html_writer::tag("span", get_string('class_average', 'local_intelliboard') . ":$average |", array("class"=>"average","style"=>"width:$average%"));
          }
          if($goal and get_config('local_intelliboard', 't39')){
              $html .= html_writer::tag("span", get_string('goal_grade', 'local_intelliboard') . ":$goal", array("class"=>"goal","style"=>"width:$goal%"));
          }
          $html .= html_writer::end_tag("div");
        } else {
          $html = get_string('current_grade', 'local_intelliboard') . ":$gade | " . get_string('class_average', 'local_intelliboard') . ":$average | " . get_string('goal_grade', 'local_intelliboard') . ":$goal";
        }
        return $html;
    }

    function col_startdate($values) {
        return  ($values->startdate) ? intelli_date($values->startdate) : "";
    }
    function col_timecompleted($values) {
        if(!$values->enablecompletion){
            return get_string('completion_is_not_enabled', 'local_intelliboard');
        }
        return  ($values->timecompleted) ? get_string('completed_on', 'local_intelliboard', intelli_date($values->timecompleted)) : get_string('incomplete', 'local_intelliboard');
    }
    function col_grade($values) {
        if (!optional_param('download', '', PARAM_ALPHA)) {
          $html = html_writer::start_tag("div",array("class"=>"grade"));
          $html .= html_writer::tag("div", "", array("class"=>"circle-progress", "data-percent"=>(int)$values->grade));
          $html .= html_writer::end_tag("div");
        } else {
          $html = (int)$values->grade;
        }
        return $html;
    }
    function col_completedmodules($values) {
        return intval($values->completedmodules)."/".intval($values->modules);
    }
    function col_letter($values) {
        $letter = '';
        $context = context_course::instance($values->id,IGNORE_MISSING);
        $letters = grade_get_letters($context);
        foreach($letters as $lowerboundary=>$value){
            if($values->grade >= $lowerboundary){
                $letter = $value;
                break;
            }
        }
        return $letter;
    }
    function col_timemodified($values) {
      return ($values->timemodified) ? intelli_date($values->timemodified) : '';
    }
    function col_course($values) {
        global $CFG;
        if (!optional_param('download', '', PARAM_ALPHA)) {
          return html_writer::link(new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$values->id)), format_string($values->course), array("target"=>"_blank"));
        } else {
          return format_string($values->course);
        }
    }
    function col_actions($values) {
        global $PAGE;

        return html_writer::link(new moodle_url($PAGE->url, array('id'=>$values->id)), get_string('activities', 'local_intelliboard'), array('class' =>'btn'));
    }
}

class intelliboard_activities_grades_table extends table_sql {
    public $scale_real;

    function __construct($uniqueid, $userid = 0, $courseid = 0, $search = '', $mod = 0) {
        global $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns = array('itemname');
        $headers = array(get_string('activity_name', 'local_intelliboard'));

        if(get_config('local_intelliboard', 't43')){
            $columns[] =  'itemmodule';
            $headers[] =  get_string('type', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't44')){
            $columns[] =  'grade';
            $headers[] =  get_string('score', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't45')){
            $columns[] =  'timepoint';
            $headers[] =  get_string('graded', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't46')){
            $columns[] =  'timecompleted';
            $headers[] =  get_string('completed', 'local_intelliboard');
        }if(get_config('local_intelliboard', 't54')) {
            $columns[] =  'timespend';
            $headers[] =  get_string('time_spent','local_intelliboard');
        }

        $this->define_headers($headers);
        $this->define_columns($columns);

        $hiddencoursemodules = $this->get_hidden_coursemodules($courseid, $userid);
        $coursemodulesfilter = $DB->get_in_or_equal($hiddencoursemodules, SQL_PARAMS_NAMED, 'cmp1', false);

        $sql = "";
        $params = array();
        if ($mod) {
            $sql .= " AND cm.module IN (1,15,16,17,20,23)";
        }
        if ($search) {
            $sql .= " AND " . $DB->sql_like('gi.itemname', ":itemname", false, false);
            $params['itemname'] = "%$search%";
        }
        $params['userid1'] = $userid;
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;
        $params['courseid'] = $courseid;

        $grade_single = intelliboard_grade_sql();
        $completion = intelliboard_compl_sql("cmc.");

        $fields = "gi.id, gi.itemname, cm.id as cmid, gi.itemmodule, cmc.timemodified as timecompleted, $grade_single AS grade,
                   CASE WHEN g.timemodified > 0 THEN g.timemodified ELSE g.timecreated END AS timepoint, lit.timespend";
        $from = "     {grade_items} gi
            LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid1
            LEFT JOIN {modules} m ON m.name = gi.itemmodule
            LEFT JOIN {course_modules} cm ON cm.instance = gi.iteminstance AND cm.module = m.id
            LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id $completion AND cmc.userid = :userid2
            LEFT JOIN {local_intelliboard_tracking} lit ON lit.userid=:userid3 AND lit.courseid=gi.courseid AND lit.page='module' AND lit.param=cm.id ";

        $where = "gi.hidden <> 1 AND gi.courseid = :courseid AND gi.itemtype = 'mod' AND cm.visible = 1 AND cm.instance > 0 AND
                  cm.id {$coursemodulesfilter[0]} $sql";

        $this->set_sql($fields, $from, $where, array_merge($params, $coursemodulesfilter[1]));
        $this->define_baseurl($PAGE->url);
        $this->scale_real = get_config('local_intelliboard', 'scale_real');
    }

    function col_grade($values) {
        if($this->scale_real>0){
            return $values->grade;
        }else{
            if (!optional_param('download', '', PARAM_ALPHA)) {
              $html = html_writer::start_tag("div", array("class" => "grade"));
              $html .= html_writer::tag("div", "", array("class" => "circle-progress", "data-percent" => (int)$values->grade));
              $html .= html_writer::end_tag("div");
              return $html;
            } else {
              return (int)$values->grade;
            }
        }
    }


    function col_timecompleted($values) {
      return ($values->timecompleted) ? get_string('completed_on', 'local_intelliboard', intelli_date($values->timecompleted)) : get_string('incomplete', 'local_intelliboard');
    }
    function col_timepoint($values) {
      return ($values->timepoint) ? intelli_date($values->timepoint) : '';
    }
    function col_itemname($values) {
        global $CFG;
        if (!optional_param('download', '', PARAM_ALPHA)) {
          return html_writer::link(new moodle_url("$CFG->wwwroot/mod/$values->itemmodule/view.php", array('id'=>$values->cmid)), format_string($values->itemname), array("target"=>"_blank"));
        } else {
          return format_string($values->itemname);
        }
    }
    function col_timespend($values) {
        return ($values->timespend) ? seconds_to_time($values->timespend) : '-';
    }

    private function get_hidden_coursemodules($courseid, $userid) {
        $hiddencoursemodules = [];
        $modinfo = \get_fast_modinfo($courseid, $userid);

        foreach ($modinfo->get_cms() as $cmobj) {
            if ((!$cmobj->uservisible && !$cmobj->availableinfo) || !empty($cmobj->availableinfo)) {
                $hiddencoursemodules[] = $cmobj->id;
            }
        }

        if (!$hiddencoursemodules) {
            return [-1];
        }

        return $hiddencoursemodules;
    }
}

/**
 * IntelliCart custom tables.
 */
class intelliboard_custom_intellicart_table extends table_sql {

    public $context;
    public $page;

    // Statuses for display payment methods.
    public $ordersstatuses = [
        \local_intellicart\payment::STATUS_COMPLETED,
        \local_intellicart\payment::STATUS_PENDING,
        \local_intellicart\payment::STATUS_SUSPENDED,
        \local_intellicart\payment::STATUS_REFUND,
        \local_intellicart\payment::STATUS_WAITING,
        \local_intellicart\payment::STATUS_REFUNDPARTIAL,
        \local_intellicart\payment::STATUS_REJECTED,
        \local_intellicart\payment::STATUS_INCART,
        \local_intellicart\payment::STATUS_DELETED,
        \local_intellicart\payment::STATUS_EXPIRED,
    ];

    // Statuses for display payment methods.
    public $activeordersstatuses = [
        \local_intellicart\payment::STATUS_COMPLETED,
        \local_intellicart\payment::STATUS_PENDING,
        \local_intellicart\payment::STATUS_SUSPENDED,
        \local_intellicart\payment::STATUS_REFUND,
        \local_intellicart\payment::STATUS_WAITING,
        \local_intellicart\payment::STATUS_REFUNDPARTIAL,
        \local_intellicart\payment::STATUS_REJECTED
    ];

    public function __construct($uniqueid) {
        global $PAGE;

        parent::__construct($uniqueid);
        $this->page = $PAGE;
        $this->context = \context_system::instance();
    }

    /**
     * @param $value
     * @return string
     * @throws coding_exception
     */
    public function format_date($value) {
        return (!empty($value)) ? userdate($value, get_string('strftimedatefullshort', 'langconfig')) : '-';
    }

    /**
     * @param $value
     * @param null $format
     * @return string
     * @throws coding_exception
     */
    public function format_datetime($value, $format = null) {
        $format = ($format) ? $format : get_string('strftimedatetime', 'langconfig');
        return ($value) ? userdate($value, $format) : '-';
    }

    /**
     * @param $value
     * @param $currency
     * @return string
     */
    public function format_amount($value, $currency) {
        return ($value && $value != 'NULL') ? $currency.format_float($value, 2, true) : '-';
    }

    /**
     * @param $value
     * @return string
     */
    public function format_notempty($value) {
        return ($value && $value != 'NULL') ? $value : '-';
    }

    /**
     * @param $values
     * @param $download
     * @return false|mixed|object|string|null
     * @throws dml_exception
     */
    public function get_currency($values, $download) {

        if (get_config('local_intellicart', 'enablemulticurrency') and
            get_config('local_intellicart', 'displayusercurrencyinreports') and
            !empty($values->currency)) {

            return ($download) ? $values->currency : \local_intellicart\payment::get_current_currency_code($values->currency);
        }

        $type = ($download) ? 'code' : 'symbol';
        return \local_intellicart\payment::get_currency($type);
    }

    /**
     * @param $values
     * @param $fieldname
     * @return string
     * @throws dml_exception
     */
    public function get_price($values, $fieldname) {

        if (get_config('local_intellicart', 'enablemulticurrency') and
            get_config('local_intellicart', 'displayusercurrencyinreports') and
            !empty($values->currencyrate)) {

            $value = str_replace(',', '.', $values->{$fieldname});
            return \local_intellicart\helpers\PriceHelper::formatted_price($value,
                $values->currencyrate, false, true, false, true);
        }

        return $values->{$fieldname};
    }
}

/**
 * IntelliCart Sales table.
 */
class intelliboard_user_orders_table extends intelliboard_custom_intellicart_table {

    public $currency = '';
    public $download = false;

    function __construct($uniqueid, $search) {
        global $USER, $PAGE, $DB;

        parent::__construct($uniqueid);
        $stringingnvoice = get_string('invoice', 'local_intellicart');

        $columns = array(
            'orderid', 'products', 'timeupdated', 'amountpaid', 'subtotal', 'discount', 'tax', 'total', 'refund', 'status', 'paymenttype'
        );
        $headers = array(
            get_string('orderid', 'local_intellicart'),
            get_string('products', 'local_intellicart'),
            get_string('created', 'local_intellicart'),
            get_string('amountpaid', 'local_intellicart'),
            get_string('subtotal', 'local_intellicart'),
            get_string('discount', 'local_intellicart'),
            get_string('taxes', 'local_intellicart'),
            get_string('total', 'local_intellicart'),
            get_string('refunded', 'local_intellicart'),
            get_string('status', 'local_intellicart'),
            get_string('paymenttype', 'local_intellicart'),
        );

        if (\local_intellicart\checkout::display_bililngtypes()) {
            $columns[] = 'billingtype';
            $headers[] = get_string('billingtype', 'local_intellicart');
        }

        if (!\local_intellicart\taxes::enabled()) {
            // Remove taxes column.
            $taxescolumn_key = array_search('tax', $columns);
            unset($columns[$taxescolumn_key]); unset($headers[$taxescolumn_key]);
            $columns = array_values($columns); $headers = array_values($headers);
        }

        if (!get_config('local_intellicart', 'enablerefunds')) {
            // Remove refunded column.
            $refundcolumnkey = array_search('refund', $columns);
            unset($columns[$refundcolumnkey]);
            unset($headers[$refundcolumnkey]);
            $columns = array_values($columns);
            $headers = array_values($headers);
        }

        array_push($columns, 'actions');
        array_push($headers, get_string('actions', 'local_intelliboard'));

        $this->define_headers($headers);
        $this->define_columns($columns);

        $this->sortable(false);
        $this->is_collapsible = false;

        $sqlparams = [
            'userid' => $USER->id,
        ];

        // Prepare correct statuses.
        $activestatusin = $DB->get_in_or_equal($this->activeordersstatuses, SQL_PARAMS_NAMED);
        $sqlparams += $activestatusin[1];
        $where = ' AND ch.payment_status ' . $activestatusin[0];

        if ($search) {
            $where .= ' AND '.$DB->sql_like('ch.item_name', ':search1', false, false, false);
            $sqlparams['search1'] = "%{$search}%";
        }

        // Prepare status column.
        $statusfield = "(CASE ch.payment_status ";
        foreach ($this->ordersstatuses as $status) {
            $statusfield .= "WHEN '$status' THEN '" . get_string('status_' . $status, 'local_intellicart') . "' ";
        }
        $statusfield .= "ELSE '" . get_string('undefined', 'local_intellicart') . "'" . "END) AS status";

        // Prepare billing type column.
        $statusin = $DB->get_in_or_equal($this->ordersstatuses, SQL_PARAMS_NAMED);
        $sqlparams += $statusin[1];

        $fields = "ch.id as orderid, ch.item_name as products, ch.timeupdated, ch.paymentid, ch.billingtype,
                   ch.amount, ch.amount as total, ch.subtotal, ch.discount, ch.payment_type, ch.payment_status as status,
                   ch.invoicepayment, ch.items, $statusfield, ch.tax, ch.currency, ch.currencyrate,
                   (CASE
                        WHEN ch.payment_status {$statusin[0]}
                        THEN
                            CASE
                                WHEN (ch.paymentid > 0 AND p.name <> '')
                                THEN p.name
                                ELSE '{$stringingnvoice}'
                            END
                        ELSE
                            CASE
                                WHEN ch.invoicepayment > 0
                                THEN '{$stringingnvoice}'
                                ELSE '-'
                            END
                   END) AS paymenttype, ch.payment_status";

        if (get_config('local_intellicart', 'enablerefunds')) {
            $tofloatfefund = \local_intellicart\helpers\DBHelper::get_operator('CAST_FLOAT', '12,2', ['field' => 'ch.refund']);
            $tofloatamount = \local_intellicart\helpers\DBHelper::get_operator('CAST_FLOAT', '12,2', ['field' => 'ch.amount']);
            $fields .= ", {$tofloatfefund} AS refund, 
                ($tofloatamount - refund) as amountpaid";
        } else {
            $fields .= ", ch.amount as amountpaid";
        }

        $from = "{local_intellicart_checkout} ch";
        $from .= " LEFT JOIN {local_intellicart_payments} p ON p.id = ch.paymentid";

        $where = "ch.userid = :userid {$where}";
        $where .= "ORDER BY ch.id DESC";

        $this->set_sql($fields, $from, $where, $sqlparams);
        $this->define_baseurl($PAGE->url);
    }

    /**
     * @param $values
     * @return string
     * @throws coding_exception
     */
    function col_timeupdated($values) {
        return ($values->timeupdated) ? userdate($values->timeupdated, get_string('strftimedate', 'langconfig')) : '-';
    }

    /**
     * @param $values
     * @return string
     * @throws dml_exception
     */
    public function col_amountpaid($values) {

        $amount = '-';

        if (in_array($values->payment_status, [
            \local_intellicart\payment::STATUS_REFUND,
            \local_intellicart\payment::STATUS_REFUNDPARTIAL,
            \local_intellicart\payment::STATUS_COMPLETED
        ])) {
            $values->amountpaid = format_float($values->amountpaid, 2, false);
            $amount = $this->get_currency($values, $this->download).$this->get_price($values, 'amountpaid');
        }

        return $amount;
    }

    /**
     * @param $values
     * @return string
     * @throws dml_exception
     */
    public function col_subtotal($values) {
        return ($values->subtotal) ? $this->get_currency($values, $this->download).$this->get_price($values, 'subtotal') : '-';
    }

    /**
     * @param $values
     * @return mixed
     */
    public function col_status($values) {
        return $values->status;
    }

    /**
     * @param $values
     * @return string
     * @throws dml_exception
     */
    public function col_total($values) {
        return ($values->amount) ? $this->get_currency($values, $this->download).$this->get_price($values, 'amount') : '-';
    }

    public function col_refund($values) {
        return ($values->refund > 0) ? $this->get_currency($values, $this->download).$this->get_price($values, 'refund') : '-';
    }

    /**
     * @param $values
     * @return string
     * @throws dml_exception
     */
    public function col_discount($values) {
        return ($values->discount) ? $this->get_currency($values, $this->download).$this->get_price($values, 'discount') : '-';
    }

    /**
     * @param $values
     * @return string
     * @throws dml_exception
     */
    public function col_tax($values) {
        return ($values->tax && $values->tax != 'NULL') ? $this->get_currency($values, $this->download).$this->get_price($values, 'tax') : '-';
    }

    /**
     * @param $values
     * @return lang_string|string
     */
    public function col_billingtype($values) {
        return \local_intellicart\checkout::get_bililngtype($values->billingtype);
    }

    function col_actions($values) {
        global $OUTPUT;

        $buttons = [];

        /** Download invoice button */
        $urlparams = array('id' => $values->orderid, 'sesskey' => sesskey());
        $aurl = new moodle_url(
            '/local/intellicart/sales/actions.php',
            $urlparams + array('action' => 'print')
        );

        $buttons[] = $OUTPUT->action_icon(
            $aurl,
            new pix_icon(
                't/download',
                get_string('downloadinvoice', 'local_intelliboard'),
                'core',
                array('class' => 'iconsmall')
            ),
            null,
            array('target'=>'_blank')
        );

        /** Process subscription button */
        if($values->status == \local_intellicart\payment::STATUS_PENDING) {
            // Action URL
            if($values->billingtype == \local_intellicart\checkout::BILLING_TYPE_REGULAR) {
                $aurl = new moodle_url('/local/intellicart/index.php', []);
            }

            if($values->billingtype == \local_intellicart\checkout::BILLING_TYPE_SUBSCRIPTION) {
                $urlparams = array('id' => $values->items, 'sesskey' => sesskey());
                $aurl = new moodle_url('/local/intellicart/subscribe.php', $urlparams);
            }

            $buttons[] = $OUTPUT->action_icon(
                $aurl,
                new pix_icon(
                    'e/redo',
                    get_string('process', 'local_intelliboard'),
                    'core',
                    array('class' => 'iconsmall')
                ),
                null,
                array('target'=>'_blank')
            );
        }

        return $buttons ? implode(' ', $buttons) : '-';
    }

    function start_html() {
        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', array('class' => 'no-overflow'));
        echo html_writer::start_tag('table', $this->attributes);

    }
}

class intelliboard_user_waitlist_table extends table_sql {
    public $search = '';
    public $categoryid = '';
    public $currency = '';

    function __construct($uniqueid, $search) {
        global $USER, $PAGE, $DB;

        parent::__construct($uniqueid);
        $this->currency = \local_intellicart\payment::get_currency('symbol');

        $columns = ['product', 'price', 'timemodified', 'seatnumber', 'actions'];
        $headers = [
            get_string('product', 'local_intellicart'),
            get_string('price', 'local_intellicart'),
            get_string('created', 'local_intellicart'),
            get_string('seatnumber', 'local_intellicart'),
            get_string('actions', 'local_intelliboard'),
        ];

        $this->sortable(true, 'timemodified', SORT_DESC);
        $this->no_sorting('actions');
        $this->no_sorting('seatnumber');
        $this->is_collapsible = false;

        $this->define_columns($columns);
        $this->define_headers($headers);

        $sqlparams = ['userid' => $USER->id];

        $where = '';
        if($search) {
            $where = ' AND '.$DB->sql_like('p.name', ':search1', false, false, false);
            $sqlparams['search1'] = "%{$search}%";
        }

        $fields = "w.*, w.timemodified as seatnumber, p.name as product, p.price";
        $from = "{local_intellicart_waitlist} w
                 JOIN {local_intellicart_products} p ON p.id = w.productid";

        $where = "p.id > 0 AND w.userid = :userid {$where}";

        $this->set_sql($fields, $from, $where, $sqlparams);
        $this->define_baseurl($PAGE->url);
    }

    function col_product($values) {
        return $values->product;
    }

    function col_price($values) {
        return ($values->price) ? $this->currency.$values->price : $this->currency.'0';
    }

    function col_timemodified($values) {
        return ($values->timemodified) ? userdate($values->timemodified, get_string('strftimedate', 'langconfig')) : '-';
    }

    function col_seatnumber($values) {
        global $DB;

        return $DB->count_records_sql(
            "SELECT COUNT(id)
               FROM {local_intellicart_waitlist}
              WHERE productid = :productid AND timemodified <= :timemodified
           ORDER BY timemodified",
            ['productid'=>$values->productid, 'sent' => 0, 'timemodified' => $values->timemodified]
        );
    }

    function col_actions($values) {
        global $OUTPUT;

        if (!has_capability('local/intellicart:editwaitlist', context_system::instance())) {
            return '';
        }

        $buttons = array();
        $urlparams = array('id' => $values->id, 'sesskey' => sesskey());

        $aurl = new moodle_url('/local/intellicart/waitlist/index.php', $urlparams + array('action' => 'delete'));
        $buttons[] = $OUTPUT->action_icon(
            $aurl,
            new pix_icon('t/delete', get_string('delete'), 'core', array('class' => 'iconsmall')),
            null
        );

        return implode(' ', $buttons);
    }

    function start_html() {
        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', array('class' => 'no-overflow'));
        echo html_writer::start_tag('table', $this->attributes);

    }
}

class intelliboard_user_seats_table extends table_sql {
    public $currency = '';

    function __construct($uniqueid, $search) {
        global $USER, $PAGE, $DB;

        parent::__construct($uniqueid);
        $this->currency = \local_intellicart\payment::get_currency();

        $columns = [
            'product',
            'seatkey',
            'timecreated',
            'quantity',
            'used',
            'actions'
        ];
        $headers = [
            get_string('product', 'local_intelliboard'),
            get_string('key', 'local_intelliboard'),
            get_string('created', 'local_intelliboard'),
            get_string('seatnumber', 'local_intelliboard'),
            get_string('seatsused', 'local_intelliboard'),
            get_string('details', 'local_intelliboard')
        ];

        $this->define_headers($headers);
        $this->define_columns($columns);

        $this->sortable(false);
        $this->is_collapsible = false;

        $sqlparams = [
            'userid' => $USER->id,
            'ltype' => \local_intellicart\log::TYPE_USEDSEAT,
            'lstatus' => \local_intellicart\log::STATUS_COMPLETED
        ];

        $searchwhere = '';
        if($search) {
            $searchwhere = ' AND '.$DB->sql_like('p.name', ':search1', false, false, false);
            $sqlparams['search1'] = "%{$search}%";
        }

        $fields = "s.*,"
                . "s.id as sid,"
                . "l.used,"
                . "p.name as product";
        $from = "{local_intellicart_seats} s
                LEFT JOIN {local_intellicart_products} p ON p.id = s.productid
                LEFT JOIN (
                            SELECT COUNT(id) as used, instanceid
                              FROM {local_intellicart_logs}
                             WHERE type = :ltype AND status = :lstatus
                          GROUP BY instanceid
                           ) l ON l.instanceid = s.id";

        $where = "p.id > 0 AND s.userid = :userid {$searchwhere}";

        $this->set_sql($fields, $from, $where, $sqlparams);
        $this->define_baseurl($PAGE->url);
    }

    function col_customer($values) {
        $user = $values;
        $user->id = $values->userid;

        return fullname($user);
    }

    function col_timecreated($values) {
        return ($values->timemodified) ? userdate($values->timecreated, get_string('strftimedate', 'langconfig')) : '-';
    }

    function col_used($values) {
        return ($values->used) ? $values->used : '-';
    }

    function col_actions($values) {
        global $OUTPUT;

        $buttons = [];

        $urlparams = ['id' => $values->sid, 'sesskey' => sesskey()];

        $aurl = new moodle_url('/local/intelliboard/student/seatsdetails.php', $urlparams);
        $buttons[] = $OUTPUT->action_icon(
            $aurl, new pix_icon(
                't/viewdetails',
                get_string('details', 'local_intelliboard'),
                'core',
                ['class' => 'iconsmall']
            ),
            null
        );

        return implode(' ', $buttons);
    }

    function start_html() {
        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);

    }
}

class intelliboard_used_seats_table extends table_sql {
    private $seatid;
    private $search;

    function __construct($uniqueid, $params) {
        global $PAGE, $DB;
        $this->seatid = $params['id'];
        $this->search = $params['search'];

        parent::__construct($uniqueid);

        $columns = [
            'customer', 'product', 'timemodified', 'status'
        ];
        $headers = [
            get_string('username', 'local_intelliboard'),
            get_string('product', 'local_intelliboard'),
            get_string('used', 'local_intelliboard'),
            get_string('status', 'local_intelliboard')
        ];

        $this->sortable(true, 'timemodified');
        $this->is_collapsible = false;

        $this->define_headers($headers);
        $this->define_columns($columns);

        $sql_params = [
            'ltype' => \local_intellicart\log::TYPE_USEDSEAT,
            'seatid' => $this->seatid,
        ];
        $userfields = \core_user\fields::for_name()->get_sql('u', false, '', '', false)->selects;
        $searchwhere = '';
        if($this->search) {
            $searchwhere = ' AND ('.$DB->sql_like('p.name', ':search1', false, false, false);
            $searchwhere .= ' OR '.$DB->sql_like(
                'CONCAT(u.firstname, " ", u.lastname)', ':search2', false, false, false
            );
            $searchwhere .= ' OR '.$DB->sql_like(
                'CONCAT(u.lastname, " ", u.firstname)', ':search3', false, false, false
            );
            $searchwhere .= ')';
            $sql_params['search1'] = "%{$this->search}%";
            $sql_params['search2'] = "%{$this->search}%";
            $sql_params['search3'] = "%{$this->search}%";
        }

        $fields = "l.id as lid,"
                . "l.status,"
                . "s.seatkey,"
                . "l.timemodified,"
                . "l.userid,"
                . "p.name as product,"
                . "CONCAT(u.firstname, ' ', u.lastname) as customer,"
                . $userfields;
        $from = "{local_intellicart_logs} l
                LEFT JOIN {local_intellicart_seats} s ON s.id = l.instanceid
                LEFT JOIN {local_intellicart_products} p ON p.id = s.productid
                LEFT JOIN {user} u ON u.id = l.userid";

        $where = 'p.id > 0 AND '
               . 'u.deleted = 0 AND '
               . 'u.suspended = 0 AND '
               . 'u.confirmed = 1 AND '
               . 'l.type = :ltype AND '
               . "l.instanceid = :seatid {$searchwhere}";

        $this->set_sql($fields, $from, $where, $sql_params);
        $this->define_baseurl($PAGE->url);
    }

    function col_customer($values) {
        $user = $values;
        $user->id = $values->userid;

        return fullname($user);
    }

    function col_timemodified($values) {
        return ($values->timemodified) ?
               userdate($values->timemodified, get_string('strftimedate', 'langconfig')) :
               '-';
    }

    function col_status($values) {
        return ($values->status) ? get_string('status_'.$values->status, 'local_intellicart') : '';
    }

    function start_html() {
        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);
    }
}

/**
 * Class intelliboard_user_subscriptions_table
 */
class intelliboard_user_subscriptions_table extends table_sql {
    public $currency = '';

    function __construct($uniqueid, $search) {
        global $USER, $PAGE, $DB;

        parent::__construct($uniqueid);

        $this->currency = \local_intellicart\payment::get_currency();
        $columns = [
            'product',
            'subscriptiondate',
            'price',
            'recurringperiod',
            'billingcycles',
            'status',
            'actions',
        ];
        $headers = [
            get_string('product', 'local_intelliboard'),
            get_string('subscriptiondate', 'local_intelliboard'),
            get_string('price', 'local_intelliboard'),
            get_string('recurringperiod', 'local_intelliboard'),
            get_string('billingcycles', 'local_intelliboard'),
            get_string('status', 'local_intelliboard'),
            get_string('actions', 'local_intelliboard'),
        ];

        $this->define_headers($headers);
        $this->define_columns($columns);
        $this->no_sorting('actions');
        $this->sortable(true, 'subscriptiondate', SORT_DESC);
        $this->is_collapsible = false;

        $sqlparams = [
            'userid' => $USER->id,
        ];

        $searchwhere = '';
        if($search) {
            $searchwhere = ' AND '.$DB->sql_like('ip.name', ':search1', false, false, false);
            $sqlparams['search1'] = "%{$search}%";
        }

        $fields = "lis.id, ip.name as product, lis.subscr_date AS subscriptiondate, lis.recur_period AS recurringperiod,
                   lis.status, lis.recur_times AS billingcycles, lis.amount as price, ch.invoicepayment,
                   lis.id AS subscrid, lip.name as paymentname, ip.id as productid, lip.settings as paymsettings";

        $from = "{local_intellicart_subscr} AS lis
                 LEFT JOIN {local_intellicart_products} AS ip ON ip.id = lis.productid
                 LEFT JOIN {local_intellicart_checkout} AS ch ON ch.id = lis.checkoutid
                 LEFT JOIN {local_intellicart_payments} AS lip ON lip.id = ch.paymentid";

        $where = "lis.userid = :userid {$searchwhere}";

        $this->set_sql($fields, $from, $where, $sqlparams);
        $this->define_baseurl($PAGE->url);
    }

    function col_subscriptiondate($values) {
        if(!$values->subscriptiondate) {
            return '-';
        }

        return userdate(
            $values->subscriptiondate,
            get_string('strftimedate', 'langconfig')
        );
    }

    function col_recurringperiod($values) {
        if($values->recurringperiod) {
            return \local_intellicart\subscription::get_recurringperiods()[$values->recurringperiod];
        }
        return '-';
    }

    function col_billingcycles($values) {
        return $values->billingcycles ? $values->billingcycles : '-';
    }

    function col_price($values) {
        return $this->currency.($values->price ? $values->price : '0');
    }

    function col_status($values) {
        switch($values->status) {
            case \local_intellicart\subscription::STATUS_ACTIVE:
                return get_string('active', 'local_intelliboard');
            case \local_intellicart\subscription::STATUS_SUSPENDED:
                return get_string('suspended', 'local_intelliboard');
            case \local_intellicart\subscription::STATUS_CANCELED:
                return get_string('canceled', 'local_intelliboard');
            case \local_intellicart\subscription::STATUS_EXPIRED:
                return get_string('expired', 'local_intelliboard');
            case \local_intellicart\subscription::STATUS_PENDING:
                return get_string('pending', 'local_intellicart');
            default:
                return '-';
        }
    }

    function col_actions($values) {
        /** Manual subscription button */
        $paypalpaymentname = \local_intellicart\payment::get_types()['paypal'];
        $buttons = [];

        if(
            $values->invoicepayment == 1 &&
           ($values->status == \local_intellicart\subscription::STATUS_ACTIVE or
            $values->status == \local_intellicart\subscription::STATUS_SUSPENDED)
        ) {
            $urlargs = [
                'action' => 'cancel',
                'id' => $values->productid,
            ];
            $url = (new \moodle_url('/local/intellicart/subscribe.php', $urlargs))->out();
            $manualsubscriptionbutton = sprintf(
                '<a class="btn btn-primary" href="%s">%s</a>',
                $url,
                get_string('cancel_subscription', 'local_intelliboard')

            );
            $buttons[] = $manualsubscriptionbutton;
        }

        /** PayPal subscription button */
        if(
            $values->paymentname == $paypalpaymentname &&
           ($values->status == \local_intellicart\subscription::STATUS_ACTIVE or
            $values->status == \local_intellicart\subscription::STATUS_SUSPENDED)
        ) {
            $paypalsandbox = unserialize(
                $values->paymsettings
            )['paypalsandbox'];
            $paypalsubscritionsbutton = sprintf(
                '<a class="btn btn-primary" target="_blank" href="https://www.%spaypal.com/us/cgi-bin/webscr?cmd=_manage-paylist">%s</a>',
                ($paypalsandbox ? 'sandbox.' : ''),
                get_string('cancel_subscription', 'local_intelliboard')
            );
            $buttons[] = $paypalsubscritionsbutton;
        }

        return $buttons ? implode(' ', $buttons) : '-';
    }

    function start_html() {
        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        // Start of main data table

        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);

    }
}
