<?php  // $Id$

// Authorize.net
define('AN_HOST', 'secure.authorize.net');
define('AN_PORT', 443);
define('AN_PATH', '/gateway/transact.dll');
define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR', '3');
define('AN_DELIM', '|');
define('AN_ENCAP', '"');

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

var $ccerrormsg;

/// Override: print_entry()
function print_entry($course) {
    global $CFG, $USER, $form;

    if ($this->zero_cost($course) || isguest()) { // No money for guests ;)
        parent::print_entry($course);
        return;
    }

    // check payment
    $this->check_paid();

    // I want to paid on SSL.
    if (empty($_SERVER['HTTPS'])) {
        if (empty($CFG->loginhttps)) {
            error(get_string("httpsrequired", "enrol_authorize"));
        } else {
            $wwwsroot = str_replace('http://','https://', $CFG->wwwroot);
            $sdestination = "$wwwsroot/course/enrol.php?id=$course->id";
            redirect($sdestination);
            exit;
        }
    }

    $CCTYPES = array(
        'mcd' => 'Master Card', 'vis' => 'Visa',        'amx' => 'American Express',
        'dsc' => 'Discover',    'dnc' => 'Diners Club', 'jcb' => 'JCB',
        'swi' => 'Switch',      'dlt' => 'Delta',       'enr' => 'EnRoute'
    );

    $formvars = array('password','ccfirstname','cclastname','cc','ccexpiremm','ccexpireyyyy','cctype','cvv','cczip');
    foreach ($formvars as $var) {
        if (!isset($form->$var)) {
            $form->$var = '';
        }
    }

    $teacher = get_teacher($course->id);
    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");
    $userfirstname = empty($form->ccfirstname) ? $USER->firstname : $form->ccfirstname;
    $userlastname = empty($form->cclastname) ? $USER->lastname : $form->cclastname;
    $curcost = $this->get_course_cost($course);

    print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
    print_course($course, "80%");

    if ($course->password) {
        print_simple_box(get_string('choosemethod', 'enrol_authorize'), 'center');
        $password = ''; 
        include($CFG->dirroot . '/enrol/internal/enrol.html'); 
    }

    print_simple_box_start("center");
    include($CFG->dirroot . '/enrol/authorize/enrol.html');
    print_simple_box_end();

    print_footer();
}

/// Override: check_entry()
function check_entry($form, $course) {
    if ($this->zero_cost($course) || isguest() || (!empty($form->password))) {
        parent::check_entry($form, $course);
    } else {
        $this->cc_submit($form, $course);
    }
}

