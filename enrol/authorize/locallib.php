<?PHP // $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once('const.php');
require_once('authorizenetlib.php');

define('ORDER_CAPTURE', 'capture');
define('ORDER_DELETE',  'delete');
define('ORDER_REFUND',  'refund');
define('ORDER_VOID',    'void');

/**
 * authorize_print_orders
 *
 */
function authorize_print_orders()
{
    global $CFG, $USER, $SITE;
    global $strs, $authstrs;
    global $courseid, $userid;
    require_once($CFG->libdir.'/tablelib.php');

    $perpage = 10;
    $status = optional_param('status', AN_STATUS_NONE, PARAM_INT);

    if (! isteacher($courseid)) {
        $userid = $USER->id;
    }

    $baseurl = $CFG->wwwroot."/enrol/authorize/index.php?user=$userid";
    $statusmenu = array(AN_STATUS_NONE => $strs->all,
                        AN_STATUS_AUTH => $authstrs->authorizedpendingcapture,
                        AN_STATUS_AUTHCAPTURE => $authstrs->authcaptured,
                        AN_STATUS_CREDIT => $authstrs->refunded,
                        AN_STATUS_VOID => $authstrs->cancelled,
                        AN_STATUS_EXPIRE => $authstrs->expired,
                        AN_STATUS_TEST => $authstrs->tested
    );

    $sql = "SELECT c.id, c.fullname FROM {$CFG->prefix}course c INNER JOIN {$CFG->prefix}enrol_authorize e ON c.id = e.courseid ";
    if ($CFG->enrol == 'authorize') { // default enrolment plugin
        $sql .= "WHERE (c.enrol IS NULL) OR (c.enrol='') OR (c.enrol = 'authorize') ";
    }
    else {
        $sql .= "WHERE (c.enrol = 'authorize') ";
    }
    $sql .= "ORDER BY c.sortorder, c.fullname";
    if ($popupcrs = get_records_sql_menu($sql)) {
        $popupcrs = array($SITE->id => $SITE->fullname) + $popupcrs;
        print_simple_box_start('center', '100%');
        echo "$strs->status: ";
        echo popup_form($baseurl.'&amp;course='.$courseid.'&amp;status=',$statusmenu,'statusmenu',$status,'','','',true);
        echo " &nbsp; $strs->course: ";
        echo popup_form($baseurl.'&amp;status='.$status.'&amp;course=',$popupcrs,'coursesmenu',$courseid,'','','',true);
        print_simple_box_end();
    }

    $table = new flexible_table('enrol-authorize');
    $table->set_attribute('width', '100%');
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('cellpadding', '3');
    $table->set_attribute('id', 'orders');
    $table->set_attribute('class', 'generaltable generalbox');

    $table->define_columns(array('id', 'timecreated', 'userid', 'status', ''));
    $table->define_headers(array($authstrs->orderid, $strs->time, $authstrs->nameoncard, $strs->status, $strs->action));
    $table->define_baseurl($baseurl."&amp;status=$status");

    $table->sortable(true, 'id', SORT_DESC);
    $table->pageable(true);
    $table->setup();

    $select = "SELECT E.id, E.transid, E.courseid, E.userid, E.status, E.ccname, E.timecreated, E.settletime ";
    $from   = "FROM {$CFG->prefix}enrol_authorize E ";
    $where  = "WHERE (1=1) ";

    if ($status > AN_STATUS_NONE) {
        switch ($status)
        {
            case AN_STATUS_CREDIT:
                $from .= "INNER JOIN {$CFG->prefix}enrol_authorize_refunds R ON E.id = R.orderid ";
                $where .= "AND (E.status = '" . AN_STATUS_AUTHCAPTURE . "') ";
                break;

            case AN_STATUS_TEST:
                $newordertime = time() - 120; // -2 minutes. Order may be still in process.
                $where .= "AND (E.status = '" . AN_STATUS_NONE . "') AND (E.transid = '0') AND (E.timecreated < $newordertime) ";
                break;

            default:
                $where .= "AND (E.status = '$status') ";
                break;
        }
    }
    else {
        if (empty($CFG->an_test)) {
            $where .= "AND (E.status != '" . AN_STATUS_NONE . "') ";
        }
    }

    if ($userid > 0) {
        $where .= "AND (E.userid = '" . $userid . "') ";
    }
    if ($courseid != SITEID) {
        $where .= "AND (E.courseid = '" . $courseid . "') ";
    }

    if ($sort = $table->get_sql_sort()) {
        $sort = ' ORDER BY ' . $sort;
    }

    $totalcount = count_records_sql('SELECT COUNT(*) ' . $from . $where);
    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $totalcount);
    if ($table->get_page_start() !== '' && $table->get_page_size() !== '') {
        $limit = ' ' . sql_paging_limit($table->get_page_start(), $table->get_page_size());
    }
    else {
        $limit = '';
    }

    if ($records = get_records_sql($select . $from . $where . $sort . $limit)) {
        foreach ($records as $record) {
            $actionstatus = authorize_get_status_action($record);
            $actions = '';

            if (empty($actionstatus->actions)) {
                $actions .= $strs->none;
            }
            else {
                foreach ($actionstatus->actions as $value) {
                    $actions .= "&nbsp;&nbsp;<a href='index.php?$value=y&amp;sesskey=$USER->sesskey&amp;order=$record->id'>{$authstrs->$value}</a> ";
                }
            }

            $table->add_data(array(
                "<a href='index.php?order=$record->id'>$record->id</a>",
                userdate($record->timecreated),
                $record->ccname,
                $authstrs->{$actionstatus->status},
                $actions
            ));
        }
    }

    $table->print_html();
}

