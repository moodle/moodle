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

function ABAVal($aba)
{
    if (ereg("^[0-9]{9}$", $aba)) {
        $n = 0;
        for($i = 0; $i < 9; $i += 3) {
            $n += (substr($aba, $i, 1) * 3) +
                  (substr($aba, $i + 1, 1) * 7) +
                  (substr($aba, $i + 2, 1));
        }
        if ($n != 0 and $n % 10 == 0) {
            return true;
        }
    }
    return false;
}

function CCVal($Num, $Name = "n/a", $Exp = "")
{
    // Check the expiration date first
    if (strlen($Exp))
    {
        $Month = substr($Exp, 0, 2);
        $Year  = substr($Exp, -2);
        $WorkDate = "$Month/01/$Year";
        $WorkDate = strtotime($WorkDate);
        $LastDay  = date("t", $WorkDate);
        $Expires  = strtotime("$Month/$LastDay/$Year 11:59:59");
        if ($Expires < time()) return 0;
    }

    //  Innocent until proven guilty
    $GoodCard = true;

    //  Get rid of any non-digits
    $Num = ereg_replace("[^0-9]", "", $Num);

    // Perform card-specific checks, if applicable
    switch ($Name)
    {
        case "mcd" :
        $GoodCard = ereg("^5[1-5].{14}$", $Num);
        break;

        case "vis" :
        $GoodCard = ereg("^4.{15}$|^4.{12}$", $Num);
        break;

        case "amx" :
        $GoodCard = ereg("^3[47].{13}$", $Num);
        break;

        case "dsc" :
        $GoodCard = ereg("^6011.{12}$", $Num);
        break;

        case "dnc" :
        $GoodCard = ereg("^30[0-5].{11}$|^3[68].{12}$", $Num);
        break;

        case "jcb" :
        $GoodCard = ereg("^3.{15}$|^2131|1800.{11}$", $Num);
        break;

        case "dlt" :
        $GoodCard = ereg("^4.{15}$", $Num);
        break;

        case "swi" :
        $GoodCard = ereg("^[456].{15}$|^[456].{17,18}$", $Num);
        break;

        case "enr" :
        $GoodCard = ereg("^2014.{11}$|^2149.{11}$", $Num);
        break;
    }

    // The Luhn formula works right to left, so reverse the number.
    $Num = strrev($Num);
    $Total = 0;

    for ($x=0; $x < strlen($Num); $x++)
    {
        $digit = substr($Num, $x, 1);

        // If it's an odd digit, double it
        if ($x/2 != floor($x/2)) {
            $digit *= 2;

            // If the result is two digits, add them
            if (strlen($digit) == 2)
                $digit = substr($digit, 0, 1) + substr($digit, 1, 1);
        }
        // Add the current digit, doubled and added if applicable, to the Total
        $Total += $digit;
    }

    // If it passed (or bypassed) the card-specific check and the Total is
    // evenly divisible by 10, it's cool!
    return ($GoodCard && $Total % 10 == 0);
}

function validate_cc_form($form, &$err)
{
    global $CFG;

    if (empty($form->cc)) {
        $err['cc'] = get_string('missingcc', 'enrol_authorize');
    }
    if (empty($form->ccexpiremm) || empty($form->ccexpireyyyy)) {
        $err['ccexpire'] = get_string('missingccexpire', 'enrol_authorize');
    }
    else {
        $expdate = sprintf("%02d", intval($form->ccexpiremm)) . $form->ccexpireyyyy;
        $validcc = CCVal($form->cc, $form->cctype, $expdate);
        if (!$validcc) {
            if ($validcc === 0) {
                $err['ccexpire'] = get_string('ccexpired', 'enrol_authorize');
            }
            else {
                $err['cc'] = get_string('ccinvalid', 'enrol_authorize');
            }
        }
    }

    if (empty($form->firstname) || empty($form->lastname)) {
        $err['ccfirstlast'] = get_string('missingfullname');
    }

    if (empty($form->cvv) || !is_numeric($form->cvv)) {
        $err['cvv'] = get_string('missingcvv', 'enrol_authorize');
    }

    if (empty($form->cctype) or !in_array($form->cctype, array_keys(get_list_of_creditcards()))) {
        $err['cctype'] = get_string('missingcctype', 'enrol_authorize');
    }

    if (!empty($CFG->an_avs))
    {
        if (empty($form->ccaddress)) {
            $err['ccaddress'] = get_string('missingaddress', 'enrol_authorize');
        }
        if (empty($form->cccity)) {
            $err['cccity'] = get_string('missingcity');
        }
        if (empty($form->cccountry)) {
            $err['cccountry'] = get_string('missingcountry');
        }
    }

    if (empty($form->cczip) || !is_numeric($form->cczip)) {
        $err['cczip'] = get_string('missingzip', 'enrol_authorize');
    }

    if (!empty($err)) {
        $err['header'] = get_string('someerrorswerefound');
        return false;
    }

    return true;
}

function validate_echeck_form($form, &$err)
{
    global $CFG;

    if (empty($form->abacode) || !is_numeric($form->abacode)) {
        $err['abacode'] = get_string('missingaba', 'enrol_authorize');
    }
    elseif (!ABAVal($form->abacode)) {
        $err['abacode'] = get_string('invalidaba', 'enrol_authorize');
    }

    if (empty($form->accnum) || !is_numeric($form->accnum)) {
        $err['accnum'] = get_string('invalidaccnum', 'enrol_authorize');
    }

    if (empty($form->acctype) || !in_array($form->acctype, get_list_of_bank_account_types())) {
        $err['acctype'] = get_string('invalidacctype', 'enrol_authorize');
    }

    if (empty($form->bankname)) {
        $err['bankname'] = get_string('missingbankname', 'enrol_authorize');
    }

    if (empty($form->firstname) || empty($form->lastname)) {
        $err['firstlast'] = get_string('missingfullname');
    }

    if (!empty($err)) {
        $err['header'] = get_string('someerrorswerefound');
        return false;
    }

    return true;
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
