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
 * Authorize enrolment plugin.
 *
 * This plugin allows you to set up paid courses, using authorize.net.
 *
 * @package    enrol
 * @subpackage authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

define('ORDER_CAPTURE', 'capture');
define('ORDER_DELETE',  'delete');
define('ORDER_REFUND',  'refund');
define('ORDER_VOID',    'void');

/**
 * authorize_print_orders
 *
 */
function authorize_print_orders($courseid, $userid) {
    global $course;
    global $CFG, $USER, $SITE, $DB, $OUTPUT, $PAGE;
    global $strs, $authstrs;

    $plugin = enrol_get_plugin('authorize');

    require_once($CFG->libdir.'/tablelib.php');

    $perpage = optional_param('perpage', 10, PARAM_INT);
    $showonlymy = optional_param('showonlymy', 0, PARAM_BOOL);
    $searchquery = optional_param('searchquery', '0', PARAM_INT);
    $searchtype = optional_param('searchtype', 'orderid', PARAM_ALPHA);
    $status = optional_param('status', AN_STATUS_NONE, PARAM_INT);

    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);

    $searchmenu = array('orderid' => $authstrs->orderid, 'transid' => $authstrs->transid, 'cclastfour' => $authstrs->cclastfour);
    $buttons = "<form method='post' action='index.php' autocomplete='off'><div>";
    $buttons .= html_writer::select($searchmenu, 'searchtype', $searchtype, false);
    $buttons .= "<input type='text' size='16' name='searchquery' value='' />";
    $buttons .= "<input type='submit' value='$strs->search' />";
    $buttons .= "</div></form>";

    if (has_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_USER, $USER->id))) {
        $buttons .= "<form method='get' action='uploadcsv.php'><div><input type='submit' value='".get_string('uploadcsv', 'enrol_authorize')."' /></div></form>";
    }

    $canmanagepayments = has_capability('enrol/authorize:managepayments', $coursecontext);
    if ($showonlymy || !$canmanagepayments) {
        $userid = $USER->id;
    }

    $baseurl = $CFG->wwwroot.'/enrol/authorize/index.php?user='.$userid;

    $params = array('userid'=>$userid);
    $sql = "SELECT c.id, c.fullname FROM {course} c JOIN {enrol_authorize} e ON c.id = e.courseid ";
    $sql .= ($userid > 0) ? "WHERE (e.userid=:userid) " : '';
    $sql .= "ORDER BY c.sortorder, c.fullname";
    if (($popupcrs = $DB->get_records_sql_menu($sql, $params))) {
        $popupcrs = array($SITE->id => $SITE->fullname) + $popupcrs;
    }
    $popupmenu = empty($popupcrs) ? '' : $OUTPUT->single_select(new moodle_url($baseurl.'&status='.$status), 'course', $popupcrs, $courseid, null, 'coursesmenu');
    $popupmenu .= '<br />';
    $statusmenu = array(
        AN_STATUS_NONE => $strs->all,
        AN_STATUS_AUTH | AN_STATUS_UNDERREVIEW | AN_STATUS_APPROVEDREVIEW => $authstrs->allpendingorders,
        AN_STATUS_AUTH => $authstrs->authorizedpendingcapture,
        AN_STATUS_AUTHCAPTURE => $authstrs->authcaptured,
        AN_STATUS_CREDIT => $authstrs->refunded,
        AN_STATUS_VOID => $authstrs->cancelled,
        AN_STATUS_EXPIRE => $authstrs->expired,
        AN_STATUS_UNDERREVIEW => $authstrs->underreview,
        AN_STATUS_APPROVEDREVIEW => $authstrs->approvedreview,
        AN_STATUS_REVIEWFAILED => $authstrs->reviewfailed,
        AN_STATUS_TEST => $authstrs->tested
    );

    $popupmenu .= $OUTPUT->single_select(new moodle_url($baseurl.'&course='.$courseid), 'status', $statusmenu, $status, null, 'statusmenu');
    if ($canmanagepayments) {
        $popupmenu .= '<br />';
        $PAGE->requires->js('/enrol/authorize/authorize.js');
        $aid = $OUTPUT->add_action_handler(new component_action('click', 'authorize_jump_to_mypayments', array('userid' => $USER->id, 'status' => $status)));
        $popupmenu .= html_writer::checkbox('enrol_authorize', 1, $userid == $USER->id, get_string('mypaymentsonly', 'enrol_authorize'), array('id'=>$aid));
    }

    if (SITEID != $courseid) {
        $shortname = format_string($course->shortname, true, array('context' => $coursecontext));
        $PAGE->navbar->add($shortname, new moodle_url('/course/view.php', array('id'=>$course->id)));
    }
    $PAGE->navbar->add($authstrs->paymentmanagement, 'index.php');
    $PAGE->set_title("$course->shortname: $authstrs->paymentmanagement");
    $PAGE->set_heading($authstrs->paymentmanagement);
    $PAGE->set_headingmenu($popupmenu);
    $PAGE->set_button($buttons);
    echo $OUTPUT->header();

    $table = new flexible_table('enrol-authorize');
    $table->set_attribute('width', '100%');
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('cellpadding', '3');
    $table->set_attribute('id', 'orders');
    $table->set_attribute('class', 'generaltable generalbox');

    if ($perpage > 100) { $perpage = 100; }
    $perpagemenus = array(5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100);
    $perpagemenu = $OUTPUT->single_select(new moodle_url($baseurl.'&status='.$status.'&course='.$courseid), 'perpage', $perpagemenus, $perpage, array(''=>'choosedots'), 'perpagemenu');
    $table->define_columns(array('id', 'userid', 'timecreated', 'status', 'action'));
    $table->define_headers(array($authstrs->orderid, $authstrs->shopper, $strs->time, $strs->status, $perpagemenu));
    $table->define_baseurl($baseurl."&amp;status=$status&amp;course=$courseid&amp;perpage=$perpage");

    $table->no_sorting('action');
    $table->sortable(true, 'id', SORT_DESC);
    $table->pageable(true);
    $table->setup();

    $select = "SELECT e.id, e.paymentmethod, e.refundinfo, e.transid, e.courseid, e.userid, e.status, e.ccname, e.timecreated, e.settletime ";
    $from   = "FROM {enrol_authorize} e ";
    $where  = "WHERE (1=1) ";
    $params = array();

    if (!empty($searchquery)) {
        switch($searchtype) {
            case 'orderid':
                $where = "WHERE (e.id = :searchquery) ";
                $params['searchquery'] = $searchquery;
                break;

            case 'transid':
                $where = "WHERE (e.transid = :searchquery) ";
                $params['searchquery'] = $searchquery;
                break;

            case 'cclastfour':
                $searchquery = sprintf("%04d", $searchquery);
                $where = "WHERE (e.refundinfo = :searchquery) AND (e.paymentmethod=:method) ";
                $params['searchquery'] = $searchquery;
                $params['method'] = AN_METHOD_CC;
                break;
        }
    }
    else {
        switch ($status)
        {
            case AN_STATUS_NONE:
                if (!$plugin->get_config('an_test')) {
                    $where .= "AND (e.status != :status) ";
                    $params['status'] = AN_STATUS_NONE;
                }
                break;

            case AN_STATUS_TEST:
                $newordertime = time() - 120; // -2 minutes. Order may be still in process.
                $where .= "AND (e.status = :status) AND (e.transid = '0') AND (e.timecreated < :newordertime) ";
                $params['status'] = AN_STATUS_NONE;
                $params['newordertime'] = $newordertime;
                break;

            case AN_STATUS_AUTH | AN_STATUS_UNDERREVIEW | AN_STATUS_APPROVEDREVIEW:
                $where .= 'AND (e.status IN(:status1,:status2,:status3)) ';
                $params['status1'] = AN_STATUS_AUTH;
                $params['status2'] = AN_STATUS_UNDERREVIEW;
                $params['status3'] = AN_STATUS_APPROVEDREVIEW;
                break;

            case AN_STATUS_CREDIT:
                $from .= "INNER JOIN {enrol_authorize_refunds} r ON e.id = r.orderid ";
                $where .= "AND (e.status = :status) ";
                $params['status'] = AN_STATUS_AUTHCAPTURE;
                break;

            default:
                $where .= "AND (e.status = :status) ";
                $params['status'] = $status;
                break;
        }

        if (SITEID != $courseid) {
            $where .= "AND (e.courseid = :courseid) ";
            $params['courseid'] = $courseid;
        }
    }

    // This must be always LAST where!!!
    if ($userid > 0) {
        $where .= "AND (e.userid = :userid) ";
        $params['userid'] = $userid;
    }

    if (($sort = $table->get_sql_sort())) {
        $sort = ' ORDER BY ' . $sort;
    }

    $totalcount = $DB->count_records_sql('SELECT COUNT(*) ' . $from . $where, $params);
    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $totalcount);

    if (($records = $DB->get_records_sql($select . $from . $where . $sort, $params, $table->get_page_start(), $table->get_page_size()))) {
        foreach ($records as $record) {
            $actionstatus = authorize_get_status_action($record);
            $color = authorize_get_status_color($actionstatus->status);
            $actions = '';

            if (empty($actionstatus->actions)) {
                $actions .= $strs->none;
            }
            else {
                foreach ($actionstatus->actions as $val) {
                    $actions .= authorize_print_action_button($record->id, $val);
                }
            }

            $table->add_data(array(
                "<a href='index.php?order=$record->id'>$record->id</a>",
                $record->ccname,
                userdate($record->timecreated),
                "<font style='color:$color'>" . $authstrs->{$actionstatus->status} . "</font>",
                $actions
            ));
        }
    }

    $table->print_html();
    echo $OUTPUT->footer();
}