function cc_submit($form, $course) {
    global $CFG, $USER, $SESSION;
    require_once($CFG->dirroot . '/enrol/authorize/ccval.php');

    if (empty($form->ccfirstname) || empty($form->cclastname) ||
        empty($form->cc) || empty($form->cvv) || empty($form->cctype) ||
        empty($form->ccexpiremm) || empty($form->ccexpireyyyy) || empty($form->cczip)) {
              $this->ccerrormsg = get_string("allfieldsrequired");
              return;
    }

    $exp_date = ($form->ccexpiremm < 10) ? strval('0'.$form->ccexpiremm) : strval($form->ccexpiremm);
    $exp_date .= $form->ccexpireyyyy;
    $valid_cc = CCVal($form->cc, $form->cctype, $exp_date);
    $curcost = $this->get_course_cost($course);
    $useripno = getremoteaddr(); // HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR, REMOTE_ADDR
    $order_number = 0; // can be get from db

    if (!$valid_cc) {
        $this->ccerrormsg = get_string( (($valid_cc===0) ? 'ccexpired' : 'ccinvalid'), 'enrol_authorize' );
        return;
    }

    $this->check_paid();
    $formdata = array (
        'x_version'        => '3.1',
        'x_delim_data'     => 'True',
        'x_delim_char'     => AN_DELIM,
        'x_encap_char'     => AN_ENCAP,
        'x_relay_response' => 'False',
        'x_login'          => $CFG->an_login,
        'x_test_request'   => (!empty($CFG->an_test)) ? 'True' : 'False',
        'x_type'           => 'AUTH_CAPTURE',
        'x_method'         => 'CC',
        'x_first_name'     => (empty($form->ccfirstname) ? $USER->firstname : $form->ccfirstname),
        'x_last_name'      => (empty($form->cclastname) ? $USER->lastname : $form->cclastname),
        'x_address'        => $USER->address,
        'x_city'           => $USER->city,
        'x_zip'            => $form->cczip,
        'x_country'        => $USER->country,
        'x_state'          => '',
        'x_card_num'       => $form->cc,
        'x_card_code'      => $form->cvv,
        'x_currency_code'  => $curcost['currency'],
        'x_amount'         => $curcost['cost'],
        'x_exp_date'       => $exp_date,
        'x_email'          => $USER->email,
        'x_email_customer' => 'False',
        'x_cust_id'        => $USER->id,
        'x_customer_ip'    => $useripno,
        'x_phone'          => '',
        'x_fax'            => '',
        'x_invoice_num'    => $order_number,
        'x_description'    => $course->shortname
    );

    //build the post string
    $poststring = '';
    if (!empty($CFG->an_tran_key)) {
        $poststring .= urlencode("x_tran_key") . "=" . urlencode($CFG->an_tran_key);
    } else { // MUST be x_tran_key or x_password
        $poststring .= urlencode("x_password") . "=" . urlencode($CFG->an_password);
    }
    foreach($formdata as $key => $val) {
        $poststring .= "&" . urlencode($key) . "=" . urlencode($val);
    }
    //built

    $response = array();
    $anrefererheader = '';
    if((!empty($CFG->an_referer)) && ($CFG->an_referer != "http://") && ($CFG->an_referer != "https://")) {
        $anrefererheader = "Referer: " . $CFG->an_referer . "\r\n";
    }

    $fp = fsockopen("ssl://" . AN_HOST, AN_PORT, $errno, $errstr, 60);
    if(!$fp) {
        $this->ccerrormsg =  "$errstr ($errno)";
        return;
    } else {
        fputs($fp,
            "POST " . AN_PATH . " HTTP/1.0\r\n" .
            "Host: " . AN_HOST . "\r\n" . 
            $anrefererheader .
            "Content-type: application/x-www-form-urlencoded\r\n" .
            "Content-length: " . strlen($poststring) . "\r\n" .
            "Connection: close\r\n\r\n" .
            $poststring . "\r\n\r\n");

        $str = '';
        while(!feof($fp) && !stristr($str, 'content-length')) {
            $str = fgets($fp, 4096);
        }
        // If didnt get content-lenght, something is wrong.
        if (!stristr($str, 'content-length')) {
            $this->ccerrormsg =  "content-length error";
            @fclose($fp);
            return;
        }
        // Get length of data to be received.
        $length = trim(substr($str,strpos($str,'content-length') + 15));
        // Get buffer (blank data before real data)
        fgets($fp, 4096);
        // Get real data
        $data = fgets($fp, $length);
        @fclose($fp);
        $response = explode(AN_ENCAP.AN_DELIM.AN_ENCAP, $data);
        if ($response === false) {
            $this->ccerrormsg = "response error";
            return;
        }
        $rcount = count($response) - 1;
        if ($response[0]{0} == AN_ENCAP) {
            $response[0] = substr($response[0], 1);
        }
        if (substr($response[$rcount], -1) == AN_ENCAP) {
            $response[$rcount] = substr($response[$rcount], 0, -1);
        }
    }

    if ($response[0] != AN_APPROVED) {
        $this->ccerrormsg = isset($response[3]) ? $response[3] : 'unknown error';
    } else {
        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        if ($course->enrolperiod) {
            $timestart = time();
            $timeend   = $timestart + $course->enrolperiod;
        } else {
            $timestart = $timeend = 0;
        }

        if (!enrol_student($USER->id, $course->id, $timestart, $timeend)) {
            $this->email_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $response);
        } else {
            // begin: send email
            $teacher = get_teacher($course->id);
            if (!empty($CFG->enrol_mailstudents)) {
                $a->coursename = "$course->fullname";
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id";
                email_to_user($USER, $teacher, get_string("enrolmentnew", '', $course->shortname),
                get_string('welcometocoursetext', '', $a));
            }
            if (!empty($CFG->enrol_mailteachers)) {
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                email_to_user($teacher, $USER, get_string("enrolmentnew", '', $course->shortname),
                get_string('enrolmentnewuser', '', $a));
            }
            if (!empty($CFG->enrol_mailadmins)) {
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin, $USER, get_string("enrolmentnew", '', $course->shortname),
                       get_string('enrolmentnewuser', '', $a));
                }
            }
            // end: send email
            // begin: authorize_table
            $datax->cclastfour = substr($form->cc, -4);
            $datax->ccexp = $exp_date;
            $datax->cvv = $form->cvv;
            $datax->ccname = $formdata['x_first_name'] . " " . $formdata['x_last_name'];
            $datax->courseid = $course->id;
            $datax->userid = $USER->id;
            $datax->avscode = strval($response[5]);
            $datax->transid = strval($response[6]);
            if (!insert_record("enrol_authorize", $datax)) { // Insert a transaction record
                $this->email_to_admin("Error while trying to insert valid transaction", $datax);
            }
        } // end if (!enrol_student)

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
    } // end if ($response[0] != AN_APPROVED)
}

