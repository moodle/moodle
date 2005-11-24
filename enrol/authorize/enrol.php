<?php  // $Id$

// Authorize.net
define('AN_HOST', 'secure.authorize.net');
define('AN_HOST_TEST', 'certification.authorize.net');
define('AN_PORT', 443);
define('AN_PATH', '/gateway/transact.dll');
define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR', '3');
define('AN_DELIM', '|');
define('AN_ENCAP', '"');

/**
 * New order. No transaction was made.
 */
define('AN_STATUS_NONE',    0x0);

/**
 * Authorized.
 */
define('AN_STATUS_AUTH',    0x1);

/**
 * Captured.
 */
define('AN_STATUS_CAPTURE', 0x2);

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

    /**
     * Credit card error message.
     *
     * @var string
     */
    var $ccerrormsg;

    
    /**
     * Shows a credit card form for registration.
     *
     * @param object $course Course info
     */
    function print_entry($course) {
        global $CFG, $USER, $form;

        if ($this->zero_cost($course) || isguest()) {
            // No money for guests ;)
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
    
    
    /**
     * Checks form params.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     */
    function check_entry($form, $course) {
        if ($this->zero_cost($course) || isguest() || (!empty($form->password))) {
            parent::check_entry($form, $course);
        } else {
            $this->cc_submit($form, $course);
        }
    }

    /**
     * Credit card number mode.
     * Send to authorize.net.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function cc_submit($form, $course) {
        global $CFG, $USER, $SESSION;
        require_once($CFG->dirroot . '/enrol/authorize/ccval.php');

        if (empty($form->ccfirstname) || empty($form->cclastname) ||
            empty($form->cc) || empty($form->cvv) || empty($form->cctype) ||
            empty($form->ccexpiremm) || empty($form->ccexpireyyyy) || empty($form->cczip)) {
                $this->ccerrormsg = get_string("allfieldsrequired");
                return;
        }
        
        $this->check_paid();
        $exp_date = ($form->ccexpiremm < 10) ? strval('0'.$form->ccexpiremm) : strval($form->ccexpiremm);
        $exp_date .= $form->ccexpireyyyy;
        $valid_cc = CCVal($form->cc, $form->cctype, $exp_date);
        $curcost = $this->get_course_cost($course);
        $useripno = getremoteaddr(); // HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR, REMOTE_ADDR

        if (!$valid_cc) {
            $this->ccerrormsg = get_string( (($valid_cc===0) ? 'ccexpired' : 'ccinvalid'), 'enrol_authorize' );
            return;
        }
        
        // NEW ORDER ID
        $datanew = new stdClass();
        $datanew->cclastfour = substr($form->cc, -4);
        $datanew->ccexp = $exp_date;
        $datanew->cvv = $form->cvv;
        $datanew->ccname = $form->ccfirstname . " " . $form->cclastname;
        $datanew->courseid = $course->id;
        $datanew->userid = $USER->id;
        $datanew->avscode = 'P';
        $datanew->status = AN_STATUS_NONE; // it will be changed...
        $datanew->timeupdated = 0; // cron changes this.
        $datanew->timecreated = time();
        $datanew->id = insert_record("enrol_authorize", $datanew);
        if (!$datanew->id) {
            $this->email_to_admin("Error while trying to insert new data", $datanew);
            $this->ccerrormsg = "Insert record error. Admin has been notified!";
            return;
        }
        
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
            'x_address'         => $USER->address,
            'x_city'            => $USER->city,
            'x_zip'             => $form->cczip,
            'x_country'         => $USER->country,
            'x_state'           => '',
            'x_card_num'        => $form->cc,
            'x_card_code'       => $form->cvv,
            'x_currency_code'   => $curcost['currency'],
            'x_amount'          => $curcost['cost'],
            'x_exp_date'        => $exp_date,
            'x_email'           => $USER->email,
            'x_email_customer'  => 'False',
            'x_cust_id'         => $USER->id,
            'x_customer_ip'     => $useripno,
            'x_phone'           => '',
            'x_fax'             => '',
            'x_invoice_num'     => $datanew->id,
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
        $connect_host = empty($CFG->an_test) ? AN_HOST : AN_HOST_TEST;
        $fp = fsockopen("ssl://" . $connect_host, AN_PORT, $errno, $errstr, 60);
        if (!$fp) {
            $this->ccerrormsg =  "$errstr ($errno)";
            delete_records("enrol_authorize", "id", $datanew->id); // no connection
            return;
        } else {
            fputs($fp,
                "POST " . AN_PATH . " HTTP/1.0\r\n" .
                "Host: $connect_host\r\n" .
                $anrefererheader .
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-length: " . strlen($poststring) . "\r\n" .
                "Connection: close\r\n\r\n" .
                $poststring . "\r\n\r\n"
            );

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

        if ($response[0] == AN_APPROVED) {
            $SESSION->ccpaid = 1; // security check: don't duplicate payment
            $datanew->authcode = strval($response[4]); // Authorization or Approval code
            $datanew->avscode = strval($response[5]); // Address Verification System code
            $datanew->transid = strval($response[6]); // TransactionID
            $datanew->status = AN_STATUS_AUTH | AN_STATUS_CAPTURE; // AUTH_CAPTURE
            $datanew->timeupdated = time(); // captured, so change it.
            if (!update_record("enrol_authorize", $datanew)) {
                $this->email_to_admin( "Error while trying to update data. Please edit manually this record: " .
                "ID=$datanew->id in enrol_authorize table." , $datanew);
                // no error occured??? enrol student??? return??? Database busy???
            }

            if ($course->enrolperiod) {
                $timestart = time();
                $timeend = $timestart + $course->enrolperiod;
            } else {
                $timestart = $timeend = 0;
            }

            if (enrol_student($USER->id, $course->id, $timestart, $timeend, 'authorize')) {
                $teacher = get_teacher($course->id);
                if (!empty($CFG->enrol_mailstudents)) {
                    $a->coursename = "$course->fullname";
                    $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id";
                    email_to_user($USER,
                                  $teacher,
                                  get_string("enrolmentnew", '', $course->shortname),
                                  get_string('welcometocoursetext', '', $a));
                }
                if (!empty($CFG->enrol_mailteachers)) {
                    $a->course = "$course->fullname";
                    $a->user = fullname($USER);
                    email_to_user($teacher,
                                  $USER,
                                  get_string("enrolmentnew", '', $course->shortname),
                                  get_string('enrolmentnewuser', '', $a));
                }
                if (!empty($CFG->enrol_mailadmins)) {
                    $a->course = "$course->fullname";
                    $a->user = fullname($USER);
                    $admins = get_admins();
                    foreach ($admins as $admin) {
                        email_to_user($admin,
                                      $USER,
                                      get_string("enrolmentnew", '', $course->shortname),
                                      get_string('enrolmentnewuser', '', $a));
                    }
                }
            } else {
                $this->email_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $response);
            }

            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }
            redirect($destination);

        } else {
            $this->ccerrormsg = isset($response[3]) ? $response[3] : 'unknown error';
        }
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


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     */
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

    
    /**
     * Shows config form & errors
     *
     * @param object $frm
     */
    function config_form($frm) {
        global $CFG;

        $vars = array('an_login', 'an_tran_key', 'an_password', 'an_referer', 'an_test', 'an_review', 'an_review_day',
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
            // something POSTed, Some required fields
            if (empty($frm->an_login)) {
                notify("an_login required");
            }
            if (empty($frm->an_tran_key) && empty($frm->an_password)) {
                notify("an_tran_key or an_password required");
            }
            if (empty($CFG->loginhttps)) {
                notify("\$CFG->loginhttps must be ON");
            }
            
            // ******************* AUTOCAPTURE *******************
            if (!(empty($frm->an_review) || $frm->an_review_day < 1)) {
                // ++ENABLED++
                // Cron must be runnig!!! Check last cron...
                $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
                if (time() - $lastcron > 3600 * 24) {
                    // Cron must be enabled if you want to use autocapture feature.
                    // Setup cron or disable an_review again...
                    // Otherwise, transactions will be cancelled unless you review it within 30 days.
                    notify(get_string('cronwarning', 'admin'));
                }
            } else {
                // --DISABLED--
                // Cron will NOT run anymore, because autocapture runs with cron.
                // Transactions with AN_STATUS_AUTH will be cancelled and we can display this warning to admin!
                // Admin can check (Accept|Deny) new transactions manually.
                // :: TO DO ::
                //if ($count = count_records('enrol_authorize', 'status', AN_STATUS_AUTH)) {
                //    notify('CRON DISABLED. TRANSACTIONS WITH AN_STATUS_AUTH WILL BE CANCELLED UNLESS YOU CHECK IT. TOTAL $count');
                //}
            }
            // ***************************************************
        }

        include($CFG->dirroot.'/enrol/authorize/config.html');
    }

    
    /**
     * process_config
     *
     * @param object $config
     * @return bool true if it will be saved.
     */
    function process_config($config) {
        global $CFG;
        
        // ENROL config
        set_config('enrol_cost', optional_param('enrol_cost', 5, PARAM_INT) );
        set_config('enrol_currency', optional_param('enrol_currency', 'USD', PARAM_ALPHA) );
        set_config('enrol_mailstudents', optional_param('enrol_mailstudents', '') );
        set_config('enrol_mailteachers', optional_param('enrol_mailteachers', '') );
        set_config('enrol_mailadmins', optional_param('enrol_mailadmins', '') );

        // AUTHORIZE.NET config
        
        // not required!
        set_config('an_test', optional_param('an_test', '') );
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL) );

        // required!
        // if is it OK, process next config.
        if (empty($CFG->loginhttps)) return false;
        if (!$this->check_openssl_loaded()) return false;

        $login_val = optional_param('an_login', '');
        if (empty($login_val)) return false;
        set_config('an_login', $login_val);

        $password_val = optional_param('an_password', '');
        $tran_val = optional_param('an_tran_key', '');
        if (empty($tran_val) && empty($password_val)) return false;
        set_config('an_password', $password_val);
        set_config('an_tran_key', $tran_val);

        // an_review & an_review_day & cron depencies...
        $review_val = optional_param('an_review', '');
        if (empty($review_val)) {
            // review disabled. cron is not required. AUTH_CAPTURE works.
            set_config('an_review', $review_val);
        } else {
            // review enabled.
            $review_day_val = optional_param('an_review_day', 5, PARAM_INT);
            if ($review_day_val < 0) $review_day_val = 0;
            elseif ($review_day_val > 29) $review_day_val = 29;
            if ($review_day_val > 0) {
                // cron is required.
                $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
                if (time() - $lastcron > 3600 * 24) {
                    // No!!! I am not lucky. No changes please...
                    return false;
                }
            }
            set_config('an_review', $review_val);
            set_config('an_review_day', $review_day_val);
        }
        
        return true;
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

    function cron() {
        global $CFG;
        parent::cron();
        
        $timenow = time();
        // delete very old records: status=AN_STATUS_NONE & timecreated=-60day.
        // no credit card transaction is made in status AN_STATUS_NONE.
        $timediff = $timenow - (60 * 3600 * 24);
        $select = "(status = '" . AN_STATUS_NONE . "') AND (timecreated < '$timediff')";
        if (count_records_select('enrol_authorize', $select)) {
            mtrace("Deleting records in authorize table older than 60 days (status=AN_STATUS_NONE).");
            delete_records_select('enrol_authorize', $select);
        }
        
        // (review not enabled) || (AUTOCAPTURE disabled = admin, teacher review it manually)
        if ( empty($CFG->an_review) || empty($CFG->an_review_day) || $CFG->an_review_day < 1 ) return;

        // :: TO DO ::
        // AUTO CAPTURE
        
    }

} // end of class definition
?>