/**
 * authorize_print_order
 *
 * @param object $order
 */
function authorize_print_order($orderid)
{
    global $CFG, $USER, $DB, $OUTPUT, $PAGE;
    global $strs, $authstrs;

    $plugin = enrol_get_plugin('authorize');
    $an_test = $plugin->get_config('an_test');

    $do = optional_param('do', '', PARAM_ALPHA);
    $unenrol = optional_param('unenrol', 0, PARAM_BOOL);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    if (!$order = $DB->get_record('enrol_authorize', array('id'=>$orderid))) {
        print_error('orderidnotfound', '',
                "$CFG->wwwroot/enrol/authorize/index.php", $orderid);
    }

    if (!$course = $DB->get_record('course', array('id'=>$order->courseid))) {
        print_error('invalidcourseid', '', "$CFG->wwwroot/enrol/authorize/index.php");
    }

    if (!$user = $DB->get_record('user', array('id'=>$order->userid))) {
        print_error('nousers', '', "$CFG->wwwroot/enrol/authorize/index.php");
    }

    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
    if ($USER->id != $order->userid) { // Current user viewing someone else's order
        require_capability('enrol/authorize:managepayments', $coursecontext);
    }

    $settled = AuthorizeNet::settled($order);
    $statusandactions = authorize_get_status_action($order);
    $color = authorize_get_status_color($statusandactions->status);

    $buttons = '';
    if (empty($do))
    {
        if (empty($statusandactions->actions)) {
            if ((AN_METHOD_ECHECK == $order->paymentmethod) && has_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_USER, $USER->id))) {
                $buttons .= "<form method='get' action='uploadcsv.php'><div><input type='submit' value='".get_string('uploadcsv', 'enrol_authorize')."' /></div></form>";
            }
        }
        else {
            foreach ($statusandactions->actions as $val) {
                $buttons .= authorize_print_action_button($orderid, $val);
            }
        }
    }

    if (SITEID != $course->id) {
        $shortname = format_string($course->shortname, true, array('context' => $coursecontext));
        $PAGE->navbar->add($shortname, new moodle_url('/course/view.php', array('id'=>$course->id)));
    }
    $PAGE->navbar->add($authstrs->paymentmanagement, 'index.php?course='.$course->id);
    $PAGE->navbar->add($authstrs->orderid . ': ' . $orderid, 'index.php');
    $PAGE->set_course($course);
    $PAGE->set_title("$course->shortname: $authstrs->paymentmanagement");
    $PAGE->set_heading($authstrs->orderdetails);
    $PAGE->set_cacheable(false);
    $PAGE->set_button($buttons);
    echo $OUTPUT->header();

    $table = new html_table();
    $table->width = '100%';
    $table->size = array('30%', '70%');
    $table->align = array('right', 'left');

    if (AN_METHOD_CC == $order->paymentmethod) {
        $table->data[] = array("<b>$authstrs->paymentmethod:</b>", $authstrs->methodcc);
        $table->data[] = array("<b>$authstrs->nameoncard:</b>", $order->ccname . ' (<b><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'">'.fullname($user).'</a></b>)');
        $table->data[] = array("<b>$authstrs->cclastfour:</b>", $order->refundinfo);
    }
    else {
        $table->data[] = array("<b>$authstrs->paymentmethod:</b>", $authstrs->methodecheck);
        $table->data[] = array("<b>$authstrs->echeckfirslasttname:</b>", $order->ccname . ' (<b><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'">'.fullname($user).'</a></b>)');
        $table->data[] = array("<b>$authstrs->isbusinesschecking:</b>", ($order->refundinfo == 1) ? $strs->yes : $strs->no);
    }

    $table->data[] = array("<b>$authstrs->amount:</b>", "$order->currency $order->amount");
    $table->data[] = array("<b>$authstrs->transid:</b>", $order->transid);
    $table->data[] = array("<b>$strs->time:</b>", userdate($order->timecreated));
    $table->data[] = array("<b>$authstrs->settlementdate:</b>", $settled ? userdate($order->settletime) : $authstrs->notsettled);
    $table->data[] = array("<b>$strs->status:</b>", "<b><font style='color:$color'>" . $authstrs->{$statusandactions->status} . "</font></b>");

    if (ORDER_CAPTURE == $do && in_array(ORDER_CAPTURE, $statusandactions->actions)) {
        if ($confirm && confirm_sesskey()) {
            $message = '';
            $extra = NULL;
            if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE)) {
                if (empty($an_test)) {
                    if (enrol_into_course($course, $user, 'authorize')) {
                        if ($plugin->get_config('enrol_mailstudents')) {
                            send_welcome_messages($orderid);
                        }
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid");
                    }
                    else {
                        $shortname = format_string($course->shortname, true, array('context' => $coursecontext));
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", "Error while trying to enrol ".fullname($user)." in '" . $shortname . "'", 20);
                    }
                }
                else {
                    redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", get_string('testwarning', 'enrol_authorize'), 10);
                }
            }
            else {
                redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $message, 20);
            }
        }
        $table->data[] = array("<b>$strs->confirm:</b>", get_string('captureyes', 'enrol_authorize') . '<br />' .
                               authorize_print_action_button($orderid, ORDER_CAPTURE, 0, true, false, $strs->no));
        echo html_writer::table($table);
    }
    elseif (ORDER_REFUND == $do && in_array(ORDER_REFUND, $statusandactions->actions)) {
        $refunded = 0.0;
        $sql = "SELECT SUM(amount) AS refunded
                  FROM {enrol_authorize_refunds}
                 WHERE (orderid = ?)
                   AND (status = ?)";

        if (($refundval = $DB->get_field_sql($sql, array($orderid, AN_STATUS_CREDIT)))) {
            $refunded = floatval($refundval);
        }
        $upto = round($order->amount - $refunded, 2);
        if ($upto <= 0) {
            print_error('refoundtoorigi', '',
                    "$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $order->amount);
        }
        $amount = round(optional_param('amount', $upto, PARAM_RAW), 2);
        if ($amount > $upto) {
            print_error('refoundto', '',
                    "$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $upto);
        }
        if ($confirm && confirm_sesskey()) {
            $extra = new stdClass;
            $extra->orderid = $orderid;
            $extra->amount = $amount;
            $message = '';
            $success = AuthorizeNet::process($order, $message, $extra, AN_ACTION_CREDIT);
            if (AN_APPROVED == $success || AN_REVIEW == $success) {
                if (empty($an_test)) {
                    if (empty($extra->id)) {
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", "insert record error", 20);
                    }
                    else {
                        if (!empty($unenrol)) {
                            $pinstance = $DB->get_record('enrol', array('id'=>$order->instanceid));
                            $plugin->unenrol_user($pinstance, $order->userid);
                            //role_unassign_all(array('userid'=>$order->userid, 'contextid'=>$coursecontext->id, 'component'=>'enrol_authorize'), true, true);
                        }
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid");
                    }
                }
                else {
                    redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", get_string('testwarning', 'enrol_authorize'), 10);
                }
            }
            else {
                redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $message, 20);
            }
        }
        $a = new stdClass;
        $a->upto = $upto;
        $extrahtml = get_string('howmuch', 'enrol_authorize') .
                     ' <input type="text" size="5" name="amount" value="'.$amount.'" /> ' .
                     get_string('canbecredit', 'enrol_authorize', $a) . '<br />';
        $table->data[] = array("<b>$strs->confirm:</b>",
                               authorize_print_action_button($orderid, ORDER_REFUND, 0, true, $authstrs->unenrolstudent, $strs->no, $extrahtml));
        echo html_writer::table($table);
    }
    elseif (ORDER_DELETE == $do && in_array(ORDER_DELETE, $statusandactions->actions)) {
        if ($confirm && confirm_sesskey()) {
            if (!empty($unenrol)) {
                $pinstance = $DB->get_record('enrol', array('id'=>$order->instanceid));
                $plugin->unenrol_user($pinstance, $order->userid);
                //role_unassign_all(array('userid'=>$order->userid, 'contextid'=>$coursecontext->id, 'component'=>'enrol_authorize'), true, true);
            }
            $DB->delete_records('enrol_authorize', array('id'=>$orderid));
            redirect("$CFG->wwwroot/enrol/authorize/index.php");
        }
        $table->data[] = array("<b>$strs->confirm:</b>",
                               authorize_print_action_button($orderid, ORDER_DELETE, 0, true, $authstrs->unenrolstudent,$strs->no));
        echo html_writer::table($table);
    }
    elseif (ORDER_VOID == $do) { // special case: cancel original or refunded transaction?
        $suborderid = optional_param('suborder', 0, PARAM_INT);
        if (empty($suborderid) && in_array(ORDER_VOID, $statusandactions->actions)) { // cancel original
            if ($confirm && confirm_sesskey()) {
                $extra = NULL;
                $message = '';
                if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, AN_ACTION_VOID)) {
                    if (empty($an_test)) {
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid");
                    }
                    else {
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", get_string('testwarning', 'enrol_authorize'), 10);
                    }
                }
                else {
                    redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $message, 20);
                }
            }
            $table->data[] = array("<b>$strs->confirm:</b>", get_string('voidyes', 'enrol_authorize') . '<br />' .
                                   authorize_print_action_button($orderid, ORDER_VOID, 0, true, false, $strs->no));
            echo html_writer::table($table);
        }
        elseif (!empty($suborderid)) { // cancel refunded
            $sql = "SELECT r.*, e.courseid, e.paymentmethod
                      FROM {enrol_authorize_refunds} r
                INNER JOIN {enrol_authorize} e
                        ON r.orderid = e.id
                     WHERE r.id = ?
                       AND r.orderid = ?
                       AND r.status = ?";

            $suborder = $DB->get_record_sql($sql, array($suborderid, $orderid, AN_STATUS_CREDIT));
            if (!$suborder) { // not found
                print_error('transactionvoid', '', "$CFG->wwwroot/enrol/authorize/index.php?order=$orderid");
            }
            $refundedstatus = authorize_get_status_action($suborder);
            unset($suborder->courseid);
            if (in_array(ORDER_VOID, $refundedstatus->actions)) {
                if ($confirm && confirm_sesskey()) {
                    $message = '';
                    $extra = NULL;
                    if (AN_APPROVED == AuthorizeNet::process($suborder, $message, $extra, AN_ACTION_VOID)) {
                        if (empty($an_test)) {
                            if (!empty($unenrol)) {
                                $pinstance = $DB->get_record('enrol', array('id'=>$order->instanceid));
                                $plugin->unenrol_user($pinstance, $order->userid);
                                //role_unassign_all(array('userid'=>$order->userid, 'contextid'=>$coursecontext->id, 'component'=>'enrol_authorize'), true, true);
                            }
                            redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid");
                        }
                        else {
                            redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", get_string('testwarning', 'enrol_authorize'), 10);
                        }
                    }
                    else {
                        redirect("$CFG->wwwroot/enrol/authorize/index.php?order=$orderid", $message, 20);
                    }
                }
                $a = new stdClass;
                $a->transid = $suborder->transid;
                $a->amount = $suborder->amount;
                $table->data[] = array("<b>$strs->confirm:</b>", get_string('subvoidyes', 'enrol_authorize', $a) . '<br />' .
                                       authorize_print_action_button($orderid, ORDER_VOID, $suborderid, true, $authstrs->unenrolstudent, $strs->no));
                echo html_writer::table($table);
            }
        }
    }
    else {
        echo html_writer::table($table);

        if ($settled) { // show refunds.
            $t2 = new html_table();
            $t2->size = array('45%', '15%', '20%', '10%', '10%');
            $t2->align = array('right', 'right', 'right', 'right', 'right');
            $t2->head = array($authstrs->settlementdate, $authstrs->transid, $strs->status, $strs->action, $authstrs->amount);

            $sql = "SELECT r.*, e.courseid, e.paymentmethod
                      FROM {enrol_authorize_refunds} r
                INNER JOIN {enrol_authorize} e
                        ON r.orderid = e.id
                     WHERE r.orderid = ?";

            if (($refunds = $DB->get_records_sql($sql, array($orderid)))) {
                $sumrefund = floatval(0.0);
                foreach ($refunds as $rf) {
                    $subactions = '';
                    $substatus = authorize_get_status_action($rf);
                    if (empty($substatus->actions)) {
                        $subactions .= $strs->none;
                    }
                    else {
                        foreach ($substatus->actions as $vl) {
                            $subactions .= authorize_print_action_button($orderid, $vl, $rf->id);
                        }
                    }
                    $sign = '';
                    $color = authorize_get_status_color($substatus->status);
                    if ($substatus->status == 'refunded' or $substatus->status == 'settled') {
                        $sign = '-';
                        $sumrefund += floatval($rf->amount);
                    }
                    $t2->data[] = array(
                        userdate($rf->settletime),
                        $rf->transid,
                        "<b><font style='color:$color'>" .$authstrs->{$substatus->status} . "</font></b>",
                        $subactions,
                        format_float($sign . $rf->amount, 2)
                    );
                }
                $t2->data[] = array('','',get_string('total'),$order->currency,format_float('-'.$sumrefund, 2));
            }
            else {
                $t2->data[] = array('','',get_string('noreturns', 'enrol_authorize'),'','');
            }
            echo "<h4>" . get_string('returns', 'enrol_authorize') . "</h4>\n";
            echo html_writer::table($t2);
        }
    }

    echo $OUTPUT->footer();
}

