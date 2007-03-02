<?php // $Id$

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
    global $CFG, $SESSION, $USER;

    $sql = "SELECT id FROM {$CFG->prefix}enrol_authorize WHERE userid = '$USER->id' AND courseid = '$course->id' ";

    if (empty($CFG->an_test)) { // Real mode
        $sql .= 'AND status IN('.AN_STATUS_AUTH.','.AN_STATUS_UNDERREVIEW.','.AN_STATUS_APPROVEDREVIEW.')';
    }
    else { // Test mode
        $sql .= 'AND status='.AN_STATUS_NONE;
    }

    if ($recid = get_field_sql($sql)) {
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

function email_to_admin($subject, $data)
{
    global $SITE;

    $admin = get_admin();
    $data = (array)$data;

    $message = "$SITE->fullname: Transaction failed.\n\n$subject\n\n";
    $message .= print_r($data, true);
    email_to_user($admin, $admin, "$SITE->fullname: Authorize.net ERROR", $message);
}


function send_welcome_messages($orderdata)
{
    global $CFG, $SITE;

    if (empty($orderdata)) {
        return;
    }

    if (is_numeric($orderdata)) {
        $orderdata = array($orderdata);
    }

    $select = "SELECT e.id, e.courseid, e.userid, c.fullname
                 FROM {$CFG->prefix}enrol_authorize e
                 INNER JOIN {$CFG->prefix}course c ON c.id = e.courseid
               WHERE e.id IN(" . implode(',', $orderdata) . ")
               ORDER BY e.userid";

    $emailinfo = get_records_sql($select);
    if (1 == count($emailinfo)) {
        $ei = reset($emailinfo);
        $context = get_context_instance(CONTEXT_COURSE, $ei->courseid);
        $paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments', '', '', '0', '1');
        $sender = array_shift($paymentmanagers);
    }
    else {
        $sender = get_admin();
    }

    $ei = reset($emailinfo);
    while ($ei !== false) {
        $usercourses = array();
        $lastuserid = $ei->userid;
        for ($current = $ei; $current !== false && $current->userid == $lastuserid; $current = next($emailinfo)) {
            $usercourses[] = $current->fullname;
        }
        $ei = $current;
        $a = new stdClass;
        $a->courses = implode("\n", $usercourses);
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$lastuserid";
        $a->paymenturl = "$CFG->wwwroot/enrol/authorize/index.php?user=$lastuserid";
        $emailmessage = get_string('welcometocoursesemail', 'enrol_authorize', $a);
        $user = get_record('user', 'id', $lastuserid);
        @email_to_user($user, $sender, get_string("enrolmentnew", '', $SITE->shortname), $emailmessage);
    }
}


function check_openssl_loaded()
{
    return extension_loaded('openssl');
}

?>
