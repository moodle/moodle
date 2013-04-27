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
 * @package    enrol_authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/eventslib.php');

function get_course_cost($plugininstance) {
    $defaultplugin = enrol_get_plugin('authorize');

    $cost = (float)0;
    $currency = (!empty($plugininstance->currency))
                 ? $plugininstance->currency :( empty($defaultplugin->currency)
                                        ? 'USD' : $defaultplugin->enrol_currency );

    if (!empty($plugininstance->cost)) {
        $cost = (float)(((float)$plugininstance->cost) < 0) ? $defaultplugin->cost : $plugininstance->cost;
    }

    $cost = format_float($cost, 2);
    $ret = array(
        'cost' => $cost,
        'currency' => $currency
    );

    return $ret;
}

function zero_cost($plugininstance) {
    $curcost = get_course_cost($plugininstance);
    return (abs($curcost['cost']) < 0.01);
}

function prevent_double_paid($plugininstance) {
    global $CFG, $SESSION, $USER, $DB;
    $plugin = enrol_get_plugin('authorize');

    $sql = "SELECT id FROM {enrol_authorize} WHERE userid = ? AND courseid = ? AND instanceid = ?";
    $params = array($USER->id, $plugininstance->courseid, $plugininstance->id);

    if (!$plugin->get_config('an_test')) { // Real mode
        $sql .= ' AND status IN(?,?,?)';
        $params[] = AN_STATUS_AUTH;
        $params[] = AN_STATUS_UNDERREVIEW;
        $params[] = AN_STATUS_APPROVEDREVIEW;
    }
    else { // Test mode
        $sql .= ' AND status=?';
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

function get_list_of_creditcards($getall = false) {
    $plugin = enrol_get_plugin('authorize');

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

    if ($getall) {
        return $alltypes;
    }

    $ret = array();
    foreach ($alltypes as $code=>$name) {
        if ($plugin->get_config("an_acceptcc_{$code}")) {
            $ret[$code] = $name;
        }
    }

    return $ret;
}

function get_list_of_payment_methods($getall = false) {
    $plugin = enrol_get_plugin('authorize');
    $method_cc = $plugin->get_config('an_acceptmethod_cc');
    $method_echeck = $plugin->get_config('an_acceptmethod_echeck');


    if ($getall || (empty($method_cc) && empty($method_echeck))) {
        return array(AN_METHOD_CC, AN_METHOD_ECHECK);
    } else {
        $methods = array();
        if ($method_cc) {
            $methods[] = AN_METHOD_CC;
        }

        if ($method_echeck) {
            $methods[] = AN_METHOD_ECHECK;
        }

        return $methods;
    }
}

function get_list_of_bank_account_types($getall = false) {
    $plugin = enrol_get_plugin('authorize');
    $alltypes = array('CHECKING', 'BUSINESSCHECKING', 'SAVINGS');

    if ($getall) {
        return $alltypes;
    } else {
        $types = array();
        foreach ($alltypes as $type) {
            if ($plugin->get_config("an_acceptecheck_{$type}")) {
                $types[] = $type;
            }
        }

        return $types;
    }
}

function message_to_admin($subject, $data) {
    global $SITE;

    $admin = get_admin();
    $data = (array)$data;

    $emailmessage = "$SITE->fullname: Transaction failed.\n\n$subject\n\n";
    $emailmessage .= print_r($data, true);
    $eventdata = new stdClass();
    $eventdata->modulename        = 'moodle';
    $eventdata->component         = 'enrol_authorize';
    $eventdata->name              = 'authorize_enrolment';
    $eventdata->userfrom          = $admin;
    $eventdata->userto            = $admin;
    $eventdata->subject           = "$SITE->fullname: Authorize.net ERROR";
    $eventdata->fullmessage       = $emailmessage;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    message_send($eventdata);
}

function send_welcome_messages($orderdata) {
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

    $rs = $DB->get_recordset_sql($sql);
    if (!$rs->valid()) {
        $rs->close(); // Not going to iterate (but exit), close rs
        return;
    }

    if ($rs->valid() and $ei = current($rs))
    {
        if (1 < count($orderdata)) {
            $sender = get_admin();
        }
        else {
            $context = context_course::instance($ei->courseid);
            $paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments', '', '', '0', '1');
            $sender = array_shift($paymentmanagers);
        }

        do
        {
            $usercourses = array();
            $lastuserid = $ei->userid;

            while ($ei && $ei->userid == $lastuserid) {
                $context = context_course::instance($ei->courseid);
                $usercourses[] = format_string($ei->fullname, true, array('context' => $context));
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
                $subject = get_string("enrolmentnew", 'enrol', format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))));

                $eventdata = new stdClass();
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_authorize';
                $eventdata->name              = 'authorize_enrolment';
                $eventdata->userfrom          = $sender;
                $eventdata->userto            = $user;
                $eventdata->subject           = $subject;
                $eventdata->fullmessage       = $emailmessage;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }
        while ($ei);

        $rs->close(); // end of iteration, close rs
    }
}

function check_curl_available() {
    return function_exists('curl_init') &&
           function_exists('stream_get_wrappers') &&
           in_array('https', stream_get_wrappers());
}

function authorize_verify_account() {
    global $USER, $SITE;
    $plugin = enrol_get_plugin('authorize');

    require_once('authorizenet.class.php');

    $original_antest = $plugin->get_config('an_test');
    $plugin->set_config('an_test', 1); // Test mode
    $shortname = format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID)));

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
    $extra->x_description = $shortname . ' - Authorize.net Merchant Account Verification Test';

    $ret = '';
    $message = '';
    if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, AN_ACTION_AUTH_CAPTURE)) {
        $ret = get_string('verifyaccountresult', 'enrol_authorize', get_string('success'));
    }
    else {
        $ret = get_string('verifyaccountresult', 'enrol_authorize', $message);
    }

    $plugin->set_config('an_test', $original_antest);

    return $ret;
}


