<?php

require_once($CFG->libdir.'/eventslib.php');

function get_course_cost($course)
{
    global $CFG;

    $cost = (float)0;
    $currency = (!empty($course->currency))
                 ? $course->currency :( empty($CFG->enrol_currency)
                                        ? 'USD' : $CFG->enrol_currency );

    if (!empty($course->cost)) {
        $cost = (float)(((float)$course->cost) < 0) ? $CFG->enrol_cost : $course->cost;
    }

    $cost = format_float($cost, 2);
    $ret = array(
        'cost' => $cost,
        'currency' => $currency
    );

    return $ret;
}

function zero_cost($course) {
    $curcost = get_course_cost($course);
    return (abs($curcost['cost']) < 0.01);
}

function prevent_double_paid($course)
{
    global $CFG, $SESSION, $USER, $DB;

    $sql = "SELECT id FROM {enrol_authorize} WHERE userid = ? AND courseid = ? ";
    $params = array($USER->id, $course->id);

    if (empty($CFG->an_test)) { // Real mode
        $sql .= 'AND status IN(?,?,?)';
        $params[] = AN_STATUS_AUTH;
        $params[] = AN_STATUS_UNDERREVIEW;
        $params[] = AN_STATUS_APPROVEDREVIEW;
    }
    else { // Test mode
        $sql .= 'AND status=?';
        $params[] = AN_STATUS_NONE;
    }

    if (($recid = $DB->get_field_sql($sql, $params))) {
        $a = new stdClass;
        $a->orderid = $recid;
        $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$a->orderid";
        redirect($a->url, get_string("paymentpending", "enrol_authorize", $a), '10');
        return;
    }
    if (isset($SESSION->ccpaid)) {
        unset($SESSION->ccpaid);
        redirect($CFG->wwwroot . '/login/logout.php?sesskey='.sesskey());
        return;
    }
}

function get_list_of_creditcards($getall = false)
{
    global $CFG;

    $alltypes = array(
        'mcd' => 'Master Card',
        'vis' => 'Visa',
        'amx' => 'American Express',
        'dsc' => 'Discover',
        'dnc' => 'Diners Club',
        'jcb' => 'JCB',
        'swi' => 'Switch',
        'dlt' => 'Delta',
        'enr' => 'EnRoute'
    );

    if ($getall or empty($CFG->an_acceptccs)) {
        return $alltypes;
    }

    $ret = array();
    $ccs = explode(',', $CFG->an_acceptccs);
    foreach ($ccs as $key) {
        $ret[$key] = $alltypes[$key];
    }
    return $ret;
}

function get_list_of_payment_methods($getall = false)
{
    global $CFG;

    if ($getall || empty($CFG->an_acceptmethods)) {
        return array(AN_METHOD_CC, AN_METHOD_ECHECK);
    }
    else {
        return explode(',', $CFG->an_acceptmethods);
    }
}

function get_list_of_bank_account_types($getall = false)
{
    global $CFG;

    if ($getall || empty($CFG->an_acceptechecktypes)) {
        return array('CHECKING', 'BUSINESSCHECKING', 'SAVINGS');
    }
    else {
        return explode(',', $CFG->an_acceptechecktypes);
    }
}

function message_to_admin($subject, $data)
{
    global $SITE;

    $admin = get_admin();
    $data = (array)$data;

    $emailmessage = "$SITE->fullname: Transaction failed.\n\n$subject\n\n";
    $emailmessage .= print_r($data, true);
    $eventdata = new object();
    $eventdata->modulename        = 'moodle';
    $eventdata->userfrom          = $admin;
    $eventdata->userto            = $admin;
    $eventdata->subject           = "$SITE->fullname: Authorize.net ERROR";
    $eventdata->fullmessage       = $emailmessage;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    message_send($eventdata);
}

function send_welcome_messages($orderdata)
{
    global $CFG, $SITE, $DB;

    if (empty($orderdata)) {
        return;
    }

    if (is_numeric($orderdata)) {
        $orderdata = array($orderdata);
    }

    $sql = "SELECT e.id, e.courseid, e.userid, c.fullname
              FROM {enrol_authorize} e
              JOIN {course} c ON c.id = e.courseid
             WHERE e.id IN(" . implode(',', $orderdata) . ")
          ORDER BY e.userid";

    if (!$rs = $DB->get_recordset_sql($sql)) {
        return;
    }

    if ($rs->valid() and $ei = current($rs))
    {
        if (1 < count($orderdata)) {
            $sender = get_admin();
        }
        else {
            $context = get_context_instance(CONTEXT_COURSE, $ei->courseid);
            $paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments', '', '', '0', '1');
            $sender = array_shift($paymentmanagers);
        }

        do
        {
            $usercourses = array();
            $lastuserid = $ei->userid;

            while ($ei && $ei->userid == $lastuserid) {
                $usercourses[] = $ei->fullname;
                if (!$rs->valid()) {
                    break;
                }
                $rs->next();
                $ei = $rs->current();
            }

            if (($user = $DB->get_record('user', array('id'=>$lastuserid)))) {
                $a = new stdClass;
                $a->name = $user->firstname;
                $a->courses = implode("\n", $usercourses);
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$lastuserid";
                $a->paymenturl = "$CFG->wwwroot/enrol/authorize/index.php?user=$lastuserid";
                $emailmessage = get_string('welcometocoursesemail', 'enrol_authorize', $a);

                $eventdata = new object();
                $eventdata->modulename        = 'moodle';
                $eventdata->userfrom          = $sender;
                $eventdata->userto            = $user;
                $eventdata->subject           = get_string("enrolmentnew", '', $SITE->shortname);
                $eventdata->fullmessage       = $emailmessage;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }
        while ($ei);

        $rs->close();
    }
}

function check_curl_available()
{
    return function_exists('curl_init') &&
           function_exists('stream_get_wrappers') &&
           in_array('https', stream_get_wrappers());
}

function authorize_verify_account()
{
    global $CFG, $USER, $SITE;
    require_once('authorizenet.class.php');

    $original_antest = $CFG->an_test;
    $CFG->an_test = 1; // Test mode

    $order = new stdClass();
    $order->id = -1;
    $order->paymentmethod = AN_METHOD_CC;
    $order->refundinfo = '1111';
    $order->ccname = 'Test User';
    $order->courseid = $SITE->id;
    $order->userid = $USER->id;
    $order->status = AN_STATUS_NONE;
    $order->settletime = 0;
    $order->transid = 0;
    $order->timecreated = time();
    $order->amount = '0.01';
    $order->currency = 'USD';

    $extra = new stdClass();
    $extra->x_card_num = '4111111111111111';
    $extra->x_card_code = '123';
    $extra->x_exp_date = "12" . intval(date("Y")) + 5;
    $extra->x_currency_code = $order->currency;
    $extra->x_amount = $order->amount;
    $extra->x_first_name = 'Test';
    $extra->x_last_name = 'User';
    $extra->x_country = $USER->country;

    $extra->x_invoice_num = $order->id;
    $extra->x_description = $SITE->shortname . ' - Authorize.net Merchant Account Verification Test';

    $ret = '';
    $message = '';
    if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, AN_ACTION_AUTH_CAPTURE)) {
        $ret = get_string('verifyaccountresult', 'enrol_authorize', get_string('success'));
    }
    else {
        $ret = get_string('verifyaccountresult', 'enrol_authorize', $message);
    }
    $CFG->an_test = $original_antest;
    return $ret;
}


