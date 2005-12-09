<?php // $Id$

/**
 * No action.
 */
define('AN_ACTION_NONE', 0x0);

/**
 * Authorize only. Don't capture.
 */
define('AN_ACTION_AUTH_ONLY', 0x1);

/**
 * Authorized before, capture now.
 */
define('AN_ACTION_PRIOR_AUTH_CAPTURE', 0x2);
/**
 * Authorize and capture
 */
define('AN_ACTION_AUTH_CAPTURE', 0x3);
/**
 * Cancel transaction in status
 * - authorized
 * - auth_captured/pending settle
 * 
 * @todo cut-off time
 */
define('AN_ACTION_VOID', 0x4);
/**
 * Refund it.
 * must be in 120 days after settled.
 * auth_captured/settled.
 */
define('AN_ACTION_CREDIT', 0x8);



/**
 * authorizenet_action
 *
 * @param &object $order Which transaction data will be send. See enrol_authorize table.
 * @param &string $message Info about errors
 * @param int $action Which action will be performed. See AN_ACTION_*
 * @param object $extra Extra info
 * @return bool true, transaction was successful, false otherwise
 * @author Ethem Evlice <ethem a.t evlice d.o.t com>
 * @uses $CFG
 * @todo AN_ACTION_VOID and AN_ACTION_CREDIT
 */
function authorizenet_action(&$order, &$message, $action=AN_ACTION_NONE, $extra = null)
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
        foreach($consdata as $ky => $vl) { $str .= $ky . '=' . urlencode($vl) . '&'; }
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
            $poststring .= "&" . "x_type=" . ($action==AN_ACTION_AUTH_ONLY ? "AUTH_ONLY" : "AUTH_CAPTURE");
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
            $poststring .= "&" . "x_type=PRIOR_AUTH_CAPTURE";
            $poststring .= "&" . "x_trans_id=" . urlencode($order->transid);
            break;           
        }
        
        case AN_ACTION_VOID: // 30 days
        case AN_ACTION_CREDIT: // 120 days
        {
            $message = "not implemented yet!";
            return false;
        }

        default: { // ???
            $message = "missing action: $action";
            return false;
        }
    }
    
    // referer
    $anrefererheader = '';
    if (!(empty($CFG->an_referer) || $CFG->an_referer == "http://" || $CFG->an_referer == "https://")) {
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
    
    if ($response[0] == AN_APPROVED) {
        $order->authcode = strval($response[4]); // Authorization or Approval code
        $order->avscode = strval($response[5]); // Address Verification System code
        $order->transid = strval($response[6]); // TransactionID
        $order->timeupdated = time();
        switch ($action) {
            case AN_ACTION_AUTH_ONLY:
            $order->status |= AN_STATUS_AUTH;
            break;
            
            case AN_ACTION_AUTH_CAPTURE:
            $order->status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
            break;

            case AN_ACTION_PRIOR_AUTH_CAPTURE:
            $order->status |= AN_STATUS_CAPTURE;
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
