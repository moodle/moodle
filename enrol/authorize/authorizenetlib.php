<?php //  $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR',    '3');
define('AN_DELIM',    '|');
define('AN_ENCAP',    '"');

require_once('const.php');

/**
 * Gets settlement date and time
 *
 * @param int $time Processed time, usually now.
 * @return int Settlement date
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

    if (empty($timediff30)) {
        $timediff30 = authorize_getsettletime(time()) - (30 * 24 * 3600);
    }

    if ($order->status == AN_STATUS_EXPIRE) {
        return true;
    }
    elseif ($order->status != AN_STATUS_AUTH) {
        return false;
    }

    $exp = (authorize_getsettletime($order->timecreated) < $timediff30);

    if ($exp) {
        $order->status = AN_STATUS_EXPIRE;
        update_record('enrol_authorize', $order);
    }

    return $exp;
}

/**
 * Performs an action on authorize.net
 *
 * @param object &$order Which transaction data will be sent. See enrol_authorize table.
 * @param string &$message Information about error messages.
 * @param object &$extra Extra transaction data.
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @return bool true, transaction was successful, false otherwise.
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 */
function authorize_action(&$order, &$message, &$extra, $action=AN_ACTION_NONE)
{
    global $CFG;
    static $conststring;

    $test = !empty($CFG->an_test);

    if (!isset($conststring)) {
        $consdata = array(
             'x_version'         => '3.1',
             'x_delim_data'      => 'True',
             'x_delim_char'      => AN_DELIM,
             'x_encap_char'      => AN_ENCAP,
             'x_relay_response'  => 'FALSE',
             'x_method'          => 'CC',
             'x_login'           => $CFG->an_login,
             'x_test_request'    => $test ? 'TRUE' : 'FALSE'
        );
        $str = '';
        foreach($consdata as $ky => $vl) {
            $str .= $ky . '=' . urlencode($vl) . '&';
        }
        $str .= (!empty($CFG->an_tran_key)) ?
                'x_tran_key=' . urlencode($CFG->an_tran_key):
                'x_password=' . urlencode($CFG->an_password);

        $conststring = $str;
    }

    $action = intval($action);

    if (empty($order) || empty($order->id)) {
        $message = "Check order->id!";
        return false;
    }
    elseif ($action <= AN_ACTION_NONE || $action > AN_ACTION_VOID) {
        $message = "No action taken!";
        return false;
    }

    $poststring = $conststring;

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
                $message = "need extra fields for CREDIT!";
                return false;
            }
            $total = floatval($extra->sum) + floatval($extra->amount);
            unset($extra->sum); // this is not used in refunds table.
            if (($extra->amount == 0) || ($total > $order->amount)) {
                $message = "Can be credited up to original amount.";
                return false;
            }
            $poststring .= '&x_type=CREDIT&x_trans_id=' . urlencode($order->transid);
            $poststring .= '&x_card_num=' . sprintf("%04d", intval($order->cclastfour));
            $poststring .= '&x_currency_code=' . urlencode($order->currency);
            $poststring .= '&x_amount=' . urlencode($extra->amount);
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
                $message = "Order status must be authorized, auth/captured or refunded!";
                return false;
            }
            $poststring .= '&x_type=VOID&x_trans_id=' . urlencode($order->transid);
            break;
        }

        default: {
            $message = "Missing action? $action";
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
                    // dont't update settletime
                } else {
                    $order->status = AN_STATUS_AUTHCAPTURE;
                    $order->settletime = authorize_getsettletime(time());
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
                break;
            }
            case AN_ACTION_VOID:
            {
                $order->status = AN_STATUS_VOID;
                // dont't update settletime
                break;
            }
            default: return false;
        }
        return true;
    }
    else
    {
        $reason = "reason" . $response[2];
        $message = get_string($reason, "enrol_authorize");
        if ($message == '[[' . $reason . ']]') {
            $message = isset($response[3]) ? $response[3] : 'unknown error';
        }
        if (!empty($CFG->an_avs)) {
            $avs = "avs" . strtolower($response[5]);
            $stravs = get_string($avs, "enrol_authorize");
            $message .= "<br />" . get_string("avsresult", "enrol_authorize", $stravs);
        }
        return false;
    }
}

?>