function zero_cost($course) {
    $curcost = $this->get_course_cost($course);
    return (abs($curcost['cost']) < 0.01);
}

function get_course_cost($course) {
    global $CFG;
    $cost = (float)0;

    if (!empty($course->cost)) {
        $cost = (float)(((float)$course->cost) < 0) ? $CFG->enrol_cost : $course->cost;
    }

    $currency = (!empty($course->currency))
                 ? $course->currency :( empty($CFG->enrol_currency)
                                        ? 'USD' : $CFG->enrol_currency );

    $cost = format_float($cost, 2);
    $ret = array('cost' => $cost, 'currency' => $currency);

    return $ret;
}

/// Override the get_access_icons() function
function get_access_icons($course) {

    $str = parent::get_access_icons($course);
    $curcost = $this->get_course_cost($course);

    if (abs($curcost['cost']) > 0.00) {
        $strrequirespayment = get_string("requirespayment");
        $strcost = get_string("cost");
        $currency = $curcost['currency'];

        switch ($currency) {
            case 'USD': $currency = 'US$'; break;
            case 'CAD': $currency = 'C$'; break;
            case 'EUR': $currency = '&euro;'; break;
            case 'GBP': $currency = '&pound;'; break;
            case 'JPY': $currency = '&yen;'; break;
        }

        $str .= '<div class="cost" title="'.$strrequirespayment.'">'.$strcost.': ';
        $str .= $currency . ' ' . $curcost['cost'].'</div>';
    }

    return $str;
}

function config_form($frm) {
    global $CFG;

    $vars = array('an_login', 'an_tran_key', 'an_password', 'an_referer', 'an_test',
                  'enrol_cost', 'enrol_currency', 'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins');

    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        }
    }

    if (!$this->check_openssl_loaded()) {
        notify('PHP must be compiled with SSL support (--with-openssl)');
    }

    if (data_submitted()) {  // something POSTed
        // Some required fields
        if (empty($frm->an_login)) {
            notify("an_login required");
        }
        if (empty($frm->an_tran_key) && empty($frm->an_password)) {
            notify("an_tran_key or an_password required");
        }
        if (empty($CFG->loginhttps)) {
            notify("\$CFG->loginhttps MUST BE ON");
        }
    }

    include($CFG->dirroot.'/enrol/authorize/config.html');
}

function process_config($config) {
    global $CFG;

    // enrol config
    $val = optional_param('enrol_cost', 0, PARAM_INT);
    set_config('enrol_cost', $val);

    $val = optional_param('enrol_currency', 'USD', PARAM_ALPHA);
    set_config('enrol_currency', $val);

    $val = optional_param('enrol_mailstudents', '');
    set_config('enrol_mailstudents', $val);

    $val = optional_param('enrol_mailteachers', '');
    set_config('enrol_mailteachers', $val);

    $val = optional_param('enrol_mailadmins', '');
    set_config('enrol_mailadmins', $val);

    // authorize config
    $return = $this->check_openssl_loaded();

    $login_val = optional_param('an_login', '');
    set_config('an_login', $login_val);

    $password_val = optional_param('an_password', '');
    set_config('an_password', $password_val);

    $tran_val = optional_param('an_tran_key', '');
    set_config('an_tran_key', $tran_val);

    $test_val = optional_param('an_test', '');
    set_config('an_test', $test_val);

    $referer_val = optional_param('an_referer', 'http://', PARAM_URL);
    set_config('an_referer', $referer_val);

    // some required fields
    if (empty($login_val)) {
        $return = false;       
    }
    if (empty($tran_val) && empty($password_val)) {
        $return = false;       
    }
    if (empty($CFG->loginhttps)) {
        $return = false;
    }

    return $return;
}

function email_to_admin($subject, $data) {
    $admin = get_admin();
    $site = get_site();

    $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";
    foreach ($data as $key => $value) {
        $message .= "$key => $value\n";
    }
    email_to_user($admin, $admin, "CC ERROR: ".$subject, $message);
}

function check_paid() {
    global $CFG, $SESSION;

    if (isset($SESSION->ccpaid)) {
        unset($SESSION->ccpaid);
        redirect($CFG->wwwroot . '/login/logout.php');
        exit;
    }
}

function check_openssl_loaded() {
    return extension_loaded('openssl');
}

} // end of class definition
?>
