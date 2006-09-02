<?php //  $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR',    '3');
define('AN_DELIM',    '|');
define('AN_ENCAP',    '"');

define('AN_REASON_NOCCTYPE',    17);
define('AN_REASON_NOCCTYPE2',   28);
define('AN_REASON_NOACH',       18);
define('AN_REASON_ACHONLY',     56);
define('AN_REASON_NOACHTYPE',  245);
define('AN_REASON_NOACHTYPE2', 246);

require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');
require_once($CFG->dirroot.'/enrol/authorize/enrol.php');

/**
 * Gets settlement date and time
 *
 * @param int $time Time processed, usually now.
 * @return int Settlement date and time
 */
function authorize_getsettletime($time)
{
    global $CFG;

    $cutoff = intval($CFG->an_cutoff);
    $mins = $cutoff % 60;
    $hrs = ($cutoff - $mins) / 60;
    $cutofftime = strtotime("$hrs:$mins", $time);
    if ($cutofftime < $time) {
        $cutofftime = strtotime("$hrs:$mins", $time + (24 * 3600));
    }
    return $cutofftime;
}

/**
 * Is order settled? Status must be auth_captured or credited.
 *
 * @param object $order Order details
 * @return bool true, if settled, false otherwise.
 */
function authorize_settled($order)
{
    return (($order->status == AN_STATUS_AUTHCAPTURE || $order->status == AN_STATUS_CREDIT) &&
            ($order->settletime > 0) && ($order->settletime < time()));
}

/**
 * Is order expired? 'Authorized/Pending Capture' transactions are expired after 30 days.
 *
 * @param object &$order Order details.
 * @return bool true, transaction is expired, false otherwise.
 */
function authorize_expired(&$order)
{
    static $timediff30;

    if ($order->status == AN_STATUS_EXPIRE) {
        return true;
    }
    elseif ($order->status != AN_STATUS_AUTH) {
        return false;
    }

    if (empty($timediff30)) {
        $timediff30 = authorize_getsettletime(time()) - (30 * 24 * 3600);
    }

    $isexpired = (authorize_getsettletime($order->timecreated) < $timediff30);
    if ($isexpired) {
        $order->status = AN_STATUS_EXPIRE;
        update_record('enrol_authorize', $order);
    }
    return $isexpired;
}

