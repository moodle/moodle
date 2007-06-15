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

        $zerocost = $this->zero_cost($course);
        if ($zerocost) {
            parent::print_entry($course);
            return;
        }

        // I want to paid on SSL.
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            if (empty($CFG->loginhttps)) {
                error(get_string("httpsrequired", "enrol_authorize"));
            } else {
                $wwwsroot = str_replace('http://','https://', $CFG->wwwroot);
                $sdestination = "$wwwsroot/course/enrol.php?id=$course->id";
                redirect($sdestination);
                exit;
            }
        }

        $strloginto = get_string("loginto", "", $course->shortname);
        $strcourses = get_string("courses");

        print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, "80%");

        if (!empty($course->password)) {
            print_heading(get_string('choosemethod', 'enrol_authorize'), 'center');
        }

        if (isguest()) {
            $cost = $this->get_course_cost($course);
            echo '<div align="center"><p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $CFG->enrol_currency $cost".'</b></p>';
            echo '<p><a href="'.$CFG->wwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        }
        else {
            print_simple_box_start('center');
            include($CFG->dirroot . '/enrol/authorize/enrol.html');
            print_simple_box_end();
        }

        if (!empty($course->password)) {
            $password = '';
            $teacher = get_teacher($course->id);
            include($CFG->dirroot . '/enrol/internal/enrol.html');
        }

        print_footer();
    }

    /// Override: check_entry()
    function check_entry($form, $course) {
        if ($this->zero_cost($course) or ((!empty($course->password)) and (!empty($form->password)))) {
            parent::check_entry($form, $course);
        }
        else {
            $this->cc_submit($form, $course);
        }
    }

    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once($CFG->dirroot . '/enrol/authorize/ccval.php');

        if (empty($form->ccfirstname) || empty($form->cclastname) ||
            empty($form->cc) || empty($form->cvv) || empty($form->cctype) ||
            empty($form->ccexpiremm) || empty($form->ccexpireyyyy)) {
            $this->ccerrormsg = get_string("allfieldsrequired");
            return;
        }

        if (!empty($CFG->an_avs)) {
            if (empty($form->ccaddress) || empty($form->cccity) ||
                empty($form->cccountry) || empty($form->cczip)) {
                $this->ccerrormsg = get_string("allfieldsrequired");
                return;
            }
        }

        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;
        $valid_cc = CCVal($form->cc, $form->cctype, $exp_date);
        if (!$valid_cc) {
            $this->ccerrormsg = get_string((($valid_cc===0) ? 'ccexpired' : 'ccinvalid'), 'enrol_authorize');
            return;
        }

        $this->check_paid();
        $order_number = 0; // can be get from db
        $cost = $this->get_course_cost($course);
        $formdata = array (
            'x_version'         => '3.1',
            'x_delim_data'      => 'True',
            'x_delim_char'      => AN_DELIM,
            'x_encap_char'      => AN_ENCAP,
            'x_relay_response'  => 'False',
            'x_login'           => $CFG->an_login,
            'x_test_request'    => (!empty($CFG->an_test)) ? 'True' : 'False',
            'x_type'            => 'AUTH_CAPTURE',
            'x_method'          => 'CC',
            'x_first_name'      => $form->ccfirstname,
            'x_last_name'       => $form->cclastname,
            'x_address'         => $form->ccaddress,
            'x_city'            => $form->cccity,
            'x_zip'             => $form->cczip,
            'x_country'         => $form->cccountry,
            'x_state'           => $form->ccstate,
            'x_card_num'        => $form->cc,
            'x_card_code'       => $form->cvv,
            'x_currency_code'   => $CFG->enrol_currency,
            'x_amount'          => $cost,
            'x_exp_date'        => $exp_date,
            'x_email'           => $USER->email,
            'x_email_customer'  => 'False',
            'x_cust_id'         => $USER->id,
            'x_customer_ip'     => $_SERVER["REMOTE_ADDR"],
            'x_phone'           => '',
            'x_fax'             => '',
            'x_invoice_num'     => $order_number,
            'x_description'     => $course->shortname
        );

        // build the post string
        $poststring = '';
        foreach($formdata as $k => $v) {
            $poststring .= $k . "=" . urlencode($v) . "&";
        }
        $poststring .= (!empty($CFG->an_tran_key)) ?
                        "x_tran_key" . "=" . urlencode($CFG->an_tran_key):
                        "x_password" . "=" . urlencode($CFG->an_password);

        // referer
        $anrefererheader = '';
        if (!(empty($CFG->an_referer) || $CFG->an_referer == "http://" || $CFG->an_referer == "https://")) {
            $anrefererheader = "Referer: " . $CFG->an_referer . "\r\n";
        }

        $response = array();
        $fp = fsockopen("ssl://" . AN_HOST, AN_PORT, $errno, $errstr, 60);
        if (!$fp) {
            $this->ccerrormsg =  "$errstr ($errno)";
            return;
        } else {
            //send the server request
            fputs($fp,
                  "POST " . AN_PATH . " HTTP/1.0\r\n" .
                  "Host: " . AN_HOST . "\r\n" . $anrefererheader .
                  "Content-type: application/x-www-form-urlencoded\r\n" .
                  "Connection: close\r\n" .
                  "Content-length: " . strlen($poststring) . "\r\n\r\n" .
                  $poststring . "\r\n"
            );

            //Get the response header from the server
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
            if ($response === false)
            {
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
                $this->email_cc_error_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $response);
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
                    $this->email_cc_error_to_admin("Error while trying to insert valid transaction", $datax);
                }

            } // end if (!enrol_student)

            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }
            redirect($destination);
        }
    }

    function zero_cost($course) { return(abs($this->get_course_cost($course)) < 0.01); }
    function get_course_cost($course) {
        global $CFG;
        $cost = (float)0;

        if (isset($course->cost)) {
            if (((float)$course->cost) < 0) {
                $cost = (float)$CFG->enrol_cost;
            } else {
                $cost = (float)$course->cost;
            }
        }
        $cost = format_float($cost, 2);
        return $cost;
    }

    /// Override the get_access_icons() function
    function get_access_icons($course) {
        global $CFG;

        $str = parent::get_access_icons($course);
        $cost = $this->get_course_cost($course);

        if (abs($cost) > 0.00) {
            $strrequirespayment = get_string("requirespayment");
            $strcost = get_string("cost");

            if (empty($CFG->enrol_currency)) {
                set_config('enrol_currency', 'USD');
            }

            switch ($CFG->enrol_currency) {
                case 'EUR': $currency = '&euro;'; break;
                case 'CAD': $currency = '$'; break;
                case 'GBP': $currency = '&pound;'; break;
                case 'JPY': $currency = '&yen;'; break;
                default:    $currency = '$'; break;
            }

            $str .= "<p class=\"coursecost\"><font size=-1>$strcost: " .
                    "<a title=\"$strrequirespayment\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">" .
                    "$currency" . format_float($cost, 2) . '</a></font></p>';
        }
        return $str;
    }


    function config_form($frm) {
        global $CFG;

        $vars = array('an_login', 'an_tran_key', 'an_password', 'delete_current', 'an_referer', 'an_avs', 'an_test',
                      'enrol_cost', 'enrol_currency', 'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins');

        foreach ($vars as $var) {
            if (!isset($frm->$var)) {
                $frm->$var = '';
            }
        }

        if (!$this->check_openssl_loaded()) {
            notify('PHP must be compiled with SSL support (--with-openssl)');
        }

        if (data_submitted()) {
            // Some required fields
            if (empty($frm->an_login)) {
                notify("an_login required");
            }
            if (empty($frm->an_tran_key) && empty($frm->an_password)) {
                notify("an_tran_key or an_password required");
            }
            if (empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') {
                notify("loginhttps must be ON");
            }
        }
        include($CFG->dirroot.'/enrol/authorize/config.html');
    }

    function check_openssl_loaded() {
        return extension_loaded('openssl');
    }

    function process_config($config) {
        global $CFG;

        if ((empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') ||
            !$this->check_openssl_loaded()) {
            return false;
        }

        $return = true;

        if (!isset($config->an_login)) {
            $config->an_login = '';
        }
        set_config('an_login', $config->an_login);

        if (empty($config->an_referer)) {
            $config->an_referer = 'http://';
        }
        set_config('an_referer', $config->an_referer);

        if (!isset($config->an_avs)) {
            $config->an_avs = '';
        }
        set_config('an_avs', $config->an_avs);

        if (!isset($config->an_test)) {
            $config->an_test = '';
        }
        set_config('an_test', $config->an_test);

        // enrol config

        if (!isset($config->enrol_cost)) {
            $config->enrol_cost = '0';
        }
        set_config('enrol_cost', $config->enrol_cost);

        if (!isset($config->enrol_currency)) {
            $config->enrol_currency = 'USD';
        }
        set_config('enrol_currency', $config->enrol_currency);

        if (!isset($config->enrol_mailstudents)) {
            $config->enrol_mailstudents = '';
        }
        set_config('enrol_mailstudents', $config->enrol_mailstudents);

        if (!isset($config->enrol_mailteachers)) {
            $config->enrol_mailteachers = '';
        }
        set_config('enrol_mailteachers', $config->enrol_mailteachers);

        if (!isset($config->enrol_mailadmins)) {
            $config->enrol_mailadmins = '';
        }
        set_config('enrol_mailadmins', $config->enrol_mailadmins);

        // Some required fields
        if (empty($config->an_login)) {
            $return = false;
        }

        if (!isset($config->an_password)) {
            $config->an_password = '';
        }
        if (!isset($config->an_tran_key)) {
            $config->an_tran_key = '';
        }

        if (!empty($config->an_password)) { // password is changing
            set_config('an_password', $config->an_password);
        }
        elseif (!empty($config->delete_current) and !empty($config->an_tran_key)) {
            set_config('an_password', '');
            $CFG->an_password = '';
        }

        if (empty($config->an_tran_key) and empty($CFG->an_password)) {
            $return = false;
        }
        else {
            set_config('an_tran_key', $config->an_tran_key);
        }

        return $return;
    }

    function email_cc_error_to_admin($subject, $data) {
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

} // end of class definition
?>
