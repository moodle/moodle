<?php // $Id$

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
function authorize_print_orders($courseid, $userid)
{
    global $CFG, $USER, $SITE;
    global $strs, $authstrs;
    require_once($CFG->libdir.'/tablelib.php');

    $perpage = 10;
    $status = optional_param('status', AN_STATUS_NONE, PARAM_INT);
    $searchtype = optional_param('searchtype', 'id', PARAM_ALPHA);
    $idortransid = optional_param('idortransid', '0', PARAM_INT);
    $showonlymy = optional_param('showonlymy', 0, PARAM_BOOL);

    $canmanagepayments = has_capability('enrol/authorize:managepayments', get_context_instance(CONTEXT_COURSE, $courseid));

    if ($showonlymy || !$canmanagepayments) {
        $userid = $USER->id;
    }

    $baseurl = $CFG->wwwroot."/enrol/authorize/index.php?user=$userid";
    $statusmenu = array(AN_STATUS_NONE => $strs->all,
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

    $sql = "SELECT c.id, c.fullname FROM {$CFG->prefix}course c INNER JOIN {$CFG->prefix}enrol_authorize e ON c.id = e.courseid ";
    if ($userid > 0) {
        $sql .= "WHERE (e.userid='$userid') ";
    }
    $sql .= "ORDER BY c.sortorder, c.fullname";
    if (($popupcrs = get_records_sql_menu($sql))) {
        $popupcrs = array($SITE->id => $SITE->fullname) + $popupcrs;
        echo "<table border='0' width='100%' cellspacing='0' cellpadding='3' class='generaltable generalbox'>";
        echo "<tr>";
        echo "<td width='5%' valign='top'>$strs->status: </td><td width='10%'>";
        popup_form($baseurl.'&amp;course='.$courseid.'&amp;status=',$statusmenu,'statusmenu',$status,'','','',false);
        if ($canmanagepayments) {
            echo "<br />\n";
            print_checkbox('showonlymy', '1', $userid == $USER->id, get_string('mypaymentsonly', 'enrol_authorize'), '',
            "var locationtogo = '{$CFG->wwwroot}/enrol/authorize/index.php?status=$status&amp;course=$courseid';
                                  locationtogo += '&amp;user=' + (this.checked ? '$USER->id' : '0');
                                  top.location.href=locationtogo;");
        }
        echo "</td>\n";
        echo "<td width='5%' valign='top'>$strs->course: </td><td width='10%' valign='top'>";
        popup_form($baseurl.'&amp;status='.$status.'&amp;course=',$popupcrs,'coursesmenu',$courseid,'','','',false);echo"</td>\n";
        if (has_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_USER, $USER->id))) {
            echo "<form method='get' action='uploadcsv.php'>";
            echo "<td rowspan='2' align='right' valign='middle' width='50%'><div><input type='submit' value='".get_string('uploadcsv', 'enrol_authorize')."' /></div></td>";
            echo "</form>";
        }
        else {
            echo "<td rowspan=2 width='100%'>&nbsp;</td>";
        }
        echo "</tr>\n";

        echo "<tr><td>$strs->search: </td>"; $searchmenu = array('id' => $authstrs->orderid, 'transid' => $authstrs->transid);
        echo "<form method='POST' action='index.php' autocomplete='off'>";
        echo "<td colspan='3'>"; choose_from_menu($searchmenu, 'searchtype', $searchtype, '');
        echo " = <fieldset class=\"invisiblefieldset\"><input type='text' size='14' name='idortransid' value='' /> ";
        echo "<input type='submit' value='$strs->search' /></fieldset></td>";
        echo "</form>";
        echo "</tr>";
        echo "</table>";
    }

    $table = new flexible_table('enrol-authorize');
    $table->set_attribute('width', '100%');
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('cellpadding', '3');
    $table->set_attribute('id', 'orders');
    $table->set_attribute('class', 'generaltable generalbox');

    $table->define_columns(array('id', 'timecreated', 'userid', 'status', ''));
    $table->define_headers(array($authstrs->orderid, $strs->time, $authstrs->nameoncard, $strs->status, $strs->action));
    $table->define_baseurl($baseurl."&amp;status=$status&amp;course=$courseid");

    $table->sortable(true, 'id', SORT_DESC);
    $table->pageable(true);
    $table->setup();

    $select = "SELECT e.id, e.paymentmethod, e.transid, e.courseid, e.userid, e.status, e.ccname, e.timecreated, e.settletime ";
    $from   = "FROM {$CFG->prefix}enrol_authorize e ";
    $where  = "WHERE (1=1) ";

    if ($status > AN_STATUS_NONE) {
        switch ($status)
        {
            case AN_STATUS_AUTH | AN_STATUS_UNDERREVIEW | AN_STATUS_APPROVEDREVIEW:
                $where .= 'AND (e.status IN('.AN_STATUS_AUTH.','.AN_STATUS_UNDERREVIEW.','.AN_STATUS_APPROVEDREVIEW.')) ';
                break;

            case AN_STATUS_CREDIT:
                $from .= "INNER JOIN {$CFG->prefix}enrol_authorize_refunds r ON e.id = r.orderid ";
                $where .= "AND (e.status = '" . AN_STATUS_AUTHCAPTURE . "') ";
                break;

            case AN_STATUS_TEST:
                $newordertime = time() - 120; // -2 minutes. Order may be still in process.
                $where .= "AND (e.status = '" . AN_STATUS_NONE . "') AND (e.transid = '0') AND (e.timecreated < $newordertime) ";
                break;

            default:
                $where .= "AND (e.status = '$status') ";
                break;
        }
    }
    else {
        if (empty($CFG->an_test)) {
            $where .= "AND (e.status != '" . AN_STATUS_NONE . "') ";
        }
    }

    if ($courseid != SITEID) {
        $where .= "AND (e.courseid = '" . $courseid . "') ";
    }

    if (!empty($idortransid)) {
        // Ignore old where.
        if ($searchtype == 'transid') {
            $where = "WHERE (e.transid = $idortransid) ";
        }
        else {
            $where = "WHERE (e.id = $idortransid) ";
        }
    }

    // This must be always last where!!!
    if ($userid > 0) {
        $where .= "AND (e.userid = '" . $userid . "') ";
    }

    if (($sort = $table->get_sql_sort())) {
        $sort = ' ORDER BY ' . $sort;
    }

    $totalcount = count_records_sql('SELECT COUNT(*) ' . $from . $where);
    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $totalcount);

    if (($records = get_records_sql($select . $from . $where . $sort, $table->get_page_start(), $table->get_page_size()))) {
        foreach ($records as $record) {
            $actionstatus = authorize_get_status_action($record);
            $color = authorize_get_status_color($actionstatus->status);
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
                "<font style='color:$color'>" . $authstrs->{$actionstatus->status} . "</font>",
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

    $order = get_record('enrol_authorize', 'id', $orderno);
    if (!$order) {
        notice("Order $orderno not found.", "index.php");
        return;
    }

    $course = get_record('course', 'id', $order->courseid);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    if ($USER->id != $order->userid) { // Current user viewing someone else's order
        require_capability('enrol/authorize:managepayments', $coursecontext);
    }

    echo "<form action=\"index.php\" method=\"post\">\n";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"order\" value=\"$orderno\" />\n";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />";

    $settled = authorize_settled($order);
    $status = authorize_get_status_action($order);

    $table->data[] = array("<b>$authstrs->paymentmethod:</b>",
                          ($order->paymentmethod == AN_METHOD_CC ? $authstrs->methodcc : $authstrs->methodecheck));
    $table->data[] = array("<b>$authstrs->orderid:</b>", $orderno);
    $table->data[] = array("<b>$authstrs->transid:</b>", $order->transid);
    $table->data[] = array("<b>$authstrs->amount:</b>", "$order->currency $order->amount");
    if (empty($cmdcapture) and empty($cmdrefund) and empty($cmdvoid) and empty($cmddelete)) {
        $color = authorize_get_status_color($status->status);
        $table->data[] = array("<b>$strs->course:</b>", format_string($course->shortname));
        $table->data[] = array("<b>$strs->status:</b>", "<font style='color:$color'>" . $authstrs->{$status->status} . "</font>");
        if ($order->paymentmethod == AN_METHOD_CC) {
            $table->data[] = array("<b>$authstrs->nameoncard:</b>", $order->ccname);
        }
        else {
            $table->data[] = array("<b>$authstrs->echeckfirslasttname:</b>", $order->ccname);
        }
        $table->data[] = array("<b>$strs->time:</b>", userdate($order->timecreated));
        $table->data[] = array("<b>$authstrs->settlementdate:</b>", $settled ?
                               userdate($order->settletime) : $authstrs->notsettled);
    }
    $table->data[] = array("&nbsp;", "<hr size='1' />\n");

    if (!empty($cmdcapture) and confirm_sesskey()) { // CAPTURE
        if (!in_array(ORDER_CAPTURE, $status->actions)) {
            $a = new stdClass;
            $a->action = $authstrs->capture;
            print_error('youcantdo', 'enrol_authorize', '', $a);
        }

        if (empty($confirm)) {
            $strcaptureyes = get_string('captureyes', 'enrol_authorize');
            $table->data[] = array("<b>$strs->confirm:</b>",
            "$strcaptureyes <br />
            <input type='hidden' name='confirm' value='1' /><input type='submit' name='". ORDER_CAPTURE ."' value='$authstrs->capture' />
            &nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
        }
        else {
            $message = '';
            $extra = NULL;
            if (AN_APPROVED != authorize_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE)) {
                $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", $message);
            }
            else {
                if (empty($CFG->an_test)) {
                    $user = get_record('user', 'id', $order->userid);
                    if (enrol_into_course($course, $user, 'authorize')) {
                        if (!empty($CFG->enrol_mailstudents)) {
                            send_welcome_messages($order->id);
                        }
                        redirect("index.php?order=$orderno");
                    }
                    else {
                        $table->data[] = array("<b><font color='red'>$strs->error:</font></b>",
                        "Error while trying to enrol ".fullname($user)." in '" . format_string($course->shortname) . "'");
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
            print_error('youcantdo', 'enrol_authorize', '', $a);
        }

        $refunded = 0.0;
        $sql = "SELECT SUM(amount) AS refunded FROM {$CFG->prefix}enrol_authorize_refunds " .
               "WHERE (orderid = '" . $orderno . "') AND (status = '" . AN_STATUS_CREDIT . "')";

        if (($refundval = get_field_sql($sql))) {
            $refunded = floatval($refundval);
        }
        $upto = round($order->amount - $refunded, 2);
        if ($upto <= 0) {
            error("Refunded to original amount.");
        }
        else {
            $amount = round(optional_param('amount', $upto), 2);
            if (($amount > $upto) or empty($confirm)) {
                $a = new stdClass;
                $a->upto = $upto;
                $strcanbecredit = get_string('canbecredit', 'enrol_authorize', $a);
                $strhowmuch = get_string('howmuch', 'enrol_authorize');
                $cbunenrol = print_checkbox('unenrol', '1', !empty($unenrol), '', '', '', true);
                $table->data[] = array("<b>$authstrs->unenrolstudent</b>", $cbunenrol);
                $table->data[] = array("<b>$strhowmuch</b>",
                    "<input type='hidden' name='confirm' value='1' />
                     <input type='text' size='5' name='amount' value='$amount' />
                     $strcanbecredit<br /><input type='submit' name='".ORDER_REFUND."' value='$authstrs->refund' />");
            }
            else {
                $extra = new stdClass;
                $extra->orderid = $orderno;
                $extra->amount = $amount;
                $message = '';
                $success = authorize_action($order, $message, $extra, AN_ACTION_CREDIT);
                if (AN_APPROVED == $success || AN_REVIEW == $success) {
                    if (empty($CFG->an_test)) {
                        if (empty($extra->id)) {
                            $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", 'insert record error');
                        }
                        else {
                            if (!empty($unenrol)) {
                                role_unassign(0, $order->userid, 0, $coursecontext->id);
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
                    $table->data[] = array("<b><font color='red'>$strs->error:</font></b>", $message);
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
                print_error('youcantdo', 'enrol_authorize', '', $a);
            }
            if (empty($confirm)) {
                $strvoidyes = get_string('voidyes', 'enrol_authorize');
                $table->data[] = array("<b>$strs->confirm:</b>",
                    "$strvoidyes<br /><input type='hidden' name='".ORDER_VOID."' value='y' />
                     <input type='hidden' name='confirm' value='1' />
                     <input type='submit' value='$authstrs->void' />
                     &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
            }
            else {
                $extra = NULL;
                $message = '';
                if (AN_APPROVED == authorize_action($order, $message, $extra, AN_ACTION_VOID)) {
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
            $sql = "SELECT r.*, e.courseid, e.paymentmethod FROM {$CFG->prefix}enrol_authorize_refunds r " .
                   "INNER JOIN {$CFG->prefix}enrol_authorize e ON r.orderid = e.id " .
                   "WHERE r.id = '$suborderno' AND r.orderid = '$orderno' AND r.status = '" .AN_STATUS_CREDIT. "'";

            $suborder = get_record_sql($sql);
            if (!$suborder) { // not found
                error("Transaction can not be voided because of already been voided.");
            }
            else {
                $refundedstatus = authorize_get_status_action($suborder);
                if (!in_array(ORDER_VOID, $refundedstatus->actions)) {
                    $a = new stdClass;
                    $a->action = $authstrs->void;
                    print_error('youcantdo', 'enrol_authorize', '', $a);
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
                        "$strsubvoidyes<br /><input type='hidden' name='".ORDER_VOID."' value='y' />
                         <input type='hidden' name='confirm' value='1' />
                         <input type='hidden' name='suborder' value='$suborderno' />
                         <input type='submit' value='$authstrs->void' />
                         &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
                }
                else {
                    $message = '';
                    $extra = NULL;
                    if (AN_APPROVED == authorize_action($suborder, $message, $extra, AN_ACTION_VOID)) {
                        if (empty($CFG->an_test)) {
                            if (!empty($unenrol)) {
                                role_unassign(0, $order->userid, 0, $coursecontext->id);
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
            print_error('youcantdo', 'enrol_authorize', '', $a);
        }
        if (empty($confirm)) {
            $cbunenrol = print_checkbox('unenrol', '1', !empty($unenrol), '', '', '', true);
            $table->data[] = array("<b>$authstrs->unenrolstudent</b>", $cbunenrol);
            $table->data[] = array("<b>$strs->confirm:</b>",
                "<input type='hidden' name='".ORDER_DELETE."' value='y' />
                 <input type='hidden' name='confirm' value='1' />
                 <input type='submit' value='$authstrs->delete' />
                 &nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?order=$orderno'>$strs->no</a>");
        }
        else {
            if (!empty($unenrol)) {
                role_unassign(0, $order->userid, 0, $coursecontext->id);
            }
            delete_records('enrol_authorize', 'id', $orderno);
            redirect("index.php");
        }
        print_table($table);
    }
    else { // SHOW
        $actions = '';
        if (empty($status->actions)) {
            if (($order->paymentmethod == AN_METHOD_ECHECK) && has_capability('enrol/authorize:uploadcsv', get_context_instance(CONTEXT_USER, $USER->id))) {
                $actions .= '<a href="uploadcsv.php">'.get_string('uploadcsv', 'enrol_authorize').'</a>';
            }
            else {
                $actions .= $strs->none;
            }
        }
        else {
            foreach ($status->actions as $value) {
                $actions .= "<input type='submit' name='$value' value='{$authstrs->$value}' /> ";
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

            $sql = "SELECT r.*, e.courseid, e.paymentmethod FROM {$CFG->prefix}enrol_authorize_refunds r " .
                   "INNER JOIN {$CFG->prefix}enrol_authorize e ON r.orderid = e.id " .
                   "WHERE r.orderid = '$orderno'";

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
                    $color = authorize_get_status_color($substatus->status);
                    if ($substatus->status == 'refunded' or $substatus->status == 'settled') {
                        $sign = '-';
                        $sumrefund += floatval($rf->amount);
                    }
                    $t2->data[] = array(
                        userdate($rf->settletime),
                        $rf->transid,
                        "<font style='color:$color'>" .$authstrs->{$substatus->status} . "</font>",
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
            print_table($t2);
        }
    }
    echo '</div>';
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
        if (authorize_expired($order)) {
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
        if (authorize_settled($order)) {
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
        if (authorize_settled($order)) {
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
        case 'approvedreview':
        case 'capturedpendingsettle':
            $color = '#339900'; // green
            break;

        case 'new':
        case 'tested':
        case 'underreview':
        case 'authorizedpendingcapture':
            $color = '#FF6600'; // orange
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
?>
