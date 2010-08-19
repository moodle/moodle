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

require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');

class AuthorizeNet
{
    const AN_DELIM = '|';
    const AN_ENCAP = '"';

    const AN_REASON_NOCCTYPE    =   17;
    const AN_REASON_NOCCTYPE2   =   28;
    const AN_REASON_NOACH       =   18;
    const AN_REASON_ACHONLY     =   56;
    const AN_REASON_NOACHTYPE   =  245;
    const AN_REASON_NOACHTYPE2  =  246;

    /**
     * Gets settlement time
     *
     * @param int $time Time processed, usually now.
     * @return int Settlement time
     */
    public static function getsettletime($time)
    {
        $mconfig = get_config('enrol_authorize');

        $cutoff_hour = intval($mconfig->an_cutoff_min);
        $cutoff_min = intval($mconfig->an_cutoff_hour);
        $cutofftime = strtotime("{$cutoff_hour}:{$cutoff_min}", $time);
        if ($cutofftime < $time) {
            $cutofftime = strtotime("{$cutoff_hour}:{$cutoff_min}", $time + (24 * 3600));
        }
        return $cutofftime;
    }

    /**
     * Is order settled? Status must be auth_captured or credited.
     *
     * @param object $order Order details
     * @return bool true, if settled, false otherwise.
     */
    public static function settled($order)
    {
        return ((AN_STATUS_AUTHCAPTURE == $order->status || AN_STATUS_CREDIT == $order->status) and ($order->settletime > 0) and ($order->settletime < time()));
    }

    /**
     * Is order expired? 'Authorized/Pending Capture' transactions are expired after 30 days.
     *
     * @param object &$order Order details.
     * @return bool true, transaction is expired, false otherwise.
     */
    public static function expired(&$order)
    {
        global $DB;
        static $timediff30 = 0;

        if ($order->status == AN_STATUS_EXPIRE) {
            return true;
        }
        elseif ($order->status != AN_STATUS_AUTH) {
            return false;
        }

        if (0 == $timediff30) {
            $timediff30 = self::getsettletime(time()) - (30 * 24 * 3600);
        }

        $expired = self::getsettletime($order->timecreated) < $timediff30;
        if ($expired)
        {
            $order->status = AN_STATUS_EXPIRE;
            $DB->update_record('enrol_authorize', $order);
        }
        return $expired;
    }

