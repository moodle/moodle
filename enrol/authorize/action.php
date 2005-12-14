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
 *
 * @todo cut-off time
 */
define('AN_ACTION_VOID', 0x08);


/**
 * Performs an action on authorize.net
 *
 * @param &object $order Which transaction data will be send. See enrol_authorize table.
 * @param &string $message Information about error messages.
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @param object $extra Extra transaction data.
 * @param &int $reason Response reason code.
 * @return bool true, transaction was successful, false otherwise.
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 * @todo cut-off time
 */
function authorizenet_action(&$order, &$message, $action=AN_ACTION_NONE, $extra = NULL, &$reason = 0)
{
    global $CFG;
    static $conststring;

    $an_test = !empty($CFG->an_test);

    if (empty($conststring)) {
        $consdata = array (
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
                "x_tran_key" . "=" . urlencode($CFG->an_tran_key):
                "x_password" . "=" . urlencode($CFG->an_password);

        $conststring = $str;
    }

    $action = intval($action);

    // sanity check
    if (empty($order) || empty($order->id)) {
        $message = "check order->id!";
        return false;
    }
    elseif ($action <= AN_ACTION_NONE || $action > AN_ACTION_CREDIT) {
        $message = "no action taken!";
        return false;
    }

    $poststring = $conststring;
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
            $poststring .= "&" . "x_type=" . ($action==AN_ACTION_AUTH_ONLY ?
                                              "AUTH_ONLY" : "AUTH_CAPTURE");
            foreach($ext as $k => $v) {
                $poststring .= "&" . $k . "=" . urlencode($v);
            }
            break;
        }

        case AN_ACTION_PRIOR_AUTH_CAPTURE:
        {
            if ($order->status != AN_STATUS_AUTH) {
                $message = "order->status must be AN_STATUS_AUTH!";
                return false;
            }
            // 30 days. +1 = cut-off time
            $timediff = time() - (31 * 3600 * 24);
            if ($order->timecreated < $timediff) {
                $message = "Transaction must be captured within 30 days. EXPIRED!";
                return false;
            }
            $poststring .= "&" . "x_type=PRIOR_AUTH_CAPTURE";
            $poststring .= "&" . "x_trans_id=" . urlencode($order->transid);
            break;
        }

        case AN_ACTION_CREDIT:
        {
            if ($order->status != (AN_STATUS_AUTH | AN_STATUS_CAPTURE)) {
                $message = "order->status must be AN_STATUS_AUTH & AN_STATUS_CAPTURE!";
                return false;
            }
            // 120 days
            $timediff = time() - (120 * 3600 * 24);
            if ($order->timecreated < $timediff) {
                $message = "Order can be credited within 120 days!";
                return false;
            }
            // up to original amount
            $total = doubleval($extra->sum) + doubleval($extra->amount);
            if (($extra->amount == 0) || ($total > $order->amount)) {
                $message = "Can be credited up to original amount.";
                return false;
            }
            $poststring .= "&" . "x_type=CREDIT";
            $poststring .= "&" . "x_trans_id=" . urlencode($order->transid);
            $poststring .= "&" . "x_card_num=" . sprintf("%04d", intval($order->cclastfour));
            $poststring .= "&" . "x_currency_code=" . urlencode($extra->currency);
            $poststring .= "&" . "x_amount=" . urlencode($extra->amount);
            break;
        }

        case AN_ACTION_VOID:
        {
            // only: authonly, authcapture, credit
            if ($order->status != AN_STATUS_AUTH &&
                $order->status != (AN_STATUS_AUTH | AN_STATUS_CAPTURE) &&
                $order->status != AN_STATUS_CREDIT) {
                $message = "order->status must be AUTH, AUTH_CAPTURE or CREDIT!";
                return false;
            }

            if ($order->status == AN_STATUS_AUTH) {
                // 30 days for authonly, make it expired (***********timeupdated)
                $timediff = time() - (30 * 3600 * 24);
                if ($order->timecreated < $timediff) {
                    $message = "Auth_only transaction can be voided within 30 days!";
                    $order->status = AN_STATUS_EXPIRED;
                    return false;
                }
            } elseif ($order->status == (AN_STATUS_AUTH | AN_STATUS_CAPTURE)) {
                // 1 day. Cancel pending settlement.
                $timediff = time() - (2 * 3600 * 24); // TO DO: Cut-off time
                if ($order->timecreated < $timediff) {
                    $message = "Settled transaction cannot be voided. Try REFUND!";
                    return false;
                }
            } elseif ($order->status == AN_STATUS_CREDIT) {
                // 120 days for credit
                $timediff = time() - (120 * 3600 * 24);
                if ($order->timecreated < $timediff) {
                    $message = "Ops! Settled transaction must be credited within 120 days!";
                    return false;
                }
            }
            // OK.
            $poststring .= "&" . "x_type=VOID";
            $poststring .= "&" . "x_trans_id=" . urlencode($order->transid);
        }

        default: { // ???
            $message = "missing action: $action";
            return false;
        }
    }

    // referer
    $anrefererheader = '';
    if (!(empty($CFG->an_referer) || $CFG->an_referer == "http://")) {
          $anrefererheader = "Referer: " . $CFG->an_referer . "\r\n";
    }

    $response = array();
    $connect_host = $an_test ? AN_HOST_TEST : AN_HOST;
    $fp = fsockopen("ssl://" . $connect_host, AN_PORT, $errno, $errstr, 60);
    if (!$fp) {
        $message =  "no connection: $errstr ($errno)";
        return false;
    }

    fputs($fp,
        "POST " . AN_PATH . " HTTP/1.0\r\n" .
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
    $length = trim(substr($tmpstr,strpos($tmpstr,'content-length') + 15));
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

    $reason = intval($response[2]);

    if ($response[0] == AN_APPROVED) {
        $order->transid = strval($response[6]); // TransactionID.
        $order->timeupdated = time();
        switch ($action) {
            case AN_ACTION_AUTH_ONLY:
            case AN_ACTION_AUTH_CAPTURE:
            $order->authcode = strval($response[4]); // Authorization or Approval code
            $order->avscode = strval($response[5]); // Address Verification System code
            if ($action == AN_ACTION_AUTH_ONLY) {
                $order->status = AN_STATUS_AUTH;
            }
            else {
                $order->status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
            }
            break;

            case AN_ACTION_PRIOR_AUTH_CAPTURE:
            $order->status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
            break;

            case AN_ACTION_CREDIT: // generates new TransactionID
            $order->status = AN_STATUS_CREDIT;
            break;

            case AN_ACTION_VOID:
            $order->status = AN_STATUS_VOID;
            break;
        }
        return true;
    }
    else {
        $message = isset($response[3]) ? $response[3] : 'unknown error';
        return false;
    }
}

?>