/**
 * authorize_get_status_action
 *
 * @param object $order Order details.
 * @return object
 */
function authorize_get_status_action($order)
{
    global $CFG;
    static $newordertime = 0;

    if (0 == $newordertime) {
        $newordertime = time() - 120; // -2 minutes. Order may be still in process.
    }

    $ret = new stdClass();
    $ret->actions = array();

    $canmanage = has_capability('enrol/authorize:managepayments', get_context_instance(CONTEXT_COURSE, $order->courseid));

    if (floatval($order->transid) == 0) { // test transaction or new order
        if ($order->timecreated < $newordertime) {
            if ($canmanage) {
                $ret->actions = array(ORDER_DELETE);
            }
            $ret->status = 'tested';
        }
        else {
            $ret->status = 'new';
        }
        return $ret;
    }

    switch ($order->status) {
        case AN_STATUS_AUTH:
            if (AuthorizeNet::expired($order)) {
                if ($canmanage) {
                    $ret->actions = array(ORDER_DELETE);
                }
                $ret->status = 'expired';
            }
            else {
                if ($canmanage) {
                    $ret->actions = array(ORDER_CAPTURE, ORDER_VOID);
                }
                $ret->status = 'authorizedpendingcapture';
            }
            return $ret;

        case AN_STATUS_AUTHCAPTURE:
            if (AuthorizeNet::settled($order)) {
                if ($canmanage) {
                    if (($order->paymentmethod == AN_METHOD_CC) || ($order->paymentmethod == AN_METHOD_ECHECK && !empty($order->refundinfo))) {
                        $ret->actions = array(ORDER_REFUND);
                    }
                }
                $ret->status = 'settled';
            }
            else {
                if ($order->paymentmethod == AN_METHOD_CC && $canmanage) {
                    $ret->actions = array(ORDER_VOID);
                }
                $ret->status = 'capturedpendingsettle';
            }
            return $ret;

        case AN_STATUS_CREDIT:
            if (AuthorizeNet::settled($order)) {
                $ret->status = 'settled';
            }
            else {
                if ($order->paymentmethod == AN_METHOD_CC && $canmanage) {
                    $ret->actions = array(ORDER_VOID);
                }
                $ret->status = 'refunded';
            }
            return $ret;

        case AN_STATUS_VOID:
            $ret->status = 'cancelled';
            return $ret;

        case AN_STATUS_EXPIRE:
            if ($canmanage) {
                $ret->actions = array(ORDER_DELETE);
            }
            $ret->status = 'expired';
            return $ret;

        case AN_STATUS_UNDERREVIEW:
            $ret->status = 'underreview';
            return $ret;

        case AN_STATUS_APPROVEDREVIEW:
            $ret->status = 'approvedreview';
            return $ret;

        case AN_STATUS_REVIEWFAILED:
            if ($canmanage) {
                $ret->actions = array(ORDER_DELETE);
            }
            $ret->status = 'reviewfailed';
            return $ret;

        default:
            return $ret;
    }
}