/**
 * authorize_print_order_details
 *
 * @param int $orderno
 */
function authorize_print_order_details($orderno)
{
    global $CFG, $USER;
    global $strs, $authstrs;

    $cmdcapture = optional_param(ORDER_CAPTURE, '', PARAM_ALPHA);
    $cmddelete = optional_param(ORDER_DELETE, '', PARAM_ALPHA);
    $cmdrefund = optional_param(ORDER_REFUND, '', PARAM_ALPHA);
    $cmdvoid = optional_param(ORDER_VOID, '', PARAM_ALPHA);

    $unenrol = optional_param('unenrol', 0, PARAM_BOOL);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    $table = new stdClass;
    $table->width = '100%';
    $table->size = array('30%', '70%');
    $table->align = array('right', 'left');

    $sql = "SELECT E.*, C.shortname, C.enrolperiod FROM {$CFG->prefix}enrol_authorize E " .
           "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
           "WHERE E.id = '$orderno'";

    $order = get_record_sql($sql);
    if (!$order) {
        notice("Order $orderno not found.", "index.php");
        return;
    }

    if ($USER->id != $order->userid) { // Current user viewing someone else's order
        if (! isteacher($order->courseid)) {
           error("Students can view their order.");
        }
    }

    echo "<form action=\"index.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"order\" value=\"$orderno\">\n";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />";

    $settled = authorize_settled($order);
    $status = authorize_get_status_action($order);

    $table->data[] = array("<b>$authstrs->orderid:</b>", $orderno);
    $table->data[] = array("<b>$authstrs->transid:</b>", $order->transid);
    $table->data[] = array("<b>$authstrs->amount:</b>", "$order->currency $order->amount");
    if (empty($cmdcapture) and empty($cmdrefund) and empty($cmdvoid) and empty($cmddelete)) {
        $table->data[] = array("<b>$strs->course:</b>", $order->shortname);
        $table->data[] = array("<b>$strs->status:</b>", $authstrs->{$status->status});
        $table->data[] = array("<b>$authstrs->nameoncard:</b>", $order->ccname);
        $table->data[] = array("<b>$strs->time:</b>", userdate($order->timecreated));
        $table->data[] = array("<b>$authstrs->settlementdate:</b>", $settled ?
                               userdate($order->settletime) : $authstrs->notsettled);
    }
    $table->data[] = array("&nbsp;", "<hr size='1' noshade>\n");

    if (!empty($cmdcapture) and confirm_sesskey()) { // CAPTURE
        if (!in_array(ORDER_CAPTURE, $status->actions)) {
            $a = new stdClass;
            $a->action = $authstrs->capture;
            error(get_string('youcantdo', 'enrol_authorize', $a));
        }

        if (empty($confirm)) {
            $strcaptureyes = get_string('captureyes', 'enrol_authorize');
            $table->data[] = array("<b>$strs->confirm:</b>",
            "$strcaptureyes <br />
            <input type='hidden' name='confirm' value='1'><input type='submit' name='". ORDER_CAPTURE ."' value='$authstrs->capture' />
            &nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
        }
        else {
            $message = '';
            $extra = NULL;
            $success = authorize_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE);
            if (!$success) {
                $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", $message);
            }
            else {
                if (empty($CFG->an_test)) {
                    $timestart = $timeend = 0;
                    if ($order->enrolperiod) {
                        $timestart = time(); // early start
                        $timeend = $order->settletime + $order->enrolperiod; // lately end
                    }
                    if (enrol_student($order->userid, $order->courseid, $timestart, $timeend, 'manual')) {
                        $user = get_record('user', 'id', $order->userid);
                        $teacher = get_teacher($order->courseid);
                        $a = new stdClass;
                        $a->coursename = $order->shortname;
                        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
                        email_to_user($user,
                                      $teacher,
                                      get_string("enrolmentnew", '', $order->shortname),
                                      get_string('welcometocoursetext', '', $a));
                        redirect("index.php?order=$orderno");
                    }
                    else {
                         $table->data[] = array("<b><font color=red>$strs->error:</font></b>",
                         "Error while trying to enrol ".fullname($user)." in '$order->shortname'");
                    }
                }
                else {
                    $table->data[] = array(get_string('testmode', 'enrol_authorize'),
                                           get_string('testwarning', 'enrol_authorize'));
                }
            }
        }
        print_table($table);
    }
    elseif (!empty($cmdrefund) and confirm_sesskey()) { // REFUND
        if (!in_array(ORDER_REFUND, $status->actions)) {
            $a = new stdClass;
            $a->action = $authstrs->refund;
            error(get_string('youcantdo', 'enrol_authorize', $a));
        }

        $extra = new stdClass;
        $extra->sum = 0.0;
        $extra->orderid = $orderno;

        $sql = "SELECT SUM(amount) AS refunded FROM {$CFG->prefix}enrol_authorize_refunds " .
               "WHERE (orderid = '" . $orderno . "') AND (status = '" . AN_STATUS_CREDIT . "')";

        if ($refund = get_record_sql($sql)) {
            $extra->sum = floatval($refund->refunded);
        }
        $upto = format_float($order->amount - $extra->sum, 2);
        if ($upto <= 0) {
            error("Refunded to original amount.");
        }
        else {
            $amount = format_float(optional_param('amount', $upto), 2);
            if (($amount > $upto) or empty($confirm)) {
                $a = new stdClass;
                $a->upto = $upto;
                $strcanbecredit = get_string('canbecredit', 'enrol_authorize', $a);
                $strhowmuch = get_string('howmuch', 'enrol_authorize');
                $cbunenrol = print_checkbox('unenrol', '1', !empty($unenrol), '', '', '', true);
                $table->data[] = array("<b>$authstrs->unenrolstudent</b>", $cbunenrol);
                $table->data[] = array("<b>$strhowmuch</b>",
                    "<input type='hidden' name='confirm' value='1'>
                     <input type='text' size='5' name='amount' value='$amount'>
                     $strcanbecredit<br /><input type='submit' name='".ORDER_REFUND."' value='$authstrs->refund'>");
            }
            else {
                $extra->amount = $amount;
                $message = '';
                $success = authorize_action($order, $message, $extra, AN_ACTION_CREDIT);
                if ($success) {
                    if (empty($CFG->an_test)) {
                        if (empty($extra->id)) {
                            $table->data[] = array("<b><font color=red>$strs->error:</font></b>", 'insert record error');
                        }
                        else {
                            if (!empty($unenrol)) {
                                unenrol_student($order->userid, $order->courseid);
                            }
                            redirect("index.php?order=$orderno");
                        }
                    }
                    else {
                        $table->data[] = array(get_string('testmode', 'enrol_authorize'),
                                               get_string('testwarning', 'enrol_authorize'));
                    }
                }
                else {
                    $table->data[] = array("<b><font color=red>$strs->error:</font></b>", $message);
                }
            }
        }
        print_table($table);
    }
    elseif (!empty($cmdvoid) and confirm_sesskey()) { // VOID
        $suborderno = optional_param('suborder', 0, PARAM_INT);
        if (empty($suborderno)) { // cancel original transaction.
            if (!in_array(ORDER_VOID, $status->actions)) {
                $a = new stdClass;
                $a->action = $authstrs->void;
                error(get_string('youcantdo', 'enrol_authorize', $a));
            }
            if (empty($confirm)) {
                $strvoidyes = get_string('voidyes', 'enrol_authorize');
                $table->data[] = array("<b>$strs->confirm:</b>",
                    "$strvoidyes<br /><input type='hidden' name='".ORDER_VOID."' value='y'>
                     <input type='hidden' name='confirm' value='1'>
                     <input type='submit' value='$authstrs->void'>
                     &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
            }
            else {
                $extra = NULL;
                $message = '';
                $success = authorize_action($order, $message, $extra, AN_ACTION_VOID);
                if ($success) {
                    if (empty($CFG->an_test)) {
                        redirect("index.php?order=$orderno");
                    }
                    else {
                       $table->data[] = array(get_string('testmode', 'enrol_authorize'),
                                              get_string('testwarning', 'enrol_authorize'));
                    }
                }
                else {
                    $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", $message);
                }
            }
        }
        else { // cancel refunded transaction
            $sql = "SELECT R.*, E.courseid FROM {$CFG->prefix}enrol_authorize_refunds R " .
                   "INNER JOIN {$CFG->prefix}enrol_authorize E ON R.orderid = E.id " .
                   "WHERE R.id = '$suborderno' AND R.orderid = '$orderno' AND R.status = '" .AN_STATUS_CREDIT. "'";

            $suborder = get_record_sql($sql);
            if (!$suborder) { // not found
                error("Transaction can not be voided because of already been voided.");
            }
            else {
                $refundedstatus = authorize_get_status_action($suborder);
                if (!in_array(ORDER_VOID, $refundedstatus->actions)) {
                    $a = new stdClass;
                    $a->action = $authstrs->void;
                    error(get_string('youcantdo', 'enrol_authorize', $a));
                }
                unset($suborder->courseid);
                if (empty($confirm)) {
                    $a = new stdClass;
                    $a->transid = $suborder->transid;
                    $a->amount = $suborder->amount;
                    $strsubvoidyes = get_string('subvoidyes', 'enrol_authorize', $a);
                    $cbunenrol = print_checkbox('unenrol', '1', !empty($unenrol), '', '', '', true);
                    $table->data[] = array("<b>$authstrs->unenrolstudent</b>", $cbunenrol);
                    $table->data[] = array("<b>$strs->confirm:</b>",
                        "$strsubvoidyes<br /><input type='hidden' name='".ORDER_VOID."' value='y'>
                         <input type='hidden' name='confirm' value='1'>
                         <input type='hidden' name='suborder' value='$suborderno'>
                         <input type='submit' value='$authstrs->void'>
                         &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
                }
                else {
                    $message = '';
                    $extra = NULL;
                    $success = authorize_action($suborder, $message, $extra, AN_ACTION_VOID);
                    if ($success) {
                        if (empty($CFG->an_test)) {
                            if (!empty($unenrol)) {
                                unenrol_student($order->userid, $order->courseid);
                            }
                            redirect("index.php?order=$orderno");
                        }
                        else {
                            $table->data[] = array(get_string('testmode', 'enrol_authorize'),
                                                   get_string('testwarning', 'enrol_authorize'));
                        }
                    }
                    else {
                        $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", $message);
                    }
                }
            }
        }
        print_table($table);
    }
    elseif (!empty($cmddelete) and confirm_sesskey()) { // DELETE
        if (!in_array(ORDER_DELETE, $status->actions)) {
            $a = new stdClass;
            $a->action = $authstrs->delete;
            error(get_string('youcantdo', 'enrol_authorize', $a));
        }
        if (empty($confirm)) {
            $cbunenrol = print_checkbox('unenrol', '1', !empty($unenrol), '', '', '', true);
            $table->data[] = array("<b>$authstrs->unenrolstudent</b>", $cbunenrol);
            $table->data[] = array("<b>$strs->confirm:</b>",
                "<input type='hidden' name='".ORDER_DELETE."' value='y'>
                 <input type='hidden' name='confirm' value='1'>
                 <input type='submit' value='$authstrs->delete'>
                 &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
        }
        else {
            if (!empty($unenrol)) {
                unenrol_student($order->userid, $order->courseid);
            }
            delete_records('enrol_authorize', 'id', $orderno);
            redirect("index.php");
        }
        print_table($table);
    }
    else { // SHOW
        $actions = '';
        if (empty($status->actions)) {
            $actions .= $strs->none;
        }
        else {
            foreach ($status->actions as $value) {
                $actions .= "<input type='submit' name='$value' value='{$authstrs->$value}'> ";
            }
        }
        $table->data[] = array("<b>$strs->action</b>", $actions);
        print_table($table);
        if ($settled) { // show refunds.
            $t2 = new stdClass;
            $t2->size = array('45%', '15%', '20%', '10%', '10%');
            $t2->align = array('right', 'right', 'right', 'right', 'right');
            $t2->head = array($authstrs->settlementdate,
                              $authstrs->transid,
                              $strs->status,
                              $strs->action,
                              $authstrs->amount);

            $sql = "SELECT R.*, E.courseid FROM {$CFG->prefix}enrol_authorize_refunds R " .
                   "INNER JOIN {$CFG->prefix}enrol_authorize E ON R.orderid = E.id " .
                   "WHERE R.orderid = '$orderno'";

            $refunds = get_records_sql($sql);
            if ($refunds) {
                $sumrefund = floatval(0.0);
                foreach ($refunds as $rf) {
                    $substatus = authorize_get_status_action($rf);
                    $subactions = '&nbsp;';
                    if (empty($substatus->actions)) {
                        $subactions .= $strs->none;
                    }
                    else {
                        foreach ($substatus->actions as $vl) {
                            $subactions .=
                            "<a href='index.php?$vl=y&amp;sesskey=$USER->sesskey&amp;order=$orderno&amp;suborder=$rf->id'>{$authstrs->$vl}</a> ";
                        }
                    }
                    $sign = '';
                    $color = '';
                    switch ($substatus->status) {
                        case 'cancelled':
                            $color = 'black';
                            break;

                        case 'refunded':
                            $sign = '-';
                            $color = 'red';
                            $sumrefund += floatval($rf->amount);
                            break;

                        case 'settled':
                        default:
                            $sign = '-';
                            $color = 'green';
                            $sumrefund += floatval($rf->amount);
                            break;
                    }

                    $t2->data[] = array(
                        userdate($rf->settletime),
                        $rf->transid,
                        $authstrs->{$substatus->status},
                        $subactions,
                        "<font color='$color'>" . format_float($sign . $rf->amount, 2) . "</font>"
                    );
                }
                $t2->data[] = array('','',get_string('total'),$order->currency,format_float('-'.$sumrefund, 2));
            }
            else {
                $t2->data[] = array('','',get_string('noreturns', 'enrol_authorize'),'','');
            }
            echo "<h4>" . get_string('returns', 'enrol_authorize') . "</h4>\n";
            print_table($t2);
        }
    }
    echo '</form>';
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
    static $newordertime;

    if (empty($newordertime)) {
        $newordertime = time() - 120; // -2 minutes. Order may be still in process.
    }

    $ret = new stdClass();
    $ret->actions = array();

    if (intval($order->transid) == 0) { // test transaction or new order
        if ($order->timecreated < $newordertime) {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
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
        if (authorize_expired($order)) {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
                $ret->actions = array(ORDER_DELETE);
            }
            $ret->status = 'expired';
        }
        else {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
                $ret->actions = array(ORDER_CAPTURE, ORDER_VOID);
            }
            $ret->status = 'authorizedpendingcapture';
        }
        return $ret;

    case AN_STATUS_AUTHCAPTURE:
        if (authorize_settled($order)) {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
                $ret->actions = array(ORDER_REFUND);
            }
            $ret->status = 'capturedsettled';
        }
        else {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
                $ret->actions = array(ORDER_VOID);
            }
            $ret->status = 'capturedpendingsettle';
        }
        return $ret;

    case AN_STATUS_CREDIT:
        if (authorize_settled($order)) {
            $ret->status = 'settled';
        }
        else {
            if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
                $ret->actions = array(ORDER_VOID);
            }
            $ret->status = 'refunded';
        }
        return $ret;

    case AN_STATUS_VOID:
        $ret->status = 'cancelled';
        return $ret;

    case AN_STATUS_EXPIRE:
        if (isadmin() || (!empty($CFG->an_teachermanagepay) && isteacher($order->courseid))) {
            $ret->actions = array(ORDER_DELETE);
        }
        $ret->status = 'expired';
        return $ret;

    default:
        return $ret;
    }
}
?>
