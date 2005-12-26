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
define('AN_STATUS_NONE',    0x00);
/**
 * Authorized.
 */
define('AN_STATUS_AUTH',    0x01);
/**
 * Captured.
 */
define('AN_STATUS_CAPTURE', 0x02);
/**
 * Auth_Captured.
 */
define('AN_STATUS_AUTHCAPTURE', AN_STATUS_AUTH|AN_STATUS_CAPTURE);
/**
 * Refunded.
 */
define('AN_STATUS_CREDIT', 0x04);
/**
 * Voided.
 */
define('AN_STATUS_VOID', 0x08);
/**
 * Expired.
 */
define('AN_STATUS_EXPIRE', 0x10);

require_once("$CFG->dirroot/enrol/enrol.class.php");

/**
 * enrolment_plugin_authorize
 *
 */
class enrolment_plugin extends enrolment_base
{
    /**
     * Credit card error message.
     *
     * @var string
     * @access public
     */
    var $ccerrormsg;

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    var $log;


    /**
     * Shows a credit card form for registration.
     *
     * @param object $course Course info
     * @access public
     */
    function print_entry($course)
    {
        global $CFG, $USER, $form;

        if ($this->zero_cost($course) || isguest()) { // No money for guests ;)
            parent::print_entry($course);
            return;
        }

        // check payment
        $this->prevent_double_paid($course);

        // I want to pay on SSL.
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
     * @access public
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
    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once($CFG->dirroot . '/enrol/authorize/ccval.php');
        require_once($CFG->dirroot . '/enrol/authorize/action.php');

        if (empty($form->ccfirstname) || empty($form->cclastname) ||
            empty($form->cc) || empty($form->cvv) || empty($form->cctype) ||
            empty($form->ccexpiremm) || empty($form->ccexpireyyyy) || empty($form->cczip)) {
                $this->ccerrormsg = get_string("allfieldsrequired");
                return;
        }

        if (!empty($CFG->an_test)) {
            error("Credit card module cannot be present because of test mode");
            return;
        }

        $this->prevent_double_paid($course);

        $exp_date = ($form->ccexpiremm < 10) ? strval('0'.$form->ccexpiremm) : strval($form->ccexpiremm);
        $exp_date .= $form->ccexpireyyyy;
        $valid_cc = CCVal($form->cc, $form->cctype, $exp_date);
        $curcost = $this->get_course_cost($course);
        $useripno = getremoteaddr(); // HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR, REMOTE_ADDR

        if (!$valid_cc) {
            $this->ccerrormsg = get_string( (($valid_cc===0) ? 'ccexpired' : 'ccinvalid'), 'enrol_authorize' );
            return;
        }

        // NEW ORDER
        $timenow = time();
        $order = new stdClass();
        $order->cclastfour = substr($form->cc, -4);
        $order->ccexp = $exp_date;
        $order->cvv = $form->cvv;
        $order->ccname = $form->ccfirstname . " " . $form->cclastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->avscode = 'P';
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            $this->email_to_admin("Error while trying to insert new data", $order);
            $this->ccerrormsg = "Insert record error. Admin has been notified!";
            return;
        }

        $extra = new stdClass();
        $extra->x_first_name = $form->ccfirstname;
        $extra->x_last_name = $form->cclastname;
        $extra->x_address = $USER->address;
        $extra->x_city = $USER->city;
        $extra->x_zip = $form->cczip;
        $extra->x_country = $USER->country;
        $extra->x_state = '';
        $extra->x_card_num = $form->cc;
        $extra->x_card_code = $form->cvv;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_exp_date = $exp_date;
        $extra->x_email = $USER->email;
        $extra->x_email_customer = 'TRUE';
        $extra->x_cust_id = $USER->id;
        $extra->x_customer_ip = $useripno;
        $extra->x_phone = '';
        $extra->x_fax = '';
        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $message = '';
        $an_review = !empty($CFG->an_review);
        $action = $an_review ? AN_ACTION_AUTH_ONLY : AN_ACTION_AUTH_CAPTURE;
        $success = authorizenet_action($order, $message, $extra, $action);
        if (!$success) {
            $this->email_to_admin($message, $order);
            $this->ccerrormsg = $message;
            return;
        }

