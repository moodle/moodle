<?php // $Id$

/**
 * No action.
 */
define('AN_ACTION_NONE', 0x00);

/**
 * Used to authorize only, don't capture.
 */
define('AN_ACTION_AUTH_ONLY', 0x01);

/**
 * Used to capture, it was authorized before.
 */
define('AN_ACTION_PRIOR_AUTH_CAPTURE', 0x02);

/**
 * Used to authorize and capture.
 */
define('AN_ACTION_AUTH_CAPTURE', 0x03);

/**
 * Used to return funds to a customer's credit card.
 *
 * - Can be credited within 120 days after the original authorization was obtained.
 * - Amount can be any amount up to the original amount charged.
 * - Captured/pending settlement transactions cannot be credited,
 *   instead a void must be issued to cancel the settlement.
 * NOTE: Assigns a new transactionID to the original transaction.
 *       SAVE IT, so we can cancel new refund if it is a fault return.
 */
define('AN_ACTION_CREDIT', 0x04);

/**
 * Used to cancel an exiting transaction with a status of
 * authorized/pending capture, captured/pending settlement or
 * settled/refunded.
 *
 * - Void requests effectively cancel the Capture request
 *   that would start the funds transfer process.
 * - Also used to cancel existing transaction with a status of
 *   settled/refunded. Credited mistakenly, so cancel it
 *   and return funds to our account.
 */
define('AN_ACTION_VOID', 0x08);


/**
 * Gets settlement date and time
 *
 * @param int $time Processed time, usually now.
 * @return int Settlement date
 */