    /**
     * Performs an action on authorize.net and updates/inserts records. If record update fails,
     * sends email to admin.
     *
     * @param object &$order Which transaction data will be sent. See enrol_authorize table.
     * @param string &$message Information about error message.
     * @param object &$extra Extra data that used for refunding and credit card information.
     * @param int $action Which action will be performed. See AN_ACTION_*
     * @param string $cctype Used internally to configure credit types automatically.
     * @return int AN_APPROVED Transaction was successful, AN_RETURNZERO otherwise. Use $message for reason.
     */
    public static function process(&$order, &$message, &$extra, $action=AN_ACTION_NONE, $cctype=NULL)
    {
        global $CFG, $DB;
        static $constpd = array();
        require_once($CFG->libdir.'/filelib.php');

        $mconfig = get_config('enrol_authorize');

        if (empty($constpd)) {
            $mconfig = get_config('enrol_authorize');
            $constpd = array(
                'x_version'         => '3.1',
                'x_delim_data'      => 'True',
                'x_delim_char'      => self::AN_DELIM,
                'x_encap_char'      => self::AN_ENCAP,
                'x_relay_response'  => 'FALSE',
                'x_login'           => $mconfig->an_login
            );

            if (!empty($mconfig->an_tran_key)) {
                $constpd['x_tran_key'] = $mconfig->an_tran_key;
            }
            else {
                $constpd['x_password'] = $mconfig->an_password;
            }
        }

        if (empty($order) or empty($order->id)) {
            $message = "Check order->id!";
            return AN_RETURNZERO;
        }

        $method = $order->paymentmethod;
        if (empty($method)) {
            $method = AN_METHOD_CC;
        }
        elseif ($method != AN_METHOD_CC && $method != AN_METHOD_ECHECK) {
            $message = "Invalid method: $method";
            return AN_RETURNZERO;
        }

        $action = intval($action);
        if ($method == AN_METHOD_ECHECK) {
            if ($action != AN_ACTION_AUTH_CAPTURE && $action != AN_ACTION_CREDIT) {
                $message = "Please perform AUTH_CAPTURE or CREDIT for echecks";
                return AN_RETURNZERO;
            }
        }

        $pd = $constpd;
        $pd['x_method'] = $method;
        $test = !empty($mconfig->an_test);
        $pd['x_test_request'] = ($test ? 'TRUE' : 'FALSE');

        switch ($action) {
            case AN_ACTION_AUTH_ONLY:
            case AN_ACTION_CAPTURE_ONLY:
            case AN_ACTION_AUTH_CAPTURE:
                {
                    if ($order->status != AN_STATUS_NONE) {
                        $message = "Order status must be AN_STATUS_NONE(0)!";
                        return AN_RETURNZERO;
                    }
                    elseif (empty($extra)) {
                        $message = "Need extra fields!";
                        return AN_RETURNZERO;
                    }
                    elseif (($action == AN_ACTION_CAPTURE_ONLY) and empty($extra->x_auth_code)) {
                        $message = "x_auth_code is required for capture only transactions!";
                        return AN_RETURNZERO;
                    }

                    $ext = (array)$extra;
                    $pd['x_type'] = (($action==AN_ACTION_AUTH_ONLY)
                                      ? 'AUTH_ONLY' :( ($action==AN_ACTION_CAPTURE_ONLY)
                                                        ? 'CAPTURE_ONLY' : 'AUTH_CAPTURE'));
                    foreach($ext as $k => $v) {
                        $pd[$k] = $v;
                    }
                }
                break;

            case AN_ACTION_PRIOR_AUTH_CAPTURE:
                {
                    if ($order->status != AN_STATUS_AUTH) {
                        $message = "Order status must be authorized!";
                        return AN_RETURNZERO;
                    }
                    if (self::expired($order)) {
                        $message = "Transaction must be captured within 30 days. EXPIRED!";
                        return AN_RETURNZERO;
                    }
                    $pd['x_type'] = 'PRIOR_AUTH_CAPTURE';
                    $pd['x_trans_id'] = $order->transid;
                }
                break;

            case AN_ACTION_CREDIT:
                {
                    if ($order->status != AN_STATUS_AUTHCAPTURE) {
                        $message = "Order status must be authorized/captured!";
                        return AN_RETURNZERO;
                    }
                    if (!self::settled($order)) {
                        $message = "Order must be settled. Try VOID, check Cut-Off time if it fails!";
                        return AN_RETURNZERO;
                    }
                    if (empty($extra->amount)) {
                        $message = "No valid amount!";
                        return AN_RETURNZERO;
                    }
                    $timenowsettle = self::getsettletime(time());
                    $timediff = $timenowsettle - (120 * 3600 * 24);
                    if ($order->settletime < $timediff) {
                        $message = "Order must be credited within 120 days!";
                        return AN_RETURNZERO;
                    }

                    $pd['x_type'] = 'CREDIT';
                    $pd['x_trans_id'] = $order->transid;
                    $pd['x_currency_code'] = $order->currency;
                    $pd['x_invoice_num'] = $extra->orderid;
                    $pd['x_amount'] = $extra->amount;
                    if ($method == AN_METHOD_CC) {
                        $pd['x_card_num'] = sprintf("%04d", intval($order->refundinfo));
                    }
                    elseif ($method == AN_METHOD_ECHECK && empty($order->refundinfo)) {
                        $message = "Business checkings can be refunded only.";
                        return AN_RETURNZERO;
                    }
                }
                break;

            case AN_ACTION_VOID:
                {
                    if (self::expired($order) || self::settled($order)) {
                        $message = "The transaction cannot be voided due to the fact that it is expired or settled.";
                        return AN_RETURNZERO;
                    }
                    $pd['x_type'] = 'VOID';
                    $pd['x_trans_id'] = $order->transid;
                }
                break;

            default:
            {
                $message = "Invalid action: $action";
                return AN_RETURNZERO;
            }
        }

        $headers = array('Connection' => 'close');
        if (! (empty($mconfig->an_referer) || $mconfig->an_referer == "http://")) {
            $headers['Referer'] = $mconfig->an_referer;
        }

        @ignore_user_abort(true);
        if (intval(ini_get('max_execution_time')) > 0) {
            @set_time_limit(300);
        }

        $host = $test ? 'test.authorize.net' : 'secure.authorize.net';
        $data = download_file_content("https://$host:443/gateway/transact.dll", $headers, $pd, false, 300, 60, true);
        if (!$data) {
            $message = "No connection to https://$host:443";
            return AN_RETURNZERO;
        }
        $response = explode(self::AN_ENCAP.self::AN_DELIM.self::AN_ENCAP, $data);
        if ($response === false) {
            $message = "response error";
            return AN_RETURNZERO;
        }
        $rcount = count($response) - 1;
        if ($response[0]{0} == self::AN_ENCAP) {
            $response[0] = substr($response[0], 1);
        }
        if (substr($response[$rcount], -1) == self::AN_ENCAP) {
            $response[$rcount] = substr($response[$rcount], 0, -1);
        }

        $responsecode = intval($response[0]);
        if ($responsecode == AN_APPROVED || $responsecode == AN_REVIEW)
        {
            $transid = floatval($response[6]);
            if ($test || $transid == 0) {
                return $responsecode; // don't update original transaction in test mode.
            }
            switch ($action) {
                case AN_ACTION_AUTH_ONLY:
                case AN_ACTION_CAPTURE_ONLY:
                case AN_ACTION_AUTH_CAPTURE:
                case AN_ACTION_PRIOR_AUTH_CAPTURE:
                    {
                        $order->transid = $transid;

                        if ($method == AN_METHOD_CC) {
                            if ($action == AN_ACTION_AUTH_ONLY || $responsecode == AN_REVIEW) {
                                $order->status = AN_STATUS_AUTH;
                            } else {
                                $order->status = AN_STATUS_AUTHCAPTURE;
                                $order->settletime = self::getsettletime(time());
                            }
                        }
                        elseif ($method == AN_METHOD_ECHECK) {
                            $order->status = AN_STATUS_UNDERREVIEW;
                        }

                        $DB->update_record('enrol_authorize', $order);
                    }
                    break;

                case AN_ACTION_CREDIT:
                    {
                        // Credit generates new transaction id.
                        // So, $extra must be updated, not $order.
                        $extra->status = AN_STATUS_CREDIT;
                        $extra->transid = $transid;
                        $extra->settletime = self::getsettletime(time());
                        $extra->id = $DB->insert_record('enrol_authorize_refunds', $extra);
                    }
                    break;

                case AN_ACTION_VOID:
                    {
                        $tableupdate = 'enrol_authorize';
                        if ($order->status == AN_STATUS_CREDIT) {
                            $tableupdate = 'enrol_authorize_refunds';
                            unset($order->paymentmethod);
                        }
                        $order->status = AN_STATUS_VOID;
                        $DB->update_record($tableupdate, $order);
                    }
                    break;
            }
        }
        else
        {
            $reasonno = $response[2];
            $reasonstr = "reason" . $reasonno;
            $message = get_string($reasonstr, "enrol_authorize");
            if ($message == '[[' . $reasonstr . ']]') {
                $message = isset($response[3]) ? $response[3] : 'unknown error';
            }
            if ($method == AN_METHOD_CC && !empty($mconfig->an_avs) && $response[5] != "P") {
                $avs = "avs" . strtolower($response[5]);
                $stravs = get_string($avs, "enrol_authorize");
                $message .= "<br />" . get_string("avsresult", "enrol_authorize", $stravs);
            }
            if (!$test) { // Autoconfigure :)
                switch($reasonno) {
                    // Credit card type isn't accepted
                    case self::AN_REASON_NOCCTYPE:
                    case self::AN_REASON_NOCCTYPE2:
                        {
                            if (!empty($cctype)) {
                                $ccaccepts = get_list_of_creditcards();

                                unset($ccaccepts[$cctype]);
                                set_config("an_acceptcc_{$cctype}", 0, 'enrol_authorize');

                                foreach ($ccaccepts as $key=>$val) {
                                    set_config("an_acceptcc_{$key}", 1, 'enrol_authorize');
                                }
                                message_to_admin("$message ($cctype) This is new config(an_acceptccs):", $ccaccepts);
                            }
                            break;
                        }
                    // Echecks only
                    case self::AN_REASON_ACHONLY:
                        {
                            set_config("an_acceptmethod_".AN_METHOD_ECHECK, 1, 'enrol_authorize');
                            message_to_admin("$message This is new config(an_acceptmethods):", array(AN_METHOD_ECHECK));
                            break;
                        }
                    // Echecks aren't accepted
                    case self::AN_REASON_NOACH:
                        {
                            set_config("an_acceptmethod_".AN_METHOD_CC, 1, 'enrol_authorize');
                            message_to_admin("$message This is new config(an_acceptmethods):", array(AN_METHOD_CC));
                            break;
                        }
                    // This echeck type isn't accepted
                    case self::AN_REASON_NOACHTYPE:
                    case self::AN_REASON_NOACHTYPE2:
                        {
                            if (!empty($extra->x_echeck_type)) {
                                switch ($extra->x_echeck_type) {
                                    // CCD=BUSINESSCHECKING
                                    case 'CCD':
                                        {
                                            set_config('an_acceptecheck_CHECKING', 1, 'enrol_authorize');
                                            set_config('an_acceptecheck_SAVINGS', 1, 'enrol_authorize');
                                            message_to_admin("$message This is new config(an_acceptechecktypes):", array('CHECKING','SAVINGS'));
                                        }
                                        break;
                                    // WEB=CHECKING or SAVINGS
                                    case 'WEB':
                                        {
                                            set_config('an_acceptecheck_BUSINESSCHECKING', 1, 'enrol_authorize');
                                            message_to_admin("$message This is new config(an_acceptechecktypes):", array('BUSINESSCHECKING'));
                                        }
                                        break;
                                }
                            }
                            break;
                        }
                }
            }
        }
        return $responsecode;
    }
}