function authorize_get_status_color($status)
{
    $color = 'black';
    switch ($status)
    {
        case 'settled':
        case 'capturedpendingsettle':
            $color = '#339900'; // green
            break;

        case 'underreview':
        case 'approvedreview':
        case 'authorizedpendingcapture':
            $color = '#FF6600'; // orange
            break;

        case 'new':
        case 'tested':
            $color = '#003366'; // blue
            break;

        case 'expired':
        case 'cancelled':
        case 'refunded';
        case 'reviewfailed':
            $color = '#FF0033'; // red
            break;
    }
    return $color;
}

function authorize_print_action_button($orderid, $do, $suborderid=0, $confirm=false, $unenrol=false, $nobutton=false, $extrahtml='')
{
    global $CFG, $OUTPUT;
    global $authstrs;

    $ret =  '<form action="'.$CFG->wwwroot.'/enrol/authorize/index.php'.'" method="post"><div>' .
            '<input type="hidden" name="order" value="'.$orderid.'" />' .
            '<input type="hidden" name="do" value="'.$do.'" />' .
            '<input type="hidden" name="sesskey" value="'. sesskey() . '" />';
    if (!empty($suborderid)) {
        $ret .= '<input type="hidden" name="suborder" value="'.$suborderid.'" />';
    }
    if (!empty($confirm)) {
        $ret .= '<input type="hidden" name="confirm" value="1" />';
    }
    if (!empty($unenrol)) {
        $ret .= html_writer::checkbox('unenrol', 1, false, $unenrol) . '<br />';
    }
    $ret .= $extrahtml;
    $ret .= '<input type="submit" value="'.$authstrs->$do.'" />' .
            '</div></form>';
    if (!empty($nobutton)) {
        $ret .= '<form method="get" action="index.php"><div><input type="hidden" name="order" value="'.$orderid.'" /><input type="submit" value="'.$nobutton.'" /></div></form>';
    }
    return $ret;
}

