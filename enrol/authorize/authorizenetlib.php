<?php //  $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

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
 * @param string &$message Information about error message.
 * @param object &$extra Extra data that used for refunding and credit card information.
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @param string $cctype Used internally to configure credit types automatically.
 * @return int AN_APPROVED Transaction was successful, AN_RETURNZERO otherwise. Use $message for reason.
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 */
function authorize_action(&$order, &$message, &$extra, $action=AN_ACTION_NONE, $cctype=NULL)
{
    global $CFG;
    static $conststring;

    if (!isset($conststring)) {
        $mconfig = get_config('enrol/authorize');
        $constdata = array(
             'x_version'         => '3.1',
             'x_delim_data'      => 'True',
             'x_delim_char'      => AN_DELIM,
             'x_encap_char'      => AN_ENCAP,
             'x_relay_response'  => 'FALSE',
             'x_login'           => rc4decrypt($mconfig->an_login)
        );
        $str = '';
        foreach($constdata as $ky => $vl) {
            $str .= $ky . '=' . urlencode($vl) . '&';
        }
        $str .= (!empty($mconfig->an_tran_key)) ?
                'x_tran_key=' . urlencode(rc4decrypt($mconfig->an_tran_key)):
                'x_password=' . urlencode(rc4decrypt($mconfig->an_password));

        $conststring = $str;
        $str = '';
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
                return AN_RETURNZERO;
            }
            if (authorize_expired($order)) {
                $message = "Transaction must be captured within 30 days. EXPIRED!";
                return AN_RETURNZERO;
            }
            $poststring .= '&x_type=PRIOR_AUTH_CAPTURE&x_trans_id=' . urlencode($order->transid);
            break;
        }

        case AN_ACTION_CREDIT:
        {
            if ($order->status != AN_STATUS_AUTHCAPTURE) {
                $message = "Order status must be authorized/captured!";
                return AN_RETURNZERO;
            }
            if (!authorize_settled($order)) {
                $message = "Order must be settled. Try VOID, check Cut-Off time if it fails!";
                return AN_RETURNZERO;
            }
            if (empty($extra->amount)) {
                $message = "No valid amount!";
                return AN_RETURNZERO;
            }
            $timenowsettle = authorize_getsettletime(time());
            $timediff = $timenowsettle - (120 * 3600 * 24);
            if ($order->settletime < $timediff) {
                $message = "Order must be credited within 120 days!";
                return AN_RETURNZERO;
            }

            $poststring .= '&x_type=CREDIT&x_trans_id=' . urlencode($order->transid);
            $poststring .= '&x_currency_code=' . urlencode($order->currency);
            $poststring .= '&x_invoice_num=' . urlencode($extra->orderid);
            $poststring .= '&x_amount=' . urlencode($extra->amount);
            if ($method == AN_METHOD_CC) {
                $poststring .= '&x_card_num=' . sprintf("%04d", intval($order->refundinfo));
            }
            elseif ($method == AN_METHOD_ECHECK && empty($order->refundinfo)) {
                $message = "Business checkings can be refunded only.";
                return AN_RETURNZERO;
            }
            break;
        }

        case AN_ACTION_VOID:
        {
            if (authorize_expired($order) || authorize_settled($order)) {
                $message = "The transaction cannot be voided due to the fact that it is expired or settled.";
                return AN_RETURNZERO;
            }
            $poststring .= '&x_type=VOID&x_trans_id=' . urlencode($order->transid);
            break;
        }

        default: {
            $message = "Invalid action: $action";
            return AN_RETURNZERO;
        }
    }

    $referer = '';
    if (! (empty($CFG->an_referer) || $CFG->an_referer == "http://")) {
        $referer = "Referer: $CFG->an_referer\r\n";
    }

    $errno = 0; $errstr = '';
    $host = $test ? 'certification.authorize.net' : 'secure.authorize.net';
    $fp = fsockopen("ssl://$host", 443, $errno, $errstr, 60);
    if (!$fp) {
        $message =  "no connection: $errstr ($errno)";
        return AN_RETURNZERO;
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
        return AN_RETURNZERO;
    }
    $length = trim(substr($tmpstr, strpos($tmpstr,'content-length')+15));
    fgets($fp, 4096);
    $data = fgets($fp, $length);
    @fclose($fp);
    $response = explode(AN_ENCAP.AN_DELIM.AN_ENCAP, $data);
    if ($response === false) {
        $message = "response error";
        return AN_RETURNZERO;
    }
    $rcount = count($response) - 1;
    if ($response[0]{0} == AN_ENCAP) {
        $response[0] = substr($response[0], 1);
    }
    if (substr($response[$rcount], -1) == AN_ENCAP) {
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
                        $order->settletime = authorize_getsettletime(time());
                    }
                }
                elseif ($method == AN_METHOD_ECHECK) {
                    $order->status = AN_STATUS_UNDERREVIEW;
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
                if (! $extra->id = insert_record('enrol_authorize_refunds', $extra)) {
                    unset($extra->id);
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
                if (! update_record($tableupdate, $order)) {
                    email_to_admin("Error while trying to update data " .
                    "in table $tableupdate. Please edit manually this record: ID=$order->id.", $order);
                }
                break;
            }
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
                // Echecks only
                case AN_REASON_ACHONLY:
                {
                    set_config('an_acceptmethods', AN_METHOD_ECHECK);
                    email_to_admin("$message " .
                    "This is new config(an_acceptmethods):", array(AN_METHOD_ECHECK));
                    break;
                }
                // Echecks aren't accepted
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
                    if (!empty($extra->x_echeck_type)) {
                        switch ($extra->x_echeck_type) {
                            // CCD=BUSINESSCHECKING
                            case 'CCD':
                            {
                                set_config('an_acceptechecktypes', 'CHECKING,SAVINGS');
                                email_to_admin("$message " .
                                "This is new config(an_acceptechecktypes):", array('CHECKING','SAVINGS'));
                            }
                            break;
                            // WEB=CHECKING or SAVINGS
                            case 'WEB':
                            {
                                set_config('an_acceptechecktypes', 'BUSINESSCHECKING');
                                email_to_admin("$message " .
                                "This is new config(an_acceptechecktypes):", array('BUSINESSCHECKING'));
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

?>
