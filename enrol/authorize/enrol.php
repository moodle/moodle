<?php // $Id$

require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');

/**
 * Authorize.net Payment Gateway plugin
 */
class enrolment_plugin_authorize
{

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    var $log;


    /**
     * Presents registration forms.
     *
     * @param object $course Course info
     * @access public
     */
    function print_entry($course) {
        global $CFG, $USER, $form;

        $zerocost = zero_cost($course);
        if ($zerocost) {
            $manual = enrolment_factory::factory('manual');
            if (!empty($this->errormsg)) {
                $manual->errormsg = $this->errormsg;
            }
            $manual->print_entry($course);
            return;
        }

        prevent_double_paid($course);
        httpsrequired();

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            if (empty($CFG->loginhttps)) {
                print_error('httpsrequired', 'enrol_authorize');
            } else {
                $wwwsroot = str_replace('http:','https:', $CFG->wwwroot);
                redirect("$wwwsroot/course/enrol.php?id=$course->id");
                exit;
            }
        }

        $strcourses = get_string('courses');
        $strloginto = get_string('loginto', '', $course->shortname);

        $navlinks = array();
        $navlinks[] = array('name' => $strcourses, 'link' => "$CFG->wwwroot/course/", 'type' => 'misc');
        $navlinks[] = array('name' => $strloginto, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header($strloginto, $course->fullname, $navigation);
        print_course($course, '80%');

        if ($course->password) {
            print_heading(get_string('choosemethod', 'enrol_authorize'), 'center');
        }

        print_simple_box_start('center', '80%');
        if ($USER->username == 'guest') { // only real guest user, not for users with guest role
            $curcost = get_course_cost($course);
            echo '<div class="mdl-align">';
            echo '<p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $curcost[currency] $curcost[cost]".'</b></p>';
            echo '<p><a href="'.$CFG->httpswwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        }
        else {
            require_once($CFG->dirroot.'/enrol/authorize/enrol_form.php');
            $frmenrol = new enrol_authorize_form('enrol.php', compact('course'));
            if ($frmenrol->get_data()) {
                $authorizeerror = '';
                switch ($form->paymentmethod) {
                    case AN_METHOD_CC:
                        $authorizeerror = $this->cc_submit($form, $course);
                        break;

                    case AN_METHOD_ECHECK:
                        $authorizeerror = $this->echeck_submit($form, $course);
                        break;
                }
                if (!empty($authorizeerror)) {
                    error($authorizeerror);
                }
            }
            $frmenrol->display();
        }
        print_simple_box_end();

        if ($course->password) {
            $password = '';
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        print_footer();
    }


    function print_enrolmentkeyfrom($course)
    {
        $manual = enrolment_factory::factory('manual');
        $manual->print_enrolmentkeyfrom($course);
    }


    /**
     * Validates registration forms and enrols student to course.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access public
     */
    function check_entry($form, $course)
    {
        global $CFG;

        if (zero_cost($course) || (!empty($course->password) && !empty($form->enrol) && $form->enrol == 'manual')) {
            $manual = enrolment_factory::factory('manual');
            $manual->check_entry($form, $course);
            if (!empty($manual->errormsg)) {
                $this->errormsg = $manual->errormsg;
            }
        }
    }



    /**
     * The user submitted credit card form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);
        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;

        // NEW CC ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_CC;
        $order->refundinfo = substr($form->cc, -4);
        $order->ccname = $form->firstname . " " . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            email_to_admin("Error while trying to insert new data", $order);
            return "Insert record error. Admin has been notified!";
        }

        $extra = new stdClass();
        $extra->x_card_num = $form->cc;
        $extra->x_card_code = $form->cvv;
        $extra->x_exp_date = $exp_date;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->firstname;
        $extra->x_last_name = $form->lastname;
        $extra->x_country = $form->cccountry;
        $extra->x_address = $form->ccaddress;
        $extra->x_state = $form->ccstate;
        $extra->x_city = $form->cccity;
        $extra->x_zip = $form->cczip;

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        if (!empty($CFG->an_authcode) && !empty($form->ccauthcode)) {
            $action = AN_ACTION_CAPTURE_ONLY;
            $extra->x_auth_code = $form->ccauthcode;
        }
        elseif (!empty($CFG->an_review)) {
            $action = AN_ACTION_AUTH_ONLY;
        }
        else {
            $action = AN_ACTION_AUTH_CAPTURE;
        }

        $message = '';
        if (AN_APPROVED != authorize_action($order, $message, $extra, $action, $form->cctype)) {
            email_to_admin($message, $order);
            return $message;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment

        if (AN_ACTION_AUTH_ONLY == $action) { // review enabled, inform payment managers and redirect the user who have paid to main page.
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
            $a->orderid = $order->id;
            $a->transid = $order->transid;
            $a->amount = "$order->currency $order->amount";
            $a->expireon = userdate(authorize_getsettletime($timenow + (30 * 3600 * 24)));
            $a->captureon = userdate(authorize_getsettletime($timenow + (intval($CFG->an_capture_day) * 3600 * 24)));
            $a->course = $course->fullname;
            $a->user = fullname($USER);
            $a->acstatus = ($CFG->an_capture_day > 0) ? get_string('yes') : get_string('no');
            $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
            $a = new stdClass;
            $a->course = $course->shortname;
            $a->orderid = $order->id;
            $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            if (($paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments'))) {
                foreach ($paymentmanagers as $paymentmanager) {
                    email_to_user($paymentmanager, $USER, $emailsubject, $emailmessage);
                }
            }
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return;
        }

        // Credit card captured, ENROL student now...
        if (enrol_into_course($course, $USER, 'authorize')) {
            if (!empty($CFG->enrol_mailstudents)) {
                send_welcome_messages($order->id);
            }
            if (!empty($CFG->enrol_mailteachers)) {
                $context = get_context_instance(CONTEXT_COURSE, $course->id);
                $paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments', '', '', '0', '1');
                $paymentmanager = array_shift($paymentmanagers);
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                email_to_user($paymentmanager,
                              $USER,
                              get_string("enrolmentnew", '', format_string($course->shortname)),
                              get_string('enrolmentnewuser', '', $a));
            }
            if (!empty($CFG->enrol_mailadmins)) {
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin,
                                  $USER,
                                  get_string("enrolmentnew", '', format_string($course->shortname)),
                                  get_string('enrolmentnewuser', '', $a));
                }
            }
        } else {
            email_to_admin("Error while trying to enrol " . fullname($USER) . " in '$course->fullname'", $order);
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl; unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        load_all_capabilities();
        redirect($destination, get_string('paymentthanks', 'moodle', $course->fullname), 10);
    }


    /**
     * The user submitted echeck form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function echeck_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);
        $isbusinesschecking = ($form->acctype == 'BUSINESSCHECKING');

        // NEW ECHECK ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_ECHECK;
        $order->refundinfo = $isbusinesschecking ? 1 : 0;
        $order->ccname = $form->firstname . ' ' . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            email_to_admin("Error while trying to insert new data", $order);
            return "Insert record error. Admin has been notified!";
        }

        $extra = new stdClass();
        $extra->x_bank_aba_code = $form->abacode;
        $extra->x_bank_acct_num = $form->accnum;
        $extra->x_bank_acct_type = $form->acctype;
        $extra->x_echeck_type = $isbusinesschecking ? 'CCD' : 'WEB';
        $extra->x_bank_name = $form->bankname;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->firstname;
        $extra->x_last_name = $form->lastname;
        $extra->x_country = $USER->country;
        $extra->x_address = $USER->address;
        $extra->x_city = $USER->city;
        $extra->x_state = '';
        $extra->x_zip = '';

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        $message = '';
        if (AN_REVIEW != authorize_action($order, $message, $extra, AN_ACTION_AUTH_CAPTURE)) {
            email_to_admin($message, $order);
            return $message;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
    }


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     * @access public
     */
    function get_access_icons($course) {

        $manual = enrolment_factory::factory('manual');
        $str = $manual->get_access_icons($course);
        $curcost = get_course_cost($course);

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
        $mconfig = get_config('enrol/authorize');

        if (!check_openssl_loaded()) {
            notify('PHP must be compiled with SSL support (--with-openssl)');
        }

        if (empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') {
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/$CFG->admin/settings.php?section=httpsecurity";
            notify(get_string('adminconfighttps', 'enrol_authorize', $a));
            return; // notice breaks the form and xhtml later
        }
        elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            $wwwsroot = qualified_me();
            $wwwsroot = str_replace('http:', 'https:', $wwwsroot);
            $a = new stdClass;
            $a->url = $wwwsroot;
            notify(get_string('adminconfighttpsgo', 'enrol_authorize', $a));
            return; // notice breaks the form and xhtml later
        }

        if (!empty($frm->an_review)) {
            $captureday = intval($frm->an_capture_day);
            $emailexpired = intval($frm->an_emailexpired);
            if ($captureday > 0 || $emailexpired > 0) {
                $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
                if ((time() - intval($lastcron) > 3600 * 24)) {
                    notify(get_string('admincronsetup', 'enrol_authorize'));
                }
            }
        }

        if (($count = count_records('enrol_authorize', 'status', AN_STATUS_AUTH))) {
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot."/enrol/authorize/index.php?status=".AN_STATUS_AUTH;
            notify(get_string('adminpendingorders', 'enrol_authorize', $a));
        }

        if (data_submitted()) {
            if (empty($mconfig->an_login)) {
                notify("an_login required");
            }
            if (empty($mconfig->an_tran_key) && empty($mconfig->an_password)) {
                notify("an_tran_key or an_password required");
            }
        }

        include($CFG->dirroot.'/enrol/authorize/config_form.php');
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
        $mconfig = get_config('enrol/authorize');

        // site settings
        if (($cost = optional_param('enrol_cost', 5, PARAM_INT)) > 0) {
            set_config('enrol_cost', $cost);
        }
        set_config('enrol_currency', optional_param('enrol_currency', 'USD', PARAM_ALPHA));
        set_config('enrol_mailstudents', optional_param('enrol_mailstudents', 0, PARAM_BOOL));
        set_config('enrol_mailteachers', optional_param('enrol_mailteachers', 0, PARAM_BOOL));
        set_config('enrol_mailadmins', optional_param('enrol_mailadmins', 0, PARAM_BOOL));

        // optional authorize.net settings
        set_config('an_avs', optional_param('an_avs', 0, PARAM_BOOL));
        set_config('an_authcode', optional_param('an_authcode', 0, PARAM_BOOL));
        set_config('an_test', optional_param('an_test', 0, PARAM_BOOL));
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL));

        $acceptmethods = optional_param('acceptmethods', get_list_of_payment_methods(), PARAM_ALPHA);
        set_config('an_acceptmethods', implode(',', $acceptmethods));
        $acceptccs = optional_param('acceptccs', array_keys(get_list_of_creditcards()), PARAM_ALPHA);
        set_config('an_acceptccs', implode(',', $acceptccs));
        $acceptechecktypes = optional_param('acceptechecktypes', get_list_of_bank_account_types(), PARAM_ALPHA);
        set_config('an_acceptechecktypes', implode(',', $acceptechecktypes));

        $cutoff_hour = optional_param('an_cutoff_hour', 0, PARAM_INT);
        $cutoff_min = optional_param('an_cutoff_min', 5, PARAM_INT);
        set_config('an_cutoff', $cutoff_hour * 60 + $cutoff_min);

        // cron depencies
        $reviewval = optional_param('an_review', 0, PARAM_BOOL);
        $captureday = optional_param('an_capture_day', 5, PARAM_INT);
        $emailexpired = optional_param('an_emailexpired', 2, PARAM_INT);
        $emailexpiredteacher = optional_param('an_emailexpiredteacher', 0, PARAM_BOOL);
        $sorttype = optional_param('an_sorttype', 'ttl', PARAM_ALPHA);

        $captureday = ($captureday > 29) ? 29 : (($captureday < 0) ? 0 : $captureday);
        $emailexpired = ($emailexpired > 5) ? 5 : (($emailexpired < 0) ? 0 : $emailexpired);

        if (!empty($reviewval) && ($captureday > 0 || $emailexpired > 0)) {
            $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
            if (time() - intval($lastcron) > 3600 * 24) {
                return false;
            }
        }

        set_config('an_review', $reviewval);
        set_config('an_capture_day', $captureday);
        set_config('an_emailexpired', $emailexpired);
        set_config('an_emailexpiredteacher', $emailexpiredteacher);
        set_config('an_sorttype', $sorttype);

        // https and openssl library is required
        if ((substr($CFG->wwwroot, 0, 5) !== 'https' and empty($CFG->loginhttps)) or !check_openssl_loaded()) {
            return false;
        }

        // REQUIRED fields;
        // an_login
        $loginval = optional_param('an_login', '');
        if (empty($loginval) && empty($mconfig->an_login)) {
            return false;
        }
        $loginval = !empty($loginval) ? rc4encrypt($loginval) : strval($mconfig->an_login);
        set_config('an_login', $loginval, 'enrol/authorize');

        // an_tran_key, an_password
        $tranval = optional_param('an_tran_key', '');
        $tranval = !empty($tranval) ? rc4encrypt($tranval) : (isset($mconfig->an_tran_key)?$mconfig->an_tran_key:'');
        $passwordval = optional_param('an_password', '');
        $passwordval = !empty($passwordval) ? rc4encrypt($passwordval) :(isset($mconfig->an_password)?$mconfig->an_password:'');
        $deletecurrent = optional_param('delete_current', '0', PARAM_BOOL);
        if (!empty($deletecurrent) and !empty($tranval)) {
            unset_config('an_password', 'enrol/authorize');
            $passwordval = '';
        }
        elseif (!empty($passwordval)) {
            set_config('an_password', $passwordval, 'enrol/authorize');
        }
        if (empty($tranval) and empty($passwordval)) {
            return false;
        }
        if (!empty($tranval)) {
            set_config('an_tran_key', $tranval, 'enrol/authorize');
        }

        return true;
    }

    /**
     * This function is run by admin/cron.php every time if admin has enabled this plugin.
     *
     * Everyday at settlement time (default is 00:05), it cleans up some tables
     * and sends email to admin/teachers about pending orders expiring if manual-capture has enabled.
     *
     * If admin set up 'Order review' and 'Capture day', it captures credits cards and enrols students.
     *
     * @access public
     */
    function cron()
    {
        global $CFG;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);
        $mconfig = get_config('enrol/authorize');

        mtrace("Processing authorize cron...");

        if (intval($mconfig->an_dailysettlement) < $settlementtime) {
            set_config('an_dailysettlement', $settlementtime, 'enrol/authorize');
            mtrace("    daily cron; some cleanups and sending email to admins the count of pending orders expiring", ": ");
            $this->cron_daily();
            mtrace("done");
        }

        mtrace("    scheduled capture", ": ");
        if (empty($CFG->an_review) or (!empty($CFG->an_test)) or (intval($CFG->an_capture_day) < 1) or (!check_openssl_loaded())) {
            mtrace("disabled");
            return; // order review disabled or test mode or manual capture or openssl wasn't loaded.
        }

        $timediffcnf = $settlementtime - (intval($CFG->an_capture_day) * $oneday);
        $select = "(status = '" .AN_STATUS_AUTH. "') AND (timecreated < '$timediffcnf') AND (timecreated > '$timediff30')";
        if (!($ordercount = count_records_select('enrol_authorize', $select))) {
            mtrace("no pending orders");
            return;
        }

        $eachconn = intval($mconfig->an_eachconnsecs);
        $eachconn = (($eachconn > 60) ? 60 : (($eachconn <= 0) ? 3 : $eachconn));
        if (($ordercount * $eachconn) + intval($mconfig->an_lastcron) > $timenow) {
            mtrace("blocked");
            return;
        }
        set_config('an_lastcron', $timenow, 'enrol/authorize');

        mtrace("    $ordercount orders are being processed now", ": ");

        $faults = '';
        $sendem = array();
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";

        $lastcourseid = 0;
        for ($rs = get_recordset_select('enrol_authorize', $select, 'courseid'); ($order = rs_fetch_next_record($rs)); )
        {
            $message = '';
            $extra = NULL;
            if (AN_APPROVED == authorize_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE)) {
                if ($lastcourseid != $order->courseid) {
                    $lastcourseid = $order->courseid;
                    $course = get_record('course', 'id', $lastcourseid);
                    $role = get_default_course_role($course);
                    $context = get_context_instance(CONTEXT_COURSE, $lastcourseid);
                }
                $timestart = $timeend = 0;
                if ($course->enrolperiod) {
                    $timestart = $timenow;
                    $timeend = $order->settletime + $course->enrolperiod;
                }
                $user = get_record('user', 'id', $order->userid);
                if (role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, 'authorize')) {
                    $this->log .= "User($user->id) has been enrolled to course($course->id).\n";
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $faults .= "Error while trying to enrol ".fullname($user)." in '$course->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else {
                $this->log .= "Error, Order# $order->id: " . $message . "\n";
            }
        }
        rs_close($rs);
        mtrace("processed");

        $timenow = time();
        $elapsed = $timenow - $elapsed;
        $eachconn = ceil($elapsed / $ordercount);
        set_config('an_eachconnsecs', $eachconn, 'enrol/authorize');

        $this->log .= "AUTHORIZE.NET CRON FINISHED: " . userdate($timenow);

        $adminuser = get_admin();
        if (!empty($faults)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON FAULTS", $faults);
        }
        if (!empty($CFG->enrol_mailadmins)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON LOG", $this->log);
        }

        // Send emails to students about which courses have enrolled.
        if (!empty($sendem)) {
            mtrace("    sending welcome messages to students", ": ");
            send_welcome_messages($sendem);
            mtrace("sent");
        }
    }

    /**
     * Daily cron. It executes at settlement time (default is 00:05).
     *
     * @access private
     */
    function cron_daily()
    {
        global $CFG, $SITE;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $onepass = $timenow - $oneday;
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);

        // Delete orders that no transaction was made.
        $select = "(status='".AN_STATUS_NONE."') AND (timecreated<'$timediff30')";
        delete_records_select('enrol_authorize', $select);

        // Pending orders are expired with in 30 days.
        $select = "(status='".AN_STATUS_AUTH."') AND (timecreated<'$timediff30')";
        execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status='".AN_STATUS_EXPIRE."' WHERE $select", false);

        // Delete expired orders 60 days later.
        $timediff60 = $settlementtime - (60 * $oneday);
        $select = "(status='".AN_STATUS_EXPIRE."') AND (timecreated<'$timediff60')";
        delete_records_select('enrol_authorize', $select);

        // XXX TODO SEND EMAIL to 'enrol/authorize:uploadcsv'
        // get_users_by_capability() does not handling user level resolving
        // After user resolving, get_admin() to get_users_by_capability()
        $adminuser = get_admin();
        $select = "status IN(".AN_STATUS_UNDERREVIEW.",".AN_STATUS_APPROVEDREVIEW.") AND (timecreated<'$onepass') AND (timecreated>'$timediff60')";
        $count = count_records_select('enrol_authorize', $select);
        if ($count) {
            $a = new stdClass;
            $a->count = $count;
            $a->course = $SITE->shortname;
            $subject = get_string('pendingechecksubject', 'enrol_authorize', $a);
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot.'/enrol/authorize/uploadcsv.php';
            $message = get_string('pendingecheckemail', 'enrol_authorize', $a);
            @email_to_user($adminuser, $adminuser, $subject, $message);
        }

        // Daily warning email for pending orders expiring.
        if (empty($CFG->an_emailexpired)) {
            return; // not enabled
        }

        // Pending orders count will be expired.
        $timediffem = $settlementtime - ((30 - intval($CFG->an_emailexpired)) * $oneday);
        $select = "(status='". AN_STATUS_AUTH ."') AND (timecreated<'$timediffem') AND (timecreated>'$timediff30')";
        $count = count_records_select('enrol_authorize', $select);
        if (!$count) {
            return;
        }

        // Email to admin
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->shortname;
        $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->fullname;
        $a->enrolurl = "$CFG->wwwroot/$CFG->admin/enrol_config.php?enrol=authorize";
        $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH;
        $message = get_string('pendingordersemail', 'enrol_authorize', $a);
        email_to_user($adminuser, $adminuser, $subject, $message);

        // Email to teachers
        if (empty($CFG->an_emailexpiredteacher)) {
            return; // email feature disabled for teachers.
        }

        $sorttype = empty($CFG->an_sorttype) ? 'ttl' : $CFG->an_sorttype;
        $sql = "SELECT e.courseid, e.currency, c.fullname, c.shortname,
                  COUNT(e.courseid) AS cnt, SUM(e.amount) as ttl
                FROM {$CFG->prefix}enrol_authorize e
                  INNER JOIN {$CFG->prefix}course c ON c.id = e.courseid
                WHERE (e.status = ". AN_STATUS_AUTH .")
                  AND (e.timecreated < $timediffem)
                  AND (e.timecreated > $timediff30)
                GROUP BY e.courseid
                ORDER BY $sorttype DESC";

        for ($rs = get_recordset_sql($sql); ($courseinfo = rs_fetch_next_record($rs)); )
        {
            $lastcourse = $courseinfo->courseid;
            $context = get_context_instance(CONTEXT_COURSE, $lastcourse);
            if (($paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments'))) {
                $a = new stdClass;
                $a->course = $courseinfo->shortname;
                $a->pending = $courseinfo->cnt;
                $a->days = $CFG->an_emailexpired;
                $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
                $a = new stdClass;
                $a->course = $courseinfo->fullname;
                $a->pending = $courseinfo->cnt;
                $a->currency = $courseinfo->currency;
                $a->sumcost = $courseinfo->ttl;
                $a->days = $CFG->an_emailexpired;
                $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?course='.$lastcourse.'&amp;status='.AN_STATUS_AUTH;
                $message = get_string('pendingordersemailteacher', 'enrol_authorize', $a);
                foreach ($paymentmanagers as $paymentmanager) {
                    email_to_user($paymentmanager, $adminuser, $subject, $message);
                }
            }
        }
        rs_close($rs);
    }
}
?>