        if (intval($order->transid) == 0) { // I know it is test mode. :)
            error("Credit card module cannot be present because of test mode");
            return;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        if ($an_review) { // review enabled, inform admin and redirect to main page.
            if (update_record("enrol_authorize", $order)) {
                $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
                $a->orderid = $order->id;
                $a->transid = $order->transid;
                $a->amount = "$order->currency $order->amount";
                $a->expireon = getsettletime($timenow + (30 * 3600 * 24));
                $a->captureon = getsettletime($timenow + (intval($CFG->an_review_day) * 3600 * 24));
                $a->course = $course->fullname;
                $a->user = fullname($USER);
                $a->acstatus = ($CFG->an_review_day > 0) ? get_string('yes') : get_string('no');
                $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
                $a->course = $course->shortname;
                $a->orderid = $order->id;
                $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin, $USER, $emailsubject, $emailmessage);
                }
            }
            else {
                $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                                      "ID=$order->id in enrol_authorize table.", $order);
            }
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return;
        }

        // credit card captured, ENROL student...
        if (!update_record("enrol_authorize", $order)) {
            $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                                   "ID=$order->id in enrol_authorize table.", $order);
                                   // no error occured??? enrol student??? return??? Database busy???
        }

        if ($course->enrolperiod) {
            $timestart = $timenow;
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
            $this->email_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $order);
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl; unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
    }


    /**
     * zero_cost
     *
     * @param unknown_type $course
     * @return number
     * @access private
     */
    function zero_cost($course) {
        $curcost = $this->get_course_cost($course);
        return (abs($curcost['cost']) < 0.01);
    }


    /**
     * get_course_cost
     *
     * @param unknown_type $course
     * @return unknown
     * @access private
     */
    function get_course_cost($course)
    {
        global $CFG;

        $cost = (float)0;
        $currency = (!empty($course->currency))
                     ? $course->currency :( empty($CFG->enrol_currency)
                                            ? 'USD' : $CFG->enrol_currency );

        if (!empty($course->cost)) {
            $cost = (float)(((float)$course->cost) < 0) ? $CFG->enrol_cost : $course->cost;
        }

        $cost = format_float($cost, 2);
        $ret = array('cost' => $cost, 'currency' => $currency);

        return $ret;
    }


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     * @access public
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
     * @access public
     */
    function config_form($frm)
    {
        global $CFG;

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
                $mconfig = get_config('enrol/authorize');
                $lastcron = intval($mconfig->an_lastcron);
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

                if ($count = count_records('enrol_authorize', 'status', AN_STATUS_AUTH)) {
                    notify("CRON DISABLED. TRANSACTIONS WITH A STATUS OF AN_STATUS_AUTH WILL BE CANCELLED UNLESS YOU CHECK IT. TOTAL $count");
                }
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
     * @access public
     */
    function process_config($config)
    {
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
        set_config('an_cutoff_hour', optional_param('an_cutoff_hour', '0') );
        set_config('an_cutoff_min', optional_param('an_cutoff_min', '5') );

        // required!
        // if is it OK, process next config.
        if (empty($CFG->loginhttps)) return false;
        if (!$this->check_openssl_loaded()) return false;

        $login_val = optional_param('an_login', '');
        if (empty($login_val)) return false;
        set_config('an_login', $login_val);

        $tran_val = optional_param('an_tran_key', '');
        $password_val = optional_param('an_password', '');
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
                // Cron must change an_lastcron. :))
                $mconfig = get_config('enrol/authorize');
                $lastcron = intval($mconfig->an_lastcron);
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


    /**
     * email_to_admin
     *
     * @param string $subject
     * @param mixed $data
     * @access private
     */
    function email_to_admin($subject, $data) {
        $admin = get_admin();
        $site = get_site();
        $data = (array)$data;

        $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";
        foreach ($data as $key => $value) {
            $message .= "$key => $value\n";
        }
        email_to_user($admin, $admin, "CC ERROR: ".$subject, $message);
    }


    /**
     * prevent_double_paid
     *
     * @param object $course
     * @access private
     */
    function prevent_double_paid($course)
    {
        global $CFG, $SESSION, $USER;

        if ($rec = get_record('enrol_authorize', 'userid', $USER->id, 'courseid', $course->id, 'status', AN_STATUS_AUTH, 'id')) {
            $a->orderid = $rec->id;
            redirect($CFG->wwwroot, get_string("paymentpending", "enrol_authorize", $a), '20');
            return;
        }
        if (isset($SESSION->ccpaid)) {
            unset($SESSION->ccpaid);
            redirect($CFG->wwwroot . '/login/logout.php');
            return;
        }
    }


    /**
     * check_openssl_loaded
     *
     * @return bool
     * @access private
     */
    function check_openssl_loaded() {
        return extension_loaded('openssl');
    }


    /**
     * cron
     * @access public
     */
    function cron()
    {
        global $CFG;
        parent::cron();
        require_once("$CFG->dirroot/enrol/authorize/action.php");

        $timenow = time();
        $timenowsettle = getsettletime($timenow);
        $timediff30 = $timenowsettle - (30 * 3600 * 24);
        // These 2 lines must be HERE and must be EXUCUTED. See process_config.
        // We use an_lastcron when processing AUTOCAPTURE feature.
        // Order is important. 1. get_config 2. set_config
        $mconfig = get_config('enrol/authorize'); // MUST be 1st.
        set_config('an_lastcron', $timenow, 'enrol/authorize'); // MUST be 2nd.

        $random100 = mt_rand(0, 100);

        if ($random100 < 33) {
            $select = "(status = '" .AN_STATUS_NONE. "') AND (timecreated < '$timediff30')";
            delete_records_select('enrol_authorize', $select);
        }
        elseif ($random100 > 66) {
            $select = "(status = '" .AN_STATUS_AUTH. "') AND (timecreated < '$timediff30')";
            execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status = '" .AN_STATUS_EXPIRE. "' WHERE $select", false);
        }
        else {
            $timediff60 = $timenowsettle - (60 * 3600 * 24);
            $select = "(status = '" .AN_STATUS_EXPIRE. "') AND (timecreated < '$timediff60')";
            delete_records_select('enrol_authorize', $select);
        }

        if (!empty($CFG->an_test)) {
            return; // AUTOCAPTURE doesn't work in test mode.
        }
        if (empty($CFG->an_review) || empty($CFG->an_review_day) || $CFG->an_review_day < 1) {
            return; // AUTOCAPTURE disabled. admin, teacher review it manually
        }

        // AUTO-CAPTURE: Transaction must be captured within 30 days. Otherwise it will expired.
        $timediffcnf = $timenowsettle - (intval($CFG->an_review_day) * 3600 * 24);
        $select = "status = '" .AN_STATUS_AUTH. "' AND timecreated < '$timediffcnf' AND timecreated > '$timediff30'";
        if (!$orders = get_records('enrol_authorize', $select)) {
            return;
        }

        // Calculate connection speed for each transaction. Default: 3 secs.
        $everyconnection = empty($mconfig->an_eachconnsecs) ? 3 : intval($mconfig->an_eachconnsecs);
        $ordercount = count((array)$orders);
        $maxsecs = $everyconnection * $ordercount;
        if ($maxsecs + intval($mconfig->an_lastcron) > $timenow) {
            return; // autocapture runs every eachconnsecs*count.
        }

        $faults = '';
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";
        foreach ($orders as $order) {
            $message = '';
            $extra = NULL;
            $oldstatus = $order->status;
            $success = authorizenet_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE);
            if ($success) {
                if (!update_record("enrol_authorize", $order)) {
                    $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                    "ID=$order->id in enrol_authorize table.", $order);
                }
                $timestart = $timeend = 0;
                if ($course = get_record_sql("SELECT enrolperiod FROM {$CFG->prefix}course WHERE id='$order->courseid'")) {
                    if ($course->enrolperiod) {
                        $timestart = $timenow;
                        $timeend = $timestart + $course->enrolperiod;
                    }
                }
                if (enrol_student($order->userid, $order->courseid, $timestart, $timeend, 'authorize')) {
                    $this->log .= "User($order->userid) has been enrolled to course($order->courseid).\n";
                }
                else {
                    $faults .= "Error while trying to enrol ".fullname($USER)." in '$course->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else { // not success
                $this->log .= "Order $order->id: " . $message . "\n";
                if ($order->status != $oldstatus) { //expired
                    update_record("enrol_authorize", $order);
                }
            }
        }

        $timenow = time();
        $elapsed = $timenow - $elapsed;
        $everyconnection = ceil($elapsed / $ordercount);
        set_config('an_eachconnsecs', $everyconnection, 'enrol/authorize');

        $this->log .= "AUTHORIZE.NET CRON FINISHED: " . userdate($timenow);

        $adminuser = get_admin();
        if (!empty($faults)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON FAULTS", $faults);
        }
        if (!empty($CFG->enrol_mailadmins)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON LOG", $this->log);
        }
    }
}
?>