function getsettletime($time)
{
    global $CFG;

    $hrs = intval($CFG->an_cutoff_hour);
    $mins = intval($CFG->an_cutoff_min);
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
function settled($order)
{
    global $CFG;
    static $timenow;

    if (!isset($timenow)) {
        $timenow = time();
    }

    return (($order->status == AN_STATUS_AUTHCAPTURE || $order->status == AN_STATUS_CREDIT)
            && $order->settletime > 0 && $order->settletime < $timenow );
}

/**
 * Performs an action on authorize.net
 *
 * @param object &$order Which transaction data will be send. See enrol_authorize table.
 * @param string &$message Information about error messages.
 * @param object &$extra Extra transaction data.
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @return bool true, transaction was successful, false otherwise.
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 */
function authorizenet_action(&$order, &$message, &$extra, $action=AN_ACTION_NONE)
{
    global $CFG;
    static $conststring;

    $an_test = !empty($CFG->an_test);

    if (!isset($conststring)) {
        $consdata = array(
             'x_version'         => '3.1',
             'x_delim_data'      => 'True',
             'x_delim_char'      => AN_DELIM,
             'x_encap_char'      => AN_ENCAP,
             'x_relay_response'  => 'False',
             'x_method'          => 'CC',
             'x_login'           => $CFG->an_login,
             'x_test_request'    => $an_test ? 'TRUE' : 'FALSE'
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

    // sanity check
    if (empty($order) || empty($order->id)) {
        $message = "check order->id!";
        return false;
    }
    elseif ($action <= AN_ACTION_NONE || $action > AN_ACTION_VOID) {
        $message = "no action taken!";
        return false;
    }

    $poststring = $conststring;
    $timenowsettle = getsettletime(time());

    switch ($action) {
        case AN_ACTION_AUTH_ONLY:
        case AN_ACTION_AUTH_CAPTURE:
        {
            if ($order->status != AN_STATUS_NONE) {
                $message = "order->status must be AN_STATUS_NONE!";
                return false;
            }
            if (empty($extra)) {
                $message = "need extra fields!";
                return false;
            }
            $ext = (array)$extra;
            $poststring .= '&x_type=' . ($action==AN_ACTION_AUTH_ONLY ?
                                         'AUTH_ONLY' : 'AUTH_CAPTURE');
            foreach($ext as $k => $v) {
                $poststring .= '&' . $k . '=' . urlencode($v);
            }
            break;
        }

        case AN_ACTION_PRIOR_AUTH_CAPTURE:
        {
            if ($order->status != AN_STATUS_AUTH) {
                $message = "order->status must be AN_STATUS_AUTH!";
                return false;
            }
            $timediff = $timenowsettle - (30 * 3600 * 24);
            $timecreatedsettle = getsettletime($order->timecreated);
            if ($timecreatedsettle < $timediff) {
                $order->status = AN_STATUS_EXPIRE;
                $message = "Transaction must be captured within 30 days. EXPIRED!";
                return false;
            }
            $poststring .= '&x_type=PRIOR_AUTH_CAPTURE&x_trans_id=' . urlencode($order->transid);
            break;
        }

        case AN_ACTION_CREDIT:
        {
            if ($order->status != AN_STATUS_AUTHCAPTURE) {
                $message = "order->status must be AN_STATUS_AUTHCAPTURE!";
                return false;
            }
            if (!settled($order)) {
                $message = "Order wasn't settled, try VOID. Check Cut-Off time if it fails!";
                return false;
            }
            // 120 days
            $timediff = $timenowsettle - (120 * 3600 * 24);
            if ($order->settletime < $timediff) {
                $message = "Order can be credited within 120 days!";
                return false;
            }
            // extra fields
            if (empty($extra)) {
                $message = "need extra fields for CREDIT!";
                return false;
            }
            // up to original amount
            $total = doubleval($extra->sum) + doubleval($extra->amount);
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
                // 30 days for authonly, make it expired (**settletime**)
                $timediff = $timenowsettle - (30 * 3600 * 24);
                $timecreatedsettle = getsettletime($order->timecreated);
                if ($timecreatedsettle < $timediff) {
                    $message = "Auth_only transaction must be voided within 30 days. EXPIRED!";
                    $order->status = AN_STATUS_EXPIRE;
                    return false;
                }
            }
            elseif ($order->status == AN_STATUS_AUTHCAPTURE || $order->status == AN_STATUS_CREDIT) {
                if (settled($order)) {
                    $message = "Settled transaction cannot be voided. Check Cut-Off time!";
                    return false;
                }
            }
            else {
                $message = "order->status must be AUTH, AUTH_CAPTURE or CREDIT!";
                return false;
            }
            $poststring .= '&x_type=VOID&x_trans_id=' . urlencode($order->transid);
            break;
        }

        default: { // ???
            $message = "missing action: $action";
            return false;
        }
    }

    // referer
    $anrefererheader = '';
    if (! (empty($CFG->an_referer) || $CFG->an_referer == "http://")) {
        $anrefererheader = "Referer: " . $CFG->an_referer . "\r\n";
    }

    $response = array();
    $connect_host = $an_test ? AN_HOST_TEST : AN_HOST;
    $fp = fsockopen("ssl://" . $connect_host, AN_PORT, $errno, $errstr, 60);
    if (!$fp) {
        $message =  "no connection: $errstr ($errno)";
        return false;
    }

    fwrite($fp, "POST " . AN_PATH . " HTTP/1.0\r\n" .
                "Host: $connect_host\r\n" . $anrefererheader .
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
        if ($an_test || intval($response[6]) == 0) {
            return true; // don't update original transaction in test mode.
        }
        switch ($action) {
            case AN_ACTION_AUTH_ONLY:
            case AN_ACTION_AUTH_CAPTURE:
            case AN_ACTION_PRIOR_AUTH_CAPTURE:
            {
                $order->transid = strval($response[6]); // TransactionID
                if ($action == AN_ACTION_AUTH_ONLY) {
                    $order->status = AN_STATUS_AUTH;
                    // dont't update settletime
                } else {
                    $order->status = AN_STATUS_AUTHCAPTURE;
                    $order->settletime = getsettletime(time());
                }
                break;
            }
            case AN_ACTION_CREDIT:
            {
                // Credit generates new transaction id.
                // So, $extra must be updated, not $order.
                $extra->status = AN_STATUS_CREDIT;
                $extra->transid = strval($response[6]);
                $extra->settletime = getsettletime(time());
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
        $message = isset($response[3]) ? $response[3] : 'unknown error';
        return false;
    }
}

?>