/**
 * Performs an action on authorize.net and updates/inserts records. If record update fails,
 * sends email to admin.
 *
 * @param object &$order Which transaction data will be sent. See enrol_authorize table.
 * @param string &$message Information about error message if this function returns false.
 * @param object &$extra Extra data that used for refunding and credit card information.
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @param string $cctype Credit card type, used internally to configure automatically types.
 * @return bool true Transaction was successful, false otherwise. Use $message for reason.
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 */
function authorize_action(&$order, &$message, &$extra, $action=AN_ACTION_NONE, $cctype=NULL)
{
    global $CFG;
    static $conststring;

    if (!isset($conststring)) {
        $constdata = array(
             'x_version'         => '3.1',
             'x_delim_data'      => 'True',
             'x_delim_char'      => AN_DELIM,
             'x_encap_char'      => AN_ENCAP,
             'x_relay_response'  => 'FALSE',
             'x_login'           => $CFG->an_login
        );
        $str = '';
        foreach($constdata as $ky => $vl) {
            $str .= $ky . '=' . urlencode($vl) . '&';
        }
        $str .= (!empty($CFG->an_tran_key)) ?
                'x_tran_key=' . urlencode($CFG->an_tran_key):
                'x_password=' . urlencode($CFG->an_password);

        $conststring = $str;
        $str = '';
    }

    if (empty($order) or empty($order->id)) {
        $message = "Check order->id!";
        return false;
    }

    $method = $order->paymentmethod;
    if (empty($method)) {
        $method = AN_METHOD_CC;
    }
    elseif ($method != AN_METHOD_CC && $method != AN_METHOD_ECHECK) {
        $message = "Invalid method: $method";
        return false;
    }

    $action = intval($action);
    if ($method == AN_METHOD_ECHECK) {
        if ($action != AN_ACTION_AUTH_CAPTURE && $action != AN_ACTION_CREDIT) {
            $message = "Please perform AUTH_CAPTURE or CREDIT for echecks";
            return false;
        }
    }

    if ($action <= AN_ACTION_NONE or $action > AN_ACTION_VOID) {
        $message = "Invalid action!";
        return false;
    }

    $poststring = $conststring;
    $poststring .= '&x_method=' . $method;

    $test = !empty($CFG->an_test);
    $poststring .= '&x_test_request=' . ($test ? 'TRUE' : 'FALSE');

    switch ($action) {
        case AN_ACTION_AUTH_ONLY:
        case AN_ACTION_CAPTURE_ONLY:
        case AN_ACTION_AUTH_CAPTURE:
        {
            if ($order->status != AN_STATUS_NONE) {
                $message = "Order status must be AN_STATUS_NONE(0)!";
                return false;
            }
            elseif (empty($extra)) {
                $message = "Need extra fields!";
                return false;
            }
            elseif (($action == AN_ACTION_CAPTURE_ONLY) and empty($extra->x_auth_code)) {
                $message = "x_auth_code is required for capture only transactions!";
                return false;
            }

            $ext = (array)$extra;
            $poststring .= '&x_type=' . (($action==AN_ACTION_AUTH_ONLY)
                                          ? 'AUTH_ONLY' :( ($action==AN_ACTION_CAPTURE_ONLY)
                                                            ? 'CAPTURE_ONLY' : 'AUTH_CAPTURE'));
            foreach($ext as $k => $v) {
                $poststring .= '&' . $k . '=' . urlencode($v);
            }
            break;
        }

        case AN_ACTION_PRIOR_AUTH_CAPTURE:
        {
            if ($order->status != AN_STATUS_AUTH) {
                $message = "Order status must be authorized!";
                return false;
            }
            if (authorize_expired($order)) {
                $message = "Transaction must be captured within 30 days. EXPIRED!";
                return false;
            }
            $poststring .= '&x_type=PRIOR_AUTH_CAPTURE&x_trans_id=' . urlencode($order->transid);
            break;
        }

        case AN_ACTION_CREDIT:
        {
            if ($order->status != AN_STATUS_AUTHCAPTURE) {
                $message = "Order status must be authorized/captured!";
                return false;
            }
            if (!authorize_settled($order)) {
                $message = "Order must be settled. Try VOID, check Cut-Off time if it fails!";
                return false;
            }
            $timenowsettle = authorize_getsettletime(time());
            $timediff = $timenowsettle - (120 * 3600 * 24);
            if ($order->settletime < $timediff) {
                $message = "Order must be credited within 120 days!";
                return false;
            }
            if (empty($extra)) {
                $message = "Need extra fields to REFUND!";
                return false;
            }
            $total = floatval($extra->sum) + floatval($extra->amount);
            if (($extra->amount == 0) || ($total > $order->amount)) {
                $message = "Can be credited up to original amount.";
                return false;
            }
            $poststring .= '&x_type=CREDIT&x_trans_id=' . urlencode($order->transid);
            $poststring .= '&x_currency_code=' . urlencode($order->currency);
            $poststring .= '&x_amount=' . urlencode($extra->amount);
            if ($method == AN_METHOD_CC) {
                $poststring .= '&x_card_num=' . sprintf("%04d", intval($order->cclastfour));
            }
            break;
        }

        case AN_ACTION_VOID:
        {
            if ($order->status == AN_STATUS_AUTH) {
                if (authorize_expired($order)) {
                    $message = "Authorized transaction must be voided within 30 days. EXPIRED!";
                    return false;
                }
            }
            elseif ($order->status == AN_STATUS_AUTHCAPTURE or $order->status == AN_STATUS_CREDIT) {
                if (authorize_settled($order)) {
                    $message = "Settled transaction cannot be voided. Check Cut-Off time!";
                    return false;
                }
            }
            else {
                $message = "Order status must be authorized/pending capture or captured-refunded/pending settlement!";
                return false;
            }
            $poststring .= '&x_type=VOID&x_trans_id=' . urlencode($order->transid);
            break;
        }

        default: {
            $message = "Invalid action: $action";
            return false;
        }
    }

    $referer = '';
    if (! (empty($CFG->an_referer) || $CFG->an_referer == "http://")) {
        $referer = "Referer: $CFG->an_referer\r\n";
    }

    $host = $test ? 'certification.authorize.net' : 'secure.authorize.net';
    $fp = fsockopen("ssl://$host", 443, $errno, $errstr, 60);
    if (!$fp) {
        $message =  "no connection: $errstr ($errno)";
        return false;
    }

    // critical section
    @ignore_user_abort(true);
    if (intval(ini_get('max_execution_time')) > 0) {
        @set_time_limit(300);
    }

    fwrite($fp, "POST /gateway/transact.dll HTTP/1.0\r\n" .
                "Host: $host\r\n" . $referer .
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Connection: close\r\n" .
                "Content-length: " . strlen($poststring) . "\r\n\r\n" .
                $poststring . "\r\n"
    );

    $tmpstr = '';
    while(!feof($fp) && !stristr($tmpstr, 'content-length')) {
        $tmpstr = fgets($fp, 4096);
    }
    if (!stristr($tmpstr, 'content-length')) {
        $message =  "content-length error";
        @fclose($fp);
        return false;
    }
    $length = trim(substr($tmpstr, strpos($tmpstr,'content-length')+15));
    fgets($fp, 4096);
    $data = fgets($fp, $length);
    @fclose($fp);
    $response = explode(AN_ENCAP.AN_DELIM.AN_ENCAP, $data);
    if ($response === false) {
        $message = "response error";
        return false;
    }
    $rcount = count($response) - 1;
    if ($response[0]{0} == AN_ENCAP) {
        $response[0] = substr($response[0], 1);
    }
    if (substr($response[$rcount], -1) == AN_ENCAP) {
        $response[$rcount] = substr($response[$rcount], 0, -1);
    }

    if ($response[0] == AN_APPROVED)
    {
        $transid = intval($response[6]);
        if ($test || $transid == 0) {
            return true; // don't update original transaction in test mode.
        }
        switch ($action) {
            case AN_ACTION_AUTH_ONLY:
            case AN_ACTION_CAPTURE_ONLY:
            case AN_ACTION_AUTH_CAPTURE:
            case AN_ACTION_PRIOR_AUTH_CAPTURE:
            {
                $order->transid = $transid;
                if ($action == AN_ACTION_AUTH_ONLY) {
                    $order->status = AN_STATUS_AUTH;
                    // don't update order->settletime
                } else {
                    $order->status = AN_STATUS_AUTHCAPTURE;
                    $order->settletime = authorize_getsettletime(time());
                }
                if (! update_record('enrol_authorize', $order)) {
                    email_to_admin("Error while trying to update data " .
                    "in table enrol_authorize. Please edit manually this record: ID=$order->id.", $order);
                }
                break;
            }
            case AN_ACTION_CREDIT:
            {
                // Credit generates new transaction id.
                // So, $extra must be updated, not $order.
                $extra->status = AN_STATUS_CREDIT;
                $extra->transid = $transid;
                $extra->settletime = authorize_getsettletime(time());
                unset($extra->sum); // this is not used in refunds table.
                if (! $extra->id = insert_record('enrol_authorize_refunds', $extra)) {
                    email_to_admin("Error while trying to insert data " .
                    "into table enrol_authorize_refunds. Please add manually this record:", $extra);
                }
                break;
            }
            case AN_ACTION_VOID:
            {
                $tableupdate = 'enrol_authorize';
                if ($order->status == AN_STATUS_CREDIT) {
                    $tableupdate = 'enrol_authorize_refunds';
                    unset($order->paymentmethod);
                }
                $order->status = AN_STATUS_VOID;
                // don't update order->settletime
                if (! update_record($tableupdate, $order)) {
                    email_to_admin("Error while trying to update data " .
                    "in table $tableupdate. Please edit manually this record: ID=$order->id.", $order);
                }
                break;
            }
            default: return false;
        }
        return true;
    }
    else
    {
        $reasonno = $response[2];
        $reasonstr = "reason" . $reasonno;
        $message = get_string($reasonstr, "enrol_authorize");
        if ($message == '[[' . $reasonstr . ']]') {
            $message = isset($response[3]) ? $response[3] : 'unknown error';
        }
        if ($method == AN_METHOD_CC && !empty($CFG->an_avs) && $response[5] != "P") {
            $avs = "avs" . strtolower($response[5]);
            $stravs = get_string($avs, "enrol_authorize");
            $message .= "<br />" . get_string("avsresult", "enrol_authorize", $stravs);
        }
        if (!$test) { // Autoconfigure :)
            switch($reasonno) {
                // Credit card type isn't accepted
                case AN_REASON_NOCCTYPE:
                case AN_REASON_NOCCTYPE2:
                {
                    if (!empty($cctype)) {
                        $ccaccepts = get_list_of_creditcards();
                        unset($ccaccepts[$cctype]);
                        set_config('an_acceptccs', implode(',', array_keys($ccaccepts)));
                        email_to_admin("$message ($cctype)" .
                        "This is new config(an_acceptccs):", $ccaccepts);
                    }
                    break;
                }
                // Electronic checks aren't accepted
                case AN_REASON_NOACH:
                {
                    set_config('an_acceptmethods', AN_METHOD_CC);
                    email_to_admin("$message " .
                    "This is new config(an_acceptmethods):", array(AN_METHOD_CC));
                    break;
                }
                // This echeck type isn't accepted
                case AN_REASON_NOACHTYPE:
                case AN_REASON_NOACHTYPE2:
                {
                    // Not implemented yet.
                    break;
                }
            }
        }
        return false;
    }
}

?>
